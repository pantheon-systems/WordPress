<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Vendor_WPML
 * @since 4.9
 */
class Vc_Vendor_WPML implements Vc_Vendor_Interface {

	public function load() {
		add_filter( 'vc_object_id', array(
			$this,
			'filterMediaId',
		) );

		add_filter( 'vc_basic_grid_filter_query_suppress_filters', '__return_false' );

		add_filter( 'vc_grid_request_url', array(
			$this,
			'appendLangToUrlGrid',
		) );

		global $sitepress;
		$action = vc_post_param( 'action' );
		if ( vc_is_page_editable() && 'vc_frontend_load_template' === $action ) {
			// Fix Issue with loading template #135512264670405
			remove_action( 'wp_loaded', array(
				$sitepress,
				'maybe_set_this_lang',
			) );
		}
	}

	public function appendLangToUrlGrid( $link ) {
		global $sitepress;
		if ( is_object( $sitepress ) ) {
			if ( is_string( $link ) && strpos( $link, 'lang' ) === false ) {
				// add langs for vc_inline/vc_editable requests
				if ( strpos( $link, 'admin-ajax' ) !== false ) {
					return add_query_arg( array( 'lang' => $sitepress->get_current_language() ), $link );
				}
			}
		}

		return $link;
	}

	public function filterMediaId( $id ) {
		return apply_filters( 'wpml_object_id', $id, 'post', true );
	}
}
