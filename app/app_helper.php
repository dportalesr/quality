<?php
class AppHelper extends Helper {
	function url($url = null, $full = false){
		return parent::url(my_url_parser($url,$this), $full);
	}
}
?>