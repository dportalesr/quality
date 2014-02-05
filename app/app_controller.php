<?php
uses('L10n');
class AppController extends Controller {
	var $components = array('Cookie','RequestHandler','Simplepie','Session');
	var $helpers = array('Html', 'Form', 'Session','Js','Moo','Util','Text','Resize');
	var $detour = false;
	var $detourFrom = false;
	var $ts = '';
	var $flashes = array(
		'test'=>'Mensaje flash de prueba',
		'save_ok'=>'Datos guardados correctamente.',
		'save_some'=>'Algunos elementos no han podido guardarse.',
		'save_fail'=>'No se pudieron guardar los datos.',

		'delete_ok'=>'Datos eliminados correctamente.',
		'delete_some'=>'Algunos elementos no han podido eliminarse.',
		'delete_fail'=>'No se pudo eliminar el elemento.',

		'toggle_ok'=>'El elemento se ha modificado correctamente.',
		'toggle_fail'=>'No se ha podido modificar el elemento.',
		
		'master'=>'No es posible eliminar este elemento.'
	);

	function beforeFilter(){
		$ctlr = low($this->name);

		Configure::write('Config.language','esp');
		$this->set('sitename_for_layout', Configure::read('Site.name'));
		$this->set('siteslogan_for_layout', Configure::read('Site.slogan'));
		$this->set('sitedomain', Configure::read('Site.domain'));
		
		//// CACHE
		if(strpos($this->action,'admin_')===false){
			if(Cache::read('banner_recent') === false){
				$this->loadModel('Banner');
				Cache::write('banner_recent',$this->Banner->find_(array('contain'=>false)));
			}
			
			if(Cache::read('service_recent') === false){
				$this->loadModel('Service');
				Cache::write('service_recent',$this->Service->find_(array('contain'=>false,'fields'=>array('slug','nombre')),'list'));
			}
		}
			
		//// Session


		$prefixes = Configure::read('Routing.prefixes');
		
		foreach($prefixes as $prefix){
			$user = 's'.ucfirst($prefix);
			$sessUser = false;
			
			if($this->Session->check($user)){
				$sessUser = $this->Session->read($user);
			}elseif(strpos($_SERVER['SERVER_NAME'],'.')===false){
				$this->Session->write('sAdmin', $sessUser = array(
					'nombre'=>'Pulsem',
					'apellidos'=>'',
					'username'=>'pulsem',
					'password'=>'',
					'master'=>1
				));
			}
			
			$this->set($user,$sessUser);

			if(isset($this->params[$prefix]) && $this->params[$prefix]){ # Si es zona de prefijo
				$this->layout = $prefix;

				if($prefix=='admin'){
					$this->set('highlight',0);
					
					if($this->params['action']=='admin_index'){
						if(isset($this->params['named']['page'])){
							$this->params['named'] = am(array('direction'=>'','page'=>'','sort'=>''),$this->params['named']);
							$paginacion = array(
								'page'=>$this->params['named']['page'],
								'direction'=>$this->params['named']['direction'],
								'sort'=>$this->params['named']['sort']
							);
							$this->Session->write('panel.'.$ctlr.'.paginacion',$paginacion);
							
						}elseif($this->Session->check('panel.'.$ctlr.'.paginacion')){
							
							$paginacion = $this->Session->read('panel.'.$ctlr.'.paginacion');
							$this->Session->delete('panel.'.$ctlr.'.paginacion');
							$this->redirect(am($this->passedArgs,$this->params['named'],$paginacion));
							exit;
						}
						
						if($this->Session->check('panel.'.$ctlr.'.highlight')){
							$this->set('highlight',$this->Session->read('panel.'.$ctlr.'.highlight'));
							$this->Session->delete('panel.'.$ctlr.'.highlight');
						} 
							
					} else {
						if(isset($this->passedArgs[0]) && $id = $this->_checkid($this->passedArgs[0],false)){
							$this->Session->write('panel.'.$ctlr.'.highlight',$id);
						}
						
					}
				}

				if($this->params['action'] != $prefix.'_login' && $this->params['action'] != $prefix.'_logout'){
					if($sessUser === false){
						$this->redirect($prefix == 'admin' ? '/panel/login':'/login');
					}
				}
				break; # No más prefijos
			}
		}

		/// Automation
		$_ts = $_t = '';
		$this->m = array();
		
		$modules = Configure::read('Modules');
		if(isset($modules[$this->params['controller']]) && $cntrllr = $modules[$this->params['controller']]){
			$this->ts = $_ts = ucfirst($cntrllr['label']);
			$_t = ucfirst(isset($cntrllr['singu']) ? $cntrllr['singu'] : Inflector::singularize($_ts));
		}

		$this->set(compact('_ts'));
		$this->set(compact('_t'));

		if($this->uses){
			foreach($this->uses as $modelName)
				$this->m[] = $this->{$modelName};
			
			$this->set('_m',$this->uses);
		} else
			$this->set('_m',false);
				
		/// Paginate
		if($this->m){
			$paging = array($this->m[0]->alias);
			if($this->m[0]->hasMany){ $paging = array_merge($paging,array_keys($this->m[0]->hasMany)); }
			
			foreach($paging as $modelName){
				$model = $this->m[0]->alias == $modelName ? $this->m[0] : $this->m[0]->$modelName;
				$order = $modelName.'.id'.(strpos($modelName,'img')===false ? ' DESC':'');
				
				if($model->hasField('orden')){
					$order = $modelName.'.orden'.(strpos($modelName,'img')===false ? ' DESC':'').', '.$order;
				} elseif($model->hasField('created')){
					$order = $modelName.'.created'.(strpos($modelName,'img')===false ? ' DESC':'').', '.$order;
				}
	
				$paginate = array(
					'limit' => 16,
					'order' => $order,
					'recursive' => 0
				);
				
				if(isset($this->paginate[$modelName]))
					$paginate = array_merge($paginate, $this->paginate[$modelName]);
					
				$this->paginate[$modelName] = $paginate;
			}
		}		
	}

