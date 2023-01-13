<?php
/**
 * Logo shortcode for the Custom Header style
 */

if ( ! class_exists( 'OceanWP_Logo_Shortcode' ) ) {

	class OceanWP_Logo_Shortcode {

		/**
		 * Start things up
		 *
		 * @since 1.1.1
		 */
		public function __construct() {
			add_shortcode( 'ocean_logo', array( $this, 'logo_shortcode' ) );
		}

		/**
		 * Registers the function as a shortcode
		 *
		 * @since 1.1.1
		 */
		public function logo_shortcode( $atts, $content = null ) {
			$settings = shortcode_atts(
				array(
					'position' => 'left',
				),
				$atts
			);

			$position = $settings['position'];

			// Add classes
			$classes   = array( 'custom-header-logo', 'clr' );
			$classes[] = $position;
			$classes   = implode( ' ', $classes ); ?>

			<div class="<?php echo esc_attr( $classes ); ?>">

				<?php
				// Logo
				get_template_part( 'partials/header/logo' );
				?>

			</div>

			<?php
		}

	}

}
new OceanWP_Logo_Shortcode();
