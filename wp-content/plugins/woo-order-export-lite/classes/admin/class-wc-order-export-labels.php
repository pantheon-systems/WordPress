<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class WC_Order_Export_Labels {

	private $labels;

	public function __get( $key ) {
		if ( ! $key || empty( $this->labels ) ) {
			return false;
		}

		foreach ( $this->labels as $label_data ) {
			if ( $label_data['key'] == $key ) {
				return $label_data;
			}
		}

		return false;
	}

	public function __isset( $key ) {
		if ( ! $key || empty( $this->labels ) ) {
			return false;
		}

		foreach ( $this->labels as $label_data ) {
			if ( $label_data['key'] == $key ) {
				return $label_data;
			}
		}

		return false;
	}

	public function __unset( $key ) {
		if ( ! $key || empty( $this->labels ) ) {
			return false;
		}

		foreach ( $this->labels as $num_index => $label_data ) {
			if ( $label_data['key'] === $key || $label_data['parent_key'] === $key ) {
				unset( $this->labels[ $num_index ] );
			}
		}

		$this->labels = array_values( $this->labels );
	}

	public function __set( $key, $label ) {
		if ( ! $key ) {
			return;
		}

		$temp_index = 0;
		$new_key    = $key;
		while ( $this->__isset( $new_key ) ) {
			$new_key = $temp_index ? $key . '_' . $temp_index : $new_key;
			$temp_index ++;
		}

		$parent_key = $new_key !== $key ? $key : false;

		$this->labels[] = array(
			'key'        => $new_key,
			'label'      => $label,
			'parent_key' => $parent_key,
		);
	}

	public function get_keys() {
		return array_map( function ( $label_data ) {
			return $label_data['key'];
		}, $this->labels );
	}

	public function is_not_empty() {
		return (boolean) $this->labels;
	}

	public function to_Array() {
		return array_combine(
			array_map( function ( $label_data ) {
				return $label_data['key'];
			}, $this->labels ),
			array_map( function ( $label_data ) {
				return $label_data['label'];
			}, $this->labels )
		);
	}

	public function unique_keys() {
		$unique_keys = array();
		foreach ( $this->labels as $label_data ) {
			if ( ! $label_data['parent_key'] ) {
				$unique_keys[] = $label_data['key'];
			}
		}

		return $unique_keys;
	}

	public function get_childs( $key ) {
		$child_labels = array();
		foreach ( $this->labels as $label_data ) {
			if ( $label_data['parent_key'] == $key ) {
				$child_labels[] = $label_data;
			}
		}

		return $child_labels;
	}

	public function get_parent( $key ) {
		foreach ( $this->labels as $label_data ) {
			if ( $label_data['key'] == $key ) {
				return $label_data['parent_key'];
			}
		}

		return false;
	}

	public function replace_label( $key, $new_label ) {
		if ( ! $key || empty( $this->labels ) ) {
			return;
		}

		foreach ( $this->labels as &$label_data ) {
			if ( $label_data['key'] == $key || $label_data['parent_key'] === $key ) {
				$label_data['label'] = $new_label;
			}
		}
	}

	public function get_labels() {
		return $this->labels;
	}

	public function get_fetch_fields() {
		$fetch_fields = array();
		foreach ( $this->labels as $label_data ) {
			if ( ! $label_data['parent_key'] AND ! preg_match( '/^plain_(products|coupons)_.+/',
					$label_data['key'] ) ) {
				$fetch_fields[] = $label_data['key'];
			}
		}

		return $fetch_fields;
	}

	public function get_legacy_labels() {
		$unique_keys = array();
		foreach ( $this->labels as $label_data ) {
			if ( ! $label_data['parent_key'] ) {
				$unique_keys[ $label_data["key"] ] = $label_data["label"];
			}
		}

		return $unique_keys;
	}
}