var search_results_div = jQuery('#' + solr.search_results_id);
var solr_first_load = true;
if (solr.allow_ajax && search_results_div.length) {
	search_results_div.html('<div id="solr-search-loading"></div>');
}

jQuery(document).ready(function ($) {
	var the_body = $('body');

	the_body.on('change', '#solr_s', function (e) {
		$('#solr_facet input').prop('checked', false);
	});

	var search_results_div = $('#' + solr.search_results_id);

	if (0 === search_results_div.length) {
		search_results_div = $('#solr_search_results');
	}

	if (solr.allow_ajax && search_results_div.length) {
		var facets = $('#solr_facets');
		var search_form = $('#solr_facet');
		var loadResults = function () {
			search_results_div.hide();
			var args = search_form.serializeArray();
			args.push({name: 'action', value: 'solr_search'});
			$.get(solr.ajaxurl, args, function (res) {
				var results = jQuery.parseJSON(res);

				search_results_div.html(results.posts).fadeIn();
				facets.html(results.facets);

				$('ul', facets).each(function(n) {
					var facetUL = $(this);
					if ($('.facet_check:checked', facetUL).length > 1){
						$('a.solr_reset', facetUL).show();
					}
				});

				$('#solr_paged').val(1);

			});
		};
		var solr_submit = function () {
			search_results_div.html('<div id="solr-search-loading"></div>');
			loadResults();
		};

		the_body.on('change', '.facet_check', function (e) {
			solr_submit();
		});

		the_body.on('click', '.facet_link', function (e) {
			var facet_id = $('#' + $(this).data('for'));
			if (facet_id.prop('checked')) {
				facet_id.prop('checked', false);
			} else {
				facet_id.prop('checked', true);
			}
			solr_submit();
		});

		the_body.on('click', '.solr_reset', function (e) {
			$('#' + $(this).data('for') + ' input').prop('checked', false);
			document.getElementById('solr_paged').value = 1;
			e.preventDefault();
			solr_submit();
		});

		search_form.submit(function (event) {
			event.preventDefault();
			loadResults();
			return false;
		});

		if (solr.allow_ajax && solr_first_load) {
			solr_first_load = false;
			solr_submit();
		}

	}
});