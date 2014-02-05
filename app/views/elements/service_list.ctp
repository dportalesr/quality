<?php
if($items = Cache::read('service_recent')){
	echo $html->tag('ul',null,array('class'=>'bulleted','id'=>'service_list'));

	foreach($items as $slug => $nombre){
		echo $html->tag('li',$html->link($nombre,array('controller'=>'services','action'=>'index','#service_'.$slug)));
	}

	echo '</ul>';
}
?>