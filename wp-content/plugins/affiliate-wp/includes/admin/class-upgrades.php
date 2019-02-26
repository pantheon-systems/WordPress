<?php

class Affiliate_WP_Upgrades {

	/**
	 * Whether debug mode is enabled.
	 *
	 * @since 1.8.6
	 * @access private
	 * @var bool
	 */
	private $debug;

	/**
	 * Affiliate_WP_Logging instance.
	 *
	 * @since 1.8.6
	 * @access private
	 * @var Affiliate_WP_Logging
	 */
	private $logs;

	/**
	 * Signals whether the upgrade was successful.
	 *
	 * @access public
	 * @var    bool
	 */
	private $upgraded = false;

	/**
	 * AffiliateWP version.
	 *
	 * @access private
	 * @since  2.0
	 * @var    string
	 */
	private $version;

	/**
	 * Utilities class instance.
	 *
	 * @access private
	 * @since  2.0
	 * @var    \Affiliate_WP_Utilities
	 */
	private $utils;

	/**
	 * Upgrade routine registry.
	 *
	 * @access private
	 * @since  2.0.5
	 * @var    \AffWP\Utils\Upgrades\Registry
	 */
	private $registry;

	/**
	 * Sets up the Upgrades class instance.
	 *
	 * @access public
	 *
	 * @param \Affiliate_WP_Utilities $utils Utilities class instance.
	 */
	public function __construct( $utils ) {

		$this->utils    = $utils;
		$this->version  = get_option( 'affwp_version' );
		$this->registry = new \AffWP\Utils\Upgrades\Registry;

		add_action( 'admin_init', array( $this, 'init' ), -9999 );

		$settings = new Affiliate_WP_Settings;
		$this->debug = (bool) $settings->get( 'debug_mode', false );

		$this->register_core_upgrades();
	}

	/**
	 * Initializes upgrade routines for the current version of AffiliateWP.
	 *
	 * @access public
	 */
	public function init() {

		if ( empty( $this->version ) ) {
			$this->version = '1.0.6'; // last version that didn't have the version option set
		}

		if ( version_compare( $this->version, '1.1', '<' ) ) {
			$this->v11_upgrades();
		}

		if ( version_compare( $this->version, '1.2.1', '<' ) ) {
			$this->v121_upgrades();
		}

		if ( version_compare( $this->version, '1.3', '<' ) ) {
			$this->v13_upgrades();
		}

		if ( version_compare( $this->version, '1.6', '<' ) ) {
			$this->v16_upgrades();
		}

		if ( version_compare( $this->version, '1.7', '<' ) ) {
			$this->v17_upgrades();
		}

		if ( version_compare( $this->version, '1.7.3', '<' ) ) {
			$this->v173_upgrades();
		}

		if ( version_compare( $this->version, '1.7.11', '<' ) ) {
			$this->v1711_upgrades();
		}

		if ( version_compare( $this->version, '1.7.14', '<' ) ) {
			$this->v1714_upgrades();
		}

		if ( version_compare( $this->version, '1.9', '<' ) ) {
			$this->v19_upgrade();
		}

		if ( version_compare( $this->version, '1.9.5', '<' ) ) {
			$this->v195_upgrade();
		}

		if ( true === version_compare( AFFILIATEWP_VERSION, '2.0', '<' ) ) {
			$this->v20_upgrade();
		}

		if ( version_compare( $this->version, '2.0.2', '<' ) ) {
			$this->v202_upgrade();
		}

		if ( version_compare( $this->version, '2.0.10', '<' ) ) {
			$this->v210_upgrade();
		}

		if ( version_compare( $this->version, '2.1', '<' ) ) {
			$this->v21_upgrade();
		}

		if ( version_compare( $this->version, '2.1.3.1', '<' ) ) {
			$this->v2131_upgrade();
		}

		// Inconsistency between current and saved version.
		if ( version_compare( $this->version, AFFILIATEWP_VERSION, '<>' ) ) {
			$this->upgraded = true;
		}

		// If upgrades have occurred
		if ( $this->upgraded ) {
			update_option( 'affwp_version_upgraded_from', $this->version );
			update_option( 'affwp_version', AFFILIATEWP_VERSION );
		}

	}

	/**
	 * Registers core upgrade routines.
	 *
	 * @access private
	 * @since  2.0.5
	 *
	 * @see \Affiliate_WP_Upgrades::add_routine()
	 */
	private function register_core_upgrades() {
		$this->add_routine( 'upgrade_v20_recount_unpaid_earnings', array(
			'version' => '2.0',
			'compare' => '<',
			'batch_process' => array(
				'id'    => 'recount-affiliate-stats-upgrade',
				'class' => 'AffWP\Utils\Batch_Process\Upgrade_Recount_Stats',
				'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/upgrades/class-batch-upgrade-recount-affiliate-stats.php'
			)
		) );

	}

