<?php
$model = isset($model) && $model ? $model : $_m[0];
$fields = isset($fields) && $fields ? Set::normalize($fields) : array('id'=>'','nombre'=>'');
$options = isset($options) && $options ? $options : array();
$atts = isset($atts) && $atts ? $atts : array();

$listHTML = '';
$data = false;

$options = array_merge(array(
	'data'=>false,
	'offset'=>0, // Offset en caso de ser resultados paginados
	'sort'=>false, // Reordenable?
	'adder'=>false, // Botón para agregar elementos
	'remover'=>false, // Botón para eliminar
	'confirmdelete'=>false,
	'zoom'=>false,
	'images'=>false,
	'custom'=>false,
	'oncreate'=>false,
	'ondelete'=>false
),$options);

if(!isset($options['offset'])) $options['offset'] = 0; // Prevenir contra variables no seteadas

if(isset($this->data[$model])){ // $this->data tiene mayor precedencia que options[data]
	$data = array();
	foreach($this->data[$model] as $idx => $xdata){ // Formateo data
		if(is_numeric($idx))
			$data[$idx] = array($model => $xdata);
	} 
}

if((!$data) && isset($options['data']) && $options['data']) { // No hay $this->data? => $options[data]
	$data = $options['data'];
	unset($options['data']);
}

$listSize = $data ? sizeof($data) : 1;
$id = $atts['id'] = isset($atts['id']) && $atts['id'] ? $atts['id'] : $model.'_elist';

// HTML del elemento de lista base

$base_list_item = $editable_fields = array();
$this->Moo->findParent($fields,1,'edit',null,$editable_fields);# Obtenemos los campos definidos como editables para copiarlos al demo (campos con editable === 1)

/// Lista con datos existentes

for($i=0;$i<$listSize;$i++){
	$list_item = '';

	if($options['sort']){
		$list_item.= $base_list_item['sort'] = $this->Html->tag('span','',array('class'=>'elist_button elist_handler','title'=>'Arrastra para ordenar'));
	}

	if($options['zoom'] && isset($data[$i][$model]['id']) && $data[$i][$model]['id'])
		$list_item.= $this->Html->link('',array($data[$i][$model]['id']),array('class'=>'elist_button elist_zoom','title'=>'Ver'));

	foreach($fields as $field => $f_data){
		$editable = false;
		$fields[$field] = $f_data = (array)$f_data;
		$value = '';

		// Asignar Modelo
		$def_model = $model;
		if(strpos($field,'.')!== false){ $def_model = strtok($field,'.');$field = strtok('.'); }

		if(isset($data[$i][$def_model][$field]))
			$value = $data[$i][$def_model][$field];

		// Defaults
		$f_atts = array(
			'label'=>false,
			'class'=>'',
			'div'=>'',
			'type'=>'text',
			'value'=>_dec($value),
			'format'=>array('before', 'between', 'input', 'after','label', 'error')
		);
		$f_opts = array('edit'=>false,'hide'=>true,'separator'=>false);
		$base_f_atts = array('value'=>''); // Atts for new list items (base_list_item)

		// Split
		$f_atts = array_merge($f_atts,array_intersect_key($f_data,$f_atts));
		$f_opts = array_merge($f_opts,array_intersect_key($f_data,$f_opts));

		if($f_opts['edit']){
			$editable = true;
		}
		
		#fb($field,'=======');fb(compact('f_atts','f_opts'));

		// Concatenar
		$f_atts['div'] .= ' ib';
		$f_atts['class'] .= ' elist_input elist_input_unselected';

		// Previene type vacío = textarea; DELETE by fix: default 'text' in $f_atts
		//if(!$f_atts['type']) unset($f_atts['type']);

		if($f_atts['type'] == 'checkbox' && $value){
			$f_atts['checked'] = 'checked';
			$base_f_atts['checked'] = '';
		}

		// editable
		if($editable){
			$base_f_atts = array_merge($f_atts,$base_f_atts);
			$base_list_item[implode('.',array($def_model,'{n}',$field))] = $this->Form->input(implode('.',array($def_model,'{n}',$field)), $base_f_atts);
			
		} else {
			if($field === 'id'){
				$f_atts['type'] = 'text';
				$f_atts['class'] = 'elist_id';
				$f_atts['div'] = 'hide';
			} else 
				$f_atts['type'] = 'hidden';
		}
		
		$list_item.= $this->Form->input(implode('.',array($def_model,$i,$field)), $f_atts);

		// Si el campo se debe mostrar como etiqueta o es tipo archivo, y no es vacío
		if((is_numeric($value) || !empty($value)) && (((!$f_opts['hide']) && (!$editable)) || $f_atts['type'] == 'file')){
			$tagTxt = '';

			if($f_opts['separator']) $list_item.= $f_opts['separator'];
			
			if($f_atts['type'] == 'file'){ // Tipo archivo
				$tagTxt = $this->Html->link(
					basename($value),
					array('admin'=>false,'controller'=>Inflector::tableize($def_model),'action'=>'download',$data[$i][$def_model]['id'])
				);
			} else {
				if($f_atts['label']) $tagTxt = $f_atts['label'].': ';
				$tagTxt.= $value;
			}
			
			$list_item.= $this->Html->tag('span',$tagTxt,array('class'=>'elist_tag '.$f_atts['class'])); #!?
		}
	}

	if($options['sort']){
		$orden_atts = array(
			//'value'=>isset($data[$i][$model]['orden']) ? $data[$i][$model]['orden']:$i+$options['offset'], #!?
			'value'=>$i+$options['offset'],
			'type'=>'text',
			'label'=>false,
			'class'=>'orderInput',
			'div'=>'hide'
		);

		$list_item.= $this->Form->input(implode('.',array($model,$i,'orden')),$orden_atts);
		$base_list_item['orden'] = $this->Form->input(implode('.',array($model,'{n}','orden')),array_merge($orden_atts,array('value'=>'')));
	}

	//if($data && $options['custom']){ #!?
	if($options['custom'] && isset($data[$i][$model]['id']) && $data[$i][$model]['id']){
		foreach($options['custom'] as $custom){
			$custom_text = $custom['text']; unset($custom['text']);
			$custom_action = $custom['action']; unset($custom['action']);
			$custom_url = array_merge(array('admin'=>true,'action'=>$custom_action,$data[$i][$model]['id']),$custom);

			$list_item.= $this->Html->link($custom_text, $custom_url, array('class'=>'datagridButton'));
		}
	}

	if($options['remover']){
		$list_item.= $base_list_item['remover'] = $this->Html->link('','javascript:;',array('class'=>'elist_remove elist_button','title'=>'Eliminar'));
	}

	$listHTML_atts = array();
	if(isset($data[$i][$model]['id']) && $data[$i][$model]['id'])
		$listHTML_atts['id'] = 'elistitem_'.$data[$i][$model]['id'];

	$listHTML.= $this->Html->div('elist_item',$list_item,$listHTML_atts); #!?
	//$listHTML.= $this->Html->div('elist_item',$list_item,array('id'=>'elistitem_'.($i+$options['offset'])));
}

