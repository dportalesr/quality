<?php
$frontMdl = $_m[0].'front';
$schema = array('activo'=>'skip');

if(isset($this->data[$_m[0]]['parent_nombre'])){
	$schema['parent_id'] = array('div'=>'hide','type'=>'text');
	$schema['parent'] = 'skip';
	$schema['parent_nombre'] = array('label'=>'Artículo relacionado','afterof'=>'parent_id');

	$moo->suggest($frontMdl.'ParentId', $frontMdl.'ParentNombre','/'.Inflector::tableize($_m[0]).'/suggest/');
}

echo
	$this->element('adminhdr',array('title'=>'Imagen Principal de '.$_ts,'links'=>array('back'))),
	$this->element('inputs',array(
		'model'=>$frontMdl,
		'schema'=>$schema
	)),
	$this->element('tinymce',array('advanced'=>1));
?>