<?php
echo
	$html->div('cart_wrapper'),
		$html->div('pad'),
			$html->div('cart'),
				#----------------------------

				$html->link('Ver Carrito',array('controller'=>'products','action'=>'checkout'),array('class'=>'view_cart')),
				$html->div(null,$this->element('cart_list'),array('id'=>'cart_list_wrapper')),

				#----------------------------
			'</div>',
		'</div>',
	'</div>';
?>