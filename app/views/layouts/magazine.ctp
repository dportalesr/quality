<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?=$title_for_layout?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link type="text/css" href="/css/magazine.css" rel="stylesheet" />
	<script type="text/javascript" src="/js/swfobject.js"></script>
	<script type="text/javascript" src="/js/swfaddress.js"></script>
	<script type="text/javascript" src="/megazine/megazine.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		var flashvars = {
			basePath: "/megazine/",
			xmlFile: "/magazines/xml/<?=$mid?>"
		};
		var params = {
			menu: "false",
			/* Necessary for proper scaling of the content. */
			scale: "noScale",
			/* Necessary for fullscreen mode. */
			allowFullscreen: "true",
			/* Necessary for SWFAddress and other JavaScript interaction. */
			allowScriptAccess: "always",
			/* This is the background color used for the Flash element. */
			bgcolor: "#333333"
		};
		var attributes = {
			/* This must be the same as the ID of the HTML element that will contain the Flash element. */
			id: "megazine"
		};
		/* Actually load the Flash. */
		swfobject.embedSWF("/megazine/preloader.swf", "megazine", "100%", "100%", "9.0.115", "/js/expressInstall.swf", flashvars, params, attributes);
		//]]>
	</script>
</head>
<body>
	<div id="megazine">
		<h1>MegaZine3 requires FlashPlayer 9</h1>
		<p><a href="http://get.adobe.com/flashplayer/"><img src="http://www.adobe.com/images/shared/download_buttons/get_adobe_flash_player.png" alt="Get Adobe Flash Player"/></a></p>
		<p>Please try the above link first. If you still encounter problems after installing the Flash Player, try this one:</p>
		<p><a href="http://get.adobe.com/shockwave/"><img src="http://www.adobe.com/images/shared/download_buttons/get_adobe_shockwave_player.png" alt="Get Adobe Shockwave Player"/></a></p>
		<p><a href="http://www.megazine3.de/">Powered by MegaZine3</a></p>
	</div>
</body>
</html>