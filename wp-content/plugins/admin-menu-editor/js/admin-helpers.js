/*global wsAmeCurrentMenuItem*/

(function($) {
	var currentItem = (typeof wsAmeCurrentMenuItem !== 'undefined') ? wsAmeCurrentMenuItem : {};

	//The page heading is typically hardcoded and/or not configurable, so we need to use JS to change it.
	var customPageHeading = currentItem.hasOwnProperty('customPageHeading') ? currentItem['customPageHeading'] : null;
	var pageHeadingSelector = currentItem.hasOwnProperty('pageHeadingSelector') ? currentItem['pageHeadingSelector'] : '.wrap > h2:first';
	var ameHideHeading = null;
	if ( customPageHeading ) {
		//Temporarily hide the heading to prevent the original text from showing up briefly
		//before being replaced when the DOM is ready (see below).
		ameHideHeading = $('<style type="text/css">.wrap > h2:first-child,.wrap > h1:first-child { visibility: hidden; }</style>')
			.appendTo('head');
	}

	jQuery(function($) {
		var adminMenu = $('#adminmenu');

		//Menu separators shouldn't be clickable and should have a custom class.
		adminMenu
				.find('.ws-submenu-separator')
				.closest('a').click(function() {
					return false;
				})
				.closest('li').addClass('ws-submenu-separator-wrap');

		//Menus with the target "< None >" also shouldn't be clickable.
		adminMenu
			.find(
				'a.menu-top.ame-unclickable-menu-item, ul.wp-submenu > li > a[href^="#ame-unclickable-menu-item"]'
			)
			.click(function() {
				//Exception: At small viewport sizes, WordPress changes how top level menus work: clicking a menu
				//now expands its submenu. We must not ignore that click or it will be impossible to expand the menu.
				var viewportWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
				if ((viewportWidth <= 782) && $(this).closest('li').hasClass('wp-has-submenu')) {
					return;
				}

				//Ignore the click.
				return false;
			});

		//Open submenus that have the "always open" flag.
		adminMenu
			.find('li.ws-ame-has-always-open-submenu.wp-has-submenu')
			.addClass('wp-has-current-submenu')
			.removeClass('wp-not-current-submenu');

		//Replace the original page heading with the custom heading.
		if ( customPageHeading ) {
			function replaceAdminPageHeading(newText) {
				var headingText = $(pageHeadingSelector)
					.contents()
					.filter(function() {
						//Find text nodes.
						if ((this.nodeType != 3) || (!this.nodeValue)) {
							return false;
						}
						//Skip whitespace.
						return /\S/.test(this.nodeValue);
					}).get(0);

				if (headingText && headingText.nodeValue) {
					headingText.nodeValue = newText;
				}
			}

			//Normal headings have at least one tab's worth of trailing whitespace. We need to replicate that
			//to keep the page layout roughly the same.
			replaceAdminPageHeading(customPageHeading + '\t');
			ameHideHeading.remove(); //Make the heading visible.
		}
	});

})(jQuery);