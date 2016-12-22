(function(global){
	'use strict';
	
	class UtilitiesProto{
		constructor(selector){
			console.log('@UtilitiesProto.contructor()');
			this.treeSearchDepth 	= 4;
			
			this.feature			= {};
			this.setFeatures(this.feature);
		}
		
		event(event, context=this){
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
					event.forEach((ev) => {		 // ES 6.0+
						ele[el].addEventListener(ev, (ev) => {
							this.eventController( ev, context, null );
						});
					});
				}
			}
		}
		
		eventController(event, context=this, args){
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
					console.log(this);
				}
			}
			return this;
		}
		
		/*
			*
			*	@description 		Javascript engine feature detection
			*
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
	}
	
	class Utilities extends UtilitiesProto{
		constructor( selector ) {
			super();
			var elements 			= document.querySelectorAll(selector);
			this.length 			= elements.length;
			this.elements 			= elements;
			this.context			= this;
			this.action;
		}
	}
	
	return global.Utils = selector => new Utilities(selector);
	
})(this || window); // babel doesn't like "this", transpiles to 'undefined'
// call


/*
	*
	*
	*
	*
	*
**/







	(function( $, global ) {
		'use strict';
		let NetworkNanny	=	 function(){
			this.UIelements 		= $('#network-nanny-ui');
		};

		NetworkNanny.prototype.registerUIelements		= function(){
			this.UIelements.forEach(function(ele){
				Utils(ele).event();
			});	
		};
		jQuery(document).ready(function(global = window){
			global.NetworkNanny		= NetworkNanny;
		});
	})( jQuery, this || window );
