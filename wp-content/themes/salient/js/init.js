/*-------------------------------------------------------------------------

	1.	Plugin Init
	2.	Helper Functions
	3.	Shortcode Stuff
	4.	Header + Search
	5.	Page Specific
	6.  Scroll to top 
	7.	Cross Browser Fixes


-------------------------------------------------------------------------*/






/*-------------------------------------------------------------------------*/
/*	1.	Plugin Init
/*-------------------------------------------------------------------------*/


/*!
Waypoints - 4.0.1
*/
!function(){"use strict";function t(o){if(!o)throw new Error("No options passed to Waypoint constructor");if(!o.element)throw new Error("No element option passed to Waypoint constructor");if(!o.handler)throw new Error("No handler option passed to Waypoint constructor");this.key="waypoint-"+e,this.options=t.Adapter.extend({},t.defaults,o),this.element=this.options.element,this.adapter=new t.Adapter(this.element),this.callback=o.handler,this.axis=this.options.horizontal?"horizontal":"vertical",this.enabled=this.options.enabled,this.triggerPoint=null,this.group=t.Group.findOrCreate({name:this.options.group,axis:this.axis}),this.context=t.Context.findOrCreateByElement(this.options.context),t.offsetAliases[this.options.offset]&&(this.options.offset=t.offsetAliases[this.options.offset]),this.group.add(this),this.context.add(this),i[this.key]=this,e+=1}var e=0,i={};t.prototype.queueTrigger=function(t){this.group.queueTrigger(this,t)},t.prototype.trigger=function(t){this.enabled&&this.callback&&this.callback.apply(this,t)},t.prototype.destroy=function(){this.context.remove(this),this.group.remove(this),delete i[this.key]},t.prototype.disable=function(){return this.enabled=!1,this},t.prototype.enable=function(){return this.context.refresh(),this.enabled=!0,this},t.prototype.next=function(){return this.group.next(this)},t.prototype.previous=function(){return this.group.previous(this)},t.invokeAll=function(t){var e=[];for(var o in i)e.push(i[o]);for(var n=0,r=e.length;r>n;n++)e[n][t]()},t.destroyAll=function(){t.invokeAll("destroy")},t.disableAll=function(){t.invokeAll("disable")},t.enableAll=function(){t.Context.refreshAll();for(var e in i)i[e].enabled=!0;return this},t.refreshAll=function(){t.Context.refreshAll()},t.viewportHeight=function(){return window.innerHeight||document.documentElement.clientHeight},t.viewportWidth=function(){return document.documentElement.clientWidth},t.adapters=[],t.defaults={context:window,continuous:!0,enabled:!0,group:"default",horizontal:!1,offset:0},t.offsetAliases={"bottom-in-view":function(){return this.context.innerHeight()-this.adapter.outerHeight()},"right-in-view":function(){return this.context.innerWidth()-this.adapter.outerWidth()}},window.Waypoint=t}(),function(){"use strict";function t(t){window.setTimeout(t,1e3/60)}function e(t){this.element=t,this.Adapter=n.Adapter,this.adapter=new this.Adapter(t),this.key="waypoint-context-"+i,this.didScroll=!1,this.didResize=!1,this.oldScroll={x:this.adapter.scrollLeft(),y:this.adapter.scrollTop()},this.waypoints={vertical:{},horizontal:{}},t.waypointContextKey=this.key,o[t.waypointContextKey]=this,i+=1,n.windowContext||(n.windowContext=!0,n.windowContext=new e(window)),this.createThrottledScrollHandler(),this.createThrottledResizeHandler()}var i=0,o={},n=window.Waypoint,r=window.onload;e.prototype.add=function(t){var e=t.options.horizontal?"horizontal":"vertical";this.waypoints[e][t.key]=t,this.refresh()},e.prototype.checkEmpty=function(){var t=this.Adapter.isEmptyObject(this.waypoints.horizontal),e=this.Adapter.isEmptyObject(this.waypoints.vertical),i=this.element==this.element.window;t&&e&&!i&&(this.adapter.off(".waypoints"),delete o[this.key])},e.prototype.createThrottledResizeHandler=function(){function t(){e.handleResize(),e.didResize=!1}var e=this;this.adapter.on("resize.waypoints",function(){e.didResize||(e.didResize=!0,n.requestAnimationFrame(t))})},e.prototype.createThrottledScrollHandler=function(){function t(){e.handleScroll(),e.didScroll=!1}var e=this;this.adapter.on("scroll.waypoints",function(){(!e.didScroll||n.isTouch)&&(e.didScroll=!0,n.requestAnimationFrame(t))})},e.prototype.handleResize=function(){n.Context.refreshAll()},e.prototype.handleScroll=function(){var t={},e={horizontal:{newScroll:this.adapter.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.adapter.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};for(var i in e){var o=e[i],n=o.newScroll>o.oldScroll,r=n?o.forward:o.backward;for(var s in this.waypoints[i]){var a=this.waypoints[i][s];if(null!==a.triggerPoint){var l=o.oldScroll<a.triggerPoint,h=o.newScroll>=a.triggerPoint,p=l&&h,u=!l&&!h;(p||u)&&(a.queueTrigger(r),t[a.group.id]=a.group)}}}for(var c in t)t[c].flushTriggers();this.oldScroll={x:e.horizontal.newScroll,y:e.vertical.newScroll}},e.prototype.innerHeight=function(){return this.element==this.element.window?n.viewportHeight():this.adapter.innerHeight()},e.prototype.remove=function(t){delete this.waypoints[t.axis][t.key],this.checkEmpty()},e.prototype.innerWidth=function(){return this.element==this.element.window?n.viewportWidth():this.adapter.innerWidth()},e.prototype.destroy=function(){var t=[];for(var e in this.waypoints)for(var i in this.waypoints[e])t.push(this.waypoints[e][i]);for(var o=0,n=t.length;n>o;o++)t[o].destroy()},e.prototype.refresh=function(){var t,e=this.element==this.element.window,i=e?void 0:this.adapter.offset(),o={};this.handleScroll(),t={horizontal:{contextOffset:e?0:i.left,contextScroll:e?0:this.oldScroll.x,contextDimension:this.innerWidth(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:e?0:i.top,contextScroll:e?0:this.oldScroll.y,contextDimension:this.innerHeight(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};for(var r in t){var s=t[r];for(var a in this.waypoints[r]){var l,h,p,u,c,d=this.waypoints[r][a],f=d.options.offset,w=d.triggerPoint,y=0,g=null==w;d.element!==d.element.window&&(y=d.adapter.offset()[s.offsetProp]),"function"==typeof f?f=f.apply(d):"string"==typeof f&&(f=parseFloat(f),d.options.offset.indexOf("%")>-1&&(f=Math.ceil(s.contextDimension*f/100))),l=s.contextScroll-s.contextOffset,d.triggerPoint=Math.floor(y+l-f),h=w<s.oldScroll,p=d.triggerPoint>=s.oldScroll,u=h&&p,c=!h&&!p,!g&&u?(d.queueTrigger(s.backward),o[d.group.id]=d.group):!g&&c?(d.queueTrigger(s.forward),o[d.group.id]=d.group):g&&s.oldScroll>=d.triggerPoint&&(d.queueTrigger(s.forward),o[d.group.id]=d.group)}}return n.requestAnimationFrame(function(){for(var t in o)o[t].flushTriggers()}),this},e.findOrCreateByElement=function(t){return e.findByElement(t)||new e(t)},e.refreshAll=function(){for(var t in o)o[t].refresh()},e.findByElement=function(t){return o[t.waypointContextKey]},window.onload=function(){r&&r(),e.refreshAll()},n.requestAnimationFrame=function(e){var i=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||t;i.call(window,e)},n.Context=e}(),function(){"use strict";function t(t,e){return t.triggerPoint-e.triggerPoint}function e(t,e){return e.triggerPoint-t.triggerPoint}function i(t){this.name=t.name,this.axis=t.axis,this.id=this.name+"-"+this.axis,this.waypoints=[],this.clearTriggerQueues(),o[this.axis][this.name]=this}var o={vertical:{},horizontal:{}},n=window.Waypoint;i.prototype.add=function(t){this.waypoints.push(t)},i.prototype.clearTriggerQueues=function(){this.triggerQueues={up:[],down:[],left:[],right:[]}},i.prototype.flushTriggers=function(){for(var i in this.triggerQueues){var o=this.triggerQueues[i],n="up"===i||"left"===i;o.sort(n?e:t);for(var r=0,s=o.length;s>r;r+=1){var a=o[r];(a.options.continuous||r===o.length-1)&&a.trigger([i])}}this.clearTriggerQueues()},i.prototype.next=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints),o=i===this.waypoints.length-1;return o?null:this.waypoints[i+1]},i.prototype.previous=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints);return i?this.waypoints[i-1]:null},i.prototype.queueTrigger=function(t,e){this.triggerQueues[e].push(t)},i.prototype.remove=function(t){var e=n.Adapter.inArray(t,this.waypoints);e>-1&&this.waypoints.splice(e,1)},i.prototype.first=function(){return this.waypoints[0]},i.prototype.last=function(){return this.waypoints[this.waypoints.length-1]},i.findOrCreate=function(t){return o[t.axis][t.name]||new i(t)},n.Group=i}(),function(){"use strict";function t(t){this.$element=e(t)}var e=window.jQuery,i=window.Waypoint;e.each(["innerHeight","innerWidth","off","offset","on","outerHeight","outerWidth","scrollLeft","scrollTop"],function(e,i){t.prototype[i]=function(){var t=Array.prototype.slice.call(arguments);return this.$element[i].apply(this.$element,t)}}),e.each(["extend","inArray","isEmptyObject"],function(i,o){t[o]=e[o]}),i.adapters.push({name:"jquery",Adapter:t}),i.Adapter=t}(),function(){"use strict";function t(t){return function(){var i=[],o=arguments[0];return t.isFunction(arguments[0])&&(o=t.extend({},arguments[1]),o.handler=arguments[0]),this.each(function(){var n=t.extend({},o,{element:this});"string"==typeof n.context&&(n.context=t(this).closest(n.context)[0]),i.push(new e(n))}),i}}var e=window.Waypoint;window.jQuery&&(window.jQuery.fn.waypoint=t(window.jQuery)),window.Zepto&&(window.Zepto.fn.waypoint=t(window.Zepto))}();

//visible
!function(t){var i=t(window);t.fn.visible=function(t,e,o){if(!(this.length<1)){var r=this.length>1?this.eq(0):this,n=r.get(0),f=i.width(),h=i.height(),o=o?o:"both",l=e===!0?n.offsetWidth*n.offsetHeight:!0;if("function"==typeof n.getBoundingClientRect){var g=n.getBoundingClientRect(),u=g.top>=0&&g.top<h,s=g.bottom>0&&g.bottom<=h,c=g.left>=0&&g.left<f,a=g.right>0&&g.right<=f,v=t?u||s:u&&s,b=t?c||a:c&&a;if("both"===o)return l&&v&&b;if("vertical"===o)return l&&v;if("horizontal"===o)return l&&b}else{var d=i.scrollTop(),p=d+h,w=i.scrollLeft(),m=w+f,y=r.offset(),z=y.top,B=z+r.height(),C=y.left,R=C+r.width(),j=t===!0?B:z,q=t===!0?z:B,H=t===!0?R:C,L=t===!0?C:R;if("both"===o)return!!l&&p>=q&&j>=d&&m>=L&&H>=w;if("vertical"===o)return!!l&&p>=q&&j>=d;if("horizontal"===o)return!!l&&m>=L&&H>=w}}}}(jQuery);


/*
* jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
*/
jQuery.easing["jswing"]=jQuery.easing["swing"];jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(a,b,c,d,e){return jQuery.easing[jQuery.easing.def](a,b,c,d,e)},easeInQuad:function(a,b,c,d,e){return d*(b/=e)*b+c},easeOutQuad:function(a,b,c,d,e){return-d*(b/=e)*(b-2)+c},easeInOutQuad:function(a,b,c,d,e){if((b/=e/2)<1)return d/2*b*b+c;return-d/2*(--b*(b-2)-1)+c},easeInCubic:function(a,b,c,d,e){return d*(b/=e)*b*b+c},easeOutCubic:function(a,b,c,d,e){return d*((b=b/e-1)*b*b+1)+c},easeInOutCubic:function(a,b,c,d,e){if((b/=e/2)<1)return d/2*b*b*b+c;return d/2*((b-=2)*b*b+2)+c},easeInQuart:function(a,b,c,d,e){return d*(b/=e)*b*b*b+c},easeOutQuart:function(a,b,c,d,e){return-d*((b=b/e-1)*b*b*b-1)+c},easeInOutQuart:function(a,b,c,d,e){if((b/=e/2)<1)return d/2*b*b*b*b+c;return-d/2*((b-=2)*b*b*b-2)+c},easeInQuint:function(a,b,c,d,e){return d*(b/=e)*b*b*b*b+c},easeOutQuint:function(a,b,c,d,e){return d*((b=b/e-1)*b*b*b*b+1)+c},easeInOutQuint:function(a,b,c,d,e){if((b/=e/2)<1)return d/2*b*b*b*b*b+c;return d/2*((b-=2)*b*b*b*b+2)+c},easeInSine:function(a,b,c,d,e){return-d*Math.cos(b/e*(Math.PI/2))+d+c},easeOutSine:function(a,b,c,d,e){return d*Math.sin(b/e*(Math.PI/2))+c},easeInOutSine:function(a,b,c,d,e){return-d/2*(Math.cos(Math.PI*b/e)-1)+c},easeInExpo:function(a,b,c,d,e){return b==0?c:d*Math.pow(2,10*(b/e-1))+c},easeOutExpo:function(a,b,c,d,e){return b==e?c+d:d*(-Math.pow(2,-10*b/e)+1)+c},easeInOutExpo:function(a,b,c,d,e){if(b==0)return c;if(b==e)return c+d;if((b/=e/2)<1)return d/2*Math.pow(2,10*(b-1))+c;return d/2*(-Math.pow(2,-10*--b)+2)+c},easeInCirc:function(a,b,c,d,e){return-d*(Math.sqrt(1-(b/=e)*b)-1)+c},easeOutCirc:function(a,b,c,d,e){return d*Math.sqrt(1-(b=b/e-1)*b)+c},easeInOutCirc:function(a,b,c,d,e){if((b/=e/2)<1)return-d/2*(Math.sqrt(1-b*b)-1)+c;return d/2*(Math.sqrt(1-(b-=2)*b)+1)+c},easeInElastic:function(a,b,c,d,e){var f=1.70158;var g=0;var h=d;if(b==0)return c;if((b/=e)==1)return c+d;if(!g)g=e*.3;if(h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return-(h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g))+c},easeOutElastic:function(a,b,c,d,e){var f=1.70158;var g=0;var h=d;if(b==0)return c;if((b/=e)==1)return c+d;if(!g)g=e*.3;if(h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);return h*Math.pow(2,-10*b)*Math.sin((b*e-f)*2*Math.PI/g)+d+c},easeInOutElastic:function(a,b,c,d,e){var f=1.70158;var g=0;var h=d;if(b==0)return c;if((b/=e/2)==2)return c+d;if(!g)g=e*.3*1.5;if(h<Math.abs(d)){h=d;var f=g/4}else var f=g/(2*Math.PI)*Math.asin(d/h);if(b<1)return-.5*h*Math.pow(2,10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)+c;return h*Math.pow(2,-10*(b-=1))*Math.sin((b*e-f)*2*Math.PI/g)*.5+d+c},easeInBack:function(a,b,c,d,e,f){if(f==undefined)f=1.70158;return d*(b/=e)*b*((f+1)*b-f)+c},easeOutBack:function(a,b,c,d,e,f){if(f==undefined)f=1.70158;return d*((b=b/e-1)*b*((f+1)*b+f)+1)+c},easeInOutBack:function(a,b,c,d,e,f){if(f==undefined)f=1.70158;if((b/=e/2)<1)return d/2*b*b*(((f*=1.525)+1)*b-f)+c;return d/2*((b-=2)*b*(((f*=1.525)+1)*b+f)+2)+c},easeInBounce:function(a,b,c,d,e){return d-jQuery.easing.easeOutBounce(a,e-b,0,d,e)+c},easeOutBounce:function(a,b,c,d,e){if((b/=e)<1/2.75){return d*7.5625*b*b+c}else if(b<2/2.75){return d*(7.5625*(b-=1.5/2.75)*b+.75)+c}else if(b<2.5/2.75){return d*(7.5625*(b-=2.25/2.75)*b+.9375)+c}else{return d*(7.5625*(b-=2.625/2.75)*b+.984375)+c}},easeInOutBounce:function(a,b,c,d,e){if(b<e/2)return jQuery.easing.easeInBounce(a,b*2,0,d,e)*.5+c;return jQuery.easing.easeOutBounce(a,b*2-e,0,d,e)*.5+d*.5+c}})


/*anime*/
/*
 2017 Julian Garnier
 Released under the MIT license
*/
var $jscomp={scope:{}};$jscomp.defineProperty="function"==typeof Object.defineProperties?Object.defineProperty:function(e,r,p){if(p.get||p.set)throw new TypeError("ES3 does not support getters and setters.");e!=Array.prototype&&e!=Object.prototype&&(e[r]=p.value)};$jscomp.getGlobal=function(e){return"undefined"!=typeof window&&window===e?e:"undefined"!=typeof global&&null!=global?global:e};$jscomp.global=$jscomp.getGlobal(this);$jscomp.SYMBOL_PREFIX="jscomp_symbol_";
$jscomp.initSymbol=function(){$jscomp.initSymbol=function(){};$jscomp.global.Symbol||($jscomp.global.Symbol=$jscomp.Symbol)};$jscomp.symbolCounter_=0;$jscomp.Symbol=function(e){return $jscomp.SYMBOL_PREFIX+(e||"")+$jscomp.symbolCounter_++};
$jscomp.initSymbolIterator=function(){$jscomp.initSymbol();var e=$jscomp.global.Symbol.iterator;e||(e=$jscomp.global.Symbol.iterator=$jscomp.global.Symbol("iterator"));"function"!=typeof Array.prototype[e]&&$jscomp.defineProperty(Array.prototype,e,{configurable:!0,writable:!0,value:function(){return $jscomp.arrayIterator(this)}});$jscomp.initSymbolIterator=function(){}};$jscomp.arrayIterator=function(e){var r=0;return $jscomp.iteratorPrototype(function(){return r<e.length?{done:!1,value:e[r++]}:{done:!0}})};
$jscomp.iteratorPrototype=function(e){$jscomp.initSymbolIterator();e={next:e};e[$jscomp.global.Symbol.iterator]=function(){return this};return e};$jscomp.array=$jscomp.array||{};$jscomp.iteratorFromArray=function(e,r){$jscomp.initSymbolIterator();e instanceof String&&(e+="");var p=0,m={next:function(){if(p<e.length){var u=p++;return{value:r(u,e[u]),done:!1}}m.next=function(){return{done:!0,value:void 0}};return m.next()}};m[Symbol.iterator]=function(){return m};return m};
$jscomp.polyfill=function(e,r,p,m){if(r){p=$jscomp.global;e=e.split(".");for(m=0;m<e.length-1;m++){var u=e[m];u in p||(p[u]={});p=p[u]}e=e[e.length-1];m=p[e];r=r(m);r!=m&&null!=r&&$jscomp.defineProperty(p,e,{configurable:!0,writable:!0,value:r})}};$jscomp.polyfill("Array.prototype.keys",function(e){return e?e:function(){return $jscomp.iteratorFromArray(this,function(e){return e})}},"es6-impl","es3");var $jscomp$this=this;
(function(e,r){"function"===typeof define&&define.amd?define([],r):"object"===typeof module&&module.exports?module.exports=r():e.anime=r()})(this,function(){function e(a){if(!h.col(a))try{return document.querySelectorAll(a)}catch(c){}}function r(a,c){for(var d=a.length,b=2<=arguments.length?arguments[1]:void 0,f=[],n=0;n<d;n++)if(n in a){var k=a[n];c.call(b,k,n,a)&&f.push(k)}return f}function p(a){return a.reduce(function(a,d){return a.concat(h.arr(d)?p(d):d)},[])}function m(a){if(h.arr(a))return a;
h.str(a)&&(a=e(a)||a);return a instanceof NodeList||a instanceof HTMLCollection?[].slice.call(a):[a]}function u(a,c){return a.some(function(a){return a===c})}function C(a){var c={},d;for(d in a)c[d]=a[d];return c}function D(a,c){var d=C(a),b;for(b in a)d[b]=c.hasOwnProperty(b)?c[b]:a[b];return d}function z(a,c){var d=C(a),b;for(b in c)d[b]=h.und(a[b])?c[b]:a[b];return d}function T(a){a=a.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i,function(a,c,d,k){return c+c+d+d+k+k});var c=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(a);
a=parseInt(c[1],16);var d=parseInt(c[2],16),c=parseInt(c[3],16);return"rgba("+a+","+d+","+c+",1)"}function U(a){function c(a,c,b){0>b&&(b+=1);1<b&&--b;return b<1/6?a+6*(c-a)*b:.5>b?c:b<2/3?a+(c-a)*(2/3-b)*6:a}var d=/hsl\((\d+),\s*([\d.]+)%,\s*([\d.]+)%\)/g.exec(a)||/hsla\((\d+),\s*([\d.]+)%,\s*([\d.]+)%,\s*([\d.]+)\)/g.exec(a);a=parseInt(d[1])/360;var b=parseInt(d[2])/100,f=parseInt(d[3])/100,d=d[4]||1;if(0==b)f=b=a=f;else{var n=.5>f?f*(1+b):f+b-f*b,k=2*f-n,f=c(k,n,a+1/3),b=c(k,n,a);a=c(k,n,a-1/3)}return"rgba("+
255*f+","+255*b+","+255*a+","+d+")"}function y(a){if(a=/([\+\-]?[0-9#\.]+)(%|px|pt|em|rem|in|cm|mm|ex|ch|pc|vw|vh|vmin|vmax|deg|rad|turn)?$/.exec(a))return a[2]}function V(a){if(-1<a.indexOf("translate")||"perspective"===a)return"px";if(-1<a.indexOf("rotate")||-1<a.indexOf("skew"))return"deg"}function I(a,c){return h.fnc(a)?a(c.target,c.id,c.total):a}function E(a,c){if(c in a.style)return getComputedStyle(a).getPropertyValue(c.replace(/([a-z])([A-Z])/g,"$1-$2").toLowerCase())||"0"}function J(a,c){if(h.dom(a)&&
u(W,c))return"transform";if(h.dom(a)&&(a.getAttribute(c)||h.svg(a)&&a[c]))return"attribute";if(h.dom(a)&&"transform"!==c&&E(a,c))return"css";if(null!=a[c])return"object"}function X(a,c){var d=V(c),d=-1<c.indexOf("scale")?1:0+d;a=a.style.transform;if(!a)return d;for(var b=[],f=[],n=[],k=/(\w+)\((.+?)\)/g;b=k.exec(a);)f.push(b[1]),n.push(b[2]);a=r(n,function(a,b){return f[b]===c});return a.length?a[0]:d}function K(a,c){switch(J(a,c)){case "transform":return X(a,c);case "css":return E(a,c);case "attribute":return a.getAttribute(c)}return a[c]||
0}function L(a,c){var d=/^(\*=|\+=|-=)/.exec(a);if(!d)return a;var b=y(a)||0;c=parseFloat(c);a=parseFloat(a.replace(d[0],""));switch(d[0][0]){case "+":return c+a+b;case "-":return c-a+b;case "*":return c*a+b}}function F(a,c){return Math.sqrt(Math.pow(c.x-a.x,2)+Math.pow(c.y-a.y,2))}function M(a){a=a.points;for(var c=0,d,b=0;b<a.numberOfItems;b++){var f=a.getItem(b);0<b&&(c+=F(d,f));d=f}return c}function N(a){if(a.getTotalLength)return a.getTotalLength();switch(a.tagName.toLowerCase()){case "circle":return 2*
Math.PI*a.getAttribute("r");case "rect":return 2*a.getAttribute("width")+2*a.getAttribute("height");case "line":return F({x:a.getAttribute("x1"),y:a.getAttribute("y1")},{x:a.getAttribute("x2"),y:a.getAttribute("y2")});case "polyline":return M(a);case "polygon":var c=a.points;return M(a)+F(c.getItem(c.numberOfItems-1),c.getItem(0))}}function Y(a,c){function d(b){b=void 0===b?0:b;return a.el.getPointAtLength(1<=c+b?c+b:0)}var b=d(),f=d(-1),n=d(1);switch(a.property){case "x":return b.x;case "y":return b.y;
case "angle":return 180*Math.atan2(n.y-f.y,n.x-f.x)/Math.PI}}function O(a,c){var d=/-?\d*\.?\d+/g,b;b=h.pth(a)?a.totalLength:a;if(h.col(b))if(h.rgb(b)){var f=/rgb\((\d+,\s*[\d]+,\s*[\d]+)\)/g.exec(b);b=f?"rgba("+f[1]+",1)":b}else b=h.hex(b)?T(b):h.hsl(b)?U(b):void 0;else f=(f=y(b))?b.substr(0,b.length-f.length):b,b=c&&!/\s/g.test(b)?f+c:f;b+="";return{original:b,numbers:b.match(d)?b.match(d).map(Number):[0],strings:h.str(a)||c?b.split(d):[]}}function P(a){a=a?p(h.arr(a)?a.map(m):m(a)):[];return r(a,
function(a,d,b){return b.indexOf(a)===d})}function Z(a){var c=P(a);return c.map(function(a,b){return{target:a,id:b,total:c.length}})}function aa(a,c){var d=C(c);if(h.arr(a)){var b=a.length;2!==b||h.obj(a[0])?h.fnc(c.duration)||(d.duration=c.duration/b):a={value:a}}return m(a).map(function(a,b){b=b?0:c.delay;a=h.obj(a)&&!h.pth(a)?a:{value:a};h.und(a.delay)&&(a.delay=b);return a}).map(function(a){return z(a,d)})}function ba(a,c){var d={},b;for(b in a){var f=I(a[b],c);h.arr(f)&&(f=f.map(function(a){return I(a,
c)}),1===f.length&&(f=f[0]));d[b]=f}d.duration=parseFloat(d.duration);d.delay=parseFloat(d.delay);return d}function ca(a){return h.arr(a)?A.apply(this,a):Q[a]}function da(a,c){var d;return a.tweens.map(function(b){b=ba(b,c);var f=b.value,e=K(c.target,a.name),k=d?d.to.original:e,k=h.arr(f)?f[0]:k,w=L(h.arr(f)?f[1]:f,k),e=y(w)||y(k)||y(e);b.from=O(k,e);b.to=O(w,e);b.start=d?d.end:a.offset;b.end=b.start+b.delay+b.duration;b.easing=ca(b.easing);b.elasticity=(1E3-Math.min(Math.max(b.elasticity,1),999))/
1E3;b.isPath=h.pth(f);b.isColor=h.col(b.from.original);b.isColor&&(b.round=1);return d=b})}function ea(a,c){return r(p(a.map(function(a){return c.map(function(b){var c=J(a.target,b.name);if(c){var d=da(b,a);b={type:c,property:b.name,animatable:a,tweens:d,duration:d[d.length-1].end,delay:d[0].delay}}else b=void 0;return b})})),function(a){return!h.und(a)})}function R(a,c,d,b){var f="delay"===a;return c.length?(f?Math.min:Math.max).apply(Math,c.map(function(b){return b[a]})):f?b.delay:d.offset+b.delay+
b.duration}function fa(a){var c=D(ga,a),d=D(S,a),b=Z(a.targets),f=[],e=z(c,d),k;for(k in a)e.hasOwnProperty(k)||"targets"===k||f.push({name:k,offset:e.offset,tweens:aa(a[k],d)});a=ea(b,f);return z(c,{children:[],animatables:b,animations:a,duration:R("duration",a,c,d),delay:R("delay",a,c,d)})}function q(a){function c(){return window.Promise&&new Promise(function(a){return p=a})}function d(a){return g.reversed?g.duration-a:a}function b(a){for(var b=0,c={},d=g.animations,f=d.length;b<f;){var e=d[b],
k=e.animatable,h=e.tweens,n=h.length-1,l=h[n];n&&(l=r(h,function(b){return a<b.end})[0]||l);for(var h=Math.min(Math.max(a-l.start-l.delay,0),l.duration)/l.duration,w=isNaN(h)?1:l.easing(h,l.elasticity),h=l.to.strings,p=l.round,n=[],m=void 0,m=l.to.numbers.length,t=0;t<m;t++){var x=void 0,x=l.to.numbers[t],q=l.from.numbers[t],x=l.isPath?Y(l.value,w*x):q+w*(x-q);p&&(l.isColor&&2<t||(x=Math.round(x*p)/p));n.push(x)}if(l=h.length)for(m=h[0],w=0;w<l;w++)p=h[w+1],t=n[w],isNaN(t)||(m=p?m+(t+p):m+(t+" "));
else m=n[0];ha[e.type](k.target,e.property,m,c,k.id);e.currentValue=m;b++}if(b=Object.keys(c).length)for(d=0;d<b;d++)H||(H=E(document.body,"transform")?"transform":"-webkit-transform"),g.animatables[d].target.style[H]=c[d].join(" ");g.currentTime=a;g.progress=a/g.duration*100}function f(a){if(g[a])g[a](g)}function e(){g.remaining&&!0!==g.remaining&&g.remaining--}function k(a){var k=g.duration,n=g.offset,w=n+g.delay,r=g.currentTime,x=g.reversed,q=d(a);if(g.children.length){var u=g.children,v=u.length;
if(q>=g.currentTime)for(var G=0;G<v;G++)u[G].seek(q);else for(;v--;)u[v].seek(q)}if(q>=w||!k)g.began||(g.began=!0,f("begin")),f("run");if(q>n&&q<k)b(q);else if(q<=n&&0!==r&&(b(0),x&&e()),q>=k&&r!==k||!k)b(k),x||e();f("update");a>=k&&(g.remaining?(t=h,"alternate"===g.direction&&(g.reversed=!g.reversed)):(g.pause(),g.completed||(g.completed=!0,f("complete"),"Promise"in window&&(p(),m=c()))),l=0)}a=void 0===a?{}:a;var h,t,l=0,p=null,m=c(),g=fa(a);g.reset=function(){var a=g.direction,c=g.loop;g.currentTime=
0;g.progress=0;g.paused=!0;g.began=!1;g.completed=!1;g.reversed="reverse"===a;g.remaining="alternate"===a&&1===c?2:c;b(0);for(a=g.children.length;a--;)g.children[a].reset()};g.tick=function(a){h=a;t||(t=h);k((l+h-t)*q.speed)};g.seek=function(a){k(d(a))};g.pause=function(){var a=v.indexOf(g);-1<a&&v.splice(a,1);g.paused=!0};g.play=function(){g.paused&&(g.paused=!1,t=0,l=d(g.currentTime),v.push(g),B||ia())};g.reverse=function(){g.reversed=!g.reversed;t=0;l=d(g.currentTime)};g.restart=function(){g.pause();
g.reset();g.play()};g.finished=m;g.reset();g.autoplay&&g.play();return g}var ga={update:void 0,begin:void 0,run:void 0,complete:void 0,loop:1,direction:"normal",autoplay:!0,offset:0},S={duration:1E3,delay:0,easing:"easeOutElastic",elasticity:500,round:0},W="translateX translateY translateZ rotate rotateX rotateY rotateZ scale scaleX scaleY scaleZ skewX skewY perspective".split(" "),H,h={arr:function(a){return Array.isArray(a)},obj:function(a){return-1<Object.prototype.toString.call(a).indexOf("Object")},
pth:function(a){return h.obj(a)&&a.hasOwnProperty("totalLength")},svg:function(a){return a instanceof SVGElement},dom:function(a){return a.nodeType||h.svg(a)},str:function(a){return"string"===typeof a},fnc:function(a){return"function"===typeof a},und:function(a){return"undefined"===typeof a},hex:function(a){return/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(a)},rgb:function(a){return/^rgb/.test(a)},hsl:function(a){return/^hsl/.test(a)},col:function(a){return h.hex(a)||h.rgb(a)||h.hsl(a)}},A=function(){function a(a,
d,b){return(((1-3*b+3*d)*a+(3*b-6*d))*a+3*d)*a}return function(c,d,b,f){if(0<=c&&1>=c&&0<=b&&1>=b){var e=new Float32Array(11);if(c!==d||b!==f)for(var k=0;11>k;++k)e[k]=a(.1*k,c,b);return function(k){if(c===d&&b===f)return k;if(0===k)return 0;if(1===k)return 1;for(var h=0,l=1;10!==l&&e[l]<=k;++l)h+=.1;--l;var l=h+(k-e[l])/(e[l+1]-e[l])*.1,n=3*(1-3*b+3*c)*l*l+2*(3*b-6*c)*l+3*c;if(.001<=n){for(h=0;4>h;++h){n=3*(1-3*b+3*c)*l*l+2*(3*b-6*c)*l+3*c;if(0===n)break;var m=a(l,c,b)-k,l=l-m/n}k=l}else if(0===
n)k=l;else{var l=h,h=h+.1,g=0;do m=l+(h-l)/2,n=a(m,c,b)-k,0<n?h=m:l=m;while(1e-7<Math.abs(n)&&10>++g);k=m}return a(k,d,f)}}}}(),Q=function(){function a(a,b){return 0===a||1===a?a:-Math.pow(2,10*(a-1))*Math.sin(2*(a-1-b/(2*Math.PI)*Math.asin(1))*Math.PI/b)}var c="Quad Cubic Quart Quint Sine Expo Circ Back Elastic".split(" "),d={In:[[.55,.085,.68,.53],[.55,.055,.675,.19],[.895,.03,.685,.22],[.755,.05,.855,.06],[.47,0,.745,.715],[.95,.05,.795,.035],[.6,.04,.98,.335],[.6,-.28,.735,.045],a],Out:[[.25,
.46,.45,.94],[.215,.61,.355,1],[.165,.84,.44,1],[.23,1,.32,1],[.39,.575,.565,1],[.19,1,.22,1],[.075,.82,.165,1],[.175,.885,.32,1.275],function(b,c){return 1-a(1-b,c)}],InOut:[[.455,.03,.515,.955],[.645,.045,.355,1],[.77,0,.175,1],[.86,0,.07,1],[.445,.05,.55,.95],[1,0,0,1],[.785,.135,.15,.86],[.68,-.55,.265,1.55],function(b,c){return.5>b?a(2*b,c)/2:1-a(-2*b+2,c)/2}]},b={linear:A(.25,.25,.75,.75)},f={},e;for(e in d)f.type=e,d[f.type].forEach(function(a){return function(d,f){b["ease"+a.type+c[f]]=h.fnc(d)?
d:A.apply($jscomp$this,d)}}(f)),f={type:f.type};return b}(),ha={css:function(a,c,d){return a.style[c]=d},attribute:function(a,c,d){return a.setAttribute(c,d)},object:function(a,c,d){return a[c]=d},transform:function(a,c,d,b,f){b[f]||(b[f]=[]);b[f].push(c+"("+d+")")}},v=[],B=0,ia=function(){function a(){B=requestAnimationFrame(c)}function c(c){var b=v.length;if(b){for(var d=0;d<b;)v[d]&&v[d].tick(c),d++;a()}else cancelAnimationFrame(B),B=0}return a}();q.version="2.2.0";q.speed=1;q.running=v;q.remove=
function(a){a=P(a);for(var c=v.length;c--;)for(var d=v[c],b=d.animations,f=b.length;f--;)u(a,b[f].animatable.target)&&(b.splice(f,1),b.length||d.pause())};q.getValue=K;q.path=function(a,c){var d=h.str(a)?e(a)[0]:a,b=c||100;return function(a){return{el:d,property:a,totalLength:N(d)*(b/100)}}};q.setDashoffset=function(a){var c=N(a);a.setAttribute("stroke-dasharray",c);return c};q.bezier=A;q.easings=Q;q.timeline=function(a){var c=q(a);c.pause();c.duration=0;c.add=function(d){c.children.forEach(function(a){a.began=
!0;a.completed=!0});m(d).forEach(function(b){var d=z(b,D(S,a||{}));d.targets=d.targets||a.targets;b=c.duration;var e=d.offset;d.autoplay=!1;d.direction=c.direction;d.offset=h.und(e)?b:L(e,b);c.began=!0;c.completed=!0;c.seek(d.offset);d=q(d);d.began=!0;d.completed=!0;d.duration>b&&(c.duration=d.duration);c.children.push(d)});c.seek(0);c.reset();c.autoplay&&c.restart();return c};return c};q.random=function(a,c){return Math.floor(Math.random()*(c-a+1))+a};return q});


/*! Mousewheel by Brandon Aaron (http://brandon.aaron.sh) */
!function(a){"function"==typeof define&&define.amd?define(["jquery"],a):"object"==typeof exports?module.exports=a:a(jQuery)}(function(a){function b(b){var g=b||window.event,h=i.call(arguments,1),j=0,l=0,m=0,n=0,o=0,p=0;if(b=a.event.fix(g),b.type="mousewheel","detail"in g&&(m=-1*g.detail),"wheelDelta"in g&&(m=g.wheelDelta),"wheelDeltaY"in g&&(m=g.wheelDeltaY),"wheelDeltaX"in g&&(l=-1*g.wheelDeltaX),"axis"in g&&g.axis===g.HORIZONTAL_AXIS&&(l=-1*m,m=0),j=0===m?l:m,"deltaY"in g&&(m=-1*g.deltaY,j=m),"deltaX"in g&&(l=g.deltaX,0===m&&(j=-1*l)),0!==m||0!==l){if(1===g.deltaMode){var q=a.data(this,"mousewheel-line-height");j*=q,m*=q,l*=q}else if(2===g.deltaMode){var r=a.data(this,"mousewheel-page-height");j*=r,m*=r,l*=r}if(n=Math.max(Math.abs(m),Math.abs(l)),(!f||f>n)&&(f=n,d(g,n)&&(f/=40)),d(g,n)&&(j/=40,l/=40,m/=40),j=Math[j>=1?"floor":"ceil"](j/f),l=Math[l>=1?"floor":"ceil"](l/f),m=Math[m>=1?"floor":"ceil"](m/f),k.settings.normalizeOffset&&this.getBoundingClientRect){var s=this.getBoundingClientRect();o=b.clientX-s.left,p=b.clientY-s.top}return b.deltaX=l,b.deltaY=m,b.deltaFactor=f,b.offsetX=o,b.offsetY=p,b.deltaMode=0,h.unshift(b,j,l,m),e&&clearTimeout(e),e=setTimeout(c,200),(a.event.dispatch||a.event.handle).apply(this,h)}}function c(){f=null}function d(a,b){return k.settings.adjustOldDeltas&&"mousewheel"===a.type&&b%120===0}var e,f,g=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],h="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(a.event.fixHooks)for(var j=g.length;j;)a.event.fixHooks[g[--j]]=a.event.mouseHooks;var k=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var c=h.length;c;)this.addEventListener(h[--c],b,!1);else this.onmousewheel=b;a.data(this,"mousewheel-line-height",k.getLineHeight(this)),a.data(this,"mousewheel-page-height",k.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var c=h.length;c;)this.removeEventListener(h[--c],b,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(b){var c=a(b),d=c["offsetParent"in a.fn?"offsetParent":"parent"]();return d.length||(d=a("body")),parseInt(d.css("fontSize"),10)||parseInt(c.css("fontSize"),10)||16},getPageHeight:function(b){return a(b).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};a.fn.extend({mousewheel:function(a){return a?this.bind("mousewheel",a):this.trigger("mousewheel")},unmousewheel:function(a){return this.unbind("mousewheel",a)}})});

(function($, window, document) {



jQuery(document).ready(function($){
	
	
	
  // ========================= smartresize ===============================

  /*
   * smartresize: debounced resize event for jQuery
   *
   * latest version and complete README available on Github:
   * https://github.com/louisremi/jquery.smartresize.js
   *
   * Copyright 2011 @louis_remi
   * Licensed under the MIT license.
   */

  var $event = $.event,
      dispatchMethod = $.event.handle ? 'handle' : 'dispatch',
      resizeTimeout;

  $event.special.smartresize = {
    setup: function() {
      $(this).on( "resize", $event.special.smartresize.handler );
    },
    teardown: function() {
      $(this).off( "resize", $event.special.smartresize.handler );
    },
    handler: function( event, execAsap ) {
      // Save the context
      var context = this,
          args = arguments;

      // set correct event type
      event.type = "smartresize";

      if ( resizeTimeout ) { clearTimeout( resizeTimeout ); }
      resizeTimeout = setTimeout(function() {
        $event[ dispatchMethod ].apply( context, args );
      }, execAsap === "execAsap"? 0 : 100 );
    }
  };

  $.fn.smartresize = function( fn ) {
    return fn ? this.on( "smartresize", fn ) : this.trigger( "smartresize", ["execAsap"] );
  };



	/*!
	 * jQuery Transit - CSS3 transitions and transformations
	 * (c) 2011-2012 Rico Sta. Cruz <rico@ricostacruz.com>
	 * MIT Licensed.
	 *
	 * http://ricostacruz.com/jquery.transit
	 * http://github.com/rstacruz/jquery.transit
	 */
	(function(k){k.transit={version:"0.9.9",propertyMap:{marginLeft:"margin",marginRight:"margin",marginBottom:"margin",marginTop:"margin",paddingLeft:"padding",paddingRight:"padding",paddingBottom:"padding",paddingTop:"padding"},enabled:true,useTransitionEnd:false};var d=document.createElement("div");var q={};function b(v){if(v in d.style){return v}var u=["Moz","Webkit","O","ms"];var r=v.charAt(0).toUpperCase()+v.substr(1);if(v in d.style){return v}for(var t=0;t<u.length;++t){var s=u[t]+r;if(s in d.style){return s}}}function e(){d.style[q.transform]="";d.style[q.transform]="rotateY(90deg)";return d.style[q.transform]!==""}var a=navigator.userAgent.toLowerCase().indexOf("chrome")>-1;q.transition=b("transition");q.transitionDelay=b("transitionDelay");q.transform=b("transform");q.transformOrigin=b("transformOrigin");q.transform3d=e();var i={transition:"transitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd",WebkitTransition:"webkitTransitionEnd",msTransition:"MSTransitionEnd"};var f=q.transitionEnd=i[q.transition]||null;for(var p in q){if(q.hasOwnProperty(p)&&typeof k.support[p]==="undefined"){k.support[p]=q[p]}}d=null;k.cssEase={_default:"ease","in":"ease-in",out:"ease-out","in-out":"ease-in-out",snap:"cubic-bezier(0,1,.5,1)",easeOutCubic:"cubic-bezier(.215,.61,.355,1)",easeInOutCubic:"cubic-bezier(.645,.045,.355,1)",easeInCirc:"cubic-bezier(.6,.04,.98,.335)",easeOutCirc:"cubic-bezier(.075,.82,.165,1)",easeInOutCirc:"cubic-bezier(.785,.135,.15,.86)",easeInExpo:"cubic-bezier(.95,.05,.795,.035)",easeOutExpo:"cubic-bezier(.19,1,.22,1)",easeInOutExpo:"cubic-bezier(1,0,0,1)",easeInQuad:"cubic-bezier(.55,.085,.68,.53)",easeOutQuad:"cubic-bezier(.25,.46,.45,.94)",easeInOutQuad:"cubic-bezier(.455,.03,.515,.955)",easeInQuart:"cubic-bezier(.895,.03,.685,.22)",easeOutQuart:"cubic-bezier(.165,.84,.44,1)",easeInOutQuart:"cubic-bezier(.77,0,.175,1)",easeInQuint:"cubic-bezier(.755,.05,.855,.06)",easeOutQuint:"cubic-bezier(.23,1,.32,1)",easeInOutQuint:"cubic-bezier(.86,0,.07,1)",easeInSine:"cubic-bezier(.47,0,.745,.715)",easeOutSine:"cubic-bezier(.39,.575,.565,1)",easeInOutSine:"cubic-bezier(.445,.05,.55,.95)",easeInBack:"cubic-bezier(.6,-.28,.735,.045)",easeOutBack:"cubic-bezier(.175, .885,.32,1.275)",easeInOutBack:"cubic-bezier(.68,-.55,.265,1.55)"};k.cssHooks["transit:transform"]={get:function(r){return k(r).data("transform")||new j()},set:function(s,r){var t=r;if(!(t instanceof j)){t=new j(t)}if(q.transform==="WebkitTransform"&&!a){s.style[q.transform]=t.toString(true)}else{s.style[q.transform]=t.toString()}k(s).data("transform",t)}};k.cssHooks.transform={set:k.cssHooks["transit:transform"].set};if(k.fn.jquery<"1.8"){k.cssHooks.transformOrigin={get:function(r){return r.style[q.transformOrigin]},set:function(r,s){r.style[q.transformOrigin]=s}};k.cssHooks.transition={get:function(r){return r.style[q.transition]},set:function(r,s){r.style[q.transition]=s}}}n("scale");n("translate");n("rotate");n("rotateX");n("rotateY");n("rotate3d");n("perspective");n("skewX");n("skewY");n("x",true);n("y",true);function j(r){if(typeof r==="string"){this.parse(r)}return this}j.prototype={setFromString:function(t,s){var r=(typeof s==="string")?s.split(","):(s.constructor===Array)?s:[s];r.unshift(t);j.prototype.set.apply(this,r)},set:function(s){var r=Array.prototype.slice.apply(arguments,[1]);if(this.setter[s]){this.setter[s].apply(this,r)}else{this[s]=r.join(",")}},get:function(r){if(this.getter[r]){return this.getter[r].apply(this)}else{return this[r]||0}},setter:{rotate:function(r){this.rotate=o(r,"deg")},rotateX:function(r){this.rotateX=o(r,"deg")},rotateY:function(r){this.rotateY=o(r,"deg")},scale:function(r,s){if(s===undefined){s=r}this.scale=r+","+s},skewX:function(r){this.skewX=o(r,"deg")},skewY:function(r){this.skewY=o(r,"deg")},perspective:function(r){this.perspective=o(r,"px")},x:function(r){this.set("translate",r,null)},y:function(r){this.set("translate",null,r)},translate:function(r,s){if(this._translateX===undefined){this._translateX=0}if(this._translateY===undefined){this._translateY=0}if(r!==null&&r!==undefined){this._translateX=o(r,"px")}if(s!==null&&s!==undefined){this._translateY=o(s,"px")}this.translate=this._translateX+","+this._translateY}},getter:{x:function(){return this._translateX||0},y:function(){return this._translateY||0},scale:function(){var r=(this.scale||"1,1").split(",");if(r[0]){r[0]=parseFloat(r[0])}if(r[1]){r[1]=parseFloat(r[1])}return(r[0]===r[1])?r[0]:r},rotate3d:function(){var t=(this.rotate3d||"0,0,0,0deg").split(",");for(var r=0;r<=3;++r){if(t[r]){t[r]=parseFloat(t[r])}}if(t[3]){t[3]=o(t[3],"deg")}return t}},parse:function(s){var r=this;s.replace(/([a-zA-Z0-9]+)\((.*?)\)/g,function(t,v,u){r.setFromString(v,u)})},toString:function(t){var s=[];for(var r in this){if(this.hasOwnProperty(r)){if((!q.transform3d)&&((r==="rotateX")||(r==="rotateY")||(r==="perspective")||(r==="transformOrigin"))){continue}if(r[0]!=="_"){if(t&&(r==="scale")){s.push(r+"3d("+this[r]+",1)")}else{if(t&&(r==="translate")){s.push(r+"3d("+this[r]+",0)")}else{s.push(r+"("+this[r]+")")}}}}}return s.join(" ")}};function m(s,r,t){if(r===true){s.queue(t)}else{if(r){s.queue(r,t)}else{t()}}}function h(s){var r=[];k.each(s,function(t){t=k.camelCase(t);t=k.transit.propertyMap[t]||k.cssProps[t]||t;t=c(t);if(k.inArray(t,r)===-1){r.push(t)}});return r}function g(s,v,x,r){var t=h(s);if(k.cssEase[x]){x=k.cssEase[x]}var w=""+l(v)+" "+x;if(parseInt(r,10)>0){w+=" "+l(r)}var u=[];k.each(t,function(z,y){u.push(y+" "+w)});return u.join(", ")}k.fn.transition=k.fn.transit=function(z,s,y,C){var D=this;var u=0;var w=true;if(typeof s==="function"){C=s;s=undefined}if(typeof y==="function"){C=y;y=undefined}if(typeof z.easing!=="undefined"){y=z.easing;delete z.easing}if(typeof z.duration!=="undefined"){s=z.duration;delete z.duration}if(typeof z.complete!=="undefined"){C=z.complete;delete z.complete}if(typeof z.queue!=="undefined"){w=z.queue;delete z.queue}if(typeof z.delay!=="undefined"){u=z.delay;delete z.delay}if(typeof s==="undefined"){s=k.fx.speeds._default}if(typeof y==="undefined"){y=k.cssEase._default}s=l(s);var E=g(z,s,y,u);var B=k.transit.enabled&&q.transition;var t=B?(parseInt(s,10)+parseInt(u,10)):0;if(t===0){var A=function(F){D.css(z);if(C){C.apply(D)}if(F){F()}};m(D,w,A);return D}var x={};var r=function(H){var G=false;var F=function(){if(G){D.unbind(f,F)}if(t>0){D.each(function(){this.style[q.transition]=(x[this]||null)})}if(typeof C==="function"){C.apply(D)}if(typeof H==="function"){H()}};if((t>0)&&(f)&&(k.transit.useTransitionEnd)){G=true;D.bind(f,F)}else{window.setTimeout(F,t)}D.each(function(){if(t>0){this.style[q.transition]=E}k(this).css(z)})};var v=function(F){this.offsetWidth;r(F)};m(D,w,v);return this};function n(s,r){if(!r){k.cssNumber[s]=true}k.transit.propertyMap[s]=q.transform;k.cssHooks[s]={get:function(v){var u=k(v).css("transit:transform");return u.get(s)},set:function(v,w){var u=k(v).css("transit:transform");u.setFromString(s,w);k(v).css({"transit:transform":u})}}}function c(r){return r.replace(/([A-Z])/g,function(s){return"-"+s.toLowerCase()})}function o(s,r){if((typeof s==="string")&&(!s.match(/^[\-0-9\.]+$/))){return s}else{return""+s+r}}function l(s){var r=s;if(k.fx.speeds[r]){r=k.fx.speeds[r]}return o(r,"ms")}k.transit.getTransitionValue=g})(jQuery);
	



	
/****************** Nectar ******************/	

// Create cross browser requestAnimationFrame method:
window.requestAnimationFrame = window.requestAnimationFrame
|| window.mozRequestAnimationFrame
|| window.webkitRequestAnimationFrame
|| window.msRequestAnimationFrame
|| function(f){setTimeout(f, 1000/60)}
	 
var nectarDOMInfo = {
	
	usingMobileBrowser: (navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) ? true : false,
	
	usingFrontEndEditor: (typeof window.vc_iframe === 'undefined') ? false : true,
	
	getWindowSize: function() {
		nectarDOMInfo.windowHeight = window.innerHeight;
		nectarDOMInfo.windowWidth = window.innerWidth;
		
		nectarDOMInfo.adminBarHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0;
		nectarDOMInfo.secondaryHeaderHeight = ($('#header-secondary-outer').length > 0) ? $('#header-secondary-outer').height() : 0;
	},

	scrollPosMouse: function() {
		return $(window).scrollTop();
	},
	
	scrollPosRAF: function() {
		nectarDOMInfo.scrollTop = $(window).scrollTop();
		requestAnimationFrame(nectarDOMInfo.scrollPosRAF);
	},
	

	bindEvents: function() {
		
		if(!nectarDOMInfo.usingMobileBrowser) {
			$(window).on('scroll',function(){ nectarDOMInfo.scrollTop = nectarDOMInfo.scrollPosMouse(); });
		}
		
		$(window).on('resize',nectarDOMInfo.getWindowSize);
		
	}

}

nectarDOMInfo.getWindowSize();
nectarDOMInfo.scrollTop = nectarDOMInfo.scrollPosMouse();

if(nectarDOMInfo.usingMobileBrowser) { requestAnimationFrame(nectarDOMInfo.scrollPosRAF); } 

nectarDOMInfo.bindEvents();


//set row sizes
fullWidthSections();
fwsClasses();








/***************** VC FRONTEND  ******************/

function addRowCtrls() {


		//remove padding for rows with only fullwidth ns
		$('.wpb_row').removeClass('only-ns');
		$('.nectar-slider-wrap[data-full-width="true"], .page-submenu, .portfolio-items[data-col-num="elastic"]:not(.fullwidth-constrained), .blog-fullwidth-wrap').parents('.wpb_row').addClass('only-ns');
		
		//padding when column contains item that could get lost under column controls 
		$('body.vc_editor.compose-mode .wpb_row .vc_vc_column > .wpb_column > .vc_column-inner').each(function(){
			
			if($(this).find('> .vc_element-container > div').length > 0) {
				
				if( $(this).find('> .vc_element-container > div:first-child').is('.vc_vc_row_inner') ) {
					$(this).find('> .vc_element-container > div:first-child').addClass('first-inner-row-el');
				} else {
					$(this).find('> .vc_element-container > div:first-child').removeClass('first-inner-row-el');
				}
				
			}
			
		}); 
		
	} //addRowCtrl end
	
	
	function convertFrontEndPadding() {
		 $('.vc_element > .wpb_column[class*="padding-"][class*="-percent"]').each(function(){
			 
				var $elPaddingPercent = 4;

				var elclassName = this.className.match(/padding-\d+/); 
				if (elclassName) {
						$elPaddingPercent = elclassName[0].match(/\d+/); 
						if($elPaddingPercent) {
							$elPaddingPercent = $elPaddingPercent[0]/100;
						} else {
							$elPaddingPercent = 0;
						}
				}
				
				if($elPaddingPercent) {
					 var $parentRowWidth = $(this).parents('.span_12').width();
					 
					 if($(this).is('[data-padding-pos="all"]')){
							$(this).css('padding', $parentRowWidth * $elPaddingPercent );
					 } else if($(this).is('[data-padding-pos="top"]')) {
						 $(this).css('padding-top', $parentRowWidth * $elPaddingPercent );
					 } else if($(this).is('[data-padding-pos="bottom"]')) {
						 $(this).css('padding-bottom', $parentRowWidth * $elPaddingPercent );
					 } else if($(this).is('[data-padding-pos="left"]')) {
						 $(this).css('padding-left', $parentRowWidth * $elPaddingPercent );
					 } else if($(this).is('[data-padding-pos="right"]')) {
						 $(this).css('padding-right', $parentRowWidth * $elPaddingPercent );
					 } else if($(this).is('[data-padding-pos="top-bottom"]')) {
						 $(this).css({
							 'padding-top': $parentRowWidth * $elPaddingPercent,
							 'padding-bottom': $parentRowWidth * $elPaddingPercent,
						 });
					 } else if($(this).is('[data-padding-pos="top-right"]')) {
						 $(this).css({
							 'padding-top': $parentRowWidth * $elPaddingPercent,
							 'padding-right': $parentRowWidth * $elPaddingPercent,
						 });
					 } else if($(this).is('[data-padding-pos="bottom-right"]')) {
						 $(this).css({
							 'padding-right': $parentRowWidth * $elPaddingPercent,
							 'padding-bottom': $parentRowWidth * $elPaddingPercent,
						 });
					 } else if($(this).is('[data-padding-pos="bottom-left"]')) {
						 $(this).css({
							 'padding-left': $parentRowWidth * $elPaddingPercent,
							 'padding-bottom': $parentRowWidth * $elPaddingPercent,
						 });
					 } else if($(this).is('[data-padding-pos="left-right"]')) {
						 $(this).css({
							 'padding-left': $parentRowWidth * $elPaddingPercent,
							 'padding-right': $parentRowWidth * $elPaddingPercent,
						 });
					 }
			 }

		 }); //each
		 
		 //remove margins on element wrappers that are setting custom margin
		 $('.wpb_row[class*="vc_custom_"]').each(function(){
			 
				$(this).parent().addClass('no-bottom-margin');
		
		 });
		 
	} //convertFrontEndPadding end
 
 
 setTimeout(function(){
	 
	if($('body.compose-mode').length > 0) {
		$('.container-wrap').addClass('visible-editor-controls');
  }
	
	if(nectarDOMInfo.usingFrontEndEditor) {
		addRowCtrls();
		convertFrontEndPadding();
		
		$(window).smartresize(convertFrontEndPadding);
	}

},200);
 
 

 
 var $fp_section_length = 0, $fp_section_prev_length = 0;
 
 
 $(window).on( 'vc_reload', function() {
	 
	 addRowCtrls();
	 
	 columnBGColors();
	 coloredButtons();
	 twentytwentyInit();
	 parallaxRowsBGCals();
	 
	 if($('.carousel').length > 0) {
		standardCarouselInit();
		clientsCarouselInit();
		carouselHeightCalcs();
	}
	
	
	if($('.owl-carousel').length > 0) { 
		$('.owl-carousel').each(function(){
			$(this).trigger('destroy.owl.carousel').removeClass('owl-loaded');
			$(this).find('.owl-stage-outer .owl-stage > *').unwrap();
			$(this).find('.owl-stage-outer > *').unwrap();
			$(this).find('.owl-item > *').unwrap();
			$(this).find('.owl-dots').remove();
			$(this).find('.owl-nav').remove();
		});
		owlCarouselInit();
	}
	
	flexsliderInit();
	accordionInit();
	ulChecks();
	oneFourthClasses();
	carouselfGrabbingClass();
	if($('.nectar_cascading_images').length > 0) {
		imagesLoaded($('.nectar_cascading_images'),function(instance){
			cascadingImageBGSizing();
		});
	}
	fullWidthSections();
	fwsClasses();
	fullwidthImgOnlySizingInit();
	fullwidthImgOnlySizing();
	fullWidthRowPaddingAdjustInit();
	recentPostsFlickityInit();
	if(nectarDOMInfo.usingMobileBrowser) {
		fullWidthRowPaddingAdjustCalc();
	}
	if($('.nectar-box-roll').length == 0) { boxRollInit(); }
	
  /*flickity carousels*/
	if($flickitySliders.length > 0) {
		for(var i=0; i<$flickitySliders.length; i++) {
			
			$flickitySliders[i].reloadCells();
			$flickitySliders[i].off( 'scroll.flickity'); 
			$flickitySliders[i].off( 'dragStart.flickity'); 
			$flickitySliders[i].off( 'dragEnd.flickity'); 

		 }
	}

	setTimeout(function(){ 
		
		flickityInit();
		if($flickitySliders.length > 0) { 
			for(var i=0; i<$flickitySliders.length; i++) {
				$flickitySliders[i].reloadCells();
				$flickitySliders[i].resize();
			 }
		}
	
	},100);
	
	/*flickity product carousels*/
	if($wooFlickityCarousels.length > 0) {
		for(var i=0; i<$wooFlickityCarousels.length; i++) {
			
			$wooFlickityCarousels[i].flickity('reloadCells');
			$wooFlickityCarousels[i].off( 'scroll.flickity'); 
			$wooFlickityCarousels[i].off( 'dragStart.flickity'); 
			$wooFlickityCarousels[i].off( 'dragEnd.flickity'); 

		 }
	}

	setTimeout(function(){ 
		
		if( $('.nectar-woo-flickity').length > 0) { productCarouselInit(); }
		if($wooFlickityCarousels.length > 0) { 
			for(var i=0; i<$wooFlickityCarousels.length; i++) {
				$wooFlickityCarousels[i].flickity('reloadCells');
				$wooFlickityCarousels[i].flickity('resize');
			 }
		}
	
	},100);
	
	
	socialSharingInit();
	hotSpotHoverBind();
	pricingTableHeight();
	nectarIconMatchColoring();
	
	if($testimonialSlider.length > 0) {
		for(var i=0; i<$testimonialSlider.length; i++) {
			var testimonialflkty = $testimonialSlider[i].data('flickity');
			$testimonialSlider[i].flickity('reloadCells');
			$testimonialSlider[i].off( 'select.flickity');
		}
	} 

	createTestimonialControls();
	
	lightBoxInit();
	
	imageWithHotspotClickEvents();
	testimonialSliderHeight(); 
	largeIconHover();
	if($('.nectar-box-roll').length == 0) boxRollMouseWheelInit();
	midnightInit();
	responsiveVideoIframesInit();
	responsiveVideoIframes();
	fullWidthContentColumns();
	setTimeout(fullWidthContentColumns,1000);
	videoBGInit();
	$(window).off('scroll.parallaxSections').off('resize.parallaxSections');
	parallaxScrollInit();
	
	//update blog arr
	$('.posts-container').each(function(i){
		$blog_containers[i] = $(this);
	});
	masonryBlogInit();
	masonryPortfolioInit();
	portfolioAccentColor();
	portfolioHoverEffects();
	portfolioFiltersInit();
	style6Img();
	
	fsProjectSliderInit();
	
	setTimeout(function(){
		if($('.nectar_fullscreen_zoom_recent_projects').length > 0){
			portfolioFullScreenSliderCalcs();
			splitLineText();
			$(window).resize(splitLineText);
			$(window).resize(portfolioFullScreenSliderCalcs);
		}
	},300);
	
	isotopeCatSelection();
	$(window).unbind('.infscr');
	infiniteScrollInit();
	postNextButtonEffect();
	mouseParallaxInit();
	
	//play video BGs
	$('.nectar-video-wrap').each(function(i){
		if($(this).find('video').length > 0) {
			  $(this).find('video').css('visibility','visible');
		}
	});
	
	//tablet inherits small desktop
	$('.wpb_column[data-t-w-inherits]').each(function(){
		if( $(this).is('[data-t-w-inherits="small_desktop"]') ) {
			$(this).parent().addClass('inherits-s-desktop-col');
		} else {
			$(this).parent().removeClass('inherits-s-desktop-col');
		}
	});
	
	//full screen page rows
	if($('#nectar_fullscreen_rows').length > 0) {
		
		$fp_section_length = $('#nectar_fullscreen_rows > .vc_element').length;
		
		
		if( $('#nectar_fullscreen_rows > .vc_element').length == 0 ) {
			$('#nectar_fullscreen_rows').prepend('<div class="vc_element empty_placeholder" />');
		} 
		
		
		if( $('#nectar_fullscreen_rows > .vc_element:not(.empty_placeholder)').length > 0 ) {
			$('#nectar_fullscreen_rows >.vc_element.empty_placeholder').remove();
		}
		
		setFPNames();

		
		//first load	
		if($('#fp-nav').length == 0) {
			initNectarFP();
			
			var currentFPSectionIndex = ($('#nectar_fullscreen_rows > .vc_element.active').length > 0) ? $('#nectar_fullscreen_rows > .vc_element.active').index() + 1 : 1;
			setFPNavColoring(currentFPSectionIndex,'na');
		} 
		//after
		////reinit if sections have been added/removed
		else if($fp_section_length != $fp_section_prev_length) {

			$.fn.fullpage.destroy('all');
			initNectarFP();

			var currentFPSectionIndex = ($('#nectar_fullscreen_rows > .vc_element.active').length > 0) ? $('#nectar_fullscreen_rows > .vc_element.active').index() + 1 : 1;
			setFPNavColoring(currentFPSectionIndex,'na');

		}
		

		//keep tooltip active status
		var $nectarFPindex = 0;
		var nectarFPOffsets = [{el: '', offset: 0}]; 
		
		////create list of offsets
		$('#nectar_fullscreen_rows > div.vc_element').each(function(i){
			nectarFPOffsets[i] = {
				el: $(this),
				offset: $(this).offset().top
			}
		});
		
		////loop to find lowest
		for(var x in nectarFPOffsets) {
			
			if(nectarFPOffsets[x].offset < $(window).height()){
				$nectarFPindex = x;
			} 
			
		} 
		////start active on that index
		//$('#fp-nav ul > li:nth-child(' + (parseInt($nectarFPindex)+1) + ')').find('> a').trigger('click');
		
		//move back to first section when all have been deleted but one
		if($('#nectar_fullscreen_rows > div.vc_element').length === 1) {
			$('#nectar_fullscreen_rows').css({'transform' : 'translate3d(0,0,0)'});
		}
		
		$('body').scrollTo(0,0);
			
		//update prev section length
		$fp_section_prev_length = $fp_section_length;
		
	} else {
		
		//remove container wrap padding/margin when needded
		if($('body .main-content > .row > .vc_element:first > .wpb_row[class*="full-width-"]').length > 0 || $('body .main-content > .row > .vc_element:first .nectar-slider-wrap[data-full-width="true"]').length > 0 ) {
				$('.container-wrap').css({ 'padding-top': '0', 'margin-top' : '0'});
		} else {
			$('.container-wrap').css({ 'padding-top': '40px'});
		}
		
	}
	
	
	//reset svg icon arr
	$svg_icons = [];
	$('.svg-icon-holder').removeClass('animated-in').removeClass('bound');

	
	//gmap
	if($('.vc_nectar_gmap').length > 0) {
		
		setTimeout(function(){
			
			if(typeof google === 'object' && typeof google.maps === 'object') {
				mapAPI_Loaded();
			} else {

				if(nectarLove.mapApiKey.length > 0) {
					$.getScript('https://maps.google.com/maps/api/js?sensor=false&key='+nectarLove.mapApiKey+'&callback=mapAPI_Loaded');
				}
			}
			
		},100);
}
	
	if( $().theiaStickySidebar ) {
		blogStickySS();
	}
	
	
	 if(typeof window.Waypoint != 'undefined') {
		 Waypoint.destroyAll();
		 waypoints();
	 }
	 
 });





/***************** Lightbox Init ******************/
	
	function fancyBoxInit() {
		//convert old pp links
		$('a.pp').removeClass('pp').attr('data-fancybox','');
		$("a[rel^='prettyPhoto']:not([rel*='_gal']):not([rel*='product-gallery']):not([rel*='prettyPhoto['])").removeAttr('rel').attr('data-fancybox','');
		
		//image gallery nectar
		$('.wpb_gallery .wpb_gallery_slidesnectarslider_style').each(function(){
			var $unique_id = Math.floor(Math.random()*10000);
			$(this).find('.swiper-slide a:not(.ext-url-link)').attr('data-fancybox','group_'+$unique_id);
		});
		
		//flex
		$('.wpb_gallery_slides.wpb_flexslider').each(function(){
			var $unique_id = Math.floor(Math.random()*10000);
			$(this).find('.slides > li > a').attr('data-fancybox','group_'+$unique_id);
		});
		
		//touch enabled gal
		$('.wpb_gallery_slidesflickity_style').each(function(){
			var $unique_id = Math.floor(Math.random()*10000);
			$(this).find('.cell > a:not(.ext-url-link)').attr('data-fancybox','group_'+$unique_id);
		});
		
		//add galleries to portfolios
		$('.portfolio-items, .wpb_gallery .parallax-grid-item').each(function(){
			var $unique_id = Math.floor(Math.random()*10000);
			if($(this).find('.pretty_photo').length > 0) {
				$(this).find('.pretty_photo').removeClass('pretty_photo').attr('data-fancybox','group_'+$unique_id);
			} else if($(this).find('a[rel*="prettyPhoto["]').length > 0){
				$(this).find('a[rel*="prettyPhoto["]').removeAttr('rel').attr('data-fancybox','group_'+$unique_id);
			}

		});
		
		//nectar auto lightbox
		if($('body').hasClass('nectar-auto-lightbox')){
			$('.gallery').each(function(){
				if($(this).find('.gallery-icon a[rel^="prettyPhoto"]').length == 0) {
					var $unique_id = Math.floor(Math.random()*10000);
					$(this).find('.gallery-item .gallery-icon a[href*=".jpg"], .gallery-item .gallery-icon a[href*=".png"], .gallery-item .gallery-icon a[href*=".gif"], .gallery-item .gallery-icon a[href*=".jpeg"]').attr('data-fancybox','group_'+$unique_id).removeClass('pretty_photo');
				}
			});
			$('.main-content img').each(function(){
				if($(this).parent().is("[href]") && !$(this).parent().is(".magnific-popup") && $(this).parents('.tiled-gallery').length == 0 && $(this).parents('.product-image').length == 0 && $(this).parents('.iosSlider.product-slider').length == 0) {
					var match = $(this).parent().attr('href').match(/\.(jpg|png|gif)\b/);
					if(match) $(this).parent().attr('data-fancybox','');
				} 
			});
		}
		
		//regular
		fbMarginArr = ($('body.admin-bar').length > 0) ? [60,100] : [60,100];
		if(window.innerWidth < 1000) {
			fbMarginArr = [0,0];
		}
		$("[data-fancybox]").fancybox({
			animationEffect : "zoom-in-out",
			animationDuration : 350,
			buttons : [
        //'slideShow',
        'fullScreen',
        //'thumbs',
        //'share',
        //'download',
        'zoom',
        'close'
    	],
			margin : fbMarginArr,
			loop     : true,
			caption : function( instance, item ) {
				return $(this).attr('title');
			},
			beforeLoad: function(instance,current) {
				//when there's nothing to load
				if(typeof instance.current.src !== 'string') {  $.fancybox.close(true);  }
			},
			mobile : {
				margin   : 0
			}
		});
	}
	
	
	function magnificInit() {
		
		//convert old pp links
		$('a.pp').removeClass('pp').addClass('magnific-popup');
		$("a[rel^='prettyPhoto']:not([rel*='_gal']):not([rel*='product-gallery']):not([rel*='prettyPhoto['])").removeAttr('rel').addClass('magnific-popup');


		//image gallery nectar
		$('.wpb_gallery .wpb_gallery_slidesnectarslider_style').each(function(){
			var $unique_id = Math.floor(Math.random()*10000);
			$(this).find('.swiper-slide a:not(.ext-url-link)').addClass('pretty_photo');
		});

		//flex
		$('.wpb_gallery_slides.wpb_flexslider').each(function(){
			var $unique_id = Math.floor(Math.random()*10000);
			$(this).find('.slides > li > a').addClass('pretty_photo');
		});
		//touch enabled gal
		$('.wpb_gallery_slidesflickity_style').each(function(){
			var $unique_id = Math.floor(Math.random()*10000);
			$(this).find('.cell > a:not(.ext-url-link)').addClass('pretty_photo');
		});

		//add galleries to portfolios
		$('.portfolio-items, .wpb_gallery .swiper-slide, .wpb_gallery_slidesflickity_style .cell, .wpb_gallery_slides.wpb_flexslider ul > li,  .wpb_gallery .parallax-grid-item').each(function(){
			if($(this).find('.pretty_photo').length > 0) {
				$(this).find('.pretty_photo').removeClass('pretty_photo').addClass('gallery').addClass('magnific');
			} else if($(this).find('a[rel*="prettyPhoto["]').length > 0){
				$(this).find('a[rel*="prettyPhoto["]').removeAttr('rel').addClass('gallery').addClass('magnific');
			}

		});
		
		$("a[data-rel='prettyPhoto[product-gallery]']").each(function(){
			$(this).removeAttr('data-rel').addClass('magnific').addClass('gallery');
		});
		
		//nectar auto lightbox
		if($('body').hasClass('nectar-auto-lightbox')){
			$('.gallery').each(function(){
				if($(this).find('.gallery-icon a[rel^="prettyPhoto"]').length == 0) {
					var $unique_id = Math.floor(Math.random()*10000);
					$(this).find('.gallery-item .gallery-icon a[href*=".jpg"], .gallery-item .gallery-icon a[href*=".png"], .gallery-item .gallery-icon a[href*=".gif"], .gallery-item .gallery-icon a[href*=".jpeg"]').addClass('magnific').addClass('gallery').removeClass('pretty_photo');
				}
			});
			$('.main-content img').each(function(){
				if($(this).parent().is("[href]") && !$(this).parent().is(".magnific-popup") && $(this).parents('.tiled-gallery').length == 0 && $(this).parents('.product-image').length == 0 && $(this).parents('.iosSlider.product-slider').length == 0) {
					var match = $(this).parent().attr('href').match(/\.(jpg|png|gif)\b/);
					if(match) $(this).parent().addClass('magnific-popup').addClass('image-link');
				} 
			});
		}
		

		//regular
		$('a.magnific-popup:not(.gallery):not(.nectar_video_lightbox)').magnificPopup({ 
			type: 'image', 
			callbacks: {
				
				imageLoadComplete: function()  {	
					var $that = this;
					setTimeout( function() { $that.wrap.addClass('mfp-image-loaded'); }, 10);
				},
				beforeOpen: function() {
				    this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
			    },
			    open: function() {
					    	
					$.magnificPopup.instance.next = function() {
						var $that = this;

						this.wrap.removeClass('mfp-image-loaded');
						setTimeout( function() { $.magnificPopup.proto.next.call($that); }, 100);
					}

					$.magnificPopup.instance.prev = function() {
						var $that = this;

						this.wrap.removeClass('mfp-image-loaded');
						setTimeout( function() { $.magnificPopup.proto.prev.call($that); }, 100);
					}
					
				}
			},
			fixedContentPos: false,
		    mainClass: 'mfp-zoom-in', 
		    removalDelay: 400 
		});

		//video
		$('a.magnific-popup.nectar_video_lightbox, .magnific_nectar_video_lightbox a.link_text, .swiper-slide a[href*=youtube], .swiper-slide a[href*=vimeo], .nectar-video-box a.full-link.magnific-popup').magnificPopup({ 
			type: 'iframe', 
			fixedContentPos: false,
		    mainClass: 'mfp-zoom-in', 
		    removalDelay: 400 
		});


		//galleries
		$('a.magnific.gallery').each(function(){

			var $parentRow = ($(this).closest('.wpb_column').length > 0) ? $(this).closest('.wpb_column') : $(this).parents('.row');
			if($parentRow.length > 0 && !$parentRow.hasClass('lightbox-col')) {

				$parentRow.magnificPopup({
					type: 'image',
					delegate: 'a.magnific',
					mainClass: 'mfp-zoom-in',
					fixedContentPos: false,
					callbacks: {

						elementParse: function(item) {
			
							 if($(item.el.context).is('[href]') && $(item.el.context).attr('href').indexOf('iframe=true') != -1 || $(item.el.context).is('[href]') && $(item.el.context).attr('href').indexOf('https://www.youtube.com/watch') != -1 ) {
						         item.type = 'iframe';
						      } else if ($(item.el.context).is('[href]') && $(item.el.context).attr('href').indexOf('video-popup-') != -1) {
						      	item.type = 'inline';
						      } else {
						         item.type = 'image';
						      }
						},

						imageLoadComplete: function()  {	
							var $that = this;
							setTimeout( function() { $that.wrap.addClass('mfp-image-loaded'); }, 10);
						},

						beforeOpen: function() {
					       this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
					    },

					    open: function() {
					    	

					    	//self hosted video
					    	if($(this.content).find('.mejs-video video').length > 0 && $().mediaelementplayer ) {
	            				$(this.content).find('.mejs-video video')[0].player.remove();
	            				
	            				var $that = this;
	            				setTimeout(function(){
	            					$($that.content).find('video').mediaelementplayer();
	            					$($that.content).find('.mejs-video video')[0].player.play();
	            				},50);
	            				
	            			}

							$.magnificPopup.instance.next = function() {
								var $that = this;

								this.wrap.removeClass('mfp-image-loaded');
								setTimeout( function() { 
									$.magnificPopup.proto.next.call($that); 

									//self hosted video
									if($($that.content).find('.mejs-video video').length > 0) {
			            				$($that.content).find('.mejs-video video')[0].play();
			            			}

								}, 100);
							}

							$.magnificPopup.instance.prev = function() {
								var $that = this;

								this.wrap.removeClass('mfp-image-loaded');
								setTimeout( function() { 
									$.magnificPopup.proto.prev.call($that); 

									//self hosted video
									if($($that.content).find('.mejs-video video').length > 0) {
			            				$($that.content).find('.mejs-video video')[0].play();
			            			}
								}, 100);
							}
							
						},

						close: function() {
							//self hosted video
					    	if($(this.content).find('.mejs-video video').length > 0) {
	            				 $(this.content).find('.mejs-video video')[0].load();
	            			}
						}
					},
					removalDelay: 400, 
					gallery: {
			          enabled:true
			        }
				});

				$parentRow.addClass('lightbox-col');
			}
			
		});

	}

	function lightBoxInit() {
		if($('body[data-ls="magnific"]').length > 0 || $('body[data-ls="pretty_photo"]').length > 0 ) {
			magnificInit();
		} else if($('body[data-ls="fancybox"]').length > 0) {
			fancyBoxInit();
		}
	}

	lightBoxInit();
	//check for late links
	setTimeout(lightBoxInit,500);
	



/*nectar liquid*/

function NectarLiquid(bgIMG, type, el_type) {
	this.canvasContainer = bgIMG[0];
	this.rowBG = bgIMG;
	this.animationType = type;
	this.elType = el_type;
	
	this.bgDivWidth = $(this.rowBG).width();
	this.bgDivHeight = $(this.rowBG).height();
	this.bgDivRatio = this.bgDivHeight / this.bgDivWidth;
	
	
	this.app = new PIXI.Application({
		width: this.bgDivWidth, 
		height: this.bgDivHeight,
		transparent: true,
		sharedTicker: true
	});
	
	this.app.stage = new window.PIXI.Container();
	this.imgContainer = new window.PIXI.Container();
	

	//grab displacement filter from css
	$(this.canvasContainer).remove('.nectar-displacement');
	$(this.canvasContainer).append('<div class="nectar-displacement"></div>');
	this.displacementIMG_URL = $(this.canvasContainer).find('.nectar-displacement').css('background-image');			
	this.displacementIMG_URL = this.displacementIMG_URL.replace(/(url\(|\)|")/g, '');
				
	//get row BG img
	this.bgIMG_SRC = bgIMG.css('background-image');
	this.bgIMG_SRC = this.bgIMG_SRC.replace(/(url\(|\)|")/g, '');
	
	
	//init everything once loaded
	this.loader = new PIXI.loaders.Loader();
	this.loader.add('rowBG',this.bgIMG_SRC);
	this.loader.add('displaceBG', this.displacementIMG_URL);
	this.loader.load(this.initialize.bind(this));

}

NectarLiquid.prototype.initialize = function() {
	
  this.settings = {
      animationStrength: 1,
			animationStrengthSpeed: 1,
		  displacementScaleX: 30,
			displacementScaleY: 90,
      time: Math.random()*20,
			filterMultX: 200,
			filterMultY: 350,
			shouldAnimate: true
  };
	if(this.animationType == 'displace-filter-fade') {
		
		if(this.elType == 'row') {
			this.settings.filterMultX = 150;
			this.settings.filterMultY = 375;
			this.settings.displacementScaleX = 80;
			this.settings.displacementScaleY = 170;
			this.settings.animationStrengthSpeed = 2;
		} else {
			this.settings.filterMultX = 55;
			this.settings.filterMultY = 150;
			this.settings.displacementScaleX = 50;
			this.settings.displacementScaleY = 160;
			this.settings.animationStrengthSpeed = 1;
		}
	} else {
		
		if(this.elType != 'row') {
			this.settings.displacementScaleY = 70;
		}
		
	}
	

	//displacement
	this.filterSprite = new window.PIXI.Sprite( this.loader.resources.displaceBG.texture );
	this.filterSprite.texture.baseTexture.wrapMode = PIXI.WRAP_MODES.REPEAT;
	this.filter = new window.PIXI.filters.DisplacementFilter( this.filterSprite );

	//bg	
	this.bg = new PIXI.Sprite( this.loader.resources.rowBG.texture );

	////store actual img height/ratio
	this.imgHeight = this.loader.resources.rowBG.texture.orig.height;
	this.imgWidth = this.loader.resources.rowBG.texture.orig.width;
	this.imgRatio = this.imgWidth/this.imgHeight;
	
	// Set image anchor to the center of the image
  this.bg.anchor.x = 0.5;
  this.bg.anchor.y = 0.5;
	
	//sim background-size cover
	this.bg.height = $(this.rowBG).height();
	this.bg.width = this.imgRatio * this.bg.height;
	
	if( this.bg.width <= $(this.rowBG).width() ) {
		
		this.bg.width = $(this.rowBG).width() + 100;
		this.bg.height = this.bg.width / this.imgRatio;
		
	} else {
		this.bg.height = $(this.rowBG).height() + 100;
		this.bg.width = this.imgRatio * this.bg.height;
	}

	//reset the dimensions for the renderer incase the row height has changed inbetween loading the bg resouce
	this.app.renderer.resize( $(this.rowBG).width(), $(this.rowBG).height() ); 

  this.buildStage();
  this.createFilters();
	
	this.app.view.setAttribute( "class", "nectar-liquid-bg" );
  this.canvasContainer.appendChild( this.app.view );
	
	
	if(this.animationType == 'displace-filter-loop') {
		
		if($('#nectar_fullscreen_rows').length == 0) {
			
			this.animateFilters();
			
			$liquidBGOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '105%';
			
			var self = this;
			var $parentSelector = (self.elType == 'row') ? '.row-bg-wrap' : '.column-image-bg-wrap';
			
			var waypoint = new Waypoint({
				element: $(this.canvasContainer).parents($parentSelector),
				handler: function(direction) {
					//add bg to container
					self.imgContainer.addChild( self.bg );
				},
				offset: $liquidBGOffsetPos
			});
			
		} else {
			
			this.animateFilters();
			this.imgContainer.addChild( this.bg );
			
		}

		
	} //end displace loop
	
	else if(this.animationType == 'displace-filter-fade') {
		
		this.animateFilters();
		this.settings.shouldAnimate = false;
		
		$liquidBGOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '85%';
		
		var self = this;
		var $parentSelector = (self.elType == 'row') ? '.row-bg-wrap' : '.column-image-bg-wrap';
			
		if($('#nectar_fullscreen_rows').length == 0 || $('#nectar_fullscreen_rows').length > 0 && $(this.canvasContainer).parents('.wpb_row.fp-section').index() == 0 || $disableFPonMobile == 'on') {
			
			var waypoint = new Waypoint({
				 element: $(this.canvasContainer).parents($parentSelector),
				 handler: function(direction) {

					if($(self.canvasContainer).parents('.wpb_tab').length > 0 && $(self.canvasContainer).parents('.wpb_tab').css('visibility') == 'hidden' || $(self.canvasContainer).hasClass('animated-in')) { 
							 waypoint.destroy();
							 return;
					}
				

					//add bg to container
					self.imgContainer.addChild( self.bg );
					
					//animate down
					self.animateProps(self);
					
					waypoint.destroy();
				},
				offset: $liquidBGOffsetPos

			}); //waypoint end
		}
		
		
	} // end fade in displace
	
	//resize logic
 	$(window).resize(this.resize.bind(this));
  $(window).smartresize(this.resize.bind(this));
}

NectarLiquid.prototype.animateProps = function(self) {
	
	setTimeout(function(){

		$(self.canvasContainer).find('.nectar-liquid-bg').addClass('animated-in');
		
		self.settings.shouldAnimate = true;
		self.settings.animationStrength = 1;
		self.settings.animationStrengthSpeed = 2.5;
		self.animateFilters();
		
		//animate displacement down
		anime({
			targets: self.settings,
			animationStrength: 0,
			duration: 1900,
			easing: [.36,.42,.3,1],
			complete: function(anim) {
				self.settings.shouldAnimate = false
			}
		});
		
		anime({
			targets: self.settings,
			animationStrengthSpeed: 0.8,
			duration: 1900,
			easing: [.36,.42,.3,1],
			complete: function(anim) {
				self.settings.shouldAnimate = false
			}
		});
	
	},100);
	
}

NectarLiquid.prototype.resize = function(){
	var self = this;

  self.bgDivRatio = $(self.rowBG).height() / $(self.rowBG).width();
	 
	 self.imgContainer.position.x = $(self.rowBG).width() / 2;
   self.imgContainer.position.y = $(self.rowBG).height() / 2;
	 
	 //sim background-size cover
	 self.bg.height = $(self.rowBG).height();
	 self.bg.width = self.imgRatio * self.bg.height;
	 
	 if( self.bg.width <= $(self.rowBG).width() ) {
		 self.bg.width = $(self.rowBG).width() + 100;
		 self.bg.height = self.bg.width / self.imgRatio;
		 
	 } else {
		 self.bg.height = $(self.rowBG).height() + 100;
		 self.bg.width = self.imgRatio * self.bg.height;
	 }
	 
	 self.app.stage.scale.x = self.app.stage.scale.y = 1;

	 self.app.renderer.resize( $(self.rowBG).width(), $(self.rowBG).height() ); 

}

NectarLiquid.prototype.createFilters = function() {
  this.app.stage.addChild( this.filterSprite );
  this.filter.scale.x = this.filter.scale.y = 1;
  this.imgContainer.filters = [
      this.filter
  ];
}

	
NectarLiquid.prototype.buildStage = function() {
  this.imgContainer.position.x = $(this.rowBG).width() / 2;
  this.imgContainer.position.y = $(this.rowBG).height() / 2;
  this.app.stage.scale.x = this.app.stage.scale.y = 1;
  this.app.stage.addChild( this.imgContainer );
}


NectarLiquid.prototype.animateFilters = function() {
	this.filterSprite.rotation = this.settings.time * 0.01;
	this.filterSprite.x = Math.sin(this.settings.time * 0.1) * this.settings.filterMultX;
  this.filterSprite.y = Math.cos(this.settings.time * 0.1) * this.settings.filterMultY;
  this.filter.scale.x = this.settings.displacementScaleX * this.settings.animationStrength;
  this.filter.scale.y = this.settings.displacementScaleY * this.settings.animationStrength;
  
	
	this.settings.time += 0.05 * this.settings.animationStrengthSpeed;
	
	if(this.animationType == 'displace-filter-loop') {
		requestAnimationFrame(function(){
	    this.animateFilters();
	  }.bind(this));
	}
	else if(this.animationType == 'displace-filter-fade' && this.settings.shouldAnimate) {
		requestAnimationFrame(function(){
	    this.animateFilters();
	  }.bind(this));
	}
	
} 

function nectarLiquidBGs() {
	$liquidBG_EL = [];

	$('.row-bg-wrap[data-bg-animation*="displace-filter"] .row-bg.using-image, .column-image-bg-wrap[data-bg-animation*="displace-filter"] .column-image-bg').each(function(i){
		
		var $that_el = $(this);
		
		if( $(this).is('.row-bg') ) {
			
			var $type = $(this).parents('.row-bg-wrap').attr('data-bg-animation');
			var $el_type = 'row';
			
		} else if(  $(this).is('.column-image-bg') ) {
			
			var $type = $(this).parents('.column-image-bg-wrap').attr('data-bg-animation');
			var $el_type = 'col';
		}
		
		$liquidBG_EL[i] = new NectarLiquid($that_el, $type, $el_type);
		
	});
}





/***************** Global vars ******************/
var $standAnimatedColTimeout = [];
var $animatedSVGIconTimeout = [];
var $svg_icons = [];
var $nectarCustomSliderRotate = [];
var $flickitySliders = [];
var $wooFlickityCarousels = [];
var $liquidBG_EL = [];

/***************** Smooth Scrolling ******************/

	function niceScrollInit(){
		//removed since 9.0.
	}

	var $smoothActive = 0; 
	var $smoothCache = false;
	
	
	//chrome ss
	if($smoothCache == false && $('body.material').length == 0 && navigator.platform.toUpperCase().indexOf('MAC') === -1 && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/) && $(window).width() > 690 && $('#nectar_fullscreen_rows').length == 0) {
		!function(){function e(){var e=!1;e&&c("keydown",r),v.keyboardSupport&&!e&&u("keydown",r)}function t(){if(document.body){var t=document.body,n=document.documentElement,o=window.innerHeight,r=t.scrollHeight;if(S=document.compatMode.indexOf("CSS")>=0?n:t,w=t,e(),x=!0,top!=self)y=!0;else if(r>o&&(t.offsetHeight<=o||n.offsetHeight<=o)){var a=!1,i=function(){a||n.scrollHeight==document.height||(a=!0,setTimeout(function(){n.style.height=document.height+"px",a=!1},500))};if(n.style.height="auto",setTimeout(i,10),S.offsetHeight<=o){var l=document.createElement("div");l.style.clear="both",t.appendChild(l)}}v.fixedBackground||b||(t.style.backgroundAttachment="scroll",n.style.backgroundAttachment="scroll")}}function n(e,t,n,o){if(o||(o=1e3),d(t,n),1!=v.accelerationMax){var r=+new Date,a=r-C;if(a<v.accelerationDelta){var i=(1+30/a)/2;i>1&&(i=Math.min(i,v.accelerationMax),t*=i,n*=i)}C=+new Date}if(M.push({x:t,y:n,lastX:0>t?.99:-.99,lastY:0>n?.99:-.99,start:+new Date}),!T){var l=e===document.body,u=function(){for(var r=+new Date,a=0,i=0,c=0;c<M.length;c++){var s=M[c],d=r-s.start,f=d>=v.animationTime,h=f?1:d/v.animationTime;v.pulseAlgorithm&&(h=p(h));var m=s.x*h-s.lastX>>0,w=s.y*h-s.lastY>>0;a+=m,i+=w,s.lastX+=m,s.lastY+=w,f&&(M.splice(c,1),c--)}l?window.scrollBy(a,i):(a&&(e.scrollLeft+=a),i&&(e.scrollTop+=i)),t||n||(M=[]),M.length?N(u,e,o/v.frameRate+1):T=!1};N(u,e,0),T=!0}}function o(e){x||t();var o=e.target,r=l(o);if(!r||e.defaultPrevented||s(w,"embed")||s(o,"embed")&&/\.pdf/i.test(o.src))return!0;var a=e.wheelDeltaX||0,i=e.wheelDeltaY||0;return a||i||(i=e.wheelDelta||0),!v.touchpadSupport&&f(i)?!0:(Math.abs(a)>1.2&&(a*=v.stepSize/120),Math.abs(i)>1.2&&(i*=v.stepSize/120),n(r,-a,-i),void e.preventDefault())}function r(e){var t=e.target,o=e.ctrlKey||e.altKey||e.metaKey||e.shiftKey&&e.keyCode!==H.spacebar;if(/input|textarea|select|embed/i.test(t.nodeName)||t.isContentEditable||e.defaultPrevented||o)return!0;if(s(t,"button")&&e.keyCode===H.spacebar)return!0;var r,a=0,i=0,u=l(w),c=u.clientHeight;switch(u==document.body&&(c=window.innerHeight),e.keyCode){case H.up:i=-v.arrowScroll;break;case H.down:i=v.arrowScroll;break;case H.spacebar:r=e.shiftKey?1:-1,i=-r*c*.9;break;case H.pageup:i=.9*-c;break;case H.pagedown:i=.9*c;break;case H.home:i=-u.scrollTop;break;case H.end:var d=u.scrollHeight-u.scrollTop-c;i=d>0?d+10:0;break;case H.left:a=-v.arrowScroll;break;case H.right:a=v.arrowScroll;break;default:return!0}n(u,a,i),e.preventDefault()}function a(e){w=e.target}function i(e,t){for(var n=e.length;n--;)E[A(e[n])]=t;return t}function l(e){var t=[],n=S.scrollHeight;do{var o=E[A(e)];if(o)return i(t,o);if(t.push(e),n===e.scrollHeight){if(!y||S.clientHeight+10<n)return i(t,document.body)}else if(e.clientHeight+10<e.scrollHeight&&(overflow=getComputedStyle(e,"").getPropertyValue("overflow-y"),"scroll"===overflow||"auto"===overflow))return i(t,e)}while(e=e.parentNode)}function u(e,t,n){window.addEventListener(e,t,n||!1)}function c(e,t,n){window.removeEventListener(e,t,n||!1)}function s(e,t){return(e.nodeName||"").toLowerCase()===t.toLowerCase()}function d(e,t){e=e>0?1:-1,t=t>0?1:-1,(k.x!==e||k.y!==t)&&(k.x=e,k.y=t,M=[],C=0)}function f(e){if(e){e=Math.abs(e),D.push(e),D.shift(),clearTimeout(z);var t=h(D[0],120)&&h(D[1],120)&&h(D[2],120);return!t}}function h(e,t){return Math.floor(e/t)==e/t}function m(e){var t,n,o;return e*=v.pulseScale,1>e?t=e-(1-Math.exp(-e)):(n=Math.exp(-1),e-=1,o=1-Math.exp(-e),t=n+o*(1-n)),t*v.pulseNormalize}function p(e){return e>=1?1:0>=e?0:(1==v.pulseNormalize&&(v.pulseNormalize/=m(1)),m(e))}var w,g={frameRate:150,animationTime:500,stepSize:120,pulseAlgorithm:!0,pulseScale:8,pulseNormalize:1,accelerationDelta:20,accelerationMax:1,keyboardSupport:!0,arrowScroll:50,touchpadSupport:!0,fixedBackground:!0,excluded:""},v=g,b=!1,y=!1,k={x:0,y:0},x=!1,S=document.documentElement,D=[120,120,120],H={left:37,up:38,right:39,down:40,spacebar:32,pageup:33,pagedown:34,end:35,home:36},v=g,M=[],T=!1,C=+new Date,E={};setInterval(function(){E={}},1e4);var z,A=function(){var e=0;return function(t){return t.uniqueID||(t.uniqueID=e++)}}(),N=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||function(e,t,n){window.setTimeout(e,n||1e3/60)}}(),K=/chrome/i.test(window.navigator.userAgent),L=null;"onwheel"in document.createElement("div")?L="wheel":"onmousewheel"in document.createElement("div")&&(L="mousewheel"),L&&K&&(u(L,o),u("mousedown",a),u("load",t))}();
	}


/***************** Sliders ******************/

	//gallery
	function flexsliderInit(){
		$('.flex-gallery').each(function(){
			
			var $that = $(this);
			
			imagesLoaded($(this),function(instance){
			
				 $that.flexslider({
			        animation: 'fade',
			        smoothHeight: false, 
			        animationSpeed: 500,
			        useCSS: false, 
			        touch: true
			    });
				
				////gallery slider add arrows
				$('.flex-gallery .flex-direction-nav li a.flex-next').html('<i class="fa fa-angle-right"></i>');
				$('.flex-gallery .flex-direction-nav li a.flex-prev').html('<i class="fa fa-angle-left"></i>');
			
			});
			
		});
	}
	flexsliderInit();

  


	function flickityInit() {
		if($('.nectar-flickity:not(.masonry)').length == 0) return false;
		$flickitySliders = [];
		$('.nectar-flickity:not(.masonry)').each(function(i){
			
			$(this).removeClass(function (index, className) {
			    return (className.match (/(^|\s)instance-\S+/g) || []).join(' ');
			});
			$(this).addClass('instance-'+i);
			
			var $freeScrollBool = ($(this).is('[data-free-scroll]') && $(this).attr('data-free-scroll') == 'true') ? true : false;
			var $groupCellsBool = true;
			var $flickContainBool = true;
			var $flcikAttr = 0.025;
			
			var $flickCellAlign = 'center';
			if( $(this).is('[data-format="fixed_text_content_fullwidth"]') ) {
					$flickCellAlign = 'left';
					$groupCellsBool = false;
					$flickContainBool = false;
					$flcikAttr = 0.02;
			}

			if($freeScrollBool == true) {
				 $groupCellsBool = false;
			}

			if($(this).attr('data-controls').length > 0 && $(this).attr('data-controls') == 'next_prev_arrows') {
				var $paginationBool = false;
				var $nextPrevArrowBool = true;
			} else {
				var $paginationBool = true;
				var $nextPrevArrowBool = false;
			}

			if($(this).attr('data-controls').length > 0 && $(this).attr('data-controls') == 'none') {
				var $paginationBool = false;
				var $nextPrevArrowBool = false;
			}

			var $flickity_autoplay = false;
			var $selectedAttraction = 0.025;

			if($(this).is('[data-autoplay]') && $(this).attr('data-autoplay') == 'true') {
				
				$flickity_autoplay = true;
				$selectedAttraction = 0.019;

				if($(this).is('[data-autoplay-dur]') && $(this).attr('data-autoplay-dur').length > 0) {

					if(parseInt($(this).attr('data-autoplay-dur')) > 100 && parseInt($(this).attr('data-autoplay-dur')) < 30000) {
						$flickity_autoplay = parseInt($(this).attr('data-autoplay-dur'));
					}
					
				}

			}

			var $that = $(this);
			var $frontEndEditorDrag =  ($('body.vc_editor').length > 0) ? false: true;
			var $frontEndEditorPause =  ($('body.vc_editor').length > 0) ? true: false;
			
			$flickitySliders[i] = new Flickity('.nectar-flickity.instance-'+i, {
			  contain: $flickContainBool,
			  draggable:$frontEndEditorDrag,
			  lazyLoad: false,
			  imagesLoaded: true,
			  percentPosition: true,
				cellAlign: $flickCellAlign,
			  selectedAttraction: $selectedAttraction,
			  groupCells: $groupCellsBool,
			  prevNextButtons: $nextPrevArrowBool,
			  freeScroll: $freeScrollBool,
			  pageDots: $paginationBool,
			  resize: true,
				selectedAttraction: $flcikAttr,
			  autoPlay: $flickity_autoplay,
				pauseAutoPlayOnHover: $frontEndEditorPause,
			  setGallerySize: true,
			  wrapAround: true,
			  accessibility: false,
			  arrowShape: { 
				     x0: 20,
					  x1: 70, y1: 30,
					  x2: 70, y2: 25,
					  x3: 70
				}
			});
			

			if($(this).is('[data-format="fixed_text_content_fullwidth"]') && !nectarDOMInfo.usingFrontEndEditor) {
				
				var $onMobileBrowser = navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/);
				
				$flickitySliders[i].on( 'scroll', function() {
					
					if($onMobileBrowser) { return; }
					
					var $curFlkSlid = $flickitySliders[i];
					var $flkSlideWidth = $that.find('.cell').outerWidth() + 25;
					var $leftHeaderSize = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 275 : 0;
					var $extraWindowSpace = ( ($(window).width() + $leftHeaderSize) - $that.parents('.main-content').width())/2;
					$extraWindowSpace += parseInt($that.css('margin-left')) + 2;
					$flickitySliders[i].slides.forEach( function( slide, j ) {

							var $scaleAmt = 1;
							var $translateXAmt = 0;
							var $rotateAmt = 0;
							var $slideZIndex = 10;
							var $opacityAmt = 1;
							
							var $slideOffset = $(slide.cells[0].element).offset().left;
							
							var flkInstanceSlide = $('.nectar-flickity.instance-'+i+' .cell:nth-child('+ (j+1) +')');
							
							if($slideOffset - $extraWindowSpace < 0 && $slideOffset  - $extraWindowSpace > $flkSlideWidth*-1 ) {
								$scaleAmt = 1 + ( ($slideOffset - $extraWindowSpace) / 1500);
								$opacityAmt = 1 + ( ($slideOffset - $extraWindowSpace + 70) / 550);
								$translateXAmt = ( ($slideOffset - $extraWindowSpace)) * -1;
								$rotateAmt = ( ($slideOffset  - $extraWindowSpace) / 25) * -1;
							} else {
								$scaleAmt = 1;
								$opacityAmt = 1;
								$translateXAmt = 0;
								$rotateAmt = 0;
							}
							
							//handle z separately for an extra cushion of 5px in case
							if($slideOffset + 5 - $extraWindowSpace < 0 && $slideOffset  - $extraWindowSpace > $flkSlideWidth*-1 ) {
								$slideZIndex = 5;
							} else {
								$slideZIndex = 10;
							}
							
							flkInstanceSlide.css({
								'z-index' : $slideZIndex
							});
							
							flkInstanceSlide.find('.inner-wrap-outer').css({
								'transform': 'perspective(800px) translateX('+ $translateXAmt +'px) rotateY('+$rotateAmt+'deg) translateZ(0)',
								'opacity' : $opacityAmt
							});
							
							flkInstanceSlide.find('.inner-wrap').css({
								'transform': 'scale('+ $scaleAmt +') translateZ(0)'
							}); 
							
						  
					
					});
				});
				
				
			}

			var $removeHiddenTimeout;

			$flickitySliders[i].on( 'dragStart', function() {
			   clearTimeout($removeHiddenTimeout);
				 $that.addClass('is-dragging');
			   $that.find('.flickity-prev-next-button').addClass('hidden');
			});
			$flickitySliders[i].on( 'dragEnd', function() {
				$that.removeClass('is-dragging');
				$removeHiddenTimeout = setTimeout(function(){
					$that.find('.flickity-prev-next-button').removeClass('hidden');
				},600);
			 
			});

			$('.flickity-prev-next-button').on( 'click', function() {
			   clearTimeout($removeHiddenTimeout);
			   $(this).parents('.nectar-flickity').find('.flickity-prev-next-button').addClass('hidden');
			   $removeHiddenTimeout = setTimeout(function(){
					$that.find('.flickity-prev-next-button').removeClass('hidden');
				},600);
			});
			
			
			//imgs loaded
			if($that.hasClass('nectar-carousel')) {
				imagesLoaded($that,function(instance){
					nectarCarouselFlkEH($that);
				});
			}
			

		});
		

	}
	
	
	setTimeout(flickityInit,100);
	
	
	//nectar carousel flickity equal height
	
	////loop
	function setNectarCarouselFlkEH() {
			$('.nectar-carousel.nectar-flickity:not(.masonry)').each(function(){
				  nectarCarouselFlkEH($(this));
			});
	}
	
	function nectarCarouselFlkEH($slider_instance) {
			
			var $tallestSlideCol = 0;
			
			$slider_instance.find('.flickity-slider > .cell').css('height','auto');
			
			$slider_instance.find('.flickity-slider > .cell').each(function(){
				($(this).height() > $tallestSlideCol) ? $tallestSlideCol = $(this).height() : $tallestSlideCol = $tallestSlideCol;
			});	
			
			//safety net incase height couldn't be determined
			if($tallestSlideCol < 10) $tallestSlideCol = 'auto';

			//set even height
			$slider_instance.find('.flickity-slider > .cell').css('height',$tallestSlideCol+'px');

	}
	
	


	function flickityBlogInit() {
		if($('.nectar-flickity.masonry.not-initialized').length == 0) return false;

		$('.nectar-flickity.masonry.not-initialized').each(function(){

			//move pos for large_featured
			if($(this).parents('article').hasClass('large_featured')) 
				$(this).insertBefore( $(this).parents('article').find('.content-inner') );
			
		});


		$('.nectar-flickity.masonry.not-initialized').flickity({
		  contain: true,
		  draggable: false,
		  lazyLoad: false,
		  imagesLoaded: true,
		  percentPosition: true,
		  prevNextButtons: true,
		  pageDots: false,
		  resize: true,
		  setGallerySize: true,
		  wrapAround: true,
		  accessibility: false
		});

		$('.nectar-flickity.masonry').removeClass('not-initialized');

		//add count
		$('.nectar-flickity.masonry:not(.not-initialized)').each(function(){

			if($(this).find('.item-count').length == 0) {
				
				$('<div class="item-count"/>').insertBefore($(this).find('.flickity-prev-next-button.next'));
				$(this).find('.item-count').html('<span class="current">1</span>/<span class="total">' + $(this).find('.flickity-slider .cell').length + '</span>');

				$(this).find('.flickity-prev-next-button, .item-count').wrapAll('<div class="control-wrap" />');

				//move pos for wide_tall
				if($(this).parents('article').hasClass('wide_tall') && $(this).parents('.masonry.material').length == 0) 
					$(this).find('.control-wrap').insertBefore( $(this) );
			}
		});

		//update count
		$('.masonry .flickity-prev-next-button.previous,  .masonry .flickity-prev-next-button.next').on('click',function(){
			if($(this).parents('.wide_tall').length > 0) 
				$(this).parent().find('.item-count .current').html($(this).parents('article').find('.nectar-flickity .cell.is-selected').index()+1);
			else 
				$(this).parent().find('.item-count .current').html($(this).parents('.nectar-flickity').find('.cell.is-selected').index()+1);
		});

		$('body').on('mouseover','.flickity-prev-next-button.next',function(){
			$(this).parent().find('.flickity-prev-next-button.previous, .item-count').addClass('next-hovered');
		});
		$('body').on('mouseleave','.flickity-prev-next-button.next',function(){
			$(this).parent().find('.flickity-prev-next-button.previous, .item-count').removeClass('next-hovered');
		});
	
	}
/****************twenty twenty******************/
function twentytwentyInit() {
	$('.twentytwenty-container').each(function(){
		var $that = $(this);
		
		if($that.find('.twentytwenty-handle').length == 0) {
			$(this).imagesLoaded(function(){
				$that.twentytwenty();
			});
		}
		
	});
}
twentytwentyInit();

/****************split line text******************/
if($('.nectar-recent-posts-single_featured.multiple_featured').length > 0) {
  splitLineText();
}
	
/****************full page******************/
var $frontEndEditorFPRDiv = '';
var $usingFullScreenRows = false;
var $fullscreenSelector = '';
var $disableFPonMobile = ($('#nectar_fullscreen_rows[data-mobile-disable]').length > 0) ? $('#nectar_fullscreen_rows').attr('data-mobile-disable') : 'off';
var $onMobileBrowser = navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/);

if(!$onMobileBrowser) {
	$disableFPonMobile = 'off';
}

//change anchor link IDs for when disabled on mobile
if($disableFPonMobile == 'on' && $('#nectar_fullscreen_rows').length > 0) {
	$('#nectar_fullscreen_rows > .wpb_row[data-fullscreen-anchor-id]').each(function(){
		if($(this).attr('data-fullscreen-anchor-id').length > 0)
			$(this).attr('id',$(this).attr('data-fullscreen-anchor-id'));
	});


	//remove main content row padding
	$('.container-wrap .main-content > .row').css({'padding-bottom':'0'});

	//extra padding for first row is using transparent header
	if( $('#nectar_fullscreen_rows > .wpb_row:nth-child(1)').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0 && !$('#nectar_fullscreen_rows > .wpb_row:nth-child(1)').hasClass('full-width-content') ) {
		$('#nectar_fullscreen_rows > .wpb_row:nth-child(1)').addClass('extra-top-padding');
	}
}

if($('#nectar_fullscreen_rows').length > 0 && $disableFPonMobile != 'on' || $().fullpage && $disableFPonMobile != 'on') {

	function setFPNavColoring(index,direction) {
			
		  if($('#boxed').length > 0 && overallWidth > 750) return;
		
		  if($('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv+':nth-child('+index+')').find('.span_12.light').length > 0) {
    		$('#fp-nav').addClass('light-controls');

    		  if(direction == 'up')
    			  $('#header-outer.dark-slide').removeClass('dark-slide');
    		  else
    		  	setTimeout(function(){ $('#header-outer.dark-slide').removeClass('dark-slide'); },520);
    	} else {
    		  $('#fp-nav.light-controls').removeClass('light-controls');

    		  if(direction == 'up')
    		  	$('#header-outer').addClass('dark-slide');
    		  else
    			  setTimeout(function(){ $('#header-outer').addClass('dark-slide'); },520);
    	}
			
			//handle n slider coloring
			if($('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv+':nth-child('+index+')').find('.nectar-slider-wrap[data-fullscreen="true"]').length > 0) {
			 var $currentSlider = $('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv+':nth-child('+index+')').find('.nectar-slider-wrap[data-fullscreen="true"]');
		
				if( $currentSlider.is('[data-overall_style="directional"]') && $('#header-outer #logo span.dark').length > 0 ){
					$('#header-outer').addClass('directional-nav-effect').removeClass('dne-disabled');
				}
				
				if($currentSlider.find('.swiper-slide-active[data-color-scheme="light"]').length > 0) {
					$('#header-outer').removeClass('dark-slide');
				} else if($currentSlider.find('.swiper-slide-active[data-color-scheme="dark"]').length > 0) {
					$('#header-outer').addClass('dark-slide');
				}
				
			} else {
				
					$('#header-outer').removeClass('directional-nav-effect').addClass('dne-disabled');
				
			}
			
	}
	
	

	var $anchors = [];
	var $names = [];
	
	function setFPNames() {
		$anchors = [];
		$names = [];
		$frontEndEditorFPRDiv =  (window.vc_iframe) ? '> .vc_element': '> .wpb_row';
		
		$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv).each(function(i){
			var $id = ($(this).is('[data-fullscreen-anchor-id]')) ? $(this).attr('data-fullscreen-anchor-id') : '';

			//anchor checks
			if($('#nectar_fullscreen_rows[data-anchors="on"]').length > 0) {
				if($id.indexOf('fws_') == -1) $anchors.push($id);
				else $anchors.push('section-'+(i+1));
			}

			//name checks
			if($(this).find('.full-page-inner-wrap[data-name]').length > 0) 
				$names.push($(this).find('.full-page-inner-wrap').attr('data-name'));
			else 
				$names.push(' ');
		});
	}
	setFPNames();

	function initFullPageFooter() {
		var $footerPos = $('#nectar_fullscreen_rows').attr('data-footer');

		if($footerPos == 'default') {
			$('#footer-outer').appendTo('#nectar_fullscreen_rows').addClass('fp-auto-height').addClass('fp-section').addClass('wpb_row').attr('data-anchor',' ').wrapInner('<div class="span_12" />').wrapInner('<div class="container" />').wrapInner('<div class="full-page-inner" />').wrapInner('<div class="full-page-inner-wrap" />').wrapInner('<div class="full-page-inner-wrap-outer" />');
		}
		else if($footerPos == 'last_row') {
			$('#footer-outer').remove();
			$('#nectar_fullscreen_rows > .wpb_row:last-child').attr('id','footer-outer').addClass('fp-auto-height');
		} else {
			$('#footer-outer').remove();
		}
		
	}	

	if($('#nectar_fullscreen_rows').length > 0)
		initFullPageFooter();

	//fullscreen row logic
	function fullscreenRowLogic() {
		$('.full-page-inner-wrap .full-page-inner > .span_12 > .wpb_column').each(function(){
			if($(this).find('> .vc_column-inner > .wpb_wrapper').find('> .wpb_row').length > 0) {

				//add class for css
				$(this).find('> .vc_column-inner > .wpb_wrapper').addClass('only_rows');

				//set number of rows for css
				var $rowNum = $(this).find('> .vc_column-inner > .wpb_wrapper').find('> .wpb_row').length;
				$(this).find('> .vc_column-inner > .wpb_wrapper').attr('data-inner-row-num',$rowNum);
			} 

			else if($(this).find('> .column-inner-wrap > .column-inner > .wpb_wrapper').find('> .wpb_row').length > 0) {

				//add class for css
				$(this).find('> .column-inner-wrap > .column-inner > .wpb_wrapper').addClass('only_rows');

				//set number of rows for css
				var $rowNum = $(this).find('> .column-inner-wrap > .column-inner > .wpb_wrapper').find('> .wpb_row').length;
				$(this).find('> .column-inner-wrap > .column-inner > .wpb_wrapper').attr('data-inner-row-num',$rowNum);
			}
		});
	}

	fullscreenRowLogic();

	function fullHeightRowOverflow() {
		//handle rows with full height that are larger than viewport
		if($(window).width() >= 1000) {

	    	$('#nectar_fullscreen_rows > .wpb_row .full-page-inner-wrap[data-content-pos="full_height"]').each(function(){

	    		//reset mobile calcs incase user plays with window resize
	    		$(this).find('> .full-page-inner').css('height','100%');

	    		var maxHeight = overallHeight;
	    		var columnPaddingTop = 0;
	    		var columnPaddingBottom = 0;

	    		
	    		if($('#nectar_fullscreen_rows').attr('data-animation') == 'none')
	    			$(this).find('> .full-page-inner > .span_12 ').css('height','100%');
	    		else
	    			$(this).find('> .full-page-inner > .span_12 ').css('height',overallHeight);

	    		$(this).find('> .full-page-inner > .span_12 > .wpb_column > .vc_column-inner > .wpb_wrapper').each(function(){
	    			 columnPaddingTop = parseInt($(this).parents('.wpb_column').css('padding-top'));
	    			 columnPaddingBottom = parseInt($(this).parents('.wpb_column').css('padding-bottom'));

	    			 maxHeight = maxHeight > $(this).height() + columnPaddingTop + columnPaddingBottom ? maxHeight : $(this).height() + columnPaddingTop + columnPaddingBottom;
	    		});
	    		
	    	
	    		if(maxHeight > overallHeight)
	    			$(this).find('> .full-page-inner > .span_12').height(maxHeight).css('float','none');
	    		
	    	});

	    }

	    else {
	    	//mobile min height set
	    	$('#nectar_fullscreen_rows > .wpb_row').each(function(){
	    		$totalColHeight = 0;
	    		$(this).find('.fp-scrollable > .fp-scroller > .full-page-inner-wrap-outer > .full-page-inner-wrap[data-content-pos="full_height"] > .full-page-inner > .span_12 > .wpb_column').each(function(){
	    			$totalColHeight += $(this).outerHeight(true);
	    		});

	    		$(this).find('.fp-scrollable > .fp-scroller > .full-page-inner-wrap-outer > .full-page-inner-wrap > .full-page-inner').css('height','100%');
	    		if($totalColHeight > $(this).find('.fp-scrollable > .fp-scroller > .full-page-inner-wrap-outer > .full-page-inner-wrap > .full-page-inner').height())
	    			$(this).find('.fp-scrollable  > .fp-scroller > .full-page-inner-wrap-outer > .full-page-inner-wrap > .full-page-inner').height($totalColHeight);
	    	});
	    }

	}

	function fullscreenElementSizing() {
		//nectar slider
		var $nsSelector = '.nectar-slider-wrap[data-fullscreen="true"][data-full-width="true"], .nectar-slider-wrap[data-fullscreen="true"][data-full-width="boxed-full-width"]';
		if($('.nectar-slider-wrap[data-fullscreen="true"][data-full-width="true"]').length > 0 || $('.nectar-slider-wrap[data-fullscreen="true"][data-full-width="boxed-full-width"]').length > 0) {
			if($('#nectar_fullscreen_rows .wpb_row').length > 0) 
				$($nsSelector).find('.swiper-container').attr('data-height',$('#nectar_fullscreen_rows .wpb_row').height()+1);
	        
	        $(window).trigger('resize.nsSliderContent');

	        $($nsSelector).parents('.full-page-inner').addClass('only-nectar-slider');
	    }
	}


	//kenburns first slide fix
	$('#nectar_fullscreen_rows[data-row-bg-animation="ken_burns"] > .wpb_row:first-child .row-bg.using-image').addClass('kenburns');
	setTimeout(function(){
		//ken burns first slide fix
		$('#nectar_fullscreen_rows[data-row-bg-animation="ken_burns"] > .wpb_row:first-child .row-bg.using-image').removeClass('kenburns');
	},500);

	//remove kenburns from safari
	if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) $('#nectar_fullscreen_rows[data-row-bg-animation="ken_burns"]').attr('data-row-bg-animation','none');

	var overallHeight = $(window).height();
	var overallWidth = $(window).width();
	var $fpAnimation = $('#nectar_fullscreen_rows').attr('data-animation');
	var $fpAnimationSpeed;
	var $svgResizeTimeout;
	
	switch($('#nectar_fullscreen_rows').attr('data-animation-speed')) {
		case 'slow':
			$fpAnimationSpeed = 1150;
			break;
		case 'medium':
			$fpAnimationSpeed = 850;
			break;
		case 'fast':
			$fpAnimationSpeed = 650;
			break;
		default:
			$fpAnimationSpeed = 850;
	}
	
	function heyFirefoxDrawTheEl(){
			var $drawTheEl = $('#nectar_fullscreen_rows > div:first-child').height();
			if($('#nectar_fullscreen_rows.trans-animation-active').length > 0){
				requestAnimationFrame(heyFirefoxDrawTheEl);
			}
	}

	
	function initNectarFP() {
		
		$frontEndEditorFPRDiv =  (window.vc_iframe) ? '> .vc_element': '> .wpb_row';
		
		if(window.vc_iframe) {
			setTimeout(function(){
				$('html,body').css({
					'height':'100%',
				  'overflow-y':'hidden'
			  });
			},100);
			
			//remove scrolling to help performance of FE editor
			$('body,html').on("mousewheel.removeScroll", function() {
			    return false;
			});
			
		}
		
		//move row IDs onto parents for front end editor
		if($('body.vc_editor').length > 0) {
			$('#nectar_fullscreen_rows > .vc_empty-placeholder').remove();
			
			$('#nectar_fullscreen_rows > .vc_element').each(function(){
				var innerRowID = $(this).find('> .wpb_row').attr('id');
				$(this).attr('id',innerRowID);
			});
		}
		
		$usingFullScreenRows = true;
		$fullscreenSelector = (window.vc_iframe) ? '.vc_element.vc_vc_row.active ' : '.wpb_row.active ';

		$('.container-wrap, .container-wrap .main-content > .row').css({'padding-bottom':'0', 'margin-bottom': '0'});
		$('#nectar_fullscreen_rows').fullpage({
			sectionSelector: '#nectar_fullscreen_rows '+$frontEndEditorFPRDiv,
			navigation: true,
			css3: true,
			scrollingSpeed: $fpAnimationSpeed,
			anchors: $anchors,
			scrollOverflow: true,
			navigationPosition: 'right',
			navigationTooltips: $names,
			afterLoad: function(anchorLink, index, slideAnchor, slideIndex){ 

				if($('#nectar_fullscreen_rows').hasClass('afterLoaded')) {
				 	
					//ensure no scrolling body occurs
					if(nectarDOMInfo.scrollTop != 0) {
						window.scrollTo(0,0);
					}
					
					//reset slim scroll to top
					$('.wpb_row:not(.last-before-footer):not(:nth-child('+index+')) .fp-scrollable').each(function(){
						var $scrollable = $(this).data('iscrollInstance');
						$scrollable.scrollTo(0,0);
					});

					//reset carousel
					$('.wpb_row:not(:nth-child('+index+')) .owl-carousel').trigger('to.owl.carousel',[0]);

					var $row_id = $('#nectar_fullscreen_rows > .wpb_row:nth-child('+index+')').attr('id');

					$('#nectar_fullscreen_rows '+$frontEndEditorFPRDiv).removeClass('transition-out').removeClass('trans');
					

					$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +':nth-child('+index+')').removeClass('next-current');
					$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +':nth-child('+index+') .full-page-inner-wrap-outer').css({'height': '100%'});
					$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +' .full-page-inner-wrap-outer').css({'transform':'none'});
					//take care of nav/control coloring
					//setFPNavColoring(index,'na');
					
					//handle waypoints
					if($row_id != 'footer-outer' && $('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +':nth-child('+index+').last-before-footer').length == 0) {
						waypoints();
						if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) && !nectarDOMInfo.usingFrontEndEditor) {
							resetWaypoints();
							Waypoint.destroyAll();
							startMouseParallax();
						}
						if(!nectarDOMInfo.usingFrontEndEditor) {
							nectarLiquidBGFP();
						}
						responsiveTooltips();
					}
					
					if($row_id !='footer-outer') {
						$('#nectar_fullscreen_rows ' + $frontEndEditorFPRDiv).removeClass('last-before-footer').css('transform','initial');

						//reset animation attrs
						if(!window.vc_iframe) {

							$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +':not(.active):not(#footer-outer)').css({'transform':'translateY(0)','left':'-9999px', 'transition': 'none', 'opacity':'1', 'will-change':'auto'});
							$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +':not(#footer-outer)').find('.full-page-inner-wrap-outer').css({'transition': 'none',  'transform':'none', 'will-change':'auto'});
							$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +':not(#footer-outer)').find('.fp-tableCell').css({'transition': 'none', 'transform':'none', 'will-change':'auto'});
						}
						
						//stacking fix
						$('#nectar_fullscreen_rows '+ $frontEndEditorFPRDiv +':not(#footer-outer)').find('.full-page-inner-wrap-outer > .full-page-inner-wrap > .full-page-inner > .container').css({'backface-visibility':'visible', 'z-index':'auto'});
					}
				} else {
					fullHeightRowOverflow();
					overallHeight = $('#nectar_fullscreen_rows').height();
					$('#nectar_fullscreen_rows').addClass('afterLoaded');

					//for users that have scrolled down prior to turning on full page
					setTimeout(function(){ window.scrollTo(0,0); },1800);

					//ken burns first slide fix
					$('#nectar_fullscreen_rows[data-row-bg-animation="ken_burns"] '+ $frontEndEditorFPRDiv +':first-child .row-bg.using-image').removeClass('kenburns');

					//handle fullscreen elements
	        fullscreenElementSizing();

						
				}

				
				$('#nectar_fullscreen_rows').removeClass('nextSectionAllowed');

				
			 },
	        onLeave: function(index, nextIndex, direction){ 
	        	
						$('#nectar_fullscreen_rows').addClass('trans-animation-active');
						
						var $row_id = $('#nectar_fullscreen_rows ' + $frontEndEditorFPRDiv + ':nth-child('+nextIndex+')').attr('id');
	        	var $indexRow = $('#nectar_fullscreen_rows ' + $frontEndEditorFPRDiv + ':nth-child('+index+')');
	        	var $nextIndexRow = $('#nectar_fullscreen_rows ' + $frontEndEditorFPRDiv + ':nth-child('+nextIndex+')');
	        	var $nextIndexRowInner = $nextIndexRow.find('.full-page-inner-wrap-outer');
	        	var $nextIndexRowFpTable = $nextIndexRow.find('.fp-tableCell');
	        	//mobile/safari  fix
	        	var $transformProp = (!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) ? 'transform' : 'all'; 
	        	//if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) $transformProp = 'all';

	        	if( $row_id == 'footer-outer') {
	        		$indexRow.addClass('last-before-footer'); 
	        		$('#footer-outer').css('opacity','1');
	        	} else {
	        		$('#nectar_fullscreen_rows '+$frontEndEditorFPRDiv+'.last-before-footer').css('transform','translateY(0px)');
	        		$('#footer-outer').css('opacity','0');
	        	}
	        	if($indexRow.attr('id') == 'footer-outer') {
	        		$('#footer-outer').css({'transition': $transformProp+' 460ms cubic-bezier(0.60, 0.23, 0.2, 0.93)', 'backface-visibility': 'hidden'});
	        		$('#footer-outer').css({'transform': 'translateY(45%) translateZ(0)'});
	        	}
	 			

	 			//stacking fix
	 			if($nextIndexRow.attr('id') != 'footer-outer') {
					$nextIndexRowFpTable.find('.full-page-inner-wrap-outer > .full-page-inner-wrap > .full-page-inner > .container').css({'backface-visibility':'hidden', 'z-index':'110'});
				}

	        	//animation
	        	if($nextIndexRow.attr('id') != 'footer-outer' && $indexRow.attr('id') != 'footer-outer' && $('#nectar_fullscreen_rows[data-animation="none"]').length == 0 ) {

	        		//scrolling down
	        		if(direction == 'down') {

	        			if($fpAnimation == 'parallax') {
		        			$indexRow.css({'transition': $transformProp+' '+$fpAnimationSpeed+'ms cubic-bezier(.29,.23,.13,1)', 'will-change':'transform', 'transform':'translateZ(0)' ,'z-index': '100'});
		        			setTimeout(function() { 
		        				$indexRow.css({'transform': 'translateY(-50%) translateZ(0)'});
		        			}, 60);

		        			$nextIndexRow.css({'z-index':'1000','top':'0','left':'0'});
		        			$nextIndexRowFpTable.css({'transform':'translateY(100%) translateZ(0)', 'will-change':'transform'});
		        			$nextIndexRowInner.css({'transform':'translateY(-50%) translateZ(0)', 'will-change':'transform'});
		        			
	        			} else if($fpAnimation == 'zoom-out-parallax') {

	        				$indexRow.css({'transition': 'opacity '+$fpAnimationSpeed+'ms cubic-bezier(0.37, 0.31, 0.2, 0.85), transform '+$fpAnimationSpeed+'ms cubic-bezier(0.37, 0.31, 0.2, 0.85)', 'z-index': '100', 'will-change':'transform'});
		        			setTimeout(function() { 
		        				$indexRow.css({'transform': 'scale(0.77) translateZ(0)', 'opacity': '0'});
		        			}, 60);

		        			$nextIndexRow.css({'z-index':'1000','top':'0','left':'0'});
		        			$nextIndexRowFpTable.css({'transform':'translateY(100%) translateZ(0)', 'will-change':'transform'});
		        			$nextIndexRowInner.css({'transform':'translateY(-50%) translateZ(0)',  'will-change':'transform'});
	        			} 

	        		}

	        		//scrolling up
	        		else {

	        			if($fpAnimation == 'parallax') {
		        			$indexRow.css({'transition': $transformProp+' '+$fpAnimationSpeed+'ms cubic-bezier(.29,.23,.13,1)', 'z-index': '100', 'will-change':'transform'});
		        			setTimeout(function() { 
		        				$indexRow.css({'transform': 'translateY(50%) translateZ(0)'});
		        			}, 60);

		        			$nextIndexRow.css({'z-index':'1000','top':'0','left':'0'});
		        			$nextIndexRowFpTable.css({'transform':'translateY(-100%) translateZ(0)','will-change':'transform'});
		        			$nextIndexRowInner.css({'transform':'translateY(50%) translateZ(0)','will-change':'transform'});
	        			}

	        			else if($fpAnimation == 'zoom-out-parallax') {
		        			$indexRow.css({'transition': 'opacity '+$fpAnimationSpeed+'ms cubic-bezier(0.37, 0.31, 0.2, 0.85), transform '+$fpAnimationSpeed+'ms cubic-bezier(0.37, 0.31, 0.2, 0.85)', 'z-index': '100', 'will-change':'transform'});
		        			setTimeout(function() { 
		        				$indexRow.css({'transform': 'scale(0.77) translateZ(0)', 'opacity': '0'});
		        			}, 60);

		        			$nextIndexRow.css({'z-index':'1000','top':'0','left':'0'});
		        			$nextIndexRowFpTable.css({'transform':'translateY(-100%) translateZ(0)', 'will-change':'transform'});
		        			$nextIndexRowInner.css({'transform':'translateY(50%) translateZ(0)', 'will-change':'transform'});
	        			} 

	        		}
	        		
	        		setTimeout(function() { 
	    				$nextIndexRowFpTable.css({'transition':$transformProp+' '+$fpAnimationSpeed+'ms cubic-bezier(.29,.23,.13,1) 0ms', 'transform':'translateY(0%) translateZ(0)'});
	    				if($fpAnimation != 'none') { 
									$nextIndexRowInner.css({'transition':$transformProp+' '+$fpAnimationSpeed+'ms cubic-bezier(.29,.23,.13,1) 0ms', 'transform':'translateY(0%) translateZ(0)'});
									if(navigator.userAgent.indexOf('Firefox') != -1) {
										requestAnimationFrame(heyFirefoxDrawTheEl);
									}
							}
							
							

							
	    			},60);

	        	}

	        	//adjust transform if larger than row height for parallax
	        	if($('#nectar_fullscreen_rows[data-animation="none"]').length == 0 && $nextIndexRow.find('.fp-scrollable').length > 0) {
	        		$nextIndexRow.find('.full-page-inner-wrap-outer').css('height',overallHeight);
						}

	        	setTimeout(function() { 
	        		
	        		if( $row_id == 'footer-outer') {

		        		$indexRow.css('transform','translateY(-'+($('#footer-outer').height()-1)+'px)');

		        		$('#footer-outer').css({'transform': 'translateY(45%) translateZ(0)'});
		        		$('#footer-outer').css({'transition-duration': '0s', 'backface-visibility': 'hidden'});
		        		setTimeout(function() { 
		        			$('#footer-outer').css({'transition': $transformProp+' 500ms cubic-bezier(0.60, 0.23, 0.2, 0.93)', 'backface-visibility': 'hidden'});
	        				$('#footer-outer').css({'transform': 'translateY(0%) translateZ(0)'});
	        			},30);
		        	}
	        	},30);

	        	if($row_id!='footer-outer') {
	        		
	        		stopMouseParallax();

	        		//take care of nav/control coloring
	        		setFPNavColoring(nextIndex,direction);
							
							//handle main nav link highlight
							setTimeout(function(){
								FPActiveMenuItems(nextIndex);
							},50);
							
							
	        	}
	        		
	        },

	        afterResize: function(){
	        	overallHeight = $('#nectar_fullscreen_rows').height();
	        	overallWidth = $(window).width();
	        	fullHeightRowOverflow();
	        	fullscreenElementSizing();
	        	fullscreenFooterCalcs();

	        	if( $('#footer-outer.active').length > 0) {
	        		setTimeout(function(){
	        			$('.last-before-footer').css('transform','translateY(-'+$('#footer-outer').height()+'px)');
	        		},200);
		        } 

		        //fix for svg animations when resizing and iscroll wraps/unwraps
		        clearTimeout($svgResizeTimeout);
		        $svgResizeTimeout = setTimeout(function(){ 

		        	if($svg_icons.length > 0) {
			        	$('.svg-icon-holder.animated-in').each(function(i){
									$(this).css('opacity','1');
									$svg_icons[$(this).find('svg').attr('id').slice(-1)].finish();
								});
			        }

		         },300);
	        }

		});
	}
	
	if(window.vc_iframe) {
		
		//do nothing
	
	}	 else {
		if($('#nectar_fullscreen_rows').length > 0)
			initNectarFP();
	}


	$(window).smartresize(function(){
		
		if($('#nectar_fullscreen_rows').length > 0) {
			setTimeout(function(){
				$('.wpb_row:not(.last-before-footer) .fp-scrollable').each(function(){
					var $scrollable = $(this).data('iscrollInstance');
					$scrollable.refresh();
				});
			},200);

			fullHeightRowOverflow();

		}
	});

	function fullscreenFooterCalcs() {
		if($('#footer-outer.active').length > 0) {
	    		$('.last-before-footer').addClass('fp-notransition').css('transform','translateY(-'+$('#footer-outer').height()+'px)');
	    		setTimeout(function(){
	    			$('.last-before-footer').removeClass('fp-notransition');
	    		},10);
	    	}
	}



	function stopMouseParallax(){
		$.each($mouseParallaxScenes,function(k,v){
			v.parallax('disable');
		});
	}

	function startMouseParallax(){
		if($('#nectar_fullscreen_rows > .wpb_row.active .nectar-parallax-scene').length > 0) {
			$.each($mouseParallaxScenes,function(k,v){
				v.parallax('enable');
			});
		}
	}

	if($('#nectar_fullscreen_rows').length > 0) {
		
		if(window.vc_iframe) { 
			setTimeout(function(){
				setFPNavColoring(1,'na'); 
			},500);
		}
		else {
			setFPNavColoring(1,'na');
		}
		
		fullscreenElementSizing();
		
		//slide out right OCM material nav compat
		if($('body[data-slide-out-widget-area-style="slide-out-from-right"].material').length > 0) {
			$('#slide-out-widget-area .off-canvas-menu-container').find("a[href*='#']").on('click',function(e){
					
					var $link_hash = $(this).prop("hash");	

					if($link_hash != '#' && $link_hash.indexOf("#") != -1 && $('div[data-fullscreen-anchor-id="'+$link_hash.substr($link_hash.indexOf("#")+1)+'"]').length > 0) {
						
							if($('body.material-ocm-open').length > 0) {
									
									$('body > .slide_out_area_close').addClass('non-human-allowed').trigger('click');

									setTimeout(function(){
										$('body > .slide_out_area_close').removeClass('non-human-allowed');
									},100);

							} 
							
					} // if a section has been found
					
			}); //click
		}
		
		
	}
	
	function FPActiveMenuItems(index) {
		
		if(!$('#nectar_fullscreen_rows[data-anchors="on"]').length > 0 || !index) return;
		
		var $hash = window.location.hash;
		var $hashSubstrng = ($hash && $hash.length > 0) ? $hash.substring(1,$hash.length) : '';

		 if($('body:not(.mobile) #header-outer[data-has-menu="true"]').length > 0 && $('#nectar_fullscreen_rows > .wpb_row:nth-child('+index+')[data-fullscreen-anchor-id]').length > 0 && $('header#top nav > ul.sf-menu > li').find('> a[href$="#'+$hashSubstrng+'"]').length > 0 ) {
			  $('header#top nav > ul.sf-menu > li').removeClass('current-menu-item');
				$('header#top nav > ul.sf-menu > li').find('> a[href$="'+$hashSubstrng+'"]').parent().addClass('current-menu-item');
		 } 
	}

	function resetWaypoints() {
		//animated columns / imgs
		$('img.img-with-animation.animated-in:not([data-animation="none"])').css({'transition':'none'});
		$('img.img-with-animation.animated-in:not([data-animation="none"])').css({'opacity':'0','transform':'none'}).removeClass('animated-in');
		$('.col.has-animation.animated-in:not([data-animation*="reveal"]), .wpb_column.has-animation.animated-in:not([data-animation*="reveal"])').css({'transition':'none'});
		$('.col.has-animation.animated-in:not([data-animation*="reveal"]), .wpb_column.has-animation.animated-in:not([data-animation*="reveal"]), .nectar_cascading_images .cascading-image:not([data-animation="none"]) .inner-wrap').css({'opacity':'0','transform':'none','left':'auto','right':'auto'}).removeClass('animated-in');	
		$('.col.has-animation.boxed:not([data-animation*="reveal"]), .wpb_column.has-animation.boxed:not([data-animation*="reveal"])').addClass('no-pointer-events');
		
		//row BG animations
		$('.row-bg-wrap[data-bg-animation]:not([data-bg-animation="none"]):not([data-bg-animation*="displace-filter"]) .inner-wrap.using-image').removeClass('animated-in');
		$('.column-image-bg-wrap[data-bg-animation]:not([data-bg-animation="none"]):not([data-bg-animation*="displace-filter"]) .inner-wrap').removeClass('animated-in');
		
		//reveal columns
		$('.wpb_column.has-animation[data-animation*="reveal"], .nectar_cascading_images').removeClass('animated-in');
		if(overallWidth > 1000 && $('.using-mobile-browser').length == 0) {
			$('.wpb_column.has-animation[data-animation="reveal-from-bottom"] > .column-inner-wrap').css({'transition':'none','transform':'translate(0, 100%)'});
			$('.wpb_column.has-animation[data-animation="reveal-from-bottom"] > .column-inner-wrap > .column-inner').css({'transition':'none','transform':'translate(0, -90%)'});
			$('.wpb_column.has-animation[data-animation="reveal-from-top"] > .column-inner-wrap').css({'transition':'none','transform':'translate(0, -100%)'});
			$('.wpb_column.has-animation[data-animation="reveal-from-top"] > .column-inner-wrap > .column-inner').css({'transition':'none','transform':'translate(0, 90%)'});
			$('.wpb_column.has-animation[data-animation="reveal-from-left"] > .column-inner-wrap').css({'transition-duration':'0s','transform':'translate(-100%, 0)'});
			$('.wpb_column.has-animation[data-animation="reveal-from-left"] > .column-inner-wrap > .column-inner').css({'transition-duration':'0s','transform':'translate(90%, 0)'});
			$('.wpb_column.has-animation[data-animation="reveal-from-right"] > .column-inner-wrap').css({'transition-duration':'0s','transform':'translate(100%, 0)'});
			$('.wpb_column.has-animation[data-animation="reveal-from-right"] > .column-inner-wrap > .column-inner').css({'transition-duration':'0s','transform':'translate(-90%, 0)'});
		}
		$('.wpb_column.has-animation[data-animation*="reveal"] > .column-inner-wrap, .wpb_column.has-animation[data-animation*="reveal"] > .column-inner-wrap > .column-inner').removeClass('no-transform');

		//vcwaypoints
		$('.wpb_animate_when_almost_visible.animated').removeClass('wpb_start_animation').removeClass('animated');

		//column borders
		$('.wpb_column[data-border-animation="true"] .border-wrap.animation').removeClass('animation').removeClass('completed');

		//milestone
		$('.nectar-milestone.animated-in').removeClass('animated-in').removeClass('in-sight');
		$('.nectar-milestone .symbol').removeClass('in-sight');

		//fancy ul
		$('.nectar-fancy-ul[data-animation="true"]').removeClass('animated-in');
		$('.nectar-fancy-ul[data-animation="true"] ul li').css({'opacity':'0','left':'-20px'});

		//progress bars
		$('.nectar-progress-bar').parent().removeClass('completed');
		$('.nectar-progress-bar .bar-wrap > span').css({'width':'0px'});
		$('.nectar-progress-bar .bar-wrap > span > strong').css({'opacity':'0'});
		//$('.nectar-progress-bar .bar-wrap').css({'opacity':'0'});

		//clients
		$('.clients.fade-in-animation').removeClass('animated-in');
		$('.clients.fade-in-animation > div').css('opacity','0');

		//carousel
		$('.owl-carousel[data-enable-animation="true"]').removeClass('animated-in');
		$('.owl-carousel[data-enable-animation="true"] .owl-stage > .owl-item').css({'transition':'none','opacity':'0','transform':'translate(0, 70px)'});
		//dividers
		$('.divider-small-border[data-animate="yes"], .divider-border[data-animate="yes"]').removeClass('completed').css({'transition':'none','transform':'scale(0,1)'});

		//icon list
		$('.nectar-icon-list').removeClass('completed');
		$('.nectar-icon-list-item').removeClass('animated');

		//portfolio
		$('.portfolio-items .col').removeClass('animated-in');

		//split line
		$('.nectar-split-heading').removeClass('animated-in');
		$('.nectar-split-heading .heading-line > div').transit({'y':'200%'},0);

		//image with hotspots
		$('.nectar_image_with_hotspots[data-animation="true"]').removeClass('completed');
		$('.nectar_image_with_hotspots[data-animation="true"] .nectar_hotspot_wrap').removeClass('animated-in');

		//animated titles
		$('.nectar-animated-title').removeClass('completed');
		
		//highlighted text
		$('.nectar-highlighted-text em').removeClass('animated');

		if($('.vc_pie_chart').length > 0) {
			vc_pieChart();
		}

		$('.col.has-animation:not([data-animation*="reveal"]), .wpb_column.has-animation:not([data-animation*="reveal"])').each(function(i) {
		   	//clear previous timeout (needed for fullscreen rows)
			clearTimeout($standAnimatedColTimeout[i]); 
		});

	}

	

} else if($('#nectar_fullscreen_rows').length > 0 && $disableFPonMobile == 'on' || $().fullpage && $disableFPonMobile == 'on') {

	//remove markup
	$('html,body').css({'height':'auto','overflow-y':'auto'});
}


function nectarLiquidBGFP(){
	
	$('.nectar-liquid-bg').removeClass('animated-in');
	
	for(var k=0; k<$liquidBG_EL.length;k++) {
		if($liquidBG_EL[k].animationType == 'displace-filter-fade' && $($liquidBG_EL[k].canvasContainer).parents('.fp-section.active').length > 0) {
			
			//add bg to container
			if($($liquidBG_EL[k].canvasContainer).find('.image-added-to-stage').length == 0) {
				$liquidBG_EL[k].imgContainer.addChild( $liquidBG_EL[k].bg );
			}
			$($liquidBG_EL[k].canvasContainer).find('.nectar-liquid-bg').addClass('image-added-to-stage');
			
			$liquidBG_EL[k].animateProps($liquidBG_EL[k]);

		}
	}
}


/***************** Superfish ******************/
	
	function initSF(){

		if($('body[data-header-format="left-header"]').length == 0) {


			var $disableHI = ($('body[data-dropdown-style="minimal"]').length > 0 && !($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0) ) ? true : false;

			$(".sf-menu").superfish({
				 delay: 650,
				 speed: 'fast',
				 disableHI: $disableHI,
				 speedOut:      'fast',             
				 animation:   {opacity:'show'}
			}); 

			//handle initial show on left class for minimal styling
			$('#header-outer .sf-menu > li:not(.megamenu) > ul > li > ul').each(function(){
				
				if($(this).offset().left + $(this).outerWidth() > $(window).width()) {
					$(this).addClass('on-left-side');
					$(this).find('ul').addClass('on-left-side');
				} 
				
			});
			

			//megamenu multi section per column title support
			$('body:not([data-header-format="left-header"]) header#top nav > ul > li.megamenu > ul > li > ul > li:has("> ul")').addClass('has-ul'); 

			//fullwidth megamenu
			if($('body[data-megamenu-width="full-width"]').length > 0 && $('ul.sub-menu').length > 0) {
				megamenuFullwidth();
				$(window).on('smartresize',megamenuFullwidth);
				$('header#top nav > ul > li.megamenu > .sub-menu').css('box-sizing','content-box');
			}

			//extra hover class for megamenu check
			$('header#top nav > ul.sf-menu > li').on('mouseenter',function(){
				$(this).addClass('menu-item-over');
			});
			$('header#top nav > ul.sf-menu > li').on('mouseleave',function(){
				$(this).removeClass('menu-item-over');
			});

			//remove arrows on mega menu item
			$('header#top nav .megamenu .sub-menu a.sf-with-ul .sf-sub-indicator, header#top .megamenu .sub-menu a .sf-sub-indicator').remove();

			//blank title megamenu columns
			////desktop
			$('header#top nav > ul > li.megamenu > ul.sub-menu > li > a').each(function(){
				if( $(this).text() == '–' ) {
					$(this).remove();
				}
			});

		
		}
		
	}

	
	function megamenuFullwidth() {
		var $windowWidth = $(window).width();
		var $headerContainerWidth = $('header#top > .container').width();
		$('header#top nav > ul > li.megamenu > .sub-menu').css({
			'padding-left' : ($windowWidth - $headerContainerWidth)/2 + 'px',
			'padding-right' : ($windowWidth+2 - $headerContainerWidth)/2 + 'px',
			'width' : $headerContainerWidth,
			'left' : '-' + ($windowWidth - $headerContainerWidth)/2 + 'px'
		});
	}
	
	var $navLeave;
	
	function addOrRemoveSF(){
		
		if( window.innerWidth < 1000 && $('body').attr('data-responsive') == '1'){
			$('body').addClass('mobile');
			$('header#top nav').css('display','none');
		}
		
		else {
			$('body').removeClass('mobile');
			$('header#top nav').css('display','');
			$('#mobile-menu').hide();
			$('.slide-out-widget-area-toggle #toggle-nav .lines-button').removeClass('close');
			
			//recalc height of dropdown arrow
			$('.sf-sub-indicator').css('height',$('a.sf-with-ul').height());
		}

		//add specific class if on device for better tablet tracking
		if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) { $('body').addClass('using-mobile-browser'); }

	}

	function showOnLeftSubMenu() {
		//show on left class for minimal styling
		$('#header-outer .sf-menu > li:not(.megamenu) > ul > li > ul').each(function(){
			
			$(this).removeClass('on-left-side');

			if($(this).offset().left + $(this).outerWidth() > $(window).width()) {
				$(this).addClass('on-left-side');
				$(this).find('ul').addClass('on-left-side');
			} else {
				$(this).removeClass('on-left-side');
				$(this).find('ul').removeClass('on-left-side');
			}
			
		});
	}
	
	addOrRemoveSF();
	initSF();
	
	$(window).resize(addOrRemoveSF);

	
	function SFArrows(){

		//set height of dropdown arrow
		$('.sf-sub-indicator').css('height',$('a.sf-with-ul').height());
	}
	
	SFArrows();
	

	//deactivate hhun on phone
	if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/))
		$('body').attr('data-hhun','0');


/***************** Caroufredsel ******************/
	
	function standardCarouselInit() {
		$('ul.carousel:not(".clients")').each(function(){
	    	var $that = $(this); 
	    	var maxCols = ($(this).parents('.carousel-wrap').attr('data-full-width') == 'true') ? 'auto' : 3 ;
	    	var scrollNum = ($(this).parents('.carousel-wrap').attr('data-full-width') == 'true') ? 'auto' : '' ;
	    	var colWidth = ($(this).parents('.carousel-wrap').attr('data-full-width') == 'true') ? 500 : 453 ;
	    	var scrollSpeed, easing;
	    	var $autoplayBool = ($(this).attr('data-autorotate') == 'true') ? true : false;
			
			if($('body.ascend').length > 0 && $(this).parents('.carousel-wrap').attr('data-full-width') != 'true' || $('body.material').length > 0 && $(this).parents('.carousel-wrap').attr('data-full-width') != 'true') {
				if($(this).find('li').length % 3 === 0) {
					var $themeSkin = true;
					var $themeSkin2 = true;
				} else {
					var $themeSkin = false;
					var $themeSkin2 = true;
				}
	
			} else {
				var $themeSkin = true;
				var $themeSkin2 = true;
			}

			(parseInt($(this).attr('data-scroll-speed'))) ? scrollSpeed = parseInt($(this).attr('data-scroll-speed')) : scrollSpeed = 700;
			($(this).is('[data-easing]')) ? easing = $(this).attr('data-easing') : easing = 'linear';
			
			
			var $element = $that;
			if($that.find('img').length == 0) $element = $('body');
			
			imagesLoaded($element,function(instance){
	
				
		    	$that.carouFredSel({
		    		circular: $themeSkin,
		    		infinite: $themeSkin2,
		    		height : 'auto',
		    		responsive: true,
			        items       : {
						width : colWidth,
				        visible     : {
				            min         : 1,
				            max         : maxCols
				        }
				    },
				    swipe       : {
				        onTouch     : true,
				        onMouse         : true,
				        options      : {
				        	excludedElements: "button, input, select, textarea, .noSwipe",
				        	tap: function(event, target){ if($(target).attr('href') && !$(target).is('[target="_blank"]') && !$(target).is('[rel^="prettyPhoto"]') && !$(target).is('.magnific-popup') && !$(target).is('.magnific')) window.open($(target).attr('href'), '_self'); }
				        },
				        onBefore : function(){
				    		//hover effect fix
				    		$that.find('.work-item').trigger('mouseleave');
				    		$that.find('.work-item .work-info a').trigger('mouseup');
				    	}
				    },
				    scroll: {
				    	items			: scrollNum,
				    	easing          : easing,
			            duration        : scrollSpeed,
			            onBefore	: function( data ) {
			            	
			            	 if($('body.ascend').length > 0 && $that.parents('.carousel-wrap').attr('data-full-width') != 'true' || $('body.material').length > 0 && $that.parents('.carousel-wrap').attr('data-full-width') != 'true') {
			            	 	$that.parents('.carousel-wrap').find('.item-count .total').html(Math.ceil($that.find('> li').length / $that.triggerHandler("currentVisible").length));

			            	 }	
						},
						onAfter	: function( data ) {
			            	 if($('body.ascend').length > 0 && $that.parents('.carousel-wrap').attr('data-full-width') != 'true' || $('body.material').length > 0 && $that.parents('.carousel-wrap').attr('data-full-width') != 'true') {
			            	 	$that.parents('.carousel-wrap').find('.item-count .current').html( $that.triggerHandler('currentPage') +1);
			            	 	$that.parents('.carousel-wrap').find('.item-count .total').html(Math.ceil($that.find('> li').length / $that.triggerHandler("currentVisible").length));

			            	 }	
						}

				    },
			        prev    : {
				        button  : function() {
				           return $that.parents('.carousel-wrap').find('.carousel-prev');
				        }
			    	},
				    next    : {
			       		button  : function() {
				           return $that.parents('.carousel-wrap').find('.carousel-next');
				        }
				    },
				    auto    : {
				    	play: $autoplayBool
				    }
			    }, { transition: true }).animate({'opacity': 1},1300);
			    
			    $that.parents('.carousel-wrap').wrap('<div class="carousel-outer">');

			    if($that.parents('.carousel-wrap').attr('data-full-width') == 'true') $that.parents('.carousel-outer').css('overflow','visible');

			    //add count for non full width ascend skin
			    if($('body.ascend').length > 0 && $that.parents('.carousel-wrap').attr('data-full-width') != 'true' || $('body.material').length > 0 && $that.parents('.carousel-wrap').attr('data-full-width') != 'true') {
					$('<div class="item-count"><span class="current">1</span>/<span class="total">'+($that.find('> li').length / $that.triggerHandler("currentVisible").length) +'</span></div>').insertAfter($that.parents('.carousel-wrap').find('.carousel-prev'));
				}
			    
			    $that.addClass('finished-loading');

			    carouselHeightCalcs();

		     });//images loaded
		     	     
	    });//each

		
    }
    if($('.carousel').length > 0) standardCarouselInit();


    function owlCarouselInit() {
    	//owl
		$('.owl-carousel').each(function(){
			
			$(this).addClass('owl-theme'); //new version removed this
			
			var $that = $(this);
			var $stagePadding = ($(window).width()<1000) ? 0 : parseInt($(this).attr('data-column-padding'));
			var $autoRotateBool = $that.attr('data-autorotate');
			var $autoRotateSpeed = $that.attr('data-autorotation-speed');
			var $owlLoopBool = ($that.is('[data-loop="true"]')) ? true : false;
			
			$(this).owlCarousel({
			      responsive:{
				        0:{
				            items: $(this).attr('data-mobile-cols')
				        },
				        690:{
				            items: $(this).attr('data-tablet-cols')
				        },
				        1000:{
				          items: $(this).attr('data-desktop-small-cols')
				        },
				        1300:{
				            items: $(this).attr('data-desktop-cols')
				        }
				    },
			      /*stagePadding: $stagePadding,*/
			      autoplay: $autoRotateBool,
			      autoplayTimeout: $autoRotateSpeed,
						loop: $owlLoopBool,
						smartSpeed: 350,
			      onTranslate: function(){
			      	$that.addClass('moving');
			      },
			      onTranslated: function(){
			      	$that.removeClass('moving');
			      }

			  });

			$(this).on('changed.owl.carousel', function (event) {
			    if (event.item.count - event.page.size == event.item.index)
			        $(event.target).find('.owl-dots div:last')
			          .addClass('active').siblings().removeClass('active');
			});	

		});	


    }



	function owl_carousel_animate() {
		$($fullscreenSelector+'.owl-carousel[data-enable-animation="true"]').each(function(){

			$owlOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : 'bottom-in-view';

			var $animationDelay = ($(this).is('[data-animation-delay]') && $(this).attr('data-animation-delay').length > 0 && $(this).attr('data-animation') != 'false') ? $(this).attr('data-animation-delay') : 0;

			var $that = $(this);
			var waypoint = new Waypoint({
	 			element: $that,
	 			 handler: function(direction) {

	 			 	if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
					     waypoint.destroy();
					     return;
					}

					setTimeout(function(){
		 			 	$that.find('.owl-stage > .owl-item').each(function(i){
		 			 		var $that = $(this);
							$that.delay(i*200).transition({
								'opacity': '1',
								'y' : '0'
							},600,'easeOutQuart');
						});
						$that.addClass('animated-in');
		 			 },$animationDelay);

					waypoint.destroy();
				},
				offset: $owlOffsetPos

			}); 

		});
	}




    function productCarouselInit() {
		$('.products-carousel').each(function(){
	    	var $that = $(this).find('ul'); 
	    	var maxCols = 'auto';
	    	var scrollNum = 'auto';
	    	var colWidth = ($(this).parents('.full-width-content ').length > 0) ? 400 : 353 ;
			var scrollSpeed = 800;
			var easing = 'easeInOutQuart';
			
			
			var $element = $that;
			if($that.find('img').length == 0) $element = $('body');
			
			//controls on hover
			$(this).append('<a class="carousel-prev" href="#"><i class="icon-salient-left-arrow"></i></a> <a class="carousel-next" href="#"><i class="icon-salient-right-arrow"></i></a>')

			imagesLoaded($element,function(instance){
	
				
		    	$that.carouFredSel({
		    		circular: true,
		    		responsive: true,
			        items       : {
						width : colWidth,
				        visible     : {
				            min         : 1,
				            max         : maxCols
				        }
				    },
				    swipe       : {
				        onTouch     : true,
				        onMouse         : true,
				        options      : {
				        	excludedElements: "button, input, select, textarea, .noSwipe",
				        	tap: function(event, target){ 
				        		if($(target).attr('href') && !$(target).is('[target="_blank"]') && !$(target).hasClass('add_to_wishlist') && !$(target).hasClass('add_to_cart_button') && !$(target).is('[rel^="prettyPhoto"]')) 
				        			window.open($(target).attr('href'), '_self'); 
				        		if($(target).parent().attr('href') && !$(target).parent().is('[target="_blank"]') && !$(target).parent().hasClass('add_to_wishlist') && !$(target).parent().hasClass('add_to_cart_button') && !$(target).parent().is('[rel^="prettyPhoto"]')) 
				        			window.open($(target).parent().attr('href'), '_self'); 
				        	}
				        },
				        onBefore : function(){
				    		//hover effect fix
				    		$that.find('.product-wrap').trigger('mouseleave');
				    		$that.find('.product a').trigger('mouseup');
				    	}
				    },
				    scroll: {
				    	items			: scrollNum,
				    	easing          : easing,
			            duration        : scrollSpeed
				    },
			        prev    : {
				        button  : function() {
				           return $that.parents('.carousel-wrap').find('.carousel-prev');
				        }
			    	},
				    next    : {
			       		button  : function() {
				           return $that.parents('.carousel-wrap').find('.carousel-next');
				        }
				    },
				    auto    : {
				    	play: false
				    }
			    }).animate({'opacity': 1},1300);
			    
			    $that.parents('.carousel-wrap').wrap('<div class="carousel-outer">');
			    
			    $that.addClass('finished-loading');
			    fullWidthContentColumns();
			    $(window).trigger('resize');

		     });//images loaded
				 
		     	     
	    });//each
			
			
			
		
			$wooFlickityCarousels = [];
			$('.nectar-woo-flickity').each(function(i){
				
				var $that = $(this);

				$(this).find('.products > li').each(function(){
					$(this).wrap('<div class="flickity-cell"></div>');
				});
				
				fullWidthSections();
				
				var pageDotsBool = ($that.is('[data-controls="bottom-pagination"]')) ? true : false;
				var arrowsBool = ($that.is('[data-controls="bottom-pagination"]')) ? false : true;
				var $autoplay = ( $that.is('[data-autorotate-speed]') && parseInt( $that.attr('data-autorotate-speed') ) > 800) ? parseInt($that.attr('data-autorotate-speed')) : 5000;
				if(!$that.is('[data-autorotate="true"]')) { $autoplay = false; }
				
				$(this).find('ul').addClass('generate-markup');
				
				$wooFlickityCarousels[i] = $(this).find('ul');
				
				if(arrowsBool == true) {
					$wooFlickityCarousels[i].on( 'ready.flickity', function() {
						var flickityPrv = $that.find('.flickity-prev-next-button.previous').detach();
	 				  var flickityNxt = $that.find('.flickity-prev-next-button.next').detach();
						
						$that.find('.nectar-woo-carousel-top').append(flickityPrv).append(flickityNxt);
				  });
				}
				
				$wooFlickityCarousels[i].flickity({
					 draggable: true,
					 lazyLoad: false,
					 imagesLoaded: true,
					 cellAlign: 'left',
					 groupCells: pageDotsBool,
					 prevNextButtons: arrowsBool,
					 pageDots: pageDotsBool,
					 resize: true,
					 percentPosition: true,
					 setGallerySize: true,
					 wrapAround: true,
					 autoPlay: $autoplay,
					 accessibility: false
				 });
				 
				 
				 //mobile pagination numbers
				 if(arrowsBool == true) {
					 
					 	$that.find('.flickity-prev-next-button').append('<svg width="65px" height="65px" viewBox="0 0 72 72" xmlns="http://www.w3.org/2000/svg"><circle stroke-width="3" fill="none" stroke-linecap="round" cx="33" cy="33" r="28"></circle> <circle class="time" stroke-width="3" fill="none" stroke-linecap="round" cx="33" cy="33" r="28"></circle></svg>');
					 
						var $wooFlickityCount = $('<div class="woo-flickity-count" />');
						$that.append($wooFlickityCount);
						var $wooFlickityData = $wooFlickityCarousels[i].data('flickity');
						
						function updateWooFlickityCount() {
						  var slideNumber = $wooFlickityData.selectedIndex + 1;
						  $wooFlickityCount.text( slideNumber + '/' + $wooFlickityData.slides.length );
						}
						updateWooFlickityCount();
						$wooFlickityCarousels[i].on( 'select.flickity', updateWooFlickityCount );
				 }
				 

				
			});
			
			
    }
		
    if($('.products-carousel').length > 0 || $('.nectar-woo-flickity').length > 0) { productCarouselInit(); }




    
    //fullwidth carousel swipe link fix
    function fwCarouselLinkFix() {
	    var $mousePosStart = 0;
	    var $mousePosEnd = 0;
	    $('.carousel-wrap .portfolio-items .col .work-item .work-info a, .woocommerce .products-carousel ul.products li.product a').mousedown(function(e){
	    	$mousePosStart = e.clientX;
	    });
	    
	    $('.carousel-wrap .portfolio-items .col .work-item .work-info a, .woocommerce .products-carousel ul.products li.product a').mouseup(function(e){
	    	$mousePosEnd = e.clientX;
	    });
	    
	    $('.carousel-wrap .portfolio-items .col .work-item .work-info a, .woocommerce .products-carousel ul.products li.product a').click(function(e){
	    	if(Math.abs($mousePosStart - $mousePosEnd) > 10)  return false;
	    });
	}
	fwCarouselLinkFix();
    
     
	function carouselHeightCalcs(){
		
		//recent work carousel
		$('.carousel.portfolio-items.finished-loading').each(function(){

			var bottomSpace = ($(this).parents('.carousel-wrap').attr('data-full-width') == 'true' && $(this).find('.style-2, .style-3, .style-4').length > 0) ? 0 : 28 ;
			
			var tallestMeta = 0;
			
			$(this).find('> li').each(function(){
				($(this).find('.work-meta').height() > tallestMeta) ? tallestMeta = $(this).find('.work-meta').height() : tallestMeta = tallestMeta;
			});	
    	 
     		$(this).parents('.caroufredsel_wrapper').css({
     			'height' : ($(this).find('.work-item').outerHeight() + tallestMeta + bottomSpace -2) + 'px'
     		});

     		 if($('body.ascend').length > 0 && $(this).parents('.carousel-wrap').attr('data-full-width') != 'true' || $('body.material').length > 0 && $(this).parents('.carousel-wrap').attr('data-full-width') != 'true') {
        	 	$(this).parents('.carousel-wrap').find('.item-count .current').html(Math.ceil(($(this).triggerHandler("currentPosition")+1)/$(this).triggerHandler("currentVisible").length));
        	 	$(this).parents('.carousel-wrap').find('.item-count .total').html(Math.ceil($(this).find('> li').length / $(this).triggerHandler("currentVisible").length));
        	 }	
   	  	});
   	  	
   	  	//standard carousel
   	  	$('.carousel.finished-loading:not(".portfolio-items, .clients"), .caroufredsel_wrapper .products.finished-loading').each(function(){
			
			var tallestColumn = 0;
			
			$(this).find('> li').each(function(){
				($(this).height() > tallestColumn) ? tallestColumn = $(this).height() : tallestColumn = tallestColumn;
			});	

         	$(this).css('height',tallestColumn + 5);
         	$(this).parents('.caroufredsel_wrapper').css('height',tallestColumn + 5);

         	 if($('body.ascend').length > 0 && $(this).parents('.carousel-wrap').attr('data-full-width') != 'true' || $('body.material').length > 0 && $(this).parents('.carousel-wrap').attr('data-full-width') != 'true') {
        	 	$(this).parents('.carousel-wrap').find('.item-count .current').html(Math.ceil(($(this).triggerHandler("currentPosition")+1)/$(this).triggerHandler("currentVisible").length));
        	 	$(this).parents('.carousel-wrap').find('.item-count .total').html(Math.ceil($(this).find('> li').length / $(this).triggerHandler("currentVisible").length));
        	 }	
			
   	  	});
   	  	
	}


	function clientsCarouselInit(){
	     $('.carousel.clients').each(function(){
	    	var $that = $(this);
	    	var columns; 
	    	var $autoRotate = (!$(this).hasClass('disable-autorotate')) ? true : false;
	    	(parseInt($(this).attr('data-max'))) ? columns = parseInt($(this).attr('data-max')) : columns = 5;
	    	if($(window).width() < 690 && $('body').attr('data-responsive') == '1') { columns = 2; $(this).addClass('phone') }
	    	
	    	var $element = $that;
			if($that.find('img').length == 0) $element = $('body');
			
			imagesLoaded($element,function(instance){
	    		
		    	$that.carouFredSel({
			    		circular: true,
			    		responsive: true, 
				        items       : {
							
							height : $that.find('> div:first').height(),
							width  : $that.find('> div:first').width(),
					        visible     : {
					            min         : 1,
					            max         : columns
					        }
					    },
					    swipe       : {
					        onTouch     : true,
					        onMouse         : true
					    },
					    scroll: {
					    	items           : 1,
					    	easing          : 'easeInOutCubic',
				            duration        : '800',
				            pauseOnHover    : true
					    },
					    auto    : {
					    	play            : $autoRotate,
					    	timeoutDuration : 2700
					    }
			    }).animate({'opacity': 1},1300);
			    
			    $that.addClass('finished-loading');

			    $that.parents('.carousel-wrap').wrap('<div class="carousel-outer">');
			     

			    $(window).trigger('resize');
			    
		    
		    });
	
	    });
    }
   if($('.carousel').length > 0)  clientsCarouselInit();
    

    function clientsCarouselHeightRecalc(){

    	var tallestImage = 0;
		  			
    	 $('.carousel.clients.finished-loading').each(function(){
    	 	
    	 	$(this).find('> div').each(function(){
				($(this).height() > tallestImage) ? tallestImage = $(this).height() : tallestImage = tallestImage;
			});	
    	 	
         	$(this).css('height',tallestImage);
         	$(this).parent().css('height',tallestImage);
         });
    }


    //carousel grabbing class
    function carouselfGrabbingClass() {
	    $('body').on('mousedown','.caroufredsel_wrapper, .carousel-wrap[data-full-width="true"] .portfolio-items .col .work-item .work-info a, .woocommerce .products-carousel ul.products li.product a',function(){
	    	$(this).addClass('active');
	    });
	    
	    $('body').on('mouseup','.caroufredsel_wrapper, .carousel-wrap[data-full-width="true"] .portfolio-items .col .work-item .work-info a, .woocommerce .products-carousel ul.products li.product a',function(){
	    	$(this).removeClass('active');
	    });
	}
	carouselfGrabbingClass();
	    

	//ascend arrow hover class
	$('body.ascend, body.material').on('mouseover','.carousel-next',function(){
		$(this).parent().find('.carousel-prev, .item-count').addClass('next-hovered');
	});
	$('body.ascend, body.material').on('mouseleave','.carousel-next',function(){
		$(this).parent().find('.carousel-prev, .item-count').removeClass('next-hovered');
	});

	//fadein for clients / carousels
	function clientsFadeIn() {

		var $clientsOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? 100: 'bottom-in-view';
		$($fullscreenSelector+'.clients.fade-in-animation').each(function() {

			var $that = $(this);
			var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
				
				if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
					 waypoint.destroy();
					return;
				}

				 $that.find('> div').each(function(i){
					$(this).delay(i*80).transition({'opacity':"1"},450);
				});
				
				
				//add the css transition class back in after the aniamtion is done
				setTimeout(function(){ $that.addClass('completed'); },($that.find('> div').length*80) + 450);
			

				$that.addClass('animated-in');
				waypoint.destroy();
			},
			offset: $clientsOffsetPos

			}); 
		}); 
	}
	 
	//if($('.nectar-box-roll').length == 0) clientsFadeIn();
	
	
/*-------------------------------------------------------------------------*/
/*	2.	Helper Functions
/*-------------------------------------------------------------------------*/

	jQuery.fn.setCursorPosition = function(position){
		
	    if(this.length == 0) { 
				return this; 
			}
	    return $(this).setSelection(position, position);
	}
	
	jQuery.fn.setSelection = function(selectionStart, selectionEnd) {
	    if(this.length == 0) { 
				return this;
			}
	    input = this[0];
	
	    if (input.createTextRange) {
	        var range = input.createTextRange();
	        range.collapse(true);
	        range.moveEnd('character', selectionEnd);
	        range.moveStart('character', selectionStart);
	        range.select();
	    } else if (input.setSelectionRange) {
	        input.focus();
	        input.setSelectionRange(selectionStart, selectionEnd);
	    }
	
	    return this;
	}
	
	

	$.extend($.expr[':'], {
	    transparent: function(elem, i, attr){
	      return( $(elem).css("opacity") === "0" );
	    }
	});
	

	function getQueryParams(qs) {
	    qs = qs.split("+").join(" ");
	    var params = {},
	        tokens,
	        re = /[?&]?([^=]+)=([^&]*)/g;

	    while (tokens = re.exec(qs)) {
	        params[decodeURIComponent(tokens[1])]
	            = decodeURIComponent(tokens[2]);
	    }

	    return params;
	}

	var $_GET = getQueryParams(document.location.search);

	
	//count
	var CountUp = function(target, startVal, endVal, decimals, duration, options) {

    // make sure requestAnimationFrame and cancelAnimationFrame are defined
    // polyfill for browsers without native support
    // by Opera engineer Erik Möller
    var lastTime = 0;
    var vendors = ['webkit', 'moz', 'ms', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame =
          window[vendors[x]+'CancelAnimationFrame'] || window[vendors[x]+'CancelRequestAnimationFrame'];
    }
    if (!window.requestAnimationFrame) {
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() { callback(currTime + timeToCall); },
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };
    }
    if (!window.cancelAnimationFrame) {
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
    }

    var self = this;

     // default options
    self.options = {
        useEasing : true, // toggle easing
        useGrouping : true, // 1,000,000 vs 1000000
        separator : ',', // character to use as a separator
        decimal : '.', // character to use as a decimal
        easingFn: null, // optional custom easing closure function, default is Robert Penner's easeOutExpo
        formattingFn: null // optional custom formatting function, default is self.formatNumber below
    };
    // extend default options with passed options object
    for (var key in options) {
        if (options.hasOwnProperty(key)) {
            self.options[key] = options[key];
        }
    }
    if (self.options.separator === '') { self.options.useGrouping = false; }
    if (!self.options.prefix) self.options.prefix = '';
    if (!self.options.suffix) self.options.suffix = '';

    self.d = (typeof target === 'string') ? document.getElementById(target) : target;
    self.startVal = Number(startVal);
    self.endVal = Number(endVal);
    self.countDown = (self.startVal > self.endVal);
    self.frameVal = self.startVal;
    self.decimals = Math.max(0, decimals || 0);
    self.dec = Math.pow(10, self.decimals);
    self.duration = Number(duration) * 1000 || 2000;

    self.formatNumber = function(nStr) {
        nStr = nStr.toFixed(self.decimals);
        nStr += '';
        var x, x1, x2, rgx;
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? self.options.decimal + x[1] : '';
        rgx = /(\d+)(\d{3})/;
        if (self.options.useGrouping) {
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + self.options.separator + '$2');
            }
        }
        return self.options.prefix + x1 + x2 + self.options.suffix;
    };
    // Robert Penner's easeOutExpo
    self.easeOutExpo = function(t, b, c, d) {
        return c * (-Math.pow(2, -10 * t / d) + 1) * 1024 / 1023 + b;
    };

    self.easingFn = self.options.easingFn ? self.options.easingFn : self.easeOutExpo;
    self.formattingFn = self.options.formattingFn ? self.options.formattingFn : self.formatNumber;

    self.version = function () { return '1.7.1'; };

    // Print value to target
    self.printValue = function(value) {
        var result = self.formattingFn(value);

        if (self.d.tagName === 'INPUT') {
            this.d.value = result;
        }
        else if (self.d.tagName === 'text' || self.d.tagName === 'tspan') {
            this.d.textContent = result;
        }
        else {
            this.d.innerHTML = result;
        }
    };

    self.count = function(timestamp) {

        if (!self.startTime) { self.startTime = timestamp; }

        self.timestamp = timestamp;
        var progress = timestamp - self.startTime;
        self.remaining = self.duration - progress;

        // to ease or not to ease
        if (self.options.useEasing) {
            if (self.countDown) {
                self.frameVal = self.startVal - self.easingFn(progress, 0, self.startVal - self.endVal, self.duration);
            } else {
                self.frameVal = self.easingFn(progress, self.startVal, self.endVal - self.startVal, self.duration);
            }
        } else {
            if (self.countDown) {
                self.frameVal = self.startVal - ((self.startVal - self.endVal) * (progress / self.duration));
            } else {
                self.frameVal = self.startVal + (self.endVal - self.startVal) * (progress / self.duration);
            }
        }

        // don't go past endVal since progress can exceed duration in the last frame
        if (self.countDown) {
            self.frameVal = (self.frameVal < self.endVal) ? self.endVal : self.frameVal;
        } else {
            self.frameVal = (self.frameVal > self.endVal) ? self.endVal : self.frameVal;
        }

        // decimal
        self.frameVal = Math.round(self.frameVal*self.dec)/self.dec;

        // format and print value
        self.printValue(self.frameVal);

        // whether to continue
        if (progress < self.duration) {
            self.rAF = requestAnimationFrame(self.count);
        } else {
            if (self.callback) { self.callback(); }
        }
    };
    // start your animation
    self.start = function(callback) {
        self.callback = callback;
        self.rAF = requestAnimationFrame(self.count);
        return false;
    };
    // toggles pause/resume animation
    self.pauseResume = function() {
        if (!self.paused) {
            self.paused = true;
            cancelAnimationFrame(self.rAF);
        } else {
            self.paused = false;
            delete self.startTime;
            self.duration = self.remaining;
            self.startVal = self.frameVal;
            requestAnimationFrame(self.count);
        }
    };
    // reset to startVal so animation can be run again
    self.reset = function() {
        self.paused = false;
        delete self.startTime;
        self.startVal = startVal;
        cancelAnimationFrame(self.rAF);
        self.printValue(self.startVal);
    };
    // pass a new endVal and start animation
    self.update = function (newEndVal) {
        cancelAnimationFrame(self.rAF);
        self.paused = false;
        delete self.startTime;
        self.startVal = self.frameVal;
        self.endVal = Number(newEndVal);
        self.countDown = (self.startVal > self.endVal);
        self.rAF = requestAnimationFrame(self.count);
    };

    // format startVal on initialization
    self.printValue(self.startVal);
};
	
var easeOutCubic = function(t, b, c, d) {
    return c*((t=t/d-1)*t*t + 1) + b;
};	
	
	
/*-------------------------------------------------------------------------*/
/*	3.	Shortcode Stuff
/*-------------------------------------------------------------------------*/


function vcWaypoints() {
	$($fullscreenSelector+' .wpb_animate_when_almost_visible').each(function() {
		  
			var $vcOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '90%';

			var $that = $(this);
			var waypoint = new Waypoint({
	 			element: $that,
	 			 handler: function(direction) {

	 			 	if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated')) { 
					     waypoint.destroy();
					     return;
					}
	 				$that.addClass("animated");
	 				$that.addClass("wpb_start_animation");
	 				waypoint.destroy();
					
					if($that.is('.nectar-button') && $('body[data-button-style*="rounded_shadow"]').length > 0) {
						setTimeout(function(){
							$that.removeClass('wpb_start_animation');
						},1100);
					}
				
				},
				offset: $vcOffsetPos

			}); 

	});
}
			 

/***************** Milestone Counter ******************/
	
	function milestoneInit() {

		$('.nectar-milestone').each(function() {
			
			//symbol
			if($(this).is('[data-symbol]')) {
				if($(this).find('.symbol-wrap').length == 0) {
					if($(this).attr('data-symbol-pos') == 'before') {
						$(this).find('.number').prepend('<div class="symbol-wrap"><span class="symbol">' + $(this).attr('data-symbol') + '</span></div>');
					} else {
						$(this).find('.number').append('<div class="symbol-wrap"><span class="symbol">' + $(this).attr('data-symbol') + '</span></div>');
					}
				}

				var $symbol_size = (  $(this).attr('data-symbol-size') == $(this).find('.number').attr('data-number-size') && $(this).attr('data-symbol-alignment') == 'superscript' ) ? 32 :  parseInt($(this).attr('data-symbol-size'));
			
				$(this).find('.symbol-wrap').css({'font-size': $symbol_size + 'px', 'line-height': $symbol_size + 'px'});
			}

			$(this).find('.number').css({'font-size': $(this).find('.number').attr('data-number-size') +'px', 'line-height': $(this).find('.number').attr('data-number-size') + 'px'});
		});
		
		if(!$('body').hasClass('mobile') && $('.nectar-milestone').length > 0) {
			

			//blur effect
			var $blurCssString = '';
			$($fullscreenSelector+'.nectar-milestone.motion_blur').each(function(i){
				
				$(this).removeClass(function (index, className) {
				    return (className.match (/(^|\s)instance-\S+/g) || []).join(' ');
				});	
					
				$(this).addClass('instance-'+i);

				var $currentColor = $(this).find('.number').css('color');
				var colorInt = parseInt($currentColor.substring(1),16);
		   		var R = (colorInt & 0xFF0000) >> 16;
		    	var G = (colorInt & 0x00FF00) >> 8;
		   		var B = (colorInt & 0x0000FF) >> 0;
		   		
		   		var $rgbaColorStart = 'rgba('+R+','+G+','+B+',0.2)';
				var $rgbaColorEnd = 'rgba('+R+','+G+','+B+',1)';
				var $numberSize = parseInt($(this).find('.number').attr('data-number-size'));

				$blurCssString += '@keyframes motion-blur-number-'+i+' { ' +
				   ' 0% { '+
				   		'opacity: 0;'+
						'color: '+$rgbaColorStart+'; '+
   						'text-shadow: 0 '+$numberSize/20+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/10+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/6+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/5+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/4+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/20+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/10+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/6+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/5+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/4+'px 0 '+$rgbaColorStart+'; '+
    					'transform: translateZ(0px) translateY(-100%); '+
    					'-webkit-transform: translateZ(0px) translateY(-100%); '+
    				'} '+
    				'33% { opacity: 1 }' +
    				'100% { '+
						'color: '+$rgbaColorEnd+'; '+
   						'text-shadow: none; '+
    					'transform: translateZ(0px) translateY(0px); '+
    					'-webkit-transform: translateZ(0px) translateY(0px); '+
    				'} '+
    			'} '+
    			'@-webkit-keyframes motion-blur-number-'+i+' { ' +
				   ' 0% { '+
				  	    'opacity: 0;'+
						'color: '+$rgbaColorStart+'; '+
   						'text-shadow: 0 '+$numberSize/20+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/10+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/6+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/5+'px 0 '+$rgbaColorStart+', 0 '+$numberSize/4+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/20+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/10+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/6+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/5+'px 0 '+$rgbaColorStart+', 0 -'+$numberSize/4+'px 0 '+$rgbaColorStart+'; '+
    					'transform: translateZ(0px) translateY(-100%); '+
    					'-webkit-transform: translateZ(0px) translateY(-100%); '+
    				'} '+
    				'33% { opacity: 1 }' +
    				'100% { '+
						'color: '+$rgbaColorEnd+'; '+
   						'text-shadow: none; '+
    					'transform: translateZ(0px) translateY(0px); '+
    					'-webkit-transform: translateZ(0px) translateY(0px); '+
    				'} '+
    			'} '+
    			'.nectar-milestone.motion_blur.instance-'+i+' .number span.in-sight { animation: 0.65s cubic-bezier(0, 0, 0.17, 1) 0s normal backwards 1 motion-blur-number-'+i+'; -webkit-animation: 0.65s cubic-bezier(0, 0, 0.17, 1) 0s normal backwards 1 motion-blur-number-'+i+'; } ';
    			
    			//separate each character into spans
    			$symbol = $(this).find('.symbol-wrap').clone();
    			$(this).find('.symbol-wrap').remove();
    			var characters = $(this).find('.number').text().split("");
    			$this = $(this).find('.number');
				$this.empty();
    			$.each(characters, function (i, el) {
				    $this.append("<span>" + el + "</span");
				});

				//handle symbol
				if($(this).has('[data-symbol]')) {
	    			if($(this).attr('data-symbol-pos') == 'after') {
	    				$this.append($symbol);
	    			} else {
	    				$this.prepend($symbol);
	    			}
	    		}
				
			});

			var head = document.head || document.getElementsByTagName('head')[0];
				var style = document.createElement('style');

				style.type = 'text/css';
			if (style.styleSheet){
			  style.styleSheet.cssText = $blurCssString;
			} else {
			  style.appendChild(document.createTextNode($blurCssString));
			}
			$(style).attr('id','milestone-blur');
			$('head #milestone-blur').remove();
			head.appendChild(style);


			//activate
			milestoneWaypoint();

		}

	}

	function milestoneWaypoint() {
		$($fullscreenSelector+'.nectar-milestone').each(function() {
			//animation
			
			var $offset = ($('#nectar_fullscreen_rows').length > 0) ? '250%' : '98%';

			var $that = $(this);
			var waypoint = new Waypoint({
	 			element: $that,
	 			 handler: function(direction) {

	 			 	if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
					     waypoint.destroy();
					     return;
					}

	 			 	var $endNum = parseInt($that.find('.number span:not(.symbol)').text().replace(/,/g , ''));

					if(!$that.hasClass('motion_blur')) {

						var countOptions = { easingFn: easeOutCubic };
						var $countEle = $that.find('.number span:not(.symbol)')[0];
						var numAnim = new CountUp($countEle, 0, $endNum,0,2.2,countOptions);
						numAnim.start();

					} else {
						$that.find('span').each(function(i){
							var $that = $(this);
							setTimeout(function(){ $that.addClass('in-sight'); },200*i);
						});
					}


					$that.addClass('animated-in');
					waypoint.destroy();
				},
				offset: $offset

			}); 

		}); 
	}

	var $animationOnScrollTimeOut = ($('.nectar-box-roll').length > 0) ? 850: 125;	

	
/***************** Tabbed ******************/
	
	var $tabbedClickCount = 0;
	$('body').on('click','.tabbed > ul li:not(.cta-button) a',function(){
		var $id = $(this).parents('li').index()+1;
		
		var $frontEndEditorTabDiv =  ($('body.vc_editor').length > 0) ? '> .wpb_tab ': '';
		
		if(!$(this).hasClass('active-tab') && !$(this).hasClass('loading')){
			$(this).parents('ul').find('a').removeClass('active-tab');
			$(this).addClass('active-tab');
			
			$(this).parents('.tabbed').find('> div:not(.clear)' + $frontEndEditorTabDiv).css({'visibility':'hidden','position':'absolute','opacity':'0','left':'-9999px'});
			
			if($('body.vc_editor').length > 0) {
				//front end editor locate tab by modal id
				var $data_m_id = ($(this).parent().is('[data-m-id]')) ? $(this).parent().attr('data-m-id') : '';
				$(this).parents('.tabbed').find('> div[data-model-id="'+$data_m_id+'"]' + $frontEndEditorTabDiv).css({'visibility':'visible', 'position' : 'relative','left':'0','display':'block'}).stop().transition({'opacity':1},400);
				//update padding
				convertFrontEndPadding();
				
			} else {
				$(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).css({'visibility':'visible', 'position' : 'relative','left':'0','display':'block'}).stop().transition({'opacity':1},400);
			}
			
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+') .iframe-embed').length > 0 || $(this).parents('.tabbed').find('> div:nth-of-type('+$id+') .portfolio-items').length > 0) setTimeout(function(){ $(window).resize(); },10); 
		}
		
		//waypoint checking
		if($tabbedClickCount != 0) {
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-progress-bar').length > 0 ) 
				progressBars();
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.divider-small-border [data-animate="yes"]').length > 0 || $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')').find('.divider-border [data-animate="yes"]').length > 0 ) 
				dividers();
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('img.img-with-animation').length > 0 ||
			   $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.col.has-animation').length > 0  || 
			   $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar_cascading_images').length > 0  || 
			   $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.wpb_column.has-animation').length > 0 ) {
				colAndImgAnimations();
				cascadingImageBGSizing();
			}
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.column-image-bg-wrap[data-bg-animation="displace-filter-fade"]').length > 0){
				for(var k=0; k<$liquidBG_EL.length;k++) {
					if($($liquidBG_EL[k].canvasContainer).parents('.wpb_tab').length > 0 && $($liquidBG_EL[k].canvasContainer).parents('.wpb_tab').css('visibility') != 'hidden') {
						
						//add bg to container
						if($($liquidBG_EL[k].canvasContainer).find('.image-added-to-stage').length == 0) {
							$liquidBG_EL[k].imgContainer.addChild( $liquidBG_EL[k].bg );
						}
						$($liquidBG_EL[k].canvasContainer).find('.nectar-liquid-bg').addClass('image-added-to-stage');
						
						//resize calcs
						$liquidBG_EL[k].resize();
						
						//animate
						if($($liquidBG_EL[k].canvasContainer).find('.nectar-liquid-bg.animated-in').length == 0) {
							$liquidBG_EL[k].animateProps($liquidBG_EL[k]);
						}
			
					}
				}
			}
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-milestone').length > 0 ) 
				milestoneWaypoint();
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar_image_with_hotspots[data-animation="true"]').length > 0 ) 
				imageWithHotspots();
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-fancy-ul').length > 0 ) 
				nectar_fancy_ul_init();
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-split-heading').length > 0 ) 
				splitLineHeadings();
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.wpb_column[data-border-animation="true"]').length > 0) {
				animatedColBorders();
			}
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.wpb_animate_when_almost_visible').length > 0) {
				vcWaypoints();
			}
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-animated-title').length > 0) {
				animated_titles();
			}
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-highlighted-text').length > 0) {
				highlighted_text();
			}
			if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar_food_menu_item').length > 0) {
				foodMenuItems();
			}
			if($(this).parents('.wpb_row').length > 0) {
				if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.vc_pie_chart').length > 0  ||
				   $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.wp-video-shortcode').length > 0 ||
					 $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.post-area.masonry .posts-container').length > 0 ||
				   $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.twentytwenty-container').length > 0 ||
					 $(this).parents('#nectar_fullscreen_rows[data-content-overflow="scrollbar"]').length > 0 ||
					 $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')').find('.wpb_gallery').length > 0 ||
				   $(this).parents('.wpb_row').next().hasClass('parallax_section'))
					$(window).trigger('resize');
					
					
				if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-flickity').length > 0 && typeof Flickity != 'undefined' ) {
					var tabbedFlkty = Flickity.data( $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')').find('.nectar-flickity')[0] );
					tabbedFlkty.resize();
				}
				
				if($(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.nectar-woo-flickity').length > 0 && typeof Flickity != 'undefined' ) {
					var wootabbedFlkty = Flickity.data( $(this).parents('.tabbed').find('> div:nth-of-type('+$id+')').find('.nectar-woo-flickity > ul')[0] );
					wootabbedFlkty.resize();
				}
				
			}

			//svg icons
		
			$(this).parents('.tabbed').find('> div:nth-of-type('+$id+')' + $frontEndEditorTabDiv).find('.svg-icon-holder').each(function(i){
				var $that = $(this);

				setTimeout(function(){

					var $animationDelay = ($(this).is('[data-animation-delay]') && $(this).attr('data-animation-delay').length > 0 && $(this).attr('data-animation') != 'false') ? $(this).attr('data-animation-delay') : 0;

					clearTimeout($animatedSVGIconTimeout[i]);

					if($that.attr('data-animation') == 'false') { 
						$animationSpeed = 1;
						$that.css('opacity','1');
						$svg_icons[$that.find('svg').attr('id').slice(-1)].finish();
					} else {
						
						$svg_icons[$that.find('svg').attr('id').slice(-1)].reset();
						$animatedSVGIconTimeout[i] = setTimeout(function(){ $svg_icons[$that.find('svg').attr('id').slice(-1)].play(); },$animationDelay);
						
					}
				},150);
			});

		}

		//fix columns inside tabs
		$(this).parents('.tabbed').find('.wpb_row').each(function(){
			if( typeof $(this).find('[class*="vc_col-"]').first().offset() != 'undefined') {
				
				var $firstChildOffset = $(this).find('[class*="vc_col-"]').first().offset().left;
				$(this).find('[class*="vc_col-"]').each(function(){
					$(this).removeClass('no-left-margin');
					if($(this).offset().left < $firstChildOffset + 15) { 
						$(this).addClass('no-left-margin');
					} else {
						$(this).removeClass('no-left-margin');
					}
				});
			}
		});

		$tabbedClickCount++;

		//magic line
		if($(this).parent().parent().find('.magic-line').length > 0) {
			magicLineCalc($(this));
	     }

		return false;
	});


	function magicLineCalc($ele) {
		var el, leftPos, ratio;
	    el = $ele.parent();
	    if (el.length) {
	        leftPos = el.position().left;
	        ratio = el.width();
	    } else {
	        leftPos = ratio = 0;
	    }

	    $ele.parent().parent().find('.magic-line').css('transform', 'translateX(' + leftPos + 'px) scaleX(' + ratio + ')');
	}
	
	
	function tabbedInit(){ 
		$('.tabbed').each(function(){
			
			//handle icons
			$(this).find('.wpb_tab').each(function(i){
				
				if($(this).is('[data-tab-icon]') && $(this).attr('data-tab-icon').length > 0) {
					$(this).parents('.tabbed').addClass('using-icons');
					$(this).parents('.tabbed').find('.wpb_tabs_nav li:nth-child('+ (i+1) +') > a').prepend('<i class="'+$(this).attr("data-tab-icon")+'"></i>');
				}
			});
			
			//make sure the tabs don't have a nectar slider - we'll init this after the sliders load in that case
			if($(this).find('.swiper-container').length == 0 && $(this).find('.testimonial_slider').length == 0 && $(this).find('.portfolio-items:not(".carousel")').length == 0 && $(this).find('.wpb_gallery .portfolio-items').length == 0 && $(this).find('iframe').length == 0){
				$(this).find('> ul li:first-child a').click();
			}	
			if($(this).find('.testimonial_slider').length > 0 || $(this).find('.portfolio-items:not(".carousel")').length > 0 || $(this).find('.wpb_gallery .portfolio-items').length > 0 || $(this).find('iframe').length > 0 ){
				var $that = $(this);
				
				$(this).find('.wpb_tab').show().css({'opacity':0,'height':'1px'});
				$(this).find('> ul li a').addClass('loading');
				
				setTimeout(function(){ 
					$that.find('.wpb_tab').hide().css({'opacity':1,'height':'auto'}); 
					$that.find('> ul li a').removeClass('loading');
					$that.find('> ul li:first-child a').click(); 
				},900);
			}

			var $that = $(this);
			//minimal alt effect
			setTimeout(function(){
				if($that.is('[data-style="minimal_alt"]')) {
					$that.find('> ul').append('<li class="magic-line" />');
					 magicLineCalc($that.find('> ul > li:first-child > a'));
				}
			},100);


		});
	}
	setTimeout(tabbedInit,60);

	//deep linking
	function tabbbedDeepLinking(){
		if(typeof $_GET['tab'] != 'undefined'){
			$('.wpb_tabs_nav').each(function(){

				$(this).find('li').each(function(){
					var $currentText = $(this).find('a').text();
					var $getText = $_GET['tab'];
					var $that = $(this);

					$currentText = $currentText.replace(/\s+/g, '-').toLowerCase();
					$getText = $getText.replace(/\s+/g, '-').replace(/</g, '&lt;').replace(/"/g, '&quot;').toLowerCase();

					if($currentText == $getText)  { 

			          $(this).find('a').click(); 
			           setTimeout(function(){ 
			              $that.find('a').click(); 
			           },901);
			        }
				})
			});
		}
	}
	tabbbedDeepLinking();

/***************** Toggle ******************/
	
	//toggles
	$('body').on('click','.toggle h3 a', function(){
	
		if(!$(this).parents('.toggles').hasClass('accordion')) { 
			$(this).parents('.toggle').find('> div').slideToggle(300);
			$(this).parents('.toggle').toggleClass('open');
			
			//switch icon
			if( $(this).parents('.toggle').hasClass('open') ){
				$(this).find('i').attr('class','icon-minus-sign');
			} else {
				$(this).find('i').attr('class','icon-plus-sign');
			}

			if($(this).parents('.toggle').find('> div .iframe-embed').length > 0 && $(this).parents('.toggle').find('> div iframe.iframe-embed').height() == '0') responsiveVideoIframes();
			if($(this).parents('.full-width-content').length > 0) setTimeout(function(){ fullWidthContentColumns(); },300);
			if($('#nectar_fullscreen_rows').length > 0) setTimeout(function(){ $(window).trigger('smartresize'); },300);
			return false;
		}
	});
	
	//accordion
	$('body').on('click','.accordion .toggle h3 a', function(){
		
		if($(this).parents('.toggle').hasClass('open')) return false;
		
		$(this).parents('.toggles').find('.toggle > div').slideUp(300);
		$(this).parents('.toggles').find('.toggle h3 a i').attr('class','icon-plus-sign');
		$(this).parents('.toggles').find('.toggle').removeClass('open');
		
		$(this).parents('.toggle').find('> div').slideDown(300);
		$(this).parents('.toggle').addClass('open');
		
		//switch icon
		if( $(this).parents('.toggle').hasClass('open') ){
			$(this).find('i').attr('class','icon-minus-sign');
		} else {
			$(this).find('i').attr('class','icon-plus-sign');
		}

		if($(this).parents('.full-width-content').length > 0) { 
			clearTimeout($t);
			var $t = setTimeout(function(){ fullWidthContentColumns(); },400);
		}
		if($('#nectar_fullscreen_rows').length > 0) {
			clearTimeout($t);
			var $t = setTimeout(function(){ $(window).trigger('smartresize'); },400);
		}
		
		return false;
	});
	
	//accordion start open
	function accordionInit(){ 
		$('.accordion').each(function(){
			$(this).find('> .toggle').first().addClass('open').find('> div').show();
			$(this).find('> .toggle').first().find('a i').attr('class','icon-minus-sign');
		});
		
		
		$('.toggles').each(function(){
			
			var $isAccordion = ($(this).hasClass('accordion')) ? true : false;
			
			$(this).find('.toggle').each(function(){
				if($(this).find('> div .testimonial_slider').length > 0 || $(this).find('> div iframe').length > 0) {
					var $that = $(this);
					$(this).find('> div').show().css({'opacity':0,'height':'1px', 'padding':'0'});
					
					testimonialHeightResize();
					
					setTimeout(function(){
						$that.find('> div').hide().css({'opacity':1,'height':'auto', 'padding':'10px 14px'}); 
						if($isAccordion == true && $that.index() == 0) $that.find('> div').slideDown(300);
					},900);
				} 
			});
		})
	}
	accordionInit();

	//deep linking
	function accordionDeepLinking(){
		if(typeof $_GET['toggle'] != 'undefined'){
			$('.toggles').each(function(){

				$(this).find('.toggle').each(function(){
					var $currentText = $(this).find('h3 a').clone();
					var $getText = $_GET['toggle'];

					$($currentText).find('i').remove();
					$currentText = $currentText.text();
					$currentText = $currentText.replace(/\s+/g, '-').toLowerCase();
					$getText = $getText.replace(/\s+/g, '-').replace(/</g, '&lt;').replace(/"/g, '&quot;').toLowerCase();

					if($currentText == $getText) $(this).find('h3 a').click();
				});
			});
		}
	}
	accordionDeepLinking();

/***************** Button ******************/
	
	$.cssHooks.color = {
	    get: function(elem) {
	        if (elem.currentStyle)
	            var color = elem.currentStyle["color"];
	        else if (window.getComputedStyle)
	            var color = document.defaultView.getComputedStyle(elem,
	                null).getPropertyValue("color");
	        if (color.search("rgb") == -1)
	            return color;
	        else {
	            color = color.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	            function hex(x) {
	                return ("0" + parseInt(x).toString(16)).slice(-2);
	            }
	            if(color) {
	            	return "#" + hex(color[1]) + hex(color[2]) + hex(color[3]);
	            }
	        }
	    }
	}

	$.cssHooks.backgroundColor = {
	    get: function(elem) {
	        if (elem.currentStyle)
	            var bg = elem.currentStyle["backgroundColor"];
	        else if (window.getComputedStyle)
	            var bg = document.defaultView.getComputedStyle(elem,
	                null).getPropertyValue("background-color");
	        if (bg.search("rgb") == -1)
	            return bg;
	        else {
	            bg = bg.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
	            function hex(x) {
	                return ("0" + parseInt(x).toString(16)).slice(-2);
	            }
	            if(bg) {
	            	return "#" + hex(bg[1]) + hex(bg[2]) + hex(bg[3]);
	            }
	        }
	    }
	}
	
	function shadeColor(hex, lum) {

	  // validate hex string
		hex = String(hex).replace(/[^0-9a-f]/gi, '');
		if (hex.length < 6) {
			hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
		}
		lum = lum || 0;

		// convert to decimal and change luminosity
		var rgb = "#", c, i;
		for (i = 0; i < 3; i++) {
			c = parseInt(hex.substr(i*2,2), 16);
			c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
			rgb += ("00"+c).substr(c.length);
		}

		return rgb;
	}

	//color
	function coloredButtons() {
		$('.nectar-button.see-through[data-color-override], .nectar-button.see-through-2[data-color-override], .nectar-button.see-through-3[data-color-override]').each(function(){
			
			var $usingMaterialSkin = ($('body.material[data-button-style^="rounded"]').length > 0) ? true : false;
			$(this).css('visibility','visible');
			
			if($(this).hasClass('see-through-3') && $(this).attr('data-color-override') == 'false') {
				return true;
			}
			//if($(this).attr('data-color-override') != 'false'){

				if($(this).attr('data-color-override') != 'false') {
					var $color = $(this).attr('data-color-override') ;
				} else {
					if($(this).parents('.dark').length > 0) 
						var $color = '#000000';
					else 
						var $color = '#ffffff';
				}

				if(!$(this).hasClass('see-through-3')) $(this).css('color',$color);
				$(this).find('i').css('color',$color);
				
				var colorInt = parseInt($color.substring(1),16);
				var $hoverColor = ($(this).has('[data-hover-color-override]')) ? $(this).attr('data-hover-color-override') : 'no-override';
				var $hoverTextColor = ($(this).has('[data-hover-text-color-override]')) ? $(this).attr('data-hover-text-color-override') : '#fff';
				
	   		var R = (colorInt & 0xFF0000) >> 16;
	    	var G = (colorInt & 0x00FF00) >> 8;
	   		var B = (colorInt & 0x0000FF) >> 0;
	   		
	   		var $opacityStr = ($(this).hasClass('see-through-3')) ? '1': '0.75';

				$(this).css('border-color','rgba('+R+','+G+','+B+','+$opacityStr+')');
				
				//material buttons w/ icons starting
				if($usingMaterialSkin) {
					$(this).find('i').css({'background-color': 'rgba('+R+','+G+','+B+',1)', 'box-shadow': '0px 8px 15px rgba('+R+','+G+','+B+',0.24)'});
				}
				
				if($(this).hasClass('see-through')) {
					
					var $that = $(this);
					
					$(this).on('mouseenter touchstart', function(){
						$that.css('border-color','rgba('+R+','+G+','+B+',1)');
					});
					
					$(this).on('mouseleave touchtouchend', function(){
						$that.css('border-color','rgba('+R+','+G+','+B+',1)');
						$opacityStr = ($(this).hasClass('see-through-3')) ? '1': '0.75';
						$that.css('border-color','rgba('+R+','+G+','+B+','+$opacityStr+')');
					});
					
					
				} else {
					
					$(this).find('i').css('color', $hoverTextColor);
					
					if($hoverColor != 'no-override'){
						
						var $that = $(this);
						
						$(this).on('mouseenter touchstart', function(){
							
							$that.css({
								'border-color': $hoverColor,
								'background-color': $hoverColor,
								'color': $hoverTextColor
							});
							
							//material buttons w/ icons over
							if($usingMaterialSkin) {
								$that.find('i').css({'background-color': '', 'box-shadow': ''});
							}
							
						});
						
						$(this).on('mouseleave touchtouchend', function(){
								
								$opacityStr = ($(this).hasClass('see-through-3')) ? '1': '0.75';
								
								//material buttons w/ icons leave
								if($usingMaterialSkin) {
									$that.find('i').css({'background-color': 'rgba('+R+','+G+','+B+',1)', 'box-shadow': '0px 8px 15px rgba('+R+','+G+','+B+',0.24)'});
								}
								
								if(!$that.hasClass('see-through-3')) {
									$that.css({
										'border-color':'rgba('+R+','+G+','+B+','+$opacityStr+')',
										'background-color': 'transparent',
										'color': $color
									});
								} else {
									$that.css({
										'border-color':'rgba('+R+','+G+','+B+','+$opacityStr+')',
										'background-color': 'transparent'
									});
								}
							
						});
							

					
					} else {
						
						var $that = $(this);
						
						$(this).on('mouseenter touchstart', function(){
							
								$that.css({
									'border-color': $hoverColor,
									'color': $hoverTextColor
								});
								
						});
						
						$(this).on('mouseleave touchtouchend', function(){
							
								$opacityStr = ($that.hasClass('see-through-3')) ? '1': '0.75';
								$that.css({
									'border-color':'rgba('+R+','+G+','+B+','+$opacityStr+')',
									'color':  $hoverTextColor
								});
								
						});
							

					
					}
			//	}
			
			}
		});
		
		$('.nectar-button:not(.see-through):not(.see-through-2):not(.see-through-3)[data-color-override]').each(function(){
			
			$(this).css('visibility','visible');
			
			if($(this).attr('data-color-override') != 'false'){
				
				var $color = $(this).attr('data-color-override');
				$(this).removeClass('accent-color').removeClass('extra-color-1').removeClass('extra-color-2').removeClass('extra-color-3');
				$(this).css('background-color',$color);
				
			}
			
		});


		//solid color tilt 
		if($('.swiper-slide .solid_color_2').length > 0 || $('.tilt-button-inner').length > 0) {

			var $tiltButtonCssString = '';

			$('.swiper-slide .solid_color_2 a').each(function(i){
				
				$(this).addClass('instance-'+i);

				if($(this).attr('data-color-override') != 'false') {
					var $color = $(this).attr('data-color-override');
				} else {
					if($(this).parents('.dark').length > 0) 
						var $color = '#000000';
					else 
						var $color = '#ffffff';
				}

				$(this).css('color',$color);
				$(this).find('i').css('color',$color);
				
				var $currentColor = $(this).css('background-color');
				var $topColor = shadeColor($currentColor, 0.13);
				var $bottomColor = shadeColor($currentColor, -0.15);
	
				$tiltButtonCssString += '.swiper-slide .solid_color_2 a.instance-'+i + ':after { background-color: '+$topColor+';  }' + ' .swiper-slide .solid_color_2 a.instance-'+i + ':before { background-color: '+$bottomColor+'; } ';

			});


			$('.tilt-button-wrap a').each(function(i){
				
				$(this).addClass('instance-'+i);

				var $currentColor = $(this).css('background-color');

				if($(this).attr('data-color-override') != 'false') {
					var $color = $(this).attr('data-color-override');
					$(this).css('background-color',$color);
					$currentColor = $color;
				} 
			
				var $topColor = shadeColor($currentColor, 0.13);
				var $bottomColor = shadeColor($currentColor, -0.15);
	
				$tiltButtonCssString += '.tilt-button-wrap a.instance-'+i + ':after { background-color: '+$topColor+';  }' + ' .tilt-button-wrap a.instance-'+i + ':before { background-color: '+$bottomColor+'; } ';

			});

			var head = document.head || document.getElementsByTagName('head')[0];
   			var style = document.createElement('style');

   			style.type = 'text/css';
			if (style.styleSheet){
			  style.styleSheet.cssText = $tiltButtonCssString;
			} else {
			  style.appendChild(document.createTextNode($tiltButtonCssString));
			}

			head.appendChild(style);
		}


		//transparent 3d
		if($('.nectar-3d-transparent-button').length > 0) {

			var $3dTransButtonCssString = '';
			$('.nectar-3d-transparent-button').each(function(i){

				var $that = $(this);
				var $size = $that.attr('data-size');
				var $padding = 0;
				var $font_size;
		

				//size

				if($size == 'large') {
					$padding = 46;
					$font_size = 16;
				} else if($size == 'medium') {
					$padding = 30;
					$font_size = 16;
				} else if($size == 'small') {
					$padding = 20;
					$font_size = 12;
				} else if($size == 'jumbo') {
					$padding = 54;
					$font_size = 24;
				} else if($size == 'extra_jumbo') {
					$padding = 100;
					$font_size = 64;
				}

				$that.find('svg text').attr('font-size',$font_size);
				var $boundingRect = $(this).find('.back-3d .button-text')[0].getBoundingClientRect();

				var $text_width = $boundingRect.width;
				var $text_height = $font_size*1.5;

				var $extraMult = (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) ? 0 : 1;

				$that.css({'width': ($text_width+$padding*1.5)+'px','height': ($text_height+$padding)+'px'});
				$that.find('> a').css({'height': ($text_height+$padding)+'px'});

				$that.find('.back-3d svg, .front-3d svg').css({'width': ($text_width+$padding*1.5)+'px','height': ($text_height+$padding)+'px'}).attr('viewBox','0 0 '+ ($text_width+$padding) + ' ' + ($text_height+$padding));
				if($size == 'jumbo')
					$that.find('svg text').attr('transform','matrix(1 0 0 1 '+($text_width+$padding*1.5)/2 +' ' + (($text_height+$padding) / 1.68) +')');
				else if($size == 'extra_jumbo')
					$that.find('svg text').attr('transform','matrix(1 0 0 1 '+($text_width+$padding*1.6)/2 +' ' + (($text_height+$padding) / 1.6) +')');
				else if($size == 'large') {
					$that.find('svg text').attr('transform','matrix(1 0 0 1 '+($text_width+$padding*1.5)/2 +' ' + (($text_height+$padding) / 1.7) +')');
				}
				else {
					$that.find('svg text').attr('transform','matrix(1 0 0 1 '+($text_width+$padding*1.5)/2 +' ' + (($text_height+$padding) / 1.65) +')');
				}
				$that.find('.front-3d ').css('transform-origin','50% 50% -'+($text_height+$padding)/2+'px');
				$that.find('.back-3d').css('transform-origin','50% 50% -'+($text_height+$padding)/2+'px');

				//mask
				$(this).find('.front-3d svg > rect').attr('id','masked-rect-id-'+i);
				$(this).find('.front-3d defs mask').attr('id','button-text-mask-'+i);

				$that.css('visibility','visible');
				$3dTransButtonCssString+= '#masked-rect-id-'+i+' { mask: url(#button-text-mask-'+i+'); -webkit-mask: url(#button-text-mask-'+i+')} ';

			});

			//extra jumbo resize
			function createExtraJumboSize() {
				$('.nectar-3d-transparent-button').each(function(i){
					
					if($(this).css('visibility') != 'visible') return;

					var $that = $(this);
					var $size = $that.attr('data-size');
					if($size == 'extra_jumbo') {
						
						var $font_size;
						
						var $extraMult = (navigator.userAgent.toLowerCase().indexOf('firefox') > -1) ? 0 : 1;

						if(window.innerWidth < 1000 && window.innerWidth > 690) {
							$padding = 64;
							$font_size = 34;
							$that.find('.back-3d rect').attr('stroke-width','12');
							$vert_height_divider = 1.7;
						} else if(window.innerWidth <= 690 ) {
							$padding = 46;
							$font_size = 16;
							$that.find('.back-3d rect').attr('stroke-width','10');
							$vert_height_divider = 1.7;
						}  else {
							$padding = 100;
							$font_size = 64;
							$that.find('.back-3d rect').attr('stroke-width','20');
							$vert_height_divider = 1.6;
						}
			

						$that.find('svg text').attr('font-size',$font_size);

						var $boundingRect = $(this).find('.back-3d .button-text')[0].getBoundingClientRect();
						var $text_width = $boundingRect.width;
						var $text_height = $font_size*1.5;

						$that.css({'width': ($text_width+$padding*1.5)+'px','height': ($text_height+$padding)+'px'});
						$that.find('> a').css({'height': ($text_height+$padding)+'px'});

						$that.find('.back-3d svg, .front-3d svg').css({'width': ($text_width+$padding*1.5)+'px','height': ($text_height+$padding)+'px'}).attr('viewBox','0 0 '+ ($text_width+$padding) + ' ' + ($text_height+$padding));

						$that.find('svg text').attr('transform','matrix(1 0 0 1 '+($text_width+$padding*1.6)/2 +' ' + (($text_height+$padding) / $vert_height_divider) +')');

						$that.find('.front-3d ').css('transform-origin','50% 50% -'+($text_height+$padding)/2+'px');
						$that.find('.back-3d').css('transform-origin','50% 50% -'+($text_height+$padding)/2+'px');

					}
				});
			}
			createExtraJumboSize();
			$(window).on('smartresize',createExtraJumboSize);

			var head = document.head || document.getElementsByTagName('head')[0];
				var style = document.createElement('style');

				style.type = 'text/css';
			if (style.styleSheet){
			  style.styleSheet.cssText = $3dTransButtonCssString;
			} else {
			  style.appendChild(document.createTextNode($3dTransButtonCssString));
			}

			head.appendChild(style);
		}

		//gradient btn init
		setTimeout(function(){
			$('.nectar-button.extra-color-gradient-1 .start, .nectar-button.extra-color-gradient-2 .start, .nectar-button.see-through-extra-color-gradient-1 .start, .nectar-button.see-through-extra-color-gradient-2 .start').removeClass('loading');
		},150);
		//no grad for ff
		if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1 || navigator.userAgent.indexOf("MSIE ") > -1 || navigator.userAgent.match(/Trident\/7\./)) {
			$('.nectar-button.extra-color-gradient-1, .nectar-button.extra-color-gradient-2, .nectar-button.see-through-extra-color-gradient-1, .nectar-button.see-through-extra-color-gradient-2').addClass('no-text-grad');
		}
	}	

	coloredButtons();


	//large icon hover
	function largeIconHover(){
		$('.icon-3x').each(function(){
			
			$(this).closest('.col').on('mouseenter',function(){
				$(this).find('.icon-3x').addClass('hovered')
			});
			$(this).closest('.col').on('mouseleave',function(){
				$(this).find('.icon-3x').removeClass('hovered')
			});
			
		});

		//remove gradient from FF
		if( navigator.userAgent.indexOf("MSIE ") > -1 || navigator.userAgent.match(/Trident\/7\./))
			$('[class^="icon-"].extra-color-gradient-1, [class^="icon-"].extra-color-gradient-2, [class^="icon-"][data-color="extra-color-gradient-1"], [class^="icon-"][data-color="extra-color-gradient-2"], .nectar_icon_wrap[data-color*="extra-color-gradient"] .nectar_icon, .nectar-gradient-text').addClass('no-grad');
	}
	largeIconHover();


/***************** Team Member ******************/
function teamMemberFullscreen() {

	//open
	$('body').on('click','.team-member[data-style="bio_fullscreen"]',function(){
		
		if($('.nectar_team_member_overlay').length > 0) return;

		var $usingBoxedClass = ($('body > #boxed').length > 0) ? 'in-boxed' : null;

		$teamMemberMeta = $(this).find('.nectar_team_bio').html();
		$teamMemberImg = ($(this).find('.nectar_team_bio_img[data-img-src]').length > 0) ? $(this).find('.nectar_team_bio_img').attr('data-img-src') : '';
		$('body').append('<div class="nectar_team_member_overlay '+$usingBoxedClass+'"><div class="inner-wrap"><div class="team_member_details"><div class="bio-inner"><span class="mobile-close"></span><h2>'+$(this).find('.team-meta h3').html()+'</h2><div class="title">'+$(this).find('.team-meta p').html()+'</div><div class="team-desc">'+$teamMemberMeta+'</div></div></div><div class="team_member_picture"><div class="team_member_image_bg_cover"></div><div class="team_member_picture_wrap"><div class="team_member_image"></div></div></div></div></div><div class="nectar_team_member_close '+$usingBoxedClass+'"><div class="inner"></div></div>');
		if($teamMemberImg.length > 0) {

			//fadein img on load
			var teamTmpImg = new Image();
			teamTmpImg.src = $teamMemberImg;
		    teamTmpImg.onload = function() {
		        $('.nectar_team_member_overlay .team_member_image').css('opacity','1');
		    };
			$('.nectar_team_member_overlay .team_member_image').css({ 'background-image': 'url("'+$teamMemberImg+'")'});
		}
		
		
		var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-outer').height();
		$('.nectar_team_member_overlay .inner-wrap').css({ 'padding-top': $headerNavSpace });
		
		//no-scroll class - ios ready
		if($('.using-mobile-browser').length > 0) {
			$('body,html').addClass('nectar-no-scrolling');
		}
		
		teamFullscreenResize();

		//transition in
		$('.nectar_team_member_overlay').addClass('open').addClass('animating');

		setTimeout(function(){
			$('.nectar_team_member_close').addClass('visible');
			$('.nectar_team_member_overlay').removeClass('animating');
		},500);

		//bind close mousemove
		$(document).on('mousemove',teamMousemoveOn);

		//bind overflow
		if($('.using-mobile-browser').length == 0) {
			fullscreenBioScrolling();
		}

		if($('.team-member[data-style="bio_fullscreen"]').length > 0 && navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/)) { 
			$('.nectar_team_member_overlay').addClass('on-mobile');
		}

	});

	//close
	$('body').on('click','.nectar_team_member_overlay',function(){
		if(!$(this).hasClass('animating')) {

			$('.nectar_team_member_overlay').removeClass('open');
			$('.nectar_team_member_close').removeClass('visible');
			
			if($('.using-mobile-browser').length > 0) {
				$('body,html').removeClass('nectar-no-scrolling');
			}
			
			setTimeout(function(){
				
				//unbind close mousemove
				$(document).off('mousemove',teamMousemoveOn);

				$('.nectar_team_member_overlay, .nectar_team_member_close').remove();

			},820);
		}
	});

	if($('.team-member[data-style="bio_fullscreen"]').length > 0) {
		$(window).resize(teamFullscreenResize);
	}

	
}

function teamFullscreenResize() {
	var $leftHeaderSize = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 275 : 0;
	$('.nectar_team_member_overlay').css({'width' : $(window).width()-$leftHeaderSize, 'left': $leftHeaderSize});
}

function fullscreenBioScrolling(){ 
	$('.nectar_team_member_overlay .inner-wrap').mousewheel(function(event, delta) {

	     this.scrollTop -= (delta * 30);
	     event.preventDefault();

	});

}

function teamMousemoveOn(e) {
	
	if($('a:hover').length > 0) {
		$('.nectar_team_member_close .inner').removeClass('visible');
	} else {
		$('.nectar_team_member_close .inner').addClass('visible');
	}
    $('.nectar_team_member_close').css({
       left:  e.pageX - 26,
       top:   e.pageY - $(window).scrollTop() - 29
    });
}

if($('.team-member').length > 0) {
	teamMemberFullscreen();
}

/***************** Column Hover BG ******************/

function columnBGColors() {	
	
	var $columnColorCSS = '';

	$('.wpb_column').each(function(i){
		
		$(this).removeClass(function (index, className) {
		    return (className.match (/(^|\s)instance-\S+/g) || []).join(' ');
		});
		
		$(this).addClass('instance-'+i);

		//bg color
		if($(this).attr('data-has-bg-color') == 'true') {
			if($(this).is('[data-animation*="reveal"]') && $(this).hasClass('has-animation')) {
				$columnColorCSS += '.wpb_column.instance-'+i+ ' > .column-bg-overlay { background-color:' + $(this).attr('data-bg-color') + ';  opacity: '+$(this).attr('data-bg-opacity')+'; }';
			}
			else {
				$columnColorCSS += '.wpb_column.instance-'+i+ ' > .column-bg-overlay{ background-color:' + $(this).attr('data-bg-color') + ';  opacity: '+$(this).attr('data-bg-opacity')+'; }';
			}
		}

		//hover bg color
		if($(this).is('[data-hover-bg^="#"]')) {
			if($(this).is('[data-animation*="reveal"]') && $(this).hasClass('has-animation')) {
				 $columnColorCSS += '.wpb_column.instance-'+i+ ':hover > .column-bg-overlay { background-color: '+$(this).attr('data-hover-bg') + '!important; opacity: '+$(this).attr('data-hover-bg-opacity')+'!important; }';
			}
			else {
	   		 $columnColorCSS += '.wpb_column.instance-'+i+ ':hover > .column-bg-overlay { background-color: '+$(this).attr('data-hover-bg') + '!important; opacity: '+$(this).attr('data-hover-bg-opacity')+'!important; }';
			}

		}
	});
	
	if($('head #column-bg-colors').length > 0) {
		$('head #column-bg-colors').remove();
	}
	if($columnColorCSS.length > 1) {
		var head = document.head || document.getElementsByTagName('head')[0];
		var style = document.createElement('style');

		style.type = 'text/css';
		if (style.styleSheet){
		  style.styleSheet.cssText = $columnColorCSS;
		} else {
		  style.appendChild(document.createTextNode($columnColorCSS));
		}
		
		$(style).attr('id','column-bg-colors');
		head.appendChild(style);
	}

}
columnBGColors();






/***************** morphing button ******************/

function morphingOutlines() {

	if($('.morphing-outline').length > 0) {

		$morphingOutlineCSS = '';
		var $frontEndEditorMOSelector =  ($('body.vc_editor').length > 0) ? '' : '>';
		
		$('.morphing-outline').each(function(i){
			
			$(this).removeClass(function (index, className) {
			    return (className.match (/(^|\s)instance-\S+/g) || []).join(' ');
			});
			
			$(this).addClass('instance-'+i).css({'visibility':'visible'});
			var $width = $(this).find('.inner').width();
			var $height = $(this).find('.inner').height();
			var $border = parseInt($(this).attr("data-border-thickness"));
			var $hover = ($('body[data-button-style*="rounded"]').length > 0) ? ':hover': '';
			var $hover2 = ($('body[data-button-style*="rounded"]').length > 0) ? '': ':hover';

			$morphingOutlineCSS += 'body .morphing-outline.instance-'+i+' .inner > * { color: '+$(this).attr("data-starting-color")+'; } ';
			$morphingOutlineCSS += 'body .morphing-outline.instance-'+i+' .inner:after  { border-width:'+$(this).attr("data-border-thickness")+'px ; border-color: '+$(this).attr("data-starting-color")+'; } ';
			
			$morphingOutlineCSS += 'body .wpb_column:hover > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner > *, body .wpb_column:hover > .vc_column-inner > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner > * { color: '+$(this).attr("data-hover-color")+'; } ';
			$morphingOutlineCSS += 'body .wpb_column:hover > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after, body .wpb_column:hover > .vc_column-inner > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after  { border-color: '+$(this).attr("data-hover-color")+'; } ';
			//padding calcs
			$morphingOutlineCSS += 'body .wpb_column'+$hover2+' > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after, body .wpb_column'+$hover2+' > .vc_column-inner > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after { padding: '+(($width+100 + $border*2 - $height)/2 - $border) +'px 50px}';
			$morphingOutlineCSS += '.morphing-outline.instance-'+i+' { padding: '+(30+($width+80 + $border*2 - $height)/2 - $border) +'px 50px}'; //extra space on the outer for mobile/close elements
			$morphingOutlineCSS += 'body .wpb_column'+$hover2+' > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after, body .wpb_column'+$hover2+' > .vc_column-inner > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after { top: -'+ parseInt((($width+100 + $border*2 - $height)/2 - $border) + $border)+ 'px }';

			$morphingOutlineCSS += 'body .wpb_column > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after, body .wpb_column > .vc_column-inner > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after { left: -' + parseInt(50+$border) + 'px }';
			////hover
			$morphingOutlineCSS += 'body .wpb_column'+$hover+' > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after, body .wpb_column'+$hover+' > .vc_column-inner > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after { padding: 50px 50px}';
			$morphingOutlineCSS += 'body .wpb_column'+$hover+' > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after, body .wpb_column'+$hover+' > .vc_column-inner > .wpb_wrapper ' + $frontEndEditorMOSelector + ' .morphing-outline.instance-'+i+' .inner:after { top: -'+parseInt(50+$border) +'px }';

		});

		var head = document.head || document.getElementsByTagName('head')[0];
		var style = document.createElement('style');

		style.type = 'text/css';
		style.id = 'morphing-outlines';
		if (style.styleSheet){
		  style.styleSheet.cssText = $morphingOutlineCSS;
		} else {
		  style.appendChild(document.createTextNode($morphingOutlineCSS));
		}

		$('#morphing-outlines').remove();
		head.appendChild(style);
	}
}

setTimeout(morphingOutlines,100); 
setTimeout(fullWidthContentColumns,126);


/***************** svg icons *******************/

var $svg_icons = [];
function svgAnimations() {
	
	var $svgOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : 'bottom-in-view';

	if($svg_icons.length == 0) {

		$('.svg-icon-holder:not(.animated-in)').has('svg').each(function(i){
			var $that = $(this);

			if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/)) $that.attr('data-animation','false');

			//size
			$that.find('svg').css({'height': parseInt($that.attr('data-size')) +'px', 'width': parseInt($that.attr('data-size')) +'px'});

			//animation
			$(this).find('svg').attr('id','nectar-svg-animation-instance-'+i);
			var $animationSpeed = ($that.is('[data-animation-speed]') && $that.attr('data-animation-speed').length > 0) ? $that.attr('data-animation-speed') : 200;
			if($that.attr('data-animation') == 'false') { 
				$animationSpeed = 1;
				$that.css('opacity','1');
			}

			if(!$that.hasClass('bound')) {
				$svg_icons[i] = new Vivus($that.find('svg').attr('id'), {type: 'delayed', pathTimingFunction: Vivus.EASE_OUT, animTimingFunction: Vivus.LINEAR, duration: $animationSpeed, onReady: svgInit });
			}

			if($animationSpeed !== 1) {

				var $that = $(this);
				var waypoint = new Waypoint({
		 			element: $that,
		 			 handler: function(direction) {
		 			 	if( $that.hasClass('animated-in')) { 
						     waypoint.destroy();
						     return;
						}

		 			 	checkIfReady();
						$that.addClass('animated-in');
						waypoint.destroy();
					},
					offset: $svgOffsetPos

				}); 

			} else {
				checkIfReady();
			}

			function checkIfReady() {
				var $animationDelay = ($that.is('[data-animation-delay]') && $that.attr('data-animation-delay').length > 0 && $that.attr('data-animation') != 'false') ? $that.attr('data-animation-delay') : 0;
				
				var $iconID = $that.find('svg').attr('id').replace(/[^0-9]/g,'');

				if($svg_icons[$iconID].isReady == true) {
					 setTimeout(function(){ $that.css('opacity','1'); $svg_icons[$iconID].reset().play(); },$animationDelay);
				} else {
					setTimeout(checkIfReady,50);
				}
			}

			function svgInit() {

				//set size
				$that.css({'height': parseInt($that.attr('data-size')) +'px', 'width': parseInt($that.attr('data-size')) +'px'});


			}

			$that.addClass('bound');

		});	
	} else {
		$('.svg-icon-holder').addClass('animated-in').css('opacity','1');
	}
	
	//full vc row support
	$('#nectar_fullscreen_rows .svg-icon-holder.animated-in').has('svg').each(function(i){
		
		var $animationDelay = ($(this).is('[data-animation-delay]') && $(this).attr('data-animation-delay').length > 0 && $(this).attr('data-animation') != 'false') ? $(this).attr('data-animation-delay') : 0;
		var $that = $(this);

		var $iconID = $that.find('svg').attr('id').replace(/[^0-9]/g,'');

		clearTimeout($animatedSVGIconTimeout[i]);

		if($that.attr('data-animation') == 'false') { 
			$animationSpeed = 1;
			$that.css('opacity','1');
			$svg_icons[$iconID].finish();
		} else {
			if($(this).parents('.active').length > 0 || $(this).parents('#footer-outer').length > 0 || $('body.mobile').length > 0) {
				$svg_icons[$iconID].reset();
				$animatedSVGIconTimeout[i] = setTimeout(function(){ $svg_icons[$iconID].play(); },$animationDelay);
			}

			else {
				$svg_icons[$iconID].reset().stop();
			}
		}
	});
}
//svg in equal height column resize
if($('.vc_row-o-equal-height .svg-icon-holder[data-animation="true"]').length > 0 && $('#nectar_fullscreen_rows').length == 0) {
	 $(window).on('smartresize', function(){
	    clearTimeout($svgResizeTimeout);
	    $svgResizeTimeout = setTimeout(function(){ 

	    	if($svg_icons.length > 0) {
	        	$('.svg-icon-holder.animated-in').each(function(i){
					$(this).css('opacity','1');
					var $iconID = $(this).attr('id').replace(/[^0-9]/g,'');
					$svg_icons[$iconID].finish();
				});
	        }

	     },300);
	});
}


/***************** fancy ul ******************/

	function nectar_fancy_ul_init() {
		$($fullscreenSelector+'.nectar-fancy-ul').each(function(){


			var $icon = $(this).attr('data-list-icon');
			var $color = $(this).attr('data-color');
			var $animation = $(this).attr('data-animation');
			var $animationDelay = ($(this).is('[data-animation-delay]') && $(this).attr('data-animation-delay').length > 0 && $(this).attr('data-animation') != 'false') ? $(this).attr('data-animation-delay') : 0;
			
			$(this).find('li').each(function(){
				
				if($(this).find('> i').length == 0) 
					$(this).prepend('<i class="icon-default-style '+$icon+ ' ' + $color +'"></i> ');
			});

			
			if($animation == 'true') {


				var $that = $(this);
				var waypoint = new Waypoint({
		 			element: $that,
		 			 handler: function(direction) {

		 			 	if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
						     waypoint.destroy();
						     return;
						}

						setTimeout(function(){
			 			 	$that.find('li').each(function(i){
			 			 		var $that = $(this);
								$that.delay(i*220).transition({
									'opacity': '1',
									'left' : '0'
								},220,'easeOutCubic');
							});
			 			 },$animationDelay);

						$that.addClass('animated-in');
						waypoint.destroy();
					},
					offset: 'bottom-in-view'

				}); 

			} 
			
			
			
		});
	}


	

/***************** flip box min heights ******************/
//if content height exceeds min height change it
function flipBoxHeights() {
	$('.nectar-flip-box').each(function(){
		
		var $flipBoxMinHeight = parseInt($(this).attr('data-min-height'));
		var $flipBoxHeight = ( $(this).find('.flip-box-back .inner').height() > $(this).find('.flip-box-front .inner').height() ) ? $(this).find('.flip-box-back .inner').height() : $(this).find('.flip-box-front .inner').height();

		if($flipBoxHeight >= $flipBoxMinHeight - 80) {
			$(this).find('> div').css('height', $flipBoxHeight + 80);
		} else 
			$(this).find('> div').css('height','auto');

	});
}
flipBoxHeights();

if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/)){
	$('body').on('click','.nectar-flip-box', function(){
		$(this).toggleClass('flipped');
	});
}

/***************** PARALLAX SECTIONS ******************/
	
	var $window = $(window);
	var windowHeight = $window.height();

		
	$window.off('scroll.parallaxSections').off('resize.parallaxSections');
	$window.unbind('resize.parallaxSectionsUpdateHeight');
	$window.unbind('load.parallaxSectionsOffsetL');
	$window.unbind('resize.parallaxSectionsOffsetR');

	$window.on('resize.parallaxSectionsUpdateHeight',psUpdateWindowHeight);

	function psUpdateWindowHeight() {
		windowHeight = $window.height();
	}

	function psUpdateOffset($this) {
		$this.each(function(){
	  	    firstTop = $this.offset().top;
		});
	}
	
	var firstTop;
	
	$.fn.parallaxScroll = function(xpos, speedFactor, outerHeight) {
		var $this = $(this);
		var getHeight;
		var paddingTop = 0;
		
		var $windowDOMWidth = window.innerWidth, $windowDOMHeight = window.innerHeight;
		var $orientationChange = 0;
		
		//get the starting position of each element to have parallax applied to it		
		$this.each(function(){
		    firstTop = $this.offset().top;
		});

		
		$window.on('resize.parallaxSectionsOffsetR',psUpdateOffset($this));
		$window.on('load.parallaxSectionsOffsetL',psUpdateOffset($this));
	
		getHeight = function(jqo) {
			return jqo.outerHeight(true);
		};
		 
			
		// setup defaults if arguments aren't specified
		if (arguments.length < 1 || xpos === null) xpos = "50%";
		if (arguments.length < 2 || speedFactor === null) speedFactor = 0.25;
		if (arguments.length < 3 || outerHeight === null) outerHeight = true;
		
		// function to be called whenever the window is scrolled or resized

		var $element, top, height;
		
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("MSIE ");
		var $onMobileBrowser = navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/);		
		
		var $toTransformOrNot = ($smoothCache == true) ? true : false;
		
		if(!$toTransformOrNot) {
			$this.find('.row-bg.using-image, .page-header-bg-image, .image-bg, .video-wrap').addClass('translate');
		}
		
		var $ifFast = 0;
		$element = $this;
		
		height = getHeight($element);
		
		var classic_mobile_menu_open = false;
		
		setInterval(function(){
			height = getHeight($element);
			
			classic_mobile_menu_open = ( $('body.classic_mobile_menu_open.mobile').length > 0 ) ? true : false;
			
		},600);
		
		var firstSection = ( ($element.parents('.top-level').length > 0 && $element.parents('.parallax_slider_outer').length > 0) || ($element.parents('.top-level').length > 0 && $element.is('.nectar-recent-posts-single_featured') ) || $element.is('.wpb_row.top-level') || $('.wpb_row').length == 0) ? true : false;
		
		//for first nectar slider shortcode without page builder or portfolio first
		if( $('.wpb_row').length == 0 && $element.parents('.parallax_slider_outer').length > 0 && $element.is('[data-full-width="true"]') ||
	      ($('#portfolio-extra').length > 0 && $element.parents('.parallax_slider_outer').length > 0 && $element.parents('.wpb_row').length > 0 && $element.parents('.wpb_row').index() == '0') ) {
			firstSection = true;
		}
		
		if(nectarDOMInfo.usingFrontEndEditor) {
			firstSection = false;
		}
		
		var nectarSliderElBool = $this.is('.nectar-slider-wrap');
		var pageHeaderBool = ($this.find('.page-header-bg-image').length > 0) ? true : false;
		
		var $elToParallax = false;
		
		if(nectarSliderElBool) {
			
				if($this.find('.video-wrap').length > 0 || $this.find('.image-bg').length > 0) {
					$elToParallax = $this.find('.video-wrap, .image-bg');
				} 
		}
		else {
			
				if($this.find('.row-bg.using-image').length > 0) {
					
					$elToParallax = $this.find('.row-bg.using-image');
					
				} else if($this.find('.page-header-bg-image').length > 0) {
					
					$elToParallax = $this.find('.page-header-bg-image');
				}
				
		}
		

		function update(){
				
				firstTop = $element.offset().top;
				
				// Check if totally above or totally below viewport
				if ($elToParallax == false || firstTop + height < nectarDOMInfo.scrollTop || firstTop > nectarDOMInfo.scrollTop + windowHeight || $('body.material-ocm-open').length > 0) {
					
				} else {

		        //for IE, Safari or any setup using the styled scrollbar default to animating the BG pos
		        if ($toTransformOrNot) {
		        	$this.find('.row-bg.using-image').css('backgroundPosition', xpos + " " + Math.round((firstTop - nectarDOMInfo.scrollTop) * speedFactor) + "px");
		        }
		       	//for Firefox/Chrome use a higher performing method
		        else  {
							
							//nectar slider
							if(nectarSliderElBool) {
									
								 if(firstSection) {
									 //top level row
 										if(!classic_mobile_menu_open) {
									 		$this.find('.video-wrap, .image-bg').css({ 'transform':  'translate3d(0, ' + parseFloat(nectarDOMInfo.scrollTop * speedFactor)  + 'px, 0)' }); 
									  }
								 } else {
									 	$this.find('.video-wrap, .image-bg').css({ 'transform':  'translate3d(0, ' + parseFloat((($windowDOMHeight + nectarDOMInfo.scrollTop -  firstTop) * speedFactor) ) + 'px, 0)' }); 
								 }
								
							} else {
								
								//rows
								if(firstSection) {
									//top level row
									if(!classic_mobile_menu_open) {
		        				$elToParallax.css({ 'transform':  'translate3d(0, ' + parseFloat(nectarDOMInfo.scrollTop * speedFactor)  + 'px, 0)' });
									}
								} else {
									//regular rows
									$elToParallax.css({ 'transform':  'translate3d(0, ' + parseFloat((($windowDOMHeight + nectarDOMInfo.scrollTop -  firstTop) * speedFactor) ) + 'px, 0), scale(1.005)' });
								}
								
								//page header
								if(pageHeaderBool && !classic_mobile_menu_open) {
									$elToParallax.css({ 'transform':  'translate3d(0, ' + parseFloat(nectarDOMInfo.scrollTop * speedFactor) + 'px, 0)' });
								}
							
							}
							
						} 
				
				}
				

			//if on mobile, auto RAF
			if($onMobileBrowser){
				requestAnimationFrame(update); 
			}
			
		}		

		if (window.addEventListener) {
			
			if(nectarDOMInfo.usingFrontEndEditor) {
				$(window).on('scroll.parallaxSections', update);
				
				$(window).resize(function(){
					$windowDOMWidth = window.innerWidth;
					$windowDOMHeight = window.innerHeight;
				});
				
			}
			else if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/)){
					 window.addEventListener('scroll', function(){ 
			          requestAnimationFrame(update); 
			     }, false);
					 
					 $(window).resize(function(){
						 $windowDOMWidth = window.innerWidth;
						 $windowDOMHeight = window.innerHeight;
					 });
					 
			 } else {
				 	//if on mobile, auto RAF
					requestAnimationFrame(update); 
					

					window.addEventListener("orientationchange", function() {
						 $orientationChange = 1;
					});
					
					$(window).resize(function(){
							if( ($(window).width() != $windowDOMWidth && $(window).height != $windowDOMHeight) || $orientationChange == 1){

									//store the current window dimensions
									$windowDOMWidth = window.innerWidth;
									$windowDOMHeight = window.innerHeight;
									
									$orientationChange = 0;
							}
					});
					 
			 }
			 
		}

		$window.on('resize.parallaxSections',update);

		update();
	};

 

	
/***************** Full Width Section ******************/

	//nectar slider in container but fullwidth fix
	$('.wpb_row .vc_col-sm-12 .nectar-slider-wrap[data-full-width="true"]').each(function(){
		if($(this).parents('.wpb_row.full-width-section').length == 0 && $(this).parents('.wpb_row.full-width-content').length == 0) {
			$(this).parents('.wpb_row').addClass('full-width-section');
		}
	});

	function fullWidthSections(){

		var $windowInnerWidth = window.innerWidth;
		var $scrollBar = ($('#ascrail2000').length > 0 && $windowInnerWidth > 1000) ? -13 : 0;
		var $bodyBorderWidth = ($('.body-border-right').length > 0 && $windowInnerWidth > 1000) ? parseInt($('.body-border-right').width())*2 : 0;
		var $justOutOfSight;
		
		if($('#boxed').length == 1){
			$justOutOfSight = ((parseInt($('.container-wrap').width()) - parseInt($('.main-content').width())) / 2) + 4;
		} else {
			
			//if the ext responsive mode is on - add the extra padding into the calcs
			var $extResponsivePadding = ($('body[data-ext-responsive="true"]').length > 0 && $windowInnerWidth >= 1000) ? 180 : 0;
			var $leftHeaderSize = ($('#header-outer[data-format="left-header"]').length > 0 && $windowInnerWidth >= 1000) ? parseInt($('#header-outer[data-format="left-header"]').width()) : 0;
			if($(window).width() - $leftHeaderSize - $bodyBorderWidth  <= parseInt($('.main-content').css('max-width'))) { 
				var $windowWidth = parseInt($('.main-content').css('max-width'));

				//no need for the scrollbar calcs with ext responsive on desktop views
				if($extResponsivePadding == 180) $windowWidth = $windowWidth - $scrollBar;

			} else { 
				var $windowWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth;
			}

			
			var $contentWidth = parseInt($('.main-content').css('max-width'));

			//single post fullwidth
			if($('body.single-post[data-ext-responsive="true"]').length > 0 && $('.container-wrap.no-sidebar').length > 0 ) {
				$contentWidth = $('.post-area').width();
				$extResponsivePadding = 0;
			}
			
			$justOutOfSight = Math.ceil( (($windowWidth + $extResponsivePadding + $scrollBar - $contentWidth) / 2) )
		}
		
	
	    //full width content sections
	    $('.carousel-outer').has('.carousel-wrap[data-full-width="true"]').css('overflow','visible');
	    
	    $('.carousel-wrap[data-full-width="true"], .portfolio-items[data-col-num="elastic"]:not(.fullwidth-constrained), .full-width-content').each(function(){
			
			var $leftHeaderSize = ($('#header-outer[data-format="left-header"]').length > 0 && $windowInnerWidth >= 1000) ? parseInt($('#header-outer[data-format="left-header"]').width()) : 0;
			var $bodyBorderWidth = ($('.body-border-right').length > 0 && $windowInnerWidth > 1000) ? (parseInt($('.body-border-right').width())*2) - 2 : 0;

			//single post fullwidth
			if($('#boxed').length == 1){

				var $mainContentWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').width()) : parseInt($(this).parents('.container').width());

				if($('body.single-post[data-ext-responsive="true"]').length > 0 && $('.container-wrap.no-sidebar').length > 0 && $(this).parents('.post-area').length > 0) {
					$contentWidth = $('.post-area').width();
					$extResponsivePadding = 0;
					$windowWidth = $(window).width() - $bodyBorderWidth;
					$justOutOfSight = Math.ceil( (($windowWidth + $extResponsivePadding + $scrollBar - $contentWidth) / 2) )
				} else {
					if($(this).parents('.page-submenu').length > 0)
						$justOutOfSight = ((parseInt($('.container-wrap').width()) - $mainContentWidth) / 2);
					else 
						$justOutOfSight = ((parseInt($('.container-wrap').width()) - $mainContentWidth) / 2) + 4;
				}
			} else {
				if($('body.single-post[data-ext-responsive="true"]').length > 0 && $('.container-wrap.no-sidebar').length > 0 && $(this).parents('.post-area').length > 0) {
					$contentWidth = $('.post-area').width();
					$extResponsivePadding = 0;
					$windowWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth;
				} else {

					var $mainContentMaxWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').css('max-width')) : parseInt($(this).parents('.container').css('max-width'));

					//when using gutter on portfolio don't add extra space for scroll bar
			    	if($('#boxed').length == 0 && $(this).hasClass('portfolio-items') && $(this).is('[data-gutter*="px"]') && $(this).attr('data-gutter').length > 0 && $(this).attr('data-gutter') != 'none') {
			    		$scrollBar = ($('#ascrail2000').length > 0 && $windowInnerWidth > 1000) ? -13 : 0;
			    	}

					if($(window).width() - $leftHeaderSize - $bodyBorderWidth <= $mainContentMaxWidth) { 
						$windowWidth = $mainContentMaxWidth;
						//no need for the scrollbar calcs with ext responsive on desktop views
						if($extResponsivePadding == 180) $windowWidth = $windowWidth - $scrollBar;
					}
					$contentWidth = $mainContentMaxWidth;
					$extResponsivePadding = ($('body[data-ext-responsive="true"]').length > 0 && window.innerWidth >= 1000) ? 180 : 0;
					if($leftHeaderSize > 0) $extResponsivePadding = ($('body[data-ext-responsive="true"]').length > 0 && window.innerWidth >= 1000) ? 120 : 0;
				}

				$justOutOfSight = Math.ceil( (($windowWidth + $extResponsivePadding + $scrollBar - $contentWidth) / 2) )
			}

			var $extraSpace = 0;
			if( $(this).hasClass('carousel-wrap')) $extraSpace = 1;
			if( $(this).hasClass('portfolio-items')) $extraSpace = 5;
			
	    	var $carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : $(window).width() - $leftHeaderSize - $bodyBorderWidth +$extraSpace  + $scrollBar ;

	    	//when using gutter on portfolio don't add extra space
	    	if($('#boxed').length == 0 && $(this).hasClass('portfolio-items') && $(this).is('[data-gutter*="px"]') && $(this).attr('data-gutter').length > 0 && $(this).attr('data-gutter') != 'none') {
	    		if($(window).width() > 1000)
	    			$carouselWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth + $scrollBar + 3
	    		else 
	    			$carouselWidth = $(window).width() - $leftHeaderSize - $bodyBorderWidth + $scrollBar 
	    	}

	    	if($(this).parent().hasClass('default-style')) { 

	    		var $mainContentWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').width()) : parseInt($(this).parents('.container').width());
	    		
	    		if($('#boxed').length != 0) {
	    			$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : $(window).width() - $leftHeaderSize + $extraSpace + $scrollBar ;
				}
				else {
					$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : ($(window).width() - $leftHeaderSize - $bodyBorderWidth) - (($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.025) + $extraSpace + $scrollBar ;
					$windowWidth = ($(window).width() - $leftHeaderSize - $bodyBorderWidth <= $mainContentWidth) ? $mainContentWidth : ($(window).width() - $leftHeaderSize - $bodyBorderWidth) - (($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.025);
					$justOutOfSight = Math.ceil( (($windowWidth + $scrollBar - $mainContentWidth) / 2) )
				}
			}

			else if($(this).parent().hasClass('spaced')) { 

				var $mainContentWidth = ($('#nectar_fullscreen_rows').length == 0) ? parseInt($('.main-content').width()) : parseInt($(this).parents('.container').width());

				if($('#boxed').length != 0) {
	    			$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) - ($(window).width()*.02) : $(window).width() + $extraSpace + $scrollBar ;
				} else {
					$carouselWidth = ($('#boxed').length == 1) ? $mainContentWidth + parseInt($justOutOfSight*2) : ($(window).width()- $leftHeaderSize - $bodyBorderWidth)  - Math.ceil(($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.02) + $extraSpace + $scrollBar ;
					var $windowWidth2 = ($(window).width() - $leftHeaderSize - $bodyBorderWidth <= $mainContentWidth) ? $mainContentWidth : ($(window).width() - $leftHeaderSize - $bodyBorderWidth) - (($(window).width()- $leftHeaderSize - $bodyBorderWidth)*.02);
					$justOutOfSight = Math.ceil( (($windowWidth2 + $scrollBar - $mainContentWidth) / 2) +2)
				}
			}
	    	
	    	if(!$(this).parents('.span_9').length > 0 && !$(this).parent().hasClass('span_3') && $(this).parent().attr('id') != 'sidebar-inner' && $(this).parent().attr('id') != 'portfolio-extra' 
	      && !$(this).find('.carousel-wrap[data-full-width="true"]').length > 0
				&& !$(this).find('.nectar-carousel-flickity-fixed-content').length > 0
	    	&& !$(this).find('.portfolio-items:not(".carousel")[data-col-num="elastic"]').length > 0){

	    		//escape if inside woocoommerce page and not using applicable layout
	    		if($('.single-product').length > 0 && $(this).parents('#tab-description').length > 0 && $(this).parents('.full-width-tabs').length == 0) {
	    			$(this).css({
						'visibility': 'visible'
					});	
	    		} else {
	    			if($(this).hasClass('portfolio-items')) {
		    				$(this).css({
								'transform': 'translateX(-'+ $justOutOfSight + 'px)',
								'margin-left': 0,
								'left': 0,
								'width': $carouselWidth,
								'visibility': 'visible'
							});	
	    			} else {
							
							//fullscreen page rows left fix
							if($('#nectar_fullscreen_rows').length > 0 && $(this).hasClass('wpb_row')) {
								$(this).css({
									'margin-left': - $justOutOfSight,
									'width': $carouselWidth,
									'visibility': 'visible'
								});	
							} else {
								$(this).css({
									'left': 0,
									'margin-left': - $justOutOfSight,
									'width': $carouselWidth,
									'visibility': 'visible'
								});	
							}
							
	    			}
					
				}
			}  else if($(this).parent().attr('id') == 'portfolio-extra' && $('#full_width_portfolio').length != 0) {
				$(this).css({
					'left': 0,
					'margin-left': - $justOutOfSight,
					'width': $carouselWidth,
					'visibility': 'visible'
				});	
			}
			
			else {

				$(this).css({
					'margin-left': 0,
					'width': 'auto',
					'left': '0',
					'visibility': 'visible'
				});	
			}
	    	
	    });
			
	}
	
	var $contentElementsNum = ($('#portfolio-extra').length == 0) ? $('.main-content > .row > *').length : $('.main-content > .row #portfolio-extra > *').length ;

	function parallaxSrollSpeed(speedString) {

		var ua = window.navigator.userAgent;	
		var msie = ua.indexOf("MSIE ");
		var speed;

		 //not as modern browsers
		 if ($smoothCache == true) {
			 switch(speedString) {
			   	  case 'slow':
			   	      speed = 0.2;
			   	      break;
			   	  case 'medium': 
			   	  	  speed = 0.4;
			   	      break;
			   	  case 'fast': 
			    	  speed = 0.6;
			   	       break;
			   }
		}
		 //chrome/ff
		 else {
		 	 switch(speedString) {
			   	  case 'slow':
			   	      speed = 0.6;
			   	      break;
			   	  case 'medium': 
			   	  	  speed = 0.4;
			   	      break;
			   	  case 'fast': 
			    	  speed = 0.25;
			   	       break;
			   }
		}

		   return speed;
	}

	function parallaxScrollInit(){
		
		if(nectarDOMInfo.usingMobileBrowser && $('body[data-remove-m-parallax="1"]').length > 0) {
			return;
		}
		
		parallaxRowsBGCals();
		
		$('.nectar-recent-posts-single_featured, .wpb_row.parallax_section, #page-header-bg[data-parallax="1"] .page-header-bg-image-wrap, .parallax_slider_outer .nectar-slider-wrap').each(function(){
		   var $id = $(this).attr('id');	

		    var ua = window.navigator.userAgent;	
		    var msie = ua.indexOf("MSIE ");

			if ($smoothCache == true)  {
				
		   		if($(this).find('[data-parallax-speed="fixed"]').length == 0) { 
						
							if($(this).find('.row-bg').length == 0) {
								$('#'+$id).parallaxScroll("50%", 0.25);
							} else {
								$('#'+$id + ".parallax_section").parallaxScroll("50%", parallaxSrollSpeed($(this).find('.row-bg').attr('data-parallax-speed')) );
							}
					}
					
		  } else if($(this).find('[data-parallax-speed="fixed"]').length == 0) {
				
					if($(this).find('.row-bg').length == 0) {
							//set default scroll speed if not defined
							$('#'+$id).parallaxScroll("50%", 0.25);
					} else {
							$('#'+$id + ".parallax_section").parallaxScroll("50%", parallaxSrollSpeed($(this).find('.row-bg').attr('data-parallax-speed')) );
					}
		  }
			
			$(this).addClass('nectar-parallax-enabled');
			
		});
		
	}
	
	
	
	//add first class for rows for page header trans effect (zoom only as of now)
	$('.full-width-section.wpb_row, .full-width-content.wpb_row').each(function(){
		
		if(!$(this).parent().hasClass('span_9') && !$(this).parent().hasClass('span_3') && $(this).parent().attr('id') != 'sidebar-inner'){
			
			
			if($(this).parents('#portfolio-extra').length > 0 && $('#full_width_portfolio').length == 0) return false;
				
			if($(this).index() == '0' && $('#page-header-bg').length == 0 && $('.page-header-no-bg').length == 0 
							 && $('.project-title').length == 0 && $('body.single').length == 0 
							 && $('.project-title').length == 0 ) {

				$(this).addClass('first-section');
				var $that = $(this);
				setTimeout( function() { $that.addClass('loaded'); },50);
				
			} 
				

		}
	});	
	
	
	

	parallaxScrollInit();
	parallaxRowsBGCals();

	function parallaxRowsBGCals(){
		
		if(nectarDOMInfo.usingMobileBrowser && $('body[data-remove-m-parallax="1"]').length > 0) {
			return;
		}

		$('.nectar-recent-posts-single_featured, .wpb_row.parallax_section, #page-header-bg[data-parallax="1"] .page-header-bg-image-wrap, .parallax_slider_outer .nectar-slider-wrap .slide-bg-wrap').each(function(){
			
			 var ua = window.navigator.userAgent;
		   var msie = ua.indexOf("MSIE ");

			 if ($smoothCache == true) {
			 	 $(this).find('.row-bg').css({'height': $(this).outerHeight(true)*2.8, 'margin-top': '-' + ($(this).outerHeight(true)*2.8)/2 + 'px' });
			 } else {
				 
				 if($(this).find('.row-bg').length == 0 && $(this).find('.page-header-bg-image').length > 0 ) {
					 	//$(this).find('.page-header-bg-image').css({'height': $(this).outerHeight(true)   });
				 } 
				 else if($(this).find('.row-bg').length == 0 && $(this).find('.image-bg').length > 0 ) {
					 	
						var $non_page_builder_slider = false;
						//for first nectar slider shortcode without page builder
				 		if( $('.wpb_row').length == 0 && $(this).parents('.nectar-slider-wrap[data-full-width="true"]').length > 0 && $(this).parents('.parallax_slider_outer').length > 0 && $(this).parents('.parallax_slider_outer').index() == '0' ) {
				 			$non_page_builder_slider = true;
				 		}
						//portfolio first
						if($('#portfolio-extra').length > 0 && $(this).parents('.wpb_row').length > 0 && $(this).parents('.parallax_slider_outer').length > 0 && $(this).parents('.wpb_row').index() == '0' ) {
								$non_page_builder_slider = true;
						}
					 
					 if($(this).parents('.top-level').length > 0 && !nectarDOMInfo.usingFrontEndEditor || $non_page_builder_slider && !nectarDOMInfo.usingFrontEndEditor) {
						 $(this).find('.image-bg').css({'height':  Math.ceil( $(this).parent().offset().top * 0.25 ) + $(this).outerHeight(true) });
					 } else {
						 	$(this).find('.image-bg').css({'height': Math.ceil( $(window).height() * 0.25 ) + $(this).outerHeight(true)   });
					 }
					 
				 }
				 else if($(this).find('.row-bg').length == 0 && $(this).find('.video-wrap').length > 0 ) {
					 
					 
					 var $non_page_builder_slider = false;
					 //for first nectar slider shortcode without page builder
					 if( $('.wpb_row').length == 0 && $(this).parents('.nectar-slider-wrap[data-full-width="true"]').length > 0 && $(this).parents('.parallax_slider_outer').length > 0 && $(this).parents('.parallax_slider_outer').index() == '0' ) {
						 $non_page_builder_slider = true;
					 } 
					 //portfolio first
					 if($('#portfolio-extra').length > 0 && $(this).parents('.wpb_row').length > 0 && $(this).parents('.parallax_slider_outer').length > 0 && $(this).parents('.wpb_row').index() == '0' ) {
							 $non_page_builder_slider = true;
					 }
					 
					 if($(this).parents('.top-level').length > 0 && !nectarDOMInfo.usingFrontEndEditor || $non_page_builder_slider && !nectarDOMInfo.usingFrontEndEditor) {
						 $(this).find('.video-wrap').css({'height': Math.ceil( $(this).parent().offset().top * 0.25 ) + $(this).outerHeight(true) });
					 } else {
						 	$(this).find('.video-wrap').css({'height': Math.ceil( $(window).height() * 0.25 ) + $(this).outerHeight(true)   });
					 }
					 
					 var vid = $(this).find('.video-wrap video');
					 var vid_w_orig = 1280;
					 var vid_h_orig = 720;

					 // get the parent element size
					 var container_w = vid.parent().width();
					 var container_h = vid.parent().height();	 
					 var scale_w = container_w / vid_w_orig;
					 var scale_h = container_h / vid_h_orig;
					 var scale = scale_w > scale_h ? scale_w : scale_h;
					 
					 // scale the video
					 vid.width(scale * vid_w_orig);
					 vid.height(scale * vid_h_orig);

				 }  
				 else {
					  
					 	if($(this).is('.nectar-recent-posts-single_featured') && $(this).parents('.top-level').length > 0 && !nectarDOMInfo.usingFrontEndEditor ) {
							
						}
					 	else if( !$(this).hasClass('top-level') || nectarDOMInfo.usingFrontEndEditor) {
							var $ifFast = ($(this).find('.row-bg[data-parallax-speed="fast"]').length > 0) ? 60 : 0;
						 	$(this).find('.row-bg').css({'height': Math.ceil( $(window).height() * parallaxSrollSpeed($(this).find('.row-bg').attr('data-parallax-speed')) ) + $(this).outerHeight(true) + $ifFast  });
						} 
						
				 }
			 
			 }
			 
		});
	}
	
	//if fullwidth section is first or last, remove the margins so it fits flush against header/footer
	function fwsClasses() {
		
		$('.wpb_wrapper > .nectar-slider-wrap[data-full-width="true"]').each(function(){
			if(!$(this).parent().hasClass('span_9') && !$(this).parent().hasClass('span_3') && $(this).parent().attr('id') != 'sidebar-inner'){
				if($(this).parents('.wpb_row').index() == '0'){
					$(this).addClass('first-nectar-slider');
				} 
			}
		});

		if($('#portfolio-extra').length == 0) {
			$contentElementsNum = ($('.main-content > .row > .wpb_row').length > 0) ? $('.main-content > .row > .wpb_row').length : $('.main-content > .row > *').length;
		} else {
			$contentElementsNum = $('.main-content > .row #portfolio-extra > *').length;
		}

		$('.full-width-section, .full-width-content:not(.page-submenu .full-width-content):not(.blog-fullwidth-wrap), .row > .nectar-slider-wrap[data-full-width="true"], .wpb_wrapper > .nectar-slider-wrap[data-full-width="true"], .portfolio-items[data-col-num="elastic"]').each(function(){
			
			if(!$(this).parent().hasClass('span_9') && !$(this).parent().hasClass('span_3') && $(this).parent().attr('id') != 'sidebar-inner'){
				
				if($(this).parents('.wpb_row').length > 0){ 
				
					if($(this).parents('#portfolio-extra').length > 0 && $('#full_width_portfolio').length == 0) return false;
					
					////first
					if($(this).parents('.wpb_row').index() == '0' && $('#page-header-bg').length != 0 ){
						//$(this).css('margin-top','-2.1em').addClass('first-section nder-page-header');
					} 
					else if($(this).parents('.wpb_row').index() == '0' && $('#page-header-bg').length == 0 && $('.page-header-no-bg').length == 0 
					         && $('.project-title').length == 0 && $(this).parents('.wpb_row').index() == '0' 
					         && $('.project-title').length == 0 
					         && $('body[data-bg-header="true"]').length == 0) {

					     if($('.single').length == 0) {
					     	$('.container-wrap').css('padding-top','0px');
					     } else {
					     	$(this).addClass('first-section');
					     } 	
						
					} 
					
					//check if it's also last (i.e. the only fws)
					if($(this).parents('.wpb_row').index() == $contentElementsNum-1 && $('#respond').length == 0 ) { 
						if($(this).attr('id') != 'portfolio-filters-inline') {
							$('.container-wrap').css('padding-bottom','0px');
							$('#call-to-action .triangle').remove();
						}
					} 
				
				} else {

					if($(this).parents('#portfolio-extra').length > 0 && $('#full_width_portfolio').length == 0) return false;
					
					if( $(this).find('.portfolio-filters-inline').length == 0 && $(this).attr('id') != 'post-area' ) {
						
						////first
						if($(this).index() == '0' && $('#page-header-bg').length != 0 ){
							//$(this).css('margin-top','-2.1em').addClass('first-section nder-page-header');
			
						} 
						else if($(this).index() == '0' && $('#page-header-bg').length == 0 && $(this).index() == '0' && $('.page-header-no-bg').length == 0 && 
						        $(this).index() == '0' && !$(this).hasClass('blog_next_prev_buttons') && !$(this).hasClass('nectar-shop-outer') && $(this).parents('.pum-container').length == 0 ) {
						     
						      if($('body[data-header-resize="0"]').length == 1 && $('.single').length == 0 || $('body.material').length > 0 && $('.single').length == 0) { 
											
											if(!$('body.blog .blog-fullwidth-wrap > .masonry:not(.meta-overlaid)').length > 0) {
						          	$('.container-wrap').css('padding-top','0px');
											}
											
						      } else {
						      	  $(this).addClass('first-section');
						      }   	
							
						} 
						
						//check if it's also last (i.e. the only fws)
						if($(this).index() == $contentElementsNum-1 && $('#respond').length == 0 && $('body.woocommerce-checkout').length == 0) { 
							$('.container-wrap').css('padding-bottom','0px');
							$('.bottom_controls').css('margin-top','0px');
							$('#call-to-action .triangle').remove();
						} 
					}
					
				}
			}
		});


		$('#portfolio-extra > .nectar-slider-wrap[data-full-width="true"], .portfolio-wrap').each(function(){
			//check if it's last 
			if($(this).index() == $contentElementsNum-1 && $('#commentform').length == 0 && $('#pagination').length == 0) { 
				if(parseInt($('.container-wrap').css('padding-bottom')) > 0) $(this).css('margin-bottom','-40px');
				$('#call-to-action .triangle').remove();
			}
		});
		

		$('.portfolio-filters').each(function(){
			////first
			if($(this).index() == '0' && $('#page-header-bg').length != 0 || $(this).index() == '0' ){
				$(this).addClass('first-section nder-page-header');
			}  else if($(this).index() == '0' && $('#page-header-bg').length == 0 || $(this).index() == '0'){
				$(this).css({'margin-top':'0px'}).addClass('first-section');
			}
		});
		
		$('.portfolio-filters-inline').each(function(){
			////first
			if($(this).parents('.wpb_row').length > 0){ 
				
				if($(this).parents('.wpb_row').index() == '0' && $('#page-header-bg').length != 0 || $(this).parents('.wpb_row').index() == '0' ){
					if($('body[data-header-resize="0"]').length == 0) { 
						//$(this).css({'margin-top':'-2.1em', 'padding-top' : '19px'}).addClass('first-section nder-page-header');
					}
				}  
				
			} else {
				if($(this).index() == '0' && $('#page-header-bg').length != 0 || $(this).index() == '0' ){
					$(this).css({'margin-top':'-2.1em', 'padding-top' : '19px'}).addClass('first-section nder-page-header');
				}  else if($(this).index() == '0' && $('#page-header-bg').length == 0 || $(this).index() == '0'){
					
					//if($('body[data-header-resize="0"]').length == 1 || $('body.material').length > 0) { 
				          $(this).css({'margin-top':'-30px', 'padding-top' : '50px'}).addClass('first-section');
				      //} else {
				      //	  $(this).css({'margin-top':'-70px', 'padding-top' : '50px'}).addClass('first-section');
				      //} 

				
				}
			}
			
		});
		

	}
	

	//sizing for fullwidth sections that are image only

	function fullwidthImgOnlySizingInit(){
		////set inital sizes
		$('.full-width-section:not(.custom-skip)').each(function(){
			
			var $fwsHeight = $(this).outerHeight(true);

			//make sure it's empty and also not being used as a small dvider
			if($(this).find('.span_12 *').length == 0 && $.trim( $(this).find('.span_12').text() ).length == 0  && $fwsHeight > 40){
				$(this).addClass('bg-only');
				$(this).css({'height': $fwsHeight, 'padding-top': '0px', 'padding-bottom': '0px'});
				$(this).attr('data-image-height',$fwsHeight);
			}

		});
	}

	function fullwidthImgOnlySizing(){

		$('.full-width-section.bg-only').each(function(){
			var $initialHeight = $(this).attr('data-image-height');
			
			if( window.innerWidth < 1000 && window.innerWidth > 690 ) {
				$(this).css('height', $initialHeight - $initialHeight*.60);
			} 
			
			else if( window.innerWidth <= 690 ) {
				$(this).css('height', $initialHeight - $initialHeight*.78);
			} 
			
			else if( window.innerWidth < 1300 && window.innerWidth >= 1000  ) {
				$(this).css('height', $initialHeight - $initialHeight*.33);
			} 
			
			else {
				$(this).css('height', $initialHeight);
			}
			
		});
		
	}

	fullwidthImgOnlySizingInit();
	fullwidthImgOnlySizing();
	
	
	
	//change % padding on rows to be relative to screen
	function fullWidthRowPaddingAdjustInit(){
		if($('#boxed').length == 0){
			$('.full-width-section, .full-width-content').each(function(){
				var $topPadding = $(this)[0].style.paddingTop;
				var $bottomPadding = $(this)[0].style.paddingBottom;

				if($topPadding.indexOf("%") >= 0) $(this).attr('data-top-percent',$topPadding);
				if($bottomPadding.indexOf("%") >= 0) $(this).attr('data-bottom-percent',$bottomPadding);
				

			});
		}
	}

	function fullWidthRowPaddingAdjustCalc(){
		if($('#boxed').length == 0){
			$('.full-width-section[data-top-percent], .full-width-section[data-bottom-percent], .full-width-content[data-top-percent],  .full-width-content[data-bottom-percent]').each(function(){

				var $windowHeight = $(window).width();
				var $topPadding = ($(this).attr('data-top-percent')) ? $(this).attr('data-top-percent') : 'skip';
				var $bottomPadding = ($(this).attr('data-bottom-percent')) ? $(this).attr('data-bottom-percent') : 'skip';

				//top
				if($topPadding != 'skip') {
					$(this).css('padding-top',$windowHeight*(parseInt($topPadding)/100));
				}

				//bottom
				if($bottomPadding != 'skip'){
					$(this).css('padding-bottom',$windowHeight*(parseInt($bottomPadding)/100));
				}
				

			});
		}
	}
	if(nectarDOMInfo.usingMobileBrowser) {
		fullWidthRowPaddingAdjustCalc();
	}

	
	//full width content column sizing
	function fullWidthContentColumns(){
		
		var $frontEndEditorElDiv = ($('body.vc_editor').length > 0) ? '.vc_element > ': '';
		
		//standard carousel
		$('.main-content > .row > '+$frontEndEditorElDiv+' .full-width-content, #portfolio-extra > '+$frontEndEditorElDiv+' .full-width-content, .woocommerce-tabs #tab-description > .full-width-content, .post-area.span_12 article .content-inner > .full-width-content').each(function(){
			
			//only set the height if more than one column
			if($(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col').length > 1){
				
				var tallestColumn = 0;
				var $columnInnerHeight = 0;
				var $column_inner_selector; 
				
				$(this).find('> .span_12 > '+$frontEndEditorElDiv+'  .col').each(function(){

					$column_inner_selector = ($(this).find('> .vc_column-inner > .wpb_wrapper').length > 0) ? '.vc_column-inner' : '.column-inner-wrap > .column-inner';
					
					var $padding = parseInt($(this).css('padding-top'));
					var $frontEndEditorElPadding = ($frontEndEditorElDiv.length > 2 && $(this).find('> .vc_column-inner').length > 0) ? parseInt($(this).find('> .vc_column-inner').css('padding-top')) : 0;

					($(this).find('> '+$column_inner_selector+' > .wpb_wrapper').height() + ($padding*2) + $frontEndEditorElPadding > tallestColumn) ? tallestColumn = $(this).find('> '+$column_inner_selector+' > .wpb_wrapper').height() + ($padding*2) + $frontEndEditorElPadding : tallestColumn = tallestColumn;
				});	
	    	 	
	    	 	$(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col').each(function(){

	    	 		$column_inner_selector = ($(this).find('> .vc_column-inner > .wpb_wrapper').length > 0) ? '.vc_column-inner' : '.column-inner-wrap > .column-inner';
					
	    	 		//columns with content
		    	 	if($(this).find('> '+$column_inner_selector+' > .wpb_wrapper > *').length > 0){
		    	 		//added in 7.6 to fix equal height columns 
							if($frontEndEditorElDiv.length < 2 && !$(this).parent().parent().hasClass('vc_row-o-equal-height')) { $(this).css('height',tallestColumn); }
							else if($frontEndEditorElDiv.length > 2 && !$(this).parent().parent().parent().hasClass('vc_row-o-equal-height')) { $(this).css('height',tallestColumn); }
		    	 	} 
		    	 	//empty columns
		    	 	else {
		    	 		$(this).css('min-height',tallestColumn);
		    	 		if($(this).is('[data-animation*="reveal"]')) $(this).find('.column-inner').css('min-height',tallestColumn);
		    	 	}
	    	 	});
	         	
	         	//nested column height
						var $childRows = $(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col .wpb_row').length;
	         	if(window.innerWidth > 1000) { 
	         		
	         		var $padding = parseInt($(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col').css('padding-top'));
	         		
	         		//$(this).find('> .span_12 > .col .wpb_row .col').css('min-height',(tallestColumn-($padding*2))/$childRows + 'px'); 
	         	} else {
	         		$(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col .wpb_row .col').css('min-height','0px'); 
	         	}
	         	
	         	
	         	//vertically center
	         	if($(this).hasClass('vertically-align-columns') && window.innerWidth > 1000 && !$(this).hasClass('vc_row-o-equal-height')){
	         		
	         		//parent columns
		         	$(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col').each(function(){

		         		$column_inner_selector = ($(this).find('> .vc_column-inner > .wpb_wrapper').length > 0) ? '.vc_column-inner' : '.column-inner-wrap > .column-inner';
						
						$columnInnerHeight = $(this).find('> '+$column_inner_selector+' > .wpb_wrapper').height();
						var $marginCalc = ($(this).height()/2)-($columnInnerHeight/2);
						if($marginCalc <= 0) $marginCalc = 0;
						
						$(this).find('> '+$column_inner_selector+' > .wpb_wrapper').css('margin-top',$marginCalc);
						$(this).find('> '+$column_inner_selector+' > .wpb_wrapper').css('margin-bottom',$marginCalc);
						
					});	
	
					
				}
			
			}
			
   	  	});

		//equal height columns in container type with reveal columns
		$('.main-content > .row > .wpb_row:not(.full-width-content).vc_row-o-equal-height').each(function(){
			if($(this).find('>.span_12 > '+$frontEndEditorElDiv+' .wpb_column[data-animation*="reveal"]').length >0) {
				var tallestColumn = 0;
				var $columnInnerHeight = 0;
				
			$(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col').each(function(){
					
					var $padding = parseInt($(this).find('> .column-inner-wrap > .column-inner').css('padding-top'));
					($(this).find('> .column-inner-wrap > .column-inner').height() + ($padding*2) > tallestColumn) ? tallestColumn = $(this).find('> .column-inner-wrap > .column-inner').height() + ($padding*2)  : tallestColumn = tallestColumn;
				});	
	    	 	
	    	 	$(this).find('> .span_12 > '+$frontEndEditorElDiv+' .col').each(function(){
					
	    	 		//columns with content
		    	 	if($(this).find('> .column-inner-wrap > .column-inner .wpb_wrapper > *').length > 0){
		    	 		$(this).find('> .column-inner-wrap').css('height',tallestColumn);
		    	 	} 
		    	 	//empty columns
		    	 	else {
		    	 		$(this).css('min-height',tallestColumn);
		    	 		if($(this).is('[data-animation*="reveal"]')) $(this).find('.column-inner').css('min-height',tallestColumn);
		    	 	}
	    	 	});

			}	
		});

		//using equal height option, top/bottom padding % needs to be convered into px for cross browser (flex bug)
		$('.wpb_row.vc_row-o-equal-height>.span_12> '+$frontEndEditorElDiv+'.wpb_column[class*="padding-"][data-padding-pos="all"]').each(function(){
			if($(this).parents('.tabbed').length == 0) {
				$(this).css({ 'padding-top': $(this).css('padding-left'), 'padding-bottom': $(this).css('padding-left')});
			}
		});
		
	}
	
	fullWidthContentColumns();
	if($('.owl-carousel').length > 0) owlCarouselInit();


var $mouseParallaxScenes = [];
function mouseParallaxInit(){
	$('.wpb_row:has(.nectar-parallax-scene)').each(function(i){

		var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-space').height();
		
		var $strength = parseInt($(this).find('.nectar-parallax-scene').attr('data-scene-strength'));

		$mouseParallaxScenes[i] = $(this).find('.nectar-parallax-scene').parallax({
			scalarX: $strength,
	  		scalarY: $strength
		});

		//wait until the images in the scene have loaded
		var images = $(this).find('.nectar-parallax-scene li');
		
		$.each(images, function(){
			if($(this).find('div').length > 0) {
			    var el = $(this).find('div'),
			    image = el.css('background-image').replace(/"/g, '').replace(/url\(|\)$/ig, '');
			    if(image && image !== '' && image !== 'none')
			        images = images.add($('<img>').attr('src', image));
			}
		});

		var $that = $(this);


	});
}
mouseParallaxInit();


	
/***************** Checkmarks ******************/

function ulChecks() {
	$('ul.checks li').each(function(){
		if( $(this).find('i.icon-ok-sign').length == 0 ) {
			$(this).prepend('<i class="icon-ok-sign"></i>');
		}
	});
}
ulChecks();



function rowBGAnimations() {
	var $rowBGAnimationsOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '93%';
	
	$($fullscreenSelector+'.row-bg-wrap[data-bg-animation]:not([data-bg-animation="none"]):not([data-bg-animation*="displace-filter"]) .row-bg.using-image').each(function() {
		
		var $that = $(this);
		var $animationEasing = ($('body[data-cae]').length > 0) ? $('body').attr('data-cae') : 'easeOutSine';
		var $animationDuration = ($('body[data-cad]').length > 0) ? $('body').attr('data-cad') : '650';
		
		var waypoint = new Waypoint({
			 element: $that.parents('.row-bg-wrap'),
			 handler: function(direction) {
				 
				 if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
						waypoint.destroy();
					 return;
				 }
				 
					$that.parents('.inner-wrap').addClass('animated-in');
				 
				 waypoint.destroy();
			 },
			 offset: $rowBGAnimationsOffsetPos
		});
		
	});
	
}

function columnBGAnimations() {
	var $rowBGAnimationsOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '93%';
	
	$($fullscreenSelector+'.column-image-bg-wrap[data-bg-animation]:not([data-bg-animation="none"]):not([data-bg-animation*="displace-filter"]) .column-image-bg').each(function() {
		
		var $that = $(this);
		var $animationEasing = ($('body[data-cae]').length > 0) ? $('body').attr('data-cae') : 'easeOutSine';
		var $animationDuration = ($('body[data-cad]').length > 0) ? $('body').attr('data-cad') : '650';
		
		var waypoint = new Waypoint({
			 element: $that.parents('.column-image-bg-wrap'),
			 handler: function(direction) {
				 
				 if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
						waypoint.destroy();
					 return;
				 }
				 
					$that.parents('.inner-wrap').addClass('animated-in');
				 
				 waypoint.destroy();
			 },
			 offset: $rowBGAnimationsOffsetPos
		});
		
	});
	
}

/***************** Image with Animation / Col Animation *******************/


	
function colAndImgAnimations(){

	var $colAndImgOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '88%';
	var $colAndImgOffsetPos2 = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '70%';

	$($fullscreenSelector+'img.img-with-animation').each(function() {
		
		var $that = $(this);
		var $animationEasing = ($('body[data-cae]').length > 0) ? $('body').attr('data-cae') : 'easeOutSine';
		var $animationDuration = ($('body[data-cad]').length > 0) ? $('body').attr('data-cad') : '650';

		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
						 waypoint.destroy();
						return;
					}

					if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) || $('body[data-responsive="0"]').length > 0) {
				
						if($that.attr('data-animation') == 'fade-in-from-left'){
							$that.delay($that.attr('data-delay')).transition({
								'opacity' : 1,
								'x' : '0px'
							},$animationDuration, $animationEasing);
						} else if($that.attr('data-animation') == 'fade-in-from-right'){
							$that.delay($that.attr('data-delay')).transition({
								'opacity' : 1,
								'x' : '0px'
							},$animationDuration, $animationEasing);
						} else if($that.attr('data-animation') == 'fade-in-from-bottom'){
							$that.delay($that.attr('data-delay')).transition({
								'opacity' : 1,
								'y' : '0px'
							},$animationDuration, $animationEasing);
						} else if($that.attr('data-animation') == 'fade-in') {
							$that.delay($that.attr('data-delay')).transition({
								'opacity' : 1
							},$animationDuration, $animationEasing);	
						} else if($that.attr('data-animation') == 'grow-in') {
							setTimeout(function(){ 
								$that.transition({ scale: 1, 'opacity':1 },$animationDuration,$animationEasing);
							},$that.attr('data-delay'));
						}
						else if($that.attr('data-animation') == 'flip-in') {
							setTimeout(function(){ 
								$that.transition({  rotateY: 0, 'opacity':1 },$animationDuration, $animationEasing);
							},$that.attr('data-delay'));
						}
						else if($that.attr('data-animation') == 'flip-in-vertical') {
							setTimeout(function(){ 
								$that.transition({  rotateX: 0, 'opacity':1 },$animationDuration, $animationEasing);
							},$that.attr('data-delay'));
						}

						$that.addClass('animated-in');
						
					}

					waypoint.destroy();

			  },
			  offset: $colAndImgOffsetPos
		});

		
	
	});


	$($fullscreenSelector+'.nectar_cascading_images').each(function() {
		
		var $that = $(this);
		var $animationEasing = ($('body[data-cae]').length > 0) ? $('body').attr('data-cae') : 'easeOutSine';
		var $animationDuration = ($('body[data-cad]').length > 0) ? $('body').attr('data-cad') : '650';
		var $animationDelay = ($(this).is('[data-animation-timing]')) ? $(this).attr('data-animation-timing') : 175;
		$animationDelay = parseInt($animationDelay);

		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
						 waypoint.destroy();
						return;
					}

					if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) || $('body[data-responsive="0"]').length > 0) {
					
						$that.find('.cascading-image').each(function(i){

							var $that2 = $(this);

							if($that2.attr('data-animation') == 'flip-in' || $that2.attr('data-animation') == 'flip-in-vertical') {
								setTimeout(function(){
									$that2.find('.inner-wrap').css({
										'opacity' : 1,
										'transform' : 'rotate(0deg) translateZ(0px)'
									});
								}, i* $animationDelay);
							} else {
								setTimeout(function(){
									$that2.find('.inner-wrap').css({
										'opacity' : 1,
										'transform' : 'translateX(0px) translateY(0px) scale(1,1) translateZ(0px)'
									});
								}, i* $animationDelay);
							}
					

						});

						$that.addClass('animated-in');
						
					}

					waypoint.destroy();

			  },
			  offset: $colAndImgOffsetPos
		});

		
	
	});
	

	
	$($fullscreenSelector+'.col.has-animation:not([data-animation*="reveal"]), '+$fullscreenSelector+'.wpb_column.has-animation:not([data-animation*="reveal"]), '+$fullscreenSelector+'.nectar-fancy-box.has-animation').each(function(i) {
	    
		var $that = $(this);
		var $animationEasing = ($('body[data-cae]').length > 0) ? $('body').attr('data-cae') : 'easeOutSine';
		var $animationDuration = ($('body[data-cad]').length > 0) ? $('body').attr('data-cad') : '650';

		//set perspective for vertical flip
		if($that.is('[data-animation="flip-in-vertical"]')) {
			$that.parents('.col.span_12').addClass('flip-in-vertical-wrap');
		}

		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
				
				if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
					 waypoint.destroy();
					return;
				}

				if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) || $('body[data-responsive="0"]').length > 0) {
				 	
				 	
					if($that.attr('data-animation') == 'fade-in-from-left'){
						$standAnimatedColTimeout[i] = setTimeout(function(){ 
							$that.transition({
								'opacity' : 1,
								'x' : '0px'
							},$animationDuration,$animationEasing);
						},$that.attr('data-delay'));
					} else if($that.attr('data-animation') == 'fade-in-from-right'){
						$standAnimatedColTimeout[i] = setTimeout(function(){ 
							$that.transition({
								'opacity' : 1,
								'x' : '0px'
							},$animationDuration,$animationEasing);
						},$that.attr('data-delay'));
					} else if($that.attr('data-animation') == 'fade-in-from-bottom'){
						$standAnimatedColTimeout[i] = setTimeout(function(){ 
							$that.transition({
								'opacity' : 1,
								'y' : '0px'
							},$animationDuration,$animationEasing);
						},$that.attr('data-delay'));
					} else if($that.attr('data-animation') == 'fade-in') {
						$standAnimatedColTimeout[i] = setTimeout(function(){ 
							$that.transition({
								'opacity' : 1
							},$animationDuration,$animationEasing);	
						},$that.attr('data-delay'));
					} else if($that.attr('data-animation') == 'grow-in' || $that.attr('data-animation') == 'zoom-out') {
						$standAnimatedColTimeout[i] = setTimeout(function(){ 
							$that.transition({ scale: 1, 'opacity':1 },$animationDuration,$animationEasing);
						},$that.attr('data-delay'));
					} else if($that.attr('data-animation') == 'flip-in') {
						$standAnimatedColTimeout[i] = setTimeout(function(){ 
							$that.transition({  rotateY: 0, 'opacity':1 },$animationDuration, $animationEasing);
						},$that.attr('data-delay'));
					} else if($that.attr('data-animation') == 'flip-in-vertical') {
						$standAnimatedColTimeout[i] = setTimeout(function(){ 
							$that.transition({  rotateX: 0, y: 0, 'opacity':1 },$animationDuration, $animationEasing);
						},$that.attr('data-delay'));
					}

					//boxed column hover fix
					if($that.hasClass('boxed')) {
						$that.addClass('no-pointer-events');
						setTimeout(function(){
							$that.removeClass('no-pointer-events');
						},parseInt($animationDuration) + parseInt($that.attr('data-delay')) + 30 );
					}

					$that.addClass('animated-in');
				
				}

				waypoint.destroy();
			},
			offset: $colAndImgOffsetPos
		});
	
	});

	
	$($fullscreenSelector+'.wpb_column.has-animation[data-animation*="reveal"]').each(function() {
	    
		var $that = $(this);
		var $animationEasing = ($('body[data-cae]').length > 0) ? $('body').attr('data-cae') : 'easeOutSine';
		var $animationDuration = ($('body[data-cad]').length > 0) ? $('body').attr('data-cad') : '650';

		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
				
				if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
					 waypoint.destroy();
					return;
				}

				if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) || $('body[data-responsive="0"]').length > 0) {
					
					if($that.attr('data-animation') == 'reveal-from-bottom' || $that.attr('data-animation') == 'reveal-from-top') {
						setTimeout(function(){ 
							if($that.hasClass('animated-in')) $that.find('.column-inner-wrap, .column-inner').transition({  'y': 0 },$animationDuration, $animationEasing,function(){ if($that.hasClass('animated-in')) $that.find('.column-inner-wrap, .column-inner').addClass('no-transform'); });
						},$that.attr('data-delay'));
					} else if($that.attr('data-animation') == 'reveal-from-right' || $that.attr('data-animation') == 'reveal-from-left') {
						setTimeout(function(){ 
							if($that.hasClass('animated-in'))  $that.find('.column-inner-wrap, .column-inner').transition({  'x': 0 },$animationDuration, $animationEasing,function(){ if($that.hasClass('animated-in')) $that.find('.column-inner-wrap, .column-inner').addClass('no-transform'); });
						},$that.attr('data-delay'));
					} 

					$that.addClass('animated-in');
				
				}

				waypoint.destroy();
			},
			offset: $colAndImgOffsetPos2
		});
	
	}); 	

	
}



function cascadingImageBGSizing() {
	$('.nectar_cascading_images').each(function(){

		//handle max width for cascading images in equal height columns
		if($(this).parents('.vc_row-o-equal-height').length > 0 && $(this).parents('.wpb_column').length > 0) 
			$(this).css('max-width',$(this).parents('.wpb_column').width());

		//set size for layers with no images
		$(this).find('.bg-color').each(function(){
			var $bgColorHeight = 0;
			var $bgColorWidth = 0;
			if($(this).parent().find('.img-wrap').length == 0) {
				var $firstSibling = $(this).parents('.cascading-image').siblings('.cascading-image[data-has-img="true"]').first();

				$firstSibling.css({'position':'relative', 'visiblity':'hidden'});
				$bgColorHeight = $firstSibling.find('.img-wrap').height();
				$bgColorWidth = $firstSibling.find('.img-wrap').width();
				if($firstSibling.index() == 0) {
					$firstSibling.css({'visiblity':'visible'});
				} else {
					$firstSibling.css({'position':'absolute', 'visiblity':'visible'});
				}
			} else {
				$bgColorHeight = $(this).parent().find('.img-wrap').height();
				$bgColorWidth = $(this).parent().find('.img-wrap').width();
			}

			$(this).css({'height': $bgColorHeight,'width': $bgColorWidth});
		});
	});
}

if($('.nectar_cascading_images').length > 0) {
	imagesLoaded($('.nectar_cascading_images'),function(instance){
		cascadingImageBGSizing();
	});
}

function splitLineHeadings() {

	var $splitLineOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : 'bottom-in-view';
	$($fullscreenSelector+'.nectar-split-heading').each(function() {

		var $that = $(this);
		var $animationEasing = ($('body[data-cae]').length > 0) ? $('body').attr('data-cae') : 'easeOutSine';
		var $animationDuration = ($('body[data-cad]').length > 0) ? $('body').attr('data-cad') : '650';

		var waypoint = new Waypoint({
				element: $that,
				 handler: function(direction) {
				
				if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
					 waypoint.destroy();
					return;
				}

				if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) || $('body[data-responsive="0"]').length > 0) {
				 	
					
					$that.find('.heading-line').each(function(i){
						//if($that.parents('.first-section').length > 0 && $('body[data-aie="zoom-out"]').length > 0) i = i+4;
						$(this).find('> div').delay(i*70).transition({
							'y' : '0px'
						},$animationDuration,$animationEasing);

					});
					

					$that.addClass('animated-in');
				
				}

				waypoint.destroy();
			},
			offset: $splitLineOffsetPos
		});

	});
}

	
/***************** Custom tablet default column widths  ******************/
	
	//add one-fourth class
	function oneFourthClasses() {
		$('.col.span_3, .vc_span3, .vc_col-sm-3').each(function(){
			if( !$(this).is('[data-t-w-inherits="small_desktop"]') ) {
				var $currentDiv = $(this);
				var $nextDiv = $(this).next('div');
				if( $nextDiv.hasClass('span_3') && !$currentDiv.hasClass('one-fourths') || $nextDiv.hasClass('vc_span3') && !$currentDiv.hasClass('one-fourths') || $nextDiv.hasClass('vc_col-sm-3') && !$currentDiv.hasClass('one-fourths') ) {
					$currentDiv.addClass('one-fourths clear-both');
					$nextDiv.addClass('one-fourths right-edge');
				}
			}
		});
		
		//make empty second 1/2 half columsn display right on iPad
		$('.span_12 .col.span_6').each(function(){
			if($(this).next('div').hasClass('span_6') && $.trim( $(this).next('div').html() ).length == 0 ) {
				$(this).addClass('empty-second')
			}
		}); 
		
	}
	oneFourthClasses();
	
/***************** Bar Graph ******************/
function progressBars(){
	var $progressBarsOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : 'bottom-in-view';
	$($fullscreenSelector+'.nectar-progress-bar').parent().each(function(i){
		
		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('completed')) { 
						 waypoint.destroy();
						return;
					}

					if($progressBarsOffsetPos == '100%') $that.find('.nectar-progress-bar .bar-wrap').css('opacity','1');

					$that.find('.nectar-progress-bar').each(function(i){


						var percent = $(this).find('span').attr('data-width');
						var $endNum = parseInt($(this).find('span strong i').text());
						var $that = $(this);
						
						$that.find('span').delay(i*90).transition({
							'width' : percent + '%'
						},1050, 'easeInOutQuint',function(){
						});
	
						setTimeout(function(){

							var countOptions = { useEasing : false };
							var $countEle = $that.find('span strong i')[0];
							var numAnim = new CountUp($countEle, 0, $endNum,0,1,countOptions);
							numAnim.start();

							$that.find('span strong').transition({
								'opacity' : 1
							},550, 'easeInCirc');
						}, (i*90) );
					
						////100% progress bar 
						if(percent == '100'){
							$that.find('span strong').addClass('full');
						}
					});

					$that.addClass('completed');

					waypoint.destroy();

			  },
			  offset: $progressBarsOffsetPos
		});

	});
}


/***************** Column Borders ******************/
function animatedColBorders(){
	var $progressBarsOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '75%';
	$($fullscreenSelector+'.wpb_column[data-border-animation="true"]').each(function(i){

		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('completed')) { 
						 waypoint.destroy();
						return;
					}

					var $borderDelay = ($that.attr('data-border-animation-delay').length > 0) ? parseInt($that.attr('data-border-animation-delay')) : 0;
					setTimeout(function(){
						$that.find('.border-wrap').addClass('animation');
						$that.find('.border-wrap').addClass('completed');
					},$borderDelay)
					

					waypoint.destroy();

			  },
			  offset: $progressBarsOffsetPos
		});

	});
}

/***************** Food Menu Item ******************/
function foodMenuItems() {
	var $foodItemOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '80%';
	$($fullscreenSelector+'.nectar_food_menu_item').parent().each(function(i){

		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('completed')) { 
						 waypoint.destroy();
						return;
					}

					$that.find('.nectar_food_menu_item').each(function(i){

						var $that = $(this);

						setTimeout(function(){
							$that.addClass('animated-in');
						},i*150);
						
					});
					
					waypoint.destroy();

			  },
			  offset: $foodItemOffsetPos
		});

	});
}


/***************** Dividers ******************/
function dividers() {
	var $dividerOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : 'bottom-in-view';

	$($fullscreenSelector+'.divider-small-border[data-animate="yes"], '+$fullscreenSelector+'.divider-border[data-animate="yes"]').each(function(i){

		var $lineDur = ($(this).hasClass('divider-small-border')) ? 1300 : 1500;
		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('completed')) { 
						 waypoint.destroy();
						return;
					}
				
					$that.each(function(i){

						$(this).css({'transform':'scale(0,1)', 'visibility': 'visible'});
						var $that = $(this);
						
						$that.delay($that.attr('data-animation-delay')).transition({
							'transform' : 'scale(1, 1)'
						},$lineDur, 'cubic-bezier(.18,1,.22,1)');
						
					});

					$that.addClass('completed');

					waypoint.destroy();

			  },
			  offset: $dividerOffsetPos
		});

	});
}



/***************** Icon List ******************/
function iconList() {
	var $iconListOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '250%' : '75%';

	$($fullscreenSelector+'.nectar-icon-list[data-animate="true"]').each(function(i){

		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('completed')) { 
						 waypoint.destroy();
						return;
					}
				
					$that.each(function(i){
						
						var $listItemAnimationDelay = ( $that.is('[data-direction="horizontal"]') ) ? 100 : 300;
						
						$(this).find('.nectar-icon-list-item').each(function(i){
							var $thatt = $(this);
							setTimeout(function(){ $thatt.addClass('animated') },i*$listItemAnimationDelay);
						});
						
					});

					$that.addClass('completed');

					waypoint.destroy();

			  },
			  offset: $iconListOffsetPos
		});

	});
}

//bg color match 

function narrowParentBGC(element) {
	
	var narrowedBGC;
	
	if(element.parents('.wpb_column[data-bg-color*="#"]').length > 0 && element.parents('.wpb_column[data-bg-opacity="1"]').length > 0) {
		var narrowedBGC = element.parents('.wpb_column').attr('data-bg-color');
	}
	else if(element.parents('.wpb_row').length > 0 && element.parents('.wpb_row').find('.row-bg.using-bg-color').length > 0) {
		var narrowedBGC = element.parents('.wpb_row').find('.row-bg.using-bg-color').css('background-color');
	}
	else {
		if($('#nectar_fullscreen_rows').length > 0)
			var narrowedBGC = $('#nectar_fullscreen_rows > .wpb_row .full-page-inner-wrap').css('background-color');
		else 
			var narrowedBGC = $('.container-wrap').css('background-color');
	}
	
	return narrowedBGC;
	
}

var nectarMatchingBGCss = '';

function nectarIconMatchColoring() {
	
	//icon list
	$('.nectar-icon-list[data-icon-style="border"], .nectar_icon_wrap[data-style="border-animation"][data-color*="extra-color-gradient-"]').each(function(i){
		
		var $bgColorToSet = narrowParentBGC($(this));
		
		if($(this).hasClass('nectar-icon-list')) 
			$(this).find('.list-icon-holder').css('background-color',$bgColorToSet);
		else {
			//must be set in css, can't manip pseudo
			$(this).removeClass(function (index, className) {
			    return (className.match (/(^|\s)instance-\S+/g) || []).join(' ');
			});
			$(this).addClass('instance-'+i);
			nectarMatchingBGCss += '.nectar_icon_wrap.instance-'+i+' .nectar_icon:before { background-color: '+$bgColorToSet+'!important; }';
		}
			
	});

	//material gradient btns
	$('body.material .nectar-button.see-through[class*="m-extra-color-gradient"]').each(function(i){
		
		var $bgColorToSet = narrowParentBGC($(this));
		
		$(this).removeClass(function (index, className) {
				return (className.match (/(^|\s)instance-\S+/g) || []).join(' ');
		});
		$(this).addClass('instance-'+i);
		nectarMatchingBGCss += '.nectar-button.see-through.instance-'+i+':after { background-color: '+$bgColorToSet+'!important; }';
		
	});

	if(nectarMatchingBGCss.length > 0) {
		var head = document.head || document.getElementsByTagName('head')[0];
		var style = document.createElement('style');

		style.type = 'text/css';
		if (style.styleSheet){
		  style.styleSheet.cssText = nectarMatchingBGCss;
		} else {
		  style.appendChild(document.createTextNode(nectarMatchingBGCss));
		}
		
		$(style).attr('id','nectaricon-color-match');
		$('head #nectaricon-color-match').remove();
		head.appendChild(style);
	}
	
}
nectarIconMatchColoring();


/***************** Hotspot ******************/
//add pulse animation
$('.nectar_image_with_hotspots[data-hotspot-icon="numerical"]').each(function(){
	$(this).find('.nectar_hotspot_wrap').each(function(i){
		var $that = $(this);
		setTimeout(function(){
			$that.find('.nectar_hotspot').addClass('pulse');
		},i*300);	
	});
});



function hotSpotHoverBind() {

	var hotSpotHoverTimeout = [];

	$('.nectar_image_with_hotspots:not([data-tooltip-func="click"]) .nectar_hotspot').each(function(i){
		
		hotSpotHoverTimeout[i] = '';

		$(this).on('mouseover', function(){
			clearTimeout(hotSpotHoverTimeout[i]);
			$(this).parent().css({'z-index':'400', 'height':'auto','width':'auto'});
		});

		$(this).on('mouseleave', function(){

			var $that = $(this);
			$that.parent().css({'z-index':'auto'});

			hotSpotHoverTimeout[i] = setTimeout(function(){
				$that.parent().css({'height':'30px','width':'30px'});
			},300);

		});

	});

}

hotSpotHoverBind();

function responsiveTooltips() {

	$('.nectar_image_with_hotspots').each(function(){
		$(this).find('.nectar_hotspot_wrap').each(function(i){
			
			if(  window.innerWidth > 690) {

				//remove click if applicable
				if($(this).parents('.nectar_image_with_hotspots[data-tooltip-func="hover"]').length > 0) {
					$(this).find('.nectar_hotspot').removeClass('click');
					$(this).find('.nttip').removeClass('open');
				}
				$(this).find('.nttip .inner a.tipclose').remove();
				$('.nttip').css('height','auto');

				//reset for positioning
				$(this).css({'width': 'auto','height': 'auto'});
				$(this).find('.nttip').removeClass('force-right').removeClass('force-left').removeClass('force-top').css('width','auto');

				var $tipOffset = $(this).find('.nttip').offset();

				//against right side fix
				if($tipOffset.left > $(this).parents('.nectar_image_with_hotspots').width() - 200)
					$(this).find('.nttip').css('width','250px');
				else 
					$(this).find('.nttip').css('width','auto');

				//responsive
				if($tipOffset.left < 0)
					$(this).find('.nttip').addClass('force-right');
				else if($tipOffset.left + $(this).find('.nttip').outerWidth(true) >  window.innerWidth )
					$(this).find('.nttip').addClass('force-left').css('width','250px');
				else if($tipOffset.top + $(this).find('.nttip').height() + 35 > $(window).height() && $('#nectar_fullscreen_rows').length > 0)
					$(this).find('.nttip').addClass('force-top');

				if($(this).find('> .open').length == 0)
					$(this).css({'width': '30px','height': '30px'});

			} else {
				//fixed position
				$(this).find('.nttip').removeClass('force-left').removeClass('force-right').removeClass('force-top');
				$(this).find('.nectar_hotspot').addClass('click');
			
				if($(this).find('.nttip a.tipclose').length == 0)
					$(this).find('.nttip .inner').append('<a href="#" class="tipclose"><span></span></a>');

				//change height of fixed
				$('.nttip').css('height',$(window).height());
			}
		});
	});


}
responsiveTooltips();

function imageWithHotspotClickEvents() {
	//click
	$('body').on('click','.nectar_hotspot.click',function(){
		$(this).parents('.nectar_image_with_hotspots').find('.nttip').removeClass('open');
		$(this).parent().find('.nttip').addClass('open');

		$(this).parents('.nectar_image_with_hotspots').find('.nectar_hotspot').removeClass('open');
		$(this).parent().find('.nectar_hotspot').addClass('open');

		if( window.innerWidth > 690) {
			$(this).parent().css({'z-index':'120', 'height':'auto','width':'auto'});

			var $that = $(this);

			setTimeout(function(){
				$that.parents('.nectar_image_with_hotspots').find('.nectar_hotspot_wrap').each(function(){
					if($(this).find('> .open').length == 0)
						$(this).css({'height':'30px','width':'30px', 'z-index':'auto'});
				});
			},300);
		}

		if(  window.innerWidth <= 690) $(this).parents('.wpb_row, [class*="vc_col-"]').css('z-index','200');

		return false;
	});

	$('body').on('click','.nectar_hotspot.open',function(){
		$(this).parent().find('.nttip').removeClass('open');
		$(this).parent().find('.nectar_hotspot').removeClass('open');

		$(this).parents('.wpb_row').css('z-index','auto');

		return false;
	});

	$('body').on('click','.nttip.open',function(){
		if(  window.innerWidth < 690) {
			$(this).parents('.nectar_image_with_hotspots').find('.nttip').removeClass('open');
			$(this).parents('.wpb_row').css('z-index','auto');
			return false;
		}
	});
}
imageWithHotspotClickEvents();

function imageWithHotspots() {

	var $imageWithHotspotsOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '50%';

	$($fullscreenSelector+'.nectar_image_with_hotspots[data-animation="true"]').each(function(i){

		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('completed')) { 
						 waypoint.destroy();
						return;
					}

					$that.addClass('completed');
					$that.find('.nectar_hotspot_wrap').each(function(i){
						var $that2 = $(this);
						var $extrai = ($that2.parents('.col.has-animation').length > 0) ? 1 : 0;
						setTimeout(function(){
							$that2.addClass('animated-in');
						},175*(i+$extrai));
					});

					waypoint.destroy();

			  },
			  offset: $imageWithHotspotsOffsetPos
		});

	});
}


/***************** Animated Title ******************/
function animated_titles() {
	var $animatedTitlesOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : 'bottom-in-view';

	$($fullscreenSelector+'.nectar-animated-title').each(function(i){

		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('completed')) { 
						 waypoint.destroy();
						return;
					}

					$that.addClass('completed');

					waypoint.destroy();

			  },
			  offset: $animatedTitlesOffsetPos
		});

	});
}


/***************** Highlighted Text ******************/
function highlighted_text() {
	var $highlightedTextOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : 'bottom-in-view';

	$($fullscreenSelector+'.nectar-highlighted-text').each(function(i){
		
		/*custom color*/
		if($(this).is('[data-using-custom-color="true"]')) {
			
				var highlightedColorCss = '';
				var $custom_highlight_color = $(this).attr('data-color');
			
				$(this).addClass('instance-'+i);
			
				highlightedColorCss += '.nectar-highlighted-text.instance-' + i + ' em:before { background-color: ' + $custom_highlight_color + '; }';
			
				if(highlightedColorCss.length > 1) {
		
					var head = document.head || document.getElementsByTagName('head')[0];
					var style = document.createElement('style');
		
						style.type = 'text/css';
					if (style.styleSheet){
					  style.styleSheet.cssText = highlightedColorCss;
					} else {
					  style.appendChild(document.createTextNode(highlightedColorCss));
					}
		
					head.appendChild(style);
				}
			
		}
		
		if(nectarDOMInfo.usingMobileBrowser) {
			$(this).find('em').addClass('animated');
		}
		
		/*waypoint*/
		var $that = $(this);
		var waypoint = new Waypoint({
 			element: $that,
 			 handler: function(direction) {
			   
					if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated')) { 
						 waypoint.destroy();
						return;
					}
					
					$that.find('em').each(function(i){
						var $highlighted_em = $(this);
						setTimeout(function(){
							$highlighted_em.addClass('animated');
						},i*300);

					});

					waypoint.destroy();

			  },
			  offset: $highlightedTextOffsetPos
		});

	});
}



/***************** Pricing Tables ******************/


var $tallestCol;

function pricingTableHeight(){
	$('.pricing-table[data-style="default"]').each(function(){
		$tallestCol = 0;
		
		$(this).find('> div ul').each(function(){
			($(this).height() > $tallestCol) ? $tallestCol = $(this).height() : $tallestCol = $tallestCol;
		});	
		
		//safety net incase pricing tables height couldn't be determined
		if($tallestCol == 0) $tallestCol = 'auto';
		
		//set even height
		$(this).find('> div ul').css('height',$tallestCol);

	});
}

pricingTableHeight();

 
/***************** Testimonial Slider ******************/

//testimonial slider controls

//non minimal
$('body').on('click','.testimonial_slider:not([data-style*="multiple_visible"]):not([data-style="minimal"]) .controls li', function(){
	
	if($(this).find('span').hasClass('active')) return false;
	
	var $frontEndEditorTestimonialDiv =  ($('body.vc_editor').length > 0) ? '> div': 'blockquote';
	var $index = $(this).index();
	var currentHeight = $(this).parents('.testimonial_slider').find('.slides blockquote').eq($index).height();
	
	$(this).parents('.testimonial_slider').find('li span').removeClass('active');
	$(this).find('span').addClass('active');
	
	$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).addClass('no-trans');
	$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).css({'opacity':'0', 'transform': 'translateX(-25px)', 'z-index': '1'});
	$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).eq($index).removeClass('no-trans').css({'opacity':'1', 'transform': 'translateX(0px)'}).css('z-index','20');
	$(this).parents('.testimonial_slider:not(.disable-height-animation)').find('.slides').stop(true,true).animate({'height' : currentHeight + 40 + 'px' },450,'easeOutCubic');
	
	resizeVideoToCover();
});

//minimal
$('body').on('click','.testimonial_slider[data-style="minimal"] .testimonial-next-prev a', function(){
	
	var $frontEndEditorTestimonialDiv =  ($('body.vc_editor').length > 0) ? '> div': 'blockquote';
	var $index = $(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv+'.active').index();
	var $actualIndex = $index;

	$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).addClass('no-trans');
	$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).css({'opacity':'0', 'transform': 'translateX(-25px)', 'z-index': '1'});


	$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).eq($index).removeClass('active');

	if($(this).hasClass('next')) {
		if($index+1 >= $(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).length) { 
			$actualIndex = 0; 
		} else {
			$actualIndex = $index+1; 
		}
		var currentHeight = $(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).eq($actualIndex).height();
		//show slide
		$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).eq($actualIndex).addClass('active').removeClass('no-trans').css({'opacity':'1', 'transform': 'translateX(0px)'}).css('z-index','20');
		//change pag #
		$(this).parents('.testimonial_slider').find('.control-wrap ul').css({'transform':'translateX(-'+(20*$actualIndex)+'px)'});
	} else {
		if($index-1 == -1) { 
			$actualIndex = $(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).length-1; 
		} else {
			$actualIndex = $index-1; 
		}
		var currentHeight = $(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).eq($index-1).height();
		//show slide
		$(this).parents('.testimonial_slider').find('.slides '+$frontEndEditorTestimonialDiv).eq($index-1).addClass('active').removeClass('no-trans').css({'opacity':'1', 'transform': 'translateX(0px)'}).css('z-index','20');
		//change pag #
		$(this).parents('.testimonial_slider').find('.control-wrap ul').css({'transform':'translateX(-'+(20*$actualIndex)+'px)'});
	}
	
	
	$(this).parents('.testimonial_slider:not(.disable-height-animation)').find('.slides').stop(true,true).animate({'height' : currentHeight + 40 + 'px' },450,'easeOutCubic');
	
	resizeVideoToCover();

	return false;
});


var $tallestQuote;

var $testimonialSlider = [];

//create controls
function createTestimonialControls() {
	
	var $frontEndEditorTestimonialDiv =  ($('body.vc_editor').length > 0) ? '> div': 'blockquote';
	
	//fadeIn
	$('.testimonial_slider:not([data-style*="multiple_visible"])').animate({'opacity':'1'},800);

	$('.testimonial_slider:not([data-style*="multiple_visible"])').each(function(i){
		
		if($(this).find('blockquote').length > 1) {
			$(this).find('.controls, .testimonial-next-prev').remove();
			$(this).append('<div class="controls"><ul></ul></div>');
			
			var slideNum = $(this).find('blockquote').length;
			var $that = $(this);
			
			for(var i=0;i<slideNum;i++) {

				if( !$(this).is('[data-style="minimal"]') ) {
					$that.find('.controls ul').append('<li><span class="pagination-switch"></span></li>');
				} else {
					$that.find('.controls ul').append('<li>'+(i+1)+'</li>');
				}
			}
			
			//minimal
			if( $(this).is('[data-style="minimal"]') ) {

				//add next/prev
				$(this).append('<div class="testimonial-next-prev"><a href="#" class="prev fa fa-angle-left"></a><a href="#" class="next fa fa-angle-right"></a></div>');

				//start on first
				if($(this).find('.active').length == 0) {
					$(this).find('.slides '+$frontEndEditorTestimonialDiv +':first-child').addClass('active').css({'opacity':'1', 'transform': 'translateX(0px)'}).css('z-index','20');
					if(	!$(this).hasClass('disable-height-animation') ) {
						$(this).find('.slides').css({'height' : $(this).find('.slides '+$frontEndEditorTestimonialDiv +':first-child').height() + 40 + 'px' });
					}
				}

				//autorotate
				if($(this).attr('data-autorotate').length > 0) {
					var slide_interval = (parseInt($(this).attr('data-autorotate')) < 100) ? 4000 : parseInt($(this).attr('data-autorotate'));
					var $that = $(this);
					var $rotate = setInterval(function(){ testimonialRotate($that) },slide_interval);
				}
				$(this).find('.testimonial-next-prev a').on('click',function(e){
					if(typeof e.clientX != 'undefined') clearInterval($rotate);
				});

				//wrap bullets
				$(this).find('.controls ul').wrap('<div class="control-wrap" />');
				$(this).find('.controls ul').css('width', (($(this).find('.controls ul li').length * 20) +1) + 'px');
				$(this).find('.controls').append('<span class="out-of">/</span><span class="total">'+$(this).find('blockquote').length+'</span>');

				////swipe for testimonials
				$(this).swipe({
				
					swipeLeft : function(e) {
						$(this).find('.testimonial-next-prev .next').trigger('click');
						e.stopImmediatePropagation();
						clearInterval($rotate);
						return false;
					 },
					 swipeRight : function(e) {
						$(this).find('.testimonial-next-prev .prev').trigger('click');
						e.stopImmediatePropagation();
						clearInterval($rotate);
						return false;
					 }    
				});


			}

			//non minimal
			if( !$(this).is('[data-style="minimal"]') ) {

				//activate first slide
				$(this).find('.controls ul li').first().click();

				//autorotate
				if($(this).attr('data-autorotate').length > 0) {
					var slide_interval = (parseInt($(this).attr('data-autorotate')) < 100) ? 4000 : parseInt($(this).attr('data-autorotate'));
					var $that = $(this);
					var $rotate = setInterval(function(){ testimonialRotate($that) },slide_interval);
				}
				
				$(this).find('.controls li').on('click',function(e){
					if(typeof e.clientX != 'undefined') clearInterval($rotate);
				});
				
				////swipe for testimonials
				$(this).swipe({
				
					swipeLeft : function(e) {
						$(this).find('.controls ul li span.active').parent().next('li').find('span').trigger('click');
						e.stopImmediatePropagation();
						clearInterval($rotate);
						return false;
					 },
					 swipeRight : function(e) {
						$(this).find('.controls ul li span.active').parent().prev('li').find('span').trigger('click');
						e.stopImmediatePropagation();
						clearInterval($rotate);
						return false;
					 }    
				});

		}
		} 
		//only one testimonial
		else if($(this).find('.controls').length == 0) {
			var currentHeight = $(this).find('.slides blockquote').height();
			$(this).find('.slides blockquote').css({'opacity':'0', 'transform': 'translateX(-25px)', 'z-index': '1'});
			$(this).find('.slides blockquote').css({'opacity':'1', 'transform': 'translateX(0px)'}).css('z-index','20');
			$(this).find('.slides').stop(true,true).animate({'height' : currentHeight + 20 + 'px' },450,'easeOutCubic');
		}
	});


  $testimonialSlider = [];
		
	$('.testimonial_slider[data-style*="multiple_visible"] .slides').each(function(i){
	    	var $that = $(this); 
	    	var $element = $that;
	    	var $autoplay = ($that.parents('.testimonial_slider').attr('data-autorotate').length > 1 && parseInt($that.parents('.testimonial_slider').attr('data-autorotate')) > 100) ? parseInt($that.parents('.testimonial_slider').attr('data-autorotate')) : false;
			if($that.find('img').length == 0) $element = $('body');

			//move img pos
			if( $(this).parents('.testimonial_slider').attr('data-style') != 'multiple_visible_minimal') {
				$(this).find('blockquote').each(function(){
					$(this).find('.image-icon').insertAfter($(this).find('p'));
				});
			} else {
				//has alf class
				if($(this).find('blockquote').length > 4) {
					$(this).parents('.testimonial_slider').addClass('has-alf');
				}
			}
			
			var $testimonialGroupCells = ($(this).parents('.testimonial_slider').attr('data-style') == 'multiple_visible_minimal') ? true : false;

			imagesLoaded($element,function(instance){
				
				var $frontEndEditorDrag =  ($('body.vc_editor').length > 0) ? false: true;
				var $frontEndEditorPause =  ($('body.vc_editor').length > 0) ? true: false;

		    	$testimonialSlider[i] = $that.flickity({
		    		contain: true,
					  draggable: $frontEndEditorDrag,
					  groupCells: $testimonialGroupCells,
					  lazyLoad: false,
					  imagesLoaded: true,
					  percentPosition: true,
					  prevNextButtons: false,
					  pageDots: true,
					  resize: true,
					  setGallerySize: true,
					  wrapAround: true,
					  autoPlay: $autoplay,
						pauseAutoPlayOnHover: $frontEndEditorPause,
					  accessibility: false
		    	});
					
					if($testimonialSlider[i].find('.vc_element.is-selected > blockquote').length > 0) {
						
						//starting
						$testimonialSlider[i].find('.vc_element.is-selected > blockquote').addClass('is-selected');
						
						//changed
						$testimonialSlider[i].on( 'select.flickity', function() {
							$testimonialSlider[i].find('.vc_element > blockquote').removeClass('is-selected');
							$testimonialSlider[i].find('.vc_element.is-selected > blockquote').addClass('is-selected');
						});
					}
					

			    $that.parents('.testimonial_slider').css('opacity','1');
			    

		     });//images loaded
		     	     
	    });//each	


}
createTestimonialControls();

function testimonialRotate(slider){
	
	var $testimonialLength = slider.find('li').length;
	var $currentTestimonial = slider.find('.pagination-switch.active').parent().index();
	
	//stop the rotation when toggles are closed
	if( slider.parents('.toggle').length > 0 && slider.parents('.toggle').hasClass('open') ) {

		if( !slider.is('[data-style="minimal"]') ) {
			if( $currentTestimonial+1 == $testimonialLength) {
				slider.find('ul li:first-child').click();
			} else {
				slider.find('.pagination-switch.active').parent().next('li').click();
			}
		} else {
			slider.find('.testimonial-next-prev .next').click();
		}
			
	} else {
		
		if( !slider.is('[data-style="minimal"]') ) {
			if( $currentTestimonial+1 == $testimonialLength) {
				slider.find('ul li:first-child').click();
			} else {
				slider.find('.pagination-switch.active').parent().next('li').click();
			}
		} else {
			slider.find('.testimonial-next-prev .next').click();
		}
	
	}

}

function testimonialHeightResize(){
	$('.testimonial_slider:not(.disable-height-animation):not([data-style*="multiple_visible"])').each(function(){
		
		var $frontEndEditorTestimonialDiv =  ($('body.vc_editor').length > 0) ? '> div': 'blockquote';
		
		var $index = $(this).find('.controls ul li span.active').parent().index();
		var currentHeight = $(this).find('.slides '+$frontEndEditorTestimonialDiv).eq($index).height();
		$(this).find('.slides').stop(true,true).css({'height' : currentHeight + 40 + 'px' });
		
	});
}


function testimonialSliderHeight() {
		
	$('.testimonial_slider.disable-height-animation:not([data-style*="multiple_visible"])').each(function(){
		$tallestQuote = 0;
			
		$(this).find('blockquote').each(function(){
			($(this).height() > $tallestQuote) ? $tallestQuote = $(this).height() : $tallestQuote = $tallestQuote;
		});	
		
		//safety net incase height couldn't be determined
		if($tallestQuote == 0) $tallestQuote = 100;
		
		//set even height
		$(this).find('.slides').css('height',$tallestQuote+40+'px');
		
		//show the slider once height is set
		$(this).animate({'opacity':'1'});

		fullWidthContentColumns();

	});


}

function testimonialSliderHeightMinimalMult() {
		
	$('.testimonial_slider[data-style="multiple_visible_minimal"]').each(function(){
		$tallestQuote = 0;
		
		$(this).find('blockquote > .inner p').css('height','auto');
		
		$(this).find('blockquote > .inner p').each(function(){
			($(this).height() > $tallestQuote) ? $tallestQuote = $(this).height() : $tallestQuote = $tallestQuote;
		});	
		
		//safety net incase height couldn't be determined
		if($tallestQuote == 0) $tallestQuote = 200;
		
		//set even height
		$(this).find('blockquote > .inner p').css('height',$tallestQuote+'px');
		
	});

}

if($('.testimonial_slider.disable-height-animation:not([data-style*="multiple_visible"])').length > 0) {
	testimonialSliderHeight(); 
	setTimeout(testimonialSliderHeight,500);
}

if($('.testimonial_slider[data-style="multiple_visible_minimal"]').length > 0) {
	testimonialSliderHeightMinimalMult();
	setTimeout(testimonialSliderHeightMinimalMult,500);
}


/***************** WP Media Embed / External Embed ******************/

//this isn't the for the video shortcode* This is to help any external iframe embed fit & resize correctly 
function responsiveVideoIframesInit(){
	$('iframe').each(function(){
		
		//make sure the iframe has a src (things like adsense don't)
		if(typeof $(this).attr('src') != 'undefined' && !$(this).hasClass('iframe-embed') && $(this).parents('.ult_modal').length == 0 && $(this).parents('.ls-slide').length == 0 && $(this).parents('.esg-entry-media').length == 0 && $(this).parents('.wpb_video_widget.wpb_content_element').length == 0){
			
			if( $(this).attr('src').toLowerCase().indexOf("youtube") >= 0 || $(this).attr('src').toLowerCase().indexOf("vimeo") >= 0  || $(this).attr('src').toLowerCase().indexOf("twitch.tv") >= 0 || $(this).attr('src').toLowerCase().indexOf("kickstarter") >= 0 || $(this).attr('src').toLowerCase().indexOf("embed-ssl.ted") >= 0  || $(this).attr('src').toLowerCase().indexOf("dailymotion") >= 0) {
				
				$(this).addClass('iframe-embed');	

				$(this).attr('data-aspectRatio', $(this).height() / $(this).width()).removeAttr('height').removeAttr('width');
				
				if($(this).parents('.post-area.masonry.classic').length > 0) {
						$(this).attr('data-aspectRatio', '0.56').removeAttr('height').removeAttr('width');
				}

			}
			 
		}
		
	});


}

function responsiveVideoIframes(){
	 $('iframe[data-aspectRatio]').each(function() {
	 	var newWidth = $(this).parent().width();
	 	 
		var $el = $(this);
		
		//in nectar slider
		if($(this).parents('.swiper-slide').length > 0) {
			if($(this).is(':visible')) $el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio'));
		} 
		//all others
		else {
			$el.width(newWidth).height(newWidth * $el.attr('data-aspectRatio'));
		}
		
		
	});
}


function videoshortcodeSize(){
	//removed in 9.0.
}

responsiveVideoIframesInit();
responsiveVideoIframes();


//unwrap post and protfolio videos
$('.video-wrap iframe').unwrap();
$('#sidebar iframe[src]').unwrap();

$('audio').attr('width','100%');
$('audio').attr('height','100%');

$('audio').css('visibility','visible');

if($('body').hasClass('mobile')){
	$('video').css('visibility','hidden');
} else {
	$('video').css('visibility','visible');
}


$('.wp-video').each(function(){
	 var video = $(this).find('video').get(0);
	 video.addEventListener('loadeddata', function() {
	   $(window).trigger('resize');
	 }, false);
});

//webkit video back button fix 
$('.main-content iframe[src]').each(function(){
	$(this).css({'opacity':'1', 'visibility':'visible'});
});


/***************** Nectar Video BG ******************/

	$('.wpb_row:has(".nectar-video-wrap"):not(.fp-section)').each(function(i){
		$(this).css('z-index',100 + i);
	});

	var min_w = 1200; // minimum video width allowed
	var vid_w_orig;  // original video dimensions
	var vid_h_orig;
	
    vid_w_orig = 1280;
    vid_h_orig = 720;
 
	function resizeVideoToCover() {
		$('.nectar-video-wrap').each(function(i){
			if($(this).find('video').length == 0 ) return;

			if($(this).parents('#page-header-bg').length > 0) {
				if($('.container-wrap.auto-height').length > 0) return false;
				var $containerHeight = $(this).parents('#page-header-bg').outerHeight();			
				var $containerWidth = $(this).parents('#page-header-bg').outerWidth();
			} else {
				
				if($(this).hasClass('column-video')) {
					var $containerHeight = $(this).parents('.wpb_column').outerHeight();			
					var $containerWidth = $(this).parents('.wpb_column').outerWidth();			
				} else {
					var $containerHeight = $(this).parents('.wpb_row').outerHeight();			
					var $containerWidth = ($(this).parents('.full-width-section').length > 0) ? window.innerWidth : $(this).parents('.wpb_row').outerWidth();			
				}	
			}
			
		    // set the video viewport to the window size
		    $(this).width($containerWidth);
				if($(this).parents('#page-header-bg').length > 0) {
			    $(this).height($containerHeight);
				}
		
		    // use largest scale factor of horizontal/vertical
		    var scale_h = $containerWidth / vid_w_orig;
		    var scale_v = ($containerHeight - $containerHeight) / vid_h_orig; 
		    var scale = scale_h > scale_v ? scale_h : scale_v;
			
			//update minium width to never allow excess space
		    min_w = 1280/720 * ($containerHeight+40);
		    
		    // don't allow scaled width < minimum video width
		    if (scale * vid_w_orig < min_w) {scale = min_w / vid_w_orig;}
		        
		    // now scale the video
		    $(this).find('video, .mejs-overlay, .mejs-poster').width(Math.ceil(scale * vid_w_orig +0));
		    $(this).find('video, .mejs-overlay, .mejs-poster').height(Math.ceil(scale * vid_h_orig +0));
		    
		    // and center it by scrolling the video viewport
		    $(this).scrollLeft(($(this).find('video').width() - $containerWidth) / 2);
		    $(this).scrollTop(($(this).find('video').height() - ($containerHeight)) / 2);
		    $(this).find('.mejs-overlay, .mejs-poster').scrollTop(($(this).find('video').height() - ($containerHeight)) / 2);


		    //align bottom
		    if($(this).attr('data-bg-alignment') == 'center bottom' || $(this).attr('data-bg-alignment') == 'bottom'){
		    	$(this).scrollTop(($(this).find('video').height() - ($containerHeight+6)));
		    }
		    //align top
		    else if($(this).attr('data-bg-alignment') == 'center top' || $(this).attr('data-bg-alignment') == 'top') {
		    	$(this).scrollTop(0);
		    } 
				
				//add loaded class
				$(this).addClass('position-loaded');
		});
	}
    
    //init
    function videoBGInit(){
	    setTimeout(function(){
	    	resizeVideoToCover();
	    	$('.video-color-overlay').each(function(){
	    		$(this).css('background-color',$(this).attr('data-color'));
	    	});
	    	$('.nectar-video-wrap').each(function(i){

	    		if($(this).find('video').length == 0) return;

	    		var $headerVideo = ($(this).parents('#page-header-bg').length > 0) ? true : false;
	    		var $that = $(this);

	    		 var videoReady = setInterval(function(){

	        		if($that.find('video').get(0).readyState > 3) {
								
								if(!nectarDOMInfo.usingMobileBrowser) {
		        			$that.transition({'opacity':'1'},400);
				    			$that.find('video').transition({'opacity':'1'},400);
				    			$that.parent().find('.video-color-overlay').transition({'opacity':'0.7'},400);
									if($headerVideo == true) {
					    			pageHeaderTextEffect();
								  }
								}

							//remove page loading screen
							$('#ajax-loading-screen').addClass('loaded');
							setTimeout(function(){ $('#ajax-loading-screen').addClass('hidden'); },1000);
					

							clearInterval(videoReady);
						}
						
	    		},60);
					
					if(nectarDOMInfo.usingMobileBrowser) {
						if($that.parents('.full-width-section').length > 0 && $that.parents('#nectar_fullscreen_rows').length == 0) {
							$that.css('left','50%');
						} else {
							$that.css('left','0px');
						}
						
						if($headerVideo == true) {
							pageHeaderTextEffect();
						}
						$that.find('video')[0].onplay = function(){
							$that.transition({'opacity':'1'},400);
							$that.find('video').transition({'opacity':'1'},400);
							$that.parent().find('.video-color-overlay').transition({'opacity':'0.7'},400);
						};
					}
	    		
	    	});
	    },300);

		if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)){

				
				$('.nectar-video-wrap').each(function(){
					
						if(!$(this).find('video').is('[muted]')) {

								//autoplay not supported unless muted
								$(this).parent().find('.mobile-video-image').show();
								$(this).remove();
						}
						
				});
		
		
		}

		
		 if(navigator.userAgent.indexOf('Chrome') > 0 && !/Edge\/12./i.test(navigator.userAgent) && !/Edge\/\d./i.test(navigator.userAgent)) { 
		 	
		 	$('.nectar-video-wrap').each(function(i){
		 		if(jQuery(this).find('video source[type="video/webm"]').length > 0 ) {
				  	var webmSource = jQuery(this).find('video source[type="video/webm"]').attr('src') + "?id="+Math.ceil(Math.random()*10000);
		          	var firstVideo = jQuery(this).find('video').get(0);
		          	firstVideo.src = webmSource;
		          	firstVideo.load();
		         }
            });
	    }




	    jQuery(".vc_row").each(function() {
	        var youtubeUrl, youtubeId, $row = jQuery(this);
	        $row.find('.nectar-youtube-bg').length > 0 ? (youtubeUrl = $row.find('.nectar-youtube-bg span').text(), youtubeId = nectarExtractYoutubeId(youtubeUrl), youtubeId && ($row.find(".vc_video-bg").remove(), nectarInsertYoutubeVideoAsBackground($row.find('.nectar-youtube-bg'), youtubeId))) : $row.find(".nectar-youtube-bg").remove()

	        //remove yt url
	        $row.find('.nectar-youtube-bg span').remove();
					
					if(!nectarDOMInfo.usingMobileBrowser) {
		        $row.find('.nectar-video-wrap, .nectar-youtube-bg').css({'opacity':'1','width':'100%', 'height':'100%'});
					}
	         $row.find('.video-color-overlay').transition({'opacity':'0.7'},400);
	    });
		

		function nectarInsertYoutubeVideoAsBackground($element, youtubeId, counter) {
		    if ("undefined" == typeof YT || void 0 === YT.Player) return 100 < (counter = void 0 === counter ? 0 : counter) ? void console.warn("Too many attempts to load YouTube api") : void setTimeout(function() {
		        nectarInsertYoutubeVideoAsBackground($element, youtubeId, counter++)
		    }, 100);
		    var $container = $element.prepend('<div class="vc_video-bg"><div class="inner"></div></div>').find(".inner");
		    new YT.Player($container[0], {
		        width: "100%",
		        height: "100%",
		        videoId: youtubeId,
		        playerVars: {
		            playlist: youtubeId,
		            iv_load_policy: 3,
		            enablejsapi: 1,
		            disablekb: 1,
		            autoplay: 1,
		            controls: 0,
		            showinfo: 0,
		            rel: 0,
		            loop: 1
		        },
		        events: {
		            onReady: function(event) {
		                event.target.mute().setLoop(!0);
										nectarResizeVideoBackground($element);
		            }
		        }
		    }), nectarResizeVideoBackground($element), jQuery(window).on("resize", function() {
		        nectarResizeVideoBackground($element);
		    });
				
				setTimeout(function(){
					 nectarResizeVideoBackground($element);
				},100);
		}

		function nectarResizeVideoBackground($element) {
		    var iframeW, iframeH, marginLeft, marginTop, containerW = $element.innerWidth(),
		        containerH = $element.innerHeight(),
		        ratio1 = 16,
		        ratio2 = 9;
		    ratio1 / ratio2 > containerW / containerH ? (iframeW = containerH * (ratio1 / ratio2), 
		    	iframeH = containerH, marginLeft = -Math.round((iframeW - containerW) / 2) + "px", marginTop = -Math.round((iframeH - containerH) / 2) + "px", iframeW += "px", iframeH += "px") : (iframeW = containerW, iframeH = containerW * (ratio2 / ratio1), marginTop = -Math.round((iframeH - containerH) / 2) + "px", 
		    	marginLeft = -Math.round((iframeW - containerW) / 2) + "px", iframeW += "px", iframeH += "px"), 
		    	$element.find(".vc_video-bg iframe").css({
			        maxWidth: "1000%",
			        marginLeft: marginLeft,
			        marginTop: marginTop,
			        width: iframeW,
			        height: iframeH
			    })
		}

		function nectarExtractYoutubeId(url) {
		    if ("undefined" == typeof url) return !1;
		    var id = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
		    return null !== id ? id[1] : !1
		}


	}

	if($('.nectar-video-wrap').length > 0 || $('.nectar-youtube-bg').length > 0) {
		videoBGInit();
	}


/*-------------------------------------------------------------------------*/
/*	4.	Header + Search
/*-------------------------------------------------------------------------*/	 

////mobile megamenus without titles / nested groupings
var $mobileNavSelector = ($('.off-canvas-menu-container.mobile-only').length > 0) ? '.off-canvas-menu-container.mobile-only ': '#mobile-menu .container ';
$($mobileNavSelector+'.megamenu > ul > li > a').each(function(){
	if( $(this).text() == '–' ) {
		$navLIs = $(this).parent().find('> ul > li').clone();
		$(this).parent().find('ul').remove();
		$(this).parent().parent().append($navLIs);
		$(this).parent().remove();
	}
});

/***************** Slide Out Widget Area **********/

var $bodyBorderHeaderColorMatch = ($('.body-border-top').css('background-color') == '#ffffff' && $('body').attr('data-header-color') == 'light' || $('.body-border-top').css('background-color') == $('#header-outer').attr('data-user-set-bg')) ? true : false;
var $bodyBorderWidth = ($('.body-border-right').length > 0) ? $('.body-border-right').width() : 0;
var $resetHeader;


function mobileBreakPointCheck() {
	var $mobileBreakpoint = ( $('body[data-header-breakpoint]').length > 0 && $('body').attr('data-header-breakpoint') != '1000' ) ? parseInt($('body').attr('data-header-breakpoint')) : 1000;
	var $withinCustomBreakpoint = false;

	if($mobileBreakpoint != 1000) {
		if( $('body[data-user-set-ocm="1"][data-slide-out-widget-area-style="slide-out-from-right-hover"]').length == 0 && window.innerWidth > 1000 && window.innerWidth <= $mobileBreakpoint ) {
			$withinCustomBreakpoint = true;
		}
	}

	return $withinCustomBreakpoint;
}

//icon effect html creation
if($('#slide-out-widget-area.slide-out-from-right-hover').length > 0) {

	if($('#ajax-content-wrap > .slide-out-widget-area-toggle').length == 0) {
		$('<div class="slide-out-widget-area-toggle slide-out-hover-icon-effect" data-icon-animation="simple-transform"><div> <a href="#sidewidgetarea" class="closed"> <span> <i class="lines-button x2"> <i class="lines"></i> </i> </span> </a> </div> </div>').insertAfter('#slide-out-widget-area');
		if($('#header-outer[data-has-menu="true"]').length > 0 || $('body[data-header-search="true"]').length > 0) $('#ajax-content-wrap > .slide-out-widget-area-toggle').addClass('small');
	}


	function calculateHoverNavMinHeight() {
		$widgetHeights = 0;
		$('#slide-out-widget-area > .widget').each(function(){
			$widgetHeights += $(this).height();
		});
		$menuHeight = ( ($('#slide-out-widget-area').height() - 25 - $('.bottom-meta-wrap').outerHeight(true) -$widgetHeights) > $('#slide-out-widget-area .off-canvas-menu-container:last-child').height() ) ? $('#slide-out-widget-area').height() - 25 - $('.bottom-meta-wrap').outerHeight(true) -$widgetHeights : $('#slide-out-widget-area .off-canvas-menu-container:last-child').height();

		$('#slide-out-widget-area .inner').css({'height':'auto', 'min-height': $menuHeight  });

		$('#slide-out-widget-area.slide-out-from-right-hover > .inner .off-canvas-menu-container').transition({ y : '-' + ($('#slide-out-widget-area.slide-out-from-right-hover > .inner .off-canvas-menu-container:last-child').height()/2) + 'px' },0);
	
	}

	function openRightHoverNav() {

		

			calculateHoverNavMinHeight();

			if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) && $('#slide-out-widget-area.open').length > 0) {
				mobileCloseNavCheck();
				return;
			}
			

			$('#slide-out-widget-area').css({ 'transform': 'translate3d(0,0,0)' }).addClass('open');

			var $bodyBorderHeaderColorMatch = ($('.body-border-top').length > 0 && $('.body-border-top').css('background-color') == '#ffffff' && $('body').attr('data-header-color') == 'light' || $('.body-border-top').length > 0 && $('.body-border-top').css('background-color') == $('#header-outer').attr('data-user-set-bg')) ? true : false;

			//icon effect
			////set pos 
			if($('header#top .container .span_9 > .slide-out-widget-area-toggle').length > 0) {
				
				var secondaryBarHeight = ($('#header-secondary-outer').length > 0) ? $('#header-secondary-outer').height() : 0; 
				

				if($('body.mobile').length > 0) {

					$('.slide-out-hover-icon-effect').css({'top': $('header#top .span_9 > .slide-out-widget-area-toggle a').offset().top - $(window).scrollTop(), 'right': parseInt($('#header-outer header > .container').css('padding-right')) + 1 });
				} else {

					if($bodyBorderHeaderColorMatch) {

						var $extraCushion = ($('#header-outer[data-has-menu="false"]').length > 0) ? 2 : 1;
						$('.slide-out-hover-icon-effect').css({
							'top': nectarDOMInfo.adminBarHeight + secondaryBarHeight + parseInt($('header#top nav >ul .slide-out-widget-area-toggle a').css('padding-top')), 
							'right': 29 + $extraCushion });

					} else {

						var $withinCustomBreakpoint = mobileBreakPointCheck();

						if($('body.ascend').length > 0 && $withinCustomBreakpoint != true) {
							var $extraCushion = ($('#header-outer[data-has-menu="false"]').length > 0) ? 2 : 1;
							$('.slide-out-hover-icon-effect').css({'top': nectarDOMInfo.adminBarHeight + secondaryBarHeight + parseInt($('header#top nav >ul .slide-out-widget-area-toggle a').css('padding-top')), 'right': parseInt($('#header-outer header >.container').css('padding-right')) + $extraCushion });
						} else {

							if($('body.material').length > 0) {
								if($('#header-outer[data-format="centered-menu-bottom-bar"]').length > 0) {
									var $nectarHamMenuPos = ($('header#top .span_9 > .slide-out-widget-area-toggle').css('display') == 'block') ? $('#header-outer header#top > .container .span_9 > .slide-out-widget-area-toggle.mobile-icon').position() : $('header#top .span_3 .right-side .slide-out-widget-area-toggle > div').offset();
									if($('#header-secondary-outer.hide-up').length > 0) { secondaryBarHeight = 0; }
									$('.slide-out-hover-icon-effect').css({'top': secondaryBarHeight + parseInt($nectarHamMenuPos.top) - nectarDOMInfo.scrollTop , 'right': parseInt($('#header-outer header >.container').css('padding-right')) + 2  });
								} else {
									var $nectarHamMenuPos = ($('header#top .span_9 > .slide-out-widget-area-toggle').css('display') == 'block') ? $('#header-outer header#top > .container .span_9 > .slide-out-widget-area-toggle.mobile-icon').position() : $('header#top nav .buttons .slide-out-widget-area-toggle > div').position();
									if($('#header-secondary-outer.hide-up').length > 0) { secondaryBarHeight = 0; }
									$('.slide-out-hover-icon-effect').css({'top': nectarDOMInfo.adminBarHeight + secondaryBarHeight + parseInt($nectarHamMenuPos.top) , 'right': parseInt($('#header-outer header >.container').css('padding-right')) + 2  });
								}
								
								
							} else {
								$menuToggleSelector = ($('header#top nav > ul .slide-out-widget-area-toggle').length > 0 && $('header#top nav > ul .slide-out-widget-area-toggle').css('display') != 'none') ? $('header#top nav > ul .slide-out-widget-area-toggle') : $('body header#top .span_9 >.slide-out-widget-area-toggle');
								$('.slide-out-hover-icon-effect').css({'top': nectarDOMInfo.adminBarHeight + secondaryBarHeight + parseInt($menuToggleSelector.css('padding-top')) + parseInt($('#header-outer').css('padding-top')), 'right': parseInt($('#header-outer header >.container').css('padding-right')) + 1 + parseInt($menuToggleSelector.css('margin-right')) });
							}
						}
					}
					
				}
			}

			////open

			$('.slide-out-hover-icon-effect .lines-button').removeClass('no-delay').addClass('unhidden-line');
			

			if($('#header-outer[data-permanent-transparent="1"]').length == 0 && $('#nectar_fullscreen_rows').length == 0 && !nectarDOMInfo.usingFrontEndEditor) {

				if(!($(window).scrollTop() == 0 && $('#header-outer.transparent').length > 0)) {

					if($('body.mobile').length == 0 && $bodyBorderHeaderColorMatch) {

						$('#header-outer').attr('data-transparent','true').addClass('no-bg-color').addClass('slide-out-hover');
						$('#header-outer header, #header-outer > .cart-outer').addClass('all-hidden');
					}

				}

				if($('#header-outer[data-remove-fixed="1"]').length == 0 && $('body.mobile').length == 0 && $bodyBorderHeaderColorMatch) {
					var headerResize = $('#header-outer').attr('data-header-resize');
					if(headerResize == 1) {

						$(window).off('scroll',bigNav);
						$(window).off('scroll',smallNav);


					} else {
						
						$(window).off('scroll',opaqueCheck);
						$(window).off('scroll',transparentCheck);
					}
				}

			}

			if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) {
				$(window).on('mousemove.rightOffsetCheck',closeNavCheck);
			}

	}

	function closeNavCheck(e) {
		var $windowWidth = $(window).width();
		if(e.clientX < $windowWidth - 340 - $bodyBorderWidth) {

				$(window).off('mousemove.rightOffsetCheck',closeNavCheck);

				$('#slide-out-widget-area').css({ 'transform': 'translate3d(341px,0,0)' }).removeClass('open');

				$('#header-outer').removeClass('style-slide-out-from-right');

				$('.slide-out-hover-icon-effect .lines-button').removeClass('unhidden-line').addClass('no-delay');

				var $bodyBorderHeaderColorMatch = ($('.body-border-top').length > 0 && $('.body-border-top').css('background-color') == '#ffffff' && $('body').attr('data-header-color') == 'light' || $('.body-border-top').length > 0 && $('.body-border-top').css('background-color') == $('#header-outer').attr('data-user-set-bg')) ? true : false;

				if($('#header-outer[data-permanent-transparent="1"]').length == 0) {

					if($('#header-outer[data-remove-fixed="1"]').length == 0 && $('body.mobile').length == 0 && $bodyBorderHeaderColorMatch) {

						if($('body.mobile').length == 0) {
							$('#header-outer').removeClass('no-bg-color');
							$('#header-outer header, #header-outer > .cart-outer').removeClass('all-hidden');
						}
					}

					if($('#header-outer[data-remove-fixed="1"]').length == 0 && $('body.mobile').length == 0 && $bodyBorderHeaderColorMatch) {
						var headerResize = $('#header-outer').attr('data-header-resize');
						if(headerResize == 1) {
						
							$(window).off('scroll.headerResizeEffect');
							if($(window).scrollTop() == 0) {
								$(window).on('scroll.headerResizeEffect',smallNav); 

								if($('#header-outer[data-full-width="true"][data-transparent-header="true"]').length > 0 && $('.body-border-top').length > 0 && $bodyBorderHeaderColorMatch == true && $('#header-outer.pseudo-data-transparent').length > 0) {
									$('#header-outer[data-full-width="true"] header > .container').stop(true,true).animate({
										'padding' : '0'			
									},{queue:false, duration:250, easing: 'easeOutCubic'});	
								}
							}
							else {
								$(window).on('scroll.headerResizeEffect',bigNav);
							}
							
				
						} else {
							
							$(window).off('scroll.headerResizeEffectOpaque');
							$(window).on('scroll.headerResizeEffectOpaque',opaqueCheck);
						}
					}
				}

			}

	}

	function mobileCloseNavCheck(e) {
		

				$('#slide-out-widget-area').css({ 'transform': 'translate3d(341px,0,0)' }).removeClass('open');

				$('#header-outer').removeClass('style-slide-out-from-right');

				$('.slide-out-hover-icon-effect .lines-button').removeClass('unhidden-line').addClass('no-delay');

				if($('#header-outer[data-permanent-transparent="1"]').length == 0) {

					$('#header-outer').removeClass('no-bg-color');
					$('#header-outer header').removeClass('all-hidden');

				}

				var $bodyBorderHeaderColorMatch = ($('.body-border-top').length > 0 && $('.body-border-top').css('background-color') == '#ffffff' && $('body').attr('data-header-color') == 'light' || $('.body-border-top').length > 0 && $('.body-border-top').css('background-color') == $('#header-outer').attr('data-user-set-bg')) ? true : false;

				if($('#header-outer[data-remove-fixed="1"]').length == 0 && $('body.mobile').length == 0 && $bodyBorderHeaderColorMatch) {
					
					var headerResize = $('#header-outer').attr('data-header-resize');
					if(headerResize == 1) {
					
						$(window).off('scroll.headerResizeEffect');
						if($(window).scrollTop() == 0) {
							$(window).on('scroll.headerResizeEffect',smallNav); 

							if($('#header-outer[data-full-width="true"][data-transparent-header="true"]').length > 0 && $('.body-border-top').length > 0 && $bodyBorderHeaderColorMatch == true && $('#header-outer.pseudo-data-transparent').length > 0) {
								$('#header-outer[data-full-width="true"] header > .container').stop(true,true).animate({
									'padding' : '0'			
								},{queue:false, duration:250, easing: 'easeOutCubic'});	
							}
						}
						else {
							$(window).on('scroll.headerResizeEffect',bigNav);
						}
						
			
					} else {
						
						$(window).off('scroll.headerResizeEffectOpaque');
						$(window).on('scroll.headerResizeEffectOpaque',opaqueCheck);
					}
			}

			

	}

	//hover triggered
	if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) {
		$('body').on('mouseenter','#header-outer .slide-out-widget-area-toggle:not(.std-menu) a',openRightHoverNav);
	}
	else {
		$('body').on('click','.slide-out-widget-area-toggle:not(.std-menu) a',openRightHoverNav);
	}

	$(window).on('smartresize',calculateHoverNavMinHeight);

}



function setMaterialWidth() {
	
	
	$('#slide-out-widget-area.slide-out-from-right').css({
		'padding-top' : $(window).height()*0.1,
		'padding-bottom' : $(window).height()*0.1
	});

	slideOutWidgetOverflowState();
}

if($('body.material[data-slide-out-widget-area-style="slide-out-from-right"]').length > 0) {
	setMaterialWidth();
}

//icon hover effect
if($('body.material').length > 0 && $('body[data-slide-out-widget-area-style="slide-out-from-right-hover"]').length == 0) {

	if($('body[data-slide-out-widget-area-style*="fullscreen"]').length == 0) {
		var $menuIconClone = $('header#top nav ul .slide-out-widget-area-toggle a > span > i').clone();
		$menuIconClone.addClass('hover-effect');
		$('header#top nav ul .slide-out-widget-area-toggle a > span').append($menuIconClone);

		var $menuIconClone2 = $('header#top .slide-out-widget-area-toggle.mobile-icon a > span > i').clone();
		$menuIconClone2.addClass('hover-effect');
		$('header#top .slide-out-widget-area-toggle.mobile-icon a > span').append($menuIconClone2);
	}

	$('body:not([data-slide-out-widget-area-style="slide-out-from-right"]) header#top .slide-out-widget-area-toggle a > span').append($('<span class="close-wrap"> <span class="close-line close-line1"></span> <span class="close-line close-line2"></span> </span>'));
}

if($('body.material #boxed').length > 0 && $('body[data-slide-out-widget-area-style="slide-out-from-right-hover"]').length > 0) {
	$('#ajax-content-wrap > .slide-out-widget-area-toggle.slide-out-hover-icon-effect.small').insertAfter('.ocm-effect-wrap');
}

//move material skin default ocm
if($('body.material').length > 0 && $('body[data-slide-out-widget-area-style*="fullscreen"]').length == 0) {
	$('body.material #slide-out-widget-area.slide-out-from-right .slide_out_area_close').insertAfter('.ocm-effect-wrap');
	$('#slide-out-widget-area-bg').insertAfter('.ocm-effect-wrap');
	$('#slide-out-widget-area').insertAfter('.ocm-effect-wrap');
	
}

//remove trans on material search/ocm when resizing 
if($('body.material[data-header-search="true"]').length > 0 || $('body.material .ocm-effect-wrap').length > 0 ) {
		
	var materialTransTO;

	$(window).resize(function(){

		clearTimeout(materialTransTO);

		$('body[data-slide-out-widget-area-style="slide-out-from-right"] > a.slide_out_area_close, .material #header-outer, .ocm-effect-wrap, .ocm-effect-wrap-shadow').addClass('no-material-transition');

		materialTransTO = setTimeout(function(){
			$('body[data-slide-out-widget-area-style="slide-out-from-right"] > a.slide_out_area_close, .material #header-outer, .ocm-effect-wrap, .ocm-effect-wrap-shadow').removeClass('no-material-transition');
		},250);

		setMaterialWidth();

	});
}


function materialOCM_Size() {
	if($('.ocm-effect-wrap.material-ocm-open').length > 0 ) {

		//$('#ajax-content-wrap').css({'position' : 'relative', 'top' : '-' + $(window).scrollTop() + 'px' });
		$('.ocm-effect-wrap').css({'height': $(window).height() });
		$('.ocm-effect-wrap-inner').css({'padding-top': nectarDOMInfo.adminBarHeight });
		
		
	}
}
$(window).resize(materialOCM_Size);


function OCM_dropdown_function() {
	var $nectar_ocm_dropdown_func = ($('#slide-out-widget-area[data-dropdown-func]').length > 0) ? $('#slide-out-widget-area').attr('data-dropdown-func') : 'default';
	if($nectar_ocm_dropdown_func == 'separate-dropdown-parent-link') {
		$('#slide-out-widget-area .off-canvas-menu-container li.menu-item-has-children').append('<span class="ocm-dropdown-arrow"><i class="fa-angle-down"></i></span>');
	}
}

OCM_dropdown_function();

//click triggered
$('body').on('click','.slide-out-widget-area-toggle:not(.std-menu) a.closed:not(.animating)',function(){
	if(animating == 'true' || $('.slide-out-from-right-hover').length > 0) return false;
	var $that = $(this);

	$('#header-outer').removeClass('no-transition');

	//slide out from right
	if($('#slide-out-widget-area').hasClass('slide-out-from-right')) {

		var $slideOutAmount = ($('.body-border-top').length > 0 && $('body.mobile').length == 0) ? $('.body-border-top').height() : 0;


		if($('body.material').length == 0) {

				//calc height if used bottom meta
				$('#slide-out-widget-area .inner').css({'height':'auto', 'min-height': $('#slide-out-widget-area').height() - 25 - $('.bottom-meta-wrap').height() });

				if($('#boxed').length == 0) {
					$('.container-wrap, .home-wrap, #header-secondary-outer, #footer-outer:not(#nectar_fullscreen_rows #footer-outer), .nectar-box-roll,   #page-header-wrap .page-header-bg-image,  #page-header-wrap .nectar-video-wrap, #page-header-wrap .mobile-video-image, #page-header-wrap #page-header-bg > .container, .page-header-no-bg, div:not(.container) > .project-title').stop(true).transition({ x: '-300px' },700,'easeInOutCubic');

					var $withinCustomBreakpoint = mobileBreakPointCheck();

					if($('#header-outer[data-format="centered-logo-between-menu"]').length == 0 || $withinCustomBreakpoint) {
						if($('#header-outer[data-transparency-option="1"]').length == 0 || ($('#header-outer[data-transparency-option="1"]').length > 0 && $('#header-outer[data-full-width="true"]').length == 0) || $('body.mobile').length > 0) {
							$('#header-outer').stop(true).css('transform','translateY(0)').transition({ x: '-' + (300+$slideOutAmount) +'px'},700,'easeInOutCubic');
						} else {
							$('#header-outer').stop(true).css('transform','translateY(0)').transition({ x: '-' + (300+$slideOutAmount) +'px', 'background-color':'transparent', 'border-bottom': '1px solid rgba(255,255,255,0.22)' },700,'easeInOutCubic');
						}
					} else {
						$('#header-outer header#top nav > ul.buttons, body:not(.material) #header-outer .cart-outer .cart-menu-wrap').transition({ x: '-300px'},700,'easeInOutCubic');
					}

					$('#ascrail2000').transition({ 'x': '-' + (300+$slideOutAmount) +'px' },700,'easeInOutCubic');
					$('body:not(.ascend):not(.material) #header-outer .cart-menu').stop(true).transition({ 'x': '300px' },700,'easeInOutCubic');
				}

				$('#slide-out-widget-area').stop(true).transition({ x: '-' + $slideOutAmount +'px' },700,'easeInOutCubic').addClass('open');


				if($('#boxed').length == 0) {
					//full width menu adjustments
					if($('#header-outer[data-full-width="true"]').length > 0 && !$('body').hasClass('mobile')) { 
						$('#header-outer').addClass('highzI'); 
						$('#ascrail2000').addClass('z-index-adj');

						if($('#header-outer[data-format="centered-logo-between-menu"]').length == 0) {
							if($bodyBorderWidth == 0)
								$('header#top #logo').stop(true).transition({ x: (300+$slideOutAmount) +'px' },700,'easeInOutCubic'); 
							
						}

						$('header#top .slide-out-widget-area-toggle .lines-button').addClass('close');

						if($('#header-outer[data-remove-border="true"]').length > 0) {
							$('body:not(.ascend) #header-outer[data-full-width="true"] header#top nav > ul.product_added').stop(true).transition({ x: '64px' },700,'easeInOutCubic');
						} else {
							$('body:not(.ascend) #header-outer[data-full-width="true"] header#top nav > ul.product_added').stop(true).transition({ x: '89px' },700,'easeInOutCubic'); 
						}

						$('body #header-outer nav > ul > li > a').css({'margin-bottom':'0'});
						
					}
				}

				$('#header-outer').addClass('style-slide-out-from-right');

				//fade In BG Overlay
				$('#slide-out-widget-area-bg').css({'height':'100%','width':'100%'}).stop(true).transition({
					'opacity' : 1
				},700,'easeInOutCubic',function(){
					$('.slide-out-widget-area-toggle:not(.std-menu) > div > a').removeClass('animating');
				});
				
				//hide menu if no space
				if($('#header-outer[data-format="centered-logo-between-menu"]').length == 0) {
					$logoWidth = ($('#logo img:visible').length > 0) ? $('#logo img:visible').width() : $('#logo').width();
					if($('header#top nav > .sf-menu').offset().left - $logoWidth - 300 < 20) $('#header-outer').addClass('hidden-menu');
				} else {
					$('#header-outer').addClass('hidden-menu-items');
				}

				var headerResize = $('#header-outer').attr('data-header-resize');

				if($('#header-outer[data-remove-fixed="1"]').length == 0) {
					if($bodyBorderHeaderColorMatch == true && headerResize == 1) {
						
						$('#header-outer').stop(true).transition({ y: '0' },0).addClass('transparent').css('transition','transform');
						if($('#header-outer').attr('data-transparent-header') != 'true') {
							$('#header-outer').attr('data-transparent-header','true').addClass('pseudo-data-transparent');
						}

						$(window).off('scroll',bigNav);
						$(window).off('scroll',smallNav);

					} else if ($bodyBorderHeaderColorMatch == true) {
						$('#header-outer').addClass('transparent');
						$(window).off('scroll',opaqueCheck);
						$(window).off('scroll',transparentCheck);

						if($('#header-outer').attr('data-transparent-header') != 'true') {
							$('#header-outer').attr('data-transparent-header','true').addClass('pseudo-data-transparent');
						}
					}
				}

		} else if ( $('body.material').length > 0 ) {

			//material
			
			//move ajax loading outside
			if($('#ajax-loading-screen').length > 0 && $('.ocm-effect-wrap #ajax-loading-screen').length > 0) {
				$('#ajax-loading-screen').insertBefore('.ocm-effect-wrap');
			}
			
			////hide secondary header when not at top with hhun
			if($(window).scrollTop() > 40) {
				
				$('body[data-hhun="1"] #header-secondary-outer').addClass('hidden');
			}


			setTimeout(function(){ $('.slide-out-widget-area-toggle:not(.std-menu) > div > a').removeClass('animating'); },300);
			$('#slide-out-widget-area, #slide-out-widget-area-bg, #header-outer .slide-out-widget-area-toggle').addClass('material-open');


			//handle bottom bar nav
			if($('body:not(.mobile) #header-outer[data-format="centered-menu-bottom-bar"][data-condense="true"]').length > 0  && $('#header-outer[data-format="centered-menu-bottom-bar"] .span_9').css('display') != 'none') {
				$('#header-outer:not(.fixed-menu)').css('top', nectarDOMInfo.adminBarHeight - $(window).scrollTop() + 'px' );
				
				if($('#header-secondary-outer').length > 0  && $('#header-outer.fixed-menu').length > 0) {
					$('#header-secondary-outer').css('visibility','hidden');
				}
				
			}
			
			$('#ajax-content-wrap').css({'position' : 'relative', 'top' : '-' + $(window).scrollTop() + 'px' });
			$('.ocm-effect-wrap-inner').css({'padding-top': nectarDOMInfo.adminBarHeight });
			$('#fp-nav').addClass('material-ocm-open');
			$('body').addClass('material-ocm-open');
			$('.ocm-effect-wrap').css({'height': window.innerHeight });

			setTimeout(function(){
				$('.ocm-effect-wrap').addClass('material-ocm-open');
			},40);
			
			
			$('body > .slide_out_area_close').addClass('follow-body');

			//icon effect
			$('#header-outer:not([data-format="left-header"]) header#top .slide-out-widget-area-toggle a').addClass('effect-shown');

			//handle hhun when at top
			$('body[data-hhun="1"]:not(.no-scroll):not(.mobile) #header-outer[data-permanent-transparent="false"]:not(.detached):not(.parallax-contained):not(.at-top-before-box)').css({'transition':'none', 'transform':'translateY('+ nectarDOMInfo.adminBarHeight +'px)'});

			setTimeout(function(){
				$('body > .slide_out_area_close').addClass('material-ocm-open');
			},350);


		}


	}
     else if($('#slide-out-widget-area').hasClass('fullscreen')) {

     	if ( $('body.material').length > 0 ) {
     		$('header#top .slide-out-widget-area-toggle a').addClass('menu-push-out');
     	}

		//scroll away from fixed reveal footer if shown (firefox bug with bluring over it)
		var $scrollDelay = 0;
		var $scrollDelay2 = 0;

		if($(window).scrollTop() + $(window).height() > $('.blurred-wrap').height() && $('#nectar_fullscreen_rows').length == 0) {
			$('body,html').stop().animate({
				scrollTop: $('.blurred-wrap').height() - $(window).height()
			},600,'easeInOutCubic');
			$scrollDelay = 550;
			$scrollDelay2 = 200;
		}

		$('header#top .slide-out-widget-area-toggle:not(.std-menu) .lines-button').addClass('close');
		setTimeout(function(){ $('.blurred-wrap').addClass('blurred'); },$scrollDelay);
		$('#slide-out-widget-area.fullscreen').show().addClass('open');

		if($('.nectar-social-sharing-fixed').length == 0) {
			hideToTop();
		}

		//remove box shadow incase at the top of the page with nectar box roll above
		$('.container-wrap').addClass('no-shadow');
		$('#header-outer').stop(true).css('transform','translateY(0)');

		setTimeout(function(){

			$('.off-canvas-menu-container .menu > li').each(function(i){
				$(this).delay(i*50).transition({y: 0, 'opacity': 1},800,'easeOutExpo');
			});

			$('#slide-out-widget-area.fullscreen .widget').each(function(i){
				$(this).delay(i*100).transition({y: 0, 'opacity': 1},800,'easeOutExpo');
			});
		},370+$scrollDelay2);

		setTimeout(function(){
			$('#slide-out-widget-area .off-canvas-social-links').addClass('line-shown');

			$('#slide-out-widget-area .off-canvas-social-links li').each(function(i){
				$(this).delay(i*50).transition({'scale':1},400,'easeOutCubic');
			});
			$('#slide-out-widget-area .bottom-text').transition({'opacity':0.7},400,'easeOutCubic');
		},750+$scrollDelay2);
		
		//fade In BG Overlay
		setTimeout(function(){
			$easing = ($('body.mobile').length > 0) ? 'easeOutCubic' : 'easeInOutQuint';
			$('#slide-out-widget-area-bg').css({'height':'100%','width':'100%'}).show().stop(true).transition({
				'y' : '0%'
			},920,$easing,function(){
				$('.slide-out-widget-area-toggle > div > a').removeClass('animating');
			});
		},50+$scrollDelay2);

		//overflow state 
		slideOutWidgetOverflowState();
		if($('.mobile #header-outer[data-permanent-transparent="false"]').length > 0 && $('.container-wrap').hasClass('no-scroll')) $('#ajax-content-wrap').addClass('at-content');
		if($('.mobile #header-outer[data-permanent-transparent="false"]').length > 0 || $('.mobile').length == 0 && $('#header-outer.transparent').length == 0) $('#slide-out-widget-area.fullscreen .inner-wrap').css('padding-top', $('#header-outer').height());
	} 

	else if($('#slide-out-widget-area').hasClass('fullscreen-alt')) {

		if ( $('body.material').length > 0 ) {
     		$('header#top .slide-out-widget-area-toggle a').addClass('menu-push-out');
     	}

		$('header#top .slide-out-widget-area-toggle:not(.std-menu) .lines-button').addClass('close');
		$('#slide-out-widget-area.fullscreen-alt').show().addClass('open');
		$('#slide-out-widget-area-bg').addClass('open');

		$('body > div[class*="body-border"]').css('z-index','9995');

		$('.off-canvas-menu-container .menu').transition({y: '0px', 'opacity': 1},0);	

		if($('.nectar-social-sharing-fixed').length == 0) {
			hideToTop();
		}

		if($('#header-outer.transparent').length == 0) {


		}
		else { 

			if($('.body-border-top').length > 0) {
				$('.admin-bar #slide-out-widget-area-bg.fullscreen-alt').css({'padding-top': ($('.body-border-top').outerHeight(true)+32) + 'px'});
				$('body:not(.admin-bar) #slide-out-widget-area-bg.fullscreen-alt').css({'padding-top': ($('.body-border-top').outerHeight(true))+ 'px'});
			}
		}
		
		//set translateY to 0 in all cases
		$('#header-outer').stop(true).css('transform','translateY(0)');

		if($('#logo .starting-logo').length > 0 && $(window).width() > 1000 && $('#header-outer[data-format="centered-menu-bottom-bar"].fixed-menu').length == 0 && $('body.material #header-outer[data-condense="true"]').length == 0 && !nectarDOMInfo.usingFrontEndEditor) {

			$('#header-outer').stop(true).css('transform','translateY(0)').addClass('transparent');
			if($('#header-outer').attr('data-transparent-header') != 'true') {
				$('#header-outer').attr('data-transparent-header','true').addClass('pseudo-data-transparent');
			}
		}

		$('.off-canvas-menu-container .clip-wrap').css('transition-duration','0s');

		setTimeout(function(){

			$('.off-canvas-menu-container .menu > li').each(function(i){
				$(this).delay(i*50).transition({y: 0, 'opacity': 1},750,'easeOutCubic').addClass('no-pointer-events');
			});

			setTimeout(function(){
				$('.off-canvas-menu-container .menu > li').removeClass('no-pointer-events');
				$('.off-canvas-menu-container .clip-wrap').css('transition-duration','.45s');
			},500);

			$('#slide-out-widget-area.fullscreen-alt .widget').each(function(i){
				$(this).delay(i*100).transition({y: 0, 'opacity': 1},650,'easeOutCubic');
			});
		},200);

		setTimeout(function(){
			$('#slide-out-widget-area .off-canvas-social-links').addClass('line-shown');

			$('#slide-out-widget-area .off-canvas-social-links li').css('opacity','1').each(function(i){
				$(this).delay(i*50).transition({'scale':1},400,'easeOutCubic');
			});
			$('#slide-out-widget-area .bottom-text').transition({'opacity':1},600,'easeOutCubic');
		},200);
		
		//fade In BG Overlay
		if($('#slide-out-widget-area-bg').hasClass('solid')) $opacity = 1;
		if($('#slide-out-widget-area-bg').hasClass('dark')) $opacity = 0.97;
		if($('#slide-out-widget-area-bg').hasClass('medium')) $opacity = 0.6;
		if($('#slide-out-widget-area-bg').hasClass('light')) $opacity = 0.4;
		$('#slide-out-widget-area-bg').removeClass('no-transition');
		

		$('#slide-out-widget-area-bg').addClass('padding-removed').css({'height':'100%','width':'100%', 'left':'0','opacity': $opacity});
		

		setTimeout(function(){
			$('.slide-out-widget-area-toggle > div > a').removeClass('animating');
		},600);
			

		//overflow state 
		slideOutWidgetOverflowState();
		if($('.mobile #header-outer[data-permanent-transparent="false"]').length > 0 && $('.container-wrap').hasClass('no-scroll')) $('#ajax-content-wrap').addClass('at-content');
		if($('.mobile #header-outer[data-permanent-transparent="false"]').length > 0 || $('.mobile').length == 0 && $('#header-outer.transparent').length == 0) $('#slide-out-widget-area.fullscreen-alt .inner-wrap').css('padding-top', $('#header-outer').height());
	}



	//add open class
	$('#header-outer').removeClass('side-widget-closed').addClass('side-widget-open');

	//give header transparent state
	if($('#header-outer[data-transparency-option="1"]').length > 0 && $('#boxed').length == 0 && $('#header-outer[data-full-width="true"]').length > 0 && !nectarDOMInfo.usingFrontEndEditor) {
			if($('body.material[data-slide-out-widget-area-style="slide-out-from-right"]').length == 0 && $('body.material #header-outer[data-condense="true"]').length == 0) {
				$('#header-outer').addClass('transparent');
			}
	}

	//dark slide transparent nav
	if($('#header-outer.dark-slide.transparent').length > 0  && $('#boxed').length == 0 && $('body.material-ocm-open').length == 0) $('#header-outer').removeClass('dark-slide').addClass('temp-removed-dark-slide');
	
	$('.slide-out-widget-area-toggle > div > a').removeClass('closed').addClass('open');
	$('.slide-out-widget-area-toggle > div > a').addClass('animating');

	return false;
});

$('body').on('click','.slide-out-widget-area-toggle:not(.std-menu) a.open:not(.animating), #slide-out-widget-area .slide_out_area_close,  > .slide_out_area_close, #slide-out-widget-area-bg.slide-out-from-right, .material-ocm-open #ajax-content-wrap',function(e){

	if (e.originalEvent == undefined && $('.slide_out_area_close.non-human-allowed').length == 0 ) { return; }

	if($('.slide-out-widget-area-toggle:not(.std-menu) a.animating').length > 0) return;

	$('#header-outer').removeClass('no-transition');


	var $that = $(this);

	$('.slide-out-widget-area-toggle:not(.std-menu) a').removeClass('open').addClass('closed');
	$('.slide-out-widget-area-toggle:not(.std-menu) a').addClass('animating');

	//slide out from right
	if($('#slide-out-widget-area').hasClass('slide-out-from-right')) {

			if($('body.material').length == 0) {

				$('.container-wrap, .home-wrap, #header-secondary-outer, #footer-outer:not(#nectar_fullscreen_rows #footer-outer), .nectar-box-roll, #page-header-wrap .page-header-bg-image,  #page-header-wrap .nectar-video-wrap, #page-header-wrap .mobile-video-image, #page-header-wrap #page-header-bg > .container, .page-header-no-bg, div:not(.container) > .project-title').stop(true).transition({ x: '0px' },700,'easeInOutCubic');

				if($('#header-outer[data-transparency-option="1"]').length > 0  && $('#boxed').length == 0) {
					$currentRowBG = ($('#header-outer[data-current-row-bg-color]').length > 0) ? $('#header-outer').attr('data-current-row-bg-color') : $('#header-outer').attr('data-user-set-bg');
					$('#header-outer').stop(true).transition({ x: '0px', 'background-color': $currentRowBG },700,'easeInOutCubic');
				} else {
					$('#header-outer').stop(true).transition({ x: '0px' },700,'easeInOutCubic');
				}

				$('#ascrail2000').stop(true).transition({ 'x': '0px' },700,'easeInOutCubic');
				$('body:not(.ascend):not(.material) #header-outer .cart-menu').stop(true).transition({ 'x': '0px' },700,'easeInOutCubic');

				$('#slide-out-widget-area').stop(true).transition({ x: '301px' },700,'easeInOutCubic').removeClass('open');


				if($('#boxed').length == 0) {
					if($('#header-outer[data-full-width="true"]').length > 0) {  
						$('#header-outer').removeClass('highzI'); 
						$('header#top #logo').stop(true).transition({ x: '0px' },700,'easeInOutCubic'); 
						$('.lines-button').removeClass('close');

						$('body:not(.ascend) #header-outer[data-full-width="true"] header#top nav > ul.product_added').stop(true).transition({ x: '0px' },700,'easeInOutCubic');

					}
				}

				if($('#header-outer[data-format="centered-logo-between-menu"]').length > 0) {
					$('#header-outer header#top nav > ul.buttons, #header-outer .cart-outer .cart-menu-wrap').stop(true).transition({ x: '0px' },700,'easeInOutCubic'); 
				}

				//fade out overlay
				$('#slide-out-widget-area-bg').stop(true).transition({
					'opacity' : 0
				},700,'easeInOutCubic',function(){
					$('.slide-out-widget-area-toggle a').removeClass('animating');
					$(this).css({'height':'1px','width':'1px'});

					if($('#header-outer[data-remove-fixed="1"]').length == 0) {
						//hide menu if transparent, user has scrolled down and hhun is on
						if($('#header-outer').hasClass('parallax-contained') && $(window).scrollTop() > 0 && $('#header-outer[data-permanent-transparent="1"]').length == 0) {
							$('#header-outer').removeClass('parallax-contained').addClass('detached').removeClass('transparent');
						}
						else if($(window).scrollTop() == 0 && $('body[data-hhun="1"]').length > 0 && $('#page-header-bg[data-parallax="1"]').length > 0 ||
							$(window).scrollTop() == 0 && $('body[data-hhun="1"]').length > 0 && $('.parallax_slider_outer').length > 0) {

							if($('#header-outer[data-transparency-option="1"]').length > 0) $('#header-outer').addClass('transparent');
							$('#header-outer').addClass('parallax-contained').removeClass('detached');
						}
					}

					//fix for fixed subpage menu
					$('.container-wrap').css('transform','none');
				});


				$('#header-outer').removeClass('style-slide-out-from-right');


				var headerResize = $('#header-outer').attr('data-header-resize');
				if($('#header-outer[data-remove-fixed="1"]').length == 0) {

					if($bodyBorderHeaderColorMatch == true && headerResize == 1) {
					
						$(window).off('scroll.headerResizeEffect');
						if($(window).scrollTop() == 0) {
							$(window).on('scroll.headerResizeEffect',smallNav); 

							if($('#header-outer[data-full-width="true"][data-transparent-header="true"]').length > 0 && $('.body-border-top').length > 0 && $bodyBorderHeaderColorMatch == true && $('#header-outer.pseudo-data-transparent').length > 0) {
								$('#header-outer[data-full-width="true"] header > .container').stop(true,true).animate({
									'padding' : '0'			
								},{queue:false, duration:250, easing: 'easeOutCubic'});	
							}
						}
						else
							$(window).on('scroll.headerResizeEffect',bigNav); 
							//smallNav();

						if($('#header-outer').hasClass('pseudo-data-transparent')) {
							$('#header-outer').attr('data-transparent-header','false').removeClass('pseudo-data-transparent').removeClass('transparent');
						}

						$('#header-outer').css('transition','transform');

					} else if ($bodyBorderHeaderColorMatch == true) {
						
						$(window).off('scroll.headerResizeEffectOpaque');
						$(window).on('scroll.headerResizeEffectOpaque',opaqueCheck);

						$('#header-outer').css('transition','transform');

						if($('#header-outer').hasClass('pseudo-data-transparent')) {
							$('#header-outer').attr('data-transparent-header','false').removeClass('pseudo-data-transparent').removeClass('transparent');
						}
					}
				}
		}

		else if ( $('body.material').length > 0 ) {

			//material
			$('#slide-out-widget-area').removeClass('open');

			$('#slide-out-widget-area, #slide-out-widget-area-bg, #header-outer .slide-out-widget-area-toggle').removeClass('material-open');
			$('.ocm-effect-wrap, .ocm-effect-wrap-shadow, body > .slide_out_area_close, #fp-nav').removeClass('material-ocm-open');

			$('body > .slide_out_area_close').removeClass('follow-body');

			setTimeout(function(){
				$('.slide-out-widget-area-toggle a').removeClass('animating');
				$('body').removeClass('material-ocm-open');
				$('.ocm-effect-wrap').css({'height': '100%'});
				$('.ocm-effect-wrap-inner').css({'padding-top': '0' });
				$(window).scrollTop(Math.abs( parseInt($('#ajax-content-wrap').css('top')) ) );
				$('#ajax-content-wrap').css({'position' : '', 'top' : '' });
				

				//handle bottom bar nav
				if($('#header-outer[data-format="centered-menu-bottom-bar"]').length > 0 && $('#header-outer[data-format="centered-menu-bottom-bar"] .span_9').css('display') != 'none' && $('body.mobile').length == 0) {
					$('#header-outer:not(.fixed-menu)').css('top', '');
					$('#header-secondary-outer').css('visibility','');
				}
				
				//handle hhun when at top
				$('body[data-hhun="1"]:not(.no-scroll) #header-outer[data-permanent-transparent="false"]:not(.detached):not(.parallax-contained):not(.at-top-before-box)').css({'transform':''});
				setTimeout(function(){
					$('body[data-hhun="1"]:not(.no-scroll) #header-outer[data-permanent-transparent="false"]:not(.detached):not(.parallax-contained):not(.at-top-before-box)').css({'transition':''});
				},30);
				

				$('body[data-hhun="1"] #header-secondary-outer.hidden').removeClass('hidden');

			},900);

			setTimeout(function(){
				//icon effect
				$('#header-outer:not([data-format="left-header"]) header#top .slide-out-widget-area-toggle a').addClass('no-trans').removeClass('effect-shown');
			},200);

			setTimeout(function(){
				//icon
				$('#header-outer:not([data-format="left-header"]) header#top .slide-out-widget-area-toggle a').removeClass('no-trans')
			},500);
			
		}


	} 

	else if($('#slide-out-widget-area').hasClass('fullscreen')) {


		if ( $('body.material').length > 0 ) {
			setTimeout(function(){
				$('header#top .slide-out-widget-area-toggle a').removeClass('menu-push-out');
			},350);
     	}

		$('.slide-out-widget-area-toggle:not(.std-menu) .lines-button').removeClass('close');
		$('.blurred-wrap').removeClass('blurred');
		$('#slide-out-widget-area.fullscreen').transition({'opacity': 0 },700,'easeOutQuad',function(){ $('#slide-out-widget-area.fullscreen').hide().css('opacity','1'); }).removeClass('open');
		$('#slide-out-widget-area.fullscreen .widget').transition({'opacity': 0},700,'easeOutQuad',function(){
			$(this).transition({y: '110px'},0);
		});

		setTimeout(function(){
			$('.off-canvas-menu-container .menu > li').transition({y: '80px', 'opacity': 0},0);		
			$('#slide-out-widget-area .off-canvas-social-links li').transition({'scale':0},0);
			$('#slide-out-widget-area .off-canvas-social-links').removeClass('line-shown');
			$('#slide-out-widget-area .bottom-text').transition({'opacity':0},0);	

			//close submenu items
			$('#slide-out-widget-area .menuwrapper .menu').removeClass( 'subview' );
			$('#slide-out-widget-area .menuwrapper .menu li').removeClass( 'subview subviewopen' );
			$('#slide-out-widget-area.fullscreen .inner .off-canvas-menu-container').css('height','auto');
		},800);

		setTimeout(function(){
			if($('.nectar-social-sharing-fixed').length == 0) {
				showToTop();
			}
			$('.container-wrap').removeClass('no-shadow');
		},500);

		//fade out overlay
		$('#slide-out-widget-area-bg').stop(true).transition({'opacity': 0},900,'easeOutQuad',function(){
			if($('.mobile #header-outer[data-permanent-transparent="false"]').length > 0 && $('.container-wrap').hasClass('no-scroll')) $('#ajax-content-wrap').removeClass('at-content');
			if($('.mobile #header-outer[data-permanent-transparent="false"]').length == 0) $('#slide-out-widget-area.fullscreen .inner-wrap').css('padding-top', '0');
			$('.slide-out-widget-area-toggle a').removeClass('animating');
			if($('#slide-out-widget-area-bg').hasClass('solid')) $opacity = 1;
			if($('#slide-out-widget-area-bg').hasClass('dark')) $opacity = 0.93;
			if($('#slide-out-widget-area-bg').hasClass('medium')) $opacity = 0.6;
			if($('#slide-out-widget-area-bg').hasClass('light')) $opacity = 0.4;
			$(this).css({'height':'1px','width':'1px', 'opacity': $opacity}).transition({ y : '-100%'},0);
		});

		
	}

	else if($('#slide-out-widget-area').hasClass('fullscreen-alt')) {


		if ( $('body.material').length > 0 ) {
			setTimeout(function(){
				$('header#top .slide-out-widget-area-toggle a').removeClass('menu-push-out');
			},350);
     	}

		$('.slide-out-widget-area-toggle:not(.std-menu) .lines-button').removeClass('close');
		$('.blurred-wrap').removeClass('blurred');
		$('#slide-out-widget-area-bg').removeClass('open');
		
		$('#slide-out-widget-area.fullscreen-alt .widget').transition({'opacity': 0},500,'easeOutQuad',function(){
			$(this).transition({y: '40px'},0);
		});
		$('#slide-out-widget-area .bottom-text, #slide-out-widget-area .off-canvas-social-links li').transition({'opacity': 0},250,'easeOutQuad');
		$('#slide-out-widget-area .off-canvas-social-links').removeClass('line-shown');

		$('.off-canvas-menu-container .menu').transition({y: '-13px', 'opacity': 0},400);	


		setTimeout(function(){
			$('.off-canvas-menu-container .menu > li').stop(true,true).transition({y: '40px', 'opacity': 0},0);		
			$('#slide-out-widget-area .off-canvas-social-links li').transition({'scale':0},0);
			$('#slide-out-widget-area .off-canvas-social-links').removeClass('line-shown');	

			//close submenu items
			$('#slide-out-widget-area .menuwrapper .menu').removeClass( 'subview' );
			$('#slide-out-widget-area .menuwrapper .menu li').removeClass( 'subview subviewopen' );
			$('#slide-out-widget-area.fullscreen-alt .inner .off-canvas-menu-container').css('height','auto');

			if($('.mobile #header-outer[data-permanent-transparent="false"]').length > 0 && $('.container-wrap').hasClass('no-scroll')) $('#ajax-content-wrap').removeClass('at-content');
			if($('.mobile #header-outer[data-permanent-transparent="false"]').length == 0) $('#slide-out-widget-area.fullscreen-alt .inner-wrap').css('padding-top', '0');
			$('.slide-out-widget-area-toggle a').removeClass('animating');
			$('#slide-out-widget-area-bg').css({'height':'1px','width':'1px','left':'-100%'});
			$('#slide-out-widget-area.fullscreen-alt').hide().removeClass('open');
		},550);

		setTimeout(function(){
			if($('.nectar-social-sharing-fixed').length == 0) {
				showToTop();
			}
		},600);

		//fade out overlay
		setTimeout(function(){
			$('#slide-out-widget-area-bg').removeClass('padding-removed');
		},50);	

		
		var borderDelay = ($bodyBorderHeaderColorMatch == true) ? 150: 50;

		setTimeout(function(){
			$('#slide-out-widget-area-bg').stop(true).css({'opacity': 0});
			if($('[data-transparent-header="true"]').length > 0) $('body > div[class*="body-border"]').css('z-index','10000');
		},borderDelay);

		setTimeout(function(){
			$('#header-outer.transparent.small-nav, #header-outer.transparent.detached, #header-outer:not([data-permanent-transparent="1"]).transparent.scrolled-down').removeClass('transparent');
			
			if($('#header-outer').hasClass('pseudo-data-transparent')) {
				$('#header-outer').attr('data-transparent-header','false').removeClass('pseudo-data-transparent').removeClass('transparent');
			}

		},100);
		
	}


	//dark slide transparent nav
	if($('#header-outer.temp-removed-dark-slide.transparent').length > 0  && $('#boxed').length == 0) $('#header-outer').removeClass('temp-removed-dark-slide').addClass('dark-slide');

	//remove header transparent state
	if($('#header-outer[data-permanent-transparent="1"]').length == 0 && $('#slide-out-widget-area.fullscreen-alt').length == 0) {

		if($('.nectar-box-roll').length == 0) {
			if($('#header-outer.small-nav').length > 0 || $('#header-outer.scrolled-down').length > 0 || $('#header-outer.detached').length > 0) $('#header-outer').removeClass('transparent');
		} else {
			if($('#header-outer.small-nav').length > 0 || $('#header-outer.scrolled-down').length > 0 || $('.container-wrap.auto-height').length > 0) $('#header-outer').removeClass('transparent');
		}
	} 



	//remove hidden menu
	$('#header-outer').removeClass('hidden-menu');

	$('#header-outer').removeClass('side-widget-open').addClass('side-widget-closed');

	return false;
});

function slideOutWidgetOverflowState() {

	//switch position of social media/extra info based on screen size
	if(window.innerWidth < 1000 || $('body > #boxed').length > 0 || $('.ocm-effect-wrap-inner > #boxed').length > 0) {

		$('#slide-out-widget-area.fullscreen .off-canvas-social-links, #slide-out-widget-area.fullscreen-alt .off-canvas-social-links').appendTo('#slide-out-widget-area .inner');
		$('#slide-out-widget-area.fullscreen .bottom-text, #slide-out-widget-area.fullscreen-alt .bottom-text').appendTo('#slide-out-widget-area .inner');
	} else {
		$('#slide-out-widget-area.fullscreen .off-canvas-social-links,#slide-out-widget-area.fullscreen-alt .off-canvas-social-links').appendTo('#slide-out-widget-area .inner-wrap');
		$('#slide-out-widget-area.fullscreen .bottom-text, #slide-out-widget-area.fullscreen-alt .bottom-text').appendTo('#slide-out-widget-area .inner-wrap');
	}

	//add overflow
	if( $('#slide-out-widget-area[class*="fullscreen"] .inner').height() >= $(window).height()-100) { $('#slide-out-widget-area[class*="fullscreen"] .inner, #slide-out-widget-area[class*="fullscreen"]').addClass('overflow-state'); }
	else { $('#slide-out-widget-area[class*="fullscreen"] .inner, #slide-out-widget-area[class*="fullscreen"]').removeClass('overflow-state'); }

	$('#slide-out-widget-area[class*="fullscreen"] .inner').transition({ y : '-' + ($('#slide-out-widget-area[class*="fullscreen"] .inner').height()/2) + 'px' },0);

	//close mobile only slide out widget area if switching back to desktop
	if($('.slide-out-from-right.open .off-canvas-menu-container.mobile-only').length > 0 && $('body.mobile').length == 0) { $('#slide-out-widget-area .slide_out_area_close').trigger('click'); }

	//sizing for dropdown
	OCM_Dropdown_Icon_Pos();


}


function OCM_Dropdown_Icon_Pos() {
	$('#slide-out-widget-area[class*="slide-out-from-right"] .off-canvas-menu-container li.menu-item-has-children').each(function(){
		$(this).find('.ocm-dropdown-arrow').css({'top': $(this).find('a').height()/2 });
	});
}
OCM_Dropdown_Icon_Pos();


function fullWidthHeaderSlidingWidgetMenuCalc() {
	$('header#top nav > ul > li.megamenu > ul.sub-menu').stop(true).transition({'width': $(window).width() - 360, 'left': '300px' },700,'easeInOutCubic');
}

//slide out widget area scrolling 
function slideOutWidgetAreaScrolling(){ 
	$('#slide-out-widget-area').mousewheel(function(event, delta) {

	     this.scrollTop -= (delta * 30);
	    
	     event.preventDefault();

	});
}
setTimeout(slideOutWidgetAreaScrolling,500);


//handle mobile scrolling
if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) {
	$('#slide-out-widget-area').addClass('mobile');
}


function closeOCM(item) {
    if($('#slide-out-widget-area.open').length > 0) {

    	var $windowCurrentLocation = window.location.href.split("#")[0];
		  var $windowClickedLocation = item.find('> a').attr('href').split("#")[0];

    	if($windowCurrentLocation == $windowClickedLocation || item.find('a[href^="#"]').length > 0) {
				if(item.parents('.slide-out-from-right-hover').length > 0) {
					$('.slide-out-widget-area-toggle.slide-out-hover-icon-effect a').trigger('click');
				} else {
					$('.slide-out-widget-area-toggle a').trigger('click');
				}
        
			}
    } 
		
}


//left header
function leftHeaderSubmenus() {

	//remove megamenu class
	$('#header-outer[data-format="left-header"] nav li.megamenu').removeClass('megamenu');

	var $ocm_link_selector = ($('#slide-out-widget-area[data-dropdown-func="separate-dropdown-parent-link"]').length > 0) ? '#slide-out-widget-area .off-canvas-menu-container li.menu-item-has-children > .ocm-dropdown-arrow' : 'body.material #slide-out-widget-area[class*="slide-out-from-right"] .off-canvas-menu-container li.menu-item-has-children > a';
	//click
	$('#header-outer[data-format="left-header"]  li.menu-item-has-children > a, '+$ocm_link_selector).on('click',function(e){
		
		if($(this).parent().hasClass('open-submenu')) {
			$(this).parent().find('.sub-menu').css({
				'max-height' : '0'
			});
			$(this).parent().removeClass('open-submenu');
		}
		else {

			//get max height
			var $that = $(this);
			var $maxSubMenuHeight;

			$that.parent().find('> .sub-menu').addClass('no-trans');

			setTimeout(function(){

				$that.parent().find('> .sub-menu').css({
					'max-height' : 'none',
					'position' : 'absolute',
					'visibility' : 'hidden'
				});
				
				$maxSubMenuHeight =  $that.parent().find('> .sub-menu').height();

				$that.parent().find('> .sub-menu').removeClass('no-trans');
				$that.parent().find('> .sub-menu').css({
					'max-height' : '0',
					'position' : 'relative',
					'visibility' : 'visible'
				});

			},25);

			setTimeout(function(){
				
				//reset
				$that.closest('ul').find('li.menu-item-has-children').removeClass('open-submenu');
				$that.closest('ul').find('li.menu-item-has-children > .sub-menu').css({
					'max-height' : '0'
				});

				$that.parent().addClass('open-submenu');

				$that.parent().find('> .sub-menu').css('max-height', $maxSubMenuHeight);

				//add height to open parents
				if($that.parents('ul').length > 0) {

					$that.parents('ul:not(.sf-menu)').each(function(){
						$(this).css('max-height');
						$(this).css('max-height', parseInt($(this).height() + parseInt($(this).css('padding-top'))*2 + $maxSubMenuHeight)+'px');
					});
				} 
					

			},50);
		}
		
		return false;
	});
	
	//start open for current page items
	var $maxSubMenuHeightArr = [];

	$('#header-outer[data-format="left-header"] .current-menu-ancestor').find('.current-menu-item').parents('ul.sub-menu').each(function(i){

		

		$maxSubMenuHeightArr[i] =  $(this).parent().find('> .sub-menu').height();


		var $that = $(this);
		setTimeout(function(){

			var $totalSubMenuHeight = 0;

			for(var $i=0; $i < $maxSubMenuHeightArr.length; $i++) {
				$totalSubMenuHeight += parseInt($maxSubMenuHeightArr[i]);
			}
	
			$that.parent().addClass('open-submenu');
			$that.css('max-height', $totalSubMenuHeight);

		},40);
	});
}

if($('#header-outer[data-format="left-header"]').length > 0 || $('body.material[data-slide-out-widget-area-style*="slide-out-from-right"]').length > 0 || $('#slide-out-widget-area[data-dropdown-func="separate-dropdown-parent-link"]').length > 0) {
	leftHeaderSubmenus();
}


//fullscreen submenu
;( function( $, window, undefined ) {

	'use strict';

	// global
	var Modernizr = window.Modernizr, $body = $( 'body' );

	$.DLMenu = function( options, element ) {
		this.$el = $( element );
		this._init( options );
	};

	// the options
	$.DLMenu.defaults = {
		// classes for the animation effects
		animationClasses : { classin : 'dl-animate-in-1', classout : 'dl-animate-out-1' },
		onLevelClick : function( el, name ) { return false; },
		onLinkClick : function( el, ev ) { return false; }
	};

	$.DLMenu.prototype = {
		_init : function( options ) {

			// options
			this.options = $.extend( true, {}, $.DLMenu.defaults, options );
			this._config();
			
			var animEndEventNames = {
					'WebkitAnimation' : 'webkitAnimationEnd',
					'OAnimation' : 'oAnimationEnd',
					'msAnimation' : 'MSAnimationEnd',
					'animation' : 'animationend'
				},
				transEndEventNames = {
					'WebkitTransition' : 'webkitTransitionEnd',
					'MozTransition' : 'transitionend',
					'OTransition' : 'oTransitionEnd',
					'msTransition' : 'MSTransitionEnd',
					'transition' : 'transitionend'
				};
			// animation end event name
			this.animEndEventName = animEndEventNames[ Modernizr.prefixed( 'animation' ) ] + '.menu';
			// transition end event name
			this.transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ] + '.menu',
			// support for css animations and css transitions
			this.supportAnimations = Modernizr.cssanimations,
			this.supportTransitions = Modernizr.csstransitions;

			this._initEvents();

		},
		_config : function() {
			this.open = false;
			this.$trigger = this.$el.children( '.trigger' );
			this.$menu = this.$el.children( 'ul.menu' );
			this.$menuitems = this.$menu.find( 'li:not(.back) > a' );
			this.$el.find( 'ul.sub-menu' ).prepend( '<li class="back"><a href="#"> '+$('#slide-out-widget-area').attr('data-back-txt')+' </a></li>' );
			this.$back = this.$menu.find( 'li.back' );
		},
		_initEvents : function() {

			var self = this;

			this.$trigger.on( 'click.menu', function() {
				
				if( self.open ) {
					self._closeMenu();
				} 
				else {
					self._openMenu();
				}
				return false;

			} );
			
			this.$menuitems.on( 'click.menu', function( event ) {


				var $item = $(this).parent('li'),
					$submenu = $item.children( 'ul.sub-menu' );

				$('.fullscreen-alt .off-canvas-menu-container .clip-wrap, .fullscreen-alt .off-canvas-menu-container .clip-wrap span').css('transition-duration','0s');	
	
				if( $submenu.length > 0 ) {

					var $flyin = $submenu.clone().css( 'opacity', 0 ).insertAfter( self.$menu ),
						onAnimationEndFn = function() {
							self.$menu.off( self.animEndEventName ).removeClass( self.options.animationClasses.classout ).addClass( 'subview' );
							$item.addClass( 'subviewopen' ).parents( '.subviewopen:first' ).removeClass( 'subviewopen' ).addClass( 'subview' );
							$flyin.remove();
							setTimeout(function(){
								$('.off-canvas-menu-container .menu > li').removeClass('no-pointer-events');
								$('.off-canvas-menu-container .clip-wrap, .off-canvas-menu-container .clip-wrap span').css('transition-duration','.45s');
							},300);
							
			
						};

					setTimeout( function() {
						$flyin.addClass( self.options.animationClasses.classin );
						self.$menu.addClass( self.options.animationClasses.classout );
						if( self.supportAnimations ) {
							self.$menu.on( self.animEndEventName, onAnimationEndFn );
						}
						else {
							onAnimationEndFn.call();
						}

						self.options.onLevelClick( $item, $item.children( 'a:first' ).text() );
					} );


					$item.parents('.off-canvas-menu-container').css('height',$item.parents('.off-canvas-menu-container').find('.menuwrapper .menu').height()).transition({ 'height': $flyin.height() },500,'easeInOutQuad' );


					return false;

				}
				else {
		
					self.options.onLinkClick( $item.find('> a'), event );
				}

				closeOCM($item);

			});

			


			this.$back.on( 'click.menu', function( event ) {
				
				var $this = $( this ),
					$submenu = $this.parents( 'ul.sub-menu:first' ),
					$item = $submenu.parent(),

					$flyin = $submenu.clone().insertAfter( self.$menu );

				var onAnimationEndFn = function() {
					self.$menu.off( self.animEndEventName ).removeClass( self.options.animationClasses.classin );
					$flyin.remove();
				};

				setTimeout( function() {
					$flyin.addClass( self.options.animationClasses.classout );
					self.$menu.addClass( self.options.animationClasses.classin );
					if( self.supportAnimations ) {
						self.$menu.on( self.animEndEventName, onAnimationEndFn );
					}
					else {
						onAnimationEndFn.call();
					}

					$item.removeClass( 'subviewopen' );
					
					var $subview = $this.parents( '.subview:first' );
					if( $subview.is( 'li' ) ) {
						$subview.addClass( 'subviewopen' );
					}
					$subview.removeClass( 'subview' );
				} );

		
				$item.parents('.off-canvas-menu-container').css('height', $item.parents('.off-canvas-menu-container').find('.menuwrapper .menu').height())
				setTimeout(function() { 
					$item.parents('.off-canvas-menu-container').transition({ 'height': $item.parent().height() },500,'easeInOutQuad');
				},50);


				return false;

			} );
			
		},
		closeMenu : function() {
			if( this.open ) {
				this._closeMenu();
			}
		},
		_closeMenu : function() {
			var self = this,
				onTransitionEndFn = function() {
					self.$menu.off( self.transEndEventName );
					self._resetMenu();
				};
			
			this.$menu.removeClass( 'menuopen' );
			this.$menu.addClass( 'menu-toggle' );
			this.$trigger.removeClass( 'active' );
			
			if( this.supportTransitions ) {
				this.$menu.on( this.transEndEventName, onTransitionEndFn );
			}
			else {
				onTransitionEndFn.call();
			}

			this.open = false;
		},
		openMenu : function() {
			if( !this.open ) {
				this._openMenu();
			}
		},
		_openMenu : function() {
			var self = this;
			$body.off( 'click' ).on( 'click.menu', function() {
				self._closeMenu() ;
			} );
			this.$menu.addClass( 'menuopen menu-toggle' ).on( this.transEndEventName, function() {
				$( this ).removeClass( 'menu-toggle' );
			} );
			this.$trigger.addClass( 'active' );
			this.open = true;
		},
		_resetMenu : function() {
			this.$menu.removeClass( 'subview' );
			this.$menuitems.removeClass( 'subview subviewopen' );
		}
	};

	var logError = function( message ) {
		if ( window.console ) {
			window.console.error( message );
		}
	};

	$.fn.dlmenu = function( options ) {
		if ( typeof options === 'string' ) {
			var args = Array.prototype.slice.call( arguments, 1 );
			this.each(function() {
				var instance = $.data( this, 'menu' );
				if ( !instance ) {
					logError( "cannot call methods on menu prior to initialization; " +
					"attempted to call method '" + options + "'" );
					return;
				}
				if ( !$.isFunction( instance[options] ) || options.charAt(0) === "_" ) {
					logError( "no such method '" + options + "' for menu instance" );
					return;
				}
				instance[ options ].apply( instance, args );
			});
		} 
		else {
			this.each(function() {	
				var instance = $.data( this, 'menu' );
				if ( instance ) {
					instance._init();
				}
				else {
					instance = $.data( this, 'menu', new $.DLMenu( options, this ) );
				}
			});
		}
		return this;
	};

} )( jQuery, window );

function fullscreenMenuInit() {
	$('#slide-out-widget-area .off-canvas-menu-container .menu').wrap('<div class="menu-wrap menuwrapper" />');
	$('#slide-out-widget-area .off-canvas-menu-container .menu').addClass('menuopen');
	$ocmAnimationClassNum = ($('#slide-out-widget-area.fullscreen-alt').length > 0) ? '4' : '5';
	$('#slide-out-widget-area .off-canvas-menu-container .menu-wrap').dlmenu({ animationClasses : { classin : 'dl-animate-in-'+$ocmAnimationClassNum, classout : 'dl-animate-out-'+$ocmAnimationClassNum } });
	
}

if($('body.material[data-slide-out-widget-area-style*="slide-out-from-right"]').length == 0 && $('#slide-out-widget-area[data-dropdown-func="separate-dropdown-parent-link"]').length == 0 ) {
	fullscreenMenuInit();
} else if($('body.using-mobile-browser[data-slide-out-widget-area-style="slide-out-from-right-hover"]').length > 0){
	
	//close OCM on mobile when clicking anchor on same page 
	$('body #slide-out-widget-area .inner .off-canvas-menu-container li a[href]').on('click',function(){

		  if($(this).attr('href') != '#') {
				closeOCM($(this).parent());	
			}

	});
	
} 



//submenu link hover fix
$('body').on('mouseover','#slide-out-widget-area .off-canvas-menu-container .menuwrapper > .sub-menu li > a',function(){
	var $currentTxt = $(this).text();
	$('.off-canvas-menu-container .menuwrapper .menu li > a').removeClass('hovered');
	$('.off-canvas-menu-container .menuwrapper .menu li > a:contains('+$currentTxt+')').addClass('hovered');
});
$('body').on('mouseover','.off-canvas-menu-container .menuwrapper .menu li > a',function(){
	$('.off-canvas-menu-container .menuwrapper .menu li > a').removeClass('hovered');
});



/***************** Page Headers ******************/

var pageHeaderHeight;
var pageHeaderHeightCopy;
var pageHeadingHeight;
var extraSpaceFromResize = ($('#header-outer[data-header-resize="1"]').length > 0 && $('.nectar-box-roll').length == 0) ? 0 : 1;
var $headerRemoveStickyness = ($('body[data-hhun="1"]').length > 0 && $('#header-outer[data-remove-fixed="1"]').length > 0) ? 1 : 0;

if($('body.material').length > 0 ) { extraSpaceFromResize = 0; }

//full screen header
function fullScreenHeaderInit(){
	
	pageHeaderHeight = parseInt($('#page-header-bg').height());
	
	return;
	
	pageHeaderHeight = parseInt($('#page-header-bg').attr('data-height'));
	pageHeaderHeightCopy = parseInt($('#page-header-bg').attr('data-height'));

	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && window.innerWidth > 1000) ? 0 : $('#header-outer').height();

	if($('.fullscreen-header').length > 0) {

		if($('#header-outer[data-transparency-option]').length > 0 && $('#header-outer').attr('data-transparency-option') != '0'){
			var calculatedNum = (!$('body').hasClass('mobile')) ? $(window).height() : $(window).height() - parseInt($headerNavSpace) ;
		} else {
			var calculatedNum = (!$('body').hasClass('mobile')) ? $(window).height() - parseInt($headerNavSpace) + extraSpaceFromResize : $(window).height() - parseInt($headerNavSpace) ;
		}
		var extraHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0; //admin bar
		if($('.nectar-box-roll').length > 0) extraHeight = 0;
		pageHeaderHeight =   calculatedNum  - extraHeight; 
		pageHeaderHeightCopy = calculatedNum - extraHeight; 
	}

	$('#page-header-bg').css('height',pageHeaderHeight+'px').removeClass('not-loaded');
	setTimeout(function(){ $('#page-header-bg').css('overflow','visible') },800);

}

fullScreenHeaderInit();

function pageHeader(){
	
	pageHeaderHeight = parseInt($('#page-header-bg').height());
	
	//handle slide down effect
	$('body[data-aie="slide-down"] #page-header-wrap:not(.fullscreen-header)').css('height',pageHeaderHeight +'px');
  
  //as of 9.0
	return;
	
	
	var $scrollTop = 0;
	var $windowInnerWidth = window.innerWidth;
	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $windowInnerWidth > 1000) ? 0 : $('#header-outer').height();
	var $windowHeight = $(window).height();

	//full screen header
	if($('.fullscreen-header').length > 0) {
		if($('#header-outer[data-transparency-option]').length > 0 && $('#header-outer').attr('data-transparency-option') != '0'){
			var calculatedNum = (!$('body').hasClass('mobile')) ? $windowHeight : $windowHeight - parseInt($headerNavSpace) ;
			if($('body[data-permanent-transparent="1"]').length > 0) calculatedNum = $windowHeight;
		} else {
			var calculatedNum = (!$('body').hasClass('mobile')) ? $windowHeight - parseInt($headerNavSpace) + extraSpaceFromResize : $windowHeight - parseInt($headerNavSpace);
		}
		var extraHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0; //admin bar
		if($('.nectar-box-roll').length > 0) extraHeight = 0;
		pageHeaderHeight =   calculatedNum  - extraHeight; 
		pageHeaderHeightCopy = calculatedNum - extraHeight; 
	}

	if( $windowInnerWidth < 1000 && $windowInnerWidth > 690 && !$('body').hasClass('salient_non_responsive') ) {
		var $multiplier = ($('.fullscreen-header').length > 0) ? 1 : 1.6;
		$('#page-header-bg').attr('data-height', pageHeaderHeightCopy/$multiplier).css('height',pageHeaderHeightCopy/$multiplier +'px');
		$('#page-header-wrap').css('height',pageHeaderHeightCopy/$multiplier +'px');
		
	} else if( $windowInnerWidth <= 690 && $windowInnerWidth > 480 && !$('body').hasClass('salient_non_responsive')) {
		var $multiplier = ($('.fullscreen-header').length > 0) ? 1 : 2.1;
		$('#page-header-bg').attr('data-height', pageHeaderHeightCopy/$multiplier).css('height',pageHeaderHeightCopy/$multiplier +'px');
		$('#page-header-wrap').css('height',pageHeaderHeightCopy/$multiplier +'px');
		
	} else if( $windowInnerWidth <= 480 && !$('body').hasClass('salient_non_responsive')) {
		var $multiplier = ($('.fullscreen-header').length > 0) ? 1 : 2.5;
		$('#page-header-bg').attr('data-height', pageHeaderHeightCopy/$multiplier).css('height',pageHeaderHeightCopy/$multiplier +'px');
		$('#page-header-wrap').css('height',pageHeaderHeightCopy/$multiplier +'px');
		
	} else {
		$('#page-header-bg').attr('data-height', pageHeaderHeightCopy).css('height',pageHeaderHeightCopy +'px');
		if($('.fullscreen-header').length > 0){
			$('#page-header-wrap').css('height',pageHeaderHeightCopy +'px');
		} else {
			$('#page-header-wrap').css('height',pageHeaderHeightCopy +'px');
		}

		if($('#page-header-bg[data-parallax="1"]').length == 0) { $('#page-header-wrap').css('height',pageHeaderHeightCopy +'px'); }
	}


	//handle left header 
	if($('body[data-header-format="left-header"]').length > 0 ) { $('#page-header-bg[data-parallax="1"]').css('width',$('#ajax-content-wrap').width()) }
	
	
	if(!$('body').hasClass('mobile')){
		
		//recalc
		pageHeaderHeight = parseInt($('#page-header-bg').attr('data-height'));
		$('#page-header-bg .container > .row').css('top',0);
		var $divisionMultipler = ($('#header-outer[data-remove-border="true"]').length > 0 && $('#header-outer[data-format="centered-menu-under-logo"]').length == 0) ? 2 : 1;

		//center the heading
		pageHeadingHeight = $('#page-header-bg .col.span_6').height();
		
		
		if($('#header-outer[data-transparent-header="true"]').length > 0 && $('.fullscreen-header').length == 0) {

		} else {
			var $extraResizeHeight = ($('#header-outer[data-header-resize="1"]').length > 0) ? 22: 0;
		}
		
		//center portfolio filters
		$('#page-header-bg:not("[data-parallax=1]") .portfolio-filters').css('top', (pageHeaderHeight/2) + 2);	
		
		if($('#page-header-bg[data-parallax="1"]').length > 0) {
			$scrollTop = $(window).scrollTop();
		}

		if($('#page-header-bg[data-parallax="1"] .span_6 .inner-wrap').css('opacity') > 0) {
			
			if($('#header-outer[data-transparent-header="true"]').length > 0 && $('body.single-post .fullscreen-header').length == 0) {
				//center the parallax heading

				if($headerRemoveStickyness) {
					
				    //center parllax portfolio filters
				    $('#page-header-bg[data-parallax="1"] .portfolio-filters').css({ 
						'top' : ($scrollTop*-0.10) + ((pageHeaderHeight/2)) - 7 +"px"
				    });

				} else {

				    //center parllax portfolio filters
				    $('#page-header-bg[data-parallax="1"] .portfolio-filters').css({ 
						'opacity' : 1-($scrollTop/(pageHeaderHeight-($('#page-header-bg .col.span_6').height()*2)+75)),
						'top' : ($scrollTop*-0.10) + ((pageHeaderHeight/2)) - 7 +"px"
				    });
				}
			    
		  } else {

		  		if($headerRemoveStickyness) {
			  		//center the parallax heading

				    //center parllax portfolio filters
				    $('#page-header-bg[data-parallax="1"] .portfolio-filters').css({ 
						'top' : ($scrollTop*-0.10) + ((pageHeaderHeight/2)) - 7 +"px"
				    });
				} else {
 
				    //center parllax portfolio filters
				    $('#page-header-bg[data-parallax="1"] .portfolio-filters').css({ 
						'opacity' : 1-($scrollTop/(pageHeaderHeight-($('#page-header-bg .col.span_6').height()*2)+75)),
						'top' : ($scrollTop*-0.10) + ((pageHeaderHeight/2)) - 7 +"px"
				    });
				}
		  }
	   }
	}
	
	else {
		//recalc
		pageHeaderHeight = parseInt($('#page-header-bg').attr('data-height'));
		
		//center the heading
		var pageHeadingHeight = $('#page-header-bg .container > .row').height();
		$('#page-header-bg .container > .row').css('top', (pageHeaderHeight/2) - (pageHeadingHeight/2) + 5);
		
	}


	$('#page-header-bg .container > .row').css('visibility','visible');
}

var $pt_timeout = ($('body[data-ajax-transitions="true"]').length > 0 && $('#page-header-bg[data-animate-in-effect="slide-down"]').length > 0) ? 350 : 0; 

if($('#page-header-bg').length > 0) { 
	setTimeout(function(){ pageHeader(); },$pt_timeout);
}


if($('#header-outer').attr('data-header-resize') == '' || $('#header-outer').attr('data-header-resize') == '0'){
	$('#page-header-wrap').css('margin-top','0');
}


function extractUrl(input) {
	return input.replace(/"/g,"").replace(/url\(|\)$/ig, "");
}
 
/***************** Parallax Page Headers ******************/
if($('#page-header-bg[data-parallax="1"]').length > 0) {

	//fadeIn

	var img = new Image();
	
	var imgX, imgY, aspectRatio;
	var diffX, diffY;
	var pageHeadingHeight = $('#page-header-bg .col.span_6').height();
	var pageHeaderHeight = parseInt($('#page-header-bg').height());
	var headerPadding2 = parseInt($('#header-outer').attr('data-padding'))*2;
	var wooCommerceHeader = ($('.demo_store').length > 0) ? 32 : 0 ;
	
	
	var $initialImgCheck = extractUrl($('#page-header-bg[data-parallax="1"]').css('background-image'));
	
	if ($initialImgCheck && $initialImgCheck.indexOf('.') !== -1) {    
		img.onload = function() {
		   pageHeaderInit(); 
		}
		
		img.src = extractUrl($('#page-header-bg[data-parallax="1"]').css('background-image'));
		
	} else {
		 pageHeaderInit();
	}

	
	
	var extraHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0; //admin bar


	 if($('body[data-hhun="1"]').length > 0 && !$('#header-outer[data-remove-fixed="1"]').length > 0)  $('#header-outer').addClass('parallax-contained');

	 window.addEventListener('scroll', function(){ 
        window.requestAnimationFrame(bindHeaderParallax);
    }, false);

}

					
function bindHeaderParallax(){

	var $scrollTop = $(window).scrollTop();
	var pageHeadingHeight = $('#page-header-bg .col.span_6').height();
	
	if(!$('body').hasClass('mobile') && navigator.userAgent.match(/iPad/i) == null && $('body.material-ocm-open').length == 0 ){


		//material needs to set top for when effect wrap sets transform
		if(window.innerWidth > 1000) {
        $('body:not("[data-header-format=\'left-header\']") #page-header-bg[data-parallax="1"]').css('top',$('#ajax-content-wrap').offset().top);
    } else {
        $('body:not("[data-header-format=\'left-header\']") #page-header-bg[data-parallax="1"]').css('top','0');
    }

		var $multiplier1 =  ($('body[data-hhun="1"]').length > 0 || $('#header-outer[data-format="centered-menu-bottom-bar"][data-condense="true"]').length > 0) ? 0.40: 0.4;
    var $multiplier2 = ($('body[data-hhun="1"]').length > 0 || $('#header-outer[data-format="centered-menu-bottom-bar"][data-condense="true"]').length > 0) ? 0.09: 0.09;
    var $parallaxHeaderHUN = ($('#header-outer[data-transparency-option="1"]').length > 0) ? 0.49: 0.4;

		//calc bg pos
		if($('#page-header-bg.out-of-sight').length == 0) {

			if($headerRemoveStickyness) {
				$('#page-header-bg[data-parallax="1"]').css({ 'transform': 'translateY('+ $scrollTop*-0.55 +'px)' });	
			} else {
				$('#page-header-bg[data-parallax="1"]').css({ 'transform': 'translateY('+ $scrollTop*-$multiplier1 +'px)' });
			}

			var multipler = ($('body').hasClass('single')) ? 1 : 2;
			if(!$headerRemoveStickyness) {
				$('#page-header-bg[data-parallax="1"] .span_6 .inner-wrap,  #page-header-bg[data-parallax="1"][data-post-hs="default_minimal"] .author-section').css({ 
					'opacity' : 1-($scrollTop/(pageHeaderHeight-60))
				});
			}
			
			if($headerRemoveStickyness) {
				$('#page-header-bg[data-parallax="1"] .span_6 .inner-wrap, body[data-button-style="rounded"] #page-header-bg[data-parallax="1"] .scroll-down-wrap, #page-header-bg[data-parallax="1"][data-post-hs="default_minimal"] .author-section').css({ 'transform': 'translateY('+ $scrollTop*- 0.45+'px)' });
			} else {
				$('#page-header-bg[data-parallax="1"] .span_6 .inner-wrap, body[data-button-style="rounded"] #page-header-bg[data-parallax="1"] .section-down-arrow, #page-header-bg[data-parallax="1"][data-post-hs="default_minimal"] .author-section').css({ 'transform': 'translateY('+ $scrollTop*- $multiplier2+'px)' });
			}
			
			if($('#page-header-bg[data-parallax="1"] .span_6 .inner-wrap').css('opacity') == 0){
				$('#page-header-bg[data-parallax="1"] .span_6 .inner-wrap, #page-header-bg[data-parallax="1"] .portfolio-filters').hide();
			} else {
				$('#page-header-bg[data-parallax="1"] .span_6 .inner-wrap, #page-header-bg[data-parallax="1"] .portfolio-filters').show();
			}

			if($('body[data-hhun="1"]').length > 0  && !$('#header-outer').hasClass('side-widget-open') && !$('#header-outer .slide-out-widget-area-toggle a').hasClass('animating')) { 
          $('#header-outer.parallax-contained').css({ 'transform': 'translateY('+$scrollTop*-$parallaxHeaderHUN+'px)' });
      }
			
		
		}
		else if($('#page-header-bg.out-of-sight').length == 0) {
			//alt parallax effect
			var multipler = ($('body').hasClass('single')) ? 1 : 2;
			$('#page-header-wrap .nectar-particles .fade-out').css({ 
				'opacity' : 0+($scrollTop/(pageHeaderHeight+pageHeaderHeight*$multiplier))
			});
		}


		//hide elements to allow other parallax sections to work in webkit browsers
		if( ($scrollTop / (pageHeaderHeight + $('#header-space').height() + extraHeight)) > 1 ) {
			$('#page-header-bg, .nectar-particles, #page-header-bg .fade-out').css('visibility','hidden').hide().addClass('out-of-sight');
		}
		else {
		 	$('#page-header-bg, .nectar-particles, #page-header-bg .fade-out').css('visibility','visible').show().removeClass('out-of-sight');

		 		//ensure header is centered
		 		pageHeaderHeight = parseInt($('#page-header-bg').height());
				$('#page-header-bg .container > .row').css('top',0);
				var $divisionMultipler = ($('#header-outer[data-remove-border="true"]').length > 0 && $('#header-outer[data-format="centered-menu-under-logo"]').length == 0) ? 2 : 1;
				pageHeadingHeight = $('#page-header-bg .col.span_6').height();

	    }
		

	}



}

if($('#page-header-bg').length > 0) {
	var $initialImgCheckAscend = extractUrl($('#page-header-bg').css('background-image'));
	if ($initialImgCheckAscend && $initialImgCheckAscend.indexOf('.') !== -1) {    
		   $('#page-header-bg').addClass('has-bg');
	}
}


function pageHeaderInit(){

	 var wooCommerceHeader = ($('.demo_store').length > 0) ? 32 : 0 ;
	 var centeredNavAltSpace = ($('#header-outer[data-format="centered-menu-under-logo"]').length > 0) ? $('header#top nav > .sf-menu').height() -20 : null;
	 //transparent
	  if($('#header-outer[data-transparent-header="true"]').length > 0) {	
	     $('#page-header-bg[data-parallax="1"]').css({'top': extraHeight+wooCommerceHeader });
	  } else {

	   var logoHeight = parseInt($('#header-outer').attr('data-logo-height'));
		 var headerPadding = parseInt($('#header-outer').attr('data-padding'));
		 var headerPadding2 = parseInt($('#header-outer').attr('data-padding'));
		 var extraDef = 10;
		 var headerResize = ($('body').hasClass('pp-video-function')) ? '1' : $('#header-outer').attr('data-header-resize');
		 var headerResizeOffExtra = 0;
		 var extraHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0; //admin bar
		 var usingLogoImage = true;
	     var mediaElement = ($('.wp-video-shortcode').length > 0) ? 36 : 0;
	     var secondaryHeader = ($('#header-outer').attr('data-using-secondary') == '1') ? 32 : 0 ;
	  	 if($('body[data-header-resize="0"]').length == 0 && $('body.material').length == 0) {
				 $('#page-header-bg[data-parallax="1"]').css({'top': ($('#page-header-wrap').offset().top)  + 'px' });
			 }
	  }
	  
	  //fade in header
	  if($('#ajax-content-wrap').length == 0 || !$('body').hasClass('ajax-loaded')){
	  	$('#page-header-bg[data-parallax="1"]').animate({ 'opacity' : 1},350,'easeInCubic');
	  } else if($('#ajax-content-wrap').length == 1) {
	  	$('#page-header-bg[data-parallax="1"]').css({ 'opacity' : 1});
	  }

	  //verify smooth scorlling
	  if( $smoothCache == true && $(window).width() > 690 && $('body').outerHeight(true) > $(window).height() && Modernizr.csstransforms3d && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)){ niceScrollInit(); $(window).trigger('resize') } 
	  

	  $('#page-header-bg[data-parallax="1"] .nectar-particles').append('<div class="fade-out" />');
}




function nectarPageHeader() {

	if($('#page-header-bg').length > 0) {
		fullScreenHeaderInit();
		pageHeader();
	}


	if($('#page-header-bg[data-parallax="1"]').length > 0) {

		var img = new Image();
		var $initialImgCheck = extractUrl($('#page-header-bg[data-parallax="1"]').css('background-image'));
			
		if ($initialImgCheck && $initialImgCheck.indexOf('.') !== -1) {    
			img.onload = function() {
			   pageHeaderInit();    
					
			}
			
			img.src = extractUrl($('#page-header-bg[data-parallax="1"]').css('background-image'));
			
		} else {
			 pageHeaderInit();
		}

		//bindHeaderParallax();
		$('#page-header-bg[data-parallax="1"] .span_6').css({ 
			'opacity' : 1
		});

		

		if (window.addEventListener) {
			 window.addEventListener('scroll', function(){ 
	          requestAnimationFrame(bindHeaderParallax); 
	        }, false);
		}

	} 

	if($('#page-header-bg').length > 0) {
		var $initialImgCheckAscend = extractUrl($('#page-header-bg').css('background-image'));
		if ($initialImgCheckAscend && $initialImgCheckAscend.indexOf('.') !== -1) {    
			   $('#page-header-bg').addClass('has-bg');
		}
	}
}

if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 || navigator.userAgent.match(/(iPod|iPhone|iPad)/)){
	window.onunload = function(){ nectarPageHeader(); };
}


/***************** header text effects *****************/

// rotate in
function pageHeaderTextEffectInit() {
	$('#page-header-bg').each(function(){
		if($(this).attr('data-text-effect') == 'rotate_in') {
			var $topHeading = 'none';

			if($(this).find('.span_6 h1').length > 0) {
				$topHeading = 'h1';
			} 


			if($topHeading != 'none') {

				var $selector = ($(this).find('.nectar-particles').length > 0) ? '.inner-wrap.shape-1' : '.span_6';

				$(this).find($selector).find($topHeading).addClass('top-heading').contents().filter(function () {
			        return this.nodeType === 3 && typeof this.data != 'undefined' && this.data.replace(/\s+/, "");
			    }).wrap('<span class="wraped"></span>');

			    $(this).find($selector).find('.wraped').each(function () {

				    textNode = $(this);

				    text = textNode.text().split(' ');
				    replace = '';

				    $.each(text, function (index, value) {
				        if (value.replace(/\s+/, "")) {
				            replace += '<span class="wraped"><span>' + value + '</span></span> ';
				        }
				    });
				    textNode.replaceWith($(replace));
				    
				});
			}//make sure suitable heading was found

		}//tilt
	});
	
}
function pageHeaderTextEffect() {

	if($('#page-header-bg .nectar-particles').length == 0 && $('#page-header-bg[data-text-effect="none"]').length == 0 || $('.nectar-box-roll').length > 0 && $('#page-header-bg .nectar-particles').length == 0) {

		var $selector = ($('.nectar-box-roll').length == 0) ? '#page-header-bg .span_6' : '.nectar-box-roll .overlaid-content .span_6';

		$($selector).find('.wraped').each(function(i){
			$(this).find('span').delay(i*370).transition({ rotateX: '0', 'opacity' : 1, y: 0},400,'easeOutQuad');
		});

		setTimeout(function(){

			$($selector).find('.inner-wrap > *:not(.top-heading)').each(function(i){
				$(this).delay(i*370).transition({ rotateX: '0', 'opacity' : 1, y: 0 },650,'easeOutQuad');
			});

			$('.scroll-down-wrap').removeClass('hidden');

		}, $($selector).find('.wraped').length * 370);
	}

}
var $effectTimeout = ($('#ajax-loading-screen').length > 0) ? 800 : 0;

pageHeaderTextEffectInit();

if($('#page-header-bg .nectar-video-wrap video').length == 0) { setTimeout(pageHeaderTextEffect,$effectTimeout); }




 //submenu fix
  if($('header#top nav > ul.sf-menu ul').length > 0) {

  	var $midnightSubmenuTimeout;
  	$('body').on('mouseover','#header-outer .midnightHeader .sf-with-ul, #header-outer .midnightHeader .cart-menu',function(){

  		if($(this).parents('.midnightHeader').offset().top - $(window).scrollTop() < 50){
  		
  			$(this).parents('.midnightHeader').css({'z-index': '9999'}).addClass('overflow');
  			$(this).parents('.midnightInner').css('overflow','visible');
  		}
  	});
  	$('body').on('mouseleave','#header-outer .midnightHeader',function(){
  		var $that = $(this);
  		clearTimeout($midnightSubmenuTimeout);
  		$midnightSubmenuTimeout = setTimeout(function(){
  			if(!$that.is(':hover')) {
  				$that.css({'z-index': 'auto'}).removeClass('overflow');
  				$that.find('.midnightInner').css('overflow','hidden');
  		
  			}

  		},900);
  	});
  }

  function midnightInit() {

  	if( $('#header-outer[data-permanent-transparent="1"]').length > 0 && $('body[data-bg-header="true"]').length > 0) {

			//perma trans
			
			////fix pages that set no midnight coloring
			if($('.container-wrap div[data-midnight]').length == 0) {
				$('.container-wrap').attr('data-midnight','dark');
			}

			////cache midnighgt compat divs
			var $midnightCompatArr = [];
			$('div[data-midnight]').each(function(){

				if($(this).attr('data-midnight') == 'light' || $(this).attr('data-midnight') == 'dark') {
					$midnightCompatArr.push($(this));
				}

			});


			if($midnightCompatArr.length > 0) {

				$.each($midnightCompatArr,function(k,v){

						if(v.attr('data-midnight') == 'light' || v.attr('data-midnight') == 'dark') {

							var $that = v;
							var waypoint = new Waypoint({
				 				element: $that,
					 			handler: function(direction) {
				
					 				if($('body.material-ocm-open').length > 0) return;

									if(direction == 'down') {
										var $textColor = ($that.attr('data-midnight') == 'light') ? '' : 'dark-slide';
										$('#header-outer').removeClass('dark-slide').addClass($textColor);
								
									} else {

										if(k-1 >= 0) { 
											var $prevMidItem = k-1;
										} else {
											var $prevMidItem = k;
										}

										var $textColor = ($midnightCompatArr[$prevMidItem].attr('data-midnight') == 'light') ? '' : 'dark-slide';
										$('#header-outer').removeClass('dark-slide').addClass($textColor);

									} 

							
								},
								offset: $('#header-outer').height()

							}); 

						}

				}); //each

			} //if rows with color set are found

  	} //if page is using trans effect

  
  } 



//box roll
function getScrollbarWidth() {
    var outer = document.createElement("div");
    outer.style.visibility = "hidden";
    outer.style.width = "100px";
    outer.style.msOverflowStyle = "scrollbar"; // needed for WinJS apps

    document.body.appendChild(outer);

    var widthNoScroll = outer.offsetWidth;
    // force scrollbars
    outer.style.overflow = "scroll";

    // add innerdiv
    var inner = document.createElement("div");
    inner.style.width = "100%";
    outer.appendChild(inner);        

    var widthWithScroll = inner.offsetWidth;

    // remove divs
    outer.parentNode.removeChild(outer);

    return widthNoScroll - widthWithScroll;
}


function boxRollInit() {
	if($('.nectar-box-roll').length > 0) { 

		$('body').attr('data-scrollbar-width',getScrollbarWidth());
		
		$('body, html, #ajax-content-wrap, .container-wrap, .blurred-wrap').addClass('no-scroll');
		$('body,html').stop().animate({ scrollTop:0 },0);
		$('.container-wrap').css('opacity',0).addClass('no-transform-animation-bottom-out').addClass('bottomBoxOut');
		//keep loading icon centered if scrollbar is going away
		if($('.mobile').length == 0) $('#ajax-loading-screen .loading-icon > span').css({ 'left' : '-'+getScrollbarWidth()/2 +'px'});

		//change content pos
		var $overlaid = $('#page-header-bg .overlaid-content').clone();
		var $scrollDownOverlaid = $('.scroll-down-wrap').clone();
		$('#page-header-bg').removeAttr('data-midnight');
		$('#page-header-bg .overlaid-content, #page-header-bg .scroll-down-wrap').remove();
		$('.nectar-box-roll').append($overlaid);
		if($('#header-outer.dark-slide').length == 0) {
			$('.nectar-box-roll').attr('data-midnight','light');
		} else {
			$('.nectar-box-roll').attr('data-midnight','dark');
		}
		$('.overlaid-content').append($scrollDownOverlaid);

		if($('.page-submenu[data-sticky="true"]').length > 0) {
			$('.container-wrap').addClass('no-trans');
		}
		
		nectarBoxRollContentHeight();
		
		$('html').addClass('nectar-box-roll-loaded');
		
		setTimeout(function() { pageLoadHash(); },700);
	} else {
		$('#ajax-content-wrap, .blurred-wrap').addClass('at-content');
		$('body, html, #ajax-content-wrap, .container-wrap, .blurred-wrap').removeClass('no-scroll');
		$('.container-wrap').css('opacity',1).removeClass('no-transform-animation-bottom-out').removeClass('bottomBoxOut').removeClass('bottomBoxIn');
		perspect = 'not-rolled';
	}
}
if($('.nectar-box-roll').length > 0) { 
	boxRollInit();
}

function nectarBoxRollContentHeight() {

	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-space').height();

	if($('#header-outer[data-transparent-header="true"]').length == 0) {
			$('.nectar-box-roll .overlaid-content, .nectar-box-roll .canvas-bg, .container-wrap').css({'height':window.innerHeight - $headerNavSpace, 'min-height':window.innerHeight - $headerNavSpace });
			if($('.mobile').length == 0 && $('body[data-header-format="left-header"]').length == 0) { 
				$('#ajax-content-wrap').css('margin-top',$headerNavSpace); 
				$('#slide-out-widget-area.fullscreen').css('margin-top','-'+$headerNavSpace+'px'); 
			}
			else { 
				$('#ajax-content-wrap, #slide-out-widget-area.fullscreen').css('margin-top','0'); 
			}
	} else {
		
		if($('.mobile').length > 0 && $('body[data-permanent-transparent="1"]').length == 0 ) {
			$('.nectar-box-roll .overlaid-content, .nectar-box-roll .canvas-bg, .container-wrap').css('height',window.innerHeight - $headerNavSpace);
		} else {
			$('.nectar-box-roll .overlaid-content, .nectar-box-roll .canvas-bg, .container-wrap').css('height',window.innerHeight);
		}
		
	}
}

if($('.nectar-box-roll').length > 0) $(window).on('resize',nectarBoxRollContentHeight);


var perspect = 'not-rolled';
var animating = 'false';
function boxRoll(e,d) {
	
	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-space').height();

	if($('#slide-out-widget-area.open').length > 0) return false;
	if( $('.nectar-box-roll canvas').length > 0 && $('.nectar-box-roll canvas[data-loaded="true"]').length == 0) return false;

	if(perspect == 'not-rolled' && animating == 'false' && d == -1) {
		perspect = 'rolled';
		animating = 'true';
		$('body').addClass('box-animating').addClass('box-perspective-rolled').addClass('box-rolling');

		$('.nectar-box-roll #page-header-bg').removeClass('topBoxIn').addClass('topBoxOut').css('will-change','transform');
		
		$('.nectar-box-roll .overlaid-content').removeClass('topBoxIn2').removeClass('topBoxIn').addClass('topBoxOut2').css('will-change','transform');
		
		$('.container-wrap').removeClass('bottomBoxOut').addClass('bottomBoxIn').removeClass('no-transform-animation-bottom-out').addClass('nectar-box-roll-class').css('will-change','transform');
		if($('#header-outer[data-transparent-header="true"]').length == 0) {
			$('.container-wrap').css({'height':$(window).height() - $headerNavSpace, 'opacity': 1});
			$('#slide-out-widget-area.fullscreen').css('margin-top','0px');
		} else {
			$('.container-wrap').css({'height':$(window).height(), 'opacity': 1});
		}
		

		$('.nectar-slider-wrap').css({'opacity':0});

		updateRowRightPadding(d);
		pauseVideoBG();

		//old browser workaround
		var timeout1 = 1220;
		var timeout2 = 1650;
		var timeout3 = 1700;
		var timeout4 = 1350;
		if( $('html.no-cssanimations').length > 0) {
			timeout1 = 1;
			timeout2 = 1;
			timeout3 = 1;
			timeout4 = 1;
		}

		$('.container-wrap').css('padding-right',$('body').attr('data-scrollbar-width') + 'px');
		setTimeout(function(){
			$('#header-outer, #wpadminbar, body:not(.material) .cart-outer .cart-menu, .midnightHeader .midnightInner').animate({'padding-right': $('body').attr('data-scrollbar-width')},250);
			$('.nectar-box-roll .canvas-bg').addClass('out-of-sight');
			if($('#header-outer[data-permanent-transparent="1"]').length == 0) $('#header-outer').removeClass('transparent');

			if($('body.mobile').length > 0) $('.nectar-box-roll').css({'z-index':'1'});
			
			//perma trans coloring
			$first_row_coloring = ($('.container-wrap > .main-content > .row > .wpb_row').length > 0) ? $('.container-wrap > .main-content > .row > .wpb_row:first-child').attr('data-midnight') : 'dark';
			if($('#header-outer[data-permanent-transparent="1"]').length > 0) {
				
				if($first_row_coloring == 'dark') {
					$('#header-outer').addClass('dark-slide');
				} else {
					$('#header-outer').removeClass('dark-slide');
				}
				
			}
			
		},timeout1);
		setTimeout(function(){ 
			updateRowRightPadding(1);
			$('body,html,#ajax-content-wrap, .container-wrap, .blurred-wrap').removeClass('no-scroll'); 
			$('#ajax-content-wrap, .blurred-wrap').addClass('at-content');
			$('.container-wrap, #footer-outer').removeClass('bottomBoxIn').removeClass('nectar-box-roll-class').addClass('auto-height');
			$('#header-outer, #wpadminbar, .container-wrap, .cart-outer .cart-menu, .midnightHeader .midnightInner').stop().css('padding-right',0);

			if( $smoothActive == 1 && $(window).width() > 690  && Modernizr.csstransforms3d && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)){ 
				niceScrollInit();
			}
			
			$('.nectar-box-roll').css({'z-index':'-1000'}).transition({'y': '-200%'},0);
			$('.nectar-box-roll canvas').hide();
			$('body').removeClass('box-rolling');
			$('.nectar-slider-wrap').transition({'opacity':1},600,'easeOutCubic');

			$('.nectar-box-roll #page-header-bg, .nectar-box-roll .overlaid-content, .container-wrap').css('will-change','auto');
			if($waypointsBound == false) { 
				waypoints();	
				midnightInit();
			}
			
		},timeout2);
		
		//fadeIn
		setTimeout(function(){ 
			$('.container-wrap .main-content > .row > div > div[class*="col"]').css({'opacity':1});
		},timeout4);

		setTimeout(function(){ 
					animating ='false'; 
					$('body').removeClass('box-animating');		
		},timeout3);

		//header position when transparent nav was used
		if($('#header-outer[data-permanent-transparent="1"]').length == 0 && $('.mobile').length == 0 && $('#header-outer[data-transparent-header="true"]').length != 0) { 
			$('#ajax-content-wrap').transition({'margin-top':$('#header-outer').outerHeight(true) + $('#header-outer').offset().top},2000,'easeInOutQuad');
		}

		//remove header if not fixed
		if($('.mobile #header-outer[data-permanent-transparent="1"]').length > 0 && $('.mobile #header-outer[data-mobile-fixed="false"]').length == 1) $('#header-outer').transition({'y':'-100%'},400,'easeOutCubic');

	}

	else if(perspect == 'rolled' && animating == 'false' && d == 1 && $(window).scrollTop() < 100) {

		$('.container-wrap').removeClass('auto-height');
		if($('#header-outer[data-transparent-header="true"]').length == 0) {
			$('.container-wrap').css({'height':$(window).height() - $headerNavSpace, 'opacity': 1});
		} else {
			$('.container-wrap').css({'height':$(window).height(), 'opacity': 1});
		}
		
		$('#footer-outer').removeClass('auto-height');
		$('body').addClass('box-rolling');

		perspect = 'not-rolled';
		animating = 'true';
		$('body').addClass('box-animating').addClass('box-perspective-not-rolled');

		$('#header-outer, #wpadminbar, .container-wrap, .cart-outer .cart-menu, .midnightHeader .midnightInner').css('padding-right',$('body').attr('data-scrollbar-width') + 'px');
		$('.nectar-slider-wrap').transition({'opacity':0},600,'easeOutCubic');
		$('.container-wrap .main-content > .row > div > div[class*="col"]').stop(true).css({'opacity':0});
		setTimeout(function(){
			$('#header-outer, #wpadminbar, .cart-outer .cart-menu, .midnightHeader .midnightInner').animate({'padding-right': 0},250);
			$('.nectar-box-roll .canvas-bg').removeClass('out-of-sight');
			resizeVideoToCover();
			//header position when transparent nav was used
			if($('#header-outer[data-transparent-header="true"]').length != 0) { 
				$('#ajax-content-wrap').stop(true,true).transition({'margin-top':0},2000,'easeInOutCubic');
			} else {
				if($('.mobile').length == 0) $('#slide-out-widget-area.fullscreen').css('margin-top','-'+$headerNavSpace+'px');
			}

		},30);

		//old browser workaround
		var timeout1 = 1700;
		var timeout2 = 1600;
		var timeout3 = 1300;
		if( $('html.no-cssanimations').length > 0) {
			timeout1 = 1;
			timeout2 = 1;
			timeout3 = 1;
		}

		if($('body.mobile').length > 0) {
			setTimeout(function(){
				$('.nectar-box-roll').css('z-index','1000');
			},timeout3);
		} else {
			$('.nectar-box-roll').css('z-index','1000');
		}

		updateRowRightPadding(d);
		removeNiceScroll();
		$('.nectar-box-roll').transition({'y': '0'},0);
		$('.nectar-box-roll canvas').show();
		setTimeout(function(){ 
			updateRowRightPadding(1);
			animating ='false'; 
			$('body').removeClass('box-animating');
			$('#page-header-bg').removeClass('topBoxIn');
			$('.overlaid-content').removeClass('topBoxIn2');	
			$('body').removeClass('box-rolling');
			resumeVideoBG();
			$('.nectar-box-roll #page-header-bg, .nectar-box-roll .overlaid-content, .container-wrap').css('will-change','auto');
			
			
			//perma trans coloring
			if($('#header-outer[data-permanent-transparent="1"]').length > 0) {
				
				if( $('.nectar-box-roll[data-midnight="dark"]').length > 0 ) {
					$('#header-outer').addClass('dark-slide');
				} else {
					$('#header-outer').removeClass('dark-slide');
				}
				
			}
			
		},timeout1);

		setTimeout(function(){
			if($('.mobile #header-outer[data-permanent-transparent="1"]').length > 0 && $('.mobile #header-outer[data-mobile-fixed="false"]').length == 1) $('#header-outer').transition({'y':'0%'},400,'easeOutCubic');
		},timeout2);

		$('body,html,#ajax-content-wrap, .container-wrap, .blurred-wrap').addClass('no-scroll');
		$('#ajax-content-wrap, .blurred-wrap').removeClass('at-content');
		$('.container-wrap').addClass('nectar-box-roll-class');
		$('.nectar-box-roll #page-header-bg').removeClass('topBoxOut').addClass('topBoxIn').css('will-change','transform');
		
		$('.container-wrap').removeClass('bottomBoxIn').addClass('bottomBoxOut').css('will-change','transform');

		if($('#header-outer[data-transparent-header="true"]').length > 0 && $('#header-outer[data-permanent-transparent="1"]').length == 0) $('#header-outer').addClass('transparent');

		$('.nectar-box-roll .overlaid-content').removeClass('topBoxOut2').removeClass('topBoxOut').addClass('topBoxIn2').css('will-change','transform');
	
		if($('#header-outer[data-header-resize="1"]').length > 0) { bigNav(); }

		$('.nectar-box-roll .trigger-scroll-down').removeClass('hovered');
	}

	
}

function boxScrollEvent(event, delta) {
	if($('#slide-out-widget-area.open.fullscreen').length > 0 || $('.material-ocm-open').length > 0 ||  $('#search-outer.material-open').length > 0 ) return false;
	boxRoll(event,delta);
}

function boxRollMouseWheelInit() {
	if($('.nectar-box-roll').length > 0) {
		$('body').on("mousewheel", boxScrollEvent);
	} else {
		$('body').off("mousewheel", boxScrollEvent);
	}
}

if($('.nectar-box-roll').length > 0) {
	boxRollMouseWheelInit();
}

$('body').on('click','.nectar-box-roll .section-down-arrow',function(){
	boxRoll(null,-1);
	$(this).addClass('hovered');
	setTimeout(function(){ $('.nectar-box-roll .section-down-arrow').removeClass('hovered'); },2000);
	return false;
});



function updateRowRightPadding(d){
	$('.wpb_row.full-width-section').each(function(){
		if($(this).hasClass('extraPadding') && d == 1) {
			$(this).css('padding-right',parseInt($(this).css('padding-right')) - parseInt($('body').attr('data-scrollbar-width')) + 'px' ).removeClass('extraPadding');
		} else {
			$(this).css('padding-right',parseInt($('body').attr('data-scrollbar-width')) + parseInt($(this).css('padding-right')) + 'px' ).addClass('extraPadding');
		}	
	});
	$('.wpb_row.full-width-content').each(function(){
		if($(this).find('.row-bg.using-image').length == 0) {
			if($(this).hasClass('extraPadding') && d == 1) {
				$(this).find('.row-bg').css('width',parseInt($(this).width()) - parseInt($('body').attr('data-scrollbar-width')) + 'px' ).removeClass('extraPadding');
			} else {
				$(this).find('.row-bg').css('width',parseInt($('body').attr('data-scrollbar-width')) + $(this).width() + 'px' ).addClass('extraPadding');
			}	
		}
	});
}

function pauseVideoBG() {
	if($('.nectar-box-roll video').length > 0 && !nectarDOMInfo.usingMobileBrowser) { $('.nectar-box-roll video')[0].pause(); }
}
function resumeVideoBG() {
	if($('.nectar-box-roll video').length > 0 && !nectarDOMInfo.usingMobileBrowser) { $('.nectar-box-roll video')[0].play(); }
}

//touch 
if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) && $('.nectar-box-roll').length > 0) {
	$('body').swipe({
		tap: function(event,target) {
			if($(target).parents('.nectar-flip-box').length > 0)
				$(target).parents('.nectar-flip-box').trigger('click');
			if($(target).is('.nectar-flip-box'))
				$(target).trigger('click');
		},
		swipeStatus: function(event, phase, direction, distance, duration, fingers) {
			if($('#slide-out-widget-area.open').length > 0) return false;
			if(direction == 'up') {
				boxRoll(null,-1);
				if($('#ajax-content-wrap.no-scroll').length == 0) $('body').swipe("option", "allowPageScroll", 'vertical');
			} else if(direction == "down" && $(window).scrollTop() == 0) {
				boxRoll(null,1);
				$('body').swipe("option", "allowPageScroll", 'auto');
			}
		}
	});

}

function removeNiceScroll() {
		if($().niceScroll && $("html").getNiceScroll()){
			var nice = $("html").getNiceScroll();
			nice.stop();
			
			$('html').removeClass('no-overflow-y');
			$('.nicescroll-rails').hide();
			if($('#boxed').length == 0){
				$('body, body #header-outer, body #header-secondary-outer, body #search-outer, .midnightHeader .midnightInner').css('padding-right','0px');
			} else if($('body[data-ext-responsive="true"]').length == 0 ) {
				$('body').css('padding-right','0px');
			}

			$('body').attr('data-smooth-scrolling','0');
		}
	}
//called after box roll
var $waypointsBound = false;
function waypoints() {
	rowBGAnimations();
	columnBGAnimations();
	colAndImgAnimations(); 
	progressBars(); 
	dividers();
	iconList();
	animated_titles();
	highlighted_text();
	imageWithHotspots();
	clientsFadeIn(); 
	splitLineHeadings();
	svgAnimations(); 
	milestoneInit();
	nectar_fancy_ul_init();
	owl_carousel_animate();
	headerRowColorInheritInit();
	morphingOutlines(); 
	portfolioLoadIn();
	animatedColBorders();
	foodMenuItems();
	vcWaypoints();
	$waypointsBound = true;
}



/***************** WooCommerce Cart *****************/
var timeout;
var productToAdd;
var $dropdownStyle = ($('body[data-dropdown-style="minimal"]').length > 0)  ? 'minimal' : 'default';
//notification
$('body').on('click','.product .add_to_cart_button', function(){

	var $productHeading = ($(this).parents('li').find('h2').length > 0) ? 'h2' : 'h3';

	productToAdd = $(this).parents('li').find($productHeading).text();
	$('#header-outer .cart-notification span.item-name').html(productToAdd);
	
});

//notification hover
$('body').on('mouseenter','#header-outer .cart-notification',function(){
	if($dropdownStyle == 'minimal') {
		$(this).hide();
		$('#header-outer .widget_shopping_cart').addClass('open').stop(true,true).show();
		$('#header-outer .cart_list').stop(true,true).show();
	} else {
		$(this).fadeOut(400);
		$('#header-outer .widget_shopping_cart').stop(true,true).fadeIn(300);
		$('#header-outer .cart_list').stop(true,true).fadeIn(300);
	}
	
	clearTimeout(timeout);
});

//cart dropdown
var $headerCartSelector = ($('body.material').length > 0) ? '#header-outer .nectar-woo-cart' : '#header-outer  div.cart-outer';

if($($headerCartSelector).length > 0) {
	$($headerCartSelector).hoverIntent(function(){

		if($dropdownStyle == 'minimal') { 
			$('#header-outer .widget_shopping_cart').addClass('open').stop(true,true).show()
			$('#header-outer .cart_list').stop(true,true).show();
			clearTimeout(timeout);
			$('#header-outer .cart-notification').hide();
		} else {
			$('#header-outer .widget_shopping_cart').addClass('open').stop(true,true).fadeIn(300);
			$('#header-outer .cart_list').stop(true,true).fadeIn(300);
			clearTimeout(timeout);
			$('#header-outer .cart-notification').fadeOut(300);
		}
		
	});
}


$('body').on('mouseleave',$headerCartSelector,function(){
	var $that = $(this);
	setTimeout(function(){
		if(!$that.is(':hover')){
			$('#header-outer .widget_shopping_cart').removeClass('open').stop(true,true).fadeOut(300);
			$('#header-outer .cart_list').stop(true,true).fadeOut(300);
		}
	},200);
});

if($('#header-outer[data-cart="false"]').length == 0) {
	$('body').on('added_to_cart', shopping_cart_dropdown_show);
	$('body').on('added_to_cart', shopping_cart_dropdown);
	
	//update header cart markup after ajax remove
	$('body').on('removed_from_cart', wooCartImgPos);
}

function shopping_cart_dropdown() {
		
		if(!$('.widget_shopping_cart .widget_shopping_cart_content .cart_list .empty').length && $('.widget_shopping_cart .widget_shopping_cart_content .cart_list').length > 0 ) {
			$('.cart-menu-wrap').addClass('has_products');
			$('header#top nav > ul, #search-outer #search #close a, header#top .span_9 >.slide-out-widget-area-toggle').addClass('product_added');

			if(!$('.cart-menu-wrap').hasClass('static')) $('.cart-menu-wrap, #mobile-cart-link').addClass('first-load');
			
			//change position of img in cart nav dropdown
			wooCartImgPos();
			
			//nectar slider nav directional effect
			if($('#header-outer').hasClass('directional-nav-effect') && $('#header-outer .cart-icon-wrap .dark').length == 0 && $('body.ascend').length > 0){
				$('#header-outer .cart-outer .cart-icon-wrap').each(function(){
                    $(this).find('> i, > span.light, > span.dark, > span.original').remove();
                    $(this).append('<span class="dark"><span><i class="icon-salient-cart"></i></span></span><span class="light"><span><i class="icon-salient-cart"></i></span></span><span class="original"><span><i class="icon-salient-cart"></i></span></span>');
                	$(this).find('.original').attr('data-w',$(this).find('span.original').width()+1);
                });
			}
		}

}


function shopping_cart_dropdown_show(e) {
		
		clearTimeout(timeout);
		
		if(!$('.widget_shopping_cart .widget_shopping_cart_content .cart_list .empty').length && $('.widget_shopping_cart .widget_shopping_cart_content .cart_list').length > 0 && typeof e.type != 'undefined' ) {
			
			//fix for standalone woocommerce add to cart buttons
			if( $('#header-outer .cart-notification .item-name').length > 0 && $('#header-outer .cart-notification .item-name').text().length == 0 ) { return; }
			
			//before cart has slide in
			if(!$('#header-outer .cart-menu-wrap').hasClass('has_products')) {
				setTimeout(function(){ $('#header-outer .cart-notification').fadeIn(400); },400);
			}
			else if(!$('#header-outer .cart-notification').is(':visible')) {
				$('#header-outer .cart-notification').fadeIn(400);
			} else {
				$('#header-outer .cart-notification').show();
			}
			timeout = setTimeout(hideCart,2700);

			$('.cart-menu a, .widget_shopping_cart a').addClass('no-ajaxy');
		}
}

function hideCart() {
	$('#header-outer .cart-notification').stop(true,true).fadeOut();
}

function checkForWooItems(){ 
	
	var checkForCartItems = setInterval(shopping_cart_dropdown,250);
	setTimeout(function(){ clearInterval(checkForCartItems); },4500);
	
}

function wooCartImgPos() {
	$('#header-outer .widget_shopping_cart .cart_list li').each(function(){
		
		if( $(this).find('> img').length == 0 && $(this).find('.product-meta').length == 0 ) {
			
			var productCartImgLinkSrc = ($(this).find('> a[href]:not(.remove)').length > 0) ? $(this).find('> a[href]:not(.remove)').attr('href') : '';
			var productCartImg = $(this).find('> a > img').clone();
			
			$(this).wrapInner('<div class="product-meta" />');
			
			$(this).prepend(productCartImg);
			
			if(productCartImgLinkSrc.length > 0) {
			   productCartImg.wrap('<a href="'+productCartImgLinkSrc+'"></a>');
			}
			
		}
		
	});
}

if($('#header-outer[data-cart="false"]').length == 0) {
	checkForWooItems();
}

function nectarAccountPageTabs() {
	
	//if not on account page
	if($('body.woocommerce-account #customer_login').length == 0) return;
	
	//create HTML
	$('.woocommerce-account .woocommerce > #customer_login').prepend('<div class="nectar-form-controls" />');
	
	$('.woocommerce-account .woocommerce > #customer_login > div:not(.nectar-form-controls)').each(function(){
			var $title = $(this).find('> h2').text();
			$('#customer_login .nectar-form-controls').append('<div class="control">' + $title + '</div>');
	});
	
	//event
	$('.woocommerce-account .woocommerce > #customer_login .nectar-form-controls .control').on('click',function(){
		
		$('.woocommerce-account .woocommerce > #customer_login .nectar-form-controls .control').removeClass('active');
		$(this).addClass('active');
		
		var formIndex = $(this).index() + 1;
		$('#customer_login div[class*="u-column"]').hide();
		$('#customer_login div[class*="u-column"].col-'+formIndex).show();
		
		setTimeout(function(){
			$('#customer_login div[class*="u-column"]').removeClass('visible');
			$('#customer_login div[class*="u-column"].col-'+formIndex).addClass('visible');
		},30);
		
	});
	
	//starting
	$('.woocommerce-account .woocommerce > #customer_login .nectar-form-controls .control:nth-child(1)').trigger('click');
	
}

nectarAccountPageTabs();

var extraHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0; //admin bar
var secondaryHeader = ($('#header-outer').attr('data-using-secondary') == '1') ? 32 : 0 ;
function searchFieldCenter(){
	var $headerHeightSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-outer').outerHeight();
	$('#search-outer').css('top', $headerHeightSpace + extraHeight + secondaryHeader);
	$('#search-outer > #search #search-box').css('top',($(window).height()/2) - ($('#search-outer > #search input').height()/2) - $headerHeightSpace );
}



//text on hover effect
$('body').on('mouseover','.text_on_hover .product-wrap',function(){
	$(this).parent().addClass('hovered');
});
$('body').on('mouseover','.text_on_hover > a:first-child',function(){
	$(this).parent().addClass('hovered');
});

$('body').on('mouseout','.text_on_hover .product-wrap',function(){
	$(this).parent().removeClass('hovered');
});
$('body').on('mouseout','.text_on_hover > a:first-child',function(){
	$(this).parent().removeClass('hovered');
});


//material/fancy parallax hover effect zindex
if($('.material.product').length > 0 || $('.minimal.product').length > 0 || $('.nectar-fancy-box[data-style="parallax_hover"]').length > 0 || $('.nectar-category-grid[data-shadow-hover="yes"]').length > 0){

	var $productZindex = 101;
	
	$('body').on('mouseenter','.material.product, .minimal.product, .nectar-fancy-box[data-style="parallax_hover"], .nectar-category-grid[data-shadow-hover="yes"] .nectar-category-grid-item',function(){
		
		$productZindex++;

		$(this).css('z-index',$productZindex+1);
		
	});
	$('body').on('mouseleave','.material.product, .minimal.product, .nectar-fancy-box[data-style="parallax_hover"], .nectar-category-grid[data-shadow-hover="yes"] .nectar-category-grid-item',function(){
		
		var $that = $(this);
		setTimeout(function(){ if(!$that.is(':hover')) $that.css('z-index',100); },350);
	});

	//reset to stop zindex from getting too high
	setInterval(function(){ 
		if($('.nectar-fancy-box[data-style="parallax_hover"]:hover').length > 0 || $('.minimal.product:hover').length > 0) {
			 return;
		}

		$('.material.product:not(:hover), .minimal.product:not(:hover), .nectar-fancy-box[data-style="parallax_hover"]:not(:hover), .nectar-category-grid[data-shadow-hover="yes"] .nectar-category-grid-item:not(:hover)').css('z-index',100);
		$productZindex = 101; 
	},10000);

}

function minimalProductHover() {
	
	//add icons
	$('.products .classic .product-wrap .add_to_cart_button').wrapInner('<span />');
	$('.products .classic .product-wrap .add_to_cart_button').prepend('<i class="normal icon-salient-cart"></i>');
	
	//bind hover
	$('body').on('mouseover', '.products .minimal.product',function(){
		minimalProductCalc($(this));
	});
	
	$('body').on('mouseleave', '.products .minimal.product',function(){
		
		$(this).find('.background-color-expand').css({
			'transform': 'scale(1)'
		});
		
	});
	
	//starting trigger mouse over
	$('.products .minimal.product').each(function(){
		if($(this).is(':hover')) {
			$(this).trigger('mouseover');
		}
	});
}

function minimalProductCalc(el) {
	var $item = el;
	var $itemWidth = $item.width();
	var $itemHeight = $item.height();
	
	var $wChange = (parseInt($itemWidth) + 40) / parseInt($itemWidth);
	var $hChange = (parseInt($itemHeight) + 40) / parseInt($itemHeight);
	
	$item.addClass('hover-bound');
	
	$item.find('.background-color-expand').css({
		'transform': 'scale('+ $wChange + ',' + $hChange  +') translateY(0px)'
	});
}

minimalProductHover();




//mobile widget filters
////skip widgets without any titles
$('.woocommerce #sidebar .widget.woocommerce').each(function(){
	if($(this).find('> h4').length == 0) {
		$(this).addClass('no-widget-title');
	}
});

$('body').on('click','#sidebar .widget.woocommerce:not(.widget_price_filter) h4',function(){
	if($(window).width() < 1000) {
		$(this).parent().find('> ul').slideToggle();
		$(this).parent().toggleClass('open-filter');
	}
});

	//slide in cart
	$('body').on('mouseenter','#header-outer [data-cart-style="slide_in"] .cart-menu-wrap',openRightCart);

	function openRightCart() {

		if($('.nectar-slide-in-cart ul.cart_list li:not(.empty)').length > 0) {
			$('.nectar-slide-in-cart').addClass('open');

			$(window).on('mousemove.rightCartOffsetCheck',closeCartCheck);
		}
	}

	function closeCartCheck(e) {
		var $windowWidth = $(window).width();
		if(e.clientX < $windowWidth - 370 - $bodyBorderWidth) {

			$(window).off('mousemove.rightCartOffsetCheck',closeNavCheck);

			$('.nectar-slide-in-cart').removeClass('open');

		}

	}





/***************** Search ******************/
	var $placeholder = ($('#search-outer #search input[type=text][data-placeholder]').length > 0) ? $('#search-outer #search input[type=text]').attr('data-placeholder') : '';
	var logoHeight = parseInt($('#header-outer').attr('data-logo-height'));
	

	if($('body').hasClass('material') && $('#header-outer .bg-color-stripe').length == 0) {
		$('#header-outer').append('<div class="bg-color-stripe" />');
	} 

	////search box event
	$('body').on('click', '#search-btn a', function(){ return false; });
	$('body').on('mouseup', '#search-btn a:not(.inactive), #header-outer .mobile-search', function(){

		if($(this).hasClass('open-search')) { return false; } 


		if($('body').hasClass('ascend') || $('body[data-header-format="left-header"]').length > 0 && $('body.material').length == 0 ){ 
			$('#search-outer > #search form, #search-outer #search .span_12 span').css('opacity',0);
			$('#search-outer > #search form').css('bottom','10px');
			$('#search-outer #search .span_12 span').css('top','10px');
			$('#search-outer').show();
			$('#search-outer').stop().transition({scale: '1,0', 'opacity': 1},0).transition({ scale: '1,1'},400,'easeInOutCubic');

			$('#search-outer > #search form').delay(400).animate({'opacity':1, 'bottom':0},'easeOutCirc');
			$('#search-outer #search .span_12 span').delay(470).animate({'opacity':1, 'top':0},'easeOutCirc');
			
		} else if( !$('body').hasClass('material') ) {
			$('#search-outer').stop(true).fadeIn(600,'easeOutExpo');
		} else {

			/*material*/
			$('#header-outer[data-transparent-header="true"] .bg-color-stripe').css('transition',''); 

			$('#search-outer').addClass('material-open');
			$('#ajax-content-wrap').addClass('material-open');
			$('#header-outer').addClass('material-search-open');
			$('#fp-nav').addClass('material-ocm-open');


		}


		if($('body[data-header-format="left-header"]').length == 0) {
			$('body.original #search-outer > #search input[type="text"]').css({
				'top' : $('#search-outer').height()/2 - $('#search-outer > #search input[type="text"]').height()/2
			});
		}
		
		setTimeout(function(){

			$('#search input[type=text]').focus();
			
			if($('#search input[type=text]').attr('value') == $placeholder){
				$('#search input[type=text]').setCursorPosition(0);	
			}

		},300);

		//ascend
		if($('body').hasClass('ascend') || $('body[data-header-format="left-header"]').length > 0 && $('body.material').length == 0){ 
			searchFieldCenter();
		}

		$(this).toggleClass('open-search');

		//close slide out widget area
		$('.slide-out-widget-area-toggle a:not(#toggle-nav).open:not(.animating)').trigger('click');

		return false;
	});
	
	$('body:not(.material)').on('keydown','#search input[type=text]',function(){
		if($(this).attr('value') == $placeholder){
			$(this).attr('value', '');
		}
	});
	
	$('body:not(.material)').on('keyup','#search input[type=text]',function(){
		if($(this).attr('value') == ''){
			$(this).attr('value', $placeholder);
			$(this).setCursorPosition(0);
		}
	});
	
	
	////close search btn event
	$('body').on('click','#close',function(){
		closeSearch();
		$('#search-btn a, #header-outer .mobile-search').removeClass('open-search');
		return false;
	});

	//close material when clicking off the search
	$('body.material').on('click', '#ajax-content-wrap', function(e){
		if (e.originalEvent !== undefined) {
			closeSearch();
			$('#search-btn a, #header-outer .mobile-search').removeClass('open-search');
		}
	});

	//material gets esc
	if($('body.material').length > 0) {
		$(document).keyup(function(e) {

	     if (e.keyCode == 27) { 
	     	closeSearch();  
	     	$('#search-btn a').removeClass('open-search'); 

	     	//close ocm material
	     	if($('.ocm-effect-wrap.material-ocm-open').length > 0) {
	     		$('.slide-out-widget-area-toggle.material-open a').trigger('click');
	     	}
	     }

		});
	}

	//if user clicks away from the search close it
	$('body:not(.material)').on('blur','#search-box input[type=text]',function(e){
		closeSearch();
		$('#search-btn a, #header-outer .mobile-search').removeClass('open-search');
	});

	
	function closeSearch(){
		if($('body').hasClass('ascend') || $('body[data-header-format="left-header"]').length > 0 && $('body.material').length == 0){ 
			$('#search-outer').stop().transition({'opacity' :0},300,'easeOutCubic');
			$('#search-btn a').addClass('inactive');
			setTimeout(function(){ $('#search-outer').hide(); $('#search-btn a').removeClass('inactive'); },300);
		} else if($('body.material').length == 0) {
			$('#search-outer').stop(true).fadeOut(450,'easeOutExpo');
		}

		if($('body').hasClass('material')) {
			$('#ajax-content-wrap').removeClass('material-open');
			$('#header-outer').removeClass('material-search-open');
			$('#search-outer').removeClass('material-open');
			$('#fp-nav').removeClass('material-ocm-open');
		}
	}
	
	
	//mobile search
	$('body').on('click', '#mobile-menu #mobile-search .container a#show-search',function(){
		$('#mobile-menu .container > ul').slideUp(500);
		return false;
	});
	
/***************** Nav ******************/
	

	function centeredNavBottomBarReposition() {
		
		var $headerOuter = $('#header-outer');
		var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
		var $headerSpan3 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_3');
		var $secondaryHeader = $('#header-secondary-outer');
		var $headerBtns = $headerSpan3.find('nav >ul.buttons');
		var $bodyBorderSize = ($('.body-border-top').length > 0 && $(window).width() > 1000) ? $('.body-border-top').height(): 0;
		var override_remove_check = false;
		
		function centeredNavBottomBarSecondary() {
			if($('body.mobile').length > 0) {
				$('#header-outer').css('margin-top','');
			} else {
				if($('#header-outer').css('top') == '0px') {
						
						$(window).off('scroll', centeredNavBottomFixed_Add);
						$(window).off('scroll', centeredNavBottomFixed_Remove);
						
						$navLogoMargin = parseInt($('body.material #header-outer #logo').css('margin-top'));
						if($headerSpan9.offset().top - $navLogoMargin - nectarDOMInfo.scrollTop <= $headerOuter.offset().top - parseInt($secondaryHeader.height()) ) {
								$(window).on('scroll', centeredNavBottomFixed_Add);
						} else {
								$(window).on('scroll', centeredNavBottomFixed_Remove);
						}
					
				}

				
				$('#header-outer').css('margin-top',nectarDOMInfo.secondaryHeaderHeight);
				
				// custom mobile breakpoint
				if($('#header-outer .span_9').css('display') == 'none') {
					 $('#header-outer').css('margin-top','');
				} else if($('#header-outer .span_9').css('display') != 'none' && parseInt($('#header-outer').css('top')) > 0) {
					 $('#header-outer').css('top','');
				}
				
				
			}
			
		}
		
		if($secondaryHeader.length > 0) {
			
			if($('#header-outer[data-remove-fixed="1"]').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
				setTimeout(function(){
					nectarDOMInfo.secondaryHeaderHeight = $('#header-secondary-outer').height();
					centeredNavBottomBarSecondary();
				},50);
				$(window).smartresize(centeredNavBottomBarSecondary);
			}
		
		} 
		

		var $navLogoMargin = parseInt($('body.material #header-outer #logo').css('margin-top'));
		

		if($('#header-outer[data-condense="true"]').length > 0) {
			
				$(window).on('scroll',centeredNavBottomFixed_Add);
				$(window).trigger('scroll');
				
				
				$(window).smartresize(function(){
					
					condenseCustomBreakPointHelper();
					
					//trigger condense if switching from mobile to desktop and user has scrolled down
					if(nectarDOMInfo.windowWidth > 1000 && $('#header-outer').css('position') == 'fixed' && $('#header-outer').css('top') == '0px' && override_remove_check == false) {
						setTimeout(function(){
							$(window).off('scroll', centeredNavBottomFixed_Add);
							$(window).off('scroll', centeredNavBottomFixed_Remove);
							override_remove_check = true;
							centeredNavBottomFixed_Remove();
						},100);
							
					}
				});
				
				function condenseCustomBreakPointHelper() {
					var $withinCustomBreakpoint = mobileBreakPointCheck();
					
					//adding class to prevent logo from hiding when opening OCM
					if($withinCustomBreakpoint) {
						$('#header-outer').addClass('within-custom-breakpoint');
					} else {
						$('#header-outer').removeClass('within-custom-breakpoint');
					}
				}
				
				condenseCustomBreakPointHelper();

		}

		
		
		
		function centeredNavBottomFixed_Add() {
				
				if(nectarDOMInfo.windowWidth < 1000 || $('body.material-ocm-open').length > 0 || $('#header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"] .span_9').css('display') == 'none') { return; }
				
				$navLogoMargin = parseInt($('body.material #header-outer #logo').css('margin-top'));
				
				var $bodyBorderTop = ($('.body-border-top').length > 0 && nectarDOMInfo.secondaryHeaderHeight > 0) ? $('.body-border-top').height() : 0;
				
				if($headerSpan9.offset().top - $navLogoMargin - nectarDOMInfo.scrollTop <= $headerOuter.offset().top - parseInt(nectarDOMInfo.secondaryHeaderHeight) + $bodyBorderTop ) {
						
						var amountToMove = (parseInt($headerSpan9.position().top) - $navLogoMargin - parseInt(nectarDOMInfo.adminBarHeight)) + parseInt(nectarDOMInfo.secondaryHeaderHeight) - $bodyBorderTop;
						
						//megamenu RT
						if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer .megamenu').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0) {
							$('#header-outer').removeClass('no-transition');	
						}
						
						$headerOuter.addClass('fixed-menu').removeClass('transparent').css({
							'top' : '-' + amountToMove + 'px',
							'position': 'fixed'
						});
						
						var $headerNavBarOffset = $('header#top .span_9 nav >ul').offset().top;
						var $headerButtonsOffset = $headerBtns.offset().top;
						
						if($('#boxed > #header-outer').length > 0) {
							$headerButtonsOffset = $headerButtonsOffset + 20;
						}

						var $headerButtonsHeight = ($headerSpan9.find('.sf-menu > li > a').length > 0) ? (20 - parseInt($headerSpan9.find('.sf-menu > li > a').height())) / 2 : 2;
						$headerBtns.css('transform','translateY('+ (parseInt($headerNavBarOffset) - parseInt($headerButtonsOffset) - $headerButtonsHeight - 1) +'px)');
						
						$headerBtns.find('.nectar-woo-cart').css('height',$headerOuter.height() + parseInt($headerOuter.css('top')) - parseInt(nectarDOMInfo.adminBarHeight) + parseInt(nectarDOMInfo.secondaryHeaderHeight) );

						//search

						$(window).off('scroll', centeredNavBottomFixed_Add);
						$(window).on('scroll', centeredNavBottomFixed_Remove);
				}
		}
		
		function centeredNavBottomFixed_Remove() {
			
			if(nectarDOMInfo.windowWidth < 1000 || $('body.material-ocm-open').length > 0 || $('#header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"] .span_9').css('display') == 'none') { return; }
			
			$navLogoMargin = parseInt($('body.material #header-outer #logo').css('margin-top'));
			
			if($headerSpan9.offset().top - $navLogoMargin - nectarDOMInfo.scrollTop > $headerOuter.offset().top - parseInt(nectarDOMInfo.secondaryHeaderHeight) || override_remove_check ) {
						$headerOuter.removeClass('fixed-menu').css({
							'top' : '0',
							'position': 'absolute'
						});
						
						//reset override_remove_check
						override_remove_check = false;
						
						//search
						//$('#search-outer .container').css('transform','translateY(0px)');
						
						$headerBtns.css('transform','translateY(0px)');
						
						$headerBtns.find('.nectar-woo-cart').css('height','');
						
						if($('#header-outer.transparent').length == 0) {
							
							if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer .megamenu').length > 0) {
								$('#header-outer').removeClass('no-transition');	
							}
							
							if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0 && $('#header-outer .megamenu').length > 0) {
								
								if($('#header-outer').attr('data-transparent-header') == 'true' && $('.nectar-box-roll').length == 0 && $('.megamenu.sfHover').length == 0) {
									$('#header-outer').addClass('transparent');
									$('#header-outer').removeClass('no-transition');	
								}
								
								else if($('#header-outer').attr('data-transparent-header') == 'true' && $('.nectar-box-roll').length == 0 && $('.megamenu.sfHover').length > 0) {
									$('#header-outer').addClass('no-transition');	
								}
								
							} else {
								if($('#header-outer').attr('data-transparent-header') == 'true'  && $('.nectar-box-roll').length == 0) $('#header-outer').addClass('transparent');
							}
						}
						
						$(window).off('scroll', centeredNavBottomFixed_Remove);
						$(window).on('scroll', centeredNavBottomFixed_Add);
							
			}
			
		}
		
		
		
}
	
	

function condenseHeaderMobileOverride() {
	var $transCondenseHeader = $('#header-outer[data-has-menu="true"][data-format="centered-menu-bottom-bar"][data-condense="true"][data-transparent-header="true"] .span_9');
	
	if($transCondenseHeader.length > 0 && nectarDOMInfo.windowWidth > 1000 && $transCondenseHeader.css('display') == 'none') {
		$('#header-outer').removeClass('transparent').addClass('no-transition');
	} else if($transCondenseHeader.length > 0 && nectarDOMInfo.scrollTop < 20 && nectarDOMInfo.windowWidth > 1000 && $transCondenseHeader.css('display') != 'none') {
		$('#header-outer:not(.transparent)').addClass('transparent');
	}

	
}


	
	if($('#header-outer[data-format="centered-menu-bottom-bar"]').length > 0) {
		centeredNavBottomBarReposition();
		
		condenseHeaderMobileOverride();
		$(window).smartresize(condenseHeaderMobileOverride);
	}
	
	
	function centeredLogoMargins() {

		if($('#header-outer[data-format="centered-logo-between-menu"]').length > 0 && $(window).width() > 1000) {
			$midnightSelector = ($('#header-outer .midnightHeader').length > 0) ? '> .midnightHeader:first-child' : '';
			var $navItemLength = $('#header-outer[data-format="centered-logo-between-menu"] '+$midnightSelector+' nav > .sf-menu > li').length;
			if($('#header-outer #social-in-menu').length > 0) { $navItemLength--; }

			$centerLogoWidth = ($('#header-outer .row .col.span_3 #logo img:visible').length == 0) ? parseInt($('#header-outer .row .col.span_3').width()) : parseInt($('#header-outer .row .col.span_3 img:visible').width());

			$extraMenuSpace = ($('#header-outer[data-lhe="animated_underline"]').length > 0) ? parseInt($('#header-outer header#top nav > ul > li:first-child > a').css('margin-right')) : parseInt($('#header-outer header#top nav > ul > li:first-child > a').css('padding-right'));
			
			if($extraMenuSpace > 30) {
				$extraMenuSpace += 45;
			} else if($extraMenuSpace > 20) {
				$extraMenuSpace += 40;
			} else {
				$extraMenuSpace += 30;
			}

			$('#header-outer[data-format="centered-logo-between-menu"] nav > .sf-menu > li:nth-child('+Math.floor($navItemLength/2)+')').css({'margin-right': ($centerLogoWidth+$extraMenuSpace) + 'px'}).addClass('menu-item-with-margin');
			$leftMenuWidth = 0;
			$rightMenuWidth = 0;
			$('#header-outer[data-format="centered-logo-between-menu"] '+$midnightSelector+' nav > .sf-menu > li:not(#social-in-menu)').each(function(i){
				if(i+1 <= Math.floor($navItemLength/2)) {
					$leftMenuWidth += $(this).width();
				} else {
					$rightMenuWidth += $(this).width();
				}

			});

			var $menuDiff = Math.abs($rightMenuWidth - $leftMenuWidth);

			if($leftMenuWidth > $rightMenuWidth)  {
				$('#header-outer .row > .col.span_9').css('padding-right',$menuDiff);
			}
			else {
				$('#header-outer .row > .col.span_9').css('padding-left',$menuDiff);
			}

			$('#header-outer[data-format="centered-logo-between-menu"] nav').css('visibility','visible');
		}
	}
	
	var logoHeight = parseInt($('#header-outer').attr('data-logo-height'));
	var headerPadding = parseInt($('#header-outer').attr('data-padding'));
	var usingLogoImage = $('#header-outer').attr('data-using-logo');
	
	if( isNaN(headerPadding) || headerPadding.length == 0 ) { headerPadding = 28; }
	if( isNaN(logoHeight) || usingLogoImage.length == 0 ) { usingLogoImage = false; logoHeight = 30;}
	
	if($('header#top nav > ul li#search-btn a').length > 0) {
		var $searchBtnHeight = $('header#top nav > ul li#search-btn a').height();
	} else {
		var $searchBtnHeight = 24;
	}

	//inital calculations
	function headerInit() {
		
		if($('#header-outer[data-format="left-header"]').length > 0) return;


		if($('body.material').length == 0) {

			$('#header-outer #logo img').css({
				'height' : logoHeight				
			});	

			$('body:not(.material) #header-outer, .ascend #header-outer[data-full-width="true"][data-using-pr-menu="true"] header#top nav ul.buttons li.menu-item, .ascend #header-outer[data-full-width="true"][data-format="centered-menu"] header#top nav ul.buttons li#social-in-menu').css({
				'padding-top' : headerPadding
			});	
			
			if($('body.mobile').length == 0) {
				$('header#top nav > ul > li:not(#social-in-menu) > a').css({
					'padding-bottom' : Math.floor( ((logoHeight/2) - ($('header#top nav > ul > li > a').height()/2)) + headerPadding),
					'padding-top' : Math.floor( (logoHeight/2) - ($('header#top nav > ul > li > a').height()/2))
				});	
				
				var $socialInMenuHeight = ($('header#top nav > .sf-menu > li:not(#social-in-menu) a').length > 0 && $('header#top nav > .sf-menu > li:not(#social-in-menu) a').height() > 22) ? $('header#top nav > .sf-menu > li:not(#social-in-menu) a').height() : $('header#top nav > ul > li#social-in-menu > a i').height();
				
				$('header#top nav > ul > li#social-in-menu > a').css({
					'margin-top' : Math.ceil(logoHeight/2) - Math.ceil($socialInMenuHeight/2)
				});	
			}
			
			if($('#header-outer[data-format="centered-menu-under-logo"]').length == 0) {
				$('#header-outer .cart-menu').css({  
					'padding-bottom' : Math.ceil(((logoHeight/2) - ($searchBtnHeight/2)) + headerPadding),
					'padding-top' : Math.ceil(((logoHeight/2) - ($searchBtnHeight/2)) + headerPadding)
				});	
			} 
			
			
			$('header#top nav > ul li#search-btn, header#top nav > ul li#nectar-user-account, header#top nav > ul li.slide-out-widget-area-toggle').css({
				'padding-bottom' : (logoHeight/2) - ($searchBtnHeight/2),
				'padding-top' : (logoHeight/2) - ($searchBtnHeight/2)
			});	
			

			
			if($('body.ascend ').length > 0 && $('#header-outer[data-full-width="true"]').length > 0) {
				$('header#top nav > ul li#search-btn, header#top nav > ul li#nectar-user-account, header#top nav > ul li.slide-out-widget-area-toggle').css({
					'padding-top': 0,
					'padding-bottom': 0
				});

				$('header#top nav > ul.buttons').css({
					'margin-top' : - headerPadding,
					'height' : Math.floor(logoHeight + headerPadding*2) -1
				});

				$('header#top nav > ul li#search-btn a, header#top nav > ul li#nectar-user-account a, header#top nav > ul li.slide-out-widget-area-toggle a').css({
					'visibility' : 'visible',
					'padding-top': Math.floor((logoHeight/2) - ($searchBtnHeight/2) + headerPadding),
					'padding-bottom': Math.floor((logoHeight/2) - ($searchBtnHeight/2) + headerPadding)
				});
			}
			
			$('header#top .sf-menu > li > ul, header#top .sf-menu > li.sfHover > ul').css({
				'top' : $('header#top nav > ul.sf-menu > li > a').outerHeight() 
			});	
			
			
			setTimeout(function(){ 
				$('body:not(.ascend):not(.material) #search-outer #search-box .ui-autocomplete').css({
					'top': parseInt($('#header-outer').outerHeight())+'px'
				}); 
			},1000);
			
			$('body:not(.ascend):not(.material) #search-outer #search-box .ui-autocomplete').css({
				'top': parseInt($('#header-outer').outerHeight())+'px'
			});

		}//not material skin

		//header space
		if($('.nectar-parallax-scene.first-section').length == 0) {

			if($('#header-outer').attr('data-using-secondary') == '1'){
				if($('#header-outer[data-mobile-fixed="false"]').length > 0  || $('body.mobile').length == 0 ) {
					$('#header-space').css('height', parseInt($('#header-outer').outerHeight()) + $('#header-secondary-outer').height());
					
				} else {
					$('#header-space').css('height', parseInt($('#header-outer').outerHeight()));
					
				}
				
			} else {

				$('#header-space').css('height', $('#header-outer').outerHeight() );
			} 
		}
		
		$('#header-outer .container, #header-outer .cart-menu').css('visibility','visible');
		

		if($('#header-outer[data-format="centered-menu-under-logo"]').length == 0) {
			$('body:not(.ascend):not(.material) #search-outer, #search .container').css({
				'height' : logoHeight + headerPadding*2
			});	
			
			$('body:not(.ascend):not(.material) #search-outer > #search input[type="text"]').css({
				'font-size'  : 43,
				'height' : '59px',
				'top' : ((logoHeight + headerPadding*2)/2) - $('#search-outer > #search input[type="text"]').height()/2
			});
			
			$('body:not(.ascend):not(.material) #search-outer > #search #close a').css({
				'top' : ((logoHeight + headerPadding*2)/2) - 8
			});	
		} else {
			$('body:not(.ascend):not(.material) #search-outer, #search .container').css({
				'height' : logoHeight + headerPadding*2 + logoHeight + 17
			});	
			
			$('body:not(.ascend):not(.material) #search-outer > #search input[type="text"]').css({
				'font-size'  : 43,
				'height' : '59px',
				'top' : ((logoHeight + headerPadding*2)/2) - ($('#search-outer > #search input[type="text"]').height()/2) + logoHeight/2 + 17
			});
			
			$('body:not(.ascend):not(.material) #search-outer > #search #close a').css({
				'top' : ((logoHeight + headerPadding*2)/2) - 8 + logoHeight/2 + 17
			});	
		}
		

	}

	
	//is header resize on scroll enabled?
	var headerResize = $('#header-outer').attr('data-header-resize');
	var headerHideUntilNeeded = $('body').attr('data-hhun');

	//transparent fix

	if($('#header-outer[data-remove-fixed="1"]').length == 0) {
		if($(window).scrollTop() != 0 && $('#header-outer.transparent[data-permanent-transparent="false"]').length == 1) $('#header-outer').removeClass('transparent');
	}

	if( headerResize == 1 && headerHideUntilNeeded != '1'){
		
		headerInit();

		$(window).off('scroll.headerResizeEffect');

		if($('#nectar_fullscreen_rows').length == 0) {
			$(window).on('scroll.headerResizeEffect',smallNav);
		}
		else if($('#nectar_fullscreen_rows[data-mobile-disable="on"]').length > 0 && navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) ) {
			$(window).on('scroll.headerResizeEffect',smallNav);
		}

	} else if(headerHideUntilNeeded != '1') {
		headerInit();
		$(window).off('scroll.headerResizeEffectOpaque');
		$(window).on('scroll.headerResizeEffectOpaque',opaqueCheck);
		
	} else if(headerHideUntilNeeded == '1') {

		headerInit();

		if($('.nectar-box-roll').length > 0) $('#header-outer').addClass('at-top-before-box');

		var previousScroll = 0, // previous scroll position
        menuOffset = $('#header-space').height()*2, // height of menu (once scroll passed it, menu is hidden)
        detachPoint = ($('body.mobile').length > 0) ? 150 : 600, // point of detach (after scroll passed it, menu is fixed)
        hideShowOffset = 6; // scrolling value after which triggers hide/show menu

	    // on scroll hide/show menu
	    function hhunCalcs(e) {

	     //stop scrolling while animated anchors
	     if($('body.animated-scrolling').length > 0 && $('#header-outer.detached').length > 0) return false;

	     //stop when material ocm or material search is open
	     if($('body.material-ocm-open').length > 0 || $('#search-outer.material-open').length > 0) { return false; }

	     //stop on mobile if not using sticky option
	      if($('#header-outer[data-mobile-fixed="false"]').length > 0 && $('body.mobile').length > 0) {  $('#header-outer').removeClass('detached'); return false; }

	      var currentScroll = $(this).scrollTop(), // gets current scroll position
	            scrollDifference = Math.abs(currentScroll - previousScroll); // calculates how fast user is scrolling

	      if (!$('#header-outer').hasClass('side-widget-open') && !$('#header-outer .slide-out-widget-area-toggle a').hasClass('animating')) {
	       
	        // if scrolled past menu
	        if (currentScroll > menuOffset) {
	          // if scrolled past detach point add class to fix menu
	          if (currentScroll > detachPoint) {
	            if (!$('#header-outer').hasClass('detached'))
	              $('#header-outer').addClass('detached').removeClass('parallax-contained');
	          	   $('#header-outer').removeClass('no-transition');	


	          	  if($('#header-outer[data-permanent-transparent="1"]').length == 0) $('#header-outer').removeClass('transparent');
	          }

	          // if scrolling faster than hideShowOffset hide/show menu
	          if (scrollDifference >= hideShowOffset) {
	            if (currentScroll > previousScroll) {
	              // scrolling down; hide menu
	              if (!$('#header-outer').hasClass('invisible')) {
								
									if($('#header-outer.at-top').length > 0) {
									
										//prevent header from transitioning when first hiding
										$('#header-outer').addClass('no-trans-hidden');
										setTimeout(function(){
											$('#header-outer').addClass('invisible').removeClass('at-top');
											$('#header-outer').removeClass('no-trans-hidden');
										},30);
										
									} else {
										
										$('#header-outer').addClass('invisible').removeClass('at-top');
									}
								
	                
									
	                 //close submenus
	                 if( $(".sf-menu").length > 0 && $().superfish ) {
		          		 $(".sf-menu").superfish('hide');
		          		 $('header#top nav > ul.sf-menu > li.menu-item-over').removeClass('menu-item-over');
		          	 }

	              }
	                $('.page-submenu.stuck').css('transform','translateY(0px)').addClass('header-not-visible');

	            	
	            } else {
	              // scrolling up; show menu
	              if ($('#header-outer').hasClass('invisible'))
	                $('#header-outer').removeClass('invisible');

	            	if($('.body-border-top').length > 0 && $('body.mobile').length == 0) {

	            		var $bodyBorderHeaderColorMatch = ($('.body-border-top').css('background-color') == '#ffffff' && $('body').attr('data-header-color') == 'light' || $('.body-border-top').css('background-color') == $('#header-outer').attr('data-user-set-bg')) ? true : false;
	            		var $bodyBorderSizeToRemove = ($bodyBorderHeaderColorMatch) ? $('.body-border-top').height() : 0;
	          	    	$('.page-submenu.stuck').css('transform','translateY('+ ($('#header-outer').outerHeight()-$bodyBorderSizeToRemove) +'px)').removeClass('header-not-visible');

	            	} else {
	            		$('.page-submenu.stuck').css('transform','translateY('+$('#header-outer').outerHeight()+'px)').removeClass('header-not-visible');
	            	}
	            }
	          }
	        } else {
	          // only remove "detached" class if user is at the top of document (menu jump fix)
	          $topDetachNum = ($('#header-outer[data-using-secondary="1"]').length > 0) ? 32 : 0;
	          if($('.body-border-top').length > 0) {
	          		$topDetachNum = ($('#header-outer[data-using-secondary="1"]').length > 0) ? 32 + $('.body-border-top').height() : $('.body-border-top').height();
	          }

	          if (currentScroll <= $topDetachNum){
	            $('#header-outer').removeClass('detached').removeClass('invisible').addClass('at-top');
	            
	            if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0 && $('#header-outer .megamenu').length > 0) {
		            if($('#header-outer[data-transparent-header="true"]').length > 0 && $('.nectar-box-roll').length == 0 && $('.megamenu.sfHover').length == 0) { 
		            	$('#header-outer').addClass('transparent').css('transform','translateY(0)');
		            	$('#header-outer').removeClass('no-transition');	
		            }
		            else if($('.nectar-box-roll').length > 0) $('#header-outer').css('transform','translateY(0)').addClass('at-top-before-box');
		          
		        } else {
		        	 if($('#header-outer[data-transparent-header="true"]').length > 0 && $('.nectar-box-roll').length == 0) $('#header-outer').addClass('transparent').css('transform','translateY(0)');
		            else if($('.nectar-box-roll').length > 0) $('#header-outer').css('transform','translateY(0)').addClass('at-top-before-box');
		        }

	            if( $('#page-header-bg[data-parallax="1"]').length > 0) $('#header-outer').addClass('parallax-contained').css('transform','translateY(0)');
	          }
	        }

	        // if user is at the bottom of document show menu
	        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
	          $('#header-outer').removeClass('invisible');

	            if($('.body-border-top').length > 0 && $('body.mobile').length == 0) {

            		var $bodyBorderHeaderColorMatch = ($('.body-border-top').css('background-color') == '#ffffff' && $('body').attr('data-header-color') == 'light' || $('.body-border-top').css('background-color') == $('#header-outer').attr('data-user-set-bg')) ? true : false;
            		var $bodyBorderSizeToRemove = ($bodyBorderHeaderColorMatch) ? $('.body-border-top').height() : 0;
          	    	$('.page-submenu.stuck').css('transform','translateY('+ ($('#header-outer').outerHeight()-$bodyBorderSizeToRemove) +'px)').removeClass('header-not-visible');
	            }
	            else {
	            	$('.page-submenu.stuck').css('transform','translateY('+$('#header-outer').outerHeight()+'px)').removeClass('header-not-visible');
	            }
	          
	        }

	      }

	      // replace previous scroll position with new one
	      previousScroll = currentScroll;

	    }

	    //don't trigger for header remove stickiness
	    if($('#header-outer[data-remove-fixed="1"]').length == 0) {
		    hhunCalcs();
		    $(window).scroll(hhunCalcs);
		}


	}//end if hhun
	
	if($('#nectar_fullscreen_rows').length == 0 && $('.nectar-box-roll').length == 0) { midnightInit(); }	
	
	if($('#nectar_fullscreen_rows').length > 0 ) { ($('#header-outer').attr('data-permanent-transparent','false')) }

	var shrinkNum = 6;
	var extraHeight = ($('#wpadminbar').length > 0) ? $('#wpadminbar').height() : 0; //admin bar
	var $bodyBorderHeaderColorMatch = ($('.body-border-top').css('background-color') == '#ffffff' && $('body').attr('data-header-color') == 'light' || $('.body-border-top').css('background-color') == $('#header-outer').attr('data-user-set-bg')) ? true : false;
	var $scrollTriggerOffset = ( $('body.material').length > 0 ) ? 150 : 0;

	if($('#header-outer[data-shrink-num]').length > 0 ) shrinkNum = $('#header-outer').attr('data-shrink-num');

	function smallNav(){
		var $offset = $(window).scrollTop();
		var $windowWidth = $(window).width();
		

		if($offset > $scrollTriggerOffset && $windowWidth > 1000 && $('body.material-ocm-open').length == 0) {
		

			//if going to small when material search is closing, it must be closed immediately
			if($('body.material').length > 0) {
				if($('#search-outer.material-open').length == 0) {
					$('#header-outer[data-transparent-header="true"] .bg-color-stripe').css('transition','none');
				}

				//secondary header
				if($('#header-secondary-outer').length > 0) {
					$('#header-secondary-outer').addClass('hide-up').css('transform','translateY(-100%)');
					$('#header-outer').css('transform', 'translateY(-' +$('#header-secondary-outer').height()+ 'px)');
				}

			}

			if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0 && $('#header-outer .megamenu').length > 0) {
				if($('#header-outer').attr('data-transparent-header') == 'true' && $('#header-outer.side-widget-open').length == 0 && $('#header-outer[data-permanent-transparent="1"]').length == 0 && $('.megamenu.sfHover').length == 0) { 
					$('#header-outer').removeClass('transparent');
					$('#header-outer').removeClass('no-transition');	
				}
			} else {
				if($('#header-outer').attr('data-transparent-header') == 'true' && $('#header-outer.side-widget-open').length == 0 && $('#header-outer[data-permanent-transparent="1"]').length == 0) $('#header-outer').removeClass('transparent');
			}

			
			$('#header-outer, #search-outer').addClass('small-nav');
			
			//body border full width side padding
			if($('#header-outer[data-full-width="true"][data-transparent-header="true"]').length > 0 && $('.body-border-top').length > 0 && $bodyBorderHeaderColorMatch == true) {
				$('#header-outer[data-full-width="true"] header > .container').stop(true,true).animate({
					'padding' : '0'			
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
			}

			if($('body.material').length > 0) {
				$('header#top nav > ul > li.menu-item-with-margin').stop(true,true).animate({
					'margin-right': (parseInt($('header#top nav > ul > li.menu-item-with-margin').css('margin-right')) - parseInt(shrinkNum)*3) +'px'
				},{queue:false, duration:310, easing: 'easeOutQuad'});	
			}
			if($('body.material').length == 0) {

				$('#header-outer #logo img').stop(true,true).animate({
					'height' : logoHeight - shrinkNum
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
					
				$('body:not(.material) #header-outer, .ascend #header-outer[data-full-width="true"][data-using-pr-menu="true"] header#top nav ul.buttons li.menu-item, .ascend #header-outer[data-full-width="true"][data-format="centered-menu"] header#top nav ul.buttons li#social-in-menu').stop(true,true).animate({
					'padding-top' : Math.ceil(headerPadding / 1.8)
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
				
				if($('#header-outer[data-format="centered-menu-under-logo"]').length > 0) {
					$('#header-outer .row > .span_3').stop(true,true).animate({
						'padding-bottom' : Math.ceil(headerPadding / 1.8)
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}

				$('header#top nav > ul > li:not(#social-in-menu) > a').stop(true,true).animate({
					'padding-bottom' :  Math.floor((((logoHeight-shrinkNum)/2) - ($('header#top nav > ul > li > a').height()/2)) + headerPadding / 1.8) ,
					'padding-top' :  Math.floor(((logoHeight-shrinkNum)/2) - ($('header#top nav > ul > li > a').height()/2)) 
				},{queue:false, duration:250, easing: 'easeOutCubic'});	 

				

				$('header#top nav > ul > li#social-in-menu > a').stop(true,true).animate({
					'margin-bottom' :  Math.floor((((logoHeight-shrinkNum)/2) - ($('header#top nav > ul > li#social-in-menu > a').height()/2)) + headerPadding / 1.8) ,
					'margin-top' :  Math.floor(((logoHeight-shrinkNum)/2) - ($('header#top nav > ul > li#social-in-menu > a').height()/2)) 
				},{queue:false, duration:250, easing: 'easeOutCubic'});	 
				
				$('header#top nav > ul > li.menu-item-with-margin').stop(true,true).animate({
					'margin-right': (parseInt($('header#top nav > ul > li.menu-item-with-margin').css('margin-right')) - parseInt(shrinkNum)*3) +'px'
				},{queue:false, duration:250, easing: 'easeOutCubic'});	 

				if($bodyBorderHeaderColorMatch == true) {
					$('.body-border-top').stop(true,true).animate({
						'margin-top': '-'+$('.body-border-top').height()+'px'
					},{queue:false, duration:400, easing: 'easeOutCubic', complete: function() { $(this).css('margin-top',0)} });	 
				}

				if($('#header-outer[data-format="centered-menu-under-logo"]').length == 0) {
					$('#header-outer .cart-menu').stop(true,true).animate({
						'padding-top' : Math.ceil(((logoHeight-shrinkNum)/2) - ($searchBtnHeight/2) + headerPadding/ 1.7),
						'padding-bottom' : Math.ceil(((logoHeight-shrinkNum)/2) - ($searchBtnHeight/2) + headerPadding/ 1.7) +1
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				} 
				
				if($('body.ascend ').length > 0 && $('#header-outer[data-full-width="true"]').length > 0) {
					$('header#top nav > ul.buttons').stop(true,true).animate({
						'margin-top' : - Math.ceil(headerPadding/ 1.8),
						'height' : Math.floor((headerPadding*2)/ 1.8 + logoHeight-shrinkNum)
					},{queue:false, duration:250, easing: 'easeOutCubic'});	

					$('header#top nav > ul li#search-btn a, header#top nav > ul li#nectar-user-account a, header#top nav > ul li.slide-out-widget-area-toggle a').stop(true,true).animate({
						'padding-top' : Math.ceil(((logoHeight-shrinkNum)/2) - ($searchBtnHeight/2) + headerPadding/ 1.7),
						'padding-bottom' : Math.ceil(((logoHeight-shrinkNum)/2) - ($searchBtnHeight/2) + headerPadding/ 1.7) +1
					},{queue:false, duration:250, easing: 'easeOutCubic'});	

				} else {
					$('header#top nav > ul li#search-btn, header#top nav > ul li#nectar-user-account, header#top nav > ul li.slide-out-widget-area-toggle').stop(true,true).animate({
						'padding-bottom' : Math.ceil(((logoHeight-shrinkNum)/2) - ($searchBtnHeight/2)),
						'padding-top' : Math.ceil(((logoHeight-shrinkNum)/2) - ($searchBtnHeight/2))
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}

				if($('#header-outer[data-format="centered-menu-under-logo"]').length > 0) {
					$('header#top .sf-menu > li > ul, header#top .sf-menu > li.sfHover > ul').stop(true,true).animate({
						'top' : Math.floor($('header#top nav > ul > li > a').outerHeight())
					},{queue:false, duration:250, easing: 'easeOutCubic'});		
				} else {
					$('header#top .sf-menu > li > ul, header#top .sf-menu > li.sfHover > ul').stop(true,true).animate({
						'top' : Math.floor($('header#top nav > ul > li > a').height() + (((logoHeight-shrinkNum)/2) - ($('header#top nav > ul > li > a').height()/2))*2 + headerPadding / 1.8),
					},{queue:false, duration:250, easing: 'easeOutCubic'});		
				}
				
				$('body:not(.ascend) #search-outer #search-box .ui-autocomplete').stop(true,true).animate({
					'top': Math.floor((logoHeight-shrinkNum) + (headerPadding*2)/ 1.8) +'px'
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
			

				if($('#header-outer[data-format="centered-menu-under-logo"]').length == 0) {
					$('body:not(.ascend) #search-outer, #search .container').stop(true,true).animate({
						'height' : Math.floor((logoHeight-shrinkNum) + (headerPadding*2)/ 1.8)
					},{queue:false, duration:250, easing: 'easeOutCubic'});	

					$('body:not(.ascend) #search-outer > #search input[type="text"]').stop(true,true).animate({
						'font-size'  : 30,
						'line-height' : '30px',
						'height' : '44px',
						'top' : ((logoHeight-shrinkNum+headerPadding+5)/2) - ($('#search-outer > #search input[type="text"]').height()-15)/2
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				
					$('body:not(.ascend) #search-outer > #search #close a').stop(true,true).animate({
						'top' : ((logoHeight-shrinkNum + headerPadding+5)/2) - 10
					},{queue:false, duration:250, easing: 'easeOutCubic'});	

				} else {
					$('body:not(.ascend) #search-outer, #search .container').stop(true,true).animate({
						'height' : Math.floor((logoHeight-shrinkNum) + (headerPadding*2)/ 1.8) + logoHeight - shrinkNum + 17
					},{queue:false, duration:250, easing: 'easeOutCubic'});	

					$('body:not(.ascend) #search-outer > #search input[type="text"]').stop(true,true).animate({
						'font-size'  : 30,
						'line-height' : '30px',
						'height' : '44px',
						'top' : ((logoHeight-shrinkNum+headerPadding+5)/2) - ($('#search-outer > #search input[type="text"]').height()-15)/2 + (logoHeight- shrinkNum)/2 + 8
					},{queue:false, duration:250, easing: 'easeOutCubic'});	

					$('body:not(.ascend) #search-outer > #search #close a').stop(true,true).animate({
						'top' : ((logoHeight-shrinkNum + headerPadding+5)/2) - 10 + (logoHeight- shrinkNum)/2 + 8
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}

			}//not material skin
		
			

			//box roll
			if($('.nectar-box-roll').length > 0 && $('#header-outer[data-permanent-transparent="1"]').length == 0) $('#ajax-content-wrap').animate({'margin-top':  (Math.floor((logoHeight-shrinkNum) +(headerPadding*2)/ 1.8 + extraHeight + secondaryHeader))  },{queue:false, duration:250, easing: 'easeOutCubic'})
			
			
			if($('body').hasClass('ascend')){ 
				$('#search-outer').stop(true,true).animate({
					'top' : Math.floor((logoHeight-shrinkNum) +(headerPadding*2)/ 1.8 + extraHeight + secondaryHeader)
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
			}
			
			//if no image is being used
			if(usingLogoImage == false) $('body:not(.material) header#top #logo').stop(true,true).animate({
				'margin-top' : 0
			},{queue:false, duration:450, easing: 'easeOutExpo'});	
			
			$(window).off('scroll',smallNav);
			$(window).on('scroll',bigNav);

			//dark slider coloring border fix
			$('#header-outer[data-transparent-header="true"]').css('transition','transform 0.3s ease, background-color 0.30s ease, opacity 0.3s ease, box-shadow 0.30s ease, margin 0.25s ease-out');
			$('#header-outer[data-transparent-header="true"] .cart-menu').css('transition','none');
			setTimeout(function(){ 
				$('#header-outer[data-transparent-header="true"]').css('transition','transform 0.3s ease, background-color 0.30s ease, opacity 0.3s ease, box-shadow 0.30s ease, border-color 0.30s ease, margin 0.25s ease-out'); 
				$('#header-outer[data-transparent-header="true"] .cart-menu').css('transition','border-color 0.30s ease');
			},300);

		}

	}
	
	function bigNav(){
		var $offset = $(window).scrollTop();
		var $windowWidth = $(window).width();

		if($('body.material-ocm-open').length > 0) { return false; }

		if($offset <= $scrollTriggerOffset && $windowWidth > 1000 || $('.small-nav').length > 0 && $('#ajax-content-wrap.no-scroll').length > 0 ) {
			

			$('#header-outer, #search-outer').removeClass('small-nav');
			
			if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0 && $('#header-outer .megamenu').length > 0) {
				if($('#header-outer').attr('data-transparent-header') == 'true'  && $('.nectar-box-roll').length == 0 && $('.megamenu.sfHover').length == 0) {
					$('#header-outer').addClass('transparent');
					$('#header-outer').removeClass('no-transition');	
				}
			} else {
				if($('#header-outer').attr('data-transparent-header') == 'true'  && $('.nectar-box-roll').length == 0) $('#header-outer').addClass('transparent');
			}

			


			//body border full width side padding
			if($('#header-outer[data-full-width="true"][data-transparent-header="true"]').length > 0 && $('.body-border-top').length > 0 && $bodyBorderHeaderColorMatch == true) {
				$('#header-outer[data-full-width="true"] header > .container').stop(true,true).animate({
					'padding' : '0 28px'			
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
			}


			if($('body.material').length > 0) {

				$('header#top nav > ul > li.menu-item-with-margin').stop(true,true).animate({
					'margin-right': (parseInt($('header#top nav > ul > li.menu-item-with-margin').css('margin-right')) + parseInt(shrinkNum)*3) +'px'
				},{queue:false, duration:140, easing: 'easeOutQuad'});

				//secondary header
				if($('#header-secondary-outer').length > 0) {
					$('#header-secondary-outer, #header-outer').removeClass('hide-up').css('transform','translateY(0%)');
				}
		
			}

			if($('body.material').length == 0) {

				$('#header-outer #logo img').stop(true,true).animate({
					'height' : logoHeight,				
				},{queue:false, duration:250, easing: 'easeOutCubic'});	

				$('body:not(.material) #header-outer, .ascend #header-outer[data-full-width="true"][data-using-pr-menu="true"] header#top nav ul.buttons li.menu-item, .ascend #header-outer[data-full-width="true"][data-format="centered-menu"] header#top nav ul.buttons li#social-in-menu').stop(true,true).animate({
					'padding-top' : headerPadding 
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
				
				if($('#header-outer[data-format="centered-menu-under-logo"]').length > 0) {
					$('#header-outer .row > .span_3').stop(true,true).animate({
						'padding-bottom' : headerPadding 
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}

				$('header#top nav > ul > li:not(#social-in-menu) > a').stop(true,true).animate({
					'padding-bottom' : ((logoHeight/2) - ($('header#top nav > ul > li > a').height()/2)) + headerPadding,
					'padding-top' : (logoHeight/2) - ($('header#top nav > ul > li > a').height()/2) 
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
				
				var $socialInMenuHeight = ($('header#top nav > .sf-menu > li:not(#social-in-menu) a').length > 0 && $('header#top nav > .sf-menu > li:not(#social-in-menu) a').height() > 22) ? $('header#top nav > .sf-menu > li:not(#social-in-menu) a').height() : $('header#top nav > ul > li#social-in-menu > a i').height();
				
				$('header#top nav > ul > li#social-in-menu > a').stop(true,true).animate({
					'margin-top' : Math.ceil(logoHeight/2) - Math.ceil($socialInMenuHeight/2)
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
					
				$('header#top nav > ul > li.menu-item-with-margin').stop(true,true).animate({
					'margin-right': (parseInt($('header#top nav > ul > li.menu-item-with-margin').css('margin-right')) + parseInt(shrinkNum)*3) +'px'
				},{queue:false, duration:250, easing: 'easeOutCubic'});	 

				if($bodyBorderHeaderColorMatch == true) {
					$('.body-border-top').css({ 'margin-top': '-'+$('.body-border-top').height()+'px'}).stop(true,true).animate({
						'margin-top': '0'
					},{queue:false, duration:250, easing: 'easeOutCubic'});	 
				}

				if($('#header-outer[data-format="centered-menu-under-logo"]').length == 0) {
					$('#header-outer .cart-menu').stop(true,true).animate({
						'padding-bottom' : Math.ceil(((logoHeight/2) - ($searchBtnHeight/2)) + headerPadding),
						'padding-top' : Math.ceil(((logoHeight/2) - ($searchBtnHeight/2)) + headerPadding)
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}

				if($('body.ascend ').length > 0 && $('#header-outer[data-full-width="true"]').length > 0) {
					$('header#top nav > ul.buttons').stop(true,true).animate({
						'margin-top' : - Math.ceil(headerPadding),
						'height' : Math.floor(headerPadding*2 + logoHeight) -1
					},{queue:false, duration:250, easing: 'easeOutCubic'});	

					$('header#top nav > ul li#search-btn a, header#top nav > ul li#nectar-user-account a, header#top nav > ul li.slide-out-widget-area-toggle a').stop(true,true).animate({
						'padding-top': Math.floor((logoHeight/2) - ($searchBtnHeight/2) + headerPadding),
						'padding-bottom': Math.floor((logoHeight/2) - ($searchBtnHeight/2) + headerPadding)
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				} else {
					$('header#top nav > ul li#search-btn, header#top nav > ul li#nectar-user-account, header#top nav > ul li.slide-out-widget-area-toggle').stop(true,true).animate({
						'padding-bottom' : Math.floor((logoHeight/2) - ($searchBtnHeight/2)),
						'padding-top' : Math.ceil((logoHeight/2) - ($searchBtnHeight/2))
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}
				
				if($('#header-outer[data-format="centered-menu-under-logo"]').length > 0) {
					$('header#top .sf-menu > li > ul, header#top .sf-menu > li.sfHover > ul').stop(true,true).animate({
						'top' : Math.floor($('header#top nav > ul > li > a').outerHeight())
					},{queue:false, duration:250, easing: 'easeOutCubic'});		
				} else {
					$('header#top .sf-menu > li > ul, header#top .sf-menu > li.sfHover > ul').stop(true,true).animate({
						'top' : Math.ceil($('header#top nav > ul > li > a').height() + (((logoHeight)/2) - ($('header#top nav > ul > li > a').height()/2))*2 + headerPadding),
					},{queue:false, duration:250, easing: 'easeOutCubic'});		
				}
				
				$('body:not(.ascend) #search-outer #search-box .ui-autocomplete').stop(true,true).animate({
					'top': Math.ceil(logoHeight + headerPadding*2) +'px'
				},{queue:false, duration:250, easing: 'easeOutCubic'});	
				
				
				if($('#header-outer[data-format="centered-menu-under-logo"]').length == 0) {
					$('body:not(.ascend) #search-outer, #search .container').stop(true,true).animate({
						'height' : Math.ceil(logoHeight + headerPadding*2)
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
					
					$('body:not(.ascend) #search-outer > #search input[type="text"]').stop(true,true).animate({
						'font-size'  : 43,
						'line-height' : '43px',
						'height' : '59px',
						'top' : ((logoHeight + headerPadding*2)/2) - 30
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
					
					
					$('body:not(.ascend) #search-outer > #search #close a').stop(true,true).animate({
						'top' : ((logoHeight + headerPadding*2)/2) - 8
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
					
				} else {
					$('body:not(.ascend) #search-outer, #search .container').stop(true,true).animate({
						'height' : Math.ceil(logoHeight + headerPadding*2) + logoHeight + 17
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
					
					$('body:not(.ascend) #search-outer > #search input[type="text"]').stop(true,true).animate({
						'font-size'  : 43,
						'line-height' : '43px',
						'height' : '59px',
						'top' : ((logoHeight + headerPadding*2)/2) - 30 + (logoHeight)/2 + 8
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
					
					
					$('body:not(.ascend) #search-outer > #search #close a').stop(true,true).animate({
						'top' : ((logoHeight + headerPadding*2)/2) - 8 + (logoHeight)/2 + 8
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}


				if($('body').hasClass('ascend')){ 
					$('#search-outer').stop(true,true).animate({
						'top' : (logoHeight) +(headerPadding*2) + extraHeight + secondaryHeader
					},{queue:false, duration:250, easing: 'easeOutCubic'});	
				}

				//if no image is being used
				if(usingLogoImage == false) $('header#top #logo').stop(true,true).animate({
					'margin-top' : 4
				},{queue:false, duration:450, easing: 'easeOutExpo'});	
				


			}//not material skin


				//box roll
			if($('.nectar-box-roll').length > 0 && $('#header-outer[data-permanent-transparent="1"]').length == 0) $('#ajax-content-wrap').animate({'margin-top':  (Math.floor((logoHeight) +(headerPadding*2) + extraHeight + secondaryHeader))  },{queue:false, duration:250, easing: 'easeOutCubic'})
			

			$(window).off('scroll',bigNav);
			$(window).on('scroll',smallNav);


			//dark slider coloring border fix
			$('#header-outer[data-transparent-header="true"]').css('transition','transform 0.3s ease, background-color 0.30s ease, opacity 0.3s ease, box-shadow 0.30s ease, margin 0.25s ease-out');
			$('#header-outer[data-transparent-header="true"] .cart-menu').css('transition','none');
			setTimeout(function(){ 
				$('#header-outer[data-transparent-header="true"]').css('transition','transform 0.3s ease, background-color 0.30s ease, opacity 0.3s ease, box-shadow 0.30s ease, border-color 0.30s ease, margin 0.25s ease-out'); 
				$('#header-outer[data-transparent-header="true"] .cart-menu').css('transition','border-color 0.30s ease');
			},300);
		}

	}
	
	
	function headerSpace() {
		if($('.mobile').length > 0) {
			if(window.innerHeight < window.innerWidth && window.innerWidth > 1000) {
				if($('#header-outer.small-nav').length == 0)
					$('#header-space').css('height', $('#header-outer').outerHeight() + $('#header-secondary-outer').height());
			} else {
				$('#header-space').css('height', $('#header-outer').outerHeight());
			}
			
		} else {
			if($('.nectar-parallax-scene.first-section').length == 0) {

				var shrinkNum = 6;	
				var headerPadding = parseInt($('#header-outer').attr('data-padding'));
				if($('#header-outer[data-shrink-num]').length > 0 ) shrinkNum = $('#header-outer').attr('data-shrink-num');
				var headerPadding2 = headerPadding - headerPadding/1.8;
				var $headerHeight = ($('#header-outer[data-header-resize="1"]').length > 0 && $('.small-nav').length > 0 ) ? $('#header-outer').outerHeight() + (parseInt(shrinkNum) + headerPadding2*2) : $('#header-outer').outerHeight();

				if($('#header-outer').attr('data-using-secondary') == '1'){
					$('#header-space').css('height', $headerHeight + $('#header-secondary-outer').height());
				} else {
					$('#header-space').css('height', $headerHeight);
				} 
			}
		}
		
	}

	//call immediately on mobile to make sure there's no gap with fixed setups
	if(navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/) && $('#header-outer[data-mobile-fixed="1"]').length > 0 && $('#header-outer[data-permanent-transparent="false"]').length > 0) {
		$('#header-space').css('height', $('#header-outer').outerHeight());
	}


	var lastPosition = -1;
	var $headerScrollTop = nectarDOMInfo.scrollTop;

	function headerOffsetAdjust(){
		if($('body.compose-mode').length > 0) { return; }
		$headerScrollTop = nectarDOMInfo.scrollTop;

		 if (lastPosition == $headerScrollTop) {
            requestAnimationFrame(headerOffsetAdjust);
            return false;
        } else lastPosition = $headerScrollTop;
		
		headerOffsetAdjustCalc();

		requestAnimationFrame(headerOffsetAdjust);

	}
	var condenseHeaderLayout = $('#header-outer[data-condense="true"]').length > 0 ? true : false;
	var headerOuterCached = $('#header-outer');
	
	function headerOffsetAdjustCalc() {
		if($('body.mobile').length > 0 || condenseHeaderLayout == true && $('#header-outer .span_9').css('display') == 'none') {
			var $eleHeight = 0;

			var $endOffset = ($('#wpadminbar').css('position') == 'fixed') ? $('#wpadminbar').height() : 0;
			$eleHeight += ($('#header-secondary-outer').length > 0 && $('#header-secondary-outer').css('display') == 'block') ? nectarDOMInfo.secondaryHeaderHeight : 0;
			$eleHeight += nectarDOMInfo.adminBarHeight;
			
			if( $eleHeight - $headerScrollTop > $endOffset) { 
				headerOuterCached.css('top', $eleHeight - $headerScrollTop + 'px');
			}
			else { headerOuterCached.css('top', $endOffset); }

		} else {
				if(condenseHeaderLayout == false) {
					var $eleHeight = 0;
					
					$eleHeight += nectarDOMInfo.secondaryHeaderHeight;
					$eleHeight += nectarDOMInfo.adminBarHeight;
					headerOuterCached.css('top',$eleHeight+'px');
				}
		}
	}

	if($('#header-outer[data-mobile-fixed="1"]').length > 0 && $('#wpadminbar').length > 0 || $('#header-outer[data-mobile-fixed="1"]').length > 0 && $('#header-secondary-outer').length > 0) {
		if($('#nectar_fullscreen_rows').length == 0) { requestAnimationFrame(headerOffsetAdjust); }
		else if($('#nectar_fullscreen_rows').length > 0 && $onMobileBrowser) {
			requestAnimationFrame(headerOffsetAdjust);
		}
		$(window).smartresize(headerOffsetAdjustCalc);
	}
		

	function footerRevealCalcs() {
		var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-outer').outerHeight();

		if($(window).height() - $('#wpadminbar').height() - $headerNavSpace - $('#footer-outer').height() - 1 -$('#page-header-bg').height()  - $('.page-header-no-bg').height() > 0) {
			var $resizeExtra = ($('body:not(.material) #header-outer[data-header-resize="1"]').length > 0) ? 55: 0;
			$('body[data-footer-reveal="1"] .container-wrap').css({'margin-bottom': $('#footer-outer').height()-1 });
			//let even non reveal footer have min height set when using material ocm
			$('.container-wrap').css({'min-height': $(window).height() - $('#wpadminbar').height() - $headerNavSpace - $('#footer-outer').height() -1  - $('.page-header-no-bg').height() -$('#page-header-bg').height() + $resizeExtra });
		} else {
			$('body[data-footer-reveal="1"] .container-wrap').css({'margin-bottom': $('#footer-outer').height()-1 });
		}
		
		if( $(window).width() < 1000) $('#footer-outer').attr('data-midnight','light');
		else $('#footer-outer').removeAttr('data-midnight');
	}
	if($('body[data-footer-reveal="1"]').length > 0 || $('body.material[data-slide-out-widget-area-style="slide-out-from-right"]').length > 0) { 
		setTimeout(function(){
			footerRevealCalcs();
		},60);
		footerRevealCalcs();
		//set shadow to match BG color if applicable
		if($('bodybody[data-footer-reveal="1"][data-footer-reveal-shadow="large_2"]').length > 0) $('.container-wrap').css({ boxShadow: '0 70px 110px -30px '+$('#footer-outer').css('backgroundColor') });
	}
	
	
	function opaqueCheck(){
		if($('#header-outer[data-format="centered-menu-bottom-bar"][data-condense="true"]').length > 0) { return; }
		
		var $offset = $(window).scrollTop();
		var $windowWidth = $(window).width();

		if($offset > 0 && $windowWidth > 1000) {
			
			if($('body.material').length > 0) {
				$('#header-outer').addClass('scrolled-down');

				//secondary header
				if($('#header-secondary-outer').length > 0) {
					$('#header-secondary-outer').addClass('hide-up').css('transform','translateY(-100%)');
					$('#header-outer').css('transform', 'translateY(-' +$('#header-secondary-outer').height()+ 'px)');
				}
			}

			if($('#header-outer').attr('data-transparent-header') == 'true' && $('#header-outer[data-permanent-transparent="1"]').length == 0) $('#header-outer').removeClass('transparent').addClass('scrolled-down');
			
			$(window).off('scroll',opaqueCheck);
			$(window).on('scroll',transparentCheck);
		}
	}
	
	
	function transparentCheck(){
		
		if($('#header-outer[data-format="centered-menu-bottom-bar"][data-condense="true"]').length > 0) { return; }
		
		var $offset = $(window).scrollTop();
		var $windowWidth = $(window).width();

		if($offset == 0 && $windowWidth > 1000 && $('body.material-ocm-open').length == 0) {
			
			if($('#header-outer[data-megamenu-rt="1"]').length > 0 && $('#header-outer[data-transparent-header="true"]').length > 0 && $('#header-outer .megamenu').length > 0) {
				
				if($('#header-outer').attr('data-transparent-header') == 'true' && $('.megamenu.sfHover').length == 0) { 
					$('#header-outer').addClass('transparent').removeClass('scrolled-down');
					$('#header-outer').removeClass('no-transition');	
				}
				else if($('#header-outer').attr('data-transparent-header') == 'true') { $('#header-outer').removeClass('scrolled-down'); }
				
			} else {
				if($('#header-outer').attr('data-transparent-header') == 'true') { $('#header-outer').addClass('transparent').removeClass('scrolled-down'); }
			}

			if($('body.material').length > 0) {
				$('#header-outer').removeClass('scrolled-down');

				//secondary header
				if($('#header-secondary-outer').length > 0) {
					$('#header-secondary-outer, #header-outer').removeClass('hide-up').css('transform','translateY(0%)');
				}
			}

			
			$(window).off('scroll',transparentCheck);
			$(window).on('scroll',opaqueCheck);
		}
	}
	

	
	
	//header inherit row color effect
	function headerRowColorInheritInit(){
		if($('body[data-header-inherit-rc="true"]').length > 0 && $('.mobile').length == 0){
			
			var headerOffset = ($('#header-outer[data-permanent-transparent="1"]').length == 0) ? (logoHeight - shrinkNum) + Math.ceil((headerPadding*2) / 1.8) + nectarDOMInfo.adminBarHeight : logoHeight/2 + headerPadding + nectarDOMInfo.adminBarHeight;
			
			$('.main-content > .row > .wpb_row').each(function(){

				var $that = $(this);
				var waypoint = new Waypoint({
	 				element: $that,
		 			handler: function(direction) {

						if(direction == 'down') {
							
							if($that.find('.row-bg.using-bg-color').length > 0) {
								
								var $textColor = ($that.find('> .col.span_12.light').length > 0) ? 'light-text' : 'dark-text';
								$('#header-outer').css('background-color',$that.find('.row-bg').css('background-color')).removeClass('light-text').removeClass('dark-text').addClass($textColor);
								$('#header-outer').attr('data-current-row-bg-color',$that.find('.row-bg').css('background-color'));

								$('body.material #header-outer .bg-color-stripe').css('background-color',$that.find('.row-bg').css('background-color'));
							} else {
								$('#header-outer').css('background-color',$('#header-outer').attr('data-user-set-bg')).removeClass('light-text').removeClass('dark-text');
								$('#header-outer').attr('data-current-row-bg-color',$('#header-outer').attr('data-user-set-bg'));

								$('body.material #header-outer .bg-color-stripe').css('background-color', '');
							}
						
						} else {

							if($that.prev('div.wpb_row').find('.row-bg.using-bg-color').length > 0) {
								var $textColor = ($that.prev('div.wpb_row').find('> .col.span_12.light').length > 0) ? 'light-text' : 'dark-text';
								$('#header-outer').css('background-color',$that.prev('div.wpb_row').find('.row-bg').css('background-color')).removeClass('light-text').removeClass('dark-text').addClass($textColor);
								$('#header-outer').attr('data-current-row-bg-color', $that.prev('div.wpb_row').find('.row-bg').css('background-color'));

								$('body.material #header-outer .bg-color-stripe').css('background-color', $that.prev('div.wpb_row').find('.row-bg').css('background-color'));
							} else {
								$('#header-outer').css('background-color',$('#header-outer').attr('data-user-set-bg')).removeClass('light-text').removeClass('dark-text');
								$('#header-outer').attr('data-current-row-bg-color',$('#header-outer').attr('data-user-set-bg'));

								$('body.material #header-outer .bg-color-stripe').css('background-color', '');
							}

						} 

				
					},
					offset: headerOffset

				}); 


			});
		}
	}



/****************sticky page submenu******************/
	if($('.page-submenu[data-sticky="true"]').length > 0 && $('#nectar_fullscreen_rows').length == 0) {

		(function() {
		  'use strict'

		  var $ = window.jQuery
		  var Waypoint = window.Waypoint
		  var $offsetHeight = 0; 
		  var shrinkNum = 6;	
		  var headerPadding = parseInt($('#header-outer').attr('data-padding'));

		  if($('#header-outer[data-shrink-num]').length > 0 ) shrinkNum = $('#header-outer').attr('data-shrink-num');
		  var headerPadding2 = headerPadding - headerPadding/1.8;
		  var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-outer').outerHeight();
		  var $headerHeight = ($('#header-outer[data-header-resize="1"]').length > 0 && $('body.mobile').length == 0) ? $headerNavSpace - (parseInt(shrinkNum) + headerPadding2*2) : $headerNavSpace;
			
			if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
				
					var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
					var $secondaryHeader = $('#header-secondary-outer');
					
					$headerHeight = $('#header-outer').height() - (parseInt($headerSpan9.position().top) - parseInt($('#header-outer #logo').css('margin-top')) ) -  parseInt(nectarDOMInfo.secondaryHeaderHeight);
			}
			
		  if( $('.page-template-template-no-header-footer').length > 0 || $('.page-template-template-no-header').length > 0 ) { $headerNavSpace = 0; $headerHeight = 0; }

		  if($('#header-secondary-outer').length > 0 && $('body.mobile').length == 0 && $('body.material').length == 0) $headerHeight += $('#header-secondary-outer').height();
		  

		  $(window).on('smartresize',function(){

		    $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-outer').outerHeight();
		  	$headerHeight = ($('#header-outer[data-header-resize="1"]').length > 0 && $('.small-nav').length == 0 && $('body.mobile').length == 0) ? $headerNavSpace - (parseInt(shrinkNum) + headerPadding2*2) : $headerNavSpace;
		  	
				if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
					
						var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
						var $secondaryHeader = $('#header-secondary-outer');
						
						$headerHeight = $('#header-outer').height() - (parseInt($headerSpan9.position().top) - parseInt($('#header-outer #logo').css('margin-top')) ) - parseInt(nectarDOMInfo.secondaryHeaderHeight);
				}
				
				if($('#header-secondary-outer').length > 0  && $('body.mobile').length == 0 && $('body.material').length == 0) $headerHeight += $('#header-secondary-outer').height();

		  	$offsetHeight = 0; 
		
		  	  if($('#wpadminbar').length > 0 && $('#wpadminbar').css('position') == 'fixed') $offsetHeight += $('#wpadminbar').height();
 			  if($('body[data-hhun="0"] #header-outer').length > 0 && !($('body.mobile').length > 0 && $('#header-outer[data-mobile-fixed="false"]').length > 0) ) {
 			  	  $offsetHeight += $headerHeight;
 			  }

 			  if($('.body-border-top').length > 0 && $(window).width() > 1000 && $('body[data-hhun="1"]').length > 0) $offsetHeight += $('.body-border-top').height();
 	
 			
 			 //recalc for resizing (same as stuck/unstuck logic below)
 			 if($('.page-submenu.stuck').length > 0) {

		        	$('.page-submenu.stuck').addClass('no-trans').css('top',$offsetHeight).css('transform','translateY(0)').addClass('stuck');
		        	var $that = this;
		        	setTimeout(function(){ $('.page-submenu.stuck').removeClass('no-trans'); },50);
		        	$('.page-submenu.stuck').parents('.wpb_row').css('z-index',10000);

		        	//boxed
		        	if($('#boxed').length > 0) { 
		        		var $negMargin = ($(window).width() > 1000) ? $('.container-wrap').width()*0.04 :39;
		        		$('.page-submenu.stuck').css({'margin-left':'-'+$negMargin+'px', 'width' : $('.container-wrap').width()});
		        	}

		        }
		        else { 
		        	$('.page-submenu.stuck').css('top','0').removeClass('stuck');
		       	   $('.page-submenu.stuck').parents('.wpb_row').css('z-index','auto');

		       	    if($('#boxed').length > 0) $('.page-submenu.stuck').css({'margin-left':'0px', 'width' : '100%'});
		       	}

		  });

		  /* http://imakewebthings.com/waypoints/shortcuts/sticky-elements */
		  function Sticky(options) {
		    this.options = $.extend({}, Waypoint.defaults, Sticky.defaults, options)
		    this.element = this.options.element
		    this.$element = $(this.element)
		    this.createWrapper()
		    this.createWaypoint()
		  }

		  /* Private */
		  Sticky.prototype.createWaypoint = function() {
		    var originalHandler = this.options.handler

		    $offsetHeight = 0; 
		    if($('#wpadminbar').length > 0 && $('#wpadminbar').css('position') == 'fixed') $offsetHeight += $('#wpadminbar').height();
 			if($('body[data-hhun="0"] #header-outer').length > 0 && !($('body.mobile').length > 0 && $('#header-outer[data-mobile-fixed="false"]').length > 0) ) { 
 				$offsetHeight += $headerHeight;
 			}
 			 if($('.body-border-top').length > 0 && $(window).width() > 1000 && $('body[data-hhun="1"]').length > 0) $offsetHeight += $('.body-border-top').height();
 		
		    this.waypoint = new Waypoint($.extend({}, this.options, {
		      element: this.wrapper,
		      handler: $.proxy(function(direction) {
		        var shouldBeStuck = this.options.direction.indexOf(direction) > -1
		        var wrapperHeight = shouldBeStuck ? this.$element.outerHeight(true) : ''

		        this.$wrapper.height(wrapperHeight)
		        if(shouldBeStuck) {
		        	this.$element.addClass('no-trans').css('top',$offsetHeight).css('transform','translateY(0)').addClass('stuck');
		        	var $that = this;
		        	setTimeout(function(){ $that.$element.removeClass('no-trans'); },50);
		        	this.$element.parents('.wpb_row').css('z-index',10000);

		        	//boxed
		        	if($('#boxed').length > 0) { 
		        		var $negMargin = ($(window).width() > 1000) ? $('.container-wrap').width()*0.04 :39;
		        		this.$element.css({'margin-left':'-'+$negMargin+'px', 'width' : $('.container-wrap').width()});
		        	}

		        }
		        else { 
		        	this.$element.css('top','0').removeClass('stuck');

		       	    if($('#boxed').length > 0) this.$element.css({'margin-left':'0px', 'width' : '100%'});
		       	}

		        if (originalHandler) {
		          originalHandler.call(this, direction)
		        }
		      }, this),
		      offset: $offsetHeight
		    }))

		    var $that = this;

			    setInterval(function(){ 

			    	if($('body[data-hhun="1"] #header-outer.detached:not(.invisible)').length > 0)
		        		$that.waypoint.options.offset = $offsetHeight + $headerHeight;
		        	else 
		        		$that.waypoint.options.offset = $offsetHeight;
			    	Waypoint.refreshAll();
			
			    },100); 
			

		  }

		  /* Private */
		  Sticky.prototype.createWrapper = function() {
		    if (this.options.wrapper) {
		      this.$element.wrap(this.options.wrapper)
		    }
		    this.$wrapper = this.$element.parent()
		    this.wrapper = this.$wrapper[0]
		  }

		  /* Public */
		  Sticky.prototype.destroy = function() {
		    if (this.$element.parent()[0] === this.wrapper) {
		      this.waypoint.destroy()
		      this.$element.removeClass(this.options.stuckClass)
		      if (this.options.wrapper) {
		        this.$element.unwrap()
		      }
		    }
		  }

		  Sticky.defaults = {
		    wrapper: '<div class="sticky-wrapper" />',
		    stuckClass: 'stuck',
		    direction: 'down right'
		  }

		  Waypoint.Sticky = Sticky
		}())
		;

		//remove outside of column setups 
		if($('.page-submenu').parents('.span_12').find('> .wpb_column').length > 1){
			var pageMenu = $('.page-submenu').clone();
			var pageMenuParentRow = $('.page-submenu').parents('.wpb_row');
			$('.page-submenu').remove();
			pageMenuParentRow.before(pageMenu);
		}

		var sticky = new Waypoint.Sticky({
		  element: $('.page-submenu[data-sticky="true"]')[0]
		});


	}

	if($('#nectar_fullscreen_rows').length == 0)
		$('.page-submenu').parents('.wpb_row').css('z-index',10000);

	$('.page-submenu .mobile-menu-link').on('click',function(){
		$(this).parents('.page-submenu').find('ul').stop(true).slideToggle(350);
		return false;
	});

	$('.page-submenu ul li a').on('click',function(){
		if($('body.mobile').length > 0) $(this).parents('.page-submenu').find('ul').stop(true).slideToggle(350);
	});


	//responsive nav
	$('body').on('click','#toggle-nav',function(){
		if(window.innerWidth > 1000) {
			window.scrollTo(0,0);	
		}

		$(this).find('.lines-button').toggleClass('close');
		
		
		if( $('body').hasClass('classic_mobile_menu_open') ) {
			
			$('#mobile-menu').hide();
			$('body').removeClass('classic_mobile_menu_open');
			
		} else {
			
			if($('#header-outer[data-transparent-header="true"][data-permanent-transparent="1"][data-mobile-fixed="false"].transparent').length > 0) {
				$('#mobile-menu').css({
					'top': parseInt($('#header-outer').outerHeight() + nectarDOMInfo.adminBarHeight) + 'px' ,
					'position': 'absolute',
					'width': '100%',
					'left': '0' 
				});
			}
			
			$('#mobile-menu').show();
			$('body').addClass('classic_mobile_menu_open');
			
		}
		
		
		return false;
	});
	
	
	//add wpml to mobile menu
	if($('header#top nav > ul > li.menu-item-language').length > 0 && $('#header-secondary-outer ul > li.menu-item-language').length == 0){
		var $langSelector = $('header#top nav > ul > li.menu-item-language').clone();
		$langSelector.insertBefore('#mobile-menu ul #mobile-search');
	}
	
	////append dropdown indicators / give classes
	$('#mobile-menu .container ul li').each(function(){
		if($(this).find('> ul').length > 0) {
			 $(this).addClass('has-ul');
			 $(this).find('> a').append('<span class="sf-sub-indicator"><i class="icon-angle-down"></i></span>');
		}
	});
	
	////events
	$('#mobile-menu .container ul li:has(">ul") > a .sf-sub-indicator').on('click',function(e){
		$(this).parent().parent().toggleClass('open');
		$(this).parent().parent().find('> ul').stop(true,true).slideToggle();
		return false;
	});
	


/*-------------------------------------------------------------------------*/
/*	5.	Page Specific
/*-------------------------------------------------------------------------*/	
	
	function vcFullHeightRow() {
        var $element = $(".vc_row-o-full-height:first");
        if ($element.length) {
            var $window, windowHeight, offsetTop, fullHeight;
            $window = $(window), 
            windowHeight = $window.height();

            $(".vc_row-o-full-height").each(function(){
            	offsetTop = $(this).offset().top;

            	var $realRowIndex = ($(this).parent().hasClass('vc_ie-flexbox-fixer')) ? $(this).parent().index() : $(this).index();

            	if(offsetTop < windowHeight && $(this).hasClass('top-level') && !nectarDOMInfo.usingFrontEndEditor) {
            		fullHeight = 100 - offsetTop / (windowHeight / 100);
          	    	$(this).css("min-height", fullHeight + "vh");
          	    	$(this).find('> .col.span_12').css("min-height", fullHeight + "vh");
            	} else {
            		$(this).css("min-height", windowHeight);
            		$(this).find('> .col.span_12').css("min-height", windowHeight);
            	}
          	   
            });

            
        }

    }
    function fixIeFlexbox() {
        var ua = window.navigator.userAgent,
            msie = ua.indexOf("MSIE ");
        (msie > 0 || navigator.userAgent.match(/Trident.*rv\:11\./)) && $(".vc_row-o-full-height").each(function() {
            "flex" === $(this).find('> .span_12').css("display") && $(this).wrap('<div class="vc_ie-flexbox-fixer"></div>')
        })
    }
    fixIeFlexbox();

    vcFullHeightRow();
		
		if(!nectarDOMInfo.usingFrontEndEditor) {
			nectarLiquidBGs();
		}

	//recent work
	function piVertCenter() {
		$('.portfolio-items  > .col').each(function(){
				
			//style 4
			$(this).find('.style-4 .work-info .bottom-meta:not(.shown)').stop().animate({
				'bottom' : '-'+$(this).find('.work-info .bottom-meta').outerHeight()-2+'px'
			},420,'easeOutCubic');

			
		});	 
	}
	
	
	//ie8 width fix
	function ie8Width(){
		if( $(window).width() >= 1300 ) {
			$('.container').css('max-width','1100px');
		} else {
			$('.container').css('max-width','880px');
		}
	}
	
	if( $(window).width() >= 1300 && $('html').hasClass('no-video')) { $('.container').css('max-width','1100px'); $(window).resize(ie8Width); };
	


	function smartResizeInit() {
		
		 //carousel height calcs
		 carouselHeightCalcs();
		 clientsCarouselHeightRecalc();

		 //portfolio comment order
		 portfolioCommentOrder();
		 
		 //testimonial slider height
		 testimonialHeightResize(); //animated
		 testimonialSliderHeight(); //non-animated
		 
		 //full width content columns sizing
		 fullWidthContentColumns();
		 
		 //parallax BG Calculations
		 parallaxRowsBGCals();
		 
		
		 vcFullHeightRow();
		 headerSpace();

		 centeredLogoMargins();

		 slideOutWidgetOverflowState();
		 recentPostHeight();

		 morphingOutlines();

		 flipBoxHeights();

		 showOnLeftSubMenu();

		 //minimal alt effect
		if($('.tabbed[data-style="minimal_alt"]').length > 0) {
			 magicLineCalc($('.tabbed[data-style="minimal_alt"] > ul > li > a.active-tab'));
		}
		
	}

  $(window).off('smartresize.srInit'); 
	$(window).on('smartresize.srInit', smartResizeInit); 
	
	
	var $usingNectarCarouselFlk = ($('.nectar-carousel.nectar-flickity:not(.masonry)').length > 0) ? true : false;
	
	function resizeInit() {
		 portfolioDeviceCheck();

		 //fullwidth page section calcs
		 fullWidthSections(); 
		 fullwidthImgOnlySizing();
		 fullWidthContentColumns();
		 if(nectarDOMInfo.usingMobileBrowser) {
		  fullWidthRowPaddingAdjustCalc();
		}
		 
		 //iframe video emebeds
		 responsiveVideoIframes();
		 
		 //parallax BG Calculations
		 if(!nectarDOMInfo.usingMobileBrowser){
		   parallaxRowsBGCals();
		 }
		 
		 testimonialSliderHeightMinimalMult();
		 if($usingNectarCarouselFlk) {
			 setNectarCarouselFlkEH();
		 }
		 
		 if($('.nectar-social.full-width').length > 0) {
		 	nectarLoveFWCenter();
		 }

		 if($('body').hasClass('ascend') ){ 
			searchFieldCenter();
		 }

		 if($('body').hasClass('single-post')) { centerPostNextButtonImg(); }

		 cascadingImageBGSizing();

		 responsiveTooltips();

		 //vc mobile columns
		 if($('[class*="vc_col-xs-"], [class*="vc_col-md-"], [class*="vc_col-lg-"]').length > 0) { vcMobileColumns(); }

		 if($('body[data-footer-reveal="1"]').length > 0 || $('body.material[data-slide-out-widget-area-style="slide-out-from-right"]').length > 0) { footerRevealCalcs(); }

		 if($('#page-header-bg').length > 0) { pageHeader(); }

		 if($('.nectar-video-bg').length > 0) {
		 	resizeVideoToCover();
		 }
	}

	$(window).off('resize.srInit'); 
	$(window).on('resize.srInit', resizeInit); 
	
	
	$(window).load(function(){
		
		if($(window).scrollTop() == 0 ) { headerSpace(); }
		setTimeout(portfolioSidebarFollow,200);
		
		$('video').css('visibility','visible');
		
		if($('body[data-animated-anchors="true"]').length > 0) { 
				if($('.nectar-box-roll').length == 0 && $('#nectar_fullscreen_rows').length == 0) { 
					
					//inside tabs
					if( typeof $_GET['tab'] != 'undefined' ) {
							setTimeout(function(){
								pageLoadHash(); 
							},500);
					} 
					//regular
					else {
						pageLoadHash(); 
					}
					
				}
				
				if($('#nectar_fullscreen_rows[data-mobile-disable="on"]').length > 0 && $('.nectar-box-roll').length == 0 && nectarDOMInfo.usingMobileBrowser) {
					pageLoadHash();
				}
		}
		
		parallaxRowsBGCals();
		portfolioCommentOrder();
		fullWidthContentColumns();
		resizeVideoToCover();

	});


	$(window).on("orientationchange",function(){
	  setTimeout(clientsCarouselHeightRecalc,200);
	});
	
	//blog next post button
	function postNextButtonEffect(){

		$('.blog_next_prev_buttons').imagesLoaded(function(){

			centerPostNextButtonImg();
			
			$('.blog_next_prev_buttons img').css('opacity','1');

		});
	}

	function centerPostNextButtonImg(){
		
		if($('.blog_next_prev_buttons').length == 0) return false;

		if( $('.blog_next_prev_buttons img').height() >= $('.blog_next_prev_buttons').height() + 50 ) {
			var $height = 'auto';
			var $width = $('.blog_next_prev_buttons').width();
		} else {

			if( $('.blog_next_prev_buttons').width() < $('.blog_next_prev_buttons img').width()) {
				var $height = $('.blog_next_prev_buttons').height() + 49;
				var $width = 'auto';
			} else {
				var $height = 'auto';
				var $width = '100%';
			}
			
		}

		$('.blog_next_prev_buttons img').css({ 'height' : $height, 'width': $width });

		$('.blog_next_prev_buttons img').css({
			'top' : ($('.blog_next_prev_buttons').height()/2) - ($('.blog_next_prev_buttons img').height()/2) + 'px',
			'left' : ($('.blog_next_prev_buttons').width()/2) - ($('.blog_next_prev_buttons img').width()/2) + 'px'
		});

		$('.blog_next_prev_buttons .inner').each(function(){
			$(this).css({'top': $(this).parent().height()/2 - ($(this).height()/2), 'opacity':'1' });
		})
	}
	
	postNextButtonEffect();


	function recentPostHeight() {
		$('.blog-recent[data-style="title_only"]').each(function(){
			if($(this).find('> .col').length > 1) return false;
			if($(this).parent().parent().parent().hasClass('vc_col-sm-3') || 
				$(this).parent().parent().parent().hasClass('vc_col-sm-4') || 
				$(this).parent().parent().parent().hasClass('vc_col-sm-6') || 
				$(this).parent().parent().parent().hasClass('vc_col-sm-8') || 
				$(this).parent().parent().parent().hasClass('vc_col-sm-9')) {
				
				if($('body.mobile').length == 0 && $(this).next('div').length == 0) {
					var tallestColumn = 0;
					
					$(this).find('> .col').css('padding', '50px 20px');

					$(this).parents('.span_12').find(' > .wpb_column').each(function(){
						(Math.floor($(this).height()) > tallestColumn) ? tallestColumn = Math.floor($(this).height()) : tallestColumn = tallestColumn;
					});	
			
					if(Math.floor($(this).find('> .col').outerHeight(true)) < Math.floor($(this).parents('.wpb_row').height()) - 1) {
						$(this).find('> .col').css('padding-top',(tallestColumn-$(this).find('> .col').height())/2 + 'px');
						$(this).find('> .col').css('padding-bottom',(tallestColumn-$(this).find('> .col').height())/2 + 'px');
					}
					
				}
				 else {
				 		$(this).find('> .col').css('padding', '50px 20px');
				}
			}
		});
	}
	recentPostHeight();


	//recent post slider
	function recentPostsFlickityInit() {

		//classic enhanced specific 
    $('.blog-recent[data-style="classic_enhanced_alt"] .inner-wrap').each(function(){
        	$(this).find('.post-featured-img').each(function(){
				var $src = $(this).find('img').attr('src');
				$(this).css('background-image','url('+$src+')');
			});
        });
		$('.blog-recent[data-style="classic_enhanced"]').each(function(){
			if($(this).find('.inner-wrap.has-post-thumbnail').length == 0) {
				$(this).addClass('no-thumbs');
			}
		});

		if($('.nectar-recent-posts-slider-inner').length > 0) {
		
			var $rpFGroupCells = ($('.nectar-recent-posts-slider_multiple_visible').length > 0) ? '90%' : false;
			
			var $rpF = $('.nectar-recent-posts-slider-inner').flickity({
				  contain: true,
					groupCells: $rpFGroupCells,
				  draggable: true,
				  lazyLoad: false,
				  imagesLoaded: true,
				  percentPosition: true,
				  prevNextButtons: false,
				  pageDots: true,
				  resize: true,
				  setGallerySize: true,
				  wrapAround: true,
				  accessibility: false
			});
			
			setTimeout(function(){
				$('.nectar-recent-posts-slider-inner').addClass('loaded');
			},1150);
			var flkty = $rpF.data('flickity');
			
			$rpF.on( 'dragStart.flickity', function() {
				$('.flickity-viewport').addClass('is-moving');
			});
			
			$rpF.on( 'dragEnd.flickity', function() {
				$('.flickity-viewport').removeClass('is-moving');
			});
			
			var $dragTimeout;
			
			$rpF.on( 'select.flickity', function() {

				  $('.flickity-viewport').addClass('no-hover');
				  clearTimeout( $dragTimeout);
				   $dragTimeout = setTimeout(function(){ $('.flickity-viewport').removeClass('no-hover'); },400);
			 
			});
			
			
	  
			recentPostSliderHeight();
			$(window).resize(recentPostSliderHeight);

			if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/) && ! nectarDOMInfo.usingFrontEndEditor) {
				$(window).resize(recentPostSliderParallaxMargins);
			}

			function recentPostSliderHeight(){

				$('.nectar-recent-posts-slider').each(function(){
						
							var $heightCalc;
							var $minHeight = 250;
							var $windowWidth = $(window).width();
							var $definedHeight = parseInt($(this).attr('data-height'));

							var dif = ($('body[data-ext-responsive="true"]').length > 0) ? $(window).width() / 1400 : $(window).width() / 1100;

							if( window.innerWidth > 1000 && $('#boxed').length == 0) {

								if($(this).parents('.full-width-content').length == 0) {
									if($('body[data-ext-responsive="true"]').length > 0 && window.innerWidth >= 1400){
										$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',Math.ceil($definedHeight));
									} else if($('body[data-ext-responsive="true"]').length == 0 && window.innerWidth >= 1100) {
										$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',Math.ceil($definedHeight));
									} else {
										$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',Math.ceil($definedHeight*dif));
									}
									
								} else {
									$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',Math.ceil($definedHeight*dif));
								}
								
							} else {
								
								//column sliders
								var $parentCol = ($(this).parents('.wpb_column').length > 0) ? $(this).parents('.wpb_column') : $(this).parents('.col') ;
								if($parentCol.length == 0) $parentCol = $('.main-content');
									
								if(!$parentCol.hasClass('vc_span12') && !$parentCol.hasClass('main-content') && !$parentCol.hasClass('span_12') && !$parentCol.hasClass('vc_col-sm-12') ) {
								
									var $parentColWidth = sliderColumnDesktopWidth($parentCol);
									var $aspectRatio = $definedHeight/$parentColWidth;

									
									//min height
									if( $aspectRatio*$parentCol.width() <= $minHeight ){
										$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',$minHeight);
									} else {
										$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',$aspectRatio*$parentCol.width());
									}
								  
								} 
								
								//regular
								else {
									
									//min height
									if( $definedHeight*dif <= $minHeight ){
										$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',$minHeight);
									} else {
										$(this).find('.nectar-recent-post-slide, .flickity-viewport').css('height',Math.ceil($definedHeight*dif));
									}
									
								}
								
							}
							
		
						
					});
			
			}

			////helper function
			function sliderColumnDesktopWidth(parentCol) {
				
				var $parentColWidth = 1100;
				var $columnNumberParsed = $(parentCol).attr('class').match(/\d+/);
				
				if($columnNumberParsed == '2') { $parentColWidth = 170 }
				else if($columnNumberParsed == '3') { $parentColWidth = 260 } 
				else if($columnNumberParsed == '4') { $parentColWidth = 340 } 
				else if($columnNumberParsed == '6') { $parentColWidth = 530 } 
				else if($columnNumberParsed == '8') { $parentColWidth = 700 } 
				else if($columnNumberParsed == '9') { $parentColWidth = 805 }
				else if($columnNumberParsed == '10') { $parentColWidth = 916.3 }
				else if($columnNumberParsed == '12') { $parentColWidth = 1100 }
			
				return $parentColWidth;
			}
		
		}//if using rp slider
		
		
		//multiple featured controls
		
		function multipleLargeFeaturedInit() {
			 $('.nectar-recent-posts-single_featured.multiple_featured').each(function(sliderIndex){
					 if($(this).find('> .normal-container').length > 0) {
					 	$(this).find('> .normal-container').remove();
					}
				 	$(this).append('<div class="normal-container container"> <ul class="controls" data-color="' + $(this).attr('data-button-color') + '" data-num="'+ $(this).find('.nectar-recent-post-slide').length +'"></ul> </div>');
					var $that = $(this);
					
					//store instance
					$nectarCustomSliderRotate[sliderIndex] = { autorotate: '' };
					
					var tallestFeaturedSlide = 0;
					$(this).find('.nectar-recent-post-slide').each(function(i){
						
						if($(this).find('.recent-post-container').height() > tallestFeaturedSlide) {
							$(this).siblings().removeClass('tallest');
							$(this).addClass('tallest');
							tallestFeaturedSlide = $(this).find('.recent-post-container').height();
						} 
						
						var $activeClass = (i == 0 && $(this).parents('.nectar-recent-posts-single_featured.multiple_featured[data-autorotate="none"]').length > 0) ? 'class="active"': '';
						 $that.find('.controls').append('<li '+$activeClass+'><span class="title">'+ $(this).find('h2').text() +'</span></li>');
					});
					
					$(this).addClass('js-loaded');
					
					var $slideClickTimeout;
					
					//click Functionality
					$(this).find('.controls li').on('click',function(e){
						
							if($(this).hasClass('active')) 
								return;
								
							if(e.originalEvent !== undefined)
								$(this).parent().find('.active').addClass('trans-out');
							
							var $index = $(this).index();
							var $oldIndex = $(this).parent().find('.active').index();
							var $that = $(this);
							
							clearTimeout($slideClickTimeout);
							
							$(this).siblings().removeClass('active');
							$(this).addClass('active');
							$slideClickTimeout = setTimeout( function() { 
								$that.parents('.multiple_featured').find('.nectar-recent-post-slide:not(:eq('+$index+'))').css('opacity','0').removeClass('active');
								$that.parent().find('.trans-out').removeClass('trans-out');
							}, 300);
							$that.parents('.multiple_featured').find('.nectar-recent-post-slide:not(:eq('+$index+'))').css('z-index','10');
							$that.parents('.multiple_featured').find('.nectar-recent-post-slide:eq('+$oldIndex+')').css('z-index','15');
							
							$(this).parents('.multiple_featured').find('.nectar-recent-post-slide').eq($index).css({'opacity':'1', 'z-index':'20'}).addClass('active');
							
							if($(this).parents('.multiple_featured').attr('data-autorotate') != 'none') {
								nectarCustomSliderResetRotate($that.parents('.nectar-recent-posts-single_featured.multiple_featured'),sliderIndex);
							}
					});
					
					
				//autorotate
				var $that = $(this);
				 if($(this).attr('data-autorotate').length > 0 && $(this).attr('data-autorotate') != 'none' && $('body.vc_editor').length == 0 ) {
					 
					 setTimeout(function(){
							 var slide_interval = (parseInt($that.attr('data-autorotate')) < 100) ? 4000 : parseInt($that.attr('data-autorotate'));
							 $nectarCustomSliderRotate[sliderIndex].autorotate = setInterval(function(){ nectarCustomSliderRotate($that) },slide_interval);
							 
							 //set first active class
							 $that.find('.controls > li:first-child').addClass('active');

					 },30);
					 
				 }
				 
			 });
			 
			 splitLineText();
			 $(window).resize(splitLineText);
			 

		}
		
		multipleLargeFeaturedInit();
	
	}
	
	recentPostsFlickityInit();

	if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/)) {
		if($('.nectar-recent-posts-slider').length > 0 && ! nectarDOMInfo.usingFrontEndEditor) {
			window.requestAnimationFrame(recentPostSliderParallax);
		}
	}
	
	function recentPostSliderParallax(){

		$('.nectar-recent-posts-slider').each(function(){
			var $offset = parseInt($(this).find('.flickity-slider').position().left);
			var $slideLength = $(this).find('.nectar-recent-post-slide').length;
			var $lastChildIndex = $(this).find('.nectar-recent-post-slide:last-child').index();
			var $slideWidth = $(this).find('.nectar-recent-post-slide').width();
			//wrapped fix

			////first going to first
			if($offset >= -3) {
				$(this).find('.nectar-recent-post-slide:last-child .nectar-recent-post-bg').css('margin-left',parseInt(Math.ceil($slideWidth/3.5))+'px');
			} else {
				$(this).find('.nectar-recent-post-slide:last-child .nectar-recent-post-bg').css('margin-left','-'+parseInt(Math.ceil($slideWidth/3.5*$lastChildIndex))+'px');
			}
			////last going to first
			if(Math.abs($offset) >= ($slideLength-1) * $slideWidth) {
				$(this).find('.nectar-recent-post-slide:first-child .nectar-recent-post-bg').css('margin-left','-'+parseInt(Math.ceil(($slideWidth/3.5)*$slideLength))+'px');
			} else {
				$(this).find('.nectar-recent-post-slide:first-child .nectar-recent-post-bg').css('margin-left','0px');
			}

			$(this).find('.nectar-recent-post-bg').css('transform','translateX('+Math.ceil($(this).find('.flickity-slider').position().left/-3.5)+'px)');
			
			
		});
		requestAnimationFrame(recentPostSliderParallax);
	}

	function recentPostSliderParallaxMargins(){

		$('.nectar-recent-posts-slider').each(function(){		
			var $slideWidth = $(this).find('.nectar-recent-post-slide').width();
			$(this).find('.nectar-recent-post-slide').each(function(i){
				$(this).find('.nectar-recent-post-bg').css('margin-left','-'+  parseInt(Math.ceil($slideWidth/3.5)*i)+'px');
			});
		
		});
	}

	if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|BlackBerry|Opera Mini)/) && ! nectarDOMInfo.usingFrontEndEditor) {
		recentPostSliderParallaxMargins();
	}

	//portfolio item hover effect
	
	////desktop event 
	function portfolioHoverEffects() { 

		if(!$('body').hasClass('mobile') && !navigator.userAgent.match(/(iPad|IEMobile)/)) {
			
			//style 1 & 2
			$('.portfolio-items:not([data-ps="7"]) .col .work-item:not(.style-3-alt):not(.style-3):not([data-custom-content="on"])').on('mouseenter',function(){
				$(this).find('.work-info .vert-center').css({'margin-top' : 0});
				$(this).find('.work-info, .work-info .vert-center > *, .work-info > i').css({'opacity' : 1});
				$(this).find('.work-info-bg').css({ 'opacity' : 0.9 });
			});
			$('.portfolio-items:not([data-ps="7"]) .col .work-item:not(.style-3-alt):not(.style-3):not([data-custom-content="on"])').on('mouseleave',function(){	
				$(this).find('.work-info .vert-center').css({ 'margin-top' : -20 });
				$(this).find('.work-info, .work-info .vert-center > *:not(.mfp-figure), .work-info > i').css({ 'opacity' : 0 });
				$(this).find('.work-info-bg').css({ 'opacity' : 0 });
			});
			
			
			//style 3
			$('.portfolio-items .col .work-item.style-3').on('mouseenter',function(){
				$(this).find('.work-info-bg').css({ 'opacity' : 0 });
			});
			$('.portfolio-items .col .work-item.style-3').on('mouseleave',function(){
				$(this).find('.work-info-bg').css({ 'opacity' : 0.45 });
			});
			
			
			//style 4
			$('.portfolio-items .col .work-item.style-4').on('mouseenter',function(){
				$(this).find('img').stop().animate({
					'top' : '-'+$(this).find('.work-info .bottom-meta').outerHeight()/2+'px'
				},250,'easeOutCubic');
				
				$(this).find('.work-info .bottom-meta').addClass('shown').stop().animate({
					'bottom' : '0px'
				},320,'easeOutCubic');

			});
			$('.portfolio-items .col .work-item.style-4').on('mouseleave',function(){
				$(this).find('img').stop().animate({
					'top' : '0px'
				},250,'easeOutCubic');
				
				$(this).find('.work-info .bottom-meta').removeClass('shown').stop().animate({
					'bottom' : '-'+$(this).find('.work-info .bottom-meta').outerHeight()-2+'px'
				},320,'easeOutCubic');
				
			});
		
		} 
		////mobile event
		else {
			portfolioDeviceCheck();
		}

	}

	portfolioHoverEffects();

	function style6Img(){
  
		//change sizer pos
		$('.style-5').each(function(){
			$(this).find('.sizer').insertBefore($(this).find('.parallaxImg'));
		});

		//set parent zindex
		$('.style-5').parents('.wpb_row').css('z-index','100');

		var d = document,
			de = d.documentElement,
			bd = d.getElementsByTagName('body')[0],
			htm = d.getElementsByTagName('html')[0],
			win = window,
			imgs = d.querySelectorAll('.parallaxImg'),
			totalImgs = imgs.length,
			supportsTouch = 'ontouchstart' in win || navigator.msMaxTouchPoints;

		if(totalImgs <= 0){
			return;
		}

		// build HTML
		for(var l=0;l<totalImgs;l++){

			var thisImg = imgs[l],
				layerElems = thisImg.querySelectorAll('.parallaxImg-layer'),
				totalLayerElems = layerElems.length;

			if(totalLayerElems <= 0){
				continue;
			}

			while(thisImg.firstChild) {
				thisImg.removeChild(thisImg.firstChild);
			}
			
			var lastMove = 0;

			//throttle performance for all browser other than chrome
			var eventThrottle = $('html').hasClass('cssreflections') ? 1 : 80;
			if(eventThrottle == 80) $('body').addClass('cssreflections');

			var containerHTML = d.createElement('div'),
				shineHTML = d.createElement('div'),
				shadowHTML = d.createElement('div'),
				layersHTML = d.createElement('div'),
				layers = [];

			thisImg.id = 'parallaxImg__'+l;
			containerHTML.className = 'parallaxImg-container';
			//shineHTML.className = 'parallaxImg-shine';
			shadowHTML.className = 'parallaxImg-shadow';
			layersHTML.className = 'parallaxImg-layers';

			for(var i=0;i<totalLayerElems;i++){
				var layer = d.createElement('div'),
					layerInner = d.createElement('div'),
					imgSrc = layerElems[i].getAttribute('data-img');

				$(layer).html($(layerElems[i]).html());
				layer.className = 'parallaxImg-rendered-layer';
				layer.setAttribute('data-layer',i);

				if(i==0 && $(thisImg).parents('.wpb_gallery').length == 0) { 
					layerInner.className = 'bg-img';
					layerInner.style.backgroundImage = 'url('+imgSrc+')';
					layer.appendChild(layerInner);
				}
				layersHTML.appendChild(layer);

				layers.push(layer);
			}

			containerHTML.appendChild(layersHTML);
			thisImg.appendChild(containerHTML);
			$(thisImg).wrap('<div class="parallaxImg-wrap" />');
			if(!(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1)) { $(thisImg).parent().append(shadowHTML); }

			var w = thisImg.clientWidth || thisImg.offsetWidth || thisImg.scrollWidth;

			if(supportsTouch && $('body.using-mobile-browser').length > 0 ){
				
		    } else {
		    	(function(_thisImg,_layers,_totalLayers,_shine) {
					$(thisImg).parents('.style-5').on('mousemove', function(e){
						
						var parentEl = $(this);
					 	var now = Date.now();
				        if (now > lastMove + eventThrottle) {
				            lastMove = now;
							window.requestAnimationFrame(function(){
								processMovement(e,false,_thisImg,_layers,_totalLayers,_shine,parentEl);		
							});
						}


					});
		            $(thisImg).parents('.style-5').on('mouseenter', function(e){
						processEnter(e,_thisImg,_layers,_totalLayers,_shine);		
					});
					$(thisImg).parents('.style-5').on('mouseleave', function(e){
						processExit(e,_thisImg,_layers,_totalLayers,_shine);		
					});
		        })(thisImg,layers,totalLayerElems,shineHTML);
		    }

		    //set the depths
		    (function(_thisImg,_layers,_totalLayers,_shine) {
			    depths(false,_thisImg,_layers,_totalLayers,_shine);
			     window.addEventListener('resize', function(e){
			    	  depths(false,_thisImg,_layers,_totalLayers,_shine);
			    });
			 })(thisImg,layers,totalLayerElems,shineHTML);
		}

		function processMovement(e, touchEnabled, elem, layers, totalLayers, shine, parentEl){
			
			//stop raf if exit already called
			if(!$(elem.firstChild).hasClass('over')) { processExit(e,elem,layers,totalLayers,shine); return false }

			//set up multipliers

			if($(elem).parents('.col.wide').length > 0 ) {
				var yMult = 0.03;
				var xMult = 0.063;
			} else if( $(elem).parents('.col.regular').length > 0  || $(elem).parents('.wpb_gallery').length > 0) {
				var yMult = 0.045;
				var xMult = 0.045;
			} else if($(elem).parents('.col.tall').length > 0 ) {
				var yMult = 0.05;
				var xMult = 0.015;
			} else if($(elem).parents('.col.wide_tall').length > 0) {
				var yMult = 0.04;
				var xMult = 0.04;
			} else if(parentEl.hasClass('nectar-fancy-box')) {
				var yMult = 0.045;
				var xMult = 0.022;
			} else {
				var yMult = 0.045;
				var xMult = 0.075;
			}
			
			var bdst = $(window).scrollTop(),
				bdsl = bd.scrollLeft,
				pageX = (touchEnabled)? e.touches[0].pageX : e.pageX,
				pageY = (touchEnabled)? e.touches[0].pageY : e.pageY,
				offsets = elem.getBoundingClientRect(),
				w = elem.clientWidth || elem.offsetWidth || elem.scrollWidth, // width
				h = elem.clientHeight || elem.offsetHeight || elem.scrollHeight, // height
				wMultiple = 320/w,
				offsetX = 0.52 - (pageX - offsets.left - bdsl)/w, //cursor position X
				offsetY = 0.52 - (pageY - offsets.top - bdst)/h, //cursor position Y
				dy = (pageY - offsets.top - bdst) - h / 2, //@h/2 = center of container
				dx = (pageX - offsets.left - bdsl) - w / 2, //@w/2 = center of container
				yRotate = (offsetX - dx)*(yMult * wMultiple), //rotation for container Y
				xRotate = (dy - offsetY)*(xMult * wMultiple); //rotation for container X //old

				if($(elem).parents('.wpb_gallery').length > 0) {
					var imgCSS = ' perspective('+ w*3 +'px) rotateX(' + -xRotate*1.9 + 'deg) rotateY(' + -yRotate*1.3 + 'deg)'; //img transform	
				} else {
					if($(elem).parents('.wide_tall').length == 0 && $(elem).parents('.wide').length == 0 && $(elem).parents('.tall').length == 0) {
						var $scaleAmount = (parentEl.hasClass('nectar-fancy-box')) ? '1.06' : '1.03';
						var $offsetAmount = (parentEl.hasClass('nectar-fancy-box')) ? '-2' : '-10';
						
						var imgCSS = ' perspective('+ w*3 +'px) rotateX(' + xRotate + 'deg) rotateY(' + yRotate + 'deg)  translateY('+offsetY*$offsetAmount+'px) translateX('+offsetX*$offsetAmount+'px) scale('+$scaleAmount+')'; //img transform
					} else {
						var imgCSS = ' perspective('+ w*3 +'px) rotateX(' + xRotate + 'deg) rotateY(' + yRotate + 'deg)  translateY('+offsetY*-10+'px) translateX('+offsetX*-10+'px) scale(1.013)'; //img transform	
					}
				}

				
			//container transform


			$(elem).find('.parallaxImg-container').css('transform',imgCSS);

			if(!(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1)) {
				$(elem).parents('.parallaxImg-wrap').find('.parallaxImg-shadow').css('transform',imgCSS);
			}

			
		}

		function processShineMovement(e, touchEnabled, elem, layers, totalLayers, shine){

		}

		function processEnter(e, elem, layers, totalLayers, shine){

			elem.firstChild.className += ' over';
			elem.className += ' over';


				$(elem).addClass('transition');

				if($(elem).parents('.wpb_gallery').length > 0) {
					var $timeout = setTimeout(function(){ $(elem).removeClass('transition'); },450);
				} else {
					var $timeout = setTimeout(function(){ $(elem).removeClass('transition'); },200);
				}

		}

		function processExit(e, elem, layers, totalLayers, shine){

			var w = elem.clientWidth || elem.offsetWidth || elem.scrollWidth;
			var container = elem.firstChild;

			container.className = container.className.replace(' over','');
			elem.className = elem.className.replace(' over','');
			$(container).css('transform', 'perspective('+ w*3 +'px) rotateX(0deg) rotateY(0deg) translateZ(0)');
			$(elem).parents('.parallaxImg-wrap').find('.parallaxImg-shadow').css('transform','perspective('+ w*3 +'px) rotateX(0deg) rotateY(0deg) translateZ(0)');

			$(elem).addClass('transition');
				var $timeout = setTimeout(function(){ $(elem).removeClass('transition'); },200);


		}

		function depths(touchEnabled, elem, layers, totalLayers, shine) {
			
			var w = elem.clientWidth || elem.offsetWidth || elem.scrollWidth;
			var revNum = totalLayers;
			var container = elem.firstChild;
			
			//set z
			for(var ly=0;ly<totalLayers;ly++){
				if(ly == 0) $(layers[ly]).css('transform', 'translateZ(0px)');
				else $(layers[ly]).css('transform','translateZ(' +(w*3)/27*(ly*1.1) + 'px) ');

				revNum--;
			}
			
			totalLayers = totalLayers + 3;

			//set perspective from beginning
			$(container).css('transform','perspective('+ w*3 +'px)');

		}

		function removeDepths(touchEnabled, elem, layers, totalLayers, shine) {
			
			var w = elem.clientWidth || elem.offsetWidth || elem.scrollWidth;
			var revNum = totalLayers;
			
			//set z
			for(var ly=0;ly<totalLayers;ly++){
				
				if(ly == 0) $(layers[ly]).css('transform', 'translateZ(' +(w*3)/45*(ly*1.1) + 'px) scale(1)');
				else $(layers[ly]).css('transform', 'translateZ(' +(w*3)/45*(ly*1.1) + 'px) scale(1)');

				revNum--;
			}
			
			totalLayers = totalLayers + 3;
			
		}
	}

	style6Img();

	function portfolioDeviceCheck(){
		if($('body').hasClass('mobile') || navigator.userAgent.match(/(iPad|IEMobile)/) ){
			
			//if using more details
			if($('.portfolio-items .col .work-item').find('a:not(".pp")').length > 0){
				$('.portfolio-items .col .work-item').find('a.pp').css('display','none');
			} 
			
			//if only using pp
			else {
				$('.portfolio-items .col .work-item').find('a:not(".pp")').css('display','none');
			}
		
		} else {
			$('.portfolio-items .col .work-item').find('a').css('display','inline');
		}
	}
	
	
	//portfolio fullscreen zoom slider

	//remove outside of column setups 
	$('.nectar_fullscreen_zoom_recent_projects').each(function(){
		if($(this).parents('.span_12').find('> .wpb_column').length > 1){
			var $zoomProjects = $(this).clone();
			var $zoomProjectsRow = $(this).parents('.span_12');
			$(this).remove();
			$zoomProjectsRow.prepend($zoomProjects);
		}
	});

	 $.fn.lines = function (opts) {
        var s = $.extend({
            'lineClass' : 'line'
        },opts);
        return this.each(function () {
            var self = this,
                $self = $(self),
                $line,
                $prev;
            $self.find('.' + s.lineClass).contents().unwrap();
            $self.html(function (i, h) {
                return h.replace(/(\b[\w']+\b)/g, '<span class="' + s.lineClass + '">$1</span>');
            });

            $self.find('.line + .line').each(function(i, el){
                $line = $(this),
                $prev = $line.prev('.line');
                if ($line.offset().top === $prev.offset().top) {
                    $prev.append(el.previousSibling, $line.contents());
                    $line.remove();
                }
            });
        });
    };

	function splitLineText() {
		$('.nectar_fullscreen_zoom_recent_projects, .nectar-recent-posts-single_featured.multiple_featured').each(function(){
			
			var $slideClass = ($(this).find('.project-slides').length > 0) ? '.project-slide' : '.nectar-recent-post-slide';
			var $slideInfoClass = ($(this).find('.project-slides').length > 0) ? '.project-info h1' : '.inner-wrap h2 a';
			
			$(this).find($slideClass).each(function(i){
				
				$(this).find($slideInfoClass).each(function(){
					
					var textArr = $(this).text();
		      textArr = textArr.trim();
		      textArr = textArr.split(' ');
		
		      $(this)[0].innerHTML = '';
		      
		      for(var i=0;i<textArr.length;i++) {
		          $(this)[0].innerHTML += '<span>'+ textArr[i] + '</span> ';
		      }
					
				});

				$(this).find($slideInfoClass + ' > span').wrapInner('<span class="inner" />');

			});
			
		});
		
	}

	function portfolioFullScreenSliderCalcs() {

		var $bodyBorderSize = ($('.body-border-top').length > 0 && $(window).width() > 1000) ? $('.body-border-top').height(): 0;

		$('.nectar_fullscreen_zoom_recent_projects').each(function(){
			
			//frontend editor fix
			if(nectarDOMInfo.usingFrontEndEditor) {
				if($(this).parents('.wpb_row').parent().index() > 1) { $(this).parents('.first-section').removeClass('first-section'); }
			}
			
			if($(this).parents('.first-section').length > 0) {
				$(this).css('height',$(window).height() - $(this).offset().top - $bodyBorderSize);
			} else {
				$(this).css('height',$(window).height());
			}
		});

	}

				
	function nectarCustomSliderRotate(slider){
		
		if($('body.vc_editor').length > 0) { return; }
		
		var $controlSelector = (slider.find('.project-slides').length > 0) ? '.dot-nav > span' : '.controls > li';
		var $controlSelectorInd = (slider.find('.project-slides').length > 0) ? 'span' : ' li';
		
		var $slideLength = slider.find($controlSelector).length;
		var $currentSlide = slider.find($controlSelector+'.active').index();
		if( $currentSlide+1 == $slideLength) {
			slider.find($controlSelector+':first-child').click();
		} else {
			slider.find($controlSelector+'.active').next($controlSelectorInd).click();
		}
	}
	
	function nectarCustomSliderResetRotate(slider,index){
		clearInterval($nectarCustomSliderRotate[index].autorotate);

		//reinit autorotate
		if(slider.attr('data-autorotate').length > 0) {
			var slide_interval = (parseInt(slider.attr('data-autorotate')) < 100) ? 4000 : parseInt(slider.attr('data-autorotate'));
			$nectarCustomSliderRotate[index].autorotate = setInterval(function(){ nectarCustomSliderRotate(slider) },slide_interval);
		}
	}


	if($('.nectar_fullscreen_zoom_recent_projects').length > 0){
		portfolioFullScreenSliderCalcs();
		splitLineText();
		$(window).resize(splitLineText);
		$(window).resize(portfolioFullScreenSliderCalcs);
	}
	
	
	function fsProjectSliderInit() {
		
		$('.nectar_fullscreen_zoom_recent_projects').each(function(recentProjectSliderIndex){

			
			//store instance
			$nectarCustomSliderRotate[recentProjectSliderIndex] = { autorotate: '' };
			
			var $projLength = $(this).find('.project-slide').length;
			
			//autorotate
			if($(this).attr('data-autorotate').length > 0) {
				var slide_interval = (parseInt($(this).attr('data-autorotate')) < 100) ? 4000 : parseInt($(this).attr('data-autorotate'));
				var $that = $(this);
				$nectarCustomSliderRotate[recentProjectSliderIndex].autorotate = setInterval(function(){ nectarCustomSliderRotate($that) },slide_interval);
			}

			//next/prev
			$(this).find('.zoom-slider-controls .next').on('click',function(e){

				//thres
				
				var $that = $(this);
				if(!$that.parent().hasClass('timeout')) {
					setTimeout(function(){
						$that.parent().removeClass('timeout');
					},1150);
				}

				if($(this).parent().hasClass('timeout')) return false;
				$(this).parent().addClass('timeout');
				//switch logic

				nectarCustomSliderResetRotate($that.parents('.nectar_fullscreen_zoom_recent_projects'), recentProjectSliderIndex);
				
				var $current = $(this).parents('.nectar_fullscreen_zoom_recent_projects').find('.project-slide.current');
				var $sliderInstance = $(this).parents('.nectar_fullscreen_zoom_recent_projects');

				$sliderInstance.find('.project-slide').removeClass('next').removeClass('prev');
				$sliderInstance.find('.project-slide').each(function(i){

					if(i < $current.index()+1 && $current.index()+1 < $projLength)
						$(this).addClass('prev');
					else
						$(this).addClass('next');
				});

				if($current.index()+1 == $projLength) {
					$sliderInstance.find('.project-slide:first-child').addClass('no-trans');
				}

				setTimeout(function(){

					if($current.index()+1 == $projLength) {
						$sliderInstance.find('.project-slide:first-child').removeClass('no-trans').removeClass('next').removeClass('prev').addClass('current');
						$sliderInstance.find('.project-slide:last-child').removeClass('next').removeClass('current').addClass('prev');
					} else {
						$current.next('.project-slide').removeClass('next').removeClass('prev').addClass('current');
						$current.removeClass('current').addClass('prev');
					}

					//update dot nav
					if($sliderInstance.find('.dot-nav').length > 0) {
						$sliderInstance.find('.dot-nav span.active').removeClass('active');
						$sliderInstance.find('.dot-nav span:nth-child('+ ($sliderInstance.find('.project-slide.current').index() + 1) +')').addClass('active');
					}

				},30);

				return false;
				
			});

			$(this).find('.zoom-slider-controls .prev').on('click',function(e){

				//thres
				var $that = $(this);
				if(!$that.parent().hasClass('timeout')) {
					setTimeout(function(){
						$that.parent().removeClass('timeout');
					},1150);
				}

				if($(this).parent().hasClass('timeout')) return false;
				$(this).parent().addClass('timeout');
				
				nectarCustomSliderResetRotate($that.parents('.nectar_fullscreen_zoom_recent_projects'), recentProjectSliderIndex);

				//switch logic
				var $current = $(this).parents('.nectar_fullscreen_zoom_recent_projects').find('.project-slide.current');
				var $sliderInstance = $(this).parents('.nectar_fullscreen_zoom_recent_projects');

				
				$sliderInstance.find('.project-slide').removeClass('next').removeClass('prev');
				$sliderInstance.find('.project-slide').each(function(i){

					if(i < $current.index() || $current.index() == 0)
						$(this).addClass('prev');
					else
						$(this).addClass('next');
				});

				if($current.index() == 0)
					$sliderInstance.find('.project-slide:last-child').addClass('no-trans');

				setTimeout(function(){

					if($current.index() == 0) {
						$sliderInstance.find('.project-slide:last-child').removeClass('no-trans').removeClass('next').removeClass('prev').addClass('current');
						$sliderInstance.find('.project-slide:first-child').removeClass('next').removeClass('prev').removeClass('current').addClass('next');
					} else {
						$current.prev('.project-slide').removeClass('next').removeClass('prev').addClass('current');
						$current.removeClass('current').addClass('next');
					}

					//update dot nav
					if($sliderInstance.find('.dot-nav').length > 0) {
						$sliderInstance.find('.dot-nav span.active').removeClass('active');
						$sliderInstance.find('.dot-nav span:nth-child('+ ($sliderInstance.find('.project-slide.current').index() + 1) +')').addClass('active');
					}

				},30);


				return false;

			});

			//pagination
				$(this).find('> .normal-container > .dot-nav').remove();
				$(this).find('> .normal-container').append('<div class="dot-nav"></div>');
				for(var $i=0;$i < $projLength;$i++) {
					if($i == 0) {
						$(this).find('.dot-nav').append('<span class="dot active"><span></span></span>');
					} else {
						$(this).find('.dot-nav').append('<span class="dot"><span></span></span>');
					}
			
				}
	

			var $dotIndex = 1;

			$('.nectar_fullscreen_zoom_recent_projects .dot-nav > span').on('click',function(e){

				if($(this).hasClass('active')) return;

				//thres
				var $that = $(this);
				if(!$that.parent().hasClass('timeout')) {
					setTimeout(function(){
						$that.parent().removeClass('timeout');
					},1150);
				}

				if($(this).parent().hasClass('timeout')) return;
				$(this).parent().addClass('timeout');

				nectarCustomSliderResetRotate($that.parents('.nectar_fullscreen_zoom_recent_projects'), recentProjectSliderIndex);

				//switch logic
				$(this).parent().find('span.active').removeClass('active');
				$(this).addClass('active');

				$dotIndex = $(this).index() + 1;

				var $current = $(this).parents('.nectar_fullscreen_zoom_recent_projects').find('.project-slide.current');
				var $sliderInstance = $(this).parents('.nectar_fullscreen_zoom_recent_projects');

				var $prevIndex = $current.index() + 1;

				$sliderInstance.find('.project-slide').removeClass('next').removeClass('prev');

				$sliderInstance.find('.project-slide').each(function(i){
					if(i < $dotIndex-1)
						$(this).addClass('prev');
					else
						$(this).addClass('next');
				});

				//going prev
				if($prevIndex > $dotIndex) {
					$sliderInstance.find('.project-slide').eq($dotIndex-1).addClass('no-trans').addClass('prev').removeClass('next');
					setTimeout(function(){
						$sliderInstance.find('.project-slide').eq($dotIndex-1).removeClass('no-trans').removeClass('next').removeClass('prev').addClass('current');
						$current.removeClass('current').addClass('next');
					},30);
				
				} 

				//going forawrd
				else {
					$sliderInstance.find('.project-slide').eq($dotIndex-1).addClass('no-trans').addClass('next').removeClass('prev');
					setTimeout(function(){
						$sliderInstance.find('.project-slide').eq($dotIndex-1).removeClass('no-trans').removeClass('next').removeClass('prev').addClass('current');
						$current.removeClass('current').addClass('prev');
					},30);

				}
				
			});	


		});
		
	}
	fsProjectSliderInit();
 	

	//portfolio accent color
	function portfolioAccentColor() {

		var portfolioSocialColorCss = '';

		$('.portfolio-items .col').each(function(){
			if ($(this).has('[data-project-color]')) { 
				$(this).find('.work-info-bg, .bottom-meta').css('background-color',$(this).attr('data-project-color'));

				//style5
				$(this).find('.parallaxImg-rendered-layer .bg-overlay').css('border-color',$(this).attr('data-project-color'));

				var	$projColor = $(this).attr('data-project-color');
				if($(this).find('.custom-content .nectar-social').length > 0 && $('body[data-button-style="rounded"]') ) portfolioSocialColorCss += 'body[data-button-style="rounded"] .col[data-project-color="'+$projColor+'"] .custom-content .nectar-social > *:hover i { color: '+ $projColor +'!important; } ';

			}
		});
		
		if(portfolioSocialColorCss.length > 1) {

			var head = document.head || document.getElementsByTagName('head')[0];
			var style = document.createElement('style');

				style.type = 'text/css';
			if (style.styleSheet){
			  style.styleSheet.cssText = portfolioSocialColorCss;
			} else {
			  style.appendChild(document.createTextNode(portfolioSocialColorCss));
			}

			head.appendChild(style);
		}
	}
	portfolioAccentColor();
	
	//portfolio sort
	$('body').on('mouseenter','.portfolio-filters',function(){
		if(!portfolioFiltersOnMobile) {
			$(this).find('> ul').stop(true,true).slideDown(500,'easeOutExpo');
		}
		$(this).find('a#sort-portfolio span').html($(this).find('a#sort-portfolio').attr('data-sortable-label'));
	});

	$('body').on('mouseleave','.portfolio-filters',function(){
		var $activeCat = $(this).find('a.active').html();
		if( typeof $activeCat == 'undefined' || $activeCat.length == 0) $activeCat = $(this).attr('data-sortable-label');
		$(this).find('a#sort-portfolio span').html($activeCat);
		if(!portfolioFiltersOnMobile) {
			$(this).find('> ul').stop(true,true).slideUp(500,'easeOutExpo');
		}
	});
	
	//portfolio selected category
	$('body').on('click','.portfolio-filters ul li a', function(){
		$(this).parents('.portfolio-filters').find('#sort-portfolio span').html($(this).html());
	});
	
	//portfolio prevent jump on parent dropdown click
	$('body').on('click','.portfolio-filters > a#sort-portfolio', function(){
		return false;
	});
	
	//inline portfolio selected category
	$('body').on('click','.portfolio-filters-inline ul li a',function(){

		$(this).parents('ul').find('li a').removeClass('active');
		$(this).addClass('active');
		$(this).parents('.portfolio-filters-inline').find('#current-category').html($(this).html());
		
	});
	
	var portfolioFiltersOnMobile = false;
	
	function portfolioFiltersInit() {
		//mobile sort menu fix
		if($('body').hasClass('mobile') || navigator.userAgent.match(/(iPad|IEMobile)/)){
			portfolioFiltersOnMobile = true;
			$('.portfolio-filters').unbind('mouseenter mouseleave');
			$('.portfolio-filters > a, .portfolio-filters ul li a').on('click',function(e){
				if(e.originalEvent !== undefined) $(this).parents('.portfolio-filters').find('> ul').stop(true,true).slideToggle(600,'easeOutCubic');
			});
		}

		if($('.portfolio-filters-inline[data-alignment="left"]').length > 0 || $('.portfolio-filters-inline[data-alignment="center"]').length > 0) {
			$('.portfolio-filters-inline .container > ul > li:nth-child(1) a').click();
		} else {
			$('.portfolio-filters-inline .container > ul > li:nth-child(2) a').click();
		}
		
		//portfolio more details page menu highlight
		$('body.single-portfolio #header-outer nav > ul > li > a:contains("Portfolio")').parents('li').addClass('current-menu-item');
		
		//blog page highlight
		$('body.single-post #header-outer nav > ul > li > a:contains("Blog")').parents('li').addClass('current-menu-item');
	}

	portfolioFiltersInit();

	
	//blog love center
	function centerLove(){
		$('.post').each(function(){
			
			var $loveWidth = $(this).find('.post-meta .nectar-love').outerWidth();
			var $loveWrapWidth = $(this).find('.post-meta  .nectar-love-wrap').width();
			
			//center
			$(this).find('.post-meta .nectar-love').css('margin-left', $loveWrapWidth/2 - $loveWidth/2 + 'px' );
			$(this).find('.nectar-love-wrap').css('visibility','visible');
		});
	}
	
	$('.nectar-love').on('click',function(){
		centerLove();
	});
	
	centerLove();	
	

	//portfolio single comment order
	function portfolioCommentOrder(){
	
		if($('body').hasClass('mobile') && $('body').hasClass('single-portfolio') && $('#respond').length > 0){
			$('#sidebar').insertBefore('.comments-section');
		}
		 
		else if($('body').hasClass('single-portfolio') && $('#respond').length > 0) {
			$('#sidebar').insertAfter('.post-area');
		}
		
	}

	portfolioCommentOrder();
	 
	
	//portfolio sidebar follow
	var sidebarFollow = $('.single-portfolio #sidebar').attr('data-follow-on-scroll');
	
	function portfolioSidebarFollow(){
		
		if($('body.single-portfolio').length == 0 || $('#sidebar[data-follow-on-scroll]').length == 0) { return; }

		sidebarFollow = $('.single-portfolio #sidebar').attr('data-follow-on-scroll');
	
		if( sidebarFollow == 1 && !$('body').hasClass('mobile') && parseInt($('#sidebar').height()) + 50 <= parseInt($('.post-area').height())) {
			
			 
			 //padding from top of screen
			 var $ssExtraTopSpace = 50;

			 if($('#header-outer[data-remove-fixed="0"]').length > 0 && $('body[data-hhun="1"]').length == 0) { 
				 $ssExtraTopSpace += $('#header-outer').outerHeight();	
				 
				 //resize effect
				 if($('#header-outer[data-shrink-num][data-header-resize="1"]').length > 0 ) {
						var shrinkNum = 6;		
						var headerPadding2 = parseInt($('#header-outer').attr('data-padding')) - parseInt($('#header-outer').attr('data-padding'))/1.8;
						shrinkNum = $('#header-outer').attr('data-shrink-num');
						$ssExtraTopSpace -= shrinkNum;
						$ssExtraTopSpace -= headerPadding2;
				 }
				 
				 //condesne
				 if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
					 
						 var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
						 var $secondaryHeader = $('#header-secondary-outer');
						 
						 $ssExtraTopSpace = 50;
						 $ssExtraTopSpace += $('#header-outer').height() - (parseInt($headerSpan9.position().top) - parseInt($('#header-outer #logo').css('margin-top')) ) - parseInt(nectarDOMInfo.secondaryHeaderHeight);
				 }
				 

			 }

			 if($('#wpadminbar').length > 0) {
				 $ssExtraTopSpace += $('#wpadminbar').outerHeight();
			 }

			if($('#header-outer').attr('data-using-secondary') == '1' && $('body.material').length == 0) {
				 $ssExtraTopSpace += $('#header-secondary-outer').outerHeight();
			 }
			 
			 $('.single-portfolio #sidebar').theiaStickySidebar({
				 additionalMarginTop: $ssExtraTopSpace,
				 updateSidebarHeight: false
			 });

			 
		} //if sticky sidebar is active and needs to be called
		
	} // end  portfolioSidebarFollow function
	

	
	
	
	//remove the portfolio filters that are not found in the current page
	function isotopeCatSelection() {


		$('.portfolio-items:not(".carousel")').each(function(){
			
			var isotopeCatArr = [];
			var $portfolioCatCount = 0;
			$(this).parent().parent().find('div[class^=portfolio-filters] ul li').each(function(i){
				if($(this).find('a').length > 0) {
					isotopeCatArr[$portfolioCatCount] = $(this).find('a').attr('data-filter').substring(1);	
					$portfolioCatCount++;
				}
			});
		
			
			////ice the first (all)
			isotopeCatArr.shift();
			
			
			var itemCats = '';
			
			$(this).find('> div').each(function(i){
				itemCats += $(this).attr('data-project-cat');
			});
			itemCats = itemCats.split(' ');
			
			////remove the extra item on the end of blank space
			itemCats.pop();
			
			////make sure the array has no duplicates
			itemCats = $.unique(itemCats);
			
			////if user has chosen a set of filters to display - only show those
			if($(this).is('[data-categories-to-show]') && $(this).attr('data-categories-to-show').length != 0 && $(this).attr('data-categories-to-show') != 'all') {
				var $userSelectedCats = $(this).attr('data-categories-to-show').replace(/,/g , ' ');
				$userSelectedCats = $userSelectedCats.split(' ');
				
				if(!$(this).hasClass('infinite_scroll')) $(this).removeAttr('data-categories-to-show');
			} else {
				var $userSelectedCats = itemCats;
			}
			
			
			////Find which categories are actually on the current page
			var notFoundCats = [];
			$.grep(isotopeCatArr, function(el) {

		    	if ($.inArray(el, itemCats) == -1) notFoundCats.push(el);
		    	if ($.inArray(el, $userSelectedCats) == -1) notFoundCats.push(el);

			});
			
			//manipulate the list
			if(notFoundCats.length != 0){
				$(this).parent().parent().find('div[class^=portfolio-filters] ul li').each(function(){
					if($(this).find('a').length > 0) {
						if( $.inArray($(this).find('a').attr('data-filter').substring(1), notFoundCats) != -1 ){ 
							
							if($(this).find('> ul.children').length > 0) {
								$(this).find('> a').hide(); 
							} else {
								$(this).hide(); 
							}
							
						} else {
							$(this).show();
						}
					}
				})
			}
		});
	}
	
	isotopeCatSelection();
	
	
	//sharing buttons

	var completed = 0;
	var windowLocation = window.location.href.replace(window.location.hash, '');

	function facebookShare(){
		windowLocation = window.location.href.replace(window.location.hash, '');
		window.open( 'https://www.facebook.com/sharer/sharer.php?u='+windowLocation, "facebookWindow", "height=380,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}

	function googlePlusShare(){
		windowLocation = window.location.href.replace(window.location.hash, '');
		window.open( 'https://plus.google.com/share?url='+windowLocation, "googlePlusWindow", "height=380,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}

	function twitterShare(){
        windowLocation = window.location.href.replace(window.location.hash, '');		
		if($(".section-title h1").length > 0) {
			var $pageTitle = encodeURIComponent($(".section-title h1").text());
		} else {
			var $pageTitle = encodeURIComponent($(document).find("title").text());
		}
		window.open( 'http://twitter.com/intent/tweet?text='+$pageTitle +' '+windowLocation, "twitterWindow", "height=380,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}

	function wooTwitterShare(){
		windowLocation = window.location.href.replace(window.location.hash, '');
		window.open( 'http://twitter.com/intent/tweet?text='+$("h1.product_title").text() +' '+windowLocation, "twitterWindow", "height=380,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}

	function linkedInShare(){
	    windowLocation = window.location.href.replace(window.location.hash, '');	
		if($(".section-title h1").length > 0) {
			var $pageTitle = encodeURIComponent($(".section-title h1").text());
		} else {
			var $pageTitle = encodeURIComponent($(document).find("title").text());
		}
		window.open( 'http://www.linkedin.com/shareArticle?mini=true&url='+windowLocation+'&title='+$pageTitle+'', "linkedInWindow", "height=480,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}

	function woolinkedInShare(){
	    windowLocation = window.location.href.replace(window.location.hash, '');	
		window.open( 'http://www.linkedin.com/shareArticle?mini=true&url='+windowLocation+'&title='+$("h1.product_title").text(), "twitterWindow", "height=380,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}

	function pinterestShare(){
		windowLocation = window.location.href.replace(window.location.hash, '');
		var $sharingImg = ($('.single-portfolio').length > 0 && $('div[data-featured-img]').attr('data-featured-img') != 'empty' ) ? $('div[data-featured-img]').attr('data-featured-img') : $('#ajax-content-wrap img').first().attr('src'); 
		
		if($(".section-title h1").length > 0) {
			var $pageTitle = encodeURIComponent($(".section-title h1").text());
		} else {
			var $pageTitle = encodeURIComponent($(document).find("title").text());
		}
		
		window.open( 'http://pinterest.com/pin/create/button/?url='+windowLocation+'&media='+$sharingImg+'&description='+$pageTitle, "pinterestWindow", "height=640,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}
	
	function wooPinterestShare(){
		$imgToShare = ($('img.attachment-shop_single').length > 0) ? $('img.attachment-shop_single').first().attr('src') : $('.single-product-main-image img').first().attr('src');
		windowLocation = window.location.href.replace(window.location.hash, '');
		window.open( 'http://pinterest.com/pin/create/button/?url='+windowLocation+'&media='+$imgToShare+'&description='+$('h1.product_title').text(), "pinterestWindow", "height=640,width=660,resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0" ) 
		return false;
	}


	function socialFade(){

			if(completed == $('a.nectar-sharing').length && $('a.nectar-sharing').parent().hasClass('in-sight')) {

					//love fadein
					$('.nectar-social .nectar-love span').show(350,'easeOutSine',function(){
						$(this).stop().animate({'opacity':1},800);
					});
					
					//sharing loadin
					$('.nectar-social > a').each(function(i){
						var $that = $(this);
						
						$(this).find('> span').show(350,'easeOutSine',function(){
							$that.find('> span').stop().animate({'opacity':1},800);
						});
						
					});


				//alt blog layout total share count
				var $totalShares = 0;
				$('.nectar-social > a .count').each(function(){
					$totalShares += parseInt($(this).html());
				});
				
				if($totalShares != 1){
					$('.single .meta-share-count .plural').css({'opacity':'1', 'display':'inline'});
					$('.single .meta-share-count .singular').remove();
				} else {
					$('.single .meta-share-count .singular').css({'opacity':'1', 'position':'relative',  'display':'inline'});
					$('.single .meta-share-count .plural').remove();
				}

				$('.meta-share-count .share-count-total').html($totalShares).css('opacity',1);
			}
		}

	$('body').on('click','#single-below-header .nectar-social a', function(){ return false; });

	$('body').on('click','.facebook-share:not(.inactive)', facebookShare);
	$('body').on('click','.google-plus-share:not(.inactive)', googlePlusShare);
	$('body').on('click','.nectar-social:not(".woo") .twitter-share:not(.inactive)', twitterShare);
	$('body').on('click','.nectar-social.woo .twitter-share', wooTwitterShare);
	$('body').on('click','.nectar-social:not(".woo") .linkedin-share:not(.inactive)', linkedInShare);
	$('body').on('click','.nectar-social.woo .linkedin-share', woolinkedInShare);
	$('body').on('click','.nectar-social:not(".woo") .pinterest-share:not(.inactive)', pinterestShare);
	$('body').on('click','.nectar-social.woo .pinterest-share', wooPinterestShare);
	$('body').on('click','.nectar-social-sharing-fixed > a', function(){ return false; });

	function socialSharingInit() {

		//mobile fullscreen blog class for click event fix
		if($('body').hasClass('mobile') && $('.single-post .fullscreen-header').length > 0) {
			$('#single-below-header .nectar-social .nectar-sharing, #single-below-header .nectar-social .nectar-sharing-alt').addClass('inactive');
		}

		completed = 0;

		if( $('a.facebook-share').length > 0 || $('a.twitter-share').length > 0 || $('a.google-plus-share').length > 0 || $('a.linkedin-share').length > 0 || $('a.pinterest-share').length > 0) {
  
		 
			////facebook
			if($('a.facebook-share:not(.sharing-default-minimal a.facebook-share)').length > 0 && $('body[data-button-style="rounded"]').length == 0 || $('#project-meta a.facebook-share').length > 0 || $('#single-meta a.facebook-share').length > 0 || $('#single-below-header .facebook-share').length > 0) {
				
				//load share count on load  
				$.getJSON("https://graph.facebook.com/?id="+ windowLocation +"&callback=?", function(data) {
					if( data.share != undefined && data.share.share_count != undefined && data.share.share_count != 0 && (data.share.share_count != null)) { 
						$('.facebook-share a span.count, a.facebook-share span.count').html( data.share.share_count );	
					}
					else {
						$('.facebook-share a span.count, a.facebook-share span.count').html( 0 );	
					}
					completed++;
					socialFade();
				});
			 
				
				
			} else if($('a.facebook-share').length > 0 && $('body[data-button-style="rounded"]').length > 0 || $('.sharing-default-minimal a.facebook-share').length > 0) {
				completed++;
				socialFade();
			}
			
			
			////twitter
			if($('a.twitter-share:not(.sharing-default-minimal a.twitter-share)').length > 0 && $('body[data-button-style="rounded"]').length == 0 || $('#project-meta a.twitter-share').length > 0 || $('#single-meta a.twitter-share').length > 0 || $('#single-below-header .twitter-share').length > 0) {
				//load tweet count on load 
			
					$('.twitter-share a span.count, a.twitter-share span.count').html( 0 );
				
					completed++;
					socialFade();
				


			} else if($('a.twitter-share').length > 0 && $('body[data-button-style="rounded"]').length > 0 || $('.sharing-default-minimal a.twitter-share').length > 0 ) {
				completed++;
				socialFade();
			}
			
			
			////linkedIn
			if($('a.linkedin-share:not(.sharing-default-minimal a.linkedin-share)').length > 0 && $('body[data-button-style="rounded"]').length == 0 || $('#project-meta a.linkedin-share').length > 0 || $('#single-meta a.linkedin-share').length > 0 || $('#single-below-header .linkedin-share').length > 0) {
				//load share count on load 
			
						$('.linkedin-share a span.count, a.linkedin-share span.count').html( 0 );
			
					completed++;
					socialFade();

				
			} else if($('a.linkedin-share').length > 0 && $('body[data-button-style="rounded"]').length > 0 || $('.sharing-default-minimal a.linkedin-share').length > 0) {
				completed++;
				socialFade();
			}
			
			
			////pinterest
			if(nectarDOMInfo.usingFrontEndEditor) {
				completed++;
				socialFade();
			}
			else {
				if($('a.pinterest-share:not(.sharing-default-minimal a.pinterest-share)').length > 0 && $('body[data-button-style="rounded"]').length == 0 || $('#project-meta a.pinterest-share').length > 0 || $('#single-meta a.pinterest-share').length > 0 || $('#single-below-header .pinterest-share').length > 0) {
					//load pin count on load 
					$.getJSON('https://api.pinterest.com/v1/urls/count.json?url='+windowLocation+'&callback=?', function(data) {
						if((data.count != 0) && (data.count != undefined) && (data.count != null)) { 
							$('.pinterest-share a span.count, a.pinterest-share span.count').html( data.count );
						}
						else {
							$('.pinterest-share a span.count, a.pinterest-share span.count').html( 0 );
						}
						completed++;
						socialFade();
					});

				} else if($('a.pinterest-share').length > 0 && $('body[data-button-style="rounded"]').length >  0 || $('.sharing-default-minimal a.pinterest-share').length > 0) {
					completed++;
					socialFade();
				}
		}

		//fadeIn
		$('a.nectar-sharing > span.count, a.nectar-sharing-alt > span.count').hide().css('width','auto');


		//social light up
		$('.nectar-social').each(function() {
			if($(this).parents('.custom-content').length == 0 && $(this).parents('.nectar-social-sharing-fixed').length == 0) {


				var $that = $(this);
				var waypoint = new Waypoint({
	 			element: $that,
	 			 handler: function(direction) {

					var $slide_timeout = ($('#page-header-bg[data-animate-in-effect="slide-down"] .nectar-social').length > 0 ) ? 900 : 1;

					setTimeout(function(){

						$that.addClass('in-sight');
						socialFade();
						
						if($('#page-header-bg .nectar-social').length == 0) {
							$that.find('> *').each(function(i){
								
								var $that = $(this);
								var $timeout = ($('body[data-button-style="rounded"]').length > 0) ? 0: 750;

								setTimeout(function(){ 
									
									$that.delay(i*80).queue(function(){ 
										
										var $that = $(this); $(this).addClass('hovered'); 
										
										setTimeout(function(){ 
											$that.removeClass('hovered');
										},300); 
										
									});
								
								},$timeout);
							});
						}

					},$slide_timeout );

					$that.addClass('animated-in');
					waypoint.destroy();
				},
				offset: 'bottom-in-view'

			}); 

				
			}
			}); 

		}

	}
	
	socialSharingInit();


	if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) {

		var $socialTimeout;
		$('body').on('mouseenter','#single-meta .meta-share-count, #project-meta .meta-share-count', function(){
			clearTimeout($socialTimeout);

			if($(this).parents('[id*="single-meta"]').length > 0 && $('[data-tab-pos="fullwidth"]').length == 0) 
				$(this).find('.nectar-social').show().stop(true).animate({'opacity': 1, 'right':'0px'},0);
			else 
				$(this).find('.nectar-social').show().stop(true).animate({'opacity': 1, 'left':'0px'},0);

			$(this).parents('[id*="-meta"]').addClass('social-hovered');

			$(this).parents('[id*="-meta"]').find('.n-shortcode a, .meta-comment-count a, .meta-share-count > a ').stop(true).animate({'opacity':0},250);
			$(this).find('.nectar-social a').each(function(i){
				$(this).stop(true).delay(i*40).animate({'opacity': 1,  'left':'0px'}, 150);
			});

		});

	
		$('body').on('mouseleave','#single-meta .meta-share-count, #project-meta .meta-share-count', function(){
			$(this).parents('[id*="-meta"]').removeClass('social-hovered');

			if($(this).parents('[id*="single-meta"]').length > 0 && $('[data-tab-pos="fullwidth"]').length == 0) 
				$(this).find('.nectar-social').stop(true).animate({'opacity': 0,  'right':'-20px'}, 200);
			else 
				$(this).find('.nectar-social').stop(true).animate({'opacity': 0,  'left':'-20px'}, 200);

			$(this).parents('[id*="-meta"]').find('.n-shortcode a, .meta-comment-count a, .meta-share-count > a ').stop(true).animate({'opacity':1},250);

			var $that = $(this);
			
			$socialTimeout = setTimeout(function(){ 
				$that.find('.nectar-social').hide(); 
				if($that.parents('[id*="single-meta"]').length > 0 && $('[data-tab-pos="fullwidth"]').length == 0) 
					$that.find('.nectar-social a').stop(true).animate({'opacity': 0,  'left':'20px'},0);   
				else 
					$that.find('.nectar-social a').stop(true).animate({'opacity': 0,  'left':'-20px'},0);   
			}, 200);
		});
	} else {
		var $socialTimeout;
		$('body').on('click','#single-meta .meta-share-count, #project-meta .meta-share-count', function(){
			clearTimeout($socialTimeout);

			if($(this).parents('[id*="single-meta"]').length > 0 && $('[data-tab-pos="fullwidth"]').length == 0) 
				$(this).find('.nectar-social').show().stop(true).animate({'opacity': 1, 'right':'0px'},0);
			else 
				$(this).find('.nectar-social').show().stop(true).animate({'opacity': 1, 'left':'0px'},0);

			$(this).parents('[id*="-meta"]').addClass('social-hovered');

			$(this).parents('[id*="-meta"]').find('.n-shortcode a, .meta-comment-count a, .meta-share-count > a ').stop(true).animate({'opacity':0},250);
			$(this).find('.nectar-social a').each(function(i){
				$(this).stop(true).delay(i*40).animate({'opacity': 1,  'left':'0px'}, 150);
			});

			return false;
		});

	}

	$('body').on('mouseenter','.fullscreen-header  .meta-share-count', function(){
		$(this).find('> a, > i').stop(true).animate({'opacity': 0},400);
		$(this).find('.nectar-social > *').each(function(i){
			$(this).stop(true).delay(i*50).animate({'opacity':'1', 'top': '0px'},250,'easeOutCubic');
		});
		//allow clickable on mobile
		setTimeout(function(){ $('.meta-share-count .nectar-sharing, .meta-share-count .nectar-sharing-alt').removeClass('inactive'); },300);
	});

	if(!navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) {
		$('body').on('mouseleave','.fullscreen-header  .meta-share-count', function(){
			$(this).find('> a, > i').stop(true).animate({'opacity': 1},300,'easeInCubic');
			$(this).find('.nectar-social > *').each(function(i){
				$(this).stop(true).animate({'opacity':'0', 'top': '10px'},200,'easeInCubic');
			});
		});
	}

	//full width love center
	function nectarLoveFWCenter(){
		$('.nectar-social.full-width').each(function(){ 
			$(this).find('.n-shortcode .nectar-love').css('padding-top', $(this).find('> a').css('padding-top'));
		});
	}
	
	nectarLoveFWCenter();
	

	//-----------------------------------------------------------------
	// NectarLove
	//-----------------------------------------------------------------
	
	////love iwth txt
	$('.fullscreen-header .nectar-love').each(function(){
		if($(this).find('.nectar-love-count').text() == '1') {
			$(this).find('span.love-txt.single').css({'visibility':'visible', 'text-indent':'0'});
			$(this).find('span.love-txt.plural').css({'visibility':'hidden', 'text-indent':'-9999px'});
		} else {
			$(this).find('span.love-txt.single').css({'visibility':'hidden', 'text-indent':'-9999px'});
			$(this).find('span.love-txt.plural').css({'visibility':'visible', 'text-indent':'0'});
		}
	});

	$('body').on('click','.nectar-love', function() {
			

			var $loveLink = $(this);
			var $id = $(this).attr('id');
			var $that = $(this);
			
			if($loveLink.hasClass('loved')) return false;
			if($(this).hasClass('inactive')) return false;
			
			var $dataToPass = {
				action: 'nectar-love', 
				loves_id: $id,
				love_nonce: nectarLove.loveNonce
			}
			
			$.post(nectarLove.ajaxurl, $dataToPass, function(data){
				$loveLink.find('span:not(.love-txt)').html(data);
				$loveLink.addClass('loved').attr('title','You already love this!');
				$loveLink.find('span:not(.love-txt)').css({'opacity': 1,'width':'auto'});

				if($(data).text() == '1') {
					$loveLink.find('span.love-txt.single').css({'visibility':'visible', 'text-indent':'0'});
					$loveLink.find('span.love-txt.plural').css({'visibility':'hidden', 'text-indent':'-9999px'});
				} else {
					$loveLink.find('span.love-txt.single').css({'visibility':'hidden', 'text-indent':'-9999px'});
					$loveLink.find('span.love-txt.plural').css({'visibility':'visible', 'text-indent':'0'});
				}

				//ascend
				if($('body').hasClass('ascend') && $that.parents('.classic_enhanced').length == 0 ){
					$that.find('.icon-salient-heart-2').addClass('loved');
				} else if($that.parents('.classic_enhanced').length > 0 ) {
					$that.find('.icon-salient-heart-2').addClass('loved');
				}
			});
			
			$(this).addClass('inactive');
			
			return false;
	});


	
	//infinite scroll
	function infiniteScrollInit() {

		if($('.infinite_scroll').length > 0) {
			
			//portfolio
			$('.portfolio-items.infinite_scroll').infinitescroll({
		    	navSelector  : "div#pagination",            
		   	    nextSelector : "div#pagination a:first",    
		   	    itemSelector : ".portfolio-items.infinite_scroll .element",
		   	    finishedMsg: "<em>Congratulations, you've reached the end of the internet.</em>",
		        msgText: " ",         
		   },function(newElements){
		   	

				var $container = $('.portfolio-items.infinite_scroll:not(.carousel)');
				//loading effect   

		        var $newElems = $( newElements ).css('opacity',0);
		        //// ensure that images load before adding to masonry layout
		        $newElems.imagesLoaded(function(){
		          
		          $( newElements ).css('opacity',1);

		          $container.isotope( 'appended', $( newElements ));
		          
		          $( newElements ).find('.work-item').addClass('ajax-loaded');
		          $( newElements ).addClass('ajax-loaded');
		          ///// show elems now they're ready
		          
		          $(newElements).find('.work-meta, .nectar-love-wrap').css({'opacity':1});
		          
		          //keep filtering
		          if($('.portfolio-filters-inline').length > 0 || $('.portfolio-filters').length > 0) {
		          	
		          	  if($('.portfolio-filters-inline').length > 0) {
		          	  	 var selector = $('.portfolio-filters-inline a.active').attr('data-filter');
		          	  } else {
		          	  	 var selector = $('.portfolio-filters a.active').attr('data-filter');
		          	  }
		          	  
		          	  $('.portfolio-filters-inline a.active').attr('data-filter');
			  	 	  $container.isotope({ filter: selector });
		          }
		          
			  	//set width
			  	//portfolioItemWidths();
			  	reLayout();

		        if($(newElements).find('.work-item.style-5').length > 0) style6Img();

	          	if($(newElements).find('.inner-wrap').attr('data-animation') == 'none') {
					$('.portfolio-items .col .inner-wrap').removeClass('animated');
				} else {

					masonryZindex();
					$(newElements).each(function(i){
						
						var $portfolioOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '90%';
						
						//not already visible
						var $that = $(this);
						var waypoint = new Waypoint({
			 			element: $that,
			 			 handler: function(direction) {
							
							var $portfolioAnimationDelay = ($that.is('[data-masonry-type="photography"].masonry-items')) ? 85 : 115;
						
							setTimeout(function(){
								$that.addClass("animated-in");
							},$portfolioAnimationDelay * $that.attr('data-delay-amount'));
						
							
							waypoint.destroy();
						},
						offset: $portfolioOffsetPos

						}); //waypoint

	
					}); //each
				}

		       
		    portfolioHoverEffects();	
				portfolioAccentColor();
		         
		    //verify smooth scorlling
				if( $smoothCache == true && $(window).width() > 690 && $('body').outerHeight(true) > $(window).height() && Modernizr.csstransforms3d && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)){ niceScrollInit(); $(window).trigger('resize') } 
				
				
				
				//prettyphoto
				$('.portfolio-items').each(function(){
					var $unique_id = Math.floor(Math.random()*10000);
					$(this).find('a[rel^="prettyPhoto"], a.pretty_photo').attr('rel','prettyPhoto['+$unique_id+'_gal]').removeClass('pretty_photo');
				});
		
				lightBoxInit();
				
				piVertCenter();

				setTimeout(function(){masonryZindex(); reLayout(); $( newElements ).removeClass('ajax-loaded'); },700);
		        
		        //recalc the filters
		        isotopeCatSelection();

		        parallaxRowsBGCals();
	          
	          }); 

				
		   });

			//blog
			$('.post-area.infinite_scroll .posts-container').infinitescroll({
		    	navSelector  : "div#pagination",            
		   	    nextSelector : "div#pagination a:first",    
		   	    itemSelector : ".post-area .posts-container .post",
		   	    finishedMsg: "<em>Congratulations, you've reached the end of the internet.</em>",
		        msgText: " " 
		   },function(newElements){
		   	
		   	if($('.masonry.meta_overlaid').length == 0) { 
			   	//reinit js
			   	centerLove();
			   	
			   	//gallery
				$(newElements).find('.flex-gallery').each(function(){
					
					var $that = $(this);
					
					 $that.flexslider({
				        animation: 'fade',
				        smoothHeight: false, 
				        animationSpeed: 500,
				        useCSS: false, 
				        touch: true
				    });
					
					////gallery slider add arrows
					$('.flex-gallery .flex-direction-nav li a.flex-next').html('<i class="fa fa-angle-right"></i>');
					$('.flex-gallery .flex-direction-nav li a.flex-prev').html('<i class="fa fa-angle-left"></i>');

				});
			   	
			   	
			   	//media players
			   	if($().mediaelementplayer) $(newElements).find('.wp-audio-shortcode, .wp-video-shortcode').mediaelementplayer();
			   	
			   	
			   	//lightbox
			    lightBoxInit();
			   	
			   	//carousels
			   	if($('.carousel').length > 0) {
				   	standardCarouselInit();
			    	clientsCarouselInit();
			    }
			   	

			   	//milestone
			   	$(newElements).find('.nectar-milestone').each(function() {
					//symbol
					if($(this).has('[data-symbol]')) {
						if($(this).attr('data-symbol-pos') == 'before') {
							$(this).find('.number').prepend($(this).attr('data-symbol'));
						} else {
							$(this).find('.number').append($(this).attr('data-symbol'));
						}
					}
				});
				
				if(!$('body').hasClass('mobile')) {
					
					$(newElements).find('.nectar-milestone').each(function() {
						//animation

							var $that = $(this);
							var waypoint = new Waypoint({
				 			element: $that,
				 			 handler: function(direction) {
								if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
								     waypoint.destroy();
								     return;
								}

								var $endNum = parseInt($that.find('.number span').text());

								var countOptions = { easingFn: easeOutCubic };
								var $countEle = $that.find('.number span:not(.symbol)')[0];
								var numAnim = new CountUp($countEle, 0, $endNum,0,2.2,countOptions);
								numAnim.start();


								$that.addClass('animated-in');
								waypoint.destroy();
							},
							offset: 'bottom-in-view'

						}); 

						
					}); 
				}
				
				//pie chart		
			    if($().vcChat) $(newElements).find('.vc_pie_chart').vcChat();
		    	
		    	//fancy ul
		    	nectar_fancy_ul_init();
		    	
		    	//testimonial slider
		    	$('.testimonial_slider').animate({'opacity':'1'},800);
		    	createTestimonialControls();
				testimonialSliderHeight(); 
				testimonialHeightResize();
		    	
				//bar graph
				$(newElements).find('.nectar-progress-bar').each(function(i){
				

				var $that = $(this);
				var waypoint = new Waypoint({
		 			element: $that,
		 			 handler: function(direction) {
						if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
						     waypoint.destroy();
						     return;
						}

						var percent = $that.find('span').attr('data-width');
						var $endNum = parseInt($that.find('span strong i').text());
						
						$that.find('span').transition({
							'width' : percent + '%'
						},1600, 'easeInOutCirc',function(){
						});
						
						$that.find('span strong').transition({
							'opacity' : 1
						},1350);
						
						
						var countOptions = { useEasing : false };
						var $countEle = $that.find('span strong i')[0];
						var numAnim = new CountUp($countEle, 0, $endNum,0,1.2,countOptions);
						numAnim.start();

						
						////100% progress bar 
						if(percent == '100'){
							$that.find('span strong').addClass('full');
						}


						$that.addClass('animated-in');
						waypoint.destroy();
					},
					offset: 'bottom-in-view'

				}); 


			
				});
				
				
				//columns & images with animation
				colAndImgAnimations();
				splitLineHeadings();

				setTimeout(function(){
					responsiveVideoIframesInit();
					responsiveVideoIframes();
					$(window).trigger('resize');
				},500);


				parallaxRowsBGCals();

				$(window).trigger('resize');
			   	
		   	}//non meta overlaid style 
		   	else {
		   		parallaxRowsBGCals();

				  $(window).trigger('resize');
		   	}

		   	// trigger Masonry as a callback
		   	var $container = $('.posts-container');
		    if($container.parent().hasClass('masonry')) { 
		    	 
		    	$(newElements).addClass('masonry-blog-item');
				$(newElements).prepend('<span class="bottom-line"></span>');
				
				//move the meta to the bottom
				$(newElements).each(function(){
					
					var $metaClone = $(this).find('.post-meta').clone();
		
					$(this).find('.post-meta').remove();
					
					if($('.post-area.meta_overlaid').length > 0){
						$(this).find('.post-header h2').after($metaClone);
					} else {
						$(this).find('.content-inner').after($metaClone);
					}
					
				});
			
		    	}//if masonry


		    	//loading effect   
		    	
		        //// hide new items while they are loading
		        var $newElems = $( newElements );
		        //// ensure that images load before adding to masonry layout
						
		        if($newElems.find('img').length == 0) $newElems = $('body');
		        
		        $newElems.imagesLoaded(function(){
							
							if($container.parent().hasClass('masonry') && !$container.parent().hasClass('auto_meta_overlaid_spaced')) {
			          $container.isotope( 'appended', $( newElements ));
							}

		          flickityBlogInit();

		          $( newElements ).addClass('ajax-loaded');
		          ///// show elems now they're ready


		        //classic enhanced specific 
		        if($container.parent().hasClass('classic_enhanced')){
					$container.find('.large_featured.has-post-thumbnail.ajax-loaded .post-featured-img, .wide_tall.has-post-thumbnail.ajax-loaded .post-featured-img').each(function(){
						var $src = $(this).find('img').attr('src');
						$(this).css('background-image','url('+$src+')');
					});

					$container.find('.large_featured.ajax-loaded .nectar-flickity, .wide_tall.ajax-loaded .nectar-flickity').each(function(){

						$(this).find('.cell').each(function(){
							var $src = $(this).find('img').attr('src');
							$(this).css('background-image','url('+$src+')');
						});
						
					});
				}


		          if($(newElements).parents('.posts-container').attr('data-animation') == 'none') {
						$( newElements ).find('.inner-wrap').removeClass('animated');
					} else {
						blogMasonryZindex();
						$(newElements).each(function(i){

							var $that = $(this);
							var waypoint = new Waypoint({

					 			element: $that,
					 			 handler: function(direction) {
									
									setTimeout(function(){
									    $that.addClass("animated-in");
									},80*$that.attr('data-delay-amount'));

									waypoint.destroy();
								},
								offset: '90%'

							}); 

		
						}); //each
					}

					setTimeout(function(){$( newElements ).removeClass('ajax-loaded'); },700);

		        
		        });
		        
		    
		   	
		   });
		   
	   }

}

infiniteScrollInit();

function destroyInfiniteScroll(){
	$('.post-area.infinite_scroll .posts-container').infinitescroll('destroy');
	$('.portfolio-items.infinite_scroll').infinitescroll('destroy');
}
	
/*-------------------------------------------------------------------------*/
/*	6.	Scroll to top
/*-------------------------------------------------------------------------*/	

var $scrollTop = $(window).scrollTop();

//starting bind
function toTopBind() {
	if( $('#to-top').length > 0 && $(window).width() > 1020 || $('#to-top').length > 0 &&  $('#to-top.mobile-enabled').length > 0 ) {
		
		if($scrollTop > 350){
			$(window).on('scroll',hideToTop);
		}
		else {
			$(window).on('scroll',showToTop);
		}
	}
}

if($('.nectar-social-sharing-fixed').length == 0) {
	toTopBind();
} else {
	if($(window).width() < 1000 && $('body.single').length > 0) {
		if($scrollTop > 150){
			$(window).on('scroll',hideFixedSharing);
		}
		else {
			$(window).on('scroll',showFixedSharing);
		}
	}

	$(window).smartresize(function(){
		if($(window).width() > 1000) { 
			$('.nectar-social-sharing-fixed').addClass('visible');
		}	
		else if($scrollTop < 150) { 
			$(window).off('scroll',hideFixedSharing);
			$(window).on('scroll',showFixedSharing);
			$('.nectar-social-sharing-fixed').removeClass('visible');	
		} else {
			$(window).off('scroll',showFixedSharing);
			$(window).on('scroll',hideFixedSharing);
		}
	});
}

function showFixedSharing(){
	
  $scrollTop = $(window).scrollTop();
	if( $scrollTop > 150){

		$('.nectar-social-sharing-fixed').addClass('visible');	
		
		$(window).off('scroll',showFixedSharing);
		$(window).on('scroll',hideFixedSharing);
	}

}

function hideFixedSharing(){
  
	$scrollTop = $(window).scrollTop();
	if( $scrollTop < 150){

		$('.nectar-social-sharing-fixed').removeClass('visible');	
		
		$(window).off('scroll',hideFixedSharing);
		$(window).on('scroll',showFixedSharing);
	}

}



function showToTop(){

	if( nectarDOMInfo.scrollTop > 350 && $('#slide-out-widget-area.fullscreen.open').length == 0){

		$('#to-top').stop().transition({
			'bottom' : '17px'
		},350,'easeInOutCubic');	
		
		$(window).off('scroll',showToTop);
		$(window).on('scroll',hideToTop);
	}

}

function hideToTop(){
	
	if( nectarDOMInfo.scrollTop < 350 || $('#slide-out-widget-area.fullscreen.open').length > 0){

		var $animationTiming = ($('#slide-out-widget-area.fullscreen.open').length > 0) ? 1150 : 350;

		$('#to-top').stop().transition({
			'bottom' : '-30px'
		},$animationTiming,'easeInOutQuint');	
		
		$(window).off('scroll',hideToTop);
		$(window).on('scroll',showToTop);	
		
	}
}


//to top color
if( $('#to-top').length > 0 ) {
	
	var $windowHeight, $pageHeight, $footerHeight, $ctaHeight;
	
	function calcToTopColor(){
		$scrollTop = $(window).scrollTop();
		$windowHeight = $(window).height();
		$pageHeight = $('body').height();
		$footerHeight = $('#footer-outer').height();
		$ctaHeight = ($('#call-to-action').length > 0) ? $('#call-to-action').height() : 0;
		
		if( ($scrollTop-35 + $windowHeight) >= ($pageHeight - $footerHeight) && $('#boxed').length == 0){
			$('#to-top').addClass('dark');
		}
		
		else {
			$('#to-top').removeClass('dark');
		}
	}
	
	if(!nectarDOMInfo.usingMobileBrowser) {
		//calc on scroll
		$(window).scroll(calcToTopColor);
		
		//calc on resize
		$(window).resize(calcToTopColor);
	}

}

//alt style
if($('body[data-button-style*="rounded"]').length > 0){
	var $clone = $('#to-top .fa-angle-up').clone();
	$clone.addClass('top-icon');
	$('#to-top').prepend($clone)
}

//scroll up event
$('body').on('click','#to-top, a[href="#top"]',function(){
	$('body,html').stop().animate({
		scrollTop:0
	},800,'easeOutQuad',function(){
		if($('.nectar-box-roll').length > 0) {
			$('body').trigger('mousewheel', [1, 0, 0]);
		}
	})
	return false;
});


/* one page scrolling */
function scrollSpyInit(){ 

	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-outer').outerHeight();

	if( $('.page-template-template-no-header-footer').length > 0 || $('.page-template-template-no-header').length > 0 ) { $headerNavSpace = 0; }

	//prevent jump to rop on empty items
	$('header#top .sf-menu li a[href="#"]').on('click',function(e){
		e.preventDefault();
	});
	
	if( $('#nectar_fullscreen_rows').length == 0 || $disableFPonMobile == 'on') {

		$('a.nectar-next-section').each(function(){
			
			if($(this).parents('.wpb_row:not(.inner_row)').length > 0) {
				
				var $parentRow = $(this).parents('.wpb_row:not(.inner_row)');
				var $parentRowIndex = $(this).parents('.wpb_row:not(.inner_row)').index();
				
				if($parentRow.parent().find('> .wpb_row[id]:eq('+($parentRowIndex+1)+')').length > 0) {
					var $nextRowID = $parentRow.parent().find('> .wpb_row:eq('+($parentRowIndex+1)+')').attr('id');
					$(this).attr('href', '#' + $nextRowID);
				} 
				
			}
			
		});
	} else if( $().fullpage ) {
			$('a.nectar-next-section').on('click',function(){
				$.fn.fullpage.moveSectionDown();
				return false;
			});
	}

	//remove full page URLs from hash if located on same page to fix current menu class
	//if(location.pathname.length > 1) {
	
	  //ocm
		if($('#slide-out-widget-area .off-canvas-menu-container').length > 0) {
			$('#slide-out-widget-area .off-canvas-menu-container').find("a[href*='" + location.pathname + "']").each(function(){
					
					var $href = $(this).attr('href');

					//regular animated anchors
					if($href != '#' && $href.indexOf("#") != -1 && $('div'+$href.substr($href.indexOf("#"))).length > 0 ) {
						$(this).attr('href',$href.substr($href.indexOf("#")));
						$(this).parent().removeClass('current_page_item').removeClass('current-menu-item');
					}
					
					//fullpage is a little different
					if($('div[data-fullscreen-anchor-id="'+$href.substr($href.indexOf("#")+1)+'"]').length > 0) {
						$(this).parent().removeClass('current_page_item').removeClass('current-menu-item');
					}
					
			});
		}
		
	  //header
		$("#header-outer").find("a[href*='" + location.pathname + "']").each(function(){
			var $href = $(this).attr('href');

			//regular animated anchors
			if($href.indexOf("#") != -1 && $('div'+$href.substr($href.indexOf("#"))).length > 0 ) {

				$(this).attr('href',$href.substr($href.indexOf("#")));
				$(this).parent().removeClass('current_page_item').removeClass('current-menu-item');
			}

			//fullpage is a little different
			if($('div[data-fullscreen-anchor-id="'+$href.substr($href.indexOf("#")+1)+'"]').length > 0) {
				$(this).parent().removeClass('current_page_item').removeClass('current-menu-item');
			}

		});
	//}

	var $target = ($('.page-submenu[data-sticky="true"]').length == 0) ? '#header-outer nav': '.page-submenu';
	$('body').scrollspy({
		target: $target,
		offset: $headerNavSpace + nectarDOMInfo.adminBarHeight + 40
	});

}


/*helper function to scroll the page in an animated manner*/
function nectar_scrollToY(scrollTargetY, speed, easing) {

		var scrollY = window.scrollY || document.documentElement.scrollTop,
				scrollTargetY = scrollTargetY || 0,
				speed = speed || 2000,
				easing = easing || 'easeOutSine',
				currentTime = 0;

		var time = Math.max(.1, Math.min(Math.abs(scrollY - scrollTargetY) / speed, .8));


		var easingEquations = {
						easeInOutQuint: function (pos) {
								if ((pos /= 0.5) < 1) {
										return 0.5 * Math.pow(pos, 5);
								}
								return 0.5 * (Math.pow((pos - 2), 5) + 2);
						}
				};


		function tick() {
				currentTime += 1 / 60;

				var p = currentTime / time;
				var t = easingEquations[easing](p);

				if (p < 1) {
						requestAnimationFrame(tick);

						window.scrollTo(0, scrollY + ((scrollTargetY - scrollY) * t));
				} else {
						window.scrollTo(0, scrollTargetY);
				}
		}

		tick();
}


function pageLoadHash() {

	var $hash = window.location.hash;
	
	var $hashSubstrng = ($hash && $hash.length > 0) ? $hash.substring(1,$hash.length) : 0;

	//if hash has slashes 
	var $hasSlashLength = 0;
	if($hashSubstrng) {
		$hasSlashLength = $hashSubstrng.split("/");
		$hasSlashLength = $hasSlashLength.length;
	}
	
	if($hashSubstrng && $hasSlashLength > 1 ) { 
		$hashSubstrng = $hashSubstrng.replace(/\//g, ""); 
		$hash = $hash.replace(/\//g, ""); 
	}

	if($hash && $('.main-content').find($hash).length > 0 || $hash && $('.main-content').find('[data-fullscreen-anchor-id="'+$hashSubstrng+'"]').length > 0) {

		var $hashObj = ($('.main-content').find($hash).length > 0) ? $('.main-content').find($hash) : $('.main-content').find('[data-fullscreen-anchor-id="'+$hashSubstrng+'"]');

		var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-space').outerHeight();
		if( $('.page-template-template-no-header-footer').length > 0 || $('.page-template-template-no-header').length > 0 ) { $headerNavSpace = 0; }

		$timeoutVar = 0;
		if($('.nectar-box-roll').length > 0 && $('.container-wrap.bottomBoxOut').length > 0) {
			boxRoll(null,-1);
			$timeoutVar = 2050;
		} 
		setTimeout(function(){
		
			if( $('body[data-permanent-transparent="1"]').length == 0 ) {
				
				if(!$('body').hasClass('mobile')){
					$resize = ($('#header-outer[data-header-resize="0"]').length > 0) ? 0 : parseInt(shrinkNum) + headerPadding2*2;
					if($('#header-outer[data-remove-fixed="1"]').length > 0) { 
						$headerNavSpace = 0;
					}
					var $scrollTopDistance =  $hashObj.offset().top - parseInt($headerNavSpace) +$resize + 3 - nectarDOMInfo.adminBarHeight;
					
					
					
					//condesne
					if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
						
							var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
							var $secondaryHeader = $('#header-secondary-outer');
							var $headerHeightStored = $('#header-outer').height();
							
							$headerHeightCondensed = $headerHeightStored - ( parseInt($headerSpan9.height()) + parseInt($('#header-outer #logo').css('margin-top')) );
						
							$scrollTopDistance =  $hashObj.offset().top - parseInt($headerNavSpace) + $headerHeightCondensed - nectarDOMInfo.adminBarHeight;
					}
						
					
					
				} else {
					var $scrollTopDistance = ($('#header-outer[data-mobile-fixed="1"]').length > 0) ? $hashObj.offset().top + 2 - $headerNavSpace + nectarDOMInfo.adminBarHeight : $hashObj.offset().top - nectarDOMInfo.adminBarHeight + 1;	
				}

			} else {
				var $scrollTopDistance = $hashObj.offset().top - nectarDOMInfo.adminBarHeight + 1;
			}

			if($('body[data-hhun="1"]').length > 0 && $('#header-outer[data-remove-fixed="1"]').length == 0) {
				//alter offset 
				if($('#header-outer.detached').length == 0) 
					$scrollTopDistance = $scrollTopDistance + $headerNavSpace;
			}

			var $pageSubMenu = ($('.page-submenu[data-sticky="true"]').length > 0) ? $('.page-submenu').height() : 0;
			
			if($('body.material').length > 0 && 
				 $('#header-secondary-outer').length > 0 &&
				 $('body[data-hhun="1"]').length == 0 && 
				 $('#header-outer[data-remove-fixed="1"]').length == 0 && 
				 !$('body').hasClass('mobile')) { 
					 
					 var $headerSecondary = $('#header-secondary-outer').height();
					 
			 } else {
					 var $headerSecondary = 0;
			 }
			 
			nectar_scrollToY($scrollTopDistance - $pageSubMenu + $headerSecondary, 700, 'easeInOutQuint');

		},$timeoutVar);
	}
}

if($('body[data-animated-anchors="true"]').length > 0 || $('.single-product [data-gallery-style="left_thumb_sticky"]').length > 0) { 


+ function(t) {
    "use strict";

    function s(e, i) {
        var r = t.proxy(this.process, this);
        this.$body = t("body"), this.$scrollElement = t(t(e).is("body") ? window : e), this.options = t.extend({}, s.DEFAULTS, i), this.selector = (this.options.target || "") + " ul li > a", this.offsets = [], this.targets = [], this.activeTarget = null, this.scrollHeight = 0, this.$scrollElement.on("scroll.bs.scrollspy", r), this.refresh(), this.process()
    }

    function e(e) {
        return this.each(function() {
            var i = t(this),
                r = i.data("bs.scrollspy"),
                o = "object" == typeof e && e;
            r || i.data("bs.scrollspy", r = new s(this, o)), "string" == typeof e && r[e]()
        })
    }
    s.VERSION = "3.2.0", s.DEFAULTS = {
        offset: 10
    }, s.prototype.getScrollHeight = function() {
        return this.$scrollElement[0].scrollHeight || Math.max(this.$body[0].scrollHeight, document.documentElement.scrollHeight)
    }, s.prototype.refresh = function() {
        var s = "offset",
            e = 0;
        t.isWindow(this.$scrollElement[0]) || (s = "position", e = this.$scrollElement.scrollTop()), this.offsets = [], this.targets = [], this.scrollHeight = this.getScrollHeight();
        var i = this;
        this.$body.find(this.selector).map(function() {
            var i = t(this),
                r = i.data("target") || i.attr("href"),
                o = /^#./.test(r) && t(r);
            return o && o.length && o.is(":visible") && [
                [o[s]().top + e, r]
            ] || null
        }).sort(function(t, s) {
            return t[0] - s[0]
        }).each(function() {
            i.offsets.push(this[0]), i.targets.push(this[1])
        })
    }, s.prototype.process = function() {
    	var $pageSubMenu = ($('.page-submenu[data-sticky="true"]').length > 0 && $('body[data-hhun="1"]').length == 0 || $('.page-submenu[data-sticky="true"]').length > 0 && $('#header-outer[data-remove-fixed="1"]').length > 0 ) ? $('.page-submenu').height() : 0;

        var t, s = this.$scrollElement.scrollTop() + this.options.offset + $pageSubMenu,
            e = this.getScrollHeight(),
            i = this.options.offset + e - this.$scrollElement.height() -$pageSubMenu,
            r = this.offsets,
            o = this.targets,
            l = this.activeTarget;
        if (this.scrollHeight != e && this.refresh(), s >= i) return l != (t = o[o.length - 1]) && this.activate(t);
        if (l && s <= r[0]) return l != (t = o[0]) && this.activate(t);
        for (t = r.length; t--;) l != o[t] && s >= r[t] && (!r[t + 1] || s <= r[t + 1]) && this.activate(o[t])
    }, s.prototype.activate = function(s) {
        this.activeTarget = s, t(this.selector).parentsUntil(this.options.target, ".current-menu-item").removeClass("current-menu-item").removeClass('sfHover');
        var e = this.selector + '[data-target="' + s + '"],' + this.selector + '[href="' + s + '"]',
            i = t(e).parents("li").addClass("current-menu-item");
        i.parent(".dropdown-menu").length && (i = i.closest("li.dropdown").addClass("current-menu-item")), i.trigger("activate.bs.scrollspy")
    };
    var i = t.fn.scrollspy;
    t.fn.scrollspy = e, t.fn.scrollspy.Constructor = s, t.fn.scrollspy.noConflict = function() {
        return t.fn.scrollspy = i, this
    }
}(jQuery);


var shrinkNum = 6;	
if($('#header-outer[data-shrink-num]').length > 0 ) shrinkNum = $('#header-outer').attr('data-shrink-num');
headerPadding2 = headerPadding - headerPadding/1.8;

setTimeout(scrollSpyInit,200);

var $animatedScrollingTimeout;

$('body').on('click','#header-outer nav .sf-menu a, #footer-outer .nectar-button, .container-wrap a:not(.wpb_tabs_nav a):not(.magnific):not([data-fancybox]):not(.woocommerce-tabs a):not(.testimonial-next-prev a), .swiper-slide .button a, #slide-out-widget-area a, #mobile-menu .container ul li a, #slide-out-widget-area .inner div a',function(e){
	


	var $hash = $(this).prop("hash");	

	$('body').addClass('animated-scrolling');
	clearTimeout($animatedScrollingTimeout);
	$animatedScrollingTimeout = setTimeout(function(){ $('body').removeClass('animated-scrolling'); },850);
	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0 && $(window).width() > 1000) ? 0 : $('#header-space').outerHeight();
	if( $('.page-template-template-no-header-footer').length > 0 || $('.page-template-template-no-header').length > 0 ) { $headerNavSpace = 0; }

	if($hash && $('body').find($hash).length > 0 && $hash != '#top' && $hash != '' && $(this).attr('href').indexOf(window.location.href.split("#")[0]) !== -1 || $(this).is('[href^="#"]') && $hash != '' && $('body').find($hash).length > 0 && $hash != '#top') {


		//update hash
		if(!$(this).hasClass('skip-hash')) {
			if(history.pushState) {
			    history.pushState(null, null, $hash);
			}
			else {
			    location.hash = $hash;
			}
		}

		if($(this).parents('ul').length > 0) { 
			$(this).parents('ul').find('li').removeClass('current-menu-item');
		}

		//side widget area click
		if($(this).parents('#slide-out-widget-area').length > 0){
			
			if($('body.material[data-slide-out-widget-area-style="slide-out-from-right"].material-ocm-open').length > 0) {
				$('body > .slide_out_area_close').addClass('non-human-allowed').trigger('click');
				//scroll
				var $clickedLinkStore = $(this);
				setTimeout(function(){
					$clickedLinkStore.trigger('click');
				},1000);
				
			} else {
				$('#slide-out-widget-area .slide_out_area_close').addClass('non-human-allowed').trigger('click');
			}
			
			setTimeout(function(){
				if($('body.material[data-slide-out-widget-area-style="slide-out-from-right"]').length > 0) {
					$('body > .slide_out_area_close').removeClass('non-human-allowed');

				} else {
					$('#slide-out-widget-area .slide_out_area_close').removeClass('non-human-allowed');
				}
			},100);
		}

		//mobile menu click
		if($(this).parents('#mobile-menu').length > 0) $('#toggle-nav').trigger('click');
		var $mobileMenuHeight = ($(this).parents('#mobile-menu').length > 0) ? $(this).parents('#mobile-menu').height() : null;
		
		$timeoutVar = 1;
		if($('.nectar-box-roll').length > 0 && $('.container-wrap.bottomBoxOut').length > 0) {
			boxRoll(null,-1);
			$timeoutVar = 2050;
		} 

		var $that = $(this);

		setTimeout(function(){

			//scrolling
			var $headerSpace = ($('body[data-permanent-transparent="1"]').length > 0) ? 0 : parseInt($headerNavSpace);
			
			if( $('body[data-permanent-transparent="1"]').length == 0 ) {
				
				if(!$('body').hasClass('mobile')){
					$resize = ($('#header-outer[data-header-resize="0"]').length > 0) ? 0 : parseInt(shrinkNum) + headerPadding2*2;
					if($('#header-outer[data-remove-fixed="1"]').length > 0) { 
						$headerNavSpace = 0;
					}
					
					var $scrollTopDistance =  $($hash).offset().top - $mobileMenuHeight - parseInt($headerNavSpace) +$resize + 3 - nectarDOMInfo.adminBarHeight;
					
					
					//condesne
					if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
							var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
							var $secondaryHeader = $('#header-secondary-outer');
							var $headerHeightStored = $('#header-outer').height();
							
							$headerHeightCondensed = $headerHeightStored - ( parseInt($headerSpan9.height()) + parseInt($('#header-outer #logo').css('margin-top')) );
						
							$scrollTopDistance =  $($hash).offset().top - parseInt($headerNavSpace) + $headerHeightCondensed - nectarDOMInfo.adminBarHeight;
					}
					
					
				} else {
					var $scrollTopDistance = ($('#header-outer[data-mobile-fixed="1"]').length > 0) ? $($hash).offset().top + 2 - $headerNavSpace + nectarDOMInfo.adminBarHeight : $($hash).offset().top - $mobileMenuHeight - nectarDOMInfo.adminBarHeight + 1;	
				}

			} else {
				var $scrollTopDistance = $($hash).offset().top - nectarDOMInfo.adminBarHeight + 1;
			}

			if($('body[data-hhun="1"]').length > 0 && $('#header-outer[data-remove-fixed="1"]').length == 0) {
				//alter offset 
				if($('#header-outer.detached').length == 0 || $that.parents('.page-submenu[data-sticky="true"]').length > 0) 
					$scrollTopDistance = $scrollTopDistance + $headerNavSpace;

				//hide top header
				if($that.parents('.page-submenu[data-sticky="true"]').length > 0) { 
					$('#header-outer.detached').addClass('invisible');
					$('.page-submenu').addClass('header-not-visible').css('transform','translateY(0px)');
				}
			} 

			var $pageSubMenu = ($that.parents('.page-submenu[data-sticky="true"]').length > 0) ? $that.parents('.page-submenu').height() : 0;
			
			 if($('body.material').length > 0 && 
			 		$('#header-secondary-outer').length > 0 &&
			    $('body[data-hhun="1"]').length == 0 && 
			    $('#header-outer[data-remove-fixed="1"]').length == 0 && 
					!$('body').hasClass('mobile')) { 
						
						var $headerSecondary = $('#header-secondary-outer').height();
						
				} else {
						var $headerSecondary = 0;
				}

			nectar_scrollToY($scrollTopDistance - $pageSubMenu + $headerSecondary, 700, 'easeInOutQuint');
			

		},$timeoutVar);
		

		e.preventDefault();

	}

	if($hash == '#top') {
		//side widget area click
		if($(this).parents('#slide-out-widget-area').length > 0){
			$('#slide-out-widget-area .slide_out_area_close').trigger('click');
		}
	}


});



}




function searchResultMasonry() {
	var $searchContainer = $('#search-results');
	var $dividerNum = ($searchContainer.is('[data-layout="masonry-no-sidebar"]')) ? 4 : 3;
	
	$searchContainer.imagesLoaded(function(){
		
		$searchContainer.isotope({
			 itemSelector: '.result',
			 layoutMode: 'packery',
			 packery: { columnWidth: $('#search-results').width() / $dividerNum }
		});
		
		$searchContainer.find('article').css('opacity','1');
		
	});
				

	$(window).resize(function(){
		 $searchContainer.isotope({
				layoutMode: 'packery',
				packery: { columnWidth: $('#search-results').width() / $dividerNum }
		 });
	});
	
}

if($('body.search-results').length > 0 && $('#search-results article').length > 0 && $('#search-results[data-layout="list-no-sidebar"]').length == 0) { searchResultMasonry(); }



	//portfolio colors
	if($('.portfolio-items .col .style-3-alt').length > 0 || $('.portfolio-items .col .style-3').length > 0 || $('.portfolio-items .col .style-2').length > 0 || $('.portfolio-items .col .style-5').length > 0 ) {
		var portfolioColorCss = '';
		$('.portfolio-items .col').each(function(){
			var $titleColor = $(this).attr('data-title-color');
			var $subTitleColor = $(this).attr('data-subtitle-color');

			 if($titleColor.length > 0 ) { 
			 	portfolioColorCss += '.col[data-title-color="'+$titleColor+'"] .vert-center h3, .portfolio-items[data-ps="6"] .col[data-title-color="'+$titleColor+'"] .work-meta h4 { color: '+$titleColor+'!important; } ';
			 	portfolioColorCss += ' .portfolio-items[data-ps="8"] .col[data-title-color="'+$titleColor+'"] .line { background-color: '+$titleColor+'; }';
			 	portfolioColorCss += '.portfolio-items[data-ps="8"] .col[data-title-color="'+$titleColor+'"] .next-arrow line { stroke: '+$titleColor+'; } ';
			 }
			 if($subTitleColor.length > 0 ) portfolioColorCss += '.col[data-subtitle-color="'+$subTitleColor+'"] .vert-center p, .portfolio-items[data-ps="6"] .col[data-title-color="'+$titleColor+'"] .work-meta p { color: '+$subTitleColor+'; } ';
	
		});


		var head = document.head || document.getElementsByTagName('head')[0];
		var style = document.createElement('style');

			style.type = 'text/css';
		if (style.styleSheet){
		  style.styleSheet.cssText = portfolioColorCss;
		} else {
		  style.appendChild(document.createTextNode(portfolioColorCss));
		}

		head.appendChild(style);
	}

	//bottom controls 2
	$('body').on('mouseleave','.container-wrap[data-nav-pos="after_project_2"] #portfolio-nav ul li, .blog_next_prev_buttons[data-style="fullwidth_next_prev"] ul li',function(){
		$(this).addClass('mouse-leaving');
	});	

	// masonryPortfolio

	var $portfolio_containers = [];

	$('.portfolio-items:not(.carousel)').each(function(i){
		$portfolio_containers[i] = $(this);
	});

	function masonryPortfolioInit() {

		$portfolio_containers = [];
		$('.portfolio-items:not(.carousel)').each(function(i){
			$portfolio_containers[i] = $(this);
		});

		//// cache window
		var $window = jQuery(window);	
		
			
			$.each($portfolio_containers,function(i){

				
				//// start up isotope with default settings
				$portfolio_containers[i].imagesLoaded(function(){
					
					//verify smooth scorlling
					if( $smoothCache == true && $(window).width() > 690 && $('body').outerHeight(true) > $(window).height() && Modernizr.csstransforms3d && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)){ niceScrollInit(); $(window).trigger('resize') } 
					
					//transformns enabled logic
					var $isoUseTransforms = true;
					
					//Panr 
					if(!$('body').hasClass('mobile') && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/)) {

						$isoUseTransforms = true;
						
					}

					piVertCenter();

					//initial call to setup isotope
					var $layoutMode = ( $portfolio_containers[i].hasClass('masonry-items')) ? 'packery' : 'fitRows';
					var $startingFilter = ($portfolio_containers[i].attr('data-starting-filter') != '' && $portfolio_containers[i].attr('data-starting-filter') != 'default') ? '.' + $portfolio_containers[i].attr('data-starting-filter') : '*';

					reLayout();
					
					$portfolio_containers[i].addClass('isotope-activated');
					
					$portfolio_containers[i].isotope({
					  itemSelector : '.element',
					  filter: $startingFilter,
					  layoutMode: $layoutMode,
					  transitionDuration: '0.6s',
					  packery: {
						 gutter: 0
					  }
					}).isotope( 'layout' );
					
					
					if($startingFilter != '*'){
						$('.portfolio-filters ul a[data-filter="'+$startingFilter+'"], .portfolio-filters-inline ul a[data-filter="'+$startingFilter+'"]').click();
					}

					//call the reLayout to get things rollin'
					masonryZindex();
					setTimeout(function(){masonryZindex(); },800);
					
				
					
					//inside fwc fix
					if($portfolio_containers[i].parents('.full-width-content').length > 0) { setTimeout(function(){ fullWidthContentColumns(); },200);  }

					//fadeout the loading animation
					$('.portfolio-loading').stop(true,true).fadeOut(200);
					
					//fadeIn items one by one
					if($portfolio_containers[i].find('.inner-wrap').attr('data-animation') == 'none') {
						$portfolio_containers[i].find('.inner-wrap').removeClass('animated');
					} 

			
				});
				


			});//each
			
			if($portfolio_containers.length > 0) {
				$window.resize( reLayout );

				$window.smartresize( function(){
					setTimeout(masonryZindex,700);
				});
			}
			
		

	}

	masonryPortfolioInit();

	function portfolioLoadIn() {

		$($fullscreenSelector+'.portfolio-items').each(function(){

			var $portfolioOffsetPos = ($('#nectar_fullscreen_rows').length > 0) ? '200%' : '90%';

			if($(this).find('.inner-wrap').attr('data-animation') == 'none') return;
		
			$(this).find('.col').each(function(i){

				var $that = $(this);

				//loaded visible
				if($(this).visible(true) || $(this).parents('#nectar_fullscreen_rows').length > 0) {

					var $portfolioAnimationDelay = ($that.is('[data-masonry-type="photography"].masonry-items')) ? 90 : 115;
					$(this).delay($portfolioAnimationDelay *i).queue(function(next){
					    $(this).addClass("animated-in");
					    next();
					});

				} else {

					//not already visible
					var waypoint = new Waypoint({
		 			element: $that,
		 			 handler: function(direction) {
						
						if($that.parents('.wpb_tab').length > 0 && $that.parents('.wpb_tab').css('visibility') == 'hidden' || $that.hasClass('animated-in')) { 
						     waypoint.destroy();
						     return;
						}

						var $portfolioAnimationDelay = ($that.is('[data-masonry-type="photography"].masonry-items')) ? 85 : 100;
					
						setTimeout(function(){
							$that.addClass("animated-in");
						},$portfolioAnimationDelay * $that.attr('data-delay-amount'));
					
						
						waypoint.destroy();
					},
					offset: $portfolioOffsetPos

					}); //waypoint
				}

			}); //each
		}); //each
					
	}


	//perspective load in
	if($('.portfolio-items .inner-wrap[data-animation="perspective"]').length > 0 || $('.posts-container[data-load-animation="perspective"]').length > 0) {

		var lastScrollTop = $(window).scrollTop();

		$('.portfolio-items, .posts-container[data-load-animation="perspective"]').css('perspective-origin','50% '+ (lastScrollTop + $(window).height()) + 'px');

		requestAnimationFrame(updatePerspectiveOrigin);
		
		function updatePerspectiveOrigin() {

			var scrollTop = $(window).scrollTop();

			if (lastScrollTop === scrollTop) {
				requestAnimationFrame(updatePerspectiveOrigin);
				return;
			} else {
				lastScrollTop = scrollTop;
				$('.portfolio-items,.posts-container[data-load-animation="perspective"]').css('perspective-origin','50% '+ (lastScrollTop + $(window).height()) + 'px');
				requestAnimationFrame(updatePerspectiveOrigin);
			}
		}

	}

	var mediaQuerySize;
	function reLayout() {

		clearTimeout(clearIsoAnimation);
	    $('.portfolio-items .col').addClass('no-transition');
	    clearIsoAnimation = setTimeout(function(){  $('.portfolio-items .col').removeClass('no-transition'); },700); 

		var windowSize = $window.width();
		var masonryObj;
		var masonryObjHolder = [];


		//user defined cols
		var userDefinedColWidth;

		$.each($portfolio_containers,function(i,v){

			if( $portfolio_containers[i].attr('data-user-defined-cols') == 'span4') {
				userDefinedColWidth = 3
			} 
			
			else if( $portfolio_containers[i].attr('data-user-defined-cols') == 'span3') {
				userDefinedColWidth = 4
			} 
			
			var isFullWidth = $portfolio_containers[i].attr('data-col-num') == 'elastic';
			
			
			//chrome 33 approved method for getting column sizing
			if(window.innerWidth > 1600){
				
				if($portfolio_containers[i].hasClass('fullwidth-constrained')) {
					if($portfolio_containers[i].is('[data-masonry-type="photography"]')) {
						mediaQuerySize = 'three';
					} else {
						mediaQuerySize = 'four';
					}
	
				} else {
					if($portfolio_containers[i].hasClass('constrain-max-cols')) {
						mediaQuerySize = 'four';
					} else {
						mediaQuerySize = 'five';
					}
				}
				
			} else if(window.innerWidth <= 1600 && window.innerWidth > 1300){

				if($portfolio_containers[i].hasClass('fullwidth-constrained')) {
					if($portfolio_containers[i].is('[data-masonry-type="photography"]')) {
						mediaQuerySize = 'three';
					} else {
						mediaQuerySize = 'four';
					}
				} else {
					mediaQuerySize = 'four';
				}
			} else if(window.innerWidth <= 1300 && window.innerWidth > 990){
				
				if($portfolio_containers[i].hasClass('constrain-max-cols')) {
					mediaQuerySize = 'four';
				} else {
					mediaQuerySize = 'three';
				}
				
			} else if(window.innerWidth <= 990 && window.innerWidth > 470){
				mediaQuerySize = 'two';
			} else if(window.innerWidth <= 470){
				mediaQuerySize = 'one';
			}
			
			//boxed
			if($('#boxed').length > 0) {
				if(window.innerWidth > 1300){
					mediaQuerySize = 'four';
				} else if(window.innerWidth < 1300 && window.innerWidth > 990){
					
					if($portfolio_containers[i].hasClass('constrain-max-cols')) {
						mediaQuerySize = 'four';
					} else {
						mediaQuerySize = 'three';
					}

				} else if(window.innerWidth < 990){
					mediaQuerySize = 'one';
				}
				
			}
			
			//change masonry columns depending on screen size
			var colWidth;
			
			switch (mediaQuerySize) {
				case 'five':
					(isFullWidth) ? colWidth = 5 : colWidth = userDefinedColWidth;
					//change cols for photography
					if(isFullWidth && $portfolio_containers[i].is('[data-masonry-type="photography"]')) colWidth = 6;

					masonryObj = { columnWidth: Math.floor($portfolio_containers[i].width() / parseInt(colWidth)) };
				break;
				
				case 'four':
					(isFullWidth) ? colWidth = 4 : colWidth = userDefinedColWidth;
					//change cols for photography
					if(isFullWidth && $portfolio_containers[i].is('[data-masonry-type="photography"]')) colWidth = 5;

					masonryObj = { columnWidth: Math.floor($portfolio_containers[i].width() / parseInt(colWidth)) };
				break;
				
				case 'three':
					(isFullWidth) ? colWidth = 3 : colWidth = userDefinedColWidth;
					//change cols for photography
					if(isFullWidth && $portfolio_containers[i].is('[data-masonry-type="photography"]')) colWidth = 4;
					
					masonryObj = { columnWidth: Math.floor($portfolio_containers[i].width() / parseInt(colWidth)) };
				break;
				
				case 'two':
					masonryObj = { columnWidth: Math.floor($portfolio_containers[i].width() / 2) };
				break;
				
				case 'one':
					masonryObj = { columnWidth: Math.floor($portfolio_containers[i].width() / 1) };
				break;
			}


			 //set widths
			 portfolioItemWidths(i,v);
			

			//sizing for large items
			if(!$portfolio_containers[i].is('[data-bypass-cropping="true"]')) {
				
				if( $portfolio_containers[i].find('.col.elastic-portfolio-item[class*="regular"]:visible').length > 0 || $portfolio_containers[i].find('.col.elastic-portfolio-item[class*="wide"]:visible').length > 0 || $portfolio_containers[i].find('.col.elastic-portfolio-item[class*="tall"]:visible').length > 0 || $portfolio_containers[i].find('.col.elastic-portfolio-item[class*="wide_tall"]:visible').length > 0) {

					var $gutterSize = ($portfolio_containers[i].is('[data-gutter*="px"]') && $portfolio_containers[i].attr('data-gutter').length > 0 && $portfolio_containers[i].attr('data-gutter') != 'none') ? parseInt($portfolio_containers[i].attr('data-gutter')) : 0;
					var multipler = (window.innerWidth > 470) ? 2 : 1;

					//reset height for calcs
					$itemClassForSizing = 'regular';

					if($portfolio_containers[i].find('.col.elastic-portfolio-item[class*="regular"]:visible').length == 0 && $portfolio_containers[i].find('.col.elastic-portfolio-item.wide:visible').length > 0) {
						$itemClassForSizing = 'wide';
					} else if($portfolio_containers[i].find('.col.elastic-portfolio-item[class*="regular"]:visible').length == 0 && $portfolio_containers[i].find('.col.elastic-portfolio-item.wide_tall:visible').length > 0) {
						$itemClassForSizing = 'wide_tall';
						multipler = 1;
					} else if($portfolio_containers[i].find('.col.elastic-portfolio-item[class*="regular"]:visible').length == 0 && $portfolio_containers[i].find('.col.elastic-portfolio-item.tall:visible').length > 0) {
						$itemClassForSizing = 'tall';
						multipler = 1;
					}

				    $portfolio_containers[i].find('.col.elastic-portfolio-item.'+$itemClassForSizing+' img').css('height','auto');

					var tallColHeight = $portfolio_containers[i].find('.col.elastic-portfolio-item.'+$itemClassForSizing+':visible img').height();
					
					 $portfolio_containers[i].find('.col.elastic-portfolio-item[class*="tall"] img, .col.elastic-portfolio-item.wide img, .col.elastic-portfolio-item.regular img').removeClass('auto-height');
					 $portfolio_containers[i].find('.col.elastic-portfolio-item[class*="tall"] img:not(.custom-thumbnail)').css('height',(tallColHeight*multipler) + ($gutterSize*2));

					 if($itemClassForSizing == 'regular' || $itemClassForSizing == 'wide') {
					 	$portfolio_containers[i].find('.col.elastic-portfolio-item.wide img:not(.custom-thumbnail), .col.elastic-portfolio-item.regular img:not(.custom-thumbnail)').css('height',tallColHeight);
					 } else {
					 	$portfolio_containers[i].find('.col.elastic-portfolio-item.wide img:not(.custom-thumbnail), .col.elastic-portfolio-item.regular img:not(.custom-thumbnail)').css('height',(tallColHeight/2) - ($gutterSize*2));
					 }

					 $portfolio_containers[i].find('.col.elastic-portfolio-item[class*="tall"] .parallaxImg').css('height',(tallColHeight*multipler) + parseInt($portfolio_containers[i].find('.col.elastic-portfolio-item').css('padding-bottom'))*2 );
					 
					 if($itemClassForSizing == 'regular' || $itemClassForSizing == 'wide') {
						 $portfolio_containers[i].find('.col.elastic-portfolio-item.regular .parallaxImg, .col.elastic-portfolio-item.wide .parallaxImg').css('height',tallColHeight);
					} else {
					 	 $portfolio_containers[i].find('.col.elastic-portfolio-item.regular .parallaxImg, .col.elastic-portfolio-item.wide .parallaxImg').css('height',(tallColHeight/2) - ($gutterSize*2));
					 }

				} else {
					$portfolio_containers[i].find('.col.elastic-portfolio-item[class*="tall"] img, .col.elastic-portfolio-item.wide img, .col.elastic-portfolio-item.regular img').addClass('auto-height');
				}
			
			} //bypass cropping option

			//non masonry
			if($portfolio_containers[i].hasClass('no-masonry') && $portfolio_containers[i].find('.col:first:visible').length > 0 && $portfolio_containers[i].parents('.wpb_gallery').length == 0){
			  
				//skip style 9
				if( !$portfolio_containers[i].is('[data-ps="9"]') && !$portfolio_containers[i].is('[data-bypass-cropping="true"]') ) {

				   	//reset height for calcs
			   	   $portfolio_containers[i].find('.col img').css('height','auto');
			   	   var tallColHeight = $portfolio_containers[i].find('.col:first:visible img').height();
			   	   $portfolio_containers[i].find('.col img:not(.custom-thumbnail)').css('height',tallColHeight);
			   	   $portfolio_containers[i].find('.col .parallaxImg').css('height',tallColHeight);
				 }
				 
			}
	


			masonryObjHolder[i] = masonryObj;
			
			if($portfolio_containers[i].isotope()) $portfolio_containers[i].isotope( 'layout' ); 
				
			

		}); //each
	
	}

	function portfolioItemWidths(i,v) {
		// passing each index value where function is called first to avoid nested loops

		 		var isFullWidth = $portfolio_containers[i].attr('data-col-num') == 'elastic';

		 		if(isFullWidth) { 

				 	var $colSize = 4;
				 	var $mult = (mediaQuerySize == 'one') ? 1 : 2;
				 	if(mediaQuerySize == 'five') $colSize = 5;
				 	if(mediaQuerySize == 'four') $colSize = 4;
				 	if(mediaQuerySize == 'three') $colSize = 3;
				 	if(mediaQuerySize == 'two') $colSize = 2;
				 	if(mediaQuerySize == 'one') $colSize = 1;
				 	if($(v).is('[data-ps="6"]') && $colSize == 5) $colSize = 4;

				 	//photography
				 	if(isFullWidth && $portfolio_containers[i].is('[data-masonry-type="photography"]') && !$portfolio_containers[i].hasClass('no-masonry')) {
				 		if(mediaQuerySize == 'five') $colSize = 6;
				 		if(mediaQuerySize == 'four') $colSize = 5;
				 		if(mediaQuerySize == 'three') $colSize = 4;
				 	}
				 	
				 	if($(v).width() % $colSize == 0) {
					 	$(v).find('.elastic-portfolio-item:not(.wide):not(.wide_tall)').css('width',Math.floor($(v).width()/$colSize) +'px');
					 	$(v).find('.elastic-portfolio-item.wide, .elastic-portfolio-item.wide_tall').css('width',Math.floor($(v).width()/$colSize*$mult) +'px');
					 } else {
					 	var $loopEndNum = ($(window).width() > 1000) ? 6 : 3;
					 	if($portfolio_containers[i].hasClass('fullwidth-constrained') && $(window).width() > 1000) $loopEndNum = 4;
					 	//find closest number to give 0
					 	for(var i = 1; i<$loopEndNum; i++) {

					 		if(($(v).width() - i) % $colSize == 0) {
					 			$(v).find('.elastic-portfolio-item:not(.wide):not(.wide_tall)').css('width',($(v).width()- i)/$colSize +'px');
					 			$(v).find('.elastic-portfolio-item.wide, .elastic-portfolio-item.wide_tall').css('width',($(v).width()-i)/$colSize*$mult +'px');
					 		}

					 	}
					 }

				} // isFullWidth

	}

	//z-index for masonry
	function masonryZindex(){

		//escape if no browser support
		if($('body .portfolio-items:not(".carousel") > .col').length > 0 && $('body .portfolio-items:not(".carousel") > .col').offset().left) {

			$('body .portfolio-items:not(".carousel")').each(function(){

				var $coords = {};
				var $zindexRelation = {};
				var $that = $(this);

				$(this).find('> .col').each(function(){
					var $itemOffset = $(this).offset();
					$itemOffset = $itemOffset.left;

					$coords[$(this).index()] = $itemOffset;
					$(this).css('z-index',Math.abs(Math.floor($(this).offset().left/20)));
				});

				var $corrdsArr = $.map($coords, function (value) { return value; });

				$corrdsArr = removeDuplicates($corrdsArr);
				$corrdsArr.sort(function(a,b){return a-b});

				for(var i = 0; i < $corrdsArr.length; i++){
					$zindexRelation[$corrdsArr[i]] = i; 
				}
		
				$.each($coords,function(k,v){
					
					var $zindex;
					var $coordCache = v;
					$.each($zindexRelation,function(k,v){
						if($coordCache == k) {
							$zindex = v;
						}
					});
				
					$that.find('> .col:eq('+k+')').attr('data-delay-amount',$zindex);
				});

				
			});
			
			
			
		}


	}

	function blogMasonryZindex(){
	
		//escape if no browser support
		if($('body .post-area .masonry-blog-item').length > 0 && $('body .post-area .masonry-blog-item').offset().left) {
		
			$('body .post-area.masonry').each(function(){
				
				var $coords = {};
				var $zindexRelation = {};
				var $that = $(this);

				$(this).find('.masonry-blog-item').each(function(){
					var $itemOffset = $(this).offset();
					$itemOffset = $itemOffset.left;

					$coords[$(this).index()] = $itemOffset;
					$(this).css('z-index',Math.abs(Math.floor($(this).offset().left/20)));
				});

				var $corrdsArr = $.map($coords, function (value) { return value; });

				$corrdsArr = removeDuplicates($corrdsArr);
				$corrdsArr.sort(function(a,b){return a-b});
		
				for(var i = 0; i < $corrdsArr.length; i++){
					$zindexRelation[$corrdsArr[i]] = i*1; 
				}
		
				$.each($coords,function(k,v){
					
					var $zindex;
					var $coordCache = v;
					$.each($zindexRelation,function(k,v){
						if($coordCache == k) {
							$zindex = v;
						}
					});
		
					$that.find('.masonry-blog-item:eq('+k+')').css('z-index',$zindex).attr('data-delay-amount',$zindex);
					
				});
			
			});
			
		}
		
	}
	
	function matrixToArray(matrix) {
	    return matrix.substr(7, matrix.length - 8).split(', ');
	}
	
	function removeDuplicates(inputArray) {
        var i;
        var len = inputArray.length;
        var outputArray = [];
        var temp = {};

        for (i = 0; i < len; i++) {
            temp[inputArray[i]] = 0;
        }
        for (i in temp) {
            outputArray.push(i);
        }
        return outputArray;
    }

    //// filter items when filter link is clicked
	var clearIsoAnimation = null;
	var $checkForScrollBar = null;


	//number portfolios so multiple sortable ones can work easily on same page
	$('.portfolio-items:not(".carousel")').each(function(i){
		$(this).attr('instance',i);
		$(this).parent().parent().find('div[class^=portfolio-filters]').attr('instance',i);
	});

    function isoClickFilter(){
		 var $timeout;		 
		 if(window.innerWidth > 690 && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|IEMobile|Opera Mini)/)){
		 	
			  
			 clearTimeout($timeout);
			 $timeout = setTimeout(function(){masonryZindex();  },600);

			  
		 }
		  
		  var selector = $(this).attr('data-filter');
		  var $instance = $(this).parents('div[class^=portfolio-filters]').attr('instance');

		  $.each($portfolio_containers,function(i){
		  	if($portfolio_containers[i].attr('instance') == $instance) { 
		  		$portfolio_containers[i].isotope({ filter: selector }).attr('data-current-cat',selector);

		  		//fade in all incase user hasn't scrolled down yet
		  		if($portfolio_containers[i].find('.inner-wrap[data-animation="none"]').length == 0) {
			   		$portfolio_containers[i].find('.col').addClass('animated-in');
				}
		  	}
		  });




		  //active classes
		  $(this).parent().parent().find('li a').removeClass('active');
		  $(this).addClass('active');
		  
		  //update pp
		  if($('.portfolio-items a[rel^="prettyPhoto"]').length > 0) {
		  	setTimeout(updatePrettyPhotoGallery,170);
		  }

		  else {
		  	setTimeout(updateMagPrettyPhotoGallery,170);
		  }

		  return false;
	}

	////filter event
	$('body').on('click','.portfolio-filters ul li a, .portfolio-filters-inline ul li a', isoClickFilter);


	function updatePrettyPhotoGallery(){
		$('.portfolio-items').each(function(){

			if($(this).find('a[rel^="prettyPhoto"]').length > 0) {

			var $unique_id = Math.floor(Math.random()*10000);
			var $currentCat = $(this).attr('data-current-cat');
			$(this).find('.col'+$currentCat).find('a[rel^="prettyPhoto"]').attr('rel','prettyPhoto['+$unique_id+'_sorted]');
			
			} 
			
		});
	}

	function updateMagPrettyPhotoGallery(){
		$('.portfolio-items').each(function(){

			var $currentCat = $(this).attr('data-current-cat');
			var $unique_id = Math.floor(Math.random()*10000);
			
			if($(this).is('[data-lightbox-only="true"]')){
				
					$(this).find('.col').each(function(){
		
						$(this).find('a.gallery').removeClass('gallery').removeClass('magnific');
						
						if($(this).is($currentCat)) {
							
							//parallax styles
							if($(this).find('.parallaxImg-wrap').length > 0) {
								
									if($('body[data-ls="fancybox"]').length > 0) {
										$(this).find('.work-item > a').attr('data-fancybox','group_'+$unique_id);
									} else {
										$(this).find('.work-item > a').addClass('gallery').addClass('magnific');
									}
									
							} else {
								//others
								
									if($('body[data-ls="fancybox"]').length > 0) {
										$(this).find('.work-item a').attr('data-fancybox','group_'+$unique_id);
									} else {
										$(this).find('.work-info a').addClass('gallery').addClass('magnific');
									}
									
							}
							
						}
		
					});
				
			}
			
			else if ($(this).find('.work-item.style-1').length > 0){
				
					$(this).find('.col').each(function(){
		
						$(this).find('a.gallery').removeClass('gallery').removeClass('magnific');
						
						if($(this).is($currentCat)) {
							
								if($('body[data-ls="fancybox"]').length > 0) {
									$(this).find('.work-info .vert-center a:first-of-type').attr('data-fancybox','group_'+$unique_id);
								} 
								else {
									$(this).find('.work-info .vert-center a:first-of-type').addClass('gallery').addClass('magnific');
								}
								
						}
		
					});
			}
			
		
			
		});
	}



	var $blog_containers = [];

	$('.posts-container').each(function(i){
		$blog_containers[i] = $(this);
	});

  function masonryBlogInit() {

		//// cache window
		var $window = jQuery(window);	
		
			
		$.each($blog_containers,function(i){

			
				if($blog_containers[i].parent().hasClass('masonry') && !$blog_containers[i].parent().hasClass('auto_meta_overlaid_spaced')) { 
					
					$blog_containers[i].find('article').addClass('masonry-blog-item');
					
					if($blog_containers[i].parents('.masonry.classic').length > 0) {
						$blog_containers[i].find('article').prepend('<span class="bottom-line"></span>');
					}
					
					//move the meta to the bottom
					$blog_containers[i].find('article').each(function(){
						
						var $metaClone = $(this).find('.post-meta').clone();

						$(this).find('.post-meta').remove();

						if($blog_containers[i].parents('.post-area.meta_overlaid').length > 0){
							$(this).find('.post-header h2').after($metaClone);
						} else {
							$(this).find('.content-inner').after($metaClone);
						}
						
						$blog_containers[i].addClass('meta-moved');
						
					});
				
					
					if($blog_containers[i].parent().hasClass('masonry') && $blog_containers[i].parents('.blog-fullwidth-wrap').length > 0){

						//page header animation fix
						if( $blog_containers[i].parents('.wpb_row').length > 0 ) $blog_containers[i].parents('.wpb_row').css('z-index',100);

						if(!$blog_containers[i].parent().hasClass('meta_overlaid') && !$blog_containers[i].parent().hasClass('auto_meta_overlaid_spaced')) {

							if($blog_containers[i].parent().hasClass('classic_enhanced')) {
								$blog_containers[i].parent().parents('.full-width-content').css({
									'padding' : '0px 0.2% 0px 2.4%'
								});
							} else {
								$blog_containers[i].parent().parents('.full-width-content').css({
									'padding' : '0px 0.2% 0px 3.2%'
								});
							}
							
						} else {
							$blog_containers[i].parent().parents('.full-width-content').addClass('meta-overlaid');
							$('.container-wrap').addClass('meta_overlaid_blog');
						}

						fullWidthSections(); 
					}
					
					var $cols = 3;
					var $element = $blog_containers[i];
					
					if($blog_containers[i].find('img').length == 0) $element = $('<img />');
					
					imagesLoaded($element,function(instance){
						
						var $multiplier;
						
						if($('body').hasClass('mobile') || $blog_containers[i].parents('.post-area').hasClass('span_9')) {
							$cols = 2;
						}

						//set img as BG if masonry classic enhanced
						if($blog_containers[i].parent().hasClass('classic_enhanced')){
							$blog_containers[i].find('.large_featured.has-post-thumbnail .post-featured-img, .wide_tall.has-post-thumbnail .post-featured-img').each(function(){
								var $src = $(this).find('img').attr('src');
								$(this).css('background-image','url('+$src+')');
							});

							$blog_containers[i].find('.large_featured .nectar-flickity, .wide_tall .nectar-flickity').each(function(){

								$(this).find('.cell').each(function(){
									var $src = $(this).find('img').attr('src');
									$(this).css('background-image','url('+$src+')');
								});
								
							});
						}

						$cols = blogColumnNumbCalcs($blog_containers[i]);
						blogHeightCalcs($blog_containers[i], $cols);

						if($blog_containers[i].parents('.post-area.meta_overlaid').length > 0) {
							$blog_containers[i].isotope({
							   itemSelector: 'article',
							   transitionDuration: '0s',
							   layoutMode: 'packery',
							   packery: { 
							   	 gutter: 0
							   	}
							}).isotope( 'layout' );

						   
						} else if ($blog_containers[i].parents('.auto_meta_overlaid_spaced').length > 0) {
							
							$multiplier = .025;
							if($blog_containers[i].parents('.blog-fullwidth-wrap').length > 0) {
									$multiplier = .02;
							}
							
							$blog_containers[i].isotope({
								layoutMode: 'packery',
								transitionDuration: '0s',
								packery: {
									 gutter: 0
								}
						 });
						 
						}  else {
						   if($blog_containers[i].parent().hasClass('classic_enhanced')) {
							   	if($blog_containers[i].parents('.span_9.masonry').length == 0) {
							   		$multiplier = (window.innerWidth >= 1600) ? .015 : .02;
							   	} else {
							   		$multiplier = .04;
							    } 
						    }
							else {
 								 		$multiplier = ($blog_containers[i].parents('.span_9.masonry').length == 0) ? .03: .055;
 							 }
						   
							$blog_containers[i].isotope({
							   itemSelector: 'article',
							   transitionDuration: '0s',
							   layoutMode: 'packery',
							   packery: { 
							   	 gutter: $blog_containers[i].parents('.post-area').width()*$multiplier
							   	}
							}).isotope( 'layout' );
						}

						blogLoadIn($blog_containers[i]);
						flickityBlogInit();
						
						$(window).trigger('resize');

							
					});
					
					$(window).resize(function(){
						

					   //size all items in grid 
					   //sizing for large items
						 if(typeof $blog_containers[i] !== 'undefined') { 
							  
								var $multiplier;
								
							  $cols = blogColumnNumbCalcs($blog_containers[i]);
								blogHeightCalcs($blog_containers[i], $cols);
								
								if($blog_containers[i].parents('.post-area.meta_overlaid').length > 0) {
								
								    $blog_containers[i].isotope({
								      layoutMode: 'packery',
								      packery: {
								      	 gutter: 0
								      }
								   });
								} else if ($blog_containers[i].parents('.auto_meta_overlaid_spaced').length > 0) {
									
									$multiplier = .025;
									if($blog_containers[i].parents('.blog-fullwidth-wrap').length > 0) {
											$multiplier = .02;
									}
									
									$blog_containers[i].isotope({
										layoutMode: 'packery',
										transitionDuration: '0s',
										packery: {
											 gutter: 0
										}
								 });
								} else {
								   
								   if($blog_containers[i].parent().hasClass('classic_enhanced')) {
								   		if($blog_containers[i].parents('.span_9.masonry').length == 0) {
									   		$multiplier = (window.innerWidth >= 1600) ? .015 : .02;
									   	} else {
									   		$multiplier = .04;
									    } 
								   } else {
										 		$multiplier = ($blog_containers[i].parents('.span_9.masonry').length == 0) ? .03: .055;
												if($blog_containers[i].parents('.blog-fullwidth-wrap').length > 0) {
														$multiplier = .02;
												}
									 } 
								  
								   $blog_containers[i].isotope({
								   	layoutMode: 'packery',
								      packery: { 
								      	gutter: $blog_containers[i].parents('.post-area').width()*$multiplier
								      }
								   });
								}
								
							} // if $blog_containers[i]

					});
					
					
			    } else {
			    	blogLoadIn($blog_containers[i]);
			    }

		});
		
		//set z-index / animation order only once
		setTimeout(blogMasonryZindex,700);
		$window.smartresize( function(){
			setTimeout(blogMasonryZindex,700);
		});
		
	}
	
	masonryBlogInit();

	function blogLoadIn(post_container){


		if(post_container.attr('data-load-animation') == 'none') {
		
			post_container.find('.inner-wrap').removeClass('animated');

		} else {

			post_container.find('article').each(function(i){

				//loaded visible
				if($(this).visible(true)) {

					$(this).delay(110*i).queue(function(next){
					    $(this).addClass("animated-in");
					    next();
					});


				} else {
					//not already visible
					var $that = $(this);
					var waypoint = new Waypoint({

			 			element: $that,
			 			 handler: function(direction) {
							
							setTimeout(function(){
							    $that.addClass("animated-in");
							},80*$that.attr('data-delay-amount'));

							waypoint.destroy();
						},
						offset: '90%'

					}); 
				}


			});
	
			
		}

	}

	function blogHeightCalcs($posts_container, cols) {
		if( $posts_container.parent().hasClass('meta_overlaid') && $posts_container.find('article[class*="regular"]').length > 0) {

			//widths
			$.each($posts_container,function(i,v){
			 	var $colSize = 4;
			 	var $mult = (cols == 1) ? 1 : 2;

			 	//check if higher than IE9 -- bugs out with width calc
			 	if($('html.no-csstransitions').length == 0) {
			 		$(v).find('article[class*="regular"]').css('width',Math.floor($(v).width()/cols) +'px');
			 		$(v).find('article[class*="tall"]').css('width',Math.floor($(v).width()/cols*$mult) +'px');
			 	} else {
			 		$('.post-area.masonry').css('width','100%');
			 	}
			 	
			 	
			 });

			   //reset height for calcs
			   $posts_container.find('article[class*="regular"] img').css('height','auto');

			   var tallColHeight = Math.ceil($posts_container.find('article[class*="regular"]:not(".format-link"):not(".format-quote") img').first().height());
			   var multipler = (window.innerWidth > 690) ? 2 : 1 ;
			   $posts_container.find('article[class*="tall"] img, .article.wide img, article.regular img').removeClass('auto-height');
			   $posts_container.find('article[class*="tall"] img').css('height',(tallColHeight*multipler));
			   $posts_container.find('article[class*="regular"] img').css('height',(tallColHeight));
			   //quote/links
			   $posts_container.find('article.regular.format-link,article.regular.format-quote').each(function(){

			   		if(window.innerWidth > 690) {
			   			$(this).css({
			  	 			'height': tallColHeight
			   			});
			   		} else {
			   			$(this).css({
			  	 			'height': 'auto'
			   			});			 		
			   		}
			  	 	
			   	});


		} else {
			$posts_container.find('article[class*="tall"] img, article.regular img').addClass('auto-height');
		}


		if( $posts_container.parent().hasClass('classic_enhanced') && $posts_container.find('article[class*="regular"]').length > 0) {
			
			if($(window).width() > 690 ) {
				classicEnhancedSizing($posts_container.find('article:not(.large_featured):not(.wide_tall)'));
			}
			else { 
				classicEnhancedSizing($posts_container.find('article:not(.wide_tall)'));
			}

			var tallColHeight = ($posts_container.find('article[class*="regular"]:not(".format-link"):not(".format-quote").has-post-thumbnail').first().length > 0) ? Math.ceil($posts_container.find('article[class*="regular"]:not(".format-link"):not(".format-quote").has-post-thumbnail').first().css('height','auto').height()) : 600;

			if($(window).width() > 690 ) {
				$posts_container.find('article.large_featured, article.regular, article[class*="wide_tall"]').css('height',(tallColHeight));
			}
			else {
				$posts_container.find('article.regular, article[class*="wide_tall"]').css('height',(tallColHeight));
			}

		//for when no regular articles exist	
		} else if( $posts_container.parent().hasClass('classic_enhanced') && $posts_container.find('article[class*="regular"]').length == 0) {
			var tallColHeight = ($posts_container.find('article[class*="regular"]:not(".format-link"):not(".format-quote").has-post-thumbnail').first().length > 0) ? Math.ceil($posts_container.find('article[class*="regular"]:not(".format-link"):not(".format-quote").has-post-thumbnail').first().css('height','auto').height()) : 600;

			if($(window).width() > 690 ) {
				$posts_container.find('article.large_featured, article.regular, article[class*="wide_tall"]').css('height',(tallColHeight));
			}
			else {
				$posts_container.find('article.regular, article[class*="wide_tall"]').css('height',(tallColHeight));
			}
		}

		//IE9 fix
		if($('html.no-csstransitions').length > 0) 		
			$('.post-area.masonry').css('width','100%');
			 	
			 	
	}

	function classicEnhancedSizing(elements) {

		var tallestCol = 0;
		elements.find('.article-content-wrap').css('height','auto');
		elements.filter('.has-post-thumbnail').each(function(){
			($(this).find('.article-content-wrap').outerHeight(true) > tallestCol) ? tallestCol = $(this).find('.article-content-wrap').outerHeight(true) : tallestCol = tallestCol;
		});	
		
		elements.filter('.has-post-thumbnail').find('.article-content-wrap').css('height',(tallestCol));

	}


	function blogStickySS() {
		$('#sidebar[data-nectar-ss="true"], #sidebar[data-nectar-ss="1"]').each(function(){

			//padding from top of screen
			var $ssExtraTopSpace = 50;

			if($('#header-outer[data-remove-fixed="0"]').length > 0 && $('body[data-hhun="1"]').length == 0 && $('#header-outer[data-format="left-header"]').length == 0) { 
			 	$ssExtraTopSpace += $('#header-outer').outerHeight();	
				
				//resize effect
				if($('#header-outer[data-shrink-num][data-header-resize="1"]').length > 0 ) {
					 var shrinkNum = 6;		
					 var headerPadding2 = parseInt($('#header-outer').attr('data-padding')) - parseInt($('#header-outer').attr('data-padding'))/1.8;
					 shrinkNum = $('#header-outer').attr('data-shrink-num');
					 $ssExtraTopSpace -= shrinkNum;
					 $ssExtraTopSpace -= headerPadding2;
				}
				
				//condesne
				if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
					
						var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
						var $secondaryHeader = $('#header-secondary-outer');
						
						$ssExtraTopSpace = 50;
						$ssExtraTopSpace += $('#header-outer').height() - (parseInt($headerSpan9.position().top) - parseInt($('#header-outer #logo').css('margin-top')) ) - parseInt(nectarDOMInfo.secondaryHeaderHeight);
				}
				

			}

			if($('#wpadminbar').length > 0) {
				$ssExtraTopSpace += $('#wpadminbar').outerHeight();
			}

 			if($('#header-outer').attr('data-using-secondary') == '1') {
				$ssExtraTopSpace += $('#header-secondary-outer').outerHeight();
			}
			
			if($(this).parents('.wpb_widgetised_column').length > 0) {
				
				if($('body.vc_editor').length > 0) {

				} else {
						$(this).parents('.wpb_column').theiaStickySidebar({
							additionalMarginTop: $ssExtraTopSpace,
							updateSidebarHeight: false
						});
				}
				
			} 
			
			else {
				$(this).theiaStickySidebar({
			      additionalMarginTop: $ssExtraTopSpace,
						updateSidebarHeight: false
			    });
			}
			
		});
	}

	if( $().theiaStickySidebar ) {
		blogStickySS();
	}

	var blogMediaQuerySize;
	function blogColumnNumbCalcs(post_container){
		
			 var $cols = 3;
				
		   if($('body').hasClass('mobile') && window.innerWidth < 990 || post_container.parents('.post-area').hasClass('span_9') && post_container.parents('.post-area.meta_overlaid').length == 0) {
			   $cols = 2;
		   } else if( post_container.parents('.post-area').hasClass('full-width-content') || post_container.parents('.post-area').parent().hasClass('full-width-content') && $('#boxed').length == 0 || post_container.parents('.post-area.meta_overlaid').length > 0 ){
		   		
				var windowSize = $(window).width();

				
				if(window.innerWidth >= 1600){
					blogMediaQuerySize = (post_container.parents('.post-area.meta_overlaid').length > 0) ? 'four' :'five';
				} else if(window.innerWidth < 1600 && window.innerWidth >= 1300){
					blogMediaQuerySize = 'four';
				} else if(window.innerWidth < 1300 && window.innerWidth >= 990){
					blogMediaQuerySize = (post_container.parents('.post-area.meta_overlaid').length > 0) ? 'four' :'three';
				} else if(window.innerWidth < 990 && window.innerWidth >= 470){
					blogMediaQuerySize = 'two';
				} else if(window.innerWidth < 470){
					blogMediaQuerySize = (post_container.parents('.post-area.meta_overlaid').length > 0) ? 'two' :'one';
				}
			
				
				//boxed
				if($('#boxed').length > 0) {
					if(window.innerWidth > 1300){
						blogMediaQuerySize = 'four';
					} else if(window.innerWidth < 1300 && window.innerWidth > 990){
						blogMediaQuerySize = (post_container.parents('.post-area.meta_overlaid').length > 0) ? 'four' :'three';
					} else if(window.innerWidth < 990){
						blogMediaQuerySize = (post_container.parents('.post-area.meta_overlaid').length > 0) ? 'two' :'one';
					}
					
				}
				
				
				switch (blogMediaQuerySize) {
					case 'five':
						$cols = 5;
					break;
					
					case 'four':
						$cols = 4;
					break;
					
					case 'three':
						$cols = 3;
					break;
					
					case 'two':
						$cols = 2;
					break;
					
					case 'one':
						$cols = 1;
					break;
				}
		   		
			
		   } else {

		   	   $cols = 3;
		   }

		   return $cols;

	}


var shrinkNum = 6;
		
if($('#header-outer[data-shrink-num]').length > 0 ) shrinkNum = $('#header-outer').attr('data-shrink-num');

headerPadding2 = headerPadding - headerPadding/1.8;

$('body').on('click','.section-down-arrow',function(){
	
	if($(this).parents('.nectar-box-roll').length > 0) return false;

	var $currentSection = $(this).parents('#page-header-bg');
	var $topDistance = $currentSection.height();
	var $offset = ($currentSection.parents('.first-section').length == 0 || $('body[data-transparent-header="false"]').length > 0) ? $currentSection.offset().top : 0;
	var $bodyBorderSize = ($('.body-border-top').length > 0 && $(window).width() > 1000) ? $('.body-border-top').height(): 0;
	var $headerNavSpace = ($('body[data-header-format="left-header"]').length > 0) ? 0 : $('#header-space').height();
	
	var $materialSecondary = 0;
	if($('body.material').length > 0 && $('#header-secondary-outer').length > 0) { $materialSecondary = $('#header-secondary-outer').height(); }
	
	if($('body[data-permanent-transparent="1"]').length == 0) {
		//regular
		if(!$('body').hasClass('mobile')){
			
			if($('body[data-hhun="1"]').length > 0 && $('#header-outer[data-remove-fixed="1"]').length == 0) {
				$('body,html').stop().animate({
					scrollTop: parseInt($topDistance) + $offset + 2 - $bodyBorderSize*2
				},1000,'easeInOutCubic')
			} 
			else {
				
				$resize = ($('#header-outer[data-header-resize="0"]').length > 0) ? 0 : parseInt(shrinkNum) + headerPadding2*2;
				if($('#header-outer[data-remove-fixed="1"]').length > 0) { 
					$headerNavSpace = 0;
					$offset = 0;
				}
					
				//condesne
				if($('body.mobile').length == 0 && $('#header-outer[data-condense="true"]').length > 0) {
					
						var $headerSpan9 = $('#header-outer[data-format="centered-menu-bottom-bar"] header#top .span_9');
						var $secondaryHeader = $('#header-secondary-outer');
						
						$headerNavSpace = $('#header-outer').height() - (parseInt($headerSpan9.position().top) - parseInt($('#header-outer #logo').css('margin-top')) ) - parseInt(nectarDOMInfo.secondaryHeaderHeight);
				}
				
				
				$('body,html').stop().animate({
					scrollTop: parseInt($topDistance - $headerNavSpace) +$resize + 3 + $offset + $materialSecondary
				},1000,'easeInOutCubic')

			}
			
		} else {
			$scrollPos = ($('#header-outer[data-mobile-fixed="1"]').length > 0) ? parseInt($topDistance) - $headerNavSpace + parseInt($currentSection.offset().top) + 2 : parseInt($topDistance) + parseInt($currentSection.offset().top) + 2;
			$('body,html').stop().animate({
				scrollTop: $scrollPos - $bodyBorderSize*2
			},1000,'easeInOutCubic')
		}
	} else {
		//permanent transparent
		$('body,html').stop().animate({
			scrollTop: parseInt($topDistance) + parseInt($currentSection.offset().top) + 2 - $bodyBorderSize*2
		},1000,'easeInOutCubic')
	}
	return false;
});


$('body').on('mouseover','.post-area.featured_img_left .grav-wrap .text a, .masonry.material .masonry-blog-item .grav-wrap .text a, .blog-recent[data-style="material"] .grav-wrap .text a',function(){
	$(this).parents('.grav-wrap').find('img').addClass('hovered');
});
$('body').on('mouseleave','.post-area.featured_img_left .grav-wrap .text a, .masonry.material .masonry-blog-item .grav-wrap .text a, .blog-recent[data-style="material"] .grav-wrap .text a',function(){
	$(this).parents('.grav-wrap').find('img').removeClass('hovered');
});  



/*-------------------------------------------------------------------------*/
/*	7.	Cross Browser Fixes
/*-------------------------------------------------------------------------*/	
	 
	 function crossBrowserFixes() {
	
		 
		//Fix current class in menu 
		if ($("body").hasClass("single-portfolio") || $('body').hasClass("error404") || $('body').hasClass("search-results")) {   
			$("li").removeClass("current_page_parent").removeClass("current-menu-ancestor").removeClass('current_page_ancestor');   
		}
		
		//for users updating to 8 with a custom, old header template file
		if($('html.js').length == 0) { $('html').removeClass('no-js').addClass('js'); }
		
		//remove br's from code tag
		$('code').find('br').remove();	
		
		//if a clear is the last div, remove the padding
		if($('.container.main-content > .row > div:last-child').hasClass('clear')) {
			$('.container.main-content > .row > div:last-child').css('padding-bottom','0');
		}
		
		//homepage recent blog for IE8
		$('.container-wrap .blog-recent > div:last-child').addClass('col_last');
		
		//blog ascend bottom padding
		if($('.single .blog_next_prev_buttons').length > 0) $('.container-wrap').css('padding-bottom',0);

		//contact form
		$('.wpcf7-form p:has(input[type=submit])').css('padding-bottom','0px');

		$('.full-width-content .wpcf7-submit').on('click',function(){ setTimeout(function(){ fullWidthContentColumns() },1000); setTimeout(function(){ fullWidthContentColumns() },2000); });
		
		//no caption home slider fix
		$('#featured article').each(function(){
			if($(this).find('h2').attr('data-has-caption') == '0') {
				$(this).parents('.slide').addClass('no-caption');
			}
		});
		
		//add class for IE
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("Edge/");
		if(msie > 0) {
			$('body').addClass('msie');
		}

		//gravity form inside fw content row
		$('.gform_body').on('click',function(e){
		   setTimeout(function(){ fullWidthContentColumns(); },200);
		 });
		 
		//pum salient close 
		$('.pum.pum-theme-salient-page-builder-optimized button.pum-close').wrapInner('<span />'); 
		
		//pum inside fspr
		if($('#nectar_fullscreen_rows').length > 0 && $('.pum-container .pum-content > .wpb_row .full-page-inner > .container > .span_12').length > 0) {
			$('.pum-container .pum-content > .wpb_row .full-page-inner > .container > .span_12').unwrap();
		}

		//chat post format nth child color
		$('article.post.format-chat .content-inner dt:odd').css('color','#333');
		
		//remove margin on last cols inside of full width sections 
		$('.full-width-section').each(function(){
			$(this).find('> .span_12 > div.col_last').last().css('margin-bottom','0');
		});
		
		//remove p tags from extra content editor when warpping only an img 
		$('#portfolio-extra p').each(function(){
			if($(this).find('*').length == 1 && $(this).find('img').length == 1) {
				$(this).find('img').unwrap();
			}
		});
	

		//vc text_separator color
		$('.vc_text_separator').each(function(){
			if( $(this).parents('.full-width-section').length > 0 ) $(this).find('div').css('background-color',$(this).parents('.full-width-section').find('.row-bg').css('background-color'));
		});
		
		//carousel head button alignment  
		$('.carousel-heading').each(function(){
			if($(this).find('h2').length > 0) $(this).find('.carousel-prev, .carousel-next').css('top','7px');
		});
		
		//remove carousel heading if not being used
		$('.carousel-wrap').each(function(){
			if($(this).find('.carousel-heading .container:empty').length > 0) $(this).find('.carousel-heading').remove();
		});
		
		//woocommerce product thuimbnails
		$('.woocommerce div.product div.images div.thumbnails a:nth-child(4n+4)').css('margin-right','0px');
		
		//remove extra galleries when using the nectar gallery slider on projects and posts
		$('article.post .gallery-slider .gallery,  article.post .gallery-slider .jetpack-slideshow, .single-portfolio .gallery-slider .gallery, .single-portfolio .gallery-slider .jetpack-slideshow').remove();
		
		
		$('.woocommerce .span_9 .products.related .products li:nth-child(4), .woocommerce .span_9 .products.upsells .products li:nth-child(4)').remove();
		$('.woocommerce .span_9 .products.related .products li:nth-child(3), .woocommerce .span_9 .products.upsells .products li:nth-child(3)').css('margin-right','0');	
		
		$('.cart-menu a, .widget_shopping_cart a').addClass('no-ajaxy');

		//clients no hover if no link
		$('div.clients').each(function(){
			$(this).find('> div').each(function(){
				if($(this).find('a').length == 0) {
					$(this).addClass('no-link');
				}
			});
		});

		//remove ajaxy from single posts when using disqus
		if(nectarLove.disqusComments == 'true') $('.post-area article a, .blog_next_prev_buttons a, #portfolio-nav #prev-link a, #portfolio-nav #next-link a, .portfolio-items .col .work-item .work-info a').addClass('no-ajaxy');

		//blog next color bg only 
		if($('.blog_next_prev_buttons').find('.bg-color-only-indicator').length > 0) $('.blog_next_prev_buttons').addClass('bg-color-only').find('.bg-color-only-indicator').remove();
		
		if($('#single-below-header').hasClass('fullscreen-header') && $('.blog_next_prev_buttons').length == 0 ) $('#author-bio, .comment-wrap').addClass('lighter-grey');

		//shop header parallax margin 
		if($('body.woocommerce').find('#page-header-bg').length > 0){
			$('.container-wrap').css({'margin-top':'0px','padding-top':'30px'});
		}
		
		//if using wooCommerce sitewide notice
		if($('.demo_store').length > 0) $('#header-outer, #header-space').css('margin-top','32px');
	
		
		//nectar slider external links
		$('.swiper-slide.external-button-1 .buttons > div:nth-child(1) a').attr('target','_blank');
		$('.swiper-slide.external-button-2 .buttons > div:nth-child(2) a').attr('target','_blank');
		
		//portfolio external links
		$(".portfolio-items").find("a[href*='http://']:not([href*='"+window.location.hostname+"'])").attr("target","_blank"); 
		$(".recent_projects_widget").find("a[href*='http://']:not([href*='"+window.location.hostname+"'])").attr("target","_blank"); 
		
		$(".portfolio-items").find("a[href*='https://']:not([href*='"+window.location.hostname+"'])").attr("target","_blank"); 
		$(".recent_projects_widget").find("a[href*='https://']:not([href*='"+window.location.hostname+"'])").attr("target","_blank"); 
		
		//remove excess inner content when empty row
		$('.container-wrap .row > .wpb_row').each(function(){
			if($(this).find('> .span_12 > .wpb_column > .wpb_wrapper').length > 0 && $(this).find('> .span_12 > .wpb_column > .wpb_wrapper').find('*').length == 0) $(this).find('> .span_12 ').remove();
		});
		
		//remove boxed style from full width content
		$('.full-width-content .col.boxed').removeClass('boxed');
		
		//remove full width attribute on slider in full width content section
		$('.full-width-content .wpb_column .nectar-slider-wrap[data-full-width="true"]').attr('data-full-width','false');	

	    //neg marg z-index
	    $('.wpb_column.neg-marg').parents('.wpb_row').css('z-index','110');


	    //portfolio description remove on hover
	    var $tmpTitle = null;
	    $('.portfolio-items > .col a[title]').on('mouseenter',function(){
	    	$tmpTitle = $(this).attr('title');
	    	$(this).attr('title','');
	    });
			$('.portfolio-items > .col a[title]').on('mouseleave',function(){
	    	$(this).attr('title', $tmpTitle);
	    });
			
	    $('.portfolio-items > .col a[title]').on('click',function(e){
			$(this).attr('title', $tmpTitle);
	    });

	};

	crossBrowserFixes();



	jQuery( document.body ).on( 'updated_cart_totals', function() { 
		if($('.plus').length == 0) 
			$( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" class="plus" />' ).prepend( '<input type="button" value="-" class="minus" />' );
	});

	// Quantity buttons
	if($('.plus').length == 0) {
		$( 'div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)' ).addClass( 'buttons_added' ).append( '<input type="button" value="+" class="plus" />' ).prepend( '<input type="button" value="-" class="minus" />' );

		$( document ).on( 'click', '.plus, .minus', function() {

			// Get values
			var $qty		= $( this ).closest( '.quantity' ).find( '.qty' ),
				currentVal	= parseFloat( $qty.val() ),
				max			= parseFloat( $qty.attr( 'max' ) ),
				min			= parseFloat( $qty.attr( 'min' ) ),
				step		= $qty.attr( 'step' );

			// Format values
			if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
			if ( max === '' || max === 'NaN' ) max = '';
			if ( min === '' || min === 'NaN' ) min = 0;
			if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

			// Change the value
			if ( $( this ).is( '.plus' ) ) {

				if ( max && ( max == currentVal || currentVal > max ) ) {
					$qty.val( max );
				} else {
					$qty.val( currentVal + parseFloat( step ) );
				}

			} else {

				if ( min && ( min == currentVal || currentVal < min ) ) {
					$qty.val( min );
				} else if ( currentVal > 0 ) {
					$qty.val( currentVal - parseFloat( step ) );
				}

			}

			// Trigger change event
			$qty.trigger( 'change' );

		});
	}

	function wooPriceSlider(){


		// woocommerce_price_slider_params is required to continue, ensure the object exists
		if ( typeof woocommerce_price_slider_params === 'undefined' || !$('body').hasClass('woocommerce') ) {
			return false;
		}

		// Get markup ready for slider
		$( 'input#min_price, input#max_price' ).hide();
		$( '.price_slider, .price_label' ).show();

		// Price slider uses jquery ui
		var min_price = $( '.price_slider_amount #min_price' ).data( 'min' ),
			max_price = $( '.price_slider_amount #max_price' ).data( 'max' );

		current_min_price = parseInt( min_price, 10 );
		current_max_price = parseInt( max_price, 10 );

		if ( woocommerce_price_slider_params.min_price ) current_min_price = parseInt( woocommerce_price_slider_params.min_price, 10 );
		if ( woocommerce_price_slider_params.max_price ) current_max_price = parseInt( woocommerce_price_slider_params.max_price, 10 );

		$( 'body' ).bind( 'price_slider_create price_slider_slide', function( event, min, max ) {
			if ( woocommerce_price_slider_params.currency_pos === 'left' ) {

				$( '.price_slider_amount span.from' ).html( woocommerce_price_slider_params.currency_symbol + min );
				$( '.price_slider_amount span.to' ).html( woocommerce_price_slider_params.currency_symbol + max );

			} else if ( woocommerce_price_slider_params.currency_pos === 'left_space' ) {

				$( '.price_slider_amount span.from' ).html( woocommerce_price_slider_params.currency_symbol + " " + min );
				$( '.price_slider_amount span.to' ).html( woocommerce_price_slider_params.currency_symbol + " " + max );

			} else if ( woocommerce_price_slider_params.currency_pos === 'right' ) {

				$( '.price_slider_amount span.from' ).html( min + woocommerce_price_slider_params.currency_symbol );
				$( '.price_slider_amount span.to' ).html( max + woocommerce_price_slider_params.currency_symbol );

			} else if ( woocommerce_price_slider_params.currency_pos === 'right_space' ) {

				$( '.price_slider_amount span.from' ).html( min + " " + woocommerce_price_slider_params.currency_symbol );
				$( '.price_slider_amount span.to' ).html( max + " " + woocommerce_price_slider_params.currency_symbol );

			}

			$( 'body' ).trigger( 'price_slider_updated', min, max );
		});

		$( '.price_slider' ).slider({
			range: true,
			animate: true,
			min: min_price,
			max: max_price,
			values: [ current_min_price, current_max_price ],
			create : function( event, ui ) {

				$( '.price_slider_amount #min_price' ).val( current_min_price );
				$( '.price_slider_amount #max_price' ).val( current_max_price );

				$( 'body' ).trigger( 'price_slider_create', [ current_min_price, current_max_price ] );
			},
			slide: function( event, ui ) {

				$( 'input#min_price' ).val( ui.values[0] );
				$( 'input#max_price' ).val( ui.values[1] );

				$( 'body' ).trigger( 'price_slider_slide', [ ui.values[0], ui.values[1] ] );
			},
			change: function( event, ui ) {

				$( 'body' ).trigger( 'price_slider_change', [ ui.values[0], ui.values[1] ] );

			},
		});

	}
	

//vc col mobile fixes
function vcMobileColumns() {

	$('.wpb_row').each(function(){
		if(typeof $(this).find('.span_12').offset() != 'undefined' ) {
		
			$(this).find('[class*="vc_col-"]').each(function(){

				var $firstChildOffset = $(this).parents('.span_12').offset().left;
				
				$(this).removeClass('no-left-margin');
				if($(this).offset().left < $firstChildOffset + 27) { 
					$(this).addClass('no-left-margin');
				} else {
					$(this).removeClass('no-left-margin');
				}
			});
		}
	});
}

if( $('[class*="vc_col-xs-"], [class*="vc_col-md-"], [class*="vc_col-lg-"]').length > 0) { vcMobileColumns(); }


/*-------------------------------------------------------------------------*/
/*	8.	Form Styling
/*-------------------------------------------------------------------------*/	


if($('body[data-fancy-form-rcs="1"]').length > 0) {


    //select only as of v9
    $('select:not(.comment-form-rating #rating)').each(function(){
			
			var $selector;

    	//cf7
    	if($(this).parents('.wpcf7-form-control-wrap').length > 0) {

    		//select 2 already initialized
	    	if($(this).parents('.wpcf7-form-control-wrap').find('.select2-container').length > 0) {
	    		$selector = $($(this).prev('.select2-container'));
	    	} else {
	    		$selector = $(this);
	    	}

	    	//if label is found
	    	if($selector.parents('.wpcf7-form-control-wrap').parent().find('label').length == 1) {
	    		$selector.parents('.wpcf7-form-control-wrap').parent().wrapInner('<div class="fancy-select-wrap" />');
	    	} else {
	    		//default wrap
	    		$selector.wrap('<div class="fancy-select-wrap" />');
	    	}
    	} 
    	//default
    	else {

	    	//select 2 already initialized
	    	if($(this).prev('.select2-container').length > 0) {
	    		$selector = $(this).prev('.select2-container');
	    	} else {
	    		$selector = $(this);
	    	}
				
				if($(this).parents('#buddypress').length == 0) {
			    	//if label is found
			    	if($selector.prev('label').length == 1) {
			    		$selector.prev('label').andSelf().wrapAll('<div class="fancy-select-wrap" />');
			    	} else if($selector.next('label').length == 1) {
			    		$selector.next('label').andSelf().wrapAll('<div class="fancy-select-wrap" />');
			    	} else {
			    		//default wrap
			    		$selector.wrap('<div class="fancy-select-wrap" />');
			    	}
				}
				
    	}
    });
	
	function select2Init(){
		$('select:not(.state_select):not(.country_select):not(.comment-form-rating #rating):not(#tribe-bar-form select):not(.woocommerce-currency-switcher)' ).each( function() {
			
			if($(this).parents('#buddypress').length == 0) {
			
						if($(this).parents('.woocommerce-ordering').length == 0) {
							$( this ).select2({
								minimumResultsForSearch: 7,
								width: '100%'
							});
						} else {
							$( this ).select2({
								minimumResultsForSearch: 7,
								dropdownAutoWidth: true
							});
						}
			}

		});
	}
   
	select2Init();

}
	


//Back/forward cache OCM close
if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 || navigator.userAgent.match(/(iPod|iPhone|iPad)/)){
	
	window.onpageshow = function(event) {
		
			if(event.persisted) {
					
					//play video BGs
					$('.nectar-video-wrap, .nectar-slider-wrap .swiper-slide .video-wrap').each(function(i){
						
						if($(this).find('video').length > 0) {
							  $(this).find('video')[0].play();
						}
						
					});
	

					//close mobile nav
					
					////material
					if($('body.material-ocm-open').length > 0) {	
							$('body > .slide_out_area_close').addClass('non-human-allowed').trigger('click');

							setTimeout(function(){
								$('body > .slide_out_area_close').removeClass('non-human-allowed');
							},100);
						
					} 
					else if( $('#slide-out-widget-area.slide-out-from-right-hover.open').length > 0 && navigator.userAgent.match(/(iPod|iPhone|iPad)/) ) {
						mobileCloseNavCheck();
					}
					////others
					else if( $('#slide-out-widget-area.fullscreen.open').length > 0 || $('#slide-out-widget-area.fullscreen-alt.open').length > 0 || $('#slide-out-widget-area.slide-out-from-right.open').length > 0 ) {
						
							$('#slide-out-widget-area .slide_out_area_close').addClass('non-human-allowed');
							$('.slide-out-widget-area-toggle:not(.std-menu) a.open').addClass('non-human-allowed').trigger('click');
							setTimeout(function(){
								$('#slide-out-widget-area .slide_out_area_close').removeClass('non-human-allowed');
							},100);
						
					}

			} // if loaded from bf cache
			
		} //onpage show
		
} // only used on Safari or mobile
	
	
/*-------------------------------------------------------------------------*/
/*	10.	Page transitions
/*-------------------------------------------------------------------------*/	

	if($('body[data-ajax-transitions="true"]').length > 0 && $('#ajax-loading-screen[data-method="ajax"]').length > 0 && !navigator.userAgent.match(/(Android|iPod|iPhone|iPad|BlackBerry|IEMobile|Opera Mini)/) && $(window).width() > 690 ) {

		$('#ajax-content-wrap').ajaxify({
			'selector':'#ajax-content-wrap a:not(.no-ajaxy):not([target="_blank"]):not([href^="#"]):not(.comment-edit-link):not(#cancel-comment-reply-link):not(.comment-reply-link):not(#toggle-nav):not(.cart_list a):not(.logged-in-as a):not(.no-widget-added a):not(.add_to_cart_button):not(.product-wrap a):not(.section-down-arrow):not([data-filter]):not([data-fancybox]):not(.product_list_widget a):not(.magnific):not(.pp):not([rel^="prettyPhoto"]):not(.pretty_photo), #header-outer li:not(.no-ajaxy) > a:not(.no-ajaxy), #header-outer #logo',
			'verbosity': 0, 
			requestDelay: 400,
			previewoff : true,
			memoryoff: true,
			turbo : false
		});

		$(window).on("pronto.render", initPage)
		.on("pronto.load", destroyPage)
		.on("pronto.request", transitionPage);

		if($('.nectar-box-roll').length == 0 && $('#ajax-loading-screen[data-effect*="horizontal_swipe"]').length > 0) setTimeout(function() { waypoints(); }, 750);
		else if($('.nectar-box-roll').length == 0) setTimeout(function() { waypoints(); },300);

		if($('#ajax-loading-screen[data-effect*="horizontal_swipe"]').length > 0) {
			setTimeout(function(){ 
				$('#ajax-loading-screen').addClass('loaded');
			},30);
		}

		initPage();

	} else if($('body[data-ajax-transitions="true"]').length > 0 && $('#ajax-loading-screen[data-method="standard"]').length > 0 ) {
		
		//chrome white BG flash fix
		$('html').addClass('page-trans-loaded');

		//fadeout loading animation
		if($('#ajax-loading-screen[data-effect="standard"]').length > 0) {

			if($('.nectar-particles').length == 0) {

					$('#ajax-loading-screen').transition({'opacity':0},500,function(){ 
						$(this).css({'display':'none'}); 
					}); 
					$('#ajax-loading-screen .loading-icon').transition({'opacity':0},500) 
				
			}

			//bind waypoints after loading screen has left
			if($('.nectar-box-roll').length == 0) setTimeout(function() { waypoints(); },550);

			//safari back/prev fix
			if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 || navigator.userAgent.match(/(iPod|iPhone|iPad)/)){
				window.onunload = function(){ $('#ajax-loading-screen').stop().transition({'opacity':0},800,function(){ $(this).css({'display':'none'}); }); $('#ajax-loading-screen .loading-icon').transition({'opacity':0},600) };
				window.onpageshow = function(event) {
					
		    		if (event.persisted) {
							
							
		    			$('#ajax-loading-screen').stop().transition({'opacity':0},800,function(){ 
		    				$(this).css({'display':'none'}); 
		    			}); 
		    			$('#ajax-loading-screen .loading-icon').transition({'opacity':0},600);
							
		    		} //presisted
						
		    	}
					
			} else if(navigator.userAgent.indexOf('Firefox') != -1) {
				window.onunload = function(){};
			}

	    		
	    	
			
		} else {

			//for swipe trans add loaded immediately
			if($('#ajax-loading-screen[data-effect*="horizontal_swipe"]').length > 0) {
				setTimeout(function(){ 
					$('#ajax-loading-screen').addClass('loaded');
				},60);
			}

			if($('#page-header-wrap #page-header-bg[data-animate-in-effect="zoom-out"] .nectar-video-wrap').length == 0 && $('.first-nectar-slider').length == 0) {
				setTimeout(function(){ 
					$('#ajax-loading-screen:not(.loaded)').addClass('loaded');
					setTimeout(function(){ $('#ajax-loading-screen').addClass('hidden'); },1000);
				},150);
			}

			//safari back/prev fix
			if(navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1 || navigator.userAgent.match(/(iPod|iPhone|iPad)/)){
				window.onunload = function(){ $('#ajax-loading-screen').stop().transition({'opacity':0},800,function(){ $(this).css({'display':'none'}); }); $('#ajax-loading-screen .loading-icon').transition({'opacity':0},600) };
				window.onpageshow = function(event) {
		    		if (event.persisted) { 
		    			$('#ajax-loading-screen').stop().transition({'opacity':0},800,function(){ 
		    				$(this).css({'display':'none'}); 
		    			}); 
		    			$('#ajax-loading-screen .loading-icon').transition({'opacity':0},600);
		    		}
		    	}
			} else if(navigator.userAgent.indexOf('Firefox') != -1) {
				window.onunload = function(){};
			}
			
			//bind waypoints after loading screen has left
			if($('.nectar-box-roll').length == 0 && $('#ajax-loading-screen[data-effect*="horizontal_swipe"]').length > 0) { setTimeout(function() { waypoints(); }, 750); }
			else if($('.nectar-box-roll').length == 0) setTimeout(function() { waypoints(); },350);

		}

		//remove excess loading images now
		$('.portfolio-loading, .nectar-slider-loading .loading-icon').remove();


		if($('#ajax-loading-screen[data-disable-fade-on-click="1"]').length == 0) {
				
				if( $('body.using-mobile-browser #ajax-loading-screen[data-method="standard"][data-disable-mobile="1"]').length == 0) {
					
					var ignore_onbeforeunload = false;
					$('a[href^="mailto"], a[href^="tel"]').on('click',function(){
			        ignore_onbeforeunload = true;
			    });
					window.addEventListener('beforeunload', function () {
								
								if (!ignore_onbeforeunload){
									$('#ajax-loading-screen').addClass('set-to-fade');
									transitionPageStandard();
								}
								ignore_onbeforeunload = false;
					});
				}

		} // if disable fade out is not on
		
		
	} else {	
		//bind waypoints regularly
		if($('.nectar-box-roll').length == 0 && !nectarDOMInfo.usingFrontEndEditor) setTimeout(function() { waypoints(); },100);
	}


	function transitionPage(e) {
		
		if($('#ajax-loading-screen[data-effect*="horizontal_swipe"]').length > 0) {

			if($(window).scrollTop() > 0) {

				//stop nicescroll
				if($().niceScroll && $("html").getNiceScroll()){
					var nice = $("html").getNiceScroll();
					nice.stop();
				}
				
				$('body,html').stop(true,true).animate({
					scrollTop:0
				},500,'easeOutQuad',function(){ 
					
					//close widget area
					setTimeout(function(){  if($('#header-outer').hasClass('side-widget-open')) $('.slide-out-widget-area-toggle a').trigger('click');  },400);
					$('#ajax-loading-screen').removeClass('loaded');
					$('#ajax-loading-screen').addClass('in-from-right');
					setTimeout(function(){
						$('#ajax-loading-screen').addClass('loaded');
					},30);

				});
			}
			else { 
				//close widget area
				setTimeout(function(){  if($('#header-outer').hasClass('side-widget-open')) $('.slide-out-widget-area-toggle a').trigger('click');  },400);
				$('#ajax-loading-screen').removeClass('loaded');
				$('#ajax-loading-screen').addClass('in-from-right');
				setTimeout(function(){
					$('#ajax-loading-screen').addClass('loaded');
				},30);
			}

		} else {
			if($(window).scrollTop() > 0) {

				//stop nicescroll
				if($().niceScroll && $("html").getNiceScroll()){
					var nice = $("html").getNiceScroll();
					nice.stop();
				}
				
				$('body,html').stop(true,true).animate({
					scrollTop:0
				},500,'easeOutQuad',function(){ 
					$('#ajax-loading-screen').css({'opacity':'1', 'display':'none'});
					$('#ajax-loading-screen').stop(true,true).fadeIn(600,function(){
						$('#ajax-loading-screen .loading-icon').animate({'opacity':1},400);
						//close widget area
						setTimeout(function(){  if($('#header-outer').hasClass('side-widget-open')) $('.slide-out-widget-area-toggle a').trigger('click');  },400);
					});
				});

			} else {
				$('#ajax-loading-screen').css('opacity','1').stop().fadeIn(600,function(){
					$('#ajax-loading-screen .loading-icon').animate({'opacity':1},400);
				});

				//close widget area
				setTimeout(function(){  if($('#header-outer').hasClass('side-widget-open')) $('.slide-out-widget-area-toggle a').trigger('click');  },400);
			}
		}

		
	}

	function transitionPageStandard(e) {

		if($('#ajax-loading-screen[data-effect*="horizontal_swipe"]').length > 0) {
			$('#ajax-loading-screen').removeClass('loaded');
			$('#ajax-loading-screen').addClass('in-from-right');
			setTimeout(function(){
				$('#ajax-loading-screen').addClass('loaded');
			},30);
		} else {
			if($('#ajax-loading-screen[data-effect="center_mask_reveal"]').length > 0) {
				$('#ajax-loading-screen').css('opacity','0').css('display','block').transition({'opacity':'1'},450);
			} else {
				$('#ajax-loading-screen').show().transition({'opacity':'1'},450);
			}
		}
		
	}

	function destroyPage(e) {
		$(window).off('scroll.appear');
		if($('#nectar_fullscreen_rows').length > 0 && $().fullpage) 
			$.fn.fullpage.destroy('all');
	}

	function initPage(e) {

		if(!$('body').hasClass('ajax-loaded')) return false;


		//init js
		lightBoxInit();
		addOrRemoveSF();
		
		if($('body[data-header-format="left-header"]').length == 0) {
			$(".sf-menu").superfish('destroy');
		}

		initSF();
		SFArrows();
		headerInit();
		var $effectTimeout = ($('#ajax-loading-screen').length > 0) ? 800 : 0;
		pageHeaderTextEffectInit();
		if($('#page-header-bg .nectar-video-wrap video').length == 0) { setTimeout(pageHeaderTextEffect,$effectTimeout); }
		coloredButtons();
		columnBGColors();
		fwCarouselLinkFix();
		if($('.carousel').length > 0) {
			standardCarouselInit();
			clientsCarouselInit();
			carouselHeightCalcs();
		}
		if($('.owl-carousel').length > 0) owlCarouselInit();
		if($('.products-carousel').length > 0 || $('.nectar-woo-flickity').length > 0) productCarouselInit();
		if($('#nectar_fullscreen_rows').length > 0 && $().fullpage) {
			setFPNames();
			initFullPageFooter();
			fullscreenRowLogic();
			initNectarFP();
		}
		flexsliderInit();
		accordionInit();
		tabbedInit();
		nectarLiquidBGs();
		tabbbedDeepLinking();
		accordionDeepLinking();
		ulChecks();
		oneFourthClasses();
		carouselfGrabbingClass();
		cascadingImageBGSizing();
		fullWidthSections();
		fwsClasses();
		fullwidthImgOnlySizingInit();
		fullwidthImgOnlySizing();
		if(nectarDOMInfo.usingMobileBrowser) {
			fullWidthRowPaddingAdjustCalc();
		}
		boxRollInit();
		setTimeout(function(){ 
		   waypoints();
		   flickityInit();
		},100); 
		if($('body[data-animated-anchors="true"]').length > 0) setTimeout(scrollSpyInit,200);
		socialSharingInit();
		hotSpotHoverBind();
		pricingTableHeight();
		createTestimonialControls();
		imageWithHotspotClickEvents();
		testimonialSliderHeight(); 
		largeIconHover();
		if($('body.material[data-slide-out-widget-area-style="slide-out-from-right"]').length == 0) {
			fullscreenMenuInit();
		}
		boxRollMouseWheelInit();
		midnightInit();
		responsiveVideoIframesInit();
		responsiveVideoIframes();
		fullWidthContentColumns();
		videoBGInit();
		$window.unbind('scroll.parallaxSections').unbind('resize.parallaxSections');
		parallaxScrollInit();
		
		$blog_containers = [];
		$('.posts-container').each(function(i){
			$blog_containers[i] = $(this);
		});
		
		nectarAccountPageTabs();
		masonryBlogInit();
		masonryPortfolioInit();
		portfolioAccentColor();
		portfolioHoverEffects();
		portfolioFiltersInit();
		style6Img();
		isotopeCatSelection();
		$(window).unbind('.infscr');
		infiniteScrollInit();
		toTopBind();
		centerLove();
		postNextButtonEffect();
		pageLoadHash();
		slideOutWidgetAreaScrolling();
		
		//search results
		if($('body.search-results').length > 0 && $('#search-results article').length > 0) { searchResultMasonry(); }
		
		//cf7
		if($().wpcf7InitForm) $('div.wpcf7 > form').wpcf7InitForm();

		//woocommerce price slider
		wooPriceSlider();

		//twitter widget 
		if(typeof twttr != 'undefined') { twttr.widgets.load(); }

		//Calendarize.it
		if(typeof init_rhc === 'function') { init_rhc(); }

		//unwrap post and protfolio videos
		$('.video-wrap iframe').unwrap();
		$('#sidebar iframe[src]').unwrap();

		$('video:not(.slider-video)').attr('width','100%');
		$('video:not(.slider-video)').attr('height','100%'); 

		$('.wp-video-shortcode.mejs-container').each(function(){
			$(this).attr('data-aspectRatio', parseInt($(this).css('height')) / parseInt($(this).css('width')));
		});

		//mediaElement
		$('video.wp-media-shortcode-ajax, audio.wp-media-shortcode-ajax').each(function(){ 
			if(!$(this).parent().hasClass('mejs-mediaelement') && $().mediaelementplayer) {
				$(this).mediaelementplayer();  
			}
		});

		$('.mejs-container').css({'height': '100%', 'width': '100%'});
		
		$('audio').attr('width','100%');
		$('audio').attr('height','100%');

		$('audio').css('visibility','visible');

		if($('body').hasClass('mobile')){
			$('video').css('visibility','hidden');
		} else {
			$('video').css('visibility','visible');
		}

		$('.wpb_row:has(".nectar-video-wrap")').each(function(i){
			$(this).css('z-index',100 + i);
		});

		mouseParallaxInit();

		//chrome self hosted slider bg video correct
		 if(navigator.userAgent.indexOf('Chrome') > 0) { 
		 	$('.swiper-wrapper .video-wrap').each(function(i){
			  	var webmSource = jQuery(this).find('video source[type="video/webm"]').attr('src') + "?id="+Math.ceil(Math.random()*10000);
	          	var firstVideo = jQuery(this).find('video').get(0);
	          	firstVideo.src = webmSource;
	          	firstVideo.load();
            });
	    }


		if($('.nectar-video-bg').length > 0) {
			setTimeout(function(){
			    	resizeVideoToCover();
			    	$('.video-color-overlay').each(function(){
			    		$(this).css('background-color',$(this).attr('data-color'));
			    	});
			    	$('.nectar-video-wrap').transition({'opacity':'1'},0);
			    	$('.video-color-overlay').transition({'opacity':'0.7'},0);
			    	
			    },400);
		}

		
		nectarPageHeader();
	

		//cart dropdown
		$('#header-outer div.cart-outer').hoverIntent(function(){
			$('#header-outer .widget_shopping_cart').addClass('open').stop(true,true).fadeIn(400);
			$('#header-outer .cart_list').stop(true,true).fadeIn(400);
			clearTimeout(timeout);
			$('#header-outer .cart-notification').fadeOut(300);
		});


		//remove excess loading images now
		$('.portfolio-loading, .nectar-slider-loading .loading-icon').remove();

		setTimeout(portfolioSidebarFollow,250);
		setTimeout(portfolioSidebarFollow,500);
		setTimeout(portfolioSidebarFollow,1000);

		crossBrowserFixes();


		$(window).trigger('resize');

		//fix admin bar
		$("#wpadminbar").show();	


		//close widget area
		if($('#header-outer').hasClass('side-widget-open')) $('.slide-out-widget-area-toggle a').trigger('click'); 

		if($('#ajax-loading-screen[data-effect*="horizontal_swipe"]').length > 0) {
			closeSearch();
			$('#ajax-loading-screen').removeClass('in-from-right').removeClass('loaded');
			setTimeout(function(){ $('#ajax-loading-screen').addClass('loaded'); },30);
		} else {
			//fade in page
			setTimeout(function(){ $('#ajax-loading-screen').stop(true,true).fadeOut(500, function(){ $('#ajax-loading-screen .loading-icon').css({'opacity':0}); }); closeSearch();  },200);
			setTimeout(function(){ $('#ajax-loading-screen').stop(true,true).fadeOut(500, function(){ $('#ajax-loading-screen .loading-icon').css({'opacity':0}); }); closeSearch(); },900);
		}
	} 



(function($){
	if(!$.fn.textareaCount) { 
		$.fn.textareaCount = function(options, fn) {
	        var defaults = {
				maxCharacterSize: -1,
				originalStyle: 'originalTextareaInfo',
				warningStyle: 'warningTextareaInfo',
				warningNumber: 20,
				displayFormat: '#input characters | #words words'
			};

			var options = $.extend(defaults, options);

			var container = $(this);

			$("<div class='charleft'>&nbsp;</div>").insertAfter(container);


			//create charleft css
			var charLeftCss = {
				'width' : container.width()
			};

			var charLeftInfo = getNextCharLeftInformation(container);
			charLeftInfo.addClass(options.originalStyle);
			//charLeftInfo.css(charLeftCss);


			var numInput = 0;
			var maxCharacters = options.maxCharacterSize;
			var numLeft = 0;
			var numWords = 0;

			container.on('keyup', function(event){limitTextAreaByCharacterCount();})
					 .on('mouseover', function(event){setTimeout(function(){limitTextAreaByCharacterCount();}, 10);})
					 .on('paste', function(event){setTimeout(function(){limitTextAreaByCharacterCount();}, 10);});

	        limitTextAreaByCharacterCount();

			function limitTextAreaByCharacterCount(){
				charLeftInfo.html(countByCharacters());

				//function call back
				if(typeof fn != 'undefined'){
					fn.call(this, getInfo());
				}
				return true;
			}

			function countByCharacters(){
				var content = container.val();
				var contentLength = content.length;
				//Start Cut
				if(options.maxCharacterSize > 0){
					//If copied content is already more than maxCharacterSize, chop it to maxCharacterSize.
					if(contentLength >= options.maxCharacterSize) {
						content = content.substring(0, options.maxCharacterSize);
					}

					var newlineCount = getNewlineCount(content);

					// newlineCount new line character. For windows, it occupies 2 characters
					var systemmaxCharacterSize = options.maxCharacterSize - newlineCount;
					if (!isWin()){
						 systemmaxCharacterSize = options.maxCharacterSize
					}
					if(contentLength > systemmaxCharacterSize){
						//avoid scroll bar moving
						var originalScrollTopPosition = this.scrollTop;
						container.val(content.substring(0, systemmaxCharacterSize));
						this.scrollTop = originalScrollTopPosition;
					}
					charLeftInfo.removeClass(options.warningStyle);
					if(systemmaxCharacterSize - contentLength <= options.warningNumber){
						charLeftInfo.addClass(options.warningStyle);
					}

					numInput = container.val().length + newlineCount;
					if(!isWin()){
						numInput = container.val().length;
					}

					numWords = countWord(getCleanedWordString(container.val()));

					numLeft = maxCharacters - numInput;
				} else {
					//normal count, no cut
					var newlineCount = getNewlineCount(content);
					numInput = container.val().length + newlineCount;
					if(!isWin()){
						numInput = container.val().length;
					}
					numWords = countWord(getCleanedWordString(container.val()));
				}

				return formatDisplayInfo();
			}

			function formatDisplayInfo(){
				var format = options.displayFormat;
				format = format.replace('#input', numInput);
				format = format.replace('#words', numWords);
				//When maxCharacters <= 0, #max, #left cannot be substituted.
				if(maxCharacters > 0){
					format = format.replace('#max', maxCharacters);
					format = format.replace('#left', numLeft);
				}
				return format;
			}

			function getInfo(){
				var info = {
					input: numInput,
					max: maxCharacters,
					left: numLeft,
					words: numWords
				};
				return info;
			}

			function getNextCharLeftInformation(container){
					return container.next('.charleft');
			}

			function isWin(){
				var strOS = navigator.appVersion;
				if (strOS.toLowerCase().indexOf('win') != -1){
					return true;
				}
				return false;
			}

			function getNewlineCount(content){
				var newlineCount = 0;
				for(var i=0; i<content.length;i++){
					if(content.charAt(i) == '\n'){
						newlineCount++;
					}
				}
				return newlineCount;
			}

			function getCleanedWordString(content){
				var fullStr = content + " ";
				var initial_whitespace_rExp = /^[^A-Za-z0-9]+/gi;
				var left_trimmedStr = fullStr.replace(initial_whitespace_rExp, "");
				var non_alphanumerics_rExp = rExp = /[^A-Za-z0-9]+/gi;
				var cleanedStr = left_trimmedStr.replace(non_alphanumerics_rExp, " ");
				var splitString = cleanedStr.split(" ");
				return splitString;
			}

			function countWord(cleanedWordString){
				var word_count = cleanedWordString.length-1;
				return word_count;
			}
		};
	}
})(jQuery);	
	
	
	
	
});


 }(window.jQuery, window, document));


function resizeIframe() {
	var element = document.getElementById("pp_full_res").getElementsByTagName("iframe");
	var height = element[0].contentWindow.document.body.scrollHeight;
    
    //iframe height
    element[0].style.height = height + 'px';
	
	//pp height
	document.getElementsByClassName("pp_content_container")[0].style.height = height+40+ 'px';
	document.getElementsByClassName("pp_content")[0].style.height = height+40+ 'px';
	
}



/*!
 * hoverIntent v1.9.0 // 2017.09.01 // jQuery v1.7.0+
 * http://briancherne.github.io/jquery-hoverIntent/
 *
 * You may use hoverIntent under the terms of the MIT license. Basically that
 * means you are free to use hoverIntent as long as this header is left intact.
 * Copyright 2007-2017 Brian Cherne
 */
!function(factory){"use strict";"function"==typeof define&&define.amd?define(["jquery"],factory):jQuery&&!jQuery.fn.hoverIntent&&factory(jQuery)}(function($){"use strict";var cX,cY,_cfg={interval:100,sensitivity:6,timeout:0},INSTANCE_COUNT=0,track=function(ev){cX=ev.pageX,cY=ev.pageY},compare=function(ev,$el,s,cfg){if(Math.sqrt((s.pX-cX)*(s.pX-cX)+(s.pY-cY)*(s.pY-cY))<cfg.sensitivity)return $el.off(s.event,track),delete s.timeoutId,s.isActive=!0,ev.pageX=cX,ev.pageY=cY,delete s.pX,delete s.pY,cfg.over.apply($el[0],[ev]);s.pX=cX,s.pY=cY,s.timeoutId=setTimeout(function(){compare(ev,$el,s,cfg)},cfg.interval)},delay=function(ev,$el,s,out){return delete $el.data("hoverIntent")[s.id],out.apply($el[0],[ev])};$.fn.hoverIntent=function(handlerIn,handlerOut,selector){var instanceId=INSTANCE_COUNT++,cfg=$.extend({},_cfg);$.isPlainObject(handlerIn)?(cfg=$.extend(cfg,handlerIn),$.isFunction(cfg.out)||(cfg.out=cfg.over)):cfg=$.isFunction(handlerOut)?$.extend(cfg,{over:handlerIn,out:handlerOut,selector:selector}):$.extend(cfg,{over:handlerIn,out:handlerIn,selector:handlerOut});var handleHover=function(e){var ev=$.extend({},e),$el=$(this),hoverIntentData=$el.data("hoverIntent");hoverIntentData||$el.data("hoverIntent",hoverIntentData={});var state=hoverIntentData[instanceId];state||(hoverIntentData[instanceId]=state={id:instanceId}),state.timeoutId&&(state.timeoutId=clearTimeout(state.timeoutId));var mousemove=state.event="mousemove.hoverIntent.hoverIntent"+instanceId;if("mouseenter"===e.type){if(state.isActive)return;state.pX=ev.pageX,state.pY=ev.pageY,$el.off(mousemove,track).on(mousemove,track),state.timeoutId=setTimeout(function(){compare(ev,$el,state,cfg)},cfg.interval)}else{if(!state.isActive)return;$el.off(mousemove,track),state.timeoutId=setTimeout(function(){delay(ev,$el,state,cfg.out)},cfg.timeout)}};return this.on({"mouseenter.hoverIntent":handleHover,"mouseleave.hoverIntent":handleHover},cfg.selector)}});