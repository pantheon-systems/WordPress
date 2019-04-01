<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Direct_Link_Tracking_Frontend {

    public $field_name = 'direct_link_tracking_urls';

    public function __construct() {

        add_action( 'affwp_direct_link_tracking_update_direct_links', array( $this, 'update_direct_links' ), 10, 1 );
        add_action( 'wp_head', array( $this, 'styles' ), 100 );
        add_action( 'wp_footer', array( $this, 'scripts' ), 100 );

		// notices
		add_action( 'affwp_affiliate_dashboard_top', array( $this, 'save_notice' ), 10, 2 );
		add_action( 'affwp_dismiss_dlt_notice', array( $this, 'dismiss_notice' ) );
		add_action( 'affwp_direct_link_tracking_show_notices', array( $this, 'domain_notices' ) );

		// Add the "Direct Links" tab to the Affiliate Area.
		if ( apply_filters( 'affwp_direct_link_tracking_show_dashboard_tab', true ) ) {
			add_action( 'affwp_affiliate_dashboard_tabs', array( $this, 'add_tab' ), 10, 2 );
			add_filter( 'affwp_affiliate_area_tabs', array( $this, 'register_tab' ) );
		}

    }

	/**
	 * The Direct Links tab
	 *
	 * @since 1.1
	 * @since 1.1.2 Added a return statement if user is on older version of AffiliateWP.
	 */
	public function add_tab( $affiliate_id, $active_tab ) {

		// Return early if user has AffiliateWP 2.1.7 or newer. This method is no longer needed.
		if ( $this->has_2_1_7() || is_admin() ) {
			return;
		}

		?>
		<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'direct-links' ? ' active' : ''; ?>">
		    <a href="<?php echo esc_url( add_query_arg( 'tab', 'direct-links' ) ); ?>"><?php _e( 'Direct Links', 'affiliatewp-direct-link-tracking' ); ?></a>
		</li>
		<?php
	}

	/**
	 * Register the "Direct Links" tab.
	 * 
	 * @since 1.1
	 * @since  AffiliateWP 1.8.1
	 * @since  AffiliateWP 2.1.7 The tab being registered requires both a slug and title.
	 * 
	 * @return array $tabs The list of tabs
	 */
	public function register_tab( $tabs ) {
		
		/**
		 * User is on older version of AffiliateWP, use the older method of
		 * registering the tab. 
		 * 
		 * The previous method was to register the slug, and add the tab
		 * separately, @see add_tab()
		 * 
		 * @since 1.1.2
		 */
		if ( ! $this->has_2_1_7() ) {
			return array_merge( $tabs, array( 'direct-links' ) );
		}

		/**
		 * Don't show tab to affiliate if they don't have access.
		 * Doesn't run within admin since we need the tab to show for Affiliate Area Tabs
		 */
		if ( ! affwp_dlt_allow_direct_link_tracking( affwp_get_affiliate_id() ) && ! is_admin() ) {
			return $tabs;
		}

		// Register the "Direct Links" tab.
		$tabs['direct-links'] = __( 'Direct Links', 'affiliatewp-direct-link-tracking' );
		
		// Return the tabs.
		return $tabs;
	}	

	/**
	 * Determine if the user has at least version 2.1.7 of AffiliateWP.
	 *
	 * @since 1.1.2
	 * 
	 * @return boolean True if AffiliateWP v2.1.7 or newer, false otherwise.
	 */
	public function has_2_1_7() {
		
		$return = true;

		if ( version_compare( AFFILIATEWP_VERSION, '2.1.7', '<' ) ) {
			$return = false;
		}

		return $return;
	}
	
	/**
	 * Dismiss the notice from the affiliate area when a URL is rejected
	 *
	 * @since 1.0.0
	 */
	public function dismiss_notice( $data ) {

		if ( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'dismiss-notice-nonce' ) ) {
			return;
		}

		$url_id = $data['url_id'];

		// get URL by row
		$direct_link = affwp_dlt_get_direct_link( $url_id );

		// update the direct link
		affwp_dlt_update_direct_link( $url_id, array( 'url' => $direct_link->url_old, 'status' => 'active', 'url_old' => '' ) );

		// redirect back to same page and strip the query string args
		wp_redirect( remove_query_arg( array( 'affwp_action', 'url_id', '_wpnonce' ) ) );
		exit;

	}

	/**
	 * Show a message above the tab menu (similar to settings) whenever the "save direct links" button is clicked.
	 *
	 * @since 1.1
	 *
	 * @return string
	 */
	public function save_notice( $affiliate_id, $active_tab ) {

		if ( ! empty( $_GET['affwp_notice'] ) && 'direct-links-updated' === $_GET['affwp_notice'] ) : ?>
			<p class="affwp-notice"><?php _e( 'Your direct link(s) have been updated', 'affiliatewp-direct-link-tracking' ); ?></p>
		<?php endif;
	}

    /**
     * Check to see if we're on the direct links tab
	 *
	 * @todo better method to detect if page is using [affiliate_direct_links] shortcode
     */
    public function is_direct_links_tab() {

		$post = get_post();

		if( ! $post ) {
			return;
		}

        $affiliate_area = function_exists( 'affwp_get_affiliate_area_page_id' ) ? is_page( affwp_get_affiliate_area_page_id() ) : is_page( affiliate_wp()->settings->get( 'affiliates_page' ) );

		// The direct links tab
        if ( $affiliate_area && isset( $_GET['tab'] ) && 'direct-links' === $_GET['tab'] ) {
            return true;
        }

		// Has [affiliate_direct_links] shortcode
		if ( has_shortcode( $post->post_content, 'affiliate_direct_links' ) || apply_filters( 'affwp_direct_link_tracking_force_frontend_scripts', false ) ) {
			return true;
		}

        return false;

    }

    /**
     * CSS styling
     *
     * @since 1.0.0
     */
    public function styles() {

        if ( ! $this->is_direct_links_tab() ) {
            return;
        }

        ?>
		<style>
		#affwp-affiliate-dashboard-direct-links label { width: auto; }
		#affwp-affiliate-dashboard-direct-links input[type="text"] { width: 100%; }
		#affwp-affiliate-dashboard-direct-links .affwp-wrap { margin-bottom: 40px; }
		#affwp-affiliate-dashboard-direct-links .affwp-notice { margin-top: 10px; margin-bottom: 40px; }
		#affwp-affiliate-dashboard-direct-links .affwp-notice.error { background: #f6e8e8; border: 1px solid #cb7070; }
		.direct-link-row-clone{ display:none; }
		.affwp-dlt-remove-url { margin-top:5px; display:inline-block; }
		</style>
        <?php
    }

    /**
     * Scripts
     */
    public function scripts() {

        if ( ! $this->is_direct_links_tab() ) {
            return;
        }

		$url_count = max( affwp_dlt_get_domain_limit( affwp_get_affiliate_id() ), 1 );
        $url_count_upper_limit = $url_count - 1;

        ?>
        <script>
            jQuery(document).ready(function($) {

				var count = $( '#affwp-affiliate-dashboard-direct-links .affwp-wrap' ).not( '.direct-link-row-clone' ).length;

				// Show the add new URL button
				if ( count !== <?php echo $url_count; ?> ) {
                    $('.affwp-dlt-add-url').show();
                }

                $('.affwp-dlt-add-url').click( function(e) {

                    e.preventDefault();

					// find the clone
					var toClone = $( '.direct-link-row-clone' );

                    var last_url = $( '#affwp-affiliate-dashboard-direct-links .affwp-wrap:last' );

					var count = $( '#affwp-affiliate-dashboard-direct-links .affwp-wrap' ).not( '.direct-link-row-clone' ).length;

                    if ( count < <?php echo $url_count; ?> ) {

						 // clone the clone
						 clone = toClone.clone( true );

						// remove cloning CSS class
						clone.removeClass( 'direct-link-row-clone' );

                        // increase count in input fields
						clone.find( 'input' ).each(function() {

                            var name = $( this ).attr( 'name' );
							var id   = $( this ).attr( 'id' );

                            name = name.replace( /\[(\d+)\]/, '[' + parseInt( count + 1 ) + ']');
							id   = id.replace( /\[(\d+)\]/, '[' + parseInt( count + 1 ) + ']');

							$( this ).attr( 'name', name );
							$( this ).attr( 'id', id );

                        });

                        // increase count in hidden input fields
						clone.find( 'input[type=hidden]' ).each(function() {
							$(this).val( parseInt( count + 1 ) );
						});

                        // increase count in label for attribute, plus label text
                        clone.find( 'label' ).each(function() {
            				var name = $( this ).attr( 'for' );

            				name = name.replace( /\[(\d+)\]/, '[' + parseInt( count + 1 ) + ']');

                    		$(this).attr( 'for', name );

							$(this).find('.count').text( parseInt( count + 1 ) );
                        });

						// insert new clone after last row
                        clone.insertAfter( last_url );

                        // Remove disabled attribute and then focus on input field
                        clone.find( 'input' ).removeAttr("disabled").focus();
                    }

					// remove link to add new URLs when limit has been reached
                    if ( count === <?php echo $url_count_upper_limit; ?> ) {
                        $(this).parent().remove();
                    }

                });

				// remove URL link
				$('.affwp-dlt-remove-url').click( function(e) {
					e.preventDefault();
					$(this).closest('.affwp-wrap').hide().find( 'input' ).val('');
				});

            });
        </script>
        <?php
    }

	/**
	 * Set up the input clone
	 *
	 * @since 1.0.0
	 */
	public function to_clone() {

        $key = 0;
		?>

		<div class="affwp-wrap direct-link-row-clone">
			<label for="direct-link-tracking-url[<?php echo $key;?>]"><?php _e( 'Direct Link Domain #', 'affiliatewp-direct-link-tracking' ); ?><span class="count"><?php echo $key; ?></span> <?php echo $this->remove_url(); ?></label>
			<input class="wide" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="direct_link_tracking_urls_new[]" disabled />
		</div>

		<?php
	}

	/**
	 * Remove URL link
	 *
	 * @since 1.1
	 */
	public function remove_url( $url_id = null ) {

		if ( ! apply_filters( 'affwp_direct_link_tracking_show_remove_link', true ) ) {
			return;
		}

		$url_limit = (int) affwp_dlt_get_domain_limit( affwp_get_affiliate_id() );
		$status    = affwp_dlt_get_direct_link_status( $url_id );

		// Pending or inactive URLs cannot be removed by the affiliate.
		if ( 'pending' === $status || 'inactive' === $status ) {
			return;
		}

		// Hide the "remove" link when there's a URL limit of 1 and the domain is blank.
		// If the domain is active then the remove link is shown.
		if ( (int) affwp_dlt_get_domain_limit( affwp_get_affiliate_id() ) === 1 && 'active' !== $status ) {
			return;
		}

		?>
		<small>(<a href="#" class="affwp-dlt-remove-url"><?php _e( 'remove', 'affiliatewp-direct-link-tracking' ); ?></a>)</small>

		<?php
	}

	/**
	 * Domain notices
	 *
	 * @since 1.0.0
	 */
	public function domain_notices( $direct_link ) {

		$url_id      = isset( $direct_link->url_id ) ? $direct_link->url_id : '';
		$url         = isset( $direct_link->url ) ? $direct_link->url : '';
		$current_url = affiliatewp_direct_link_tracking()->direct_links->get_column( 'url', $url_id );
		$old_url     = isset( $direct_link->url_old ) ? $direct_link->url_old : '';
		$is_active   = isset( $direct_link->status ) && 'active' === $direct_link->status ? true : false;
		$is_inactive = isset( $direct_link->status ) && 'inactive' === $direct_link->status ? true : false;
		$is_pending  = isset( $direct_link->status ) && 'pending' === $direct_link->status ? true : false;
		$is_rejected = isset( $direct_link->status ) && 'rejected' === $direct_link->status ? true : false;

		$notice      = '';

		/**
		 * Pending URL
		 */
		if ( $is_pending ) {

			if ( $url && $old_url ) {
				$notice = sprintf( __( '%s has been submitted for approval. Until it has been approved, the old domain will continue to work: %s', 'affiliatewp-direct-link-tracking' ), $url, $old_url );
			} else {
				$notice = sprintf( __( '%s has been submitted for approval.', 'affiliatewp-direct-link-tracking' ), $url );
			}

		}

		/**
		 * Rejected URL
		 */
		if ( $is_rejected ) {

			if ( $url && $old_url ) {
				$notice = sprintf( __( '%s was not approved. Your old domain will continue to work: %s', 'affiliatewp-direct-link-tracking' ), $url, $old_url );
				$notice .= '<br/><a href="' . wp_nonce_url( add_query_arg( array( "affwp_action" => "dismiss_dlt_notice", "url_id" => $url_id ) ), 'dismiss-notice-nonce' ) .'">' . __( "Dismiss this notice", "affiliatewp-direct-link-tracking" ) . '</a>';
			} else {
				$notice = sprintf( __( '%s was not approved.', 'affiliatewp-direct-link-tracking' ), $url );
			}

		}

		/**
		 * Inactive domain
		 */
		if ( $is_inactive ) {
			$notice = __( 'This domain is inactive. Contact the site owner for more information.', 'affiliatewp-direct-link-tracking' );
		}

		$classes = array( 'affwp-notice' );

		if ( $is_rejected ) {
			$classes[] = 'error';
		}

		$classes = implode( ' ', $classes );


		if ( $notice ) {
			echo '<p class="' . $classes . '">' . $notice . '</p>';
		}

	}

	/**
	 * Update an affiliate's direct links
	 *
	 * @since 1.0.0
	 * @return void
	 */
    public function update_direct_links( $data ) {

		if ( ! empty( $_POST['affwp_action'] ) ) {

			if ( ! affwp_dlt_update_direct_links( $data ) ) {
				// There were errors.
				wp_safe_redirect(
					add_query_arg( array(
					    'affwp_notice' => 'direct-links-updated',
					    'invalid'      => 'true'
					) )
				);
				exit;

			} else {
				// No errors.
				wp_safe_redirect( add_query_arg( 'affwp_notice', 'direct-links-updated', remove_query_arg( 'invalid' ) ) );
				exit;
			}
		}

    }

}
