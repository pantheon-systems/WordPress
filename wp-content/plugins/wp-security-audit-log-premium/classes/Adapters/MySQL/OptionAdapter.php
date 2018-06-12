<?php
/**
 * Adapter: Option.
 *
 * MySQL database Option class.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MySQL database Option class.
 *
 * MySQL wsal_options table used for to store the plugin settings and Add-Ons settings.
 *
 * @package Wsal
 */
class WSAL_Adapters_MySQL_Option extends WSAL_Adapters_MySQL_ActiveRecord {

	/**
	 * Contains the table name.
	 *
	 * @var string
	 */
	protected $_table = 'wsal_options';

	/**
	 * Contains primary key column name, override as required.
	 *
	 * @var string
	 */
	protected $_idkey = 'id';

	/**
	 * Option id.
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * Option name.
	 *
	 * @var string
	 */
	public $option_name = '';

	/**
	 * Option name max length.
	 *
	 * @var int
	 */
	public static $option_name_maxlength = 100;

	/**
	 * Option value.
	 *
	 * @var mix
	 */
	public $option_value = '';

	/**
	 * Method: Constructor.
	 *
	 * @param array $conn - Connection array.
	 */
	public function __construct( $conn ) {
		parent::__construct( $conn );
	}

	/**
	 * Returns the model class for adapter.
	 *
	 * @return WSAL_Models_Occurrence
	 */
	public function GetModel() {
		return new WSAL_Models_Option();
	}

	/**
	 * Get option by name.
	 *
	 * @param string $name - Option name.
	 * @return string|null - Option value.
	 */
	public function GetNamedOption( $name ) {
		if ( $this->IsInstalled() ) {
			return $this->Load( 'option_name = %s', array( $name ) );
		} else {
			return null;
		}
	}

	/**
	 * Get options by prefix (notifications stored in json format).
	 *
	 * @param string $opt_prefix - Prefix.
	 * @return array|null - Options.
	 */
	public function GetNotificationsSetting( $opt_prefix ) {
		if ( $this->IsInstalled() ) {
			return $this->LoadArray( 'option_name LIKE %s', array( $opt_prefix . '%' ) );
		} else {
			return null;
		}
	}

	/**
	 * Get option by id (notifications stored in json format).
	 *
	 * @param int $id - Option ID.
	 * @return string|null - Option.
	 */
	public function GetNotification( $id ) {
		if ( $this->IsInstalled() ) {
			return $this->Load( 'id = %d', array( $id ) );
		} else {
			return null;
		}
	}

	/**
	 * Delete option by name.
	 *
	 * @param string $name - Option name.
	 * @return boolean.
	 */
	public function DeleteByName( $name ) {
		if ( ! empty( $name ) ) {
			$sql = 'DELETE FROM ' . $this->GetTable() . " WHERE option_name = '" . $name . "'";
			// Execute query.
			return parent::DeleteQuery( $sql );
		} else {
			return false;
		}
	}

	/**
	 * Delete options start with prefix.
	 *
	 * @param string $opt_prefix - Prefix.
	 * @return boolean.
	 */
	public function DeleteByPrefix( $opt_prefix ) {
		if ( ! empty( $opt_prefix ) ) {
			$sql = 'DELETE FROM ' . $this->GetTable() . " WHERE option_name LIKE '" . $opt_prefix . "%'";
			// Execute query.
			return parent::DeleteQuery( $sql );
		} else {
			return false;
		}
	}

	/**
	 * Number of options start with prefix.
	 *
	 * @param string $opt_prefix - Prefix.
	 * @return integer Indicates the number of items.
	 */
	public function CountNotifications( $opt_prefix ) {
		$_wpdb = $this->connection;
		$sql = 'SELECT COUNT(id) FROM ' . $this->GetTable() . " WHERE option_name LIKE '" . $opt_prefix . "%'";
		return (int) $_wpdb->get_var( $sql );
	}
}
