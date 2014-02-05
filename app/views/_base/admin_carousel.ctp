<?php
echo $this->element('temp/admin_images',array(
	'model'=>$_m[0].'carousel',
	'links'=>array('back'),
	'title'=>'Carrusel '.$_ts
));
?>