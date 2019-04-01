/*jshint devel:true */
/*global jQuery */
var WPML_String_Translation = WPML_String_Translation || {};

WPML_String_Translation.CustomTypeSlugUI = function () {
	var init = function () {
		jQuery(document).ready(function() {
			jQuery('.js-translate-slug-original').on('change', change_original_lang);
			jQuery('.js-toggle-slugs-table').on('click', toggle_slugs_table );
		});
	};
	
	var change_original_lang = function () {
		var new_lang = jQuery(this).val();
		
		jQuery(this).closest('.js-custom-type-slugs').find('input').each( function() {
			var input_lang = jQuery(this).data('lang');
			if (input_lang == new_lang) {
				jQuery(this).closest('tr').hide();
			} else {
				jQuery(this).closest('tr').show();
			}
		})
	};

	var toggle_slugs_table = function(e) {
		e.preventDefault();
		var toggle = jQuery(e.currentTarget);
		var slugsTable = toggle.closest('.icl_slug_translation_choice').find('.js-custom-type-slugs');

		slugsTable.fadeToggle(400, function() {
			var arrow = toggle.find('span');
			arrow.toggleClass('otgs-ico-caret-up', slugsTable.is(':visible'));
			arrow.toggleClass('otgs-ico-caret-down', ! slugsTable.is(':visible'));
		});
	};

	init();
};

WPML_String_Translation.custom_post_ui = new WPML_String_Translation.CustomTypeSlugUI();

