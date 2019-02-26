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

global $YWCM_Instance;

return array(

	'settings' => array(

        'section_general_settings_videobox'         => array(
            'name' => __( 'Upgrade to the PREMIUM VERSION', 'yith-woocommerce-cart-messages' ),
            'type' => 'videobox',
            'default' => array(
                'plugin_name'        => __( 'YITH WooCommerce Cart Messages', 'yith-woocommerce-cart-messages' ),
                'title_first_column' => __( 'Discover the Advanced Features', 'yith-woocommerce-cart-messages' ),
                'description_first_column' => __('Upgrade to the PREMIUM VERSION
of YITH WooCommerce Cart Messages to benefit from all features!', 'yith-woocommerce-cart-messages'),

                'video' => array(
                   'video_id'           => '118792418',
                   'video_image_url'    =>  YITH_YWCM_ASSETS_URL.'/images/yith-woocommerce-cart-messages.jpg',
                   'video_description'  => __( 'YITH WooCommerce Cart Messages', 'yit' ),
               ),
                'title_second_column' => __( 'Get Support and Pro Features', 'yith-woocommerce-cart-messages' ),
                'description_second_column' => __('By purchasing the premium version of the plugin, you will take advantage of the advanced features of the product and you will get one year of free updates and support through our platform available 24h/24.', 'yith-woocommerce-cart-messages'),
                'button' => array(
                    'href' => $YWCM_Instance->get_premium_landing_uri(),
                    'title' => 'Get Support and Pro Features'
                )
            ),
            'id'   => 'yith_wcas_general_videobox'
        ),

		'section_general_settings'     => array(
			'name' => __( 'General settings', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_general'
		),

        'show_in_cart' => array(
            'name'    => __( 'Show in cart', 'yith-woocommerce-cart-messages' ),
            'desc'    => '',
            'id'      => 'ywcm_show_in_cart',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),

        'show_in_checkout' => array(
            'name'    => __( 'Show in checkout', 'yith-woocommerce-cart-messages' ),
            'desc'    => '',
            'id'      => 'ywcm_show_in_checkout',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),

		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_general_end'
		)
	)
);