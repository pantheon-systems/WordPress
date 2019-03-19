<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils\Batch_Process as Batch;

/**
 * Implements a batch processor for importing affiliate accounts from a CSV file.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Batch_Process\Import\CSV
 * @see \AffWP\Utils\Batch_Process\With_PreFetch
 */
class Import_Affiliates extends Batch\Import\CSV implements Batch\With_PreFetch {

	/**
	 * Batch process ID.
	 *
	 * @access public
	 * @since  2.1.7
	 * @var    string
	 */
	public $batch_id = 'import-affiliates';

	/**
	 * Whether to use 'strict' mode when sanitizing generated usernames.
	 *
	 * See {@see 'affwp_batch_import_affiliates_strict_usernames'}.
	 *
	 * @access public
	 * @since  2.1
	 * @var    bool
	 */
	public $use_strict;

	/**
	 * Instantiates the batch process.
	 *
	 * @param string $_file
	 * @param int    $_step
	 */
	public function __construct( $_file = '', $_step = 1 ) {

		/**
		 * Filters whether to generate new affiliate usernames using 'strict' mode,
		 * i.e. reduce generated usernames to ASCII-only.
		 *
		 * Notes: Some platform scenarios such as multisite will apply further sanitization
		 * to usernames regardless of whether `$use_strict` is enabled or not.
		 *
		 * @since 2.1
		 *
		 * @param bool $use_strict Whether to use 'strict' mode. Default true.
		 */
		$this->use_strict = apply_filters( 'affwp_batch_import_affiliates_strict_usernames', true );

		$fields = affiliate_wp()->affiliates->get_columns();

		unset( $fields['affiliate_id'] );
		unset( $fields['user_id'] );

		$fields   = array_keys( $fields );
		$fields[] = 'user_name';
		$fields   = array_fill_keys( $fields, '' );

		$this->map_fields( $fields );

		parent::__construct( $_file, $_step );
	}

	/**
	 * Initializes the batch process.
	 *
	 * This is the point where any relevant data should be initialized for use by the processor methods.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function init( $data = null ) {
		if ( null !== $data ) {
			if ( ! empty( $data['affwp-import-field'] ) ) {
				$this->data = $data['affwp-import-field'];
			}
		}
	}

	/**
	 * Pre-fetches data to speed up processing.
	 *
	 * @access public
	 * @since  2.1
	 */
	public function pre_fetch() {
		$total_to_import = $this->get_total_count();

		if ( false === $total_to_import  ) {
			$this->set_total_count( absint( $this->total ) );
		}
	}

