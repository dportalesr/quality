<?php
	$selector = isset($selector) ? $selector : false;
	$deselector = isset($deselector) ? $deselector : false;
	$advanced = isset($advanced) ? $advanced : false;
	$size = isset($size) ? $size : 's';
	
	switch($size){
		case 's': $size = '160px'; break;
		case 'm': $size = '270px'; break;
		case 'l': $size = '380px'; break;
		default: break;
	}
	$lipsum = Configure::read('debug') ? ',loremipsum':'';
	
	if($advanced){
	    $options = '
	    plugins : "safari,contextmenu,paste,fullscreen,table'.$lipsum.'",
	    theme_advanced_statusbar_location : "bottom",
	    theme_advanced_path : true,
	    theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,undo,redo,link,unlink,|,cleanup,removeformat,|,fullscreen",
	    theme_advanced_buttons2 : "bold,italic,underline,forecolor,|,formatselect,fontselect,fontsizeselect,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,sub,sup,cite,|,code'.$lipsum.'",
	    theme_advanced_buttons3 : "tablecontrols",';
	} else {
	    $options = 'plugins: "safari,paste'.$lipsum.'",
	    theme_advanced_buttons1 : "bold,italic,underline,link'.$lipsum.',|,pastetext,pasteword",
	    theme_advanced_buttons2 : "",
	    theme_advanced_buttons3 : "",';
	}

    $options.=
		($selector ? 'editor_selector: "'.$selector.'",':'').
		($deselector ? 'editor_deselector: "'.$deselector.'",':'').
		'height:"'.$size.'" ,
		theme : "advanced",
		mode : "textareas",
		theme_advanced_blockformats : "h1,h2,h3,p,div,blockquote,code",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		extended_valid_elements : "a[href|target|title],img[class|src|alt|title|width|height|align|name]",
		content_css : "/css/generic.css?'.(time()).',/css/main.css?'.(time()).'",
		body_class : "tmce",
		theme_advanced_font_sizes: "10px=10px,11px=11px,12px=12px,14px=14px,16px=16px,18px=18px,20px=20px,24px=24px,28px=28px,32px=32px,36px=36px",
		language : "es",
		width:"100%"';
	
	$html->script('tinymce/tiny_mce.js',false);
	$moo->buffer('tinyMCE.init({'.$options.'});',array('inline'=>false));
?>