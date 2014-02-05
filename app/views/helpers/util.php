<?php
class UtilHelper extends AppHelper {
	var $helpers = array('Html','Form','Text','Javascript','Resize');

	function trim($text,$long=180) { return $this->Text->truncate($text,$long,array('ending'=>' [..]','exact'=>true,'html'=>true)); }
	function txt($text,$decode = false) {
		$text = $decode ? _dec($text):$text;
		return nl2br($this->Text->autoLink(strip_tags($text),array('rel'=>'nofollow','target'=>'_blank')));
	}

	function th($item, $model = false, $opts = array()){
		$th = $footer = '';
		$resizeopts = array();
		$opts = array_merge(
			array(
				'id'=>'',
				'class'=>'',
				'atts'=>array(),
				'url'=>false,
				'field'=>'src',
				'w'=>0,
				'h'=>0,
				'descripcion'=>'descripcion',
				'fill'=>false,
				'pad'=>false,
				'footer'=>false
			),$opts
		);

		if(!$item) return '';

		if($model){
			if(array_key_exists($model.'portada',$item))
				$item = $item[$model.'portada'];
			elseif(array_key_exists($model,$item))
				$item = $item[$model];
		}

		if(!empty($item[$opts['field']])){
			if(!empty($opts['id'])){ $opts['atts']['id'] = $opts['id']; unset($opts['id']); }
			if(!empty($opts['class'])){ $opts['atts']['class'] = $opts['class']; unset($opts['class']); }

			if(!empty($opts['atts']['class']))
				$opts['atts']['class'].= ' thWrap';
			else
				$opts['atts']['class'] = 'thWrap';

			if($opts['footer']){
				if($opts['footer'] === true)
					$opts['footer'] = $item[$model.'portada']['descripcion'];

				if(!empty($opts['footer']))
					$footer = $this->Html->para('imagefoot',$this->txt($opts['footer']));
			}

			if($opts['descripcion'] && $opts['url']){
				if(!empty($item[$opts['descripcion']])){
					$opts['atts']['title'] = substr(strip_tags(_dec($item[$opts['descripcion']])),0,155);
					$opts['atts']['name'] = $item[$opts['descripcion']];
				}
			}

			if($opts['w'] || $opts['h']){
				if($opts['w']) $resizeopts['w'] = $opts['w'];
				if($opts['h']) $resizeopts['h'] = $opts['h'];
				if($opts['fill']) $resizeopts['fill'] = true;
				if($opts['pad']) $resizeopts['pad'] = true;
			}
			
			if(array_key_exists($opts['field'],$item)){
				if($resizeopts){
					$th = $this->Resize->resize($item[$opts['field']],$resizeopts);
				} else {
					$th = $this->Html->image('/'.$item[$opts['field']]);
				}
			}

			if(empty($th)) return '';

			if($opts['url']){
				if($opts['url']===true){
					$opts['url'] = '/'.$item[$opts['field']];
					$opts['atts']['rel'] = 'roller';
					$opts['atts']['class'].= ' pulsembox';
				}
				
				$th = $this->Html->link($this->Html->tag('span',$th,'thMask').$footer,$opts['url'],$opts['atts']);
			} else
				$th = $this->Html->div(null,$this->Html->div('thMask',$th).$footer,$opts['atts']);

		
		}// else $th = $this->Html->div(null,$th.$footer,$opts['atts']);

		return $th;
	}

	function ofuscar($text, $mode = true, $atts = array()) {
		if(is_array($text)){
			list($text,$caption) = $text;
			$ofus = $caption;
			
		} else {
			$long = strlen($text);
			$ofus = '';
			for ($i=0; $i<$long; ++$i)
				$ofus.= '&#'.ord(substr($text,$i,1)).';';
		}
		
		switch($mode){
			case true:
			case 'link':
				$ofus = $this->Html->link($ofus,'mailto:'.$text,$atts);
			break;
			case 'span':
				$ofus = $this->Html->tag('span',$ofus,$atts);
			break;
			default:
			break;
		}
			
		return $ofus;
	}

	function generate($n = 6, $model = false, $fields = array('id','nombre','descripcion','slug')) {
		$model = $model ? $model : $_m[0];
		$dummydata = array();
		$defaults = array('id'=>'integer','nombre'=>'string','slug'=>'slug','descripcion'=>'text');
		$lipsum = 'Lorem ipsum dolor sit amet consectetuer adipiscing elit Pellentesque id massa Duis sollicitudin ipsum vel diam Aliquam pulvinar sagittis felis Nullam hendrerit semper elit Donec convallis mollis risus Cras blandit mollis turpis Vivamus facilisis sapien at tincidunt accumsan arcu dolor suscipit sem tristique convallis ante ante id diam Curabitur mollis lacus vel gravida accumsan enim quam condimentum est vitae rutrum neque magna ac enim';

		for($i=0;$i<$n;$i++){
			$row = array();
			foreach($fields as $f => $ftype){
				if(is_numeric($f)){
					$f = $ftype;
					if(array_key_exists($ftype,$defaults))
						$ftype = $defaults[$ftype];
					else
						$ftype = 'string';
				}

				switch($ftype){
					case 'text': $row[$f] = $lipsum; break;
					case 'int': $row[$f] = rand(1,1000); break;
					case 'slug': $row[$f] = trim(Inflector::slug(substr($lipsum,rand(0,425),30))); break;
					case 'string':
					default:
						$row[$f] = ucfirst(trim(substr($lipsum,rand(0,405),50))); break;
				}
			}

			$dummydata[] = array($model=>$row);
		}

		return $dummydata;
	}

