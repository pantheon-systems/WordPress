<?php
/**
 * Sensor: WooCommerce
 *
 * WooCommerce sensor file.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Support for WooCommerce Plugin.
 *
 * 9000 User created a new product
 * 9001 User published a product
 * 9002 User created a new product category
 * 9003 User changed the category of a product
 * 9004 User modified the short description of a product
 * 9005 User modified the text of a product
 * 9006 User changed the URL of a product
 * 9007 User changed the Product Data of a product
 * 9008 User changed the date of a product
 * 9009 User changed the visibility of a product
 * 9010 User modified the published product
 * 9011 User modified the draft product
 * 9012 User moved a product to trash
 * 9013 User permanently deleted a product
 * 9014 User restored a product from the trash
 * 9015 User changed status of a product
 * 9016 User changed type of a price
 * 9017 User changed the SKU of a product
 * 9018 User changed the stock status of a product
 * 9019 User changed the stock quantity
 * 9020 User set a product type
 * 9021 User changed the weight of a product
 * 9022 User changed the dimensions of a product
 * 9023 User added the Downloadable File to a product
 * 9024 User Removed the Downloadable File from a product
 * 9025 User changed the name of a Downloadable File in a product
 * 9026 User changed the URL of the Downloadable File in a product
 * 9027 User changed the Weight Unit
 * 9028 User changed the Dimensions Unit
 * 9029 User changed the Base Location
 * 9030 User Enabled/Disabled taxes
 * 9031 User changed the currency
 * 9032 User Enabled/Disabled the use of coupons during checkout
 * 9033 User Enabled/Disabled guest checkout
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_WooCommerce extends WSAL_AbstractSensor {

	/**
	 * Old Post.
	 *
	 * @var WP_Post
	 */
	protected $_old_post = null;

	/**
	 * Old Post Link.
	 *
	 * @var string
	 */
	protected $_old_link = null;

	/**
	 * Old Post Categories.
	 *
	 * @var array
	 */
	protected $_old_cats = null;

	/**
	 * Old Product Data.
	 *
	 * @var array
	 */
	protected $_old_data = null;

	/**
	 * Old Product Stock Status.
	 *
	 * @var string
	 */
	protected $_old_stock_status = null;

	/**
	 * Old Product File Names.
	 *
	 * @var array
	 */
	protected $_old_file_names = array();

	/**
	 * Old Product File URLs.
	 *
	 * @var array
	 */
	protected $_old_file_urls = array();

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'admin_init', array( $this, 'EventAdminInit' ) );
		}
		add_action( 'post_updated', array( $this, 'EventChanged' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'EventDeleted' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'EventTrashed' ), 10, 1 );
		add_action( 'untrash_post', array( $this, 'EventUntrashed' ) );

		add_action( 'create_product_cat', array( $this, 'EventCategoryCreation' ), 10, 1 );
		/* add_action('edit_product_cat', array($this, 'EventCategoryChanged'), 10, 1); */
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		// Load old data, if applicable.
		$this->RetrieveOldData();
		$this->CheckSettingsChange();
	}

	/**
	 * Retrieve Old data.
	 *
	 * @global mixed $_POST post data
	 */
	protected function RetrieveOldData() {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		if ( isset( $post_array ) && isset( $post_array['post_ID'] )
			&& ! (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)
			&& ! (isset( $post_array['action'] ) && 'autosave' == $post_array['action'] )
		) {
			$post_id = intval( $post_array['post_ID'] );
			$this->_old_post = get_post( $post_id );
			$this->_old_link = get_post_permalink( $post_id, false, true );
			$this->_old_cats = $this->GetProductCategories( $this->_old_post );
			$this->_old_data = $this->GetProductData( $this->_old_post );
			$this->_old_stock_status = get_post_meta( $post_id, '_stock_status', true );

			$old_downloadable_files  = get_post_meta( $post_id, '_downloadable_files', true );
			if ( ! empty( $old_downloadable_files ) ) {
				foreach ( $old_downloadable_files as $file ) {
					array_push( $this->_old_file_names, $file['name'] );
					array_push( $this->_old_file_urls, $file['file'] );
				}
			}
		}
	}

	/**
	 * WooCommerce Product Updated.
	 *
	 * @param integer  $post_id - Post ID.
	 * @param stdClass $newpost - The new post.
	 * @param stdClass $oldpost - The old post.
	 */
	public function EventChanged( $post_id, $newpost, $oldpost ) {
		if ( $this->CheckWooCommerce( $oldpost ) ) {
			$changes = 0 + $this->EventCreation( $oldpost, $newpost );
			if ( ! $changes ) {
				// Change Categories.
				$changes = $this->CheckCategoriesChange( $this->_old_cats, $this->GetProductCategories( $newpost ), $oldpost, $newpost );
			}
			if ( ! $changes ) {
				// Change Short description, Text, URL, Product Data, Date, Visibility, etc.
				$changes = 0
					+ $this->CheckShortDescriptionChange( $oldpost, $newpost )
					+ $this->CheckTextChange( $oldpost, $newpost )
					+ $this->CheckProductDataChange( $this->_old_data, $newpost )
					+ $this->CheckDateChange( $oldpost, $newpost )
					+ $this->CheckVisibilityChange( $oldpost )
					+ $this->CheckStatusChange( $oldpost, $newpost )
					+ $this->CheckPriceChange( $oldpost )
					+ $this->CheckSKUChange( $oldpost )
					+ $this->CheckStockStatusChange( $oldpost )
					+ $this->CheckStockQuantityChange( $oldpost )
					+ $this->CheckTypeChange( $oldpost, $newpost )
					+ $this->CheckWeightChange( $oldpost )
					+ $this->CheckDimensionsChange( $oldpost )
					+ $this->CheckDownloadableFileChange( $oldpost );
			}
			if ( ! $changes ) {
				// Change Permalink.
				$changes = $this->CheckPermalinkChange( $this->_old_link, get_post_permalink( $post_id, false, true ), $newpost );
				if ( ! $changes ) {
					// If no one of the above changes happen.
					$this->CheckModifyChange( $oldpost, $newpost );
				}
			}
		}
	}

	/**
	 * WooCommerce Product Created.
	 *
	 * Trigger events 9000, 9001.
	 *
	 * @param object $old_post - Old Product.
	 * @param object $new_post - New Product.
	 */
	private function EventCreation( $old_post, $new_post ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$original = isset( $post_array['original_post_status'] ) ? $post_array['original_post_status'] : '';
		if ( 'draft' == $original && 'draft' == $new_post->post_status ) {
			return 0;
		}

		if ( 'draft' == $old_post->post_status || 'auto-draft' == $original ) {
			if ( 'product' == $old_post->post_type ) {
				$editor_link = $this->GetEditorLink( $new_post );
				if ( 'draft' == $new_post->post_status ) {
					$this->plugin->alerts->Trigger(
						9000, array(
							'ProductTitle' => $new_post->post_title,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					return 1;
				} elseif ( 'publish' == $new_post->post_status ) {
					$this->plugin->alerts->Trigger(
						9001, array(
							'ProductTitle' => $new_post->post_title,
							'ProductUrl' => get_post_permalink( $new_post->ID ),
							$editor_link['name'] => $editor_link['value'],
						)
					);
					return 1;
				}
			}
		}
		return 0;
	}

	/**
	 * Trigger events 9002
	 *
	 * @param int|WP_Term $term_id - Term ID.
	 */
	public function EventCategoryCreation( $term_id = null ) {
		$term = get_term( $term_id );
		if ( ! empty( $term ) ) {
			$this->plugin->alerts->Trigger(
				9002, array(
					'CategoryName' => $term->name,
					'Slug' => $term->slug,
				)
			);
		}
	}

	/**
	 * Not implemented
	 *
	 * @param int|WP_Term $term_id - Term ID.
	 */
	/**
	public function EventCategoryChanged( $term_id = null ) {
		$old_term = get_term( $term_id );
		if ( isset( $_POST['taxonomy'] ) ) {
			// new $term in $_POST
		}
	} */

	/**
	 * Trigger events 9003
	 *
	 * @param array  $old_cats - Old Categories.
	 * @param array  $new_cats - New Categories.
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckCategoriesChange( $old_cats, $new_cats, $oldpost, $newpost ) {
		if ( 'trash' == $newpost->post_status || 'trash' == $oldpost->post_status ) {
			return 0;
		}
		$old_cats = is_array( $old_cats ) ? implode( ', ', $old_cats ) : $old_cats;
		$new_cats = is_array( $new_cats ) ? implode( ', ', $new_cats ) : $new_cats;
		if ( $old_cats != $new_cats ) {
			$editor_link = $this->GetEditorLink( $newpost );
			$this->plugin->alerts->Trigger(
				9003, array(
					'ProductTitle' => $newpost->post_title,
					'OldCategories' => $old_cats ? $old_cats : 'no categories',
					'NewCategories' => $new_cats ? $new_cats : 'no categories',
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9004
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckShortDescriptionChange( $oldpost, $newpost ) {
		if ( 'auto-draft' == $oldpost->post_status ) {
			return 0;
		}
		if ( $oldpost->post_excerpt != $newpost->post_excerpt ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9004, array(
					'ProductTitle' => $oldpost->post_title,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9005
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckTextChange( $oldpost, $newpost ) {
		if ( 'auto-draft' == $oldpost->post_status ) {
			return 0;
		}
		if ( $oldpost->post_content != $newpost->post_content ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9005, array(
					'ProductTitle' => $oldpost->post_title,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9006
	 *
	 * @param string $old_link - Old product link.
	 * @param string $new_link - New product link.
	 * @param object $post - Product object.
	 * @return int
	 */
	protected function CheckPermalinkChange( $old_link, $new_link, $post ) {
		if ( ($old_link && $new_link) && ($old_link != $new_link) ) {
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger(
				9006, array(
					'ProductTitle' => $post->post_title,
					'OldUrl' => $old_link,
					'NewUrl' => $new_link,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9007
	 *
	 * @param array  $old_data - Product Data.
	 * @param object $post - Product object.
	 * @return int
	 */
	protected function CheckProductDataChange( $old_data, $post ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		if ( isset( $post_array['product-type'] ) ) {
			$old_data = is_array( $old_data ) ? implode( ', ', $old_data ) : $old_data;
			$new_data = $post_array['product-type'];
			if ( $old_data != $new_data ) {
				$editor_link = $this->GetEditorLink( $post );
				$this->plugin->alerts->Trigger(
					9007, array(
						'ProductTitle' => $post->post_title,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
		return 0;
	}

	/**
	 * Trigger events 9008
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckDateChange( $oldpost, $newpost ) {
		if ( 'draft' == $oldpost->post_status || 'auto-draft' == $oldpost->post_status ) {
			return 0;
		}
		$from = strtotime( $oldpost->post_date );
		$to = strtotime( $newpost->post_date );
		if ( $from != $to ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9008, array(
					'ProductTitle' => $oldpost->post_title,
					'OldDate' => $oldpost->post_date,
					'NewDate' => $newpost->post_date,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9009
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckVisibilityChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$old_visibility = isset( $post_array['hidden_post_visibility'] ) ? $post_array['hidden_post_visibility'] : null;
		$new_visibility = isset( $post_array['visibility'] ) ? $post_array['visibility'] : null;

		if ( 'password' == $old_visibility ) {
			$old_visibility = __( 'Password Protected', 'wp-security-audit-log' );
		} else {
			$old_visibility = ucfirst( $old_visibility );
		}

		if ( 'password' == $new_visibility ) {
			$new_visibility = __( 'Password Protected', 'wp-security-audit-log' );
		} else {
			$new_visibility = ucfirst( $new_visibility );
		}

		if ( ($old_visibility && $new_visibility) && ($old_visibility != $new_visibility) ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9009, array(
					'ProductTitle' => $oldpost->post_title,
					'OldVisibility' => $old_visibility,
					'NewVisibility' => $new_visibility,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9010, 9011
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckModifyChange( $oldpost, $newpost ) {
		if ( 'trash' == $newpost->post_status ) {
			return 0;
		}
		$editor_link = $this->GetEditorLink( $oldpost );
		if ( 'publish' == $oldpost->post_status ) {
			$this->plugin->alerts->Trigger(
				9010, array(
					'ProductTitle' => $oldpost->post_title,
					'ProductUrl' => get_post_permalink( $oldpost->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
		} elseif ( 'draft' == $oldpost->post_status ) {
			$this->plugin->alerts->Trigger(
				9011, array(
					'ProductTitle' => $oldpost->post_title,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Moved to Trash 9012
	 *
	 * @param int $post_id - Product ID.
	 */
	public function EventTrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckWooCommerce( $post ) ) {
			$this->plugin->alerts->Trigger(
				9012, array(
					'ProductTitle' => $post->post_title,
					'ProductUrl' => get_post_permalink( $post->ID ),
				)
			);
		}
	}

	/**
	 * Permanently deleted 9013
	 *
	 * @param int $post_id - Product ID.
	 */
	public function EventDeleted( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckWooCommerce( $post ) ) {
			$this->plugin->alerts->Trigger(
				9013, array(
					'ProductTitle' => $post->post_title,
				)
			);
		}
	}

	/**
	 * Restored from Trash 9014
	 *
	 * @param int $post_id - Product ID.
	 */
	public function EventUntrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( $this->CheckWooCommerce( $post ) ) {
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger(
				9014, array(
					'ProductTitle' => $post->post_title,
					$editor_link['name'] => $editor_link['value'],
				)
			);
		}
	}

	/**
	 * Trigger events 9015
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckStatusChange( $oldpost, $newpost ) {
		if ( 'draft' == $oldpost->post_status || 'auto-draft' == $oldpost->post_status ) {
			return 0;
		}
		if ( $oldpost->post_status != $newpost->post_status ) {
			if ( 'trash' != $oldpost->post_status && 'trash' != $newpost->post_status ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9015, array(
						'ProductTitle' => $oldpost->post_title,
						'OldStatus' => $oldpost->post_status,
						'NewStatus' => $newpost->post_status,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
		return 0;
	}

	/**
	 * Trigger events 9016
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckPriceChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$result = 0;
		$old_price = get_post_meta( $oldpost->ID, '_regular_price', true );
		$old_sale_price = get_post_meta( $oldpost->ID, '_sale_price', true );
		$new_price = isset( $post_array['_regular_price'] ) ? $post_array['_regular_price'] : null;
		$new_sale_price = isset( $post_array['_sale_price'] ) ? $post_array['_sale_price'] : null;

		if ( ($new_price) && ($old_price != $new_price) ) {
			$result = $this->EventPrice( $oldpost, 'Regular price', $old_price, $new_price );
		}
		if ( ($new_sale_price) && ($old_sale_price != $new_sale_price) ) {
			$result = $this->EventPrice( $oldpost, 'Sale price', $old_sale_price, $new_sale_price );
		}
		return $result;
	}

	/**
	 * Group the Price changes in one function
	 *
	 * @param object $oldpost - Old Product Object.
	 * @param string $type - Price Type.
	 * @param int    $old_price - Old Product Price.
	 * @param int    $new_price - New Product Price.
	 * @return int
	 */
	private function EventPrice( $oldpost, $type, $old_price, $new_price ) {
		$currency = $this->GetCurrencySymbol( $this->GetConfig( 'currency' ) );
		$editor_link = $this->GetEditorLink( $oldpost );
		$this->plugin->alerts->Trigger(
			9016, array(
				'ProductTitle' => $oldpost->post_title,
				'PriceType' => $type,
				'OldPrice' => ( ! empty( $old_price ) ? $currency . $old_price : 0),
				'NewPrice' => $currency . $new_price,
				$editor_link['name'] => $editor_link['value'],
			)
		);
		return 1;
	}

	/**
	 * Trigger events 9017
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckSKUChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$old_sku = get_post_meta( $oldpost->ID, '_sku', true );
		$new_sku = isset( $post_array['_sku'] ) ? $post_array['_sku'] : null;

		if ( ($new_sku) && ($old_sku != $new_sku) ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9017, array(
					'ProductTitle' => $oldpost->post_title,
					'OldSku' => ( ! empty( $old_sku ) ? $old_sku : 0),
					'NewSku' => $new_sku,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9018
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckStockStatusChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$old_status = $this->_old_stock_status;
		$new_status = isset( $post_array['_stock_status'] ) ? $post_array['_stock_status'] : null;

		if ( ($old_status && $new_status) && ($old_status != $new_status) ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9018, array(
					'ProductTitle' => $oldpost->post_title,
					'OldStatus' => $this->GetStockStatusName( $old_status ),
					'NewStatus' => $this->GetStockStatusName( $new_status ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9019
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckStockQuantityChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$old_value = get_post_meta( $oldpost->ID, '_stock', true );
		$new_value = isset( $post_array['_stock'] ) ? $post_array['_stock'] : null;

		if ( ($new_value) && ($old_value != $new_value) ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9019, array(
					'ProductTitle' => $oldpost->post_title,
					'OldValue' => ( ! empty( $old_value ) ? $old_value : 0),
					'NewValue' => $new_value,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9020
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckTypeChange( $oldpost, $newpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$result = 0;
		if ( 'trash' != $oldpost->post_status && 'trash' != $newpost->post_status ) {
			$old_virtual = get_post_meta( $oldpost->ID, '_virtual', true );
			$new_virtual = isset( $post_array['_virtual'] ) ? 'yes' : 'no';
			$old_downloadable  = get_post_meta( $oldpost->ID, '_downloadable', true );
			$new_downloadable = isset( $post_array['_downloadable'] ) ? 'yes' : 'no';

			if ( ($old_virtual && $new_virtual) && ($old_virtual != $new_virtual) ) {
				$type = ( 'no' == $new_virtual ) ? 'Non Virtual' : 'Virtual';
				$result = $this->EventType( $oldpost, $type );
			}
			if ( ($old_downloadable && $new_downloadable) && ($old_downloadable != $new_downloadable) ) {
				$type = ( 'no' == $new_downloadable ) ? 'Non Downloadable' : 'Downloadable';
				$result = $this->EventType( $oldpost, $type );
			}
		}
		return $result;
	}

	/**
	 * Group the Type changes in one function.
	 *
	 * @param object $oldpost - Old product object.
	 * @param string $type - Product Type.
	 * @return int
	 */
	private function EventType( $oldpost, $type ) {
		$editor_link = $this->GetEditorLink( $oldpost );
		$this->plugin->alerts->Trigger(
			9020, array(
				'ProductTitle' => $oldpost->post_title,
				'Type' => $type,
				$editor_link['name'] => $editor_link['value'],
			)
		);
		return 1;
	}

	/**
	 * Trigger events 9021
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckWeightChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$old_weight = get_post_meta( $oldpost->ID, '_weight', true );
		$new_weight = isset( $post_array['_weight'] ) ? $post_array['_weight'] : null;

		if ( ($new_weight) && ($old_weight != $new_weight) ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9021, array(
					'ProductTitle' => $oldpost->post_title,
					'OldWeight' => ( ! empty( $old_weight ) ? $old_weight : 0),
					'NewWeight' => $new_weight,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9022
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckDimensionsChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$result = 0;
		$old_length = get_post_meta( $oldpost->ID, '_length', true );
		$new_length = isset( $post_array['_length'] ) ? $post_array['_length'] : null;
		$old_width  = get_post_meta( $oldpost->ID, '_width', true );
		$new_width  = isset( $post_array['_width'] ) ? $post_array['_width'] : null;
		$old_height = get_post_meta( $oldpost->ID, '_height', true );
		$new_height = isset( $post_array['_height'] ) ? $post_array['_height'] : null;

		if ( ($new_length) && ($old_length != $new_length) ) {
			$result = $this->EventDimension( $oldpost, 'Length', $old_length, $new_length );
		}
		if ( ($new_width) && ($old_width != $new_width) ) {
			$result = $this->EventDimension( $oldpost, 'Width', $old_width, $new_width );
		}
		if ( ($new_height) && ($old_height != $new_height) ) {
			$result = $this->EventDimension( $oldpost, 'Height', $old_height, $new_height );
		}
		return $result;
	}

	/**
	 * Group the Dimension changes in one function.
	 *
	 * @param object $oldpost - Old Product object.
	 * @param string $type - Dimension type.
	 * @param string $old_dimension - Old dimension.
	 * @param string $new_dimension - New dimension.
	 * @return int
	 */
	private function EventDimension( $oldpost, $type, $old_dimension, $new_dimension ) {
		$dimension_unit = $this->GetConfig( 'dimension_unit' );
		$editor_link = $this->GetEditorLink( $oldpost );
		$this->plugin->alerts->Trigger(
			9022, array(
				'ProductTitle' => $oldpost->post_title,
				'DimensionType' => $type,
				'OldDimension' => ( ! empty( $old_dimension ) ? $dimension_unit . ' ' . $old_dimension : 0),
				'NewDimension' => $dimension_unit . ' ' . $new_dimension,
				$editor_link['name'] => $editor_link['value'],
			)
		);
		return 1;
	}

	/**
	 * Trigger events 9023, 9024, 9025, 9026
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckDownloadableFileChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		$result = 0;
		$is_url_changed = false;
		$is_name_changed = false;
		$new_file_names = ! empty( $post_array['_wc_file_names'] ) ? $post_array['_wc_file_names'] : array();
		$new_file_urls = ! empty( $post_array['_wc_file_urls'] ) ? $post_array['_wc_file_urls'] : array();
		$editor_link = $this->GetEditorLink( $oldpost );
		$added_urls = array_diff( $new_file_urls, $this->_old_file_urls );

		// Added files to the product.
		if ( count( $added_urls ) > 0 ) {
			// If the file has only changed URL.
			if ( count( $new_file_urls ) == count( $this->_old_file_urls ) ) {
				$is_url_changed = true;
			} else {
				foreach ( $added_urls as $key => $url ) {
					$this->plugin->alerts->Trigger(
						9023, array(
							'ProductTitle' => $oldpost->post_title,
							'FileName' => $new_file_names[ $key ],
							'FileUrl' => $url,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$result = 1;
			}
		}

		$removed_urls = array_diff( $this->_old_file_urls, $new_file_urls );
		// Removed files from the product.
		if ( count( $removed_urls ) > 0 ) {
			// If the file has only changed URL.
			if ( count( $new_file_urls ) == count( $this->_old_file_urls ) ) {
				$is_url_changed = true;
			} else {
				foreach ( $removed_urls as $key => $url ) {
					$this->plugin->alerts->Trigger(
						9024, array(
							'ProductTitle' => $oldpost->post_title,
							'FileName' => $this->_old_file_names[ $key ],
							'FileUrl' => $url,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$result = 1;
			}
		}

		$added_names = array_diff( $new_file_names, $this->_old_file_names );
		if ( count( $added_names ) > 0 ) {
			// If the file has only changed Name.
			if ( count( $new_file_names ) == count( $this->_old_file_names ) ) {
				foreach ( $added_names as $key => $name ) {
					$this->plugin->alerts->Trigger(
						9025, array(
							'ProductTitle' => $oldpost->post_title,
							'OldName' => $this->_old_file_names[ $key ],
							'NewName' => $name,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$result = 1;
			}
		}

		if ( $is_url_changed ) {
			foreach ( $added_urls as $key => $url ) {
				$this->plugin->alerts->Trigger(
					9026, array(
						'ProductTitle' => $oldpost->post_title,
						'FileName' => $new_file_names[ $key ],
						'OldUrl' => $removed_urls[ $key ],
						'NewUrl' => $url,
						$editor_link['name'] => $editor_link['value'],
					)
				);
			}
			$result = 1;
		}
		return $result;
	}

	/**
	 * Trigger events Settings: 9027, 9028, 9029, 9030, 9031, 9032, 9033
	 */
	protected function CheckSettingsChange() {
		// Filter POST and GET global arrays.
		$post_array = filter_input_array( INPUT_POST );
		$get_array = filter_input_array( INPUT_GET );

		if ( isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'woocommerce-settings' ) ) {
			return false;
		}

		if ( isset( $get_array['page'] ) && 'wc-settings' == $get_array['page'] ) {
			if ( isset( $get_array['tab'] ) && 'products' == $get_array['tab'] ) {
				if ( isset( $post_array['woocommerce_weight_unit'] ) ) {
					$old_unit = $this->GetConfig( 'weight_unit' );
					$new_unit = $post_array['woocommerce_weight_unit'];
					if ( $old_unit != $new_unit ) {
						$this->plugin->alerts->Trigger(
							9027, array(
								'OldUnit' => $old_unit,
								'NewUnit' => $new_unit,
							)
						);
					}
				}
				if ( isset( $post_array['woocommerce_dimension_unit'] ) ) {
					$old_unit = $this->GetConfig( 'dimension_unit' );
					$new_unit = $post_array['woocommerce_dimension_unit'];
					if ( $old_unit != $new_unit ) {
						$this->plugin->alerts->Trigger(
							9028, array(
								'OldUnit' => $old_unit,
								'NewUnit' => $new_unit,
							)
						);
					}
				}
			} elseif ( isset( $get_array['tab'] ) && 'checkout' == $get_array['tab'] ) {
				if ( ! empty( $post_array ) && '' == $get_array['section'] ) {
					$old_enable_coupons = $this->GetConfig( 'enable_coupons' );
					$new_enable_coupons = isset( $post_array['woocommerce_enable_coupons'] ) ? 'yes' : 'no';
					if ( $old_enable_coupons != $new_enable_coupons ) {
						$status = ( 'yes' == $new_enable_coupons ) ? 'Enabled' : 'Disabled';
						$this->plugin->alerts->Trigger(
							9032, array(
								'Status' => $status,
							)
						);
					}
					$old_enable_guest_checkout = $this->GetConfig( 'enable_guest_checkout' );
					$new_enable_guest_checkout = isset( $post_array['woocommerce_enable_guest_checkout'] ) ? 'yes' : 'no';
					if ( $old_enable_guest_checkout != $new_enable_guest_checkout ) {
						$status = ( 'yes' == $new_enable_guest_checkout ) ? 'Enabled' : 'Disabled';
						$this->plugin->alerts->Trigger(
							9033, array(
								'Status' => $status,
							)
						);
					}
				} elseif ( ! empty( $post_array ) && 'cod' === $get_array['section'] ) {
					$old_cash_on_delivery = $this->GetConfig( 'cod_settings' );
					$old_cash_on_delivery = isset( $old_cash_on_delivery['enabled'] ) ? $old_cash_on_delivery['enabled'] : '';
					$new_cash_on_delivery = isset( $post_array['woocommerce_cod_enabled'] ) ? 'yes' : 'no';
					if ( $old_cash_on_delivery !== $new_cash_on_delivery ) {
						$status = ( 'yes' === $new_cash_on_delivery ) ? 'Enabled' : 'Disabled';
						$this->plugin->alerts->Trigger(
							9034, array(
								'Status' => $status,
							)
						);
					}
				}
			} else {
				if ( isset( $post_array['woocommerce_default_country'] ) ) {
					$old_location = $this->GetConfig( 'default_country' );
					$new_location = $post_array['woocommerce_default_country'];
					if ( $old_location != $new_location ) {
						$this->plugin->alerts->Trigger(
							9029, array(
								'OldLocation' => $old_location,
								'NewLocation' => $new_location,
							)
						);
					}
					$old_calc_taxes = $this->GetConfig( 'calc_taxes' );
					$new_calc_taxes = isset( $post_array['woocommerce_calc_taxes'] ) ? 'yes' : 'no';
					if ( $old_calc_taxes != $new_calc_taxes ) {
						$status = ( 'yes' == $new_calc_taxes ) ? 'Enabled' : 'Disabled';
						$this->plugin->alerts->Trigger(
							9030, array(
								'Status' => $status,
							)
						);
					}
				}
				if ( isset( $post_array['woocommerce_currency'] ) ) {
					$old_currency = $this->GetConfig( 'currency' );
					$new_currency = $post_array['woocommerce_currency'];
					if ( $old_currency != $new_currency ) {
						$this->plugin->alerts->Trigger(
							9031, array(
								'OldCurrency' => $old_currency,
								'NewCurrency' => $new_currency,
							)
						);
					}
				}
			}
		}
	}

	/**
	 * Get Stock Status Name.
	 *
	 * @param string $slug - Stock slug.
	 * @return string
	 */
	private function GetStockStatusName( $slug ) {
		if ( 'instock' == $slug ) {
			return __( 'In stock', 'wp-security-audit-log' );
		} elseif ( 'outofstock' == $slug ) {
			return __( 'Out of stock', 'wp-security-audit-log' );
		}
	}

	/**
	 * Return: Product Categories.
	 *
	 * @param object $post - Product post object.
	 * @return array
	 */
	protected function GetProductCategories( $post ) {
		return wp_get_post_terms(
			$post->ID, 'product_cat', array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Return: Product Data.
	 *
	 * @param object $post - Product post object.
	 * @return array
	 */
	protected function GetProductData( $post ) {
		return wp_get_post_terms(
			$post->ID, 'product_type', array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Get the config setting
	 *
	 * @param string $option_name - Option Name.
	 * @return string
	 */
	private function GetConfig( $option_name ) {
		$fn = $this->IsMultisite() ? 'get_site_option' : 'get_option';
		return $fn( 'woocommerce_' . $option_name );
	}

	/**
	 * Check post type.
	 *
	 * @param stdClass $post - Post.
	 * @return bool
	 */
	private function CheckWooCommerce( $post ) {
		switch ( $post->post_type ) {
			case 'product':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Get editor link.
	 *
	 * @param stdClass $post - The post.
	 * @return array $editor_link - Name and value link.
	 */
	private function GetEditorLink( $post ) {
		$name = 'EditorLinkProduct';
		$value = get_edit_post_link( $post->ID );
		$editor_link = array(
			'name' => $name,
			'value' => $value,
		);
		return $editor_link;
	}

	/**
	 * Get Currency symbol.
	 *
	 * @param string $currency - Currency (default: '').
	 * @return string
	 */
	private function GetCurrencySymbol( $currency = '' ) {
		$symbols = array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => '&fnof;',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x10da;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'Kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => 'KZT',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;. &#x645;.',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'L',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRO' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => '&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/.',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#x434;&#x438;&#x43d;.',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STD' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'Fr',
			'XCD' => '&#36;',
			'XOF' => 'Fr',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
		$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

		return $currency_symbol;
	}
}
