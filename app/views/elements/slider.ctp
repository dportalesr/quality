<?php
$id = isset($id) ? $id : 'roller';
$model = isset($model) ? $model : $_m[0].'img';
$srcmodel = isset($srcmodel) ? $srcmodel : $model;
if(isset($titlemodel)){ if(!isset($title)) $title = true; } else { $titlemodel = $model; }
$title = isset($title) ? (is_string($title) ? $title : 'nombre') : false;
$display = isset($display) ? $display : 'src'; // Campo imagen
$enlace = isset($enlace) && $enlace ? (is_string($enlace) ? $enlace : 'enlace') : false; // Nombre del campo que tiene el enlace
$element = isset($element) ? $element : false;
$redirect = isset($redirect) ? ((!(is_string($redirect) || is_array($redirect))) && $redirect ? array('controller'=>Inflector::tableize($model),'action'=>'ver',':slug') : $redirect) : array(); // Array URL
$opts = isset($opts) ? $opts : array(); // JS Options

$zoom =  $redirect ? false : (isset($zoom) ? $zoom : true); // Se abre en Lightbox?
$rsize = isset($rsize)? $rsize : true;
$w = isset($w) ? $w : false;
$h = isset($h) ? $h : 90;
$min = isset($min) ? $min : 1;
$skip = isset($skip) ? $skip : false;

$data = isset($data) ? $data : Cache::read(strtolower($model).'_slider');

$dinamic = array(); // Contiene los campos que serán reemplazados por item en el FOR
$output = '';

if($data){
	$datasize = sizeof($data);
	if($min && $datasize < $min) return;

	foreach($redirect as $idx => $param){
		if(strpos($param,':')===0){
			$dinamic[$idx] = substr($param,1);
			unset($redirect[$idx]);
		}
	}
	
	for($i=0; $i < $datasize; $i++){
		$src = '';
		$caption = '';
		$it = $data[$i];
		$linkAtts = array();
		$atts = array(
			'id' => 'sgItem_'.$i,
			'class'=>'sgItem'.($i+1==$datasize ? ' omega':'').($zoom ? ' pulsembox' : '')
		);

		/// Adecuación para cuando data es tiene array simple
		if(isset($it[$display])) $it = array($srcmodel => $it);
			
		/// Skip
		if($skip && isset($it[$srcmodel]['portada']) && $it[$srcmodel]['portada'])
			continue;

		//// Title
		if($title && isset($it[$titlemodel][$title]) && $it[$titlemodel][$title]){
			$caption = $it[$titlemodel][$title];
			$linkAtts['name'] = str_replace('"','',_dec($caption));
			$linkAtts['title'] = strip_tags($linkAtts['name']);
		}
			
		/// Src
		if(isset($it[$srcmodel][$display]) && $it[$srcmodel][$display] && file_exists(WWW_ROOT.$it[$srcmodel][$display])){
			$src = $it[$srcmodel][$display];
		} elseif(isset($it[$model.'portada'][$display]) && $it[$model.'portada'][$display] && file_exists(WWW_ROOT.$it[$model.'portada'][$display])){
			$src = $it[$model.'portada'][$display];
		} else
			continue;
		
		if($esFlash = strtolower(strrchr($src,'.'))=='.swf'){
			$newSize = array();
			if($rsize){ $newSize = $util->resize($src,0,$h); }
			$th = $util->swf($src,$newSize);
			
		} else {
			if($rsize){
				$th = $this->Resize->resize($src,array('h'=>$h));
			} else {
				$th = $html->image('/'.$src,array('alt'=>basename($src)));
			}
		}

		//// URL
		$url = false;
/*		
		if($zoom){
			if(!$esFlash){
				$linkAtts['rel'] = $id;
				$url = '/'.$src;
			}
		} else {
			if($enlace && isset($it[$model][$enlace]) && $it[$model][$enlace]){
				$url = $enlace;
				$linkAtts['target'] = '_blank';
				
			} elseif($redirect) {
				$url = $redirect;
				
				foreach($dinamic as $idx => $param){
					$dinamicmodel = $model;
					if(strpos($param,'.')!== false){
						$dinamicmodel = strtok($param,'.');
						$param = strtok('.');
					}
					
					if(isset($it[$dinamicmodel][$param]) && $it[$dinamicmodel][$param])
						$url[$idx] = $it[$dinamicmodel][$param];
				}
			}	
		}
*/
		
		if($enlace && isset($it[$model][$enlace]) && $it[$model][$enlace]){
			$url = $enlace;
			$linkAtts['target'] = '_blank';
			
		} elseif($redirect) {
			$url = $redirect;
			
			foreach($dinamic as $idx => $param){
				$dinamicmodel = $model;
				if(strpos($param,'.')!== false){
					$dinamicmodel = strtok($param,'.');
					$param = strtok('.');
				}
				
				if(isset($it[$dinamicmodel][$param]) && $it[$dinamicmodel][$param])
					$url[$idx] = $it[$dinamicmodel][$param];
			}
		} elseif($zoom){
			if(!$esFlash){
				$linkAtts['class'] = $atts['class'].' pulsembox';
				$linkAtts['rel'] = $id;

				/*
				if(isset($it[$titlemodel]['descripcion']) && $it[$titlemodel]['descripcion']){
					$linkAtts['name'] = _dec($it[$titlemodel]['descripcion']);
					$linkAtts['title'] = strip_tags($linkAtts['name']);
				}
				*/
				
				$url = '/'.$src;
			}
		}
		
		/////
		
		if($caption){
			if($zoom)
				$caption = '';
			else
				$caption = $html->tag('span',$caption,array('class'=>'caption'));
		}
		
		/////

		if($element && file_exists(VIEWS.'elements'.DS.$element.'.ctp')){
			$output.= $this->element($element,am(compact('src','th','url','atts','linkAtts','esFlash'),array('item'=>$it,'idx'=>$i)));
		} else {
			if($url){
				$output.= $html->link($th.$caption,$url,am($atts,$linkAtts));
			} else {
				$output.= $html->div(null,$th.$caption,$atts);
			}
		}
	}
	
	echo $html->div(null,$output,array('id'=>$id)), $moo->slider($id,(array)$opts), $moo->pbox();
}
?>