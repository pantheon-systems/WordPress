<?php

class Affiliate_WP_PayPal extends Affiliate_WP_Base {

	/**
	 * Get thigns started
	 *
	 * @access  public
	 * @since   1.9
	 */
	public function init() {

		$this->context = 'paypal';

		add_action( 'wp_footer', array( $this, 'scripts' ) );
		add_action( 'wp_ajax_affwp_maybe_insert_paypal_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'wp_ajax_nopriv_affwp_maybe_insert_paypal_referral', array( $this, 'maybe_insert_referral' ) );
		add_action( 'init', array( $this, 'process_ipn' ) );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

	}

	/**
	 * Add JS to site footer for detecting PayPal form submissions
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function scripts() {
		if ( defined( 'AFFILIATEWP_PAYPAL_IPN' ) && AFFILIATEWP_PAYPAL_IPN ) {
			$ipn_url = AFFILIATEWP_PAYPAL_IPN;
		} else {
			$ipn_url = home_url( 'index.php?affwp-listener=paypal' );
		}
		?>

		<script type="text/javascript">
		jQuery(document).ready(function($) {

			$('form').on('submit', function(e) {

				// Use attr() to grab the action since the attribute is likely set in the DOM regardless.
				var action = $(this).attr( 'action' );

				// Bail if there's no action attribute on the form tag.
				if ( 'undefined' === typeof action ) {
					return;
				}

				paypalMatch = new RegExp( 'paypal\.com\/cgi-bin\/webscr' );

				if ( ! action.match( paypalMatch ) ) {
					return;
				}

				e.preventDefault();

				var $form = $(this);
				var ipn_url = "<?php echo esc_js( $ipn_url ); ?>";

				$.ajax({
					type: "POST",
					data: {
						action: 'affwp_maybe_insert_paypal_referral'
					},
					url: '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>',
					success: function (response) {

						$form.append( '<input type="hidden" name="custom" value="' + response.data.ref + '"/>' );
						$form.append( '<input type="hidden" name="notify_url" value="' + ipn_url + '"/>' );

						$form.get(0).submit();

					}

				}).fail(function (response) {

					if ( window.console && window.console.log ) {
						console.log( response );
					}

				});

			});
		});
		</script>
<?php
	}

	/**
	 * Create a referral during PayPal form submission if customer was referred
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function maybe_insert_referral() {

		$response = array();

		if( $this->was_referred() ) {

			$reference   = affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id . '|' . time();
			$referral_id = $this->insert_pending_referral( 0.01, $reference, __( 'Pending PayPal referral', 'affiliate-wp' ) );

			if( $referral_id ) {

				$this->log( 'Pending referral created successfully during maybe_insert_referral()' );

			} else {

				$this->log( 'Pending referral failed to be created during maybe_insert_referral()' );

			}

			$response['ref'] = affiliate_wp()->tracking->get_visit_id() . '|' . $this->affiliate_id . '|' . $referral_id;

		}

		wp_send_json_success( $response );

	}

	/**
	 * Process PayPal IPN requests in order to mark referrals as Unpaid
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function process_ipn() {

		if( empty( $_GET['affwp-listener'] ) || 'paypal' !== strtolower( $_GET['affwp-listener'] ) ) {
			return;
		}

		$ipn_data = $_POST;

		if( ! is_array( $ipn_data ) ) {
			wp_parse_str( $ipn_data, $ipn_data );
		}

		$verified = $this->verify_ipn( $ipn_data );

		if( ! $verified ) {
			die( 'IPN verification failed' );
		}

		$to_process = array(
			'web_accept',
			'cart',
			'subscr_payment',
			'express_checkout',
			'recurring_payment',
		);

		if( ! empty( $ipn_data['txn_type'] ) && ! in_array( $ipn_data['txn_type'], $to_process ) ) {
			return;
		}

		if( empty( $ipn_data['mc_gross'] ) ) {

			$this->log( 'IPN not processed because mc_gross was empty' );

			return;
		}

		if( empty( $ipn_data['custom'] ) ) {

			$this->log( 'IPN not processed because custom was empty' );

			return;
		}

		$total        = sanitize_text_field( $ipn_data['mc_gross'] );
		$custom       = explode( '|', $ipn_data['custom'] );
		$visit_id     = $custom[0];
		$affiliate_id = $custom[1];
		$referral_id  = $custom[2];
		$visit        = affwp_get_visit( $visit_id );
		$referral     = affwp_get_referral( $referral_id );

		if( empty( $affiliate_id ) ) {

			$this->log( 'IPN not processed because affiliate ID was empty' );

			return;
		}

		if( ! $visit || ! $referral ) {

			if( ! $visit ) {

				$this->log( 'Visit not successfully retrieved during process_ipn()' );

			}

			if( ! $referral ) {

				$this->log( 'Referral not successfully retrieved during process_ipn()' );

			}

			die( 'Missing visit or referral data' );
		}

		$this->log( 'Referral ID (' . $referral->ID . ') successfully retrieved during process_ipn()' );

		$payer_email = ! empty( $ipn_data['payer_email'] ) ? sanitize_text_field( $ipn_data['payer_email'] ) : '';

		if ( ! empty( $payer_email ) ) {

			$customer = affwp_get_customer( $payer_email );

			if ( ! $customer ) {

				$first_name = ! empty( $ipn_data['first_name'] ) ? sanitize_text_field( $ipn_data['first_name'] ) : '';
				$last_name  = ! empty( $ipn_data['last_name'] ) ? sanitize_text_field( $ipn_data['last_name'] ) : '';

				$args = array(
					'email'        => $payer_email,
					'first_name'   => $first_name,
					'last_name'    => $last_name,
					'affiliate_id' => $affiliate_id,
					'ip'           => $visit->ip
				);

				$user = get_user_by( 'email', $payer_email );

				if ( $user ) {
					$args['user_id'] = $user->ID;
				}

				affwp_add_customer( $args );

			} else {

				affwp_add_customer_meta( $customer->customer_id, 'affiliate_id', $affiliate_id, true );

			}

		}

		if( 'completed' === strtolower( $ipn_data['payment_status'] ) ) {

			if( 'pending' !== $referral->status ) {

				$this->log( 'Referral has status other than Pending during process_ipn()' );

				return;
			}

			$visit->set( 'referral_id', $referral->ID, true );

			$reference   = sanitize_text_field( $ipn_data['txn_id'] );
			$description = ! empty( $ipn_data['item_name'] ) ? sanitize_text_field( $ipn_data['item_name'] ) : sanitize_text_field( $ipn_data['payer_email'] );
			$amount      = $this->calculate_referral_amount( $total, $reference, 0, $referral->affiliate_id );

			$referral->set( 'description', $description );
			$referral->set( 'amount', $amount );
			$referral->set( 'reference', $reference );

			$this->log( 'Referral updated in preparation for save(): ' . print_r( $referral->to_array(), true ) );

			if( $referral->save() ) {

				$this->log( 'Referral saved: ' . print_r( $referral->to_array(), true ) );

				$completed = $this->complete_referral( $referral );

				if( $completed ) {

					$this->log( 'Referral completed successfully during process_ipn()' );

					return;

				} else {

					$this->log( 'Referral failed to be completed during process_ipn()' );

				}

				return;

			} else {

				$this->log( 'Referral not updated successfully during process_ipn()' );

				return;

			}

		} elseif ( 'refunded' === strtolower( $ipn_data['payment_status'] ) || 'reversed' === strtolower( $ipn_data['payment_status'] ) ) {

			if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {

				$this->log( 'Referral not rejected because revoke on refund is not enabled' );

				return;
			}

			$referral = affiliate_wp()->referrals->get_by( 'reference', $ipn_data['parent_txn_id'] );

			if ( $referral ) {

				$this->reject_referral( $referral->reference );

			}

		} else {

			$this->log( 'Payment status in IPN data not Complete, Refunded, or Reversed' );

		}

	}

	/**
	 * Verify IPN from PayPal
	 *
	 * @access  public
	 * @since   1.9
	 * @return  bool True|false
	*/
	private function verify_ipn( $post_data ) {

		$verified = false;
		$endpoint = array_key_exists( 'test_ipn', $post_data ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
		$args     = wp_unslash( array_merge( array( 'cmd' => '_notify-validate' ), $post_data ) );

		$this->log( 'Data passed to verify_ipn(): ' . print_r( $post_data, true ) );
		$this->log( 'Data to be sent to IPN verification: ' . print_r( $args, true ) );

		$request  = wp_remote_post( $endpoint, array( 'timeout' => 45, 'sslverify' => false, 'httpversion' => '1.1', 'body' => $args ) );
		$body     = wp_remote_retrieve_body( $request );
		$code     = wp_remote_retrieve_response_code( $request );
		$message  = wp_remote_retrieve_response_message( $request );

		if( ! is_wp_error( $request ) && 200 === (int) $code && 'OK' == $message ) {

			if( 'VERIFIED' == strtoupper( $body ) ) {

				$verified = true;

				$this->log( 'IPN successfully verified' );

			} else {

				$this->log( 'IPN response came back as INVALID' );

			}

		} else {

			$this->log( 'IPN verification request failed' );
			$this->log( 'Request: ' . print_r( $request, true ) );

		}

		return $verified;
	}

	/**
	 * Sets up the reference link in the Referrals table
	 *
	 * @access  public
	 * @since   1.9
	*/
	public function reference_link( $reference = 0, $referral ) {

		if ( empty( $referral->context ) || 'paypal' != $referral->context ) {

			return $reference;

		}

		$url = 'https://www.paypal.com/webscr?cmd=_history-details-from-hub&id=' . $reference ;

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}

}
new Affiliate_WP_PayPal;
