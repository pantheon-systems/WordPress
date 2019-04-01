jQuery(document).ready(function ($) {
  var clipboard = new Clipboard('.ebanx-button--copy');

  clipboard.on('success', function(e) {
    var $target = $(e.trigger);

    $target
    .addClass('ebanx-button--copy-success')
    .text('✔︎ Copiado!');

    setTimeout(function() {
      $target
      .removeClass('ebanx-button--copy-success')
      .text('Copiar');

    }, 2000);
  });

  clipboard.on('error', function(e) {
    var $target = $(e.trigger);

    $target
      .addClass('ebanx-button--copy-error')
      .text('Erro! :(');

    setTimeout(function() {
      $target
        .addClass('ebanx-button--copy-error')
        .text('Copiar');
    }, 2000);
  });

  // iFrame Resizer
  var iframe = $('.woocommerce-order-received iframe');

  if (iframe) {
    var resizeIframe = function resizeIframe() {
	  iframe.height(iframe.contents().height());
    };

    $(window).on('load', function () {
      resizeIframe();
    });

    iframe.contents().on('resize', function () {
      resizeIframe();
    });
  }
});
