<?php
echo $html->div(null,null,array('id'=>'crumbs')),
	$crumbs = $html->link($_ts,array('action'=>'index','category'=>''));
	
	if(isset($path) && $path){
		foreach($path as $link)
			echo $html->link($link['Category']['nombre'],array('action'=>'index','category'=>$link['Category']['slug']));
	}
	
	echo '</div>';
?>