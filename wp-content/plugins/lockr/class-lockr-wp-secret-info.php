<?php
/**
 * Class to handle all the metadata like wrapping keys used to store secrets in Lockr.
 *
 * @package Lockr
 */

use Lockr\SecretInfoInterface;

/**
 * Class to handle all the metadata like wrapping keys used to store secrets in Lockr.
 */
class Lockr_WP_Secret_Info implements SecretInfoInterface {

	/**
	 * Data array for secrets stored in Lockr
	 *
	 * @var array $data the data array of secrets stored in Lockr.
	 * **/
	private $data;

	/**
	 * Get all the data on secrets stored in Lockr on when constructed.
	 **/
	public function __construct() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'lockr_keys';
		$query      = "SELECT `key_name`, `key_value` FROM $table_name ";
		$return     = $wpdb->get_results( $query ); // WPCS: unprepared SQL OK.
		$keys       = array();
		foreach ( $return as $key ) {
			$keys[ $key->key_name ] = $key->key_value;
		}
		$this->data = $keys;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $name The name of the key stored in Lockr.
	 */
	public function getSecretInfo( $name ) {
		return isset( $this->data[ $name ] ) ? json_decode( $this->data[ $name ], true ) : [];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $name The name of the key stored in Lockr.
	 * @param array  $info The array of information used to find and decrypt the value.
	 *
	 * @throws \Exception If the database does not connect we cannot store information.
	 */
	public function setSecretInfo( $name, array $info ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'lockr_keys';
		$key_exists = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE key_name = %s", array( $name ) ) ); // WPCS: unprepared SQL OK.
		$key_store  = false;

		if ( $key_exists ) {
			$key_data  = array(
				'key_value' => wp_json_encode( $info ),
			);
			$where     = array(
				'key_name' => $name,
			);
			$key_store = $wpdb->update( $table_name, $key_data, $where );
		} else {
			$key_data  = array(
				'time'            => date( 'Y-m-d H:i:s' ),
				'key_name'        => $name,
				'key_label'       => '',
				'key_abstract'    => '',
				'option_override' => null,
				'key_value'       => wp_json_encode( $info ),
				'auto_created'    => false,
			);
			$key_store = $wpdb->insert( $table_name, $key_data );
		}

		if ( ! $key_store ) {
			throw new \Exception( 'Could not store the new key data in the database.' );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAllSecretInfo() {
		return $this->data;
	}

}
