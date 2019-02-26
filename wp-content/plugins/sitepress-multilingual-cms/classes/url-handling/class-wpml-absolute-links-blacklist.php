<?php

class WPML_Absolute_Links_Blacklist {

	private $blacklist_requests;

	public function __construct( $blacklist_requests ) {
		$this->blacklist_requests = $blacklist_requests;
		if ( ! is_array( $this->blacklist_requests ) ) {
			$this->blacklist_requests = array();
		}
	}

	public function is_blacklisted( $request ) {
		$blacklisted = in_array( $request, $this->blacklist_requests, true );
		if ( $blacklisted ) {
			return true;
		}

		return $this->is_blacklisted_with_regex( $request );
	}

	private function is_blacklisted_with_regex( $request ) {
		foreach ( $this->blacklist_requests as $blacklist_request ) {
			if ( $this->is_regex( $blacklist_request ) && preg_match( $blacklist_request, $request ) ) {
				return true;
			}
		}
		return false;
	}

	private function is_regex( $blacklist_request ) {
		return strpos( $blacklist_request, '/' ) === 0 && strrpos( $blacklist_request, '/' ) === strlen( $blacklist_request ) - 1;
	}
}