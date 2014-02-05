<?php
$url = array('controller'=>'products','action'=>'ver','id'=>$item['Product']['slug']);
$item_nombre = $html->para(null,$item['Product']['nombre'].(empty($item['Type']['nombre']) ? '':' ('.$item['Type']['nombre'].')'));

$th =	$util->th($item,'Product',array('w'=>90,'h'=>64,'fill'=>true)).
		$html->div('item_qty',$item['qty'].' x ').
		$html->div('item_nombre',$item_nombre);

echo $html->tag('li',$html->link($th,$url));
?>