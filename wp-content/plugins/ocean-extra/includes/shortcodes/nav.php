<?php
/**
 * Nav menu shortcode for the Custom Header style
 */

if ( ! class_exists( 'OceanWP_Nav_Shortcode' ) ) {

	class OceanWP_Nav_Shortcode {

		/**
		 * Start things up
		 *
		 * @since 1.1.1
		 */
		public function __construct() {
			add_shortcode( 'ocean_nav', array( $this, 'nav_shortcode' ) );
		}

		/**
		 * Registers the function as a shortcode
		 *
		 * @since 1.1.1
		 */
		public function nav_shortcode( $atts, $content = null ) {
			$settings = shortcode_atts(
				array(
					'position' => 'left',
				),
				$atts
			);

			$position = $settings['position'];

			// Add classes
			$classes = array( 'custom-header-nav', 'clr' );

			$classes[] = $position;
			$classes   = implode( ' ', $classes ); ?>

			<div class="<?php echo esc_attr( $classes ); ?>">

				<?php
				// Menu
				get_template_part( 'partials/header/nav' );

				// Mobile menu
				get_template_part( 'partials/header/mobile-icon' );
				?>

			</div>

			<?php
		}

	}

}
new OceanWP_Nav_Shortcode();
