<?php

class WPML_Element_Sync_Settings_Factory {

	const POST = 'post';
	const TAX  = 'taxonomy';

	const KEY_POST_SYNC_OPTION = 'custom_posts_sync_option';
	const KEY_TAX_SYNC_OPTION  = 'taxonomies_sync_option';

	/**
	 * @param string $type
	 *
	 * @return WPML_Element_Sync_Settings
	 * @throws Exception
	 */
	public function create( $type ) {
		/** @var SitePress $sitepress */
		global $sitepress;

		if ( self::POST === $type ) {
			$settings = $sitepress->get_setting( self::KEY_POST_SYNC_OPTION, array() );
		} elseif ( self::TAX === $type ) {
			$settings = $sitepress->get_setting( self::KEY_TAX_SYNC_OPTION, array() );
		} else {
			throw new Exception( 'Unknown element type.' );
		}

		return new WPML_Element_Sync_Settings( $settings );
	}
}
