<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Config_Update_Log implements WPML_Log {
	const OPTION_NAME = 'wpml-xml-config-update-log';

	public function get( $page_size = 0, $page = 0 ) {
		$data = get_option( self::OPTION_NAME );
		if ( ! $data ) {
			$data = array();
		}

		return $this->paginate( $data, $page_size, $page );
	}

	/**
	 * @param string|int|float $timestamp
	 * @param array            $entry
	 */
	public function insert( $timestamp, array $entry ) {
		if ( $entry && is_array( $entry ) ) {
			$log = $this->get();
			if ( ! $log ) {
				$log = array();
			}
			$log[ (string) $timestamp ]       = $entry;
			$this->save( $log );
		}
	}

	public function clear() {
		$this->save( array() );
	}

	public function save( array $data ) {
		if ( $data === array() ) {
			delete_option( self::OPTION_NAME );

			return;
		}

		update_option( self::OPTION_NAME, $data, false );
	}

	public function is_empty() {
		return ! $this->get();
	}

	/**
	 * @param array $data
	 * @param int   $page_size
	 * @param int   $page
	 *
	 * @return array
	 */
	protected function paginate( array $data, $page_size, $page ) {
		if ( (int) $page_size > 0 ) {
			$total      = count( $data ); //total items in array
			$limit      = $page_size; //per page
			$totalPages = ceil( $total / $limit ); //calculate total pages
			$page       = max( $page, 1 ); //get 1 page when$page <= 0
			$page       = min( $page, $totalPages ); //get last page when$page > $totalPages
			$offset     = ( $page - 1 ) * $limit;
			if ( $offset < 0 ) {
				$offset = 0;
			}

			$data = array_slice( $data, $offset, $limit );
		}

		return $data;
	}

	/**
	 * @return string
	 */
	public function get_log_url() {
		return add_query_arg( array( 'page' => self::get_support_page_log_section() ), get_admin_url( null, 'admin.php#xml-config-log' ) );
	}

	/** @return string */
	public static function get_support_page_log_section() {
		return WPML_PLUGIN_FOLDER . '/menu/support.php';
	}
}
