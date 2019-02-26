<?php

interface WPML_ST_MO_Components_Find {
	/**
	 * @param string $mo_file
	 *
	 * @return string|null
	 */
	public function find_id( $mo_file );
}
