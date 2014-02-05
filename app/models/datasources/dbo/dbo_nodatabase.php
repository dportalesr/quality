<?php
class DboNodatabase extends DataSource {
	var $description = "This is a dummy data source";
	function connect(){
		$this->connected = true;
		return $this->connected;
	}
	function disconnect(){
		$this->connected = false;
		return !$this->connected;
	}
	function value($string){
		return "\0".$string."\0";
	}
}
?>