<?php
$successmsg = empty($successmsg) ? false : $successmsg;
$model = ucfirst(empty($model) ? $_m[0] : $model);
$fid = empty($fid) ? false : $fid;
?>
<script type="text/javascript">
	var tmp_ajaxform = <?=$fid ? $fid.'_af':'false'?>;
	/* Revertir/Remover mensajes de error de Form para revalidar */
	formtips.detach('.input_error');
	$$('.input_error').removeClass('input_error');

	var invalidscrolled = false;
	<?
	if($successmsg){ # Success
		if($fid){ ?> tmp_ajaxform.success('<?=$successmsg?>'); <? }
	} else { # Failure
		$keys = array_keys($errors);
		
		# Sub modelo
		if(is_array($errors[$keys[0]])){
			$model = $keys[0];
			$errors = $errors[$keys[0]];
		}
		
		foreach($errors as $campo => $msgerror){
			$input_id = $model.Inflector::camelize($campo); ?>
			
			if($('<?=$input_id?>')){
				if(!invalidscrolled) //Scroll al primer input en marcar error
					invalidscrolled = new Fx.Scroll(window,{ duration:600,transition:Fx.Transitions.Quint.easeInOut }).toElement($("<?=$input_id?>").getParent('form'));
				//CommentCuerpo_ifr
				tip = $('<?=$input_id?>_parent') ? $('<?=$input_id?>_parent') : $('<?=$input_id?>');
				
			} else
				alert('No se encontró el campo en el formulario: <?=$input_id?>');

			if(tip.get('type')=='checkbox')
				tip = tip.getParent('.checkbox');

			tip.addClass('input_error');
			tip.store('tip:text', "<?=htmlspecialchars($msgerror,ENT_QUOTES,'UTF-8')?>");
			formtips.attach(tip);
	<? } ?>
		if(tmp_ajaxform)
			tmp_ajaxform.failure();
	<? } ?>
</script>