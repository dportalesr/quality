/*
* 08/Abr/10 - Fix: Comprueba si la imagen ya fue cargada antes de asignar el evento onLoad.
* 18/Oct/10 - Fix: Removida la variable firsttime. Ahora el primer caption sigue la misma rutina de verificación de contenido para decidir si mostrarse (Anteriormente siempre se mostraba)
* 07/Abr/11 - Fix: Se mostraban Captions incorrectos.
* 12/May/11 - Fix: Se localizaron las variables globales que causaban conflicto en varias versiones del componente.
*/
var mooShowcase = new Class({
	Implements: Options,
	options: {
		nav: 'out',
		numbers: false,
		nextprev: false,
		spinner: true,
		fullscreen: false,
		fadenav: true,
		duration: 1200,
		captionheight: 72, // caption area height
		captionwidth: 336, // caption area width
		captionpos: 'bottom', // caption area height
		delay: 4000
	},
	previous:false,
	current:false,
	next:false,
	fx:[],
	fxCaption:false,
	navs:[],
	imgs_loaded:0,
	initialize: function(el,options){
		var captionstyles;
		
		this.setOptions(options);
		this.nav = this.options.nav;
		this.showcase = $(el) || false;
		if(!this.showcase){
			return alert('No se encontró el contenedor con el id '+el);
		} else {
			/// Setting up layout
			this.showcase.setStyle('position','relative');
			this.wrapper = new Element('div',{'class':'mooshowcase_wrapper'}).adopt(this.showcase.getChildren()).inject(this.showcase);
			
			this.captions = this.wrapper.getChildren('.caption');
			if(this.captions.length){
				if(this.options.captionpos == 'bottom')
					captionstyles = { bottom:-this.options.captionheight, height:this.options.captionheight };
				else
					captionstyles = { right: 0, height:'100%', width: this.options.captionwidth };
				
				this.captionpad = new Element('div',{'class':'mooshowcase_captionpad'}).adopt(this.captions);
				this.caption = new Element('div',{
					'class':'mooshowcase_caption tmce',
					styles:captionstyles
				}).adopt(this.captionpad);
				this.caption.inject(this.wrapper);
			} else
				this.captions = false;
			
			this.snaps = this.wrapper.getElements('.item');
			
			if(this.snaps.length == 1)
				this.nav = false;
			
			this.container = new Element('div',{'class':'mooshowcase_container' });
			this.fxHeight = new Fx.Tween(this.container,{ property:'height', duration:this.options.duration *0.3, transition:Fx.Transitions.Quart.easeInOut, link:'cancel'});
			this.container.inject(this.wrapper).adopt(this.snaps);
			
			if(this.nav){
				this.navbar = new Element('div',{'class':'mooshowcase_nav '+this.nav});

				if(this.nav=='in' && this.options.fadenav){
					this.navbarFx = new Fx.Tween(this.navbar,{ link:'cancel', 'property':'opacity', 'duration':'short' });
					this.navbarFx.start(0.5);
					this.showcase.addEvents({
						'mouseenter':function(){ this.navbarFx.start(1); }.bindWithEvent(this),
						'mouseleave':function(){ this.navbarFx.start(0.5); }.bindWithEvent(this)
					});
				}

				if(this.options.nextprev){
					this.navbar_np = new Element('div',{'class':'navnp'});
					new Element('a',{
						'href':'javascript:;',
						'class':'next'
					}).addEvent('click',function(e){ (function(){ this.reset(); this.change(this.current+1); }.bind(this,e))(); }.bind(this)).inject(this.navbar_np);
					
					new Element('a',{
						'href':'javascript:;',
						'class':'prev'
					}).addEvent('click',function(e){ (function(){ this.reset(); this.change(this.current-1); }.bind(this,e))(); }.bind(this)).inject(this.navbar_np);

					this.navbar_np.inject(this.navbar);
				}
				
				this.navbar.inject(this.showcase);
			}
			
			if(this.options.spinner){
				this.spinner = new Element('div',{ 'class':'mooshowcase_spinner' });
				this.spinner.inject(this.container);
			}
			 
			if(this.captions){
				if(this.options.captionpos == 'bottom')
					this.fxCaption = new Fx.Tween(this.caption,{ property:this.options.captionpos, duration:this.options.duration *0.4, transition:Fx.Transitions.Quart.easeInOut, link:'cancel'});
				else
					this.fxCaption = new Fx.Tween(this.caption,{ property:'opacity', duration:this.options.duration *0.4, transition:Fx.Transitions.Quart.easeInOut, link:'cancel'});
			}
			this.snaps.each(function(el,idx){
				if(this.nav){
					this.navs.push(new Element('a',{
						href:'javascript:;',
						html: this.options.numbers ? idx+1 : '',
						'class':idx==0 ? 'selected':''
					}).addEvent('click',function(e){ this.reset(); this.change(idx); }.bind(this)).inject(this.navbar,this.nav=='in' ? 'top':'bottom'));
				}
				
				el.fade('hide');

				this.fx.push(new Fx.Tween(el,{
					property: 		'opacity',
					duration: 		this.options.duration,
					transition: 	Fx.Transitions.Quart.easeInOut,
					link: 				'cancel',
					onStart: 			function(){
													if(!this.getStyle('opacity'))
														this.setStyle('visibility', 'visible');
												}.bind(el),
					onComplete: 	function(){
													if(!this.getStyle('opacity'))
														this.setStyle('visibility', 'hidden');
												}.bind(el)
				}));
				
				var complete = false;
				
				if(el.get('tag')!='img'){
					el = el.getElement('img');
					complete = el ? el.complete : true;
				} else 
					complete = el.complete;
				
				var countimg = function(){ this.imgs_loaded++;this.checkload(); };
				
				if(complete){ // Si ya cargó, contabilizamos..
					(countimg.bind(this))();
				} else {
					el.addEvent('load',countimg.bind(this));
					el.addEvent('error',countimg.bind(this));
				}
				
			}.bind(this));
		}
	},
	reset:function(){
		$clear(this.period);
		this.period = this.change.periodical(this.options.delay,this,false);
	},
	checkload:function(){
		
		if(this.imgs_loaded >= this.snaps.length){
			this.current = this.snaps.length - 1;
			if(this.options.spinner)
				this.spinner.setStyle('display','none');
			
			this.change(0);
				
			if(this.snaps.length > 1)
				this.period = this.change.periodical(this.options.delay,this,false);
		}
	},
	change:function(idx){
		var fxCaptionStart, fxCaptionEnd, current, previous, next;

		current = this.current;
		previous = this.previous;
		
		if(idx !== false){
			if(idx < 0)
				next = this.snaps.length-1;
			else if(idx > this.snaps.length-1)
				next = 0;
			else 
				next = idx;
		} else {
			next = (current >= (this.snaps.length - 1)) ? 0 : current + 1;
		}

		// Disminución de altura
		if(!this.options.fullscreen)
			this.fxHeight.start(this.snaps[next].getSize().y);

		this.fx[current].start(0);
		this.fx[next].start(1);

		if(this.captions){
			if(this.options.captionpos == 'bottom'){
				fxCaptionStart = -this.options.captionheight;
				fxCaptionEnd = 0;
			} else {
				fxCaptionStart = 0;
				fxCaptionEnd = 1;
			}
			
			this.fxCaption.start(fxCaptionStart).chain(function(){
				
				if(current!==false)
					this.captions[current].setStyle('display','none');

				this.captions[next].setStyle('display','block');

				if(this.captions[next].get('html')){ /* ¿Quién quiere ver un caption vacío? R= Nadie */
					this.fxCaption.start(fxCaptionEnd);
				}
					
			}.bind(this));
		}

		if(this.nav){
			this.navs[current].removeClass('selected');
			this.navs[next].addClass('selected');
		}

		this.previous = current;
		this.current = next;
	}
});