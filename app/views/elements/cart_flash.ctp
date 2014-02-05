<?php
$class = empty($class) ? 'warning':$class;
if(!empty($cart_flash)){
	echo
		$html->div($class,_enc($cart_flash),array('id'=>'flashMessage')),
		$moo->pop();
}
?>