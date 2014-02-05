<?php
echo
	$form->create('Product',array(
		'url'=>array('controller'=>'products','action'=>'add2cart'),
		'id'=>'Add2Cart_'.$data[$_m[0]]['id'],
		'class'=>'add2cart',
		'inputDefaults'=>array('label'=>false)
	)),
	$form->input('id',array('value'=>$data[$_m[0]]['id']));

	if(!empty($data['Type'])){
		$options = array();
		foreach ($data['Type'] as $item_type){
			if(empty($item_type['precio']) && !empty($data[$_m[0]]['precio']))
				$precio = $data[$_m[0]]['precio'];
			else
				$precio = $item_type['precio'];

			if(!empty($precio))
				$precio = ' - $'.number_format($precio,2);

			$label = _dec($item_type['nombre'].$precio);
			
			if(isset($item_type['stock']) && empty($item_type['stock']))
				$options[$label.' ('.ucfirst(__('agotado',true)).')'] = array();
			else
				$options[$item_type['id']] = $label;
		}
		$xoptions = array_filter($options);
		if(empty($xoptions))
			$options = array(''=>ucfirst(__('agotado',true)));

		echo $form->input('type',array('options'=>$options));
	}

	echo
		$form->end('Agregar al Carrito'),
		$this->element('cart_flash');
	
?>