<?php
class About extends AppModel {
	var $name = 'About';
	var $useTable = 'about';
	var $skipValidation = array('src');
	var $labels = array('src'=>'Imagen Nosotros');
	var $actsAs = array('File' => array('portada'=>false,'fields'=>array('src'=>array('strict'=>'950 x 424','maxsize'=>409600))));
}
?>