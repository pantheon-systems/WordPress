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

    if ( class_exists( 'FS_Client_Migration_Abstract_v2' ) ) {
        return;
    }

    abstract class FS_Client_Migration_Abstract_v2 {
        /**
         * @var \Freemius Freemius instance manager.
         */
        protected $_fs;

        /**
         * @var \FS_Logger
         */
        protected $_logger;

        /**
         * @var string Store URL.
         */
        protected $_store_url;

        /**
         * @var string Product ID.
         */
        protected $_product_id;

        /**
         * @var string Optional license key override.
         */
        protected $_license_key;

        /**
         * @var string[] Optional children license keys override.
         */
        protected $_children_license_keys;

        /**
         * @var FS_Client_License_Abstract_v2
         */
        protected $_license_accessor;

        /**
         * @var string
         */
        protected $_migraiton_type;

        /**
         * @var bool
         */
        protected $_was_freemius_in_prev_version;

        /**
         * @var bool
         */
        protected $_is_blocking;

        /**
         * @var string Migration namespace.
         */
        protected $_namespace;

        /**
         * A migration of a product's license to the same product on Freemius.
         */
        const TYPE_PRODUCT_TO_PRODUCT = 'product';
        /**
         * A migration of bundle's child product licenses to a single license of a selected product on Freemius. This particular migration type should be used if the developer is transitioning from selling add-ons bundles via EDD into selling plans of the add-on's parent product instead.
         */
        const TYPE_CHILDREN_TO_PRODUCT = 'children';
        /**
         * A migration of a bundle's license from a product within the bundle. Since the migration is of a bundle's license, the resulting request will not generate any installs (bundle's don't have installs), so the activation of the bundle's license will be handled as a callback after the license migration.
         */
        const TYPE_BUNDLE_TO_BUNDLE = 'bundle';

        /**
         * @var FS_Client_Migration_Abstract_v2[]
         */
        protected static $instances;

        /**
         * @param string                        $namespace                    Migration namespace (e.g. EDD, WC)
         * @param Freemius                      $freemius
         * @param string                        $store_url                    Store URL.
         * @param string                        $product_id                   The product ID set on the system we're migrating from (not the Freemius product ID).
         * @param FS_Client_License_Abstract_v2 $license_accessor             License accessor.
         * @param string                        $migration_type               Migration type.
         * @param bool                          $was_freemius_in_prev_version By default, the migration process will only be executed upon activation of the product for the 1st time with Freemius. By modifying this flag to `true`, it will also initiate a migration request even if the user already opted into Freemius. This flag is particularly relevant when the developer already released a Freemius powered version before releasing a version with the migration code.
         * @param bool                          $is_blocking                  Special argument for testing. When false, will
         *                                                                    initiate the migration in the same HTTP request.
         */
        protected function init(
            $namespace,
            Freemius $freemius,
            $store_url,
            $product_id,
            FS_Client_License_Abstract_v2 $license_accessor,
            $migration_type = self::TYPE_PRODUCT_TO_PRODUCT,
            $was_freemius_in_prev_version = false,
            $is_blocking = false
        ) {
            $this->_namespace                    = strtolower( $namespace );
            $this->_fs                           = $freemius;
            $this->_store_url                    = $store_url;
            $this->_product_id                   = $product_id;
            $this->_license_accessor             = $license_accessor;
            $this->_migraiton_type               = $migration_type;
            $this->_is_blocking                  = $is_blocking;
            $this->_was_freemius_in_prev_version = $was_freemius_in_prev_version;

            $this->_logger = FS_Logger::get_logger(
                WP_FS__SLUG . '_oceanwp_migration_' . $this->_fs->get_slug(),
                WP_FS__DEBUG_SDK,
                WP_FS__ECHO_DEBUG_SDK
            );

            /**
             * If no license is set it might be one of the following:
             *  1. User purchased module directly from Freemius.
             *  2. User did purchase from store, but has never activated the license on this site.
             *  3. User got access to the code without ever purchasing.
             *
             * In case it's reason #2 or if the license key is wrong, the migration will not work.
             * Since we do want to support store licenses, hook to Freemius `after_install_failure`
             * event. That way, if a license activation fails, try activating the license on store
             * first, and if works, migrate to Freemius right after.
             */
            $this->_fs->add_filter( 'after_install_failure', array( &$this, 'try_migrate_on_activation' ), 10, 2 );

            if ( ! isset( self::$instances ) ) {
                self::$instances = array();

                add_action( 'admin_menu', array( 'FS_Client_Migration_Abstract_v2', 'add_migration_debug' ) );
            }

            self::$instances[] = $this;

            if ( $is_blocking || $this->should_try_migrate() ) {
                if ( $this->has_any_keys() ) {
                    if ( ! defined( 'DOING_AJAX' ) ) {
                        $this->non_blocking_license_migration( $is_blocking );
                    }
                }
            }
        }

        #--------------------------------------------------------------------------------
        #region Debugging
        #--------------------------------------------------------------------------------

        public static function add_migration_debug() {
            $hook = FS_Admin_Menu_Manager::add_subpage(
                null,
                'Freemius Migration Debug',
                'Freemius Migration Debug',
                'manage_options',
                'freemius-migration',
                array( 'FS_Client_Migration_Abstract_v2', '_debug_page_render' )
            );

            if ( ! empty( $hook ) ) {
                add_action( "load-$hook", array( 'FS_Client_Migration_Abstract_v2', '_debug_page_actions' ) );
            }
        }

        public static function _debug_page_actions() {
            Freemius::_clean_admin_content_section();

            if ( fs_request_is_action( 'try_migrate' ) ) {
                $module_id = fs_request_get( 'module_id' );

                if ( FS_Plugin::is_valid_id( $module_id ) ) {
                    check_admin_referer( "try_migrate_{$module_id}" );

                    foreach ( self::$instances as $instance ) {
                        if ( $module_id == $instance->_fs->get_id() ) {
                            $instance->do_license_migration( false, true );
                            break;
                        }
                    }
                }
            }
        }

        /**
         * Special migration debugging page rendering.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    2.0.0
         */
        public static function _debug_page_render() { ?>
            <h1 style="margin-bottom: 25px;">Freemius Migration Debug</h1>
            <?php
            date_default_timezone_set( 'UTC' );

            foreach ( self::$instances as $instance ) {
                $can_start_migration_string = $instance->can_start_migration();
                $can_start_migration        = ! is_string( $can_start_migration_string );
                $can_start_migration_string = $can_start_migration ? 'Yes' : $can_start_migration_string;

                $should_try_migrate = $instance->should_try_migrate();

                $transient_key = "fsm_{$instance->_namespace}_{$instance->_product_id}";
                $migration_uid = $instance->get_transient( $transient_key );

                $last_migration_timestamp = get_transient( "fs_license_migration_{$instance->_product_id}_timestamp" );
                $last_migration_response  = get_transient( "fs_license_migration_{$instance->_product_id}_last_response" );
                $last_migration_response_body  = get_transient( "fs_license_migration_{$instance->_product_id}_last_response_body" );

                // force migration

                $result       = $instance->get_site_migration_data_and_licenses();
                $all_licenses = $result['licenses'];

                $non_empty_licenses = array();
                foreach ( $all_licenses as $license_key ) {
                    if ( ! empty( $license_key ) ) {
                        $non_empty_licenses[] = $license_key;
                    }
                }

                $has_keys_to_migrate = ! empty( $non_empty_licenses );

                $has_an_issue = (
                    $has_keys_to_migrate &&
                    ( ! $can_start_migration || ! $should_try_migrate )
                );

                $module_id = $instance->_fs->get_id();

                $props = array(
                    array(
                        'key' => 'External ID',
                        'val' => $instance->_product_id,
                    ),
                    array(
                        'key' => 'Freemius ID',
                        'val' => $module_id,
                    ),
                    array(
                        'key' => 'Migration Type',
                        'val' => strtoupper( $instance->_migraiton_type ),
                    ),
                    array(
                        'key' => 'Store URL',
                        'val' => $instance->_store_url,
                    ),
                    array(
                        'key' => 'Is Blocking Migration',
                        'val' => ( $instance->_is_blocking ? 'Yes' : 'No' ),
                    ),
                    array(
                        'key'   => 'Should migrate?',
                        'val'   => $should_try_migrate ? 'Yes' : 'No',
                        'color' => $should_try_migrate ? 'green' : 'red',
                    ),
                    array(
                        'key'   => 'Can migrate?',
                        'val'   => $can_start_migration_string,
                        'color' => $can_start_migration ? 'green' : 'red',
                    ),
                    array(
                        'key' => 'Migrate if FS was in prev version',
                        'val' => ( $instance->_was_freemius_in_prev_version ? 'Yes' : 'No' ),
                    ),
                    array(
                        'key'   => 'Is in activation?',
                        'val'   => ( $instance->_fs->is_activation_mode() ? 'Yes' : 'No' ),
                        'color' => ( ! $instance->_was_freemius_in_prev_version && $instance->_fs->is_activation_mode() ? 'inherit' : 'red' ),
                    ),
                    array(
                        'key'   => 'Is upgrade mode?',
                        'val'   => ( $instance->_fs->is_plugin_upgrade_mode() ? 'Yes' : 'No' ),
                        'color' => ( ! $instance->_was_freemius_in_prev_version && $instance->_fs->is_plugin_upgrade_mode() ? 'inherit' : 'red' ),
                    ),
                    array(
                        'key'   => 'Is first version with Freemius?',
                        'val'   => ( $instance->_fs->is_first_freemius_powered_version() ? 'Yes' : 'No' ),
                        'color' => ( ! $instance->_was_freemius_in_prev_version && $instance->_fs->is_first_freemius_powered_version() ? 'inherit' : 'red' ),
                    ),
                    array(
                        'key' => 'Migration UID',
                        'val' => ( is_string( $migration_uid ) ? $migration_uid : '' ),
                    ),
                    array(
                        'key' => 'Last Migration Execution',
                        'val' => ( empty( $last_migration_timestamp ) ? '' : date( 'Y-m-d H:i:s', $last_migration_timestamp ) ),
                    ),
                    array(
                        'key' => 'Last Migration Response',
                        'val' => ( ! empty( $last_migration_response ) ? var_export( $last_migration_response, true ) : '' ),
                    ),
                    array(
                        'key' => ' -> Response Body',
                        'val' => ( ! empty( $last_migration_response_body ) ? $last_migration_response_body : '' ),
                    ),
                );
                ?>
                <div style="float: left; padding: 5px 10px 5px 10px;">
                    <table class="widefat">
                        <thead>
                        <tr>
                            <th colspan="2" style="position: relative">
                                <h3 style="color: <?php echo $has_an_issue ? 'red' : 'green' ?>">
                                    <span class="dashicons dashicons-<?php echo $has_an_issue ? 'warning' : 'yes-alt' ?>"></span><?php if ( $has_keys_to_migrate ) : ?>
                                        <span class="dashicons dashicons-admin-network"></span><?php endif ?> <?php echo esc_html( $instance->_fs->get_plugin_title() ) ?>
                                </h3>

                                <form action="" method="POST" style="position: absolute; top: 20px; right: 20px;">
                                    <input type="hidden" name="fs_action" value="try_migrate">
                                    <input type="hidden" name="module_id" value="<?php echo $module_id ?>">
                                    <?php wp_nonce_field( "try_migrate_{$module_id}" ) ?>
                                    <button class="button button-primary"<?php disabled( ! $has_keys_to_migrate ) ?>>Try Migrate</button>
                                </form>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $alternate = false;
                            foreach ( $props as $p ) : ?>
                                <tr<?php if ( $alternate ) {
                                    echo ' class="alternate"';
                                } ?>>
                                    <td style="width: 150px">
                                        <nobr<?php if ( ! empty( $p['color'] ) )
                                            echo ' style="color: ' . $p['color'] . '"' ?>><?php echo esc_html( $p['key'] ) ?></nobr>
                                    </td>
                                    <td><code><?php echo esc_html( $p['val'] ) ?></code></td>
                                </tr>
                                <?php $alternate = ! $alternate ?>
                            <?php endforeach ?>
                        <tr>
                            <td<?php if ( empty( $non_empty_licenses ) )
                                echo ' style="color: red;"' ?>>License Keys
                            </td>
                            <td>
                                <?php if ( ! $has_keys_to_migrate ) : ?>
                                    <code>No licenses to migrate</code>
                                <?php else : ?>
                                    <?php foreach ( $all_licenses as $license_key ) : ?>
                                        <code style="display: block"><?php echo $license_key ?></code>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br>
                </div>
                <?php
            }
        }

        #endregion

        /**
         * The license migration script.
         *
         * IMPORTANT:
         *  You should use your own function name, and be sure to replace it throughout this file.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @param bool $redirect
         * @param bool $flush Since 2.0.0
         *
         * @return bool
         */
        protected function do_license_migration( $redirect = false, $flush = false ) {
            $this->_logger->entrance();

            $result = $this->get_site_migration_data_and_licenses();

            $migration_data = $result['data'];
            $all_licenses   = $result['licenses'];

            $transient_key = 'fs_license_migration_' . $this->_product_id . '_' . md5( implode( '', $all_licenses ) );
            $response      = $flush ? false : get_transient( $transient_key );

            if ( false !== $response ) {
                $this->_logger->info( 'Response already cached and fetched directly from the 15 min transient.');
            } else {
                set_transient( "fs_license_migration_{$this->_product_id}_timestamp", WP_FS__SCRIPT_START_TIME, WP_FS__TIME_24_HOURS_IN_SEC * 30 );

                $endpoint_url = $this->get_migration_endpoint();

                $this->_logger->info( "Initiating a license migration call to {$endpoint_url}." );

                $response = wp_remote_post(
                    $endpoint_url,
                    array(
                        'timeout'   => 60,
                        'sslverify' => false,
                        'body'      => json_encode( $migration_data ),
                    )
                );

                // Cache result (15-min).
                $this->set_transient( $transient_key, $response, 15 * MINUTE_IN_SECONDS );
            }

            set_transient( "fs_license_migration_{$this->_product_id}_last_response", $response, WP_FS__TIME_24_HOURS_IN_SEC * 30 );

            $should_migrate_transient = $this->get_should_migrate_transient_key();

            // make sure the response came back okay
            if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                $error_message = $response->get_error_message();

                delete_transient( "fs_license_migration_{$this->_product_id}_last_response_body" );

                $this->_logger->error( $error_message );

                return ( is_wp_error( $response ) && ! empty( $error_message ) ) ?
                    $error_message :
                    __( 'An error occurred, please try again.' );

            } else {
                $response_body = wp_remote_retrieve_body( $response );

                set_transient( "fs_license_migration_{$this->_product_id}_last_response_body", $response_body, WP_FS__TIME_24_HOURS_IN_SEC * 30 );

                $response = json_decode( $response_body );

                if ( ! is_object( $response ) ||
                     ! isset( $response->success ) ||
                     true !== $response->success
                ) {
                    if ( isset( $response->error ) ) {
                        $this->_logger->error( $response->error->code . ': ' . $response->error->message );

                        switch ( $response->error->code ) {
                            case 'empty_license_key':
                            case 'invalid_license_key':
                            case 'license_expired':
                            case 'license_disabled':
                                $this->set_transient( $should_migrate_transient, 'no', WP_FS__TIME_24_HOURS_IN_SEC * 365 );
                                break;
                            default:
                                // Unexpected error.
                                break;
                        }
                    } else {
                        // Unexpected error.
                        $this->_logger->error( 'Unexpected migration error.' );
                    }

                    // Failed to pull account information.
                    return false;
                }

                // Delete transient on successful migration.
                $this->delete_transient_mixed( $transient_key );

                if ( $this->_was_freemius_in_prev_version && $this->_fs->is_registered() ) {
                    if ( $this->_license_accessor->is_network_migration() ) {
                        $this->_logger->info( 'Deleting network account to apply the account of the migrated license.' );

                        $this->_fs->delete_network_account_event();
                    } else {
                        $this->_logger->info( 'Deleting account to apply the account of the migrated license.' );

                        $this->_fs->delete_account_event();
                    }
                }

                $fs_user = new FS_User( $response->data->user );

                if ( self::TYPE_BUNDLE_TO_BUNDLE === $this->_migraiton_type ||
                     ( isset( $response->data->type ) && self::TYPE_BUNDLE_TO_BUNDLE === $response->data->type )
                ) {
                    $this->_logger->info( 'Activating bundle license after migration.' );

                    $this->_license_accessor->activate_bundle_license_after_migration(
                        $fs_user,
                        ( self::TYPE_BUNDLE_TO_BUNDLE === $this->_migraiton_type ) ?
                            null :
                            $response->data->license_key
                    );
                } else {
                    if ( isset( $response->data->type ) && 'addon' === $response->data->type ) {
                        /**
                         * An environment can have multiple add-ons with activated licenses that were purchased by different customers. Since the add-on install entities need to be associated with the same user that is connected with the parent product's install, the new migration logic on the migrating store will NOT create the install for add-ons, to avoid the situation when a license is activated on an install that is owned by a different user. Therefore, the actual install creation will happen on the client's site right here. And if the license belongs to a different user, it will be treated as a foreign license.
                         *
                         * @author Vova Feldman
                         */
                        $this->_logger->info( 'Activating migrated add-on license.' );

                        $this->_fs->activate_migrated_license( $response->data->license_key );
                    } else {
                        if ( $this->_license_accessor->is_network_migration() ) {
                            $installs = array();
                            foreach ( $response->data->installs as $install ) {
                                $installs[] = new FS_Site( $install );
                            }

                            $this->_logger->info( 'Setting up a network account after migration.' );

                            $this->_fs->setup_network_account(
                                $fs_user,
                                $installs,
                                $redirect
                            );
                        } else {
                            $this->_logger->info( 'Setting up an account after migration.' );

                            $this->_fs->setup_account(
                                $fs_user,
                                new FS_Site( $response->data->install ),
                                $redirect
                            );
                        }

                        $this->_fs->remove_sticky( 'plan_upgraded' );
                    }
                }

                if ( $this->_fs->is_addon() ) {
                    $parent_fs = $this->_fs->get_parent_instance();

                    if ( ! $parent_fs->is_registered() && $parent_fs->has_free_plan() ) {
                        // Opt-in to the parent with the add-on's user.

                        $this->_logger->info( 'Opting into the parent with the add-on\'s user.' );

                        $parent_fs->install_with_user(
                            $fs_user,
                            false,
                            false,
                            false,
                            true,
                            $this->_license_accessor->is_network_migration() ?
                                $parent_fs->get_sites_for_network_level_optin() :
                                array()
                        );
                    }
                }

                // Upon successful migration, store the no-migration flag for 5 years.
                $this->set_transient( $should_migrate_transient, 'no', WP_FS__TIME_24_HOURS_IN_SEC * 365 * 5 );

                do_action( 'fs_after_client_migration', $this->_license_accessor );

                $this->_logger->info( 'Migration completed successfully.' );

                return true;
            }
        }

        /**
         * Initiate a non-blocking HTTP POST request to the same URL
         * as the current page, with the addition of "fsm_{namespace}_{product_id}"
         * param in the query string that is set to a unique migration
         * request identifier, making sure only one request will make
         * the migration.
         *
         * @todo     Test 2 threads in parallel and make sure that `add_transient()` works as expected.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @return bool Is successfully spawned the migration request.
         */
        protected function spawn_license_migration() {
            #region Make sure only one request handles the migration (prevent race condition)

            // Generate unique md5.
            $migration_uid = md5( rand() . microtime() );

            $loaded_migration_uid = false;

            /**
             * Use `add_transient()` instead of `set_transient()` because
             * we only want that one request will succeed writing this
             * option to the storage.
             */
            $transient_key = "fsm_{$this->_namespace}_{$this->_product_id}";
            if ( $this->add_transient( $transient_key, $migration_uid, MINUTE_IN_SECONDS ) ) {
                $loaded_migration_uid = $this->get_transient( $transient_key );
            }

            if ( $migration_uid !== $loaded_migration_uid ) {
                return false;
            }

            #endregion

            $migration_url = add_query_arg(
                "fsm_{$this->_namespace}_{$this->_product_id}",
                $migration_uid,
                $this->get_current_url()
            );

            // Add cookies to trigger request with same user access permissions.
            $cookies = array();
            foreach ( $_COOKIE as $name => $value ) {
                $cookies[] = new WP_Http_Cookie( array(
                    'name'  => $name,
                    'value' => $value
                ) );
            }

            wp_remote_post(
                $migration_url,
                array(
                    'timeout'   => 0.01,
                    'blocking'  => false,
                    'sslverify' => false,
                    'cookies'   => $cookies,
                )
            );

            return true;
        }

        /**
         * Run non blocking migration if all of the following (AND condition):
         *  1. Has API connectivity to api.freemius.com
         *  2. User isn't yet identified with Freemius.
         *  3. Freemius is in "activation mode".
         *  4. It's a plugin version upgrade.
         *  5. It's the first installation of the context plugin that have Freemius integrated with.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @param bool $is_blocking Special argument for testing. When false, will initiate the migration in the same HTTP request.
         *
         * @return string|bool
         */
        protected function non_blocking_license_migration( $is_blocking = false ) {
            $can_start_migration = $this->can_start_migration( $is_blocking );

            if ( is_string( $can_start_migration ) ) {
                return $can_start_migration;
            }

            $key = "fsm_{$this->_namespace}_{$this->_product_id}";

            $migration_uid = $this->get_transient( $key );
            $in_migration  = ! empty( $_REQUEST[ $key ] );

            if ( ! $is_blocking && ! $in_migration ) {
                // Initiate license migration in a non-blocking request.
                return $this->spawn_license_migration();
            } else {
                if ( $is_blocking ||
                     ( ! empty( $_REQUEST[ $key ] ) &&
                       $migration_uid === $_REQUEST[ $key ] &&
                       'POST' === $_SERVER['REQUEST_METHOD'] )
                ) {
                    $success = $this->do_license_migration();

                    if ( $success ) {
                        $this->_fs->set_plugin_upgrade_complete();

                        return 'success';
                    }
                }
            }

            return 'failed';
        }

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    2.0.0
         *
         * @param bool $ignore_prev_version Ignore whether Freemius was already in prev versions of the product or not.
         *
         * @return bool|string
         */
        protected function can_start_migration( $ignore_prev_version = false ) {
            if ( ! $this->_fs->has_api_connectivity() ) {
                // No connectivity to Freemius API, it's up to you what to do.
                return 'no_connectivity';
            }

            if ( ! $this->_fs->is_premium() && self::TYPE_BUNDLE_TO_BUNDLE != $this->_migraiton_type ) {
                // Running the free product version, so don't migrate.
                return 'free_code_version';
            }

            if ( $this->_fs->is_registered() && $this->_fs->has_any_license( false ) ) {
                // User already identified by the API and has a license.
                return 'user_registered_with_license';
            }

            if ( ! $ignore_prev_version && ! $this->_was_freemius_in_prev_version ) {
                if ( $this->_fs->is_registered() ) {
                    // User already identified by the API.
                    return 'user_registered';
                }

                if ( ! $this->_fs->is_activation_mode() ) {
                    // Plugin isn't in Freemius activation mode.
                    return 'not_in_activation';
                }

                if ( ! $this->_fs->is_plugin_upgrade_mode() ) {
                    // Plugin isn't in plugin upgrade mode.
                    return 'not_in_upgrade';
                }

                if ( ! $this->_fs->is_first_freemius_powered_version() ) {
                    // It's not the 1st version of the plugin that runs with Freemius.
                    return 'freemius_installed_before';
                }
            }

            return true;
        }

        /**
         * If installation failed due to license activation on Freemius try to
         * activate the license on store first, and if successful, migrate the license
         * with a blocking request.
         *
         * This method will only be triggered upon failed module installation.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @param object $response Freemius installation request result.
         * @param array  $args     Freemius installation request arguments.
         *
         * @return object|string
         */
        public function try_migrate_on_activation( $response, $args ) {
            if ( empty( $args['license_key'] ) ||
                 $this->_fs->apply_filters( 'license_key_maxlength', 32 ) !== strlen( $args['license_key'] )
            ) {
                // No license key provided (or invalid length), ignore.
                return $response;
            }

            if ( ! $this->_fs->has_api_connectivity() ) {
                // No connectivity to Freemius API, it's up to you what to do.
                return $response;
            }

            if ( ( is_object( $response->error ) && 'invalid_license_key' === $response->error->code ) ||
                 ( is_string( $response->error ) && false !== strpos( strtolower( $response->error ), 'license' ) )
            ) {
                // Set license for migration.
                if ( self::TYPE_CHILDREN_TO_PRODUCT === $this->_migraiton_type ) {
                    $this->_children_license_keys = array( $args['license_key'] );
                } else {
                    $this->_license_key = $args['license_key'];
                }

                // Try to migrate the license.
                if ( 'success' === $this->non_blocking_license_migration( true ) ) {
                    /**
                     * If successfully migrated license and got to this point (no redirect),
                     * it means that it's an AJAX installation (opt-in), therefore,
                     * override the response with the after connect URL.
                     */
                    return $this->_fs->get_after_activation_url( 'after_connect_url' );
                } else {
                    $result = $this->get_site_migration_data_and_licenses();

                    $all_licenses   = $result['licenses'];

                    $transient_key = 'fs_license_migration_' . $this->_product_id . '_' . md5( implode( '', $all_licenses ) );
                    $response      = $this->get_transient_mixed( $transient_key );

                    $response->error->message = 'Migration error: ' . var_export( $response, true );
                }
            }

            return $response;
        }

        #--------------------------------------------------------------------------------
        #region Helper Methods
        #--------------------------------------------------------------------------------

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.3
         *
         * @return string
         */
        protected function get_migration_endpoint() {
            return sprintf(
                '%s/fs-api/%s/migrate-license.json',
                $this->_store_url,
                $this->_namespace
            );
        }

        /**
         * Prepare data for migration.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @return array
         */
        private function get_site_migration_data_and_licenses() {
            $is_network_migration = $this->_license_accessor->is_network_migration();

            $this->wp_cookie_constants();

            $migration_data = $this->_fs->get_opt_in_params( array(
                // Include the migrating product ID.
                'module_id'      => $this->_product_id,
                // Override is_premium flat because it's a paid license migration.
                'is_premium'     => true,
                // The plugin is active for sure and not uninstalled.
                'is_active'      => true,
                'is_uninstalled' => false,
            ), ( $is_network_migration ? true : null ) );

            // Clean unnecessary arguments.
            unset( $migration_data['return_url'] );
            unset( $migration_data['account_url'] );

            $all_licenses = array();

            if ( false === $is_network_migration || $this->_license_accessor->are_licenses_network_identical() ) {
                if ( self::TYPE_CHILDREN_TO_PRODUCT === $this->_migraiton_type ) {
                    $migration_data['children_license_keys'] = $this->get_children_licenses();

                    $all_licenses = $migration_data['children_license_keys'];
                } else {
                    $migration_data['license_key'] = $this->get_license();

                    $all_licenses[] = $migration_data['license_key'];
                }
            } else {
                $blog_ids = self::get_blog_ids();

                $keys_by_blog_id = array();

                foreach ( $blog_ids as $blog_id ) {
                    $site_keys = ( self::TYPE_CHILDREN_TO_PRODUCT === $this->_migraiton_type ) ?
                        $this->get_children_licenses( $blog_id ) :
                        $this->get_license( $blog_id );

                    if ( empty( $site_keys ) ) {
                        continue;
                    }

                    $keys_by_blog_id[ $blog_id ] = $site_keys;
                }

                foreach ( $migration_data['sites'] as $index => &$site ) {
                    if ( ! isset( $keys_by_blog_id[ $site['blog_id'] ] ) ) {
                        unset( $migration_data['sites'][ $index ] );
                    }

                    $site_keys = $keys_by_blog_id[ $site['blog_id'] ];

                    if ( is_array( $site_keys ) ) {
                        $site['children_license_keys'] = $site_keys;

                        $all_licenses = array_merge( $all_licenses, $site_keys );
                    } else {
                        $site['license_key'] = $site_keys;

                        $all_licenses[] = $site_keys;
                    }
                }

                // Reorder indexes.
                $migration_data = array_values( $migration_data );
            }

            return array(
                'data'     => $migration_data,
                'licenses' => $all_licenses,
            );
        }

        /**
         * Get a collection of all the license keys for the migration.
         * We use this to generate a unique transient name to store a helper
         * value to avoid trying a migration with a keys combination that already
         * failed before.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.2.0
         *
         * @return string[]
         */
        private function get_all_migration_licenses() {
            $is_network_migration = $this->_license_accessor->is_network_migration();

            $all_licenses = array();

            if ( false === $is_network_migration || $this->_license_accessor->are_licenses_network_identical() ) {
                if ( self::TYPE_CHILDREN_TO_PRODUCT === $this->_migraiton_type ) {
                    $all_licenses = $this->get_children_licenses();
                } else {
                    $all_licenses[] = $this->get_license();
                }
            } else {
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {
                    $site_keys = ( self::TYPE_CHILDREN_TO_PRODUCT === $this->_migraiton_type ) ?
                        $this->get_children_licenses( $blog_id ) :
                        $this->get_license( $blog_id );

                    if ( empty( $site_keys ) ) {
                        continue;
                    }

                    if ( is_array( $site_keys ) ) {
                        $all_licenses = array_merge( $all_licenses, $site_keys );
                    } else {
                        $all_licenses[] = $site_keys;
                    }
                }
            }

            return $all_licenses;
        }

        /**
         * Define cookie constants which are required by Freemius::get_opt_in_params() since
         * it uses wp_get_current_user() which needs the cookie constants set. When a plugin
         * is network activated the cookie constants are only configured after the network
         * plugins activation, therefore, if we don't define those constants WP will throw
         * PHP warnings/notices.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.1
         */
        private function wp_cookie_constants() {
            if ( defined( 'LOGGED_IN_COOKIE' ) &&
                 ( defined( 'AUTH_COOKIE' ) || defined( 'SECURE_AUTH_COOKIE' ) )
            ) {
                return;
            }

            /**
             * Used to guarantee unique hash cookies
             *
             * @since 1.5.0
             */
            if ( ! defined( 'COOKIEHASH' ) ) {
                $siteurl = get_site_option( 'siteurl' );
                if ( $siteurl ) {
                    define( 'COOKIEHASH', md5( $siteurl ) );
                } else {
                    define( 'COOKIEHASH', '' );
                }
            }

            if ( ! defined( 'LOGGED_IN_COOKIE' ) ) {
                define( 'LOGGED_IN_COOKIE', 'wordpress_logged_in_' . COOKIEHASH );
            }

            /**
             * @since 2.5.0
             */
            if ( ! defined( 'AUTH_COOKIE' ) ) {
                define( 'AUTH_COOKIE', 'wordpress_' . COOKIEHASH );
            }

            /**
             * @since 2.6.0
             */
            if ( ! defined( 'SECURE_AUTH_COOKIE' ) ) {
                define( 'SECURE_AUTH_COOKIE', 'wordpress_sec_' . COOKIEHASH );
            }
        }

        /**
         * Get current request full URL.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.3
         *
         * @return string
         */
        private function get_current_url() {
            $host = $_SERVER['HTTP_HOST'];
            $uri  = $_SERVER['REQUEST_URI'];
            $port = $_SERVER['SERVER_PORT'];
            $port = ( ( ! WP_FS__IS_HTTPS && $port == '80' ) || ( WP_FS__IS_HTTPS && $port == '443' ) ) ? '' : ':' . $port;

            return ( WP_FS__IS_HTTPS ? 'https' : 'http' ) . "://{$host}{$port}{$uri}";
        }

        /**
         * Checks if there are any keys set at all.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         */
        private function has_any_keys() {
            if ( ! $this->_license_accessor->is_network_migration() ) {
                return ( self::TYPE_CHILDREN_TO_PRODUCT === $this->_migraiton_type ) ?
                    $this->_license_accessor->site_has_children_keys() :
                    $this->_license_accessor->site_has_key();
            }

            $blog_ids = self::get_blog_ids();

            foreach ( $blog_ids as $blog_id ) {
                $site_has_keys = ( self::TYPE_CHILDREN_TO_PRODUCT === $this->_migraiton_type ) ?
                    $this->_license_accessor->site_has_children_keys( $blog_id ) :
                    $this->_license_accessor->site_has_key( $blog_id );

                if ( $site_has_keys ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param int|null $blog_id
         *
         * @return string
         */
        private function get_license( $blog_id = null ) {
            return empty( $this->_license_key ) ?
                $this->_license_accessor->get( $blog_id ) :
                $this->_license_key;
        }

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @param int|null $blog_id
         *
         * @return string[]
         */
        private function get_children_licenses( $blog_id = null ) {
            return empty( $this->_children_license_keys ) ?
                $this->_license_accessor->get_children( $blog_id ) :
                $this->_children_license_keys;
        }

        /**
         * @var string
         */
        private $_shouldMigrateTransientKey;

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.2
         *
         * @return string
         *
         * @uses     get_all_migration_licenses()
         */
        private function get_should_migrate_transient_key() {
            if ( ! isset( $this->_shouldMigrateTransientKey ) ) {
                $keys = $this->get_all_migration_licenses();

                $this->_shouldMigrateTransientKey = 'fs_should_migrate_' . md5( $this->_store_url . ':' . $this->_product_id . implode( ':', $keys ) );
            }

            return $this->_shouldMigrateTransientKey;
        }

        /**
         * Check if should try to migrate or not.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.2
         *
         * @return bool
         */
        protected function should_try_migrate() {
            $key = $this->get_should_migrate_transient_key();

            $should_migrate = $this->get_transient_mixed( $key );

            return ( ! is_string( $should_migrate ) || 'no' !== $should_migrate );
        }

        #endregion

        #--------------------------------------------------------------------------------
        #region Database Transient
        #--------------------------------------------------------------------------------

        /**
         * Very similar to the WP transient mechanism.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @param string $transient
         *
         * @return mixed
         */
        private function get_transient( $transient ) {
            $transient_option  = '_fs_transient_' . $transient;
            $transient_timeout = '_fs_transient_timeout_' . $transient;

            $timeout = get_option( $transient_timeout );

            if ( false !== $timeout && $timeout < time() ) {
                delete_option( $transient_option );
                delete_option( $transient_timeout );
                $value = false;
            } else {
                $value = get_option( $transient_option );
            }

            return $value;
        }

        /**
         * @author Leo Fajardo (@leorw)
         * @since 1.1.2.1
         *
         * @param string $transient
         *
         * @return mixed
         */
        private function get_transient_mixed( $transient ) {
            $value = get_transient( $transient );

            if ( false === $value ) {
                $value = $this->get_transient( $transient );
            }

            return $value;
        }

        /**
         * @author Leo Fajardo (@leorw)
         * @since 1.1.2.1
         *
         * @param string $transient
         */
        private function delete_transient_mixed( $transient ) {
            delete_transient( $transient );
            $this->delete_transient( $transient );
        }

        /**
         * Not like `set_transient()`, this function will only ADD
         * a transient if it's not yet exist.
         *
         * @author   Vova Feldman (@svovaf)
         * @since    1.0.0
         *
         * @param string $transient
         * @param mixed  $value
         * @param int    $expiration
         *
         * @return bool TRUE if successfully added a transient.
         */
        private function add_transient( $transient, $value, $expiration = 0 ) {
            $transient_option  = '_fs_transient_' . $transient;
            $transient_timeout = '_fs_transient_timeout_' . $transient;

            $current_value = $this->get_transient( $transient );

            if ( false === $current_value ) {
                $autoload = 'yes';
                if ( $expiration ) {
                    $autoload = 'no';
                    add_option( $transient_timeout, time() + $expiration, '', 'no' );
                }

                return add_option( $transient_option, $value, '', $autoload );
            } else {
                // If expiration is requested, but the transient has no timeout option,
                // delete, then re-create the timeout.
                if ( $expiration ) {
                    if ( false === get_option( $transient_timeout ) ) {
                        add_option( $transient_timeout, time() + $expiration, '', 'no' );
                    }
                }
            }

            return false;
        }

        /**
         * @author Leo Fajardo (@leorw)
         * @since 1.1.2.1
         *
         * @param string $transient
         * @param mixed  $value
         * @param int    $expiration
         *
         * @return bool true if successfully updated a transient.
         */
        private function set_transient( $transient, $value, $expiration = 0 ) {
            $this->delete_transient( $transient );

            return $this->add_transient( $transient, $value, $expiration );
        }

        /**
         * @author Leo Fajardo (@leorw)
         * @since 1.1.2.1
         *
         * @param string $transient
         *
         * @return bool true if successfully removed.
         */
        private function delete_transient( $transient ) {
            $transient_option  = '_fs_transient_' . $transient;
            $transient_timeout = '_fs_transient_timeout_' . $transient;

            $result = delete_option( $transient_option );

            if ( false !== $result ) {
                delete_option( $transient_timeout );
            }

            return $result;
        }

        #endregion

        #--------------------------------------------------------------------------------
        #region Multisite Network
        #--------------------------------------------------------------------------------

        /**
         * @author   Vova Feldman (@svovaf)
         * @since    1.1.0
         *
         * @return int[]
         */
        public static function get_blog_ids() {
            global $wpdb;

            return $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
        }

        #endregion
    }