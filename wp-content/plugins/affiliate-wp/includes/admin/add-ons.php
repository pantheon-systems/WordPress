<?php
/**
 * Admin Add-ons
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add-ons Page
 *
 * Renders the add-ons page content.
 *
 * @return void
 */
function affwp_add_ons_admin() {
	/**
	 * Filters the add-ons tabs.
	 *
	 * @param array $tabs Add-ons tabs.
	 */
	$add_ons_tabs = (array) apply_filters( 'affwp_add_ons_tabs', array(
		'pro'           => 'Pro',
		'official-free' => 'Official Free'
	) );

	$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $add_ons_tabs ) ? $_GET['tab'] : 'pro';

	ob_start();
	?>
	<div class="wrap" id="affwp-add-ons">
		<h1>
			<?php _e( 'Add-ons for AffiliateWP', 'affiliate-wp' ); ?>
			<span>
				&nbsp;&nbsp;<a href="https://affiliatewp.com/addons/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=All%20Add-ons" class="button-primary" title="<?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?>" target="_blank"><?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?></a>
			</span>
		</h1>
		<p><?php _e( 'These add-ons <em><strong>add functionality</strong></em> to your AffiliateWP-powered site.', 'affiliate-wp' ); ?></p>
		<h2 class="nav-tab-wrapper">
			<?php affwp_navigation_tabs( $add_ons_tabs, $active_tab, array( 'settings-updated' => false ) ); ?>
		</h2>
		<div id="tab_container">

			<?php if ( 'pro' === $active_tab ) : ?>
				<p><?php printf( __( 'Pro add-ons are only available with a Professional or Ultimate license. If you already have one of these licenses, simply <a href="%s">log in to your account</a> to download any of these add-ons.', 'affiliate-wp' ), 'https://affiliatewp.com/account/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=Account' ); ?></p>
				<p><?php printf( __( 'If you have a Personal or Plus license, you can easily upgrade from your account page to <a href="%s">get access to all of these add-ons</a>!', 'affiliate-wp' ), 'https://affiliatewp.com/account/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=Account' ); ?></p>
			<?php else : ?>
				<p><?php _e( 'Our official free add-ons are available to all license holders!', 'affiliate-wp' ); ?></p>
			<?php endif; ?>

			<?php echo affwp_add_ons_get_feed( $active_tab ); ?>
			<div class="affwp-add-ons-footer">
				<a href="https://affiliatewp.com/addons/?utm_source=plugin-add-ons-page&utm_medium=plugin&utm_campaign=AffiliateWP%20Add-ons%20Page&utm_content=All%20Add-ons" class="button-primary" title="<?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?>" target="_blank"><?php _e( 'Browse all add-ons', 'affiliate-wp' ); ?></a>
			</div>
		</div>
	</div>
	<?php
	echo ob_get_clean();
}

/**
 * Add-ons Get Feed
 *
 * Gets the add-ons page feed.
 *
 * @return void
 */
function affwp_add_ons_get_feed( $tab = 'pro' ) {

	$cache = get_transient( 'affiliatewp_add_ons_feed_' . $tab );

	if ( false === $cache ) {
		$url = 'https://affiliatewp.com/?feed=feed-add-ons';

		if ( 'pro' !== $tab ) {
			$url = add_query_arg( array( 'display' => $tab ), $url );
		}

		$feed = wp_remote_get( esc_url_raw( $url ), array( 'sslverify' => false ) );

		if ( ! is_wp_error( $feed ) ) {

			if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
				$cache = wp_remote_retrieve_body( $feed );
				set_transient( 'affiliatewp_add_ons_feed_' . $tab, $cache, HOUR_IN_SECONDS );
			}

		} else {
			$cache = '<div class="error"><p>' . __( 'There was an error retrieving the add-ons list from the server. Please try again later.', 'affiliate-wp' ) . '</div>';
		}

	}

	return $cache;

}
