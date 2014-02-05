<?php
class MooHelper extends JsHelper {
	var $helpers = array('Html','Form','Util');

	function __construct() { parent::__construct('Mootools'); }
	function inline($script = ''){ return '<script type="text/javascript">window.addEvent("domready", function(){ '.$script.' });</script>'; }
	function findParent(&$arr,$searchvalue=null,$searchkey=null,$parent=false,&$parents){
		foreach($arr as $key => &$value){
			if(is_array($value)){
				$this->findParent($value,$searchvalue,$searchkey,$key,$parents);
			} else{
				if((isset($searchkey) && isset($searchvalue) && $searchkey===$key && $searchvalue===$value) || (!isset($searchvalue) && isset($searchkey) && $searchkey===$key) || (!isset($searchkey) && isset($searchvalue) && $searchvalue===$value)){
					$parents[] = $parent ? $parent : false;
				}
			}
		}
	}

	function addEvent($element,$event,$options,$xoptions = array()){
		/// Options: update; data; url; stop; append; eval; css;
		$script = '';
		if(!is_array($options)){ /// options es el script para la forma corta de addEvent
			$script = $options;
			$options = $xoptions;
		}
		$mceEditorSave = '';

		$options = array_merge(array(
			'script'=>'',
			'spinner'=>false,
			'json'=>false,
			'url'=>false,
			'if'=>false,
			'oncomplete'=>'',
			'onsuccess'=>'',
			'onrequest'=>'',
			'onfailure'=>'',
			'update'=>false,
			'data'=>false,
			'prevent'=>false,
			'propagation'=>true,
			'confirm'=>false,
			'append'=>false,
			'evalscripts'=>true,
			'css'=>false,
			'fade'=>false
		),$options);

		if($options['url']){ // Ajax
			if(!is_string($options['url'])) $options['url'] = '"+this.getParent("form").get("action")+"';
			if($event === 'submit')
				$mceEditorSave = 'if(this.getElement(".mceEditor")){ tinyMCE.triggerSave(true,true);}';

			$script.= 'new Request';
			if($options['update'] || $options['append'])
				$script.= '.HTML';
			elseif($options['json'])
				$script.= '.JSON';

			$script.= '({ url:"'.$options['url'].'?isAjax=1", evalScripts:'.($options['evalscripts'] ? 'true':'false');

			if($options['spinner']){
				$autoid = time();
				if(is_array($options['spinner'])){
					$options['onrequest'].= ' new Element("div.spinnerLayer",{ id:"spinner_'.$autoid.'", styles:{ opacity:0 }}).inject($('.(reset($options['spinner'])).')).fade(0.6); ';
				} else {
					$options['onrequest'].= ' new Element("img",{ id:"spinner_'.$autoid.'", src:"/img/spinner.gif", alt:"Cargando...", styles:{ "margin-left": 6, "vertical-align":"middle" } }).inject($('.(is_string($options['spinner']) ? $options['spinner'] : '"'.$element.'"').'),"after"); ';
				}
				$options['oncomplete'].= '$("spinner_'.$autoid.'").destroy();';
				$options['onfailure'].= '$("spinner_'.$autoid.'").destroy();';
			}

			if($options['onrequest'])
				$script.= ', onRequest:function(){ '.$options['onrequest'].' }.bind(this)';

			if($options['oncomplete'])
				$script.= ', onComplete:function(a,b){ '.$options['oncomplete'].' }.bind(this)';

			if($options['onsuccess'])
				$script.= ', onSuccess:function(oResponse){'.$options['onsuccess'].'}.bind(this)';

			if($options['onfailure'])
				$script.= ', onFailure: function(){ '.$options['onfailure'].' }.bind(this)';
				
			if($options['update'])
				$script.= ', update:$('.$options['update'].')';

			if($options['append'])
				$script.= ', append:$('.$options['append'].')';

			$script.= '}).'.($options['data'] ? 'post':'get').'('.($options['data'] ? (is_string($options['data']) ? '$('.$options['data'].')' : 'this.getParent("form")'):'').');';

			if($options['update'] && $options['fade']){
				$script = '
					var updater = $('.$options['update'].');
					var fader = new Element("div.fader").adopt(updater.getChildren());
					var spinner = new Element("div.spinner").inject(updater);
					fader.inject(spinner).fade("out").get("tween").chain(function(){
						'.$script.'
					}.bind(this));';
			}
			$script = $mceEditorSave.$script;
		}

		if($options['confirm'])
			$script = 'if(confirm("'.$options['confirm'].'")){ '.$mceEditorSave.$script.' }';

		if($options['if'])
			$script = 'if('.$options['if'].'){ '.$script.' } ';

		if($options['propagation'])
			$script = 'e.stopPropagation(); '.$script;

		if($options['prevent'])
			$script = 'e.stop(); '.$script;

		if(strpos($event,'|')!==false){
			$exploded = explode('|',$event);
			$event = $exploded[0];
			$script = 'if(e.key == "'.$exploded[1].'"){ '.$script.' } ';
		}

		$script = ($options['css'] ? '$':'').'$("'.$element.'").addEvent("'.$event.'", function(e){ e = new Event(e); '.$script.' });';

		if($xoptions===true)
			return $script;
		else
			$this->buffer($script);
	}


	//////////////// Componentes

	function headjs($scripts = false) {
		if($scripts)
			echo $this->Html->scriptBlock('head.js("/js/'.implode('.js").js("/js/',$scripts).'.js");',array('inline'=>true));
	}

