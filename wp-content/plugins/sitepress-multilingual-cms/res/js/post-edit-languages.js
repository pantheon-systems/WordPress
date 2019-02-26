/*globals jQuery, post_edit_languages_data, icl_ajx_url */

function build_language_links(data, $, container) {
	"use strict";

	var queryString;
	var urlData;
	if (data.hasOwnProperty('language_links')) {
		var languages_container = $('<ul></ul>');
		languages_container.prependTo(container);

		/** @namespace data.language_links */
		/** @namespace data.statuses */
		for (var i = 0; i < data.language_links.length; i++) {
			var item = data.language_links[i];
			var is_current = item.current || false;
			var language_code = item.code;
			var language_count = item.count;
			var language_name = item.name;
			var statuses = item.statuses;
			var type = item.type;

			var language_item = $('<li></li>');
			language_item.addClass('language_' + language_code);
			if (i > 0) {
				language_item.append('&nbsp;|&nbsp;');
			}

			var language_summary = $('<span></span>');
			language_summary.addClass('count');
			language_summary.addClass(language_code);
			language_summary.text(' (' + ( language_count < 0 ? "0" : language_count ) + ')');

			var current;
			if (is_current) {
				current = $('<strong></strong>');
			} else if (language_count >= 0) {
				current = $('<a></a>');
				urlData = {
					post_type: type,
					lang:      language_code
				};

				if (statuses && statuses.length) {
					urlData.post_status = statuses.join(',');
				}
				queryString = $.param(urlData);
				current.attr('href', '?' + queryString);
			} else {
				current = $('<span></span>');
			}

			current.append(language_name);
			current.appendTo(language_item);
			current.append(language_summary);

			language_item.appendTo(languages_container);
		}

		$(document).trigger('wpml_language_links_added', [languages_container]);
	}
}

jQuery(document).ready(function ($) {
	"use strict";

	var data = post_edit_languages_data;
	var subsubsub = $('.subsubsub');
	var container = subsubsub.next('.icl_subsubsub');

	if (container.length === 0) {
		container = $('<div></div>');
		container.addClass('icl_subsubsub');

		subsubsub.after(container);
	}

	build_language_links(data, $, container);
});