<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<title><?=$title_for_layout?></title>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="Title" content="<?=Configure::read('Site.name')?>" />
<meta name="Author" content="pulsem.mx" />
<meta name="Generator" content="daetherius" />
<meta name="Language" content="Spanish" /> 
<meta name="Robots" content="Index" />
<?=$html->css(array('generic','main'))?> 
</head>
<?php
echo
	$html->tag('body',null,'error'),
		$html->div('error_page'),
			$html->tag('h1',$html->link(Configure::read('Site.name'),'/',array('title'=>Configure::read('Site.name'))),array('id'=>'logo')),
			$content_for_layout,
			$html->script(array('moo13','moo13m')),
		'</div>',
	'</body>';
?></html>