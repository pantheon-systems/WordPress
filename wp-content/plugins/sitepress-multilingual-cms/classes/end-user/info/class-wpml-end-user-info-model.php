<?php

class WPML_End_User_Info_Model {
	/**
	 * @param array $input
	 *
	 * @return array
	 */
	public function get( $input ) {
		$result = array();

		foreach ( $input as $key => $data ) {
			if ( is_array( $data ) ) {
				$result[ $key ] = $data;
			} elseif ( $data instanceof WPML_End_User_Info ) {
				$result[ $key ] = $data->to_array();
			}
		}

		return $result;
	}
}
