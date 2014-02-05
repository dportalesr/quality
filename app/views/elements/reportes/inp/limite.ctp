<?php
echo
	$form->input('limite',array(
		'type'=>'select',
		'legend'=>'Limitar a',
		'options'=>array(5=>5,10=>10,15=>15,20=>20,30=>30,50=>50),
		'default'=>'10',
		'type'=>'radio',
		'before'=>$html->div('radio_opt'), 'separator'=>'</div>'.$html->div('radio_opt'), 'after'=>'</div>'
	));
?>