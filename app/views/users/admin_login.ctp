<?php
echo $form->create('User',array('id'=>'UserLoginForm')),
	$form->input('username',array('label'=>'Usuario:')),
	$form->input('password',array('label'=>'Contraseña:')),
	$form->submit('Entrar'),
	$form->end();
?>