	function named($array) {
		$qry = '';
		foreach($array as $k => $v)
			$qry.= '/'.$k.':'.$v;

		return $qry;
	}

	function tip($text) {
		$title = null;
		if(is_array($text)) list($text,$title) = $text;
		return $this->Html->link($this->Html->image('admin/info.gif'),'javascript:;',array('class'=>'tipCaller hint','title'=>$title,'rel'=>$text));
	}

	function toggle($value, $id = false, $field = 'activo', $exclusive = false) {
		$toggle = $this->Html->image('admin/'.($value ?'yes':'no').'.png');
		if($id) $toggle = $this->Html->link($toggle, array_merge(array('admin'=>1, 'action'=>'toggle', $id, $field, $exclusive),$this->params['named']));
		return $toggle;
	}

	function uploadinfo($field,$uploadsrc) {
		if((!empty($uploadsrc)) && is_string($uploadsrc)){
			$model = false;
			if(strpos($field,'.') !== FALSE){
				$model = strtok($field,'.');
				$field = strtok('.');
			}

			$ext = strtolower(strrchr($uploadsrc,'.'));
			$esDoc = true;
			$esFlash = false;
			if(in_array($ext,array('.jpg','.jpeg','.gif','.png','.swf'))){
				$esDoc = false;
				$esFlash = $ext == '.swf';
				$newSize = $this->resize($uploadsrc,160);
				$uploadinfo = strtolower(strrchr($uploadsrc,'.'))=='.swf' ? $this->swf($uploadsrc,array('class'=>'preview','width'=>$newSize[0],'height'=>$newSize[1])):$this->Html->image('/'.$uploadsrc,array('class'=>'preview','width'=>$newSize[0],'height'=>$newSize[1]));
			} else {
				$uploadinfo = $this->Html->link(basename($uploadsrc),'/'.$uploadsrc).' | ';
			}
			$filetype = $esFlash ? 'el flash' :($esDoc ? 'el archivo':'la imagen');

			$uploadinfo.= $this->Form->input(($model ? $model.'.' : '').$field.'_delete', array('type'=>'checkbox','label'=>array('text'=>'Eliminar','class'=>'inlinelabel'),'div'=>false)).$this->tip('Si selecciona esta opción, debe guardar para eliminar '.$filetype.'. No es necesario marcar esta opción si ha seleccionado otro archivo para reemplazar.');
		} else
			$uploadinfo = 'No se ha subido archivo';

		return $this->Html->tag('div',$uploadinfo,array('class'=>'uploadinfo'));
	}

	function recursivelist($data = false, $opts = array(),$filtermode = false){
		$output = '';
		$opts = array_merge(array(
			'current'=>false,
			'class'=>'children',
			'model'=>false,
			'belongs'=>false,
			'leafs_only'=>false,
			'route_param'=>false,
			'urlmerge'=>array(),
			'action'=>'ver'
		),$opts);

		if($data && is_array($data)){

			foreach($data as $it){
				$is_leaf = empty($it['children']);

				if(isset($it[$opts['model']]['externo']) && $it[$opts['model']]['externo']){
					$atts = array('target'=>'_blank','rel'=>'nofollow','class'=>'external');
					$url = $it[$opts['model']]['enlace'];

				} else {
					$atts = array();
					if($filtermode){
						$url = array_merge(array(
							'controller'=>'search',
							'action'=>'resultados',
							low($opts['model']).'_id'=> $it[$opts['model']]['id']
						), $opts['urlmerge']);
					} else {
						$url = array(
							'controller'=>Inflector::tableize($opts['belongs'] ? $opts['belongs'] : $opts['model']),
							'action'=>$opts['action']
						);

						$slug = isset($it[$opts['model']]['slug']) ? $it[$opts['model']]['slug']:$it[$opts['model']]['id'];

						if($opts['route_param'])
							$url[$opts['route_param']] = $slug;
						else
							$url[] = $slug;
						
						$url = array_merge($url, $opts['urlmerge']);
					}

				}

				$output.= $this->Html->tag('li',null,array('class'=>($opts['current'] && $it[$opts['model']]['id']==$opts['current']['id'] ? 'selected':'')));
					if($opts['leafs_only'] && !$is_leaf)
						$url = 'javascript:;';

					$output.= $this->Html->link($this->Html->tag('span',$it[$opts['model']]['nombre']), $url,$atts);

					if(!$is_leaf)
						$output.= $this->recursivelist($it['children'],array_merge($opts,array('class'=>'')));

				$output.= '</li>';
			}

			$output = $this->Html->tag('ul',$output,array('class'=>$opts['class']));
		}

		return $output;
	}

