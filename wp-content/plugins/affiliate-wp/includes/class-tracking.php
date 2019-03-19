<?php
/**
 * The Affiliate_WP_Tracking class.
 *
 * This class defines all primary methods by which
 * AffiliateWP tracks referral activity.
 *
 * @since  1.0
 * @uses   Affiliate_WP_Logging  Logs activity.
 */
class Affiliate_WP_Tracking {

	private $referral_var;

	private $expiration_time;

	public $referral;

	private $debug;

	/**
	 * Logger instance.
	 *
	 * @access protected
	 * @deprecated 2.0.2
	 *
	 * @var Affiliate_WP_Logging
	 */
	protected $logs;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->set_expiration_time();
		$this->set_referral_var();

		/*
		 * Referrals are tracked via javascript by default
		 * This fails on sites that have jQuery errors, so a fallback method is available
		 * With the fallback, the template_redirect action is used
		 */

		if( ! $this->use_fallback_method() ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
			add_action( 'wp_ajax_nopriv_affwp_track_visit', array( $this, 'track_visit' ) );
			add_action( 'wp_ajax_affwp_track_visit', array( $this, 'track_visit' ) );
			add_action( 'wp_ajax_affwp_get_affiliate_id', array( $this, 'ajax_get_affiliate_id_from_login' ) );
			add_action( 'wp_ajax_nopriv_affwp_get_affiliate_id', array( $this, 'ajax_get_affiliate_id_from_login' ) );

		} else {

			add_action( 'template_redirect', array( $this, 'fallback_track_visit' ), -9999 );

		}

		add_action( 'wp_head', array( $this, 'header_scripts' ) );
		add_action( 'wp_ajax_nopriv_affwp_track_conversion', array( $this, 'track_conversion' ) );
		add_action( 'wp_ajax_affwp_track_conversion', array( $this, 'track_conversion' ) );
		add_action( 'wp_ajax_affwp_check_js', array( $this, 'check_js' ) );
		add_action( 'wp_ajax_nopriv_affwp_check_js', array( $this, 'check_js' ) );

