<?php
/**
 * Class used for Constants,
 * E_NOTICE, E_WARNING, E_CRITICAL, etc.
 *
 * @package Wsal
 */
class WSAL_ConstantManager {

	/**
	 * Constants array.
	 *
	 * @var array
	 */
	protected $_constants = array();

	/**
	 * Use an existing PHP constant.
	 *
	 * @param string $name - Constant name.
	 * @param string $description - Constant description.
	 */
	public function UseConstant( $name, $description = '' ) {
		$this->_constants[] = (object) array(
			'name' => $name,
			'value' => constant( $name ),
			'description' => $description,
		);
	}

	/**
	 * Add new PHP constant.
	 *
	 * @param string         $name - Constant name.
	 * @param integer|string $value - Constant value.
	 * @param string         $description - Constant description.
	 * @throws string - Error if a constant is already defined.
	 */
	public function AddConstant( $name, $value, $description = '' ) {
		// Check for constant conflict and define new one if required.
		if ( defined( $name ) && constant( $name ) !== $value ) {
			throw new Exception( 'Constant already defined with a different value.' );
		} else {
			define( $name, $value );
		}
		// Add constant to da list.
		$this->UseConstant( $name, $description );
	}

	/**
	 * Add multiple constants in one go.
	 *
	 * @param array $items - Array of arrays with name, value, description pairs.
	 */
	public function AddConstants( $items ) {
		foreach ( $items as $item ) {
			$this->AddConstant(
				$item['name'],
				$item['value'],
				$item['description']
			);
		}
	}

	/**
	 * Use multiple constants in one go.
	 *
	 * @param array $items - Array of arrays with name, description pairs.
	 */
	public function UseConstants( $items ) {
		foreach ( $items as $item ) {
			$this->UseConstant(
				$item['name'],
				$item['description']
			);
		}
	}

	/**
	 * Get constant details by a particular detail.
	 *
	 * @param string $what - The type of detail: 'name', 'value'.
	 * @param mixed  $value - The detail expected value.
	 * @param mix    $default - Default value of constant.
	 * @throws string - Error if detail type is unexpected.
	 * @return mixed Either constant details (props: name, value, description) or $default if not found.
	 */
	public function GetConstantBy( $what, $value, $default = null ) {
		// Make sure we do have some constants.
		if ( count( $this->_constants ) ) {
			// Make sure that constants do have a $what property.
			if ( ! isset( $this->_constants[0]->$what ) ) {
				throw new Exception( 'Unexpected detail type "' . $what . '".' );
			}
			// Return constant match the property value.
			foreach ( $this->_constants as $constant ) {
				if ( $constant->$what == $value ) {
					return $constant;
				}
			}
		}
		return $default;
	}
}
