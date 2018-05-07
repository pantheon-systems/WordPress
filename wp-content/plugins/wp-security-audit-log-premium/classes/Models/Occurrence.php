<?php
/**
 * Class: Occurrence Model Class
 *
 * Occurrence model is the model for the Occurrence adapter,
 * used for get the alert, set the meta fields, etc.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Occurrence model is the model for the Occurrence adapter,
 * used for get the alert, set the meta fields, etc.
 *
 * @package Wsal
 */
class WSAL_Models_Occurrence extends WSAL_Models_ActiveRecord {

	/**
	 * Occurrence ID.
	 *
	 * @var integer
	 */
	public $id = 0;

	/**
	 * Site ID.
	 *
	 * @var integer
	 */
	public $site_id = 0;

	/**
	 * Alert ID.
	 *
	 * @var integer
	 */
	public $alert_id = 0;

	/**
	 * Created On.
	 *
	 * @var string
	 */
	public $created_on = 0.0;

	/**
	 * Is read.
	 *
	 * @var bool
	 */
	public $is_read = false;

	/**
	 * Is migrated.
	 *
	 * @var bool
	 */
	public $is_migrated = false;

	/**
	 * Model Name.
	 *
	 * @var string
	 */
	protected $adapterName = 'Occurrence';

	/**
	 * Returns the alert related to this occurrence.
	 *
	 * @see WSAL_AlertManager::GetAlert()
	 * @return WSAL_Alert
	 */
	public function GetAlert() {
		return WpSecurityAuditLog::GetInstance()->alerts->GetAlert( $this->alert_id );
	}

	/**
	 * Returns the value of a meta item.
	 *
	 * @see WSAL_Adapters_MySQL_Occurrence::GetNamedMeta()
	 * @param string $name - Name of meta item.
	 * @param mixed  $default - Default value returned when meta does not exist.
	 * @return mixed The value, if meta item does not exist $default returned.
	 */
	public function GetMetaValue( $name, $default = array() ) {
		// Get meta adapter.
		$meta = $this->getAdapter()->GetNamedMeta( $this, $name );
		return maybe_unserialize( $meta['value'] );

		// TO DO: re-introduce add is loaded check before running query
		// return $meta->IsLoaded() ? $meta->value : $default;
	}

	/**
	 * Sets the value of a meta item (creates or updates meta item).
	 *
	 * @param string $name - Meta name.
	 * @param mixed  $value - Meta value.
	 */
	public function SetMetaValue( $name, $value ) {
		if ( ! empty( $value ) ) {
			// Get meta adapter.
			$model = new WSAL_Models_Meta();
			$model->occurrence_id = $this->getId();
			$model->name = $name;
			$model->value = maybe_serialize( $value );
			$model->SaveMeta();
		}
	}

	/**
	 * Update Metadata of this occurrence by name.
	 *
	 * @see WSAL_Models_Meta::UpdateByNameAndOccurenceId()
	 * @param string $name - Meta name.
	 * @param mixed  $value - Meta value.
	 */
	public function UpdateMetaValue( $name, $value ) {
		$model = new WSAL_Models_Meta();
		$model->UpdateByNameAndOccurenceId( $name, $value, $this->getId() );
	}

	/**
	 * Returns a key-value pair of meta data.
	 *
	 * @see WSAL_Adapters_MySQL_Occurrence::GetMultiMeta()
	 * @return array
	 */
	public function GetMetaArray() {
		$result = array();
		$metas = $this->getAdapter()->GetMultiMeta( $this );
		foreach ( $metas as $meta ) {
			$result[ $meta->name ] = maybe_unserialize( $meta->value );
		}
		return  $result;
	}

	/**
	 * Creates or updates all meta data passed as an array of meta-key/meta-value pairs.
	 *
	 * @param array $data - New meta data.
	 */
	public function SetMeta( $data ) {
		foreach ( (array) $data as $key => $val ) {
			$this->SetMetaValue( $key, $val );
		}
	}

