<?php

class WCML_Tracking_Link {

	public function generate( $link, $term = false, $content = false, $id = false ) {
		$params = array(
			'utm_source'   => 'wcml-admin',
			'utm_medium'   => 'plugin',
			'utm_term'     => $term ? $term : 'WPML',
			'utm_content'  => $content ? $content : 'required-plugins',
			'utm_campaign' => 'WCML',
		);

		$link = add_query_arg( $params, $link );

		if ( $id ) {
			$link .= $id;
		}

		return $link;
	}
}