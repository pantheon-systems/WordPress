<?php
/**
 * User Name Widget
 *
 * Username widget class file.
 *
 * @since 	1.1.7
 * @package search-wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! class_exists( 'WSAL_AS_Filters_UserNameWidget' ) ) :

	/**
	 * WSAL_AS_Filters_UserNameWidget.
	 *
	 * Class: User Name Widget.
	 *
	 * @since 1.1.7
	 */
	class WSAL_AS_Filters_UserNameWidget extends WSAL_AS_Filters_AbstractWidget {

		/**
		 * Method: Function to render field.
		 *
		 * @since 1.1.7
		 */
		protected function RenderField() {
			?>
			<input type="text"
				class="<?php echo esc_attr( $this->GetSafeName() ); ?>"
				id="<?php echo esc_attr( $this->id ); ?>"
				data-prefix="<?php echo esc_attr( $this->prefix ); ?>"
			/>
			<button id="<?php echo esc_attr( "wsal-add-$this->prefix-filter" ); ?>" class="wsal-add-button dashicons-before dashicons-plus"></button>
			<?php
		}

		/**
		 * Method: Render JS in footer regarding this widget.
		 *
		 * @since 1.1.7
		 */
		public function StaFooter() {
			?>
			<script type="text/javascript">
				jQuery( '<?php echo esc_attr( "#wsal-add-$this->prefix-filter" ); ?>' ).click( function( event ) {
			    	event.preventDefault();
			    	var username_input = jQuery( 'input.<?php echo esc_attr( $this->GetSafeName() ); ?>' );
			    	var username = username_input.val();
			    	if ( username.length == 0 ) return;
			    	var username_filter_value = username_input.attr( 'data-prefix' ) + ':' + username;
			    	window.WsalAs.AddFilter( username_filter_value );
			    } );
			    jQuery( document ).ready( function( $ ) {
			    	// Username validation.
				    var username_error = jQuery( '<span />' );
				    username_error.addClass( 'wsal-input-error' );
				    username_error.text( '* Invalid Username' );
				    var username_label = jQuery( 'label[for="<?php echo esc_attr( $this->id ); ?>"]' );
				    username_label.append( username_error );

				    $( 'input.<?php echo esc_attr( $this->GetSafeName() ); ?>' ).on( 'change keyup paste', function() {
				    	var username_value = $( this ).val();
				    	var username_add_btn = $( '<?php echo esc_attr( "#wsal-add-$this->prefix-filter" ); ?>' );
				    	username_error.hide();
				    	username_add_btn.removeAttr( 'disabled' );

				    	var username_pattern = /^[a-z0-9\s\_\.\\\-\@]+$/i;
				    	if ( username_value.length && ! username_pattern.test( username_value ) ) {
				    		username_error.show();
				    		username_add_btn.attr( 'disabled', 'disabled' );
				    	}
				    } );
			    } );
			</script>
			<?php
		}

	}

endif;
