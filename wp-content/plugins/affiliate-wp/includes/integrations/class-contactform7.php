<?php

/**
 * Contact Form 7 integration class.
 *
 * This integration provides support for Contact Form 7 and one of the PayPal add-ons, https://wordpress.org/plugins/contact-form-7-paypal-add-on/
 *
 * @since 2.0
 */
class Affiliate_WP_Contact_Form_7 extends Affiliate_WP_Base {

	/**
	 * The Help Scout docs url for this integration.
	 *
	 * @since 2.0
	 * @var string Documentation URL.
	 */
	public $doc_url;

	/**
	 * The PayPal transaction success page url
	 * Specific to the `Contact Form 7 - PayPal Add-on` CF7 add-on.
	 *
	 * @since 2.0
	 */
	public $return_url;

	/**
	 * The PayPal transaction cancellation page url.
	 * Specific to the `Contact Form 7 - PayPal Add-on` CF7 add-on.
	 *
	 * @since 2.0
	 */
	public $cancel_url;

	/**
	 * @access  public
	 * @see     Affiliate_WP_Base::init
	 * @since   2.0
	 */
	public function init() {

		$this->doc_url = 'http://docs.affiliatewp.com/article/657-contact-form-7';

		$this->context = 'contactform7';

		// Set the success and cancel url
		$paypal_options   = get_option( 'cf7pp_options' );
		$this->return_url = $paypal_options['return'];
		$this->cancel_url = $paypal_options['cancel'];

		// Misc AffWP CF7 functions
		$this->include_cf7_functions();

		// Register core settings
		add_filter( 'affwp_settings_tabs', array( $this, 'register_settings_tab' ) );
		add_filter( 'affwp_settings',      array( $this, 'register_settings'     ) );

		// Add PayPal meta to the contact form submision object.
		add_action( 'wpcf7_submit', array( $this, 'add_paypal_meta' ), 1, 2 );

		add_action( 'wpcf7_mail_sent', array( $this, 'maybe_unhook_cf7pp' ), -999 );

		// Mark referral complete.
		add_action( 'wp_footer', array( $this, 'mark_referral_complete' ), 9999 );

		// Revoke referral.
		add_action( 'wp_footer', array( $this, 'revoke' ), 9999 );

		// Set reference.
		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
	}

