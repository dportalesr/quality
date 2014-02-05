<?php
echo
	$form->input('modo',array(
		'options'=>array('mas'=>'Más vendido','menos'=>'Menos vendido'),
		'type'=>'radio',
		'default'=>'mas',
		'before'=>$html->div('radio_opt'), 'separator'=>'</div>'.$html->div('radio_opt'), 'after'=>'</div>'
	));
?>