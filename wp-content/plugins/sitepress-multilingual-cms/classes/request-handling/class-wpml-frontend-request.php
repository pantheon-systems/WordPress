<?php

/**
 * Class WPML_Frontend_Request
 *
 * @package    wpml-core
 * @subpackage wpml-requests
 */
class WPML_Frontend_Request extends WPML_Request {
	public function get_requested_lang() {
		return $this->wp_api->is_comments_post_page() ? $this->get_comment_language() : $this->get_request_uri_lang();
	}

	protected function get_cookie_name() {

		return '_icl_current_language';
	}
}