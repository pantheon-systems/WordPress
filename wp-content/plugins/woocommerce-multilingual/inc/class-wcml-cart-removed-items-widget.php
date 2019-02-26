<?php

class WCML_Cart_Removed_Items_Widget extends WP_Widget {

    function __construct() {

        $widget_opt = array(
            'description' => __( 'Shows a list of the products that existed in the cart before the cart is reset on the front end after switching the language or the currency. It will be hidden when there are no products to show.', 'woocommerce-multilingual' ),
        );

        parent::__construct(
            'wcml_cart_deleted_items',
            __( 'Products before cart reset', 'woocommerce-multilingual' ),
            $widget_opt
        );

    }

    function widget( $args, $instance ) {

        echo $args[ 'before_widget' ];

        do_action( 'wcml_removed_cart_items' );

        echo $args[ 'after_widget' ];
    }

}