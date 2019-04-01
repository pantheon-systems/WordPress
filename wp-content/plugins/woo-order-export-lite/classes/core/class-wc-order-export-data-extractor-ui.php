<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Data_Extractor_UI extends WC_Order_Export_Data_Extractor {
	static $object_type = 'shop_order';
	const  HUGE_SHOP_CUSTOMERS = 1000;// more than 1000 customers

	// ADD custom fields for export
	public static function get_all_order_custom_meta_fields( $sql_order_ids = '' ) {
		global $wpdb;

		$transient_key = 'woe_get_all_order_custom_meta_fields_results_' . md5( json_encode( $sql_order_ids ) ); // complex key
		$fields        = get_transient( $transient_key );
		if ( $fields === false ) {
			$sql_in_orders = '';
			if ( $sql_order_ids ) {
				$sql_in_orders = " AND ID IN ($sql_order_ids) ";
			}

			// must show all
			if ( ! $sql_in_orders ) {
				//rewrite for huge # of users
				$total_users = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" );
				if ( $total_users >= self::HUGE_SHOP_CUSTOMERS ) {
					$user_ids    = $wpdb->get_col( "SELECT  ID FROM {$wpdb->users} ORDER BY ID DESC LIMIT 1000" ); // take last 1000
					$user_ids    = join( ",", $user_ids );
					$where_users = "WHERE user_id IN ($user_ids)";
				} else {
					$where_users = '';
				}
				$user_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta} $where_users" );
				$fields      = self::get_order_custom_fields();
			} else {
				$user_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->posts} INNER JOIN {$wpdb->usermeta} ON {$wpdb->posts}.post_author = {$wpdb->usermeta}.user_id WHERE post_type = '" . self::$object_type . "' {$sql_in_orders}" );
				$fields      = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE post_type = '" . self::$object_type . "' {$sql_in_orders}" );
			}

			foreach ( $user_fields as $k => $v ) {
				$user_fields[ $k ] = 'USER_' . $v;
			}
			$fields = array_unique( array_merge( $fields, $user_fields ) );
			sort( $fields );
			//debug set_transient( $transient_key, $fields, 60 ); //valid for a 1 min
		}

		return apply_filters( 'woe_get_all_order_custom_meta_fields', $fields );
	}

	//filter attributes by matched orders
	public static function get_all_product_custom_meta_fields_for_orders( $sql_order_ids ) {
		global $wpdb;

		$wc_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id IN
									(SELECT DISTINCT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type = 'line_item' AND order_id IN ($sql_order_ids))" );

		// WC internal table add attributes
		$wc_attr_fields = $wpdb->get_results( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies" );
		foreach ( $wc_attr_fields as $f ) {
			$wc_fields[] = 'pa_' . $f->attribute_name;
		}

		//sql to gather product id for orders
		$sql_products = "SELECT DISTINCT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key ='_product_id' AND order_item_id IN
									(SELECT DISTINCT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type = 'line_item' AND order_id IN ($sql_order_ids))";

		$wp_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE post_id IN
									(SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type IN ('product','product_variation') AND ID IN ($sql_products))" );

		$fields = array_unique( array_merge( $wp_fields, $wc_fields ) );
		$fields = sort( $fields );

		return apply_filters( 'get_all_product_custom_meta_fields_for_orders', $fields );
	}

	public static function get_order_item_custom_meta_fields_for_orders( $sql_order_ids ) {
		global $wpdb;

		$wc_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id IN
									(SELECT DISTINCT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type = 'line_item' AND order_id IN ($sql_order_ids))" );
		// WC internal table add attributes
		$wc_attr_fields = $wpdb->get_results( "SELECT DISTINCT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies" );
		foreach ( $wc_attr_fields as $f ) {
			$wc_fields[] = 'pa_' . $f->attribute_name;
		}

		$wc_fields = array_unique( $wc_fields );
		sort( $wc_fields );


		return apply_filters( 'get_order_item_custom_meta_fields_for_orders', $wc_fields );
	}

	public static function get_product_custom_meta_fields_for_orders( $sql_order_ids ) {
		global $wpdb;

		$sql_products = "SELECT DISTINCT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key ='_product_id' AND order_item_id IN
									(SELECT DISTINCT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type = 'line_item' AND order_id IN ($sql_order_ids))";

		$wp_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE post_id IN
									(SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type IN ('product','product_variation') AND ID IN ($sql_products))" );

		sort( $wp_fields );

		return apply_filters( 'get_product_custom_meta_fields_for_orders', $wp_fields );
	}

	public static function get_all_product_custom_meta_fields() {
		global $wpdb;

		$wc_fields = self::get_product_itemmeta();

		// WC internal table add attributes
		$wc_attr_fields = $wpdb->get_results( "SELECT attribute_name FROM {$wpdb->prefix}woocommerce_attribute_taxonomies" );
		foreach ( $wc_attr_fields as $f ) {
			$wc_fields[] = 'pa_' . $f->attribute_name;
		}

		// WP internal table	, skip hidden and attributes
		$wp_fields = self::get_product_custom_fields();

		$fields = array_unique( array_merge( $wp_fields, $wc_fields ) );
		sort( $fields );

		return apply_filters( 'woe_get_all_product_custom_meta_fields', $fields );
	}

	public static function get_all_coupon_custom_meta_fields() {
		global $wpdb;

		// WP internal table	, skip hidden and attributes
		$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
											WHERE post_type = 'shop_coupon'" );
		sort( $fields );

		return apply_filters( 'woe_get_all_coupon_custom_meta_fields', $fields );
	}

	//for FILTERS

	public static function get_products_like( $like, $limit ) {
		global $wpdb;
		$like  = $wpdb->esc_like( $like );
		$query = "
                SELECT      post.ID as id,post.post_title as text,att.ID as photo_id,att.guid as photo_url
                FROM        " . $wpdb->posts . " as post
                LEFT JOIN  " . $wpdb->posts . " AS att ON post.ID=att.post_parent AND att.post_type='attachment'
                WHERE       post.post_title LIKE %s
                AND         post.post_type = 'product'
                AND         post.post_status <> 'trash'
                GROUP BY    post.ID
                ORDER BY    post.post_title
                LIMIT " . intval( $limit );

		$products = $wpdb->get_results( $wpdb->prepare($query, '%'.$like.'%') );
		foreach ( $products as $key => $product ) {
			if ( $product->photo_id ) {
				$photo                       = wp_get_attachment_image_src( $product->photo_id, 'thumbnail' );
				$products[ $key ]->photo_url = $photo[0];
			} else {
				unset( $products[ $key ]->photo_url );
			}
		}

		return $products;
	}

	public static function get_users_like( $like ) {
		global $wpdb;
		$ret = array();

		$like  = '*' . $wpdb->esc_like( $like ) . '*';
		$users = get_users( array( 'search' => $like, 'orderby' => 'display_name' ) );

		foreach ( $users as $key => $user ) {
			$ret[] = array(
				'id'   => $user->ID,
				'text' => $user->display_name,
			);
		}

		return $ret;
	}

	public static function get_coupons_like( $like ) {
		global $wpdb;

		$like  = $wpdb->esc_like( $like );
		$query = "
                SELECT      post.post_title as id, post.post_title as text
                FROM        " . $wpdb->posts . " as post
                WHERE       post.post_title LIKE %s
                AND         post.post_type = 'shop_coupon'
                AND         post.post_status <> 'trash'
                ORDER BY    post.post_title
                LIMIT 0,10
        ";

		return $wpdb->get_results( $wpdb->prepare($query, '%'.$like.'%') );
	}

	public static function get_categories_like( $like ) {
		$cat = array();
		foreach (
			get_terms( 'product_cat', 'hide_empty=0&hierarchical=1&name__like=' . $like . '&number=10' ) as $term
		) {
			$cat[] = array( "id" => $term->term_id, "text" => $term->name );
		}

		return $cat;
	}

	public static function get_order_custom_fields_values( $key ) {
		global $wpdb;
		$values = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s  AND post_id IN (SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = '" . self::$object_type . "' )",
			$key ) );
		sort( $values );

		return $values;
	}

	public static function get_user_custom_fields_values( $key ) {
		global $wpdb;
		$values = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s",
			$key ) );
		sort( $values );

		return $values;
	}

	public static function get_product_custom_fields_values( $key ) {
		global $wpdb;
		$values = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s    AND post_id IN (SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = 'product_variation' OR post_type = 'product')",
			$key ) );
		sort( $values );

		return $values;
	}

	public static function get_products_taxonomies_values( $key ) {
		$values = array();
		$terms  = get_terms( array( 'taxonomy' => $key ) );
		if ( ! empty( $terms ) ) {
			$values = array_map( function ( $term ) {
				return $term->name;
			}, $terms );
		}
		sort( $values );

		return $values;
	}

	public static function get_products_itemmeta_values( $key ) {
		global $wpdb;
		$max_len      = apply_filters( 'woe_itemmeta_values_max_length', 50 );
		$limit        = apply_filters( 'woe_itemmeta_values_max_records', 200 );
		$meta_key_ent = esc_html( $key );
		$metas        = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta where (meta_key = '%s' OR meta_key='%s') AND LENGTH(meta_value) <= $max_len LIMIT $limit",
			$key, $meta_key_ent ) );
		sort( $metas );

		return $metas;
	}

	public static function get_products_attributes_values( $key ) {
		$data  = array();
		$attrs = wc_get_attribute_taxonomies();
		foreach ( $attrs as $item ) {
			if ( $item->attribute_label == $key && $item->attribute_type != 'select' ) {
				break;
			} elseif ( $item->attribute_label == $key ) {
				$name   = wc_attribute_taxonomy_name( $item->attribute_name );
				$values = get_terms( $name, array( 'hide_empty' => false ) );
				if ( is_array( $values ) ) {
					$data = array_map( function ( $elem ) {
						return $elem->slug;
					}, $values );
				}
				break;
			}
		}
		sort( $data );

		return $data;
	}

	public static function get_order_meta_values( $type, $key ) {
		global $wpdb;
		$query   = $wpdb->prepare( 'SELECT DISTINCT meta_value FROM ' . $wpdb->postmeta . ' WHERE meta_key = %s',
			array( $type . strtolower( $key ) ) );
		$results = $wpdb->get_col( $query );
		$data    = array_filter( $results );
		sort( $data );

		return $data;
	}

	public static function get_order_item_names( $type ) {
		global $wpdb;

		$names = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT order_item_name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type = %s ORDER BY order_item_id DESC LIMIT 1000",
			$type ) );
		sort( $names );

		return $names;
	}

	public static function get_item_meta_keys() {
		global $wpdb;

		$names = $wpdb->get_results( "SELECT distinct order_item_type,meta_key  FROM  {$wpdb->prefix}woocommerce_order_items AS items
			INNER JOIN (SELECT ID AS order_id FROM {$wpdb->prefix}posts WHERE post_type='shop_order' ORDER BY ID DESC LIMIT 1000) AS orders ON orders.order_id = items.order_id 
			JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS meta ON meta.order_item_id = items.order_item_id
			ORDER BY order_item_type,meta_key" );

		$keys = array();
		foreach ( $names as $n ) {
			$keys[ $n->order_item_type ][ $n->meta_key ] = $n->meta_key;
		}

		return $keys;
	}

	public static function get_order_item_meta_key_values( $meta_key ) {
		global $wpdb;

		self::extract_item_type_and_key( $meta_key, $type, $key );

		//we skip serialized and long values!
		$values = $wpdb->get_col( $wpdb->prepare( "SELECT distinct meta_value FROM  {$wpdb->prefix}woocommerce_order_items AS items
			JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS meta ON meta.order_item_id = items.order_item_id
			WHERE items.order_item_type = %s AND meta.meta_key=%s 
				AND meta_value NOT LIKE  'a:%' AND LENGTH(meta_value)<20
			ORDER BY meta_value", $type, $key ) );

		return $values;
	}


	public static function get_order_product_fields( $format ) {
		$map = array(
			'item_id'                     => array(
				'label'   => __( 'Item ID', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'line_id'                     => array(
				'label'   => __( 'Item #', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'number',
			),
			'sku'                         => array(
				'label'   => __( 'SKU', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'name'                        => array(
				'label'   => __( 'Item Name', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'product_id'                  => array(
				'label'   => __( 'Product Id', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'product_name'                => array(
				'label'   => __( 'Product Name', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'variation_id'                => array(
				'label'   => __( 'Variation Id', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'product_variation'           => array(
				'label'   => __( 'Product Variation', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'seller'                      => array(
				'label'   => __( 'Item Seller', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'qty'                         => array(
				'label'   => __( 'Quantity', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'number',
			),
			'qty_minus_refund'            => array(
				'label'   => __( 'Quantity (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'item_price'                  => array(
				'label'   => __( 'Item Cost', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'price'                       => array(
				'label'   => __( 'Product Current Price', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_no_tax'                 => array(
				'label'   => __( 'Order Line (w/o tax)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_tax'                    => array(
				'label'   => __( 'Order Line Tax', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_tax_refunded'           => array(
				'label'   => __( 'Order Line Tax Refunded', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_tax_minus_refund'       => array(
				'label'   => __( 'Order Line Tax (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_subtotal'               => array(
				'label'   => __( 'Order Line Subtotal', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_subtotal_tax'           => array(
				'label'   => __( 'Order Line Subtotal Tax', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_total'                  => array(
				'label'   => __( 'Order Line Total', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_total_plus_tax'         => array(
				'label'   => __( 'Order Line Total (include tax)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_total_refunded'         => array(
				'label'   => __( 'Order Line Total Refunded', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'line_total_minus_refund'     => array(
				'label'   => __( 'Order Line Total (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'discount_amount'             => array(
				'label'   => __( 'Item Discount Amount', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'tax_rate'                    => array(
				'label'   => __( 'Item Tax Rate', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'type'                        => array(
				'label'   => __( 'Type', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'category'                    => array(
				'label'   => __( 'Category', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'tags'                        => array(
				'label'   => __( 'Tags', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'width'                       => array(
				'label'   => __( 'Width', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'length'                      => array(
				'label'   => __( 'Length', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'height'                      => array(
				'label'   => __( 'Height', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'weight'                      => array(
				'label'   => __( 'Weight', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'product_url'                 => array(
				'label'   => __( 'Product URL', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'download_url'                => array(
				'label'   => __( 'Download URL', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'image_url'                   => array(
				'label'   => __( 'Image URL', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'product_shipping_class'      => array(
				'label'   => __( 'Product Shipping Class', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'post_content'                => array(
				'label'   => __( 'Description', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'post_excerpt'                => array(
				'label'   => __( 'Short Description', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'full_category_names'         => array(
				'label'   => __( 'Full names for categories', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'summary_report_total_qty'    => array(
				'label'   => __( 'Summary Report Total Quantity', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'summary_report_total_amount' => array(
				'label'   => __( 'Summary Report Total Amount', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
		);

		foreach ( $map as $key => $value ) {
			$map[ $key ]['colname'] = $value['label'];
			$map[ $key ]['default'] = 1;
		}

		return apply_filters( 'woe_get_order_product_fields', $map, $format );
	}

	public static function get_order_coupon_fields( $format ) {
		$map = array(
			'code'                     => array(
				'label'   => __( 'Coupon Code', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'discount_amount'          => array(
				'label'   => __( 'Discount Amount', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'discount_amount_tax'      => array(
				'label'   => __( 'Discount Amount Tax', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'discount_amount_plus_tax' => array(
				'label'   => __( 'Discount Amount + Tax', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'excerpt'                  => array(
				'label'   => __( 'Coupon Description', 'woo-order-export-lite' ),
				'checked' => 0,
			),
			'discount_type'            => array(
				'label'   => __( 'Coupon Type', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'coupon_amount'            => array(
				'label'   => __( 'Coupon Amount', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
		);

		foreach ( $map as $key => $value ) {
			$map[ $key ]['colname'] = $value['label'];
			$map[ $key ]['default'] = 1;
		}

		return apply_filters( 'woe_get_order_coupon_fields', $map, $format );
	}


	public static function get_order_fields( $format, $segments = array() ) {
		if ( ! $segments ) {
			$segments = array(
				'common',
				'user',
				'billing',
				'shipping',
				'products',
				'coupons',
				'cart',
				'ship_calc',
				'totals',
				'misc',
			);
		}
		$map = array();
		foreach ( $segments as $segment ) {
			$method      = "get_order_fields_" . $segment;
			$map_segment = self::$method();

			foreach ( $map_segment as $key => $value ) {
				$map_segment[ $key ]['segment'] = $segment;
				$map_segment[ $key ]['colname'] = $value['label'];
				$map_segment[ $key ]['default'] = 1; //debug
			}
			// woe_get_order_fields_common	filter
			$map_segment = apply_filters( "woe_$method", $map_segment, $format );
			$map         = array_merge( $map, $map_segment );
		}

		return apply_filters( 'woe_get_order_fields', $map );
	}

	public static function get_order_fields_common() {
		return array(
			'line_number'       => array(
				'label'   => __( 'Line number', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'order_id'          => array(
				'label'   => __( 'Order ID', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'order_number'      => array(
				'label'   => __( 'Order Number', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'order_status'      => array(
				'label'   => __( 'Order Status', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'order_date'        => array(
				'label'   => __( 'Order Date', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'date',
			),
			'modified_date'     => array(
				'label'   => __( 'Modification Date', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'date',
			),
			'transaction_id'    => array(
				'label'   => __( 'Transaction ID', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'order_currency'    => array(
				'label'   => __( 'Currency', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'completed_date'    => array(
				'label'   => __( 'Completed Date', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'date',
			),
			'paid_date'         => array(
				'label'   => __( 'Paid Date', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'date',
			),
			'first_refund_date' => array(
				'label'   => __( 'Date of first refund', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'date',
			),
			'customer_note'     => array(
				'label'   => __( 'Customer Note', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'order_notes'       => array(
				'label'   => __( 'Order Notes', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
		);
	}

	public static function get_order_fields_user() {
		return array(
			'customer_ip_address'   => array(
				'label'   => __( 'Customer IP address', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'customer_user'         => array(
				'label'   => __( 'Customer User ID', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'user_login'            => array(
				'label'   => __( 'Customer Username', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'user_url'              => array(
				'label'   => __( 'User Website', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'user_email'            => array(
				'label'   => __( 'Customer User Email', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'user_role'             => array(
				'label'   => __( 'Customer Role', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'customer_total_orders' => array(
				'label'   => __( 'Customer Total Orders', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
		);
	}

	public static function get_order_fields_billing() {
		return array(
			'billing_first_name'   => array(
				'label'   => __( 'First Name (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_last_name'    => array(
				'label'   => __( 'Last Name (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_full_name'    => array(
				'label'   => __( 'Full Name (Billing)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'billing_company'      => array(
				'label'   => __( 'Company (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_address'      => array(
				'label'   => __( 'Address 1&2 (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_address_1'    => array(
				'label'   => __( 'Address 1 (Billing)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'billing_address_2'    => array(
				'label'   => __( 'Address 2 (Billing)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'billing_city'         => array(
				'label'   => __( 'City (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_state'        => array(
				'label'   => __( 'State Code (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_citystatezip' => array(
				'label'   => __( 'City, State, Zip (Billing)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'billing_citystatezip_us' => array(
				'label'   => __( 'City, State Zip (Billing)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'billing_state_full'   => array(
				'label'   => __( 'State Name (Billing)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'billing_postcode'     => array(
				'label'   => __( 'Postcode (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_country'      => array(
				'label'   => __( 'Country Code (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_country_full' => array(
				'label'   => __( 'Country Name (Billing)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'billing_email'        => array(
				'label'   => __( 'Email (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'billing_phone'        => array(
				'label'   => __( 'Phone (Billing)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
		);
	}

	public static function get_order_fields_shipping() {
		return array(
			'shipping_first_name'   => array(
				'label'   => __( 'First Name (Shipping)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_last_name'    => array(
				'label'   => __( 'Last Name (Shipping)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_full_name'    => array(
				'label'   => __( 'Full Name (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'shipping_company'      => array(
				'label'   => __( 'Company (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'shipping_address'      => array(
				'label'   => __( 'Address 1&2 (Shipping)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_address_1'    => array(
				'label'   => __( 'Address 1 (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'shipping_address_2'    => array(
				'label'   => __( 'Address 2 (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'shipping_city'         => array(
				'label'   => __( 'City (Shipping)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_state'        => array(
				'label'   => __( 'State Code (Shipping)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_citystatezip' => array(
				'label'   => __( 'City, State, Zip (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'shipping_citystatezip_us' => array(
				'label'   => __( 'City, State Zip (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'shipping_state_full'   => array(
				'label'   => __( 'State Name (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'shipping_postcode'     => array(
				'label'   => __( 'Postcode (Shipping)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_country'      => array(
				'label'   => __( 'Country Code (Shipping)', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_country_full' => array(
				'label'   => __( 'Country Name (Shipping)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
		);
	}

	// meta
	public static function get_order_fields_products() {
		return array(
			'products' => array(
				'label'    => __( 'Products', 'woo-order-export-lite' ),
				'checked'  => 1,
				'repeat'   => 'rows',
				'max_cols' => 10,
			),
		);
	}

	// meta
	public static function get_order_fields_coupons() {
		return array(
			'coupons' => array(
				'label'    => __( 'Coupons', 'woo-order-export-lite' ),
				'checked'  => 1,
				'repeat'   => 'rows',
				'max_cols' => 10,
			),
		);
	}

	public static function get_order_fields_cart() {
		return array(
			'shipping_method_title'         => array(
				'label'   => __( 'Shipping Method Title', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'shipping_method'               => array(
				'label'   => __( 'Shipping Method', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'payment_method_title'          => array(
				'label'   => __( 'Payment Method Title', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'string',
			),
			'payment_method'                => array(
				'label'   => __( 'Payment Method', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'coupons_used'                  => array(
				'label'   => __( 'Coupons Used', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'string',
			),
			'cart_discount'                 => array(
				'label'   => __( 'Cart Discount Amount', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'cart_discount_tax'             => array(
				'label'   => __( 'Cart Discount Amount Tax', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_subtotal'                => array(
				'label'   => __( 'Order Subtotal Amount', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'order_subtotal_minus_discount' => array(
				'label'   => __( 'Order Subtotal - Cart Discount', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_subtotal_refunded'       => array(
				'label'   => __( 'Order Subtotal Amount Refunded', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_subtotal_minus_refund'   => array(
				'label'   => __( 'Order Subtotal Amount (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_tax'                     => array(
				'label'   => __( 'Order Tax Amount', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
		);
	}

	public static function get_order_fields_ship_calc() {
		return array(
			'order_shipping'                  => array(
				'label'   => __( 'Order Shipping Amount', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'order_shipping_plus_tax'         => array(
				'label'   => __( 'Order Shipping + Tax Amount', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_shipping_refunded'         => array(
				'label'   => __( 'Order Shipping Amount Refunded', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_shipping_minus_refund'     => array(
				'label'   => __( 'Order Shipping Amount (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_shipping_tax'              => array(
				'label'   => __( 'Order Shipping Tax Amount', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_shipping_tax_refunded'     => array(
				'label'   => __( 'Order Shipping Tax Refunded', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_shipping_tax_minus_refund' => array(
				'label'   => __( 'Order Shipping Tax Amount (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
		);
	}

	public static function get_order_fields_totals() {
		return array(
			'order_total_fee'              => array(
				'label'   => __( 'Order Total Fee', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_refund'                 => array(
				'label'   => __( 'Order Refund Amount', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'order_total_inc_refund'       => array(
				'label'   => __( 'Order Total Amount (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_total'                  => array(
				'label'   => __( 'Order Total Amount', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'order_total_no_tax'           => array(
				'label'   => __( 'Order Total Amount without Tax', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_total_tax'              => array(
				'label'   => __( 'Order Total Tax Amount', 'woo-order-export-lite' ),
				'checked' => 1,
				'format'  => 'money',
			),
			'order_total_tax_refunded'     => array(
				'label'   => __( 'Order Total Tax Amount Refunded', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
			'order_total_tax_minus_refund' => array(
				'label'   => __( 'Order Total Tax Amount (- Refund)', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'money',
			),
		);
	}

	public static function get_order_fields_misc() {
		return array(
			'total_weight_items'    => array(
				'label'   => __( 'Total weight', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'count_total_items'     => array(
				'label'   => __( 'Total items', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'count_exported_items'  => array(
				'label'   => __( 'Exported items', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
			'count_unique_products' => array(
				'label'   => __( 'Total products', 'woo-order-export-lite' ),
				'checked' => 0,
				'format'  => 'number',
			),
		);
	}

	// for UI only
	public static function get_visible_segments( $fields ) {
		$sections = array();
		foreach ( $fields as $field ) {
			if ( $field['checked'] ) {
				$sections[ $field['segment'] ] = 1;
			}
		}

		return array_keys( $sections );
	}

	public static function get_order_segments() {
		return array(
			'common'    => __( 'Common', 'woo-order-export-lite' ),
			'user'      => __( 'User', 'woo-order-export-lite' ),
			'billing'   => __( 'Billing Address', 'woo-order-export-lite' ),
			'shipping'  => __( 'Shipping Address', 'woo-order-export-lite' ),
			'products'  => __( 'Products', 'woo-order-export-lite' ),
			'coupons'   => __( 'Coupons', 'woo-order-export-lite' ),
			'cart'      => __( 'Cart', 'woo-order-export-lite' ),
			'ship_calc' => __( 'Shipping', 'woo-order-export-lite' ),
			'totals'    => __( 'Totals', 'woo-order-export-lite' ),
			'misc'      => __( 'Others', 'woo-order-export-lite' ),
		);
	}

	public static function get_format_fields() {
		return array(
			'string' => __( 'String', 'woo-order-export-lite' ),
			'money'  => __( 'Money', 'woo-order-export-lite' ),
			'number' => __( 'Number', 'woo-order-export-lite' ),
			'date'   => __( 'Date', 'woo-order-export-lite' ),
		);
	}


}