<?php

/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 20/04/17
 * Time: 11:39 AM
 */
class WPML_TM_WP_Query extends WP_Query {

	public function get_found_count() {
		return $this->found_posts;
	}
}

