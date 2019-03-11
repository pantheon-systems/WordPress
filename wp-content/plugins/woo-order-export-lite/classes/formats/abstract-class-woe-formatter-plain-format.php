<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once 'trait-woe-plain-format.php';

abstract class WOE_Formatter_Plain_Format extends WOE_Formatter {
	use WOE_Order_Export_Plain_Format;
	private $duplicate_settings = array();
	private $summary_report;
	protected $rows;

	protected $summary_processing = false;

	public function __construct(
		$mode,
		$filename,
		$settings,
		$format,
		$labels,
		$field_formats,
		$date_format,
		$offset
	) {
		parent::__construct( $mode, $filename, $settings, $format, $labels, $field_formats, $date_format, $offset );
		$this->duplicate_settings = $this->settings['global_job_settings']['duplicated_fields_settings'];
		$this->summary_report     = $this->settings['global_job_settings']['summary_report_by_products'];
		if ( $this->summary_report ) {
			self::check_create_session();
			$this->field_formats["order"] = $this->field_formats["products"];// Products at top level!
		}
		$this->summary_processing = false; //true only when we finish order scan
	}

	public function output( $rec ) {
		//don't output orders in summary mode!
		if ( $this->summary_report AND ! $this->summary_processing ) {
			$this->try_fill_summary_report_fields( $rec );

			return array();
		}

		$rec = parent::output( $rec );
		if ( $this->summary_processing ) {
			return array( $rec ); // need array for plain format iterators
		}

		return apply_filters( 'woe_fetch_order_data', $this->maybe_multiple_fields( $rec ) );
	}

	protected function maybe_multiple_fields( $rec ) {
		//get modes 
		$products_repeat = $this->duplicate_settings['products']['repeat'];
		$coupons_repeat  = $this->duplicate_settings['coupons']['repeat'];
		
		$tmp_rec = array();
		foreach ( $this->labels['order']->get_labels() as $label_data ) {
			$original_key = $label_data['key'];
			$key          = $label_data['parent_key'] ? $label_data['parent_key'] : $original_key;

			$tmp_rec[ $original_key ] = isset( $rec[ $key ] ) ? $rec[ $key ] : "";
		}
		$rec = $tmp_rec;

		 
		//simpleast 
		if ( $products_repeat == 'inside_one_cell' ) {
			$rec = $this->merge_nested_rows_to_one_record( $rec, 'products' );
		}
		if ( $coupons_repeat == 'inside_one_cell' ) {
			$rec = $this->merge_nested_rows_to_one_record( $rec, 'coupons' );
		}

		//more complex 
		$repeat_as_cols = array();
		if ( $products_repeat == 'columns' ) {
			$repeat_as_cols[] = 'products';
		}
		if ( $coupons_repeat == 'columns' ) {
			$repeat_as_cols[] = 'coupons';
		}
		if ( $repeat_as_cols ) {
			$rec = $this->add_nested_rows_as_columns( $rec, $repeat_as_cols );
		}

		//we still have one records at this point!
		if ( $products_repeat == 'rows' || $coupons_repeat == 'rows' ) {
			$new_rows = $this->try_multi_rows( $rec );
			//var_dump($new_rows ); die();
		} else {
			$new_rows = array( $rec );
		}


		foreach ( $new_rows as $index => &$row ) {
			if ( isset( $row['products'] ) ) {
				unset( $row['products'] );
			}
			if ( isset( $row['coupons'] ) ) {
				unset( $row['coupons'] );
			}
			if ( isset( $row['line_number'] ) && $index > 0 ) {
				$row['line_number'] = $this->counter_value;
				$this->counter_value ++;
			}

//			json for complex structures, don't encode nested products&coupons
			foreach ( $row as $key => &$val ) {
				if ( is_array( $val ) ) {
					$val = json_encode( $val );
				}
			}
		}

		return ( $new_rows );
	}


	protected function make_header( $data = '' ) {
		$header             = array();
		$repeat['products'] = $this->duplicate_settings['products']['repeat'];
		$repeat['coupons']  = $this->duplicate_settings['coupons']['repeat'];

		if ( $this->summary_report ) {
			return $this->make_summary_header( $data );
		}

		$group_fields       = array(
			'products' => array(),
			'coupons'  => array(),
		);
		$current_group_type = "";

		foreach ( $this->labels['order']->to_Array() as $field => $label ) {
			//should ignore start of 1st product/coupon section
			if ( $field == 'products' OR $field == 'coupons' ) {
				continue;
			}

			$prev_group_type = $current_group_type;
			$added_to_group  = false;

			if ( preg_match( '/^plain_(products|coupons)_.+/', $field, $matches ) ) {
				$type               = $matches[1];
				$current_group_type = $type;
				if ( 'columns' == $repeat[ $type ] ) {
					$group_fields[ $type ][] = $field;
					$added_to_group          = true;
				}
			} else {
				$current_group_type = 'order';
			}

			// previously we get product/coupon fields?
			if ( $prev_group_type !== $current_group_type AND ! empty( $group_fields[ $prev_group_type ] ) ) {
				$this->multiplicate_header( $header, $prev_group_type, $group_fields[ $prev_group_type ] );
				$group_fields[ $prev_group_type ] = array();
			}

			if ( ! $added_to_group ) {
				// TODO create filter
				$header[] = $label;

			}
		}

		//have groups at the end ?
		foreach ( $group_fields as $group_type => $fields ) {
			if ( ! empty( $fields ) ) {
				$this->multiplicate_header( $header, $group_type, $fields );
			}
		}

		do_action( 'woe_make_header_custom_formatter', $this->labels );// BUG: wrong hook

		return $header;
	}