	/**
	 * Registers a new upgrade routine.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param string $upgrade_id Upgrade ID.
	 * @param array  $args {
	 *     Arguments for registering a new upgrade routine.
	 *
	 *     @type string $version       Version the upgrade routine should be run against.
	 *     @type string $compare       Comparison operator to use when determining if the routine
	 *                                 should be executed.
	 *     @type array  $batch_process {
	 *         Optional. Arguments for registering a batch process.
	 *
	 *         @type string $id    Batch process ID.
	 *         @type string $class Batch processor class to use.
	 *         @type string $file  File containing the batch processor class.
	 *     }
	 * }
	 * @return bool True if the upgrade routine was added, otherwise false.
	 */
	public function add_routine( $upgrade_id, $args ) {
		// Register the batch process if one has been defined.
		if ( ! empty( $args['batch_process'] ) ) {

			$utils = $this->utils;
			$batch = $args['batch_process'];

			// Log an error if it's too late to register the batch process.
			if ( did_action( 'affwp_batch_process_init' ) ) {

				$utils->log( sprintf( 'The %s batch process was registered too late. Registrations must occur while/before <code>affwp_batch_process_init</code> fires.',
					esc_html( $args['batch_process']['id'] )
				) );

				return false;

			} else {

				add_action( 'affwp_batch_process_init', function() use ( $utils, $batch ) {
					$utils->batch->register_process( $batch['id'], array(
						'class' => $batch['class'],
						'file'  => $batch['file'],
					) );
				} );

			}

			unset( $args['batch_process'] );
		}

		// Add the routine to the registry.
		return $this->registry->add_upgrade( $upgrade_id, $args );
	}

	/**
	 * Retrieves an upgrade routine from the registry.
	 *
	 * @access public
	 * @since  2.0.5
	 *
	 * @param string $upgrade_id Upgrade ID.
	 * @return array|false Upgrade entry from the registry, otherwise false.
	 */
	public function get_routine( $upgrade_id ) {
		return $this->registry->get( $upgrade_id );
	}

	/**
	 * Writes a log message.
	 *
	 * @access private
	 * @since 1.8.6
	 *
	 * @param string $message Optional. Message to log.
	 */
	private function log( $message = '' ) {
		$this->utils->log( $message );
	}

	/**
	 * Perform database upgrades for version 1.1
	 *
	 * @access  private
	 * @since   1.1
	*/
	private function v11_upgrades() {

		@affiliate_wp()->affiliates->create_table();

		$this->upgraded = true;

	}

	/**
	 * Perform database upgrades for version 1.2.1
	 *
	 * @access  private
	 * @since   1.2.1
	*/
	private function v121_upgrades() {

		@affiliate_wp()->creatives->create_table();

		$this->upgraded = true;

	}

	/**
	 * Perform database upgrades for version 1.3
	 *
	 * @access  private
	 * @since   1.3
	 */
	private function v13_upgrades() {

		@affiliate_wp()->creatives->create_table();

		// Clear rewrite rules
		flush_rewrite_rules();

		$this->upgraded = true;

	}

	/**
	 * Perform database upgrades for version 1.6
	 *
	 * @access  private
	 * @since   1.6
	 */
	private function v16_upgrades() {

		@affiliate_wp()->affiliate_meta->create_table();
		@affiliate_wp()->referrals->create_table();

		$this->upgraded = true;

	}

	/**
	 * Perform database upgrades for version 1.7
	 *
	 * @access  private
	 * @since   1.7
	 */
	private function v17_upgrades() {

		@affiliate_wp()->referrals->create_table();
		@affiliate_wp()->visits->create_table();
		@affiliate_wp()->campaigns->create_view();

		$this->v17_upgrade_referral_rates();

		$this->v17_upgrade_gforms();

		$this->v17_upgrade_nforms();

		$this->upgraded = true;

	}

	/**
	 * Perform database upgrades for version 1.7.3
	 *
	 * @access  private
	 * @since   1.7.3
	 */
	private function v173_upgrades() {

		$this->v17_upgrade_referral_rates();

		$this->upgraded = true;

	}

