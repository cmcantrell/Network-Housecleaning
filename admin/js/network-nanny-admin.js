(function(global){
	'use strict';			// ES5.1+: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Strict_mode
	
	class UtilitiesProto{
		constructor(selector){
			console.log('@UtilitiesProto.contructor()');
			this.treeSearchDepth 	= 2;
			this.feature			= {};
			this.setFeatures(this.feature);
		}
		
		/*
			*
			*	@description 		Javascript engine feature detection
			*
			*
			*
		**/ 
		setFeatures(feature){
			feature.ontouchstart		= 'ontouchstart' in document.documentElement === true ? true : false;
			feature.ontouchmove			= 'ontouchmove' in document.documentElement === true ? true : false;
			feature.ontoucend			= 'ontouchend' in document.documentElement === true ? true : false;
			
			feature.transitionEndEvent			= this.whichTransitionEvent();
		}
		
		whichTransitionEvent(){
			let t,
				el						= document.createElement('faux'),
				transitions				= {
					'transition'		: 'transitionend',
					'OTransition'		: 'oTransitionEnd',
					'MozTransition'		: 'transitionend',
					'WebkitTransition'	: 'webkitTransitionEnd'
				}
			for(let i in transitions){
				if(el.style[i] !== undefined){
					return transitions[i];
				}
			}
			return false;
		}

		/*
			*
			*	@description 			event controller
			*
			*
			*
		**/
		event(event, context=this){
			console.log('@UtilitiesProto.event()');
			if( !event ){
				return false;
			}
			let ele=this.elements;
			
			if( event instanceof Array === false ){
				if(typeof event === 'string'){
					event = [event];
				}
			}
			
			for(let el in ele){
				if( ele.hasOwnProperty(el) ){
					event.forEach((ev) => {
						ele[el].addEventListener(ev, (ev) => {
							ev.preventDefault();
							ev.stopPropagation();
							this.eventController( ev, context, null );
						});
					});
				}
			}
		}
		
		eventController(event, context=this, args){
			console.log('@UtilitiesProto.eventController()');
			let ele 	= event.target,
				i 		= 0;
			while( typeof ele !== null && ele.nodeType === 1 && ele.getAttribute('data-action') === null && i < this.treeSearchDepth ){
				ele = ele.parentNode;
				i++;
			}

			if( typeof ele.getAttribute !== 'function' || ele.getAttribute('data-action') === null){return false;}

			let action 		= ele.getAttribute('data-action').split(','),
				Obj			= context || this;
		
			if( action === null ){return false;}
			
			for(let i=0, n=action.length; i<n; i++){
				if( typeof Obj[action[i]] === 'function' ){
					Obj[action[i]](event, ele);
					this.action		= action[i];
				}
			}
			return this;
		}
	}
	
	class Utilities extends UtilitiesProto{
		constructor( selector ) {
			super();
			console.log('@Utilities.constructor()');
			var elements 			= document.querySelectorAll(selector);
			this.length 			= elements.length;
			this.elements 			= elements;
			this.context			= this;
			this.action;
		}
	}
	
	return global.Utils = selector => new Utilities(selector);

})(this || window); // babel doesn't like "this", transpiles to 'undefined'

/*
	*
	*
	*
	*
	*
**/
let NetworkNanny	=	 function(){
	this.UIelement			= document.getElementById('network-nanny-js-compile-ui');
	this.registerUIelements();
	this.profile  		 	= {};
	return this;
};

NetworkNanny.prototype.registerUIelements		= function(){
	Utils('.network-nanny-ui').event('click',this);
};

NetworkNanny.prototype.networkNannyCompileGetAutoProfile 		= function(e,t){
	console.log('@NetworkNanny.networkNannyCompile()');
	this.updateNetworkNannyUI("<b>compiling files...</b>");
	t.disabled = true;
	nonce 								= jQuery(this).attr("data-nonce");
	let action 							= this.getAjaxAction(t),
		self							= this; 
	jQuery.ajax({
		url 		: _networknanny.ajax_url,
		type 		: 'post',
		context 	: self,
		data 		: {
			action 		: action,
			nonce 		: nonce,
			data 		: {
				'js' 		: _networknanny.registeredJSScripts
			}
		},
		timeout 	: 0

	}).done(function(response){
		if(response){
			if(JSON.parse(response)){
				let res 						= JSON.parse(response);
				self.profile[action] 			= res;
				self.updateNetworkNannyUI(self.buildCompileResponseHTML(res));
			}else{

			}
		}else{

		}
	}).error(function(){
		self.updateNetworkNannyUI('<b>Something went terribly wrong</b>');
	}).fail(function(){
		self.updateNetworkNannyUI('<b>Something went terribly wrong</b>');
	}).always(function() {
		t.disabled = false;
	});
};

NetworkNanny.prototype.buildCompileResponseHTML = function(res){
	let htmlOut;
		htmlOut						= '<div id="network-nanny_script-profile"><b>Retrieved automated script profile.</b><p>This is an automated list of your scripts ordered by dependencies. Press save to compile your scripts and use this profile.</p>';
		htmlOut 					+= '<button id="fuck-off" data-action="saveScriptProfile" data-wpajax_action="saveProfile">Use Profile</button>';
		htmlOut						+= '<table id="network-nanny_script-profile-data-table">';
	for( let index in res ){
		//htmlOut 		+= `<tr><td data-index="${index}">${res[index]['handle']}<span class="dashicons dashicons-sort"></span></td></tr>`;
		htmlOut 		+= `<tr><td data-index="${index}">${res[index]['handle']}</td></tr>`;
	}
	htmlOut							+= '</table></div>';
	return htmlOut;
}

NetworkNanny.prototype.saveScriptProfile		= function(e,t){
	console.log('NetworkNanny.saveScriptProfile()');
	console.log(this.profile);
	this.updateNetworkNannyUI("<b>Saving profiles...</b>");
	t.disabled = true;
	nonce 								= jQuery(this).attr("data-nonce");
	let action 							= this.getAjaxAction(t),
		self							= this;
	jQuery.ajax({
		url 		: _networknanny.ajax_url,
		type 		: 'post',
		context 	: self,
		data 		: {
			action 		: action,
			profile 	: self.profile,
			nonce 		: nonce
		},
		timeout 	: 0

	}).done(function(response){
		if(response){
			let res 				= JSON.parse(response),
				UImessage			= "<ul>";
			if(res){
				console.log(res);
				for(let index in res){
					UImessage 		+= `<li>${res[index]['error']}: ${res[index]['message']}</li>`;
				}
			}
			UImessage			+= "</ul>";
			self.updateNetworkNannyUI(UImessage);
		}else{

		}
	}).error(function(){
		self.updateNetworkNannyUI('<b>Something went terribly wrong</b>');
	}).fail(function(){
		self.updateNetworkNannyUI('<b>Something went terribly wrong</b>');
	}).always(function() {
		t.disabled = false;
	});
}

NetworkNanny.prototype.updateNetworkNannyUI 	= function(data, UI = this.UIelement){
	if(!data){
		console.log('no data to UI update');
		return false;
	}
	UI.innerHTML			= data;
}

NetworkNanny.prototype.getAjaxAction			= function(t){
	return t.getAttribute('data-wpajax_action') || false;
};

jQuery(document).ready(function(){
	window.NetworkNanny		= new NetworkNanny();
});
