<?php
namespace AffWP\Utils\Batch_Process;

use AffWP\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements a batch process registry class.
 *
 * @since 2.0
 *
 * @see \AffWP\Utils\Registry
 */
class Registry extends Utils\Registry {

	/**
	 * Initializes the batch registry.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function init() {

		$this->includes();
		$this->register_core_processes();

		/**
		 * Fires during instantiation of the batch processing registry.
		 *
		 * @since 2.0
		 *
		 * @param \AffWP\Utils\Batch_Process\Registry $this Registry instance.
		 */
		do_action( 'affwp_batch_process_init', $this );
	}

	/**
	 * Brings in core process files.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function includes() {
		// Batch processing bootstrap.
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-batch-process.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-batch-process-with-prefetch.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-batch-process.php';

		// Exporters bootstrap.
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-base-exporter.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-csv-exporter.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-base-importer.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/interfaces/interface-csv-importer.php';

		// Importer / Exporter needed files.
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-batch-import.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-batch-import-csv.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-export.php';
		require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-export-csv.php';
	}

	/**
	 * Registers core batch processes.
	 *
	 * @access protected
	 * @since  2.0
	 */
	protected function register_core_processes() {
		//
		// Migrations
		//

		// User Migration.
		$this->register_process( 'migrate-users', array(
			'class' => 'AffWP\Utils\Batch_Process\Migrate_Users',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-batch-migrate-users.php',
		) );

		// WP Affiliate Migration.
		$this->register_process( 'migrate-wp-affiliate', array(
			'class' => 'AffWP\Utils\Batch_Process\Migrate_WP_Affiliate',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/utilities/class-batch-migrate-wp-affiliate.php',
		) );
//
//		// Affiliates Pro Migration.
//		$this->register_process( 'migrate-affiliates-pro', array(
//			'class' => 'Affiliate_WP_Migrate_Affiliates_Pro',
//		) );

		//
		// Exporters
		//

		// Export Affiliates.
		$this->register_process( 'export-affiliates', array(
			'class' => 'AffWP\Utils\Batch_Process\Export_Affiliates',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-export-affiliates.php',
		) );

		// Export Referrals.
		$this->register_process( 'export-referrals', array(
			'class' => 'AffWP\Utils\Batch_Process\Export_Referrals',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-export-referrals.php',
		) );

		// Export Payouts.
		$this->register_process( 'export-payouts', array(
			'class' => 'AffWP\Utils\Batch_Process\Export_Payouts',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-export-payouts.php',
		) );

		// Export Visits.
		$this->register_process( 'export-visits', array(
			'class' => 'AffWP\Utils\Batch_Process\Export_Visits',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-export-visits.php',
		) );

		//
		// Importers
		//
		$this->register_process( 'import-affiliates', array(
			'class' => 'AffWP\Utils\Batch_Process\Import_Affiliates',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-batch-import-affiliates.php',
		) );

		$this->register_process( 'import-referrals', array(
			'class' => 'AffWP\Utils\Batch_Process\Import_Referrals',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/import/class-batch-import-referrals.php',
		) );

		//
		// Other stuff.
		//

		// Generate Payout Log.
		$this->register_process( 'generate-payouts', array(
			'class' => 'AffWP\Utils\Batch_Process\Generate_Payouts',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-generate-payouts.php',
		) );

		// Recount all affiliate stats.
		$this->register_process( 'recount-affiliate-stats', array(
			'class' => 'AffWP\Utils\Batch_Process\Recount_Affiliate_Stats',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/class-batch-recount-affiliate-stats.php',
		) );
	}

	/**
	 * Registers a new batch process.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id     Unique batch process ID.
	 * @param array  $process_args {
	 *     Arguments for registering a new batch process.
	 *
	 *     @type string $class Batch processor class to use.
	 *     @type string $file  File containing the batch processor class.
	 * }
	 * @return \WP_Error|true True on successful registration, otherwise a WP_Error object.
	 */
	public function register_process( $batch_id, $process_args ) {
		$process_args = wp_parse_args( $process_args,  array_fill_keys( array( 'class', 'file' ), '' ) );

		if ( empty( $process_args['class'] ) ) {
			return new \WP_Error( 'invalid_batch_class', __( 'A batch process class must be specified.', 'affiliate-wp' ) );
		}

		if ( empty( $process_args['file'] ) ) {
			return new \WP_Error( 'missing_batch_class_file', __( 'No batch class handler file has been supplied.', 'affiliate-wp' ) );
		}

		// 2 if Windows path.
		if ( ! in_array( validate_file( $process_args['file'] ), array( 0, 2 ), true ) ) {
			return new \WP_Error( 'invalid_batch_class_file', __( 'An invalid batch class handler file has been supplied.', 'affiliate-wp' ) );
		}

		return $this->add_item( $batch_id, $process_args );
	}

	/**
	 * Removes a batch process from the registry by ID.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function remove_process( $batch_id ) {
		$this->remove_item( $batch_id );
	}

}