	private function multiplicate_header( &$header, $type, $grouped_headers ) {
		$multiply_fields = array();
		$this->multiplicate_fields( $multiply_fields, $type, array(), $grouped_headers );
		foreach ( array_keys( $multiply_fields ) as $multiply_field ) {
			if ( preg_match( '/^plain_' . $type . '_(.+)_(\d+)/', $multiply_field, $matches ) ) {
				$segment_field = $matches[1];
				$index         = $matches[2];
				$header_tmp    = $this->labels[ $type ]->$segment_field;

				$header[] = apply_filters( 'woe_add_csv_headers', $header_tmp['label'] . ' #' . $index,
					$multiply_field );
			}
		}

	}

	//Summary report started here!

	private static function check_create_session() {
		if ( ! session_id() ) {
			@session_start();
		}
	}

	protected function make_summary_header( $data = '' ) {
		$header = array();
		self::check_create_session();
		$_SESSION['woe_summary_products'] = array();

		foreach ( $this->labels['products']->get_labels() as $label_data ) {
			$field_header = $label_data['label'];
			$key          = $label_data['key'];

			$field_header = apply_filters( 'woe_add_csv_headers', $field_header, $key );
			if ( ! preg_match( '#^(line_|qty)#', $label_data['key'] ) ) {
				$header[] = $field_header;
			} else {
				unset( $this->labels['products']->$key );
			}
		}

		return apply_filters( 'woe_summary_headers', $header );
	}

	private function try_fill_summary_report_fields( $row ) {
		$order = false;

		foreach ( self::get_array_from_array( $row, 'products' ) as $item_id => $item ) {
			$product_item = new WC_Order_Item_Product( $item_id );
			$product      = $product_item->get_product();
			if ( ! $product ) {
				continue;
			}
			if ( ! $order ) {
				$order = new WC_Order( $product_item->get_order_id() );
			}

			$key = $product->get_id();
			$key = apply_filters( "woe_summary_products_adjust_key", $key, $product, $product_item, $order );

			//add new product 
			if ( ! isset( $_SESSION['woe_summary_products'][ $key ] ) ) {
				$new_row = array();
				foreach ( $this->labels['products']->get_labels() as $label_data ) {
					$original_key = $label_data['key'];
					if ( preg_match( '#^(line_|qty)#', $original_key ) )//skip item values!
					{
						continue;
					}
					$field_key = $label_data['parent_key'] ? $label_data['parent_key'] : $original_key;
					if ( preg_match( '#^summary_report_total_#', $field_key ) ) {
						$new_row[ $original_key ] = 0;
					}//total fields
					else {
						$new_row[ $original_key ] = $item[ $field_key ];
					}  // already calculated  
				}
				$new_row                                  = apply_filters( 'woe_summary_column_keys',
					$new_row );// legacy hook
				$new_row                                  = apply_filters( "woe_summary_products_prepare_product",
					$new_row, $key, $product, $product_item, $order );
				$_SESSION['woe_summary_products'][ $key ] = $new_row;
			}

			//increase totals 
			if ( isset( $_SESSION['woe_summary_products'][ $key ]['summary_report_total_qty'] ) ) {
				$_SESSION['woe_summary_products'][ $key ]['summary_report_total_qty'] += $product_item->get_quantity();
			}

			if ( isset( $_SESSION['woe_summary_products'][ $key ]['summary_report_total_amount'] ) ) {
				$total                                                                   = method_exists( $product_item,
					'get_total' ) ? $product_item->get_total() : $product_item['line_total'];
				$_SESSION['woe_summary_products'][ $key ]['summary_report_total_amount'] += wc_round_tax_total( $total );
			}
			do_action( "woe_summary_products_add_item", $key, $product_item, $order );
		}

		//no lines for order!
		return array();
	}

	protected function try_apply_summary_report_fields() {
		if ( $this->summary_report ) {
			$this->summary_processing = true;
			
			//sort by name 
			$first_row = reset($_SESSION['woe_summary_products']);
			if ( isset( $first_row['name'] ) ) {
				uasort($_SESSION['woe_summary_products'], function($a,$b) {
					return strcmp($a['name'],$b['name']);
				});
			}
			
			do_action( 'woe_summary_before_output' );
			if ( $this->mode == 'preview' ) {
				if ( empty( $this->rows ) ) // no headers!
				{
					$this->rows = array();
				}
				$this->rows += $_SESSION['woe_summary_products'];
			} else {
				foreach ( $_SESSION['woe_summary_products'] as $item ) {
					$this->output( $item );
				}
			}
		}
	}

}