<?php
/**
 * Dynamic date shortcode
 */

if ( ! class_exists( 'OceanWP_Date_Shortcode' ) ) {

	class OceanWP_Date_Shortcode {

		/**
		 * Start things up
		 *
		 * @since 1.1.8
		 */
		public function __construct() {
			add_shortcode( 'oceanwp_date', array( $this, 'date_shortcode' ) );
		}

		/**
		 * Registers the function as a shortcode
		 *
		 * @since 1.1.8
		 */
		public function date_shortcode( $atts, $content = null ) {
			$settings = shortcode_atts(
				array(
					'year' => '',
				),
				$atts
			);

			$year = $settings['year'];

			// Var
			$date = '';

			if ( '' != $year ) {
				$date .= $year . ' - ';
			}

			$date .= date( 'Y' );

			return esc_attr( $date );
		}

	}

}
new OceanWP_Date_Shortcode();
