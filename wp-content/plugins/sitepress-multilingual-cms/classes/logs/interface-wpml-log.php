<?php

/**
 * @author OnTheGo Systems
 */
interface WPML_Log {
	public function insert( $timestamp, array $entry );

	public function get( $page_size = 0, $page = 0 );

	public function save( array $data );

	public function clear();
	
	public function is_empty();
}