	/**
	 * Include Contact Form 7 functions
	 * @access  public
	 * @since   2.0
	 */
	public function include_cf7_functions() {
		require_once ( AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/extras/contactform7-functions.php' );
	}

	/**
	 * Unhooks the `cf7pp_after_send_mail` function only if a referring affiliate is found.
	 *
	 * If referred, processes the PayPal redirect after generating the initial referral.
	 *
	 * @since  2.0.3
	 *
	 * @return void
	 */
	public function maybe_unhook_cf7pp() {

		if ( $this->was_referred() ) {
			remove_action( 'wpcf7_mail_sent', 'cf7pp_after_send_mail' );
			add_action( 'wpcf7_submit', array( $this, 'add_pending_referral' ), 10, 2 );
			add_action( 'affwp_cf7_submit', 'affwp_cf7_paypal_redirect', 10, 3 );
		}
	}

	/**
	 * Register the Contact Form 7 integration settings tab
	 *
	 * @access public
	 * @since  2.0
	 * @return array The new tab name
	 */
	public function register_settings_tab( $tabs = array() ) {

		$tabs['contactform7'] = __( 'Contact Form 7', 'affiliate-wp' );

		return $tabs;
	}

	/**
	 * Adds AffiliateWP integration settings
	 *
	 * @access public
	 * @since  2.0
	 * @param  array $settings The existing settings
	 * @return array $settings The updated settings
	 */
	public function register_settings( $settings = array() ) {

		$doc_url = $this->doc_url;

		$settings[ 'contactform7' ] = array(
			'affwp_cf7_enable_all_forms' => array(
				'name' => __( 'Enable referrals on all Contact Form 7 forms', 'affiliate-wp' ),
				'desc' => sprintf( __( 'Check this box to enable referrals on all Contact Form 7 forms.<ul><li>%3$s Once enabled, referrals will be generated for all valid Contact Form 7 forms.</li><li>%2$s <a href="%1$s" target="_blank">Documentation for this integration</a></li></ul>', 'affiliate-wp' ),
					/**
					 * The Contact Form 7 Help Scout docs url displayed within plugin settings.
					 *
					 * @param  $doc_url Help Scout docs url to provide within plugin settings.
					 *
					 * @since  1.0
					 */
					esc_url( apply_filters( 'afwp_cf7_admin_docs_url', $doc_url ) ),
					'<span class="dashicons dashicons-external"></span>',
					'<span class="dashicons dashicons-info"></span>'
				),
				'type' => 'checkbox'
			),
			'affwp_cf7_enable_specific_forms' => array(
				'name' => '<strong>' . __( 'Enable referrals for specific Contact Form 7 forms', 'affiliate-wp' ) . '</strong>',
				'type' => 'multicheck',
				'options' => $this->all_forms_multicheck_render()
			),
		);

		$types = array();
		foreach( affiliate_wp()->referrals->types_registry->get_types() as $type_id => $type ) {
			$types[ $type_id ] =  $type['label'];
		}

		$forms = $this->get_all_forms();
		if( $forms ) {

			foreach( $forms as $form_id => $title ) {

				$settings[ 'contactform7' ][ 'cf7_referral_type_' . $form_id ] = array(
					'name'     => sprintf( __( 'Referral type for %s (Form ID: %d)', 'affiliate-wp' ), $title, $form_id ),
					'type'     => 'select',
					'options'  => $types,
					'selected' => affiliate_wp()->settings->get( 'cf7_referral_type_' . $form_id ) 
				);

			}
			
		}


		return $settings;
	}

	/**
	 * Get forms which have AffiliateWP enabled.
	 * Directly checks the `wpcf7_contact_form` post type.
	 *
	 * @since  2.0
	 *
	 * @return array $enabled_forms All enabled CF7 forms
	 */
	public function get_all_forms() {

		$all_forms = array();

		$args = array(
			'post_type'   => array( 'wpcf7_contact_form' ),
			'post_status' => array( 'publish' )
		);

		$forms = get_posts( $args );

		// The Loop
		if ( $forms ) {
			foreach( $forms as $form ) {

				$all_forms[ $form->ID ] = get_the_title( $form->ID );
			}
		}

		return $all_forms;
	}

	/**
	 * Outputs all Contact Form 7 forms presently created in the site.
	 *
	 * @since  2.0
	 *
	 * @return $cf7_forms  Array of Contact Form 7 form titles and IDs.
	 */
	public function all_forms_multicheck_render() {

		$cf7_forms = array();

		$all_forms = $this->get_all_forms();

		foreach ( $all_forms as $id => $title ) {
			$cf7_forms[ $id ] = '<strong>' . $title . '</strong> <em>(' . __( 'Form ID: ', 'affiliate-wp' ) . $id . ' )</em>';
		}

		return $cf7_forms;
	}

	/**
	 * Gets forms which have AffiliateWP enabled.
	 *
	 * @since  2.0
	 *
	 * @return $enabled  The enabled forms
	 */
	public function get_enabled_forms() {
		$enabled = array();
		$enabled = affiliate_wp()->settings->get( 'affwp_cf7_enable_specific_forms' );

		if ( empty( $enabled ) ) {
			$enabled = array();
		}

		/**
		 * The Contact Form 7 forms for which AffiliateWP is enabled.
		 *
		 * @param array $enabled An array of integers, each being the ID of a Contact Form 7 form for which AffiliateWP is enabled.
		 */
		return apply_filters( 'affwp_cf7_enabled_forms', $enabled );
	}

	/**
	 * Checks if a form has referrals enabled.
	 *
	 * @since  2.0
	 *
	 * @return $enabled bool True if the form has referrals enabled.
	 */
	public function form_enabled( $form_id 	) {
		$enabled = array_key_exists( $form_id, $this->get_enabled_forms() ) ? true : false;

		return (bool) $enabled;
	}

	/**
	 * Adds PayPal add-on meta to the form object.
	 *
	 * @param stdClass  $contactform The contact form data.
	 * @return stdClass $contactform The modified contact form data.
	 * @since 2.0
	 *
	 */
	public function add_paypal_meta( $contactform ) {

		$form_id = $contactform->id();

		update_post_meta( $form_id, 'affwp_cf7_form_id', $form_id );

		$enabled     = get_post_meta( $form_id, '_cf7pp_enable', true );
		$email       = get_post_meta( $form_id, '_cf7pp_email',  true );
		$amount      = get_post_meta( $form_id, '_cf7pp_price',  true );
		$description = get_post_meta( $form_id, '_cf7pp_name',   true );
		$sku         = get_post_meta( $form_id, '_cf7pp_id',     true );

		// Temporarily cast object and add referral data.
		$contactform = (object) array_merge(
			(array) $contactform, array(
				'affwp_paypal_enabled'       => $enabled,
				'affwp_customer_email'       => $email,
				'affwp_base_amount'          => $amount,
				'affwp_referral_description' => $description,
				'affwp_product_sku'          => $sku
			)
		);

		return $contactform;
	}

	/**
	 * Provides CF7 form meta via ajax.
	 *
	 * @since  2.0
	 *
	 * @return void
	 * @see    affwp_cf7_ajax
	 */
	public function ajax_get_paypal_meta(){

		if ( isset( $_REQUEST ) ) {
			$form_id = absint( $_REQUEST['form_id'] );
		}

		$enabled     = get_post_meta( $form_id, '_cf7pp_enable', true );
		$amount      = get_post_meta( $form_id, '_cf7pp_price',  true );
		$description = get_post_meta( $form_id, '_cf7pp_name',   true );
		$sku         = get_post_meta( $form_id, '_cf7pp_id',     true );

		$response = array(
			'success'     => true,
			'form_id'     => $form_id,
			'enabled'     => $enabled,
			'amount'      => $amount,
			'description' => $description,
			'sku'         => $sku
		);

		echo json_encode( $response );

		die();
	}

	/**
	 * Returns PayPal form submission meta as arguments, allowing for transactions to be trackable by AffiliateWP.
	 *
	 * @since  2.0
	 *
	 * @param  object             $contactform  CF7 form object.
	 * @param  object             $result       Modified CF7 form object.
	 *
	 * @return mixed|bool|string  $url_args     A string containing query parameters, used in paypal redirects. Returns false if parameters could not be determined.
	 */
	public function get_url_args( $cf7 ) {

		$form_id = absint( $cf7->id() );

		if ( ! $form_id ) {
			return false;
		}

		if( ! $this->form_enabled( $form_id ) && ! affiliate_wp()->settings->get( 'affwp_cf7_enable_all_forms', false ) ) {
			return false;
		}

		if ( ! get_post_meta( $form_id, '_cf7pp_enable', true ) ) {
			return false;
		}

		$amount      = get_post_meta( $form_id, '_cf7pp_price',  true );
		$description = get_post_meta( $form_id, '_cf7pp_name',   true );
		$sku         = get_post_meta( $form_id, '_cf7pp_id',     true );

		// Add meta to the return and cancel urls.
		$args = '?form_id=' . $form_id . '&sub_time=' . date_i18n( 'U' ) . '&amount=' . $amount . '&description=' . $description . '&sku=' . $sku;

		$url_args = esc_url( $args );

		return $url_args;

	}

	/**
	 * Utility function which returns the current page ID.
	 *
	 * @since  2.0
	 *
	 * @return mixed int|bool  $current_page_id The current page ID, or booean false if unable to locate the current page ID.
	 */
	public function get_current_page_id() {

		global $post;

		if ( ! $post ) {
			return false;
		}

		$current_page_id = $post->ID;

		return $current_page_id ? $current_page_id : false;
	}

	/**
	 * Adds a referral when a form is submitted.
	 *
	 * @since 2.0
	 *
	 * @param object $contactform   CF7 form submission object.
	 * @param object $result        Submitted CF7 form submission data.
	 */
	public function add_pending_referral( $contactform, $result ) {

		$current_page_id = $this->get_current_page_id();

		$form_id = absint( $contactform->id() );

		if ( ! $form_id ) {
			return false;
		}

		if( ! $this->form_enabled( $form_id ) && ! affiliate_wp()->settings->get( 'affwp_cf7_enable_all_forms', false ) ) {

			// Let the PayPal plugin take over
			if( function_exists( 'cf7pp_after_send_mail' ) ) {

				cf7pp_after_send_mail( $contactform );

			}

			return false;
		}

		if ( $this->was_referred() ) {

			$paypal = get_post_meta( $form_id, '_cf7pp_enable', true );

			if( $paypal ) {

				$product_id  = get_post_meta( $form_id, '_cf7pp_id',     true );
				$description = get_post_meta( $form_id, '_cf7pp_name',   true );
				$base_amount = floatval( get_post_meta( $form_id, '_cf7pp_price',  true ) );

			} else {

				$product_id  = 0;
				$description = get_the_title( $form_id );
				$base_amount = 0;

			}

			$this->referral_type = affiliate_wp()->settings->get( 'cf7_referral_type_' . $form_id );

			/**
			 * Filters the referral description for the AffiliateWP Contact Form 7 integration.
			 *
			 * @since  2.1.12
			 *
			 * @param string $description   Item description or CF7 form title
			 * @param string $form_id       CF7 form id
			 * @param object $contactform   CF7 form submission object.
			 * @param object $result        Submitted CF7 form submission data.
			 *
			 */
			$description = apply_filters( 'affwp_cf7_referral_description', $description, $form_id, $contactform, $result );

			$reference       = $form_id . '-' . date_i18n( 'U' );
			$affiliate_id    = $this->get_affiliate_id( $reference );
			$referral_total  = $this->calculate_referral_amount( $base_amount, $reference, $product_id, $affiliate_id );
			$referral_id     = $this->insert_pending_referral( $referral_total, $reference, $description, $product_id );

			if ( empty( $referral_total ) ) {
				$this->complete_referral( $reference );
			}

			// Bail if PayPal add-on is not enabled.
			if ( ! $paypal ) {
				return false;
			}

			/**
			 * Provides the referral ID to PayPal transaction processes.
			 * This action is specific to the AffiliateWP Contact Form 7 integration.
			 *
			 * @param object $contactform The form object.
			 * @param object $result      The modified form object.
			 * @param int    $referral_id The referral ID.
			 *
			 * @since 2.0
			 */
			do_action( 'affwp_cf7_submit', $contactform, $result, $referral_id );
		}


	}

	/**
	 * Updates the referral status when a PayPal refund or transaction completion occurs,
	 * via the success or cancel pages provided in the PayPal add-ons.
	 *
	 * @param int    $current_page_id  The current page ID.
	 * @param mixed  $reference        The referral reference.
	 * @since 2.0
	 */
	public function mark_referral_complete( $current_page_id = 0, $reference = '' ) {

		$current_page_id = $this->get_current_page_id();
		$form_id         = ! empty( $_GET['form_id'] )     ? absint( $_GET['form_id'] )         : false;
		$referral_id     = ! empty( $_GET['referral_id'] ) ? absint( $_GET['referral_id'] )     : false;
		$txn_id          = ! empty( $_GET['tx'] )          ? sanitize_text_field( $_GET['tx'] ) : false;

		$paypal          = get_post_meta( $form_id, '_cf7pp_enable', true );

		// Bail if PayPal add-on is not enabled.
		if ( ! $paypal ) {
			return false;
		}

		if ( ! $form_id || ! $referral_id ) {
			$this->log( 'CF7 integration: The form ID or referral ID could not be determined.' );

			return false;
		}

		$return_url     = $this->return_url;
		$return_page_id = url_to_postid( $return_url );

		// Bail if not on the return page.
		if ( (int) $return_page_id !== (int) $current_page_id ) {
			$this->log( 'CF7 integration: The specified success page ID does not match the current page ID.' );

			return false;
		}

		$referral = affwp_get_referral( $referral_id );

		if( $referral ) {

			if( ! empty( $txn_id ) ) {
				$referral->set( 'reference', $txn_id, true );
			}

			$this->complete_referral( $referral );


		} else {

			$this->log( sprintf( 'CF7 integration: Referral could not be retrieved during mark_referral_complete(). ID given: %d.' ), $referral_id );

		}

	}

	/**
	 * Updates the status of the referral. Fires when the cancel page url is loaded from a PayPal transaction.
	 *
	 * @param  string $reference        The reference.
	 * @param  int    $current_page_id  The current page ID.
	 * @return mixed void|bool          Returns nothing if successful, returns boolean false if the
	 * @since 2.0                       current page ID does not match the PayPal cancel page ID.
	 *
	 */
	public function revoke( $current_page_id = 0, $reference = '' ) {

		$current_page_id = $this->get_current_page_id();
		$form_id         = ! empty( $_GET['form_id'] )     ? absint( $_GET['form_id'] )     : false;
		$referral_id     = ! empty( $_GET['referral_id'] ) ? absint( $_GET['referral_id'] ) : false;

		$paypal          = get_post_meta( $form_id, '_cf7pp_enable', true );

		// Bail if PayPal add-on is not enabled.
		if ( ! $paypal ) {
			return false;
		}

		if ( ! $form_id || ! $referral_id ) {
			$this->log( 'CF7 integration: The form ID or referral ID could not be determined.' );

			return false;
		}

		$cancel_url     = $this->cancel_url;
		$cancel_page_id = url_to_postid( $cancel_url );

		// Bail if not on the cancel page
		if ( (int) $cancel_page_id !== (int) $current_page_id ) {
			$this->log( 'CF7 integration: The specified cancel page ID does not match the current page ID.' );

			return false;

		}

		$referral = affwp_get_referral( $referral_id );

		if( $referral ) {

			$this->reject_referral( $referral );

		} else {

			$this->log( sprintf( 'CF7 integration: Referral could not be retrieved during revoke(). ID given: %d.' ), $referral_id );

		}

	}

	/**
	 * Generates a link to the associated Contact Form 7 form in the referral reference column.
	 *
	 * @param  int    $reference
	 * @param  object $referral
	 * @return string
	 * @since  2.0
	 *
	 */
	public function reference_link( $reference, $referral ) {

		if ( ! $referral ) {

			$this->log( 'CF7 integration: No referral data found when attempting to add a referral reference.' );

			return false;
		}

		// To provide a working link to the CF7 form,
		// the Unix date (stored as `sub_time` in submission args) is stripped from the reference string.

		if( false !== strpos( $reference, '-' ) ) {

			$form_id = strstr( $reference, '-', true );

		} else {

			return $reference; // Return transaction ID without a link

		}

		$url = admin_url( 'admin.php?page=wpcf7&action=edit&post=' . $form_id );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

	}

}

if ( class_exists( 'WPCF7_ContactForm' ) ) {
	new Affiliate_WP_Contact_Form_7;
}