	/**
	 * Perform database upgrades for referral rates in version 1.7
	 *
	 * @access  private
	 * @since   1.7
	 */
	private function v17_upgrade_referral_rates() {

		global $wpdb;

		$prefix  = ( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) ? null : $wpdb->prefix;
		$results = $wpdb->get_results( "SELECT affiliate_id, rate FROM {$prefix}affiliate_wp_affiliates WHERE rate_type = 'percentage' AND rate > 0 AND rate <= 1;" );

		if ( $results ) {
			foreach ( $results as $result ) {
				$wpdb->update(
					"{$prefix}affiliate_wp_affiliates",
					array( 'rate' => floatval( $result->rate ) * 100 ),
					array( 'affiliate_id' => $result->affiliate_id ),
					array( '%d' ),
					array( '%d' )
				);
			}
		}

		$settings  = get_option( 'affwp_settings' );
		$rate_type = ! empty( $settings['referral_rate_type'] ) ? $settings['referral_rate_type'] : null;
		$rate      = isset( $settings['referral_rate'] ) ? $settings['referral_rate'] : 20;

		if ( 'percentage' !== $rate_type ) {
			return;
		}

		if ( $rate > 0 && $rate <= 1 ) {
			$settings['referral_rate'] = floatval( $rate ) * 100;
		} elseif ( '' === $rate || '0' === $rate || '0.00' === $rate ) {
			$settings['referral_rate'] = 0;
		} else {
			$settings['referral_rate'] = floatval( $rate );
		}

		// Update settings.
		affiliate_wp()->settings->set( $settings, $save = true );
	}

	/**
	 * Perform database upgrades for Gravity Forms in version 1.7
	 *
	 * @access  private
	 * @since   1.7
	 */
	private function v17_upgrade_gforms() {

		$settings = get_option( 'affwp_settings' );

		if ( empty( $settings['integrations'] ) || ! array_key_exists( 'gravityforms', $settings['integrations'] ) ) {
			return;
		}

		global $wpdb;

		$tables = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}rg_form%';" );

		if ( ! $tables ) {
			return;
		}

		$forms = $wpdb->get_results( "SELECT id FROM {$wpdb->prefix}rg_form;" );

		if ( ! $forms ) {
			return;
		}

