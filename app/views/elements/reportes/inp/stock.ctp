<?php
echo
	$html->div('',null),
		$form->input('stock',array(
			'options'=>array(
				'cualquiera'=>'Cualquiera',
				'minimo'=>'Su mínimo',
				'mayor'=>'Mayor o igual que...',
				'menor'=>'Menor o igual que...',
				'igual'=>'Igual a...'
			),
			'div'=>'radio_stock',
			'type'=>'radio',
			'default'=>'cualquiera',
			'before'=>$html->div('radio_opt'), 'separator'=>'</div>'.$html->div('radio_opt'), 'after'=>'</div>'
		)),
		$form->input('stock_qty',array('value'=>'','type'=>'text','label'=>false)),
	'</div>';

	$script = '
		var stock_mode = $$(".radio_stock input[type=radio]:checked")[0].get("value");
		var stock_qty = $("ReportStockQty");

		if(["menor","mayor","igual"].contains(stock_mode)){
			stock_qty.set("disabled", "").reveal();
		} else {
			stock_qty.set("disabled", "disabled").dissolve();
		}
	';
	$moo->buffer($script);
	$moo->addEvent('.radio_stock input[type=radio]','click',$script,array('css'=>true));
?>