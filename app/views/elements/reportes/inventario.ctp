<?php
echo
	$html->div('clear report_controls'),
		$this->element('reportes/inp/stock'),
		$this->element('reportes/inp/orden'),
		$this->element('reportes/inp/items'),
	'</div>';
?>