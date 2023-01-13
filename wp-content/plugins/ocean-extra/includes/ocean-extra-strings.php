<?php
/**
 * Ocean Extra plugin translation strings
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'oe_lang_strings' ) ) {

	/**
	 * Ocean Extra plugin Strings
	 *
	 *  @author OceanWP
	 *  @since 1.7.8
	 *
	 * @param  string  $value  String key.
	 * @param  boolean $echo   Print string.
	 * @return mixed           Return string or nothing.
	 */
	function oe_lang_strings( $value, $echo = true ) {

		$oe_txt_strings = apply_filters(
			'oe_lang_strings',
			array(
				
				// Mailchimp Widget.
				'oe-string-mc-email'                     => apply_filters( 'oe_wai_mc_email', __( 'Enter your email address to subscribe', 'ocean-extra' ) ),
				'oe-string-mc-submit'                    => apply_filters( 'oe_wai_mc_submit', __( 'Submit email address', 'ocean-extra' ) ),
				'oe-string-mc-email-req-alert'           => apply_filters( 'oe_mc_email_req', __( 'Email is required', 'ocean-extra' ) ),
				'oe-string-mc-email-inv-alert'           => apply_filters( 'oe_mc_email_inv', __( 'Email is not valid', 'ocean-extra' ) ),
				'oe-string-mc-gdpr-check'                => apply_filters( 'oe_mc_gdpr_check', __( 'This field is required', 'ocean-extra' ) ),
				'oe-string-mc-msg-succ'                  => apply_filters( 'oe_mc_msg_succ', __( 'Thanks for your subscription.', 'ocean-extra' ) ),
				'oe-string-mc-msg-fail'                  => apply_filters( 'oe_mc_msg_fail', __( 'Failed to subscribe, please contact admin.', 'ocean-extra' ) ),

				// Aria.
				'oe-string-search-form-label'            => apply_filters( 'oe_wai_search_form_label', __( 'Search this website', 'ocean-extra' ) ),
				'oe-string-search-field'                 => apply_filters( 'oe_wai_search_field', __( 'Insert search query', 'ocean-extra' ) ),
				'oe_string_search_submit'                => apply_filters( 'oe_wai_search_submit', __( 'Submit your search', 'ocean-extra' ) ),

			)
		);

		if ( is_rtl() ) {
			// do your stuff.
		}

		$oet_string = isset( $oe_txt_strings[ $value ] ) ? $oe_txt_strings[ $value ] : '';

		/**
		 * Print or return strings
		 */
		if ( $echo ) {
			echo $oet_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $oet_string;
		}
	}
}
