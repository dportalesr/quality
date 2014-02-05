<?php
class Post extends AppModel {
	var $name = 'Post';
	var $labels = array(
		'comment_count'=>'comentarios',
		'postimg_count'=>'Imágenes'
	);
	var $hasMany = array(
		'Comment'=>array(
			'className'=>'Comment',
			'foreignKey'=>'parent_id',
			'conditions'=>array('parent'=>'Post'),
			'dependent'=>true
		),
		'Postimg'=>array(
			'className'=>'Postimg',
			'order'=>'Postimg.orden asc',
			'dependent'=>true
		)
	);
	var $hasOne = array(
		'Postportada'=>array(
			'className'=>'Postimg',
			'foreignKey'=>'post_id',
			'conditions'=>'Postportada.portada = 1'
		)
	);
	var $validate = array();
}
?>