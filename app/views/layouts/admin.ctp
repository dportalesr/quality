<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<title>Panel | <?=$title_for_layout?> | <?=$sitename_for_layout?></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<meta name="Title" content="<?=$sitename_for_layout?>" />
<meta name="Author" content="Pulsem" />
<meta name="Generator" content="daetherius" />
<meta name="Language" content="Spanish" /> 
<meta name="Robots" content="noindex,nofollow" />
<?=$html->css(array('generic','admin','pulsembox'))?> 
</head>
<body class="admin">
<?php
echo
	$html->div('nofooter'),
		$html->div(null,null,array('id'=>'header')),
			$html->link('Cerrar Sesión',array('admin'=>1,'controller'=>'users','action'=>'logout'),array('class'=>'logout','title'=>'Finalizar sesión como administrador','escape'=>false)),
			$html->link($html->image('admin/logo.jpg',array('alt'=>$sitename_for_layout)),'/',array('id'=>'logo','title'=>'Ir al Inicio')),
			$html->tag('span','Panel de Administración',array('id'=>'title')),
		'</div>';
			
	if(isset($sAdmin) && $sAdmin){

		echo $html->div('sidebar');
			$modules = Configure::read('Modules');
			foreach($modules as $cntrllr => $mod){
				if(isset($mod['admin']) && $mod['admin']){
					echo $html->link($html->tag('span',ucfirst($mod['admin']['label']), array('class'=>$mod['admin']['class'])),array('controller'=>$cntrllr, 'action'=>'index','admin'=>true),array('class'=>array_key_exists('br', $mod) ? 'withbreak':''));
				}
			}
				
		echo '</div>';
	}

	echo
		$html->div('content'.(isset($sAdmin) && $sAdmin ? ' logged':''), $this->element('flash').$content_for_layout),
		$html->div('cleaner',''),
	'</div>',
	$html->div('footer'),
		$html->link('','http://pulsem.mx',array('id'=>'pulsem','title'=>'Web + Identidad + Consultoría')),
		$html->para('','Web | Identidad | Consultoría'),
	'</div>';
	?>
	  <script src="//ajax.googleapis.com/ajax/libs/mootools/1.4.1/mootools-yui-compressed.js"></script>
	  <script>window.MooTools || document.write('<script src="/js/moo.js"><\/script>')</script>
	<?php
echo
	$html->script(array('moo_m','utils','pulsembox')),
	$scripts_for_layout,
	$moo->writeBuffer(array('onDomReady'=>false)),
	$moo->highlight($highlight,1);
?>
</body>
</html>