		foreach ( $forms as $form ) {

			$meta = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT display_meta FROM {$wpdb->prefix}rg_form_meta WHERE form_id = %d;",
					$form->id
				)
			);

			$meta = json_decode( $meta );

			if ( isset( $meta->gform_allow_referrals ) ) {
				continue;
			}

			$meta->gform_allow_referrals = 1;

			$meta = json_encode( $meta );

			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$wpdb->prefix}rg_form_meta SET display_meta = %s WHERE form_id = %d;",
					$meta,
					$form->id
				)
			);

		}

	}

	/**
	 * Perform database upgrades for Ninja Forms in version 1.7
	 *
	 * @access  private
	 * @since   1.7
	 */
	private function v17_upgrade_nforms() {

		$settings = get_option( 'affwp_settings' );

		if ( empty( $settings['integrations'] ) || ! array_key_exists( 'ninja-forms', $settings['integrations'] ) ) {
			return;
		}

		global $wpdb;

		$tables = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}nf_object%';" );

		if ( ! $tables ) {
			return;
		}

		$forms = $wpdb->get_results( "SELECT id FROM {$wpdb->prefix}nf_objects WHERE type = 'form';" );

		if ( ! $forms ) {
			return;
		}

		// There could be forms that already have this meta saved in the DB, we will ignore those
		$_forms = $wpdb->get_results( "SELECT object_id FROM {$wpdb->prefix}nf_objectmeta WHERE meta_key = 'affwp_allow_referrals';" );

		$forms  = wp_list_pluck( $forms, 'id' );
		$_forms = wp_list_pluck( $_forms, 'object_id' );
		$forms  = array_diff( $forms, $_forms );

		if ( ! $forms ) {
			return;
		}

		foreach ( $forms as $form_id ) {

			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO {$wpdb->prefix}nf_objectmeta (object_id,meta_key,meta_value) VALUES (%d,'affwp_allow_referrals','1');",
					$form_id
				)
			);

		}

	}

	/**
	 * Perform database upgrades for version 1.7.11
	 *
	 * @access  private
	 * @since   1.7.11
	 */
	private function v1711_upgrades() {

		$settings = affiliate_wp()->settings->get_all();

		// Ensures settings are not lost if the duplicate email/subject fields were used before they were removed
		if( ! empty( $settings['rejected_email'] ) && empty( $settings['rejection_email'] ) ) {
			$settings['rejection_email'] = $settings['rejected_email'];
			unset( $settings['rejected_email'] );
		}

		if( ! empty( $settings['rejected_subject'] ) && empty( $settings['rejection_subject'] ) ) {
			$settings['rejection_subject'] = $settings['rejected_subject'];
			unset( $settings['rejected_subject'] );
		}

		// Update settings.
		affiliate_wp()->settings->set( $settings, $save = true );

		$this->upgraded = true;

	}

	/**
	 * Perform database upgrades for version 1.7.14
	 *
	 * @access  private
	 * @since   1.7.14
	 */
	private function v1714_upgrades() {

		@affiliate_wp()->visits->create_table();

		$this->upgraded = true;

	}

	/**
	 * Performs database upgrades for version 1.9.
	 *
	 * @since 1.9
	 * @access private
	 */
	private function v19_upgrade() {
		@affiliate_wp()->referrals->create_table();
		@affiliate_wp()->utils->log( 'Upgrade: The Referrals table upgrade for 1.9 has completed.' );

		@affiliate_wp()->affiliates->payouts->create_table();
		@affiliate_wp()->utils->log( 'Upgrade: The Payouts table creation process for 1.9 has completed.' );

		@affiliate_wp()->REST->consumers->create_table();
		@affiliate_wp()->utils->log( 'Upgrade: The API consumers table creation process for 1.9 has completed' );

		$this->upgraded = true;
	}

	/**
	 * Performs database upgrades for version 1.9.5.
	 *
	 * @since 1.9.5
	 * @access private
	 */
	private function v195_upgrade() {
		@affiliate_wp()->affiliates->payouts->create_table();
		@affiliate_wp()->utils->log( 'Upgrade: The Payouts table upgrade for 1.9.5 has completed.' );

		wp_cache_set( 'last_changed', microtime(), 'payouts' );
		@affiliate_wp()->utils->log( 'Upgrade: The Payouts cache has been invalidated following the 1.9.5 upgrade routine.' );

		$this->upgraded = true;
	}

	/**
	 * Performs database upgrades for version 2.0.
	 *
	 * @since 2.0
	 * @access private
	 */
	private function v20_upgrade() {
		// New primitive and meta capabilities.
		@affiliate_wp()->capabilities->add_caps();
		@affiliate_wp()->utils->log( 'Upgrade: Core capabilities have been upgraded.' );


		// Update settings
		@affiliate_wp()->settings->set( array(
			'required_registration_fields' => array(
				'your_name'   => __( 'Your Name', 'affiliate-wp' ),
				'website_url' => __( 'Website URL', 'affiliate-wp' )
			)
		), $save = true );
		@affiliate_wp()->utils->log( 'Upgrade: The default required registration field settings have been configured.' );

		// Affiliate schema update.
		@affiliate_wp()->affiliates->create_table();
		@affiliate_wp()->utils->log( 'Upgrade: The unpaid_earnings column has been added to the affiliates table.' );

		wp_cache_set( 'last_changed', microtime(), 'affiliates' );
		@affiliate_wp()->utils->log( 'Upgrade: The Affiliates cache has been invalidated following the 2.0 upgrade.' );

		$this->upgraded = true;
	}

	/**
	 * Performs database upgrades for version 2.0.2.
	 *
	 * @since 2.0.2
	 * @access private
	 */
	private function v202_upgrade() {
		// New 'context' column for visits.
		@affiliate_wp()->visits->create_table();
		$this->log( 'Upgrade: The context column has been added to the Visits table.' );

		wp_cache_set( 'last_changed', microtime(), 'visits' );
		$this->log( 'Upgrade: The Visits cache has been invalidated following the 2.0.2 upgrade.' );

		$this->upgraded = true;
	}

	/**
	 * Performs database upgrades for version 2.0.10.
	 *
	 * @since 2.0.10
	 * @access private
	 */
	private function v210_upgrade() {
		update_option( 'affwp_flush_rewrites', '1' );
		@affiliate_wp()->utils->log( 'Upgrade: AffiliateWP rewrite rules have been flushed following the 2.0.10 upgrade.' );

		$this->upgraded = true;
	}

	/**
	 * Performs database upgrades for version 2.1.
	 *
	 * @access private
	 * @since  2.1
	 */
	private function v21_upgrade() {
		// Schedule a rewrites flush.
		flush_rewrite_rules();
		$this->log( 'Upgrade: Rewrite rules flushed following the 2.1 upgrade.' );

		$this->upgraded = true;
	}

	/**
	 * Performs database upgrades for version 2.1.3.1.
	 *
	 * @access private
	 * @since  2.1.3.1
	 */
	private function v2131_upgrade() {
		// Refresh capabilities missed in 2.1 update (export_visit_data).
		@affiliate_wp()->capabilities->add_caps();
		@affiliate_wp()->utils->log( 'Upgrade: Core capabilities have been upgraded for 2.1.3.1.' );

		$this->upgraded = true;
	}
}
