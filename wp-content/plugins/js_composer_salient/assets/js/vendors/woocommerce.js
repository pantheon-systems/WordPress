if ( ! window.ajaxurl ) {
	window.ajaxurl = window.location.href;
}
var vcWoocommerceProductAttributeFilterDependencyCallback;

vcWoocommerceProductAttributeFilterDependencyCallback = function () {
	(function ( $, that ) {
		var $filterDropdown, $empty;

		$filterDropdown = $( '[data-vc-shortcode-param-name="filter"]', that.$content );
		$filterDropdown.removeClass( 'vc_dependent-hidden' );
		$empty = $( '#filter-empty', $filterDropdown );
		if ( $empty.length ) {
			$empty.parent().remove();
			$( '.edit_form_line',
				$filterDropdown ).prepend( $( '<div class="vc_checkbox-label"><span>No values found</span></div>' ) );
		}
		$( 'select[name="attribute"]', that.$content ).change( function () {
			$( '.vc_checkbox-label', $filterDropdown ).remove();
			$filterDropdown.removeClass( 'vc_dependent-hidden' );

			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: window.ajaxurl,
				data: {
					action: 'vc_woocommerce_get_attribute_terms',
					attribute: this.value,
					_vcnonce: window.vcAdminNonce
				}
			} ).done( function ( data ) {
				if ( 0 < data.length ) {
					$( '.edit_form_line', $filterDropdown ).prepend( $( data ) );
				} else {
					$( '.edit_form_line',
						$filterDropdown ).prepend( $( '<div class="vc_checkbox-label"><span>No values found</span></div>' ) );
				}
			} );
		} );
	}( window.jQuery, this ));
};
