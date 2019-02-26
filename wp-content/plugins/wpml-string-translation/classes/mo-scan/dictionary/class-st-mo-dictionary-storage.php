<?php

interface WPML_ST_MO_Dictionary_Storage {

	public function save( WPML_ST_MO_File $file );

	/**
	 * @param null|string $path
	 * @param null|string $status
	 *
	 * @return WPML_ST_MO_File[]
	 */
	public function find( $path = null, $status = null );
}