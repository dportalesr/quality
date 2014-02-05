<?php
$field = 'src';
$model = empty($model) ? $_m[0] : $model;
$header = empty($header) ? false : $header;
$atts = empty($atts) ? array() : $atts;
$grow = empty($grow) ? false : $grow;
$rel = empty($rel) ? '' : $rel;

if(isset($data) && $data){
	echo $html->div('inlineGallery',null,$atts);
	
	if($header)
		echo $html->tag('h1',$header,'pTitle');
	
	foreach($data as $it){
		$desc = $src = '';
		
		if(isset($it[$model]))
			$it = $it[$model];
		
		if(isset($it[$field]))
			$src = $it[$field];
		
		if(isset($it['descripcion']) && $it['descripcion']){
			$desc = $it['descripcion'];
			$desc_raw = $util->txt($desc,1);
		}
		
		if($src && file_exists(WWW_ROOT.$src)){
			if($grow){
				$rel_th = $resize->resize($src,array('h'=>180,'urlonly'=>true));
				$rela = '';
				$class = '';
				$href = 'javascript:;';
			} else {
				$rel_th = '';
				$rela = 'pbox';
				$class = ' pulsembox';
				$href = '/'.$src;
			}

			$rela.= $rel;
			
			echo $html->link($resize->resize($src,array('w'=>206,'h'=>180,'pad'=>true,'atts'=>array('alt'=>$desc_raw,'rel'=>$rel_th))),$href,array('class'=>'inlineGal'.$class,'rel'=>$rela,'name'=>$desc,'title'=>$desc_raw));
		}
	}
	
	echo '</div>';
		
}
?>