<div style="padding:15px;border:#CCC solid 1px;margin:0;margin-top:10px;">
	<h1 style="font-weight:normal;font-size:24px;margin:0;margin-bottom:18px;color:#444">Mensaje desde <?=Configure::read('Site.domain')?></h1>
<?php
	$labels = array('telefono'=>'Teléfono');
	$skip = array();

	foreach($fields as $field){
		$field_data = implode(', ',(array)$data[$field]);
		$label = (in_array($field, $labels) ? $labels[$field] : ucfirst($field)).': ';

		if(!in_array($field, $skip)){
			if($field != 'mensaje')	
				echo $html->para(null,$html->tag('strong',$label).$field_data);
			else
				echo
					$html->para(null,$html->tag('strong',$label)),
					$html->div('',$field_data,array('style'=>'background:#F6F6F6;padding:10px;margin-bottom:8px;'));
		}
	}
?>
</div>