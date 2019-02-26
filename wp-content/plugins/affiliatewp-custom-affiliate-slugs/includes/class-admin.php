<?php

class AffiliateWP_Custom_Affiliate_Slugs_Admin {

	/**
	 * Affiliate meta key
	 *
	 * @since 1.0.0
	 */
	private $meta_key = 'custom_slug';

	public function __construct() {
        add_filter( 'affwp_settings_tabs', array( $this, 'setting_tab' ) );
        add_filter( 'affwp_settings', array( $this, 'register_settings' ) );
        add_filter( 'affwp_settings_referral_format', array( $this, 'referral_format' ) );
		add_filter( 'affwp_export_csv_cols_affiliates', array( $this, 'export_affiliates_csv_cols' ) );
		add_filter( 'affwp_export_get_data_affiliates', array( $this, 'export_get_data_affiliates' ) );

		// add options to the edit affiliate screen
		add_action( 'affwp_edit_affiliate_end', array( $this, 'edit_affiliate' ) );

		// update affiliate from edit affiliate screen
		add_action( 'affwp_update_affiliate', array( $this, 'update_affiliate_slug' ), -1 );

		// allow admin to set custom slug when manually adding an affiliate
		add_action( 'affwp_new_affiliate_end', array( $this, 'add_affiliate_slug' ) );
		add_action( 'affwp_insert_affiliate', array( $this, 'process_add_affiliate_slug' ) );

		add_action( 'in_admin_footer', array( $this, 'scripts' ) );

		// validate the custom slug added by the admin
		add_action( 'in_admin_footer', array( $this, 'validate_admin_custom_slug' ) );

	}

	/**
	 * Saves the custom affiliate slug when the affiliate is added via the admin, or via front-end registration.
	 *
	 * @since 1.0.0
	 */
	public function process_add_affiliate_slug( $affiliate_id = 0 ) {

		$base = new AffiliateWP_Custom_Affiliates_Slugs_Base;

		if ( ! empty( $_POST['custom_slug'] ) ) {

			// validate slug in case JS is disabled
			$validated = $base->validate( $_POST );

			if ( $validated ) {
				affwp_add_affiliate_meta( $affiliate_id, $this->meta_key, $_POST['custom_slug'] );
			}

		} elseif ( affwp_cas_is_automatic_slug_creation_enabled() ) {
			// generate a random slug is enabled

			$args = array(
				'length' => affiliate_wp()->settings->get( 'custom_affiliate_slugs_length' ),
				'type'   => affiliate_wp()->settings->get( 'custom_affiliate_slugs_slug_type' )
			);

			// create the slug
			$slug = $base->generate_random_string( $args );

	        // store it in affiliate meta for the affiliate_wp
	        affwp_add_affiliate_meta( $affiliate_id, $this->meta_key, $slug );

		}

	}

	/**
	 * Adds a new "Custom Slug" column header to the affiliate's exported .csv file.
	 *
	 * @since 1.0.0
	 */
	function export_affiliates_csv_cols( $cols ) {
		$cols['custom-slug'] = __( 'Custom Slug', 'affiliatewp-custom-affiliate-slugs' );
		return $cols;
	}

	/**
	 * Adds an affiliate's custom slug to the exported affiliate's .csv file.
	 *
	 * @since 1.0.0
	 */
	function export_get_data_affiliates( $data ) {

		$base = new AffiliateWP_Custom_Affiliates_Slugs_Base;

	    foreach ( $data as $key => $d ) {
			$data[$key]['custom-slug'] = $base->get_slug( $d['affiliate_id'] );
		}

		return $data;
	}

