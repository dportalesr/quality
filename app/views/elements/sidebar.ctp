<div class="sidebar">
<div class="pad">
<?php
if(!is_c(array('inicio','about'),$this))
	echo $this->element('service_list');

echo $html->div('banners',$this->element('banners'),array('id'=>'banners')), $moo->showcase('banners',array('nav'=>'out'));
?>
</div>
</div>