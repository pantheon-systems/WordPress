<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_Order_Export_Manage {
	const settings_name_now      = 'woocommerce-order-export-now';
	const settings_name_cron     = 'woocommerce-order-export-cron';
	const settings_name_profiles = 'woocommerce-order-export-profiles';
	const settings_name_actions  = 'woocommerce-order-export-actions';

	const EXPORT_NOW          = 'now';
	const EXPORT_PROFILE      = 'profiles';
	const EXPORT_SCHEDULE     = 'cron';
	const EXPORT_ORDER_ACTION = 'order-action';

	public static $edit_existing_job = false;

	static function get_days() {
		return array(
			'Sun' => __( 'Sun', 'woo-order-export-lite' ),
			'Mon' => __( 'Mon', 'woo-order-export-lite' ),
			'Tue' => __( 'Tue', 'woo-order-export-lite' ),
			'Wed' => __( 'Wed', 'woo-order-export-lite' ),
			'Thu' => __( 'Thu', 'woo-order-export-lite' ),
			'Fri' => __( 'Fri', 'woo-order-export-lite' ),
			'Sat' => __( 'Sat', 'woo-order-export-lite' ),
		);
	}

	static function get_settings_name_for_mode( $mode ) {
		$name = '';
		if ( $mode == self::EXPORT_NOW ) {
			$name = self::settings_name_now;
		} elseif ( $mode == self::EXPORT_SCHEDULE ) {
			$name = self::settings_name_cron;
		} elseif ( $mode == self::EXPORT_PROFILE ) {
			$name = self::settings_name_profiles;
		} elseif ( $mode == self::EXPORT_ORDER_ACTION ) {
			$name = self::settings_name_actions;
		}

		return $name;
	}

	static function remove_settings() {
		$options = array(
			self::settings_name_now,
			self::settings_name_cron,
			self::settings_name_profiles,
			self::settings_name_actions,
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}

	// arrays
	static function get_export_settings_collection( $mode ) {
		$name = self::get_settings_name_for_mode( $mode );

		return get_option( $name, array() );
	}

	static function save_export_settings_collection( $mode, $jobs ) {
		$name = self::get_settings_name_for_mode( $mode );
		$result = update_option( $name, $jobs, false );
		
		if ( $mode == self::EXPORT_SCHEDULE ) {
			WC_Order_Export_Cron::try_install_job( true ); // must delete existing job!
		}

		return $result;
	}


	static function make_new_settings( $in ) {
		$new_settings = $in['settings'];

		// use old PHP code if no permissions, just to stop trcky persons ;)
		if ( ! WC_Order_Export_Admin::user_can_add_custom_php() ) {
			unset( $new_settings['custom_php_code'] );
		}

		// UI don't pass empty multiselects
		$multiselects = array(
			'from_status',
			'to_status',
			'statuses',
			'order_custom_fields',
			'product_custom_fields',
			'product_categories',
			'product_vendors',
			'products',
			'shipping_locations',
			'shipping_methods',
			'user_roles',
			'user_names',
			'user_custom_fields',
			'coupons',
			'billing_locations',
			'payment_methods',
			'product_attributes',
			'product_itemmeta',
			'product_taxonomies',
			'item_names',
			'item_metadata',
		);
		foreach ( $multiselects as $m_select ) {
			if ( ! isset( $new_settings[ $m_select ] ) ) {
				$new_settings[ $m_select ] = array();
			}
		}

		if ( ! isset( $in['mode'] ) ) {
			$in['mode'] = null;
		}

		if ( ! isset( $in['id'] ) ) {
			$in['id'] = null;
		}

		$settings                               = self::get( $in['mode'], $in['id'] );
		$settings['id']                         = (int)$in['id'];
		$settings['duplicated_fields_settings'] = isset( $in['duplicated_fields_settings'] ) ? $in['duplicated_fields_settings'] : array();

		// setup new values for same keys
		foreach ( $new_settings as $key => $val ) {
			$settings[ $key ] = $val;
		}

		$sections = array(
			'orders' => 'order_fields',
		);

		if ( ! WC_Order_Export_Engine::is_plain_format( $new_settings['format'] ) ) {
			$sections['products'] = 'order_product_fields';
			$sections['coupons']  = 'order_coupon_fields';
		}

		foreach ( $sections as $section => $fieldset ) {
			$section_fields        = isset( $in[ $section ] ) ? $in[ $section ] : array();
			$settings[ $fieldset ] = $section_fields;
		}

		return self::apply_defaults( $in['mode'], $settings );
	}

	static function get( $mode, $id = false ) {

		$all_jobs = self::get_export_settings_collection( $mode );

		if ( $mode == self::EXPORT_NOW ) { // one job
			if ( ! isset( $all_jobs['version'] ) ) {
				$all_jobs = self::convert_settings_to_version_2( $mode, $all_jobs );
			}

			return self::apply_defaults( $mode, $all_jobs );
		} elseif ( $id === false ) {
			if ( empty( $all_jobs ) OR ! is_array( $all_jobs ) ) {
				return array();
			}

			return array_map( function ( $item ) use ( $mode ) {

				if ( ! isset( $item['version'] ) ) {
					$item = self::convert_settings_to_version_2( $mode, $item );
				}

				return WC_Order_Export_Manage::apply_defaults( $mode, $item );
			}, $all_jobs );
		}

		$settings = isset( $all_jobs[ $id ] ) ? $all_jobs[ $id ] : array();

		if ( ! isset( $settings['version'] ) ) {
			$settings = self::convert_settings_to_version_2( $mode, $settings );
		}

		return self::apply_defaults( $mode, $settings );
	}

	private static function get_defaults( $mode ) {
		return array(
			'version'                                  => '2.0',
			'mode'                                     => $mode,
			'title'                                    => '',
			'skip_empty_file'                          => true,
			'log_results'                              => false,
			'from_status'                              => array(),
			'to_status'                                => array(),
			'change_order_status_to'                   => '',
			'statuses'                                 => array(),
			'from_date'                                => '',
			'to_date'                                  => '',
			'shipping_locations'                       => array(),
			'shipping_methods'                         => array(),
			'item_names'                               => array(),
			'item_metadata'                            => array(),
			'user_roles'                               => array(),
			'user_names'                               => array(),
			'user_custom_fields'                       => array(),
			'billing_locations'                        => array(),
			'payment_methods'                          => array(),
			'any_coupon_used'                          => 0,
			'coupons'                                  => array(),
			'order_custom_fields'                      => array(),
			'product_categories'                       => array(),
			'product_vendors'                          => array(),
			'products'                                 => array(),
			'product_taxonomies'                       => array(),
			'product_custom_fields'                    => array(),
			'product_attributes'                       => array(),
			'product_itemmeta'                         => array(),
			'format'                                   => 'XLS',
			'format_xls_use_xls_format'                => 0,
			'format_xls_sheet_name'                    => __( 'Orders', 'woo-order-export-lite' ),
			'format_xls_display_column_names'          => 1,
			'format_xls_auto_width'                    => 1,
			'format_xls_direction_rtl'                 => 0,
			'format_xls_force_general_format'          => 0,
			'format_csv_enclosure'                     => '"',
			'format_csv_delimiter'                     => ',',
			'format_csv_linebreak'                     => '\r\n',
			'format_csv_display_column_names'          => 1,
			'format_csv_add_utf8_bom'                  => 0,
			'format_csv_item_rows_start_from_new_line' => 0,
			'format_csv_encoding'                      => 'UTF-8',
			'format_csv_delete_linebreaks'             => 0,
			'format_tsv_linebreak'                     => '\r\n',
			'format_tsv_display_column_names'          => 1,
			'format_tsv_add_utf8_bom'                  => 0,
			'format_tsv_encoding'                      => 'UTF-8',
			'format_xml_root_tag'                      => 'Orders',
			'format_xml_order_tag'                     => 'Order',
			'format_xml_product_tag'                   => 'Product',
			'format_xml_coupon_tag'                    => 'Coupon',
			'format_xml_prepend_raw_xml'               => '',
			'format_xml_append_raw_xml'                => '',
			'format_xml_self_closing_tags'             => 1,

			'format_pdf_display_column_names'          => 1,
			'format_pdf_repeat_header'                 => 1,
			'format_pdf_orientation'                   => 'L',
			'format_pdf_page_size'                     => 'A4',
			'format_pdf_font_size'                     => 8,
			'format_pdf_header_text'                   => '',
			'format_pdf_footer_text'                   => '',
			'format_pdf_pagination'                    => 'C',
			'format_pdf_fit_page_width'                => 0,
			'format_pdf_cols_width'                   => '25',
			'format_pdf_cols_align'                   => 'L',
			'format_pdf_page_header_text_color'        => '#000000',
			'format_pdf_page_footer_text_color'        => '#000000',
			'format_pdf_table_header_text_color'       => '#000000',
			'format_pdf_table_header_background_color' => '#FFFFFF',
			'format_pdf_table_row_text_color'          => '#000000',
			'format_pdf_table_row_background_color'    => '#FFFFFF',
			'format_pdf_logo_source'                   => '',
			'format_pdf_logo_width'                    => 0,
			'format_pdf_logo_height'                   => 15,
			'format_pdf_logo_align'                    => 'R',

			'all_products_from_order'                  => 1,
			'skip_refunded_items'                      => 1,
			'skip_suborders'                           => 0,
			'export_refunds'                           => 0,
			'date_format'                              => 'Y-m-d',
			'time_format'                              => 'H:i',
			'sort_direction'                           => 'DESC',
			'sort'                                     => 'order_id',
			'format_number_fields'                     => 0,
			'export_all_comments'                      => 0,
			'export_refund_notes'                      => 0,
			'strip_tags_product_fields'                => 0,
			'cleanup_phone'                            => 0,
			'enable_debug'                             => 0,
			'format_json_start_tag'                    => '[',
			'format_json_end_tag'                      => ']',
			'custom_php'                               => 0,
			'custom_php_code'                          => '',
			'mark_exported_orders'                     => 0,
			'export_unmarked_orders'                   => 0,

			'summary_report_by_products' => 0,
			'duplicated_fields_settings' => array(
				'products' =>
					array(
						'repeat'                 => 'rows',
						'populate_other_columns' => '1',
						'max_cols'               => '10',
						'line_delimiter'         => '\\n',
						'group_by'               => 'product',
					),
				'coupons'  =>
					array(
						'repeat'         => 'rows',
						'max_cols'       => '10',
						'line_delimiter' => '\\n',
						'group_by'       => 'product',
					),
			),
		);
	}

	static function apply_defaults( $mode, $settings ) {
		$settings = apply_filters( "woe_before_apply_default_settings", $settings, $mode );

		$defaults = self::get_defaults( $mode );

		if ( ! isset( $settings['format'] ) ) {
			$settings['format'] = 'XLS';
		}

		if ( ! isset( $settings['export_rule_field'] ) AND $mode == WC_Order_Export_Manage::EXPORT_SCHEDULE ) {
			$settings['export_rule_field'] = 'modified';
		}

		foreach ( array( 'order_fields', 'order_product_fields', 'order_coupon_fields' ) as $index ) {
			if ( ! isset( $settings[ $index ] ) ) {
				$additional_fields = self::move_fields_key( self::get_default_fields( $index, $settings['format'] ) );
				self::remove_unchecked_fields( $additional_fields );
				$settings[ $index ] = $additional_fields;
				if ( 'order_fields' !== $index ) {
					$map_segment = array(
						'order_product_fields' => 'products',
						'order_coupon_fields'  => 'coupons',
					);

					$settings['order_fields'] = array_merge(
						$settings['order_fields'],
						array_map( function ( $value ) use ( $map_segment, $index ) {
							$value['segment'] = $map_segment[ $index ];
							$value['key']     = 'plain_' . $map_segment[ $index ] . '_' . $value['key'];

							return $value;
						}, $additional_fields )
					);
				}
			}
		}

		// add parent fields if not exists
		foreach ( array( 'products', 'coupons' ) as $main_field ) {
			if ( in_array( $main_field, wp_list_pluck( $settings['order_fields'], 'key' ) ) ) {
				continue;
			}

			$add = false;

			// get correct structure
			$default = self::move_fields_key( WC_Order_Export_Data_Extractor_UI::get_order_fields( $settings['format'], (array) $main_field ) );
			self::remove_unchecked_fields( $default );

			foreach ( $settings['order_fields'] as $num_index => $field ) {
				if ( $main_field === $field['segment'] ) {
					array_splice( $settings['order_fields'], $num_index, 0, $default );
					$add = true;
					break;
				}
			}

			if ( ! $add ) {
				$settings['order_fields'][] = $default;
			}
		}

		return array_merge( $defaults, $settings );
	}

	private static function get_default_fields( $index, $format ) {
		$result = array();
		switch ( $index ) {
			case 'order_fields':
				$result = WC_Order_Export_Data_Extractor_UI::get_order_fields( $format );
				break;
			case 'order_product_fields':
				$result = WC_Order_Export_Data_Extractor_UI::get_order_product_fields( $format );
				break;
			case 'order_coupon_fields':
				$result = WC_Order_Export_Data_Extractor_UI::get_order_coupon_fields( $format );
				break;
		};

		return $result;
	}

	private static function remove_unchecked_fields( &$fields ) {
		foreach ( $fields as $key => $field ) {
			if ( empty( $field['checked'] ) ) {
				unset( $fields[ $key ] );
			} elseif ( isset( $field['checked'] ) ) {
				unset( $fields[ $key ]['checked'] );
			}
		}
		$fields = array_values( $fields );
	}

	private static function move_fields_key( $fields ) {

		return array_map( function ( $key, $value ) {
			if ( ! key_exists( 'key', $value ) ) {
				$value['key'] = $key;
			}

			return $value;
		}, array_keys( $fields ), $fields );
	}

	static function merge_settings_and_default_new( &$opt, $defaults ) {

		$opt      = self::move_fields_key( $opt );
		$defaults = self::move_fields_key( $defaults );


		foreach ( $defaults as $v ) {
			$exists = false;
			foreach ( $opt as $num_index => $option ) {
				if ( $v['key'] == $option['key'] ) {
					//set default attribute OR add to option
					if ( isset( $v['default'] ) ) {
						$option['default'] = $v['default'];
					}
					//set default format OR add to option
					if ( isset( $v['format'] ) ) {
						$option['format'] = $v['format'];
					}
					// overwrite labels for localization
					$option['label'] = $v['label'];

					$exists = true;
					break;
				}
			};

			if ( ! $exists ) {
				if ( self::$edit_existing_job AND $v['checked'] == "1" ) {
					$v['checked'] = "0";
				}
				$opt[] = $v;
			}
		};
	}

	public static function make_all_fields( $format ) {
		$order_fields = array();
		foreach ( array_keys( WC_Order_Export_Data_Extractor_UI::get_order_segments() ) as $segment ) {
			if ( 'products' == $segment ) {
				$method = "get_order_product_fields";
				$filter = "woe_get_order_product_fields";
			} elseif ( 'coupons' == $segment ) {
				$method = "get_order_coupon_fields";
				$filter = "woe_get_order_coupon_fields";
			} elseif ( 'misc' == $segment ) {
				$method = "get_order_fields_misc";
				$filter = "woe_get_order_fields"; //add ALL custom Order fields to last tab
			} else {
				$method = "get_order_fields_" . $segment;
				$filter = "woe_get_order_fields_" . $segment;
			}

			if ( method_exists( 'WC_Order_Export_Data_Extractor_UI', $method ) ) {
				// woe_get_order_fields_common	filter
				$segment_fields         = array();
				$default_segment_fields = array_merge(
					WC_Order_Export_Data_Extractor_UI::$method( $format ),
					apply_filters( $filter, array(), $format )
				);
				foreach ( $default_segment_fields as $key => $value ) {
					$order_field            = $value;
					$order_field['colname'] = $value['label'];
					$order_field['key']     = $key;
					$order_field['default'] = 1;
					unset( $order_field['checked'] );
					$segment_fields[] = $order_field;
				}

				$order_fields[ $segment ] = $segment_fields;
			}
		}

		return $order_fields;
	}

	private static function process_fields( $fields ) {

		return array_map( function ( $key, $value ) {
			if ( ! key_exists( 'key', $value ) ) {
				$value['key'] = $key;
			}

			return $value;
		}, array_keys( $fields ), $fields );
	}

	static function merge_settings_and_default( &$opt, $defaults ) {
		foreach ( $defaults as $k => $v ) {
			if ( isset( $opt[ $k ] ) ) {
				//set default attribute OR add to option
				if ( isset( $v['default'] ) ) {
					$opt[ $k ]['default'] = $v['default'];
				}
				//set default format OR add to option
				if ( isset( $v['format'] ) ) {
					$opt[ $k ]['format'] = $v['format'];
				}
				// overwrite labels for localization
				$opt[ $k ]['label'] = $v['label'];
			} else {
				if ( self::$edit_existing_job AND $v['checked'] == "1" ) {
					$v['checked'] = "0";
				}
				$opt[ $k ] = $v;
			}
		}
	}

	static function save_export_settings( $mode, $id, $options ) {
		$all_jobs = self::get_export_settings_collection( $mode );
		if ( $mode == self::EXPORT_NOW ) {
			$all_jobs = $options;// just replace
		} elseif ( $mode == self::EXPORT_SCHEDULE ) {
			if ( $id ) {
				$options['schedule']['last_run'] = isset( $all_jobs[ $id ] ) ? $all_jobs[ $id ]['schedule']['last_run'] : current_time( "timestamp", 0 );
				$options['schedule']['last_report_sent'] = isset( $all_jobs[ $id ] ) ? $all_jobs[ $id ]['schedule']['last_report_sent'] : current_time( "timestamp", 0 );
				$options['schedule']['next_run'] = WC_Order_Export_Cron::next_event_timestamp_for_schedule( $options['schedule'], $id );
				$all_jobs[ $id ]                 = $options;
			} else {
				$options['schedule']['last_run'] = current_time( "timestamp", 0 );
				$options['schedule']['last_report_sent'] = current_time( "timestamp", 0 );
				$options['schedule']['next_run'] = WC_Order_Export_Cron::next_event_timestamp_for_schedule( $options['schedule'] );
				$all_jobs[]                      = $options; // new job
				end( $all_jobs );
				$id = key( $all_jobs );
			}
		} elseif ( $mode == self::EXPORT_PROFILE OR $mode == self::EXPORT_ORDER_ACTION ) {
			if ( $id ) {
				$all_jobs[ $id ] = $options;
			} else {
				$all_jobs[] = $options; // new job
				end( $all_jobs );
				$id = key( $all_jobs );
			}
		}

		self::save_export_settings_collection( $mode, $all_jobs );

		return $id;
	}

	static function clone_export_settings( $mode, $id ) {
		return self::advanced_clone_export_settings( $id, $mode, $mode );
	}

	static function advanced_clone_export_settings(
		$id,
		$mode_in = self::EXPORT_SCHEDULE,
		$mode_out = self::EXPORT_SCHEDULE
	) {
		$all_jobs_in = self::get_export_settings_collection( $mode_in );
		//new settings
		$settings         = $all_jobs_in[ $id ];
		$settings['mode'] = $mode_out;

		if ( $mode_in !== $mode_out ) {
			$all_jobs_out = self::get_export_settings_collection( $mode_out );
		} else {
			$mode_out          = $mode_in;
			$all_jobs_out      = $all_jobs_in;
			$settings['title'] .= " [cloned]"; //add note
		}

		if ( $mode_in === self::EXPORT_PROFILE && $mode_out === self::EXPORT_SCHEDULE ) {
			if ( ! isset( $settings['destination'] ) ) {
				$settings['destination'] = array(
					'type' => 'folder',
					'path' => get_home_path(),
				);
			}

			if ( ! isset( $settings['export_rule'] ) ) {
				$settings['export_rule'] = 'last_run';
			}

			if ( ! isset( $settings['export_rule_field'] ) ) {
				$settings['export_rule_field'] = 'modified';
			}

			if ( ! isset( $settings['schedule'] ) ) {
				$settings['schedule'] = array(
					'type'   => 'schedule-1',
					'run_at' => '00:00',
				);
			}

			unset( $settings['use_as_bulk'] );
		}

		end( $all_jobs_out );
		$next_id                  = key( $all_jobs_out ) + 1;
		$all_jobs_out[ $next_id ] = $settings;

		self::save_export_settings_collection( $mode_out, $all_jobs_out );

		return $next_id;
	}


	static function set_correct_file_ext( &$settings ) {
		if ( $settings['format'] == 'XLS' AND ! $settings['format_xls_use_xls_format'] ) {
			$settings['format'] = 'XLSX';
		}
	}

	static function import_settings( $data ) {
		$allowed_options = array(
			self::EXPORT_NOW,
			self::EXPORT_SCHEDULE,
			self::EXPORT_PROFILE,
			self::EXPORT_ORDER_ACTION,
		);
		if ( isset( $data[ self::EXPORT_NOW ] ) ) { // import ALL
			foreach ( $allowed_options as $key ) {
				if ( isset( $data[ $key ] ) ) {
					$setting_name = self::get_settings_name_for_mode( $key );

					if ( isset( $data[ $key ]['mode'] ) ) {
						$data[ $key ] = self::edit_import_data( $data[ $key ] );
					} else {
						foreach ( $data[ $key ] as $index => $import_single_data ) {
							$data[ $key ][ $index ] = self::edit_import_data( $import_single_data );
						}
					}

					update_option( $setting_name, $data[ $key ], false );
				}
			}
		} elseif ( isset( $data["mode"] ) AND in_array( $data["mode"], $allowed_options ) ) { // OR import single ?
			$setting_name = self::get_settings_name_for_mode( $data["mode"] );
			if ( $setting_name == self::settings_name_now ) {
				update_option( $setting_name, self::edit_import_data( $data ), false ); // rewrite
			} else { // append!
				$items = get_option( $setting_name, array() );

				if ( empty( $items ) ) {
					$items[1] = self::edit_import_data( $data );
				} else {
					$items[] = self::edit_import_data( $data );
				}

				update_option( $setting_name, $items, false );
			}
		}// if modes
	}

	private static function edit_import_data( $data ) {

		$mode = $data['mode'];
		if ( $mode != self::EXPORT_SCHEDULE ) {
			unset( $data['export_rule'] );
			unset( $data['schedule'] );
		}

		return $data;
	}

	//backup only existing settings
	private static function backup_settings_before_version_2( $mode ) {
		$name             = self::get_settings_name_for_mode( $mode );
		$current_settings = get_option( $name, array() );
		$new_settings     = get_option( $name . "-V1", array() );
		// backup only once!
		if ( ! empty( $current_settings ) AND empty( $new_settings ) ) {
			update_option( $name . "-V1", $current_settings, false );
		}
	}

	protected static function convert_settings_to_version_2( $mode, $settings ) {

		if ( ! $settings ) {
			return $settings;
		}
		self::backup_settings_before_version_2( $mode );

		$is_flat_format = WC_Order_Export_Engine::is_plain_format( $settings['format'] );
		$is_json_format = $settings['format'] === 'JSON';

		$order_fields               = array();
		$order_coupon_fields        = array();
		$order_product_fields       = array();
		$duplicated_fields_settings = array();

		if ( ! empty( $settings['order_fields']['products']['checked'] ) ) {
			if ( isset( $settings['order_product_fields'] ) ) {

				foreach ( $settings['order_product_fields'] as $key => $values ) {

					if ( ! $values['checked'] ) {
						continue;
					}

					$field = array(
						'key'     => $key,
						'label'   => $values['label'],
						'colname' => $values['colname'],
						'format'  => isset( $values['format'] ) ? $values['format'] : 'string',
					);

					if ( $is_flat_format ) {
						$field['key']     = 'plain_products_' . $key;
						$field['segment'] = 'products';
					}

					// start FOR STATIC FIELDS
					if ( isset( $values['value'] ) ) {
						$field['value'] = $values['value'];
					}
					if ( preg_match( '/^custom_field_(\d+)/', $key, $matches ) ) {
						$field['key'] = "static_field_" . $matches[1];
					}
					// end FOR STATIC FIELDS


					$order_product_fields[] = $field;
				}
			}
		} else {
			$order_fields[] = array(
				'key'     => 'products',
				'label'   => __( 'Products', 'woo-order-export-lite' ),
				'colname' => $is_json_format ? 'products' : 'Products',
				'segment' => 'product',
				'format'  => 'string',
			);
		}


		if ( ! empty( $settings['order_fields']['coupons']['checked'] ) ) {
			if ( isset( $settings['order_coupon_fields'] ) ) {
				foreach ( $settings['order_coupon_fields'] as $key => $values ) {

					if ( ! $values['checked'] ) {
						continue;
					}

					$field = array(
						'key'     => $key,
						'label'   => $values['label'],
						'colname' => $values['colname'],
						'format'  => isset( $values['format'] ) ? $values['format'] : 'string',
					);

					if ( $is_flat_format ) {
						$field['key']     = 'plain_coupons_' . $key;
						$field['segment'] = 'coupons';
					}

					// start FOR STATIC FIELDS
					if ( isset( $values['value'] ) ) {
						$field['value'] = $values['value'];
					}
					if ( preg_match( '/^custom_field_(\d+)/', $key, $matches ) ) {
						$field['key'] = "static_field_" . $matches[1];
					}
					// end FOR STATIC FIELDS

					$order_coupon_fields[] = $field;
				}
			}
		} else {
			$order_fields[] = array(
				'key'     => 'coupons',
				'label'   => __( 'Coupons', 'woo-order-export-lite' ),
				'colname' => $is_json_format ? 'coupons' : 'Coupons',
				'segment' => 'coupon',
				'format'  => 'string',
			);
		}


		if ( isset( $settings['format'] ) ) {
			$old_populate_option_values = array(
				isset( $settings['format_xls_populate_other_columns_product_rows'] ) && $settings['format_xls_populate_other_columns_product_rows'] == '1' && $settings['format'] == 'XLS',
				isset( $settings['format_csv_populate_other_columns_product_rows'] ) && $settings['format_csv_populate_other_columns_product_rows'] == '1' && $settings['format'] == 'CSV',
				isset( $settings['format_tsv_populate_other_columns_product_rows'] ) && $settings['format_tsv_populate_other_columns_product_rows'] == '1' && $settings['format'] == 'TSV',
			);

			$old_populate_option_values = array_filter( $old_populate_option_values );

			$populate = ! empty( $old_populate_option_values );
		} else {
			// by default
			$populate = true;
		}

		if ( isset( $settings['order_fields'] ) ) {
			foreach ( $settings['order_fields'] as $key => $values ) {

				if ( ! $values['checked'] ) {
					continue;
				}
				if ( ! isset( $values['segment'] ) ) {
					$values['segment'] = 'common';
				}
				$order_field = array(
					'key'     => $key,
					'label'   => $values['label'],
					'colname' => $values['colname'],
					'segment' => $values['segment'],
					'format'  => isset( $values['format'] ) ? $values['format'] : 'string',
				);

				// start FOR STATIC FIELDS
				if ( isset( $values['value'] ) ) {
					$order_field['value'] = $values['value'];
				}
				if ( preg_match( '/^custom_field_(\d+)/', $key, $matches ) ) {
					$order_field['key'] = "static_field_" . $matches[1];
				}
				// end FOR STATIC FIELDS

				$order_fields[] = $order_field;


				if ( $key === 'products' && $is_flat_format ) {
					$order_fields = array_merge( $order_fields, $order_product_fields );
				}

				if ( $key === 'coupons' && $is_flat_format ) {
					$order_fields = array_merge( $order_fields, $order_coupon_fields );
				}

				if ( in_array( $key, array( 'products', 'coupons' ) ) ) {
					$duplicated_fields_settings[ $key ] = array(
						'repeat'         => isset( $values['repeat'] ) ? $values['repeat'] : 'rows',
						'max_cols'       => isset( $values['max_cols'] ) ? $values['max_cols'] : '10',
						'line_delimiter' => '\\n',
					);
					if ( $key == 'products' ) {
						$duplicated_fields_settings[ $key ]['populate_other_columns'] = $populate ? '1' : '0';
					}
				}
			}
		}

		if ( $duplicated_fields_settings ) {
			$settings['duplicated_fields_settings'] = $duplicated_fields_settings;
		}

		$defaults = self::get_defaults( $settings['mode'] );
		if ( ! isset( $duplicated_fields_settings['products'] ) ) {
			$settings['duplicated_fields_settings']['products'] = $defaults['duplicated_fields_settings']['products'];
		}
		if ( ! isset( $duplicated_fields_settings['coupons'] ) ) {
			$settings['duplicated_fields_settings']['coupons'] = $defaults['duplicated_fields_settings']['coupons'];
		}

		$settings['order_fields'] = $order_fields;

		$settings['order_product_fields'] = $is_flat_format ? array() : $order_product_fields;
		$settings['order_coupon_fields']  = $is_flat_format ? array() : $order_coupon_fields;

		unset( $duplicated_fields_settings, $order_coupon_fields, $order_product_fields, $order_fields );
		return $settings;
	}


}
