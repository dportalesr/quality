<?php
class HitsComponent extends Object{
	function __construct(){  }
	function getHits(){
		if(($f = file_get_contents(WWW_ROOT.'ip.txt','r')) !== false){
			if(!empty($f)){
				$f = explode(';', $f, -1);
				$acum = array_shift($f);
				$size = sizeof($f);
				
				return $size+$acum;
			}
			return false;
		}
	}
	
	function reset(){
		if($fh = fopen(WWW_ROOT.'ip.txt','w')){
			fwrite($fh, '0;');
			fclose($fh);
		}
	}
	
	function hit(){
		$now = date("Y-m-d H:i:s");
		$ip = $_SERVER['REMOTE_ADDR'];
		$f = file_get_contents(WWW_ROOT.'ip.txt','r');

		if($f !== false){
			if(empty($f)){
				$fh = fopen(WWW_ROOT.'ip.txt','w');
				fwrite($fh, '0;');
				fclose($fh);
				$f = '0;';
			}

			$f = explode(';', $f, -1);
			$acum = array_shift($f);
			$size = sizeof($f);

			//// Encontrar la fecha más reciente de la IP (si hay)
			$mostrecent = false;
			
			for($i=0;$i<$size;$i++){
				$it = explode('|',$f[$i]);
				if($it[1] == $ip){
					$mostrecent = $it[0];
				}
				
			}

			//// Si no se encontró, ó si es una IP reciente y tiene más de 2 horas desde la última visita,
			//// Se cuenta
			if((!$mostrecent) || ($mostrecent && (strtotime("-3 hours") > strtotime($mostrecent)))){
				$fh = fopen(WWW_ROOT.'ip.txt','a');
				fwrite($fh, "$now|$ip;");
				fclose($fh);
				$size++;
			}
			
			//// Compactar
			if($size > 300){
				$fh = fopen(WWW_ROOT.'ip.txt','w');
				fwrite($fh, ($acum + $size).";");
				fclose($fh);
			}
			
			return $acum + $size;
		}
	}
}
?>