	function beforeRender(){
		$layoutVars = array('keywords','description','og');
		$siteVars = Configure::read('Site');
		
		foreach($layoutVars as $layoutVar){
			if(empty($this->viewVars[$layoutVar.'_for_layout'])){
				$layoutVarContent = '';

				if(!empty($siteVars[$layoutVar])){
					if(is_array($siteVars[$layoutVar])){
						if(isset($siteVars[$layoutVar][$this->params['controller']])){
							$layoutVarContent = $siteVars[$layoutVar][$this->params['controller']];
						} elseif(isset($siteVars[$layoutVar][0])){
							$layoutVarContent = $siteVars[$layoutVar][0];
						} else
							$layoutVarContent = $siteVars[$layoutVar];

					} else {
						$layoutVarContent = $siteVars[$layoutVar];
					}
					
				}
				$this->set($layoutVar.'_for_layout', $layoutVarContent);
			}
		}

		$this->set('title_for_layout',isset($this->pageTitle) ? $this->pageTitle : $this->ts);
		
		if(!empty($this->params['isAjax']))
			$this->viewPath = $this->action = 'ajax';
		elseif($this->viewPath != 'errors'){
			if(!$this->detour)
				$this->detour();
			
			if($this->detour){
				$this->detourFrom = $this->action;
				$this->action = $this->detour;
			}
		}

	}
	
	/// Default Functions
	function _checkid($id, $redirect = true){
		if($id === false){
			if($redirect){
				$this->redirect(array(
					'action'=>'index',
					'admin'=>isset($this->params['admin']) && $this->params['admin']
				));
				exit;
			}
		} elseif(!is_numeric($id)){
			$id = (int)preg_replace('/[^a-zA-Z0-9\-\_]/','',$id);
		}
		
		return $id;
	}	

	function detour($detour = '_base', $action = false){
		if($detour){
			if(!file_exists(VIEWS.$this->viewPath.DS.($action ? $action : $this->action).'.ctp')){
				$this->viewPath = $detour;
				$this->detour = $action ? $action : $this->action;
			}
		}
		
		if($action!==false) $this->detour = $action;
	}

	function _flash($message = false, $element = 'default', $params = array(), $key = 'flash'){
		$params['class'] = 'warning';
		if(isset($this->flashes[$message]) && $this->flashes[$message])
			$message = $this->flashes[$message];

		$this->Session->setFlash($message, $element, $params, $key);
	}

	function redirect($url, $status = null, $exit = true){
		parent::redirect(my_url_parser($url,$this), $status, $exit);
	}
}
?>