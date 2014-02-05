/*
* 03/Mar/10 - Se reduce la animación de aparición de los tips a un tween de opacidad.
* 14/Abr/11 - Modo 'cancel' para las animaciones de los tips, agregado un delay.
*/

//// 

(function() {
    var method;
    var noop = function noop() {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());

new SmoothScroll({ duration:1500,transition:Fx.Transitions.Quint.easeInOut }, window);
$$('input[type=password]').each(function(el){ el.set('autocomplete','off'); });
//$$('.add2cart input[type=submit]').each(function(el){ el.set('autocomplete','off'); });

$$('.cuteCheckbox').addClass('cuteCheckboxInit');
$$('.cuteCheckboxInit input[type=checkbox]').each(function(el){
	var label = $$('label[for='+el.get('id')+']');
	
	if(!label)
		return false;

	if(el.checked){
		label.addClass('checked');
	}else{
		label.removeClass('checked');
	}
		
	el.addEvent('click',function(e){
		var ev = new Event(e);
		var chk = ev.target;
		
		if(chk.checked){
			this.addClass('checked');
		}else{
			this.removeClass('checked');
		}
		
	}.bind(label));
});

////

	formtips = new Tips('.formtipCaller',{
		className : 'formtip tooltip',
		offsets:{'x':5,'y':5},
		showDelay:0,
		hideDelay:0,
		fixed:true,
		onShow: function(el,caller){ // En Moo 1.2.4 se requiere hackear/agregar el segundo param dentro del more.
			var el_ = el.getCoordinates();
			var cal_ = caller.getCoordinates();

			var title = el.getElement('.tip-title');
			if(title.get('html')=='') title.setStyle('display','none');

			this.options.offset.y = -(el_.height);
			this.options.offset.x = ((el_.width - cal_.width)/-2).toInt();
			
			if(el.getStyle('opacity') > 0) // Reinicia el opacity cuando se posiciona sobre un nuevo input y el tip de otro aún está visible
				el.setStyle('opacity',0);
			
			el.set('tween',{ duration:300, link:'cancel', property:'opacity', transition:'pow:out' }).tween(.8);
		},
		onHide: function(el){
			if(el.getStyle('opacity') > 0)
				el.tween(0);
		}
	});

	////
	myTips = new Tips('.tipCaller',{
		className : 'tooltip',
		offsets:{'x':16,'y':8},
		showDelay:150,
		hideDelay:0,
		fixed:false,
		onShow: function(el){
			var title = el.getElement('.tip-title');
			if(title.get('html')=='') title.setStyle('display','none');
			el.set('morph',{ duration:300, link:'cancel', transition:'pow:out' }).morph({ 'margin-top':[-25,0], 'opacity':[0,1] });
		},
		onHide: function(el){ if(el.getStyle('opacity') > 0) el.morph({ 'margin-top':[0,-25], 'opacity':[1,0] }); }

	});

function mooPop(msg, timeout){

	var pop_txt = msg.indexOf('#') === 0 ? $(msg.substr(1)).setStyle('display','none').get('html') : msg;
	var pop = new Element('div',{ 'class':'mooPop'}).adopt(
		new Element('div',{ 'class':'mooPopWrap'}).adopt([
			new Element('a',{ href:'javascript:;','class':'mooPopClose',html:'Cerrar', events:{ click:function(){ this.getParent('.mooPop').nix(true); }}}),
			new Element('div',{'class':'mooPopMsg', html:pop_txt.replace('. ','<br/>')})
		])
	).inject(document.body);

	pop.makeDraggable();
	var pop_size = pop.getSize();
	pop.set('reveal',{duration:900,transition: 'quint:out' }).setStyles({
		display:'none',
		top:((window.getScrollTop()+window.getHeight()-pop_size.y)/2).toInt(),
		left:((window.getScrollLeft()+window.getWidth()-pop_size.x)/2).toInt()
	}).reveal();

	if(timeout !== null){
		pop.nix.delay(timeout,pop,true);
	}
}

//== CORE EXTENSION ====================================

var _getType = function(inp) {
	var type = typeof inp, match; var key;
	
	if (type == 'object' && !inp) return 'null';
	if (type == "object") {
		if (!inp.constructor) return 'object';

		var cons = inp.constructor.toString();
		if (match = cons.match(/(\w+)\(/)) cons = match[1].toLowerCase();

		var types = ["boolean", "number", "string", "array"];

		for(key in types) {
			if (cons == types[key]) { type = types[key]; break; }
		}
	}
	return type;
};

var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) == "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();

// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};

// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};

// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};