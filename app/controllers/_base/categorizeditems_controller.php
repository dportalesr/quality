<?php
App::import('Controller','_base/Items');
class CategorizeditemsController extends ItemsController{
	function index() {
		$conds = array();
		if($category = $this->_setcategory()){ $conds[$this->uses[0].'.'.strtolower($this->m[0]->baseCategory).'_id'] = $category['id']; }

		$items = $this->paginate($this->uses[0],$this->m[0]->find_($conds,'paginate'));
		$this->set(compact('items'));
	}

	function ver($id = false) {
		$id = $this->_checkid($id);
		$this->m[0]->recursive = 1;
		if($item = $this->m[0]->read(null,$id)){
			$this->set(compact('item'));
			$this->set('category', $item[$this->m[0]->baseCategory] ? $item[$this->m[0]->baseCategory] : false);
			$this->set('path',$this->m[0]->{$this->m[0]->baseCategory}->getpath($item[$this->m[0]->baseCategory]['id']));
			//$this->set('related',$this->m[0]->find_(array('contain'=>array($this->uses[0].'portada'),'field'=>'id','value'=>$item[$this->uses[0]]['id']),'neighbors'));
			$this->seo($item);

		} else
			$this->redirect(array('action'=>'index'));
	}
	
	function categoria($id = false) {
		$id = $this->_checkid($id);
		$this->m[0]->recursive = -1;
		if($category = $this->m[0]->{$this->m[0]->baseCategory}->read(null,$id)){
			$this->set('currentCategory',$category[$this->m[0]->baseCategory]);
			$this->set('path',$this->m[0]->{$this->m[0]->baseCategory}->getpath($category[$this->m[0]->baseCategory]['id']));
			$this->set('items',$this->paginate($this->uses[0], $this->m[0]->find_(array($this->uses[0].'.'.low($this->m[0]->baseCategory).'_id'=>$category[$this->m[0]->baseCategory]['id']),'paginate')));
			$this->pageTitle = $category[$this->m[0]->baseCategory][$this->m[0]->{$this->m[0]->baseCategory}->displayField];
		} else
			$this->redirect(array('action'=>'index'));
		$this->detour('','index');
	}

	function admin_agregar(){ $this->postcategory(); }
	function admin_editar($id){ $this->postcategory('editar',$id); }
	
	function postcategory($action = 'agregar',$id = false){
		$action = 'admin_'.$action;
		$isPost = !empty($this->data);
		$categories = array();
		
		foreach($this->m[0]->categoryModels as $categModel){
			$categLow = strtolower($categModel);
			
			if($isPost && isset($this->data[$this->uses[0]][$categLow])){
				if($this->data[$this->uses[0]][$categLow]){ // Creating Category
					$this->data['Category']['parent_id'] = $this->data['Product']['category_id'] ? $this->data['Product']['category_id']:NULL;
					unset($this->data['Product']['category_id']);
				} else { // Existing Category
					unset($this->data[$categModel]);
				}
			}
			
			$categories[$categModel] = $this->m[0]->{$categModel}->generatetreelist(null,'{n}.'.$categModel.'.id','{n}.'.$categModel.'.nombre','—');
			
			$this->m[0]->clean($categories[$categModel],true);
			$this->set(Inflector::tableize($categModel),$categories[$categModel]);
		}
		
		if($id)
			parent::$action($id);
		else
			parent::$action();
			
		foreach($this->m[0]->categoryModels as $categModel){
			$categLow = strtolower($categModel);
			if(!isset($this->data[$this->uses[0]][$categLow]))
				$this->data[$this->uses[0]][$categLow] = isset($categories[$categModel]) && $categories[$categModel] ? 0:1;
		}
	}

	function _setcategory(){
		$category = false;
		$cat_model = $this->m[0]->baseCategory;

		if(isset($this->params['category']) && $c_id = $this->_checkid($this->params['category'],false)){
			if($category = $this->m[0]->{$cat_model}->find_(array($c_id,'contain'=>false))){
				$category = $category[$cat_model];
				$conds[$this->uses[0].'.'.strtolower($cat_model).'_id'] = $category['id'];
			}
		}

		$this->set(compact('category'));
		return $category;
	}
}
?>