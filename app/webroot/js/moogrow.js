/*
* 05/Ene/12 - Versión estable con capacidad de link en las imágenes usando atributo rel = "zoomed_image_url|link_url"
*/
var moogrow = new Class({
	Implements: Options,
	ready:false,
	loaded:0,
	nitems:0,
	options: {
		selector:'img',
		fx: { duration:200 }
	},
	initialize: function(container, options){
		this.setOptions(options);

		this.container = $(container || document.body);
		this.items = this.container.getElements(this.options.selector);
		
		if(!this.items)
			return;
		else
			this.nitems = this.items.length;
		
		Object.append(this.options.fx,{link:'cancel'});
		
		this.items.each(function(el){ this.validate(el); }.bind(this));
	},
	validate: function(el){
		if(el.get('tag') == 'img' && el.get('rel') && el.get('rel').match(/\.(jpe?g|png|gif|bmp)/gi)){
			if(el.complete){
				this.checkLoaded();
			} else {
				el.addEvents({
					load:function(){ this.checkLoaded(); }.bind(this),
					error:function(){ this.checkLoaded(); }.bind(this)
				});
			}
		} else { this.checkLoaded(); }
	},
	checkLoaded: function(){
		this.loaded++;
		if(this.loaded >= this.nitems)
			this.start();
	},
	start:function(){
		this.items.each(function(img){
			var origSize = img.getDimensions({ computeSize:true });
			var caption = img.get('alt') || img.get('title') || false;
			var url = false;
			var match = img.get('rel').split('|',2);
			if(match.length == 2){
				img.set('rel',match[0]);
				url = match[1];
			}
			var rel = img.get('alt') || img.get('title') || false;
			var small = img.clone().addClass('moogrow_small').setStyles({ width: origSize.width, height: origSize.height, opacity:0.5 }).set('morph',this.options.fx).set('tween',this.options.fx);
			var _small = small;
			if(url){ _small = new Element('a',{ href:url }).adopt(small); }
			
			var big = new Element('img.moogrow_big').addEvents({
				load:function(e){
					var moogrow = this.retrieve('moogrow');
					var bigSize = moogrow.big.getDimensions({ computeSize:true });

					moogrow.loaded = true;
					var widthGained = bigSize.width - moogrow.origSize.width;
					moogrow.wrapperSize.totalWidth += widthGained;

					this.store('moogrow',moogrow);

					var morph_pos = moogrow.instance.reposition(this, widthGained);
					var morph__ = Object.merge({ width: moogrow.wrapperSize.totalWidth },morph_pos);

					moogrow.small.set('src',this.get('rel')).fade('in');
					moogrow.clon.morph(morph__).get('morph').chain(function(){
						this.addClass('moogrow_loaded');
					}.bind(moogrow.clon));
					moogrow.small.morph({ width: bigSize.width, height: bigSize.height });
					
				}.bind(img),
				error:function(e){
					var moogrow = this.retrieve('moogrow');
					moogrow.loaded = true;
					this.store('moogrow',moogrow);
					moogrow.small.fade('in');
				}.bind(img)
			});
			
			var wrapper = new Element('div.moogrow_wrapper').adopt([big,_small]);
			var clon = new Element('div.moogrow').inject(document.body).adopt(wrapper).set('morph',this.options.fx).set('tween',this.options.fx);
			var smallSize = small.getDimensions({ computeSize:true });
			var wrapperSize = wrapper.measure(function(){ return this.getDimensions({ computeSize:true }); });
			var clonSize = clon.getDimensions({ computeSize:true });

			if(caption){ clon.adopt(new Element('div.moogrow_caption',{ html:caption })); }
			
			var clon_offsets = small.measure(function(){ return this.getPosition(clon); });
			var offsets = {
				x: -(clon_offsets.x + smallSize.computedLeft + clonSize['border-left-width'])+1,
				y: -(clon_offsets.y + smallSize.computedTop + clonSize['border-top-width']),
				w: clon_offsets.x + smallSize.computedLeft,
				h: clon_offsets.y + smallSize.computedTop
			}
			var moogrow = {
				instance:this,
				timer:false,
				loaded:false,
				clon:clon,
				small:small,
				big:big,
				offsets:offsets,
				origSize:origSize,
				wrapperSize:wrapperSize
			};			
			
			img.store('moogrow',moogrow);
			clon.setStyles(this.reposition(img));
			
			var mouseenter = function(e){
				var ev = new Event(e);
				var moogrow = this.retrieve('moogrow');
				
				if(!moogrow.loaded){
					moogrow.timer = (function(){
						var moogrow = this.retrieve('moogrow');
						moogrow.big.set('src',this.get('rel'));
					}).delay(300,img);
					
					this.store('moogrow',moogrow);
				}
				
				moogrow.instance.show(img);
			};
			
			var mouseleave = function(){
				var moogrow = this.retrieve('moogrow');
				
				if(!moogrow.loaded && moogrow.timer !== false){
					clearTimeout(moogrow.timer);
					this.store('moogrow',moogrow);
				}
				
				moogrow.clon.fade("out").get('tween').chain(function(){ this.setStyle("display","none") }.bind(moogrow.clon));
			};
			
			img.addEvent("mouseenter",mouseenter.bind(img));
			clon.addEvent("focus",mouseenter.bind(img));
			
			clon.addEvent("mouseleave",mouseleave.bind(img));
		}.bind(this));
	},		
	reposition:function(img, morphing){
		morphing = morphing || false;
		var moogrow = img.retrieve('moogrow');
		
		if(morphing){
			moogrow.offsets.x -= morphing/2;img.store('moogrow',moogrow);
		} else {
			moogrow.clon.setStyle('width', moogrow.wrapperSize.totalWidth);
		}
		
		var newpos = moogrow.clon.position({
			relativeTo:img,
			position:'topLeft',
			edge:'topLeft',
			offset: moogrow.offsets,
			returnPos: true
		});

		return newpos;
	},
	show:function(img){
		var moogrow = img.retrieve('moogrow');
		var pos = img.getPosition();
		
		var morph_pos = Object.merge(this.reposition(img),{display:'block'});
		
		moogrow.clon.setStyles(morph_pos);
		moogrow.clon.fade('in');
	}
});