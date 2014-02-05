<?php
echo
	$form->input('rango',array(
		'options'=>array(
			'siempre'=>'Siempre',
			'ultimo_dia'=>'Último Día',
			'ultima_semana'=>'Última Semana',
			'ultimo_mes'=>'Último Mes',
			'personalizado'=>'Personalizado',
		),
		'type'=>'radio',
		'default'=>'ultima_semana',
		'div'=>'report_rango',
		'before'=>$html->div('radio_opt'), 'separator'=>'</div>'.$html->div('radio_opt'), 'after'=>'</div>'.$form->input('finicio',array('class'=>'datepicker','label'=>false,'placeholder'=>'Fecha inicio')).$form->input('ffin',array('class'=>'datepicker','label'=>false,'placeholder'=>'Fecha fin'))
	));

	$script = 'var inp_fechas = $$("#ReportFinicio, #ReportFfin"); if($("ReportRangoPersonalizado").get("checked")){ inp_fechas.set("disabled","").reveal(); } else { inp_fechas.dissolve().set("disabled","disabled"); }';
	$moo->buffer($script);
	$moo->addEvent(".report_rango input[type=radio]","click",$script,array('css'=>1));
?>