<?php
class MyController extends AppController{
	function admin_toggle($id = false, $field = 'activo', $exclusivo = false){
		$id = $this->_checkid($id);
		$this->m[0]->recursive = -1;
		$msg = 'fail';
		
		if($this->m[0]->hasField($field) && $current = $this->m[0]->read($field,$id)){
			if($exclusivo && (!$current[$this->uses[0]][$field]))
				$this->m[0]->updateAll(array($field=>0));
				
			$this->m[0]->create();
			$this->m[0]->id = $id;
			if($this->m[0]->saveField($field,!$current[$this->uses[0]][$field]))
				$msg = 'ok';
		}
		
		$this->_flash('toggle_'.$msg);
		$this->redirect(array('action'=>'index','admin'=>1));
	}

	function admin_eliminar($id = false, $sortable = false){
		$id = $this->_checkid($id,false);
		
		if(isset($this->params['isAjax']) && $this->params['isAjax']){
			$script = 'alert("Imposible eliminar el elemento.")';
			if($sortable){
				if($id){
					if($this->m[0]->delete($id))
						$script = '$("elistitem_'.$id.'").nix().get("reveal").chain(function(){ '.$sortable.'Sortable.reorder(); });';
				}
			}
			$this->set('ajax',$script);
			$this->render('js');
			
		} else {
			if($id){
				$this->m[0]->id = $id;

				if($this->m[0]->hasField('master') && $this->m[0]->field('master'))
					$this->_flash('master');
				elseif($this->m[0]->delete($id,true))
					$this->_flash('delete_ok');
				else
					$this->_flash('delete_fail');
			}
			
			$this->redirect(array('action'=>'index','admin'=>1));
			exit;
		}
	}

	function _export($fields = false,$conditions = false){
		$csvoutput ='';
		$fields = $this->m[0]->filtervalidfields(array_merge(array('id'=>'Clave','created'=>'Creado'),Set::normalize((array)$fields)),1);
		$data = $this->m[0]->find_(array('recursive'=>0,'fields'=>array_keys($fields),'conditions'=>$conditions));

		if($data){
			/// Títulos de Campos
			foreach($fields as $fieldName => $fieldLabel){
				if(is_null($fieldLabel)){
					if($this->m[0]->_schema[$fieldName]['label'])
						$fieldLabel = $this->m[0]->_schema[$fieldName]['label'];
					else
						$fieldLabel = $fieldName;
				}
				
				$csvoutput .= ucfirst(addslashes($fieldLabel)).',';
			}
				
			$csvoutput = substr($csvoutput,0,-1)."\n";
			
			foreach($data as $item){
				foreach($fields as $fieldName => $fieldLabel){
					$defaultmodel = $this->uses[0];
					if(strpos($fieldName,'.')!== false){
						$defaultmodel = strtok($fieldName,'.');
						$fieldName = strtok('.');
					}
					$csvoutput .= '"'.str_replace('"','""',strip_tags(html_entity_decode($item[$defaultmodel][$fieldName],ENT_QUOTES, Configure::read('App.encoding')))).'",';
				}
				$csvoutput = substr($csvoutput,0,-1)."\n";
			}
		}

		$csvoutput = iconv("UTF-8", "ISO-8859-1//IGNORE", $csvoutput);
 
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream');
		header('Content-Type: application/download');
		header('Content-Type: text/csv;charset=iso-8859-1');
		header('Content-Disposition: attachment; filename='.$this->ts.'.csv');
		#header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		echo $csvoutput;
		exit;
	}

	function download($id = false){ $this->_download($id); } // Wrapper function
	function _download($id = false, $src = 'src', $model = false){
		$id = $this->_checkid($id,false);
		
		if(!$model) $model = $this->uses[0];
		$this->$model->recursive = -1;
		$dir = isset($this->$model->Behaviors->File->settings[$model]['fields'][$src]['dir']) ? $this->$model->Behaviors->File->settings[$model]['fields'][$src]['dir'] : 'upload';
		$path = WWW_ROOT.str_replace(array('/', '\\'), DS,$dir).DS;
		$file = $this->$model->read(null,$id);
		
		if($file && isset($file[$model][$src]) && $file[$model][$src]){
			$f = pathinfo($file[$model][$src]);
			if(!isset($file[$model][$this->$model->displayField])){
				$modules = Cache::read('sitemodules');
				$filename = $modules[$this->params['controller']]['route'];
			} else
				$filename = $file[$model][$this->$model->displayField];

			$filename = ucfirst(Inflector::slug(_dec($filename)));
			$params = array(
				'download' => true,
				'id' => $f['basename'],
				'name' => $filename,
				'extension' => $f['extension'],
				'mimeType' => array(
					'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
					'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
					'rar'=>'application/x-rar-compressed'
				),
				'path' => $path
			);

			if(file_exists($params['path'].$params['id'])!== false){
				$this->view = 'Media';
				$this->autoLayout = false;
				$this->set($params);
				$this->render();
				exit;
			}
		}
	
		$this->redirect($this->referer());	
	}
			
