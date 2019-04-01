;(function($){
	var woocommerceSettings = $('.woocommerce_page_wc-settings');

	if (woocommerceSettings.length < 1)
		return;

	var subsub = $('.subsubsub > li');

	for (var i = 0, t = subsub.length; i < t; ++i) {
		var s = $(subsub[i]);
		var sub = $(s).find('a');

		if (sub.text().indexOf('EBANX -') !== -1)
			continue;

		s.css({
			display: 'inline-block'
		});
	}

	var last = subsub
		.filter(function () {
			return $(this).css('display') === 'inline-block';
		})
		.last();

	last.html(last.html().replace(/ \| ?/g, ''));

	$('.ebanx-select').select2();
})(jQuery);
