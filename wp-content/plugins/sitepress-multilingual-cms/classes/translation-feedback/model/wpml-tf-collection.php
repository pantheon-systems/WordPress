<?php

/**
 * Class WPML_TF_Collection
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Collection implements Iterator, Countable {

	/** @var  IWPML_TF_Data_Object[] */
	protected $collection = array();

	/**
	 * @param IWPML_TF_Data_Object $data_object
	 */
	public function add( IWPML_TF_Data_Object $data_object ) {
		$this->collection[ $data_object->get_id() ] = $data_object;
	}

	/**
	 * @return array
	 */
	public function get_ids() {
		return array_keys( $this->collection );
	}

	/**
	 * @param $id
	 *
	 * @return IWPML_TF_Data_Object|null
	 */
	public function get( $id ) {
		return array_key_exists( $id, $this->collection ) ? $this->collection[ $id ] : null;
	}

	/**
	 * @return int
	 */
	public function count() {
		return count( $this->collection );
	}

	public function rewind() {
		reset( $this->collection );
	}

	/**
	 * @return mixed
	 */
	public function current() {
		return current( $this->collection );
	}

	/**
	 * @return mixed
	 */
	public function key() {
		return key( $this->collection );
	}

	public function next() {
		next( $this->collection );
	}

	/**
	 * @return bool
	 */
	public function valid() {
		return key( $this->collection ) !== null;
	}
}