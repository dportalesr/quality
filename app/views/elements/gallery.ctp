<div class="adminGallery">
<?php
$model = isset($model) && $model ? $model : $_m[0].'img';
if(!isset($m)){ App::import('Model',$model);$m = new $model(); }
$hasPortada = $m->hasField('portada');
$extraFields = array();

if($model == ''){ $extraFields = array('parent','parent_nombre','parent_id'); }

$fields = Set::normalize(am($extraFields,array_diff(array_keys($m->_schema),array('created','portada','activo','src',Inflector::underscore($_m[0]).'_id'))));

/////////

if($model == ''){
	$fields['parent'] = array('options'=>array(
	));
	$fields['parent_nombre'] = array('type'=>'text','label'=>'Artículo relacionado');
	$fields['parent_id'] = array('type'=>'text','div'=>'hide');
}

/////////

$w = isset($w) && $w ? $w : 190;
$h = isset($h) && $h ? $h : 190;

if($snaps){
	$sizeof = sizeof($snaps);

	for($i=0;$i<$sizeof;$i++){
		$thAdminClass = $inputs = '';
		
		$snap = $snaps[$i];
		$snapid = $snap[$model]['id'];
		$prefix = $model.'.'.$i;

		foreach($fields as $field => $atts){
			$atts = array_merge(array('value'=>$snap[$model][$field], 'label'=>$m->_schema[$field]['label'].':'),(array)$atts);
			
			if(isset($m->_schema[$field]['type']) && strpos($m->_schema[$field]['type'],'enum(')!==false){
				if(!isset($atts['options'])){
					$keys = explode("','",substr(substr($m->_schema[$field]['type'],6),0,-2));
					$atts['options'] = array_combine($keys,array_map('ucfirst',$keys));
				}
				$atts = array_merge(array('type'=>'select','default'=>$m->_schema[$field]['default']),$atts);
			}

			$inputs.= $form->input($prefix.'.'.$field,$atts);
		}
		
		if(isset($snap[$model]['src'])){
			$src = $snap[$model]['src'];
			$thumbnail = '';

			if($src && (strtolower(strrchr($src,'.'))==='.swf'))
				$thumbnail = $util->swf($src,array('width'=>$w,'class'=>'thAdminItem'));
			else
				$thumbnail = $html->link($resize->resize($src,array('w'=>$w,'h'=>$h)),'/'.$src,array('class'=>'pulsembox thAdminItem','escape'=>false));
			
			if($hasPortada){
				$thumbnail.= $html->link('','javascript:;',array('id'=>'thPortadaLink_'.$snapid,'class'=>'thCornerLink thPortadaLink','title'=>'Convertir este elemento en Portada'));
				if($snap[$model]['portada'])
					$thAdminClass.= ' thAdminSelected';
			}
			
			if($inputs){
				$inputs = $html->div('thAdminData',$inputs);
			} else {
				$thAdminClass.= ' inline';
			}

			echo
				$html->div('thAdmin'.$thAdminClass,null,array('id'=>'thAdmin_'.$snapid)),
					$html->link('','javascript:;',array('id'=>'thDeleteLink_'.$snapid,'class'=>'thCornerLink thDeleteLink','title'=>'Eliminar este elemento')),
					$html->div('thSpinner','',array('id'=>'thSpinner_'.$snapid)),
					$html->div('thAdminWrapper',$thumbnail),
					$inputs,
				'</div>';
			
			if($model == 'Carrusel')
				$moo->suggest($_m[0].$i.'ParentId',$_m[0].$i.'ParentNombre','/{value}/suggest', $_m[0].$i.'Parent'); // Ruteable Params: 
		}
	}

	echo $moo->buffer('galleryRequest = function(action){ $("thSpinner_"+this.id.split("_")[1]).fade("in"); new Request({ url:"/admin/'.Inflector::pluralize($model).'/"+action+"/"+this.id.split("_")[1]+"/?isAjax=1", evalScripts:true, onComplete:function(){ $("thSpinner_"+this.id.split("_")[1]).fade("out");  }.bind(this)}).get(); }'),
		$this->element('tinymce',array('size'=>65));
		
	if($hasPortada)
		$moo->addEvent('.thPortadaLink','click','if(!this.getParent(".thAdmin").hasClass("thAdminSelected")) (galleryRequest.bindWithEvent(this))("portada")',array('css'=>1));

	$moo->addEvent('.thDeleteLink','click','(galleryRequest.bindWithEvent(this))("delete")',array('css'=>1));
	$moo->buffer('$$(".thSpinner").fade("hide");');
}
?>
</div>