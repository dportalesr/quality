<?php
/*
* 13/Abr/10 - Bandera $uploading para determinar si el modelo subió/actualizó un archivo.
* 27/Mar/10 -
	Reestructuración completa para llevar a cabo el upload final después del salvar el registro (cuando pase la validación).
	Settings por modelo para evitar sobreescritura al inicializar varios modelos.
	Validación de errores de Upload / tipo de archivo / Vacío cuando obligatorio
* 19/Mar/10 - Solucionado problema de hacer portada cuando no hay Parent
* 30/Mar/10 - Fix: Soporte para varias configuraciones de varios modelos
* 14/Jul/10 - Fix: Multiples llamadas a beforeValidate en Cake1.3 (bug?) causaban múltiples procesamientos en la data del modelo. Se agregó la variable skipped para llevar un control y saltar la primera validación (extra).
		  - Cambio de nombres de variables.
* 15/Jul/10 - Fix anterior revertido. Solución: Llamada a SaveAll($data,array(validate=>true))
* 15/Jul/10 - Fix: La validación y asignación de portada ahora después de comprobar que se hace upload
* 16/Ago/10 - Fix: El array de datos ya no se unsetea si el archivo NO es actualización y así validar si es vacío o no
* 12/Oct/10 - Fix: toUpload ahora es un array para subir varios archivos en el mismo registro (más de un campo tipo archivo)
* 25/Jul/11 - Reestructuración/Simplificación. Opción de marca de agua.
* 07/Nov/11 - Fix: Problema de lógica. Nueva variable maxsize para limitar de tamaño de archivo.

### USO ###
	$actsAs = array(
		'File'=>array(
			'portada'=>[
				false, 
				true: Se autoselecciona como portada (portada = 1) la primera imagen de la cola,
				<string>: Nombre de la foreignKey del modelo. Se usa para filtrar los registros para hacer la portada [se agrega la condición <foreignKey> = X],
				<array>: Array simple con los campos que servirán para filtrar registros al hacer portada; o asociativo con los valores si serán estáticos
				Ejemplo:
					Si $settings['src']['portada'] = array('album_id','category') la imagen adquirirá una portada para el conjunto de registros que tengan el mismo album_id y category_id que el actual.
					Si $settings['src']['portada'] = array('album_id'=>5,'category'=>'Lols') la imagen adquirirá una portada para el conjunto de registros que tengan el mismo album_id y category_id que el actual.
			],
			'fields'=>array(
				'<fieldname>'=>array(
					'watermark'=>false,
					'types'=>'png|jpeg|jpg|gif',
					'dir'=>'upload'
				)
			)
		)
	)
*/

class FileBehavior extends ModelBehavior{
	var $settings = array(); /// Global settings
	var $toDelete = array();
	var $isUpload = false;
	var $toUpload = array();
	var $errors = array(
		1 => 'El tamaño del archivo ha superado el límite permitido.',
		2 => 'El tamaño del archivo ha superado el límite permitido.',
		3 => 'El archivo no se ha subido completamente.',
		4 => 'No se ha seleccionado archivo para subir.',
		6 => 'El servidor no ha podido crear el directorio.',
		7 => 'El servidor no ha podido guardar el archivo.'
	);

	function setup(&$model, $settings = array()) {
		# Settings default del behaviour
		$settings = am(array(
			'portada'=>true,
			'fkey'=>false,
			'conditions'=>false,
			'fields'=>array('src')
		),$settings);
		$settings['fields'] = Set::normalize($settings['fields']);

		if($model->hasField('portada')){
			/// Si portada es String o Array con valor
			if(!(is_bool($settings['portada']) || empty($settings['portada']))){
				if(is_string($settings['portada'])){ /// String = foreignKey
					$settings['portada'] = low($settings['portada']);
					if($model->hasField($settings['portada'])){ /// Existe el campo en el modelo
						$settings['fkey'] = $settings['portada'];
					}
					$settings['portada'] = true;
				} elseif(is_array($settings['portada'])){ /// Array = condiciones
					$settings['conditions'] = Set::normalize($settings['portada']);
					$settings['portada'] = true;
				}
			}
		} else
			$settings['portada'] = false;
		
		foreach($settings['fields'] as $field => &$sets) {
			if($model->hasField($field)){
				if(!$sets) $sets = array();
				
				$sets = am(array(
					'watermark'=>false,
					'strict'=>false,
					'maxsize'=>200*1024, # 200 Kbs
					'types'=>'png|jpeg|jpg|gif',
					'dir'=>'upload',
				),$sets);
				
				if(is_string($sets['types'])){ /// String2Array
					$sets['types'] = explode('|',$sets['types']);
				}
				
				$sets['types'] = array_map('strtolower',$sets['types']);
			} else
				unset($settings['fields'][$field]);
		}
		$this->settings[$model->alias] = $settings;
	}

