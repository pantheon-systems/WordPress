<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WordPress Importer class for managing the import process of a CSV file
 *
 * @package WordPress
 * @subpackage Importer
 */
if ( ! class_exists( 'WP_Importer' ) )
	return;

class WF_ProdImpExpCsv_Product_Import extends WP_Importer {

	var $id;
	var $file_url;
	var $delimiter;
        var $merge;
	var $merge_empty_cells;

	// mappings from old information to new
	var $processed_terms = array();
	var $processed_posts = array();
	var $post_orphans    = array();
	var $attachments     = array();
	var $upsell_skus     = array();
	var $crosssell_skus  = array();

	// Results
	var $import_results  = array();

	/**
	 * Constructor
	 */
	public function __construct() {

		if(WC()->version < '2.7.0'){
			$this->log                     = new WC_Logger();

		}else
		{
			$this->log                     = wc_get_logger();

		}
		
		$this->import_page             = 'xa_woocommerce_csv';
		$this->file_url_import_enabled = apply_filters( 'woocommerce_csv_product_file_url_import_enabled', true );
	}

	/**
	 * Registered callback function for the WordPress Importer
	 *
	 * Manages the three separate stages of the CSV import process
	 */
	public function dispatch() {
		global $woocommerce, $wpdb;

		if ( ! empty( $_POST['delimiter'] ) ) {
			$this->delimiter = stripslashes( trim( $_POST['delimiter'] ) );
		}else if ( ! empty( $_GET['delimiter'] ) ) {
			$this->delimiter = stripslashes( trim( $_GET['delimiter'] ) );
		}

		if ( ! $this->delimiter )
			$this->delimiter = ',';
                
                if ( ! empty( $_POST['merge'] ) || ! empty( $_GET['merge'] ) ) {
			$this->merge = 1;
		} else{
			$this->merge = 0;
		}
                
		if ( ! empty( $_POST['merge_empty_cells'] ) || ! empty( $_GET['merge_empty_cells'] ) ) {
			$this->merge_empty_cells = 1;
		} else{
			$this->merge_empty_cells = 0;
		}

		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

		switch ( $step ) {
			case 0 :
				$this->header();
				$this->greet();
			break;
			case 1 :
				$this->header();

				check_admin_referer( 'import-upload' );

				if(!empty($_GET['file_url']))
					$this->file_url = esc_attr( $_GET['file_url'] );
				if(!empty($_GET['file_id']))
					$this->id = $_GET['file_id'] ;

				if ( !empty($_GET['clearmapping']) || $this->handle_upload() )
					$this->import_options();
				else
					_e( 'Error with handle_upload!', 'wf_csv_import_export' );
			break;
			case 2 :
				$this->header();

				check_admin_referer( 'import-woocommerce' );

				$this->id = (int) $_POST['import_id'];

				if ( $this->file_url_import_enabled )
					$this->file_url = esc_attr( $_POST['import_url'] );

				if ( $this->id )
					$file = get_attached_file( $this->id );
				else if ( $this->file_url_import_enabled )
					$file = ABSPATH . $this->file_url;

				$file = str_replace( "\\", "/", $file );

				if ( $file ) {
					?>
					<table id="import-progress" class="widefat_importer widefat">
						<thead>
							<tr>
								<th class="status">&nbsp;</th>
								<th class="row"><?php _e( 'Row', 'wf_csv_import_export' ); ?></th>
								<th><?php _e( 'SKU', 'wf_csv_import_export' ); ?></th>
								<th><?php _e( 'Product', 'wf_csv_import_export' ); ?></th>
								<th class="reason"><?php _e( 'Status Msg', 'wf_csv_import_export' ); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr class="importer-loading">
								<td colspan="5"></td>
							</tr>
						</tfoot>
						<tbody></tbody>
					</table>
					<script type="text/javascript">
						jQuery(document).ready(function($) {

							if ( ! window.console ) { window.console = function(){}; }

							var processed_terms = [];
							var processed_posts = [];
							var post_orphans    = [];
							var attachments     = [];
							var upsell_skus     = [];
							var crosssell_skus  = [];
							var i               = 1;
							var done_count      = 0;

							function import_rows( start_pos, end_pos ) {

								var data = {
									action: 	'woocommerce_csv_import_request',
									file:       '<?php echo addslashes( $file ); ?>',
									mapping: 	'<?php echo json_encode($_POST['map_from'],JSON_HEX_APOS); ?>',
									eval_field: '<?php echo stripslashes(json_encode(($_POST['eval_field']),JSON_HEX_APOS)) ?>',
									delimiter:  '<?php echo $this->delimiter; ?>',
									merge_empty_cells: '<?php echo $this->merge_empty_cells; ?>',
                                                                        merge:      '<?php echo $this->merge; ?>',
									start_pos:  start_pos,
									end_pos:    end_pos,
								};
                                                                data.eval_field = $.parseJSON(data.eval_field);
								return $.ajax({
									url:        '<?php echo add_query_arg( array( 'import_page' => $this->import_page, 'step' => '3', 'merge' => ! empty( $_GET['merge'] ) ? '1' : '0' ), admin_url( 'admin-ajax.php' ) ); ?>',
									data:       data,
									type:       'POST',
									success:    function( response ) {
										//console.log( response );
										if ( response ) {

											try {
												// Get the valid JSON only from the returned string
												if ( response.indexOf("<!--WC_START-->") >= 0 )
													response = response.split("<!--WC_START-->")[1]; // Strip off before after WC_START

												if ( response.indexOf("<!--WC_END-->") >= 0 )
													response = response.split("<!--WC_END-->")[0]; // Strip off anything after WC_END

												// Parse
												var results = $.parseJSON( response );

												if ( results.error ) {

													$('#import-progress tbody').append( '<tr id="row-' + i + '" class="error"><td class="status" colspan="5">' + results.error + '</td></tr>' );

													i++;

												} else if ( results.import_results && $( results.import_results ).size() > 0 ) {

													$.each( results.processed_terms, function( index, value ) {
														processed_terms.push( value );
													});

													$.each( results.processed_posts, function( index, value ) {
														processed_posts.push( value );
													});

													$.each( results.post_orphans, function( index, value ) {
														post_orphans.push( value );
													});

													$.each( results.attachments, function( index, value ) {
														attachments.push( value );
													});

													upsell_skus    = jQuery.extend( {}, upsell_skus, results.upsell_skus );
													crosssell_skus = jQuery.extend( {}, crosssell_skus, results.crosssell_skus );

													$( results.import_results ).each(function( index, row ) {
														$('#import-progress tbody').append( '<tr id="row-' + i + '" class="' + row['status'] + '"><td><mark class="result" title="' + row['status'] + '">' + row['status'] + '</mark></td><td class="row">' + i + '</td><td>' + row['sku'] + '</td><td>' + row['post_id'] + ' - ' + row['post_title'] + '</td><td class="reason">' + row['reason'] + '</td></tr>' );

														i++;
													});
												}

											} catch(err) {}

										} else {
											$('#import-progress tbody').append( '<tr class="error"><td class="status" colspan="5">' + '<?php _e( 'AJAX Error', 'wf_csv_import_export' ); ?>' + '</td></tr>' );
										}

										var w = $(window);
										var row = $( "#row-" + ( i - 1 ) );

										if ( row.length ) {
										    w.scrollTop( row.offset().top - (w.height()/2) );
										}

										done_count++;

										$('body').trigger( 'woocommerce_csv_import_request_complete' );
									},
                                                                error:  function (jqXHR, httpStatusMessage, customErrorMessage) {
                                                                            import_rows(start_pos, end_pos);
                                                                        }    
								});
							}

							var rows = [];

							<?php
							$limit = apply_filters( 'woocommerce_csv_import_limit_per_request', 10 );
							$enc   = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
							if ( $enc )
								setlocale( LC_ALL, 'en_US.' . $enc );
							@ini_set( 'auto_detect_line_endings', true );

							$count             = 0;
							$previous_position = 0;
							$position          = 0;
							$import_count      = 0;

							// Get CSV positions
							if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) {

								while ( ( $postmeta = fgetcsv( $handle, 0, $this->delimiter ) ) !== FALSE ) {
									$count++;

                                                                    if ( $count >= $limit ) {
                                                                        $previous_position = $position;
                                                                                        $position          = ftell( $handle );
                                                                                        $count             = 0;
                                                                                        $import_count      ++;

                                                                                        // Import rows between $previous_position $position
                                                                        ?>rows.push( [ <?php echo $previous_position; ?>, <?php echo $position; ?> ] ); <?php
                                                                    }
		  						}

		  						// Remainder
		  						if ( $count > 0 ) {
		  							?>rows.push( [ <?php echo $position; ?>, '' ] ); <?php
		  							$import_count      ++;
		  						}

                                                                fclose( $handle );
                                                        }
							?>

							var data = rows.shift();
							var regen_count = 0;
                                                        var failed_regen_count = 0;
							import_rows( data[0], data[1] );

							$('body').on( 'woocommerce_csv_import_request_complete', function() {
								if ( done_count == <?php echo $import_count; ?> ) {

									if ( attachments.length ) {

										$('#import-progress tbody').append( '<tr class="regenerating"><td colspan="5"><div class="progress"></div></td></tr>' );

										index = 0;

										$.each( attachments, function( i, value ) {
											regenerate_thumbnail( value );
											index ++;
											if ( index == attachments.length ) {
												import_done();
											}
										});

									} else {
										import_done();
									}

								} else {
									// Call next request
									data = rows.shift();
									import_rows( data[0], data[1] );
								}
							} );

                                                        // Regenerate a specified image via AJAX
							function regenerate_thumbnail( id ) {
								$.ajax({
									type: 'POST',
									url: ajaxurl,
									data: { action: "woocommerce_csv_import_regenerate_thumbnail", id: id },
									success: function( response ) {
                                                                                //console.log('On Success:-'+JSON.stringify(response, null, 4));
										if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {
											response = new Object;
											response.success = false;
											response.error = "<?php printf( esc_js( __( 'The resize request was abnormally terminated (ID %s). This is likely due to the image exceeding available memory or some other type of fatal error.', 'wf_csv_import_export' ) ), '" + id + "' ); ?>";
										}

										//regen_count ++;
                                                                                if (! response.error) {
                                                                                    regen_count ++;
                                                                                }
                                                                                if (! response.success) {
                                                                                    failed_regen_count++;
                                                                                }
                                                                                
                                                                                
                                                                                all_regen_count = failed_regen_count + regen_count;
                                                                                $('#import-progress tbody .regenerating .progress').css( 'width', ( ( all_regen_count / attachments.length ) * 100 ) + '%' ).html( regen_count + ' / ' + attachments.length + ' <?php echo esc_js(__('thumbnails regenerated.', 'wf_csv_import_export')); ?>' );
										//$('#import-progress tbody .regenerating .progress').css( 'width', ( ( regen_count / attachments.length ) * 100 ) + '%' ).html( regen_count + ' / ' + attachments.length + ' <?php //echo esc_js( __( 'thumbnails regenerated', 'wf_csv_import_export' ) ); ?>' );

//										if ( ! response.success ) {
//											$('#import-progress tbody').append( '<tr><td colspan="5">' + response.error + '</td></tr>' );
//										}
									},
									error: function( response ) {
                                                                                //console.log('On Error:-'+JSON.stringify(response, null, 4));
										failed_regen_count++;
                                                                                all_regen_count = failed_regen_count + regen_count;
                                                                                $('#import-progress tbody .regenerating .progress').css( 'width', ( ( all_regen_count / attachments.length ) * 100 ) + '%' ).html( regen_count + ' / ' + attachments.length + ' <?php echo esc_js(__('thumbnails regenerated.', 'wf_csv_import_export')); ?>' );
                                                                                //$('#import-progress tbody').append( '<tr><td colspan="5">' + response.error + '</td></tr>' );
									}
								});
							}
                                                        
							function import_done() {
								var data = {
									action: 'woocommerce_csv_import_request',
									file: '<?php echo $file; ?>',
									processed_terms: processed_terms,
									processed_posts: processed_posts,
									post_orphans: post_orphans,
									upsell_skus: upsell_skus,
									crosssell_skus: crosssell_skus
								};

								$.ajax({
									url: '<?php echo add_query_arg( array( 'import_page' => $this->import_page, 'step' => '4', 'merge' => ! empty( $_GET['merge'] ) ? 1 : 0 ), admin_url( 'admin-ajax.php' ) ); ?>',
									data:       data,
									type:       'POST',
									success:    function( response ) {
										console.log( response );
										$('#import-progress tbody').append( '<tr class="complete"><td colspan="5">' + response + '</td></tr>' );
										$('.importer-loading').hide();
									}
								});
							}
						});
					</script>
					<?php
				} else {
					echo '<p class="error">' . __( 'Error finding uploaded file!', 'wf_csv_import_export' ) . '</p>';
				}
			break;
			case 3 :
				// Check access - cannot use nonce here as it will expire after multiple requests
				if ( ! current_user_can( 'manage_woocommerce' ) )
					die();

				add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

				if ( function_exists( 'gc_enable' ) )
					gc_enable();

				@set_time_limit(0);
				@ob_flush();
				@flush();
				$wpdb->hide_errors();

				$file      = stripslashes( $_POST['file'] );
				$mapping   = json_decode( stripslashes( $_POST['mapping'] ), true );
				$eval_field = $_POST['eval_field'];
				$start_pos = isset( $_POST['start_pos'] ) ? absint( $_POST['start_pos'] ) : 0;
				$end_pos   = isset( $_POST['end_pos'] ) ? absint( $_POST['end_pos'] ) : '';
				
				update_option( 'wf_prod_csv_imp_exp_mapping', array($mapping,$eval_field ));	
				
				$position = $this->import_start( $file, $mapping, $start_pos, $end_pos, $eval_field );
				$this->import();
				$this->import_end();

				$results                    = array();
				$results['import_results']  = $this->import_results;
				$results['processed_terms'] = $this->processed_terms;
				$results['processed_posts'] = $this->processed_posts;
				$results['post_orphans']    = $this->post_orphans;
				$results['attachments']     = $this->attachments;
				$results['upsell_skus']     = $this->upsell_skus;
				$results['crosssell_skus']  = $this->crosssell_skus;

				echo "<!--WC_START-->";
				echo json_encode( $results );
				echo "<!--WC_END-->";
				exit;
			break;
			case 4 :
				// Check access - cannot use nonce here as it will expire after multiple requests
				if ( ! current_user_can( 'manage_woocommerce' ) )
					die();

				add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );

