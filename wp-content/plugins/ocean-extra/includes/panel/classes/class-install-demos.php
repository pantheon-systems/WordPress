<?php
/**
 * Install demos page
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class OWP_Install_Demos {

	/**
	 * Start things up
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ), 999 );
	}

	/**
	 * Add sub menu page for the custom CSS input
	 *
	 * @since 1.0.0
	 */
	public function add_page() {

		// If Pro Demos activated
		if ( class_exists( 'Ocean_Pro_Demos' ) ) {
			$title = '<span style="color: #36c786">' . esc_html__( 'Install Demos', 'ocean-extra' ) . '</span>';
		} else {
			$title = esc_html__( 'Install Demos', 'ocean-extra' );
		}

		add_submenu_page(
			'oceanwp',
			esc_html__( 'Install Demos', 'ocean-extra' ),
			$title,
			'manage_options',
			'oceanwp#install-demos',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Image URL
	 *
	 * @since 1.0.0
	 */
	public static function img_url( $demo ) {
		$url = 'https://demos.oceanwp.org/0images/' . esc_attr( $demo ) . '.png';

		// Return
		return apply_filters( 'owp_demos_img_url', $url );
	}

	/**
	 * Settings page output
	 *
	 * @since 1.0.0
	 */
	public function create_admin_page() {

		// Theme branding
		$brand = oceanwp_theme_branding(); ?>

		<div class="owp-demo-wrap wrap">

			<h2><?php echo esc_attr( $brand ); ?> - <?php esc_attr_e( 'Install Demos', 'ocean-extra' ); ?></h2>

			<div class="theme-browser rendered">

				<?php
				// Vars
				$demos = OceanWP_Demos::get_demos_data();
				$el_demos = $demos['elementor'];
				$gu_demos = '';
				$el_cat = OceanWP_Demos::get_demo_all_categories( $el_demos );

				// If Gutenberg
				if ( ! empty( $demos['gutenberg'] ) ) {
					$gu_demos = $demos['gutenberg'];
					$gu_cat = OceanWP_Demos::get_demo_all_categories( $gu_demos );
				}

				?>

				<div class="owp-header-bar">
					<nav class="owp-navigation">

						<?php
						if ( ! empty( $gu_demos ) ) { ?>
							<ul class="owp-demo-linked">
								<li class="active"><a href="#" class="owp-elementor-link"><?php esc_html_e( 'Elementor', 'ocean-extra' ); ?></a></li>
								<li><a href="#" class="owp-gutenberg-link"><?php esc_html_e( 'Gutenberg', 'ocean-extra' ); ?></a></li>
							</ul>
						<?php
						} ?>

						<?php
						if ( ! empty( $el_cat ) ) { ?>
							<ul class="elementor-demos">
								<li class="active"><a href="#all" class="owp-navigation-link"><?php esc_html_e( 'All', 'ocean-extra' ); ?></a></li>
								<?php foreach ( $el_cat as $key => $name ) { ?>
									<li><a href="#<?php echo esc_attr( $key ); ?>" class="owp-navigation-link"><?php echo esc_html( $name ); ?></a></li>
								<?php } ?>
							</ul>
						<?php
						} ?>

						<?php
						if ( ! empty( $gu_demos )
							&& ! empty( $gu_cat ) ) { ?>
							<ul class="gutenberg-demos" style="display: none;">
								<li class="active"><a href="#all" class="owp-navigation-link"><?php esc_html_e( 'All', 'ocean-extra' ); ?></a></li>
								<?php foreach ( $gu_cat as $key => $name ) { ?>
									<li><a href="#<?php echo esc_attr( $key ); ?>" class="owp-navigation-link"><?php echo esc_html( $name ); ?></a></li>
								<?php } ?>
							</ul>
						<?php
						} ?>

					</nav>
					<div clas="owp-search">
						<input type="text" class="owp-search-input" name="owp-search" value="" placeholder="<?php esc_html_e( 'Search demos...', 'ocean-extra' ); ?>">
					</div>
				</div>

				<div class="themes wp-clearfix elementor-items">

					<?php
					// Loop through all demos
					foreach ( $el_demos as $demo => $key ) {

						// Vars
						$item_categories = OceanWP_Demos::get_demo_item_categories( $key ); ?>

						<div class="theme-wrap" data-categories="<?php echo esc_attr( $item_categories ); ?>" data-name="<?php echo esc_attr( strtolower( $demo ) ); ?>">

							<div class="theme owp-open-popup" data-demo-id="<?php echo esc_attr( $demo ); ?>" data-demo-type="elementor" >

								<div class="theme-screenshot">
									<img src="<?php echo $this->img_url( $demo ); ?>" />

									<div class="demo-import-loader preview-all preview-all-<?php echo esc_attr( $demo ); ?>"></div>

									<div class="demo-import-loader preview-icon preview-<?php echo esc_attr( $demo ); ?>"><i class="custom-loader"></i></div>
								</div>

								<div class="theme-id-container">

									<h2 class="theme-name" id="<?php echo esc_attr( $demo ); ?>"><span><?php echo ucwords( $demo ); ?></span></h2>

									<div class="theme-actions">
										<a class="button button-primary" href="https://<?php echo esc_attr( $demo ); ?>.oceanwp.org/" target="_blank"><?php _e( 'Live Preview', 'ocean-extra' ); ?></a>
									</div>

								</div>

							</div>

						</div>

					<?php
					} ?>

				</div>

				<?php
				if ( ! empty( $gu_demos ) ) { ?>

					<div class="themes wp-clearfix gutenberg-items" style="display: none;">

						<?php
						// Loop through all demos
						foreach ( $gu_demos as $demo => $key ) {

							// Vars
							$item_categories = OceanWP_Demos::get_demo_item_categories( $key ); ?>

							<div class="theme-wrap" data-categories="<?php echo esc_attr( $item_categories ); ?>" data-name="<?php echo esc_attr( strtolower( $demo ) ); ?>">

								<div class="theme owp-open-popup" data-demo-id="<?php echo esc_attr( $demo ); ?>" data-demo-type="gutenberg">

									<div class="theme-screenshot">
										<img src="<?php echo $this->img_url( $demo ); ?>" />

										<div class="demo-import-loader preview-all preview-all-<?php echo esc_attr( $demo ); ?>"></div>

										<div class="demo-import-loader preview-icon preview-<?php echo esc_attr( $demo ); ?>"><i class="custom-loader"></i></div>
									</div>

									<div class="theme-id-container">

										<h2 class="theme-name" id="<?php echo esc_attr( $demo ); ?>"><span><?php echo ucwords( $demo ); ?></span></h2>

										<div class="theme-actions">
											<a class="button button-primary" href="https://<?php echo esc_attr( $demo ); ?>.oceanwp.org/" target="_blank"><?php _e( 'Live Preview', 'ocean-extra' ); ?></a>
										</div>

									</div>

								</div>

							</div>

						<?php
						} ?>

					</div>

				<?php
				} ?>

			</div>

		</div>

	<?php }
}
new OWP_Install_Demos();