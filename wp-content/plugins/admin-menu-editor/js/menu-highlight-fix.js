jQuery(function($) {
	// parseUri 1.2.2
	// (c) Steven Levithan <stevenlevithan.com>
	// MIT License
	// Modified: Added partial URL-decoding support.

	function parseUri (str) {
		var	o   = parseUri.options,
			m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
			uri = {},
			i   = 14;

		while (i--) uri[o.key[i]] = m[i] || "";

		uri[o.q.name] = {};
		uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
			if ($1) {
				//Decode percent-encoded query parameters.
				if (o.q.name === 'queryKey') {
					//A space can be encoded either as "%20" or "+". decodeUriComponent doesn't decode plus signs,
					//so we need to do that first.
					$2 = $2.replace('+', ' ');

					$1 = decodeURIComponent($1);
					$2 = decodeURIComponent($2);
				}
				uri[o.q.name][$1] = $2;
			}
		});

		return uri;
	}

	parseUri.options = {
		strictMode: false,
		key: ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"],
		q:   {
			name:   "queryKey",
			parser: /(?:^|&)([^&=]*)=?([^&]*)/g
		},
		parser: {
			strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
			loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
		}
	};

	// --- parseUri ends ---

	//Find the menu item whose URL best matches the currently open page.
	var currentUri = parseUri(location.href);
	var bestMatch = {
		uri : null,
		link : null,
		matchingParams : -1,
		differentParams : 10000,
		isAnchorMatch : false,
		isTopMenu : false,
        isHighlighted: false
	};

	//Special case: ".../wp-admin/" should match ".../wp-admin/index.php".
	if (currentUri.path.match(/\/wp-admin\/$/)) {
		currentUri.path = currentUri.path + 'index.php';
	}

	//Special case: if post_type is not specified for edit.php and post-new.php,
	//WordPress assumes it is "post". Here we make this explicit.
	if ( (currentUri.file === 'edit.php') || (currentUri.file === 'post-new.php') ) {
		if ( !currentUri.queryKey.hasOwnProperty('post_type') ) {
			currentUri.queryKey['post_type'] = 'post';
		}
	}

    var adminMenu = $('#adminmenu');
	adminMenu.find('li > a').each(function(index, link) {
		var $link = $(link);

		//Skip links that have no href or contain nothing but an "#anchor". Both AME and some
		//other plugins (e.g. S2Member 120703) use them as separators.
		if ( !$link.is('[href]') || ($link.attr('href').substring(0, 1) === '#') ) {
			return;
		}

		var uri = parseUri(link.href);

		//Same as above - use "post" as the default post type.
		if ( (uri.file === 'edit.php') || (uri.file === 'post-new.php') ) {
			if ( !uri.queryKey.hasOwnProperty('post_type') ) {
				uri.queryKey['post_type'] = 'post';
			}
		}
		//TODO: Consider using get_current_screen and the current_screen filter to get post types and taxonomies.

		//Check for a close match - everything but query and #anchor.
		var components = ['protocol', 'host', 'port', 'user', 'password', 'path'];
		var isCloseMatch = true;
		for (var i = 0; (i < components.length) && isCloseMatch; i++) {
			isCloseMatch = isCloseMatch && (uri[components[i]] === currentUri[components[i]]);
		}

		if (!isCloseMatch) {
			return; //Skip to the next link.
		}

		//Calculate the number of matching and different query parameters.
		var matchingParams = 0, differentParams = 0, param;
		for(param in uri.queryKey) {
			if (uri.queryKey.hasOwnProperty(param)) {
				if (currentUri.queryKey.hasOwnProperty(param)) {
                    //All parameters that are present in *both* URLs must have the same exact values.
                    if (uri.queryKey[param] === currentUri.queryKey[param]) {
                        matchingParams++;
                    } else {
                        return; //Skip to the next link.
                    }
				} else {
					differentParams++;
				}
			}
		}
		for(param in currentUri.queryKey) {
			if (currentUri.queryKey.hasOwnProperty(param) && !uri.queryKey.hasOwnProperty(param)) {
				differentParams++;
			}
		}

		var isAnchorMatch = uri.anchor === currentUri.anchor;
		var isTopMenu = $link.hasClass('menu-top');
        var isHighlighted = $link.is('.current, .wp-has-current-submenu');

		//Figure out if the current link is better than the best found so far.
		//To do that, we compare them by several criteria (in order of priority):
		var comparisons = [
			{
				better : (matchingParams > bestMatch.matchingParams),
				equal  : (matchingParams === bestMatch.matchingParams)
			},
			{
				better : (differentParams < bestMatch.differentParams),
				equal  : (differentParams === bestMatch.differentParams)
			},
			{
				better : (isAnchorMatch && (!bestMatch.isAnchorMatch)),
				equal  : (isAnchorMatch === bestMatch.isAnchorMatch)
			},

			//When a menu has multiple submenus, the first submenu usually has the same URL
			//as the parent menu. We want to highlight this item and not just the parent.
			{
				better : (!isTopMenu && bestMatch.isTopMenu
					//Is this link a child of the current best match?
					&& (!bestMatch.link || ($link.closest(bestMatch.link.closest('li')).length > 0))
				),
				equal : (isTopMenu === bestMatch.isTopMenu)
			},

            //All else being equal, the item highlighted by WP is probably a better match.
            {
                better : (isHighlighted && !bestMatch.isHighlighted),
                equal  : (isHighlighted === bestMatch.isHighlighted)
            }
		];

		var isBetterMatch = false,
			isEquallyGood = true,
			j = 0;

		while (isEquallyGood && !isBetterMatch && (j < comparisons.length)) {
			isBetterMatch = comparisons[j].better;
			isEquallyGood = comparisons[j].equal;
			j++;
		}

		if (isBetterMatch) {
			bestMatch = {
				uri : uri,
				link : $link,
				matchingParams : matchingParams,
				differentParams : differentParams,
				isAnchorMatch : isAnchorMatch,
				isTopMenu : isTopMenu,
                isHighlighted: isHighlighted
			}
		}
	});

	//Highlight and/or expand the best matching menu.
	if (bestMatch.link !== null) {
		var bestMatchLink = bestMatch.link;
		var parentMenu = bestMatchLink.closest('li.menu-top');
		//console.log('Best match is: ', bestMatchLink);

		var otherHighlightedMenus = $('li.wp-has-current-submenu, li.menu-top.current', '#adminmenu')
			.not(parentMenu)
			.not('.ws-ame-has-always-open-submenu');

		var isWrongItemHighlighted = !bestMatchLink.hasClass('current');
		var isWrongMenuHighlighted = !parentMenu.is('.wp-has-current-submenu, .current') ||
		                              (otherHighlightedMenus.length > 0);

		if (isWrongMenuHighlighted) {
			//Account for users who use the Expanded Admin Menus plugin to keep all menus expanded.
			var shouldCloseOtherMenus = ! $('div.expand-arrow', '#adminmenu').get(0);
			if (shouldCloseOtherMenus) {
				otherHighlightedMenus
					.add('> a', otherHighlightedMenus)
					.removeClass('wp-menu-open wp-has-current-submenu current')
                	.addClass('wp-not-current-submenu');
			}

			var parentMenuAndLink = parentMenu.add('> a.menu-top', parentMenu);
			parentMenuAndLink.removeClass('wp-not-current-submenu');
			if (parentMenu.hasClass('wp-has-submenu')) {
				parentMenuAndLink.addClass('wp-has-current-submenu wp-menu-open');
			}

			//Note: WordPress switches the admin menu between `position: fixed` and `position: relative` depending on
			//how tall it is compared to the browser window. Opening a different submenu can change the menu's height,
			//so we must trigger the position update to avoid bugs. If we don't, we can end up with a very tall menu
			//that's not scrollable (due to being stuck with `position: fixed`).
			if ((typeof window['stickyMenu'] === 'object') && (typeof window['stickyMenu']['update'] === 'function')) {
				window.stickyMenu.update();
			} else {
				//As of WP core revision 29599 (2014-10-05) the `stickyMenu` object no longer exists
				//and the replacement (`setPinMenu` in common.js) is not accessible from an outside scope.
				//We'll resort to faking a resize event to make WP update the menu height and state.
				$(document).trigger('wp-window-resized');
			}

			//Workaround: Prevent the current submenu from "jumping around" when you click an item. This glitch is
			//caused by a `focusin` event handler in common.js. WP adds this handler to all top level menus that
			//are not the current menu. Since we're changing the current menu, we need to also remove this handler.
			if (typeof parentMenu['off'] === 'function') {
				parentMenu.off('focusin.adminmenu');
			}
		}

		if (isWrongItemHighlighted) {
			adminMenu.find('.current').removeClass('current');
			bestMatchLink.addClass('current').closest('li').addClass('current');
		}
	}

    //If a submenu is highlighted, so must be its parent.
    //In some cases, if we decide to stick with the WP-selected highlighted menu,
    //this might not be the case and we'll need to fix it.
    var parentOfHighlightedMenu = $('.wp-submenu a.current', '#adminmenu').closest('.menu-top');
    parentOfHighlightedMenu
        .add('> a.menu-top', parentOfHighlightedMenu)
        .removeClass('wp-not-current-submenu')
        .addClass('wp-has-current-submenu wp-menu-open');

});
