<?php

class WPML_TF_Collection_Filter_Factory {

	/**
	 * @param       $type
	 * @param array $args
	 *
	 * @return null|IWPML_TF_Collection_Filter
	 */
	public function create( $type, array $args = array() ) {
		$collection_filter = null;

		switch ( $type ) {
			case 'feedback':
				$collection_filter = new WPML_TF_Feedback_Collection_Filter( $args );
				break;

			case 'message':
				$collection_filter = new WPML_TF_Message_Collection_Filter( $args );
				break;
		}

		return $collection_filter;
	}
}