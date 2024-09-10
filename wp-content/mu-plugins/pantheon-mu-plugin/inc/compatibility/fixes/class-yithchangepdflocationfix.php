<?php
/**
 * Changes PDF cache location for YITH WooCommerce extensions.
 *
 * @link https://docs.pantheon.io/plugins-known-issues#yith-woocommerce-extensions-with-mpdf-library
 * @package Pantheon\Compatibility\Fixes
 */

namespace Pantheon\Compatibility\Fixes;

/**
 * Class YITHChangePdfLocationFix
 */
class YITHChangePdfLocationFix {
	/**
	 * @return void
	 */
	public static function apply() {
		add_filter( 'ywraq_mpdf_args', [ self::class, 'yith_mpdf_change_tmp_dir' ], 20, 1 );

		add_filter( 'yith_ywpdi_mpdf_args', [ self::class, 'yith_mpdf_change_tmp_dir' ], 10, 1 );

		add_filter( 'yith_ywgc_mpdf_args', [ self::class, 'yith_mpdf_change_tmp_dir' ], 10, 1 );
	}

	/**
	 * @return void
	 */
	public static function remove() {}

	/**
	 * Changes PDF cache location for YITH WooCommerce extensions.
	 *
	 * @param array $args The configuration for MPDF initialization.
	 *
	 * @return array The updated config with writable path.
	 */
	public function yith_mpdf_change_tmp_dir( array $args ): array {
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['basedir'];
		$args['tempDir'] = $upload_dir . '/yith-mpdf-tmp/';

		return $args;
	}
}
