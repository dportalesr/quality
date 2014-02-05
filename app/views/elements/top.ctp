<?php
$wide = empty($wide) ? '':'wide';
$centered = empty($centered) ? false : $html->div('centered');

echo
	$centered,
		$html->div('content'.$wide),
			$html->div('pad');
?>