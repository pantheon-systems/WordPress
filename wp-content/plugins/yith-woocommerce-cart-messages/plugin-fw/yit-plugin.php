<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


! defined( 'YIT_CORE_PLUGIN' )                  && define( 'YIT_CORE_PLUGIN', true);
! defined( 'YIT_CORE_PLUGIN_PATH' )             && define( 'YIT_CORE_PLUGIN_PATH', dirname(__FILE__) );
! defined( 'YIT_CORE_PLUGIN_URL' )              && define( 'YIT_CORE_PLUGIN_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
! defined( 'YIT_CORE_PLUGIN_TEMPLATE_PATH' )    && define( 'YIT_CORE_PLUGIN_TEMPLATE_PATH', YIT_CORE_PLUGIN_PATH .  '/templates' );

include_once( 'yit-functions.php' );
include_once( 'yit-woocommerce-compatibility.php' );
include_once( 'yit-plugin-registration-hook.php' );
include_once( 'lib/yit-metabox.php' );
include_once( 'lib/yit-plugin-panel.php' );
include_once( 'lib/yit-plugin-panel-wc.php' );
include_once( 'lib/yit-ajax.php' );
include_once( 'lib/yit-plugin-subpanel.php' );
include_once( 'lib/yit-plugin-common.php' );
include_once( 'lib/yit-plugin-gradients.php');
include_once( 'licence/lib/yit-licence.php');
include_once( 'licence/lib/yit-plugin-licence.php');
include_once( 'licence/lib/yit-theme-licence.php');
include_once( 'lib/yit-video.php');
include_once( 'lib/yit-upgrade.php');
include_once( 'lib/yit-pointers.php');
include_once( 'lib/yit-icons.php');
include_once( 'lib/yit-assets.php');
include_once( 'lib/yit-debug.php');
include_once( 'lib/privacy/yit-privacy.php' );
include_once( 'lib/privacy/yit-privacy-plugin-abstract.php' );
include_once ( 'lib/promo/yith-promo.php' );

// load from theme folder...
load_textdomain( 'yith-plugin-fw', get_template_directory() . '/core/plugin-fw/yith-plugin-fw-' . apply_filters( 'plugin_locale', get_locale(), 'yith-plugin-fw' ) . '.mo' )

// ...or from plugin folder
|| load_textdomain( 'yith-plugin-fw', dirname(__FILE__) . '/languages/yith-plugin-fw-' . apply_filters( 'plugin_locale', get_locale(), 'yith-plugin-fw' ) . '.mo' );

add_filter( 'plugin_row_meta', 'yit_plugin_fw_row_meta', 20, 4 );

if( ! function_exists( 'yit_plugin_fw_row_meta' ) ){
	/**
	 * Hack the plugin author name from YITHEMES to YITH
	 *
	 * @param $plugin_meta
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $status
	 *
	 * @since 3.0.17
	 * @author Andrea Grillo <andrea.grillo@yithemes.com>
	 *
	 * @return null|string|string[] $plugin row meta array
	 */
	function yit_plugin_fw_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ){
		$base_uri = array(
			'live_demo'       => 'https://plugins.yithemes.com/',
			'documentation'   => 'https://docs.yithemes.com/',
			'premium_support' => 'https://yithemes.com/my-account/support/dashboard/',
			'free_support'    => 'https://wordpress.org/support/plugin/',
			'premium_version' => 'https://yithemes.com/themes/plugins/'
		);

		$default = array(
			'live_demo' => array(
				'label' => _x( 'Live Demo', 'Plugin Row Meta', 'yith-plugin-fw' ),
				'icon'  => 'dashicons  dashicons-laptop',
			),

			'documentation' => array(
				'label' => _x( 'Documentation', 'Plugin Row Meta', 'yith-plugin-fw' ),
				'icon'  => 'dashicons  dashicons-search',
			),

			'support' => array(
				'label' => _x( 'Support', 'Plugin Row Meta', 'yith-plugin-fw' ),
				'icon'  => 'dashicons  dashicons-admin-users',
			),

			'premium_version' => array(
				'label' => _x( 'Premium version', 'Plugin Row Meta', 'yith-plugin-fw' ),
				'icon'  => 'dashicons  dashicons-cart',
			)
		);

		$to_show           = array( 'live_demo', 'documentation', 'support', 'premium_version' );
		$new_row_meta_args = apply_filters( 'yith_show_plugin_row_meta', array(
			'to_show' => $to_show,
			'slug'    => ''
		), $plugin_meta, $plugin_file, $plugin_data, $status );
		$fields            = isset( $new_row_meta_args['to_show'] ) ? $new_row_meta_args['to_show'] : array();
		$slug              = isset( $new_row_meta_args['slug'] ) ? $new_row_meta_args['slug'] : '';
		$is_premium        = isset( $new_row_meta_args['is_premium'] ) ? $new_row_meta_args['is_premium'] : '';

		if( true == $is_premium ){
			$to_remove = array_search( 'premium_version', $fields );

			if( $to_remove !== false ){
				unset( $fields[ $to_remove ] );
			}
		}

		foreach( $fields as $field ){
			$row_meta = isset( $new_row_meta_args[ $field ] ) ? wp_parse_args( $new_row_meta_args[ $field ], $default[ $field ] ) : $default[ $field ];
			$url = $icon = $label = '';

			// Check for Label
			if( isset( $row_meta['label'] ) ){
				$label = $row_meta['label'];
			}

			// Check for Icon
			if( isset( $row_meta['icon'] ) ){
				$icon = $row_meta['icon'];
			}

			// Check for URL
			if( isset( $row_meta['url'] ) ){
				$url = $row_meta['url'];
			}

			else{
				if( ! empty( $slug ) ){
					if(  'support' == $field ){
						$support_field = $is_premium === true ? 'premium_support': 'free_support';
						if( ! empty( $base_uri[ $support_field ] ) ){
							$url = $base_uri[ $support_field ];
						}

						if( 'free_support' == $support_field ){
							$url = $url . $slug;
						}
					}

					else{
						if( isset( $base_uri[ $field ] ) ) {
							$url = apply_filters( "yith_plugin_row_meta_{$field}_url",  $base_uri[ $field ] . $slug, $field, $slug, $base_uri );
						}
					}
				}
			}

			//Add YITH Refer ID
			if( ! empty( $url ) && defined( 'YITH_REFER_ID' ) ){
				$url = add_query_arg( array( 'refer_id', YITH_REFER_ID ), $url );
			}

			if( ! empty( $url ) && ! empty( $label ) ){
				$plugin_meta[] = sprintf( '<a href="%s" target="_blank"><span class="%s"></span>%s</a>', $url, $icon, $label );
			}
		}

		//Author Name Hack
		$plugin_meta = preg_replace('/>YITHEMES</', '>YITH<', $plugin_meta);

		return $plugin_meta;
	}
}

