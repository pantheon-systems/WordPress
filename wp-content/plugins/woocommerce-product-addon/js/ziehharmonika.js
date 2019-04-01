// @author Tim himself

(function($) {
	var settings;
	$.fn.ziehharmonika = function(actionOrSettings, parameter) {
		if (typeof actionOrSettings === 'object' || actionOrSettings === undefined) {
			// Default settings:
			settings = $.extend({
				// To use a headline tag other than h3, adjust or overwrite ziehharmonika.css as well
				headline: 'h4',
				// Give headlines a certain prefix, e.g. "♫ My headline"
				prefix: false,
				// Only 1 accordion can be open at any given time
				highlander: true,
				// Allow or disallow last open accordion to be closed
				collapsible: false,
				// Arrow down under headline
				arrow: true,
				// Opened/closed icon on the right hand side of the headline (either false or JSON containing symbols or image paths)
				collapseIcons: {
					opened: '&ndash;',
					closed: '+'
				},
				// Collapse icon left or right
				collapseIconsAlign: 'right',
				// Scroll to opened accordion element
				scroll: false
			}, actionOrSettings);
		}
		// actions
		if (actionOrSettings == "open") {
			if (settings.highlander) {
				$(this).ziehharmonika('forceCloseAll');
			}
			var ogThis = $(this);
			$(this).addClass('active').next('div').slideDown(400, function() {
				if (settings.collapseIcons) {
					$('.collapseIcon', ogThis).html(settings.collapseIcons.opened);
				}
				// parameter: scroll to opened element
				if (parameter !== false) {
					smoothScrollTo($(this).prev(settings.collapseIcons));
				}
			});
			return this;
		} else if (actionOrSettings == "close" || actionOrSettings == "forceClose") {
			// forceClose ignores collapsible setting
			if (actionOrSettings == "close" && !settings.collapsible && $(settings.headline + '[class="active"]').length == 1) {
				return this;
			}
			var ogThis = $(this);
			$(this).removeClass('active').next('div').slideUp(400, function() {
				if (settings.collapseIcons) {
					$('.collapseIcon', ogThis).html(settings.collapseIcons.closed);
				}
			});
			return this;
		} else if (actionOrSettings == "closeAll") {
			$(settings.headline).ziehharmonika('close');
		} else if (actionOrSettings == "forceCloseAll") {
			// forceCloseAll ignores collapsible setting
			$(settings.headline).ziehharmonika('forceClose');
		}

		if (settings.prefix) {
			$(settings.headline, this).attr('data-prefix', settings.prefix);
		}
		if (settings.arrow) {
			$(settings.headline, this).append('<div class="arrowDown"></div>');
		}
		if (settings.collapseIcons) {
			$(settings.headline, this).each(function(index, el) {
				if ($(this).hasClass('active')) {
					$(this).append('<div class="collapseIcon">'+settings.collapseIcons.opened+'</div>');
				} else {
					$(this).append('<div class="collapseIcon">'+settings.collapseIcons.closed+'</div>');
				}
			});
		}
		if (settings.collapseIconsAlign == 'left') {
			$('.collapseIcon, ' + settings.headline).addClass('alignLeft');
		}

		$(settings.headline, this).click(function() {
			if ($(this).hasClass('active')) {
				$(this).ziehharmonika('close');
			} else {
				$(this).ziehharmonika('open', settings.scroll);
			}
		});
	};
}(jQuery));

function smoothScrollTo(element, callback) {
	var time = 500;
	jQuery('html, body').animate({
		scrollTop: jQuery(element).offset().top
	}, time, callback);
}