<?php

class WPML_WP_Cache_Factory {

	public function create_cache_group( $group  ) {
		return new WPML_WP_Cache( $group );
	}

	public function create_cache_item( $group, $key ) {
		return new WPML_WP_Cache_Item( $this->create_cache_group( $group ), $key );
	}

}
