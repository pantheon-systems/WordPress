<?php

class AffiliateWP_Custom_Affiliates_Slugs_Base {

	/**
	 * Affiliate meta key
	 *
	 * @since 1.0.0
	 */
	private $meta_key = 'custom_slug';

	public function __construct() {

        // Allow custom slugs to be used in affiliate URLs
		add_filter( 'affwp_tracking_get_affiliate_id', array( $this, 'allow_slugs' ), 10, 2 );

        // Filter the referral format value
        add_filter( 'affwp_get_referral_format_value', array( $this, 'filter_referral_format_value' ), 10, 3 );

		add_action( 'affwp_insert_affiliate', array( $this, 'add_slug' ), -1 );

		add_action( 'affwp_affiliate_deleted', array( $this, 'delete_slug' ), 10, 3 );

		// profile settings
		add_action( 'affwp_affiliate_dashboard_before_submit', array( $this, 'add_input' ), 10, 2 );
		add_action( 'affwp_update_affiliate_profile_settings', array( $this, 'update_slug' ), 10, 1 );

		// admin ajax actions
		add_action( 'wp_ajax_preview_slug', array( $this, 'preview_slug' ) );

		// front-end ajax actions
		add_action( 'wp_ajax_check_slug', array( $this, 'check_slug' ) );

		// load JS on the front-end affiliate settings tab
		add_action( 'wp_footer', array( $this, 'scripts_profile_settings' ) );

        // affiliate registration validation
		add_action( 'affwp_process_register_form', array( $this, 'process_affiliate_registration_form' ) );

        // shows the custom slug on the Affiliate Area's "URLs" tab
		add_filter( 'affwp_affiliate_dashboard_urls_top', array( $this, 'show_custom_slug' ) );

	}

	/**
	 * A visitor could potentially sign up on a multisite by visiting wp-login.php.
	 * This means that there's the possibility that a username could be registered
	 * that already exists as an affiliate username.

	 * However, to become an affiliate
	 * they still must register via the affiliate registration form so we'll check
	 * their username and show an error. Seems like the best we can do at this point.
	 *
	 * @since 1.0.0
	 */
	public function process_affiliate_registration_form() {

        if ( ! affwp_cas_is_automatic_slug_creation_enabled() ) {
		    return;
		}

		if ( is_user_logged_in() ) {

			$current_user = wp_get_current_user();

			if ( ! ( $current_user instanceof WP_User ) ) {
				return;
			}

			// get username of current user
			$username = $current_user->user_login;

			// check username against array of current affiliate slugs
			// if it exists, deny affiliate registration
			if ( in_array( $username, $this->current_affiliate_slugs() ) ) {
				affiliate_wp()->register->add_error( 'username_invalid', __( 'This username is already in use. Please contact the site administrator.', 'affiliatewp-custom-affiliate-slugs' ) );
			}

		} else {
			$username = isset( $_POST['affwp_user_login'] ) && $_POST['affwp_user_login'] ? $_POST['affwp_user_login'] : '';
		}

		// check username against array of current affiliate slugs
		// if it exists, deny affiliate registration
		if ( in_array( $username, $this->current_affiliate_slugs() ) ) {
			affiliate_wp()->register->add_error( 'username_invalid', __( 'This username is already in use. Please contact the site administrator.', 'affiliatewp-custom-affiliate-slugs' ) );
		}

	}

	/**
	 * Get an array of all affiliate slugs from affiliatemeta table
	 *
	 * @since 1.0.0
	 */
	public function current_affiliate_slugs() {

		global $wpdb;

		if ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single affiliate meta table for the whole network
			$table_name = 'affiliate_wp_affiliatemeta';
		} else {
			$table_name = $wpdb->prefix . 'affiliate_wp_affiliatemeta';
		}

		$slugs = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value FROM $table_name where meta_key = %s", $this->meta_key ) );

		// pluck the meta values
		$slugs = wp_list_pluck( $slugs, 'meta_value' );

