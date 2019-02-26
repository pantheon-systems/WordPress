<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * User migration class that handles importing existing user accounts as affiliates
 *
 * @since 1.3
 * @return void
 */
class Affiliate_WP_Migrate_Users extends Affiliate_WP_Migrate_Base {

	/**
	 * Migrate users belonging to these roles
	 *
	 * @var array
	 */
	public $roles = array();

	/**
	 * Process the migration routine
	 *
	 * @since  1.3
	 * @param  int    $step
	 * @param  string $part
	 * @return void
	 */
	public function process( $step = 1, $part = '' ) {

		if ( 'affiliates' !== $part || ! $this->roles ) {
			return;
		}

		$inserted = $this->do_users( $step );

		if ( $inserted ) {

			$this->step_forward( $step, 'affiliates' );

		} else {

			$this->finish();

		}

	}

	/**
	 * Move forward one step
	 *
	 * @since  1.3
	 * @param  int    $step
	 * @param  string $part
	 * @return void
	 */
	public function step_forward( $step = 1, $part = '' ) {

		$step++;

		$redirect = add_query_arg(
			array(
				'page'  => 'affiliate-wp-migrate',
				'type'  => 'users',
				'part'  => $part,
				'step'  => $step,
				'roles' => array_map( 'sanitize_key', $this->roles )
			),
			admin_url( 'index.php' )
		);

		if( $this->debug ) {
			$this->log( $redirect );
		}

		wp_safe_redirect( $redirect );

		exit;

	}

	/**
	 * Import one batch of users
	 *
	 * @since  1.3
	 * @param  int     $step
	 * @return boolean
	 */
	public function do_users( $step = 1 ) {

		if ( ! $this->roles ) {
			
			return false;
		}

		$affiliate_user_ids = get_transient( 'affwp_migrate_users_user_ids' );

		if ( false === $affiliate_user_ids ) {
			$affiliate_user_ids = affiliate_wp()->affiliates->get_affiliates( array(
				'number' => -1,
				'fields' => 'user_id',
			) );

			set_transient( 'affwp_migrate_users_user_ids', $affiliate_user_ids, 10 * MINUTE_IN_SECONDS );
		}

		$args = array(
			'number'     => 100,
			'offset'     => ( $step - 1 ) * 100,
			'exclude'    => $affiliate_user_ids,
			'orderby'    => 'ID',
			'order'      => 'ASC',
			'role__in'   => $this->roles,
			'fields'     => array( 'ID', 'user_email', 'user_registered' )
		);

		$users = get_users( $args );

		if ( empty( $users ) ) {
			return false;
		}

		$inserted = array();

		foreach ( $users as $user ) {

			$args = array(
				'status'          => 'active',
				'user_id'         => $user->ID,
				'payment_email'	  => $user->user_email,
				'date_registered' => $user->user_registered
			);

			$inserted[] = affiliate_wp()->affiliates->insert( $args, 'affiliate' );

		}

		if ( ! $inserted ) {
			return false;
		}

		if ( ! $current_count = affiliate_wp()->utils->data->get( 'affwp_migrate_users_current_count' ) ) {
			$current_count = 0;
		}
		$current_count = $current_count + count( $inserted );

		affiliate_wp()->utils->data->write( 'affwp_migrate_users_current_count', $current_count );

		return true;
	}

	/**
	 * Done creating affiliate accounts for users
	 *
	 * @since  1.7
	 * @return void
	 */
	public function finish() {

		// Clean up.
		delete_transient( 'affwp_migrate_users_user_ids' );

		wp_safe_redirect( affwp_admin_url( 'affiliates', array( 'affwp_notice' => 'affiliate_added' ) ) );
		exit;

	}

}
