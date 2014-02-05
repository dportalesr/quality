<?php
class Postimg extends AppModel {
	var $name = 'Postimg';
	var $actsAs = array('File' => array('portada'=>'post_id'));
	var $belongsTo = array(
		'Post' => array(
			'className'=>'Post',
			'counterCache' => true
		)
	);
	
}
?>