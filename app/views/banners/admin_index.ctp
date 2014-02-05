<?php
echo
	$this->element('adminhdr',array('links'=>array('add','order'))),
	$this->element('pages');
	
	if($items){
	echo
		$form->create('todelete',array('url'=>$this->here,'id'=>'todeleteForm')),
		$form->submit('Eliminar marcados',array('div'=>'deleteSubmit')),
		$html->tag('table',null,array('class'=>'datagrid')),
		$html->tableHeaders(array(
			$paginator->sort('#', 'id'),
			'Banner',
			$paginator->sort('Nombre', 'nombre'),
			$paginator->sort('Enlace', 'enlace'),
			$paginator->sort('Tipo', 'tipo'),
			$paginator->sort('Activo', 'activo'),
			$paginator->sort('Caducidad', 'caducidad'),
			$paginator->sort('Creado', 'created'),
			'Acciones'
		));

		foreach($items as $it){ $id = $it[$_m[0]]['id'];
			$id = $it[$_m[0]]['id'];
			$tipos = array('carrusel'=>'Carrusel','lateral'=>'Lateral');
			echo $html->tableCells(array(array(
				$form->input($id,array('type'=>'checkbox','div'=>'hide','class'=>'delete')).$html->link($id,'javascript:;',array('class'=>'id','id'=>'it'.$id)),
				$it[$_m[0]]['src'] ? $html->link($html->image('admin/search.gif'),'javascript:;',array('class'=>'zoom tipCaller','escape'=>false,'rel'=>$this->element('banners',array('escape'=>1,'data'=>array($it),'w'=>160,'all'=>1)))):'-',
				$it[$_m[0]]['nombre'],
				$it[$_m[0]]['enlace'],
				$it[$_m[0]]['tipo'],
				$util->toggle($it[$_m[0]]['activo'],$it[$_m[0]]['id']),
				$it[$_m[0]]['caducidad'] ? $util->fdate('d',$it[$_m[0]]['caducidad']) : '-',
				$util->fdate('s',$it[$_m[0]]['created']),
				array(
					$html->link('Editar',array('admin'=>1,'action'=>'editar',$id)).
					$html->link('Eliminar',array('admin'=>1,'action'=>'eliminar',$id),null,'¿Seguro que quiere eliminar el elemento?')
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