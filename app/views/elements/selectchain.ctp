<?
$baseModel = isset($baseModel) && $baseModel ? $baseModel : $_m[0];
$scripts = array();
$first = true;

while($data){
	$fkey = isset($data['fkey']) ? $opts['fkey'] : Inflector::singularize($data['model']).'_id';
	$reqUrl = array('controller'=>$data['model'],'action'=>'last','activo'=>1,'mode'=>'list','arrayUrl'=>1);

	if(isset($data['params']))
		$reqUrl = am($reqUrl,$data['params']);

	$atts = array('empty'=>'-- Seleccione --');	

	if($first)
		$atts = am(array('options'=>$this->requestAction($reqUrl)),$atts);

	if(isset($data['label']))
		$atts = am($atts,array('label'=>$data['label']));

	if(isset($data['selected']))
		$atts = am($atts,array('selected'=>$data['selected']));

	if(isset($data['input']))
		$atts = am($atts,$data['input']);
		
	echo $form->input(($baseModel ? $baseModel.'.':'').$fkey,$atts);
	
	if(isset($data['next'])){
		//fb('hasNext');
		$next = $data['next'];
		
		$nextfkey = isset($next['fkey']) ? $next['fkey'] : Inflector::singularize($next['model']).'_id';
		//$reqUrl = array('controller'=>$data['model'],'action'=>'last','activo'=>1,'mode'=>'list');

		//fb($nextfkey,'nextfkey');
		//fb($reqUrl,'nextReqUrl');
		
		$targetSelect = $baseModel.Inflector::camelize($nextfkey);
		$triggerSelect = $baseModel.Inflector::camelize($fkey);
		
		//fb($targetSelect,'targetSelect');
		
		$moo->addEvent($triggerSelect,'change',
			array(
				'json'=>1,
				'onsuccess'=>'
					var targetSelect = $("'.$targetSelect.'");
					targetSelect.empty();
					new Element("Option",{ "text":"-- Seleccione --", "value":"" }).inject(targetSelect);
					resp = new Hash(oResponse).each(function(label, value){
						new Element("Option",{ "text":label, "value":value }).inject(targetSelect);
					}); ',
				'url'=>'/'.$next['model'].'/last/activo:1/mode:list/'.$fkey.':"+$("'.$triggerSelect.'").value+"'
			)
		);

		$moo->addEvent($targetSelect,'focus','
			e = new Event(e);
			e.stopPropagation();
			if(this.getElements("option").length < 2)
			new Request.JSON({
				url:"/'.$next['model'].'/last/activo:1/mode:list/'.$fkey.':"+$("'.$triggerSelect.'").value+"?isAjax=1",
				evalScripts:true,
				onSuccess:function(oResponse){
					var targetSelect = $("'.$targetSelect.'");
					targetSelect.empty();
					new Element("Option",{ "text":"-- Seleccione --", "value":"" }).inject(targetSelect);
					resp = new Hash(oResponse).each(function(label, value){
						new Element("Option",{ "text":label, "value":value }).inject(targetSelect);
					});
				}
			}).get();');

/*
		$moo->addEvent($triggerSelect.'Link','click',
			array(
				'json'=>1,
				'onsuccess'=>'
					var targetSelect = $("'.$targetSelect.'");
					targetSelect.empty();
					new Element("Option",{ "text":"-- Seleccione --", "value":"" }).inject(targetSelect);
					resp = new Hash(oResponse).each(function(label, value){
						new Element("Option",{ "text":label, "value":value }).inject(targetSelect);
					}); ',
				'url'=>'/'.$next['model'].'/last/activo:1/mode:list/'.$fkey.':"+$("'.$triggerSelect.'").value+"'
			)
		);
*/

	}
	
	$data = isset($data['next']) ? $data['next'] : false;
	$first = false;
}
?>