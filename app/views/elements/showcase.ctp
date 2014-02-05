<?php
$id = empty($id) ? 'carousel' : $id;
$model = empty($model) ? 'Carousel' : $model;
$size = empty($size) ? false : explode('x',$size);
$url = empty($url) ? false : $url;
$conditions = empty($conditions) ? array() : $conditions;
$defaults = array('nav'=>'out');
$opts = empty($opts) ? $defaults : array_merge($defaults,$opts);

if($data = empty($data) ? Cache::read(strtolower($model).'_showcase') : $data){

	echo $html->div('showcase',null,array('id'=>$id));
	
	foreach($data as $snap){
		$class = '';
		$it = $model && isset($snap[$model]) ? $snap[$model] : $snap;

		if($url === true){
			$it['enlace'] = '/'.$it['src'];
			$class = ' pulsembox';
		}
		
		if(!empty($it['enlace']))
			echo $html->link(
				$size ? $resize->resize($it['src'],array('w'=>$size[0],'h'=>$size[1])) : $html->image('/'.$it['src']),
				$url && $url !== true ? $url : $it['enlace'],
				array('target'=>'_blank','rel'=>'nofollow','class'=>'item'.$class)
			);
		else
			echo $size ? $resize->resize($it['src'],array('w'=>$size[0],'h'=>$size[1],'atts'=>array('class'=>'item'))) : $html->image('/'.$it['src'],array('class'=>'item'));
		
		$descripcion = '';
		if(!empty($it['descripcion']))
			$descripcion = $it['descripcion'];
		elseif(!(empty($_lang) || empty($it['descripcion_'.$_lang])))
			$descripcion = $it['descripcion_'.$_lang];

		echo $html->div('caption',$descripcion);
	}
	echo '</div>';
	
	$moo->showcase($id,$opts);
}
?>