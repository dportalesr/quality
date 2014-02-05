<?php
App::import('Controller','_base/My');
class SectionController extends MyController{
	function index(){ $this->set('item',$this->m[0]->find_(array('contain'=>false),'first')); }
	function admin_index() {
		if(empty($this->data)){
			$this->data = $this->m[0]->find_(array('contain'=>false),'first+');
			$this->m[0]->clean($this->data,true);
			
		} elseif($this->m[0]->save($this->data)){
			$this->_flash('save_ok');
			$this->redirect(array('action'=>'index','admin'=>1));
		}
	}
}
?>