<?php
App::import('Controller','_base/Abcs');
class LabelsController extends AbcsController{
	function index() {
		$this->set('items',$this->m[0]->find_(array('contain'=>false)));
	}
	
	function admin_index($parent = false) {
		$this->set('path',$parent ? $this->m[0]->getpath($parent):array());
		if(empty($this->data)){
			$this->set(compact('parent'));
			$orderdata = $this->m[0]->children($parent,true,NULL,$this->uses[0].'.orden desc, '.$this->uses[0].'.id desc',null,null,-1);
			$this->m[0]->clean($orderdata, true);
			$this->set(compact('orderdata'));

		} else {
			$success = true;
			$parent = false;
			$redirect = array('action'=>'index','admin'=>1);

			if(isset($this->data[$this->uses[0]]['parent_id'])){
				$parent = $this->data[$this->uses[0]]['parent_id'];
				unset($this->data[$this->uses[0]]['parent_id']);
			}
			
			foreach($this->data[$this->uses[0]] as $it){
				if($parent) $it['parent_id'] = $parent;
				$this->m[0]->create(false);
				$success = $success && $this->m[0]->save($it);
			}
			
			$this->_flash('save_'.($success ? 'ok':'some'));
			if($parent) $redirect[] = $parent;
			
			$this->redirect($redirect);
		}
		
		$this->detour('_base/labels');
	}

	function admin_editar($id = false, $parent = false){
		$id = $this->_checkid($id);
		$this->m[0]->id = $id;
		
		if(empty($this->data)){
			$this->m[0]->recursive = -1;
			$this->data = $this->m[0]->read();
			$this->m[0]->clean($this->data,true);

		} else {
			if($return = $this->m[0]->saveAll($this->data,array('validate'=>true))){
				$msg = 'ok';
				if(is_array($return) && in_array(false,$return,true)){ $msg = 'some'; }
				$this->_flash('save_'.$msg);
				$this->redirect(array('action'=>'index','admin'=>1,$parent));
			}
		}
	}
}
?>