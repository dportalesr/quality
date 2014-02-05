<?php
$full = isset($full);
$model = isset($model) && $model ? $model:$_m[0];
$hiddeable = isset($hiddeable) ? $hiddeable : true;
$class = isset($class) ? $class : '';
$showtext = isset($showtext) ? $showtext : false;
$floated = isset($floated) ? $floated : false;

if(!$hiddeable  || $paginator->params['paging'][$model]['pageCount'] > 1){
	#$paginator->options(array('url' => $this->passedArgs)); # para mantener otros parámetros de url cuando se pagine

	
	$a = array('first'=>'Primero','prev'=>'Anterior','next'=>'Siguiente','last'=>'Último');
	$c = array();
	$cT = array();
	
	//// Tipo de Paginador según contexto
	
	if(isset($this->params['admin']) && $this->params['admin']){
		$showtext = $floated = true;
		unset($a['first']);
		unset($a['last']);
	}

	if($full){
		$showtext = true;
		$a = array('prev'=>'Anterior','next'=>'Siguiente');
		$numbers = $paginator->numbers(array('separator'=>'','modulus'=>4,'first'=>2,'last'=>2));
	} else {
		$numbers = $paginator->counter(array('format' => '%page% <span>|</span> %pages%'));
	}
	
	//// Generamos los links
	
	foreach($a as $k=>$v){
		$b = $showtext ? $v : '';
		$c[] = $paginator->{$k}($b, array('class'=>$k), $b, array('class'=>'disabled '.$k));
	}
	
	//// Reordenamos según formato
	
	if($floated){
		if(sizeof($c)==4){
			$cT[] = $c[3].$c[2];
			$cT[] = $c[0].$c[1];
		} else {
			$cT[] = $c[1];	
			$cT[] = $c[0];	
		}
	} else {
		if(sizeof($c)==4){
			$cT[] = $c[0].$c[1];
			$cT[] = $c[2].$c[3];
		} else {
			$cT[] = $c[0];	
			$cT[] = $c[1];	
		}
	}

	//// Output
	
	echo
		$html->div('paginator '.$class.' '.($full ? 'full':'')),
			$html->div('buttons'),
				$cT[0],
				$html->tag('span',$numbers,array('class'=>'numbers')),
				$cT[1],
			'</div>',
		'</div>';
}
?>