<?php

interface IWPML_Taxonomy_State {
	public function is_translated_taxonomy( $tax );
	public function is_display_as_translated_taxonomy( $tax );
	public function get_display_as_translated_taxonomies();
	public function get_translatable_taxonomies( $include_not_synced = false, $deprecated = 'post' );
}