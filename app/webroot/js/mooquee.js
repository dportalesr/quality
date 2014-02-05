/************************************************
*   mooquee v.1.1                               *
*   Http: WwW.developer.ps/moo/mooquee          *
*   Dirar Abu Kteish dirar@zanstudio.com        *
*   2009-01-30                                  *
*************************************************
*   Extend By www.Sod.hu                        *
*   new directions: top, bottom                 *
*   2008-04-30                                  *
/***********************************************/
/* This program is free software. It comes without any warranty, to
* the extent permitted by applicable law. You can redistribute it
* and/or modify it under the terms of the Do What The Fuck You Want
* To Public License, Version 2, as published by Sam Hocevar. See
* http://sam.zoy.org/wtfpl/COPYING for more details. */ 

var mooquee = new Class({
	Implements: this.Options,
	initialize: function(element, options) {
		this.setOptions({
			speed: 30,
			direction: 'bottom',
			pauseOnOver: true
	    }, options);
	    this.timer = null;
	    this.content = null;
	    this.marquee = $(element);
	    this.marqueesize = this.marquee.getSize();
	    this.init();
	},
	init: function() {
		var content = this.marquee.get('html');
		
		var clon = new Element('div',{ 'class':'mooquee_clon', 'html':content });
		this.content = new Element('div',{ 'class':'mooquee_content', 'styles':{ position:'absolute' }}).adopt(clon);
		this.marquee.setStyles({ position:'relative', overflow:'hidden' }).set('html', '').adopt(this.content);
		this.contentsize = this.content.getDimensions();
		
		var factor = Math.floor(this.marqueesize.y/this.contentsize.height) + 1;
		
		for(i=0;i<factor;i++)
			this.content.adopt(clon.clone()); //duplicate
		
		if(this.options.pauseOnOver){ this.bindMouse(); }

		this.setStartPos();
		this.timer = this.play.delay(this.options.speed, this);
	},
	setStartPos: function(){
		switch(this.options.direction){
			case 'bottom':
				this.content.setStyle('bottom', this.marqueesize.y.toInt());
				break;
			case 'top':
				this.content.setStyle('bottom', (this.contentsize.height*2)+this.marqueesize.y);
				break;
			case 'left':
				this.content.setStyle('left', this.marqueesize.x.toInt());
				break;
			default:
				this.content.setStyle('left', -this.contentsize.width);
				break;
		}
	},
	bindMouse : function(){ return;
		this.marquee.addEvents({
			'mouseenter' : function(){ this.clearTimer(); }.bind(this),
			'mouseleave' : function(){ this.timer = this.play.delay(this.options.speed, this); }.bind(this)
		});
	},
	play: function(){
		/* sod.hu Ext */
		if(this.options.direction == 'bottom' || this.options.direction == 'top')
			var pos = this.content.getStyle('bottom').toInt();
		else
			var pos = this.content.getStyle('left').toInt();

		switch(this.options.direction){
			case 'bottom':
				this.content.setStyle('bottom', pos-1);
				break;
			case 'top':
				this.content.setStyle('bottom',pos+1);
				break;
			case 'left':
				this.content.setStyle('left',pos-1);
				break;
			default:
				this.content.setStyle('left',pos+1);
				break;
		}
		this.checkEnd(pos);
		this.timer = this.play.delay(this.options.speed, this);
	},
	resume: function(){
		this.stop();
		//if(this.options.pauseOnOver){ this.bindMouse(); }
		this.timer = this.play.delay(this.options.speed, this);
	},
	stop: function(){
		this.clearTimer();
		//this.content.removeEvents();
	},
	clearTimer: function(){ $clear(this.timer); },
	checkEnd: function(pos){
		switch(this.options.direction){
			case 'bottom':
				if(pos < -this.contentsize.height)
					this.content.setStyle('bottom', -5);
				break;
			case 'top':
				if(pos > this.contentsize.height)
					this.content.setStyle('bottom', -this.contentsize.height*2 );
				break;
			case 'left':
				if(pos < -(this.contentsize.width))
					this.content.setStyle('left', this.contentsize.width);
				break;
			default:
				if(pos > this.contentsize.width)
					this.content.setStyle('left', -this.contentsize.width );
				break;
		}
	}
});

mooquee.implement(new Options);