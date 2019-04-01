<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Data_Extractor {
	static $statuses;
	static $countries;
	static $prices_include_tax;
	static $current_order;
	static $object_type = 'shop_order';
	static $export_subcategories_separator;
	static $export_line_categories_separator;
	static $export_itemmeta_values_separator;
	static $export_custom_fields_separator;
	static $track_sql_queries = false;
	static $sql_queries;
	static $operator_must_check_values = array( 'LIKE', '>', '<', '>=', '<=' );
	const  HUGE_SHOP_ORDERS    = 1000;// more than 1000 orders
	const  HUGE_SHOP_PRODUCTS  = 1000;// more than 1000 products
	const  HUGE_SHOP_CUSTOMERS = 1000;// more than 1000 users


	//Common

	// to parse "item_type:meta_key" strings
	public static function extract_item_type_and_key( $meta_key, &$type, &$key ) {
		$t    = explode( ":", $meta_key );
		$type = array_shift( $t );
		$key  = join( ":", $t );
	}

	public static function get_order_custom_fields() {
		global $wpdb;
		$transient_key = 'woe_get_order_custom_fields_result';

		$fields = get_transient( $transient_key );
		if ( $fields === false ) {
			$total_orders = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}  WHERE post_type = '" . self::$object_type . "'" );
			//small shop , take all orders
			if ( $total_orders < self::HUGE_SHOP_ORDERS ) {
				$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE post_type = '" . self::$object_type . "'" );
			} else { // we have a lot of orders, take last good orders, upto 1000
				$order_ids   = $wpdb->get_col( "SELECT  ID FROM {$wpdb->posts} WHERE post_type = '" . self::$object_type . "' AND post_status IN('wc-on-hold','wc-processing','wc-completed')  ORDER BY post_date DESC LIMIT 1000" );
				$order_ids[] = 0; // add fake zero
				$order_ids   = join( ",", $order_ids );
				$fields      = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta}  WHERE post_id IN ($order_ids)" );
			}
			sort( $fields );
			set_transient( $transient_key, $fields, 60 ); //valid for a minute
		}

		return apply_filters( 'woe_get_order_custom_fields', $fields );
	}

	public static function get_user_custom_fields() {
		global $wpdb;
		$transient_key = 'woe_get_user_custom_fields_result';

		$fields = get_transient( $transient_key );
		if ( $fields === false ) {
			$total_users = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->users}" );
			if ( $total_users < self::HUGE_SHOP_CUSTOMERS ) {
				$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta}" );
			} else { // we have a lot of users, so take last users, upto 1000
				$user_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->users} ORDER BY ID DESC LIMIT 1000" );
				$user_ids = join( ",", $user_ids );
				$fields   = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta}  WHERE user_id IN ($user_ids)" );
			}
			sort( $fields );
			set_transient( $transient_key, $fields, 60 ); //valid for a minute
		}

		return apply_filters( 'woe_get_user_custom_fields', $fields );
	}

	public static function get_product_attributes() {
		global $wpdb;

		$attrs = array();

		// WC internal table , skip hidden and attributes
		$wc_fields = $wpdb->get_results( "SELECT attribute_name,attribute_label FROM {$wpdb->prefix}woocommerce_attribute_taxonomies" );
		foreach ( $wc_fields as $f ) {
			$attrs[ 'pa_' . $f->attribute_name ] = $f->attribute_label;
		}


		// WP internal table, take all attributes
		$wp_fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} INNER JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                                            WHERE meta_key LIKE 'attribute\_%' AND post_type = 'product_variation'" );
		foreach ( $wp_fields as $attr ) {
			$attr = str_replace( "attribute_", "", $attr );
			if ( substr( $attr, 0, 3 ) == 'pa_' ) // skip attributes from WC table
			{
				continue;
			}
			$name           = str_replace( "-", " ", $attr );
			$name           = ucwords( $name );
			$attrs[ $attr ] = $name;
		}
		asort( $attrs );

		return apply_filters( 'woe_get_product_attributes', $attrs );
	}

	public static function get_product_itemmeta() {
		global $wpdb;
		$transient_key = 'woe_get_product_itemmeta_result';

		$metas = get_transient( $transient_key );
		if ( $metas === false ) {
			// WP internal table, take all metas
			$metas = $wpdb->get_col( "SELECT DISTINCT meta.meta_key FROM {$wpdb->prefix}woocommerce_order_itemmeta meta inner join {$wpdb->prefix}woocommerce_order_items item on item.order_item_id=meta.order_item_id and item.order_item_type = 'line_item' " );
			sort( $metas );
			set_transient( $transient_key, $metas, 60 ); //valid for a minute
		}

		return apply_filters( 'woe_get_product_itemmeta', $metas );
	}

	public static function get_product_taxonomies() {
		global $wpdb;

		$attrs = array();

		if ( function_exists( "wc_get_attribute_taxonomies" ) ) {
			$wc_attrs = wc_get_attribute_taxonomies();
			foreach ( $wc_attrs as $attr ) {
				$attrs[ "pa_" . $attr->attribute_name ] = "pa_" . $attr->attribute_name;
			}
		}

		// WP internal table, take all taxonomies for products
		$wp_fields = $wpdb->get_col( "SELECT DISTINCT taxonomy FROM {$wpdb->term_relationships}
					JOIN {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id
					WHERE {$wpdb->term_relationships}.object_id IN  (SELECT DISTINCT ID FROM {$wpdb->posts} WHERE post_type = 'product' OR post_type='product_variation')" );
		foreach ( $wp_fields as $attr ) {
			$attrs[ $attr ] = $attr;
		}
		asort( $attrs );

		return apply_filters( 'woe_get_product_taxonomies', $attrs );
	}

	public static function get_product_custom_fields() {
		global $wpdb;
		$transient_key = 'woe_get_product_custom_fields_result';

		$fields = get_transient( $transient_key );
		if ( $fields === false ) {
			//rewrite for huge # of products
			$total_products = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}  WHERE  post_type = 'product' OR post_type='product_variation' " );
			//small shop , take all orders
			if ( $total_products < self::HUGE_SHOP_PRODUCTS ) {
				$fields = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE post_type = 'product' OR post_type='product_variation' " );
			} else { // we have a lot of orders, take last good orders, upto 1000
				$product_ids   = $wpdb->get_col( "SELECT  ID FROM {$wpdb->posts} WHERE post_type IN('product','product_variation')  ORDER BY post_date DESC LIMIT 1000" );
				$product_ids[] = 0; // add fake zero
				$product_ids   = join( ",", $product_ids );
				$fields        = $wpdb->get_col( "SELECT DISTINCT meta_key FROM {$wpdb->postmeta}  WHERE post_id IN ($product_ids)" );
			}
			sort( $fields );
			set_transient( $transient_key, $fields, 60 ); //valid for a minute
		}

		return apply_filters( 'woe_get_product_custom_fields', $fields );
	}

	//For ENGINE
	private static function parse_pairs( $pairs, $valid_types, $mode = '' ) {
		$pair_types = array();
		foreach ( $pairs as $pair ) {
			list( $filter_type, $filter_value ) = array_map( 'trim', explode( "=", trim( $pair ) ) );
			if ( $mode == 'lower_filter_label' ) {
				$filter_type = strtolower( $filter_type );
			} // Country=>country for locations
			if ( ! in_array( $filter_type, $valid_types ) ) {
				continue;
			}
			if ( ! isset( $pair_types[ $filter_type ] ) ) {
				$pair_types[ $filter_type ] = array();
			}
			$pair_types[ $filter_type ][] = $filter_value;
		}

		return $pair_types;
	}

	private static function parse_complex_pairs( $pairs, $valid_types = false, $mode = '' ) {
		$pair_types = array();
		$delimiters = array(
			'NOT SET' => 'NOT SET',
			'IS SET'  => 'IS SET',
			'LIKE'    => 'LIKE',
			'<>'      => 'NOT IN',
			'>='      => '>=',
			'<='      => '<=',
			'>'       => '>',
			'<'       => '<',
			'='       => 'IN',
		);
		$single_ops = array( 'NOT SET', 'IS SET' );

		foreach ( $pairs as $pair ) {
			$pair      = trim( $pair );
			$op        = '';
			$single_op = false;
			foreach ( $delimiters as $delim => $op_seek ) {
				$t         = explode( $delim, $pair );
				$single_op = in_array( $delim, $single_ops );
				if ( count( $t ) == 2 ) {
					$op = $op_seek;
					break;
				}
			}
			if ( ! $op ) {
				continue;
			}
			if ( $single_op ) {
				$t[1] = '';
			}

			list( $filter_type, $filter_value ) = array_map( "trim", $t );
			$empty = __( 'empty', 'woo-order-export-lite' );
			if ( $empty == $filter_value ) {
				$filter_value = '';
			}

			if ( $mode == 'lower_filter_label' ) {
				$filter_type = strtolower( $filter_type );
			} // Country=>country for locations

			if ( $valid_types AND ! in_array( $filter_type, $valid_types ) ) {
				continue;
			}

			$filter_type = addslashes( $filter_type );
			if ( ! isset( $pair_types[ $op ] ) ) {
				$pair_types[ $op ] = array();
			}
			if ( ! isset( $pair_types[ $op ] [ $filter_type ] ) ) {
				$pair_types[ $op ] [ $filter_type ] = array();
			}
			$pair_types[ $op ][ $filter_type ][] = addslashes( $filter_value );
		}

		return $pair_types;
	}

	private static function sql_subset( $arr_values ) {
		$values = array();
		foreach ( $arr_values as $s ) {
			$values[] = "'$s'";
		}

		return join( ",", $values );
	}


	public static function sql_get_order_ids( $settings ) {
		//$settings['product_categories'] = array(119);
		//$settings['products'] = array(4554);
		//$settings['shipping_locations'] = array("city=cityS","city=alex","postcode=12345");
		//$settings['product_attributes'] = array("pa_material=glass");
		return self::sql_get_order_ids_Ver1( $settings );
	}

	public static function sql_get_product_ids( $settings ) {
		global $wpdb;

		$product_where = self::sql_build_product_filter( $settings );

		$wc_order_items_meta        = "{$wpdb->prefix}woocommerce_order_itemmeta";
		$left_join_order_items_meta = $order_items_meta_where = array();

		// filter by product
		if ( $product_where ) {
			$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS orderitemmeta_product ON orderitemmeta_product.order_item_id = order_items.order_item_id";
			$order_items_meta_where[]     = " (orderitemmeta_product.meta_key IN ('_variation_id', '_product_id')   $product_where)";
		} else {
			$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS orderitemmeta_product ON orderitemmeta_product.order_item_id = order_items.order_item_id";
			$order_items_meta_where[]     = " orderitemmeta_product.meta_key IN ('_variation_id', '_product_id')";
		}

		//by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_attributes'] ) {
			$attrs        = self::get_product_attributes();
			$names2fields = array_flip( $attrs );
			$filters      = self::parse_complex_pairs( $settings['product_attributes'], $attrs );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					$field = $names2fields[ $field ];
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values                   = self::sql_subset( $values );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif ( in_array( $operator, self::$operator_must_check_values ) ) {
							$pairs = array();
							foreach ( $values as $v ) {
								$pairs[] = self::operator_compare_field_and_value( "`orderitemmeta_{$field}`.meta_value",
									$operator, $v );
							}
							$pairs                    = join( "OR", $pairs );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}
				}// values
			}// operators
		}

		//by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_itemmeta'] ) {
			foreach ( $settings['product_itemmeta'] as $value ) {
				$settings['product_itemmeta'][] = esc_html( $value );
			}

			$itemmeta = self::get_product_itemmeta();
			$filters  = self::parse_complex_pairs( $settings['product_itemmeta'], $itemmeta );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					;
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values                   = self::sql_subset( $values );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif ( in_array( $operator, self::$operator_must_check_values ) ) {
							$pairs = array();
							foreach ( $values as $v ) {
								$pairs[] = self::operator_compare_field_and_value( "`orderitemmeta_{$field}`.meta_value",
									$operator, $v );
							}
							$pairs                    = join( "OR", $pairs );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}// values
				}
			}// operators
		}

		$orders_where = array();
		self::apply_order_filters_to_sql( $orders_where, $settings );
		if ( $orders_where ) {
			$left_join_order_items_meta[] = "LEFT JOIN {$wpdb->posts}  AS `orders` ON `orders`.ID  = order_items.order_id";
			$order_items_meta_where[]     = "( " . join( " AND ", $orders_where ) . " )";
		}

		$order_items_meta_where = join( " AND ", $order_items_meta_where );
		if ( $order_items_meta_where ) {
			$order_items_meta_where = " AND " . $order_items_meta_where;
		}
		$left_join_order_items_meta = join( "  ", $left_join_order_items_meta );


		// final sql from WC tables
		if ( ! $order_items_meta_where ) {
			return false;
		}

		$sql = "SELECT DISTINCT p_id FROM
						(SELECT order_items.order_item_id as order_item_id, MAX(CONVERT(orderitemmeta_product.meta_value ,UNSIGNED INTEGER)) as p_id FROM {$wpdb->prefix}woocommerce_order_items as order_items
							$left_join_order_items_meta
							WHERE order_item_type='line_item' $order_items_meta_where GROUP BY order_item_id
						) AS temp";
		if ( self::$track_sql_queries ) {
			self::$sql_queries[] = $sql;
		}

		return $sql;
	}


	public static function sql_get_filtered_product_list( $settings ) {
		global $wpdb;

		// has exact products?
		if ( $settings['products'] ) {
			;// do nothing 
		} elseif ( empty( $settings['product_vendors'] ) AND empty( $settings['product_custom_fields'] ) ) {
			$settings['products'] = array();
		} else {
			$product_where = array( "1" );

			//by owners
			$settings['product_vendors'] = apply_filters( 'woe_sql_get_product_vendor_ids',
				$settings['product_vendors'], $settings );
			if ( $settings['product_vendors'] ) {
				$values          = self::sql_subset( $settings['product_vendors'] );
				$product_where[] = " products.post_author in ($values)";
			}

			//by custom fields in Product
			$product_meta_where     = "";
			$left_join_product_meta = "";
			if ( $settings['product_custom_fields'] ) {
				$left_join_product_meta = $product_meta_where = array();
				$cf_names               = self::get_product_custom_fields();
				$filters                = self::parse_complex_pairs( $settings['product_custom_fields'], $cf_names );
				$pos                    = 1;
				foreach ( $filters as $operator => $fields ) {
					foreach ( $fields as $field => $values ) {
						if ( $values ) {
							$left_join_product_meta[] = "LEFT JOIN {$wpdb->postmeta} AS productmeta_cf_{$pos} ON productmeta_cf_{$pos}.post_id = products.ID";
							if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
								$values               = self::sql_subset( $values );
								$product_meta_where[] = " (productmeta_cf_{$pos}.meta_key='$field'  AND productmeta_cf_{$pos}.meta_value $operator ($values)) ";
							} elseif ( in_array( $operator, self::$operator_must_check_values ) ) {
								$pairs = array();
								foreach ( $values as $v ) {
									$pairs[] = self::operator_compare_field_and_value( "`productmeta_cf_{$pos}`.meta_value",
										$operator, $v );
								}
								$pairs                = join( "OR", $pairs );
								$product_meta_where[] = " (productmeta_cf_{$pos}.meta_key='$field'  AND  ($pairs) ) ";
							}
							$pos ++;
						}//if values
					}
				}

				if ( $filters ) {
					$product_where[]        = join( " AND ", $product_meta_where );
					$left_join_product_meta = join( "  ", $left_join_product_meta );
				}
			}
			//done
			$product_where        = join( " AND ", $product_where );
			$sql                  = "SELECT DISTINCT ID FROM {$wpdb->posts} AS products $left_join_product_meta  WHERE products.post_type in ('product','product_variation') AND products.post_status<>'trash' AND $product_where ";
			$settings['products'] = $wpdb->get_col( $sql );
			if ( empty( $settings['products'] ) ) // failed condition!
			{
				$settings['products'] = array( 0 );
			}
		}

		//  we have to use variations , if user sets product attributes
		if ( $settings['products'] AND $settings['product_attributes'] ) {
			$values               = self::sql_subset( $settings['products'] );
			$sql                  = "SELECT DISTINCT ID FROM {$wpdb->posts} AS products WHERE products.post_type in ('product','product_variation') AND products.post_status<>'trash' AND post_parent<>0 AND post_parent IN ($values)";
			$settings['products'] = $wpdb->get_col( $sql );
			if ( empty( $settings['products'] ) ) // failed condition!
			{
				$settings['products'] = array( 0 );
			}
		}
		if ( ! empty( $sql ) AND self::$track_sql_queries ) {
			self::$sql_queries[] = $sql;
		}

		return apply_filters( 'woe_sql_adjust_products', $settings['products'], $settings );
	}


	public static function sql_build_product_filter( $settings ) {
		global $wpdb;

		//custom taxonomies
		$taxonomy_where = "";
		if ( $settings['product_taxonomies'] ) {
			$attrs        = self::get_product_taxonomies();
			$names2fields = array_flip( $attrs );
			$filters      = self::parse_complex_pairs( $settings['product_taxonomies'], $attrs );
			//print_r($filters );die();
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $label => $values ) {
					$field  = $names2fields[ $label ];
					$values = self::sql_subset( $values );
					if ( $values ) {
						$label          = esc_sql( $label );
						$taxonomy_where .= " AND orderitemmeta_product.meta_value  $operator (SELECT  object_id FROM {$wpdb->term_relationships} AS `{$field}_rel`
							INNER JOIN {$wpdb->term_taxonomy} AS `{$field}_cat` ON `{$field}_cat`.term_taxonomy_id = `{$field}_rel`.term_taxonomy_id
							WHERE `{$field}_cat`.taxonomy='$label' AND  `{$field}_cat`.term_id IN (SELECT term_id FROM {$wpdb->terms} WHERE name IN ($values) ) )";
					}
				}
			}
		}

		$product_category_where = "";
		if ( $settings['product_categories'] ) {
			$cat_ids = array( 0 );
			foreach ( $settings['product_categories'] as $cat_id ) {
				$cat_ids[] = $cat_id;
				foreach ( get_term_children( $cat_id, 'product_cat' ) as $child_id ) {
					$cat_ids[] = $child_id;
				}
			}
			$cat_ids                = join( ',', $cat_ids );
			$product_category_where = "SELECT  DISTINCT object_id FROM {$wpdb->term_relationships} AS product_in_cat
						LEFT JOIN {$wpdb->term_taxonomy} AS product_category ON product_category.term_taxonomy_id = product_in_cat.term_taxonomy_id
						WHERE product_category.term_id IN ($cat_ids) 
					";
			// get products and variations!
			$product_category_where = "AND orderitemmeta_product.meta_value IN
				(
					SELECT DISTINCT ID FROM {$wpdb->posts} AS product_category_variations WHERE post_parent<>0 AND post_parent IN ($product_category_where)
					UNION
					$product_category_where
				)
				";
		}

		$settings['products'] = self::sql_get_filtered_product_list( $settings );

		// deep level still
		$exact_product_where = '';
		if ( $settings['products'] ) {
			$values = self::sql_subset( $settings['products'] );
			if ( $values ) {
				$exact_product_where = "AND orderitemmeta_product.meta_value IN ($values)";
			}
		}
		$product_where = join( " ",
			array_filter( array( $taxonomy_where, $product_category_where, $exact_product_where ) ) );

		//skip empty values
		if ( $product_where ) {
			$product_where = "AND orderitemmeta_product.meta_value<>'0' " . $product_where;
		}

		return $product_where;
	}

	static function operator_compare_field_and_value( $field, $operator, $value ) {
		if ( $operator == "LIKE" ) {
			$value = "'%$value%'";
		} else { // compare numbers!
			$field = "cast($field as signed)";
		}

		return " $field $operator $value ";
	}

	public static function sql_get_order_ids_Ver1( $settings ) {
		global $wpdb;

		// deep level !
		$product_where = self::sql_build_product_filter( $settings );

		$wc_order_items_meta        = "{$wpdb->prefix}woocommerce_order_itemmeta";
		$left_join_order_items_meta = $order_items_meta_where = array();

		// filter by product
		if ( $product_where ) {
			$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS orderitemmeta_product ON orderitemmeta_product.order_item_id = order_items.order_item_id";
			$order_items_meta_where[]     = " (orderitemmeta_product.meta_key IN ('_variation_id', '_product_id') $product_where)";
		}


		//by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_attributes'] ) {
			$attrs        = self::get_product_attributes();
			$names2fields = @array_flip( $attrs );
			$filters      = self::parse_complex_pairs( $settings['product_attributes'], $attrs );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					$field = $names2fields[ $field ];
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values                   = self::sql_subset( $values );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif ( in_array( $operator, self::$operator_must_check_values ) ) {
							$pairs = array();
							foreach ( $values as $v ) {
								$pairs[] = self::operator_compare_field_and_value( "`orderitemmeta_{$field}`.meta_value",
									$operator, $v );
							}
							$pairs                    = join( "OR", $pairs );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}// values
				}
			}// operators
		}

		//by attrbutes in woocommerce_order_itemmeta
		if ( $settings['product_itemmeta'] ) {
			foreach ( $settings['product_itemmeta'] as $value ) {
				$settings['product_itemmeta'][] = esc_html( $value );
			}

			$itemmeta = self::get_product_itemmeta();
			$filters  = self::parse_complex_pairs( $settings['product_itemmeta'], $itemmeta );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					;
					if ( $values ) {
						$left_join_order_items_meta[] = "LEFT JOIN $wc_order_items_meta  AS `orderitemmeta_{$field}` ON `orderitemmeta_{$field}`.order_item_id = order_items.order_item_id";
						if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values                   = self::sql_subset( $values );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND `orderitemmeta_{$field}`.meta_value $operator  ($values) ) ";
						} elseif ( in_array( $operator, self::$operator_must_check_values ) ) {
							$pairs = array();
							foreach ( $values as $v ) {
								$pairs[] = self::operator_compare_field_and_value( "`orderitemmeta_{$field}`.meta_value",
									$operator, $v );
							}
							$pairs                    = join( "OR", $pairs );
							$order_items_meta_where[] = " (`orderitemmeta_{$field}`.meta_key='$field'  AND  ($pairs) ) ";
						}
					}// values
				}
			}// operators
		}

		$order_items_meta_where = join( " AND ", $order_items_meta_where );
		if ( $order_items_meta_where ) {
			$order_items_meta_where = " AND " . $order_items_meta_where;
		}
		$left_join_order_items_meta = join( "  ", $left_join_order_items_meta );


		// final sql from WC tables
		$order_items_where = "";
		if ( $order_items_meta_where ) {
			$order_items_where = " AND orders.ID IN (SELECT DISTINCT order_items.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_items
				$left_join_order_items_meta
				WHERE order_item_type='line_item' $order_items_meta_where )";
		}

		// by coupons
		if ( ! empty( $settings['any_coupon_used'] ) ) {
			$order_items_where .= " AND orders.ID IN (SELECT DISTINCT order_coupons.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_coupons
					WHERE order_coupons.order_item_type='coupon')";
		} elseif ( ! empty( $settings['coupons'] ) ) {
			$values            = self::sql_subset( $settings['coupons'] );
			$order_items_where .= " AND orders.ID IN (SELECT DISTINCT order_coupons.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_coupons
					WHERE order_coupons.order_item_type='coupon'  AND order_coupons.order_item_name in ($values) )";
		}
		// shipping methods
		if ( ! empty( $settings['shipping_methods'] ) ) {
			$zone_values = $zone_instance_values = $itemname_values = array();
			foreach ( $settings['shipping_methods'] as $value ) {
				if ( preg_match( '#^order_item_name:(.+)#', $value, $m ) ) {
					$itemname_values[] = $m[1];
				} else {
					$zone_values[] = $value;
					// for zones -- take instance_id!
					$m = explode( ":", $value );
					if ( count( $m ) > 1 ) {
						$zone_instance_values[] = $m[1];
					}
				}
			}

			// where by type!
			$ship_where = array();
			if ( $zone_values ) {
				$zone_values  = self::sql_subset( $zone_values );
				$ship_where[] = " (shipping_itemmeta.meta_key='method_id' AND shipping_itemmeta.meta_value IN ($zone_values) ) ";
			}
			if ( $zone_instance_values ) { //since WooCommerce 3.4+  instead of $zone_values
				$zone_instance_values = self::sql_subset( $zone_instance_values );
				$ship_where[]         = " (shipping_itemmeta.meta_key='instance_id' AND shipping_itemmeta.meta_value IN ($zone_instance_values ) ) ";
			}
			if ( $itemname_values ) {
				$itemname_values = self::sql_subset( $itemname_values );
				$ship_where[]    = " (order_shippings.order_item_name IN ( $itemname_values ) ) ";
			}
			$ship_where = join( ' OR ', $ship_where );

			//done 
			$order_items_where .= " AND orders.ID IN (SELECT order_shippings.order_id FROM {$wpdb->prefix}woocommerce_order_items as order_shippings
						LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS shipping_itemmeta ON  shipping_itemmeta.order_item_id = order_shippings.order_item_id
						WHERE order_shippings.order_item_type='shipping' AND $ship_where )";
		}

		// check item names ?
		if ( ! empty( $settings['item_names'] ) ) {
			$filters = self::parse_complex_pairs( $settings['item_names'],
				array( 'coupon', 'fee', 'line_item', 'shipping', 'tax' ) );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					if ( $values ) {
						if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values            = self::sql_subset( $values );
							$where_item_names  = " SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type='$field' AND order_item_name $operator ($values) ";
							$order_items_where .= " AND orders.ID IN ($where_item_names)";
						}
					}//if values
				}
			}
		}

		// check item metadata
		if ( ! empty( $settings['item_metadata'] ) ) {
			$filters = self::parse_complex_pairs( $settings['item_metadata'] );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					if ( $values ) {
						self::extract_item_type_and_key( $field, $type, $key );
						$key = esc_sql( $key );
						if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values              = self::sql_subset( $values );
							$where_item_metadata = " SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items AS items
												JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS meta ON meta.order_item_id = items.order_item_id
												WHERE order_item_type='$type' AND meta_key='$key' AND meta_value $operator ($values) ";
							$order_items_where   .= " AND orders.ID IN ($where_item_metadata)";
						}
					}//if values
				}
			}
		}


		// pre top
		$left_join_order_meta = $order_meta_where = $user_meta_where = $inner_join_user_meta = array();
		//add filter by custom fields in order

		if ( $settings['export_unmarked_orders'] ) {
			$pos                    = "export_unmarked_orders";
			$field                  = "woe_order_exported";
			$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_cf_{$pos} ON ordermeta_cf_{$pos}.post_id = orders.ID AND ordermeta_cf_{$pos}.meta_key='$field'";
			$order_meta_where []    = " ( ordermeta_cf_{$pos}.meta_value IS NULL ) ";
		}

		if ( $settings['order_custom_fields'] ) {
			$cf_names = self::get_order_custom_fields();
			$filters  = self::parse_complex_pairs( $settings['order_custom_fields'], $cf_names );
			$pos      = 1;
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					if ( $values ) {
						$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_cf_{$pos} ON ordermeta_cf_{$pos}.post_id = orders.ID AND ordermeta_cf_{$pos}.meta_key='$field'";
						if ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values              = self::sql_subset( $values );
							$order_meta_where [] = " ( ordermeta_cf_{$pos}.meta_value $operator ($values) ) ";
						} elseif ( $operator == 'NOT SET' ) {
							$order_meta_where [] = " ( ordermeta_cf_{$pos}.meta_value IS NULL ) ";
						} elseif ( $operator == 'IS SET' ) {
							$order_meta_where [] = " ( ordermeta_cf_{$pos}.meta_value IS NOT NULL ) ";
						} elseif ( in_array( $operator, self::$operator_must_check_values ) ) {
							$pairs = array();
							foreach ( $values as $v ) {
								$pairs[] = self::operator_compare_field_and_value( "`ordermeta_cf_{$pos}`.meta_value",
									$operator, $v );
							}
							$pairs              = join( "OR", $pairs );
							$order_meta_where[] = " ( $pairs ) ";
						}
						$pos ++;
					}//if values
				}
			}
		}
		if ( ! empty( $settings['user_custom_fields'] ) ) {
			$cf_names = self::get_user_custom_fields();
			$filters  = self::parse_complex_pairs( $settings['user_custom_fields'], $cf_names );
			$pos      = 1;
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					$inner_join_user_meta[] = "INNER JOIN {$wpdb->usermeta} AS usermeta_cf_{$pos} ON usermeta_cf_{$pos}.user_id = {$wpdb->users}.ID AND usermeta_cf_{$pos}.meta_key='$field'";
					if ( $values ) {
						if ( $operator == 'NOT SET' ) {
							$user_meta_where[] = " ( usermeta_cf_{$pos}.meta_value IS NULL ) ";
						} elseif ( $operator == 'IS SET' ) {
							$user_meta_where[] = " ( usermeta_cf_{$pos}.meta_value IS NOT NULL ) ";
						} elseif ( $operator == 'IN' OR $operator == 'NOT IN' ) {
							$values            = self::sql_subset( $values );
							$user_meta_where[] = " ( usermeta_cf_{$pos}.meta_value $operator ($values) ) ";
						} elseif ( in_array( $operator, self::$operator_must_check_values ) ) {
							$pairs = array();
							foreach ( $values as $v ) {
								$pairs[] = self::operator_compare_field_and_value( "`usermeta_cf_{$pos}`.meta_value",
									$operator, $v );
							}
							$pairs             = join( "OR", $pairs );
							$user_meta_where[] = " ( $pairs ) ";
						}
						$pos ++;
					}//if values
				}
			}
		}
		if ( $settings['shipping_locations'] ) {
			$filters = self::parse_complex_pairs( $settings['shipping_locations'],
				array( 'city', 'state', 'postcode', 'country' ), 'lower_filter_label' );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					$values = self::sql_subset( $values );
					if ( $values ) {
						$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
						$order_meta_where []    = " (ordermeta_{$field}.meta_key='_shipping_$field'  AND ordermeta_{$field}.meta_value $operator ($values)) ";
					}
				}
			}
		}
		if ( $settings['billing_locations'] ) {
			$filters = self::parse_complex_pairs( $settings['billing_locations'],
				array( 'city', 'state', 'postcode', 'country' ), 'lower_filter_label' );
			foreach ( $filters as $operator => $fields ) {
				foreach ( $fields as $field => $values ) {
					$values = self::sql_subset( $values );
					if ( $values ) {
						$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
						$order_meta_where []    = " (ordermeta_{$field}.meta_key='_billing_$field'  AND ordermeta_{$field}.meta_value $operator ($values)) ";
					}
				}
			}
		}

		// users
		$user_ids                    = array();
		$user_ids_ui_filters_applied = false;
		if ( ! empty( $settings['user_names'] ) ) {
			$user_ids          = array_filter( array_map( "intval", $settings['user_names'] ) );
			$values            = self::sql_subset( $user_ids );
			$user_meta_where[] = "( {$wpdb->users}.ID IN ($values) )";
		}
		//roles
		if ( ! empty( $settings['user_roles'] ) ) {
			$metakey                = $wpdb->get_blog_prefix() . 'capabilities';
			$inner_join_user_meta[] = "INNER JOIN {$wpdb->usermeta} AS usermeta_cf_role ON usermeta_cf_role.user_id = {$wpdb->users}.ID AND usermeta_cf_role.meta_key='$metakey'";

			$roles_where = array();
			foreach ( $settings['user_roles'] as $role ) {
				$roles_where[] = "( usermeta_cf_role.meta_value LIKE '%$role%' )";
			}
			$user_meta_where[] = "(" . join( ' OR ', $roles_where ) . ")";
		}
		if ( ! empty( $user_meta_where ) AND ! empty( $inner_join_user_meta ) ) {
			$user_meta_where      = join( ' AND ', $user_meta_where );
			$inner_join_user_meta = join( ' ', $inner_join_user_meta );
			$sql                  = "SELECT DISTINCT ID FROM {$wpdb->users} $inner_join_user_meta WHERE $user_meta_where";
			if ( self::$track_sql_queries ) {
				self::$sql_queries[] = $sql;
			}
			$user_ids                    = $wpdb->get_col( $sql );
			$user_ids_ui_filters_applied = true;
		}
		$user_ids = apply_filters( "woe_sql_get_customer_ids", $user_ids, $settings );
		if ( empty( $user_ids ) AND $user_ids_ui_filters_applied ) {
			$order_meta_where [] = "0"; // user filters failed
		}

		//apply filter
		if ( $user_ids ) {
			$field  = 'customer_user';
			$values = self::sql_subset( $user_ids );
			if ( $values ) {
				$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
				$order_meta_where []    = " (ordermeta_{$field}.meta_key='_customer_user'  AND ordermeta_{$field}.meta_value in ($values)) ";
			}
		}

		// payment methods
		if ( ! empty( $settings['payment_methods'] ) ) {
			$field  = 'payment_method';
			$values = self::sql_subset( $settings['payment_methods'] );

			$left_join_order_meta[] = "LEFT JOIN {$wpdb->postmeta} AS ordermeta_{$field} ON ordermeta_{$field}.post_id = orders.ID";
			$order_meta_where []    = " (ordermeta_{$field}.meta_key='_{$field}'  AND ordermeta_{$field}.meta_value in ($values)) ";
		}
		$order_meta_where = join( " AND ",
			apply_filters( "woe_sql_get_order_ids_order_meta_where", $order_meta_where ) );

		if ( $order_meta_where !== '' ) {
			$order_meta_where = " AND " . $order_meta_where;
		}
		$left_join_order_meta = join( "  ",
			apply_filters( "woe_sql_get_order_ids_left_joins", $left_join_order_meta ) );


		//top_level
		$where = array( 1 );
		self::apply_order_filters_to_sql( $where, $settings );
		$where     = apply_filters( 'woe_sql_get_order_ids_where', $where, $settings );
		$order_sql = join( " AND ", $where );

		//setup order types to work with
		$order_types = array( "'" . self::$object_type . "'" );
		if ( $settings['export_refunds'] ) {
			$order_types[] = "'shop_order_refund'";
		}
		$order_types = join( ",", apply_filters( "woe_sql_order_types", $order_types ) );

		$sql = "SELECT " . apply_filters( "woe_sql_get_order_ids_fields", "ID AS order_id" ) . " FROM {$wpdb->posts} AS orders
			{$left_join_order_meta}
			WHERE orders.post_type in ( $order_types) AND $order_sql $order_meta_where $order_items_where";

		if ( self::$track_sql_queries ) {
			self::$sql_queries[] = $sql;
		}

		//die($sql);
		return $sql;
	}

	private static function add_date_filter( &$where, &$where_meta, $date_field, $value ) {
		if ( $date_field == 'date_paid' OR $date_field == 'date_completed' ) // 3.0+ uses timestamp
		{
			$where_meta[] = "(order_$date_field.meta_value>0 AND order_$date_field.meta_value $value )";
		} elseif ( $date_field == 'paid_date' OR $date_field == 'completed_date' ) // previous versions use mysql datetime
		{
			$where_meta[] = "(order_$date_field.meta_value<>'' AND order_$date_field.meta_value " . $value . ")";
		} else {
			$where[] = "orders.post_" . $date_field . $value;
		}
	}

	private static function apply_order_filters_to_sql( &$where, $settings ) {
		global $wpdb;
		//default filter by date
		if ( ! isset( $settings['export_rule_field'] ) ) {
			$settings['export_rule_field'] = 'modified';
		}

		$date_field     = $settings['export_rule_field'];
		$use_timestamps = ( $date_field == 'date_paid' OR $date_field == 'date_completed' );
		//rename this field for 2.6 and less
		if ( ! method_exists( 'WC_Order', "get_date_completed" ) ) {
			$use_timestamps = false;
			if ( $date_field == 'date_paid' ) {
				$date_field = 'paid_date';
			} elseif ( $date_field == 'date_completed' ) {
				$date_field = 'completed_date';
			}
		}
		$where_meta = array();

		// export and date rule

		foreach ( self::get_date_range( $settings, true, $use_timestamps ) as $date ) {
			self::add_date_filter( $where, $where_meta, $date_field, $date );
		}

		// end export and date rule

		if ( $settings['statuses'] ) {
			$values = self::sql_subset( $settings['statuses'] );
			if ( $values ) {
				$where[] = "orders.post_status in ($values)";
			}
		}

		//for date_paid or date_completed
		if ( $where_meta ) {
			$where_meta = join( " AND ", $where_meta );
			$where[]    = "orders.id  IN ( SELECT post_id FROM {$wpdb->postmeta} AS order_$date_field WHERE order_$date_field.meta_key ='_$date_field' AND $where_meta)";
		}

		// skip child orders?
		if ( $settings['skip_suborders'] AND ! $settings['export_refunds'] ) {
			$where[] = "orders.post_parent=0";
		}

		// Skip drafts and deleted
		$where[] = "orders.post_status NOT in ('auto-draft','trash')";
	}

	public static function is_datetime_timestamp( $ts ) {
		return $ts % ( 24 * 3600 ) > 0;
	}

	public static function get_date_range( $settings, $is_for_sql, $use_timestamps = false ) {
		$result = array();

		// fixed date range 
		if ( ! empty( $settings['from_date'] ) OR ! empty( $settings['to_date'] ) ) {
			if ( $settings['from_date'] ) {
				$ts = strtotime( $settings['from_date'] );
				if ( self::is_datetime_timestamp( $ts ) ) {
					$from_date = date( 'Y-m-d H:i:s', $ts );
				} else {
					$from_date = date( 'Y-m-d', $ts ) . " 00:00:00";
				}
				if ( $is_for_sql ) {
					if ( $use_timestamps ) {
						$from_date = mysql2date( 'U', $from_date );
					}
					$from_date = sprintf( ">='%s'", $from_date );
				}
				$result['from_date'] = $from_date;
			}

			if ( $settings['to_date'] ) {
				$ts = strtotime( $settings['to_date'] );
				if ( self::is_datetime_timestamp( $ts ) ) {
					$to_date = date( 'Y-m-d H:i:s', $ts );
				} else {
					$to_date = date( 'Y-m-d', $ts ) . " 23:59:59";
				}
				if ( $is_for_sql ) {
					if ( $use_timestamps ) {
						$to_date = mysql2date( 'U', $to_date );
					}
					$to_date = sprintf( "<='%s'", $to_date );
				}
				$result['to_date'] = $to_date;
			}

			return $result;
		}

		$_time = current_time( "timestamp", 0 );

		$export_rule = isset( $settings['export_rule'] ) ? $settings['export_rule'] : '';

		switch ( $export_rule ) {
			case "none":
				unset( $from_date );
				unset( $to_date );
				break;
			case "last_run":
				$last_run = isset( $settings['schedule']['last_run'] ) ? $settings['schedule']['last_run'] : '';
				if ( isset( $last_run ) AND $last_run ) {
					$from_date = date( 'Y-m-d H:i:s', $last_run );
				}
				break;
			case "today":
				$_date = date( 'Y-m-d', $_time );

				$from_date = sprintf( '%s %s', $_date, '00:00:00' );
				$to_date   = sprintf( '%s %s', $_date, '23:59:59' );
				break;
			case "this_week":
				$day        = ( date( 'w', $_time ) + 6 ) % 7;// 0 - Sun , must be Mon = 0
				$_date      = date( 'Y-m-d', $_time );
				$week_start = date( 'Y-m-d', strtotime( $_date . ' -' . $day . ' days' ) );
				$week_end   = date( 'Y-m-d', strtotime( $_date . ' +' . ( 6 - $day ) . ' days' ) );

				$from_date = sprintf( '%s %s', $week_start, '00:00:00' );
				$to_date   = sprintf( '%s %s', $week_end, '23:59:59' );
				break;
			case "this_month":
				$month_start = date( 'Y-m-01', $_time );
				$month_end   = date( 'Y-m-t', $_time );

				$from_date = sprintf( '%s %s', $month_start, '00:00:00' );
				$to_date   = sprintf( '%s %s', $month_end, '23:59:59' );
				break;
			case "last_day":
				$_date    = date( 'Y-m-d', $_time );
				$last_day = strtotime( $_date . " -1 day" );
				$_date    = date( 'Y-m-d', $last_day );

				$from_date = sprintf( '%s %s', $_date, '00:00:00' );
				$to_date   = sprintf( '%s %s', $_date, '23:59:59' );
				break;
			case "last_week":
				$day        = ( date( 'w', $_time ) + 6 ) % 7;// 0 - Sun , must be Mon = 0
				$_date      = date( 'Y-m-d', $_time );
				$last_week  = strtotime( $_date . " -1 week" );
				$week_start = date( 'Y-m-d', strtotime( date( 'Y-m-d', $last_week ) . ' -' . $day . ' days' ) );
				$week_end   = date( 'Y-m-d', strtotime( date( 'Y-m-d', $last_week ) . ' +' . ( 6 - $day ) . ' days' ) );

				$from_date = sprintf( '%s %s', $week_start, '00:00:00' );
				$to_date   = sprintf( '%s %s', $week_end, '23:59:59' );
				break;
			case "last_month":
				$_date       = date( 'Y-m-d', $_time );
				$last_month  = strtotime( $_date . " -1 month" );
				$month_start = date( 'Y-m-01', $last_month );
				$month_end   = date( 'Y-m-t', $last_month );

				$from_date = sprintf( '%s %s', $month_start, '00:00:00' );
				$to_date   = sprintf( '%s %s', $month_end, '23:59:59' );
				break;
			case "last_quarter":
				$_date         = date( 'Y-m-d', $_time );
				$last_month    = strtotime( $_date . " -3 month" );
				$quarter_start = date( 'Y-' . self::get_quarter_month( $last_month ) . '-01', $last_month );
				$quarter_end   = date( 'Y-' . ( self::get_quarter_month( $last_month ) + 2 ) . '-31', $last_month );

				$from_date = sprintf( '%s %s', $quarter_start, '00:00:00' );
				$to_date   = sprintf( '%s %s', $quarter_end, '23:59:59' );
				break;
			case "this_year":
				$year_start = date( 'Y-01-01', $_time );

				$from_date = sprintf( '%s %s', $year_start, '00:00:00' );
				break;
			case "custom":
				$export_rule_custom = isset( $settings['export_rule_custom'] ) ? $settings['export_rule_custom'] : '';
				if ( isset( $export_rule_custom ) AND $export_rule_custom ) {
					$day_start = date( 'Y-m-d',
						strtotime( date( 'Y-m-d', $_time ) . ' -' . intval( $export_rule_custom ) . ' days' ) );
					$day_end   = date( 'Y-m-d', $_time );

					$from_date = sprintf( '%s %s', $day_start, '00:00:00' );
					$to_date   = sprintf( '%s %s', $day_end, '23:59:59' );
				}
				break;
			default:
				break;
		}

		if ( isset( $from_date ) AND $from_date ) {
			if ( $is_for_sql ) {
				if ( $use_timestamps ) {
					$from_date = mysql2date( 'U', $from_date );
				}
				$from_date = sprintf( ">='%s'", $from_date );
			}
			$result['from_date'] = $from_date;
		}

		if ( isset( $to_date ) AND $to_date ) {
			if ( $is_for_sql ) {
				if ( $use_timestamps ) {
					$to_date = mysql2date( 'U', $to_date );
				}
				$to_date = sprintf( "<='%s'", $to_date );
			}
			$result['to_date'] = $to_date;
		}

		return $result;
	}

	public static function get_quarter_month( $time ) {
		$month = date( "m", $time );
		if ( $month <= 3 ) {
			return 1;
		}
		if ( $month <= 6 ) {
			return 4;
		}
		if ( $month <= 9 ) {
			return 7;
		}

		return 10;
	}

	public static function prepare_for_export() {
		self::$statuses                         = wc_get_order_statuses();
		self::$countries                        = WC()->countries->countries;
		self::$prices_include_tax               = get_option( 'woocommerce_prices_include_tax' ) == 'yes' ? true : false;
		self::$export_subcategories_separator   = apply_filters( 'woe_export_subcategories_separator', ">" );
		self::$export_line_categories_separator = apply_filters( 'woe_export_line_categories_separator', ",\n" );
		self::$export_itemmeta_values_separator = apply_filters( 'woe_export_itemmeta_values_separator', ", " );
		self::$export_custom_fields_separator   = apply_filters( 'woe_export_custom_fields_separator', ", " );
	}

	//for debug 
	public static function start_track_queries() {
		self::$track_sql_queries = true;
		self::$sql_queries       = array();
	}

	public static function get_sql_queries() {
		return self::$sql_queries;
	}

	//for csv/excel
	public static function get_max_order_items( $type, $ids ) {
		global $wpdb;

		$ids[] = 0; // for safe
		$ids   = join( ",", $ids );

		$sql = "SELECT COUNT( * ) AS t
			FROM  `{$wpdb->prefix}woocommerce_order_items`
			WHERE order_item_type =  '$type'
			AND order_id
			IN ( $ids)
			GROUP BY order_id
			ORDER BY t DESC
			LIMIT 1";

		$max = $wpdb->get_var( $sql );
		if ( ! $max ) {
			$max = 1;
		}

		return $max;
	}

	public static function fetch_order_coupons(
		$order,
		$labels,
		$static_vals,
		$options
	) {
		global $wpdb;
		$coupons = array();
		foreach ( $order->get_items( 'coupon' ) as $item ) {
			$coupon_meta     = array();
			$get_coupon_meta = ( array_diff( $labels->get_keys(),
				array( 'code', 'discount_amount', 'discount_amount_tax', 'excerpt' ) ) );
			if ( $get_coupon_meta ) {
				$recs = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value,meta_key FROM {$wpdb->postmeta} AS meta
					JOIN {$wpdb->posts} AS posts ON posts.ID = meta.post_id
					WHERE posts.post_title=%s", $item['name'] ) );
				foreach ( $recs as $rec ) {
					$coupon_meta[ $rec->meta_key ] = $rec->meta_value;
				}
			}

			$row = array();
			foreach ( $labels->unique_keys() as $field ) {
				if ( isset( $item[ $field ] ) ) {
					$row[ $field ] = $item[ $field ];
				} elseif ( $field == 'code' ) {
					$row['code'] = $item["name"];
				} elseif ( $field == 'discount_amount_plus_tax' ) {
					$row['discount_amount_plus_tax'] = $item["discount_amount"] + $item["discount_amount_tax"];
				} elseif ( isset( $coupon_meta[ $field ] ) ) {
					$row[ $field ] = $coupon_meta[ $field ];
				} elseif ( isset( $static_vals[ $field ] ) ) {
					$row[ $field ] = $static_vals[ $field ];
				} else {
					$row[ $field ] = '';
				}

				$row[ $field ] = apply_filters( "woe_get_order_coupon_value_{$field}", $row[ $field ], $order,
					$item );

				if ( $field == 'excerpt' ) {
					$post          = get_page_by_title( $item['name'], OBJECT, 'shop_' . $item['type'] );
					$row[ $field ] = $post ? $post->post_excerpt : '';
				}
			}
			$row = apply_filters( 'woe_fetch_order_coupon', $row, $item, $coupon_meta );
			if ( $row ) {
				$coupons[] = $row;
			}
		}

		return apply_filters( "woe_fetch_order_coupons", $coupons, $order, $labels->get_legacy_labels(), $format = "",
			$static_vals );
	}


	/**
	 * @param WC_Order               $order
	 * @param WC_Order_Export_Labels $labels
	 * @param array                  $static_vals
	 * @param array                  $options
	 *
	 * @return array
	 */
	public static function fetch_order_products(
		$order,
		$labels,
		$static_vals,
		$options
	) {
		$export_only_products     = $options['include_products'];
		$product_fields_with_tags = array( 'product_variation', 'post_content', 'post_excerpt' );
		$products                 = array();
		$i                        = 0;

		foreach ( $order->get_items( 'line_item' ) as $item_id => $item ) {
			do_action( "woe_get_order_product_item", $item );
			if ( $options['export_refunds'] AND $item['qty'] == 0 ) // skip zero items, when export refunds
			{
				continue;
			}
			// we export only matched products?
			if ( $export_only_products AND
			     ! in_array( $item['product_id'], $export_only_products ) AND // not  product
			     ( ! $item['variation_id'] OR ! in_array( $item['variation_id'],
					     $export_only_products ) )  // not variation
			) {
				continue;
			}
			$product   = $order->get_product_from_item( $item );
			$product   = apply_filters( "woe_get_order_product", $product );
			$item_meta = get_metadata( 'order_item', $item_id );
			foreach ( $item_meta as $key => $value ) {
				$clear_key = wc_sanitize_taxonomy_name( $key );
				if ( taxonomy_exists( $clear_key ) ) {
					$term                 = get_term_by( 'slug', $value[0], $clear_key );
					$item_meta[ $key ][0] = isset( $term->name ) ? $term->name : $value[0];
					if ( strpos( $key, 'attribute_' ) === false ) {
						$item_meta[ 'attribute_' . $key ][0] = isset( $term->name ) ? $term->name : $value[0];
					}
				}
			}
			$item_meta = apply_filters( "woe_get_order_product_item_meta", $item_meta );
			$product   = apply_filters( "woe_get_order_product_and_item_meta", $product, $item_meta );
			if ( $product ) {
				if ( method_exists( $product, 'get_id' ) ) {
					if ( $product->is_type( 'variation' ) ) {
						$product_id = method_exists( $product,
							'get_parent_id' ) ? $product->get_parent_id() : $product->parent->id;
					} else {
						$product_id = $product->get_id();
					}
					$post = get_post( $product_id );
				} else {    // legacy
					$product_id = $product->id;
					$post       = $product->post;
				}
			} else {
				$product_id = 0;
				$post       = false;
			}

			// skip based on products/items/meta
			if ( apply_filters( 'woe_skip_order_item', false, $product, $item, $item_meta, $post ) ) {
				continue;
			}

			if ( $options['skip_refunded_items'] ) {
				$qty_minus_refund = $item_meta["_qty"][0] + $order->get_qty_refunded_for_item( $item_id ); // Yes we add negative! qty
				if ( $qty_minus_refund <= 0 ) {
					continue;
				}
			}

			$i ++;
			$row = array();
			foreach ( $labels->unique_keys() as $field ) {
				if ( strpos( $field, '__' ) !== false && $taxonomies = wc_get_product_terms( $item['product_id'],
						substr( $field, 2 ), array( 'fields' => 'names' ) )
				) {
					$row[ $field ] = implode( ', ', $taxonomies );
				} else if ( $field == 'product_shipping_class' ) {
					$taxonomies = array();
					if ( ! empty( $item['variation_id'] ) )// try get from variation at first!
					{
						$taxonomies = wc_get_product_terms( $item['variation_id'], $field,
							array( 'fields' => 'names' ) );
					}
					if ( ! $taxonomies ) {
						$taxonomies = wc_get_product_terms( $item['product_id'], $field, array( 'fields' => 'names' ) );
					}
					//done	
					$row[ $field ] = implode( ', ', $taxonomies );
				} elseif ( $field == 'line_total_plus_tax' ) {
					$row[ $field ] = $item_meta["_line_total"][0] + $item_meta["_line_tax"][0];
				} elseif ( $field == 'line_subtotal_tax' ) {
					$row[ $field ] = $item_meta["_line_subtotal_tax"][0];
				} elseif ( $field == 'name' ) {
					$row[ $field ] = $item["name"];
				} elseif ( $field == 'product_name' ) {
					$row[ $field ] = $product ? $product->get_name() : '';
				} elseif ( $field == 'product_variation' ) {
					$row[ $field ] = self::get_product_variation( $item, $order, $item_id, $product );
				} elseif ( $field == 'seller' ) {
					$row[ $field ] = '';
					if ( $post ) {
						$user          = get_userdata( $post->post_author );
						$row[ $field ] = ! empty( $user->display_name ) ? $user->display_name : '';
					}
				} elseif ( $field == 'post_content' ) {
					$row[ $field ] = $post ? $post->post_content : '';
				} elseif ( $field == 'post_excerpt' ) {
					$row[ $field ] = $post ? $post->post_excerpt : '';
				} elseif ( $field == 'type' ) {
					$row[ $field ] = '';
					if ( $product ) {
						$row[ $field ] = method_exists( $product,
							'get_type' ) ? $product->get_type() : $product->product_type;
					}
				} elseif ( $field == 'tags' ) {
					$terms         = get_the_terms( $product_id, 'product_tag' );
					$row[ $field ] = array();
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$row[ $field ][] = $term->name;
						}
					}
					$row[ $field ] = join( ",", $row[ $field ] );
				} elseif ( $field == 'category' ) {
					$terms         = get_the_terms( $product_id, 'product_cat' );
					$row[ $field ] = array();
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$row[ $field ][] = $term->name;
						}
					}
					$row[ $field ] = join( ",", $row[ $field ] );// hierarhy ???
				} elseif ( $field == 'line_no_tax' ) {
					$row[ $field ] = $item_meta["_line_total"][0];
					//item refund
				} elseif ( $field == 'line_total_refunded' ) {
					$row[ $field ] = $order->get_total_refunded_for_item( $item_id );
				} elseif ( $field == 'line_total_minus_refund' ) {
					$row[ $field ] = $item_meta["_line_total"][0] - $order->get_total_refunded_for_item( $item_id );
				} elseif ( $field == 'qty_minus_refund' ) {
					$row[ $field ] = $item_meta["_qty"][0] + $order->get_qty_refunded_for_item( $item_id ); // Yes we add negative! qty
					//tax refund
				} elseif ( $field == 'line_tax_refunded' ) {
					$row[ $field ] = self::get_order_item_taxes_refund( $order, $item_id );
				} elseif ( $field == 'line_tax_minus_refund' ) {
					$row[ $field ] = $item_meta["_line_tax"][0] - self::get_order_item_taxes_refund( $order, $item_id );
				} elseif ( $field == 'line_id' ) {
					$row[ $field ] = $i;
				} elseif ( $field == 'item_id' ) {
					$row[ $field ] = $item_id;
				} elseif ( $field == 'item_price' ) {
					$row[ $field ] = $order->get_item_total( $item, false, true ); // YES we have to calc item price
				} elseif ( $field == 'discount_amount' ) {
					if ( method_exists( $item, "get_subtotal" ) ) {
						$row[ $field ] = $item->get_subtotal() - $item->get_total();
					} else    //2.6
					{
						$row[ $field ] = $item['line_subtotal'] - $item['line_total'];
					}
				} elseif ( $field == 'tax_rate' ) {
					if ( method_exists( $item, "get_subtotal" ) ) {
						$subtotal_amount = $item->get_subtotal();
						$subtotal_tax    = $item->get_subtotal_tax();
					} else {
						$subtotal_amount = $item['line_subtotal'];
						$subtotal_tax    = $item['line_subtotal_tax'];
					}
					$row[ $field ] = ( $subtotal_amount > 0 ) ? round( 100 * $subtotal_tax / $subtotal_amount, 2 ) : 0;
				} elseif ( $field == 'product_url' ) {
					$row[ $field ] = get_permalink( $product_id );
				} elseif ( $field == 'sku' ) {
					$row[ $field ] = method_exists( $product,
						'get_' . $field ) ? $product->{'get_' . $field}() : get_post_meta( $product_id, '_' . $field,
						true );
				} elseif ( $field == 'download_url' ) {
					$row[ $field ] = '';
					if ( $product AND $product->is_downloadable() ) {
						$files = get_post_meta( $product_id, '_downloadable_files', true );
						$links = array();
						if ( $files ) {
							foreach ( $files as $file ) {
								$links[] = $file['file'];
							}
						}
						$row[ $field ] = implode( "\n", $links );
					}
				} elseif ( $field == 'image_url' ) {
					// make full url, wp_get_attachment_image_src can return false
					$images_src    = ( is_object( $product ) AND $product->get_image_id() ) ? wp_get_attachment_image_src( $product->get_image_id(),
						'full' ) : false;
					$row[ $field ] = is_array( $images_src ) ? current( $images_src ) : '';
				} elseif ( $field == 'full_category_names' ) {
					$row[ $field ] = self::get_product_category_full( $product_id );
				} elseif ( isset( $static_vals[ $field ] ) ) {
					$row[ $field ] = $static_vals[ $field ];
				} elseif ( isset( $item_meta[ $field ] ) ) {    //meta from order
					$row[ $field ] = join( self::$export_itemmeta_values_separator, $item_meta[ $field ] );
				} elseif ( isset( $item_meta[ "_" . $field ] ) ) {// or hidden field
					$row[ $field ] = join( self::$export_itemmeta_values_separator, $item_meta[ "_" . $field ] );
				} elseif ( isset( $item['item_meta'][ $field ] ) ) {  // meta from item line
					$row[ $field ] = join( self::$export_itemmeta_values_separator, $item['item_meta'][ $field ] );
				} elseif ( isset( $item['item_meta'][ "_" . $field ] ) ) { // or hidden field
					$row[ $field ] = join( self::$export_itemmeta_values_separator,
						$item['item_meta'][ "_" . $field ] );
				} else {
					$row[ $field ] = '';
					if ( ! empty( $item['variation_id'] ) ) {
						$row[ $field ] = get_post_meta( $item['variation_id'], $field, true );
					}
					if ( $row[ $field ] === '' ) // empty value ? try get custom!
					{
						$row[ $field ] = get_post_meta( $product_id, $field, true );
					}
					if ( $row[ $field ] === '' ) // empty value ?
					{
						$row[ $field ] = method_exists( $product,
							'get_' . $field ) ? $product->{'get_' . $field}() : get_post_meta( $product_id,
							'_' . $field, true );
					}
					if ( $row[ $field ] === '' AND empty( $item['variation_id'] ) ) // empty value ? try get attribute for !variaton
					{
						$row[ $field ] = $product ? $product->get_attribute( $field ) : '';
					}
				}

				if ( $options['strip_tags_product_fields'] AND in_array( $field, $product_fields_with_tags ) ) {
					$row[ $field ] = strip_tags( $row[ $field ] );
				}

				if ( isset( $row[ $field ] ) ) {
					$row[ $field ] = apply_filters( "woe_get_order_product_value_{$field}", $row[ $field ], $order,
						$item, $product, $item_meta );
//					$row[ $field ] = apply_filters( "woe_get_order_product_{$format}_value_{$field}", $row[ $field ],
//						$order, $item, $product, $item_meta );
				}
			}
			$row = apply_filters( 'woe_fetch_order_product', $row, $order, $item, $product, $item_meta );
			if ( $row ) {
				$products[ $item_id ] = $row;
			}
		}

		return apply_filters( "woe_fetch_order_products", $products, $order, $labels->get_legacy_labels(), $format = "",
			$static_vals );
	}


	/**
	 * @param $product WC_Product
	 *
	 * @return string
	 */
	public static function get_product_category_full( $product_id ) {
		$full_names = array();
		if ( ! $product_id ) {
			return '';
		}
		$prod_terms = get_the_terms( $product_id, 'product_cat' );
		if ( ! $prod_terms ) {
			return '';
		}

		foreach ( $prod_terms as $prod_term ) {
			$parts                                  = array( $prod_term->name );
			$product_parent_categories_all_hierachy = get_ancestors( $prod_term->term_id, 'product_cat' );
			foreach ( $product_parent_categories_all_hierachy as $id ) {
				$parent  = get_term( $id );
				$parts[] = $parent->name;
			}
			$full_names[] = join( self::$export_subcategories_separator, array_reverse( $parts ) );
		}

		return join( self::$export_line_categories_separator, $full_names );
	}


	/**
	 * @param $order WC_Order
	 * @param $item_id
	 *
	 * @return int
	 */
	public static function get_order_item_taxes_refund( $order, $item_id ) {
		$tax_refund  = 0;
		$order_taxes = $order->get_taxes();
		foreach ( $order_taxes as $tax_item ) {
			$tax_item_id = $tax_item['rate_id'];
			$tax_refund  += $order->get_tax_refunded_for_item( $item_id, $tax_item_id );
		}

		return $tax_refund;
	}

	public static function fetch_order_data(
		$order_id,
		$labels,
		$export,
		$static_vals,
		$options
	) {
		global $wp_roles;

//		$extra_rows = array();
		$row = array();

		// get order meta
		$order_meta = array();
		if ( $order_post_meta = get_post_meta( $order_id ) ) {
			foreach ( $order_post_meta as $meta_key => $meta_values ) {
				$order_meta[ $meta_key ] = join( self::$export_custom_fields_separator, $meta_values );
			}
		}

		// take order
		self::$current_order = $order = new WC_Order( $order_id );

		// add fields for WC 3.0
		foreach ( array( "billing_country", "billing_state", "shipping_country", "shipping_state" ) as $field_30 ) {
			$$field_30 = method_exists( $order,
				'get_' . $field_30 ) ? $order->{'get_' . $field_30}() : $order->$field_30;
		}

		$parent_order_id = method_exists( $order,
			'get_parent_id' ) ? $order->get_parent_id() : $order->post->post_parent;
		$parent_order    = $parent_order_id ? new WC_Order( $parent_order_id ) : false;
		$post            = method_exists( $order, 'get_id' ) ? get_post( $order->get_id() ) : $order->post;

		// correct meta for child orders
		if ( $parent_order_id ) {
			// overwrite child values for refunds
			$is_refund                  = ( $post->post_type == 'shop_order_refund' );
			$overwrite_child_order_meta = apply_filters( 'woe_overwrite_child_order_meta', $is_refund );

			if ( $parent_order_meta = get_post_meta( $parent_order_id ) ) {
				foreach ( $parent_order_meta as $meta_key => $meta_values ) {
					if ( $overwrite_child_order_meta OR ! isset( $order_meta[ $meta_key ] ) ) {
						$order_meta[ $meta_key ] = join( self::$export_custom_fields_separator, $meta_values );
					}
				}
			}

			//refund rewrites it
			if ( $overwrite_child_order_meta ) {
				foreach (
					array(
						"billing_country",
						"billing_state",
						"shipping_country",
						"shipping_state",
					) as $field_30
				) {
					$$field_30 = method_exists( $parent_order,
						'get_' . $field_30 ) ? $parent_order->{'get_' . $field_30}() : $parent_order->$field_30;
				}
			}
			//refund status
			if ( $is_refund ) {
				$order_status = 'refunded';
			}
		}

		// we know parent!
		if (  ( $export['products'] || $options['include_products'] ) && ! empty( $labels['products'] )  || 
		      isset( $labels['order']->count_unique_products ) || isset( $labels['order']->total_weight_items ) ) {
		    //   no labels for products??
			$tmp_labels = !empty($labels['products']) ? clone $labels['products'] : new WC_Order_Export_Labels();
			//need qty?
			if ( isset( $labels['order']->total_weight_items )  || isset( $labels['order']->count_unique_products ) ) {
				if ( ! isset( $tmp_labels->qty ) ) {
					$tmp_labels->qty = "";
				}
			}
			// need weight too?
			if ( isset( $labels['order']->total_weight_items ) ) {
				if ( ! isset( $tmp_labels->weight ) ) {
					$tmp_labels->weight = "";
				}
			}
			
			$data['products'] = self::fetch_order_products(
				$order,
				$tmp_labels,
				isset( $static_vals['products'] ) ? $static_vals['products'] : array(),
				$options
			);
			if ( $options['include_products'] AND empty( $data['products'] ) AND apply_filters( "woe_skip_order_without_products", false ) ) {
				return array();
			}
		} else {
			$data['products'] = array();
		}
		if ( ( $export['coupons'] OR isset( $labels['order']->coupons_used ) ) && ! empty( $labels['coupons'] ) ) {
			// get coupons from main order
			$data['coupons'] = self::fetch_order_coupons(
				$parent_order ? $parent_order : $order,
				$labels['coupons'],
				isset( $static_vals['coupons'] ) ? $static_vals['coupons'] : array(),
				$options
			);
		} else {
			$data['coupons'] = array();
		}

		// extra WP_User
		$user = ! empty( $order_meta['_customer_user'] ) ? get_userdata( $order_meta['_customer_user'] ) : false;
		// setup missed fields for full addresses
		foreach ( array( '_billing_address_1', '_billing_address_2', '_shipping_address_1', '_shipping_address_2' ) as $optional_field ) {
			if ( ! isset( $order_meta[ $optional_field ] ) ) {
				$order_meta[ $optional_field ] = '';
			}
		}

		$order_meta = apply_filters( 'woe_fetch_order_meta', $order_meta, $order_id );

		// fill as it must
		foreach ( $labels['order']->get_fetch_fields() as $field ) {
			if ( substr( $field, 0, 5 ) == "USER_" ) { //user field
				$key           = substr( $field, 5 );
				$row[ $field ] = $user ? $user->get( $key ) : '';
			} elseif ( $field == 'order_id' ) {
				$row[ $field ] = $order_id;
			} elseif ( $field == 'order_date' ) {
				$row[ $field ] = ! method_exists( $order,
					"get_date_created" ) ? $order->order_date : ( $order->get_date_created() ? gmdate( 'Y-m-d H:i:s',
					$order->get_date_created()->getOffsetTimestamp() ) : '' );
			} elseif ( $field == 'modified_date' ) {
				$row[ $field ] = ! method_exists( $order,
					"get_date_modified" ) ? $order->modified_date : ( $order->get_date_modified() ? gmdate( 'Y-m-d H:i:s',
					$order->get_date_modified()->getOffsetTimestamp() ) : '' );
			} elseif ( $field == 'completed_date' ) {
				$row[ $field ] = ! method_exists( $order,
					"get_date_completed" ) ? $order->completed_date : ( $order->get_date_completed() ? gmdate( 'Y-m-d H:i:s',
					$order->get_date_completed()->getOffsetTimestamp() ) : '' );
			} elseif ( $field == 'paid_date' ) {
				$row[ $field ] = ! method_exists( $order,
					"get_date_paid" ) ? $order->paid_date : ( $order->get_date_paid() ? gmdate( 'Y-m-d H:i:s',
					$order->get_date_paid()->getOffsetTimestamp() ) : '' );
			} elseif ( $field == 'order_number' ) {
				$row[ $field ] = $parent_order ? $parent_order->get_order_number() : $order->get_order_number(); // use parent order number
			} elseif ( $field == 'order_subtotal' ) {
				$row[ $field ] = wc_format_decimal( $order->get_subtotal(), 2 );
			} elseif ( $field == 'order_subtotal_minus_discount' ) {
				$row[ $field ] = $order->get_subtotal() - $order->get_total_discount();
			} elseif ( $field == 'order_subtotal_refunded' ) {
				$row[ $field ] = wc_format_decimal( self::get_order_subtotal_refunded( $order ), 2 );
			} elseif ( $field == 'order_subtotal_minus_refund' ) {
				$row[ $field ] = wc_format_decimal( $order->get_subtotal() - self::get_order_subtotal_refunded( $order ),
					2 );
				//order total
			} elseif ( $field == 'order_total' ) {
				$row[ $field ] = $order->get_total();
			} elseif ( $field == 'order_total_no_tax' ) {
				$row[ $field ] = $order->get_total() - $order->get_total_tax();
			} elseif ( $field == 'order_refund' ) {
				$row[ $field ] = $order->get_total_refunded();
			} elseif ( $field == 'order_total_inc_refund' ) {
				$row[ $field ] = $order->get_total() - $order->get_total_refunded();
				//shipping
			} elseif ( $field == 'order_shipping' ) {
				$row[ $field ] = $order->get_shipping_total();
			} elseif ( $field == 'order_shipping_plus_tax' ) {
				$row[ $field ] = $order->get_shipping_total() + $order->get_shipping_tax();
			} elseif ( $field == 'order_shipping_refunded' ) {
				$row[ $field ] = $order->get_total_shipping_refunded();
			} elseif ( $field == 'order_shipping_minus_refund' ) {
				$row[ $field ] = $order->get_shipping_total() - $order->get_total_shipping_refunded();
				//shipping tax
			} elseif ( $field == 'order_shipping_tax_refunded' ) {
				$row[ $field ] = self::get_order_shipping_tax_refunded( $order_id );
			} elseif ( $field == 'order_shipping_tax_minus_refund' ) {
				$row[ $field ] = $order->get_shipping_tax() - self::get_order_shipping_tax_refunded( $order_id );
				//order tax
			} elseif ( $field == 'order_tax' ) {
				$row[ $field ] = wc_round_tax_total( $order->get_cart_tax() );
			} elseif ( $field == 'order_total_fee' ) {
				$row[ $field ] = array_sum( array_map( function ( $item ) {
					return $item->get_total();
				}, $order->get_fees() ) );
			} elseif ( $field == 'order_total_tax' ) {
				$row[ $field ] = wc_round_tax_total( $order->get_total_tax() );
			} elseif ( $field == 'order_total_tax_refunded' ) {
				$row[ $field ] = wc_round_tax_total( $order->get_total_tax_refunded() );
			} elseif ( $field == 'order_total_tax_minus_refund' ) {
				$row[ $field ] = wc_round_tax_total( $order->get_total_tax() - $order->get_total_tax_refunded() );
			} elseif ( $field == 'order_status' ) {
				$status        = empty( $order_status ) ? $order->get_status() : $order_status;
				$status        = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
				$row[ $field ] = isset( self::$statuses[ 'wc-' . $status ] ) ? self::$statuses[ 'wc-' . $status ] : $status;
			} elseif ( $field == 'user_login' OR $field == 'user_email' OR $field == 'user_url' ) {
				$row[ $field ] = $user ? $user->$field : "";
			} elseif ( $field == 'user_role' ) {
				$roles         = $wp_roles->roles;
				$row[ $field ] = ( isset( $user->roles[0] ) && isset( $roles[ $user->roles[0] ] ) ) ? $roles[ $user->roles[0] ]['name'] : ""; // take first role Name
			} elseif ( $field == 'customer_total_orders' ) {
				$row[ $field ] = ( isset( $user->ID ) ) ? wc_get_customer_order_count( $user->ID ) : 0;
			} elseif ( $field == 'billing_address' ) {
				$row[ $field ] = join( ", ",
					array_filter( array( $order_meta["_billing_address_1"], $order_meta["_billing_address_2"] ) ) );
			} elseif ( $field == 'shipping_address' ) {
				$row[ $field ] = join( ", ",
					array_filter( array( $order_meta["_shipping_address_1"], $order_meta["_shipping_address_2"] ) ) );
			} elseif ( $field == 'billing_full_name' ) {
				$row[ $field ] = trim( $order_meta["_billing_first_name"] . ' ' . $order_meta["_billing_last_name"] );
			} elseif ( $field == 'shipping_full_name' ) {
				$row[ $field ] = trim( $order_meta["_shipping_first_name"] . ' ' . $order_meta["_shipping_last_name"] );
			} elseif ( $field == 'billing_country_full' ) {
				$row[ $field ] = isset( self::$countries[ $billing_country ] ) ? self::$countries[ $billing_country ] : $billing_country;
			} elseif ( $field == 'shipping_country_full' ) {
				$row[ $field ] = isset( self::$countries[ $shipping_country ] ) ? self::$countries[ $shipping_country ] : $shipping_country;
			} elseif ( $field == 'billing_state_full' ) {
				$country_states = WC()->countries->get_states( $billing_country );
				$row[ $field ]  = isset( $country_states[ $billing_state ] ) ? html_entity_decode( $country_states[ $billing_state ] ) : $billing_state;
			} elseif ( $field == 'shipping_state_full' ) {
				$country_states = WC()->countries->get_states( $shipping_country );
				$row[ $field ]  = isset( $country_states[ $shipping_state ] ) ? html_entity_decode( $country_states[ $shipping_state ] ) : $shipping_state;
			} elseif ( $field == 'billing_citystatezip' ) {
				$row[ $field ] = self::get_city_state_postcode_field_value( $order, 'billing' );
			} elseif ( $field == 'billing_citystatezip_us' ) {
				$row[ $field ] = self::get_city_state_postcode_field_value( $order, 'billing', true);
			} elseif ( $field == 'shipping_citystatezip' ) {
				$row[ $field ] = self::get_city_state_postcode_field_value( $order, 'shipping' );
			} elseif ( $field == 'shipping_citystatezip_us' ) {
				$row[ $field ] = self::get_city_state_postcode_field_value( $order, 'shipping', true);
			} elseif ( $field == 'products' OR $field == 'coupons' ) {
				if ( isset( $data[ $field ] ) ) {
					$row[ $field ] = $data[ $field ];
				}
			} elseif ( $field == 'shipping_method_title' ) {
				$row[ $field ] = $order->get_shipping_method();
			} elseif ( $field == 'shipping_method' ) {
				$shipping_methods = $order->get_items( 'shipping' );
				$shipping_method  = reset( $shipping_methods ); // take first entry
				if ( ! empty( $shipping_method ) ) {
					$row[ $field ] = $shipping_method['method_id'] . ':' . $shipping_method['instance_id'];
				}
			} elseif ( $field == 'coupons_used' ) {
				$row[ $field ] = count( $data['coupons'] );
			} elseif ( $field == 'total_weight_items' ) {
				$row[ $field ] = 0;
				foreach ( $data['products'] as $product ) {
					$row[ $field ] += (float) $product['qty'] * (float) $product['weight'];
				}
			} elseif ( $field == 'count_total_items' ) {
				$row[ $field ] = $order->get_item_count();
			} elseif ( $field == 'count_exported_items' ) {
				$row[ $field ] = 0; // count only exported!
				if ( $export['products'] ) {
					foreach ( $data['products'] as $product ) {
						$row[ $field ] += $product['qty'];
					}
				}
			} elseif ( $field == 'count_unique_products' ) { // speed! replace with own counter ?
				$row[ $field ] = count( $data['products'] );
			} elseif ( $field == 'customer_note' ) {
				$notes = array( $post->post_excerpt );
				if ( $options['export_refund_notes'] ) {
					$refunds = $order->get_refunds();
					foreach ( $refunds as $refund ) {
						// added get_reason for WC 3.0
						$notes[] = method_exists( $refund,
							'get_reason' ) ? $refund->get_reason() : $refund->get_refund_reason();
					}
				}
				$row[ $field ] = implode( "\n", array_filter( $notes ) );
			} elseif ( $field == 'first_refund_date' ) {
				$value = '';
				foreach ( $order->get_refunds() as $refund ) {
					$value = ! method_exists( $refund,
						"get_date_created" ) ? $refund->date : ( $refund->get_date_created() ? gmdate( 'Y-m-d H:i:s',
						$refund->get_date_created()->getOffsetTimestamp() ) : '' );
					break;// take only first
				}
				$row[ $field ] = $value;
			} elseif ( isset( $static_vals['order'][ $field ] ) ) {
				$row[ $field ] = $static_vals['order'][ $field ];
			} elseif ( $field == 'order_notes' ) {
				remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10 );
				$args  = array(
					'post_id' => $order_id,
					'approve' => 'approve',
					'type'    => 'order_note',
				);
				$notes = get_comments( $args );
				add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
				$comments = array();
				if ( $notes ) {
					foreach ( $notes as $note ) {
						if ( ! empty( $options['export_all_comments'] ) || $note->comment_author !== __( 'WooCommerce',
								'woocommerce' ) ) { // skip system notes by default
							$comments[] = apply_filters( 'woe_get_order_notes', $note->comment_content, $note, $order );
						}
					}
				}
				$row[ $field ] = implode( "\n", $comments );
			} elseif ( isset( $order_meta[ $field ] ) ) {
				$field_data = array();
				do_action( 'woocommerce_order_export_add_field_data', $field_data, $order_meta[ $field ], $field );
				if ( empty( $field_data ) ) {
					$field_data[ $field ] = $order_meta[ $field ];
				}
				$row = array_merge( $row, $field_data );
			} elseif ( isset( $order_meta[ "_" . $field ] ) ) { // or hidden field
				$row[ $field ] = $order_meta[ "_" . $field ];
			} else { // order_date...
				$row[ $field ] = method_exists( $order,
					'get_' . $field ) ? $order->{'get_' . $field}() : get_post_meta( $order_id, '_' . $field, true );
				//print_r($field."=".$label); echo "debug static!\n\n";
			}

			//use empty value for missed field
			if ( $field != 'products' AND $field != 'coupons' ) {
				if ( ! isset( $row[ $field ] ) ) {
					$row[ $field ] = '';
				}
				if ( is_array( $row[ $field ] ) ) {
					$row[ $field ] = json_encode( $row[ $field ] );
				}
			}

			if ( isset( $row[ $field ] ) ) {
				$row[ $field ] = apply_filters( "woe_get_order_value_{$field}", $row[ $field ], $order, $field );
			} //if order field set
		}

		//no labels - no data !
		if( empty($labels['products']) ) {
			$row['products']  = array();
		}
		if( empty($labels['coupons']) ) {
			$row['coupons']  = array();
		}
		
		$row = apply_filters( "woe_fetch_order", $row, $order );

		return $row;
	}

	public static function get_city_state_postcode_field_value( $order, $type, $us_format = false  ) {
		if ( $type != 'shipping' && $type != 'billing' ) {
			return null;
		}
		$citystatepostcode_fields_name = array(
			$type . '_city',
			$type . '_state',
			$type . '_postcode',
		);
		$citystatepostcode             = array();
		foreach ( $citystatepostcode_fields_name as $field_name ) {
			$citystatepostcode[ $field_name ] = method_exists( $order,
				'get_' . $field_name ) ? $order->{'get_' . $field_name}() : $order->{$field_name};
		}

		if( $us_format ) {
			//reformat as "Austin, TX 95076"
			$parts[] = $citystatepostcode[ $type . '_city' ] ;
			$parts[] = trim( $citystatepostcode[ $type . '_state' ] . " " . $citystatepostcode[ $type . '_postcode' ] );
		} else {
			$parts = $citystatepostcode;
		}	
		return join( ", ", $parts );
	}

	public static function get_order_shipping_tax_refunded( $order_id ) {
		global $wpdb;
		$refund_ship_taxes = $wpdb->get_var( $wpdb->prepare( "
			SELECT SUM( order_itemmeta.meta_value )
			FROM {$wpdb->prefix}woocommerce_order_itemmeta AS order_itemmeta
			INNER JOIN $wpdb->posts AS posts ON ( posts.post_type = 'shop_order_refund' AND posts.post_parent = %d )
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON ( order_items.order_id = posts.ID AND order_items.order_item_type = 'tax' )
			WHERE order_itemmeta.order_item_id = order_items.order_item_id
			AND order_itemmeta.meta_key IN ( 'shipping_tax_amount')
		", $order_id ) );

		return abs( $refund_ship_taxes );
	}

	public static function get_order_subtotal_refunded( $order ) {
		$subtotal_refund = 0;
		foreach ( $order->get_refunds() as $refund ) {
			$subtotal_refund += $refund->get_subtotal();
		}

		return abs( $subtotal_refund );
	}

	/**
	 * @return string
	 */
	public static function get_product_variation( $item, $order, $item_id, $product ) {
		global $wpdb;
		$hidden_order_itemmeta = apply_filters( 'woocommerce_hidden_order_itemmeta', array(
			'_qty',
			'_tax_class',
			'_product_id',
			'_variation_id',
			'_line_subtotal',
			'_line_subtotal_tax',
			'_line_total',
			'_line_tax',
			'method_id',
			'cost',
			'_reduced_stock',
		) );

		$result = array();

		$value_delimiter = apply_filters( 'woe_fetch_item_meta_value_delimiter', ': ' );

		// pull meta directly
		$meta_data = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value, meta_id, order_item_id
			FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = %d
			ORDER BY meta_id", $item_id ), ARRAY_A );
		foreach ( $meta_data as $meta ) {
			if ( in_array( $meta['meta_key'], $hidden_order_itemmeta ) ) {
				continue;
			}
			if ( is_serialized( $meta['meta_value'] ) ) {
				continue;
			}

			//known attribute?
			if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta['meta_key'] ) ) ) {
				$term               = get_term_by( 'slug', $meta['meta_value'],
					wc_sanitize_taxonomy_name( $meta['meta_key'] ) );
				$meta['meta_key']   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta['meta_key'] ), $product );
				$meta['meta_value'] = isset( $term->name ) ? $term->name : $meta['meta_value'];
			} else {
				$meta['meta_key'] = wc_attribute_label( $meta['meta_key'], $product );
			}

			$value    = wp_kses_post( $meta['meta_key'] ) . $value_delimiter . wp_kses_post( force_balance_tags( $meta['meta_value'] ) );
			$result[] = apply_filters( 'woe_fetch_item_meta', $value, $meta, $item, $product );
		}

		//list to string!
		return join( apply_filters( 'woe_fetch_item_meta_lines_delimiter', ' | ' ), array_filter( $result ) );
	}

	/**
	 * @return array
	 */
	public static function get_shipping_methods() {
		global $wpdb;

		$shipping_methods = array();

		// get raw names
		$raw_methods = $wpdb->get_col( "SELECT DISTINCT order_item_name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type='shipping' ORDER BY order_item_name" );
		foreach ( $raw_methods as $method ) {
			$shipping_methods[ 'order_item_name:' . $method ] = $method;
		}

		// try get  methods for zones
		if ( ! class_exists( "WC_Shipping_Zone" ) ) {
			return $shipping_methods;
		}

		if ( ! method_exists( "WC_Shipping_Zone", "get_shipping_methods" ) ) {
			return $shipping_methods;
		}

		foreach ( WC_Shipping_Zones::get_zones() as $zone ) {
			$methods = $zone['shipping_methods'];
			/** @var WC_Shipping_Method $method */
			foreach ( $methods as $method ) {
				$shipping_methods[ $method->get_rate_id() ] = '[' . $zone['zone_name'] . '] ' . $method->get_title();
			}
		}

		$zone    = new WC_Shipping_Zone( 0 );
		$methods = $zone->get_shipping_methods();
		/** @var WC_Shipping_Method $method */
		foreach ( $methods as $method ) {
			$shipping_methods[ $method->get_rate_id() ] = __( '[Rest of the World]',
					'woo-order-export-lite' ) . ' ' . $method->get_title();
		}

		return $shipping_methods;
	}

}