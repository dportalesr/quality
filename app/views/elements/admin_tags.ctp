<?php
$model = isset($model) && $model ? $model : 'Tag';

echo
	$html->div('addTag'),
		$html->link('Agregar','javascript:;',array('class'=>'adminButton add')),
		$form->input('addTag',array('label'=>false,'div'=>'ib addTagText','id'=>'addTag','name'=>'data[addTag]')),
	'</div>';

$moo->buffer('var addTagCount = 0, addTagReady = true;');
$moo->addEvent('form','keypress','if(e.key=="enter") e.stop();',array('css'=>1));

$complete = '
	var el = $("'.$model.'AddTag"+addTagCount);
	var label = $$("label[for='.$model.'AddTag"+addTagCount+"]");
	if(!label){ return false;}

	if(el.get("checked")){
		label.addClass("checked");
	}else{
		label.removeClass("checked");
	}
		
	el.addEvent("change",function(e){
		var ev = new Event(e);
		var chk = ev.target;
		if(chk.checked){
			this.addClass("checked");
		}else{
			this.removeClass("checked");
		}
		
	}.bind(label));
	
	addTagCount++;
	addTagReady = true;
	$("addTag").set("value","");
';

$moo->addEvent('addTag','keyup','addTagReady = false;',array(
	'data'=>'this.getParent("form")',
	'prevent'=>true,
	'spinner'=>true,
	'url'=>'/admin/'.Inflector::tableize($model).'/agregar/"+addTagCount+"',
	'oncomplete'=>$complete,
	'append'=>'this.getParent(".input.select")',
	'if'=>'this.get("value").trim() != "" && addTagReady == true && e.key =="enter"'
));

$moo->addEvent('.addTag a','click','addTagReady = false;',array(
	'data'=>'this.getParent("form")',
	'spinner'=>'"addTag"',
	'css'=>1,
	'url'=>'/admin/'.Inflector::tableize($model).'/agregar/"+addTagCount+"',
	'oncomplete'=>$complete,
	'append'=>'this.getParent(".input.select")',
	'if'=>'this.getNext(".addTagText").getFirst().get("value").trim() != "" && addTagReady == true'
));
?>