		add_filter( 'paginate_links', array( $this, 'strip_referral_from_paged_urls' ), 100 );
	}

	/**
	 * Attempts to enqueue header scripts alongside jQuery.
	 *
	 * If the 'jquery' handle is not set for enqueue as of {@see 'wp_head'} at priority 10,
	 * then set header scripts to print via {@see 'wp_footer'}.
	 *
	 * @access public
	 * @since  1.0
	 * @since  2.0.10 Converted to a wrapper for a new protected print_header_script() helper.
	 */
	public function header_scripts() {
		// Back-compat for direct calls.
		if ( 'wp_head' !== current_action() ) {
			$this->print_header_script();

			return;
		}

		if ( wp_script_is( 'jquery', 'enqueued' )
		     || wp_script_is( 'jquery', 'to_do' )
		     || wp_script_is( 'jquery', 'done' )
		) {
			$this->print_header_script();
		} else {
			add_action( 'wp_footer', array( $this, 'header_scripts' ) );
		}
	}

	/**
	 * Outputs header scripts.
	 *
	 * @access protected
	 * @since  2.0.10
	 *
	 * @see header_scripts()
	 */
	protected function print_header_script() {
		$referral_credit_last = affiliate_wp()->settings->get( 'referral_credit_last', 0 );
?>
		<script type="text/javascript">
		var AFFWP = AFFWP || {};
		AFFWP.referral_var = '<?php echo $this->get_referral_var(); ?>';
		AFFWP.expiration = <?php echo $this->get_expiration_time(); ?>;
		AFFWP.debug = <?php echo absint( $this->debug ); ?>;

<?php if ( $cookie_domain = $this->get_cookie_domain() ) : ?>
		AFFWP.cookie_domain = '<?php echo esc_js( $cookie_domain ); ?>';
<?php endif; ?>

<?php if( 1 !== (int) get_option( 'affwp_js_works' ) )  : ?>
		jQuery(document).ready(function($) {
			// Check if JS is working properly. If it is, we update an update in the DB
			$.ajax({
				type: "POST",
				data: {
					action: 'affwp_check_js'
				},
				url: '<?php echo admin_url( "admin-ajax.php" ); ?>'
			});
		});
<?php endif; ?>
		AFFWP.referral_credit_last = <?php echo absint( $referral_credit_last ); ?>;
		</script>
<?php
	}

	/**
	 * Output the conversion tracking script
	 *
	 * @since 1.0
	 */
	public function conversion_script( $args = array() ) {

		$defaults = array(
			'amount'      => '',
			'description' => '',
			'reference'   => '',
			'context'     => '',
			'campaign'    => '',
			'status'      => '',
			'type'        => 'sale',
		);

		$args = wp_parse_args( $args, $defaults );

		if( empty( $args['amount'] ) && ! empty( $_REQUEST['amount'] ) && 0 !== $args['amount'] ) {
			// Allow the amount to be passed via a query string or post request
			$args['amount'] = affwp_sanitize_amount( sanitize_text_field( urldecode( $_REQUEST['amount'] ) ) );
		}

		if( empty( $args['reference'] ) && ! empty( $_REQUEST['reference'] ) ) {
			// Allow the reference to be passed via a query string or post request
			$args['reference'] = sanitize_text_field( $_REQUEST['reference'] );
		}

		if( empty( $args['context'] ) && ! empty( $_REQUEST['context'] ) ) {
			$args['context'] = sanitize_text_field( $_REQUEST['context'] );
		}

		if( empty( $args['description'] ) && ! empty( $_REQUEST['description'] ) ) {
			$args['description'] = sanitize_text_field( $_REQUEST['description'] );
		}

		if( empty( $args['status'] ) && ! empty( $_REQUEST['status'] ) ) {
			$args['status'] = sanitize_text_field( $_REQUEST['status'] );
		}

		if( empty( $args['campaign'] ) && ! empty( $_REQUEST['campaign'] ) ) {
			$args['campaign'] = sanitize_text_field( $_REQUEST['campaign'] );
		}

		$md5 = md5( $args['amount'] . $args['description'] . $args['reference'] . $args['context'] . $args['status'] . $args['campaign'] );

		/**
		 * Fires before the JavaScript that maybe fires off a conversion creation is triggered via the [affiliate_conversion_script] shortcode.
		 *
		 * @since 2.2.4
		 *
		 * @param array  $args The arguments passed to the track_conversion() method.
		 * @param string $md5  The md5 hash of the amount, description, reference, context, status and campaign arguments.
		 */
		do_action( 'affwp_before_conversion_tracking_script', $args, $md5 );

?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {

			var ref   = $.cookie( 'affwp_ref' );
			var visit = $.cookie( 'affwp_ref_visit_id' );

			// If a referral var is present and a referral cookie is not already set
			if( ref && visit ) {

				// Fire an ajax request to log the hit
				$.ajax({
					type: "POST",
					data: {
						action      : 'affwp_track_conversion',
						affiliate   : ref,
						amount      : '<?php echo $args["amount"]; ?>',
						status      : '<?php echo $args["status"]; ?>',
						description : '<?php echo $args["description"]; ?>',
						context     : '<?php echo $args["context"]; ?>',
						reference   : '<?php echo $args["reference"]; ?>',
						campaign    : '<?php echo $args["campaign"]; ?>',
						type        : '<?php echo $args["type"]; ?>',
						md5         : '<?php echo $md5; ?>'
					},
					url: affwp_scripts.ajaxurl,
					success: function (response) {
						if ( window.console && window.console.log ) {
							console.log( response );
						}
					}

				}).fail(function (response) {
					if ( window.console && window.console.log ) {
						console.log( response );
					}
				}).done(function (response) {
				});

			}

		});
		</script>
