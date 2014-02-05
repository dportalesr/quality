<?php
class AppModel extends Model {
	var $displayField = 'nombre';
	var $labels = array();
	var $deletable = true;
	var $actsAs = array('Containable');
	var $skipValidation = array(); // Campos a omitir para automagia de validación
	var $categoryModels = array();
	var $baseCategory = false;
	var $suggest = false; // Usar :<Modelo> si el modelo es único y fijo
	var $asTree = false;
	var $isExclusive = false; // Sólo 1 activo por catálogo; si es un nombre de campo, servirá como condición. ej.: category_id

	function __construct($id = false, $table = null, $ds = null){
		parent::__construct($id, $table, $ds); //fb($this->_schema,$this->name.' SCHEMA');
		$defaultlabels = array(
			'created'=>'creado',
			'modified'=>'modificado',
			'updated'=>'actualizado',
			'opcion'=>'opción',
			'descripcion'=>'descripción',
			'layout'=>'disposición',
			'email'=>'E-Mail',
			'src'=>'archivo'
		);
		
		foreach($this->_schema as $fieldName => $fieldData){
			$fieldLabel = false;
			
			if(isset($this->labels[$fieldName]))
				$fieldLabel = $this->labels[$fieldName];
			elseif(isset($defaultlabels[$fieldName]))
				$fieldLabel = $defaultlabels[$fieldName];
			elseif(strpos($fieldName, 'category')!==false)
				$fieldLabel = 'categoría';
			else
				$fieldLabel = $fieldName;
			
			$this->_schema[$fieldName]['label'] = ucfirst($fieldLabel);
		}

		/// Auto categoryModel
		if($this->belongsTo){
			foreach($this->belongsTo as $belongModel => $belongData){
				if(strpos(strtolower($belongModel),'category')!== false){
					$this->categoryModels[] = $belongModel;
				}
			}
		}

		/// Auto Validation
		$notEmpty = array('rule'=>array('between', 1,255), 'allowEmpty'=>false, 'message'=>'Este campo no puede quedar vacío.');
		$url = array('rule'=>'url', 'allowEmpty'=>true, 'message'=>'Ingrese una dirección web válida.');
		$date = array('rule'=>'date', 'allowEmpty'=>true, 'message'=>'Ingrese una fecha válida.');
		$email = array('rule'=>'email', 'allowEmpty'=>false, 'message'=>'Ingrese una dirección de correo electrónico válida.');
		$file = am($notEmpty,array('message'=>'Seleccione un archivo.'));
		$text = am($notEmpty,array('rule'=>'notEmpty','message'=>'Ingrese un texto.'));
		
		$rules = array(
			'nombre'=>$notEmpty,
			'web'=>$url,
			'enlace'=>$url,
			'caducidad'=>$date,
			//'fecha'=>$date,
			'email'=>$email,
			'descripcion'=>$text,
			'mensaje'=>$text,
			'src'=>$file
		);
		
		if(((!$this->skipValidation) && (strpos(strtolower($this->alias),'carousel')!==false || strpos(strtolower($this->alias),'img')!==false)) || $this->skipValidation === true){
			$this->skipValidation = array('nombre','descripcion');
		}

		foreach($rules as $field => $rule){
			$ruleName = is_array($rule['rule']) ? reset($rule['rule']) : $rule['rule'];
			
			if($this->hasField($field) && (!in_array($field, (array)$this->skipValidation))){
				if(isset($this->validate[$field])){ // Ya está validado
					if(isset($this->validate[$field]['rule'])){ // Tiene regla única
						$oldRuleName = is_array($this->validate[$field]['rule']) ? reset($this->validate[$field]['rule']) : $this->validate[$field]['rule'];
						if($oldRuleName != $ruleName){ // Es diferente a la automática
							$this->validate[$field] = array(
								$oldRuleName => $this->validate[$field],
								$ruleName => $rule
							);
						}
					} else {
						$found = false;
						foreach($this->validate[$field] as $field__ => $rule__){
							$found = $found || $rule__['rule'] == $rule['rule'];
						}
						if(!$found) $this->validate[$field][$ruleName] = $rule;
					}
				} else {
					$this->validate[$field] = $rule;
				}
			}
		}
		
		if($this->categoryModels){
			foreach($this->categoryModels as $categModel){
				$belongs = $this->belongsTo[$categModel];
				$fk = $belongs['foreignKey'];
				$rule = array('rule'=>'hasCategory', 'allowEmpty'=>false, 'message'=>'Debe seleccionar una categoría existente, o crear una nueva categoría.');
				
				if(!isset($this->validate[$fk]))
					$this->validate[$fk] = $rule;
				else
					$this->validate[$fk]['hascategory'] = $rule;
			}

			if(!$this->baseCategory) $this->baseCategory = $this->categoryModels[0];
		}
		
		/// suggest
		if($this->suggest === false && isset($this->_schema['parent']) && isset($this->_schema['parent_id'])){
			$this->suggest = true;
		}
		
		/// Auto ordenar en las relaciones de modelos
		$properties = array('hasMany','belongsTo');
		foreach($properties as $prop){
			foreach($this->{$prop} as $model => $hmany){
				if(empty($this->{$prop}[$model]['order'])){
					$order = '';
					
					if($this->$model->hasField('orden')){
						$order = $model.'.orden '.($model != $this->alias.'img' ? 'DESC' : '');
					} else {
						if($this->$model->hasField('created'))
							$order = $model.'.created DESC, ';
						
						$order.= $model.'.id '.($model != $this->alias.'img' ? ' DESC' : '');
					}
					
					$this->{$prop}[$model]['order'] = $order; 
				}
			}
		}
		
		/// Auto ordenable
		$actsas = Set::normalize($this->actsAs);
		if((!array_key_exists('Ordenable',$actsas)) && $this->hasField('orden')){
			$this->Behaviors->attach('Ordenable');
		}
	}

