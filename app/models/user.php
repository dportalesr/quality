<?php
class User extends AppModel {
	var $name = 'User';
	var $displayField = 'username';
	var $labels = array(
		'username'=>'nombre de usuario',
		'password'=>'contraseña'
	);
	function beforeSave(){
		$this->_encriptpass($this->data);
		return true;
	}
}
?>