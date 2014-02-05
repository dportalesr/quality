<?php
/*
* 16/08/10 - Soporte para varias configuraciones de modelo. Fix para guardar orden por parent.
* 20/10/10 - Fix para no recalcular orden sólo cuando sea creación (no id en data), funciona para el caso de nodos donde se guarda el parent con los hijos
* 28/02/11 - Fix lógica guardar por parent
*/
class OrdenableBehavior extends ModelBehavior{
	var $settings = array();
	
	function setup(&$model, $settings = array()){
		$this->settings[$model->alias] = am(array('group'=>false),$settings);
		
		if($group = $this->settings[$model->alias]['group']){
			if($group === true && $model->belongsTo){
				$parent = reset($model->belongsTo);
				$group = $parent['foreignKey'];
				
			} elseif(!$model->hasField($group)) {
				$group = false;
			}
			
		} else $group = false;
		
		$this->settings[$model->alias]['group'] = $group;
	}

	function beforeSave(&$model){
		$data = $model->data;
		if(
			(!isset($model->data[$model->alias]['id'])) && // Es alta de registro
			(
				(!isset($model->data[$model->alias]['orden'])) ||
				(!is_numeric($model->data[$model->alias]['orden'])) ||
				($model->data[$model->alias]['orden'] < 0)
			)
		){
			//$table = $model->table;
			$conds = array();
			
			if($group = $this->settings[$model->alias]['group']){
				if(isset($model->data[$model->alias][$group]) && $model->data[$model->alias][$group]){
					$conds = array($model->alias.'.'.$group => $model->data[$model->alias][$group]);
				}
			}

			$result = $model->find('first',array('fields'=>'MAX('.$model->alias.'.orden) as result','conditions'=>$conds));
			$model->data = $data;
			$model->data[$model->alias]['orden'] = $result[0]['result']+1;
		}
		 
		return true;
	}
}
?>