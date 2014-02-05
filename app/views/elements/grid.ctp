<?php
$fields = isset($fields) && $fields ? $fields : array();
$fields = array_merge_recursive($fields,array(
	'id'=>array('label'=>'#'),
	'created'=>array('label'=>'Creado')
	'Acciones'
));

echo $html->tag('table',null,array('class'='datagrid'));
	
$headers = array();
$defaultmodel = $_m[0];

foreach($fields as $fieldName => $fieldData){
	if(is_numeric($fieldName)){

		if(strpos('.',$fieldName)!==FALSE){
			$defaultmodel = strtok($fieldName,'.');
			$fieldName = strtok('.');
		}
			
		App::import('Model',$defaultmodel);
		$m = new $defaultmodel();
		
		if(!$m->hasField($fieldData)){
			$headers[] = $fieldData;
		}else{
			
			if($m->hasField($fieldName)){
				$headers[] = $paginator->sort($fieldData['label'], $fieldName);
			}
		}
	}
}

echo $html->tableHeaders($headers);
/*
foreach($items as $it){
	$cells = array();
	$id = $it[$_m[0]]['id'];
	
	foreach($fields as $fieldName => $fieldData){
		if(is_numeric($fieldName)){
			$headers[] = $fieldData;
		}else{
			if(strpos('.',$fieldName)!==FALSE){
				$defaultmodel = strtok($fieldName,'.');
				$fieldName = strtok('.');
			}
			
			App::import('Model',$defaultmodel);
			$m = new $defaultmodel();
			
			if(isset($fieldData['value']) && $fieldData['value']){
				
				eval('$cell[] ');
			} else {
				switch($m->_schema[$fieldName]['type']){
					case 'date': break;
					case 'datetime': break;
					case 'boolean': break;
					case 'date': break;
					
				}
				
			}
			$cells[] = $it[$defaultmodel][$fieldName];
		}
	}
}
	
*/
	
}

echo '</table>';
?>