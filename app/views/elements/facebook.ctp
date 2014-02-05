<?php
$box = isset($box) ? $box : false;
$mode = isset($mode) && $mode ? $mode : 'h';

if(!isset($url))
	$url = Router::url($this->here,true);
elseif((!is_string($url)) || strpos($url, 'http://')===false)
	$url = Router::url($url,true);

$w = isset($w) && $w ? $w : (strtolower($mode) == 'h' ? 120 : 70);
$h = isset($h) && $h ? $h : (strtolower($mode) == 'h' ? 21 : 65);

if($box)
	$options = '&amp;show_faces=true&amp;border_color='.urlencode('#fff').'&amp;stream=false&amp;header=false';
else
	$options = '&amp;show_faces=false&amp;action=like&amp;send=true&amp;layout='.($mode == 'h' ? 'button':'box').'_count';

$options.= '&amp;width='.$w.'&amp;height='.$h.'&amp;colorscheme=light&amp;font=tahoma';

?>
<div class="facebook">
<iframe
	src="http://facebook.com/plugins/like<?=$box ? 'box':''?>.php?href=<?=urlencode($url).$options?>"
	scrolling="no"
	frameborder="0"
	style="border:none; overflow:hidden;display:block;margin-bottom:0;"
	allowTransparency="true"
	width="<?php echo $w; ?>"
	height="<?php echo $h; ?>"
></iframe>
</div>