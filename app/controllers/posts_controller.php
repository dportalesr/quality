<?php
App::import('Controller','_base/Items');
class PostsController extends ItemsController{
	var $name = 'Posts';
	var $pageTitle = 'Noticias';
	var $uses = array('Post','Postimg','Comment');
	// var $paginate = array('Post'=>array('limit'=>1));
/*
	function admin_agregar() {
		if(!empty($this->data)){
			$tags = $this->data['Tag'];unset($this->data['Tag']);
			
			if($this->m[0]->saveAll($this->data,array('validate'=>true))){
				$this->m[0]->Tag->savehabtm($tags,$this->m[0]->id);
				$this->_flash('save_ok');
				$this->redirect(array('action'=>'index','admin'=>1));		
			}

		} else {
			#---- TAGS ----
			if(isset($this->m[0]->hasAndBelongsToMany['Tag'])){
				$tags = $this->m[0]->Tag->find_(null,'list');
				$this->m[0]->clean($tags,true);
				$this->set(compact('tags'));
			}
			#--------------

			if($this->m[0]->hasField('activo')) $this->data[$this->uses[0]]['activo'] = 1;
			if($this->m[0]->hasField('layout')) $this->data[$this->uses[0]]['layout'] = 'Izquierda';
		}
	}
	
	function admin_editar($id) {
		$id = $this->_checkid($id);
		$this->m[0]->id = $id;

		if(empty($this->data)){
			$this->data = $this->m[0]->find_(array($id,'contain'=>array('Tag')));
			$this->m[0]->clean($this->data,true);

			
			#---- TAGS ----
			if(isset($this->m[0]->hasAndBelongsToMany['Tag'])){
				$tags = $this->m[0]->Tag->find_(null,'list');
				$this->m[0]->clean($tags,true);
				$this->set(compact('tags'));
			}
			#--------------

		} else {
			$tags = $this->data['Tag'];unset($this->data['Tag']);
			if($this->m[0]->saveAll($this->data)){
				$this->m[0]->Tag->savehabtm($tags,$this->m[0]->id);
				$this->_flash('save_ok');
				$this->redirect(array('action'=>'index','admin'=>1));
			}
		}
	}
/**/
		
	function admin_export(){ $this->_export(array('nombre','descripcion','comment_count')); }
}
?>