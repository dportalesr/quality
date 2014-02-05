<?php
	echo $html->link($html->image('http://assets.pinterest.com/images/PinExt.png',array('alt'=>'Pin It')),urla,array('class'=>'pin-it-button','title'=>'Pin It','count-layout'=>'horizontal')),
	$html->script('http://assets.pinterest.com/js/pinit.js');
?>