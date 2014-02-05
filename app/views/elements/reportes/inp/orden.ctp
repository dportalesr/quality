<?php
echo
	$html->div('',null),
		$form->input('orden',array(
			'options'=>array(
				'desc'=>'Descendente',
				'asc'=>'Ascendente'
			),
			'type'=>'radio',
			'default'=>'desc',
			'before'=>$html->div('radio_opt'), 'separator'=>'</div>'.$html->div('radio_opt'), 'after'=>'</div>',
			'div'=>'report_orden'
		)),
	'</div>';
?>