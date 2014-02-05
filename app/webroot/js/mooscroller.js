/*
 01/Feb/2011 - Simplificado y modificado el proceso de transición para mostrar disolución entre las fotos.
*/
var mooScroller = new Class({
	Implements: Options,
	options: {
		auto:false,
		prev:false,
		next:false,
		list:false,
		listselector:'a',
		fx_delay: 600,
		wait: 3800
	},
	initialize: function(el,options){
		this.setOptions(options);
		
		this.container = $(el) || false;
		if(!this.container){
			return alert('No se encontró el contenedor con el id '+this.container);
		} else {
			this.container.setStyles({
				overflow:'hidden',
				position:'relative'
			});
		}
		
		this.bt_prev = $(this.options.prev) || false;
		this.bt_next = $(this.options.next) || false;
		this.list = $(this.options.list) || false;
		
		if(this.bt_prev) this.bt_prev.addEvent('click',function(){ this.previous(); }.bindWithEvent(this));
		if(this.bt_next) this.bt_next.addEvent('click',function(){ this.next(); }.bindWithEvent(this));
		
		this.panes = this.container.getChildren();
		this.panes.setStyles({
			position:'absolute',
			height:'100%',
			top:0,
			left:0
		}).fade('out');
		this.total_panes = this.panes.length;
		if(this.list){
			this.listoptions = this.list.getElements(this.options.listselector);
			this.total_options = this.listoptions.length;
			
			if(this.total_panes != this.total_options) { return alert('Numero de paneles ('+this.total_panes+') y Enlaces de la lista ('+this.total_options+') no coinciden ');  }
			
			this.listoptions.each(function(opt,idx){
				opt.addEvent('click',function(){
					this.change(idx);
				}.bindWithEvent(this));
			}.bind(this));
		}
		
		this.current_idx = 0;

		if(this.options.auto)
			this.timer = this.next.periodical(this.options.wait,this);
		
		this.change(this.current_idx);
	},
	change:function(idx){
		var prev = this.current_idx;
		$clear(this.timer);
		if(this.options.auto)
			this.timer = this.next.periodical(this.options.wait,this);
		
		if(this.list){
			this.listoptions.each(function(opt){ opt.removeClass('selected'); });
			this.listoptions[idx].addClass('selected');
		}

		this.panes[prev].fade('out');
		this.panes[idx].fade('in');
		this.current_idx = idx;

	},
	previous:function(){ this.change(this.current_idx <= 0 ? this.total_panes-1 : this.current_idx-1); },
	next:function(){ this.change(this.current_idx >= this.total_panes-1 ? 0 : this.current_idx+1); }
	
});