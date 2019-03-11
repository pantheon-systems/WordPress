<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Engine {
	public static $current_job_settings = '';
	public static $extractor_options = '';
	public static $current_job_build_mode = '';
	public static $date_format;

	public static $order_id = '';
	public static $orders_exported = 0;
	public static $make_separate_orders = false;

	//
	public static function export( $settings, $filepath ) {
		if ( empty( $settings['destination']['type'] ) ) {
			return __( "No destination selected", 'woo-order-export-lite' );
		}

		if ( ! is_array( $settings['destination']['type'] ) ) {
			$settings['destination']['type'] = array( $settings['destination']['type'] );
		}
		$results = array();
		foreach ( $settings['destination']['type'] as $export_type ) {
			$export_type = strtolower( $export_type );
			if ( ! in_array( strtoupper( $export_type ), WC_Order_Export_Admin::$export_types ) ) {
				return __( "Wrong format", 'woo-order-export-lite' );
			}

			include_once dirname( dirname( __FILE__ ) ) . "/exports/abstract-class-woe-export.php";
			include_once dirname( dirname( __FILE__ ) ) . "/exports/class-woe-export-{$export_type}.php";
			$class    = 'WOE_Export_' . $export_type;
			$exporter = new $class( $settings['destination'] );

			$filename      = self::make_filename( $settings['export_filename'] );
			$custom_export = apply_filters( 'woe_custom_export_to_' . $export_type, false, $filename, $filepath,
				$exporter );
			if ( ! $custom_export ) {
				// try many times?
				$num_retries = 0;
				while ( $num_retries < $exporter->get_num_of_retries() ) {
					$num_retries ++;
					$results[] = $exporter->run_export( $filename, $filepath );
					if ( $exporter->finished_successfully ) {
						break;
					}
				}
				do_action( "woe_export_destination_finished", $exporter->finished_successfully, $export_type, $filename,
					$filepath, $settings, $exporter );
			} else {
				$results[] = $custom_export;
			}
		}

		return implode( "<br>\r\n", $results );
	}

	/* Zapier will pull files! */
	public static function prepare( $settings, $filepath ) {
		if ( empty( $settings['destination']['type'] ) ) {
			return __( "No destination selected", 'woo-order-export-lite' );
		}

		if ( ! is_array( $settings['destination']['type'] ) ) {
			$settings['destination']['type'] = array( $settings['destination']['type'] );
		}
		$results = array();
		foreach ( $settings['destination']['type'] as $export_type ) {
			$export_type = strtolower( $export_type );
			if ( ! in_array( strtoupper( $export_type ), WC_Order_Export_Admin::$export_types ) ) {
				return __( "Wrong export type", 'woo-order-export-lite' );
			}

			include_once dirname( dirname( __FILE__ ) ) . "/exports/abstract-class-woe-export.php";
			include_once dirname( dirname( __FILE__ ) ) . "/exports/class-woe-export-{$export_type}.php";
			$class    = 'WOE_Export_' . $export_type;
			$exporter = new $class( $settings['destination'] );

			$filename       = self::make_filename( $settings['export_filename'] );
			$custom_prepare = apply_filters( 'woe_custom_prepare_to_' . $export_type, false, $filename, $filepath,
				$exporter );
			if ( ! $custom_prepare ) {
				if ( method_exists( $exporter, 'prepare' ) ) {
					$results[] = $exporter->prepare( $filename, $filepath );
				}
			} else {
				$results[] = $custom_prepare;
			}
		}

		return $results;
	}

	public static function make_filename( $mask ) {
		if ( self::$make_separate_orders && strpos( $mask, '%order_id' ) === false ) {
			$mask_parts                                          = explode( '.', $mask );
			$before_prefix                                       = count( $mask_parts ) > 1 ? 2 : 1;
			$mask_parts[ count( $mask_parts ) - $before_prefix ] .= '-%order_id';
			$mask                                                = implode( '.', $mask_parts );
		}
		$time = apply_filters( 'woe_make_filename_current_time', current_time( 'timestamp' ) );

		$date = WC_Order_Export_Data_Extractor::get_date_range( self::$current_job_settings, false );

		$subst = apply_filters( 'woe_make_filename_replacements', array(
			'%d'          => date( 'd', $time ),
			'%m'          => date( 'm', $time ),
			'%y'          => date( 'Y', $time ),
			'%h'          => date( 'H', $time ),
			'%i'          => date( 'i', $time ),
			'%s'          => date( 's', $time ),
			'%order_id'   => self::$order_id,
			'%orderid'    => self::$order_id,
			'%id'         => self::$order_id,
			'{from_date}' => isset( $date['from_date'] ) ? date( "Y-m-d", strtotime( $date['from_date'] ) ) : '',
			'{to_date}'   => isset( $date['to_date'] ) ? date( "Y-m-d", strtotime( $date['to_date'] ) ) : '',
		) );

		return apply_filters( 'woe_make_filename', strtr( $mask, $subst ) );
	}

	public static function kill_buffers() {
		while ( ob_get_level() ) {
			ob_end_clean();
		}
	}

	public static function tempnam( $folder, $prefix ) {
		$filename = @tempnam( $folder, $prefix );
		if ( ! $filename ) {
			$tmp_folder = dirname( dirname( dirname( __FILE__ ) ) ) . '/tmp';
			// kill expired tmp file
			foreach ( glob( $tmp_folder . "/*" ) as $f ) {
				if ( time() - filemtime( $f ) > 24 * 3600 ) {
					unlink( $f );
				}
			}
			$filename = tempnam( $tmp_folder, $prefix );
		}

		return $filename;
	}

	private static function get_order_labels( $settings, $format, $field_formats_list ) {
		$fields = $settings['order_fields'];

		$labels        = new WC_Order_Export_Labels();
		$static_fields = array();
		$field_formats = array();

		foreach ( $fields as $num_index => $field ) {
			if ( empty ( $field['key'] ) ) {
				continue;
			}
			$full_key = $field['key'];

			$key = $full_key;
			if ( preg_match( '/^plain_orders_(.+)/', $full_key, $matches ) ) {
				if ( isset( $matches[1] ) && ! strpos( $matches[1], 'static_field' ) ) {
					$key = $matches[1];
				}
			}


			if ( preg_match( '/^(static_field_.+)/', $full_key, $matches ) ) { // for static fields
				if ( isset( $matches[1] ) ) {
					$static_fields[ $matches[1] ] = isset( $field['value'] ) ? $field['value'] : $field['colname'];// FIX BUG here
				}
			}

			if ( isset( $field['format'] ) && in_array( $field['format'], $field_formats_list ) ) {
				$field_formats[ $field['format'] ][] = $key;
			}

			$field['colname'] = apply_filters( "woe_get_{$format}_label_{$key}", $field['colname'] );
			$labels->$key     = $field['colname'];
		}

		return array(
			'labels'        => $labels->is_not_empty() ? $labels : false,
			'static_fields' => $static_fields,
			'field_formats' => $field_formats,
		);
	}

	/* process product/coupon fields*/
	private static function get_sub_segment_labels( $segment, $settings, $format, $field_formats_list ) {
		$labels        = new WC_Order_Export_Labels();
		$static_fields = array();
		$field_formats = array();

		$is_flat = self::is_plain_format( $format );
		$fields  = $is_flat ? $settings['order_fields'] : $settings[ 'order_' . $segment . '_fields' ];

		foreach ( $fields as $field ) {
			if ( empty ( $field['key'] ) ) {
				continue;
			}
			$full_key = $field['key'];

			$key = $full_key;
			if ( $is_flat ) {
				if ( preg_match( '/^plain_' . $segment . 's_(.+)/', $full_key, $matches ) ) {
					if ( isset( $matches[1] ) ) {
						$key = $matches[1];
					}
				} else {
					continue;
				}
			}

			if ( preg_match( '/^(static_field_.+)/', $key, $matches ) ) { // for static fields
				if ( isset( $matches[1] ) ) {
					$static_fields[ $key ] = isset( $field['value'] ) ? $field['value'] : $field['colname'];// FIX BUG here
				}
			}

			if ( isset( $field['format'] ) && in_array( $field['format'], $field_formats_list ) ) {
				$field_formats[ $field['format'] ][] = $key;
			}

			$field['colname'] = apply_filters( "woe_get_{$format}_label_{$key}", $field['colname'] );
			$labels->$key     = $field['colname'];
		}

		return array(
			'labels'        => $labels->is_not_empty() ? $labels : false,
			'static_fields' => $static_fields,
			'field_formats' => $field_formats,
		);
	}


	/**
	 * @param string $mode
	 * @param array  $settings
	 * @param string $fname
	 * @param null   $labels
	 * @param null   $static_vals
	 *
	 * @return WOE_Formatter
	 */
	private static function init_formater( $mode, $settings, $fname, &$labels, &$static_vals, $offset ) {
		$format = strtolower( $settings['format'] );
		include_once dirname( dirname( __FILE__ ) ) . "/formats/abstract-class-woe-formatter.php";
		if ( ! apply_filters( 'woe_load_custom_formatter_' . $format, false ) ) {
			include_once dirname( dirname( __FILE__ ) ) . "/formats/class-woe-formatter-$format.php";
		}

		$format_settings = array( 'global_job_settings' => $settings );
		foreach ( $settings as $key => $val ) {
			if ( preg_match( '#^format_' . $format . '_(.+)$#', $key, $m ) ) {
				$format_settings[ $m[1] ] = $val;
			}
		}

		self::init_labels( $settings, $labels, $static_vals, $field_formats );

		$class = 'WOE_Formatter_' . $format;

		do_action( 'woe_init_custom_formatter', $mode, $fname, $format_settings, $format, $labels, $field_formats,
			self::$date_format, $settings, $offset );

		return new $class( $mode, $fname, $format_settings, $format, $labels, $field_formats, self::$date_format,
			$offset );
	}

	private static function init_labels( $settings, &$labels, &$static_vals, &$field_formats ) {
		$format = strtolower( $settings['format'] );

//		$static_vals   = array( 'order' => array(), 'products' => array(), 'coupons' => array() );
//		$field_formats = array( 'money' => array(), 'number' => array(), 'date' => array(), 'string' => array() );
//		$labels        = array(
//			'order'    => self::get_labels( $settings,'order_fields', $format, $static_vals['order'], $field_formats ),
//			'products' => self::get_labels( $settings,'order_product_fields', $format, $static_vals['products'],
//				$field_formats ),
//			'coupons'  => self::get_labels( $settings,'order_coupon_fields', $format, $static_vals['coupons'],
//				$field_formats ),
//		);

		$field_formats_ar = array( 'money', 'number', 'date', 'string' );
		$labels_data      = array(
			'order'    => self::get_order_labels( $settings, $format, $field_formats_ar ),
			'products' => self::get_sub_segment_labels( 'product', $settings, $format, $field_formats_ar ),
			'coupons'  => self::get_sub_segment_labels( 'coupon', $settings, $format, $field_formats_ar ),
		);
		$labels           = array();
		$static_vals      = array();
		$field_formats    = array();
		foreach ( $labels_data as $segment => $label_data ) {
			$labels[ $segment ] = ! empty( $label_data['labels'] ) ? $label_data['labels'] : array();
			if ( ! empty( $label_data['static_fields'] ) ) {
				$static_vals[ $segment ] = $label_data['static_fields'];
			}
			if ( ! empty( $label_data['field_formats'] ) ) {
				$field_formats[ $segment ] = array_map( "array_unique", $label_data['field_formats'] );
				//clean up possible duplicates
//				$field_formats = array_merge_recursive( $field_formats, $label_data['field_formats'] );
			}

		}
//		$field_formats  = array_map( "array_unique", $field_formats );
	}


	/**
	 * @param $settings
	 * @param $export
	 */
	private static function _check_products_and_coupons_fields( $settings, &$export ) {
		$export['products'] = false;
		$export['coupons']  = false;
		foreach ( $settings['order_fields'] as $field ) {
			if ( 'products' == $field['key'] ) {
				$export['products'] = true;
			}
			if ( 'coupons' == $field['key'] ) {
				$export['coupons'] = true;
			}
			if ( $export['coupons'] && $export['products'] ) {
				break;
			}
		}

	}

	private static function _install_options( $settings ) {
		global $wpdb;

		$format = strtolower( $settings['format'] );

		$options = array();

		$options['item_rows_start_from_new_line'] = ( $format == 'csv' AND @$settings['format_csv_item_rows_start_from_new_line'] );
		$options['products_mode']                 = isset( $settings['duplicated_fields_settings']['products']['repeat'] ) ? $settings['duplicated_fields_settings']['products']['repeat'] : "";
		$options['coupons_mode']                  = isset( $settings['duplicated_fields_settings']['coupons']['repeat'] ) ? $settings['duplicated_fields_settings']['coupons']['repeat'] : "";

		if ( ! empty( $settings['all_products_from_order'] ) ) {
			$options['include_products'] = false;
		} else {
			$options['include_products'] = $wpdb->get_col( WC_Order_Export_Data_Extractor::sql_get_product_ids( $settings ) );
		}

		if ( isset( $settings['date_format'] ) ) {
			$options['date_format'] = $settings['date_format'];
		} else {
			$options['date_format'] = 'Y-m-d';
		}

		if ( isset( $settings['time_format'] ) ) {
			$options['time_format'] = $settings['time_format'];
		} else {
			$options['time_format'] = 'H:i';
		}

		//as is
		$options['export_refunds']       = $settings['export_refunds'];
		$options['skip_refunded_items']  = $settings['skip_refunded_items'];
		$options['export_all_comments']  = $settings['export_all_comments'];
		$options['export_refund_notes']  = $settings['export_refund_notes'];
		$options['format_number_fields'] = $settings['format_number_fields'];

		if ( $settings['enable_debug'] AND ! ini_get( 'display_errors' ) ) {
			ini_set( 'display_errors', 1 );
			$old_error_reporting = error_reporting( E_ALL );
			add_action( 'woe_export_finished', function () use ( $old_error_reporting ) {
				ini_set( 'display_errors', 0 );
				error_reporting( $old_error_reporting );
			} );
		}

		if ( $settings['cleanup_phone'] ) {
			foreach ( array( "billing_phone", "USER_billing_phone" ) as $field ) {
				add_filter( 'woe_get_order_value_' . $field, function ( $value, $order, $fieldname ) {
					$value = preg_replace( "#[^\d]+#", "", $value );

					return $value;
				}, 10, 3 );
			}
		}

		$options['strip_tags_product_fields'] = ! empty( $settings['strip_tags_product_fields'] );

		return $options;
	}

	private static function validate_defaults( $settings ) {
		if ( empty( $settings['sort'] ) ) {
			$settings['sort'] = 'order_id';
		}
		if ( empty( $settings['sort_direction'] ) ) {
			$settings['sort_direction'] = 'DESC';
		}
		if ( ! isset( $settings['skip_empty_file'] ) ) {
			$settings['skip_empty_file'] = true;
		}
		if ( $settings['custom_php'] ) {
			ob_start( array( 'WC_Order_Export_Engine', 'code_error_callback' ) );
			$result = eval( $settings['custom_php_code'] );
			ob_end_clean();
		}
		// This report works with products!
		if ( $settings['summary_report_by_products'] ) {
			$settings['order_fields']['products']['checked'] = 1;
		}

		return apply_filters( 'woe_settings_validate_defaults', $settings );
	}

	private static function code_error_callback( $out ) {
		$error = error_get_last();

		if ( is_null( $error ) ) {
			return $out;
		}

		$m = '<h2>' . __( "Don't Panic", 'woo-order-export-lite' ) . '</h2>';
		$m .= '<p>' . sprintf( __( 'The code you are trying to save produced a fatal error on line %d:',
				'woo-order-export-lite' ), $error['line'] ) . '</p>';
		$m .= '<strong>' . $error['message'] . '</strong>';

		return $m;
	}

	private static function try_modify_status( $order_id, $settings ) {
		if ( isset( $settings['change_order_status_to'] ) && wc_is_order_status( $settings['change_order_status_to'] ) ) {
			$order = new WC_Order( $order_id );
			$order->update_status( $settings['change_order_status_to'] );
		}
	}

	private static function try_mark_order( $order_id, $settings ) {
		if ( $settings['mark_exported_orders'] ) {
			update_post_meta( $order_id, 'woe_order_exported', current_time( 'timestamp' ) );
		}
	}

	public static function build_file(
		$settings,
		$make_mode,
		$output_mode,
		$offset = false,
		$limit = false,
		$filename = ''
	) {
		global $wpdb;

		self::kill_buffers();
		$settings                     = self::validate_defaults( $settings );
		self::$current_job_settings   = $settings;
		self::$current_job_build_mode = $make_mode;
		self::$date_format            = trim( $settings['date_format'] . ' ' . $settings['time_format'] );
		//debug sql?
		if ( $make_mode == 'preview' AND $settings['enable_debug'] ) {
			WC_Order_Export_Data_Extractor::start_track_queries();
		}
		// might run sql!	
		self::$extractor_options = self::_install_options( $settings );

		if ( $output_mode == 'browser' ) {
			$filename = 'php://output';
		} else {
			$filename = ( ! empty( $filename ) ? $filename : self::tempnam( sys_get_temp_dir(), $settings['format'] ) );
		}

		if ( $make_mode !== 'estimate' ) {
			$formater = self::init_formater( $make_mode, $settings, $filename, $labels, $static_vals, $offset );
		}
		$format = strtolower( $settings['format'] );

		if ( $make_mode == 'finish' ) {
//			self::maybe_output_summary_report( $formater );
			$formater->finish();

			return $filename;
		}


		//get IDs
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		if ( $make_mode == 'estimate' ) { //if estimate return total count
			return $wpdb->get_var( str_replace( 'ID AS order_id', 'COUNT(ID) AS order_count', $sql ) );
		} elseif ( $make_mode == 'preview' ) {
			$sql .= apply_filters( "woe_sql_get_order_ids_order_by",
					" ORDER BY " . $settings['sort'] . " " . $settings['sort_direction'] ) . " LIMIT " . ( $limit !== false ? $limit : 1 );
		} elseif ( $make_mode == 'partial' ) {
			$sql     .= apply_filters( "woe_sql_get_order_ids_order_by",
				" ORDER BY " . $settings['sort'] . " " . $settings['sort_direction'] );
			$startat = ( $settings['mark_exported_orders'] && $settings['export_unmarked_orders'] ) ? 0 : intval( $offset );
			$limit   = intval( $limit );
			$sql     .= " LIMIT $startat,$limit";
		}

		$order_ids = apply_filters( "woe_get_order_ids", $wpdb->get_col( $sql ) );

		// prepare for XLS/CSV moved to plain formatter

		// check it once
		self::_check_products_and_coupons_fields( $settings, $export );

		// make header moved to plain formatter

		if ( $make_mode != 'partial' ) { // Preview or start_estimate
//			self::maybe_init_summary_report( $labels );
			$formater->start();
			if ( $make_mode == 'start_estimate' ) { //Start return total count
				return $wpdb->get_var( str_replace( 'ID AS order_id', 'COUNT(ID) AS order_count', $sql ) );
			}
		}
//		self::maybe_start_summary_report();

		WC_Order_Export_Data_Extractor::prepare_for_export();
		self::$orders_exported = 0;// incorrect value
		foreach ( $order_ids as $order_id ) {
			$order_id = apply_filters( "woe_order_export_started", $order_id );
			if ( ! $order_id ) {
				continue;
			}
			self::$order_id = $order_id;
			$row            = WC_Order_Export_Data_Extractor::fetch_order_data( $order_id, $labels,
				$export, $static_vals, self::$extractor_options );

			$row = apply_filters( "woe_fetch_order_row", $row, $order_id );
			if ( $row ) {
				$formater->output( $row );
				do_action( "woe_order_row_exported", $row, $order_id );
			}

			if ( $make_mode != 'preview' ) {
				do_action( "woe_order_exported", $order_id );
				self::try_mark_order( $order_id, $settings );
			} else {
				do_action( "woe_order_previewed", $order_id );
			}
		}

		// for modes
		if ( $make_mode == 'partial' ) {
			$formater->finish_partial();
		} elseif ( $make_mode == 'preview' ) {
//			self::maybe_output_summary_report( $formater );
			//limit debug output 
			if ( $settings['enable_debug'] AND self::is_plain_format( $settings['format'] ) ) {
				echo "<b>" . __( 'Main SQL queries are listed below', 'woo-order-export-lite' ) . "</b>";
				echo '<textarea rows=5 style="width:100%">';
				$s = array();
				foreach ( WC_Order_Export_Data_Extractor::get_sql_queries() as $sql ) {
					$s[] = preg_replace( "#\s+#", " ", $sql );
				}
				echo join( "\n\n", $s );
				echo '</textarea>';
			}
			$formater->finish();
		}

		// no action woe_export_finished here!
		return $filename;
	}

	public static function build_file_full( $settings, $filename = '', $limit = 0, $order_ids = array() ) {
		global $wpdb;

		//no need self::kill_buffers();
		$settings                     = self::validate_defaults( $settings );
		self::$current_job_settings   = $settings;
		self::$current_job_build_mode = 'full';
		self::$date_format            = trim( $settings['date_format'] . ' ' . $settings['time_format'] );
		self::$extractor_options      = self::_install_options( $settings );

		$filename = ( ! empty( $filename ) ? $filename : self::tempnam( sys_get_temp_dir(), $settings['format'] ) );

		$formater = self::init_formater( '', $settings, $filename, $labels, $static_vals, 0 );
//		$format   = strtolower( $settings['format'] );

//		self::maybe_init_summary_report( $labels );
//		self::maybe_start_summary_report();

		//get IDs
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		$sql .= apply_filters( "woe_sql_get_order_ids_order_by",
			" ORDER BY " . $settings['sort'] . " " . $settings['sort_direction'] );

		if ( $limit ) {
			$sql .= " LIMIT " . intval( $limit );
		}
		if ( ! $order_ids ) {
			$order_ids = apply_filters( "woe_get_order_ids", $wpdb->get_col( $sql ) );
		}

		if ( empty( $order_ids ) AND apply_filters( 'woe_schedule_job_skip_empty_file',
				(bool) $settings['skip_empty_file'] ) ) {
			unlink( $filename );

			return false;
		}

		// prepare for XLS/CSV moved to plain formatter

		// check it once
		self::_check_products_and_coupons_fields( $settings, $export );

		// make header moved to plain formatter

		$formater->start();
		do_action( 'woe_start_custom_formatter' );

		WC_Order_Export_Data_Extractor::prepare_for_export();
		self::$orders_exported = 0;
		foreach ( $order_ids as $order_id ) {
			$order_id = apply_filters( "woe_order_export_started", $order_id );
			if ( ! $order_id ) {
				continue;
			}
			self::$order_id = $order_id;
			$row            = WC_Order_Export_Data_Extractor::fetch_order_data( $order_id, $labels,
				$export, $static_vals, self::$extractor_options );
			$row            = apply_filters( "woe_fetch_order_row", $row, $order_id );
			if ( $row ) {
				$formater->output( $row );
				do_action( "woe_order_row_exported", $row, $order_id );
			}
			do_action( "woe_order_exported", $order_id );

			do_action( 'woe_formatter_output_custom_formatter', $row, $order_id, $labels,
				$export, $static_vals, self::$extractor_options );

			self::$orders_exported ++;
			self::try_modify_status( $order_id, $settings );
			self::try_mark_order( $order_id, $settings );
		}

//		self::maybe_output_summary_report( $formater );
		$formater->finish();
		do_action( 'woe_finish_custom_formatter' );

		do_action( 'woe_export_finished' );

		return $filename;
	}

	public static function build_separate_files_and_export(
		$settings,
		$filename = '',
		$limit = 0,
		$order_ids = array()
	) {
		global $wpdb;

		self::kill_buffers();
		$settings                     = self::validate_defaults( $settings );
		self::$current_job_settings   = $settings;
		self::$current_job_build_mode = 'full';
		self::$date_format            = trim( $settings['date_format'] . ' ' . $settings['time_format'] );
		self::$extractor_options      = self::_install_options( $settings );

		$filename = ( ! empty( $filename ) ? $filename : self::tempnam( sys_get_temp_dir(), $settings['format'] ) );

//		$format = strtolower( $settings['format'] );

		//get IDs
		$sql = WC_Order_Export_Data_Extractor::sql_get_order_ids( $settings );
		$sql .= apply_filters( "woe_sql_get_order_ids_order_by",
			" ORDER BY " . $settings['sort'] . " " . $settings['sort_direction'] );

		if ( $limit ) {
			$sql .= " LIMIT " . intval( $limit );
		}

		if ( ! $order_ids ) {
			$order_ids = apply_filters( "woe_get_order_ids", $wpdb->get_col( $sql ) );
		}

		if ( empty( $order_ids ) ) {
			return false;
		}
		// prepare for XLS/CSV moved to plain formatter

		// check it once
		self::_check_products_and_coupons_fields( $settings, $export );

		// make header moved to plain formatter

		$result = false;

		WC_Order_Export_Data_Extractor::prepare_for_export();
		self::$make_separate_orders = true;
		foreach ( $order_ids as $order_id ) {
			$order_id = apply_filters( "woe_order_export_started", $order_id );
			if ( ! $order_id ) {
				continue;
			}
			self::$order_id = $order_id;
			$formater       = self::init_formater( '', $settings, $filename, $labels, $static_vals, 0 );

			$formater->truncate();
			$formater->start();
			$row = WC_Order_Export_Data_Extractor::fetch_order_data( $order_id, $labels,
				$export, $static_vals, self::$extractor_options );
			$row = apply_filters( "woe_fetch_order_row", $row, $order_id );

			if ( $row ) {
				$formater->output( $row );
				do_action( "woe_order_row_exported", $row, $order_id );
			}
			do_action( "woe_order_exported", $order_id );
			self::$orders_exported = 1;
			self::try_modify_status( $order_id, $settings );
			self::try_mark_order( $order_id, $settings );
			$formater->finish();

			if ( $filename !== false ) {
				$result = self::export( $settings, $filename );
				//if ($result) {
				//	return $result;
				//}
			}
			self::$order_id = '';
		}

		do_action( 'woe_export_finished' );

		return $result; //return last result
	}


	public static function build_files_and_export( $settings, $filename = '', $limit = 0, $order_ids = array() ) {
		if ( ! empty( $settings['destination']['separate_files'] ) ) {
			$result = self::build_separate_files_and_export( $settings, $filename, $limit, $order_ids );
		} else {
			$file = self::build_file_full( $settings, $filename, $limit, $order_ids );
			if ( $file !== false ) {

				$result = self::export( $settings, $file );

				if ( file_exists( $file ) ) {
					unlink( $file );
				}

			} else {
				$result = false;
			}
		}

		if ( $result === false ) {
			$result = __( 'Nothing to export. Please, adjust your filters', 'woo-order-export-lite' );
		}

		return $result;
	}

	public static function build_files_and_prepare( $settings, $filename = '', $limit = 0, $order_ids = array() ) {
		$file = self::build_file_full( $settings, $filename, $limit, $order_ids );
		if ( $file !== false ) {
			$result = self::prepare( $settings, $file );

			return $result;
		} else {
			return __( 'Nothing to export. Please, adjust your filters', 'woo-order-export-lite' );
		}
	}

	public static function is_plain_format( $format ) {
		return in_array( strtolower( $format ), self::get_plain_formats() );
	}

	public static function get_plain_formats() {
		return array( 'xls', 'csv', 'tsv', 'pdf' );
	}
}