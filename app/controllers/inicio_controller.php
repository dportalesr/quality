<?php
App::import('Controller','_base/My');
class InicioController extends MyController {
	var $name = 'Inicio';
	var $uses = array('Carousel','About');

	function index(){
		$carrusel = $this->Carousel->find_();
		$this->set(compact('carrusel'));
		
		$this->set('about', $this->About->find_(array('contain'=>false,'fields'=>array('id','intro')),'first'));

		$this->pageTitle = Configure::read('Site.slogan');
		
	}
	
	function email(){ $this->layout = 'empty'; }
}
?>