	/**
	 * Provides Edit Affiliate screen options.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function edit_affiliate( $affiliate ) {

		$base           = new AffiliateWP_Custom_Affiliates_Slugs_Base;
		$affiliate_slug = $base->get_slug( $affiliate->affiliate_id );

		$checked = affwp_get_affiliate_meta( $affiliate->affiliate_id, 'custom_slugs_enabled', true );

		$allow_slugs = $base->allow_affiliate_slugs();

		?>

		<?php if ( ! $allow_slugs ) : ?>
		<tr class="form-row">
			<th scope="row">
				<?php _e( 'Allow Custom Slug', 'affiliatewp-custom-affiliate-slugs' ); ?>
			</th>
			<td>
				<label for="custom-slugs-enabled">
				<input type="checkbox" name="custom_slugs_enabled" id="custom-slugs-enabled" value="1" <?php checked( $checked, '1' ); ?>/>
				<?php _e( 'Allow affiliate to create a custom slug.', 'affiliatewp-custom-affiliate-slugs' ); ?>
				</label>

			</td>
		</tr>
		<?php endif; ?>

		<tr class="form-row">
			<th scope="row">
				<label for="custom-slug"><?php _e( 'Custom Slug', 'affiliatewp-custom-affiliate-slugs' ); ?></label>
			</th>
			<td>
				<span class="affwp-ajax-search-wrap">
					<input class="regular-text" type="text" name="custom_slug" id="custom-slug" value="<?php echo esc_attr( $affiliate_slug ); ?>" maxlength="60" />
					<img class="affwp-ajax-loader" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" style="display: none;" />
				</span>
				<p class="description"><?php _e( 'The affiliate\'s custom slug.', 'affiliatewp-custom-affiliate-slugs' ); ?></p>
			</td>
		</tr>

		<?php
	}

	/**
	 * Updates slug from the edit affiliate screen.
	 *
	 * @since 1.0.0
	 */
	public function update_affiliate_slug( $data ) {

		if ( ! is_admin() ) {
			return false;
		}

		if ( ! current_user_can( 'manage_affiliates' ) ) {
			wp_die( __( 'You do not have permission to manage affiliates', 'affiliatewp-custom-affiliate-slugs' ), __( 'Error', 'affiliatewp-custom-affiliate-slugs' ), array( 'response' => 403 ) );
		}

		// slug field is not empty
		if ( ! empty( $_POST['custom_slug'] ) ) {

			$base = new AffiliateWP_Custom_Affiliates_Slugs_Base;

			// validate slug in case JS is disabled
			$validated = $base->validate( $_POST );

			$confirm_slug = isset( $_POST['confirm_custom_slug'] ) ? $_POST['confirm_custom_slug'] : '';

			// sluf is updated and admin has confirmed change
			if ( $validated && $confirm_slug ) {
				affwp_update_affiliate_meta( $data['affiliate_id'], $this->meta_key, $data['custom_slug'] );
			}

		} else {
			// delete the slug, since it's empty
			affwp_delete_affiliate_meta( $data['affiliate_id'], $this->meta_key );
		}

		// per-affiliate custom slugs
		if ( isset( $_POST['custom_slugs_enabled'] ) ) {
			affwp_add_affiliate_meta( $data['affiliate_id'], 'custom_slugs_enabled', $data['custom_slugs_enabled'] );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'custom_slugs_enabled' );
		}
	}

	/**
	 * Shows the Custom Slug input field on the add new affiliate screen.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function add_affiliate_slug() {
		?>

		<tr class="form-row">
			<th scope="row">
				<label for="custom-slug"><?php _e( 'Custom Slug', 'affiliatewp-custom-affiliate-slugs' ); ?></label>
			</th>
			<td>
				<span class="affwp-ajax-search-wrap">
					<input class="regular-text" type="text" name="custom_slug" id="custom-slug" value="" maxlength="60" />
					<img class="affwp-ajax-loader" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" style="display: none;" />
				</span>
				<p class="description"><?php _e( 'The affiliate\'s custom slug.', 'affiliatewp-custom-affiliate-slugs' ); ?></p>
			</td>
		</tr>

		<?php
	}

    /**
	 * Registers a new settings tab.
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function setting_tab( $tabs ) {
		$tabs['custom_affiliate_slugs'] = __( 'Custom Affiliate Slugs', 'affiliatewp-custom-affiliate-slugs' );
		return $tabs;
	}

    /**
     * Registers our settings.
     *
     * @access public
     * @since 1.0
     * @return array
     */
    public function register_settings( $settings = array() ) {

        $settings[ 'custom_affiliate_slugs' ] = array(

			'custom_affiliate_slugs_affiliate_slug_creation' => array(
                'name' => __( 'Allow Custom Affiliate Slugs', 'affiliatewp-custom-affiliate-slugs' ),
                'desc' => __( 'Allow all affiliates to create a custom slug. Leave unchecked if you would like to enable custom slugs on a per-affiliate basis.', 'affiliatewp-custom-affiliate-slugs' ),
                'type' => 'checkbox'
            ),
			'custom_affiliate_slugs_affiliate_show_slug' => array(
                'name' => __( 'Show Custom Slug', 'affiliatewp-custom-affiliate-slugs' ),
                'desc' => sprintf( __( 'Show the affiliate\'s custom slug in the <strong>Affiliate URLs</strong> tab of the Affiliate Area. This will only show if the affiliate has a custom slug, and the %sDefault Referral Format%s is set to <strong>ID</strong> or <strong>Username</strong>.', 'affiliatewp-custom-affiliate-slugs' ), '<a href="' . admin_url( 'admin.php?page=affiliate-wp-settings&tab=general' ) . '">', '</a>' ),
                'type' => 'checkbox'
            ),
			'custom_affiliate_slugs_automatic_slug_creation' => array(
                'name' => __( 'Auto-create Custom Slugs', 'affiliatewp-custom-affiliate-slugs' ),
                'desc' => __( 'Enable this if you\'d like custom slugs to be automatically created for affiliates when they register, or when affiliates are added manually.', 'affiliatewp-custom-affiliate-slugs' ),
                'type' => 'checkbox'
            ),
			'custom_affiliate_slugs_settings' => array(
                'name' => __( 'Auto-create Slug Settings', 'affiliatewp-custom-affiliate-slugs' ),
                'type' => 'header'
            ),
			'custom_affiliate_slugs_slug_type' => array(
                'name' => __( 'Slug Type', 'affiliatewp-custom-affiliate-slugs' ),
                'desc' => __( 'Select how the slug should appear, Alphanumeric or Alphabetic.', 'affiliatewp-custom-affiliate-slugs' ),
                'type' => 'select',
				'options' => array(
		            'alphanumeric' => __( 'Alphanumeric', 'affiliatewp-signup-referrals' ),
		            'alphabetic'  => __( 'Alphabetic', 'affiliatewp-signup-referrals' ),
		        ),
		        'std' => ''
            ),
			'custom_affiliate_slugs_length' => array(
                'name' => __( 'Slug Length', 'affiliatewp-custom-affiliate-slugs' ),
                'desc' => __( 'The length of the custom slug. Will only apply to newly generated slugs.', 'affiliatewp-custom-affiliate-slugs' ),
                'type' => 'number',
                'min'  => 1,
                'max'  => 60, // the limit of WordPress core
                'step' => '1',
                'size' => 'small',
				'std'  => 5
            )

        );

        return $settings;

    }

    /**
     * Adds a custom slug option to the referral format select menu.
	 *
	 * @since 1.0.0
     */
    public function referral_format( $options ) {
        $options['slug'] = __( 'Custom Affiliate Slug', 'affiliatewp-custom-affiliate-slugs' );

        return $options;
    }

	/**
	 * Validates the custom slug added by the admin.
	 *
	 * @since 1.0.0
	 */
	public function validate_admin_custom_slug() {

		$screen       = get_current_screen();
		$affiliate_id = isset( $_GET['affiliate_id'] ) && $_GET['affiliate_id'] ? $_GET['affiliate_id'] : '';

		if (
			$screen->id === 'affiliates_page_affiliate-wp-affiliates' &&
			isset( $_GET['action'] ) &&
			( $_GET['action'] === 'edit_affiliate' || $_GET['action'] === 'add_affiliate' )
		) :

		?>
		<style>.affwp-ajax-loader { position: absolute; right: 8px; top: 1px; }</style>

		<script>

		jQuery(document).ready(function ($) {

			var loading = $("#custom-slug + .affwp-ajax-loader");

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
				$( '#submit' ).attr( 'disabled', 'disabled' );

			}

			/**
			 * Checks slug ajax call.
			 */
			(function( $ ) {
				$.fn.checkSlug = function() {

					var postData = {
						action: 'check_slug',
						newSlug: affiliateSlugField.val(), // new value of field
						referrer: 'admin',
						affiliateID: '<?php echo $affiliate_id; ?>'
					};

					$.ajax({
						type: "POST",
						data: postData,
						dataType: "json",
						url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
						success: function ( response ) {

							var confirmSlugRow = $( '.confirm-slug-row' );

							// hide the loading icon
							loading.hide();

							// custom slug row
							var custom_slug_row = $('#affwp_edit_affiliate').find('.affwp-ajax-search-wrap').closest('tr');

							// validation passed
							if ( response.slug_valid ) {

								$('#submit').prop("disabled", false); // Element(s) are now enabled.

								confirmSlugRow.remove();

								// show a confirm slug field
								$( response.slug_confirm ).insertAfter(custom_slug_row);

								// remove any errors
								$( '.affwp-ajax-search-wrap + .error' ).remove();

							} else {
								// not a valid slug

								var error = $( '.error' );

								if ( ! $( error ).length ) {
									affiliateSlugField.parent('.affwp-ajax-search-wrap').after( '<div class="error notice"><p>' + response.slug_error + '</p></div>' );
								}

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

	<?php endif;
	}

	/**
	 * Shows or hides the table row based on the checkbox option.
	 *
	 * @since 1.0.0
	 */
	public function scripts() {

		$screen = get_current_screen();

		if ( ! ( $screen->id === 'affiliates_page_affiliate-wp-settings' && is_admin() && isset( $_GET['tab'] ) && $_GET['tab'] === 'custom_affiliate_slugs' ) ) {
			return;
		}

		$base_url = home_url( '/' );
		$pretty = affwp_is_pretty_referral_urls();

		// http://site.com/ref/
		$pretty_urls = trailingslashit( $base_url ) . trailingslashit( affiliate_wp()->tracking->get_referral_var() );

		// http://site.com/?ref=
		$non_pretty_urls = esc_url( add_query_arg( affiliate_wp()->tracking->get_referral_var(), '=', $base_url ) );

		if ( $pretty ) {
			$url = $pretty_urls;
		} else {
			$url = $non_pretty_urls;
		}

		?>
		<script>

			jQuery(document).ready(function($) {

				var slugTypeField = $('label[for="affwp_settings[custom_affiliate_slugs_settings]"]');
				var slugTypeFieldRow = slugTypeField.closest( 'tr' );

				slugTypeFieldRow.after( '<tr id="preview-slug"><th scope="row"><?php _e( 'Slug Preview', 'affiliatewp-custom-affiliate-slugs' );?></th><td><?php echo $url; ?><strong id="slug-preview"></strong><p class="description"><?php _e( 'This is an example of how the slug might look for the affiliate, based on the settings below.', 'affiliatewp-custom-affiliate-slugs' ); ?></p></td></tr>' );

				// Generate an affiliate slug for the preview
				// when these fields are changed the new random string will be created
				var slugFields = $('select[name="affwp_settings[custom_affiliate_slugs_slug_type]"], input[name="affwp_settings[custom_affiliate_slugs_length]"]');

				(function( $ ){
					$.fn.previewSlug = function() {

						var slugLength = $('input[name="affwp_settings[custom_affiliate_slugs_length]"]').val();
						var slugType = $('select[name="affwp_settings[custom_affiliate_slugs_slug_type]"]').val();

		   				var postData = {
		   					action: 'preview_slug',
		   					length: slugLength,
		   					type: slugType
		   				};

		   				$.ajax({
			   				type: "POST",
			   				data: postData,
			   				dataType: "json",
			   				url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
			   				success: function ( response ) {

			   					if ( response.slug ) {
			   						jQuery('#slug-preview').html( response.slug );
			   					}

			   				}
		   				}).fail(function (data) {
		   					console.log( data );
		   				});

				      return this;
				   };
				})( jQuery );

				// retrieve random slug when the field values are changed
				$( slugFields ).change(function(e) {
					$(this).previewSlug();
				});

				// change them on page load
				$( slugFields ).previewSlug();

				// The "Allow Affiliates to Create Slug" option
				var optionAllowAffiliateSlugCreation = $('input[name="affwp_settings[custom_affiliate_slugs_automatic_slug_creation]"]');

				// The "Automatic Slug Creation" option
				var optionEnableAutomaticSlugCreation = $('input[name="affwp_settings[custom_affiliate_slugs_automatic_slug_creation]"]');


				// The TR that holds the "Allow Affiliates to Create Slug" option
				var rowAllowAffiliateSlugCreation = optionAllowAffiliateSlugCreation.closest('tr');

				// An object that contains the TRs for the automatic slug creation settings
				var automaticSlugCreationSettings = $('label[for="affwp_settings[custom_affiliate_slugs_settings]"]').closest('tr').nextAll("*:lt(3)").andSelf();

				if ( optionEnableAutomaticSlugCreation.is(':checked') ) {
					//
					$( automaticSlugCreationSettings ).show();
				} else {
					$( automaticSlugCreationSettings ).hide();
				}

				// show the settings for automatic slug creation when checked
				optionEnableAutomaticSlugCreation.click( function() {

					if ( this.checked ) {
						$( automaticSlugCreationSettings ).show();
					} else {
						$( automaticSlugCreationSettings ).hide();
					}

				});

			});
		</script>

		<?php
	}

}
new AffiliateWP_Custom_Affiliate_Slugs_Admin;
