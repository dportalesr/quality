<script type="text/javascript">
	WebFontConfig = {
		google: { families: ['<?=implode("','",$fonts)?>'] }
	};
</script>
<script src="http<?=isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 's':''?>://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js" type="text/javascript" defer="defer"></script>
<style type="text/css">
body .wf-loading { display:none; }
body .wf-inactive { font-family: serif; }
</style>