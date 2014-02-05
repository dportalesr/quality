<?php
echo $html->tag('ul',null,'form_nav st4');
foreach($report_types as $rtype => $rtype_label){
	echo $html->tag('li',$html->link($rtype_label,array($rtype)),$rtype == $this->passedArgs[0] ? 'selected':'');
}
echo '</ul>';
?>