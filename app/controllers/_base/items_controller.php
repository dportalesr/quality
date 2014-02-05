<?php
App::import('Controller','_base/Abcs');
class ItemsController extends AbcsController{
	var $name = 'Items';
	
	function index($paginate = true) {
	   $items = $paginate ? $this->paginate($this->uses[0],$this->m[0]->find_(null,'paginate')):$this->m[0]->find_(array('contain'=>false));
	   $this->set(compact('items'));
	}
	
	function ver($id = false) {
		$id = $this->_checkid($id);
		$hasComments = false;
		
		if(isset($this->m[0]->hasMany['Comment'])){ $hasComments = true; $this->m[0]->unbindModel(array('hasMany'=>array('Comment'))); }
		$this->m[0]->recursive = 1;

		if($item = $this->m[0]->read(null,$id)){
			$this->set(compact('item'));
			$this->set('related',$this->m[0]->find_(array('recursive'=>0,'field'=>'orden','value'=>$item[$this->uses[0]]['orden']),'neighbors'));
			$this->seo($item);
		}
		
		if($hasComments){
			$this->paginate['Comment']['limit'] = 8;
			$comments = $this->paginate('Comment',array('parent_id'=>$id,'parent'=>$this->uses[0]));
			$this->set(compact('comments'));
			
			if(isset($this->params['named']['reply']) && $this->params['named']['reply']){
				$this->data['Comment'] = array(
					'autor' => Configure::read('Site.name'),
					'email' => Configure::read('Site.email')
				);
			}

			if($this->Session->check('form_errors.Comment')){ $this->data = $this->Session->read('form_errors.Comment.data'); }
		}
	}
	
	///// Admin
	
	function admin_images($id = false){
		$id = $this->_checkid($id);
		$this->paginate[$this->uses[0].'img']['limit'] = 16;

		if($id = (int)$id){
			$this->set('items',$this->paginate($this->uses[0].'img',array($this->uses[0].'img.'.(low($this->uses[0])).'_id'=>$id)));
			$this->set('itemtitle',$this->m[0]->field($this->m[0]->displayField,array('id'=>$id)));
		}
			
		if(!empty($this->data)){
			if($return = $this->m[0]->saveAll($this->data,array('validate'=>true))){
				$msg = 'ok';
				if(is_array($return)){ $msg = 'some'; }
				$this->_flash('save_'.$msg);
				$this->redirect(array_merge($this->passedArgs,array('action'=>'index','admin'=>1)));
			}
		}
		
		$this->detour('elements/temp');
	}
	
	function admin_orden($limit = 30){
		if(!empty($this->data)){
			if(isset($this->data['limit_change']) && $this->data['limit_change']){
				$this->redirect(array($this->data['limit']));
			}

			$success = true;
			foreach($this->data[$this->uses[0]] as $it){
				$this->m[0]->create(false);
				$success = $success && $this->m[0]->save($it);
			}
			
			$this->_flash('save_'.($success ? 'ok':'some'));
			$this->redirect(array('action'=>'index','admin'=>1));
		}

		$total = $this->m[0]->find_(array('order'=>''),'count+');

		$this->paginate[$this->uses[0]]['fields'] = array('id',$this->m[0]->displayField,'orden');
		$this->paginate[$this->uses[0]]['contain'] = false;
		$this->paginate[$this->uses[0]]['limit'] = $limit;

		$this->set('orderdata',$this->paginate($this->uses[0],$this->m[0]->find_(null,'paginate')));

		if(isset($this->params['named']['page']) && $this->params['named']['page'])
			$page = $this->params['named']['page'];
		else
			$page = 1;

		$offset = $total - ($limit*$page);
		$offset = $offset < 0 ? 0 : $offset;
		$this->set(compact('offset'));
		$this->data['limit'] = $limit;
	}

	function seo($item){
		$this->pageTitle = $item[$this->uses[0]][$this->m[0]->displayField];
		
		$descripcion = '';
		if(!empty($item[$this->uses[0]]['description']))
			$descripcion = $item[$this->uses[0]]['description'];
		elseif(!empty($item[$this->uses[0]]['descripcion']))
			$descripcion = $item[$this->uses[0]]['descripcion'];

		$descripcion = substr(strip_tags(_dec($descripcion)), 0, 155);

		$this->set('description_for_layout',$descripcion);

		$keywords = '';
		if(!empty($item[$this->uses[0]]['keywords']))
			$keywords = $item[$this->uses[0]]['keywords'];

		$this->set('keywords_for_layout',$keywords);
		
		/// og:meta
		$og_for_layout = array(
			'title'=>$item[$this->uses[0]]['nombre'],
			'type'=>'article',// blog, article
			'url'=>'http://'.Configure::read('Site.domain').$this->here,
			'description'=>$descripcion,

			'itemtype'=>'article' //article
		);

		if(!empty($item[$this->uses[0].'portada']['src']))
			$og_for_layout['image'] = 'http://'.Configure::read('Site.domain').'/'.$item[$this->uses[0].'portada']['src'];

		$og_for_layout = array_merge(Configure::read('Site.og'),$og_for_layout);
		$this->set(compact('og_for_layout'));
	}
}
?>