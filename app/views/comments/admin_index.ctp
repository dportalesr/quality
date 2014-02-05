<?php
echo
	$this->element('adminhdr'),
	$this->element('pages');
	
	if($items){
	echo
		$form->create('todelete',array('url'=>$this->here,'id'=>'todeleteForm')),
		$form->submit('Eliminar marcados',array('div'=>'deleteSubmit')),
		$html->tag('table',null,array('class'=>'datagrid')),
		$html->tableHeaders(array(
			$paginator->sort('#', 'id'),
			$paginator->sort('Autor', 'autor'),
			$paginator->sort('Email', 'email'),
			$paginator->sort('Comentario', 'descripcion'),
			$paginator->sort('En', 'Post.nombre'),
			$paginator->sort('Publicado', 'created'),
			'Acciones'
		));

		foreach($items as $it){
			$id = $it[$_m[0]]['id'];
			$parent = isset($it[$_m[0]]['parent']) && $it[$_m[0]]['parent'] ? Inflector::classify($it[$_m[0]]['parent']) : 'Post';
			$url = array(
				'controller'=>Inflector::tableize($parent),
				'action'=>'ver',
				'admin'=>0,
				$it[$parent]['slug']
			);
			
			echo $html->tableCells(array(array(
				$form->input($id,array('type'=>'checkbox','div'=>'hide','class'=>'delete')).$html->link($id,'javascript:;',array('class'=>'id','id'=>'it'.$id)),
				$it[$_m[0]]['autor'],
				$it[$_m[0]]['email'],
				$util->txt($util->trim($it[$_m[0]]['descripcion'])),
				$html->link($it[$parent]['nombre'], $url, array('target'=>'_blank')),
				$util->fdate('s',$it[$_m[0]]['created']),
				array(
					$html->link('Ver/Responder',am($url, array('reply'=>true, 'comment'=>'#comment_'.$id)),array('target'=>'_blank')).
					$html->link('Editar',array('action'=>'editar','admin'=>1,$id)).
					$html->link('Eliminar',array('action'=>'eliminar','admin'=>1,$id),null,'¿Seguro que quiere eliminar este elemento?')
				,array('class'=>'actions'))
			)),array('class'=>'odd'));
		}
	echo
		'</table>',
		$form->submit('Eliminar marcados',array('div'=>'deleteSubmit'));
			
	} else echo $html->para('noresults','No hay elementos para mostrar');
	
	if($items)
		$moo->addEvent('todeleteForm','submit','e.stop(); if(confirm("Se eliminarán los elementos seleccionados. ¿Desea continuar?")){ this.submit(); } ');
	$moo->addEvent('.datagrid tr','click','this.removeProperty("style"); this.toggleClass("selected"); this.getElement(".delete").set("checked",!this.getElement(".delete").get("checked"));',array('css'=>1));
	$moo->addEvent('.datagrid a:not(.id)','click','e.stopPropagation();',array('css'=>1));
?>