<?php

class WPML_Change_String_Language_Select {
	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @param wpdb $wpdb
	 * @param SitePress $sitepress
	 */
	public function __construct( wpdb $wpdb, SitePress $sitepress ) {
		$this->wpdb      = $wpdb;
		$this->sitepress = $sitepress;
	}


	public function show( ) {

		$lang_selector = new WPML_Simple_Language_Selector( $this->sitepress );
		echo $lang_selector->render( array(
			'id'                 => 'icl_st_change_lang_selected',
			'class'              => 'wpml-select2-button',
			'please_select_text' => __( 'Change the language of selected strings', 'wpml-string-translation' ),
			'disabled'           => true
		) );

		wp_nonce_field( 'wpml_change_string_language_nonce', 'wpml_change_string_language_nonce' );
	}

	/**
	 * @param int[] $strings
	 * @param string $lang
	 *
	 * @return array
	 */
	public function change_language_of_strings( $strings, $lang ) {
		$package_translation = new WPML_Package_Helper();
		$response = $package_translation->change_language_of_strings( $strings, $lang );
		
		if ( $response[ 'success' ] ) {
			$strings_in = implode(',', $strings);
			$update_query   = "UPDATE {$this->wpdb->prefix}icl_strings SET language=%s WHERE id IN ($strings_in)";
			$update_prepare = $this->wpdb->prepare( $update_query, $lang );
			$this->wpdb->query( $update_prepare );
		
			$response[ 'success' ] = true;
			
			foreach( $strings as $string ) {
				icl_update_string_status( $string );
			}
		}
		
		return $response;
	}
}

