<html><body style="padding:0;margin:0;font-family:Tahoma, Geneva, sans-serif;">
<?php
$hostURL = 'http://'.$_SERVER['SERVER_NAME'].'/img';
$basestyle = 'padding:20px;';
$headerimgs = array('emailHdr.png','emailHdr.jpg','logo.png','logo.jpg');
$headerimg = false;
$imgheight = 80;

foreach($headerimgs as $hdr)	{
	if(file_exists(IMAGES.$hdr)){
		if($size = getimagesize(IMAGES.$hdr)){
			$imgheight = $size[1];
			$headerimg = $hdr;
			break;
		}
	}
}

$h1Atts = array('style'=>'background:transparent;color:#666;margin:0;line-height:'.$imgheight.'px;height:'.$imgheight.'px;font-size:40px;font-weight:normal;');

if($headerimg){
	$header = $html->div(null,$html->image($hostURL.'/'.$headerimg,array('alt'=>Configure::read('Site.name'),'style'=>'height:'.$imgheight.'px:display:block;')),$h1Atts);
} else {
	$header = $html->div(null,$html->tag('h1',Configure::read('Site.name'),$h1Atts),array('style'=>$basestyle));
}

echo
	$html->div(null,null,array('style'=>'width:815px;margin:0 auto 30px auto;')),
		$header,
		$content_for_layout,
		$html->para(null,date('d-m-Y H:i:s'),array('style'=>'color:#999;font-size:10px;margin-left:15px;margin-top:6px;')),
	'</div>';
?>
</body></html>