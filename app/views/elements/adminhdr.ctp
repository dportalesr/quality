<?php
if(!(isset($title) && $title)){
	switch($this->action){
		case 'admin_index': $title = $_ts; break;
		case 'admin_orden': $title = 'Ordenar '.$_ts; break;
		case 'admin_images': $title = 'Imágenes'; break;
		default : $title = ucfirst(substr(strrchr($this->action,'_'),1)).' '.ucfirst($_t);
	}
}

$title = $html->tag('h1',$title);

///////

$links = isset($links) ? $links : false;
$linksHtml = '';

if($links){
	foreach($links as $link){
		if(is_string($link)){ /// Common
			switch($link){
				case 'add': $link = array('text'=>'Agregar','href'=>'agregar','class'=>'add'); break;
				case 'adder': $link = array('text'=>'Agregar','href'=>false,'class'=>'add','atts'=>array('id'=>'elist_adder')); break;
				case 'edit': $link = array('text'=>'Editar','href'=>array('action'=>'editar',$this->passedArgs[0]),'class'=>'edit'); break;
				case 'back': $link = array('text'=>'Regresar','href'=>'index','class'=>'back'); break;
				case 'export': $link = $items ? array('text'=>'Exportar','href'=>'export','class'=>'export') : false; break;
				case 'order': $link = sizeof($items)>1 ? array('text'=>'Ordenar','href'=>'orden','class'=>'order') : false; break;
				default: $link = array('text'=>ucfirst($link),'href'=>$link,'class'=>$link); break;
			}
		}

		if($link === false) continue;

		if(isset($link['atts']))
			$atts = $link['atts'];

		if(isset($link['class']))
			$atts['class'] = $link['class'];

		if($link['href'] !== false){
			if(is_array($link['href']))
				$url = $link['href'];
			else
				$url = array('action'=>$link['href']);
		}else{
			$url = 'javascript:;';
		}

		$linksHtml.= $html->link($link['text'],$url,$atts);
	}
}

///////

$filtro = isset($filtro) ? $filtro : true;
$pgcnt = isset($paginator) && isset($paginator->params['paging'][$_m[0]]['pageCount']) && $paginator->params['paging'][$_m[0]]['pageCount'];
$filtering = isset($this->params['named']['q']);

if(($pgcnt && $filtro) || $filtering){
	$filtro = $form->create(false,array('url'=>array('page'=>1))).
		$form->input('q',array('div'=>'ib','label'=>false,'placeholder'=>'Escriba ID o Nombre')).
		$form->submit('Filtrar',array('name'=>'data[action]','div'=>'submit ib')).
		(isset($this->data['q']) && $this->data['q'] ? $form->submit('reset',array('name'=>'data[action]','div'=>'submit ib','class'=>'reset','title'=>'Cancelar búsqueda')):'').
		$form->end();
} else
	$filtro = '';

//////

echo $html->div('controles',$title.$filtro.$linksHtml);

?>