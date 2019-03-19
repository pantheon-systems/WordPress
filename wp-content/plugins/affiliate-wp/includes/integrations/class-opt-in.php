<?php
namespace AFFWP\Integrations;

class Opt_In {

	public $contact = array();
	public $platform = '';
	public $platform_obj;
	public $platforms = array();

	private $platform_registry = array();
	private $errors;

	/**
	 * Get things started
	 *
	 * @since 2.2
	 */
	public function __construct() {

		// Load opt-in platform registry
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/integrations/opt-in-platforms/class-opt-in-platform-registry.php';

		$this->platform_registry = new \AFFWP\Integrations\Opt_In\Platform_Registry;
		$this->platform_registry->init();

		foreach( $this->platform_registry->get_platforms() as $platform_id => $platform ) {
			$this->platforms[ $platform_id ] = $platform['label'];
		}

		$this->platform = $this->platform_registry->get_platform( affiliate_wp()->settings->get( 'opt_in_platform', '' ) );

		// Instantiate the platform object if we have a saved platform in settings
		if( ! empty( $this->platform ) && file_exists( $this->platform['file'] ) ) {

			require_once $this->platform['file'];

			$this->platform_obj = new $this->platform['class'];

		}

		add_action( 'affwp_opt_in', array( $this, 'process_opt_in' ) );

	}

	/**
	 * Opt-in Form
	 *
	 * @since 2.2
	 * @global $affwp_opt_in_redirect
	 * @param string $redirect Redirect page URL
	 * @return string Login form
	*/
	public function form( $redirect = '' ) {
		global $affwp_opt_in_redirect;

		if ( empty( $redirect ) ) {
			$redirect = affiliate_wp()->tracking->get_current_page_url();
		}

		$affwp_opt_in_redirect = $redirect;

		ob_start();

		if( empty( $this->platform ) ) {
			return '<p class="affwp-error">' . __( 'No opt-in platform has been configured. Please configure a platform in settings.', 'affiliate_wp' ) . '</p>';
		}

		affiliate_wp()->templates->get_template_part( 'opt-in' );

		return apply_filters( 'affwp_opt_in_form', ob_get_clean() );
	}

	/**
	 * Process the opt-in form submission
	 *
	 * @since 2.2
	 */
	public function process_opt_in( $data ) {

		if ( ! isset( $_POST['affwp_opt_in_nonce'] ) || ! wp_verify_nonce( $_POST['affwp_opt_in_nonce'], 'affwp-opt-in-nonce' ) ) {
			return;
		}

		/**
		 * Fires immediately prior to processing the opt-in form.
		 */
		do_action( 'affwp_pre_process_opt_in_form', $this );

		if( empty( $this->platform ) ) {
			$this->add_error( 'no_opt_in_platform', __( 'No opt-in platform has been configured. Please configure a platform in settings.', 'affiliate_wp' ) );
		}

		if ( empty( $data['affwp_first_name'] ) ) {
			$this->add_error( 'empty_first_name', __( 'Please enter your first name', 'affiliate-wp' ) );
		}

		if ( empty( $data['affwp_last_name'] ) ) {
			$this->add_error( 'empty_last_name', __( 'Please enter your last name', 'affiliate-wp' ) );
		}

		if ( empty( $data['affwp_email'] ) ) {
			$this->add_error( 'empty_email', __( 'Please enter your email address', 'affiliate-wp' ) );
		}

		if ( ! empty( $data['affwp_email'] ) && ! is_email( $data['affwp_email'] ) ) {
			$this->add_error( 'invalid_email', __( 'Please enter a valid email address', 'affiliate-wp' ) );
		}

		/**
		 * Fires immediately after processing the opt-in form.
		 */
		do_action( 'affwp_process_opt_in_form', $this );

		// only log the user in if there are no errors
		if ( empty( $this->errors ) ) {

			$this->contact = array(
				'email'      => $data['affwp_email'],
				'first_name' => $data['affwp_first_name'],
				'last_name'  => $data['affwp_last_name']
			);

			$result = $this->subscribe_contact();

			if( empty( $this->errors ) ) {

				$referral_id   = 0;
				$referral_args = array();

				if( affiliate_wp()->tracking->was_referred() ) {

					$affiliate_id = affiliate_wp()->tracking->get_affiliate_id();

					$referral_args = array(
						'description'  => $data['affwp_first_name'] . ' ' . $data['affwp_last_name'],
						'amount'       => affiliate_wp()->settings->get( 'opt_in_referral_amount', 0.00 ),
						'affiliate_id' => $affiliate_id,
						'type'         => 'opt-in',
						'visit_id'     => affiliate_wp()->tracking->get_visit_id(),
						'reference'    => $data['affwp_email'],
						'status'       => affiliate_wp()->settings->get( 'opt_in_referral_status', 'pending' ),
						'customer'     => array(
							'first_name'   => $data['affwp_first_name'],
							'last_name'    => $data['affwp_last_name'],
							'email'        => $data['affwp_email'],
							'ip'           => affiliate_wp()->tracking->get_ip(),
							'affiliate_id' => $affiliate_id
						),
					);

					$referral_id = affiliate_wp()->referrals->add( $referral_args );

					if( 'unpaid' == $referral_args['status'] || 'paid' == $referral_args['status'] ) {
						affiliate_wp()->visits->update( affiliate_wp()->tracking->get_visit_id(), array( 'referral_id' => $referral_id ), '', 'visit' );
					}

				}

				do_action( 'affwp_opt_in_success', $this, $referral_id, $referral_args );

				$redirect = empty( $data['affwp_redirect'] ) ? affiliate_wp()->tracking->get_current_page_url() : $data['affwp_redirect'];
				$redirect = add_query_arg( 'affwp-notice', 'opted-in', $redirect );
				$redirect = apply_filters( 'affwp_opt_in_redirect', $redirect );

				if ( $redirect ) {
					wp_redirect( $redirect ); exit;
				}

			}

		}
	}

	/**
	 * Subscribe a contact to the opt-in platform
	 *
	 * @since 2.2
	 */
	public function subscribe_contact() {

		do_action( 'affwp_opt_in_pre_subscribe_contact', $this );

		$this->platform_obj->contact = $this->contact;

		$ret = $this->platform_obj->subscribe_contact( $this->contact );

		$this->errors = $this->platform_obj->errors;

		do_action( 'affwp_opt_in_post_subscribe_contact', $this );

		return $ret;

	}

	/**
	 * Register a submission error
	 *
	 * @since 2.2
	 */
	public function add_error( $error_id, $message = '' ) {
		$this->errors[ $error_id ] = $message;
	}

	/**
	 * Print errors
	 *
	 * @since 2.2
	 */
	public function print_errors() {

		if ( empty( $this->errors ) ) {
			return;
		}

		echo '<div class="affwp-errors">';

		foreach( $this->errors as $error_id => $error ) {

			echo '<p class="affwp-error">' . esc_html( $error ) . '</p>';

		}

		echo '</div>';

	}

}