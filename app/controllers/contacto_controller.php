<?php
App::import('Controller','_base/Empty');
class ContactoController extends EmptyController {
	var $name = 'Contacto';
	var $components = array('Email');
	var $uses = array('Contact');
	var $helpers = array('Captcha');

	function index(){
		$this->_set_services();
	}

	function enviar(){
		$this->_set_services();
		$this->Contact->set($this->data);
		if($this->Contact->validates()){
			$site = Configure::read('Site');
			$data = $this->data['Contact'];

			$this->Contact->clean($data,false,false);
			$fields = array_keys($this->Contact->_schema);
			$this->set(compact('data','fields'));
			
			$this->Email->to = $site['email'];
			$this->Email->from = $site['name'].' <noreply@'.$site['domain'].'>';
			$this->Email->subject = 'Mensaje enviado desde '.ucfirst($site['domain']);
			$this->Email->delivery = 'mail';
			$this->Email->sendAs = 'html';
			$this->Email->template = 'contact';

			if(Configure::read('debug')===0){
				if($this->Email->send()){
					$msg = 'Su mensaje ha sido enviado correctamente. ¡Gracias por contactar con nosotros!';
				}
				else
					$msg = 'Lo sentimos, pero hubo un problema al enviar el mensaje.';
			} else {
				$this->Email->delivery = 'debug';
				$this->Email->send();
				$msg = 'El Formulario ha sido desactivado porque está en modo Demo.';
			}

			$this->set('successmsg',$msg);
		} else
			$this->set('errors',$this->Contact->invalidFields());

		$this->set('fid',$this->params['url']['fid']);
		$this->render('form');
	}

	function _set_services(){
		$srvs = array();
		if($services_list = Cache::read('service_recent')){
			foreach ($services_list as $key => $value) {
				$srvs[$value] = _dec($value);
			}
			$services_list = $srvs;
		}

		$this->set(compact('services_list'));
	}

}
?>