		return $slugs;
	}

	/**
	 * Get an affiliate ID from the custom slug
	 *
	 * @since 1.0.1
	 */
	public function get_affiliate_id_from_slug( $slug = '' ) {

		global $wpdb;

		if ( ! $slug ) {
			return false;
		}

		if ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single affiliate meta table for the whole network
			$table_name = 'affiliate_wp_affiliatemeta';
		} else {
			$table_name = $wpdb->prefix . 'affiliate_wp_affiliatemeta';
		}

		$affiliate_id = $wpdb->get_var( $wpdb->prepare( "SELECT affiliate_id FROM $table_name where meta_key = %s AND meta_value = %s", $this->meta_key, $slug ) );

		return (int) $affiliate_id;
	}

    /**
     * Sets up the validation rules required when:
     *
     * 1. Admin adds a new affiliate with a slug
     * 2. Admin edits an affiliate and changes their slug
     * 3. An affiliate changes their slug from the Affiliate Area
	 *
	 * @since 1.0.0
     */
    public function validate( $data = array() ) {

        // get the affiliate ID
		if ( is_admin() ) {
			$affiliate_id = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '';
		} else {
			// get the affiliate ID
			$affiliate_id = $data['affiliate_id'];
		}

        // get affiliate's current slug from affiliate meta
		$current_slug = $this->get_slug( $affiliate_id );

        // the new slug
        $new_slug = $data['custom_slug'];

        // affiliate slug has changed
		if ( isset( $new_slug ) && $new_slug !== $current_slug ) {

			$confirm_slug = isset( $data['custom_slug_confirm'] ) ? $data['custom_slug_confirm'] : '';

			// new slug and confirmed slug must match exactly
			// fallback for when JS is not enabled
            // only runs on the front-end
			if ( $confirm_slug && $new_slug !== $confirm_slug ) {
				$error = __( 'Slugs do not match!', 'affiliatewp-custom-affiliate-slugs' );
			}

			// slug already exists
			if ( username_exists( $new_slug ) || $this->check( $new_slug ) ) {
				$error = __( 'This slug cannot be used.', 'affiliatewp-custom-affiliate-slugs' );
			}

			// Check if the slug contains invalid characters
			// Can only be alphabetic or alphanumerical
            if ( ( ! ( ctype_alpha( $new_slug ) || ctype_alnum( $new_slug ) ) ) && strlen( $new_slug ) > 0 ) {
				$error = __( 'This slug contains invalid characters.', 'affiliatewp-custom-affiliate-slugs' );
			}

			// Slug cannot be over 60 characters (WordPress core limit)
			if ( strlen( $new_slug ) > 60 ) {
				$error = __( 'This slug is too long.', 'affiliatewp-custom-affiliate-slugs' );
			}

			// Slug is the same as current slug
			if ( $new_slug === $current_slug ) {
				$error = __( 'This is your current slug.', 'affiliatewp-custom-affiliate-slugs' );
			}

			// Slug cannot be numeric or it will be treated as an affiliate ID
			if ( is_numeric( $new_slug ) ) {
				$error = __( 'Your slug cannot contain all numbers.', 'affiliatewp-custom-affiliate-slugs' );
			}

			if ( isset( $error ) ) {
				wp_die( $error );
			} else {
                return true;
            }

		}

        return false;

    }

	/**
	 * Update an affiliate's slug from their settings page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_slug( $data ) {

        if ( ! $this->allow_affiliate_slugs( $data['affiliate_id'] ) ) {
			return;
		}

		// slug field is not empty, validate it
		if ( ! empty( $data['custom_slug'] ) ) {

			// validate slug in case JS is disabled
			$validated = $this->validate( $_POST );

			if ( $validated ) {
				// slug has been validated, update it
				affwp_update_affiliate_meta( $data['affiliate_id'], $this->meta_key, $data['custom_slug'] );
			}

		} else {
			// slug field is empty, delete it
			affwp_delete_affiliate_meta( $data['affiliate_id'], $this->meta_key );
		}

	}

	/**
	 * Are affiliates allowed to create their own slugs?
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function allow_affiliate_slugs( $affiliate_id = 0 ) {

		$all_affiliates   = affiliate_wp()->settings->get( 'custom_affiliate_slugs_affiliate_slug_creation' );
		$single_affiliate = affwp_get_affiliate_meta( $affiliate_id, 'custom_slugs_enabled', true );

		if ( $all_affiliates || $single_affiliate ) {
			return true;
		}

		return false;

	}

	/**
	 * Allow affiliate to update/change their affiliate slug
	 * Displays an input field for the affiliate on the settings tab of the affiliate area
	 *
	 * @since 1.0.0
	 */
	public function add_input( $affiliate_id, $user_id ) {

		// return if affiliates cannot edit their affiliate slug
		if ( ! $this->allow_affiliate_slugs( $affiliate_id ) ) {
			return;
		}

		$current_slug = $this->get_slug( $affiliate_id );
		?>

		<h4><?php _e( 'Custom Slug Settings', 'affiliatewp-custom-affiliate-slugs' ); ?></h4>
		<div class="affwp-wrap affwp-custom-slug-wrap">
			<label for="custom-slug"><?php _e( 'Custom Affiliate Slug', 'affiliatewp-custom-affiliate-slugs' ); ?></label>
			<span>
				<input id="custom-slug" type="text" name="custom_slug" value="<?php echo $current_slug; ?>" />
				<img id="slug-loader" src="<?php echo AFFWP_CAS_PLUGIN_URL . 'assets/images/ajax-loader.gif' ;?>" style="display:none;" />
			</span>
		</div>

		<?php
	}

	/**
	 * Show custom slug to the affiliate on the dashboard-tab-urls.php template
	 *
	 * @since 1.0.0
	 */
	public function show_custom_slug( $affiliate_id ) {

		$display_slug = affiliate_wp()->settings->get( 'custom_affiliate_slugs_affiliate_show_slug' );
		$custom_slug  = $this->get_slug( $affiliate_id );

		if ( 'slug' == affwp_get_referral_format() && $custom_slug ) : ?>
			<p><?php printf( __( 'Your custom slug is: <strong>%s</strong>', 'affiliatewp-custom-affiliate-slugs' ), $custom_slug ); ?></p>
		<?php elseif ( $custom_slug && $display_slug ) : // fallback to username ?>
			<p><?php printf( __( 'Your custom slug is: <strong>%s</strong>', 'affiliatewp-custom-affiliate-slugs' ), $custom_slug ); ?></p>
			<p><?php printf( __( 'Your referral URL using your custom slug is: <strong>%s</strong>', 'affiliate-wp' ), esc_url( urldecode( affwp_get_affiliate_referral_url( array( 'format' => 'slug' ) ) ) ) ); ?></p>
		<?php endif;
	}

    /**
     * Filter the referral format value
     *
     * @since 1.0.0
     */
    public function filter_referral_format_value( $value, $format, $affiliate_id ) {

        /**
         * Show the affiliate their custom slug (if they have one) and if
         * "Default Referral Format" is set to "Custom Affiliate Slug" in the admin
         */
        if ( 'slug' == $format && $this->get_slug( $affiliate_id ) ) {
            $value = $this->get_slug( $affiliate_id );

        } elseif( 'slug' == $format && ! $this->get_slug( $affiliate_id ) ) {

			/**
	         * Fallback to username if the affiliate does not have a custom slug and
	         * "Default Referral Format" is set to "Custom Affiliate Slug" in the admin
	         */
            $value = urlencode( affwp_get_affiliate_username( $affiliate_id ) );
        }

        return $value;

    }

    /**
     * Allow custom slugs to be used in affiliate URLs
	 *
	 * @since 1.0.0
     */
    public function allow_slugs( $affiliate_id, $login = '' ) {

		if ( ! isset( $login ) ) {
			return $affiliate_id;
		}

        // get affiliate ID from string
        $get_affiliate_id_from_login = affiliate_wp()->affiliate_meta->get_column_by( "affiliate_id", "meta_value", $login );

        if ( $get_affiliate_id_from_login ) {
            $affiliate_id = $get_affiliate_id_from_login;
        }

        return $affiliate_id;

    }

	/**
	 * Generates a slug for the affiliate when they register from the front-end
     * affiliate registration form
	 *
	 * @since 1.0.0
	 * @todo merge with process_add_affiliate_slug() function
	 */
	public function add_slug( $affiliate_id = 0 ) {

        if ( ! affwp_cas_is_automatic_slug_creation_enabled() ) {
		    return;
		}

		// only process front-end requests
		if ( is_admin() ) {
			return;
		}

		$args = array(
			'length' => affiliate_wp()->settings->get( 'custom_affiliate_slugs_length' ),
			'type'   => affiliate_wp()->settings->get( 'custom_affiliate_slugs_slug_type' )
		);

		// create the slug
		$slug = $this->generate_random_string( $args );

        // store it in affiliate meta for the affiliate_wp
        affwp_add_affiliate_meta( $affiliate_id, $this->meta_key, $slug );

	}

	/**
	 * Delete an affiliate's custom slug when the affiliate is also deleted
	 *
	 * @since 1.0.0
	 */
	public function delete_slug( $affiliate_id, $delete_data ) {
		affwp_delete_affiliate_meta( $affiliate_id, $this->meta_key );
	}

	/**
	 * Generate a random string of alphabetic or alphanumeric characters for the affiliate slug
	 * http://stackoverflow.com/a/4356295
	 *
	 * @since 1.0.0
	 */
	public function generate_random_string( $args = array() ) {

		// get the slug length
		$length = isset( $args['length'] ) ? $args['length'] : affiliate_wp()->settings->get( 'custom_affiliate_slugs_length' );

		// prevent strings over 60 characters from being generated
		if ( $length > 60 ) {
			$length = 60;
		}

		// get the slug type
		$type = isset( $args['type'] ) ? $args['type'] : affiliate_wp()->settings->get( 'custom_affiliate_slugs_slug_type' );

		switch ( $type ) {

			case 'alphabetic':
				$characters = 'abcdefghijklmnopqrstuvwxyz';
				break;

			case 'alphanumeric':
			default:
				$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
				break;

		}

        // prevent admin preview from showing a number if 1 character is set and type is alphanumeric
        if ( is_admin() && $length == 1 && 'alphanumeric' == $type ) {
            $characters = 'abcdefghijklmnopqrstuvwxyz'; // no numbers
        }

		// Get the string length
	    $characters_length = strlen( $characters );

		if ( is_admin() ) {

			// admin preview doesn't need to check if a slug already exists
			$random_string = '';

			for ( $i = 0; $i < $length; $i++ ) {
				$random_string .= $characters[ mt_rand( 0, $characters_length - 1 ) ];
			}

		} else {

			do {

				$random_string = '';

				for ( $i = 0; $i < $length; $i++ ) {
					$random_string .= $characters[ mt_rand( 0, $characters_length - 1 ) ];
				}
			}

			while ( $this->check( $random_string ) );

		}

    	return $random_string;

	}

    /**
     * Check if the slug already exists
	 *
	 * @since 1.0.0
     */
    public function check( $slug ) {

    	global $wpdb;

        if ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
            // Allows a single affiliate meta table for the whole network
            $table_name = 'affiliate_wp_affiliatemeta';
        } else {
            $table_name = $wpdb->prefix . 'affiliate_wp_affiliatemeta';
        }

        $wpdb->get_results( $wpdb->prepare( "SELECT meta_key FROM $table_name where meta_key = '$this->meta_key' and meta_value = %s", $slug ) );

		// slug cannot be all numeric
		if ( is_numeric( $slug ) ) {
			return true;
		}

		if ( ( $wpdb->num_rows ) > 0 ) {
            return true;
        }

    	return false;
    }

    /**
     * Get a slug based on the affiliate ID
	 *
	 * @since 1.0.0
     */
    public function get_slug( $affiliate_id = 0 ) {

        if ( ! $affiliate_id ) {
            $affiliate_id = affwp_get_affiliate_id();
        }

        $slug = affwp_get_affiliate_meta( $affiliate_id, $this->meta_key, true );

		if ( $slug ) {
			return $slug;
		}

		return false;

    }

	/**
	 * Returns a random string for an admin preview, based on the admin settings chosen
	 *
	 * @since 1.0.0
	 */
	public function preview_slug() {

		// the posted slug length
		$slug_length = isset( $_POST['length'] ) ? $_POST['length'] : '';

		// the posted slug type
		$slug_type   = isset( $_POST['type'] ) ? $_POST['type'] : '';

		// generate a random string based on the posted options
		$random_string = $this->generate_random_string( array( 'length' => $slug_length, 'type' => $slug_type ) );

		// get the current transients
		$current_slug        = get_transient( 'affwp_admin_preview_slug' );
		$current_slug_length = get_transient( 'affwp_admin_preview_slug_length' );
		$current_slug_type   = get_transient( 'affwp_admin_preview_slug_type' );

		// if any of the posted settings are different than what's set in transients
		// then generate a new random string and set new transients
		if ( $slug_length !== $current_slug_length || $slug_type !== $current_slug_type	) {

			// set the new transients
			set_transient( 'affwp_admin_preview_slug', $random_string );
			set_transient( 'affwp_admin_preview_slug_length', $slug_length );
			set_transient( 'affwp_admin_preview_slug_type', $slug_type );

			// return a new random string
			$slug = $random_string;

		} else {
			// nothing changed, return the current slug in transient
			$slug = $current_slug;
		}

		$return['slug'] = $slug;

		echo json_encode( $return );

		wp_die();
	}

	/**
	 * Checks slug
	 *
	 * @since 1.0.0
	 */
	public function check_slug() {

		// Get the new slug
		$new_slug = isset( $_POST['newSlug'] ) ? $_POST['newSlug'] : '';
        $is_admin = isset( $_POST['referrer'] ) && 'admin' === $_POST['referrer'] ? true : false;

		// Get affiliate's current slug
		if ( $is_admin ) {
			$affiliate_id = isset( $_POST['affiliateID'] ) ? $_POST['affiliateID'] : '';
		} else {
			$affiliate_id = affwp_get_affiliate_id();
		}

		$current_slug = $this->get_slug( $affiliate_id );

		$return = false;

		if ( $this->check( $new_slug ) ) {
			$return['slug_valid'] = false;
			$return['slug_error'] = __( 'This slug cannot be used.', 'affiliatewp-custom-affiliate-slugs' );
		}

		// Slug cannot be an existing WordPress username
		if ( username_exists( $new_slug ) ) {
			$return['slug_valid'] = false;
			$return['slug_error'] = __( 'This slug cannot be used.', 'affiliatewp-custom-affiliate-slugs' );
		}

		// Check if the slug contains invalid characters
		// Can only be alphabetic or alphanumerical
        // only run this check when the slug is greater than 0 characters
		if ( ( ! ( ctype_alpha( $new_slug ) || ctype_alnum( $new_slug ) ) ) && strlen( $new_slug ) > 0 ) {
			$return['slug_valid'] = false;
			$return['slug_error'] = __( 'This slug contains invalid characters.', 'affiliatewp-custom-affiliate-slugs' );
		}

		// Check the length of the slug
		$length = affiliate_wp()->settings->get( 'custom_affiliate_slugs_length' );

		// Slug cannot be over 60 characters (WordPress core limit)
		if ( strlen( $new_slug ) > 60 ) {
			$return['slug_valid'] = false;
			$return['slug_error'] = __( 'This slug is too long.', 'affiliatewp-custom-affiliate-slugs' );
		}

		// Slug cannot be numeric or it will be treated as an affiliate ID
		if ( is_numeric( $new_slug ) ) {
			$return['slug_valid'] = false;
			$return['slug_error'] = __( 'Your slug cannot contain all numbers.', 'affiliatewp-custom-affiliate-slugs' );
		}

		// Slug is the same as current slug
		if ( $new_slug === $current_slug ) {
			$return['slug_valid'] = false;
			$return['slug_error'] = __( 'This is your current slug.', 'affiliatewp-custom-affiliate-slugs' );
		}

		// no errors
		if ( ! $return['slug_error'] ) {
			$return['slug_valid']   = true;

            // only the front-end needs the additional HTML
            if ( ! $is_admin ) {

                if ( empty( $new_slug ) ) {
                    $return['slug_confirm'] = $this->confirm_empty_slug_html();
                } else {
                    $return['slug_confirm'] = $this->confirm_slug_html();
                }

            } elseif ( $is_admin ) {
				$return['slug_confirm'] = $this->confirm_slug_admin_html();
			}


		}

		echo json_encode( $return );

		wp_die();
	}

    /**
     * Confirm slug HTML
     *
     * @since 1.0.0
     */
    public function confirm_slug_html() {

        ob_start();

        ?>

        <div class="affwp-wrap affwp-custom-slug-confirm-wrap">
            <label for="affwp-slug-confirm"><?php _e( 'Confirm Affiliate Slug', 'affiliatewp-custom-affiliate-slugs' ); ?></label>
            <input id="affiliate-slug-confirm" type="text" name="custom_slug_confirm" value="" />
            <p class="description" style="margin-top:20px;"><?php _e( 'By changing your affiliate slug you acknowledge that any existing links using an older affiliate slug may no longer work. Type your new custom slug one more time to confirm, and then click the "Save Profile Settings" button below.', 'affiliatewp-custom-affiliate-slugs' ); ?></p>
        </div>

    <?php
        return ob_get_clean();
    }

    /**
     * Confirm empty slug HTML
     *
     * @since 1.0.0
     */
    public function confirm_empty_slug_html() {

        ob_start();

        ?>

        <div class="affwp-wrap affwp-custom-slug-confirm-wrap">
            <label for="affiliate-slug-confirm-removal"><?php _e( 'Confirm Removal', 'affiliatewp-custom-affiliate-slugs' ); ?></label>
			<input id="affiliate-slug-confirm-removal" type="checkbox" name="custom_slug_confirm_removal" value="" />
            <p class="description" style="margin-top:20px;"><?php _e( 'By removing your affiliate slug you acknowledge that any existing links using an older affiliate slug may no longer work.', 'affiliatewp-custom-affiliate-slugs' ); ?></p>
        </div>

    <?php
        return ob_get_clean();
    }

	/**
	 * Confirm slug admin HTML
	 *
	 * @since 1.0.0
	 */
	public function confirm_slug_admin_html() {

		ob_start();

		?>

		<tr class="form-row confirm-slug-row">
			<th scope="row">
				<?php _e( 'Confirm Slug Change', 'affiliatewp-custom-affiliate-slugs' ); ?>
			</th>
			<td>
				<label for="confirm-custom-slug">
				<input id="confirm-custom-slug" type="checkbox" name="confirm_custom_slug" />
				<?php _e( 'I acknowledge that by changing this affiliate\'s custom slug, any existing referral links using the old slug may no longer work.', 'affiliatewp-custom-affiliate-slugs' ); ?>
				</label>
			</td>
		</tr>

	<?php
		return ob_get_clean();
	}

	/**
	 * Displays the agree to slug change input and description when the slug has been changed
	 *
	 * @since 1.0.0
	 */
	public function scripts_profile_settings() {

		// return if custom slugs are not allowed
		if ( ! $this->allow_affiliate_slugs( affwp_get_affiliate_id() ) ) {
			return;
		}

		$post = get_post();

		if( ! $post ) {
			return;
		}

		// only load this on the affiliate settings tab of the affiliate area
		if ( ! ( has_shortcode( $post->post_content, 'affiliate_area' ) && isset( $_GET['tab'] ) && $_GET['tab'] == 'settings' ) ) {
			return;
		}

		?>
		<script>

		jQuery(document).ready(function ($) {

            var loading = $("#slug-loader");
			var affiliateSlugField = $('#custom-slug');
			var requestRunning = false;
			var typingTimer;
			var doneTypingInterval = 250;

            // hide the loading icon
            loading.hide();

			// on keyup, start the countdown
			affiliateSlugField.on('keyup', function () {

				// don't do anything if an AJAX request is pending
				if ( requestRunning ) {
					return;
				}

				clearTimeout( typingTimer );
				typingTimer = setTimeout( doneTyping, doneTypingInterval );

			});

			// on keydown, clear the countdown
			affiliateSlugField.on('keydown', function () {
				clearTimeout( typingTimer );
			});

			// user is "finished typing", do something
			function doneTyping() {

				$(this).checkSlug();

				// show the loading icon
				loading.show();

				requestRunning = true;

				// disable the submit button
				$( '.affwp-save-profile-wrap .button' ).attr( 'disabled', 'disabled' );

			}

			$( document ).ajaxSuccess(function() {

				// the confirm slug input field
				var affiliateSlugFieldConfirm = $( '#affiliate-slug-confirm' );

                //
                var affiliateSlugConfirmRemoval = $( '#affiliate-slug-confirm-removal' );

				// When the confirm slug field has been modified in any way, check the value to see if it matches
				$( affiliateSlugFieldConfirm ).on('input propertychange paste', function() {

					if ( affiliateSlugField.val() == $( this ).val() ) {
						// fields match, remove the disabled attribute
						$( '.affwp-save-profile-wrap .button' ).removeAttr( 'disabled' );
					} else {
						// disable the fields again, they don't match
						$( '.affwp-save-profile-wrap .button' ).attr( 'disabled', 'disabled' );
					}

				});

                affiliateSlugConfirmRemoval.click( function() {

					if ( this.checked ) {
					    $( '.affwp-save-profile-wrap .button' ).removeAttr( 'disabled' );
					} else {
                        $( '.affwp-save-profile-wrap .button' ).attr( 'disabled', 'disabled' );
                    }

				});

			});

			/**
			 * Check slug ajax call
			 */
			(function( $ ) {
				$.fn.checkSlug = function() {

	   				var postData = {
	   					action: 'check_slug',
						newSlug: affiliateSlugField.val() // new value of field
	   				};

	   				$.ajax({
		   				type: "POST",
		   				data: postData,
		   				dataType: "json",
		   				url: affwp_scripts.ajaxurl,
		   				success: function ( response ) {

							var confirmSlug = $( '.affwp-custom-slug-confirm-wrap' );

							// hide the loading icon
							loading.hide();

							// validation passed
							if ( response.slug_valid ) {

								confirmSlug.remove();

								// show a confirm slug field
								$( response.slug_confirm ).insertAfter('.affwp-custom-slug-wrap');

								// remove any errors
								$( '.affwp-custom-slug-wrap .affwp-errors' ).remove();

							} else {
								// not a valid slug

								var error = $( '.affwp-errors' );

								if ( ! $( error ).length ) {
									$( '.affwp-custom-slug-wrap' ).append( '<div class="affwp-errors" style="margin-top:20px;"><p class="affwp-error">' + response.slug_error + '</p></div>' );
								}

								// remove the confirm slug wrap
								confirmSlug.remove();

							}

		   				},
						complete: function() {
							requestRunning = false;
						}
	   				}).fail(function (data) {
	   					console.log( data );
	   				});

			      return this;
			   };
			})( jQuery );

		});

		</script>

		<?php
	}

}
new AffiliateWP_Custom_Affiliates_Slugs_Base;
