<?php
    /**
     * @package     Freemius Migration
     * @copyright   Copyright (c) 2016, Freemius, Inc.
     * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
     * @since       1.0.3
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    if ( class_exists( 'FS_EDD_Client_Migration_v2' ) ) {
        return;
    }

    // Include abstract class.
    require_once dirname( __FILE__ ) . '/class-fs-client-migration-abstract.php';

    /**
     * Class My_EDD_Freemius_Migration
     */
    class FS_EDD_Client_Migration_v2 extends FS_Client_Migration_Abstract_v2 {
        /**
         *
         * @param Freemius                      $freemius
         * @param string                        $edd_store_url                Your EDD store URL.
         * @param int                           $edd_download_id              The context EDD download ID (from your store).
         * @param FS_Client_License_Abstract_v2 $edd_license_accessor         License accessor.
         * @param string                        $migration_type               Migration type.
         * @param bool                          $was_freemius_in_prev_version By default, the migration process will only be executed upon activation of the product for the 1st time with Freemius. By modifying this flag to `true`, it will also initiate a migration request even if the user already opted into Freemius. This flag is particularly relevant when the developer already released a Freemius powered version before releasing a version with the migration code.
         * @param bool                          $is_blocking                  Special argument for testing. When false, will
         *                                                                    initiate the migration in the same HTTP request.
         */
        public function __construct(
            Freemius $freemius,
            $edd_store_url,
            $edd_download_id,
            FS_Client_License_Abstract_v2 $edd_license_accessor,
            $migration_type = FS_Client_Migration_Abstract_v2::TYPE_PRODUCT_TO_PRODUCT,
            $was_freemius_in_prev_version = false,
            $is_blocking = false
        ) {
            $this->init(
                'edd',
                $freemius,
                $edd_store_url,
                $edd_download_id,
                $edd_license_accessor,
                $migration_type,
                $was_freemius_in_prev_version,
                $is_blocking
            );
        }
    }
