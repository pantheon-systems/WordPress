( function($) {
	ET_PageBuilder.Theme_Compat_Make = window.wp.Backbone.View.extend({
		initialize : function() {
			this.listenTo( Backbone.Events, 'et-activate-builder', this.activateDiviBuilder );
			this.listenTo( Backbone.Events, 'et-deactivate-builder', this.deactivateDiviBuilder );
		},

		activateDiviBuilder : function() {
			this.toggleMakeBuilder( 'off' );
		},

		deactivateDiviBuilder : function() {
			this.toggleMakeBuilder( 'on' );
		},

		toggleMakeBuilder : function( toggle ) {
			var $make_builder      = $('#ttfmake-builder'),
				$template_selector = $('#page_template'),
				$template_selector_builder = $template_selector.find('option[value="template-builder.php"]');

			if ( toggle === 'on' ) {
				$make_builder.show();
				$template_selector_builder.attr({ 'selected' : 'selected' }).removeAttr( 'disabled' );
			} else {
				$make_builder.hide();
				$template_selector_builder.attr({ 'disabled' : 'disabled' }).removeAttr( 'selected' );
			}

			$template_selector.trigger( 'change' );
		}
	});

	// Initialize view
	var ET_PageBuilder_Theme_Compat = new ET_PageBuilder.Theme_Compat_Make;

	// On page load, disable Make Builder if Divi Builder is already used
	if ( $('#et_pb_toggle_builder.et_pb_builder_is_used').length ) {
		ET_PageBuilder_Theme_Compat.activateDiviBuilder();
	}
} )(jQuery);