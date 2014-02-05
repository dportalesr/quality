<?php
echo
	$this->element('adminhdr',array('links'=>array('back'))),
	$this->element('inputs',array(
		'schema'=>array(
			'passwordc'=>array(
				'afterof'=>'password',
				'type'=>'password',
				'label'=>'Repetir Contraseña:'
			)
		)
	));
?>