<?php
	}

	/**
	 * Load JS files
	 *
	 * @since 1.0
	 */
	public function load_scripts() {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_script( 'jquery-cookie', AFFILIATEWP_PLUGIN_URL . 'assets/js/jquery.cookie' . $suffix . '.js', array( 'jquery' ), '1.4.0' );
		wp_enqueue_script( 'affwp-tracking', AFFILIATEWP_PLUGIN_URL . 'assets/js/tracking' . $suffix . '.js', array( 'jquery-cookie' ), AFFILIATEWP_VERSION );
		wp_localize_script( 'jquery-cookie', 'affwp_scripts', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_localize_script( 'affwp-tracking', 'affwp_debug_vars', $this->js_debug_data() );
	}

	/**
	 * Retrieves debug data strings for use in tracking.js.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return array Array of debug data strings.
	 */
	public function js_debug_data() {

		$integrations  = affiliate_wp()->integrations->get_enabled_integrations();
		$affwp_version = defined( 'AFFILIATEWP_VERSION' ) ? AFFILIATEWP_VERSION : 'undefined';
		$currency      = affwp_get_currency();


		$data = array (
			'integrations' => $integrations,
			'version'      => $affwp_version,
			'currency'     => $currency
		);

		/**
		 * JavaScript debug data to make available in AffiliateWP.
		 *
		 * @since 2.0
		 * @param $data An array of data to pass to the AffiliateWP tracking.js file.
		 */
		return apply_filters( 'affwp_js_debug_data', (array) $data );
	}

	/**
	 * Record referral visit via ajax
	 *
	 * @since 1.0
	 */
	public function track_visit() {

		$affiliate_id = isset( $_POST['affiliate'] ) ? absint( $_POST['affiliate'] ) : 0;
		$is_valid     = $this->is_valid_affiliate( $affiliate_id );
		$referrer     = isset( $_POST['referrer'] ) ? sanitize_text_field( $_POST['referrer'] ) : '';

		/**
		 * Filters whether to completely short-circuit tracking a visit.
		 *
		 * An explicitly true value must be passed back to the filter to execute the short-circuit.
		 *
		 * Example:
		 *
		 *     add_filter( 'affwp_tracking_skip_track_visit', '__return_true' );
		 *
		 * @since 2.1
		 *
		 * @param bool                   $skip_visit   Whether to skip tracking a visit. Default false.
		 * @param int                    $affiliate_id Affiliate ID.
		 * @param bool                   $is_valid     Whether the affiliate is valid.
		 * @param string                 $referrer     Visit referrer.
		 * @param \Affiliate_WP_Tracking $this         Tracking class instance.
		 */
		if ( true === apply_filters( 'affwp_tracking_skip_track_visit', false, $affiliate_id, $is_valid, $referrer, $this ) ) {

			affiliate_wp()->utils->log( 'Visit creation skipped during track_visit() via the affwp_tracking_skip_track_visit hook.' );

			die( '-3' );

		} elseif ( ! empty( $affiliate_id ) && $is_valid ) {

			if( ! affwp_is_url_banned( $referrer ) ) {
				// Store the visit in the DB
				$visit_id = affiliate_wp()->visits->add( array(
					'affiliate_id' => $affiliate_id,
					'ip'           => $this->get_ip(),
					'url'          => sanitize_text_field( $_POST['url'] ),
					'campaign'     => ! empty( $_POST['campaign'] ) ? sanitize_text_field( $_POST['campaign'] ) : '',
					'referrer'     => $referrer,
				) );

				affiliate_wp()->utils->log( sprintf( 'Visit #%d recorded for affiliate #%d in track_visit()', $visit_id, $affiliate_id ) );

				echo $visit_id;

				exit;

			} else {

				affiliate_wp()->utils->log( sprintf( '"%s" is a banned URL. A visit was not recorded.', $referrer ) );

				die( '-2' );
			}

		} elseif ( ! $is_valid ) {

			affiliate_wp()->utils->log( 'Invalid affiliate ID during track_visit()' );

			die( '-2' );

		} else {

			affiliate_wp()->utils->log( 'Affiliate ID missing during track_visit()' );

			die( '-2' );

		}

	}

	/**
	 * Record referral conversion via ajax
	 *
	 * This is called anytime a referred visitor lands on a success page, defined by the [affiliate_conversion_script] shortcode
	 *
	 * @since 1.0
	 */
	public function track_conversion() {

		$affiliate_id = absint( $_POST['affiliate'] );
		$is_valid     = $this->is_valid_affiliate( $affiliate_id );
		$visit_id     = $this->get_visit_id();

		/**
		 * Filters whether to completely short-circuit tracking a conversion.
		 *
		 * An explicitly true value must be passed back to the filter to execute the short-circuit.
		 *
		 * Example:
		 *
		 *     add_filter( 'affwp_tracking_skip_track_conversion', '__return_true' );
		 *
		 * @since 2.1
		 *
		 * @param bool                   $skip_visit   Whether to skip tracking the conversion. Default false.
		 * @param int                    $affiliate_id Affiliate ID.
		 * @param bool                   $is_valid     Whether the affiliate is valid.
		 * @param int|false              $visit_id     Visit ID derived from the cookie, otherwise false.
		 * @param \Affiliate_WP_Tracking $this         Tracking class instance.
		 */
		if ( true === apply_filters( 'affwp_tracking_skip_track_conversion', false, $affiliate_id, $is_valid, $visit_id, $this ) ) {

			affiliate_wp()->utils->log( 'Conversion handling skipped during track_conversion() via the affwp_tracking_skip_track_conversion hook.' );

			die( '-6' );

		} elseif( $is_valid ) {

			affiliate_wp()->utils->log( sprintf( 'Valid affiliate ID, %d, in track_conversion()', $affiliate_id ) );

			$md5 = md5( $_POST['amount'] . $_POST['description'] . $_POST['reference'] . $_POST['context'] . $_POST['status'] . $_POST['campaign'] );

			if( $md5 !== $_POST['md5'] ) {

				affiliate_wp()->utils->log( sprintf( 'Invalid MD5 in track_conversion(). Needed: %s. Posted: %s', $md5, $_POST['md5'] ) );

				die( '-3' ); // The args were modified
			}

			$referral = affiliate_wp()->referrals->get_by( 'visit_id', $visit_id );

			if( $referral ) {

				affiliate_wp()->utils->log( sprintf( 'Referral already generated for visit #%d.', $visit_id ) );

				die( '-4' ); // This visit has already generated a referral
			}

			$status = ! empty( $_POST['status'] ) ? $_POST['status'] : 'unpaid';
			$amount = sanitize_text_field( urldecode( $_POST['amount'] ) );

			if( 0 == $amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

				affiliate_wp()->utils->log( 'Referral not created due to 0.00 amount.' );

				die( '-5' ); // Ignore a zero amount referral
			}

			$amount 	 = $amount > 0 ? affwp_calc_referral_amount( $amount, $affiliate_id ) : 0;
			$description = sanitize_text_field( $_POST['description'] );
			$context     = sanitize_text_field( $_POST['context'] );
			$campaign    = sanitize_text_field( $_POST['campaign'] );
			$reference   = sanitize_text_field( $_POST['reference'] );
			$type        = sanitize_text_field( $_POST['type'] );

			// Create a new referral
			$referral_id = affiliate_wp()->referrals->add( apply_filters( 'affwp_insert_pending_referral', array(
					'affiliate_id' => $affiliate_id,
					'amount'       => $amount,
					'status'       => 'pending',
					'description'  => $description,
					'context'      => $context,
					'campaign'     => $campaign,
					'reference'    => $reference,
					'type'         => $type,
					'visit_id'     => $visit_id,
			), $amount, $reference, $description, $affiliate_id, $visit_id, array(), $context ) );

			affiliate_wp()->utils->log( sprintf( 'Referral created for visit #%d.', $visit_id ) );

			// Update the referral status.
			affwp_set_referral_status( $referral_id, $status );

			affiliate_wp()->utils->log( sprintf( 'Referral #%d set to %s for visit #%d.', $referral_id, $status, $visit_id ) );

			// Update the visit.
			affiliate_wp()->visits->update( $this->get_visit_id(), array( 'referral_id' => $referral_id ), '', 'visit' );

			affiliate_wp()->utils->log( sprintf( 'Visit #%d marked as converted.', $visit_id ) );

			echo $referral_id; exit;

		} else {

			affiliate_wp()->utils->log( 'Affiliate ID missing or invalid during track_conversion()' );

			die( '-2' );

		}

	}

	/**
	 * Record referral visit via template_redirect
	 *
	 * @since 1.0
	 */
	public function fallback_track_visit() {

		$affiliate_id = $this->referral;

		if( empty( $affiliate_id ) ) {

			$affiliate_id = $this->get_fallback_affiliate_id();

		}

		if( empty( $affiliate_id ) ) {
			return;
		}

		if( ! is_numeric( $affiliate_id ) ) {
			$affiliate_id = $this->get_affiliate_id_from_login( $affiliate_id );
		}

		$affiliate_id = absint( $affiliate_id );
		$is_valid     = $this->is_valid_affiliate( $affiliate_id );
		$visit_id     = $this->get_visit_id();
		$campaign     = $this->get_campaign();
		$referrer     = ! empty( $_SERVER['HTTP_REFERER'] ) ? sanitize_text_field( $_SERVER['HTTP_REFERER'] ) : '';

		/** This filter is documented in includes/class-tracking.php. */
		if ( true === apply_filters( 'affwp_tracking_skip_track_visit', false, $affiliate_id, $is_valid, $referrer, $this ) ) {

			affiliate_wp()->utils->log( 'Visit creation skipped during fallback_track_visit() via the affwp_tracking_skip_track_visit hook.' );

		} elseif ( $is_valid && ( ! $visit_id || affiliate_wp()->settings->get( 'referral_credit_last' ) ) ) {

			if ( ( ! empty( $referrer ) && ! affwp_is_url_banned( $referrer ) ) || empty( $referrer ) ) {

				if( $this->get_affiliate_id() === $affiliate_id && affiliate_wp()->settings->get( 'referral_credit_last' ) ) {
					affiliate_wp()->utils->log( 'Visit creation skipped during fallback_track_visit() with Credit Last Referrer enabled because ID already tracked.' );
					return;
				}

				$this->set_affiliate_id( $affiliate_id );

				// Store the visit in the DB
				$visit_id = affiliate_wp()->visits->add( array(
					'affiliate_id' => $affiliate_id,
					'ip'           => $this->get_ip(),
					'url'          => $this->get_current_page_url(),
					'campaign'     => $campaign,
					'referrer'     => $referrer,
				) );

				$this->set_visit_id( $visit_id );

				$this->set_campaign( $campaign );
			}

		} elseif( ! $is_valid ) {

			affiliate_wp()->utils->log( 'Invalid affiliate ID during fallback_track_visit()' );

		} elseif( ! $visit_id ) {

			affiliate_wp()->utils->log( 'Missing visit ID during fallback_track_visit()' );

		} elseif( $visit_id ) {

			affiliate_wp()->utils->log( 'Visit already logged during fallback_track_visit()' );

		} else {

			affiliate_wp()->utils->log( 'Invalid affiliate ID during fallback_track_visit()' );

		}

	}

	/**
	 * Get the affiliate ID when using fallback tracking method
	 *
	 * @since 1.7.11
	 */
	public function get_fallback_affiliate_id() {

		$affiliate_id = ! empty( $_GET[ $this->get_referral_var() ] ) ? $_GET[ $this->get_referral_var() ] : false;

		if ( empty( $affiliate_id ) ) {

			$path = ! empty( $_SERVER['REQUEST_URI' ] ) ? $_SERVER['REQUEST_URI' ] : '';

			if ( false !== strpos( $path, $this->get_referral_var() . '/' ) ) {

				$pieces = explode( '/', str_replace( '?', '/', $path ) );
				$key    = array_search( $this->get_referral_var(), $pieces );

				$pieces[ $key + 1 ] = strtolower( sanitize_user( $pieces[ $key + 1 ] ) );

				if ( $key ) {

					$key += 1;
					$affiliate_id = isset( $pieces[ $key ] ) ? $pieces[ $key ] : false;

					// Look for affiliate ID by username
					if ( intval( $affiliate_id ) < 1 || ! is_numeric( $affiliate_id ) ) {

						$affiliate_id = $this->get_affiliate_id_from_login( $affiliate_id );

						if ( empty( $affiliate_id ) ) {

							affiliate_wp()->utils->log( 'No user account found for given affiliate ID or login during get_fallback_affiliate_id()' );

							$affiliate_id = false;

						}

					}

				}

			}

		}

		return $affiliate_id;
	}


	/**
	 * Get the referral campaign
	 *
	 * @since 1.7
	 */
	public function get_campaign() {
		$campaign = isset( $_COOKIE['affwp_campaign'] ) ? sanitize_text_field( $_COOKIE['affwp_campaign'] ) : '';

		if( empty( $campaign ) ) {

			$campaign = isset( $_REQUEST['campaign'] ) ? sanitize_text_field( $_REQUEST['campaign'] ) : '';

		}
		return apply_filters( 'affwp_get_campaign', $campaign, $this );
	}

	/**
	 * Get the referral variable
	 *
	 * @since 1.0
	 */
	public function get_referral_var() {
		return $this->referral_var;
	}

	/**
	 * Set the referral variable
	 *
	 * @since 1.0
	 */
	public function set_referral_var() {
		$var = affiliate_wp()->settings->get( 'referral_var', 'ref' );
		$this->referral_var = apply_filters( 'affwp_referral_var', $var );
	}

	/**
	 * Set the cookie expiration time
	 *
	 * @since 1.0
	 */
	public function set_expiration_time() {

		// Default time is 1 day
		$days = affiliate_wp()->settings->get( 'cookie_exp', 1 );

		// Cannot permit cookies to go past 2038
		$max  = ( 2038 - date( 'Y' ) ) * 365;
		$days = $days > $max ? $max : $days;

		$this->expiration_time = apply_filters( 'affwp_cookie_expiration_time', $days );
	}

	/**
	 * Get the cookie expiration time in days
	 *
	 * @since 1.0
	 */
	public function get_expiration_time() {
		return $this->expiration_time;
	}

	/**
	 * Determine if current visit was referred
	 *
	 * @since 1.0
	 */
	public function was_referred() {
		$bool = isset( $_COOKIE['affwp_ref'] ) && $this->is_valid_affiliate( $_COOKIE['affwp_ref'] );
		return (bool) apply_filters( 'affwp_was_referred', $bool, $this );
	}

	/**
	 * Retrieves the visit ID from the affwp_ref_visit_id cookie (if set).
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return int|false Visit ID from the cookie or false.
	 */
	public function get_visit_id() {
		return ! empty( $_COOKIE['affwp_ref_visit_id'] ) ? absint( $_COOKIE['affwp_ref_visit_id'] ) : false;
	}

	/**
	 * Set the visit ID
	 *
	 * @since 1.0
	 */
	public function set_visit_id( $visit_id = 0 ) {
		setcookie( 'affwp_ref_visit_id', $visit_id, strtotime( '+' . $this->get_expiration_time() . ' days' ), COOKIEPATH, $this->get_cookie_domain() );

		/**
		 * Fires immediately after the affwp_ref_visit_id cookie is set
		 *
		 * @since 2.1.17
		 *
		 * @param int                    $visit_id Visit ID.
		 * @param \Affiliate_WP_Tracking $this     Tracking class instance.
		 */
		do_action( 'affwp_tracking_set_visit_id', $visit_id, $this );
	}

	/**
	 * Get the referring affiliate ID
	 *
	 * @since 1.0
	 */
	public function get_affiliate_id() {

		$affiliate_id = ! empty( $_COOKIE['affwp_ref'] ) ? $_COOKIE['affwp_ref'] : false;

		if ( ! empty( $affiliate_id ) ) {

			$affiliate_id = absint( $affiliate_id );

		}

		return apply_filters( 'affwp_tracking_get_affiliate_id', $affiliate_id );
	}

	/**
	 * Get the affiliate's ID from their user login
	 *
	 * @since 1.3
	 */
	public function get_affiliate_id_from_login( $login = '' ) {

		$affiliate_id = 0;

		if ( $affiliate = affwp_get_affiliate( urldecode( $login ) ) ) {
			$affiliate_id = $affiliate->ID;
		}

		/**
		 * Filters the affiliate ID retrieved from login in Affiliate_WP_Tracking.
		 *
		 * @since 1.3
		 *
		 * @param int    $affiliate_id Affiliate ID or 0 if no matching affiliate was found.
		 * @param string $login
		 */
		return apply_filters( 'affwp_tracking_get_affiliate_id', $affiliate_id, $login );

	}

	/**
	 * Get the affiliate's ID from their user login
	 *
	 * @since 1.3
	 */
	public function ajax_get_affiliate_id_from_login() {

		$success      = 0;
		$affiliate_id = 0;

		if( ! empty( $_POST['affiliate'] ) ) {

			$affiliate_id = $this->get_affiliate_id_from_login( $_POST['affiliate'] );

			if( ! empty( $affiliate_id ) ) {

				$success = 1;

			}

		}

		$return = array(
			'success'      => $success,
			'affiliate_id' => $affiliate_id
		);

		wp_send_json_success( $return );

	}

	/**
	 * Set the referring affiliate ID
	 *
	 * @since 1.0
	 */
	public function set_affiliate_id( $affiliate_id = 0 ) {
		setcookie( 'affwp_ref', $affiliate_id, strtotime( '+' . $this->get_expiration_time() . ' days' ), COOKIEPATH, $this->get_cookie_domain() );

		/**
		 * Fires immediately after the affwp_ref cookie is set
		 *
		 * @since 2.1.17
		 *
		 * @param int                    $affiliate_id Affiliate ID.
		 * @param \Affiliate_WP_Tracking $this         Tracking class instance.
		 */
		do_action( 'affwp_tracking_set_affiliate_id', $affiliate_id, $this );
	}

	/**
	 * Set the campaign
	 *
	 * @since 2.1.15
	 */
	public function set_campaign( $campaign = '' ) {
		setcookie( 'affwp_campaign', $campaign, strtotime( '+' . $this->get_expiration_time() . ' days' ), COOKIEPATH, $this->get_cookie_domain() );

		/**
		 * Fires immediately after the affwp_campaign cookie is set
		 *
		 * @since 2.1.17
		 *
		 * @param string                 $campaign Campaign.
		 * @param \Affiliate_WP_Tracking $this     Tracking class instance.
		 */
		do_action( 'affwp_tracking_set_campaign', $campaign, $this );
	}

	/**
	 * Get the cookie domain.
	 *
	 * @since 2.1.10
	 * @return bool|string false if a cookie domain isn't set, string hostname (host.tld) otherwise
	 */
	public function get_cookie_domain() {

		// COOKIE_DOMAIN is false by default
		$cookie_domain = COOKIE_DOMAIN;

		$share_cookies = affiliate_wp()->settings->get( 'cookie_sharing', false );

		// providing a domain to jQuery.cookie or PHP's setcookie results prefixes the cookie domain
		// with a dot, indicating it should be shared with sub-domains
		if ( ! $cookie_domain && $share_cookies ) {
			$cookie_domain = parse_url( get_home_url(), PHP_URL_HOST );
		}

		/**
		 * Filters the tracking cookie domain.
		 *
		 * @since 2.1.10
		 *
		 * @param string $cookie_domain cookie domain
		 */
		return apply_filters( 'affwp_tracking_cookie_domain', $cookie_domain );
	}

	/**
	 * Check if it is a valid affiliate
	 *
	 * @since 1.0
	 */
	public function is_valid_affiliate( $affiliate_id = 0 ) {

		if( empty( $affiliate_id ) ) {
			$affiliate_id = $this->get_affiliate_id();
		}

		$ret       = false;
		$affiliate = affwp_get_affiliate( $affiliate_id );

		if( $affiliate ) {

			$is_self = is_user_logged_in() && get_current_user_id() == $affiliate->user_id;
			$active  = 'active' === $affiliate->status;
			$ret     = ! $is_self && $active;
		}

		return apply_filters( 'affwp_tracking_is_valid_affiliate', $ret, $affiliate_id );
	}

	/**
	 * Get the visitor's IP address
	 *
	 * @since 1.0
	 */
	public function get_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			//check ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			//to check ip is pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return apply_filters( 'affwp_get_ip', $ip );
	}

	/**
	 * Get the current page URL
	 *
	 * @since  1.0
	 * @global $post
	 * @return string $page_url Current page URL
	 */
	public function get_current_page_url() {
		global $post;

		if ( is_front_page() ) {

			$page_url = home_url();

		} else {

			$page_url = set_url_scheme( 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] );

		}

		return apply_filters( 'affwp_get_current_page_url', $page_url );
	}

	/**
	 * Determine if we need to use the fallback tracking method
	 *
	 * @since 1.0
	 */
	public function use_fallback_method() {

		$use_fallback = affiliate_wp()->settings->get( 'tracking_fallback', false );
		$js_works     = (int) get_option( 'affwp_js_works' );
		$use_fallback = $use_fallback || 1 !== $js_works;

		return apply_filters( 'affwp_use_fallback_tracking_method', $use_fallback );
	}

	/**
	 * Set whether JS works or not. This is called via ajax.
	 *
	 * @since 1.7
	 */
	public function check_js() {

		update_option( 'affwp_js_works', 1 );

		die( '1' );
	}

	/**
	 * Strips pretty referral bits from pagination links.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param string $link Pagination link.
	 * @return string (Maybe) filtered pagination link.
	 */
	public function strip_referral_from_paged_urls( $link ) {
		// Only mess with $link if there's pagination.
		preg_match( '/page\/\d\/?/', $link, $matches );

		if ( ! empty( $matches[0] ) ) {
			$referral_var = $this->get_referral_var();

			// Remove a non-pretty referral ID.
			$link = remove_query_arg( $referral_var, $link );

			// Remove a pretty referral ID or username.
			preg_match( "/\/$referral_var\/(\w+)\//", $link, $pretty_matches );

			if ( ! empty( $pretty_matches[0] ) ) {
				$link = str_replace( $pretty_matches[0], '/', $link );
			}
		}

		return $link;
	}

	/**
	 * Writes a debug log message.
	 *
	 * @access private
	 * @since  1.8
	 * @deprecated 2.0.2 Use affiliate_wp()->utils->log() instead
	 *
	 * @see affiliate_wp()->utils->log()
	 */
	private function log( $message = '' ) {
		_deprecated_function( __METHOD__, '2.0.2', 'affiliate_wp()->utils->log()' );

		affiliate_wp()->utils->log( $message );
	}

}
