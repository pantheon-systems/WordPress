<?php
/**
 * WooCommerce Plugin Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Plugin/Classes
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'SV_WC_Helper' ) ) :

	/**
	 * SkyVerge Helper Class
	 *
	 * The purpose of this class is to centralize common utility functions that
	 * are commonly used in SkyVerge plugins
	 *
	 * @since 2.2.0
	 */
	class SV_WC_Helper {


		/** encoding used for mb_*() string functions */
		const MB_ENCODING = 'UTF-8';


		/** String manipulation functions (all multi-byte safe) ***************/

		/**
		 * Returns true if the haystack string starts with needle
		 *
		 * Note: case-sensitive
		 *
		 * @since 2.2.0
		 * @param string $haystack
		 * @param string $needle
		 * @return bool
		 */
		public static function str_starts_with( $haystack, $needle ) {

			if ( self::multibyte_loaded() ) {

				if ( '' === $needle ) {
					return true;
				}

				return 0 === mb_strpos( $haystack, $needle, 0, self::MB_ENCODING );

			} else {

				$needle = self::str_to_ascii( $needle );

				if ( '' === $needle ) {
					return true;
				}

				return 0 === strpos( self::str_to_ascii( $haystack ), self::str_to_ascii( $needle ) );
			}
		}


		/**
		 * Return true if the haystack string ends with needle
		 *
		 * Note: case-sensitive
		 *
		 * @since 2.2.0
		 * @param string $haystack
		 * @param string $needle
		 * @return bool
		 */
		public static function str_ends_with( $haystack, $needle ) {

			if ( '' === $needle ) {
				return true;
			}

			if ( self::multibyte_loaded() ) {

				return mb_substr( $haystack, -mb_strlen( $needle, self::MB_ENCODING ), null, self::MB_ENCODING ) === $needle;

			} else {

				$haystack = self::str_to_ascii( $haystack );
				$needle   = self::str_to_ascii( $needle );

				return substr( $haystack, -strlen( $needle ) ) === $needle;
			}
		}


		/**
		 * Returns true if the needle exists in haystack
		 *
		 * Note: case-sensitive
		 *
		 * @since 2.2.0
		 * @param string $haystack
		 * @param string $needle
		 * @return bool
		 */
		public static function str_exists( $haystack, $needle ) {

			if ( self::multibyte_loaded() ) {

				if ( '' === $needle ) {
					return false;
				}

				return false !== mb_strpos( $haystack, $needle, 0, self::MB_ENCODING );

			} else {

				$needle = self::str_to_ascii( $needle );

				if ( '' === $needle ) {
					return false;
				}

				return false !== strpos( self::str_to_ascii( $haystack ), self::str_to_ascii( $needle ) );
			}
		}


		/**
		 * Truncates a given $string after a given $length if string is longer than
		 * $length. The last characters will be replaced with the $omission string
		 * for a total length not exceeding $length
		 *
		 * @since 2.2.0
		 * @param string $string text to truncate
		 * @param int $length total desired length of string, including omission
		 * @param string $omission omission text, defaults to '...'
		 * @return string
		 */
		public static function str_truncate( $string, $length, $omission = '...' ) {

			if ( self::multibyte_loaded() ) {

				// bail if string doesn't need to be truncated
				if ( mb_strlen( $string, self::MB_ENCODING ) <= $length ) {
					return $string;
				}

				$length -= mb_strlen( $omission, self::MB_ENCODING );

				return mb_substr( $string, 0, $length, self::MB_ENCODING ) . $omission;

			} else {

				$string = self::str_to_ascii( $string );

				// bail if string doesn't need to be truncated
				if ( strlen( $string ) <= $length ) {
					return $string;
				}

				$length -= strlen( $omission );

				return substr( $string, 0, $length ) . $omission;
			}
		}


		/**
		 * Returns a string with all non-ASCII characters removed. This is useful
		 * for any string functions that expect only ASCII chars and can't
		 * safely handle UTF-8. Note this only allows ASCII chars in the range
		 * 33-126 (newlines/carriage returns are stripped)
		 *
		 * @since 2.2.0
		 * @param string $string string to make ASCII
		 * @return string
		 */
		public static function str_to_ascii( $string ) {

			// strip ASCII chars 32 and under
			$string = filter_var( $string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );

			// strip ASCII chars 127 and higher
			return filter_var( $string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH );
		}


		/**
		 * Return a string with insane UTF-8 characters removed, like invisible
		 * characters, unused code points, and other weirdness. It should
		 * accept the common types of characters defined in Unicode.
		 *
		 * The following are allowed characters:
		 *
		 * p{L} - any kind of letter from any language
		 * p{Mn} - a character intended to be combined with another character without taking up extra space (e.g. accents, umlauts, etc.)
		 * p{Mc} - a character intended to be combined with another character that takes up extra space (vowel signs in many Eastern languages)
		 * p{Nd} - a digit zero through nine in any script except ideographic scripts
		 * p{Zs} - a whitespace character that is invisible, but does take up space
		 * p{P} - any kind of punctuation character
		 * p{Sm} - any mathematical symbol
		 * p{Sc} - any currency sign
		 *
		 * pattern definitions from http://www.regular-expressions.info/unicode.html
		 *
		 * @since 4.0.0
		 * @param string $string
		 * @return mixed
		 */
		public static function str_to_sane_utf8( $string ) {

			$sane_string = preg_replace( '/[^\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Zs}\p{P}\p{Sm}\p{Sc}]/u', '', $string );

			// preg_replace with the /u modifier can return null or false on failure
			return ( is_null( $sane_string ) || false === $sane_string ) ? $string : $sane_string;
		}


		/**
		 * Helper method to check if the multibyte extension is loaded, which
		 * indicates it's safe to use the mb_*() string methods
		 *
		 * @since 2.2.0
		 * @return bool
		 */
		protected static function multibyte_loaded() {

			return extension_loaded( 'mbstring' );
		}


		/** Array functions ***************************************************/


		/**
		 * Insert the given element after the given key in the array
		 *
		 * Sample usage:
		 *
		 * given
		 *
		 * array( 'item_1' => 'foo', 'item_2' => 'bar' )
		 *
		 * array_insert_after( $array, 'item_1', array( 'item_1.5' => 'w00t' ) )
		 *
		 * becomes
		 *
		 * array( 'item_1' => 'foo', 'item_1.5' => 'w00t', 'item_2' => 'bar' )
		 *
		 * @since 2.2.0
		 * @param array $array array to insert the given element into
		 * @param string $insert_key key to insert given element after
		 * @param array $element element to insert into array
		 * @return array
		 */
		public static function array_insert_after( Array $array, $insert_key, Array $element ) {

			$new_array = array();

			foreach ( $array as $key => $value ) {

				$new_array[ $key ] = $value;

				if ( $insert_key == $key ) {

					foreach ( $element as $k => $v ) {
						$new_array[ $k ] = $v;
					}
				}
			}

			return $new_array;
		}


		/**
		 * Convert array into XML by recursively generating child elements
		 *
		 * First instantiate a new XML writer object:
		 *
		 * $xml = new XMLWriter();
		 *
		 * Open in memory (alternatively you can use a local URI for file output)
		 *
		 * $xml->openMemory();
		 *
		 * Then start the document
		 *
		 * $xml->startDocument( '1.0', 'UTF-8' );
		 *
		 * Don't forget to end the document and output the memory
		 *
		 * $xml->endDocument();
		 *
		 * $your_xml_string = $xml->outputMemory();
		 *
		 * @since 2.2.0
		 * @param \XMLWriter $xml_writer XML writer instance
		 * @param string|array $element_key name for element, e.g. <per_page>
		 * @param string|array $element_value value for element, e.g. 100
		 * @return string generated XML
		 */
		public static function array_to_xml( $xml_writer, $element_key, $element_value = array() ) {

			if ( is_array( $element_value ) ) {

				// handle attributes
				if ( '@attributes' === $element_key ) {
					foreach ( $element_value as $attribute_key => $attribute_value ) {

						$xml_writer->startAttribute( $attribute_key );
						$xml_writer->text( $attribute_value );
						$xml_writer->endAttribute();
					}
					return;
				}

				// handle multi-elements (e.g. multiple <Order> elements)
				if ( is_numeric( key( $element_value ) ) ) {

					// recursively generate child elements
					foreach ( $element_value as $child_element_key => $child_element_value ) {

						$xml_writer->startElement( $element_key );

						foreach ( $child_element_value as $sibling_element_key => $sibling_element_value ) {
							self::array_to_xml( $xml_writer, $sibling_element_key, $sibling_element_value );
						}

						$xml_writer->endElement();
					}

				} else {

					// start root element
					$xml_writer->startElement( $element_key );

					// recursively generate child elements
					foreach ( $element_value as $child_element_key => $child_element_value ) {
						self::array_to_xml( $xml_writer, $child_element_key, $child_element_value );
					}

					// end root element
					$xml_writer->endElement();
				}

			} else {

				// handle single elements
				if ( '@value' == $element_key ) {

					$xml_writer->text( $element_value );

				} else {

					// wrap element in CDATA tags if it contains illegal characters
					if ( false !== strpos( $element_value, '<' ) || false !== strpos( $element_value, '>' ) ) {

						$xml_writer->startElement( $element_key );
						$xml_writer->writeCdata( $element_value );
						$xml_writer->endElement();

					} else {

						$xml_writer->writeElement( $element_key, $element_value );
					}

				}

				return;
			}
		}


		/** Number helper functions *******************************************/


		/**
		 * Format a number with 2 decimal points, using a period for the decimal
		 * separator and no thousands separator.
		 *
		 * Commonly used for payment gateways which require amounts in this format.
		 *
		 * @since 3.0.0
		 * @param float $number
		 * @return string
		 */
		public static function number_format( $number ) {

			return number_format( (float) $number, 2, '.', '' );
		}


		/** WooCommerce helper functions **************************************/


		/**
		 * Get order line items (products) in a neatly-formatted array of objects
		 * with properties:
		 *
		 * + id - item ID
		 * + name - item name, usually product title, processed through htmlentities()
		 * + description - formatted item meta (e.g. Size: Medium, Color: blue), processed through htmlentities()
		 * + quantity - item quantity
		 * + item_total - item total (line total divided by quantity, excluding tax & rounded)
		 * + line_total - line item total (excluding tax & rounded)
		 * + meta - formatted item meta array
		 * + product - item product or null if getting product from item failed
		 * + item - raw item array
		 *
		 * @since 3.0.0
		 * @param \WC_Order $order
		 * @return array
		 */
		public static function get_order_line_items( $order ) {

			$line_items = array();

			foreach ( $order->get_items() as $id => $item ) {

				$line_item = new stdClass();

				// TODO: remove when WC 3.0 can be required
				$name     = $item instanceof WC_Order_Item_Product ? $item->get_name() : $item['name'];
				$quantity = $item instanceof WC_Order_Item_Product ? $item->get_quantity() : $item['qty'];

				$item_desc = array();

				$product = ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_1() ) ? $item->get_product() : $order->get_product_from_item( $item );

				// add SKU to description if available
				if ( is_callable( array( $product, 'get_sku' ) ) && $product->get_sku() ) {
					$item_desc[] = sprintf( 'SKU: %s', $product->get_sku() );
				}

				$item_meta = SV_WC_Order_Compatibility::get_item_formatted_meta_data( $item, '_', true );

				if ( ! empty( $item_meta ) ) {

					foreach ( $item_meta as $meta ) {
						$item_desc[] = sprintf( '%s: %s', $meta['label'], $meta['value'] );
					}
				}

				$item_desc = implode( ', ', $item_desc );

				$line_item->id          = $id;
				$line_item->name        = htmlentities( $name, ENT_QUOTES, 'UTF-8', false );
				$line_item->description = htmlentities( $item_desc, ENT_QUOTES, 'UTF-8', false );
				$line_item->quantity    = $quantity;
				$line_item->item_total  = isset( $item['recurring_line_total'] ) ? $item['recurring_line_total'] : $order->get_item_total( $item );
				$line_item->line_total  = $order->get_line_total( $item );
				$line_item->meta        = $item_meta;
				$line_item->product     = is_object( $product ) ? $product : null;
				$line_item->item        = $item;

				$line_items[] = $line_item;
			}

			return $line_items;
		}


		/**
		 * Determines if an order contains only virtual products.
		 *
		 * @since 4.5.0
		 * @param \WC_Order $order the order object
		 * @return bool
		 */
		public static function is_order_virtual( WC_Order $order ) {

			$is_virtual = true;

			foreach ( $order->get_items() as $item ) {

				if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {
					$product = $item->get_product();
				} else {
					$product = $order->get_product_from_item( $item );
				}

				// once we've found one non-virtual product we know we're done, break out of the loop
				if ( $product && ! $product->is_virtual() ) {
					$is_virtual = false;
					break;
				}
			}

			return $is_virtual;
		}


		/**
		 * Safely get and trim data from $_POST
		 *
		 * @since 3.0.0
		 * @param string $key array key to get from $_POST array
		 * @return string value from $_POST or blank string if $_POST[ $key ] is not set
		 */
		public static function get_post( $key ) {

			if ( isset( $_POST[ $key ] ) ) {
				return trim( $_POST[ $key ] );
			}

			return '';
		}


		/**
		 * Safely get and trim data from $_REQUEST
		 *
		 * @since 3.0.0
		 * @param string $key array key to get from $_REQUEST array
		 * @return string value from $_REQUEST or blank string if $_REQUEST[ $key ] is not set
		 */
		public static function get_request( $key ) {

			if ( isset( $_REQUEST[ $key ] ) ) {
				return trim( $_REQUEST[ $key ] );
			}

			return '';
		}


		/**
		 * Get the count of notices added, either for all notices (default) or for one
 		 * particular notice type specified by $notice_type.
		 *
		 * WC notice functions are not available in the admin
		 *
		 * @since 3.0.2
		 * @param string $notice_type The name of the notice type - either error, success or notice. [optional]
		 * @return int
		 */
		public static function wc_notice_count( $notice_type = '' ) {

			if ( function_exists( 'wc_notice_count' ) ) {
				return wc_notice_count( $notice_type );
			}

			return 0;
		}


		/**
		 * Add and store a notice.
		 *
		 * WC notice functions are not available in the admin
		 *
		 * @since 3.0.2
		 * @param string $message The text to display in the notice.
		 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
		 */
		public static function wc_add_notice( $message, $notice_type = 'success' ) {

			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( $message, $notice_type );
			}
		}


		/**
		 * Print a single notice immediately
		 *
		 * WC notice functions are not available in the admin
		 *
		 * @since 3.0.2
		 * @param string $message The text to display in the notice.
		 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
		 */
		public static function wc_print_notice( $message, $notice_type = 'success' ) {

			if ( function_exists( 'wc_print_notice' ) ) {
				wc_print_notice( $message, $notice_type );
			}
		}


		/**
		 * Gets the full URL to the log file for a given $handle
		 *
		 * @since 4.0.0
		 * @param string $handle log handle
		 * @return string URL to the WC log file identified by $handle
		 */
		public static function get_wc_log_file_url( $handle ) {
			return admin_url( sprintf( 'admin.php?page=wc-status&tab=logs&log_file=%s-%s-log', $handle, sanitize_file_name( wp_hash( $handle ) ) ) );
		}


		/**
		 * Gets the current WordPress site name.
		 *
		 * This is helpful for retrieving the actual site name instead of the
		 * network name on multisite installations.
		 *
		 * @since 4.6.0
		 * @return string
		 */
		public static function get_site_name() {

			return ( is_multisite() ) ? get_blog_details()->blogname : get_bloginfo( 'name' );
		}


		/** JavaScript helper functions ***************************************/


		/**
		 * Enhanced search JavaScript (Select2)
		 *
		 * Enqueues JavaScript required for AJAX search with Select2.
		 *
		 * Example usage:
		 *    <input type="hidden" class="sv-wc-enhanced-search" name="category_ids" data-multiple="true" style="min-width: 300px;"
		 *       data-action="wc_cart_notices_json_search_product_categories"
		 *       data-nonce="<?php echo wp_create_nonce( 'search-categories' ); ?>"
		 *       data-request_data = "<?php echo esc_attr( json_encode( array( 'field_name' => 'something_exciting', 'default' => 'default_label' ) ) ) ?>"
		 *       data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'wc-cart-notices' ) ?>"
		 *       data-allow_clear="true"
		 *       data-selected="<?php
		 *          $json_ids    = array();
		 *          if ( isset( $notice->data['categories'] ) ) {
		 *             foreach ( $notice->data['categories'] as $value => $title ) {
		 *                $json_ids[ esc_attr( $value ) ] = esc_html( $title );
		 *             }
		 *          }
		 *          echo esc_attr( json_encode( $json_ids ) );
		 *       ?>"
		 *       value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>" />
		 *
		 * - `data-selected` can be a json encoded associative array like Array( 'key' => 'value' )
		 * - `value` should be a comma-separated list of selected keys
		 * - `data-request_data` can be used to pass any additional data to the AJAX request
		 *
		 * @codeCoverageIgnore no need to unit test this since it's mostly JS
		 * @since 3.1.0
		 */
		public static function render_select2_ajax() {

			if ( ! did_action( 'sv_wc_select2_ajax_rendered' ) ) {

				$javascript = "( function(){
					if ( ! $().select2 ) return;
				";

				// Ensure localized strings are used.
				$javascript .= "

					function getEnhancedSelectFormatString() {

						if ( 'undefined' !== typeof wc_select_params ) {
							wc_enhanced_select_params = wc_select_params;
						}

						if ( 'undefined' === typeof wc_enhanced_select_params ) {
							return {};
						}

						var formatString = {
							formatMatches: function( matches ) {
								if ( 1 === matches ) {
									return wc_enhanced_select_params.i18n_matches_1;
								}

								return wc_enhanced_select_params.i18n_matches_n.replace( '%qty%', matches );
							},
							formatNoMatches: function() {
								return wc_enhanced_select_params.i18n_no_matches;
							},
							formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
								return wc_enhanced_select_params.i18n_ajax_error;
							},
							formatInputTooShort: function( input, min ) {
								var number = min - input.length;

								if ( 1 === number ) {
									return wc_enhanced_select_params.i18n_input_too_short_1
								}

								return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', number );
							},
							formatInputTooLong: function( input, max ) {
								var number = input.length - max;

								if ( 1 === number ) {
									return wc_enhanced_select_params.i18n_input_too_long_1
								}

								return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', number );
							},
							formatSelectionTooBig: function( limit ) {
								if ( 1 === limit ) {
									return wc_enhanced_select_params.i18n_selection_too_long_1;
								}

								return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', number );
							},
							formatLoadMore: function( pageNumber ) {
								return wc_enhanced_select_params.i18n_load_more;
							},
							formatSearching: function() {
								return wc_enhanced_select_params.i18n_searching;
							}
						};

						return formatString;
					}
				";

				// Handle Select2 AJAX call according to Select2 version bundled with WC.
				if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_3_0() ) {

					$javascript .= "

						$( 'select.sv-wc-enhanced-search' ).filter( ':not(.enhanced)' ).each( function() {

							var select2_args = {
								allowClear:         $( this ).data( 'allow_clear' ) ? true : false,
								placeholder:        $( this ).data( 'placeholder' ),
								minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
								escapeMarkup:       function( m ) {
									return m;
								},
								ajax:               {
									url:            '" . esc_js( admin_url( 'admin-ajax.php' ) ) . "',
									dataType:       'json',
									cache:          true,
									delay:          250,
									data:           function( params ) {
										return {
											term:         params.term,
											request_data: $( this ).data( 'request_data' ) ? $( this ).data( 'request_data' ) : {},
											action:       $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
											security:     $( this ).data( 'nonce' )
										};
									},
									processResults: function( data, params ) {
										var terms = [];
										if ( data ) {
											$.each( data, function( id, text ) {
												terms.push( { id: id, text: text } );
											});
										}
										return { results: terms };
									}
								}
							};

							select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

							$( this ).select2( select2_args ).addClass( 'enhanced' );
						} );
					";

				} else {

					$javascript .= "

						$( ':input.sv-wc-enhanced-search' ).filter( ':not(.enhanced)' ).each( function() {

							var select2_args = {
								allowClear:         $( this ).data( 'allow_clear' ) ? true : false,
								placeholder:        $( this ).data( 'placeholder' ),
								minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
								escapeMarkup:       function( m ) {
									return m;
								},
								ajax:               {
									url:         '" . esc_js( admin_url( 'admin-ajax.php' ) ) . "',
									dataType:    'json',
									cache:       true,
									quietMillis: 250,
									data:        function( term, page ) {
										return {
											term:         term,
											request_data: $( this ).data( 'request_data' ) ? $( this ).data( 'request_data' ) : {},
											action:       $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
											security:     $( this ).data( 'nonce' )
										};
									},
									results:     function( data, page ) {
										var terms = [];
										if ( data ) {
											$.each( data, function( id, text ) {
												terms.push( { id: id, text: text } );
											});
										}
										return { results: terms };
									}
								}
							};

							if ( $( this ).data( 'multiple' ) === true ) {

								select2_args.multiple        = true;
								select2_args.initSelection   = function( element, callback ) {
									var data     = $.parseJSON( element.attr( 'data-selected' ) );
									var selected = [];

									$( element.val().split( ',' ) ).each( function( i, val ) {
										selected.push( { id: val, text: data[ val ] } );
									} );
									return callback( selected );
								};
								select2_args.formatSelection = function( data ) {
									return '<div class=\"selected-option\" data-id=\"' + data.id + '\">' + data.text + '</div>';
								};

							} else {

								select2_args.multiple        = false;
								select2_args.initSelection   = function( element, callback ) {
									var data = {id: element.val(), text: element.attr( 'data-selected' )};
									return callback( data );
								};
							}

							select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

							$( this ).select2( select2_args ).addClass( 'enhanced' );
						} );
					";
				}

				$javascript .= "} )();";

				wc_enqueue_js( $javascript );

				/**
				 * WC Select2 Ajax Rendered Action.
				 *
				 * Fired when an Ajax select2 is rendered.
				 *
				 * @since 3.1.0
				 */
				do_action( 'sv_wc_select2_ajax_rendered' );
			}
		}


		/** Framework translation functions ***********************************/


		/**
		 * Gettext `__()` wrapper for framework-translated strings
		 *
		 * Warning! This function should only be used if an existing
		 * translation from the framework is to be used. It should
		 * never be called for plugin-specific or untranslated strings!
		 * Untranslated = not registered via string literal.
		 *
		 * @since 4.1.0
		 * @param string $text
		 * @return string translated text
		 */
		public static function f__( $text ) {

			return __( $text, 'woocommerce-plugin-framework' );
		}


		/**
		 * Gettext `_e()` wrapper for framework-translated strings
		 *
		 * Warning! This function should only be used if an existing
		 * translation from the framework is to be used. It should
		 * never be called for plugin-specific or untranslated strings!
		 * Untranslated = not registered via string literal.
		 *
		 * @since 4.1.0
		 * @param string $text
		 */
		public static function f_e( $text ) {

			_e( $text, 'woocommerce-plugin-framework' );
		}


		/**
		 * Gettext `_x()` wrapper for framework-translated strings
		 *
		 * Warning! This function should only be used if an existing
		 * translation from the framework is to be used. It should
		 * never be called for plugin-specific or untranslated strings!
		 * Untranslated = not registered via string literal.
		 *
		 * @since 4.1.0
		 * @param string $text
		 * @return string translated text
		 */
		public static function f_x( $text, $context ) {

			return _x( $text, $context, 'woocommerce-plugin-framework' );
		}


		/** Misc functions ****************************************************/


		/**
		 * Convert a 2-character country code into its 3-character equivalent, or
		 * vice-versa, e.g.
		 *
		 * 1) given USA, returns US
		 * 2) given US, returns USA
		 *
		 * @since 4.2.0
		 * @param string $code ISO-3166-alpha-2 or ISO-3166-alpha-3 country code
		 * @return string country code
		 */
		public static function convert_country_code( $code ) {

			// ISO 3166-alpha-2 => ISO 3166-alpha3
			$countries = array(
					'AF' => 'AFG', 'AL' => 'ALB', 'DZ' => 'DZA', 'AD' => 'AND', 'AO' => 'AGO',
					'AG' => 'ATG', 'AR' => 'ARG', 'AM' => 'ARM', 'AU' => 'AUS', 'AT' => 'AUT',
					'AZ' => 'AZE', 'BS' => 'BHS', 'BH' => 'BHR', 'BD' => 'BGD', 'BB' => 'BRB',
					'BY' => 'BLR', 'BE' => 'BEL', 'BZ' => 'BLZ', 'BJ' => 'BEN', 'BT' => 'BTN',
					'BO' => 'BOL', 'BA' => 'BIH', 'BW' => 'BWA', 'BR' => 'BRA', 'BN' => 'BRN',
					'BG' => 'BGR', 'BF' => 'BFA', 'BI' => 'BDI', 'KH' => 'KHM', 'CM' => 'CMR',
					'CA' => 'CAN', 'CV' => 'CPV', 'CF' => 'CAF', 'TD' => 'TCD', 'CL' => 'CHL',
					'CN' => 'CHN', 'CO' => 'COL', 'KM' => 'COM', 'CD' => 'COD', 'CG' => 'COG',
					'CR' => 'CRI', 'CI' => 'CIV', 'HR' => 'HRV', 'CU' => 'CUB', 'CY' => 'CYP',
					'CZ' => 'CZE', 'DK' => 'DNK', 'DJ' => 'DJI', 'DM' => 'DMA', 'DO' => 'DOM',
					'EC' => 'ECU', 'EG' => 'EGY', 'SV' => 'SLV', 'GQ' => 'GNQ', 'ER' => 'ERI',
					'EE' => 'EST', 'ET' => 'ETH', 'FJ' => 'FJI', 'FI' => 'FIN', 'FR' => 'FRA',
					'GA' => 'GAB', 'GM' => 'GMB', 'GE' => 'GEO', 'DE' => 'DEU', 'GH' => 'GHA',
					'GR' => 'GRC', 'GD' => 'GRD', 'GT' => 'GTM', 'GN' => 'GIN', 'GW' => 'GNB',
					'GY' => 'GUY', 'HT' => 'HTI', 'HN' => 'HND', 'HU' => 'HUN', 'IS' => 'ISL',
					'IN' => 'IND', 'ID' => 'IDN', 'IR' => 'IRN', 'IQ' => 'IRQ', 'IE' => 'IRL',
					'IL' => 'ISR', 'IT' => 'ITA', 'JM' => 'JAM', 'JP' => 'JPN', 'JO' => 'JOR',
					'KZ' => 'KAZ', 'KE' => 'KEN', 'KI' => 'KIR', 'KP' => 'PRK', 'KR' => 'KOR',
					'KW' => 'KWT', 'KG' => 'KGZ', 'LA' => 'LAO', 'LV' => 'LVA', 'LB' => 'LBN',
					'LS' => 'LSO', 'LR' => 'LBR', 'LY' => 'LBY', 'LI' => 'LIE', 'LT' => 'LTU',
					'LU' => 'LUX', 'MK' => 'MKD', 'MG' => 'MDG', 'MW' => 'MWI', 'MY' => 'MYS',
					'MV' => 'MDV', 'ML' => 'MLI', 'MT' => 'MLT', 'MH' => 'MHL', 'MR' => 'MRT',
					'MU' => 'MUS', 'MX' => 'MEX', 'FM' => 'FSM', 'MD' => 'MDA', 'MC' => 'MCO',
					'MN' => 'MNG', 'ME' => 'MNE', 'MA' => 'MAR', 'MZ' => 'MOZ', 'MM' => 'MMR',
					'NA' => 'NAM', 'NR' => 'NRU', 'NP' => 'NPL', 'NL' => 'NLD', 'NZ' => 'NZL',
					'NI' => 'NIC', 'NE' => 'NER', 'NG' => 'NGA', 'NO' => 'NOR', 'OM' => 'OMN',
					'PK' => 'PAK', 'PW' => 'PLW', 'PA' => 'PAN', 'PG' => 'PNG', 'PY' => 'PRY',
					'PE' => 'PER', 'PH' => 'PHL', 'PL' => 'POL', 'PT' => 'PRT', 'QA' => 'QAT',
					'RO' => 'ROU', 'RU' => 'RUS', 'RW' => 'RWA', 'KN' => 'KNA', 'LC' => 'LCA',
					'VC' => 'VCT', 'WS' => 'WSM', 'SM' => 'SMR', 'ST' => 'STP', 'SA' => 'SAU',
					'SN' => 'SEN', 'RS' => 'SRB', 'SC' => 'SYC', 'SL' => 'SLE', 'SG' => 'SGP',
					'SK' => 'SVK', 'SI' => 'SVN', 'SB' => 'SLB', 'SO' => 'SOM', 'ZA' => 'ZAF',
					'ES' => 'ESP', 'LK' => 'LKA', 'SD' => 'SDN', 'SR' => 'SUR', 'SZ' => 'SWZ',
					'SE' => 'SWE', 'CH' => 'CHE', 'SY' => 'SYR', 'TJ' => 'TJK', 'TZ' => 'TZA',
					'TH' => 'THA', 'TL' => 'TLS', 'TG' => 'TGO', 'TO' => 'TON', 'TT' => 'TTO',
					'TN' => 'TUN', 'TR' => 'TUR', 'TM' => 'TKM', 'TV' => 'TUV', 'UG' => 'UGA',
					'UA' => 'UKR', 'AE' => 'ARE', 'GB' => 'GBR', 'US' => 'USA', 'UY' => 'URY',
					'UZ' => 'UZB', 'VU' => 'VUT', 'VA' => 'VAT', 'VE' => 'VEN', 'VN' => 'VNM',
					'YE' => 'YEM', 'ZM' => 'ZMB', 'ZW' => 'ZWE', 'TW' => 'TWN', 'CX' => 'CXR',
					'CC' => 'CCK', 'HM' => 'HMD', 'NF' => 'NFK', 'NC' => 'NCL', 'PF' => 'PYF',
					'YT' => 'MYT', 'GP' => 'GLP', 'PM' => 'SPM', 'WF' => 'WLF', 'TF' => 'ATF',
					'BV' => 'BVT', 'CK' => 'COK', 'NU' => 'NIU', 'TK' => 'TKL', 'GG' => 'GGY',
					'IM' => 'IMN', 'JE' => 'JEY', 'AI' => 'AIA', 'BM' => 'BMU', 'IO' => 'IOT',
					'VG' => 'VGB', 'KY' => 'CYM', 'FK' => 'FLK', 'GI' => 'GIB', 'MS' => 'MSR',
					'PN' => 'PCN', 'SH' => 'SHN', 'GS' => 'SGS', 'TC' => 'TCA', 'MP' => 'MNP',
					'PR' => 'PRI', 'AS' => 'ASM', 'UM' => 'UMI', 'GU' => 'GUM', 'VI' => 'VIR',
					'HK' => 'HKG', 'MO' => 'MAC', 'FO' => 'FRO', 'GL' => 'GRL', 'GF' => 'GUF',
					'MQ' => 'MTQ', 'RE' => 'REU', 'AX' => 'ALA', 'AW' => 'ABW', 'AN' => 'ANT',
					'SJ' => 'SJM', 'AC' => 'ASC', 'TA' => 'TAA', 'AQ' => 'ATA', 'CW' => 'CUW',
			);

			if ( 3 === strlen( $code ) ) {
				$countries = array_flip( $countries );
			}

			return isset( $countries[ $code ] ) ? $countries[ $code ] : $code;
		}


		/**
		 * Triggers a PHP error.
		 *
		 * This wrapper method ensures AJAX isn't broken in the process.
		 *
		 * @since 4.6.0
		 * @param string $message the error message
		 * @param int $type Optional. The error type. Defaults to E_USER_NOTICE
		 */
		public static function trigger_error( $message, $type = E_USER_NOTICE ) {

			if ( is_callable( 'is_ajax' ) && is_ajax() ) {

				switch ( $type ) {

					case E_USER_NOTICE:
						$prefix = 'Notice: ';
					break;

					case E_USER_WARNING:
						$prefix = 'Warning: ';
					break;

					default:
						$prefix = '';
				}

				error_log( $prefix . $message );

			} else {

				trigger_error( $message, $type );
			}
		}


	}

endif; // Class exists check
