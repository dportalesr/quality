<?php
App::import('Controller','My');
class ImgsController extends MyController{
	function admin_portada($id){
		if(!$id = (int)$id) exit;
		
		$conds = array();
		
		if(isset($this->m[0]->actsAs['File']['portada']) && is_string($this->m[0]->actsAs['File']['portada'])){
			$fkey = $this->m[0]->actsAs['File']['portada'];
			if($this->m[0]->hasField($fkey))
				$conds[$this->uses[0].'.'.$fkey] = $this->m[0]->field($fkey, array('id'=>$id));
		}
		
		$this->m[0]->updateAll(array('portada'=>0),$conds);
		$this->m[0]->id = $id;
		$this->m[0]->saveField('portada',1);
	
		$this->set('ajax','$$(".thAdminSelected").removeClass("thAdminSelected");$("thAdmin_'.$id.'").addClass("thAdminSelected");');
		$this->render('js');
	}
	
	function admin_delete($id){
		if(!$id = (int)$id) exit;
		$script = '';
		$fkey = false;
		$conds = false;
		
		$this->m[0]->recursive = -1;
		$toDelete = $this->m[0]->read(null,$id);
		$esPortada = isset($toDelete[$this->uses[0]]['portada']) && $toDelete[$this->uses[0]]['portada'];
		

		if($this->m[0]->belongsTo){
			$parent = reset($this->m[0]->belongsTo);
			$fkey = $parent['foreignKey'];
			$conds = array($this->uses[0].'.'.$fkey => $toDelete[$this->uses[0]][$fkey]);
		}

		if($this->m[0]->delete($id)){
			if($esPortada){ # Si la imagen que se eliminó era portada
				if($siblings = $this->m[0]->find_(array('fields'=>array('id'),'conditions'=>$conds,'limit'=>1),'list+'))
					$siblingId = reset($siblings);
				
				if(is_integer($siblingId)){
					$this->m[0]->id = $siblingId;
					$this->m[0]->saveField('portada',1);
					$script.= '$("thAdmin_'.$siblingId.'").addClass("thAdminSelected");';
				}

			}
			
			$script.= 'if($("thAdmin_'.$id.'").hasClass("inline"))
				$("thAdmin_'.$id.'").fade("out").tween("width",0).get("tween").chain(function(){ $("thAdmin_'.$id.'").destroy(); });
			else
				$("thAdmin_'.$id.'").nix();';
		}
		
		$this->set('ajax',$script);
		$this->render('js');
	}
}
?>