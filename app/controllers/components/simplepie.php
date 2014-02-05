<?php
/*
 * SimplePie CakePHP Component
 * Copyright (c) 2007 Matt Curry
 * www.PseudoCoder.com
 *
 * Based on the work of Scott Sansoni (http://cakeforge.org/snippet/detail.php?type=snippet&id=53)
 *
 * @author mattc <matt@pseudocoder.com>
 * @version 1.0
 * @license MIT
 *
*/

class SimplepieComponent extends Object {
	var $cache;

	function __construct() { $this->cache = CACHE . 'rss' . DS; }

	function feed($feed_url,$limit = 0,$isWeather = false) {

		if (!file_exists($this->cache)) { # make the cache dir if it doesn't exist
			$folder = new Folder();
			$folder->mkdir($this->cache);
		}

		#include the vendor class
		App::import('Vendor', 'simplepie/simplepie');
		if($isWeather) App::import('Vendor', 'simplepie/simplepie_yahooweather');

		#setup SimplePie
		$feed = new SimplePie();
		
		$feed->set_feed_url($feed_url);
		if($isWeather) $feed->set_item_class('SimplePie_Item_YWeather');
		$feed->set_cache_location($this->cache);

		$feed->init();
		$items = ($isWeather) ? $feed->get_item(0) : $feed->get_items(0,$limit);
		
		if($isWeather){
			$clima = array();
			$clima['cond_img'] = $items->get_condition_image();
			$clima['cond_code'] = $items->get_condition_code();
			$clima['cond'] = $items->get_condition();
			$clima['temp'] = $items->get_temperature();
			$items = $clima;
		}
		
		return ($items) ? $items : false;
	}
}
?>