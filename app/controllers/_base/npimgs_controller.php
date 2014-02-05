<?php
App::import('Controller','_base/Imgs');
class NpimgsController extends ImgsController{
	function admin_index(){	
		$this->paginate[$this->uses[0]]['limit'] = 6;
		if(!empty($this->data)){
			$success = true;
			foreach($this->data[$this->uses[0]] as $item){
				$this->m[0]->create(false);
				$success = $success && $this->m[0]->save($item);
			}
			$this->_flash('save_'.($success ? 'ok':'some'));
		}

		$items = $this->paginate($this->uses[0]);
		$this->m[0]->clean($items,true);
		$this->set(compact('items'));
		$this->detour('_base/npimgs');
	}

	function admin_order() {
		if(!empty($this->data)){
			$success = true;
			foreach($this->data[$this->uses[0]] as $it){
				$this->m[0]->create(false);
				$success = $success && $this->m[0]->save($it);
			}
			$this->_flash('save_'.($success ? 'ok':'some'));
		}

		$this->set('orderdata',$this->m[0]->find_(array(
			'fields'=>array('id',$this->m[0]->displayField,'orden'),
			'contain'=>false
		),'all+'));
	}
}
?>