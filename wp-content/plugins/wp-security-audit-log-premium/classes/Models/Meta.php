<?php
/**
 * Class: Meta Model Class
 *
 * Metadata model is the model for the Metadata adapter,
 * used for save and update the metadata.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Metadata model is the model for the Metadata adapter,
 * used for save and update the metadata.
 *
 * @package Wsal
 */
class WSAL_Models_Meta extends WSAL_Models_ActiveRecord {

	/**
	 * Meta ID.
	 *
	 * @var integer
	 */
	public $id = 0;

	/**
	 * Occurrence ID.
	 *
	 * @var integer
	 */
	public $occurrence_id = 0;

	/**
	 * Meta Name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Meta Value.
	 *
	 * @var array
	 */
	public $value = array(); // Force mixed type.

	/**
	 * Model Name.
	 *
	 * @var string
	 */
	protected $adapterName = 'Meta';

	/**
	 * Save Metadata into Adapter.
	 *
	 * @see WSAL_Adapters_MySQL_ActiveRecord::Save()
	 * @return integer|boolean Either the number of modified/inserted rows or false on failure.
	 */
	public function SaveMeta() {
		$this->_state = self::STATE_UNKNOWN;
		$update_id = $this->getId();
		$result = $this->getAdapter()->Save( $this );

		if ( false !== $result ) {
			$this->_state = ( ! empty( $update_id )) ? self::STATE_UPDATED : self::STATE_CREATED;
		}
		return $result;
	}

	/**
	 * Update Metadata by name and occurrence_id.
	 *
	 * @see WSAL_Adapters_MySQL_Meta::LoadByNameAndOccurenceId()
	 * @param string  $name - Meta name.
	 * @param mixed   $value - Meta value.
	 * @param integer $occurrence_id - Occurrence_id.
	 */
	public function UpdateByNameAndOccurenceId( $name, $value, $occurrence_id ) {
		$meta = $this->getAdapter()->LoadByNameAndOccurenceId( $name, $occurrence_id );
		if ( ! empty( $meta ) ) {
			$this->id = $meta['id'];
			$this->occurrence_id = $meta['occurrence_id'];
			$this->name = $meta['name'];
			$this->value = maybe_serialize( $value );
			$this->saveMeta();
		} else {
			$this->occurrence_id = $occurrence_id;
			$this->name = $name;
			$this->value = maybe_serialize( $value );
			$this->SaveMeta();
		}
	}
}