	/**
	 * Processes a single step of importing affiliates.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @return int|string
	 */
	public function process_step() {
		if ( ! $this->can_import() ) {
			wp_die( __( 'You do not have permission to import data.', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
		}

		$running_count = 0;
		$current_count = $this->get_current_count();
		$offset        = $this->get_offset();

		if ( $current_count >= $this->get_total_count() ) {
			affiliate_wp()->utils->log( 'Affiliate CSV Import Done' );
			return 'done';
		}

		affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step );

		$data = $this->get_data();

		if ( $data ) {

			$data = array_slice( $data, $offset, $this->per_step, true );

			foreach ( $data as $key => $row ) {

				affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step . ' raw data: ' . print_r( $row, true ) );

				$args = $this->map_row( $row );

				affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step . ' mapped data: ' . print_r( $args, true ) );

				if ( empty( $args['email'] ) ) {
					continue;
				}

				$user_id = $this->create_user( $args );

				if ( $user_id ) {
						affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step . ' - Affiliate ' . $key . ' user ID ' . $user_id );
					// Check for an existing affiliate for this user.
					if ( $affiliate = affiliate_wp()->affiliates->get_by( 'user_id', $user_id ) ) {
						affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step . ' - Affiliate ' . $key . ' skipped because affiliate already exists' );
						continue;
					} else {
						$args['user_id'] = $user_id;
					}
				} else {
					affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step . ' -  Affiliate ' . $key . ' skipped because user not found nor created' );
					continue;
				}

				$args['user_id']   = $user_id;

				affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step . ' -  Affiliate ' . $key . ' data pre creation: ' . print_r( $args, true ) );

				if ( false !== $affiliate = affwp_add_affiliate( $args ) ) {

					affiliate_wp()->utils->log( 'Affiliate CSV Import Step ' . $this->step . ' -  Affiliate ' . $key . ' created successfully' );

					$data = array();

					if ( ! empty( $args['visits'] ) ) {
						$data['visits'] = absint( $args['visits'] );
					}

					if ( ! empty( $args['referrals'] ) ) {
						$data['referrals'] = absint( $args['referrals'] );
					}

					// Set visit and referral counts.
					affiliate_wp()->affiliates->update( $affiliate, $data, '', 'affiliate' );

					// Set earnings (sanitized during increase).
					if ( ! empty( $args['earnings'] ) ) {
						affwp_increase_affiliate_earnings( $affiliate, $args['earnings'] );
					}

					if ( ! empty( $args['unpaid_earnings'] ) ) {
						affwp_increase_affiliate_unpaid_earnings( $affiliate, $args['unpaid_earnings'] );
					}

					// Increment the count.
					$running_count++;
				}
			}
		}

		$this->set_current_count( $current_count + $this->per_step );
		$this->set_running_count( $this->get_running_count() + $running_count );

		return ++$this->step;
	}

	/**
	 * Retrieves a message for the given code.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code ) {

		switch( $code ) {

			case 'done':
				$final_count = $this->get_running_count();
				$total_count = $this->get_total_count();
				$skipped     = $final_count < $total_count ? $total_count - $final_count : 0;

				if ( 0 == $final_count ) {

					$message = __( 'No new affiliates were imported.', 'affiliate-wp' );

				} else {

					$message = sprintf(
						_n(
							'%s affiliate was successfully imported.',
							'%s affiliates were successfully imported.',
							$final_count,
							'affiliate-wp'
						), number_format_i18n( $final_count )
					);

				}


				if ( $skipped > 0 ) {
					$message .= sprintf( ' ' .
						_n(
							'%s other existing affiliate or invalid row was skipped.',
							'%s other existing affiliates or invalid rows were skipped.',
							$skipped,
							'affiliate-wp'
						), number_format_i18n( $skipped )
					);
				}

				// Add a link to manage affiliates in the success message.
				$message .= ' ' . affwp_admin_link( 'affiliates', __( 'Manage your affiliates.', 'affiliate-wp' ) );

				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

	/**
	 * Helper that attempts to create a user account for the new affiliate.
	 *
	 * If a user account is found matching the given payment email, that user ID is returned instead.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param array $args Arguments for adding a new affiliate.
	 * @return int|false User ID if a user was found or derived, otherwise false.
	 */
	public function create_user( $args ) {
		$defaults = array_fill_keys( array( 'username', 'email', 'payment_email' ), '' );
		$args     = wp_parse_args( $args, $defaults );

		$user_id = $this->get_user_from_args( $args );

		if ( $user_id ) {
			return $user_id;
		}

		if ( ! empty( $args['username'] ) ) {
			$user_login = $args['username'];
		} else {
			$user_login = $this->generate_login_from_email( $args['email'] );
		}

		$user_id = wp_insert_user( array(
			'user_login' => sanitize_user( $user_login, $this->use_strict ),
			'user_email' => sanitize_text_field( $args['email'] ),
			'user_pass'  => wp_generate_password( 20, false ),
			'first_name' => ! empty( $args['name'] ) ? sanitize_text_field( $args['name'] ) : '',
			'last_name'  => ! empty( $args['last_name'] ) ? sanitize_text_field( $args['last_name'] ) : '',
		) );

		if ( ! is_wp_error( $user_id ) ) {
			return $user_id;
		} else {
			return false;
		}
	}

	/**
	 * Gets a user ID from a set of mapped affiliate arguments.
	 *
	 * @access protected
	 * @since  2.1
	 *
	 * @param array $args Affiliate arguments.
	 * @return int|false A derived user ID, otherwise false.
	 */
	protected function get_user_from_args( $args ) {

		if ( $user = get_user_by( 'login', $args['username'] ) ) {
			$user_id = $user->ID;
		} elseif ( $user = get_user_by( 'email', $args['email'] ) ) {
			$user_id = $user->ID;
		} elseif ( $user = get_user_by( 'email', $args['payment_email'] ) ) {
			$user_id = $user->ID;
		} else {
			$user_id = false;
		}

		return $user_id;
	}

	/**
	 * Generates a username from a given email address.
	 *
	 * @access protected
	 * @since  2.1
	 *
	 * @param string $email Email to use for generating a unique username.
	 * @return string Generated username.
	 */
	protected function generate_login_from_email( $email ) {

		$number = rand( 321, 123456 );

		preg_match( '/[^@]*/', $email, $matches );

		if ( isset( $matches[0] ) ) {
			$user_login = "{$matches[0]}{$number}";
		} else {
			$user_login = "affiliate{$number}";
		}

		return $user_login;
	}

	/**
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 * @since  2.1
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function finish( $batch_id ) {
		// Invalidate the affiliates cache.
		wp_cache_set( 'last_changed', microtime(), 'affiliates' );

		parent::finish( $batch_id );
	}

}
