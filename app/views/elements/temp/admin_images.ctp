<?php
$model = isset($model) ? $model : $_m[0].'img';
$filespec = array();
App::import('Model',$model);
$m = new $model();
$filesets = $m->Behaviors->File->settings[$m->alias]['fields']['src'];
$filespec[] = '<span>Peso máximo recomendado: <strong style="color:#ff0">'.(btop($filesets['maxsize'])).'</strong></span>';
$filespec[] = '<span style="font-size:10px;color:#9f9f9f">Tipos permitidos: '.implode(', ',array_map('strtoupper',$filesets['types'])).'</span>';

if($filestrict = isset($strict) ? $strict : $filesets['strict'])
	$filestrict = $html->para('legend','Las medidas de las imágenes a subir deben ser de '.$html->tag('span',$filestrict).' para una correcta visualización');

echo
	$this->element('adminhdr',array(
		'filtro' => false,
		'title' => isset($title) && $title ? $title : false,
		'links' => isset($links) ? $links : array('back','edit')
	)),

	$moo->moopload($model,10,'Src',sizeof($items)),

	(isset($itemtitle) ? $html->tag('h2',$itemtitle):''),
	$this->element('pages',array('model'=>$model)),

	$form->create($_m[0],array('url'=>$this->here,'type'=>'file')),

	$this->element('gallery',array_merge(array('snaps'=>$items,'model'=>$model,'m'=>$m))),

	$html->div('uploadImages'),
		$filestrict,
		$html->tag('h2','Cargar Fotos'.$util->tip(array(implode('<br/>',$filespec),'Especificaciones de Archivo'))),
		$form->input($model.'.{n}.src', array('type'=>'file','label'=>false)),
		$form->submit('Guardar Cambios'),
		isset($this->passedArgs[0]) && is_numeric($this->passedArgs[0]) ? $form->input('id',array('value'=>$this->passedArgs[0],'type'=>'hidden')):'',
	'</div>',

	$form->end(),
	$moo->pbox();
?>