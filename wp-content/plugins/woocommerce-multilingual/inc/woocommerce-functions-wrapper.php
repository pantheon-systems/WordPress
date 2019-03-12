<?php

class WooCommerce_Functions_Wrapper{

    public static function is_deprecated(){

        if( version_compare( WC_VERSION , '3.0.0', '<' ) ){
            return true;
        }
        return false;
    }

    public static function get_product_id( $product ){
        if( self::is_deprecated() ){
            return $product->id;
        }
	    return $product->get_id();
    }

    public static function get_product_type( $product_id ){
        if( self::is_deprecated() ){
            $product = wc_get_product( $product_id );
            return $product->product_type;
        }
	    return WC_Product_Factory::get_product_type( $product_id );
    }

    public static function reduce_stock( $product_id, $qty ){
        if( self::is_deprecated() ){
            $product = wc_get_product( $product_id );
            return $product->reduce_stock( $qty );
        }
	    $data_store = WC_Data_Store::load( 'product' );
	    return $data_store->update_product_stock( $product_id, $qty, 'decrease' );
    }

    public static function increase_stock( $product_id, $qty ){
        if( self::is_deprecated() ){
            $product = wc_get_product( $product_id );
            return $product->increase_stock( $qty );
        }
	    $data_store = WC_Data_Store::load( 'product' );
	    return $data_store->update_product_stock( $product_id, $qty, 'increase' );
    }

    public static function set_stock( $product_id, $qty ){
        if( self::is_deprecated() ){
            $product = wc_get_product( $product_id );
            return $product->set_stock( $qty );
        }
        return wc_update_product_stock( $product_id, $qty );
    }

	/**
	 * @param WC_Abstract_Legacy_Order|WC_Abstract_Order $order
	 *
	 * @return string
	 */
    public static function get_order_currency( $order ){
        if( self::is_deprecated() ){
            return $order->get_order_currency();
        }
        return $order->get_currency();
    }

	/**
	 * @param WC_Abstract_Legacy_Order|WC_Abstract_Order $object
	 * @param array|WC_Order_Item_Product $item
	 *
	 * @return mixed
	 */
    public static function get_item_downloads( $object, $item ){
        if( self::is_deprecated() ){
            return $object->get_item_downloads( $item );
        }
        return $item->get_item_downloads( );
    }

	/**
	 * @param WC_Abstract_Legacy_Order|WC_Abstract_Order $order
	 *
	 * @return int
	 */
    public static function get_order_id( $order ){
        if( self::is_deprecated() ){
	        /** @noinspection Annotator */
	        return $order->id;
        }
        return $order->get_id();
    }
}
