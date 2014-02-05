<?php
$mode = isset($mode) && $mode ? $mode : 'h';

if(!isset($url))
	$url = Router::url($this->here,true);
elseif((!is_string($url)) || strpos($url, 'http://')===false)
	$url = Router::url($url,true);

echo
	$html->div('share');
	if(!Configure::read('debug')){
		echo
			$this->element('facebook',compact('mode','url')),
			$this->element('twitter',compact('mode','url')),
			$this->element('gplus',compact('mode','url'));
	}
	echo '</div>';
?>