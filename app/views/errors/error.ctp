<div class="error_info">
	<div class="title title1"><img src="/img/admin/error.png" alt="ERROR"/><span>¡Vaya, algo salió mal!</span></div>
	<p class="title title3">Es posible que ésta página haya sido movida, eliminada o que realmente nunca haya <a href="javascript:;" title="Cha-chaaaaam!">existido</a>.</p>
	<p><a href="javascript:history.back();">Regrese a la página anterior</a>, o bien puede <a href="/">Ir a la página de inicio del sitio</a>.</p>
	<p><a id="seeDetails" href="javascript:;">Detalles</a></p>
	<div id="details">
		<p>Tipo de error: <strong><?php echo $type; ?></strong></p>
		<pre><?php print_r($params); ?></pre>
	</div>
</div>
<script type="text/javascript" src="js/moo13.js"></script>
<script type="text/javascript" src="js/moo13m.js"></script>
<script type="text/javascript">
	$('details').fade('hide');
	$('seeDetails').addEvent('click',function(){ $('details').fade('toggle'); });
</script>