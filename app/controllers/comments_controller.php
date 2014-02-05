<?php
App::import('Controller','_base/Abcs');
class CommentsController extends AbcsController{
	var $name = 'Comments';
	var $uses = array('Comment');
	var $paginate = array('Comment'=>array('limit' => 20));
	
	function publicar() {
		if(!empty($this->data)){
			$this->m[0]->set($this->data);# Seteamos para validar
			if(isset($this->params['isAjax']) && $this->params['isAjax']){
				if($this->m[0]->validates()){
					if(!$this->m[0]->save($this->data)){
						$this->set('ajax','Hubo un problema al publicar su comentario.');
					} else {
						$this->m[0]->recursive = -1;
						$comm = $this->m[0]->read();
						$this->set('data',$comm['Comment']);
						$this->render('/elements/comment');
					}
				} else {
					$this->set('errors',$this->Comment->invalidFields());
					$this->render('form');
				}
			} else {
				if(!$this->m[0]->save($this->data)){
					$this->Session->write('form_errors',array($this->m[0]->alias => array('data'=>$this->data,'errors'=>$this->m[0]->invalidFields())));
				}

				$this->redirect($this->referer().'#comments');
			}
			
		}
	}
	
	function admin_editar($id) {
		$id = $this->_checkid($id);
		$this->m[0]->id = $id;

		if(empty($this->data)){
			$this->m[0]->recursive = 0;
			$this->data = $this->m[0]->read();
			$this->m[0]->clean($this->data,true,false);
		
		} elseif($return = $this->m[0]->saveAll($this->data,array('validate'=>true))){

			$msg = 'ok';
			if(is_array($return) && in_array(false,$return,true)){ $msg = 'some'; }
			$this->_flash('save_'.$msg);
			$this->redirect(array('action'=>'index','admin'=>1));		
		}
	}
}
?>