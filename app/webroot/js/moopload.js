/**
 * Multi uploader for Mootools v1.2 Changes by daetherius [ http://pulsem.mx ]
 * Based on: Multiple file upload element by Stickman [ http://the-stickman.com ]
 *
 * Licence: (as defined by Stickman)
 *  You may use this script as you wish without limitation, however I would
 *  appreciate it if you left at least the credit and site link above in
 *  place. I accept no liability for any problems or damage encountered
 *  as a result of using this script.
 *
 */

var Moopload = new Class({

	initialize:function(elinput, max, start){
		if(typeof elinput == 'string')
			elinput = $(elinput);
		
		if(!(elinput.get('tag').toLowerCase() == 'input' && elinput.get('type').toLowerCase() == 'file' ))
			return alert('Error: No se encontró el Input.');

		this.cont = start || 0;
		this.max = max+start || 0;
		this.pat_name = elinput.get('name');
		this.pat_id = elinput.get('id');
		this.initializeElement(elinput);

		// Layout
		this.container = new Element('div',{'class':'mupload_container'});
		this.list = new Element('div',{'class':'mupload_list'});
		
		this.container.wraps(elinput).adopt(this.list);

		elinput.getParent('form').addEvent('submit',function(){
			this.container.getElement('input').destroy();
		}.bind(this));
	},

	addRow:function(last_el){
		if(this.max == 0 || this.cont <= this.max ){
			
			last_el.setStyle('display','none');

			var upfile = last_el.get('value');

			if(upfile.contains('\\'))
				upfile = upfile.substring(upfile.lastIndexOf('\\') + 1);
			if(upfile.contains('//'))
				upfile = upfile.substring(upfile.lastIndexOf('//') + 1);
			
			var item = new Element('span',{ text:upfile });
			var delete_button = new Element('a',{
				href:'javascript:;',
				html:'<img src="/img/admin/close.gif" alt="Eliminar" />',
				title:'Quitar de la lista',
				events:{ 'click':function(){ this.deleteRow(last_el); }.bind(this) }
			});
			
			var row_element = new Element('div',{ 'class':'mupload_item' }).adopt(delete_button,item,last_el).inject(this.list);

			var new_input = new Element('input',{
				type:'file',
				disabled:(this.cont == this.max)
			}).inject(this.container,'top');

			if(this.cont == this.max)
				alert('Para cargar más de '+this.max+' archivos, deberá hacerlo después de cargar los elementos actuales.');
				
			this.initializeElement(new_input);
		}

	},

	deleteRow:function(el){
		deleted_row = el.getParent('.mupload_item');
		if(confirm('Se eliminará el elemento de la lista de carga. ¿Desea continuar?')){
			this.container.getElement('input').set('disabled',false);
			deleted_row.destroy();
			this.cont--;
		}
	},

	initializeElement:function(el){ 
		el.addEvent('change',function(){ this.addRow(el); }.bind(this));
		el.set('name',this.pat_name.substitute({'n':this.cont}));
		el.set('id',this.pat_id.substitute({'n':this.cont}));
		this.cont++;
	}
});