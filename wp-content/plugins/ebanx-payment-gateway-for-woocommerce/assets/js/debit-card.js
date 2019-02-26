/* global wc_ebanx_params */
EBANX.config.setMode(wc_ebanx_params.mode);
EBANX.config.setPublishableKey(wc_ebanx_params.key);

// TODO: Create abstract card js to use on debit and debit ?

jQuery( function($) {
	var wc_ebanx_form = {
		init: function( form ) {
			this.form = form;

			$(this.form)
				.on('click', '#place_order', this.onSubmit)
				.on('submit checkout_place_order_ebanx-debit-card');

			$(document)
				.on(
					'ebanxErrorDebitCard',
					this.onError
				);
		},

		isEBANXPaymentMethod: function () {
			return $('input[value=ebanx-debit-card]').is(':checked');
		},

		hasToken: function () {
			return 0 < $('input#ebanx_debit_token').length;
		},

		block: function () {
			wc_ebanx_form.form.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function () {
			wc_ebanx_form.form.unblock();
		},

		onError: function (e, res) {
      		wc_ebanx_form.removeErrors();

			$('#ebanx-debit-cart-form').prepend('<p class="woocommerce-error">' + (res.response.error.err.message || 'Some error happened. Please, verify the data of your debit card and try again.') + '</p>');

			$('body, html').animate({
				scrollTop: $('#ebanx-debit-cart-form').find('.woocommerce-error').offset().top - 20
			});

			wc_ebanx_form.unblock();
		},

		removeErrors: function () {
		  $('.woocommerce-error, .ebanx_debit_token').remove();
		},

		onSubmit: function (e) {
      		wc_ebanx_form.removeHiddenInputs();

			if (wc_ebanx_form.isEBANXPaymentMethod()) {
				e.preventDefault();
				e.stopPropagation();
				e.stopImmediatePropagation();

				wc_ebanx_form.block();

				var card      = $('#ebanx-debit-card-number').val();
				var cvv       = $('#ebanx-debit-card-cvv').val();
				var expires   = $('#ebanx-debit-card-expiry').payment('cardExpiryVal');
				var card_name = $('#ebanx-debit-card-holder-name').val();
				var country   = $('#billing_country, input[name*="billing_country"]').val().toLowerCase();

				EBANX.config.setCountry(country);

				var debitcard = {
					"card_number": parseInt(card.replace(/ /g,'')),
					"card_name": card_name,
					"card_due_date": (parseInt( expires['month'] ) || 0) + '/' + (parseInt( expires['year'] ) || 0),
					"card_cvv": cvv
				};

				wc_ebanx_form.renderCvv(debitcard.card_cvv);

				EBANX.card.createToken(debitcard, wc_ebanx_form.onEBANXReponse);
			}
		},

		onCCFormChange: function () {
			$('.woocommerce-error, .ebanx_debit_token').remove();
		},

		onEBANXReponse: function (response) {
			if ( response.data && (response.data.status == 'ERROR' || !response.data.token)) {
				$( document ).trigger('ebanxErrorDebitCard', { response: response } );

        		wc_ebanx_form.removeHiddenInputs();
			} else {
				wc_ebanx_form.form.append('<input type="hidden" name="ebanx_debit_token" id="ebanx_debit_token" value="' + response.data.token + '"/>');
				wc_ebanx_form.form.append('<input type="hidden" name="ebanx_masked_card_number" id="ebanx_masked_card_number" value="' + response.data.masked_card_number + '"/>');
				wc_ebanx_form.form.submit();
			}
		},

		renderCvv: function (cvv) {
		  wc_ebanx_form.form.append('<input type="hidden" name="ebanx_billing_cvv" id="ebanx_billing_cvv" value="' + cvv + '">');
		},

		removeHiddenInputs: function () {
		  $('#ebanx_debit_token').remove();
		  $('#ebanx_billing_cvv').remove();
		  $('#ebanx_masked_card_number').remove();
		}
	};

	wc_ebanx_form.init( $( "form.checkout, form#order_review, form#add_payment_method, form.woocommerce-checkout" ) );
} );
