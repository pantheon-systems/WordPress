/*jshint devel:true */
/*global jQuery */
var WPML_String_Translation = WPML_String_Translation || {};

WPML_String_Translation.TranslationMemory = function ( $ ) {

	var init = function() {
		$(document).ready( function( $ ) {

			$('#icl_string_translations').on('wpml-open-string-translations', function (e, element) {
				var inlineTranslations = $( element );
				var emptyTranslations  = inlineTranslations.find('textarea[name="icl_st_translation"]:empty');

				if ( 0 < emptyTranslations.length ) {
					fetchTranslationMemory( inlineTranslations, emptyTranslations );
				}
			});
		});
	};

	var populateEmptyTranslations = function( emptyTranslations, translationMemory ) {
		$.each( emptyTranslations, function( i ) {
			var empty = $( emptyTranslations[i] );

			var translationObj = translationMemory.filter( function( el ) {
				return empty.data('lang') === el.language;
			}).shift();

			if ( translationObj ) {
				empty.text( translationObj.translation );
			}
		});
	};

	var fetchTranslationMemory = function( inlineTranslations, emptyTranslations ) {
		var toggle = inlineTranslations.parent('.wpml-st-col-string').find('.js-wpml-st-toggle-translations');
		toggle.prepend('<span class="spinner is-active"></span>');

		var original = inlineTranslations.data('original');
		var source_lang = inlineTranslations.data('source-lang');

		$.post(
			ajaxurl,
			{
				action: 'wpml_st_fetch_translations',
				nonce: wpml_translation_memory_nonce.value,
				strings: [ original ],
				languages: {
					source: source_lang,
					target: ''
				}
			},
			function( response ) {
				if (response.data) {
					populateEmptyTranslations( emptyTranslations, response.data );
					toggle.find('.spinner').remove();
				}
			}
		);
	};

	init();
};

new WPML_String_Translation.TranslationMemory( jQuery );