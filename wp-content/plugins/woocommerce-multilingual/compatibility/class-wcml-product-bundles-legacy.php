<?php
class WCML_Product_Bundles_Legacy{

	/**
	 * @var WPML_Element_Translation_Package
	 */
	private $tp;
	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;

	/**
	 * WCML_Product_Bundles constructor.
	 * @param SitePress $sitepress
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param WPML_Element_Translation_Package $tp
	 */
	function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml, WPML_Element_Translation_Package $tp ) {
		$this->sitepress        = $sitepress;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->tp               = $tp;
	}

	public function add_hooks(){

		add_action( 'wcml_gui_additional_box_html', array( $this, 'custom_box_html' ), 10, 3 );
		add_filter( 'wcml_gui_additional_box_data', array( $this, 'custom_box_html_data' ), 10, 4 );
		add_action( 'wcml_after_duplicate_product_post_meta', array( $this, 'sync_bundled_ids' ), 10, 2 );
		add_action( 'wcml_update_extra_fields', array( $this, 'bundle_update' ), 10, 4 );
		add_action( 'woocommerce_get_cart_item_from_session', array( $this, 'resync_bundle' ), 5, 3 );
		add_filter( 'woocommerce_cart_loaded_from_session', array( $this, 'resync_bundle_clean' ), 10 );

		if ( $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'WCML_VERSION' ), '3.7.2', '>' ) ) {
			add_filter( 'option_wpml_config_files_arr', array( $this, 'make__bundle_data_not_translatable_by_default' ), 0 );
		}

		if( is_admin() ){

			add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_bundle_data_translation_package' ), 10, 2 );
			add_action( 'wpml_translation_job_saved',   array( $this, 'save_bundle_data_translation' ), 10, 3 );

			add_filter( 'wcml_do_not_display_custom_fields_for_product', array( $this, 'replace_tm_editor_custom_fields_with_own_sections' ) );
		}


	}

	function make__bundle_data_not_translatable_by_default( $wpml_config_array ){

		if( isset( $wpml_config_array->plugins[ 'WooCommerce Product Bundles' ] ) ){
			$wpml_config_array->plugins[ 'WooCommerce Product Bundles' ] =
				str_replace(
					'<custom-field action="translate">_bundle_data</custom-field>',
					'<custom-field action="nothing">_bundle_data</custom-field>',
					$wpml_config_array->plugins[ 'WooCommerce Product Bundles' ] );
		}

		return $wpml_config_array;
	}

	// Sync Bundled product '_bundle_data' with translated values when the product is duplicated
	function sync_bundled_ids( $original_product_id, $trnsl_product_id ){

		$bundle_data_array = maybe_unserialize( get_post_meta( $original_product_id, '_bundle_data', true ) );
		if( $bundle_data_array ){
			$lang = $this->sitepress->get_language_for_element( $trnsl_product_id, 'post_product' );
			$tr_bundle_meta = maybe_unserialize( get_post_meta( $trnsl_product_id, '_bundle_data', true ) );

			$i = 2;
			foreach( $bundle_data_array as $bundle_key => $bundle_data ){

				$key_data = $this->translate_bundle_key( $bundle_key, $lang );
				$tr_id = $key_data->tr_id;
				$tr_bundle_key = $key_data->tr_key;

				$tr_bundle[ $tr_bundle_key ] = $bundle_data;
				$tr_bundle[ $tr_bundle_key ][ 'product_id' ] = $tr_id;
				if( isset( $bundle_data[ 'product_title' ] ) ){
					if( $bundle_data[ 'override_title' ] == 'yes' ){
						$tr_bundle[ $tr_bundle_key ][ 'product_title' ] =
							isset( $tr_bundle_meta[ $tr_bundle_key ][ 'product_title' ] ) ?
								$tr_bundle_meta[ $tr_bundle_key ][ 'product_title' ] :
								'';
					}else{
						$tr_title= get_the_title( $tr_id );
						$tr_bundle[ $tr_bundle_key ][ 'product_title' ] = $tr_title;
					}
				}
				if( isset( $bundle_data[ 'product_description' ] ) ){
					if( $bundle_data[ 'override_description' ] == 'yes' ){
						$tr_bundle[ $tr_bundle_key ][ 'product_description' ] =
							isset( $tr_bundle_meta[ $tr_bundle_key ][ 'product_description' ] ) ?
								$tr_bundle_meta[ $tr_bundle_key ][ 'product_description' ] :
								'';
					}else{
						$tr_prod = get_post( $tr_id );
						$tr_desc = $tr_prod->post_excerpt;
						$tr_bundle[ $tr_bundle_key ][ 'product_description' ] = $tr_desc;
					}
				}
				if( isset( $bundle_data[ 'filter_variations' ] ) && $bundle_data[ 'filter_variations' ] == 'yes' ){
					$allowed_var = $bundle_data[ 'allowed_variations' ];
					foreach( $allowed_var as $key => $var_id ){
						$tr_var_id = apply_filters( 'translate_object_id', $var_id, get_post_type( $var_id ), true, $lang );
						$tr_bundle[ $tr_bundle_key ][ 'allowed_variations' ][ $key ] = $tr_var_id;
					}
				}
				if( isset( $bundle_data[ 'bundle_defaults' ] ) && !empty( $bundle_data[ 'bundle_defaults' ] ) ){
					foreach( $bundle_data[ 'bundle_defaults' ] as $tax => $term_slug ){

						$term_id = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $tax, $term_slug );
						if( $term_id ){
							// Global Attribute
							$tr_def_id = apply_filters( 'translate_object_id', $term_id, $tax, true, $lang );
							$tr_term = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $tr_def_id, $tax );
							$tr_bundle[ $tr_bundle_key ][ 'bundle_defaults' ][ $tax ] = $tr_term->slug;
						}else{
							// Custom Attribute
							$args = array(
								'post_type' => 'product_variation',
								'meta_key' => 'attribute_'.$tax,
								'meta_value' => $term_slug,
								'meta_compare' => '='
							);
							$variationloop = new WP_Query( $args );
							while ( $variationloop->have_posts() ) : $variationloop->the_post();
								$tr_var_id = apply_filters( 'translate_object_id', get_the_ID(), 'product_variation', true, $lang );
								$tr_meta = get_post_meta( $tr_var_id, 'attribute_'.$tax , true );
								$tr_bundle[ $tr_bundle_key ][ 'bundle_defaults' ][ $tax ] = $tr_meta;
							endwhile;
						}
					}
				}
			}
			update_post_meta( $trnsl_product_id, '_bundle_data', $tr_bundle );

			return $tr_bundle;
		}

	}


	public function translate_bundle_key( $key, $lang, $return_original_if_missing = true ) {
		$key_parts = explode( '_', $key );
		$has_multiple_products = count( $key_parts ) > 1;

		$data = new stdClass;
		$data->id = $has_multiple_products ? $key_parts[ 0 ] : $key;
		$data->tr_id = apply_filters( 'translate_object_id', $data->id, get_post_type( $data->id ), $return_original_if_missing, $lang );
		$data->tr_key = $data->tr_id . ( $has_multiple_products ? '_' . $key_parts[ 1 ] : '' );

		return $data;
	}

	// Update Bundled products title and descritpion after saving the translation
	function bundle_update( $original_product_id, $tr_id, $data, $lang ){

		$tr_bundle_data = array();
		$tr_bundle_data = maybe_unserialize( get_post_meta($tr_id,'_bundle_data', true ) );

		$bundle_data = maybe_unserialize( get_post_meta( $original_product_id, '_bundle_data', true ) );

		if( empty( $bundle_data ) ){
			return;
		}

		$product_bundles = array_keys( $bundle_data );

		foreach ( $product_bundles as $key => $bundle_key ) {

			$key_data = $this->translate_bundle_key( $bundle_key, $lang );
			$tr_bundle_key = $key_data->tr_key;

			if( isset( $tr_bundle_data[ $tr_bundle_key ] ) ){
				$tr_bundle_data[ $tr_bundle_key ][ 'product_title' ] = $data[ md5( 'bundle_'.$bundle_key.'_title' ) ];
				$tr_bundle_data[ $tr_bundle_key ][ 'product_description' ] = $data[ md5( 'bundle_'.$bundle_key.'_desc' ) ];
			}
		}
		update_post_meta( $tr_id, '_bundle_data', $tr_bundle_data );

		return $tr_bundle_data;
	}

	// Add Bundles Box to WCML Translation GUI
	function custom_box_html( $obj, $product_id, $data ){

		$product_bundles = maybe_unserialize( get_post_meta( $product_id, '_bundle_data', true ) );

		if( empty( $product_bundles ) || $product_bundles == false ){
			return false;
		}

		$bundles_section = new WPML_Editor_UI_Field_Section( __( 'Product Bundles', 'woocommerce-multilingual' ) );
		end( $product_bundles );
		$last_key = key( $product_bundles );
		$divider = true;
		$flag = false;

		foreach ( $product_bundles as $bundle_id => $product_bundle ) {
			$add_group = false;
			if( $bundle_id == $last_key ){
				$divider = false;
			}

			$group = new WPML_Editor_UI_Field_Group( get_the_title( $bundle_id ), $divider );

			if( $product_bundle[ 'override_title' ] == 'yes' ) {
				$bundle_field = new WPML_Editor_UI_Single_Line_Field(
					'bundle_' . $bundle_id . '_title',
					__( 'Name', 'woocommerce-multilingual' ),
					$data,
					false
				);
				$group->add_field( $bundle_field );
				$add_group = true;
			}

			if( $product_bundle[ 'override_description' ] == 'yes' ){
				$bundle_field = new WPML_Editor_UI_Single_Line_Field(
					'bundle_'.$bundle_id.'_desc' ,
					__( 'Description', 'woocommerce-multilingual' ),
					$data,
					false
				);
				$group->add_field( $bundle_field );
				$add_group = true;
			}

			if( $add_group ){
				$bundles_section->add_field( $group );
				$flag = true;
			}
		}

		if( $flag ){
			$obj->add_field( $bundles_section );
		}
	}


	function custom_box_html_data( $data, $product_id, $translation, $lang ){
		$bundle_data = maybe_unserialize( get_post_meta( $product_id, '_bundle_data', true ) );

		if( $translation ) {
			$tr_product_id = $translation->ID;
			$tr_bundle_data = maybe_unserialize( get_post_meta( $tr_product_id, '_bundle_data', true ) );
		}

		if( empty( $bundle_data ) || $bundle_data == false ){
			return $data;
		}

		$product_bundles = array_keys( $bundle_data );

		foreach ( $product_bundles as $bundle_key ) {

			$key_data = $this->translate_bundle_key( $bundle_key, $lang );
			$tr_bundle_key = $key_data->tr_key;

			if( $bundle_data[ $bundle_key ][ 'override_title' ] == 'yes' ){
				$data[ 'bundle_'.$bundle_key.'_title' ] = array( 'original' => $bundle_data[ $bundle_key ][ 'product_title' ] );
				if( isset( $tr_bundle_data[ $tr_bundle_key ][ 'override_title' ] ) ){
					$data[ 'bundle_'.$bundle_key.'_title' ][ 'translation' ] = $tr_bundle_data[ $tr_bundle_key ][ 'product_title' ];
				}else{
					$data[ 'bundle_'.$bundle_key.'_title' ][ 'translation' ] = '';
				}
			}

			if( $bundle_data[ $bundle_key ][ 'override_description' ] == 'yes' ){
				$data[ 'bundle_'.$bundle_key.'_desc' ] = array( 'original' => $bundle_data[ $bundle_key ][ 'product_description' ] );
				if( isset( $tr_bundle_data[ $tr_bundle_key ][ 'override_description' ] ) ){
					$data[ 'bundle_'.$bundle_key.'_desc' ][ 'translation' ] = $tr_bundle_data[ $tr_bundle_key ][ 'product_description' ];
				}else{
					$data[ 'bundle_'.$bundle_key.'_desc' ][ 'translation' ] = '';
				}
			}
		}

		return $data;
	}

	function resync_bundle( $cart_item, $session_values, $cart_item_key ) {
		if ( isset( $cart_item[ 'bundled_items' ] ) && $cart_item[ 'data' ]->product_type === 'bundle' ) {
			$current_bundle_id = apply_filters( 'translate_object_id', $cart_item[ 'product_id' ], 'product', true );
			if ( $cart_item[ 'product_id' ] != $current_bundle_id ) {
				$old_bundled_item_ids      = array_keys( $cart_item[ 'data' ]->bundle_data );
				$cart_item[ 'data' ]       = wc_get_product( $current_bundle_id );
				if( is_array( $cart_item[ 'data' ]->bundle_data ) ){
					$new_bundled_item_ids      = array_keys( $cart_item[ 'data' ]->bundle_data );
					$remapped_bundled_item_ids = array();
					foreach ( $old_bundled_item_ids as $old_item_id_index => $old_item_id ) {
						$remapped_bundled_item_ids[ $old_item_id ] = $new_bundled_item_ids[ $old_item_id_index ];
					}
					$cart_item[ 'remapped_bundled_item_ids' ] = $remapped_bundled_item_ids;
					if ( isset( $cart_item[ 'stamp' ] ) ) {
						$new_stamp = array();
						foreach ( $cart_item[ 'stamp' ] as $bundled_item_id => $stamp_data ) {
							$new_stamp[ $remapped_bundled_item_ids[ $bundled_item_id ] ] = $stamp_data;
						}
						$cart_item[ 'stamp' ] = $new_stamp;
					}
				}
			}
		}
		if ( isset( $cart_item[ 'bundled_by' ] ) && isset( WC()->cart->cart_contents[ $cart_item[ 'bundled_by' ] ] ) ) {
			$bundle_cart_item = WC()->cart->cart_contents[ $cart_item[ 'bundled_by' ] ];
			if (
				isset( $bundle_cart_item[ 'remapped_bundled_item_ids' ] ) &&
				isset( $cart_item[ 'bundled_item_id' ] ) &&
				isset( $bundle_cart_item[ 'remapped_bundled_item_ids' ][ $cart_item[ 'bundled_item_id' ] ] )
			) {
				$old_id                         = $cart_item[ 'bundled_item_id' ];
				$remapped_bundled_item_ids      = $bundle_cart_item[ 'remapped_bundled_item_ids' ];
				$cart_item[ 'bundled_item_id' ] = $remapped_bundled_item_ids[ $cart_item[ 'bundled_item_id' ] ];
				if ( isset( $cart_item[ 'stamp' ] ) ) {
					$new_stamp = array();
					foreach ( $cart_item[ 'stamp' ] as $bundled_item_id => $stamp_data ) {
						$new_stamp[ $remapped_bundled_item_ids[ $bundled_item_id ] ] = $stamp_data;
					}
					$cart_item[ 'stamp' ] = $new_stamp;
				}
			}
		}

		return $cart_item;
	}

	function resync_bundle_clean( $cart ) {
		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item[ 'bundled_items' ] ) && $this->is_bundle_product( $cart_item[ 'product_id' ] ) ) {
				if ( isset( $cart_item[ 'remapped_bundled_item_ids' ] ) ) {
					unset( WC()->cart->cart_contents[ $cart_item_key ][ 'remapped_bundled_item_ids' ] );
				}
			}
		}
	}

	function append_bundle_data_translation_package( $package, $post ){

		if( $post->post_type == 'product' ) {

			$bundle_data = get_post_meta( $post->ID, '_bundle_data', true );

			if( $bundle_data ){

				$fields = array( 'title', 'description' );

				foreach( $bundle_data as $bundle_key => $product ){

					foreach( $fields as $field ) {
						if ( $product[ 'override_' . $field ] == 'yes' && !empty( $product[ 'product_' . $field ] ) ) {

							$package[ 'contents' ][ 'product_bundles:' . $bundle_key . ':' . $field ] = array(
								'translate' => 1,
								'data' => $this->tp->encode_field_data( $product[ 'product_' . $field ], 'base64' ),
								'format' => 'base64'
							);

						}
					}
				}
			}
		}

		return $package;

	}

	function save_bundle_data_translation( $post_id, $data, $job ){

		if ( $this->is_bundle_product( $post_id ) ) {

			remove_action( 'wcml_after_duplicate_product_post_meta', array( $this, 'sync_bundled_ids' ), 10, 2 );

			$bundle_data = get_post_meta( $post_id, '_bundle_data', true );

			$bundle_data_original = get_post_meta( $job->original_doc_id , '_bundle_data', true );

			$translated_bundle_pieces = array();

			foreach( $data as $value){

				if( preg_match( '/product_bundles:([0-9]+):(.+)/', $value[ 'field_type' ], $matches ) ){

					$bundle_key = $matches[1];
					$field      = $matches[2];

					$key_data = $this->translate_bundle_key( $bundle_key, $job->language_code  );

					if( !isset( $bundle_data[ $key_data->tr_key ] ) ){
						$bundle_data[ $key_data->tr_key ] = array(
							'product_id'            => $key_data->tr_id,
							'hide_thumbnail'        => $bundle_data_original[ $bundle_key ][ 'hide_thumbnail' ],
							'override_title'        => $bundle_data_original[ $bundle_key ][ 'override_title' ],
							'product_title'         => '',
							'override_description'  => $bundle_data_original[ $bundle_key ][ 'override_description' ],
							'product_description'   => '',
							'optional'              => $bundle_data_original[ $bundle_key ][ 'optional' ],
							'bundle_quantity'       => $bundle_data_original[ $bundle_key ][ 'bundle_quantity' ],
							'bundle_quantity_max'   => $bundle_data_original[ $bundle_key ][ 'bundle_quantity_max' ],
							'bundle_discount'       => $bundle_data_original[ $bundle_key ][ 'bundle_discount' ],
							'visibility'            => $bundle_data_original[ $bundle_key ][ 'visibility' ],
						);
					}

					$bundle_data[ $key_data->tr_key ][ 'product_'.$field ] = $value[ 'data' ];

				}
			}

			update_post_meta( $post_id, '_bundle_data', $bundle_data );

		}

	}

	function replace_tm_editor_custom_fields_with_own_sections( $fields ){
		$fields[] = '_bundle_data';

		return $fields;
	}

	public function is_bundle_product( $product_id ){
		if ( 'bundle' === WooCommerce_Functions_Wrapper::get_product_type( $product_id ) ) {
			return true;
		}

		return false;
	}

}