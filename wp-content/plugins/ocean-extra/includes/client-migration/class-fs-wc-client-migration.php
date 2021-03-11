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

    if ( class_exists( 'FS_WC_Client_Migration_v2' ) ) {
        return;
    }

    // Include abstract class.
    require_once dirname( __FILE__ ) . '/class-fs-client-migration-abstract.php';

    /**
     * Class FS_WC_Client_Migration_v2
     */
    class FS_WC_Client_Migration_v2 extends FS_Client_Migration_Abstract_v2 {
        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param Freemius                      $freemius
         * @param string                        $wc_store_url                 Your WC store URL.
         * @param int                           $wc_product_id                The context WC product ID (from your store).
         *                                                                    Important: If you have variants to the product,
         *                                                                    use the the ID should be of the parent product.
         * @param FS_Client_License_Abstract_v2 $wc_license_accessor          License accessor.
         * @param bool                          $migration_type               Migration type.
         * @param bool                          $was_freemius_in_prev_version By default, the migration process will only be executed upon activation of the product for the 1st time with Freemius. By modifying this flag to `true`, it will also initiate a migration request even if the user already opted into Freemius. This flag is particularly relevant when the developer already released a Freemius powered version before releasing a version with the migration code.
         * @param bool                          $is_blocking                  Special argument for testing. When false, will
         *                                                                    initiate the migration in the same HTTP request.
         */
        public function __construct(
            Freemius $freemius,
            $wc_store_url,
            $wc_product_id,
            FS_Client_License_Abstract_v2 $wc_license_accessor,
            $migration_type = false,
            $was_freemius_in_prev_version = false,
            $is_blocking = false
        ) {
            $this->init(
                'wc',
                $freemius,
                $wc_store_url,
                $wc_product_id,
                $wc_license_accessor,
                $migration_type,
                $was_freemius_in_prev_version,
                $is_blocking
            );

            $freemius->add_filter( 'license_key', array( &$this, 'convert_wc_to_fs_license_key' ) );

            add_action( 'admin_footer', 'allow_more_than_32_chars_key' );
        }

        /**
         * Convert WC long license key to FS 32 chars key.
         *
         *  WC Format:    'wc_order_5645bbd42dbfb_am_kii0TEYjrxEZ'
         *  Converted to: 'er_5645bbd42dbfb_am_kii0TEYjrxEZ'
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param string $license_key
         *
         * @return string
         */
        function convert_wc_to_fs_license_key( $license_key ) {
            $len = strlen( $license_key );

            if ( $len > 32 ) {
                $license_key = substr( $license_key, $len - 32 );
            }

            return $license_key;
        }

        /**
         * Tweak license key input box of the opt-in view to support
         * longer WC licenses.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         */
        function allow_more_than_32_chars_key() {
            if ( ! is_admin() ) {
                // Don't add logic in frontend.
                return;
            }

            if ( ! $this->_fs->is_activation_mode() ) {
                // Don't add code if not in activation mode.
                return;
            }

            if ( $this->_fs->_is_plugin_page() ) {
                // Don't add code if not on module's main page.
                return;
            }

            ?>
            <script type="text/javascript">
                jQuery(function ($) {
                    var $fsConn = $('#fs_connect');
                    if ($fsConn.length) {
                        /**
                         * Increase max license key length to 38 characters to
                         * support WC longer license keys.
                         */
                        $fsConn.find('#fs_license_key').attr('maxlength', 38);

                        // Resize license key input container to show the all key.
                        $fsConn.find('.fs-license-key-container').css('width', '322px');
                    }
                });
            </script>
            <?php
        }
    }
