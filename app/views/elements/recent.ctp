<?php
$nvp = isset($nvp) && $nvp ? $nvp : false;
$title = isset($title) ? (is_string($title) ? $title : $_ts) : false;
$data = isset($data) && $data ? $data : false;
$model = isset($model) && $model ? $model : (isset($_m[0]) && $_m[0] ? $_m[0] : false);
$params = isset($params) ? $util->named($params) : '';
$class = isset($class) && $class ? $class : '';
$current = false;

if(isset($item) && $item){
	$current = reset($item);
	$current = $current['id'];
}

if($model && ($data || $items = Cache::read(strtolower($model).'_recent'))){
	echo $html->div('bulleted '.$class.' '.$this->params['controller']);
	
	if($title)
		echo $html->div('title title2',$title);

	echo $html->tag('ul');
	
	foreach($items as $idx => $it){
		if($nvp){
			$slug = $idx;
			$nombre = $it;
			$id = (int)$slug;
			
		} else {
			$slug = isset($it[$model]['slug']) ? $it[$model]['slug'] : $it[$model]['id'];
			$nombre = $it[$model]['nombre'];
			$id = $it[$model]['id'];
		}

		$selected = $current == $id ? 'selected':'';

		$url = array('controller'=>Inflector::tableize($model),'action'=>'ver','id'=> $slug);
		echo $html->tag('li',$html->link($nombre,$url),array('class'=>$selected));
	}
		
	echo '</ul></div>';
}
?>