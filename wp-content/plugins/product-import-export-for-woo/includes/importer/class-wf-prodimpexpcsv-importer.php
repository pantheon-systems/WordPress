<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_ProdImpExpCsv_Importer {

	/**
	 * Product Exporter Tool
	 */
	public static function load_wp_importer() {
		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				require $class_wp_importer;
			}
		}
	}

	/**
	 * Product Importer Tool
	 */
	public static function product_importer() {
		if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
			return;
		}

		self::load_wp_importer();

		// includes
		require_once 'class-wf-prodimpexpcsv-product-import.php';
		require_once 'class-wf-csv-parser.php';

		// Dispatch
		$GLOBALS['WF_CSV_Product_Import'] = new WF_ProdImpExpCsv_Product_Import();
		$GLOBALS['WF_CSV_Product_Import'] ->dispatch();
	}
}