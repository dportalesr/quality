<?php
echo
	$this->element('adminhdr',array('title'=>'Sección '.$_ts)),
	$this->element('inputs'),
	$this->element('tinymce',array('size'=>'l','advanced'=>1));
?>