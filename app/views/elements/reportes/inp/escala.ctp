<?php
echo
	$form->input('escala',array(
		'options'=>array(
			'dias'=>'Días',
			'semanas'=>'Semanas',
			'meses'=>'Meses',
		),
		'type'=>'radio',
		'default'=>'dias',
		'div'=>'report_escala',
		'before'=>$html->div('radio_opt'), 'separator'=>'</div>'.$html->div('radio_opt'), 'after'=>'</div>'
	));
?>