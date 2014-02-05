<?php
$mode = isset($mode) && $mode ? $mode : 'h';

if(!isset($url))
	$url = Router::url($this->here,true);
elseif((!is_string($url)) || strpos($url, 'http://')===false)
	$url = Router::url($url,true);

$g_config = array('data-size'=>'medium','data-annotation'=>'inline','data-href'=>$url);
if($mode == 'v'){ $g_config = array('data-size'=>'tall'); }
$g_config['data-width'] = 120;

echo $html->div('gplus',$html->div('g-plusone','',$g_config));
