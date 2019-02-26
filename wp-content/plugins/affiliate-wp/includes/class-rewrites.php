<?php

class Affiliate_WP_Rewrites {

	/**
	 * Get things started
	 *
	 * @since 1.7.8
	 */
	public function __construct() {

		$this->init();

	}

	/**
	 * Allow developers to extend and overwrite the default actions we add
	 *
	 * @since 1.7.8
	 */
	public function init() {

		add_action( 'init', array( $this, 'maybe_flush_rewrites' ), 999998 );

		add_action( 'init', array( $this, 'rewrites' ), 999999 );

		add_action( 'redirect_canonical', array( $this, 'prevent_canonical_redirect' ), 0, 2 );
	}

	/**
	 * Flush rewrite rules if flag is set
	 *
	 * @since 1.7.8
	 */
	public function maybe_flush_rewrites() {

		if( get_option( 'affwp_flush_rewrites' ) ) {

			$this->flush_rewrites();

			delete_option( 'affwp_flush_rewrites' );

		}

	}

	/**
	 * Flush rewrite rules and run pre/pst actions to allow integrations to tie into the flush process
	 *
	 * @since 1.7.8
	 */
	public function flush_rewrites() {

		/**
		 * Fires immediately prior to flushing rewrite rules.
		 */
		do_action( 'affwp_pre_flush_rewrites' );

		flush_rewrite_rules();

		/**
		 * Fires immediately after flushing rewrite rules.
		 */
		do_action( 'affwp_post_flush_rewrites' );
	}

	/**
	 * Registers the rewrite rules for pretty affiliate links
	 *
	 * This was in Affiliate_WP_Tracking until 1.7.8
	 *
	 * @since 1.3
	 */
	public function rewrites() {

		$taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'objects' );

		foreach( $taxonomies as $tax_id => $tax ) {

			$ref = affiliate_wp()->tracking->get_referral_var();
			add_rewrite_rule( $tax->rewrite['slug'] . '/(.+?)/' . $ref . '(/(.*))?/?$', 'index.php?' . $tax_id . '=$matches[1]&' . $ref . '=$matches[3]', 'top');

		}

		add_rewrite_endpoint( affiliate_wp()->tracking->get_referral_var(), EP_ALL, false );

	}

	/**
	 * Removes our tracking query arg so as not to interfere with the WP query, see https://core.trac.wordpress.org/ticket/25143
	 *
	 * This was in Affiliate_WP_Tracking until 1.7.8
	 *
	 * @since 1.3.1
	 */
	public function unset_query_arg( $query ) {

		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$key  = affiliate_wp()->tracking->get_referral_var();
		$ref  = $query->get( $key );
		$path = ! empty( $_SERVER['REQUEST_URI' ] ) ? $_SERVER['REQUEST_URI' ] : '';

		if ( ! empty( $ref ) || false !== strpos( $path, '/' . $key ) ) {

			$this->referral = $ref;

			// unset ref var from $wp_query
			$query->set( $key, null );

			global $wp;

			// unset ref var from $wp
			unset( $wp->query_vars[ $key ] );

			// if in home (because $wp->query_vars is empty) and 'show_on_front' is page
			if ( empty( $wp->query_vars ) && get_option( 'show_on_front' ) === 'page' ) {

				// Look to see if we have a page with this slug
				$page = get_page_by_path( $key );

			 	// reset and re-parse query vars
			 	if( $page ) {

					$wp->query_vars['page_id'] = $page->ID;

			 	} else {

					$wp->query_vars['page_id'] = get_option( 'page_on_front' );

			 	}
				$query->parse_query( $wp->query_vars );

			}

		}

	}

	/**
	 * Filters on canonical redirects
	 *
	 * This was in Affiliate_WP_Tracking until 1.7.8
	 *
	 * @since 1.4
	 * @return string
	 */
	public function prevent_canonical_redirect( $redirect_url, $requested_url ) {

		if( ! is_front_page() ) {
			return $redirect_url;
		}

		$key = affiliate_wp()->tracking->get_referral_var();
		$ref = get_query_var( $key );

		if( ! empty( $ref ) || false !== strpos( $requested_url, $key ) ) {

			$redirect_url = $requested_url;

		}

		return $redirect_url;

	}

}