				if ( function_exists( 'gc_enable' ) )
					gc_enable();

				@set_time_limit(0);
				@ob_flush();
				@flush();
				$wpdb->hide_errors();

				$this->processed_terms = isset( $_POST['processed_terms'] ) ? $_POST['processed_terms'] : array();
				$this->processed_posts = isset( $_POST['processed_posts']) ? $_POST['processed_posts'] : array();
				$this->post_orphans    = isset( $_POST['post_orphans']) ? $_POST['post_orphans'] : array();
				$this->crosssell_skus  = isset( $_POST['crosssell_skus']) ? array_filter( (array) $_POST['crosssell_skus'] ) : array();
				$this->upsell_skus     = isset( $_POST['upsell_skus']) ? array_filter( (array) $_POST['upsell_skus'] ) : array();

				_e( 'Step 1...', 'wf_csv_import_export' ) . ' ';

				wp_defer_term_counting( true );
				wp_defer_comment_counting( true );

				_e( 'Step 2...', 'wf_csv_import_export' ) . ' ';

				echo 'Step 3...' . ' '; // Easter egg

				// reset transients for products
				if ( function_exists( 'wc_delete_product_transients' ) ) {
					wc_delete_product_transients();
				} else {
					$woocommerce->clear_product_transients();
				}

