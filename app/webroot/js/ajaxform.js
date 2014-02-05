/*
* - Métodos Success y Failure
* 10.jun.10 - Calculo de la altura del formulario al momento del envío para evitar discrepancias en distintos navegadores debido a la carga de la imágen del submit button.
*/
var ajaxForm = new Class({
	Implements: Options,
	options: {
		confirm: false,
		subform: false,
		transition: Fx.Transitions.Quart.easeInOut,
		duration: 800,
		successheight: false
	},
	initialize: function(el,options){
		this.setOptions(options);
		if(!$(el)) return alert('Error en ajaxform.js: No se encontró el formulario a enviar - "'+el+'"');
		
		this.fid = el;
		this.eform = $(el);
		this.url = this.eform.get('action');
		this.subform = this.options.subform ? $(this.options.subform) : this.eform.getElement('.subform');
		if(!this.subform){ this.subform = new Element('div.subform').adopt(this.eform.getChildren()).inject(this.eform); }

		this.fx = new Fx.Morph(this.eform,{ duration: this.options.duration, transition: this.options.transition, link:'chain'});
		
		this.message = new Element('div',{ 'class':'formMessage'}).inject(this.subform,'after');
		this.spinner = new Element('div',{ 'class':'formSpinner'}).inject(this.subform,'after');// spinner
		
		this.eform.addEvent('submit', function(e){
			e = new Event(e);e.stop(); // No te submitas!
			if((this.options.confirm && confirm(this.options.confirm)) || !this.options.confirm){
				this.subformheight = this.subform.getCoordinates().height+'px';
				this.successheight = this.options.successheight;
				this.subform.setStyle('height',this.subformheight); // fijar el alto
				this.spinner.setStyles({'height':this.subformheight,'line-height':this.subformheight});

				this.fx.start({'opacity':0}).chain(function(){ //desaparece
					this.spinner.setStyle('display','block');
					this.subform.setStyle('display','none');//desaparece el contenedor del form (Inputs)
					
					this.fx.start({'opacity':1}).chain(function(){// mostrar (Con loader)
						new Request.HTML({
							url:this.url+'?isAjax=1&fid='+this.fid,
							evalScripts:true,
							update:this.message
						}).send(this.eform);
					}.bind(this));
				}.bind(this));
			}
		}.bindWithEvent(this));
	},
	success: function (successmsg){
		this.fx.start({'opacity':0}).chain(function(){ // habiendo recibido el resultado, desaparece el form con el spinner
			this.spinner.setStyle('display','none');// Esconder el spinner
			this.message.set('html',successmsg); // Mostrar mensaje
			this.message.setStyle('display','block'); // Mostrar mensaje
			this.subform.setStyle('display','none'); // Esconder inputs

			if(this.successheight)
				this.fx.start({'height':this.successheight}).chain(function(){ this.fx.start({'opacity':1}); });
			else
				this.fx.start({'opacity':1});
		}.bind(this));
	
	},
	failure: function (){
		this.fx.start({'opacity':0}).chain(function(){ // habiendo recibido el resultado, desaparece el form con el loader
			this.spinner.setStyle('display','none');// Esconder el spinner
			this.message.setStyle('display','none'); // Esconder mensaje
			this.subform.setStyle('display','block'); // Mostrar inputs
			this.fx.start({'opacity':1});
		}.bind(this));
	}
});