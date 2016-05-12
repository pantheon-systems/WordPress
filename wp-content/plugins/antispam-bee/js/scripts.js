jQuery(document).ready(
	function($) {
		function ab_flag_spam() {
			var $$ = $('#ab_flag_spam'),
				nextAll = $$.parent('li').nextAll();

			nextAll.css(
				'visibility',
				( $$.is(':checked') ? 'visible' : 'hidden' )
			);
		}

		$('#ab_flag_spam').on(
			'change',
			ab_flag_spam
		);

		ab_flag_spam();
	}
);