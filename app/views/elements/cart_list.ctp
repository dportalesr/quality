<?php
$cart_items = $this->Session->read('cart.items');
$cart_total = $qty = 0;
$cart_list = '';

if($cart_items){
	foreach ($cart_items as $item) {
		$qty+= $item['qty'];
		$cart_total+= $item['qty']*(empty($item['Type']['precio']) ? $item['Product']['precio'] : $item['Type']['precio']);
		$cart_list.= $this->element('th_cart',compact('item'));
	}
} else 
	$cart_list = $html->tag('li','El carrito está vacío.','noresults');

echo
	$html->div('clear totals'),
		$html->para('cart_amount','$'.$html->tag('span',number_format($cart_total,2),array('id'=>'cart_amount'))),
		$html->para('cart_qty',$html->tag('span',$qty,array('id'=>'cart_qty')).' elementos'),
	'</div>',
	
	$html->div('cart_items_wrapper',$html->tag('ul',$cart_list,array('id'=>'cart_list')));

?>