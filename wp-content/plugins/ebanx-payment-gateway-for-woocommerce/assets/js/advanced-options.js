;(function($) {
  // Checkout manager managed fields
  var modesField = $('#woocommerce_ebanx-global_brazil_taxes_options');
  var fields = $('.ebanx-checkout-manager-field');
  var fieldsToggler = $('#woocommerce_ebanx-global_checkout_manager_enabled');
  var fieldBrazilTaxes = $('.brazil-taxes');
  var ebanxAdvancedOptionEnable = $('.ebanx-advanced-option-enable');
  var countryPayments = {
    brazil: $('#woocommerce_ebanx-global_brazil_payment_methods'),
    chile: $('#woocommerce_ebanx-global_chile_payment_methods'),
		argentina: $( '#woocommerce_ebanx-global_argentina_payment_methods' ),
		colombia: $( '#woocommerce_ebanx-global_colombia_payment_methods' ),
		peru: $( '#woocommerce_ebanx-global_peru_payment_methods' )
  };

  var disableFields = function(jqElementList) {
    jqElementList.closest('tr').hide();
  };

  var enableFields = function(jqElementList) {
    jqElementList.closest('tr').show();
  };

  var updateFields = function () {
    var modes = modesField.val();
    var brazilVal = countryPayments.brazil.val();
    var chileVal = countryPayments.chile.val();
    var colombiaVal = countryPayments.colombia.val();
		var argentinaVal = countryPayments.argentina.val();
		var peruVal = countryPayments.peru.val();
    disableFields(fields);
    disableFields(fieldBrazilTaxes);

    if (brazilVal != null && brazilVal.length > 0) {
      enableFields(fieldBrazilTaxes);
    }

		if (fieldsToggler.length === 1 && fieldsToggler[0].checked) {

      enableFields(fields.filter('.always-visible'));
      if (brazilVal != null && brazilVal.length > 0 && modes != null) {
        for (var i in modes) {
          enableFields(fields.filter('.' + modes[i]));
        }

				if (modes.length === 2) {
          enableFields(fields.filter('.cpf_cnpj'));
        }
      }

      if (chileVal != null && chileVal.length > 0) {
        enableFields(fields.filter('.ebanx-chile-document'));
        enableFields(fields.filter('.ebanx-chile-bdate'));
      }

      if (colombiaVal != null && colombiaVal.length > 0) {
        enableFields(fields.filter('.ebanx-colombia-document'));
      }

			if ( argentinaVal != null && argentinaVal.length > 0 ) {
				enableFields( fields.filter( '.ebanx-argentina-document' ) );
				enableFields( fields.filter( '.ebanx-argentina-document-type' ) );
			}

			if (peruVal != null && peruVal.length > 0) {
				enableFields( fields.filter( '.ebanx-peru-document' ) );
			}

      if (brazilVal == null && chileVal == null && colombiaVal == null) {
        $('#woocommerce_ebanx-global_advanced_options_title').hide();
        disableFields(ebanxAdvancedOptionEnable);
      }

      else {
        $('#woocommerce_ebanx-global_advanced_options_title').css('display', 'table');
        enableFields(ebanxAdvancedOptionEnable);
      }

    }
  };

  fieldsToggler
    .click(function () {
      updateFields();
    });

  modesField.change(function () {
    updateFields();
  });

  for (var i in countryPayments) {
    countryPayments[i].change(function () {
      updateFields();
    });
  }

  // Advanced options toggler
  var optionsToggler = $('#woocommerce_ebanx-global_advanced_options_title');

  var toggleElements = function () {
    var wasClosed = optionsToggler.hasClass('closed');
    optionsToggler.toggleClass('closed');
    $('.ebanx-advanced-option')
      .add($('.ebanx-advanced-option').closest('.form-table'))
      .slideToggle('fast');

    //Extra call to update checkout manager stuff on open
    if (wasClosed) {
      updateFields();
    }

    localStorage.setItem('ebanx_advanced_options_toggle', wasClosed ? 'open' : 'closed');
  };
  optionsToggler
    .addClass('togglable')
    .click(toggleElements);

    if (localStorage.getItem('ebanx_advanced_options_toggle') != 'open') {
      toggleElements();
    } else {
      //Extra call to update checkout manager stuff if it's already open
      updateFields();
    }
})(jQuery);
