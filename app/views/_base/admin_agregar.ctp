<?php
echo
	$this->element('adminhdr',array('links'=>array('back'))),
	$this->element('inputs'),
	$this->element('tinymce',array('advanced'=>1,'size'=>'m'));
?>