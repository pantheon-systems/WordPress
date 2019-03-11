/*jshint browser:true, devel:true */
/*global document */

var WCMLCurrecnySwitcherDropdownClick = (function() {
	"use strict";

	var wrapperSelector = '.js-wcml-dropdown-click';
	var submenuSelector = '.js-wcml-dropdown-click-submenu';
	var isOpen = false;

	var toggle = function(event) {
		var subMenu = this.querySelectorAll(submenuSelector)[0];

		if(subMenu.style.visibility === 'visible'){
			subMenu.style.visibility = 'hidden';
			document.removeEventListener('click', close);
		}else{
			subMenu.style.visibility = 'visible';
			document.addEventListener('click', close);
			isOpen = true;
		}

		return false;
	};

	var close = function(){

		if(!isOpen){
			var switchers = document.querySelectorAll(wrapperSelector);

			for(var i=0;i<switchers.length;i++){
				var altLangs = switchers[i].querySelectorAll(submenuSelector)[0];
				altLangs.style.visibility = 'hidden';
			}
		}

		isOpen = false;
	};

	var preventDefault = function(e) {
		var evt = e ? e : window.event;

		if (evt.preventDefault) {
			evt.preventDefault();
		}

		evt.returnValue = false;
	};

	var init = function() {
		var wrappers = document.querySelectorAll(wrapperSelector);

		for(var i=0; i < wrappers.length; i++ ) {
			wrappers[i].addEventListener('click', toggle );
		}
		var links = document.querySelectorAll(wrapperSelector + ' .js-wcml-dropdown-click-toggle');
		for(var j=0; j < links.length; j++) {
			links[j].addEventListener('click', preventDefault );
		}

	};
	return {

		'init': init
	};

})();

document.addEventListener('DOMContentLoaded', function(){
	"use strict";
	WCMLCurrecnySwitcherDropdownClick.init();
});