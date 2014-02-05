var mooScroll = new Class({
	Implements: Options,
	active:false,
	steps:0,
	options: {
		ignoremouse:false,
		stealth:true
	},
	initialize: function(el, options){
		this.setOptions(options);
		this.el = $(el) || false;
		if(!this.el){ alert('No elemento mooScroll'); return false; }
		
		this.el.setStyles({ position:'relative', overflow:'hidden' });
		this.el_size = this.el.getCoordinates();

		//// Layout
		this.handle = new Element('div.mooscroll_handle');
		this.slider = new Element('div.mooscroll').adopt(this.handle);
		this.el.adopt(this.slider).addEvents({
			'mouseleave':function(){ if(!this.dragging){ this.sliderFx.drag.stop(); if(this.options.stealth) this.opacityFx.start(0); }}.bind(this),
			'mouseenter':function(){ if(this.options.stealth) this.opacityFx.start(1); }.bind(this)
		});
		
		//// Fxs
		this.opacityFx = new Fx.Tween(this.slider,{ property:'opacity', transition:'pow:out', duration:900, link:'cancel' });
		if(this.options.stealth) this.opacityFx.set(0);
		this.scrollFx = new Fx.Scroll(this.el, { duration:300, /*transition:'quint:in:out',*/ link:'cancel' });
		this.sliderFx = new Slider(this.slider, this.handle, {
			steps: this.steps,
			mode: 'vertical',
			onChange: function(step){
				this.slider.setStyle('top',step);
				this.el.scrollTo(0,step);
			}.bind(this)
		}).set(0);
		
		this.sliderFx.drag.addEvent('start',function(){ this.dragging = true; }.bind(this));
		this.sliderFx.drag.addEvent('complete',function(){ this.dragging = false; }.bind(this));
		
		if(!this.options.ignoremouse){
			$$(this.el, this.slider).addEvent('mousewheel', function(e){
				if(this.active){
					e = new Event(e).stop();
					var step = this.sliderFx.step - (e.wheel * 60);
					this.sliderFx.set(step);
				}
			}.bind(this));
		}

		this.resize();
	},
	blink:function(){
		if(!this.active) return;
		this.opacityFx.start(1).start.delay(1800,this.opacityFx,0);
	},
	up:function(amount){
		if(!this.active) return;
		this.blink();
		amount = amount || this.bigstep;
		this.sliderFx.set(this.sliderFx.step - amount);
	},
	down:function(amount){
		if(!this.active) return;
		this.blink();
		amount = amount || this.bigstep;
		this.sliderFx.set(this.sliderFx.step + amount);
	},
	toElement:function(el,dir){
		var screl = $(el).getCoordinates(this.el);
		var scrollpos = this.el.getScroll().y + screl.top;
		var plus = 0;
		var fix = false;
		
		if(this.el_size.height > screl.height){
			if(screl.top < 0){
				fix = true;
			} else if((scrollpos + screl.height) > (scrollpos - screl.top + this.el_size.height)){
				fix = true;
				plus = screl.height - this.el_size.height;
			}
		}
		
			
		if(fix){
			var step = scrollpos + plus;
			
			this.sliderFx.set(step);
			this.el.scrollTo(0,step);
			this.slider.setStyle('top',step);
		}
	},
	resize:function(){
		//this.el.measure(function(){ return this.getDimensions().y; });
		this.bigstep = this.el.measure(function(){ return this.getDimensions().y; });
		this.steps = this.el.measure(function(){ return this.getScrollSize().y; }) - this.bigstep;
		this.active = this.steps > 0;
		
		this.sliderFx.setRange([0,this.steps]);
		this.sliderFx.set(0);

		this.slider.setStyle('display', this.active ? 'block':'none');
	}
});
