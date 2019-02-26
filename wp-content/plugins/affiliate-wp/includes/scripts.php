<?php

/**
 * Determines whether the current admin page is an AffiliateWP admin page.
 *
 * Only works after the `wp_loaded` hook, & most effective
 * starting on `admin_menu` hook.
 *
 * @since 1.0
 *
 * @param string $page Optional. Specific admin page to check for. Default empty (any).
 * @return bool True if AffiliateWP admin page.
 */
function affwp_is_admin_page( $page = '' ) {

	if ( ! is_admin() || ! did_action( 'wp_loaded' ) ) {
		$ret = false;
	}

	if ( empty( $page ) && isset( $_GET['page'] ) ) {
		$page = sanitize_text_field( $_GET['page'] );
	} else {
		$ret = false;
	}

	$pages = array(
		'affiliate-wp',
		'affiliate-wp-affiliates',
		'affiliate-wp-referrals',
		'affiliate-wp-payouts',
		'affiliate-wp-visits',
		'affiliate-wp-creatives',
		'affiliate-wp-reports',
		'affiliate-wp-tools',
		'affiliate-wp-settings',
		'affwp-getting-started',
		'affwp-what-is-new',
		'affwp-credits'
	);

	if ( ! empty( $page ) && in_array( $page, $pages ) ) {
		$ret = true;
	} else {
		$ret = in_array( $page, $pages );
	}

	/**
	 * Filters whether the current page is an AffiliateWP admin page.
	 *
	 * @since 1.0
	 *
	 * @param bool $ret Whether the current page is either a given admin page
	 *                  or any whitelisted admin page.
	 */
	return apply_filters( 'affwp_is_admin_page', $ret );
}

/**
 *  Load the admin scripts
 *
 *  @since 1.0
 *  @return void
 */
function affwp_admin_scripts() {

	if( ! affwp_is_admin_page() ) {
		return;
	}

	affwp_enqueue_admin_js();

	// only enqueue for creatives page
	if ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add_creative' || $_GET['action'] == 'edit_creative' ) ) {
		wp_enqueue_media();
	}

	wp_enqueue_script( 'jquery-ui-datepicker' );

	// Enqueue postbox for core meta boxes.
	wp_enqueue_script( 'postbox' );
}
add_action( 'admin_enqueue_scripts', 'affwp_admin_scripts' );

/**
 *  Load the admin styles
 *
 *  @since 1.0
 *  @return void
 */
function affwp_admin_styles() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Dashicons and our main admin CSS need to be on all pages for the menu icon
	wp_enqueue_style( 'affwp-admin', AFFILIATEWP_PLUGIN_URL . 'assets/css/admin' . $suffix . '.css', array( 'dashicons' ), AFFILIATEWP_VERSION );

	if( ! affwp_is_admin_page() ) {
		return;
	}

	// jQuery UI styles are loaded on our admin pages only
	$ui_style = ( 'classic' == get_user_option( 'admin_color' ) ) ? 'classic' : 'fresh';
	wp_enqueue_style( 'jquery-ui-css', AFFILIATEWP_PLUGIN_URL . 'assets/css/jquery-ui-' . $ui_style . '.min.css' );
}
add_action( 'admin_enqueue_scripts', 'affwp_admin_styles' );

/**
 * Enqueues and localizes admin.js.
 *
 * This is separated so it can be selectively executed outside of affwp admin pages.
 *
 * @since 2.0
 */
function affwp_enqueue_admin_js() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// Batch processing.
	wp_register_script( 'affwp-batch', AFFILIATEWP_PLUGIN_URL . 'assets/js/batch' . $suffix . '.js', array( 'jquery-form' ), AFFILIATEWP_VERSION );

	wp_localize_script( 'affwp-batch', 'affwp_batch_vars', array(
		'unsupported_browser'   => __( 'We are sorry but your browser is not compatible with this kind of file upload. Please upgrade your browser.', 'affiliate-wp' ),
		'import_field_required' => __( 'This field must be mapped for the import to proceed.', 'affiliate-wp' ),
	) );

	$admin_deps = array( 'jquery', 'jquery-ui-autocomplete', 'affwp-batch' );

	wp_enqueue_script( 'affwp-admin', AFFILIATEWP_PLUGIN_URL . 'assets/js/admin' . $suffix . '.js', $admin_deps, AFFILIATEWP_VERSION );
	wp_localize_script( 'affwp-admin', 'affwp_vars', array(
		'post_id'                 => isset( $post->ID ) ? $post->ID : null,
		'affwp_version'           => AFFILIATEWP_VERSION,
		'currency_sign'           => affwp_currency_filter(''),
		'currency_pos'            => affiliate_wp()->settings->get( 'currency_position', 'before' ),
		'confirm_delete_referral' => __( 'Are you sure you want to delete this referral?', 'affiliate-wp' ),
		'no_user_found'           => __( 'The user you entered does not exist. Enter an email below to create a new user and affiliate at the same time.', 'affiliate-wp' ),
		'existing_affiliate'      => __( 'An affiliate already exists for this username.', 'affiliate-wp' ),
		'view_affiliate'          => __( 'View Affiliate', 'affiliate-wp' ),
	) );
}

