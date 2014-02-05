<?php
echo
	$html->div('invite'),
		$html->div('title title1','Recomienda nuestro sitio'),
		$html->para('note','* Campos obligatorios'),
		$form->create('Invitacion',array('url'=>'/inicio/invitar','id'=>'InvitacionForm')),
			$html->div('subform'),
				$html->div('clear'),
					$html->div('inviteCol'),
						$html->para('inviteLabel','Enviado por:'),
						$form->input('nombre_de',array('label'=>'* Tu Nombre:')),
						$form->input('email_de',array('label'=>'* Tu Email:')),
					'</div>',
					
					$html->div('inviteCol omega'),
						$html->para('inviteLabel','Dirigido a:'),
						$form->input('nombre_para',array('label'=>'* Nombre:')),
						$form->input('email_para',array('label'=>'* Email:')),
					'</div>',
				'</div>',
				$form->input('mensaje',array('label'=>'Mensaje (opcional):','type'=>'textarea')),
				$form->submit('Enviar',array('class'=>'btSubmit')),
			'</div>',
		$form->end(),
	'</div>',
	
	$moo->ajaxform('InvitacionForm'),
	$moo->writeBuffer(array('onDomReady'=>false));
?>