<?php
$box = isset($box) ? $box : false;
$mode = isset($mode) && $mode ? $mode : 'h';
$title = isset($title) && $title ? _dec($title) : false;

if(!isset($url))
	$url = Router::url($this->here,true);
elseif((!is_string($url)) || strpos($url, 'http://')===false)
	$url = Router::url($url,true);


$w = isset($w) && $w ? $w : (strtolower($mode) == 'h' ? 120 : 75);
$h = isset($h) && $h ? $h : (strtolower($mode) == 'h' ? 21 : 65);

$tweet_config = array(
	'class'=>'twitter-share-button',
	'data-count'=>$mode == 'h' ? 'horizontal':'vertical',
	'data-via'=>'pulsem',
	'data-related'=>'pulsem',
	'data-lang'=>'es',
	'data-url'=>$url
);

if($title) $tweet_config['data-text'] = $title;

if($box){
	echo
		$html->script('http://widgets.twimg.com/j/2/widget.js',array('inline'=>true)),
		$html->scriptBlock('new TWTR.Widget({
		  version: 2,
		  type: "profile",
		  rpp: 3,
		  interval: 30000,
		  width: '.$w.',
		  height: '.$h.',
		  /*
		  theme: {
		    shell: {
		      background: "#D7B03A",
		    },
		    tweets: {
		      background: "#ffffff",
		      color: "#333",
		      links: "#C89D0B"
		    }
		  },
		  */
		  features: {
		    scrollbar: false,
		    loop: false,
		    live: false,
		    behavior: "all"
		  }
		}).render().setUser("'.(substr(strrchr(Configure::read('Site.tw'),'/'),1)).'").start();',array('inline'=>true));

} else {

	echo
		$html->div('twitter'),
			$html->link('Tweet','http://twitter.com/share',$tweet_config),
		'</div>',
		$html->script('http://platform.twitter.com/widgets.js',array('inline'=>true));
}
?>