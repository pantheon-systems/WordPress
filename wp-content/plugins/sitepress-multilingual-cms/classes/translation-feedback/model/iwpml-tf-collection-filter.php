<?php

/**
 * Interface IWPML_TF_Collection_Filter
 *
 * @author OnTheGoSystems
 */
interface IWPML_TF_Collection_Filter {

	/**
	 * @return array
	 */
	public function get_posts_args();

	/**
	 * @return WPML_TF_Collection
	 */
	public function get_new_collection();
}