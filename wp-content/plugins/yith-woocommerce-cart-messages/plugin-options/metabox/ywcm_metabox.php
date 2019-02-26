<?php
/**
 * Created by PhpStorm.
 * User: Your Inspiration
 * Date: 20/01/2015
 * Time: 12:04
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

return array(
    'label'    => __( 'Message Settings', 'yith-woocommerce-cart-messages' ),
    'pages'    => 'ywcm_message', //or array( 'post-type1', 'post-type2')
    'context'  => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'tabs'     => array(
        'settings' => array(
            'label'  => __( 'Settings', 'yith-woocommerce-cart-messages' ),
            'fields' => apply_filters( 'ywcm_message_metabox', array(
                    'ywcm_message_type' => array(
                        'label' => __( 'Message Type', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'Choose the type of the message', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'select',
                        'options' => YWCM_Cart_Message()->get_types(),
                        'std'   => 'minimum_amount' ),


                    /* Products in Cart ____________________________________________________________________________*/

                    'ywcm_message_products_cart_text'       => array(
                        'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'You can edit the text using the following placeholder: <br>
{remaining_quantity} indicates the remaining quantity,<br>
{products} specifies which of the listed product is in the cart,<br>
{quantity} indicates quantity in cart,
{required_quantity} states the exact number of product to purchase.', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'textarea',
                        'std'   => 'To benefit from free shipping, add <strong>{remaining_quantity}</strong> quantity more of <strong>{products}</strong>!',
                        'deps'  => array(
                            'ids'    => '_ywcm_message_type',
                            'values' => 'products_cart'
                        )
                    ),

                    'ywcm_message_products_cart_minimum'    => array(
                        'label' => __( 'Required quantity', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'The minimum total quantity of above selected products.', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'text',
                        'std'   => '',
                        'deps'  => array(
                            'ids'    => '_ywcm_message_type',
                            'values' => 'products_cart'
                        )
                    ),

                    'ywcm_products_cart_threshold_quantity' => array(
                        'label' => __( 'Threshold amount', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'Threshold amount after which notice should start appear.', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'text',
                        'std'   => '',
                        'deps'  => array(
                            'ids'    => '_ywcm_message_type',
                            'values' => 'products_cart'
                        )
                    ),


                    'ywcm_products_cart_products'   => array(
                        'label' => __( 'Select products', 'yith-woocommerce-cart-messages' ),
                        'desc'  => '',
                        'type'  => 'ajax-products',
                        'multiple' => true,
                        'options'  => array(),
                        'std'      => array(),
                        'deps'  => array(
                            'ids'    => '_ywcm_message_type',
                            'values' => 'products_cart'
                        )
                    ),


                    /* Category in Cart  ___________________________________________________________________________*/
                    'ywcm_message_categories_cart_text'     => array(
                        'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'You can edit the message using <br>{categories} to state the list of categories.', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'textarea',
                        'std'   => 'Do you like <strong>{categories}</strong>? Discovery our outlet!',
                        'deps'  => array(
                            'ids'    => '_ywcm_message_type',
                            'values' => 'categories_cart'
                        )
                    ),


                    'ywcm_message_category_cart_categories' => array(
                        'label' => __( 'Select categories', 'yith-woocommerce-cart-messages' ),
                        'desc'  => '',
                        'type'     => 'select',
                        'class' => 'wc-enhanced-select',
                        'multiple' => true,
                        'options'  => ywcm_get_shop_categories(false),
                        'std'      => array(),
                        'deps'  => array(
                            'ids'    => '_ywcm_message_type',
                            'values' => 'categories_cart'
                        )
                    ),



                    /* Simple message ____________________________________________________________________________*/
                    'ywcm_message_simple_message_text' => array(
                        'label' => __( 'Message', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'Edit the message', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'textarea',
                        'std'   => '',
                        'deps'     => array(
                            'ids'    => '_ywcm_message_type',
                            'values' => 'simple_message'
                        )
                    ),



                    /* Common options  ____________________________________________________________________________*/
                    'ywcm_message_button' => array(
                        'label' => __( 'Text Button (optional)', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'The text of the button for the action call. Leave it empty if you do not want to show it.', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'text',
                        'std'   => '' ),

                    'ywcm_message_button_url' => array(
                        'label' => __( 'Button URL (optional)', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'The URL of the button of the call to action', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'text',
                        'std'   => '' ),

                    'ywcm_message_expire' => array(
                        'label' => __( 'Expiration date (optional)', 'yith-woocommerce-cart-messages' ),
                        'desc'  => __( 'Choose a date until this message will appear', 'yith-woocommerce-cart-messages' ),
                        'type'  => 'text',
                        'std'   => '' ),


                )
            )
        )
    )
);