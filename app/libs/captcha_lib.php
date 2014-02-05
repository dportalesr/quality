<?php
/**
* used by captcha helper and behavior
*/
class CaptchaLib {
	public static $defaults = array (
		'dummyField' => 'mail',
		'method' => 'hash',
		'type' => 'both',     
		'checkSession' => false,
		'checkIp' => true,
		'salt' => ''
	);

	public static $types = array('passive', 'active', 'both');
	public static $methods = array('hash', 'db', 'session');

	function __construct() {}

	/**
	* @param array $data
	* @param array $options
	* 2011-06-11 ms 
	*/
	public static function buildHash($data, $options, $init = false) {
		if($init) {
			$data['captcha_time'] = time();
			$data['captcha'] = $data['result'];
		}

		$hashValue =	date('Y-m-d H:i:s', (int)$data['captcha_time']).'_'.
						($options['checkSession'] ? session_id().'_' : '').
						($options['checkIp'] ? RequestHandlerComponent::getClientIP().'_' : '').
						$data['captcha'];

		if(!class_exists('Security')) { App::import('Core', 'Security'); }

		return Security::hash($hashValue, isset($options['hashType']) ? $options['hashType'] : null, $options['salt']);
	}

}