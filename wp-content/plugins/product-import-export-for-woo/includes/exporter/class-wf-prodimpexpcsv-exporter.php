<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WF_ProdImpExpCsv_Exporter {

	/**
	 * Product Exporter Tool
	 */
	public static function do_export( $post_type = 'product' ) {
		global $wpdb;
		$export_limit                = ! empty( $_POST['limit'] ) ? intval( $_POST['limit'] ) : 999999999;
		$export_count                = 0;
		$limit                       = 100;
		$current_offset              = ! empty( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
		$csv_columns                 = include( 'data/data-wf-post-columns.php' );
                $user_columns_name           = ! empty( $_POST['columns_name'] ) ? $_POST['columns_name'] : $csv_columns;
		$product_taxonomies          = get_object_taxonomies( 'product', 'name' ); 
		$export_columns              = ! empty( $_POST['columns'] ) ? $_POST['columns'] : '';
		$include_hidden_meta         = ! empty( $_POST['include_hidden_meta'] ) ? true : false;
		$product_limit               = ! empty( $_POST['product_limit'] ) ? sanitize_text_field( $_POST['product_limit'] ) : '';
		$exclude_hidden_meta_columns = include( 'data/data-wf-hidden-meta-columns.php' );

		if ( $limit > $export_limit )
			$limit = $export_limit;
		
		$wpdb->hide_errors();
		@set_time_limit(0);
		if ( function_exists( 'apache_setenv' ) )
			@apache_setenv( 'no-gzip', 1 );
		@ini_set('zlib.output_compression', 0);

		//@ob_clean();
        @ob_end_clean(); // to prevent issue that unidentified characters when opened in MS-Excel in some servers

				
			header( 'Content-Type: text/csv; charset=UTF-8' );
			header( 'Content-Disposition: attachment; filename=woocommerce-product-export.csv' );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
			
			$fp = fopen('php://output', 'w');
		

   		// Headers
		$all_meta_keys    = self::get_all_metakeys( 'product' );
		$found_attributes = self::get_all_product_attributes( 'product' );
                
		// Loop products and load meta data
		$found_product_meta = array();
		// Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.
	foreach ( $all_meta_keys as $meta ) {
            if ( ! $meta ) continue;
            if ( ! $include_hidden_meta && ! in_array( $meta, array_keys( $csv_columns ) ) && substr( (string)$meta, 0, 1 ) == '_' )
            	continue;
            if ( $include_hidden_meta && ( in_array( $meta, $exclude_hidden_meta_columns ) || in_array( $meta, array_keys( $csv_columns ) ) ) )
				continue;
            $found_product_meta[] = $meta;
        }

		$found_product_meta = array_diff( $found_product_meta, array_keys( $csv_columns ) );

		// Variable to hold the CSV data we're exporting
		$row = array();

		// Export header rows
		foreach ( $csv_columns as $column => $value ) {
                    
                        $temp_head =    esc_attr( $user_columns_name[$column] );
                        if (strpos($temp_head, 'yoast') === false) {
                            $temp_head = ltrim($temp_head, '_');
                        }
			if ( ! $export_columns || in_array( $column, $export_columns ) ) $row[] = $temp_head;
		}

		// Handle special fields like taxonomies
		if ( ! $export_columns || in_array( 'images', $export_columns ) ) {
			$row[] = 'images';
		}

		if ( ! $export_columns || in_array( 'file_paths', $export_columns ) ) {
			if ( function_exists( 'wc_get_filename_from_url' ) ) {
				$row[] = 'downloadable_files';
			} else {
				$row[] = 'file_paths';
			}
		}

		if ( ! $export_columns || in_array( 'taxonomies', $export_columns ) ) {
			foreach ( $product_taxonomies as $taxonomy ) {
				if ( strstr( $taxonomy->name, 'pa_' ) ) continue; // Skip attributes

				$row[] = 'tax:' . self::format_data( $taxonomy->name );
			}
		}

		if ( ! $export_columns || in_array( 'meta', $export_columns ) ) {
			foreach ( $found_product_meta as $product_meta ) {
				$row[] = 'meta:' . self::format_data( $product_meta );
			}
		}

		if ( ! $export_columns || in_array( 'attributes', $export_columns ) ) {
			foreach ( $found_attributes as $attribute ) {
				$row[] = 'attribute:' . self::format_data( $attribute );
				$row[] = 'attribute_data:' . self::format_data( $attribute );
				$row[] = 'attribute_default:' . self::format_data( $attribute );
			}
		}

		
		
		// WF: Adding product permalink.
		if ( ! $export_columns || in_array( 'product_page_url', $export_columns ) ) {
			$row[] = 'Product Page URL';
		}

		$row = array_map( 'WF_ProdImpExpCsv_Exporter::wrap_column', $row );
		fwrite( $fp, implode( ',', $row ) . "\n" );
		unset( $row );

		while ( $export_count < $export_limit ) {

			$product_args = apply_filters( 'woocommerce_csv_product_export_args', array(
				'numberposts' 	=> $limit,
				'post_status' 	=> array( 'publish', 'pending', 'private', 'draft' ),
				'post_type'		=> array('product'),
				'orderby' 		=> 'ID',
                                'suppress_filters'      => false,
				'order'			=> 'ASC',
				'offset'		=> $current_offset
			) );


				if ( $product_limit ) {
					$parent_ids               = array_map( 'intval', explode( ',', $product_limit ) );
					$child_ids                = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_parent IN (" . implode( ',', $parent_ids ) . ");" );
					$product_args['post__in'] = $child_ids;
				}
			
			$products = get_posts( $product_args );
			if ( ! $products || is_wp_error( $products ) )
				break;

			// Loop products
			foreach ( $products as $product ) {
                                if($product->post_parent == 0) $product->post_parent = '';
				$row = array();

				// Pre-process data
				$meta_data = get_post_custom( $product->ID );

				$product->meta = new stdClass;
				$product->attributes = new stdClass;

				// Meta data
				foreach ( $meta_data as $meta => $value ) {
					if ( ! $meta ) {
						continue;
					}
					if ( ! $include_hidden_meta && ! in_array( $meta, array_keys( $csv_columns ) ) && substr( $meta, 0, 1 ) == '_' ) {
						continue;
					}
					if ( $include_hidden_meta && in_array( $meta, $exclude_hidden_meta_columns ) ) {
						continue;
					}

					$meta_value = maybe_unserialize( maybe_unserialize( $value[0] ) );

					if ( is_array( $meta_value ) ) {
						$meta_value = json_encode( $meta_value );
					}

					$product->meta->$meta = self::format_export_meta( $meta_value, $meta );
				}

				// Product attributes
				if ( isset( $meta_data['_product_attributes'][0] ) ) {

					$attributes = maybe_unserialize( maybe_unserialize( $meta_data['_product_attributes'][0] ) );

					if ( ! empty( $attributes ) && is_array( $attributes ) ) {
						foreach ( $attributes as $key => $attribute ) {
							if ( ! $key ) {
								continue;
							}

							if ( $attribute['is_taxonomy'] == 1 ) {
								$terms = wp_get_post_terms( $product->ID, $key, array("fields" => "names") );
								if ( ! is_wp_error( $terms ) ) {
									$attribute_value = implode( '|', $terms );
								} else {
									$attribute_value = '';
								}
							} else {
								if ( empty( $attribute['name'] ) ) {
	                   	 			continue;
	                   	 		}
								$key             = $attribute['name'];
								$attribute_value = $attribute['value'];
							}

							if ( ! isset( $attribute['position'] ) ) {
								$attribute['position'] = 0;
							}
							if ( ! isset( $attribute['is_visible'] ) ) {
								$attribute['is_visible'] = 0;
							}
							if ( ! isset( $attribute['is_variation'] ) ) {
								$attribute['is_variation'] = 0;
							}

							$attribute_data      = $attribute['position'] . '|' . $attribute['is_visible'] . '|' . $attribute['is_variation'];
							$_default_attributes = isset( $meta_data['_default_attributes'][0]  ) ? maybe_unserialize( maybe_unserialize( $meta_data['_default_attributes'][0] ) ) : '';

							if ( is_array( $_default_attributes ) ) {
								$_default_attribute = isset( $_default_attributes[ $key ] ) ? $_default_attributes[ $key ] : '';
							} else {
								$_default_attribute = '';
							}

							$product->attributes->$key = array(
								'value'		=> $attribute_value,
								'data'		=> $attribute_data,
								'default'	=> $_default_attribute
							);
						}
					}
				}

				// GPF
				if ( isset( $meta_data['_woocommerce_gpf_data'][0] ) ) {
					$product->gpf_data = $meta_data['_woocommerce_gpf_data'][0];
				}

				// Get column values
				foreach ( $csv_columns as $column => $value ) {
					if ( ! $export_columns || in_array( $column, $export_columns ) ) {

						if ($column == '_regular_price' && empty( $product->meta->$column ) ) {
							$column = '_price';
						}

						if ( isset( $product->meta->$column ) ) {
							$row[] = self::format_data( $product->meta->$column );
						} elseif ( isset( $product->$column ) && ! is_array( $product->$column ) ) {
							if ( $column === 'post_title' ) {
								$row[] = sanitize_text_field( $product->$column );
							} else {
								$row[] = self::format_data( $product->$column );
							}
						} else {
							$row[] = '';
						}
					}
				}

				// Export images/gallery
				if ( ! $export_columns || in_array( 'images', $export_columns ) ) {

					$image_file_names = array();

					// Featured image
					if ( ( $featured_image_id = get_post_thumbnail_id( $product->ID ) ) && ( $image = wp_get_attachment_image_src( $featured_image_id, 'full' ) ) ) {
						$image_file_names[] = current( $image );
					}

					// Images
					$images  = isset( $meta_data['_product_image_gallery'][0] ) ? explode( ',', maybe_unserialize( maybe_unserialize( $meta_data['_product_image_gallery'][0] ) ) ) : false;
					$results = array();

					if ( $images ) {
						foreach ( $images as $image_id ) {
							if ( $featured_image_id == $image_id ) {
								continue;
							}
							$image = wp_get_attachment_image_src( $image_id, 'full' );
							if ( $image ) {
								$image_file_names[] = current( $image );
							}
						}
					}

					$row[] = implode( ' | ', $image_file_names );

				}

				// Downloadable files
				if ( ! $export_columns || in_array( 'file_paths', $export_columns ) ) {
					if ( ! function_exists( 'wc_get_filename_from_url' ) ) {
						$file_paths           = maybe_unserialize( maybe_unserialize( $meta_data['_file_paths'][0] ) );
						$file_paths_to_export = array();

						if ( $file_paths ) {
							foreach ( $file_paths as $file_path ) {
								$file_paths_to_export[] = $file_path;
							}
						}

						$file_paths_to_export = implode( ' | ', $file_paths_to_export );
						$row[]                = self::format_data( $file_paths_to_export );
					} elseif ( isset( $meta_data['_downloadable_files'][0] ) ) {
						$file_paths           = maybe_unserialize( maybe_unserialize( $meta_data['_downloadable_files'][0] ) );
						$file_paths_to_export = array();

						if ( $file_paths ) {
							foreach ( $file_paths as $file_path ) {
								$file_paths_to_export[] = ( ! empty( $file_path['name'] ) ? $file_path['name'] : wc_get_filename_from_url( $file_path['file'] ) ) . '::' . $file_path['file'];
							}
						}
						$file_paths_to_export = implode( ' | ', $file_paths_to_export );
						$row[]                = self::format_data( $file_paths_to_export );
					} else {
						$row[]                = '';
					}
				}

				// Export taxonomies
				if ( ! $export_columns || in_array( 'taxonomies', $export_columns ) ) {
					foreach ( $product_taxonomies as $taxonomy ) {
						if ( strstr( $taxonomy->name, 'pa_' ) ) continue; // Skip attributes

						if ( is_taxonomy_hierarchical( $taxonomy->name ) ) {
							$terms           = wp_get_post_terms( $product->ID, $taxonomy->name, array( "fields" => "all" ) );
							$formatted_terms = array();

							foreach ( $terms as $term ) {
								$ancestors      = array_reverse( get_ancestors( $term->term_id, $taxonomy->name ) );
								$formatted_term = array();

								foreach ( $ancestors as $ancestor )
									$formatted_term[] = get_term( $ancestor, $taxonomy->name )->name;

								$formatted_term[]  = $term->name;

								$formatted_terms[] = implode( ' > ', $formatted_term );
							}

							$row[] = self::format_data( implode( '|', $formatted_terms ) );
						} else {
							$terms = wp_get_post_terms( $product->ID, $taxonomy->name, array( "fields" => "names" ) );

							$row[] = self::format_data( implode( '|', $terms ) );
						}
					}
				}

				// Export meta data
				if ( ! $export_columns || in_array( 'meta', $export_columns ) ) {
					foreach ( $found_product_meta as $product_meta ) {
						if ( isset( $product->meta->$product_meta ) ) {
							$row[] = self::format_data( $product->meta->$product_meta );
						} else {
							$row[] = '';
						}
					}
				}

				// Find and export attributes
				if ( ! $export_columns || in_array( 'attributes', $export_columns ) ) {
					foreach ( $found_attributes as $attribute ) {
						if ( isset( $product->attributes ) && isset( $product->attributes->$attribute ) ) {
							$values = $product->attributes->$attribute;
							$row[] = self::format_data( $values['value'] );
							$row[] = self::format_data( $values['data'] );
							$row[] = self::format_data( $values['default'] );
						} else {
							$row[] = '';
							$row[] = '';
							$row[] = '';
						}
					}
				}

				// Export GPF
				if ( function_exists( 'woocommerce_gpf_install' ) && ( ! $export_columns || in_array( 'gpf', $export_columns ) ) ) {

					$gpf_data = empty( $product->gpf_data ) ? '' : maybe_unserialize( $product->gpf_data );

					$row[] = empty( $gpf_data['availability'] ) ? '' : $gpf_data['availability'];
					$row[] = empty( $gpf_data['condition'] ) ? '' : $gpf_data['condition'];
					$row[] = empty( $gpf_data['brand'] ) ? '' : $gpf_data['brand'];
					$row[] = empty( $gpf_data['product_type'] ) ? '' : $gpf_data['product_type'];
					$row[] = empty( $gpf_data['google_product_category'] ) ? '' : $gpf_data['google_product_category'];
					$row[] = empty( $gpf_data['gtin'] ) ? '' : $gpf_data['gtin'];
					$row[] = empty( $gpf_data['mpn'] ) ? '' : $gpf_data['mpn'];
					$row[] = empty( $gpf_data['gender'] ) ? '' : $gpf_data['gender'];
					$row[] = empty( $gpf_data['age_group'] ) ? '' : $gpf_data['age_group'];
					$row[] = empty( $gpf_data['color'] ) ? '' : $gpf_data['color'];
					$row[] = empty( $gpf_data['size'] ) ? '' : $gpf_data['size'];
					$row[] = empty( $gpf_data['adwords_grouping'] ) ? '' : $gpf_data['adwords_grouping'];
					$row[] = empty( $gpf_data['adwords_labels'] ) ? '' : $gpf_data['adwords_labels'];
				}
				
				// WF: Adding product permalink.
				if ( ! $export_columns || in_array( 'product_page_url', $export_columns ) ) {
					$product_page_url = '';
					if ( $product->ID ) {
						$product_page_url = get_permalink( $product->ID );
					}
					if ( $product->post_parent ) {
						$product_page_url = get_permalink( $product->post_parent );
					}
					
					$row[] = $product_page_url;
				}

				// Add to csv
				$row = array_map( 'WF_ProdImpExpCsv_Exporter::wrap_column', $row );
				fwrite( $fp, implode( ',', $row ) . "\n" );
				unset( $row );
				
			}
			$current_offset += $limit;
			$export_count   += $limit;
			unset( $products );
		}
		
		fclose( $fp );
		exit;
	}

	/**
	 * Format the data if required
	 * @param  string $meta_value
	 * @param  string $meta name of meta key
	 * @return string
	 */
	public static function format_export_meta( $meta_value, $meta ) {
		switch ( $meta ) {
			case '_sale_price_dates_from' :
			case '_sale_price_dates_to' :
				return $meta_value ? date( 'Y-m-d', $meta_value ) : '';
			break;
			case '_upsell_ids' :
			case '_crosssell_ids' :
				return implode( '|', array_filter( (array) json_decode( $meta_value ) ) );
			break;
			default :
				return $meta_value;
			break;
		}
	}

	public static function format_data( $data ) {
		$enc  = mb_detect_encoding( $data, 'UTF-8, ISO-8859-1', true );
		$data = ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
		return $data;
	}

	/**
	 * Wrap a column in quotes for the CSV
	 * @param  string data to wrap
	 * @return string wrapped data
	 */
	public static function wrap_column( $data ) {
		return '"' . str_replace( '"', '""', $data ) . '"';
	}

	/**
     * Get a list of all the meta keys for a post type. This includes all public, private,
     * used, no-longer used etc. They will be sorted once fetched.
     */
    public static function get_all_metakeys( $post_type = 'product' ) {
        global $wpdb;

        $meta = $wpdb->get_col( $wpdb->prepare(
            "SELECT DISTINCT pm.meta_key
            FROM {$wpdb->postmeta} AS pm
            LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
            WHERE p.post_type = %s
            AND p.post_status IN ( 'publish', 'pending', 'private', 'draft' )",
            $post_type
        ) );

        sort( $meta );

        return $meta;
    }

    /**
     * Get a list of all the product attributes for a post type.
     * These require a bit more digging into the values.
     */
    public static function get_all_product_attributes( $post_type = 'product' ) {
        global $wpdb;

        $results = $wpdb->get_col( $wpdb->prepare(
            "SELECT DISTINCT pm.meta_value
            FROM {$wpdb->postmeta} AS pm
            LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
            WHERE p.post_type = %s
            AND p.post_status IN ( 'publish', 'pending', 'private', 'draft' )
            AND pm.meta_key = '_product_attributes'",
            $post_type
        ) );

        // Go through each result, and look at the attribute keys within them.
        $result = array();

        if ( ! empty( $results ) ) {
            foreach( $results as $_product_attributes ) {
                $attributes = maybe_unserialize( maybe_unserialize( $_product_attributes ) );
                if ( ! empty( $attributes ) && is_array( $attributes ) ) {
                	foreach( $attributes as $key => $attribute ) {
                   		if ( ! $key ) {
                   	 		continue;
                   		}
                   	 	if ( ! strstr( $key, 'pa_' ) ) {
                   	 		if ( empty( $attribute['name'] ) ) {
                   	 			continue;
                   	 		}
                   	 		$key = $attribute['name'];
                   	 	}

                   	 	$result[ $key ] = $key;
                   	 }
                }
            }
        }

        sort( $result );

        return $result;
    }
}
