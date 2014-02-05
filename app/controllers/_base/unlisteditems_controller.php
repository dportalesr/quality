<?php
App::import('Controller','_base/Items');
class UnlisteditemsController extends ItemsController{
	var $name = 'Unlisteditems';
	
	function index() { $this->redirect(array('action'=>'ver')); }
	function ver($id = false) {
		$id = $this->_checkid($id,false);
		
		if($id !== false && $item = $this->m[0]->read(null,$id)){
			parent::ver($id);
			
		} elseif($item = $this->m[0]->find_(null,'first')){
			$this->redirect(array('id'=>$item[$this->uses[0]]['slug']));
			exit;

		} else {
			$this->set('items',false);
			$this->detour('_base','index');
		}
	}
}
?>