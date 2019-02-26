<?php

class WPML_TM_Array_Search {

	/**
	 * @var array
	 */
	private $where;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @param array $data
	 *
	 * @return $this
	 */
	public function set_data( $data ) {
		$this->data = $data;
		return $this;
	}

	/**
	 * @param array $args
	 *
	 * @return $this
	 */
	public function set_where( $args ) {
		$this->where = $args;
		return $this;
	}

	/**
	 * @return array
	 */
	public function get_results() {
		$results = array();

		foreach( $this->where as $key => $clause ) {
			$operator = isset ( $clause['operator'] ) ? strtoupper( $clause['operator'] ) : 'LIKE';

			foreach ( $this->data as $data ) {

				if ( in_array( $data, $results, true ) ) {
					continue;
				}

				$field_value = '';

				if ( is_object( $data ) ) {
					$field_value = $data->{$clause['field']};
				} else if ( is_array( $data ) ) {
					$field_value = $data[ $clause['field'] ];
				}

				switch ( $operator ) {
					default:
					case 'LIKE':
						if ( false !== strpos( strtolower( $field_value ), strtolower( $clause['value'] ) ) ) {
							$results[] = $data;
						}
						break;
					case '=':
						if ( strlen( $field_value ) === strlen( $clause['value'] ) &&
						     0 === strpos( strtolower( $field_value ), strtolower( $clause['value'] ) ) ) {
							$results[] = $data;
						}
				}
			}
		}

		return array_values( $results );
	}
}