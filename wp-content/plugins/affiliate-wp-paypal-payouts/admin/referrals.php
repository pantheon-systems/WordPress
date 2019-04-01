<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffiliateWP_PayPal_Payouts_Referrals_Admin {

	private $api;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {

		$mode = affiliate_wp()->settings->get( 'paypal_payout_mode', 'masspay' );

		switch( $mode ) {

			case 'masspay' :

				$this->api = new AffiliateWP_PayPal_MassPay;

				break;

			case 'api' :
			default :

				$this->api = new AffiliateWP_PayPal_API;

				break;

		}

		$this->api->credentials = affiliate_wp_paypal()->get_api_credentials();

		add_filter( 'affwp_referral_action_links', array( $this, 'action_links' ), 10, 2 );
		add_filter( 'affwp_referrals_bulk_actions', array( $this, 'bulk_actions' ), 10, 2 );

		add_action( 'affwp_referrals_page_buttons', array( $this, 'bulk_pay_form' ) );
		add_action( 'affwp_pay_now', array( $this, 'process_pay_now' ) );
		add_action( 'affwp_referrals_do_bulk_action_pay_now', array( $this, 'process_bulk_action_pay_now' ) );
		add_action( 'affwp_process_bulk_paypal_payout', array( $this, 'process_bulk_paypal_payout' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

	}

	/**
	 * Add new action links to the referral actions column
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function action_links( $links, $referral ) {

		if( affiliate_wp_paypal()->has_api_credentials() ) {

			$recipient_email = affwp_get_affiliate_payment_email( $referral->affiliate_id );

			if( 'unpaid' == $referral->status && current_user_can( 'manage_referrals' ) && $recipient_email ) {
				$links[] = '<a href="' . esc_url( add_query_arg( array( 'affwp_action' => 'pay_now', 'referral_id' => $referral->referral_id, 'affiliate_id' => $referral->affiliate_id ) ) ) . '">' . __( 'Pay Now', 'affwp-paypal-payouts' ) . '</a>';
			}

		}

		return $links;
	}

	/**
	 * Register a new bulk action
	 *
	 * @access public
	 * @since 1.0
	 * @return array
	 */
	public function bulk_actions( $actions ) {

		if( affiliate_wp_paypal()->has_api_credentials() ) {

			$actions['pay_now'] = __( 'Pay Now', 'affwp-paypal-payouts' );

		}

		return $actions;
	}

	/**
	 * Render the Bulk Pay section
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function bulk_pay_form() {

		if( ! affiliate_wp_paypal()->has_api_credentials() ) {
			return;
		}
?>
		<script>
		jQuery(document).ready(function($) {
			// Show referral export form
			$('.affwp-referrals-paypal-payout-toggle').click(function() {
				$('.affwp-referrals-paypal-payout-toggle').toggle();
				$('#affwp-referrals-paypal-payout-form').slideToggle();
			});
			$('#affwp-referrals-paypal-payout-form').submit(function() {
				if( ! confirm( "<?php _e( 'Are you sure you want to payout referrals for the specified time frame via Paypal?', 'affwp-paypal-payouts' ); ?>" ) ) {
					return false;
				}
			});
		});
		</script>
		<button class="button-primary affwp-referrals-paypal-payout-toggle"><?php _e( 'Bulk Pay via Paypal', 'affwp-paypal-payouts' ); ?></button>
		<button class="button-primary affwp-referrals-paypal-payout-toggle" style="display:none"><?php _e( 'Close', 'affwp-paypal-payouts' ); ?></button>
		<form id="affwp-referrals-paypal-payout-form" class="affwp-gray-form" style="display:none;" action="<?php echo admin_url( 'admin.php?page=affiliate-wp-referrals' ); ?>" method="post">
			<p>
				<input type="text" class="affwp-datepicker" autocomplete="off" name="from" placeholder="<?php _e( 'From - mm/dd/yyyy', 'affwp-paypal-payouts' ); ?>"/>
				<input type="text" class="affwp-datepicker" autocomplete="off" name="to" placeholder="<?php _e( 'To - mm/dd/yyyy', 'affwp-paypal-payouts' ); ?>"/>
				<input type="text" class="affwp-text" name="minimum" placeholder="<?php esc_attr_e( 'Minimum amount', 'affwp-paypal-payouts' ); ?>"/>
				<input type="hidden" name="affwp_action" value="process_bulk_paypal_payout"/>
				<input type="submit" value="<?php _e( 'Process Payout via Paypal', 'affwp-paypal-payouts' ); ?>" class="button-secondary"/>
				<p><?php printf( __( 'This will send payments via Paypal for all unpaid referrals in the specified timeframe.', 'affwp-paypal-payouts' ), admin_url( 'admin.php?page=affiliate-wp-tools&tab=export_import' ) ); ?></p>
			</p>
		</form>
<?php
	}

	/**
	 * Process a single referral payment
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_pay_now( $data ) {

		$referral_id  = absint( $data['referral_id'] );

		if( empty( $referral_id ) ) {
			return;
		}

		if( ! current_user_can( 'manage_referrals' ) ) {
			wp_die( __( 'You do not have permission to process payments', 'affwp-paypal-payouts' ) );
		}

		if( ! affiliate_wp_paypal()->has_api_credentials() ) {
			wp_die( __( 'Please enter your API credentials in Affiliates > Settings > PayPal Payouts before attempting to process payments', 'affwp-paypal-payouts' ) );
		}

		$transfer = $this->pay_referral( $referral_id );

		if( is_wp_error( $transfer ) ) {

			wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-referrals&affwp_notice=paypal_error&message=' . urlencode( $transfer->get_error_message() ) . '&code=' . urlencode( $transfer->get_error_code() ) ) ); exit;

		}

		wp_safe_redirect( admin_url( 'admin.php?page=affiliate-wp-referrals&affwp_notice=paypal_success&referral=' . $referral_id ) ); exit;

	}

	/**
	 * Process a referral payment for a bulk payout
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_bulk_action_pay_now( $referral_id ) {

		if( empty( $referral_id ) ) {
			return;
		}

		if( ! current_user_can( 'manage_referrals' ) ) {
			return;
		}

		if( ! affiliate_wp_paypal()->has_api_credentials() ) {
			wp_die( __( 'Please enter your API credentials in Affiliates > Settings > PayPal Payouts before attempting to process payments', 'affwp-paypal-payouts' ) );
		}

		$transfer = $this->pay_referral( $referral_id );

	}

	/**
	 * Payouts referrals in bulk for a specified timeframe
	 *
	 * All referrals are summed and then paid as a single transfer for each affiliate
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function process_bulk_paypal_payout() {

		if( ! current_user_can( 'manage_referrals' ) ) {
			wp_die( __( 'You do not have permission to process payments', 'affwp-paypal-payouts' ) );
		}

		if( ! affiliate_wp_paypal()->has_api_credentials() ) {
			wp_die( __( 'Please enter your API credentials in Affiliates > Settings > PayPal Payouts before attempting to process payments', 'affwp-paypal-payouts' ) );
		}

		$start = ! empty( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : false;
		$end   = ! empty( $_POST['to'] )   ? sanitize_text_field( $_POST['to'] )   : false;

		$args = array(
			'status' => 'unpaid',
			'date'   => array(
				'start' => $start,
				'end'   => $end
			),
			'number' => -1
		);

		//print_r( $args ); exit;

		// Final  affiliate / referral data to be paid out
		$data         = array();

		// The affiliates that have earnings to be paid
		$affiliates   = array();

		// Retrieve the referrals from the database
		$referrals    = affiliate_wp()->referrals->get_referrals( $args );

		// The minimum payout amount
		$minimum      = ! empty( $_POST['minimum'] ) ? sanitize_text_field( affwp_sanitize_amount( $_POST['minimum'] ) ) : 0;

		if( $referrals ) {

			foreach( $referrals as $referral ) {

				if( in_array( $referral->affiliate_id, $affiliates ) ) {

					// Add the amount to an affiliate that already has a referral in the export

					$amount = $data[ $referral->affiliate_id ]['amount'] + $referral->amount;

					$data[ $referral->affiliate_id ]['amount']      = $amount;
					$data[ $referral->affiliate_id ]['referrals'][] = $referral->referral_id;

				} else {

					$email = affwp_get_affiliate_payment_email( $referral->affiliate_id );

					$data[ $referral->affiliate_id ] = array(
						'email'     => $email,
						'amount'    => $referral->amount,
						'currency'  => ! empty( $referral->currency ) ? $referral->currency : affwp_get_currency(),
						'referrals' => array( $referral->referral_id ),
					);

					$affiliates[] = $referral->affiliate_id;

				}

			}


			$payouts = array();

			$i = 0;
			foreach( $data as $affiliate_id => $payout ) {

				if ( $minimum > 0 && $payout['amount'] < $minimum ) {

					// Ensure the minimum amount was reached

					unset( $data[ $affiliate_id ] );

					// Skip to the next affiliate
					continue;

				}

				$payouts[ $affiliate_id ] = array(
					'email'       => $payout['email'],
					'amount'      => $payout['amount'],
					'description' => sprintf( __( 'Payment for referrals between %s and %s from %s', 'affwp-paypal-payouts' ), $start, $end, home_url() ),
					'referrals'   => $payout['referrals'],
				);
				$i++;
			}

			$redirect = admin_url( 'admin.php?page=affiliate-wp-referrals&affwp_notice=paypal_bulk_pay_success' );
			$success  = $this->api->send_bulk_payment( $payouts );

			if( is_wp_error( $success ) ) {

				$redirect .= '&affwp_notice=paypal_error&message=' . $success->get_error_message() . '&code=' . $success->get_error_code();

			} else {

				// We now know which referrals should be marked as paid
				foreach ( $payouts as $affiliate_id => $payout ) {
					if ( function_exists( 'affwp_add_payout' ) ) {
						affwp_add_payout( array(
							'affiliate_id'  => $affiliate_id,
							'referrals'     => $payout['referrals'],
							'amount'        => $payout['amount'],
							'payout_method' => 'PayPal'
						) );
					} else {
						foreach ( $payout['referrals'] as $referral ) {
							affwp_set_referral_status( $referral, 'paid' );
						}
					}
				}

			}

			// A header is used here instead of wp_redirect() due to the esc_url() bug that removes [] from URLs
			header( 'Location:' . $redirect ); exit;

		}

	}

	/**
	 * Pay a referral
	 *
	 * @access public
	 * @since 1.0
	 * @return string
	 */
	private function pay_referral( $referral_id = 0 ) {

		if( empty( $referral_id ) ) {
			return false;
		}

		$referral = affwp_get_referral( $referral_id );

		if( ! affiliate_wp_paypal()->has_api_credentials() ) {
			return new WP_Error( 'missing_api_keys', __( 'Please enter your API credentials in Affiliates > Settings > PayPal Payouts before attempting to process payments', 'affwp-paypal-payouts' ) );
		}

		if( empty( $referral ) ) {
			return new WP_Error( 'invalid_referral', __( 'The specified referral does not exist', 'affwp-paypal-payouts' ) );
		}

		if( empty( $referral->affiliate_id ) ) {
			return new WP_Error( 'no_affiliate', __( 'There is no affiliate connected to this referral', 'affwp-paypal-payouts' ) );
		}

		if( 'unpaid' != $referral->status ) {
			return new WP_Error( 'referral_not_unpaid', __( 'A payment cannot be processed for this referral since it is not marked as Unpaid', 'affwp-paypal-payouts' ) );
		}

		$email = affwp_get_affiliate_payment_email( $referral->affiliate_id );

		if( empty( $email ) ) {
			return new WP_Error( 'no_email', __( 'This affiliate account does not have a Paypal email attached', 'affwp-paypal-payouts' ) );
		}

		$transfer    = false;
		$api_keys    = affiliate_wp_paypal()->get_api_credentials();
		$description = sprintf( __( 'Payment for referral #%d, %s', 'affwp-paypal-payouts' ), $referral_id, $referral->description );

		return $this->api->send_payment( array( 'email' => $email, 'amount' => $referral->amount, 'description' => $description, 'referral_id' => $referral_id ) );

	}

	/**
	 * Admin notices for success and error messages
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_notices() {

		if( empty( $_REQUEST['affwp_notice' ] ) ) {
			return;
		}

		$affiliates  = ! empty( $_REQUEST['affiliate'] ) ? $_REQUEST['affiliate']                        : 0;
		$referral_id = ! empty( $_REQUEST['referral'] )  ? absint( $_REQUEST['referral'] )               : 0;
		$transfer_id = ! empty( $_REQUEST['transfer'] )  ? sanitize_text_field( $_REQUEST['transfer'] )  : '';
		$message     = ! empty( $_REQUEST['message'] )   ? urldecode( $_REQUEST['message'] )             : '';
		$code        = ! empty( $_REQUEST['code'] )      ? urldecode( $_REQUEST['code'] ) . ' '          : '';

		switch( $_REQUEST['affwp_notice'] ) {

			case 'paypal_success' :

				echo '<div class="updated"><p>' . sprintf( __( 'Referral #%d paid out via Paypal successfully', 'affwp-paypal-payouts' ), $referral_id, $transfer_id, $transfer_id ) . '</p></div>';
				break;

			case 'paypal_bulk_pay_success' :

				echo '<div class="updated"><p>' . __( 'Referrals paid out via Paypal successfully', 'affwp-paypal-payouts' ) . '</p></div>';
				break;

			case 'paypal_error' :

				echo '<div class="error"><p><strong>' . __( 'Error:', 'affwp-paypal-payouts' ) . '</strong>&nbsp;' . $code . esc_html( $message ) . '</p></div>';
				break;

		}

	}

}
new AffiliateWP_PayPal_Payouts_Referrals_Admin;
