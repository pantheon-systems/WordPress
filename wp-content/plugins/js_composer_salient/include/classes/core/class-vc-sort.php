<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Sort array values by key, default key is 'weight'
 * Used in uasort() function.
 * For fix equal weight problem used $this->data array_search
 *
 * @since 4.4
 */

/**
 * Class Vc_Sort
 * @since 4.4
 */
class Vc_Sort {
	/**
	 * @since 4.4
	 * @var array $data - sorting data
	 */
	protected $data = array();
	/**
	 * @since 4.4
	 * @var string $key - key for search
	 */
	protected $key = 'weight';

	/**
	 * @since 4.4
	 *
	 * @param $data - array to sort
	 */
	public function __construct( $data ) {
		$this->data = $data;
	}

	/**
	 * Used to change/set data to sort
	 *
	 * @since 4.5
	 *
	 * @param $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * Sort $this->data by user key, used in class-vc-mapper.
	 * If keys are equals it SAVES a position in array (index).
	 *
	 * @since 4.4
	 *
	 * @param string $key
	 *
	 * @return array - sorted array
	 */
	public function sortByKey( $key = 'weight' ) {
		$this->key = $key;
		uasort( $this->data, array( $this, '_key' ) );

		return array_merge( $this->data ); // reset array keys to 0..N
	}

	/**
	 * Sorting by key callable for usort function
	 * @since 4.4
	 *
	 * @param $a - compare value
	 * @param $b - compare value
	 *
	 * @return int
	 */
	private function _key( $a, $b ) {
		$a_weight = isset( $a[ $this->key ] ) ? (int) $a[ $this->key ] : 0;
		$b_weight = isset( $b[ $this->key ] ) ? (int) $b[ $this->key ] : 0;
		// To save real-ordering
		if ( $a_weight == $b_weight ) {
			$cmp_a = array_search( $a, $this->data );
			$cmp_b = array_search( $b, $this->data );

			return $cmp_a - $cmp_b;
		}

		return $b_weight - $a_weight;
	}

	/**
	 * @since 4.4
	 *
	 * @return array - sorting data
	 */
	public function getData() {
		return $this->data;
	}
}
