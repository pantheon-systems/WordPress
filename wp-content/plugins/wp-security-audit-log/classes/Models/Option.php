<?php
/**
 * Class: Options Model Class
 *
 * Option Model gets and sets the options of the wsal_options table in the database.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress options are always loaded from the default WordPress database.
 *
 * Option Model gets and sets the options of the wsal_options table in the database.
 *
 * @package Wsal
 */
class WSAL_Models_Option extends WSAL_Models_ActiveRecord {

	/**
	 * Option ID.
	 *
	 * @var int
	 */
	public $id = '';

	/**
	 * Option Name.
	 *
	 * @var string
	 */
	public $option_name = '';

	/**
	 * Option Value.
	 *
	 * @var string
	 */
	public $option_value = '';

	/**
	 * Model Name.
	 *
	 * @var string
	 */
	protected $adapterName = 'Option';

	/**
	 * Options are always stored in WPDB. This setting ensures that.
	 *
	 * @var bool
	 */
	protected $useDefaultAdapter = true;

	/**
	 * Sets Option record.
	 *
	 * @param string $name - Option name.
	 * @param mixed  $value - Option value.
	 */
	public function SetOptionValue( $name, $value ) {
		$option = $this->getAdapter()->GetNamedOption( $name );
		$this->id = $option['id'];
		$this->option_name = $name;
		// Serialize if $value is array or object.
		$value = maybe_serialize( $value );
		$this->option_value = $value;
		return $this->Save();
	}

	/**
	 * Gets Option record.
	 *
	 * @param string $name - Option name.
	 * @param mixed  $default - (Optional) Default value.
	 * @return mixed option value
	 */
	public function GetOptionValue( $name, $default = array() ) {
		$option = $this->getAdapter()->GetNamedOption( $name );
		$this->option_value = ( ! empty( $option )) ? $option['option_value'] : null;
		if ( ! empty( $this->option_value ) ) {
			$this->_state = self::STATE_LOADED;
		}
		// Unserialize if $value is array or object.
		$this->option_value = maybe_unserialize( $this->option_value );
		return $this->IsLoaded() ? $this->option_value : $default;
	}

	/**
	 * Save Option record.
	 *
	 * @see WSAL_Adapters_MySQL_ActiveRecord::Save()
	 * @return integer|boolean Either the number of modified/inserted rows or false on failure.
	 */
	public function Save() {
		$this->_state = self::STATE_UNKNOWN;

		$update_id = $this->getId();
		$result = $this->getAdapter()->Save( $this );

		if ( false !== $result ) {
			$this->_state = ( ! empty( $update_id )) ? self::STATE_UPDATED : self::STATE_CREATED;
		}
		return $result;
	}

	/**
	 * Get options by prefix (notifications stored in json format).
	 *
	 * @see WSAL_Adapters_MySQL_Option::GetNotificationsSetting()
	 * @param string $opt_prefix - Prefix.
	 * @return array|null options
	 */
	public function GetNotificationsSetting( $opt_prefix ) {
		return $this->getAdapter()->GetNotificationsSetting( $opt_prefix );
	}

	/**
	 * Get option by id (notifications stored in json format).
	 *
	 * @see WSAL_Adapters_MySQL_Option::GetNotification()
	 * @param int $id - Option ID.
	 * @return string|null option
	 */
	public function GetNotification( $id ) {
		return $this->LoadData(
			$this->getAdapter()->GetNotification( $id )
		);
	}

	/**
	 * Delete option by name.
	 *
	 * @see WSAL_Adapters_MySQL_Option::DeleteByName()
	 * @param string $name - Option name.
	 * @return boolean
	 */
	public function DeleteByName( $name ) {
		return $this->getAdapter()->DeleteByName( $name );
	}

	/**
	 * Delete options start with prefix.
	 *
	 * @see WSAL_Adapters_MySQL_Option::DeleteByPrefix()
	 * @param string $opt_prefix - Prefix.
	 * @return boolean
	 */
	public function DeleteByPrefix( $opt_prefix ) {
		return $this->getAdapter()->DeleteByPrefix( $opt_prefix );
	}

	/**
	 * Number of options start with prefix.
	 *
	 * @see WSAL_Adapters_MySQL_Option::CountNotifications()
	 * @param string $opt_prefix - Prefix.
	 * @return integer Indicates the number of items.
	 */
	public function CountNotifications( $opt_prefix ) {
		return $this->getAdapter()->CountNotifications( $opt_prefix );
	}
}
