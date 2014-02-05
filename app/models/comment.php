<?php
class Comment extends AppModel {
	var $name = 'Comment';
	var $displayField = 'email';
	var $belongsTo = array(
		'Post' => array(
			'className'=>'Post',
			'foreignKey'=>'parent_id',
			'conditions'=>array('Comment.parent'=>'Post'),
			'counterCache' => true
		)
	);
	var $validate = array(
		'mail' => array(
			'rule'=>'blank',
			'allowEmpty'=>true,
			'on'=>'create',
			'required'=>true,
			'message'=>'Non-Human'
		),
		'autor' => array(
			'rule'=>'notEmpty',
			'allowEmpty'=>false,
			'message'=>'Ingrese su nombre por favor'
		),
		'descripcion' => array(
			'rule' => 'notEmpty',
			'allowEmpty' => false,
			'message' => 'Ingrese su comentario'
		)			
	);

	function beforeValidate(){ $this->clean($this->data,false,false);return true; }
}
?>