				delete_transient( 'wc_attribute_taxonomies' );

				$wpdb->query("DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wc_product_type_%')");

				_e( 'Finalizing...', 'wf_csv_import_export' ) . ' ';

				$this->backfill_parents();

				if ( ! empty( $this->upsell_skus ) ) {

					_e( 'Linking upsells...', 'wf_csv_import_export' ) . ' ';

					foreach ( $this->upsell_skus as $post_id => $skus ) {
						$this->link_product_skus( 'upsell', $post_id, $skus );
					}
				}

				if ( ! empty( $this->crosssell_skus ) ) {

					_e( 'Linking crosssells...', 'wf_csv_import_export' ) . ' ';

					foreach ( $this->crosssell_skus as $post_id => $skus ) {
						$this->link_product_skus( 'crosssell', $post_id, $skus );
					}
				}
				// SUCCESS
				_e( 'Finished. Import complete.', 'wf_csv_import_export' );

				$this->import_end();
				exit;
			break;
		}

		$this->footer();
	}

	/**
	 * format_data_from_csv
	 */
	public function format_data_from_csv( $data, $enc ) {
		return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
	}

	/**
	 * Display pre-import options
	 */
	public function import_options() {
		$j = 0;
		
		if ( $this->id )
			$file = get_attached_file( $this->id );
		else if ( $this->file_url_import_enabled )
			$file = ABSPATH . $this->file_url;
		else
			return;

		// Set locale
		$enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
		if ( $enc ) setlocale( LC_ALL, 'en_US.' . $enc );
		@ini_set( 'auto_detect_line_endings', true );

		// Get headers
		if ( ( $handle = fopen( $file, "r" ) ) !== FALSE ) {

			$row = $raw_headers = array();
			$header = fgetcsv( $handle, 0, $this->delimiter );

		    while ( ( $postmeta = fgetcsv( $handle, 0, $this->delimiter ) ) !== FALSE ) {
	            foreach ( $header as $key => $heading ) {
	            	if ( ! $heading ) continue;
	            	$s_heading = strtolower( $heading );
	                $row[$s_heading] = ( isset( $postmeta[$key] ) ) ? $this->format_data_from_csv( $postmeta[$key], $enc ) : '';
	                $raw_headers[ $s_heading ] = $heading;
	            }
	            break;
		    }
		    fclose( $handle );
		}
		$mapping_from_db  = get_option( 'wf_prod_csv_imp_exp_mapping');
		$saved_mapping = null;
		$saved_evaluation = null;
		if($mapping_from_db && is_array($mapping_from_db) && count($mapping_from_db) == 2 && empty($_GET['clearmapping'])){
			//if(count(array_intersect_key ( $mapping_from_db[0] , $row)) ==  count($mapping_from_db[0])){	
				$reset_action     = 'admin.php?clearmapping=1&amp;import=' . $this->import_page . '&amp;step=1&amp;merge=' . $this->merge . '&amp;file_url=' . $this->file_url . '&amp;delimiter=' . $this->delimiter . '&amp;merge_empty_cells=' . $this->merge_empty_cells . '&amp;file_id=' . $this->id . '';
				$reset_action = esc_attr(wp_nonce_url($reset_action, 'import-upload'));
				echo '<h3>' . __( 'Map to fields are pre-selected based on your last import. <a href="'.$reset_action.'">Click here</a> to clear saved mapping.', 'wf_csv_import_export' ) . '</h3>';
				$saved_mapping = $mapping_from_db[0];
				$saved_evaluation = $mapping_from_db[1];
			//}	
		}

                $attrs = self::get_all_product_attributes();
                $attr_keys = array_values($attrs);
                
                $attributes = array();
                if(!empty($attr_keys) && !empty($attrs))
                    $attributes  = array_combine($attr_keys , $attrs);
                
                $product_ptaxonomies         = get_object_taxonomies( 'product', 'name' );
                $product_vtaxonomies         = get_object_taxonomies( 'product_variation', 'name' );
                $product_taxonomies          = array_merge($product_ptaxonomies, $product_vtaxonomies);
                $taxonomies = array_keys($product_taxonomies);
                $new_keys = array_values($taxonomies);
                $taxonomies  = array_combine($new_keys , $taxonomies);
		include( 'views/html-wf-import-options.php' );
	}

	/**
	 * The main controller for the actual import stage.
	 */
	public function import() {
		global $woocommerce, $wpdb;

		wp_suspend_cache_invalidation( true );

		$this->hf_log_data_change( 'csv-import', '---' );
		$this->hf_log_data_change( 'csv-import', __( 'Processing products.', 'wf_csv_import_export' ) );
		foreach ( $this->parsed_data as $key => &$item ) {

			$product = $this->parser->parse_product( $item, $this->merge_empty_cells );
			if ( ! is_wp_error( $product ) ){
				$this->process_product( $product );
                        }else{
				$this->add_import_result( 'failed', $product->get_error_message(), 'Not parsed', json_encode( $item ), '-' );                                
                        }
			unset( $item, $product );
		}
		$this->hf_log_data_change( 'csv-import', __( 'Finished processing products.', 'wf_csv_import_export' ) );
		wp_suspend_cache_invalidation( false );
	}

	/**
	 * Parses the CSV file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the CSV file for importing
	 */

	public function hf_log_data_change ($content = 'csv-import',$data='')
	{
		if (WC()->version < '2.7.0')
		{
			$this->log->add($content,$data);
		}else
		{
			$context = array( 'source' => $content );
			$this->log->log("debug", $data ,$context);
		}
	}
	public function import_start( $file, $mapping, $start_pos, $end_pos, $eval_field ) {

		if(WC()->version < '2.7.0'){
		$memory    = size_format( woocommerce_let_to_num( ini_get( 'memory_limit' ) ) );
		$wp_memory = size_format( woocommerce_let_to_num( WP_MEMORY_LIMIT ) );
		}else
		{
		$memory    = size_format( wc_let_to_num( ini_get( 'memory_limit' ) ) );
		$wp_memory = size_format( wc_let_to_num( WP_MEMORY_LIMIT ) );
			
		}
		$this->hf_log_data_change( 'csv-import', '---[ New Import ] PHP Memory: ' . $memory . ', WP Memory: ' . $wp_memory );
		$this->hf_log_data_change( 'csv-import', __( 'Parsing products CSV.', 'wf_csv_import_export' ) );

		$this->parser = new WF_CSV_Parser( 'product' );

		list( $this->parsed_data, $this->raw_headers, $position ) = $this->parser->parse_data( $file, $this->delimiter, $mapping, $start_pos, $end_pos, $eval_field );

		$this->hf_log_data_change( 'csv-import', __( 'Finished parsing products CSV.', 'wf_csv_import_export' ) );

		unset( $import_data );

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		return $position;
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	public function import_end() {

		//wp_cache_flush(); Stops output in some hosting environments
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		do_action( 'import_end' );
	}

	/**
	 * Handles the CSV upload and initial parsing of the file to prepare for
	 * displaying author import options
	 *
	 * @return bool False if error uploading or invalid file, true otherwise
	 */
	public function handle_upload() {
		if($this->handle_ftp()){
			return true;
		}
		if ( empty( $_POST['file_url'] ) ) {

			$file = wp_import_handle_upload();

			if ( isset( $file['error'] ) ) {
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wf_csv_import_export' ) . '</strong><br />';
				echo esc_html( $file['error'] ) . '</p>';
				return false;
			}

			$this->id = (int) $file['id'];
			return true;

		} else {

			if ( file_exists( ABSPATH . $_POST['file_url'] ) ) {

				$this->file_url = esc_attr( $_POST['file_url'] );
				return true;

			} else {

				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wf_csv_import_export' ) . '</strong></p>';
				return false;

			}

		}

		return false;
	}

	public function product_exists( $title, $sku = '', $post_name = '' ) {
		global $wpdb;

		// Post Title Check
		$post_title = stripslashes( sanitize_post_field( 'post_title', $title, 0, 'db' ) );

	    $query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )";
	    $args = array();

	    /*
             * removed title check
            if ( ! empty ( $title ) ) {
	        $query .= ' AND post_title = %s';
	        $args[] = $post_title;
	    }
            

	    if ( ! empty ( $post_name ) ) {
	        $query .= ' AND post_name = %s';
	        $args[] = $post_name;
	    }

*/

	    if ( ! empty ( $args ) ) {
	        $posts_that_exist = $wpdb->get_col( $wpdb->prepare( $query, $args ) );

	        if ( $posts_that_exist ) {

	        	foreach( $posts_that_exist as $post_exists ) {

		        	// Check unique SKU
		        	$post_exists_sku = get_post_meta( $post_exists, '_sku', true );

					if ( $sku == $post_exists_sku ) {
						return true;
					}

	        	}

		    }
		}

		// Sku Check
		if ( $sku ) {

			 $post_exists_sku = $wpdb->get_var( $wpdb->prepare( "
				SELECT $wpdb->posts.ID
			    FROM $wpdb->posts
			    LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
			    WHERE $wpdb->posts.post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )
			    AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
			 ", $sku ) );

			 if ( $post_exists_sku ) {
				 return true;
			 }
		}

	    return false;
	}
        
        
        public function wf_get_product_id_by_sku( $sku ) {
            global $wpdb;

            // phpcs:ignore WordPress.VIP.DirectDatabaseQuery.DirectQuery
            $id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT posts.ID
                FROM $wpdb->posts AS posts
                LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
                WHERE posts.post_type IN ( 'product', 'product_variation' )
                AND posts.post_status != 'trash'
                AND postmeta.meta_key = '_sku'
                AND postmeta.meta_value = %s
                LIMIT 1",
                $sku
                )
            );

            return (int) apply_filters( 'wf_get_product_id_by_sku', $id, $sku );
        }

	/**
	 * Create new posts based on import information
	 */
	public function process_product( $post ) {
		$processing_product_id    = absint( $post['post_id'] );
		$processing_product       = get_post( $processing_product_id );
		$processing_product_title = $processing_product ? $processing_product->post_title : '';
		$processing_product_sku   = $processing_product ? $processing_product->sku : '';
		$merging                  = ! empty( $post['merging'] );

		if ( ! empty( $post['post_title'] ) ) {
			$processing_product_title = $post['post_title'];
		}

		if ( ! empty( $post['sku'] ) ) {
			$processing_product_sku = $post['sku'];
		}

		if ( ! empty( $processing_product_id ) && isset( $this->processed_posts[ $processing_product_id ] ) ) {
			$this->add_import_result( 'skipped', __( 'Product already processed', 'wf_csv_import_export' ), $processing_product_id, $processing_product_title, $processing_product_sku );
			$this->hf_log_data_change( 'csv-import', __('> Post ID already processed. Skipping.', 'wf_csv_import_export'), true );
			unset( $post );
			return;
		}

		if ( ! empty ( $post['post_status'] ) && $post['post_status'] == 'auto-draft' ) {
			$this->add_import_result( 'skipped', __( 'Skipping auto-draft', 'wf_csv_import_export' ), $processing_product_id, $processing_product_title, $processing_product_sku );
			$this->hf_log_data_change( 'csv-import', __('> Skipping auto-draft.', 'wf_csv_import_export'), true );
			unset( $post );
			return;
		}
		// Check if post exists when importing

		if ( ! $merging ) {
			if ( $this->product_exists( $processing_product_title, $processing_product_sku, $post['post_name'] ) ) {
                                if(!$processing_product_id && empty($processing_product_sku)) { 
                                // if no sku , no id and  no merge + is product in db with same title -> just give message
                                       $usr_msg = 'Product with same title already exist.';
                                    }else{
                                       $usr_msg = 'Product already exists.'; 
                                    }
                                $this->add_import_result( 'skipped', __( $usr_msg, 'wf_csv_import_export' ), $processing_product_id, $processing_product_title, $processing_product_sku );
				$this->hf_log_data_change( 'csv-import', sprintf( __('> &#8220;%s&#8221;'.$usr_msg, 'wf_csv_import_export'), esc_html($processing_product_title) ), true );
				unset( $post );
				return;
			}
                        
			if ( $processing_product_id && is_string( get_post_status( $processing_product_id ) ) ) {
				$this->add_import_result( 'skipped', __( 'Importing post ID conflicts with an existing post ID', 'wf_csv_import_export' ), $processing_product_id, get_the_title( $processing_product_id ), '' );
				$this->hf_log_data_change( 'csv-import', sprintf( __('> &#8220;%s&#8221; ID already exists.', 'wf_csv_import_export'), esc_html( $processing_product_id ) ), true );
				unset( $post );
				return;
			}
		}
                
                
//		if ( ! $merging ) {
//                    error_log('<pre>$this->wf_get_product_id_by_sku(  $processing_product_sku ):-' . print_r($this->wf_get_product_id_by_sku(  $processing_product_sku ), 1) . '</per>', 3, ABSPATH . "/wp-content/uploads/wc-logs/test-log.txt");
//			if ( $this->wf_get_product_id_by_sku(  $processing_product_sku ) ) {
////                                $usr_msg =  __( 'Product already exists.', 'wf_csv_import_export' );                                 
////                                $this->add_import_result( 'skipped',$usr_msg, $processing_product_id, $processing_product_title, $processing_product_sku );
////				$this->hf_log_data_change( 'csv-import', sprintf( __('> &#8220;%s&#8221; Product already exists.', 'wf_csv_import_export'), esc_html($processing_product_title) ), true );
////				unset( $post );
////				return;
//                            
//                                if(!$processing_product_id && empty($processing_product_sku)) { 
//                                // if no sku , no id and  no merge + is product in db with same title -> just give message
//                                       $usr_msg = 'Product with same title already exist.';
//                                }else{
//                                       $usr_msg = 'Product already exists.'; 
//                                }
//                                $this->add_import_result( 'skipped', __( $usr_msg, 'wf_csv_import_export' ), $processing_product_id, $processing_product_title, $processing_product_sku );
//				$this->hf_log_data_change( 'csv-import', sprintf( __('> &#8220;%s&#8221;'.$usr_msg, 'wf_csv_import_export'), esc_html($processing_product_title) ), true );
//				unset( $post );
//				return;
//			}
//                        
//			if ( $processing_product_id && is_string( get_post_status( $processing_product_id ) ) ) {
//				$this->add_import_result( 'skipped', __( 'Importing post ID conflicts with an existing post ID', 'wf_csv_import_export' ), $processing_product_id, get_the_title( $processing_product_id ), '' );
//				$this->hf_log_data_change( 'csv-import', sprintf( __('> &#8220;%s&#8221; ID already exists.', 'wf_csv_import_export'), esc_html( $processing_product_id ) ), true );
//				unset( $post );
//				return;
//			}
//		}
		// Check post type to avoid conflicts with IDs
                $is_post_exist_in_db = get_post_type( $processing_product_id );
		if ( $merging && $processing_product_id && !empty($is_post_exist_in_db) && ($is_post_exist_in_db !== $post['post_type'] )) {
			$this->add_import_result( 'skipped', __( 'Post is not a product', 'wf_csv_import_export' ), $processing_product_id, $processing_product_title, $processing_product_sku );
			$this->hf_log_data_change( 'csv-import', sprintf( __('> &#8220;%s&#8221; is not a product.', 'wf_csv_import_export'), esc_html($processing_product_id) ), true );
			unset( $post );
			return;
		}

		if ( $merging && !empty($is_post_exist_in_db) ) {
                       
			// Only merge fields which are set
			$post_id = $processing_product_id;

			$this->hf_log_data_change( 'csv-import', sprintf( __('> Merging post ID %s.', 'wf_csv_import_export'), $post_id ), true );

			$postdata = array(
				'ID' => $post_id
			);

			if ( $this->merge_empty_cells ) {
				if ( isset( $post['post_content'] ) ) {
					$postdata['post_content'] = $post['post_content'];
				}
				if ( isset( $post['post_excerpt'] ) ) {
					$postdata['post_excerpt'] = $post['post_excerpt'];
				}
				if ( isset( $post['post_password'] ) ) {
					$postdata['post_password'] = $post['post_password'];
				}
				if ( isset( $post['post_parent'] ) ) {
					$postdata['post_parent'] = $post['post_parent'];
				}
			} else {
				if ( ! empty( $post['post_content'] ) ) {
					$postdata['post_content'] = $post['post_content'];
				}
				if ( ! empty( $post['post_excerpt'] ) ) {
					$postdata['post_excerpt'] = $post['post_excerpt'];
				}
				if ( ! empty( $post['post_password'] ) ) {
					$postdata['post_password'] = $post['post_password'];
				}
				if ( isset( $post['post_parent'] ) && $post['post_parent'] !== '' ) {
					$postdata['post_parent'] = $post['post_parent'];
				}
			}

			if ( ! empty( $post['post_title'] ) ) {
				$postdata['post_title'] = $post['post_title'];
			}

			if ( ! empty( $post['post_author'] ) ) {
				$postdata['post_author'] = absint( $post['post_author'] );
			}
			if ( ! empty( $post['post_date'] ) ) {
				$postdata['post_date'] = date("Y-m-d H:i:s", strtotime( $post['post_date'] ) );
			}
			if ( ! empty( $post['post_date_gmt'] ) ) {
				$postdata['post_date_gmt'] = date("Y-m-d H:i:s", strtotime( $post['post_date_gmt'] ) );
			}
			if ( ! empty( $post['post_name'] ) ) {
				$postdata['post_name'] = $post['post_name'];
			}
			if ( ! empty( $post['post_status'] ) ) {
				$postdata['post_status'] = $post['post_status'];
			}
			if ( ! empty( $post['menu_order'] ) ) {
				$postdata['menu_order'] = $post['menu_order'];
			}
			if ( ! empty( $post['comment_status'] ) ) {
				$postdata['comment_status'] = $post['comment_status'];
			}
			if ( sizeof( $postdata ) > 1 ) {
				$result = wp_update_post( $postdata );

				if ( ! $result ) {
					$this->add_import_result( 'failed', __( 'Failed to update product', 'wf_csv_import_export' ), $post_id, $processing_product_title, $processing_product_sku );
					$this->hf_log_data_change( 'csv-import', sprintf( __('> Failed to update product %s', 'wf_csv_import_export'), $post_id ), true );
					unset( $post );
					return;
				} else {
					$this->hf_log_data_change( 'csv-import', __( '> Merged post data: ', 'wf_csv_import_export' ) . print_r( $postdata, true ) );
				}
			}

		} else {
                        $merging = FALSE;
			// Get parent
			$post_parent = (isset($post['post_parent'])?$post['post_parent']:'');
                        
			if ( $post_parent !== "" ) {
				$post_parent = absint( $post_parent );

				if ( $post_parent > 0 ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[ $post_parent ] ) ) {
						$post_parent = $this->processed_posts[ $post_parent ];

					// otherwise record the parent for later
					} else {
                                            
						$this->post_orphans[ intval( $processing_product_id ) ] = $post_parent;
						//$post_parent = 0;
                                                
					}
                                        
				}
			}

			// Insert product
			$this->hf_log_data_change( 'csv-import', sprintf( __('> Inserting %s', 'wf_csv_import_export'), esc_html( $processing_product_title ) ), true );
                        $postdata = array(
				'import_id'      => $processing_product_id,
				'post_author'    => !empty($post['post_author']) ? absint($post['post_author']) : get_current_user_id(),
                                'post_date' => !empty( $post['post_date'] ) ? date("Y-m-d H:i:s", strtotime($post['post_date'])) : '',
                                'post_date_gmt' => ( !empty($post['post_date_gmt']) && $post['post_date_gmt'] ) ? date('Y-m-d H:i:s', strtotime($post['post_date_gmt'])) : '',
				'post_content'   => !empty($post['post_content'])?$post['post_content']:'',
				'post_excerpt'   => !empty($post['post_excerpt'])?$post['post_excerpt']:'',
				'post_title'     => $processing_product_title,
				'post_name'      => !empty( $post['post_name'] ) ? $post['post_name'] : sanitize_title( $processing_product_title ),
				'post_status'    => !empty( $post['post_status'] ) ? $post['post_status'] : 'publish',
				'post_parent'    => $post_parent,
				'menu_order'     => !empty($post['menu_order'])?$post['menu_order']:'',
				'post_type'      => !empty($post['post_type'])?$post['post_type']:"",
				'post_password'  => !empty($post['post_password'])?$post['post_password']:'',
				'comment_status' => !empty($post['comment_status'])?$post['comment_status']:'',
			);

			$post_id = wp_insert_post( $postdata, true );

			if ( is_wp_error( $post_id ) ) {

				$this->add_import_result( 'failed', __( 'Failed to import product', 'wf_csv_import_export' ), $processing_product_id, $processing_product_title, $processing_product_sku );
				$this->hf_log_data_change( 'csv-import', sprintf( __( 'Failed to import product &#8220;%s&#8221;', 'wf_csv_import_export' ), esc_html($processing_product_title) ) );
				unset( $post );
				return;

			} else {

				$this->hf_log_data_change( 'csv-import', sprintf( __('> Inserted - post ID is %s.', 'wf_csv_import_export'), $post_id ) );

			}
		}

		unset( $postdata );

		// map pre-import ID to local ID
		if ( empty( $processing_product_id ) ) {
			$processing_product_id = (int) $post_id;
		}

		$this->processed_posts[ intval( $processing_product_id ) ] = (int) $post_id;

		// add categories, tags and other terms
		if ( ! empty( $post['terms'] ) && is_array( $post['terms'] ) ) {

			$terms_to_set = array();

			foreach ( $post['terms'] as $term_group ) {

				$taxonomy 	= $term_group['taxonomy'];
				$terms		= $term_group['terms'];

				if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}

				if ( ! is_array( $terms ) ) {
					$terms = array( $terms );
				}

				$terms_to_set[ $taxonomy ] = array();

				foreach ( $terms as $term_id ) {

					if ( ! $term_id ) continue;

					$terms_to_set[ $taxonomy ][] = intval( $term_id );
				}

			}

			foreach ( $terms_to_set as $tax => $ids ) {
				$tt_ids = wp_set_post_terms( $post_id, $ids, $tax, false );
			}

			unset( $post['terms'], $terms_to_set );
		}

		// add/update post meta
		if ( ! empty( $post['postmeta'] ) && is_array( $post['postmeta'] ) ) {
			foreach ( $post['postmeta'] as $meta ) {
				$key = apply_filters( 'import_post_meta_key', $meta['key'] );

				if ( $key ) {
					update_post_meta( $post_id, $key, maybe_unserialize( $meta['value'] ) );
				}

				if ( $key == '_file_paths' ) {
					do_action( 'woocommerce_process_product_file_download_paths', $post_id, 0, maybe_unserialize( $meta['value'] ) );
				}

			}

			unset( $post['postmeta'] );
		}

		// Import images and add to post
		if ( ! empty( $post['images'] ) && is_array($post['images']) ) {

			$featured    = true;
			$gallery_ids = array();

			if ($merging) {

				// Get basenames
				$image_basenames = array();

				foreach( $post['images'] as $image )
					$image_basenames[] = basename( $image );

				// Loop attachments already attached to the product
				//$attachments = get_posts( 'post_parent=' . $post_id . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1' );
                                
                                $processing_product_object = wc_get_product($post_id);
                                $attachments = $processing_product_object->get_gallery_attachment_ids();
                                $post_thumbnail_id = get_post_thumbnail_id($post_id);
                                if(isset($post_thumbnail_id)&& !empty($post_thumbnail_id)){
                                    $attachments[]=$post_thumbnail_id;
                                }
                                
				foreach ( $attachments as $attachment_key => $attachment ) {

					$attachment_url 	= wp_get_attachment_url( $attachment );
					$attachment_basename 	= basename( $attachment_url );

					// Don't import existing images
					if ( in_array( $attachment_url, $post['images'] ) || in_array( $attachment_basename, $image_basenames ) ) {

						foreach( $post['images'] as $key => $image ) {

							if ( $image == $attachment_url || basename( $image ) == $attachment_basename ) {
								unset( $post['images'][ $key ] );

								$this->hf_log_data_change( 'csv-import', sprintf( __( '> > Image exists - skipping %s', 'wf_csv_import_export' ), basename( $image ) ) );

								if ( $key == 0 ) {
									update_post_meta( $post_id, '_thumbnail_id', $attachment );
									$featured = false;
								} else {
									$gallery_ids[ $key ] = $attachment;
								}
							}

						}

					} else {

						// Detach image which is not being merged
						$attachment_post = array();
						$attachment_post['ID'] = $attachment;
						$attachment_post['post_parent'] = '';
						wp_update_post( $attachment_post );
						unset( $attachment_post );

					}

				}

				unset( $attachments );
			}

			if ( $post['images'] ) foreach ( $post['images'] as $image_key => $image ) {

				$this->hf_log_data_change( 'csv-import', sprintf( __( '> > Importing image "%s"', 'wf_csv_import_export' ), $image ) );

				$filename = basename( $image );

				$attachment = array(
						'post_title'   => preg_replace( '/\.[^.]+$/', '', $processing_product_title . ' ' . ( $image_key + 1 ) ),
						'post_content' => '',
						'post_status'  => 'inherit',
						'post_parent'  => $post_id
				);

				$attachment_id = $this->process_attachment( $attachment, $image, $post_id );

				if ( ! is_wp_error( $attachment_id ) && $attachment_id ) {

					$this->hf_log_data_change( 'csv-import', sprintf( __( '> > Imported image "%s"', 'wf_csv_import_export' ), $image ) );

					// Set alt
					update_post_meta( $attachment_id, '_wp_attachment_image_alt', $processing_product_title );

					if ( $featured ) {
						update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
					} else {
						$gallery_ids[ $image_key ] = $attachment_id;
					}

					update_post_meta( $attachment_id, '_woocommerce_exclude_image', 0 );

					$featured = false;
				} else {
					$this->hf_log_data_change( 'csv-import', sprintf( __( '> > Error importing image "%s"', 'wf_csv_import_export' ), $image ) );
					$this->hf_log_data_change( 'csv-import', '> > ' . $attachment_id->get_error_message() );
				}

				unset( $attachment, $attachment_id );
			}

			$this->hf_log_data_change( 'csv-import', __( '> > Images set', 'wf_csv_import_export' ) );

			ksort( $gallery_ids );

			update_post_meta( $post_id, '_product_image_gallery', implode( ',', $gallery_ids ) );

			unset( $post['images'], $featured, $gallery_ids );
		}

		// Import attributes
		if ( ! empty( $post['attributes'] ) && is_array($post['attributes']) ) {

			if ($merging) {
				$attributes = array_filter( (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) ) );
				$attributes = array_merge( $attributes, $post['attributes'] );
			} else {
				$attributes = $post['attributes'];
			}

			// Sort attribute positions
			if ( ! function_exists( 'attributes_cmp' ) ) {
				function attributes_cmp( $a, $b ) {
				    if ( $a['position'] == $b['position'] ) return 0;
				    return ( $a['position'] < $b['position'] ) ? -1 : 1;
				}
			}
			uasort( $attributes, 'attributes_cmp' );

			update_post_meta( $post_id, '_product_attributes', $attributes );

			unset( $post['attributes'], $attributes );
		}

		// Import GPF
		if ( ! empty( $post['gpf_data'] ) && is_array( $post['gpf_data'] ) ) {

			update_post_meta( $post_id, '_woocommerce_gpf_data', $post['gpf_data'] );

			unset( $post['gpf_data'] );
		}

		if ( ! empty( $post['upsell_skus'] ) && is_array( $post['upsell_skus'] ) ) {
			$this->upsell_skus[ $post_id ] = $post['upsell_skus'];
		}

		if ( ! empty( $post['crosssell_skus'] ) && is_array( $post['crosssell_skus'] ) ) {
			$this->crosssell_skus[ $post_id ] = $post['crosssell_skus'];
		}

		add_post_meta( $post_id, 'total_sales', 0 );

		if ( $merging ) {
			$this->add_import_result( 'merged', 'Merge successful', $post_id, $processing_product_title, $processing_product_sku );
			$this->hf_log_data_change( 'csv-import', sprintf( __('> Finished merging post ID %s.', 'wf_csv_import_export'), $post_id ) );
		} else {
			$this->add_import_result( 'imported', 'Import successful', $post_id, $processing_product_title, $processing_product_sku );
			$this->hf_log_data_change( 'csv-import', sprintf( __('> Finished importing post ID %s.', 'wf_csv_import_export'), $post_id ) );
		}
                
                do_action('wf_refresh_after_product_import',$processing_product_object); // hook for forcefully refresh product
		unset( $post );
	}

	/**
	 * Log a row's import status
	 */
	protected function add_import_result( $status, $reason, $post_id = '', $post_title = '', $sku = '' ) {
		$this->import_results[] = array(
			'post_title' => $post_title,
			'post_id'    => $post_id,
			'sku'    	 => $sku,
			'status'     => $status,
			'reason'     => $reason
		);
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array $post Attachment post details from WXR
	 * @param string $url URL to fetch attachment from
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	public function process_attachment( $post, $url, $post_id ) {

		$attachment_id 		= '';
		$attachment_url 	= '';
		$attachment_file 	= '';
		$upload_dir 		= wp_upload_dir();

		// If same server, make it a path and move to upload directory
		/*if ( strstr( $url, $upload_dir['baseurl'] ) ) {

			$url = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );

		} else*/
		if ( strstr( $url, site_url() ) ) {
                    
                        $image_id = $this->wt_get_image_id_by_url($url);
                        if($image_id){
                            $attachment_id = $image_id;

                            $this->hf_log_data_change('csv-import', sprintf(__('> > (Image already in the site)Inserted image attachment "%s"', 'wf_csv_import_export'), $url));

                            $this->attachments[] = $attachment_id;

                            return $attachment_id;
                        }
			$abs_url 	= str_replace( trailingslashit( site_url() ), trailingslashit( ABSPATH ), urldecode($url) );
			$new_name 	= wp_unique_filename( $upload_dir['path'], basename( urldecode($url) ) );
			$new_url 	= trailingslashit( $upload_dir['path'] ) . $new_name;

			if ( copy( $abs_url, $new_url ) ) {
				$url = basename( $new_url );
			}
		}

		if ( ! strstr( $url, 'http' ) ) {  // if not a url

			// Local file
			$attachment_file 	= trailingslashit( $upload_dir['basedir'] ) . 'product_images/' . $url;

			// We have the path, check it exists
			if ( ! file_exists( $attachment_file ) )
				$attachment_file 	= trailingslashit( $upload_dir['path'] ) . $url;

			// We have the path, check it exists
			if ( file_exists( $attachment_file ) ) {

				$attachment_url 	= str_replace( trailingslashit( ABSPATH ), trailingslashit( site_url() ), $attachment_file );

				if ( $info = wp_check_filetype( $attachment_file ) )
					$post['post_mime_type'] = $info['type'];
				else
					return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'wordpress-importer') );
                                
                                
                                $image_id = $this->wt_get_image_id_by_url($attachment_url);
                                if($image_id){
                                    $attachment_id = $image_id;
                                    $this->hf_log_data_change('csv-import', sprintf(__('> > (Image already in the site)Inserted image attachment "%s"', 'wf_csv_import_export'), $url));
                                    $this->attachments[] = $attachment_id;
                                    return $attachment_id;
                                }

				$post['guid'] = $attachment_url;

				$attachment_id 		= wp_insert_attachment( $post, $attachment_file, $post_id );

			} else {
				return new WP_Error( 'attachment_processing_error', __('Local image did not exist!', 'wordpress-importer') );
			}

		} else {

			// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
			if ( preg_match( '|^/[\w\W]+$|', $url ) )
				$url = rtrim( site_url(), '/' ) . $url;

			$upload = $this->fetch_remote_file( $url, $post );

			if ( is_wp_error( $upload ) )
				return $upload;

			if ( $info = wp_check_filetype( $upload['file'] ) )
				$post['post_mime_type'] = $info['type'];
			else
				return new WP_Error( 'attachment_processing_error', __('Invalid file type', 'wordpress-importer') );

			$post['guid']       = $upload['url'];
			$attachment_file 	= $upload['file'];
			$attachment_url 	= $upload['url'];

			// as per wp-admin/includes/upload.php
			$attachment_id = wp_insert_attachment( $post, $upload['file'], $post_id );

			unset( $upload );
		}

		if ( ! is_wp_error( $attachment_id ) && $attachment_id > 0 ) {
			$this->hf_log_data_change( 'csv-import', sprintf( __( '> > Inserted image attachment "%s"', 'wf_csv_import_export' ), $url ) );

			$this->attachments[] = $attachment_id;
		}

		return $attachment_id;
	}
        
        function wt_get_image_id_by_url($image_url) {
            global $wpdb;
            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));
            return isset($attachment[0])&& $attachment[0]>0 ? $attachment[0]:'';
        }

	/**
	 * Attempt to download a remote file attachment
	 */
	public function fetch_remote_file( $url, $post ) {

		// extract the file name and extension from the url
		$file_name 		= basename( current( explode( '?', $url ) ) );
		$wp_filetype 	= wp_check_filetype( $file_name, null );
		$parsed_url 	= @parse_url( $url );

		// Check parsed URL
		if ( ! $parsed_url || ! is_array( $parsed_url ) )
			return new WP_Error( 'import_file_error', 'Invalid URL' );

		// Ensure url is valid
		$url = str_replace( " ", '%20', $url );

		// Get the file
		$response = wp_remote_get( $url, array(
			'timeout' => 10
		) );

		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 )
			return new WP_Error( 'import_file_error', 'Error getting remote image' );

		// Ensure we have a file name and type
		if ( ! $wp_filetype['type'] ) {

			$headers = wp_remote_retrieve_headers( $response );

			if ( isset( $headers['content-disposition'] ) && strstr( $headers['content-disposition'], 'filename=' ) ) {

				$disposition = end( explode( 'filename=', $headers['content-disposition'] ) );
				$disposition = sanitize_file_name( $disposition );
				$file_name   = $disposition;

			} elseif ( isset( $headers['content-type'] ) && strstr( $headers['content-type'], 'image/' ) ) {

				$file_name = 'image.' . str_replace( 'image/', '', $headers['content-type'] );

			}

			unset( $headers );
		}

		// Upload the file
		$upload = wp_upload_bits( $file_name, '', wp_remote_retrieve_body( $response ) );

		if ( $upload['error'] )
			return new WP_Error( 'upload_dir_error', $upload['error'] );

		// Get filesize
		$filesize = filesize( $upload['file'] );

		if ( 0 == $filesize ) {
			@unlink( $upload['file'] );
			unset( $upload );
			return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'wf_csv_import_export') );
		}

		unset( $response );

		return $upload;
	}

	/**
	 * Decide what the maximum file size for downloaded attachments is.
	 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
	 *
	 * @return int Maximum attachment file size to import
	 */
	public function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}

	/**
	 * Attempt to associate posts and menu items with previously missing parents
	 */
	public function backfill_parents() {
		global $wpdb;

		// find parents for post orphans
		if ( ! empty( $this->post_orphans ) && is_array( $this->post_orphans ) )
			foreach ( $this->post_orphans as $child_id => $parent_id ) {
				$local_child_id = $local_parent_id = false;
				if ( isset( $this->processed_posts[$child_id] ) )
					$local_child_id = $this->processed_posts[$child_id];
				if ( isset( $this->processed_posts[$parent_id] ) )
					$local_parent_id = $this->processed_posts[$parent_id];

				if ( $local_child_id && $local_parent_id )
					$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
			}
	}

	/**
	 * Attempt to associate posts and menu items with previously missing parents
	 */
	public function link_product_skus( $type, $product_id, $skus ) {
		global $wpdb;

		$ids = array();

		foreach ( $skus as $sku ) {
			$ids[] = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_sku' AND meta_value = %s;", $sku ) );
		}

		$ids = array_filter( $ids );

		update_post_meta( $product_id, "_{$type}_ids", $ids );
	}
	
	private function handle_ftp(){
		$enable_ftp_ie         	= !empty( $_POST['enable_ftp_ie'] ) ? true : false;
		if($enable_ftp_ie == false) return false;
		
		$ftp_server		= ! empty( $_POST['ftp_server'] ) ? $_POST['ftp_server'] : '';
		$ftp_server_path	= ! empty( $_POST['ftp_server_path'] ) ? $_POST['ftp_server_path'] : '';
		$ftp_user		= ! empty( $_POST['ftp_user'] ) ? $_POST['ftp_user'] : '';
		$ftp_password           = ! empty( $_POST['ftp_password'] ) ? $_POST['ftp_password'] : '';
		$use_ftps         	= ! empty( $_POST['use_ftps'] ) ? true : false;
		
		
		$settings = array();
		$settings[ 'ftp_server' ]		= $ftp_server;
		$settings[ 'ftp_user' ]			= $ftp_user;
		$settings[ 'ftp_password' ]		= $ftp_password;
		$settings[ 'use_ftps' ]			= $use_ftps;
		$settings[ 'enable_ftp_ie' ]	= $enable_ftp_ie;
		$settings[ 'ftp_server_path' ]	= $ftp_server_path;
		
		
		$local_file = 'wp-content/plugins/product-csv-import-export-for-woocommerce/temp-import.csv';
		$server_file = $ftp_server_path;
					   
		update_option( 'wf_shipment_tracking_importer_ftp', $settings );
		
		$ftp_conn = $use_ftps ? ftp_ssl_connect($ftp_server) : ftp_connect($ftp_server);
		$error_message = "";
		$success = false;
		if($ftp_conn == false){
			$error_message = "There is connection problem\n";
		}
		
		if(empty($error_message)){
			if(ftp_login($ftp_conn, $ftp_user, $ftp_password) == false){
				$error_message = "Not able to login \n";
			}
		}
		if(empty($error_message)){

                if (ftp_get($ftp_conn, ABSPATH.$local_file, $server_file, FTP_BINARY)) {
				$error_message =  "";
				$success = true;
			} else {
				$error_message = "There was a problem\n";
			}
		}
		
		ftp_close($ftp_conn);
		if($success){
			$this->file_url = $local_file;
		}else{
			die($error_message);
		}	
		return true;
	}

	// Display import page title
	public function header() {
		echo '<div class="pipe-import-wrap"><div class="icon32" id="icon-woocommerce-importer"><br></div>';		
	}

	// Close div.pipe-import-wrap
	public function footer() {
		echo '</div>';
	}

	/**
	 * Display introductory text and file upload form
	 */
	public function greet() {
		$action     = 'admin.php?import=xa_woocommerce_csv&amp;step=1';
		$bytes      = apply_filters( 'import_upload_size_limit', wp_max_upload_size() );
		$size       = size_format( $bytes );
		$upload_dir = wp_upload_dir();

		include( 'views/html-wf-import-greeting.php' );
	}

	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
	 * @return int 60
	 */
	public function bump_request_timeout( $val ) {
		return 60;
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
