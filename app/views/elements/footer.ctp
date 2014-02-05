<?php
echo
	$html->div('footer'),
	$html->div('center'),
		$html->link('PULSEM : Web + Identidad + Consultoría','http://pulsem.mx',array('id'=>'pulsem')),
		$html->para(null,Configure::read('Site.name').' &copy; '.date('Y'),array('id'=>'copyright'));
?>
</div><!-- .center -->
</div><!-- .footer -->