/****
 * Fancy File Uploads with Mootools and CSS
 * Version 2.0
 * Author: Paul Tulipana, Design Less Better
 * 2.0 version: Daniel Portales daetherius
 * Author URI: http://www.designlessbetter.com/
 * Last Updated: 2010-02-08
 * 
 * FancyFiles class based almost entirely on Styling File Inputs 1.0 by Shaun Inman.
 * Shaun's URI: http://www.shauninman.com/
 ***/

var moofancyupload = new Class({
	Implements: Options,
	fileNameMaxChars : 30,
	options:{ lone : null, fileNameMaxChars : null, caption: null },
	initialize: function(element, options) {
		this.setOptions({ lone: true, fileNameMaxChars:30 }, options);
		var IE6 = false /*@cc_on || @_jscript_version < 5.7 @*/;
		if (IE6 || !document.getElementsByTagName) { return; } // no support for opacity or the DOM

		this.fancy($(element));
	},
	fancy: function(el){
		el.setStyle('opacity',0.005); // on ta butonshito? :3
		this.wrapper = new Element('div',{ 'class':'fancyupload' })
		this.wrapper.wraps(el);
	
		if(this.options.caption)
			this.caption = new Element('div',{ 'class':'fancycaption','html':this.options.caption,'styles':{ 'line-height':this.wrapper.getDimensions().y }}).inject(this.wrapper);

		if(this.options.lone)
			this.filename = new Element('div',{ 'class':'fancyfilename' }).inject(this.wrapper,'after');
		
		this.eventize(el);
	},
	eventize : function(el){
		if(this.options.lone){
			el.addEvent('change', function(e){
				if (el.value != ''){
				
					var val = el.value;
					var piv = val.lastIndexOf("\\");
					var len = val.length - piv - 1;
					val = val.substr(piv + 1);

					if (len > this.options.fileNameMaxChars)
						val = val.substr(0,this.options.fileNameMaxChars-3)+"..";
						
					this.filename.set('html',val);
				}
			}.bindWithEvent(this));
		}
	
		el.getParent('div').addEvent('mousemove', function(e){
			e = new Event(e);
			if (typeof e.page.y == 'undefined' &&  typeof e.clientX == 'number' && document.documentElement)
			{
				e.page.x = e.clientX + document.documentElement.scrollLeft;
				e.page.y = e.clientY + document.documentElement.scrollTop;
			}
			
			var ox = oy = 0;
			var elem = this;
			
			if (elem.offsetParent)
			{
				ox = elem.offsetLeft;
				oy = elem.offsetTop;
				while (elem = elem.offsetParent)
				{
					ox += elem.offsetLeft;
					oy += elem.offsetTop;
				}
			}

			var x = e.page.x - ox;
			var y = e.page.y - oy;
			var w = el.offsetWidth;
			var h = el.offsetHeight;

			el.setStyle('top', y - (h / 2));
			el.setStyle('left', x - (w - 30));
		});
	}
});