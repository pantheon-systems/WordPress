<?php


trait WOE_Order_Export_Plain_Format {


	private function multiplicate_fields( &$row, $type, $field_values, $fields ) {
		$multiplied_fields = array();

		$group_by_item = $this->duplicate_settings[ $type ]['group_by'];
//		$multiply_count = $repeat ? $this->duplicate_settings[$type]['max_cols'] : 1;
		$multiply_count = $this->duplicate_settings[ $type ]['max_cols'];

		if ( $group_by_item == 'product' ) {
			$index = 1;

			foreach ( $field_values as $item ) {
				if ( $multiply_count < $index ) {
					break;
				}
				foreach ( $fields as $original_key ) {
					$label_data = $this->labels['order']->$original_key;
					$key        = $label_data['parent_key'] ? $label_data['parent_key'] : $original_key;

					if ( isset( $item[ str_replace( 'plain_' . $type . '_', '', $key ) ] ) ) {
						$multiplied_fields[ $original_key . '_' . $index ] = $item[ str_replace( 'plain_' . $type . '_',
							'',
							$key ) ];
					} else {
						$multiplied_fields[ $original_key . '_' . $index ] = '';
					}

				}

				$index ++;
			}
			for ( $i = $index; $i <= $multiply_count; $i ++ ) {
				foreach ( $fields as $original_key ) {
					$multiplied_fields[ $original_key . '_' . $i ] = '';
				}
			}

		} elseif ( $group_by_item == 'as_independent_columns' ) {
			foreach ( $fields as $original_key ) {

				$label_data = $this->labels['order']->$original_key;
				$key        = $label_data['parent_key'] ? $label_data['parent_key'] : $original_key;

				$index = 1;
				foreach ( $field_values as $item ) {
					if ( $multiply_count < $index ) {
						break;
					}

					if ( isset( $item[ str_replace( 'plain_' . $type . '_', '', $key ) ] ) ) {
						$multiplied_fields[ $original_key . '_' . $index ] = $item[ str_replace( 'plain_' . $type . '_',
							'',
							$key ) ];
					} else {
						$multiplied_fields[ $original_key . '_' . $index ] = '';
					}
					$index ++;
				}
				for ( $i = $index; $i <= $multiply_count; $i ++ ) {
					$multiplied_fields[ $original_key . '_' . $i ] = '';
				}
			}
		}

		$row = array_merge( $row, $multiplied_fields );
	}


	/**
	 * @param $row
	 * @param $repeat_as_cols
	 *
	 * @return array
	 */
	protected function add_nested_rows_as_columns( $row, $repeat_as_cols ) {
		$new_row = array();

		foreach ( $repeat_as_cols as $type ) {
			$nested_rows[ $type ] = self::get_array_from_array( $row, $type );
			$field_group[ $type ] = array();
		}
		$mask                  = "#^plain_(products|coupons)_#";
		$current_field_segment = "";

		foreach ( $this->labels['order']->get_labels() as $label_data ) {
			$original_key = $label_data['key'];
			$key          = $label_data['parent_key'] ? $label_data['parent_key'] : $original_key;
			if ( ! isset( $row[ $key ] ) ) {
				continue;
			}

			$previous_field_segment = $current_field_segment;

			if ( preg_match( $mask, $key, $m ) AND in_array( $m[1], $repeat_as_cols ) ) {
				$type                   = $m[1];
				$field_group[ $type ][] = $original_key;
				$current_field_segment  = $type;
			} else {
				$current_field_segment = 'order';
			}

			if ( $previous_field_segment != $current_field_segment AND in_array( $previous_field_segment,
					$repeat_as_cols ) ) {
				$type = $previous_field_segment;
				if ( $field_group[ $type ] ) {
					$this->multiplicate_fields( $new_row, $type, $nested_rows[ $type ], $field_group[ $type ] );
					$field_group[ $type ] = array();
				}
			}

			//just copy as is 
			if ( $current_field_segment == 'order' ) {
				$new_row[ $original_key ] = $row[ $key ];
			}
		}

		//final clean up 
		foreach ( $repeat_as_cols as $type ) {
			if ( ! empty( $field_group[ $type ] ) ) {
				$this->multiplicate_fields( $new_row, $type, $nested_rows[ $type ], $field_group[ $type ] );
			}
			unset( $new_row[ $type ] );
		}

		return $new_row;
	}

