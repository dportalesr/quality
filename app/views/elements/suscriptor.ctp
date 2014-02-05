<?
echo $form->create('Suscriptor',array('url'=>'/suscriptors/suscribir','id'=>'suscriptorForm')),
	$html->image('icoSuscriptor.png'),
	$html->tag('h2','Suscríbete al boletín'),
	$form->input('name',array('div'=>'hide')),
	$html->div('suscriptorInput',
		$form->input('email',array('label'=>false,'div'=>false)).
		$form->submit('minisubmit.gif',array('div'=>false)).
		$html->para(null,'',array('id'=>'suscribeMsg'))
	),
	$form->end();

	$moo->buffer('$("suscribeMsg").slide("hide");');
	$moo->addEvent('suscriptorForm','submit',array('data'=>'"suscriptorForm"','url'=>'/suscriptors/suscribir','update'=>'"suscribeMsg"','prevent'=>1,'oncomplete'=>'$("suscribeMsg").slide("in");'));

?>