	function beforeValidate(&$model){
		$this->isUpload = false;
		$this->toDelete = $this->toUpload[$model->alias] = array();

		///// AUTO-PORTADA
		if($this->settings[$model->alias]['portada']){
			$find = array($model->alias.'.portada'=>1);

			# Si tiene llave foránea, agregar al array de condiciones de búsqueda el "enlace" con el id del parent.
			if($this->settings[$model->alias]['fkey'])
				$find[$model->alias.'.'.$this->settings[$model->alias]['fkey']] = $model->data[$model->alias][$this->settings[$model->alias]['fkey']];
			
			if($this->settings[$model->alias]['conditions']){
				foreach($this->settings[$model->alias]['conditions'] as $field => $value){
					/// Si es valor estático ($value no es vacío), lo asigna, si no, toma del valor actual del registro.
					$find[$model->alias.'.'.$field] = $value ? $value : $model->data[$model->alias][$field];
				}
			}

			# si debe tener portada ($this->settings[$model->alias]['portada']) y aún no tiene portada...
			if(!$model->find('count',array('conditions'=>$find)))
				$model->data[$model->alias]['portada'] = 1; # ..se considera portada.
		}
		//////		

 		foreach($this->settings[$model->alias]['fields'] as $field => $sets){ # Revisamos que campos corresponden a archivos para subir.
			# Si está presente el campo archivo
			if(isset($model->data[$model->alias][$field]) && is_array($model->data[$model->alias][$field])){
				$file = $model->data[$model->alias][$field];

				if($sets['maxsize'] && $file['size'] > $sets['maxsize'])
					$file['error'] = 1;
				
				if($file['error'] == 0){ # y se subió correctamente
					$this->isUpload = true; # Bandera
					$this->absDir = WWW_ROOT.$sets['dir'];
					$this->relDir = $sets['dir'];

					if(in_array($this->_ext($file['name']),$sets['types'])){
						# Reemplazamos el array del archivo por la ruta en el servidor. Listo para guardar nuestro array DATA.
						$file['watermark'] = $sets['watermark'];
						$file = $this->_toUrl($file, $model->alias);
					} else {
						$model->invalidate($field,'La extensión del archivo es inválida. Sólo se permiten archivos del tipo: '.implode(',',$sets['types']));
						$file = 'invalidtype';
					}

					/// Eliminación por Reemplazo

					# Si hay $id en $model->alias = Actualización
					if(isset($model->data[$model->alias][$model->primaryKey]) && $model->data[$model->alias][$model->primaryKey]){
						# Hay archivo a reemplazar, se agrega a $this->toDelete para eliminación posterior en caso de guardado exitoso del nuevo archivo
						if($prevFile = $model->field($field,array($model->primaryKey => $model->data[$model->alias][$model->primaryKey])))
							$this->toDelete[] = $prevFile;
					}

				} else {
					# No se subió archivo alguno
					if($file['error'] == 4){
						# Es update
						if(isset($model->data[$model->alias][$model->primaryKey]) && $model->data[$model->alias][$model->primaryKey]){
							# "Eliminar imagen" checkbox
							if(isset($model->data[$model->alias][$field.'_delete']) && $model->data[$model->alias][$field.'_delete']){
								$this->toDelete[] = $file;
								$file = '';
							} else {
								unset($model->data[$model->alias][$field]);
							}
						} else {
							$file = '';
						}
						
					} else { # Cualquier otro error, mostrarlo como error de validación de modelo
						$model->invalidate($field,$this->errors[$file['error']]);
						$file = $this->errors[$file['error']];
					}
				}
				
				if(isset($model->data[$model->alias][$field]))
					$model->data[$model->alias][$field] = $file;
				
			} else {
				$this->isUpload = false;
				if(isset($model->data[$model->alias][$model->primaryKey]) && isset($model->data[$model->alias][$field]) && $model->data[$model->alias][$field] == '' && ($prevFile = $model->field($field,array($model->primaryKey => $model->data[$model->alias][$model->primaryKey])))){
					@unlink(WWW_ROOT.$prevFile);
				}
			}
		}
		return true;
	}

