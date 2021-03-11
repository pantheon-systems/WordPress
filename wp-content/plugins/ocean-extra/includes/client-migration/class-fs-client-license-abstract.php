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

    if ( class_exists( 'FS_Client_License_Abstract_v2' ) ) {
        return;
    }

    abstract class FS_Client_License_Abstract_v2 {
        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @param int|null $blog_id Since 1.1.0
         *
         * @return string
         */
        abstract function get( $blog_id = null );

        /**
         * When migrating a bundle license and the sales platform creates a different
         * license key for every product in the bundle which is the key that actually
         * used for activation, this method should return the collection of all
         * child license keys that were activated on the current website.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param int|null $blog_id Since 1.1.0
         *
         * @return string[]
         *
         * @throws \Exception
         */
        function get_children( $blog_id = null ) {
            throw new Exception( 'get_children() is not implemented' );
        }

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @param string   $license_key
         * @param int|null $blog_id Since 1.1.0
         *
         * @return bool True if successfully updated.
         */
        abstract function set( $license_key, $blog_id = null );

        /**
         * Checks if a given sub-site has the license key set.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param int|null $blog_id
         *
         * @return bool
         */
        public function site_has_key( $blog_id = null ) {
            $key = $this->get( $blog_id );

            return ! empty( $key );
        }

        /**
         * Checks if a given sub-site has any bundle products keys.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param int|null $blog_id
         *
         * @return bool
         */
        public function site_has_children_keys( $blog_id = null ) {
            $children_keys = $this->get_children( $blog_id );

            return ! empty( $children_keys );
        }

        /**
         * Override this only when the product supports a network level integration.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @return bool
         */
        public function is_network_migration() {
            return false;
        }

        /**
         * Override this when licenses are identical across the network. I.E. if a license
         * is activated it has to be the same license for all the product installation's in the
         * network.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @return bool
         */
        public function are_licenses_network_identical() {
            return false;
        }

        /**
         * Activates a bundle license on the installed child products, after successfully migrating the license.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    2.0.0
         *
         * @param \FS_User    $user
         * @param string|null $bundle_license_key
         */
        abstract function activate_bundle_license_after_migration( FS_User $user, $bundle_license_key = null );
    }