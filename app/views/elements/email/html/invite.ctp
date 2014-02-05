<div style="padding:15px;color:#333;margin-top:1px;border:#666 solid 1px;">
	<h1 style="font-weight:normal;font-size:24px;margin-bottom:8px;">¡Hola, <?=$nombre_para?>!</h1>
	<p>Has recibido este correo porque <?=$nombre_de?> (<a href="mailto:<?=$email_de?>"><?=$email_de?></a>)</span> quiere que visites el sitio de <a style="font-weight:bolder;" href="http://<?=$domain?>" target="_blank"><?=$business?></a>.</p>
	<? if(!empty($mensaje)){ ?>
		<p style="font-weight:bolder"><?=$nombre_de?> dijo:</p>
		<p style="padding:10px;background:#e3e3e3;"><?=$mensaje?></p>
	<? } ?>
</div>