	/**
	 * Gets alert message.
	 *
	 * @see WSAL_Alert::GetMessage()
	 * @param callable|null $meta_formatter (Optional) Meta formatter callback.
	 * @return string Full-formatted message.
	 */
	public function GetMessage( $meta_formatter = null ) {
		if ( ! isset( $this->_cachedmessage ) ) {
			// Get correct message entry.
			if ( $this->is_migrated ) {
				$this->_cachedmessage = $this->GetMetaValue( 'MigratedMesg', false );
			}
			if ( ! $this->is_migrated || ! $this->_cachedmessage ) {
				$this->_cachedmessage = $this->GetAlert()->mesg;
			}
			// Fill variables in message.
			$this->_cachedmessage = $this->GetAlert()->GetMessage( $this->GetMetaArray(), $meta_formatter, $this->_cachedmessage );
		}
		return $this->_cachedmessage;
	}

	/**
	 * Delete occurrence as well as associated meta data.
	 *
	 * @see WSAL_Adapters_ActiveRecordInterface::Delete()
	 * @return boolean True on success, false on failure.
	 */
	public function Delete() {
		foreach ( $this->getAdapter()->GetMeta() as $meta ) {
			$meta->Delete();
		}
		return parent::Delete();
	}

	/**
	 * Gets the username.
	 *
	 * @see WSAL_Adapters_MySQL_Occurrence::GetFirstNamedMeta()
	 * @return string User's username.
	 */
	public function GetUsername() {
		$meta = $this->getAdapter()->GetFirstNamedMeta( $this, array( 'Username', 'CurrentUserID' ) );
		if ( $meta ) {
			switch ( true ) {
				case 'Username' == $meta->name:
					return $meta->value;
				case 'CurrentUserID' == $meta->name:
					return ($data = get_userdata( $meta->value )) ? $data->user_login : null;
			}
		}
		return null;
	}

	/**
	 * Gets the Client IP.
	 *
	 * @return string IP address of request.
	 */
	public function GetSourceIP() {
		return $this->GetMetaValue( 'ClientIP', '' );
	}

	/**
	 * Gets if there are other IPs.
	 *
	 * @return string IP address of request (from proxies etc).
	 */
	public function GetOtherIPs() {
		$result = array();
		$data = (array) $this->GetMetaValue( 'OtherIPs', array() );
		foreach ( $data as $ips ) {
			foreach ( $ips as $ip ) {
				$result[] = $ip;
			}
		}
		return array_unique( $result );
	}

	/**
	 * Gets user roles.
	 *
	 * @return array Array of user roles.
	 */
	public function GetUserRoles() {
		return $this->GetMetaValue( 'CurrentUserRoles', array() );
	}

	/**
	 * Method: Get Microtime.
	 *
	 * @return float - Number of seconds (and microseconds as fraction) since unix Day 0.
	 * @todo This needs some caching.
	 */
	protected function GetMicrotime() {
		return microtime( true );// + get_option('gmt_offset') * HOUR_IN_SECONDS;
	}

	/**
	 * Finds occurences of the same type by IP and Username within specified time frame.
	 *
	 * @param array $args - Query args.
	 * @return WSAL_Occurrence[]
	 */
	public function CheckKnownUsers( $args = array() ) {
		return $this->getAdapter()->CheckKnownUsers( $args );
	}

	/**
	 * Finds occurences of the same type by IP within specified time frame.
	 *
	 * @param array $args - Query args.
	 * @return WSAL_Occurrence[]
	 */
	public function CheckUnKnownUsers( $args = array() ) {
		return $this->getAdapter()->CheckUnKnownUsers( $args );
	}

	/**
	 * Finds occurences of the alert 1003.
	 *
	 * @param array $args - Query args.
	 * @return WSAL_Occurrence[]
	 */
	public function check_alert_1003( $args = array() ) {
		return $this->getAdapter()->check_alert_1003( $args );
	}

	/**
	 * Gets occurrence by Post_id
	 *
	 * @see WSAL_Adapters_MySQL_Occurrence::GetByPostID()
	 * @param integer $post_id - Post ID.
	 * @return WSAL_Occurrence[]
	 */
	public function GetByPostID( $post_id ) {
		return $this->getAdapter()->GetByPostID( $post_id );
	}

	/**
	 * Gets occurences of the same type by IP within specified time frame.
	 *
	 * @see WSAL_Adapters_MySQL_Occurrence::CheckAlert404()
	 * @param array $args - Query args.
	 * @return WSAL_Occurrence[]
	 */
	public function CheckAlert404( $args = array() ) {
		return $this->getAdapter()->CheckAlert404( $args );
	}
}
