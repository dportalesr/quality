var mooTabs = new Class({	
	Implements: Options,
	options: {
		duration:200,
		transition: 'pow:out'
	},
	current:0,
	changing:false,
	initialize: function(container, options){
		this.setOptions(options || {});
		
		this.container = $(container).addClass('mooTabContainer');
		
		this.buttons = this.container.getChildren('a');
		this.panels = this.container.getChildren('div');
		
		if((!this.buttons.length) || (!this.panels.length) || this.panels.length != this.panels.length){
			this.container.setStyle('display','none');
			return;
		}
		
		this.buttons[0].addClass('selected');
		this.panels[0].addClass('selected');
		this.current = 0;
		
		this.tabButtons = new Element('div.mooTabButtons').inject(this.container).adopt(this.buttons);
		this.tabPanels = new Element('div.mooTabPanels').inject(this.container).adopt(this.panels);
		this.fx = new Fx.Tween(this.tabPanels,{ property:'opacity', duration:this.options.duration, transition: this.options.transition });
		
		//Binding buttons
		this.buttons.each(function(el,idx){
			el.addEvent('click',function(){
				if(idx != this.current) this.change(idx);
			}.bind(this));
		}.bind(this));
	},
	change:function(idx){
		if(this.changing) return;
		
		if(idx < this.panels.length){
			var current = this.current;
			this.buttons[current].removeClass('selected');
			this.buttons[idx].addClass('selected');
			
			this.changing = true;
			this.fx.start(0).chain(function(){
				this.panels[current].removeClass('selected');
				this.current = current = idx;
				this.panels[current].addClass('selected'); 
				this.fx.start(1).chain(function(){ this.changing = false; }.bind(this));
			}.bind(this));
		}
	}
});