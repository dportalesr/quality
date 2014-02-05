<?php
$steps = empty($steps) ? 2 : $steps;
echo $html->tag('ul',null,'form_nav st'.$steps);
for($i=1;$i<3;$i++){
	echo $html->tag('li',$html->link('Paso '.$i,array('paso'=>$i)),$i == $form_step ? 'selected':'');
}
echo '</ul>';
?>