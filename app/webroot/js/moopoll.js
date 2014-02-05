var mooPoll = new Class({
	Implements : [ Events, Options ],
	options: {
		data: false,	
		votes: false, // Mostrar No. total de votos
		percents: true, // Mostrar Porcentajes
		autocolor: false, // Colores al azar	
		fx: { 'duration' : 1250,'transition' : Fx.Transitions.Back.easeInOut,'opacity': 0 }
	},
	initialize: function(container, options){
		//Setting up
		this.setOptions(options);
		this.voted = false;
		this.almostvoted = false;
		this.resulted = false;
		
		if($(container)){
			this.poll = $(container);
			this.poll_id = container.split('_')[1];
		} else return false;
		if($(container)){ this.poll = $(container); this.bars_container = this.poll.getElement('.poll_options'); }else return false;
		if(!this.options.data) return false;

		this.w = this.bars_container.getSize().x;
		
		this.links = this.poll.getElements('.poll_option a');
		this.setLayout();
		this.bars = this.poll.getElements('.poll_bar');
		
		this.poll.getElement('.poll_results_button').addEvent('click',function(){ this.seeResults(); }.bind(this));
	},
	setLayout:function(){
		var fxoptions = {
			duration: this.options.fx.duration,
			transition: this.options.fx.transition
		}

		this.links.each(function(el){
			var temp = el.getProperty('id').split('_');
			var opt_id = temp[1].substr(1);
			
			el.addEvent('click',function(e){ e = new Event(e); this.vote(opt_id); }.bindWithEvent(this));
			
			var bar_style = {
				position:'absolute',
				top:0,
				left:0,
				width:5,
				height:el.getParent().getSize().y,
				opacity:this.options.fx.opacity
			};
			bar_style = this.options.autocolor ? $merge(bar_style,{ 'background-color':$RGB($random(95,255), $random(95,255), $random(95,255))}) : bar_style;
			new Element('div',{ id:'p'+this.poll_id+'_o'+opt_id+'_bar', 'class':'poll_bar', styles:bar_style }).inject(el.getParent(),'top').set('morph',fxoptions);
			
		}.bind(this));
		
	},
	vote:function(opt_id){
		if(this.voted) return;
		
		this.bars_container.fade('0.3').get('tween').chain(function(){
			new Request({
				url:'/polls/vote/poll_id:'+this.poll_id+'/opt_id:'+opt_id+'/?isAjax=1',
				method:'get', evalScripts:true, onComplete:function(){ this.bars_container.fade('in').get('tween').chain(function(){ this.almostvoted = true; this.seeResults(); }.bind(this)); }.bind(this)
			}).send();
		}.bind(this));
	},
	seeResults:function(){
		if(this.voted) return;
		if(this.almostvoted) this.voted = true;
		
		this.data = $H(this.options.data);
		//valor máximo
		var maxval = this.data.getValues().max();
		var totalvotes = this.data.getValues().sum();
		
		this.data.each(function(votes,popid){
			var resultText = '';
			// Validar si existe la barra de esa opción y en caso que sí, animarla
			if(!$('p'+this.poll_id+'_o'+popid+'_bar')) return true;
			else {
				// Setting Text Results
				if(this.options.percents) resultText+= (totalvotes?(votes/totalvotes*100).toInt():0)+'%';
				if(this.options.votes) resultText = resultText ? resultText + ' ('+votes+')': votes+' Votos';
				
				if(this.resulted)
					$('p'+this.poll_id+'_o'+popid+'_result').set('html',resultText);
				else 
					new Element('div',{ id:'p'+this.poll_id+'_o'+popid+'_result', html:resultText,'class':'poll_result'}).inject($('p'+this.poll_id+'_o'+popid),'after').slide("hide").slide("in");
				
				// Bar animation
				$('p'+this.poll_id+'_o'+popid+'_bar').morph({'width':(votes*this.w/maxval).toInt(),'opacity':1});
			}
			
		}.bind(this));
		
		if($('poll_'+this.poll_id).getElement('.poll_total_votes'))
			$('poll_'+this.poll_id).getElement('.poll_total_votes').set('html','Votos totales = '+totalvotes);
		else
			new Element('p',{'class':'poll_total_votes', html:'Votos totales = '+totalvotes}).inject($('poll_'+this.poll_id).getElement(".poll_options"),'bottom').slide('hide').slide('in');
		
		this.resulted = true;
	}
});