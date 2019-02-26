<?php

class WPML_Core_Version_Check {

	public static function is_ok( $package_file_path ) {

		$is_ok = false;

		/** @var array $bundle */
		$bundle = json_decode( file_get_contents( $package_file_path ), true );
		if ( defined( 'ICL_SITEPRESS_VERSION' ) && is_array( $bundle ) ) {
			$core_version_stripped = ICL_SITEPRESS_VERSION;
			$dev_or_beta_pos     = strpos( ICL_SITEPRESS_VERSION, '-' );
			if ( $dev_or_beta_pos > 0 ) {
				$core_version_stripped = substr( ICL_SITEPRESS_VERSION, 0, $dev_or_beta_pos );
			}
			if ( version_compare( $core_version_stripped, $bundle['sitepress-multilingual-cms'], '>=' ) ) {
				$is_ok = true;
			}
		}

		return $is_ok;
	}
}