<?php
App::import('Controller','_base/Items');
class ServicesController extends ItemsController{
	var $name = 'Services';
	var $uses = array('Service');

	function index(){
		parent::index(false);
	}
}
?>