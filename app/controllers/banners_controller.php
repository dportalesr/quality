<?php
App::import('Controller','_base/Items');
class BannersController extends ItemsController{
	var $name = 'Banners';
	var $uses = array('Banner');

	function admin_editar($id) {
		$isPost = !empty($this->data);
		
		if($isPost){
			if(!$this->data['Banner']['caduca']
				 || empty($this->data['Banner']['caducidad']['day'])
				 || empty($this->data['Banner']['caducidad']['month'])
				 || empty($this->data['Banner']['caducidad']['year'])
			){
				$this->m[0]->create(false);
				$this->m[0]->id = $id;
				$this->m[0]->saveField('caducidad', NULL);
				unset($this->data['Banner']['caducidad']);
			}
		}
		
		parent::admin_editar($id);
		
		if(!$isPost)
			$this->data['Banner']['caduca'] = !empty($this->data['Banner']['caducidad']);

	}
}
?>