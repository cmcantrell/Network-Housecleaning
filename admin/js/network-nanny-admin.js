(function(global){
	'use strict';			// ES5.1+: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Strict_mode
	
	class UtilitiesProto{
		constructor(selector){
			console.log('@UtilitiesProto.contructor()');
			this.treeSearchDepth 	= 10;
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
			while( ele.nodeType === 1 && ele.getAttribute('data-action') === null && i < this.treeSearchDepth ){
				ele = ele.parentNode;
			}
			if( typeof ele.getAttribute !== 'function' ){return false;}
	
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
};

NetworkNanny.prototype.registerUIelements		= function(){
	Utils('.network-nanny-ui').event('click',this);
};

NetworkNanny.prototype.networkNannyCompile 		= function(e,t){
	console.log('@NetworkNanny.networkNannyCompile()');
	e.stopPropagation();
	e.preventDefault();
	this.UIelement.innerHTML			= "compiling files...";
	nonce 								= jQuery(this).attr("data-nonce");
	let action 							= this.getAjaxAction(t); 
	jQuery.ajax({
		url 		: _networknanny.ajax_url,
		type 		: 'post',
		data 		: {
			action 		: action
		},
		done 		: function(){

		},
		fail		: function(){

		}
	});
};

NetworkNanny.prototype.getAjaxAction			= function(t){
	return t.getAttribute('data-wpajax_action') || false;
};

jQuery(document).ready(function(){
	window.NetworkNanny		= new NetworkNanny();
});