if( ! function_exists( 'yith_add_action_links' ) ){
	/**
	 * Action Links
	 *
	 * add the action links to plugin admin page
	 *
	 * @param $links | links plugin array
	 *
	 * @return   mixed Array
	 * @since    1.6.5
	 * @author   Andrea Grillo <andrea.grillo@yithemes.com>
	 * @return mixed
	 * @use      plugin_action_links_{$plugin_file_name}
	 */
	function yith_add_action_links( $links, $panel_page = '', $is_premium = false ) {
		if( ! empty( $panel_page )  ){
			$links[] = sprintf( '<a href="%s">%s</a>', admin_url( "admin.php?page={$panel_page}" ), _x( 'Settings', 'Action links',  'yith-plugin-fw' ) );
		}

		if( $is_premium && class_exists( 'YIT_Plugin_Licence' ) ){
			$links[] = sprintf( '<a href="%s">%s</a>', YIT_Plugin_Licence::get_license_activation_url(),__( 'License',  'yith-plugin-fw' ) );
		}

		return $links;
	}
}

/* === WooCommerce Update Message === */

/*if( apply_filters( 'yit_fw_wc_update_message_hook', true )
    &&
    ( function_exists( 'WC' ) && ! version_compare( WC()->version, '2.7', '>=' ) )
    && ! isset( $_COOKIE['yith_wc_2_7_notice'] )
){
    add_action( 'admin_notices', 'yit_fw_wc_update_message', 15 );
    add_action( 'admin_enqueue_scripts', 'yit_plugin_fw_dismissable_notice', 20 );

    if( ! function_exists( 'yit_fw_wc_update_message' ) ){
        function yit_fw_wc_update_message(){
            ?>
            <div id="yith-notice-is-dismissable" class="yith-wc-update-message notice notice-error is-dismissible">
                <?php $message = 'the new WooCommerce version 2.7 will be soon released. <strong>Before</strong> proceeding with the update, please verify the plugins you are using are already compatible. You can check the compatibility status of YITH products'; ?>
                <?php $url = 'https://support.yithemes.com/hc/en-us/articles/215945378-Theme-and-Plugin-compatibility-with-WordPress-and-WooCommerce'; ?>
                <p><?php printf( '<strong>%s</strong> - %s <a href="%s" target="_blank">HERE</a>.', 'Please note', $message, $url ); ?></p>
            </div>
            <?php
        }
    }

    if( ! function_exists( 'yit_plugin_fw_dismissable_notice' ) ){
        function yit_plugin_fw_dismissable_notice(){
            $assets_path          = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';
            wp_enqueue_script( 'jquery-cookie', $assets_path . 'js/jquery-cookie/jquery.cookie.min.js', array( 'jquery' ), '1.4.1', true);
            $js = "jQuery( document ).ready( function(){
                jQuery( '#yith-notice-is-dismissable' ).find('.notice-dismiss').on( 'click', function(){
                    jQuery.cookie('yith_wc_2_7_notice', 'dismiss', { path: '/' });
                } );
            } ); ";

            wp_add_inline_script( 'jquery-cookie', $js );
        }
    }
}*/

/* ========================== */
