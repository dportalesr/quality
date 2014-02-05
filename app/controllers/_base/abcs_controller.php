<?php
App::import('Controller','_base/My');
class AbcsController extends MyController{
	function admin_index() {
		$this->paginate[$this->uses[0]]['limit'] = 25;
		$find = array();

		//// Buscador
		if($this->data){
			if(isset($this->data['action']) && $this->data['action']=='reset'){
				$this->data['q'] = '';
			} elseif(isset($this->data['q']) && $this->data['q']) {
				$this->redirect(array(
					'q'=>b64($this->data['q'])
				));
			}
			
			if(isset($this->data['todelete']) && $this->data['todelete']){
				$msg = $this->m[0]->deleteAll(array($this->uses[0].'.id'=>array_keys(array_filter($this->data['todelete']))),true,true) ? 'ok':'some';
				$this->_flash('delete_'.$msg);
			}
			
		} elseif(isset($this->params['named']['q'])) {
			$q = b64($this->params['named']['q'],1);
			
			if(is_numeric($q)){
				$find[$this->uses[0].'.id'] = $q;
			} elseif($this->m[0]->hasField($this->m[0]->displayField)){
				$find[$this->uses[0].'.'.$this->m[0]->displayField.' LIKE'] = '%'.$q.'%';
			}

			$this->data['q'] = $q;
		}
		/////
		
		$this->set('items',$this->paginate($this->uses[0],$find));
		if($this->m[0]->belongsTo){
			$parentModels = array_keys($this->m[0]->belongsTo);
			foreach($parentModels as $parent){
				if(in_array($parent, $this->uses)) # SHOULD be true
					$this->paginate($parent);
			}
		}
		
		$this->data['todelete'] = array();
	}

	function admin_agregar() {
		$habtmData = $habtmModels = array();
		if($this->m[0]->hasAndBelongsToMany) $habtmModels = array_keys($this->m[0]->hasAndBelongsToMany);
		
		if(!empty($this->data)){
			foreach($habtmModels as $habtmModel){
				if(isset($this->data[$habtmModel]) && $this->data[$habtmModel] && method_exists($this->m[0]->$habtmModel,'savehabtm'))
					$habtmData[$habtmModel] = $this->data[$habtmModel];unset($this->data[$habtmModel]);
			}
			
			if($return = $this->m[0]->saveAll($this->data,array('validate'=>true))){
				foreach($habtmModels as $habtmModel){
					if(isset($habtmData[$habtmModel]))
						$this->m[0]->$habtmModel->savehabtm($habtmData[$habtmModel],$this->m[0]->id,$this->uses[0]);
				}
				
				$msg = 'ok';
				if(is_array($return)){ $msg = 'some'; }
				$this->_flash('save_'.$msg);
				$this->redirect(array('action'=>'index','admin'=>1));		
			}
		} else {
			if($this->m[0]->hasField('activo')) $this->data[$this->uses[0]]['activo'] = 1;
			if($this->m[0]->hasField('layout')) $this->data[$this->uses[0]]['layout'] = 'Izquierda';
		}
		
		foreach($habtmModels as $habtmModel){
			$habtmData = $this->m[0]->$habtmModel->find_(null,'list');
			$this->m[0]->clean($habtmData,true);
			$this->set(Inflector::tableize($habtmModel),$habtmData);
		}
	}
	
	function admin_editar($id) {
		$id = $this->_checkid($id);
		$this->m[0]->id = $id;
		
		$habtmData = $habtmModels = array();
		if($this->m[0]->hasAndBelongsToMany) $habtmModels = array_keys($this->m[0]->hasAndBelongsToMany);

		if(empty($this->data)){
			$this->m[0]->unbindModel(array('hasMany'=>array_keys($this->m[0]->hasMany)));
			$this->m[0]->recursive = 1;
			$this->data = $this->m[0]->read();
			$this->m[0]->clean($this->data,true);

		} else {
			foreach($habtmModels as $habtmModel){
				if(isset($this->data[$habtmModel]) && $this->data[$habtmModel] && method_exists($this->m[0]->$habtmModel,'savehabtm'))
					$habtmData[$habtmModel] = $this->data[$habtmModel];unset($this->data[$habtmModel]);
			}
			
			if($return = $this->m[0]->saveAll($this->data,array('validate'=>true))){
				foreach($habtmModels as $habtmModel){
					if(isset($habtmData[$habtmModel]))
						$this->m[0]->$habtmModel->savehabtm($habtmData[$habtmModel],$this->m[0]->id,$this->uses[0]);
				}
				
				$msg = 'ok';
				if(is_array($return)){ $msg = 'some'; }
				$this->_flash('save_'.$msg);
				$this->redirect(array('action'=>'index','admin'=>1));		
			}
		}

		foreach($habtmModels as $habtmModel){
			$habtmData = $this->m[0]->$habtmModel->find_(null,'list');
			$this->m[0]->clean($habtmData,true);
			$this->set(Inflector::tableize($habtmModel),$habtmData);
		}
	}
}
?>