	function filtervalidfields($fields,$byKey){
		$validfields = $fields;
		foreach($fields as $idx => $field){
			if($byKey) $field = $idx;
			if(!$this->hasField($field))
				unset($validfields[$idx]);
		}
		return $validfields;
	}

	function findParent(&$arr,$searchvalue=null,$searchkey=null,$parent=false,&$parents){
		foreach($arr as $key => &$value){	
			if(is_array($value)){
				$this->findParent($value,$searchvalue,$searchkey,$key,$parents);
			} else{
				if((isset($searchkey) && isset($searchvalue) && $searchkey===$key && $searchvalue===$value) || (!isset($searchvalue) && isset($searchkey) && $searchkey===$key) || (!isset($searchkey) && isset($searchvalue) && $searchvalue===$value)){
					$parents[] = $parent ? $parent : false;
				}
			}
		}
	}
	
	function clean(&$arr,$decode = false, $skip = array('descripcion','texto','intro')){
		   App::import('Sanitize');
		   if($arr){
				 foreach($arr as $key => &$value){
					    if(is_array($value)){
							  $this->clean($value,$decode,$skip);
					    } else {
							  
							  if($skip !== false){
									// Está en schema y su tipo no es string ni text
									$safeBySchema = isset($this->_schema[$key]['type']) && (!in_array($this->_schema[$key]['type'],array('string','text')));
									// No está en schema pero es numérico
									$safeNoSchema = (!isset($this->_schema[$key]['type'])) && is_numeric($value);
									
									if($safeBySchema || $safeNoSchema){
										   $skipped = true;
									} else {
										   $skipped = in_array($key,(array)$skip);
									}
							  }else { $skipped = $skip; }
							  
							  if(!$skipped){
									$value = $decode ? html_entity_decode($value,ENT_QUOTES,Configure::read('App.encoding')) : str_replace("\\n","\n",Sanitize::clean($value));
									$strip = strip_tags($value);
									if(empty($strip))
										   $value = '';
							  }
					    }
				 }
		   }
	}

	function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		$doQuery = true;
		// check if we want the cache
		if(!empty($fields['cache'])) {
			if(!is_string($fields['cache']) && $fields['cache'])
				$fields['cache'] = 'short';
				
			$cacheConfig = null;
			
			// check if we have specified a custom config, e.g. different expiry time
			if(!empty($fields['cacheConfig']))
				$cacheConfig = $fields['cacheConfig'];

			$cacheName = 'find_'.strtolower($this->name). '_' . $fields['cache'];
			
			// if so, check if the cache exists
			if(($data = Cache::read($cacheName, $cacheConfig)) === false) {
				$data = parent::find($conditions, $fields, $order, $recursive);
				Cache::write($cacheName, $data, $cacheConfig);
			}
			$doQuery = false;
		}
		
		if($doQuery) $data = parent::find($conditions, $fields, $order, $recursive);
		
