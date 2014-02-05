var mooSuggest = new Class({
	Implements: Options,
	options: { delay: 200 },
	color:'#090', /// Color del texto para datos válidos
	basecolor:null, /// Color original del texto
	active:false, /// Si la lista está mostrada
	current:false, /// Elemento (Etiqueta <a>) seleccionado de la lista
	currentdata:{}, /// Datos del elemento seleccionado
	timer:null, /// Timer para el delay de las peticiones
	oReq:null, /// Objeto Request
	oScroll:null, /// Objeto Request
	urlSelectorMsg:false,
	initialize: function(inpId,inpCaption,reqUrl,urlSelector,options){
		this.setOptions(options);
		this.reqBaseUrl = reqUrl;
		urlSelector = urlSelector || false;
		
		if(urlSelector){
			this.urlSelector = $(urlSelector);
			this.urlSelector.addEvent('change',function(){
				this.updateUrl();
				this.parseResults(false);
				this.inpId.value = '';
				this.inpCaption.value = '';
			}.bindWithEvent(this));
			this.updateUrl();
			this.urlSelectorMsg = new Element('div.urlSelectorMsg').inject(document.body).adopt(new Element('div.urlSelectorMsgWrapper.fail',{ html:'Seleccione aquí primero' })).set('reveal',{ duration:220 }).position({
				relativeTo:this.urlSelector,
				position: { x: 'right', y: 'center' },
				edge: { x:'left', y:'center' },
				offset: { x:12 }
			}).setStyle('display','none');
			
		} else
			this.reqUrl = this.reqBaseUrl;
			
		this.oReq = new Request.JSON({
			link:'cancel',
			method:'get',
			onError: function(){ this.spinner.setStyle('display','none');this.parseResults(false); }.bindWithEvent(this),
			onRequest: function(rTxt){
				this.spinner.setStyle('display','block');
			}.bindWithEvent(this),
			onCancel: function(){ this.spinner.setStyle('display','none'); }.bindWithEvent(this),
			onSuccess: function(rTxt){
				this.spinner.setStyle('display','none');
				this.parseResults(new Hash(rTxt).getLength() ? rTxt : false);
			}.bindWithEvent(this)
		});

		this.inpId = $(inpId);
		this.inpCaption = $(inpCaption).set('autocomplete','off').addEvent('keyup',function(ev){
			if(['esc','up','down','enter'].contains(ev.key)){
				if(this.active){ /// Mostrado y tiene elementos
					if(ev.key == 'up'){
						var prevOpt = this.current.getParent().getPrevious('li');
						if(prevOpt){
							this.mark(prevOpt.getFirst('a'),true);
						}
						
					} else if(ev.key == 'down'){
						var nextOpt = this.current.getParent().getNext('li');
						if(nextOpt){
							this.mark(nextOpt.getFirst('a'),true);
						}
						
					} else if(ev.key == 'enter'){
						this.accept(this.current);
					} else if(ev.key == 'esc'){
						this.cancel();
					}
				} else {
					if(ev.key == 'enter'){
						//if(this.inpId.get('value')=='')
							
					} else if(ev.key == 'esc'){
						this.cancel();
					}
					
				}
			} else {
				if(this.inpCaption.value.trim() != '' && this.inpCaption.value != this.currentdata.value && this.reqUrl){
					this.inpCaption.setStyle('color',this.basecolor);
					clearTimeout(this.timer);
					this.oReq.cancel();
					this.timer = this.oReq.send.delay(this.options.delay,this.oReq,{ url:this.reqUrl+'?q='+escape(this.inpCaption.value) });
				} else {
					if(!this.reqUrl){
						this.urlSelectorMsg.wink();
					}
					this.parseResults(false);
				}
				
				if(this.inpCaption.value == this.currentdata.value){
					this.inpCaption.setStyle('color',this.color);
				}
			}
		}.bindWithEvent(this));
		
		this.inpCaption.store('moosuggest',this);
		
		this.basecolor = this.inpCaption.getStyle('color');
		this.inpCaption.addEvent('focus',function(ev){ this.isFocused = true; }.bindWithEvent(this));
		this.inpCaption.addEvent('blur',function(ev){
			if(this.active){
				this.inpCaption.focus();
				ev.stop();
			} else {
				this.cancel();
			}
			
			if(this.inpCaption.get('value')=='') this.inpId.set('value','');
			
			this.isFocused = false;
		}.bindWithEvent(this));
		
		//// Para datos preestablecidos (Edit action)
		if(this.inpId.get('value').trim() != '' && this.inpCaption.get('value').trim() != ''){
			this.currentdata = { idx:this.inpId.get('value'), value:this.inpCaption.get('value') };
			this.inpCaption.setStyle('color',this.color);
		}
		
		//// Desactivar la tecla Enter en el form cuando la lista se muestra
		var stopform = this.inpId.getParent('form');
		if(stopform)
			stopform.addEvent('keypress',function(e){
				if(this.isFocused && e.key =='enter')
					e.stop();
			}.bindWithEvent(this));
		
		//// HTML setup
		this.spinner = new Element('div.spinner');
		this.list = new Element('ul.suggestList').inject(document.body);
		
		new Element('span.inpWrapper').wraps(this.inpCaption).adopt(this.spinner);

		var icSize = this.inpCaption.getSize();
		
		this.list.setStyle('max-width',((icSize.x - 8) < 240 ? 240: (icSize.x - 8)) + 'px');
		this.spinner.setStyles({ top:(icSize.y-3)+'px', width:icSize.x+'px' });
		
		this.oScroll = new Fx.Scroll(this.list, { duration:300, transition:'quad:out',link:'cancel' });
	},
	parseResults: function(data){
		this.data = data;
		var emptyJson = true;
		this.list.set('html','');

		if(this.data){
			for(idx in this.data){
				var encoded = this.data[idx].replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/<span>/i, '').replace(/<\/span>/i, '');
				
				/** Highlight **/
				var i, re, previdx,  piece, pieces = [], matches, idxOf, pos = 0;
				matches = encoded.match(new RegExp(this.inpCaption.value, 'i'));
				
				if(matches){
					matches.each(function(match){
						idxOf = encoded.search(new RegExp(match));
						
						piece = encoded.substring(pos,idxOf+match.length);
						
						piece = piece.replace(match,'<span>'+match+'</span>');
						
						pieces.push(piece);
						pos = idxOf+match.length;

					}.bind(this));
					
					pieces.push(encoded.substr(pos));
				}
				encoded = idx + '–' + pieces.join('');

				var anchor = new Element('a',{
					href:'javascript:;',
					html:encoded,
					events:{
						'click':function(ev){
							this.accept(ev.target);
						}.bindWithEvent(this),
						'mouseenter': function(ev){
							this.mark(ev.target);
						}.bindWithEvent(this),
						'focus':function(ev){
						}.bindWithEvent(this)
					}
				}).store('optId',idx);
				this.list.adopt(new Element('li').adopt(anchor));
				
				if(emptyJson) /// Mark first
					this.mark(anchor);
				
				emptyJson = false;
			}
		} else
			this.hide();
		
		if(!emptyJson){
			this.active = true;
			this.list.position({
				relativeTo:this.inpCaption,
				position: { x: 'left', y: 'bottom' },
				edge: { x:'left', y:'top' }
			});
			this.list.setStyle('display','inline');
		}
	},
	mark: function(marked,bykeyboard){
		if(bykeyboard)
			this.oScroll.toElement(marked);
		this.list.getElements('a').each(function(el){ el.removeClass('selected'); });
		marked.addClass('selected');
		this.current = marked;
	},
	accept: function(accepted){
		var optId = accepted.retrieve('optId');
		this.inpId.set('value',optId);
		this.inpCaption.set('value',this.data[optId]).setStyle('color',this.color);
		this.currentdata = {idx: optId, value:this.data[optId]};
		this.hide();
	},
	cancel: function(){
		var inpId = this.inpId.get('value');
		
		if(inpId.trim() == ''){ /// Cancelar sin opción seleccionada
			this.inpCaption.set('value','').setStyle('color',this.basecolor); /// Nos aseguramos que no se seleccione nada
		} else { /// Cancelar habiendo seleccionado opción
			
			if(this.inpCaption.get('value')){
				this.inpCaption.set('value',this.currentdata.value).setStyle('color',this.color); /// Reestablecemos Caption
			}
		}
		
		//this.inpCaption.focus();
		this.hide();
	},
	hide: function(){
		this.list.setStyle('display','none');
		this.active = false;
	},
	updateUrl: function(){
		if(this.urlSelector.value){
			this.reqUrl = this.reqBaseUrl.substitute({ value:this.urlSelector.value });
		} else {
			this.reqUrl = false;
		}
	}
	
});