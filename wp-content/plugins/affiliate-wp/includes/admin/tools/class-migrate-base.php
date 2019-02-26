<?php

class Affiliate_WP_Migrate_Base {

	/**
	 * Whether debug mode is enabled.
	 *
	 * @access  public
	 * @since   1.8.8
	 * @var     bool
	 */
	public $debug;

	/**
	 * Logging class object
	 *
	 * @access  public
	 * @since   1.8.8
	 * @var     Affiliate_WP_Logging
	 * @deprecated 2.0.2
	 */
	public $logs;

	/**
	 * Writes a log message.
	 *
	 * @access public
	 * @since  1.8.8
	 * @deprecated 2.0.2 Use affiliate_wp()->utils->log() instead.
	 *
	 * @see affiliate_wp()->utils->log()
	 */
	public function log( $message = '' ) {
		_deprecated_function( __METHOD__, '2.0.2', 'affiliate_wp()->utils->log()' );

		affiliate_wp()->utils->log( $message );
	}

	public function process( $step = 1, $part = '' ) {


	}

	public function step_forward() {

		$step = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
		$part = isset( $_GET['part'] ) ? $_GET['part'] : 'affiliates';

		$step++;

		$redirect = add_query_arg(
			array(
				'page' => 'affiliate-wp-migrate',
				'type' => 'affiliates-pro',
				'part' => $part,
				'step' => $step
			),
			admin_url( 'index.php' )
		);

		wp_safe_redirect( $redirect );

		exit;

	}

	public function finish() {

		wp_safe_redirect( affwp_admin_url() );
		exit;
	}

	/**
	 * Retrieves the total count of migrated items.
	 *
	 * @access public
	 * @since  1.9.5
	 * @static
	 *
	 * @param string $key The stored option key.
	 * @return mixed|false The stored data, otherwise false.
	 */
	public static function get_items_total( $key ) {
		return affiliate_wp()->utils->data->get( $key );
	}

	/**
	 * Deletes the total count of migrated items.
	 *
	 * @access public
	 * @since  1.9.5
	 * @static
	 *
	 * @param string $key The stored option name to delete.
	 */
	public static function clear_items_total( $key ) {
		affiliate_wp()->utils->data->delete( $key );
	}

	/**
	 * Retrieves stored data by key.
	 *
	 * Given a key, get the information from the database directly.
	 *
	 * @access protected
	 * @since  1.9.5
	 * @deprecated 2.0 Use affiliate_wp()->utils->data->get() instead.
	 *
	 * @param string $key The stored option key.
	 * @return mixed|false The stored data, otherwise false.
	 */
	protected function get_stored_data( $key ) {
		_deprecated_function( __METHOD__, '2.0', 'affiliate_wp()->utils->data->get()' );

		return affiliate_wp()->utils->data->get( $key );
	}

	/**
	 * Store some data based on key and value.
	 *
	 * @access protected
	 * @since  1.9.5
	 * @deprecated 2.0 Use affiliate_wp()->utils->data->write() instead.
	 *
	 * @param string $key     The option_name.
	 * @param mixed  $value   The value to store.
	 * @param array  $formats Optional. Array of formats to pass for key, value, and autoload.
	 *                        Default empty (all strings).
	 */
	protected function store_data( $key, $value, $formats = array() ) {
		_deprecated_function( __METHOD__, '2.0', 'affiliate_wp()->utils->data->write()' );

		affiliate_wp()->utils->data->write( $key, $value );
	}

	/**
	 * Deletes a piece of stored data by key.
	 *
	 * @access protected
	 * @since  1.9.5
	 * @deprecated 2.0 Use affiliate_wp()->utils->data->delete() instead.
	 *
	 * @param string $key The stored option name to delete.
	 */
	protected function delete_data( $key ) {
		_deprecated_function( __METHOD__, '2.0', 'affiliate_wp()->utils->data->delete()' );

		affiliate_wp()->utils->data->delete( $key );
	}

}
