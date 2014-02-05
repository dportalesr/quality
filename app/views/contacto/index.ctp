<?php
echo
	$this->element('top',array('wide'=>true,'header'=>true)),
	$html->div('sectionHdr'),
		$html->div('desc contact_intro'),
			$html->tag('h2','Cobertura en toda<br/>la República Mexicana','title'),
			$html->para(null,'Rutas frecuentes:'),
			$html->tag('ul'),
				$html->tag('li','Guadalajara'),
				$html->tag('li','México'),
				$html->tag('li','Querétaro'),
				$html->tag('li','Campeche'),
				$html->tag('li','Chetumal'),
				$html->tag('li','Mérida'),
				$html->tag('li','Cancún, Quintana Roo'),
			'</ul>',
		'</div>',
		$html->div('','',array('id'=>'mapa')),
	'</div>',
	$html->div('clear contact_content'),
		$html->div('form'),
			$html->div('title title2','Contacto'),
			$html->para('note','¿Necesita más información? Póngase en contacto con nosotros y con gusto le responderemos.'),
	
			$form->create('Contact',array('id'=>'ContactForm','url'=>'/contacto/enviar')),
			$form->input('mail',array('div'=>'hide')),
			$html->div('subform'),
				$this->element('inputs',array(
					'formtag'=>false,
					'end'=>'Enviar',
					'after'=>$this->Captcha->input().$html->para('leydatos','Sus datos serán usados de acuerdo a los términos de la '.$html->link('Ley Federal de Protección de Datos Personales','http://dof.gob.mx/nota_detalle.php?codigo=5150631&fecha=05/07/2010',array('target'=>'_blank','rel'=>'nofollow'))),
					'schema'=>array(
						'servicio'=> array('options'=>$services_list)
					)
				)),
			'</div>',
		'</div>',
		$html->div('info'),
			$html->para('phone','Tel. (999) 9-41-55-83 <br/> Cel. (999) 9-00-33-83'),
			$html->para('email',$util->ofuscar(Configure::read('Site.email'))),
			$html->para('address','Calle 69 No. 251 x 44 y 46<br/>Fracc. Unidad Revolución Cordemex<br/>CP. 97110. Mérida, Yucatán, México.'),
		'</div>',
	'</div>',
	$this->element('sidebar'),
	
	$html->script('http://maps.google.com/maps/api/js?sensor=false'),
	$moo->buffer('var latLong = new google.maps.LatLng(21.0382304,-89.6263368);
	var map = new google.maps.Map(document.getElementById("mapa"), { zoom: 16, center: latLong, mapTypeId: google.maps.MapTypeId.ROADMAP });
	var beachMarker = new google.maps.Marker({
		position: latLong,
		map: map,
		icon: "/img/marker.png"
	});');

	$moo->ajaxform('ContactForm');
?>
</div>
</div>