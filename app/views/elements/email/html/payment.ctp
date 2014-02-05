<?php
echo
	$html->div('',null,array('style'=>'padding:15px;border:#CCC solid 1px;margin:0;margin-top:10px;')),
		$html->tag('h1',$msg[0],array('style'=>'font-weight:normal;font-size:24px;margin:0;margin-bottom:18px;color:#444')),
		$html->para(null,$msg[1]),
		$html->div('',null,array('style'=>'padding:15px;border:#CCC solid 1px;margin:0;margin-top:10px;')),
		'</div>',
	'</div>';
?>