	function last(){
		$model = $this->uses[0];
		$params = isset($this->params['arrayUrl']) && $this->params['arrayUrl'] ? $this->params : $this->params['named'];
		$findopts = array('conditions'=>array(),'recursive'=>-1);
		/// Defaults
		$mode = isset($params['mode']) ? $params['mode'] : 'all';
			
		if(!isset($params['order']))
			$params['order'] = $this->m[0]->hasField('orden') ? 'orden_desc' : 'created_desc';
		
		foreach($params as $param => $value){
			if(!in_array($param,array('limit','order','recursive','offset'))){
				if($param == 'vigente'){
					$dateconds = array(
						'AND'=>array(
							'OR'=>array(
								'AND'=>array(
									'NOT'=>array($model.'.caducidad'=>null),
									'DATEDIFF('.$model.'.caducidad,NOW()) >'=>0
								),
								$model.'.caducidad'=>null
							),
							$model.'.activo'=>1
						)
					);
					
					$findopts['conditions'] = $dateconds;
				} elseif($this->m[0]->hasField($param) || strpos($param,'(')!==false)
					$findopts['conditions'][$model.'.'.$param] = $value;
			} else {
				if($param=='order'){
					if($value == 'rand'){
						$findopts[$param] = 'RAND()';
					} else {
						$ordermodel = $model;
						$direction = '';
						
						if(strpos($value,'.')!==false){
							$ordermodel = strtok($value,'.');
							$field = strtok('.');
						} else {
							$field = $value;
						}

						if(strpos($field,'_')!==false){
							$orderfield = strtok($field,'_');
							$direction = strtok('_');
						} else {
							$orderfield = $field;
						}
					
						if($this->m[0]->hasField($orderfield)){
							$findopts[$param] = $ordermodel.'.'.$orderfield.' '.$direction;
						}
						
						$findopts[$param].= ', '.$ordermodel.'.id DESC';
					}
				} else	
					$findopts[$param] = $value;
			}
		}

		if($mode =='treelist'){
			$results = $this->m[0]->generatetreelist(null,'{n}.'.$model.'.id','{n}.'.$model.'.nombre','—');
		} else {
			if($mode == 'list')
				$findopts = am($findopts,array('fields'=>array('id','nombre')));
				
			$results = $this->m[0]->find($mode,$findopts);
		}

		if($mode == 'list')
			$this->m[0]->clean($results,true);
		
		if(isset($this->params['isAjax']) && $this->params['isAjax'] && ((int)$params['json']!==0)){
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");  
			header('Expires: Mon, 26 Jul 2000 05:00:00 GMT');
			header('Content-type: application/json');

			echo json_encode($results);
			exit;
		}else
			return $results;
	}

	function ajax_form($action = 'Save',$options=array()){
		if(!empty($this->data)){
			$options = array_merge($options,array(
				'success'=>'El registro se ha llevado a cabo exitósamente.',
				'failure'=>'¡Lo sentimos! Hubo un problema al realizar el registro.'
			));

			$this->m[0]->set($this->data);
			if($this->m[0]->validates())
				$this->set('successmsg',$options[$this->m[0]->{$action}($this->data) ? 'success':'failure']);
			else
				$this->set('errors',$this->m[0]->invalidFields());

			$this->set('fid',$this->params['url']['fid']);
			$this->render('form');
		}
	}
	
	function suggest(){
		$suggestions = array();
		$conds = array();
		
		if(isset($this->params['named']) && $this->params['url']['q']){
			foreach($this->params['named'] as $field => $value){
				if($this->m[0]->hasField($field))
					$conds[$field] = $value;
			}
		}
		
		if(isset($this->params['url']['q']) && $this->params['url']['q']){
			$suggestions = $this->m[0]->find_(array(
				'conditions'=>am(array($this->uses[0].'.nombre LIKE '=>'%'._enc($this->params['url']['q']).'%'),$conds),
				'fields'=>array('id','nombre')
			),'list');
	
			if(is_numeric($this->params['url']['q'])){
				$suggestionsById = $this->m[0]->find_(array(
					'conditions'=>am(array($this->uses[0].'.id'=>$this->params['url']['q']),$conds),
					'fields'=>array('id','nombre')
				),'list');
				
				if($suggestionsById && $suggestions){
					$suggestions = array_merge($suggestionsById, $suggestions);
				}elseif($suggestionsById){
					$suggestions = $suggestionsById;
				}
			}
			
			if($suggestions) $this->m[0]->clean($suggestions,true,false);			
		}

		$this->set('json',$suggestions);
		$this->render('json');
	}
	
	function admin_deletehabtm($joinModel, $id, $sortable) {
		$id = $this->_checkid($id);
		$script = 'alert("Error al eliminar")';
		
		if(isset($this->m[0]->hasAndBelongsToMany[ucfirst($joinModel)])){
			$with = $this->m[0]->hasAndBelongsToMany[ucfirst($joinModel)]['with'];
			
			if($this->m[0]->{$with}->delete($id))
				$script = '$("elistitem_'.$id.'").nix().get("reveal").chain(function(){ '.$sortable.'Sortable.reorder(); });';
		}

		$this->set('ajax',$script);
		$this->render('js');
	}
	
}
?>