		return $data;
	}

	function find_($find = array(), $type = false){
		$byId = false;
		$find = (array)$find;

		if(isset($find[0]) && ((int)$find[0])){
			$byId = (int)$find[0];
			unset($find[0]);
			if($type === false)
				$type = 'first';
		}

		if(!$type)
			$type = 'all';
		
		$find = Set::normalize((array)$find);
		
		if($sole = isset($find['contain'])){
			$recursive = $this->recursive;
			$this->recursive = -1;
		}
		
		if(array_key_exists('cache',$find) && (!$find['cache'])){
			$find['cache'] = true;
		}
		
		if(!isset($find['conditions'])){
			if($type == 'paginate'){
				$find = array('conditions'=> $find);
			} else {
				$find['conditions'] = array();
			}
		}
		
		if($byId){
			$find['conditions'][$this->alias.'.'.$this->primaryKey] = $byId;
		}
		
		if(strpos($type,'+') !== false){
			$type = str_replace('+','',$type);
		}elseif($this->hasField('activo') && (!isset($find['conditions']['activo']))){
			$find['conditions'][$this->alias.'.activo'] = 1;
		}

		if($type == 'paginate'){
			return $find['conditions'];
		}else{
			if(!isset($find['order'])){
				if($this->hasField('orden'))
					$find['order'] = array($this->alias.'.orden'=>'DESC');
				else{
					if($this->hasField('created'))
						$find['order'] = array($this->alias.'.created' => 'DESC');
					
					$find['order'][$this->alias.'.id'] = 'DESC';
				}
			}
		}
		
		$results = $this->find($type,$find);
		if($sole) $this->recursive = $recursive;

		return $results;
	}
	
	function beforeValidate(){ $this->clean($this->data); return true; }
	function afterSave($created){
		$data['slug'] = '';
		# Es sluggeable y tiene el campo "Display" (posible actualización del campo)
		if($this->hasField('slug') && isset($this->data[$this->alias][$this->displayField]) && $this->data[$this->alias][$this->displayField]){
			if($slug = $this->_sluger(html_entity_decode($this->field($this->displayField,array('id'=>$this->id)),ENT_QUOTES,Configure::read('App.encoding')),119 - strlen($this->id))){
				$data['slug'] = $this->id.'_'.$slug;
				$this->clean($data);
				$this->saveField('slug',$data['slug']);
			}
		}

		# Exclusivo (Solo 1 activo por catálogo)
		if($this->isExclusive){
			$validexcl = is_string($this->isExclusive) && $this->hasField($this->isExclusive);
			$fields = array('activo');
			$conds = array($this->alias.'.id <>' => $this->id);
			
			if($validexcl) $fields[] = $this->isExclusive;
			
			$item = $this->read($fields,$this->id);
			
			if($validexcl)	$conds[$this->alias.'.'.$this->isExclusive] = $item[$this->alias][$this->isExclusive];
			if($item[$this->alias]['activo']) $this->updateAll(array($this->alias.'.activo'=>0),$conds);
		}
				
		////
		
		$this->deleteShortCache();
	}

	function afterFind($results, $primary){
		if($primary){
			$ipfields = array();
			$this->findParent($this->validate,'ip','rule',null,$ipfields);# Obtenemos los campos con rule => url
			
			//// mooSuggest, autocaptions
			if($this->suggest !== false){
				$parent = false; // Nombre del modelo relacionado
				$parent_model = $this->alias; // Modelo que contiene el nombre del modelo relacionado
				$parent_field = 'parent'; // Campo que contiene el nombre del modelo relacionado
				
				if(is_string($this->suggest)){
					$parent_field = $this->suggest;
					
					if(strpos($this->suggest,'.')!==false){
						$parent_model = strtok($this->suggest,'.');
						$parent_field = strtok('.');
					}elseif(strpos($this->suggest,':')===0){ // Usar :<Modelo> si el modelo es único y fijo
						$parent = Inflector::classify(substr($this->suggest,1));
					}
				}
			}	
			
			////
			
			foreach($results as $idx => $result){
				//// Caduca en base a caducidad
				if(isset($result[$this->alias]['caducidad']) && !is_null($result[$this->alias]['caducidad'])){
					$results[$idx][$this->alias]['caduca'] = 1;
				}

				///// Parents
				if(isset($result[$this->alias]['parent']) && $result[$this->alias]['parent']){
					$results[$idx][$this->alias]['parent'] = Inflector::tableize($result[$this->alias]['parent']);
				}

				///// IP
				if($ipfields){
					foreach($ipfields as $ipf){
						if(isset($result[$this->alias][$ipf]) && $result[$this->alias][$ipf]){
							$results[$idx][$this->alias][$ipf] = inet_dtop($result[$this->alias][$ipf]);
						}
					}
				}

				//// mooSuggest, autocaptions
				if($this->suggest !== false){
					if(!$parent){
						if(isset($result[$parent_model][$parent_field]) && $result[$parent_model][$parent_field])
							$parent = Inflector::classify($result[$parent_model][$parent_field]);
					}

					if(isset($result[$parent]['nombre']))
						$results[$idx][$this->alias]['parent_nombre'] = $result[$parent]['nombre'];
					else
						$results[$idx][$this->alias]['parent_nombre'] = '';
				}
			////
			}
		}
		return $results;
	}
		
	function beforeSave(){
		/// Validación de campos tipo URL (Agrega http://)
		$URLs = array();
		$this->findParent($this->validate,'url','rule',null,$URLs);# Obtenemos los campos con rule => url
		
		if(sizeof($URLs)){
			foreach($URLs as $campoURL){
				if(isset($this->data[$this->alias][$campoURL]) && !(empty($this->data[$this->alias][$campoURL]) || strpos($this->data[$this->alias][$campoURL],'/')===0)){ #Si está presente en el array de datos
					if(strpos(trim($this->data[$this->alias][$campoURL]),'http://')!==0)
						$this->data[$this->alias][$campoURL] = 'http://'.$this->data[$this->alias][$campoURL];
				}
			}
		}

		/// IPs

		$ipfields = array();
		$this->findParent($this->validate,'ip','rule',null,$ipfields);# Obtenemos los campos con rule => url
		
		if($ipfields){
			foreach($ipfields as $ipf){
				if(isset($this->data[$this->alias][$ipf]) && $this->data[$this->alias][$ipf]){ #Si está presente en el array de datos
					$this->data[$this->alias][$ipf] = inet_ptod($this->data[$this->alias][$ipf]);
				}
			}
		}

		//// Caducidad | Será NULL si caduca o activo es 0
		if($this->hasField('caducidad') && 
		   ((isset($this->data[$this->alias]['caduca']) && !$this->data[$this->alias]['caduca']) ||
		   (isset($this->data[$this->alias]['activo']) && !$this->data[$this->alias]['activo']))
		){ 
			if(isset($this->data[$this->alias][$this->primaryKey]) && $this->data[$this->alias][$this->primaryKey]){
				$data = $this->data;
				$this->id = $this->data[$this->alias][$this->primaryKey];
				$this->saveField('caducidad',NULL);
				$this->data = $data;
			} else {
				$this->data[$this->alias]['caducidad'] = NULL;
			}
		}

		///// Parents
		if(isset($this->data[$this->alias]['parent']) && $this->data[$this->alias]['parent']){
			$this->data[$this->alias]['parent'] = Inflector::classify($this->data[$this->alias]['parent']);
		}
		
		//// To Continue the saving
		return true;
	}
	
	function afterDelete(){ $this->deleteShortCache(); }
	function beforeDelete($cascade = true){
		if($this->hasField('master') && $this->field('master'))
			return false;
		
		/*** DELETE UNUSED TAGS ***/
		if(isset($this->hasAndBelongsToMany['Tag'])){
			$habtm = $this->hasAndBelongsToMany['Tag'];
			$join = $habtm['joinTable'];
			$conds = 'NOT EXISTS (SELECT * FROM '.$join.' WHERE Tag.id = '.$join.'.tag_id AND '.$join.'.'.$habtm['foreignKey'].' <> '.$this->id.') AND Tag.id IN (SELECT '.$join.'.tag_id FROM '.$join.' WHERE '.$join.'.'.$habtm['foreignKey'].' = '.$this->id.')';
			$this->Tag->deleteAll($conds,true,true);
		}
		/**************************/
		
		return true;
	}

	function _sluger($slug,$long = false) {
		$latin = array('á','é','í','ó','ú','ü','ñ',' ');
		$inter = array('a','e','i','o','u','u','n','-');
		return substr(preg_replace('/[^a-zA-Z0-9\-]/','',str_replace($latin, $inter, strtolower(trim($slug)))), 0, $long ? $long : 120);
	}

	function _encriptpass(&$arr){
		foreach($arr as $key => &$value){	
			if(is_array($value)){
				$this->_encriptpass($value);
			} elseif($key == 'password'){
				$value = sha1($value);
			}
		}
	}

	//// Custom Validation Function
	function hasCategory($check){
		
		$field = key($check);
		$value = $check[$field];
		$categModel = ucfirst(substr($field,0,-3));
		
		if(!in_array($categModel, $this->categoryModels)) return false;
		
		return $value || (isset($this->data[$categModel]['nombre']) && $this->data[$categModel]['nombre']);
	}
	
	function deleteShortCache(){
		$paths = array('','views'.DS);
		foreach($paths as $path){
			$cacheFolder = new Folder(CACHE.$path);
			$files = $cacheFolder->find('^(.)*\_'.Inflector::underscore($this->name).'\_(.)*');
	
			foreach($files as $f){
				$pathinfo = pathinfo($f);
				if(isset($pathinfo['extension']))
					$f = basename($f,'.'.$pathinfo['extension']);

				@unlink(CACHE.$path.$f);
			}
		}
	}
}
?>