/**
 *  Load the frontend scripts and styles
 *
 *  @since 1.0
 *  @return void
 */
function affwp_frontend_scripts_and_styles() {

	global $post;

	if ( ! is_object( $post ) ) {
		return;
	}

	$script_deps = array( 'jquery' );
	$style_deps  = array();

	if ( 'graphs' === affwp_get_active_affiliate_area_tab() || isset( $_REQUEST['tab'] ) && 'graphs' === sanitize_key( $_REQUEST['tab'] ) ) {
		$script_deps[] = 'jquery-ui-datepicker';
		$style_deps[]  = 'jquery-ui-css';
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_register_style( 'affwp-forms', AFFILIATEWP_PLUGIN_URL . 'assets/css/forms' . $suffix . '.css', $style_deps, AFFILIATEWP_VERSION );

	wp_register_style( 'jquery-ui-css', AFFILIATEWP_PLUGIN_URL . 'assets/css/jquery-ui-fresh.min.css' );

	wp_register_script( 'affwp-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), AFFILIATEWP_VERSION );

	wp_register_script( 'affwp-frontend', AFFILIATEWP_PLUGIN_URL . 'assets/js/frontend' . $suffix . '.js', $script_deps, AFFILIATEWP_VERSION );

	wp_localize_script( 'affwp-frontend', 'affwp_vars', array(
		'affwp_version'         => AFFILIATEWP_VERSION,
		'permalinks'            => get_option( 'permalink_structure' ),
		'pretty_affiliate_urls' => affwp_is_pretty_referral_urls(),
		'currency_sign'         => affwp_currency_filter(''),
		'currency_pos'          => affiliate_wp()->settings->get( 'currency_position', 'before' ),
		'invalid_url'           => __( 'Please enter a valid URL for this site', 'affiliate-wp' )
	));

	/**
	 * Filters whether to force frontend scripts to be enqueued.
	 *
	 * @since 1.0
	 *
	 * @param bool $force Whether to force frontend scripts. Default false.
	 */
	if ( true === apply_filters( 'affwp_force_frontend_scripts', false ) ) {
		affwp_enqueue_script( 'affwp-frontend', 'force_frontend_scripts' );
	}

	// Always enqueue the 'affwp-forms' stylesheet.
	affwp_enqueue_style( 'affwp-forms' );

}
add_action( 'wp_enqueue_scripts', 'affwp_frontend_scripts_and_styles' );

/**
 * Filters whether to enqueue reCAPTCHA via AffiliateWP to maintain GravityForms compatibility.
 *
 * @since 1.9.8
 *
 * @param bool   $enqueue Whether to enqueue the script. Default true.
 * @return bool Whether to enqueue the script.
 */
function affwp_enqueue_recaptcha_gravityforms_compat( $enqueue ) {
	if ( wp_script_is( 'gform-recaptcha', 'enqueued' ) ) {
		$enqueue = false;
	}
	return $enqueue;
}
add_filter( 'affwp_enqueue_script_affwp-recaptcha', 'affwp_enqueue_recaptcha_gravityforms_compat' );

/**
 *  Load the frontend creative styles for the [affiliate_creative] and [affiliate_creatives] shortcodes
 *
 *  @since 1.1.4
 *  @return void
 */
function affwp_frontend_creative_styles() {
	global $post;

	if ( ! is_object( $post ) ) {
		return;
	}

	if ( has_shortcode( $post->post_content, 'affiliate_creative' ) || has_shortcode( $post->post_content, 'affiliate_creatives' ) || apply_filters( 'affwp_force_frontend_scripts', false ) ) { ?>
		<style>.affwp-creative{margin-bottom: 4em;}</style>
	<?php }
}
add_action( 'wp_head', 'affwp_frontend_creative_styles' );
