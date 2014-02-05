<?php
echo
	$html->div(null,null,array('id'=>'product_list')),
		$html->div('title','Productos'),
		$html->link('Agregar Producto','javascript:;',array('id'=>'elistAdder')),
		$this->element('elist',array(
			'model'=>'Product',
			'fields'=>array(
				'id' => array('edit'=>1,'class'=>'prod_id','div'=>'hide'),
				'nombre' => array('edit'=>1,'class'=>'prod_nombre'),
			),
			'options'=>array('sort'=>1,'remover'=>1,'adder'=>'elistAdder', 'oncreate'=>'var tmp = newelistitem.getElement(".prod_id").get("id"); new mooSuggest(tmp,tmp.replace("Id","Nombre"),"/products/suggest");'),
			'atts'=>array('id'=>'Product_elist')
		)),
	'</div>';

	$moo->suggest();
	$moo->buffer('$$(".prod_id").each(function(el){ var tmp = el.get("id"); new mooSuggest(tmp,tmp.replace("Id","Nombre"),"/products/suggest"); });');
?>