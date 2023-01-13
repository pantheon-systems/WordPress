<?php

$oe_install_demos_active = get_option( 'oe_install_demos_active', 'yes' );

function img_url( $demo ) {
	 $url = 'https://demos.oceanwp.org/0images/' . esc_attr( $demo ) . '.png';

	// Return
	return apply_filters( 'owp_demos_img_url', $url );
}

// Theme branding
$brand = oceanwp_theme_branding();
?>

<div id="install-demos" <?php if ( $oe_install_demos_active == 'no' ) :	?> style="display:none;" <?php endif; ?>>

	<div class="owp-demo-wrap wrap">

		<div class="theme-browser rendered">

			<?php
			// Vars
			$demos    = OceanWP_Demos::get_demos_data();
			$el_demos = $demos['elementor'];
			$gu_demos = '';
			$el_cat   = OceanWP_Demos::get_demo_all_categories( $el_demos );

			// If Gutenberg
			if ( ! empty( $demos['gutenberg'] ) ) {
				$gu_demos = $demos['gutenberg'];
				$gu_cat   = OceanWP_Demos::get_demo_all_categories( $gu_demos );
			}

			?>

			<div class="owp-header-bar">
				<nav class="owp-navigation">

					<?php
					if ( ! empty( $gu_demos ) ) {
						?>
						<ul class="owp-demo-linked">
							<li class="active"><a href="#" class="owp-elementor-link"><?php esc_html_e( 'Elementor', 'ocean-extra' ); ?></a></li>
							<li><a href="#" class="owp-gutenberg-link"><?php esc_html_e( 'Gutenberg', 'ocean-extra' ); ?></a></li>
						</ul>
						<?php
					}
					?>

					<?php
					if ( ! empty( $el_cat ) ) {
						?>
						<ul class="elementor-demos">
							<li class="active"><a href="#all" class="owp-navigation-link"><?php esc_html_e( 'All', 'ocean-extra' ); ?></a></li>
							<?php foreach ( $el_cat as $key => $name ) { ?>
								<li><a href="#<?php echo esc_attr( $key ); ?>" class="owp-navigation-link"><?php echo esc_html( $name ); ?></a></li>
							<?php } ?>
						</ul>
						<?php
					}
					?>

					<?php
					if (
						! empty( $gu_demos )
						&& ! empty( $gu_cat )
					) {
						?>
						<ul class="gutenberg-demos" style="display: none;">
							<li class="active"><a href="#all" class="owp-navigation-link"><?php esc_html_e( 'All', 'ocean-extra' ); ?></a></li>
							<?php foreach ( $gu_cat as $key => $name ) { ?>
								<li><a href="#<?php echo esc_attr( $key ); ?>" class="owp-navigation-link"><?php echo esc_html( $name ); ?></a></li>
							<?php } ?>
						</ul>
						<?php
					}
					?>

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
					$item_categories = OceanWP_Demos::get_demo_item_categories( $key );
					?>

					<div class="theme-wrap" data-categories="<?php echo esc_attr( $item_categories ); ?>" data-name="<?php echo esc_attr( strtolower( $demo ) ); ?>">

						<div class="theme owp-open-popup" data-demo-id="<?php echo esc_attr( $demo ); ?>" data-demo-type="elementor">

							<div class="theme-screenshot">
								<img src="<?php echo img_url( $demo ); ?>" />

								<div class="demo-import-loader preview-all preview-all-<?php echo esc_attr( $demo ); ?>"></div>

								<div class="demo-import-loader preview-icon preview-<?php echo esc_attr( $demo ); ?>"><i class="custom-loader"></i></div>
							</div>

							<div class="theme-id-container">

								<h2 class="ocean-theme-name" id="<?php echo esc_attr( $demo ); ?>"><span><?php echo ucwords( $demo ); ?></span></h2>

								<div class="ocean-theme-actions">
									<a class="button button-primary" href="#"><?php _e( 'Import', 'ocean-extra' ); ?></a>
									<a class="button button-primary owp-live-preview" href="https://<?php echo esc_attr( $demo ); ?>.oceanwp.org/" target="_blank"><?php _e( 'Live Preview', 'ocean-extra' ); ?></a>
								</div>

							</div>

						</div>

					</div>

					<?php
				}
				?>

			</div>

			<?php
			if ( ! empty( $gu_demos ) ) {
				?>

				<div class="themes wp-clearfix gutenberg-items" style="display: none;">

					<?php
					// Loop through all demos
					foreach ( $gu_demos as $demo => $key ) {

						// Vars
						$item_categories = OceanWP_Demos::get_demo_item_categories( $key );
						?>

						<div class="theme-wrap" data-categories="<?php echo esc_attr( $item_categories ); ?>" data-name="<?php echo esc_attr( strtolower( $demo ) ); ?>">

							<div class="theme owp-open-popup" data-demo-id="<?php echo esc_attr( $demo ); ?>" data-demo-type="gutenberg">

								<div class="theme-screenshot">
									<img src="<?php echo img_url( $demo ); ?>" />

									<div class="demo-import-loader preview-all preview-all-<?php echo esc_attr( $demo ); ?>"></div>

									<div class="demo-import-loader preview-icon preview-<?php echo esc_attr( $demo ); ?>"><i class="custom-loader"></i></div>
								</div>

								<div class="theme-id-container">

									<h2 class="ocean-theme-name" id="<?php echo esc_attr( $demo ); ?>"><span><?php echo ucwords( $demo ); ?></span></h2>

									<div class="ocean-theme-actions">
										<a class="button button-primary" href="#"><?php _e( 'Import', 'ocean-extra' ); ?></a>
										<a class="button button-primary owp-live-preview" href="https://<?php echo esc_attr( $demo ); ?>.oceanwp.org/" target="_blank"><?php _e( 'Live Preview', 'ocean-extra' ); ?></a>
									</div>

								</div>

							</div>

						</div>

						<?php
					}
					?>

				</div>

				<?php
			}
			?>

		</div>

	</div>
</div>