	function resize($src,$neww=0,$newh=0){
		if($size = @getimagesize((strpos($src,'http://')=== 0 ? '':'./').$src)){
			$w = $size[0];
			$h = $size[1];

			if($w && $neww){
				$factor = $neww/$w;
				$newSize[0] = round($w * $factor);
				$newSize[1] = round($h * $factor);
				$newSize[2] = 'width="'.$newSize[0].'" height="'.$newSize[1].'"';
			}
			if($h && $newh){
				$factor = $newh/$h;
				$newSize[0] = round($w * $factor);
				$newSize[1] = round($h * $factor);
				$newSize[2] = 'width="'.$newSize[0].'" height="'.$newSize[1].'"';
			}
			return $newSize;
		}
		return false;
	}

/*
* Recibe la $url del video y devuelve la id del video, la url del thumbnail o el player embeded con el video dependiendo de $mode
* string $url
* string $mode = 'id' | 'thumb' | 'player'
*/
    function youtube($url,$mode = 'player',$HTMLatts=array()){
		$parsedUrl = parse_url($url);
		parse_str($parsedUrl['query'],$ytArgs);
		$yid = $ytArgs['v'];

		$HTMLatts = array_merge(array('class'=>'vPlayer','width'=>640,'height'=>361),$HTMLatts);
		switch($mode){
			case 'thumb': return 'http://img.youtube.com/vi/'.$yid.'/0.jpg'; break;
			case 'id': return $yid; break;
			case 'player':
			default:
			return '<object '.($this->serializeHTML($HTMLatts)).'>
				<param name="movie" value="http://www.youtube.com/v/'.$yid.'?version=3&amp;hl=es_MX"></param>
				<param name="allowTransparency" value="false"></param>
				<param name="wmode" value="transparent"></param>
				<param name="allowFullScreen" value="true"></param>
				<param name="allowscriptaccess" value="always"></param>
				<embed width="'.$HTMLatts['width'].'" height="'.$HTMLatts['height'].'" src="http://www.youtube.com/v/'.$yid.'?version=3&amp;hl=es_MX" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" allowTransparency="false" wmode="transparent" ></embed>
			</object>';
			break;
		}
    }

/*
* Si recibe la $url del audio, inicializa el objeto JS. Si $url = false, únicamente hace el include del script.
* string $url
* array $options
*/

	function swf($url, $atts = array()){
		$w = $h = 0;
		$url = (strpos($url,'http://')=== 0 ? '':'/').$url;

		if(!(isset($atts['width']) || isset($atts['height']))){
			list($atts['width'],$atts['height']) = @getimagesize((strpos($url,'http://')=== 0 ? '':'.').$url);
		} elseif(isset($atts['width']) && $atts['width'] && (!isset($atts['height']) || !$atts['height'])){
			list($atts['width'],$atts['height']) = $this->resize($url,$atts['width']);
		}

		return '<object '.($this->serializeHTML($atts)).' data="'.$url.'" type="application/x-shockwave-flash"><param name="movie" value="'.$url.'"></param><param name="wMode" value="transparent"></param></object>';
	}

	/* Formatters */

	function fdate($format='s',$fdate=false) {
		$preformats = array(
			't'=>'%H:%M:%s Hrs.',
			'h'=>'%H:%M Hrs.',
			'd'=>'%B %d de %Y',
			'dm'=>'%B %d',
			's'=>'%d-%b-%y %H:%M Hrs.',
			'm'=>'%a %d-%B-%y %H:%M Hrs.',
			'l'=>'%A, %d-%B-%y %H:%M Hrs.'
		);
		if(array_key_exists($format,$preformats)) #es preformato
			$format = $preformats[$format];

		return ucfirst(strftime($format,$fdate ? strtotime($fdate):time()));

	}

	function serializeHTML($data){
		$HTML = array();
		foreach($data as $key => $value){
			if(!is_array($value))
				$HTML[] = $key.'="'.$value.'"';
		}
		return implode(' ',$HTML);
	}

	function serializeJS($data){
		if(is_numeric($data)) return $data;
		if(is_bool($data)) return $data ? 'true':'false';
		if(is_array($data)){
			$newvals = array();
			foreach($data as $value)
				$newvals[] = $this->serializeJS($value);
			return '['.(implode(',',$newvals)).']';
		}
		return '"'.$data.'"';
	}

	function json($opts,$wrap = true,$comma = true){
		if(is_array($opts) && !empty($opts)){
			$json = array();
			foreach($opts as $key => $val)
				$json[]= $key.':'.($this->serializeJS($val));

			$json = implode(',',$json);
			return ($comma ? ',':'').($wrap ? '{'.$json.'}':$json);
		} else { return ''; }
	}
}
?>