<?php
echo
	$html->div('clear report_controls'),
		$this->element('reportes/inp/rango'),
		$this->element('reportes/inp/modo'),
		$this->element('reportes/inp/orden'),
		$this->element('reportes/inp/limite'),
	'</div>';
?>