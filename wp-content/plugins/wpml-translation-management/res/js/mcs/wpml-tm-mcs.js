jQuery(document).ready(function () {

	var ajax_success_action = function( response, response_text ) {
		if( response.success ) {
			response_text.text( icl_ajx_saved );
		} else {
			response_text.text( icl_ajx_error );
			response_text.show();
		}
		setTimeout(function () {
			response_text.fadeOut('slow');
		}, 2500);
	};

    jQuery( '#js-translated_document-options-btn' ).click(function(){

		var document_status = jQuery( 'input[name*="icl_translated_document_status"]:checked' ).val(),
			page_url = jQuery( 'input[name*="icl_translated_document_page_url"]:checked' ).val(),
			response_text = jQuery( '#icl_ajx_response_tdo' ),
			spinner = '<span id="js-document-options-spinner" style="float: inherit; margin: 0" class="spinner is-active"></span>';

		response_text.html( spinner );
		response_text.show();

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'wpml_translated_document_options',
				nonce: jQuery( '#wpml-translated-document-options-nonce' ).val(),
				document_status: document_status,
				page_url: page_url
			},
			success: function ( response ) {
				ajax_success_action( response, response_text );
			}
		});
	});

	jQuery( '#translation-pickup-mode' ).click(function(){
		var pickup_mode = jQuery( 'input[name*="icl_translation_pickup_method"]:checked' ).val(),
			response_text = jQuery( '#icl_ajx_response_tpm' ),
			spinner = '<span id="js-document-options-spinner" style="float: inherit; margin: 0" class="spinner is-active"></span>';

		response_text.html( spinner );
		response_text.show();

		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'wpml_save_translation_pickup_mode',
				nonce: jQuery( '#wpml_save_translation_pickup_mode' ).val(),
				pickup_mode: pickup_mode
			},
			success: function ( response ) {
				ajax_success_action( response, response_text );
			}
		});
	});

	jQuery("#wpml-tm-general-settings input[name='wpml-disabling']").on("change", function () {
		var checkbox = jQuery(this);
		checkbox.prop("disabled", "disabled");

		var is_disabled = checkbox.is(":checked");
		var nonce = checkbox.data("nonce");

		var spinner = checkbox.next(".spinner");
        spinner.css("visibility", "visible");

        var message_box = jQuery("<span></span>").addClass("js-wpml-tm-messages wpml-tm-messages");
        message_box.insertAfter(spinner);

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wpml_end_user_get_info',
                _wpnonce: nonce,
                value: is_disabled ? 1 : 0
            },
            error: function (response) {
                message_box.addClass("error");
                ajax_success_action({"success" : 0}, message_box);

            },
			success: function(reponse) {
                message_box.addClass("updated");
                ajax_success_action({"success" : 1}, message_box);
			},
            complete: function() {
                checkbox.prop("disabled", "");
                spinner.css("visibility", "hidden");
            }
        });
    })
});
