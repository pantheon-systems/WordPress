<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_Direct_Link_Tracking_Admin {

	private $field_name = 'direct_link_tracking_urls';

	public function __construct() {

		// Settings tab
		add_filter( 'affwp_settings_tabs', array( $this, 'setting_tab' ) );

		// Register Direct Link admin page as an admin page in AffiliateWP
		add_filter( 'affwp_is_admin_page', array( $this, 'is_admin_page' ) );

		// Settings
		add_filter( 'affwp_settings', array( $this, 'register_settings' ) );

		// Add options to the edit affiliate screen
		add_action( 'affwp_edit_affiliate_end', array( $this, 'edit_affiliate' ) );

		// Update affiliate from edit affiliate screen
		add_action( 'affwp_update_affiliate', array( $this, 'update_affiliate_url' ), -1 );

		// Register menus
		add_action( 'admin_menu', array( $this, 'register_menus' ) );

		// admin styles
		add_action( 'admin_head', array( $this, 'styles' ) );

		// admin scripts
		add_action( 'admin_footer', array( $this, 'scripts' ) );
	}

	/**
	 * Register Direct Link admin page as an admin page in AffiliateWP
	 *
	 * @access public
	 * @since 1.1
	 * @return array
	 */
	public function is_admin_page( $ret ) {

		if ( isset( $_GET['page'] ) && 'affiliate-wp-direct-links' === $_GET['page'] ) {
			$ret = true;
		}

		return $ret;

	}

	/**
	 * Register the new settings tab
	 *
	 * @access public
	 * @since 1.5
	 * @return array
	 */
	public function setting_tab( $tabs ) {
		$tabs['direct-link-tracking'] = __( 'Direct Link Tracking', 'affiliatewp-direct-link-tracking' );
		return $tabs;
	}


	/**
	 * Determine if we're on the affiliate edit screen
	 *
	 * @since 1.0.0
	 */
	public function is_affiliate_edit_screen() {

		if ( is_admin() && ( isset( $_GET['action'] ) && 'edit_affiliate' === $_GET['action'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Admin styles
	 *
	 * @since 1.0.0
	 */
	public function styles() {

		if ( ! $this->is_affiliate_edit_screen() ) {
			return;
		}

		?>
		<style>

		.direct-link-row .current-url + .pending-url{margin-top:30px;}
		.direct-link-actions{margin-top:10px;}
		.direct-link-url-clone{display:none;}
		.affwp-dlt-remove-url { margin-top:5px; display:block; font-weight: normal; }
		.dlt-notice {
			background: #fff none repeat scroll 0 0;
			border-left: 4px solid #dc3232;
			box-shadow: 0 1px 1px 0 rgba(0, 0, 0, 0.1);
			margin: 5px 0 0 0;
			padding: 1px 12px;
		}

		.form-table td .dlt-notice p {margin: 0.5em 0; }

		</style>
	<?php

	}

	/**
	 * Register submenu page in admin
	 *
	 * @since 1.0.0
	 */
	public function register_menus() {

		$pending_urls = affwp_dlt_get_direct_links( array( 'status' => 'pending' ) );
		$pending_count = $pending_urls ? count( $pending_urls ) : 0;
		$pending_title = esc_attr( sprintf( __( '%d direct links pending approval', 'affiliatewp-direct-link-tracking' ), $pending_count ) );

		$menu_label = sprintf( __( 'Direct Links %s', 'affiliatewp-direct-link-tracking' ), "<span class='update-plugins count-$pending_count' title='$pending_title'><span class='update-count'>" . number_format_i18n( $pending_count ) . "</span></span>" );

		add_submenu_page( 'affiliate-wp', __( 'Direct Links', 'affiliatewp-direct-link-tracking' ), $menu_label, 'manage_affiliates', 'affiliate-wp-direct-links', 'affwp_direct_links_admin' );
	}

	/**
	 * Register our settings
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array
	 */
	public function register_settings( $settings = array() ) {

		$settings['direct-link-tracking'] = array(
			'direct_link_tracking_header' => array(
				'name' => __( 'Direct Link Tracking', 'affiliatewp-direct-link-tracking' ),
				'type' => 'header'
			),
			'direct_link_tracking' => array(
				'name' => __( 'Allow Direct Link Tracking', 'affiliatewp-direct-link-tracking' ),
				'desc' => __( 'Allow direct link tracking for all affiliates. Leave unchecked if you would like to enable direct link tracking on a per-affiliate level.', 'affiliatewp-direct-link-tracking' ),
				'type' => 'checkbox'
			),
			'direct_link_tracking_notify_admin' => array(
				'name' => __( 'Notify Admin', 'affiliatewp-direct-link-tracking' ),
				'desc' => __( 'Notify site admin when a direct link has been submitted for approval.', 'affiliatewp-direct-link-tracking' ),
				'type' => 'checkbox'
			),
			'direct_link_tracking_notify_affiliate' => array(
				'name' => __( 'Notify Affiliate', 'affiliatewp-direct-link-tracking' ),
				'type' => 'multicheck',
				'options' => array(
					'approval_email'  => __( 'Notify affiliate when a direct link has been approved.', 'affiliatewp-direct-link-tracking' ),
					'rejection_email' => __( 'Notify affiliate when a direct link has been rejected.', 'affiliatewp-direct-link-tracking' ),
				)
			),
			'direct_link_tracking_url_limit' => array(
				'name' => __( 'Domains Allowed', 'affiliatewp-direct-link-tracking' ),
				'desc' => __( 'Enter the number of domains an affiliate can add. This can also be set on a per-affiliate level.', 'affiliatewp-direct-link-tracking' ),
				'type' => 'number',
				'size' => 'small',
				'std'  => 1,
				'step' => 1,
				'min'  => 1
			),
			'direct_link_tracking_url_blacklist' => array(
				'name' => __( 'Domain Blacklist', 'affiliate-wp' ),
				'desc' => __( 'Domains placed here will be blocked from being added (by site admin or affiliate) and prevent a visit from being created if already in use. Enter one domain per line.', 'affiliatewp-direct-link-tracking' ),
				'type' => 'textarea',
				'std'  => implode( "\n", $this->default_blacklisted_urls() ),
			),
			'direct_link_tracking_uninstall' => array(
				'name' => __( 'Remove Data When Deleted', 'affiliatewp-direct-link-tracking' ),
				'desc' => __( 'Remove all data from Direct Link Tracking when the plugin is deleted.', 'affiliatewp-direct-link-tracking' ),
				'type' => 'checkbox'
			)

		);

		return $settings;

	}

	/**
	 * The URLs that are blacklisted by default
	 *
	 * @since  1.1
	 *
	 * @return array $urls Default blacklisted URLs
	 */
	public function default_blacklisted_urls() {

		$urls = array(
			'facebook.com',
			'google.com'
		);

		return $urls;

	}

	/**
	 * Edit Affiliate screen options
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function edit_affiliate( $affiliate ) {

		$affiliate_id               = $affiliate->affiliate_id;
		$checked                    = affwp_get_affiliate_meta( $affiliate_id, 'direct_link_tracking_enabled', true );
		$allow_direct_link_tracking = affwp_dlt_allow_direct_link_tracking();
		?>

		<tr class="form-row">
			<th scope="row" id="direct-link-tracking">
				<?php _e( 'Direct Link Tracking', 'affiliatewp-direct-link-tracking' ); ?>
			</th>
			<td><hr /></td>
		</tr>

		<?php if ( ! $allow_direct_link_tracking ) : ?>
		<tr class="form-row">
			<th scope="row">
				<?php _e( 'Allow Direct Link Tracking', 'affiliatewp-direct-link-tracking' ); ?>
			</th>
			<td>
				<label for="direct-link-tracking-enabled">
				<input type="checkbox" name="direct_link_tracking_enabled" id="direct-link-tracking-enabled" value="1" <?php checked( $checked, '1' ); ?>/>
				<?php _e( 'Allow direct link tracking for this affiliate.', 'affiliatewp-direct-link-tracking' ); ?>
				</label>

			</td>
		</tr>

		<?php endif; ?>

		<?php

		/**
		 * URL limit
		 */
		$affiliate_url_limit = affwp_dlt_get_domain_limit( $affiliate_id );
		$global_url_limit    = affiliate_wp()->settings->get( 'direct_link_tracking_url_limit' );

		if ( $affiliate_url_limit ) {
			$url_limit = $affiliate_url_limit;
		} else {
			$url_limit = '';
		}

		?>
		<tr class="form-row direct-link-row">
			<th scope="row">
				<label for="direct-link-tracking-url-limit"><?php _e( 'Domains Allowed', 'affiliatewp-direct-link-tracking' ); ?></label>
			</th>
			<td>
				<input class="small-text" type="number" name="direct_link_tracking_url_limit" id="direct-link-tracking-url-limit" step="1" min="1" max="" placeholder="<?php echo esc_attr( $global_url_limit ); ?>" value="<?php echo esc_attr( $url_limit ); ?>" />
				<p class="description"><?php _e( 'The number of domains this affiliate can have for direct link tracking.', 'affiliatewp-direct-link-tracking' ); ?></p>
			</td>
		</tr>

		<?php
			$direct_links = affwp_dlt_get_direct_links( array( 'affiliate_id' => $affiliate_id ) );

			if ( $direct_links ) {
				$direct_links = array_combine( range( 1, count( $direct_links ) ), array_values( (array) $direct_links ) );
			}

			$url_count = affiliatewp_direct_link_tracking()->direct_links->count( array( 'affiliate_id' => $affiliate_id ) );

			$count = (int) affwp_dlt_get_domain_limit( $affiliate_id ) > 1 ? __( '#1', 'affiliatewp-direct-link-tracking' ) : '';
		?>

		<?php if ( ! $url_count ) : ?>

			<tr class="form-row direct-link-row direct-link-url">
				<th scope="row">
					<label for="direct-link-tracking-url[1]"><?php printf( __( 'Domain %s', 'affiliatewp-direct-link-tracking' ), $count ); ?></label>
					<?php echo $this->remove_url(); ?>
				</th>
				<td>
					<input class="large-text" id="direct-link-tracking-url[1]" type="text" name="direct_link_tracking_urls_new[]" value="" />
					<p class="description"><?php _e( 'Enter a direct link domain.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</td>
			</tr>

		<?php else : ?>

			<?php foreach ( $direct_links as $key => $direct_link ) :

				$url_id      = $direct_link->url_id;
				$url         = $direct_link->url;
				$old_url     = isset( $direct_link->url_old ) ? $direct_link->url_old : '';
				$is_pending  = isset( $direct_link->status ) && 'pending' === $direct_link->status ? true : false;
				$is_invalid  = isset( $direct_link->status ) && 'invalid' === $direct_link->status ? true : false;
				$is_rejected = isset( $direct_link->status ) && 'rejected' === $direct_link->status ? true : false;

				$count = (int) affwp_dlt_get_domain_limit( $affiliate_id ) > 1 ? sprintf( __( ' #%s', 'affiliatewp-direct-link-tracking' ), $key ) : '';
			?>
			<tr class="form-row direct-link-row direct-link-url">

				<th scope="row">
					<label for="direct-link-tracking-url[<?php echo $key;?>]"><?php printf( __( 'Domain %s', 'affiliatewp-direct-link-tracking' ), $count ); ?></label>
					<?php echo $this->remove_url( $url_id ); ?>
				</th>

				<td>

				<?php if ( $old_url && ! $is_rejected ) :

					// disable the current URL input field when a new pending URL needs to be accepted/rejected
					$disabled = $is_pending ? 'disabled="disabled"' : '';

					?>
					<div class="current-url">
						<input class="large-text" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="<?php echo $this->field_name; ?>[<?php echo $url_id; ?>]" value="<?php echo $old_url; ?>" <?php echo $disabled;?>/>
						<p class="description"><?php _e( 'Current domain.', 'affiliatewp-direct-link-tracking' ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( $url && 'inactive' === $direct_link->status ) : ?>
					<div class="current-url">
						<input class="large-text" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="<?php echo $this->field_name; ?>[<?php echo $url_id; ?>]" value="<?php echo $url; ?>" />
						<p class="description"><?php _e( 'This domain is currently inactive.', 'affiliatewp-direct-link-tracking' ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( $url && 'active' === $direct_link->status ) : ?>
					<div class="current-url">
						<input class="large-text" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="<?php echo $this->field_name; ?>[<?php echo $url_id; ?>]" value="<?php echo $url; ?>" />
						<p class="description"><?php _e( 'Current domain.', 'affiliatewp-direct-link-tracking' ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( $is_pending ) : ?>
					<div class="pending-url">
						<input class="large-text" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="<?php echo $this->field_name; ?>[<?php echo $url_id; ?>]" value="<?php echo $url; ?>" disabled="disabled" />

						<?php
						$review_text = $old_url ? __( 'New domain to review.', 'affiliatewp-direct-link-tracking' ) : __( 'Domain to review.', 'affiliatewp-direct-link-tracking' );
						?>
						<p class="description"><?php echo $review_text; ?></p>

						<div class="direct-link-actions">
							<p>
								<input type="radio" name="direct_link_action[<?php echo $url_id; ?>]" id="accept[<?php echo $url_id; ?>]" value="accept" />
								<label for="accept[<?php echo $url_id; ?>]"><?php _e( 'Accept', 'affiliatewp-direct-link-tracking' ); ?></label>
							</p>
							<p>
								<input type="radio" name="direct_link_action[<?php echo $url_id; ?>]" id="reject[<?php echo $url_id; ?>]" value="reject" />
								<label for="reject[<?php echo $url_id; ?>]"><?php _e( 'Reject', 'affiliatewp-direct-link-tracking' ); ?></label>
							</p>
						</div>

					</div>
				<?php endif; ?>

				<?php

				/**
				 * After a URL has been rejected the admin will see the old URL
				 * On the front-end the affiliate will be shown a notice
				 */
				if ( $is_rejected ) : ?>

					<div class="rejected-url">
						<input class="regular-text" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="<?php echo $this->field_name; ?>[<?php echo $url_id; ?>]" value="<?php echo $url; ?>" disabled />

						<div class="dlt-notice">
							<p><?php _e( 'This domain was rejected.', 'affiliatewp-direct-link-tracking' ); ?></p>
						</div>

				<?php endif; ?>

				<?php if ( $is_invalid ) : ?>

					<div class="invalid-url">
						<input class="large-text" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="<?php echo $this->field_name; ?>[<?php echo $url_id; ?>]" value="<?php echo $url; ?>" />

						<div class="dlt-notice">
							<p><?php _e( 'The affiliate submitted an invalid domain.', 'affiliatewp-direct-link-tracking' ); ?></p>
						</div>

					</div>
				<?php endif; ?>

				</td>

			</tr>

			<?php endforeach; ?>

		<?php endif; ?>

		<?php echo $this->to_clone(); ?>

		<?php
		/**
		 * Allow the admin to add new URLs for the affiliate
		 */
		?>
		<tr class="form-row direct-link-row">
			<th scope="row"></th>
			<td>
				<p><a href="#" class="affwp-dlt-add-url"><?php _e( 'Add new domain', 'affiliatewp-direct-link-tracking' ); ?></a></p>
			</td>
		</tr>

		<?php
	}

	/**
	 * The table row to clone
	 *
	 * @since 1.0.0
	 */
	public function to_clone() {
		$key = 0;
	?>

		<tr class="form-row direct-link-url direct-link-url-clone">

			<th scope="row">
				<label for="direct-link-tracking-url[<?php echo $key;?>]"><?php _e( 'Domain #', 'affiliatewp-direct-link-tracking' ); ?><span class="count"><?php echo $key; ?></span></label>
				<?php echo $this->remove_url(); ?>
			</th>

			<td>
				<div class="current-url">
					<input class="large-text" id="direct-link-tracking-url[<?php echo $key; ?>]" type="text" name="direct_link_tracking_urls_new[]" value="" disabled />
					<p class="description"><?php _e( 'Enter a direct link domain.', 'affiliatewp-direct-link-tracking' ); ?></p>
				</div>
			</td>

		</tr>
		<?php
	}


	/**
	 * Remove URL link
	 *
	 * @since 1.1
	 */
	public function remove_url( $url_id = null ) {

		$affiliate_id = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '';

		$url_limit = (int) affwp_dlt_get_domain_limit( $affiliate_id );
		$status    = affwp_dlt_get_direct_link_status( $url_id );

		// Pending or inactive URLs cannot be removed by the affiliate.
		if ( 'pending' === $status || 'inactive' === $status || 'rejected' === $status ) {
			return;
		}

		// Hide the "remove" link when there's a URL limit of 1 and the domain is blank.
		// If the domain is active then the remove link is shown.
		if ( (int) affwp_dlt_get_domain_limit( $affiliate_id ) === 1 && 'active' !== $status ) {
			return;
		}

		?>
		<a href="#" class="affwp-dlt-remove-url"><?php _e( 'Remove', 'affiliatewp-direct-link-tracking' ); ?></a>

		<?php
	}

	/**
	 * Admin scripts
	 *
	 * @since 1.0.0
	 */
	public function scripts() {

		if ( ! $this->is_affiliate_edit_screen() ) {
			return;
		}

		$affiliate_id = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '';

		$affiliate_url_limit = affwp_dlt_get_domain_limit( $affiliate_id );
		$global_url_limit = affiliate_wp()->settings->get( 'direct_link_tracking_url_limit', 1 );

		if ( $affiliate_url_limit ) {
			$url_limit = $affiliate_url_limit;
		} else {
			$url_limit = $global_url_limit;
		}

		$url_count_upper_limit = $url_limit - 1;
		?>

		<script>
			jQuery(document).ready(function($) {

				// get a count of the rows, but not including the clone
				var count = $( '.direct-link-url' ).not( '.direct-link-url-clone' ).length;

				// remove add new URL button if limit has been reached
				if ( count === <?php echo $url_limit; ?> ) {
					$('.affwp-dlt-add-url').closest('.form-row').remove();
				}

				// set up our array
				var rowsToHide = [];

				// the current options rows on the settings page
				var currentRows = $( '.direct-link-row' );

				// push rows to array
				rowsToHide.push( currentRows );

				$('.affwp-dlt-add-url').click( function(e) {

					e.preventDefault();

					// get the last row in the set
					var last_url = $( '.direct-link-url:last' );

					// find the clone
					var toClone = $( '.direct-link-url-clone' );

					// get a count of the rows, but not including the clone
					var count = $( '.direct-link-url' ).not( '.direct-link-url-clone' ).length;

					if ( count < <?php echo $url_limit; ?> ) {

						// clone the clone
						clone = toClone.clone( true, true );

						// push any clones to our array
						rowsToHide.push( clone );

						// remove cloning CSS class
						clone.removeClass('direct-link-url-clone');

						// increase count in input fields
						clone.find( 'input' ).each(function() {

							var name = $(this).attr( 'name' );
							var id   = $(this).attr( 'id' );

							name = name.replace( /\[(\d+)\]/, '[' + parseInt( count + 1 ) + ']');
							id   = id.replace( /\[(\d+)\]/, '[' + parseInt( count + 1 ) + ']');

							$(this).attr( 'name', name );
							$(this).attr( 'id', id );

						});

						// increase count in hidden input fields
						clone.find( 'input[type=hidden]' ).each(function() {
							$(this).val( parseInt( count + 1 ) );
						});

						// increase count in label for attribute, plus label text
						clone.find( 'label' ).each(function() {

						   var name = $(this).attr( 'for' );

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
						$(this).closest('.form-row').remove();
					}

				});

				// remove URL link
				$('.affwp-dlt-remove-url').click( function(e) {
					e.preventDefault();
					$(this).closest('.direct-link-url').hide().find( 'input' ).val('');
				});

				var optionDirectLinkTracking = $('input[name="direct_link_tracking_enabled"]');
				var directLinkTrackingRows   = $('.direct-link-row');

				if ( optionDirectLinkTracking.length ) {

					// show or hide the settings when the page loads
					if ( optionDirectLinkTracking.is(':checked') ) {

						$( rowsToHide ).each( function() {
							$(this).show();
						});

					} else {

						$( rowsToHide ).each( function() {
							$(this).hide();
						});

					}

					// show or hide the settings when the checkbox is ticked
					optionDirectLinkTracking.click( function() {

						if ( this.checked ) {

							$( rowsToHide ).each( function() {
								$(this).show();
							});

						} else {

							$( rowsToHide ).each( function() {
								$(this).hide();
							});
						}

					});

				}

			});
		</script>
		<?php
	}

	/**
	 * Update URL from the edit affiliate screen
	 *
	 * @since 1.0.0
	 */
	public function update_affiliate_url( $data ) {

		$affiliate_id = $data['affiliate_id'];

		if ( ! is_admin() ) {
			return false;
		}

		if ( ! current_user_can( 'manage_affiliates' ) ) {
			wp_die( __( 'You do not have permission to manage direct links', 'affiliatewp-direct-link-tracking' ), __( 'Error', 'affiliatewp-direct-link-tracking' ), array( 'response' => 403 ) );
		}

		/**
		 * Per-affiliate direct link tracking
		 */
		if ( isset( $_POST['direct_link_tracking_enabled'] ) ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'direct_link_tracking_enabled', $data['direct_link_tracking_enabled'] );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'direct_link_tracking_enabled' );
		}

		/**
		 * Per-affiliate direct link URL limit
		 */
		if ( ! empty( $_POST['direct_link_tracking_url_limit'] ) ) {
			affwp_update_affiliate_meta( $data['affiliate_id'], 'direct_link_tracking_url_limit', $data['direct_link_tracking_url_limit'] );
		} else {
			affwp_delete_affiliate_meta( $data['affiliate_id'], 'direct_link_tracking_url_limit' );
		}

		/**
		 * Accept or reject each domain
		 */
		$actions = isset( $data['direct_link_action'] ) ? $data['direct_link_action'] : array();

		if ( ! empty( $actions ) ) {
			foreach ( $actions as $url_id => $action ) {

				if ( 'accept' === $action ) {
					affwp_dlt_update_direct_link( $url_id, array( 'status' => 'active', 'url_old' => '' ) );
				} elseif ( 'reject' === $action ) {
					affwp_dlt_set_direct_link_status( $url_id, 'rejected' );
				}

			}
		}

		/**
		 * Update direct links
		 */

 		// Get affiliate's direct links from the database.
 		$direct_links = affwp_dlt_get_direct_links( array( 'affiliate_id' => $affiliate_id ) );

		/**
		 * Build out an array of saved domains
		 *
		 * For example: url_id => domain
		 *
		 * 4  => domain.com
		 * 10 => domain2.com
		 */
		$saved_domains = array();

		if ( $direct_links ) {
			foreach ( $direct_links as $direct_link ) {
				$saved_domains[$direct_link->url_id] = $direct_link->url;
			}
		}

		/**
		 * Update a domain
		 */

		// An array containing the posted URLs from the affiliate
		$posted_domains = isset( $data['direct_link_tracking_urls'] ) ? $data['direct_link_tracking_urls'] : array();

		if ( ! empty( $posted_domains ) ) {

			foreach ( $posted_domains as $url_id => $posted_domain ) {

				/**
				 * Delete an existing domain.
				 */
				if ( empty ( $posted_domain ) ) {

					// Domain must already exist in the current affiliate's array of domains ($saved_domains) before it can be deleted.
					if ( array_key_exists( $url_id, $saved_domains ) ) {
						affwp_dlt_delete_direct_link( $url_id );
					}

				}

				/**
				 * Update an existing domain
				 * The URL ID must exist in the affiliate's saved domains
				 */
				if ( array_key_exists( $url_id, $saved_domains ) ) {

					// The domain doesn't match the domain in the database with the same URL ID so the domain has been changed (and isn't empty).
					if ( $posted_domain !== affwp_dlt_get_url( $url_id ) && ! empty( $posted_domain ) ) {

						// Validate the domain.
						$validated_url = affwp_dlt_validate_url( $posted_domain );

						if ( ! is_array( $validated_url ) ) {

							// Get the current domain from the database.
							$current_domain = affwp_dlt_get_url( $url_id );

							// Set the args.
							$args = array(
								'url'     => $validated_url,
								'status'  => 'active'
							);

							// Update the domain.
							affwp_dlt_update_direct_link( $url_id, $args );

						}

					}

				}

			}

		}

		/**
		 * Submit new domain.
		 * Domains are set to "pending" for first-time submissions.
		 */

		// New domains that will be added.
		$new_domains = isset( $data['direct_link_tracking_urls_new'] ) ? $data['direct_link_tracking_urls_new'] : array();

		// Remove any blank values.
		$new_domains = array_filter( $new_domains );

		if ( ! empty( $new_domains ) ) {

			foreach ( $new_domains as $new_domain ) {

				// Validate the domain.
				$validated_url = affwp_dlt_validate_url( $new_domain );

				// affwp_dlt_validate_url() returns an array on failure, string if the domain is valid.
				if ( ! is_array( $validated_url ) ) {
					affwp_dlt_add_direct_link( array( 'affiliate_id' => $affiliate_id, 'url' => $validated_url, 'status' => 'active' ) );
				}

			}

		}

	}

}

new AffiliateWP_Direct_Link_Tracking_Admin();
