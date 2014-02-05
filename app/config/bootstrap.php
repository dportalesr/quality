<?php
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * App::build(array(
 *     'plugins' => array('/full/path/to/plugins/', '/next/full/path/to/plugins/'),
 *     'models' =>  array('/full/path/to/models/', '/next/full/path/to/models/'),
 *     'views' => array('/full/path/to/views/', '/next/full/path/to/views/'),
 *     'controllers' => array('/full/path/to/controllers/', '/next/full/path/to/controllers/'),
 *     'datasources' => array('/full/path/to/datasources/', '/next/full/path/to/datasources/'),
 *     'behaviors' => array('/full/path/to/behaviors/', '/next/full/path/to/behaviors/'),
 *     'components' => array('/full/path/to/components/', '/next/full/path/to/components/'),
 *     'helpers' => array('/full/path/to/helpers/', '/next/full/path/to/helpers/'),
 *     'vendors' => array('/full/path/to/vendors/', '/next/full/path/to/vendors/'),
 *     'shells' => array('/full/path/to/shells/', '/next/full/path/to/shells/'),
 *     'locales' => array('/full/path/to/locale/', '/next/full/path/to/locale/')
 * ));
 *
 */

ini_set('default_charset', 'UTF-8');
date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL, array('es_ES.utf-8','es_ES.utf8','es_ES','esm.utf-8','esm.utf8','esm'));
App::import('Vendor', 'FirePHPDebugger', array('file' => 'FirePHP' . DS . 'FirePHP.debugger.php'));

function b64($string, $decode = false){ return $decode ? base64_decode(strtr($string,'-_,','+/=')) : strtr(base64_encode($string), '+/=', '-_,'); }
function _enc($string) { return htmlentities($string,ENT_QUOTES,'UTF-8'); }
function _dec($string) { return html_entity_decode($string,ENT_QUOTES,'UTF-8'); }
function is_c($controllers,&$obj){ return in_array($obj->params['controller'],(array)$controllers); }
function my_url_parser($url,&$obj){
		$carried = array(
			'*'=>array('lang'),
			'works'=>array('tipo'),
			'posts'=>array('category'),
		);
		
		if($carried && is_array($url)){
			if(isset($url['controller']) && $url['controller']){
				$ctllr = $url['controller'];
			} else {
				$ctllr = $obj->params['controller'];
			}
			
			if(isset($carried[$ctllr]) && $carried[$ctllr]){
				foreach($carried[$ctllr] as $added){
					if((!isset($url[$added])) && (isset($obj->params[$added]) || isset($obj->params['named'][$added]))){
						if(isset($obj->params[$added]))
							$url[$added] = $obj->params[$added];
						else
							$url[$added] = $obj->params['named'][$added];
					}
				}
			}
			
			if(isset($carried['*']) && $carried['*']){
				foreach($carried['*'] as $added){
					if((!isset($url[$added])) && (isset($obj->params[$added]) || isset($obj->params['named'][$added]))){
						if(isset($obj->params[$added]))
							$url[$added] = $obj->params[$added];
						else
							$url[$added] = $obj->params['named'][$added];
					}
				}
			}
		}
	return $url;	
}

/**
 * Convert an IP address from presentation to decimal(39,0) format suitable for storage in MySQL
 *
 * @param string $ip_address An IP address in IPv4, IPv6 or decimal notation
 * @return string The IP address in decimal notation
 */
function inet_ptod($ip_address){
	// IPv4
	if(defined('AF_INET6') && strpos($ip_address, ':') === false && strpos($ip_address, '.') !== false)
		$ip_address = '::'.$ip_address;

	// IPv6
	if((!defined('AF_INET6')) && strpos($ip_address, ':') !== false) { /// Imposible to parse IPv6
		return false;
	} elseif((!defined('AF_INET6')) || strpos($ip_address, ':') !== false){
		$network = inet_pton($ip_address);
		$parts = unpack('N*', $network);

		foreach ($parts as &$part) {
			if($part < 0) $part = bcadd((string) $part, '4294967296');
			if(!is_string($part)) $part = (string) $part;
		}
		
		if(sizeof($parts) == 4){
			$decimal = $parts[4];
			$decimal = bcadd($decimal, bcmul($parts[3], '4294967296'));
			$decimal = bcadd($decimal, bcmul($parts[2], '18446744073709551616'));
			$decimal = bcadd($decimal, bcmul($parts[1], '79228162514264337593543950336'));
		} else {
			$decimal = $parts[1];
		}

		return $decimal;
	}

	return false;
}
/*
fb(inet_ptod('185.220.1.25'),'—— 185.220.1.25');
fb(inet_ptod('::185.220.1.25'),'—— ::185.220.1.25');
fb(inet_ptod('2001:0DB8::1428:57ab'),'—— 2001:0DB8::1428:57ab');
*/
/**
 * Convert an IP address from decimal format to presentation format
 *
 * @param string $decimal An IP address in IPv4, IPv6 or decimal notation
 * @return string The IP address in presentation format
 */
function inet_dtop($decimal){
	// IPv4 or IPv6 format
	if(strpos($decimal, ':') !== false || strpos($decimal, '.') !== false) {
		return $decimal;
	}

	// Decimal format
	$parts = array();
	$parts[1] = bcdiv($decimal, '79228162514264337593543950336', 0);
	$decimal = bcsub($decimal, bcmul($parts[1], '79228162514264337593543950336'));
	$parts[2] = bcdiv($decimal, '18446744073709551616', 0);
	$decimal = bcsub($decimal, bcmul($parts[2], '18446744073709551616'));
	$parts[3] = bcdiv($decimal, '4294967296', 0);
	$decimal = bcsub($decimal, bcmul($parts[3], '4294967296'));
	$parts[4] = $decimal;

	foreach ($parts as &$part) {
		if(bccomp($part, '2147483647') == 1) $part = bcsub($part, '4294967296');
		
		$part = (int) $part;
	}

	$network = pack('N4', $parts[1], $parts[2], $parts[3], $parts[4]);
	$ip_address = inet_ntop($network);

	// Turn IPv6 to IPv4 if it's IPv4
	if(preg_match('/^::\d+.\d+.\d+.\d+$/', $ip_address)) return substr($ip_address, 2);

	return $ip_address;
}

function btop($bytes, $decimals = 1) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .' '. @$sz[$factor].'b';
}

function new_array_merge_recursive($arr1, $arr2){
  foreach($arr2 as $key => $value) {
    if(array_key_exists($key, $arr1) && is_array($value))
      $arr1[$key] = new_array_merge_recursive($arr1[$key], $arr2[$key]);
    else
      $arr1[$key] = $value;
  }

  return $arr1;
}