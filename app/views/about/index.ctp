<?php
echo $this->element('service_list');
if($item)
	echo $html->div('about_portada_wrapper',$util->th($item,$_m[0],array('w'=>950,'h'=>424,'fill'=>true,'class'=>'about_portada')));

echo $this->element('top');

if($item)
	echo $html->div('desc tmce',$item[$_m[0]]['descripcion'].'');
?>
</div>
</div>
<?php echo $this->element('sidebar'); ?>