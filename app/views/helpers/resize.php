<?php
class ResizeHelper extends Helper {
/**
* 04/Mar/2010 - Compatibilidad para abrir Thumbnails de Youtube y poner el ID del video en el nombre de archivo evitando sobreescritura (0.jpg).
* 23/Mar/2010
*	- Revisión general.
*	- Fix para los hostings con allow_url_fopen desactivado (Youtube thumbnails).
* 26/Mar/2010 - Revisión de las rutas.
* 07/Mar/2011 - Parámetros como array y agregada la opción Fill: Las dimensiones especificadas funcionan como mínimas.
* 28/Nov/2012 - +Opción "pad". Permite ajustar imágenes a un alto y ancho fijo, rellenando las partes sobrantes con un color de fondo.
* 				bool | array(int[0-255],int[0-255],int[0-255]) | "[0-255] [0-255] [0-255]"
*				Si es true, tomará el valor default de array(255,255,255). Si es array, deberá componerse de 3 elementos correspondientes a los valores RGB del color de fondo. Si es cadena, los mismos valores, separados por espacios.
*/
	var $helpers = array('Html');
	var $cacheDir = 'cache';

/**
* Automatically resizes an image and returns formatted IMG tag
*
* @param string $url Path to the image file, relative to the webroot/img/ directory.
* @param integer $opts['w'] Image of returned image
* @param integer $opts['h'] Height of returned image
* @param boolean $opts['aspect'] Maintain aspect ratio (default: true)
* @param array $opts['atts'] Array of HTML attributes.
* @param boolean $opts['urlonly'] Restituisce solamente l'url invece dell'immagine completa
* @param boolean $return Wheter this method should return a value or output it. This overrides AUTO_OUTPUT.
* @return mixed Either string or echos the value, depends on AUTO_OUTPUT and $return.
* @access public
*/
	function resize($url, $opts = array()) {
		$defaults = array(
			'w'=>'',
			'h'=>'',
			'fill'=>false,
			'pad'=>false,
			'aspect'=>true,
			'atts'=>array(),
			'urlonly'=>false
		);
		$opts = array_merge($defaults,$opts);

		if(!($opts['w'] || $opts['h'])){ $this->log(array($url,$opts),'resize_error'); return false; }
		
		if($opts['pad'] && !(empty($opts['w']) || empty($opts['h']))){
			$opts['fill'] = false;
			if(is_string($opts['pad'])){
				$opts['pad'] = explode(' ',$opts['pad']);
			} elseif ($opts['pad'] === true || (int)$opts['pad'] === 1) {
				$opts['pad'] = array(255,255,255);
			}
		} else {
			$opts['pad'] = false;
		}

		$types = array(1 => 'gif', 'jpeg', 'png', 'swf', 'psd', 'wbmp'); // used to determine image type
		$dimens = $opts['w'].'x'.$opts['h'].($opts['pad'] ? 'pad':'');

		$cachePath = WWW_ROOT.$this->cacheDir;
		
		$percorso = $cachePath.DS.$dimens;
		if(!is_dir($percorso)){
			mkdir($percorso);
			chmod($this->cacheDir.DS.$dimens,0777);
		}

		$filename = basename($url);
		
		if($isExternal = (stripos($url,'http') === 0)){
			$temp = parse_url($url);
			if(stripos($temp['host'],'youtube') !== false){ # Fix for youtube 0.jpg thumbs
				list(,,$filename,) = explode('/',$temp['path']);
				$filename.= low(strrchr($temp['path'],'.'));
			}
			$localFile = $cachePath.DS.$filename;
			if(!file_exists($localFile)){ # TODO: Agregar 1 Hr de cache si el archivo existe
				$curl = curl_init($url);
				$fh = fopen($localFile, "w");
				curl_setopt ($curl, CURLOPT_FILE, $fh);
				curl_setopt ($curl, CURLOPT_HEADER, 0);
				curl_exec ($curl);
				curl_close ($curl);
				fclose ($fh);
				chmod($localFile,0777);
			}
			
			$url = $this->cacheDir.'/'.$filename;
		}
		
		$originalPath = WWW_ROOT.str_replace(array('/', '\\'), DS,$url);
		$cachedPath = $percorso.DS.$filename;
		$cachedUrl = $this->cacheDir.'/'.$dimens.'/'.$filename;
		$omitResize = false;
		
		/*/
		fb($percorso,'percorso');
		fb($originalPath,'originalPath');
		fb($isExternal,'isExternal');
		fb($url,'$url');
		fb($filename,'$filename');
		fb($cachedPath,'$cachedPath');
		fb($cachedUrl,'$cachedUrl');
		/**/
	
		if (!($size = @getimagesize($originalPath))){ $this->log($originalPath,'resize_error'); return ''; }

		/// Solo se especifica ancho ó alto y coincide con la original, omite resize
		if(($opts['w'] == $size[0] && (!$opts['h'])) || ($opts['h'] == $size[1] && (!$opts['w']))){
			$omitResize = true;

		} else {
			if($opts['aspect']){ // adjust to aspect
				$nx = $ny = 0;
				
				if(($opts['pad'])){
					# Mas horizontal que el destino, ó cuadrado: Por ancho
					if (($size[0] / $size[1]) >= ($opts['w'] / $opts['h'])) {
					    $new_width = $opts['w'];
					    $new_height = $size[1] * ($opts['w'] / $size[0]);
					    $nx = 0;
					    $ny = floor(abs($opts['h'] - $new_height) / 2);
					# Mas vertical que el destino: Por alto
					} else {
					    $new_width = $size[0] * ($opts['h'] / $size[1]);
					    $new_height = $opts['h'];
					    $nx = floor(abs($opts['w'] - $new_width) / 2);
					    $ny = 0;
					}
				} else {
					$new_width = $opts['w'];
					$new_height = $opts['h'];
					// Redimensiona en base a la altura (Por no especificar ancho o por ser imagen vertical)
					if((!$opts['w']) || ($opts['h'] && ($size[1]/$opts['h']) > ($size[0]/$opts['w']))){ // $size[0]:width, [1]:height, [2]:type
						// Si es relleno de área, se redimensiona en base al eje menor (ancho)
						if($opts['fill'] && $opts['w']) {
							$new_height = ceil($opts['w'] / ($size[0]/$size[1]));
						} else {
							$new_width = ceil(($size[0]/$size[1]) * $opts['h']);
						}

					// Redimensiona en base al ancho (Por no especificar alto o por ser imagen horizontal)
					} elseif((!$opts['h']) || ($opts['w'] && ($size[1]/$opts['h']) < ($size[0]/$opts['w']))){ // $size[0]:width, [1]:height, [2]:type
						// Si es relleno de área, se redimensiona en base al eje menor (alto)
						if($opts['fill'] && $opts['h']){
							$new_width = ceil(($size[0]/$size[1]) * $opts['h']);
						} else {
							$new_height = ceil($opts['w'] / ($size[0]/$size[1]));
						}
					}
				}
			}
		}
	
		if(file_exists($cachedPath)) {
			$csize = @getimagesize($cachedPath);
			$cached = ($csize[0] == $new_width && $csize[1] == $new_height); // image is cached
			if (@filemtime($cachedPath) < @filemtime($url)) // check if up to date
				$cached = false;
		} else {
			$cached = false;
		} $cached = false;

		$resize = (!$cached) && (!$omitResize) ? (($size[0] != $new_width) || ($size[1] != $new_height)) : false;

		if($resize){
			$original = call_user_func('imagecreatefrom'.$types[$size[2]], $originalPath);
			if($opts['pad']){
				$dst_width = $opts['w'];
				$dst_height = $opts['h'];
			} else {
				$dst_width = $new_width;
				$dst_height = $new_height;
			}
			if (function_exists('imagecreatetruecolor') && ($new = imagecreatetruecolor($dst_width, $dst_height))){
				if($opts['pad']){
					$color = imagecolorallocate($new, $opts['pad'][0], $opts['pad'][1], $opts['pad'][2]);
					imagefill($new, 0, 0, $color);
				}
				@imagecopyresampled ($new, $original, $nx, $ny, 0, 0, $new_width, $new_height, $size[0], $size[1]);
				
			} else {
				if($new = @imagecreate($dst_width, $dst_height)){
					if($opts['pad']){
						$color = imagecolorallocate($new, $opts['pad'][0], $opts['pad'][1], $opts['pad'][2]);
						imagefill($new, 0, 0, $color);
					}
					@imagecopyresized($new, $original, $nx, $ny, 0, 0, $new_width, $new_height, $size[0], $size[1]);
				}
			}
			

			if(in_array($types[$size[2]],array('jpg','jpeg'))){
				call_user_func('image'.$types[$size[2]], $new, $cachedPath, 100);
			} else {
				call_user_func('image'.$types[$size[2]], $new, $cachedPath);
			}

			@chmod($cachedPath,0777);
			@imagedestroy ($original);
			@imagedestroy ($new);

		} elseif(!$cached){
			$cachedUrl = $url;
		}
		
		if(strpos($cachedUrl,'/')=== 0) # Fix no resized, full urls and others
			$cachedUrl = substr($cachedUrl,1);

		$cachedUrl = $this->webroot.$cachedUrl;
		$opts['atts']['alt'] = $filename;
		
		if ($opts['urlonly'] != true)
			$output = sprintf($this->Html->tags['image'], $cachedUrl, $this->Html->_parseAttributes($opts['atts'],null,'',' '));
		else
			$output = $cachedUrl;
		
		/// fb($output,'output');
		return $output;
	}
}
?>