	protected function try_multi_rows( $row ) {
		$repeat_products = $this->duplicate_settings['products']['repeat'] == 'rows';
		$repeat_coupons  = $this->duplicate_settings['coupons']['repeat'] == 'rows';

		$populate_non_products = isset( $this->duplicate_settings['products']['populate_other_columns'] ) && $this->duplicate_settings['products']['populate_other_columns'] == '1';

		$item_rows_start_from_new_line = ( $this->format == 'csv' && $this->settings['global_job_settings']['format_csv_item_rows_start_from_new_line'] );

		$combinations = array();

		$p_combinations = array();
		$p_field_ids    = array();
		if ( $repeat_products ) {
			foreach ( self::get_array_from_array( $row, 'products' ) as $products_fields_item ) {
				$result_tmp = array();
				foreach ( $products_fields_item as $field_name => $products_field_value ) {
					if ( isset( $this->labels['products']->$field_name ) ) // label must be assigned!
					{
						$result_tmp[ 'plain_products_' . $field_name ] = $products_field_value;
						$p_field_ids[]                                 = 'plain_products_' . $field_name;
					}
				}

				$p_combinations[] = $result_tmp;
			}
		}

		$c_combinations = array();
		$c_field_ids    = array();
		if ( $repeat_coupons ) {
			foreach ( self::get_array_from_array( $row, 'coupons' ) as $coupons_fields_item ) {
				$result_tmp = array();
				foreach ( $coupons_fields_item as $field_name => $coupons_field_value ) {
					if ( isset( $this->labels['coupons']->$field_name ) ) // label must be assigned!
					{
						$result_tmp[ 'plain_coupons_' . $field_name ] = $coupons_field_value;
						$c_fields_id[]                                = 'plain_coupons_' . $field_name;
					}
				}

				$c_combinations[] = $result_tmp;
			}
		}

		if ( empty( $p_combinations ) ) {
			$combinations = $c_combinations;
		} elseif ( empty( $c_combinations ) ) {
			$combinations = $p_combinations;
		} else {
			// $item_rows_start_from_new_line has higher priority
			if ( $item_rows_start_from_new_line ) {
				foreach ( $p_combinations as $p_combination ) {
					$combinations[] = $p_combination;
				}
				foreach ( $c_combinations as $c_combination ) {
					$combinations[] = $c_combination;
				}
			} else {
				$max_len = max( count( $c_combinations ), count( $p_combinations ) );
				for ( $i = 0; $i < $max_len; $i ++ ) {
					// output product and coupon fields one to one if its exits
					// row 1 -> product 1 - coupon 1 (+ order fields)
					// row 2 -> product 2 - coupon 2
					// row 3 -> product 3 - coupon 3
					// and so on
					$p_combination  = isset( $p_combinations[ $i ] ) ? $p_combinations[ $i ] : array();
					$c_combination  = isset( $c_combinations[ $i ] ) ? $c_combinations[ $i ] : array();
					$combinations[] = array_merge( $p_combination, $c_combination );
				}
			}
		}
		// add blank combination because we need only order fields in first row for this option
		if ( $item_rows_start_from_new_line ) {
			array_unshift( $combinations, array() );
		}

		if ( empty( $combinations ) ) {
			return array( $row );
		}

		// exclude product and coupon fields from first row when item rows start from new line
		// to prevent empty extra delimiters in output csv
		if ( $item_rows_start_from_new_line ) {
			foreach ( $p_field_ids as $key ) {
				unset( $row[ $key ] );
			}

			foreach ( $c_field_ids as $key ) {
				unset( $row[ $key ] );
			}
		}

		$new_rows = array();
		foreach ( $combinations as $num_index => $combination ) {
			if ( $item_rows_start_from_new_line ) {
				$new_row = ( $num_index == 0 ) ? $row : array();
			} elseif ( $num_index == 0 OR $populate_non_products ) {
				$new_row = $row; // 1st -- as is 
			} else { //must adjust positions for 2nd , 3rd,... rows
				foreach ( $row as $k => $v ) {
					$new_row[ $k ] = "";
				}
			}

			foreach ( $combination as $field_name => $field_value ) {
				$new_row[ $field_name ] = $field_value;
				foreach ( $this->labels['order']->get_childs( $field_name ) as $label_order_data_1 ) {
					$new_row[ $label_order_data_1['key'] ] = $field_value;
				}
			}
			$new_rows[] = $new_row;
		}

		return $new_rows;
	}

	private function merge_nested_rows_to_one_record( $row, $type ) {
		$line_delimiter = $this->convert_literals( $this->duplicate_settings[ $type ]['line_delimiter'] );

		$merged_values = array();
		foreach ( $row[ $type ] as $line ) {
			foreach ( $line as $k => $v ) {
				$plain_key = "plain_{$type}_{$k}";
				if ( ! isset( $merged_values[ $plain_key ] ) ) {
					$merged_values[ $plain_key ] = true;
					$row[ $plain_key ]           = $v;
				} else {
					$row[ $plain_key ] .= $line_delimiter . $v;
				}
			}
		}
		unset( $row[ $type ] );

		return $row;
	}
}