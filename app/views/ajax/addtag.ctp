<?php
echo $form->input('Tag.addTag.'.$addTagCount,array(
	'label'=>_enc($tag),
	'value'=>$tag,
	'checked'=>true,
	'type'=>'checkbox',
	'hiddenField' => false
)).'<span> </span>';
?>