	function pbox() {
		$this->Html->css('pulsembox','stylesheet',array('inline'=>false));
		$this->Html->script('pulsembox',false);
	}

	function ajaxform($form = false, $options = array()){
		$this->Html->script('ajaxform',false);
		if(!$form) return;
		$options = $this->Util->json($options);
		$this->buffer($form.'_af = new ajaxForm("'.$form.'"'.$options.');');
	}

	function moopload($model,$maxUploads = 10,$field = 'Src',$start = 0){
		$this->Html->css('moopload','stylesheet',array('inline'=>false));
		$this->Html->script('moopload',false);
		$this->buffer('new Moopload($("'.ucfirst($model).'{n}'.ucfirst($field).'"),'.$maxUploads.','.$start.');');
	}

	function mooquee($el,$options = array()){
		$options = $this->Util->json($options);
		$this->Html->script('mooquee',false);
		$this->buffer('new mooquee("'.$el.'"'.$options.');');
	}

	function scroller($el='',$options = array()){
		$options = $this->Util->json(array_merge(array('auto'=>true),$options));
		$this->Html->script('mooscroller',false);
		if($el)
			$this->buffer('new mooScroller("'.$el.'"'.$options.');');
	}

	function showcase($el, $options=array()){
		$options = $this->Util->json($options);
		$this->Html->css('mooshowcase','stylesheet',array('inline'=>false));
		$this->Html->script('mooshowcase',false);
		$this->buffer('new mooShowcase("'.$el.'"'.$options.');');
	}

	function slider($el, $options=array()){
		$options = $this->Util->json($options);
		$this->Html->css('mooslider','stylesheet',array('inline'=>false));
		$this->Html->script('mooslider',false);
		$this->buffer('new mooSlider("'.$el.'"'.$options.');');
	}

	function suggest($inpId = false, $inpCaption = false, $url = false, $urlSelector = false, $options = array()){
		$this->Html->script('moosuggest',false);
		$this->Html->css('moosuggest','stylesheet',array('inline'=>false));

		if($inpId && $inpCaption && $url){
			$args = array($inpId,$inpCaption,$url);

			if($urlSelector) $args[] = $urlSelector;
			$args = '"'.implode('","',$args).'"';

			if($options){
				$options = $this->Util->json($options);
				$args.= ',{'.$options.'}';
			}

			$this->buffer('new mooSuggest('.$args.');');
		}
	}

	function tabs($el,$options = array()){
		$options = $this->Util->json($options);
		$this->Html->css('mootabs','stylesheet',array('inline'=>false));
		$this->Html->script('mootabs',false);
		$this->buffer('new mooTabs("'.$el.'"'.$options.');');
	}

	function placeholder($options = array()){
		$options = $this->Util->json($options,true,false);
		$this->Html->script('placeholder',false);
		$this->buffer('new NS.Placeholder('.$options.');');
	}

	function datepicker($options = array()){
		$options = array_merge(array('pickerClass'=>'datepicker_vista','lang'=>'es-ES'),$options);

		$this->Html->script('datepicker/Locale.'.$options['lang'].'.DatePicker',false);
		$this->Html->script('datepicker/Picker.js',false);
		$this->Html->script('datepicker/Picker.Attach.js',false);
		$this->Html->script('datepicker/Picker.Date.js',false);
		$this->buffer('Locale.use("'.$options['lang'].'");');

		$this->Html->css('/js/datepicker/'.$options['pickerClass'].'/style','stylesheet',array('inline'=>false));

		$options = $this->Util->json($options,true,true,array('onSelect'));
		$this->buffer('new Picker.Date($$(".datepicker")'.$options.');');
	}

	function pop($msg = false, $inline = false){
		if(!$msg) $msg = '#flashMessage';
		
		$script = 'mooPop("'.$msg.'");';

		if($inline)
			return $this->inline($script);
		else
			$this->buffer($script);
	}

	function highlight($id = false,$inline = false){
		if(!$id) return;
		$script = 'if($("it'.$id.'")) new Fx.Scroll(window,{ onComplete:function(){ $("it'.$id.'").getParent("tr").set("tween",{duration:5000}).highlight(); }}).toElementCenter("it'.$id.'");';

		if($inline)
			return $this->inline($script);
		else
			$this->buffer($script);
	}

	function player($video = true, $src = false, $options = array()){
		if($video){
			$this->Html->script('flowplayer-3.2.3.min',false);
			$this->buffer('flowplayer(".vPlayer", "/swf/flowplayer-3.2.3.swf");');
		} else {
			$options = array_merge(array('width'=>'100%'),$options);
			$this->Html->script('audio-player.js',false);
			$this->buffer('AudioPlayer.setup("/swf/player.swf", { width: "'.$options['width'].'",transparentpagebg: "yes" });');
			if($src){
				$options = array_merge(array('id'=>'pPlayer'),$options);
				$src = (strpos($src,'http://')!== 0 ? '/':'').$src;
				$this->buffer('AudioPlayer.embed("'.$options['id'].'", { soundFile: "'.$src.'" });');
			}
		}
	}

	function elist($model,$fields = array('id','nombre'),$options = array(),$atts=array()){ // Legacy
		return $this->element('elist',compact('model','fields','options','atts'));
	}

	function inlabel($data){
		$this->Html->script('mooinlabel',false);
		$data = $this->Util->json($data,false,false);
		$this->buffer('new mooInlabel({'.$data.'});');
	}
}
?>