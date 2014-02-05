<?php
echo
	$this->element('adminhdr',array('links'=>array('back'))),
	$this->element('inputs',array(
		'schema'=>array(
			'password'=>array('before'=>$html->div('label warning','Esta parte es opcional. Escriba una contraseña nueva para cambiarla.')),
			'passwordc'=>array(
				'afterof'=>'password',
				'type'=>'password',
				'label'=>'Repetir Contraseña:'
			)
		)
	));
?>