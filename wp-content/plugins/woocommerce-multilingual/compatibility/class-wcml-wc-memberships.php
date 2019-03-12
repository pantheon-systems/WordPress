<?php

class WCML_WC_Memberships {

	/**
	 * @var WPML_WP_API
	 */
	private $wp_api;

	public function __construct( WPML_WP_API $wp_api ) {
		$this->wp_api = $wp_api;
	}

	public function add_hooks() {

		add_filter( 'parse_request', array( $this, 'adjust_query_vars' ) );
		add_filter( 'wcml_register_endpoints_query_vars', array( $this, 'register_endpoints_query_vars' ), 10, 3 );
		add_filter( 'wcml_endpoint_permalink_filter', array( $this, 'endpoint_permalink_filter' ), 10, 2 );
		add_filter( 'wc_memberships_members_area_my-memberships_actions', array( $this, 'filter_actions_links' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	public function register_endpoints_query_vars( $query_vars, $wc_vars, $object ) {
		$query_vars['members_area'] = $this->get_translated_endpoint( $object );

		return $query_vars;
	}

	public function get_translated_endpoint( $object ) {

		$translation = $object->get_endpoint_translation(
			'members_area',
			get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' )
		);

		return $translation;
	}

	public function adjust_query_vars( $q ) {

		if ( ! isset( $q->query_vars['members-area'] ) && isset( $q->query_vars['members_area'] ) ) {
			$q->query_vars['members-area'] = $q->query_vars['members_area'];
		}

		return $q;
	}

	public function endpoint_permalink_filter( $endpoint, $key ) {

		if ( 'members_area' === $key ) {
			$endpoint = get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );
		}

		return $endpoint;
	}

	public function filter_actions_links( $actions ) {

		foreach ( $actions as $key => $action ) {
			if ( 'view' === $key ) {
				$membership_endpoints   = $this->get_members_area_endpoint();
				$actions[ $key ]['url'] = str_replace( $membership_endpoints['original'], $membership_endpoints['translated'], $action['url'] );
			}
		}

		return $actions;
	}

	public function load_assets() {
		global $post;

		if ( isset( $post->ID ) && wc_get_page_id( 'myaccount' ) == $post->ID ) {
			$wcml_plugin_url = $this->wp_api->constant( 'WCML_PLUGIN_URL' );
			$wcml_version    = $this->wp_api->constant( 'WCML_VERSION' );
			wp_register_script( 'wcml-members-js', $wcml_plugin_url . '/compatibility/res/js/wcml-members.js', array( 'jquery' ), $wcml_version );
			wp_enqueue_script( 'wcml-members-js' );
			wp_localize_script( 'wcml-members-js', 'wc_memberships_memebers_area_endpoint', $this->get_members_area_endpoint() );
		}

	}

	public function get_members_area_endpoint() {

		$endpoint            = get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );
		$translated_endpoint = apply_filters( 'wpml_translate_single_string', $endpoint, 'WooCommerce Endpoints', 'members_area' );

		return array(
			'original'   => $endpoint,
			'translated' => $translated_endpoint
		);
	}


}
