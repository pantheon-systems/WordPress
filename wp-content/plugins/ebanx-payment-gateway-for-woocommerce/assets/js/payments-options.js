;(function($){
  var availableCountries = ['ar', 'br', 'co', 'mx'];

  var getMaxInstalmentsFields = function (country) {
    return $('#woocommerce_ebanx-global_' + country + '_credit_card_instalments');
  };

  var getInterestRateInputs = function (country) {
    return $('.interest-rates-fields.interest-' + country);
  };

  var getInterestInputsToggler = function (country) {
    return $('#woocommerce_ebanx-global_'+ country + '_interest_rates_enabled');
  };

  var fieldsDueDate = $('#woocommerce_ebanx-global_due_date_days');

  var disableFields = function(jqElementList){
    jqElementList.closest('tr').hide();
  };

  var enableFields = function(jqElementList){
    jqElementList.closest('tr').show();
  };

  var updateFields = function (country) {
    var maxInstalments = getMaxInstalmentsFields(country).val();
    var interestRateInputs = getInterestRateInputs(country);
    var interestInputsToggler = getInterestInputsToggler(country);

    disableFields(interestRateInputs);

    if (interestInputsToggler.length == 1 && interestInputsToggler[0].checked) {
      interestRateInputs.each(function() {
        var $this = $(this);
        var idnum = parseInt($this.attr('id').substr(-2));
        if (idnum <= maxInstalments) {
          enableFields($this);
        }
      });
    }
  };

  availableCountries.forEach(function (country) {
    getInterestInputsToggler(country).on('click', function () { updateFields(country) });
    getMaxInstalmentsFields(country).on('change', function () { updateFields(country) });
  });

  // Fields due date
  fieldsDueDate.attr('min', '1');

  // Payments options toggler
  var optionsToggler = $('#woocommerce_ebanx-global_payments_options_title');

  var toggleElements = function() {
    var wasClosed = optionsToggler.hasClass('closed');
    optionsToggler.toggleClass('closed');
    $('.ebanx-payments-option')
      .add($('.ebanx-payments-option').closest('.form-table'))
      .slideToggle('fast');

    //Extra call to update checkout manager stuff on open
    if (wasClosed) {
      availableCountries.forEach(updateFields);
    }

    localStorage.setItem('ebanx_payments_options_toggle', wasClosed ? 'open' : 'closed');
  };

  optionsToggler
    .addClass('togglable')
    .click(toggleElements);

  if (localStorage.getItem('ebanx_payments_options_toggle') != 'open'){
    toggleElements();
  } else {
    //Extra call to update checkout manager stuff if it's already open
    availableCountries.forEach(updateFields);
  }
})(jQuery);
