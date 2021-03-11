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
			$title = '<span style="color: #36c786">' . esc_html__( 'Install Demos', 'ocean-pro-demos' ) . '</span>';
		} else {
			$title = esc_html__( 'Install Demos', 'ocean-extra' );
		}

		add_submenu_page(
			'oceanwp-panel',
			esc_html__( 'Install Demos', 'ocean-extra' ),
			$title,
			'manage_options',
			'oceanwp-panel-install-demos',
			array( $this, 'create_admin_page' )
		);
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
				$categories = OceanWP_Demos::get_demo_all_categories( $demos ); ?>

				<?php if ( ! empty( $categories ) ) : ?>
					<div class="owp-header-bar">
						<nav class="owp-navigation">
							<ul>
								<li class="active"><a href="#all" class="owp-navigation-link"><?php esc_html_e( 'All', 'ocean-extra' ); ?></a></li>
								<?php foreach ( $categories as $key => $name ) : ?>
									<li><a href="#<?php echo esc_attr( $key ); ?>" class="owp-navigation-link"><?php echo esc_html( $name ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</nav>
						<div clas="owp-search">
							<input type="text" class="owp-search-input" name="owp-search" value="" placeholder="<?php esc_html_e( 'Search demos...', 'ocean-extra' ); ?>">
						</div>
					</div>
				<?php endif; ?>

				<div class="themes wp-clearfix">

					<?php
					// Loop through all demos
					foreach ( $demos as $demo => $key ) {

						// Vars
						$item_categories = OceanWP_Demos::get_demo_item_categories( $key ); ?>

						<div class="theme-wrap" data-categories="<?php echo esc_attr( $item_categories ); ?>" data-name="<?php echo esc_attr( strtolower( $demo ) ); ?>">

							<div class="theme owp-open-popup" data-demo-id="<?php echo esc_attr( $demo ); ?>">

								<div class="theme-screenshot">
									<img src="https://demos.oceanwp.org/preview/<?php echo esc_attr( $demo ); ?>.jpg" />

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

					<?php } ?>

				</div>

			</div>

		</div>

	<?php }
}
new OWP_Install_Demos();