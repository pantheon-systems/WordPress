<?php

/**
 * Class WPML_Admin_Pagination
 *
 * @author OnTheGoSystems
 */
class WPML_Admin_Pagination {

	/** @var int $items_per_page */
	private $items_per_page;

	/** @var  int $total_items */
	private $total_items;

	/** @var  int $current_page */
	private $current_page;

	/** @var  string $current_url */
	private $current_url;

	/** @var string  */
	private $page_param_name = 'paged';

	/**
	 * @param string $page_param_name
	 */
	public function set_page_param_name( $page_param_name ) {
		$this->page_param_name = $page_param_name;
	}

	/**
	 * @return string
	 */
	public function get_page_param_name() {
		return $this->page_param_name;
	}

	/**
	 * @param int $items_per_page
	 */
	public function set_items_per_page( $items_per_page ) {
		$this->items_per_page = (int) $items_per_page;
	}

	/**
	 * @return int
	 */
	public function get_items_per_page() {
		return $this->items_per_page;
	}

	/**
	 * @param int $total_items
	 */
	public function set_total_items( $total_items ) {
		$this->total_items = (int) $total_items;
	}

	/**
	 * @return int
	 */
	public function get_total_items() {
		return $this->total_items;
	}

	/**
	 * @return int
	 */
	public function get_total_pages() {
		return ceil( $this->get_total_items() / $this->get_items_per_page() );
	}

	/**
	 * @param int $page
	 */
	public function set_current_page( $page ) {
		$this->current_page = (int) $page;
	}

	/**
	 * @return int
	 */
	public function get_current_page() {
		return $this->current_page;
	}

	/**
	 * @return null|string
	 */
	public function get_first_page_url() {
		$url = null;

		if ( 2 < $this->get_current_page() ) {
			$url = remove_query_arg( $this->page_param_name, $this->get_current_url() );
		}

		return $url;
	}

	/**
	 * @return null|string
	 */
	public function get_previous_page_url() {
		$url = null;

		if ( 1 < $this->get_current_page() ) {
			$url = add_query_arg( $this->page_param_name, $this->get_current_page() - 1, $this->get_current_url() );
		}

		return $url;
	}

	/**
	 * @return null|string
	 */
	public function get_next_page_url() {
		$url = null;

		if ( $this->get_current_page() < $this->get_total_pages() ) {
			$url = add_query_arg( $this->page_param_name, $this->get_current_page() + 1, $this->get_current_url() );
		}

		return $url;
	}

	/**
	 * @return null|string
	 */
	public function get_last_page_url() {
		$url = null;

		if ( $this->get_current_page() < $this->get_total_pages() - 1 ) {
			$url = add_query_arg( $this->page_param_name, $this->get_total_pages(), $this->get_current_url() );
		}

		return $url;
	}

	/**
	 * @return string
	 */
	private function get_current_url() {
		if ( ! $this->current_url ) {
			$removable_query_args = wp_removable_query_args();
			$this->current_url    = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$this->current_url    = remove_query_arg( $removable_query_args, $this->current_url );
		}

		return $this->current_url;
	}
}