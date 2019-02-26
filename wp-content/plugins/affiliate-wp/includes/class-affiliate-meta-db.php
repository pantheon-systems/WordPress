<?php
/**
 * Core class used to implement affiliate meta.
 *
 * @since 1.6
 *
 * @see Affiliate_WP_DB
 */
class Affiliate_WP_Affiliate_Meta_DB extends Affiliate_WP_DB {

	/**
	 * Sets up the Affiliate Meta DB class.
	 *
	 * @access public
	 * @since  1.6
	*/
	public function __construct() {
		global $wpdb;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single affiliate meta table for the whole network
			$this->table_name  = 'affiliate_wp_affiliatemeta';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_affiliatemeta';
		}
		$this->primary_key = 'meta_id';
		$this->version     = '1.0';

		add_action( 'plugins_loaded', array( $this, 'register_table' ), 11 );
		add_filter( 'get_affiliate_metadata', array( $this, 'sanitize_meta' ), 100, 4 );
	}

	/**
	 * Retrieves the table columns and data types.
	 *
	 * @access public
	 * @since  1.7.18
	 *
	 * @return array List of affiliate meta table columns and their respective types.
	*/
	public function get_columns() {
		return array(
			'meta_id'      => '%d',
			'affiliate_id' => '%d',
			'meta_key'     => '%s',
			'meta_value'   => '%s',
		);
	}

	/**
	 * Registers the table with $wpdb so the metadata api can find it.
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
	 */
	public function register_table() {
		global $wpdb;
		$wpdb->affiliatemeta = $this->table_name;
	}

	/**
	 * Retrieves an affiliate meta field for a affiliate.
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @param int    $affiliate_id Optional. Affiliate ID. Default 0.
	 * @param string $meta_key     Optional. The meta key to retrieve. Default empty.
	 * @param bool   $single       Optional. Whether to return a single value. Default false.
	 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
	 *
	 */
	function get_meta( $affiliate_id = 0, $meta_key = '', $single = false ) {
		return get_metadata( 'affiliate', $affiliate_id, $meta_key, $single );
	}

	/**
	 * Adds a meta data field to a affiliate.
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @param int    $affiliate_id Optional. Affiliate ID. Default 0.
	 * @param string $meta_key     Optional. Meta data key. Default empty.
	 * @param mixed  $meta_value   Optional. Meta data value. Default empty
	 * @param bool   $unique       Optional. Whether the same key should not be added. Default false.
	 * @return bool False for failure. True for success.
	 */
	function add_meta( $affiliate_id = 0, $meta_key = '', $meta_value = '', $unique = false ) {
		return add_metadata( 'affiliate', $affiliate_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Updates an affiliate meta field based on affiliate ID.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with the
	 * same key and affiliate ID.
	 *
	 * If the meta field for the affiliate does not exist, it will be added.
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @param int    $affiliate_id Optional. Affiliate ID. Default 0.
	 * @param string $meta_key     Optional. Meta data key. Default empty.
	 * @param mixed  $meta_value   Optional. Meta data value. Default empty.
	 * @param mixed  $prev_value   Optional. Previous value to check before removing. Default empty.
	 * @return bool False on failure, true if success.
	 */
	function update_meta( $affiliate_id = 0, $meta_key = '', $meta_value = '', $prev_value = '' ) {
		return update_metadata( 'affiliate', $affiliate_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Removes metadata matching criteria from a affiliate.
	 *
	 * You can match based on the key, or key and value. Removing based on key and
	 * value, will keep from removing duplicate metadata with the same key. It also
	 * allows removing all metadata matching key, if needed.
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @param int    $affiliate_id Optional. Affiliate ID. Default 0.
	 * @param string $meta_key     Optional. Meta data key. Default empty.
	 * @param mixed  $meta_value   Optional. Meta data value. Default empty.
	 * @return bool False for failure. True for success.
	 */
	function delete_meta( $affiliate_id = 0, $meta_key = '', $meta_value = '' ) {
		return delete_metadata( 'affiliate', $affiliate_id, $meta_key, $meta_value );
	}

	/**
	 * Sanitizes serialized affiliate meta values when retrieved.
	 *
	 * @since 2.1.4.2
	 *
	 * @param null   $value        The value get_metadata() should return - a single metadata value,
	 *                             or an array of values.
	 * @param int    $affiliate_id Affiliate ID.
	 * @param string $meta_key     Meta key.
	 * @param bool   $single       Whether to return only the first value of the specified $meta_key.
	 */
	public function sanitize_meta( $value, $affiliate_id, $meta_key, $single ) {

		$meta_cache = wp_cache_get( $affiliate_id, 'affiliate_meta' );

		if ( ! $meta_cache ) {
			$meta_cache = update_meta_cache( 'affiliate', array( $affiliate_id ) );
			$meta_cache = $meta_cache[ $affiliate_id ];
		}

		// Bail and let get_metadata() handle it if there's no cache.
		if ( ! $meta_cache || ! isset( $meta_cache[ $meta_key ] ) ) {
			return $value;
		}

		$value = $meta_cache[ $meta_key ];

		foreach ( $value as $index => $_value ) {
			$value[ $index ] = affwp_maybe_unserialize( $_value );
		}

		return $value;
	}

	/**
	 * Creates the table.
	 *
	 * @access public
	 * @since  1.6
	 *
	 * @see dbDelta()
	*/
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			affiliate_id bigint(20) NOT NULL DEFAULT '0',
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext,
			PRIMARY KEY  (meta_id),
			KEY affiliate_id (affiliate_id),
			KEY meta_key (meta_key)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}
