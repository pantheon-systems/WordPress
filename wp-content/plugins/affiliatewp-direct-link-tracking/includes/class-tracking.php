<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Direct_Link_Tracking_Base {

    public function __construct() {

		if ( ! affiliate_wp()->tracking->use_fallback_method() ) {
			add_action( 'wp_footer', array( $this, 'track_visit' ), 100 );
		} else {
			add_action( 'template_redirect', array( $this, 'fallback_track_visit' ), -9999 );
		}

    }

    /**
     * Track the visit
     *
     * @since 1.0.0
     */
    public function track_visit() {

        if ( ! affwp_dlt_can_store_visit() ) {
            return;
        }

		// Is there an affiliate link being used?
		$referral_var = isset( $_GET[ affiliate_wp()->tracking->get_referral_var() ] ) ? true : false;

		if ( $referral_var ) {
			return;
		}

        ?>
        <script>
        jQuery(document).ready( function($) {

            // Affiliate ID
            var ref = "<?php echo affwp_dlt_get_affiliate_id(); ?>";

            var ref_cookie = $.cookie( 'affwp_ref' );
            var credit_last = AFFWP.referral_credit_last;

            if ( '1' != credit_last && ref_cookie ) {
                return;
            }

            // If a referral var is present and a referral cookie is not already set
            if ( ref && ! ref_cookie ) {
                affwp_track_visit( ref );
            } else if( '1' == credit_last && ref && ref_cookie && ref !== ref_cookie ) {
                $.removeCookie( 'affwp_ref' );
                affwp_track_visit( ref );
            }

            // Track the visit
            function affwp_track_visit( affiliate_id, url_campaign ) {

				// Set the cookie and expire it after 24 hours
				$.cookie( 'affwp_ref', affiliate_id, { expires: AFFWP.expiration, path: '/' } );

				// Fire an ajax request to log the hit
				$.ajax({
					type: "POST",
					data: {
						action:    'affwp_track_visit',
						affiliate: affiliate_id,
						url:       document.URL,
						referrer:  document.referrer,
						context:   'direct-link'
					},
					url: affwp_scripts.ajaxurl,
					success: function (response) {
						$.cookie( 'affwp_ref_visit_id', response, { expires: AFFWP.expiration, path: '/' } );
					}

				}).fail(function (response) {
					if ( window.console && window.console.log ) {
						console.log( response );
					}
				});

            }

        });

        </script>
        <?php
    }

	/**
	 * Record referral visit via template_redirect
	 *
	 * @since 1.1
	 */
	public function fallback_track_visit() {

        if ( ! affwp_dlt_can_store_visit() ) {
            return;
        }

		// Get the tracked affiliate ID.
	    $affiliate_id = affwp_dlt_get_affiliate_id();

		$is_valid = affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id );
		$visit_id = affiliate_wp()->tracking->get_visit_id();

		// Is there an affiliate link being used?
		$referral_var = isset( $_GET[ affiliate_wp()->tracking->get_referral_var() ] ) ? true : false;

		if ( $is_valid && ! $visit_id && ! $referral_var ) {

			if ( ( ! empty( $_SERVER['HTTP_REFERER'] ) && ! affwp_is_url_banned( sanitize_text_field( $_SERVER['HTTP_REFERER'] ) ) )
				|| empty( $_SERVER['HTTP_REFERER'] )
			) {

				// Set affiliate ID
				affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );

				// Store the visit in the DB
				$visit_id = affiliate_wp()->visits->add( array(
					'affiliate_id' => $affiliate_id,
					'ip'           => affiliate_wp()->tracking->get_ip(),
					'url'          => affiliate_wp()->tracking->get_current_page_url(),
					'referrer'     => ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '',
					'context'      => 'direct-link'
				) );

				// Set visit
				affiliate_wp()->tracking->set_visit_id( $visit_id );

			}

		} elseif ( ! $is_valid ) {
			affiliate_wp()->utils->log( 'Invalid affiliate ID during fallback_track_visit()' );
		} elseif ( ! $visit_id ) {
			affiliate_wp()->utils->log( 'Missing visit ID during fallback_track_visit()' );
		} elseif ( $visit_id ) {
			affiliate_wp()->utils->log( 'Visit already logged during fallback_track_visit()' );
		} else {
			affiliate_wp()->utils->log( 'Invalid affiliate ID during fallback_track_visit()' );
		}

	}

}
