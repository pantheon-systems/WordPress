<?php

function translation_service_details( $service, $show_project = false ) {
	$service_details = '';
	if (defined( 'OTG_SANDBOX_DEBUG' ) && OTG_SANDBOX_DEBUG ) {
		$service_details .= '<h3>Service details:</h3>' . PHP_EOL;
		$service_details .= '<pre>' . PHP_EOL;
		$service_details .= print_r( $service, true );
		$service_details .= '</pre>' . PHP_EOL;

		if($show_project) {
			$project = TranslationProxy::get_current_project();
			echo '<pre>$project' . PHP_EOL;
			echo print_r( $project, true );
			echo '</pre>';
		}
	}

	return $service_details;
}

if ( !function_exists( 'object_to_array' ) ) {
	function object_to_array( $obj ) {
		if ( is_object( $obj ) ) {
			$obj = (array) $obj;
		}
		if ( is_array( $obj ) ) {
			$new = array();
			foreach ( $obj as $key => $val ) {
				$new[ $key ] = object_to_array( $val );
			}
		} else {
			$new = $obj;
		}

		return $new;
	}
}