$base_list_item = str_replace("\n",'',str_replace("\r",'',implode('',$base_list_item))); 

$this->Moo->buffer('
	Sortables.implement({
		reorder:function(){
			'.($options['sort'] ? 'this.serialize(false,function(el,idx){ el.getElement("input.orderInput").set("value", this.lists[0].childElementCount - (idx+1) + '.$options['offset'].'); }.bind(this));' : '').'
			return this;
		}
	});

	var '.$id.'Sortable = new Sortables("'.$id.'", {
		handle:".elist_handler",
		onComplete:function(){ this.reorder(); },
		revert:{ duration: 500, transition: "pow:in:out" },
		opacity:0.4,
		snap:10,
		clone:false,
		constrain:true
	}).reorder();
');

if($options['remover']){
	$remove_url = is_string($options['remover']) ? $options['remover'] : '/admin/'.Inflector::tableize($model).'/eliminar/';
	$remove_script = '
		var item = this.getParent();
		var item_id = item.getElement(".elist_id");

		if(item_id){
			item_id = item_id.value.toInt();

			new Request({
				url:"'.$remove_url.'"+item_id+"/'.$id.'",
				onRequest:function(){
					new Element("img",{
						id:"spinner_"+item_id,
						src:"/img/spinner.gif",
						alt:"Cargando...",
						styles:{ "margin-left": 6 }
					}).inject(this,"after");
				}.bindWithEvent(this),
				onComplete:function(){
					$("spinner_"+item_id).destroy();
				}.bindWithEvent(this),
				evalScripts:true
			}).send();
		} else {
			item.nix().get("reveal").chain(function(){ '.$id.'Sortable.reorder(); });
		} '.($options['ondelete'] ? $options['ondelete'] :'');

	if($options['confirmdelete'])
		$remove_script = 'if(confirm("¿Seguro quiere eliminar el elemento?")){'.$remove_script.'}';

	$this->Moo->addEvent('.elist_remove','click',$remove_script,array('css'=>1));
}

if($options['adder']){
	$add_script = '
	newelistitem = new Element("div",{
		"class":"elist_item",
		styles:{ display:"none" },
		html:(\''.$base_list_item.'\').substitute({ n:'.$id.'Sortable.lists[0].childElementCount })
	}).inject($("'.$id.'")).reveal();';

	if($options['remover'])
		$add_script.= 'newelistitem.getElement("a.elist_remove").addEvent("click", function(e){ '.$remove_script.' });';

	$add_script.= 'newelistitem.getElements(".elist_input").addEvents({
		"focus":function(){ this.removeClass("elist_input_unselected"); }.bindWithEvent(this),
		"blur":function(){ this.addClass("elist_input_unselected"); }.bindWithEvent(this)
	}); '.$id.'Sortable.addItems(newelistitem).reorder();';


	if($options['oncreate'])
		$add_script.= $options['oncreate'];

	$this->Moo->addEvent($options['adder'],'click',$add_script);
}

echo $this->Html->div('elist',$listHTML,$atts);
?>