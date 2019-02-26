jQuery(document).ready(function ($) {
  var buttonContainer = $('.ebanx-one-click-button-container'); 
  var button = $('#ebanx-one-click-button');
  var hidden = $('#ebanx-one-click');
  var close = $('.ebanx-one-click-close-button');
  var tooltip = $('.ebanx-one-click-tooltip');
  var cvv = $('#ebanx-one-click-cvv-input');
  var payButton = $('.ebanx-one-click-pay');
  var instalments = $('.ebanx-instalments');
  var form = $('#ebanx-one-click-form');
  var isProcessing = false;

  var addError = function (el) {
    $(el).addClass('is-invalid');
  };

  var removeError = function (el) {
    $(el).removeClass('is-invalid');
  };

  tooltip.keypress(function(e) {
    if (e.keyCode === 13) {
      e.preventDefault();
      form.submit();
    }
    return true;
  });

  button.on('click', function (e) {
    e.preventDefault();
    tooltip.toggleClass('is-active');
  });

  close.on('click', function (e) {
    e.preventDefault();
    tooltip.removeClass('is-active');
  });

  form.on('submit', function () {
    payButton.text(payButton.attr('data-processing-label')).attr('disabled', 'disabled');
    return true;
  });

  cvv.on('keyup', function () {
    var value = cvv.val();

    if (!(value.length >= 3 && value.length <= 4)) {
      addError(cvv);
    }
    else {
      removeError(cvv);
    }
  });

  // Align the tooltip
  if (buttonContainer.css('text-align') === 'center') {
    tooltip.css({
      left: '50%',
      marginLeft: -Math.abs(tooltip.outerWidth() / 2)
    });
  }
  else if (buttonContainer.css('text-align') === 'right') {
    tooltip.css({
      left: 'auto',
      right: 0
    });
  }
});