	function _toUrl($file,$alias){
		$target_path = $this->absDir.DS.$this->_fixFileName($file['name']);
		$temp_path = substr($target_path, 0, strlen($target_path) - strlen('.'.$this->_ext($file['name']))); //temp path without the ext
		$i = 1;

		while(file_exists($target_path)){ $target_path = $temp_path.time().$i.'.'.$this->_ext($file['name']); $i++; }
		
		if(!isset($this->toUpload[$alias][$file['tmp_name']])){
			$this->toUpload[$alias][$file['tmp_name']] = array(
				'watermark'=>$file['watermark'],
				'temp'=>$file['tmp_name'],
				'target'=>$target_path
			);
		}
		
		return $this->relDir.'/'.basename($target_path);
	}

	function _fixFileName($filename){
		$latin = array('á','é','í','ó','ú','ü','ñ',' ');
		$inter = array('a','e','i','o','u','u','n','_');
		return substr(preg_replace('/[^a-zA-Z0-9\_\.]/','',str_replace($latin, $inter, strtolower(trim($filename)))), 0, 100);
	}

	function _ext($filename){ return low(substr(strrchr($filename,'.'),1)); }

	///  Callbacks  //////////////////////////////////////////////////////////////////

	function afterSave(&$model,$created){
		if($this->isUpload){ // Sólo si fue update de archivo (Saltar si fue update de otros campos (descripción))
			if(!is_dir($this->absDir))
				mkdir($this->relDir,0777);

			if(!$created){ //// Al actualizar, eliminar el anterior
				foreach($this->toDelete as $delFile)
					@unlink('./'.$delFile);
				$this->toDelete = array();
			}

			//// Upload
			//fb($this->toUpload,'Starting Queue');
			ini_set("max_execution_time",300);
			
			foreach($this->toUpload[$model->alias] as $uploadme){
				move_uploaded_file($uploadme['temp'],$uploadme['target']);
				
				/****** WATERMARK *****************************/
				
				$ext = $this->_ext($uploadme['target']);
				if($ext == 'jpg') $ext = 'jpeg';
				if($uploadme['watermark'] && in_array($ext,array('jpeg','gif','png'))){
					$uploadme['watermark'] = WWW_ROOT.str_replace(array('/', '\\'), DS,$uploadme['watermark']);
					$water_ext = $this->_ext($uploadme['watermark']);
					if($water_ext == 'jpg') $water_ext = 'jpeg';
					
					$ihandle = fopen($uploadme['target'], 'r');
					$image = fread($ihandle, filesize($uploadme['target']));
					fclose($ihandle);
			
					if (false === ($image = imagecreatefromstring($image)))
						throw new Exception("Image not valid");

					$ihandle = fopen($uploadme['watermark'], 'r');
					$watermark = fread($ihandle, filesize($uploadme['watermark']));
					fclose($ihandle);
			
					if(false === ($watermark = imagecreatefromstring($watermark)))
						throw new Exception("Image not valid");

					$water_w = imagesx($watermark);
					$water_h = imagesy($watermark);
									
					$size = getimagesize($uploadme['target']);
					
					$dest_x = $size[0] - $water_w - 5;
					$dest_y = $size[1] - $water_h - 5;
					
					imagecopy($image, $watermark, $dest_x, $dest_y, 0, 0, $water_w, $water_h);
					
					if($ext=='gif')
						call_user_func('image'.$ext,$image,$uploadme['target']);
					else
						call_user_func('image'.$ext,$image,$uploadme['target'],90);

					imagedestroy($image);
					imagedestroy($watermark);
				}
				/********************************************/
			}
			$this->toUpload[$model->alias] = array();
		}
	}

	function beforeDelete(&$model){
		$data = $model->read(null, $model->id);
		$this->toDelete = array();
		foreach ($this->settings[$model->alias]['fields'] as $field => $sets)
			$this->toDelete[] = $data[$model->alias][$field];

		return true;
	}

	function afterDelete(&$model){
		foreach($this->toDelete as $delFile)
			@unlink('./'.$delFile);
		$this->toDelete = array();

		return true;
	}
}
?>