<?php
class Carousel extends AppModel {
	var $name = 'Carousel';
	var $labels = array();
	var $actsAs = array(
		'File'=>array(
			'portada'=>false,
			'fields'=>array('src'=>array('maxsize'=>307200,'strict'=>'950 x 424'))
		)
	);
}
?>