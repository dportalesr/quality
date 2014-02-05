/*
* 05/02/2010
	- Cambio de nombres de variable.
	- Se agregó el elemento itemswrapper para el cálculo del ancho visible en el evento Next/Prev, lo cual ocasionaba problema en Chrome de 0px de ancho.
	- Cambio de estilos.(Anterior)
* 25/04/2010 - Fix del autoplay, ahora hace uso del itemswrapper para el cálculo de la posición
* 28/07/2010 - Se agregó rutina para la construcción del layout del componente (antes era mediante un elemento de CakePHP)
* 14/04/2011 - Botones sólo aparecen cuando puede hacerse scroll
* 18/07/2011 - Implementación del método Element.measure para el cálculo fiel de dimensiones (Solución cuando el slider aparece con display:none).
* 25/07/2011 - Calculo de dimensiones se hacen en los eventListeners debido a que Chrome prepara el DOM antes de que las imagenes ocupen sus dimensiones finales lo que resulta en medidas diferentes.
*/
var mooSlider = new Class({
	Implements: Options,
	options: {
		auto: false, 
		wait: 3800,
		delay: 1000,
		controls: true,
		jump: false
	},
	toload:0,
	items:false,
	timer:false,
	controls:true,
	pos:0,
	initialize: function(el,options){
		this.setOptions(options);
		this.container = $(el) || false;
		
		if(!this.container){
			return alert('No se encontró el contenedor '+el);
		} else {
			// Setting up Layout
			this.spinner = new Element('div.spinner').fade(0.8);
			this.container.addClass('sgContainer').adopt(this.spinner);
			
			this.items = this.container.getElements('.sgItem');

			if(!this.items.length) return false;

			this.items[0].setStyle("margin",0);
			this.imgs = this.container.getElements('.sgItem img');
			this.toload = this.imgs.length;
			
			this.slider = new Element('div.sgItems');
			this.itemsw = new Element('div.sgItemswrapper').adopt(this.items).inject(this.slider);
			this.mask = new Element('div.sgMask').adopt(this.slider).inject(this.container);
			new Element('div.sgMaskPad').wraps(this.mask);
			this.fx = new Fx.Scroll(this.mask,{ duration: this.options.delay, offset: {'x': 0, 'y': 0}, link:'cancel', transition: 'quart:in:out' }).toLeft();
			
			if(this.toload){
				this.imgs.each(function(el){
					if(el.complete){
						this.checkloaded();
					} else {
						el.addEvents({
							load:function(){ this.checkloaded(); }.bind(this),
							error:function(){ this.checkloaded(); }.bind(this)
						});
					}
				}.bind(this));
			}
		}
	},
	checkloaded: function(){ this.toload--;if(this.toload <= 0) this.start(); },
	start: function(){
		this.spinner.fade('out');
		this.itemswidth = this.itemsw.measure(function(){ return this.getDimensions().width; });
		this.maskw = this.mask.getStyle('width').toInt();
		
		if(this.options.controls){
			new Element('a.sgPrev',{ href:'javascript:;' }).addEvent('click', function(event) {
				new Event(event).stop();
				if(this.timer) clearInterval(this.timer);
				
				this.maskw = this.mask.getStyle('width').toInt();
				this.itemswidth = this.itemsw.getDimensions().width;
				this.endscroll = this.itemswidth - this.maskw;

				if(!this.options.jump) this.options.jump = (this.maskw * 4/5).toInt();
				
				if(this.pos){
					if(this.pos-this.options.jump < 0)
						this.pos = 0;
					else
						this.pos = this.pos-this.options.jump;
				} else {
					this.pos = this.endscroll;
				}
				
				this.fx.start(this.pos);
			}.bindWithEvent(this)).inject(this.container,'top');
			
			new Element('a.sgNext',{ href:'javascript:;'}).addEvent('click', function(event){
				new Event(event).stop();
				if(this.timer) clearInterval(this.timer);
				
				this.maskw = this.mask.getStyle('width').toInt();
				this.itemswidth = this.itemsw.getDimensions().width;
				this.endscroll = this.itemswidth - this.maskw;
				
				if(!this.options.jump) this.options.jump = (this.maskw * 4/5).toInt();
				
				if(this.pos == this.endscroll){
					this.pos = 0;
				} else {
					if(this.pos + this.options.jump > this.endscroll){
						this.pos = this.endscroll;
					} else {
						this.pos = this.pos + this.options.jump;
					}
				}
				
				this.fx.start(this.pos);
			}.bindWithEvent(this)).inject(this.container,'top');
		}

		if(this.options.auto || (!this.options.controls))
			this.timer = this.autoplay.periodical(this.options.wait,this);
	},
	
	autoplay: function(){ 
		this.maskw = this.mask.getStyle('width').toInt();
		this.itemswidth = this.itemsw.getDimensions().width;
		this.endscroll = this.itemswidth - this.maskw;
		
		if(!this.options.jump) this.options.jump = (this.maskw * 4/5).toInt();
		
		if(this.pos == this.endscroll){
			this.pos = 0;
		} else {
			if(this.pos + this.options.jump > this.endscroll){
				this.pos = this.endscroll;
			} else {
				this.pos = this.pos + this.options.jump;
			}
		}
		this.fx.start(this.pos);
	}
});