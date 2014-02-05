<?php
$fields = isset($actions) && $actions ? $actions : array();

$actions = isset($actions) && $actions ? $actions : array();

echo $html->tableCells(array(array(
	['id'],
	$it[$_m[0]]['nombre'],
	$util->fdate('s',$it[$_m[0]]['created']),
	array(
		$html->link('Ver',array('action'=>'ver','admin'=>0,$id),array('target'=>'_blank')).
		$html->link('Fotos',array('action'=>'images','admin'=>1,$id)).
		$html->link('Editar',array('action'=>'editar','admin'=>1,$id)).
		$html->link('Eliminar',array('action'=>'eliminar','admin'=>1,$id),null,'¿Seguro que quiere eliminar este elemento?')
	,array('class'=>'actions'))
)),array('class'=>'odd'));

?>