<?php
App::import('Controller','_base/My');
class UsersController extends MyController{
	var $name = 'Users';
	var $uses = array('User');

	function admin_dashboard(){
		$this->pageTitle = 'Panel de Administración';
	}
	function admin_index(){ $this->set('items',$this->paginate($this->uses[0])); }
	function admin_login(){
		$this->pageTitle = 'Entrando al Sistema';
		if(!empty($this->data)) {
			if($usuario = $this->User->find_(array('contain'=>false,'conditions'=>array('username'=>$this->data['User']['username'])),'first')){
				if ($usuario['User']['password'] == sha1($this->data['User']['password'])){
					$this->Session->write('sAdmin', $usuario['User']);
					$this->redirect('/panel');
					exit;
				} else { $this->User->invalidate('password','La contraseña no es correcta.'); }
			} else { $this->User->invalidate('username','El Nombre de usuario no existe.'); }
		}
	}
	
	function admin_logout(){
		$this->Session->delete('sAdmin');
		$this->redirect('/');
	}

	function admin_agregar() {
		if (!empty($this->data)){
			$error = false;
			if($this->User->find_(array('contain'=>false,'conditions'=>array('username'=>$this->data['User']['username'])),'first')){
				$error = true;
				$this->m[0]->invalidate('username','Este usuario ya existe.');
			}

			if($this->data['User']['password'] != $this->data['User']['passwordc']){
				$error = true;
				$this->m[0]->invalidate('password','Las contraseñas no coinciden.');
				$this->m[0]->invalidate('passwordc','Las contraseñas no coinciden.');
			}
			
			if(!$error && $this->m[0]->save($this->data)){
				$this->_flash('save_ok');
				$this->redirect(array('action'=>'index'));
			}
		}
	}
	
	function admin_editar($id) {
		$id = $this->_checkid($id);
		if($id == 1) $this->redirect(array('controller'=>'users','action'=>'index','admin'=>true));

		if(!empty($this->data)){
			$error = false;
			if($this->m[0]->find_(array('contain'=>false,'conditions'=>array('username'=>$this->data['User']['username'],'User.id <>'=>$id)),'first')){
				$error = true;
				$this->m[0]->invalidate('username','Este usuario ya existe.');
			}

			if(empty($this->data['User']['password'])){
				unset($this->data['User']['password']);
				unset($this->data['User']['passwordc']);
			}else{
				if($this->data['User']['password'] != $this->data['User']['passwordc']){
					$error = true;
					$this->m[0]->invalidate('password','Las contraseñas no coinciden.');
					$this->m[0]->invalidate('passwordc','Las contraseñas no coinciden.');
				}
			}
		
			if(!$error && $this->m[0]->save($this->data)){
				$this->redirect(array('action'=>'index','msg:oksave'));
			}
		} else {
			$this->m[0]->recursive = -1;
			$this->data = $this->m[0]->read(null,$id);
			unset($this->data[$this->uses[0]]['password']);
			$this->m[0]->clean($this->data,true);

		}
	}
}
?>