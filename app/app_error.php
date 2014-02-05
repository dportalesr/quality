<?php
class AppError extends ErrorHandler {
	function error($params, $type) {
		if(Configure::read('debug')===0){
			$errorMgs = array(
				'*Le Error*',
				'*A Wild error appeared!!*',
				'OH, NOES!!1!',
				'Epic Fail.',
				'La página que buscas no existe. MINDBLOW!',
				'x_x',
				'¡Mayday! ¡Mayday!',
				'Hasta al mejor cazador...',
				'Pasa hasta en las mejores familias.',
				'"Si algo puede salir mal, saldrá mal" —Edward A. Murphy Jr',
				':\'(',
				'¡Esto es vergonzoso!',
				'¡Sorry, Darling!',
				'¡Callejón sin salida!',
				'Algo dejó de funcionar.',
				'No correr, no empujar, no gritar.',
				'Nunca lo hubieramos imaginado...',
				'Esto no se supone que pasara...',
				'¡Aw, snap!',
				'¡Houston, tenemos un problema!',
				'¡Hey, esto no estaba en el guión!',
				'¡Vaya, parece que algo ha salido mal!'
			);
			
			$this->controller->output = null;
			$this->controller->layout = 'error';
			$this->controller->pageTitle = $errorMgs[array_rand($errorMgs)];
		
			$this->controller->set('type', $type);
			$this->controller->set('params', $params);
			
			$this->controller->render('error');
			$this->controller->afterFilter();
			
			echo $this->controller->output;
		
		} else {
			parent::$type($params);
		}
	}
	function error404($params) { return $this->error($params, __FUNCTION__); }
	function missingController($params) { return $this->error($params, __FUNCTION__); }
	function missingAction($params) { return $this->error($params, __FUNCTION__); }
	function missingTable($params) { return $this->error($params, __FUNCTION__); }
	function missingDatabase($params = array()) { return $this->error($params, __FUNCTION__); }
	function missingView($params) { return $this->error($params, __FUNCTION__); }
	function missingLayout($params) { return $this->error($params, __FUNCTION__); }
	function missingConnection($params) { return $this->error($params, __FUNCTION__); }
	function missingHelperFile($params) { return $this->error($params, __FUNCTION__); }
	function missingHelperClass($params) { return $this->error($params, __FUNCTION__); }
	function missingComponentFile($params) { return $this->error($params, __FUNCTION__); }
	function missingComponentClass($params) { return $this->error($params, __FUNCTION__); }
	function privateAction($params) { return $this->error($params, __FUNCTION__); }
	function missingModel($params) { return $this->error($params, __FUNCTION__); }
}	
?>