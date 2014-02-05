/*
28/06/10 - Fix del parsing a entero de los params en la url
28/03/11 - Evita repetir anclas a una misma imagen en un grupo (galería)
27/02/12 - Pulsembox v2.0
*/
var Pulsembox = new Class({
	Implements: Options,
	options: {
		overlay_duration:300,
		duration:600
	},
	overlayFx:false,
	imgFx:false,
	captionFx:false,
	boxFx:false,
	
	isVisible:false,
	direction:false,
	built:false,
	current:-1,
	caller:false,
	isReady:true,
	isHTML:false,
	
	boxWidth:0,
	boxHeight:0,
	galleries:{},
	spinnerimg:"/img/pbox/spinner.gif",

	initialize: function(options){
		this.setOptions(options);
		this.overlay_duration = this.options.overlay_duration;
		this.duration = this.options.duration;
	},
	init: function(){
		$$('a.pulsembox').each(function(el){
			this.setGalleries(el);
			el.addEvent('click',function(e){
				var ev = new Event(e);
				ev.stop();
		
				this.binder(el);

			}.bind(this));
		}.bind(this)); 
	},
	binder: function(el){
		var caption = el.name || el.title || '';
		var group = el.rel || false;
		var href = el.href || false;
		
		this.callera = el;
		this.show(caption,href,group);
	},
	setGalleries: function (el){
		if (this.isImageURL(el.href)){ // Si es imagen a mostrar
			if(group = el.get('rel') || false){
				if(group in this.galleries){
					if(this.galleries[group].every(function(item){ return item.href != el.href; })){
						this.galleries[group][this.galleries[group].length] = el;
					}
				} else
					this.galleries[group] = [el];
			}
		}
	},
	build: function (){
		new Element('div#PboxOverlay',{styles:{ 'opacity':0 }}).addEvent('click',function(){ this.close(); }.bind(this)).inject(document.body);
		this.overlayFx = new Fx.Tween('PboxOverlay',{ property:'opacity', duration:this.overlay_duration, link:'cancel', transition:'quint:out' });

		new Element('div#Pulsembox',{ styles:{ 'opacity':0 }}).adopt(
			new Element('div#PboxHeader').adopt(
				new Element('a#PboxCloseButton', { html:'Cerrar', href:'javascript:;', title:'Tecla "ESC"', events:{ 'click':function(){ this.close(); }.bind(this) }}),
				new Element('div#PboxHeaderNav')
			),
			new Element('div#PboxPad')
		).inject(document.body);
		
		this.boxMoveFx = new Fx.Morph('Pulsembox',{ duration:this.duration,link:'cancel',transition:'quint:out', fps:80 })
		this.boxFx = new Fx.Morph('Pulsembox',{ duration:this.duration,link:'cancel',transition:'quint:out', fps:80 });
		
		// Listeners
		this.listen = {
			scroll: function(){ if(this.isVisible){ this.showBox(); } }.bind(this),
			key: function(e){
				if(this.isVisible){
					var ev = new Event(e);
					switch (ev.code) {
						case 27: this.close(); break;
						case 39: if ($('PboxNext')) this.change(this.next,this.rel); break;
						case 37: if ($('PboxPrev')) this.change(this.prev,this.rel); break;
					}
				}
				return false;
			}.bind(this),
			resize: function(){ if(this.isVisible){ this.showBox(); }}.bind(this)
		};
		
		$(window).addEvent('scroll',this.listen.scroll);
		$(document).addEvent('keyup',this.listen.key);		
		$(window).addEvent('resize',this.listen.resize);
		
		this.built = true;
	},
	showOverlay: function (){
		if(this.isVisible) return;
		this.isVisible = true;
		$$('#Pulsembox,#PboxOverlay').setStyle('display','block');
		$('PboxOverlay').setStyle('background-image','url('+this.spinnerimg+')');
		this.overlayFx.start(0.7);
	},
	show: function(caption, url, rel){
		if(!this.isReady) return false;

		if(!this.built) this.build();
		this.showOverlay();

		this.isReady = false;
		this.rel = rel;
		
		if(this.isImageURL(url)){
			this.isHTML = false;
			this.next = this.prev = { caption: '', url: '', html: '' };
			var imageCount = '';

			if(rel) {
				var found = false;
				var gallength = this.galleries[rel].length;
				
				for (var i=0; i < gallength; i++) {
					var image = this.galleries[rel][i];

					if(image.href == url) {
						found = true;
						if(this.current != -1) this.direction = i > this.current ? '>':'<';
						this.current = i;
						
						imageCount = '<span id="PboxCounter">'+(i+1)+' / '+(this.galleries[rel].length)+'</span>';
						if(this.galleries[rel][i-1]) this.prev = this.getInfo(i-1, this.galleries[rel][i-1], 'Prev', 'Anterior');
						if(this.galleries[rel][i+1]) this.next = this.getInfo(i+1, this.galleries[rel][i+1], 'Next', 'Siguiente');
						
						break;
					}
				}

				if((!found) && gallength > 0){
					this.current = 0;
					this.isReady = true;
					this.change(this.getInfo(0,this.galleries[rel][0],'',''),rel);
					return;
				}
			}
			
			var srcimg = $(new Image());
			
			srcimg.addEvents({
				'load': function(){
					$('PboxHeaderNav').set('html',this.prev['html'] + imageCount + this.next['html']);
					$('PboxPad').adopt(new Element('a#PboxImageClose', { href:'javascript:;', title:'Clic para cerrar esta ventana', events:{ 'click':function(){ this.close(); }.bind(this) }}));
	
					var pboximg = new Element('img#PboxImage',{ src:url, alt:caption }).inject('PboxImageClose');
					/* IE9 sets original size as inline atts; remove them */
					pboximg.removeProperties('width','height');
					
					this.imgFx = new Fx.Tween(pboximg,{ property:'opacity', duration:this.duration, link:'cancel' }).set(0);
	
					if($('PboxCaption')){
						$('PboxCaption').set('html',caption);
					} else {
						new Element('div#PboxCaption',{'html':caption }).inject('PboxPad');
					}
					this.captionFx = new Fx.Tween('PboxCaption',{ property:'opacity', duration:this.duration, link:'ignore' }).set(0);
	
				//// Calcular Dimensiones de Imagen
					var padding = pboximg.measure(function (){ return this.getPosition('PboxPad'); });
					var yplus = 46 + 10 + 68; // 46px (PboxHeader) + 10px (Margen) + 68 Caption
					var x = window.getWidth() - 20; // Espacio horizontal total disponible para imagen + padding: Ancho ventana - 20 margen lateral
					var y = window.getHeight() - yplus; // Espacio vertical total disponible para imagen + padding: Alto ventana - y plus

					var imgW = srcimg.width;
					var imgH = srcimg.height;
	
					if (imgW + (padding.x*2) > x){
						imgH = imgH * ((x - (padding.x*2)) / imgW);
						imgW = x - (padding.x*2);
	
						if (imgH + (padding.y*2) > y){
							imgW = imgW * ((y - (padding.y*2)) / imgH);
							imgH = y - (padding.y * 2);
						}
					} else {
						if (imgH + (padding.y * 2) > y){
							imgW = imgW * ((y - (padding.y*2)) / imgH);
							imgH = y - (padding.y * 2);
	
							if (imgW + (padding.x*2) > x){
								imgH = imgH * ((x - (padding.x*2)) / imgW);
								imgW = x - (padding.x*2);
							}
						}
					}
					
					this.boxWidth = imgW + (padding.x*2); // PboxImage + (padding.x*2)
					this.boxHeight = imgH + (padding.y*2) + yplus - 10;// yplus - 10 (margen, afuera del box, por tanto, no se considera aqui)
					
					if(this.boxWidth < 264){
						pboximg.setStyle('width',imgW);
						this.boxWidth =  264;
					}
	
				//// Eventos
					if (this.prev['html']) $("PboxPrev").addEvent('click',function(){ this.change(this.prev,rel); }.bind(this));
					if (this.next['html']) $("PboxNext").addEvent('click',function(){ this.change(this.next,rel); }.bind(this));
	
				//// SHOW
					this.showBox();// Setea el ancho de la nueva imagen
					
				}.bind(this),
				'error': function(){
					this.isReady = true;
					
					if(this.rel){
						var spliced = this.galleries[this.rel].splice(this.current,1);
						
						if(this.direction){
							if(this.direction == '>' && this.next['html']){
								this.change(this.next,this.rel);
								return;
							} else if(this.prev['html']){
								this.change(this.prev,this.rel);
								return;
							}
							
							if(this.direction == '<' && this.prev['html']){
								this.change(this.prev,this.rel);
								return;
							} else if(this.next['html']){
								this.change(this.next,this.rel);
								return;
							}
							
						} else {
							if(this.next['html']){
								this.change(this.next,this.rel);
								return;
							}

							if(this.prev['html']){
								this.change(this.prev,this.rel);
								return;
							}
						}
					}
					
					this.close();
				}.bind(this)
			});
			
			srcimg.src = url;
		
		} else { /// Es HTML
			
			this.isHTML = true;
			var queryString = (matchURL = url.match(/\?(.+)/)) ? matchURL[1]:false; //var queryString = url.match(/\?(.+)/)[1];
			var params = this.getParams(queryString);
			
			params['width'] = params['width'] || false;
			
			if(!params['width']){
				params['width'] = window.getWidth() - (20+17);
				params['width'] = params['width'] < 600 ? params['width'] : 600;
			}
			
			var ajaxContentW = (params['width']).toInt();
			
			var resized = [];
			var yplus = this.yplus = 46 + 40; // 46px (PboxHeader) + 40px (Margen)
			var x = window.getWidth() - (20+17); // 20 margen lateral + ancho scroll lateral
			var y = this.y = window.getHeight() - yplus;
			
			this.boxWidth = ajaxContentW > x ? x : ajaxContentW;

			$('PboxPad').setStyle('padding',0).adopt(new Element('div#PboxAjaxContent',{ styles:{ width:this.boxWidth }}));

			if(url.indexOf('PboxInline') != -1) { // inline
				params['inlineId'] = params['inlineId'] || false;
				
				if(!params['width']) this.failure('No se especificó ancho de la ventana');
				$("PboxAjaxContent").set('html',$(params['inlineId']).get('html'));
				this.resizeBox();
				
			} else {
				new Request.HTML({
					method: 'get',
					url:url,
					update: $("PboxAjaxContent"),
					onComplete: function(){ this.resizeBox(); }.bind(this),
					onFailure: function(){ this.failure(); }.bind(this),
					evalScripts: true
				}).get();
			}
		} 
	},
	failure: function(msg){
		this.isReady = true;
		msg = msg || 'Hubo un problema para mostrar el contenido.';
		alert(msg);
		this.close();
	},
	showBox: function(){
		var moveTo = {
			width: this.boxWidth,
			height: this.boxHeight,
			left: (window.getScrollLeft() + (window.getWidth() - this.boxWidth) / 2),
			top: (window.getScrollTop() + (window.getHeight() - this.boxHeight) / 2)
		};
		
		$('PboxOverlay').set('background-image','none');
		
		/// Position
		if($('Pulsembox').getStyle('opacity') == 0){
			$('Pulsembox').setStyles(moveTo);
		} else {
			if(this.boxMoveFx){ this.boxMoveFx.start(moveTo); }
		}

		/// Opacity
		var afterShow = function(){
			this.isReady = true;
			if(!this.isHTML){
				this.imgFx.start(1);
				this.captionFx.start(1);
				$('PboxOverlay').set('background-image','url('+this.spinnerimg+')');
			}
		};
		
		if($('Pulsembox').getStyle('opacity') < 1){
			this.boxFx.start({'opacity':1}).chain(afterShow.bind(this));
		} else {
			(afterShow.bind(this))();
		}
	},
	resizeBox: function(){
		/// Calcular dimensiones
		$('PboxAjaxContent').setStyle('height','auto');
		var measured = $('PboxAjaxContent').measure(function (){ return this.getDimensions(); }), w, h;
		if(measured.y > this.y){
			this.boxHeight = this.y;
			$('PboxAjaxContent').setStyle('height',this.boxHeight);
			this.boxWidth+=17;
		} else {
			this.boxHeight = measured.y;
		}
		
		this.boxHeight+= this.yplus - 10;
		
		this.showBox();
	},
	getParams: function(query){
		var params = {};
		if (!query) return params;

		var pairs = query.split(/[;&]/);
		for (var i = 0; i < pairs.length; i++) {
			var pair = pairs[i].split('=');
			if (!pair || pair.length != 2) continue;
			params[unescape(pair[0])] = unescape(pair[1]).replace(/\+/g,' '); // unescape both key and value, replace "+" with spaces in value
		}
		return params;
	},
	getInfo: function(idx, image, id, controlHTML){
		return {
			idx: idx,
			caption: image.name || image.title || "",
			url: image.href,
			html: "<a id='Pbox" + id + "' href='javascript:;' title='Tecla "+(id == 'Prev' ? '←':'→')+"'>"+controlHTML+"</a>"
		}
	},
	change: function(image,rel){
		if(!this.isReady) return false;
		$('PboxPad').set('html','');
		if($('PboxCaption')) $('PboxCaption').set('html','');
		
		this.callera = this.galleries[rel][image.idx];
		this.show(image.caption, image.url, rel);
	},
	close: function(){
		if(!this.isReady) return false;
		this.isReady = false;
		this.isVisible = false;
		
		if($('PboxPrev')) $("PboxPrev").removeEvents();
		if($('PboxNext')) $("PboxNext").removeEvents();
		
		if($('PboxImageClose')) $('PboxImageClose').removeEvents(); 

		this.boxFx.start({'opacity':0}).chain(function(){
			$("PboxPad").set('html','');
			$("PboxHeaderNav").set('html','');
			if($("PboxCaption")) $("PboxCaption").destroy();
			
			this.overlayFx.start(0).chain(function(){
				$$('#PboxOverlay,#Pulsembox').setStyle('display','none');
				this.isReady = true;
			}.bind(this));
		}.bind(this));
	},
	isImageURL: function(url){
		var baseURL = (matchURL = url.match(/(.+)\?/)) ? matchURL[1]:url;
		var imageURL = /\.(jpe?g|png|gif|bmp)/gi;// regex to check if a href refers to an image
		return baseURL.match(imageURL) ? true:false;
	}
});
var pulsembox = new Pulsembox();
window.addEvent('domready', function(){ pulsembox.init(); });