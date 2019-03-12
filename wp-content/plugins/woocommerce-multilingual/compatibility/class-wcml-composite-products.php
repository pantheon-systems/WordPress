<?php


class WCML_Composite_Products extends WCML_Compatibility_Helper{

	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;
	/**
	 * @var WPML_Element_Translation_Package
	 */
	private $tp;

	/**
	 * WCML_Composite_Products constructor.
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

		add_filter( 'woocommerce_composite_component_default_option', array($this, 'woocommerce_composite_component_default_option'), 10, 3 );
		add_filter( 'wcml_cart_contents', array($this, 'wpml_composites_compat'), 11, 4 );
		add_filter( 'woocommerce_composite_component_options_query_args', array($this, 'wpml_composites_transients_cache_per_language'), 10, 3 );
		add_action( 'wcml_before_sync_product', array( $this, 'sync_composite_data_across_translations'), 10, 2 );

		if( is_admin() ){		

			add_action( 'wcml_gui_additional_box_html', array( $this, 'custom_box_html' ), 10, 3 );
			add_filter( 'wcml_gui_additional_box_data', array( $this, 'custom_box_html_data' ), 10, 4 );
			add_action( 'wcml_update_extra_fields', array( $this, 'components_update' ), 10, 4 );
			add_filter( 'woocommerce_json_search_found_products', array( $this, 'woocommerce_json_search_found_products' ) );

			add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_composite_data_translation_package' ), 10, 2 );
			add_action( 'wpml_translation_job_saved',   array( $this, 'save_composite_data_translation' ), 10, 3 );
			//lock fields on translations pages
			add_filter( 'wcml_js_lock_fields_input_names', array( $this, 'wcml_js_lock_fields_input_names' ) );
			add_filter( 'wcml_js_lock_fields_ids', array( $this, 'wcml_js_lock_fields_ids' ) );
			add_filter( 'wcml_after_load_lock_fields_js', array( $this, 'localize_lock_fields_js' ) );
			add_action( 'init', array( $this, 'load_assets' ) );

			add_action( 'wcml_after_save_custom_prices', array( $this, 'update_composite_custom_prices' ), 10, 4 );

			add_filter( 'wcml_do_not_display_custom_fields_for_product', array( $this, 'replace_tm_editor_custom_fields_with_own_sections' ) );
		}else{
			add_filter( 'get_post_metadata', array( $this, 'filter_composite_product_cost' ), 10, 4 );
		}

	}

	function woocommerce_composite_component_default_option($selected_value, $component_id, $object) {

		if( !empty( $selected_value ) )
			$selected_value = apply_filters( 'wpml_object_id', $selected_value, 'product', true );


		return $selected_value;
	}
	
	function wpml_composites_compat( $new_cart_data, $cart_contents, $key, $new_key ) {

		if ( isset( $cart_contents[ $key ][ 'composite_children' ] ) || isset( $cart_contents[ $key ][ 'composite_parent' ] ) ) {

			$buff = $new_cart_data[ $new_key ];

			unset( $new_cart_data[ $new_key ] );

			$new_cart_data[ $key ] = $buff;
		}

		return $new_cart_data;
	}

	function wpml_composites_transients_cache_per_language( $args, $query_args, $component_data ) {

		$args[ 'wpml_lang' ] = apply_filters( 'wpml_current_language', NULL );

		return $args;
	}

	function sync_composite_data_across_translations(  $original_product_id, $current_product_id ){

		if( $this->get_product_type( $original_product_id ) == 'composite' ){

			$composite_data = $this->get_composite_data( $original_product_id );

			$product_trid = $this->sitepress->get_element_trid( $original_product_id, 'post_product' );
			$product_translations = $this->sitepress->get_element_translations( $product_trid, 'post_product' );

			foreach ( $product_translations as $product_translation ) {

				if ( empty($product_translation->original) ) {

					$translated_composite_data = $this->get_composite_data( $product_translation->element_id );

					foreach ( $composite_data as $component_id => $component ) {

						if( isset( $translated_composite_data[$component_id]['title'] ) ){
							$composite_data[$component_id]['title'] =  $translated_composite_data[$component_id]['title'];
						}

						if( isset( $translated_composite_data[$component_id]['description'] ) ){
							$composite_data[$component_id]['description'] =  $translated_composite_data[$component_id]['description'];
						}

						if ( $component['query_type'] == 'product_ids' ) {

							foreach ( $component['assigned_ids'] as $idx => $assigned_id ) {
								$composite_data[$component_id]['assigned_ids'][$idx] =
									apply_filters( 'translate_object_id', $assigned_id, 'product', true, $product_translation->language_code );
							}

						} elseif( $component['query_type'] == 'category_ids' ){

							foreach ( $component['assigned_category_ids'] as $idx => $assigned_id ) {
								$composite_data[$component_id]['assigned_category_ids'][$idx] =
									apply_filters( 'translate_object_id', $assigned_id, 'product_cat', true, $product_translation->language_code );

							}

						}

					}

					update_post_meta( $product_translation->element_id, '_bto_data', $composite_data );

				}

			}
		}

	}

	function custom_box_html( $obj, $product_id, $data ){

		if( $this->get_product_type( $product_id ) == 'composite' ){

			$composite_data = $this->get_composite_data( $product_id );

			$composite_section = new WPML_Editor_UI_Field_Section( __( 'Composite Products ( Components )', 'woocommerce-multilingual' ) );
			end( $composite_data );
			$last_key = key( $composite_data );
			$divider = true;
			foreach( $composite_data as $component_id => $component ) {
				if( $component_id ==  $last_key ){
					$divider = false;
				}
				$group = new WPML_Editor_UI_Field_Group( '', $divider );
				$composite_field = new WPML_Editor_UI_Single_Line_Field( 'composite_'.$component_id.'_title', __( 'Name', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $composite_field );
				$composite_field = new WPML_Editor_UI_Single_Line_Field( 'composite_'.$component_id.'_description' , __( 'Description', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $composite_field );
				$composite_section->add_field( $group );

			}

			if( $composite_data ){
				$obj->add_field( $composite_section );
			}

			$composite_scenarios_meta = $this->get_composite_scenarios_meta( $product_id );
			if( $composite_scenarios_meta ){

				$composite_scenarios = new WPML_Editor_UI_Field_Section( __( 'Composite Products ( Scenarios )', 'woocommerce-multilingual' ) );
				end( $composite_scenarios_meta );
				$last_key = key( $composite_scenarios_meta );
				$divider = true;
				foreach( $composite_scenarios_meta as $scenario_key => $scenario_meta ) {
					if( $scenario_key ==  $last_key ){
						$divider = false;
					}
					$group = new WPML_Editor_UI_Field_Group( '', $divider );
					$composite_scenario_field = new WPML_Editor_UI_Single_Line_Field( 'composite_scenario_'.$scenario_key.'_title', __( 'Name', 'woocommerce-multilingual' ), $data, false );
					$group->add_field( $composite_scenario_field );
					$composite_scenario_field = new WPML_Editor_UI_Single_Line_Field( 'composite_scenario_'.$scenario_key.'_description' , __( 'Description', 'woocommerce-multilingual' ), $data, false );
					$group->add_field( $composite_scenario_field );
					$composite_scenarios->add_field( $group );

				}

				$obj->add_field( $composite_scenarios );

			}

		}

	}

	function custom_box_html_data( $data, $product_id, $translation, $lang ){

		if( $this->get_product_type( $product_id ) == 'composite' ){

			$composite_data = $this->get_composite_data( $product_id );

			foreach( $composite_data as $component_id => $component ) {

				$data['composite_'.$component_id.'_title'] = array( 'original' =>
					isset( $composite_data[$component_id]['title'] ) ? $composite_data[$component_id]['title'] : '' );

				$data['composite_'.$component_id.'_description'] = array( 'original' =>
					isset( $composite_data[$component_id]['description'] ) ? $composite_data[$component_id]['description'] : '' );

			}

			$composite_scenarios_meta = $this->get_composite_scenarios_meta( $product_id );
			if( $composite_scenarios_meta ){
				foreach( $composite_scenarios_meta as $scenario_key => $scenario_meta ){
					$data[ 'composite_scenario_'.$scenario_key.'_title' ] = array(
						'original' => isset( $scenario_meta['title'] ) ? $scenario_meta['title'] : '',
						'translation' => ''
					);

					$data[ 'composite_scenario_'.$scenario_key.'_description' ] = array(
						'original' => isset( $scenario_meta['description'] ) ? $scenario_meta['description'] : '',
						'translation' => ''
						);
				}
			}

			if( $translation ){
				$translated_composite_data = $this->get_composite_data( $translation->ID );

				foreach( $composite_data as $component_id => $component ){

					$data['composite_'.$component_id.'_title'][ 'translation' ] =
						isset( $translated_composite_data[$component_id]['title'] ) ? $translated_composite_data[$component_id]['title'] : '';

					$data['composite_'.$component_id.'_description'][ 'translation' ] =
						isset( $translated_composite_data[$component_id]['description'] ) ? $translated_composite_data[$component_id]['description'] : '';

				}

				$translated_composite_scenarios_meta = $this->get_composite_scenarios_meta( $translation->ID );
				if( $translated_composite_scenarios_meta ){
					foreach( $translated_composite_scenarios_meta as $scenario_key => $translated_scenario_meta ){
						$data[ 'composite_scenario_'.$scenario_key.'_title' ][ 'translation' ] =
							isset( $translated_scenario_meta['title'] ) ? $translated_scenario_meta['title'] : '';

						$data[ 'composite_scenario_'.$scenario_key.'_description' ][ 'translation' ] =
							isset( $translated_scenario_meta['description'] ) ? $translated_scenario_meta['description'] : '';
					}
				}

			}

		}

		return $data;
	}

    function components_update( $original_product_id, $product_id, $data, $language ){

		$composite_data = $this->get_composite_data( $original_product_id );

		foreach( $composite_data as $component_id => $component ) {

			if(!empty($data[ md5( 'composite_'.$component_id.'_title' ) ] ) ){
				$composite_data[$component_id]['title'] = $data[ md5( 'composite_'.$component_id.'_title' ) ];
			}

			if(!empty($data[ md5( 'composite_'.$component_id.'_description' ) ])) {
				$composite_data[$component_id]['description'] = $data[ md5( 'composite_'.$component_id.'_description' ) ];
			}

			//sync product ids
			if( $component[ 'query_type' ] == 'product_ids' ){
				foreach( $component[ 'assigned_ids' ] as $key => $assigned_id ){
					$assigned_id_current_language = apply_filters( 'translate_object_id', $assigned_id, get_post_type( $assigned_id ), false, $language );
					if( $assigned_id_current_language ){
						$composite_data[ $component_id ][ 'assigned_ids' ][ $key ] = $assigned_id_current_language;
					}
				}
			}elseif( $component[ 'query_type' ] == 'category_ids' ){
				foreach( $component[ 'assigned_category_ids' ] as $key => $assigned_id ){
					$trsl_term_id = apply_filters( 'translate_object_id', $assigned_id, 'product_cat', false, $language );
					if( $trsl_term_id ){
						$composite_data[ $component_id ][ 'assigned_category_ids' ][ $key ] = $trsl_term_id;
					}
				}
			}

			//sync default
			if( $component[ 'default_id' ] ){
				$trnsl_default_id = apply_filters( 'translate_object_id', $component[ 'default_id' ], get_post_type( $component[ 'default_id' ] ), false, $language );
				if( $trnsl_default_id ){
					$composite_data[ $component_id ][ 'default_id' ] = $trnsl_default_id;
				}
			}

		}

		update_post_meta( $product_id, '_bto_data', $composite_data );

		$composite_scenarios_meta = $this->get_composite_scenarios_meta( $original_product_id );
		if( $composite_scenarios_meta ){
			foreach( $composite_scenarios_meta as $scenario_key => $scenario_meta ){
				if( !empty( $data[ md5( 'composite_scenario_'.$scenario_key.'_title' ) ] ) ){
					$composite_scenarios_meta[ $scenario_key ][ 'title' ] = $data[ md5( 'composite_scenario_'.$scenario_key.'_title' ) ];
				}

				if( !empty( $data[ md5( 'composite_scenario_'.$scenario_key.'_description' ) ])) {
					$composite_scenarios_meta[ $scenario_key ][ 'description' ] = $data[ md5( 'composite_scenario_'.$scenario_key.'_description' ) ];
				}

				//sync product ids
				foreach( $scenario_meta[ 'component_data' ] as $compon_id => $component_data ){
					if( isset( $composite_data[ $compon_id ] ) && $composite_data[ $compon_id ][ 'query_type' ] == 'product_ids' ){
						foreach( $component_data as $key => $assigned_prod_id ){
							$trnsl_assigned_prod_id = apply_filters( 'translate_object_id', $assigned_prod_id, get_post_type( $assigned_prod_id ), false, $language );
							if( $trnsl_assigned_prod_id ){
								$composite_scenarios_meta[ $scenario_key ][ 'component_data' ][ $compon_id ][ $key ] = $trnsl_assigned_prod_id;
							}
						}
					}elseif( isset( $composite_data[ $compon_id ] ) && $composite_data[ $compon_id ][ 'query_type' ] == 'category_ids' ){
						foreach( $component_data as $key => $assigned_cat_id ){
							$trslt_assigned_cat_id = apply_filters( 'translate_object_id', $assigned_cat_id, 'product_cat', false, $language );
							if( $trslt_assigned_cat_id ){
								$composite_scenarios_meta[ $scenario_key ][ 'component_data' ][ $compon_id ][ $key ] = $trslt_assigned_cat_id;
							}
						}
					}
				}
			}
		}

		update_post_meta( $product_id, '_bto_scenario_data', $composite_scenarios_meta );

		return array(
			'components' => $composite_data,
			'scenarios'  => $composite_scenarios_meta,
		);
	}

	function append_composite_data_translation_package( $package, $post ){

		if( $post->post_type == 'product' ) {

			$composite_data = get_post_meta( $post->ID, '_bto_data', true );

			if( $composite_data ){

				$fields = array( 'title', 'description' );

				foreach( $composite_data as $component_id => $component ){

					foreach( $fields as $field ) {
						if ( !empty($component[$field]) ) {

							$package['contents']['wc_composite:' . $component_id . ':' . $field] = array(
								'translate' => 1,
								'data' => $this->tp->encode_field_data( $component[$field], 'base64' ),
								'format' => 'base64'
							);

						}
					}

				}

			}

		}

		return $package;

	}

	function save_composite_data_translation( $post_id, $data, $job ){


		$translated_composite_data = array();
		foreach( $data as $value){

			if( preg_match( '/wc_composite:([0-9]+):(.+)/', $value['field_type'], $matches ) ){

				$component_id = $matches[1];
				$field        = $matches[2];

				$translated_composite_data[$component_id][$field] = $value['data'];

			}

		}

		if( $translated_composite_data ){

			$composite_data = get_post_meta( $job->original_doc_id, '_bto_data', true );


			foreach ( $composite_data as $component_id => $component ) {

				if( isset( $translated_composite_data[$component_id]['title'] ) ){
					$composite_data[$component_id]['title'] =  $translated_composite_data[$component_id]['title'];
				}

				if( isset( $translated_composite_data[$component_id]['description'] ) ){
					$composite_data[$component_id]['description'] =  $translated_composite_data[$component_id]['description'];
				}

				if ( $component['query_type'] == 'product_ids' ) {

					foreach ( $component['assigned_ids'] as $idx => $assigned_id ) {
						$composite_data[$component_id]['assigned_ids'][$idx] =
							apply_filters( 'translate_object_id', $assigned_id, 'product', true, $job->language_code );
					}

				} elseif( $component['query_type'] == 'category_ids' ){

					foreach ( $component['assigned_category_ids'] as $idx => $assigned_id ) {
						$composite_data[$component_id]['assigned_category_ids'][$idx] =
							apply_filters( 'translate_object_id', $assigned_id, 'product_cat', true, $job->language_code );

					}

				}

			}

		}

		update_post_meta( $post_id, '_bto_data', $composite_data );

	}

	function wcml_js_lock_fields_input_names( $names ){

		$names[] = '_base_regular_price';
		$names[] = '_base_sale_price';
		$names[] = 'bto_style';

		return $names;
	}

	function wcml_js_lock_fields_ids( $names ){

		$names[] = '_per_product_pricing_bto';
		$names[] = '_per_product_shipping_bto';
		$names[] = '_bto_hide_shop_price';

		return $names;
	}

	function localize_lock_fields_js(){
		wp_localize_script( 'wcml-composite-js', 'lock_settings' , array( 'lock_fields' => 1 ) );
	}

	function load_assets( ){
		global $pagenow;

		if( ( $pagenow == 'post.php' && isset( $_GET[ 'post' ] ) && WooCommerce_Functions_Wrapper::get_product_type( $_GET[ 'post' ] ) === 'composite' ) || $pagenow == 'post-new.php' ){
			wp_register_script( 'wcml-composite-js', WCML_PLUGIN_URL . '/compatibility/res/js/wcml-composite.js', array( 'jquery' ), WCML_VERSION );
			wp_enqueue_script( 'wcml-composite-js' );

		}

	}

	function woocommerce_json_search_found_products( $found_products ){
		global $wpml_post_translations;

		foreach( $found_products as $id => $product_name ){
			if( $wpml_post_translations->get_element_lang_code ( $id ) != $this->sitepress->get_current_language() ){
				unset( $found_products[ $id ] );
			}
		}

		return $found_products;
	}

	public function get_composite_scenarios_meta( $product_id ){
		return get_post_meta( $product_id, '_bto_scenario_data', true );
	}

	public function get_composite_data( $product_id ){
		return get_post_meta( $product_id, '_bto_data', true );
	}


	function filter_composite_product_cost( $value, $object_id, $meta_key, $single ) {

		if ( in_array( $meta_key, array(
			'_bto_base_regular_price',
			'_bto_base_sale_price',
			'_bto_base_price'
		) ) ) {

			if ( $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT ) {

				$original_id = $this->woocommerce_wpml->products->get_original_product_id( $object_id );

				$cost_status = get_post_meta( $original_id, '_wcml_custom_prices_status', true );
				
				$currency = $this->woocommerce_wpml->multi_currency->get_client_currency();

				if ( $currency == get_option( 'woocommerce_currency' ) ) {
					return $value;
				}

				$cost = get_post_meta( $original_id, $meta_key . '_' . $currency, true );

				if ( $cost_status && !empty( $cost ) ) {

					return $cost;

				} else {

					remove_filter( 'get_post_metadata', array( $this, 'filter_composite_product_cost' ), 10, 4 );

					$cost = get_post_meta( $original_id, $meta_key, true );

					add_filter( 'get_post_metadata', array( $this, 'filter_composite_product_cost' ), 10, 4 );

					if( $cost ){

						$cost = $this->woocommerce_wpml->multi_currency->prices->convert_price_amount( $cost, $currency );

						return $cost;
					}

				}

			}

		}

		return $value;
	}

	function update_composite_custom_prices( $product_id, $product_price, $custom_prices, $code ){

		if( $this->get_product_type( $product_id ) == 'composite' ){

			update_post_meta( $product_id, '_bto_base_regular_price'.'_'.$code, $custom_prices[ '_regular_price' ] );
			update_post_meta( $product_id, '_bto_base_sale_price'.'_'.$code, $custom_prices[ '_sale_price' ] );
			update_post_meta( $product_id, '_bto_base_price'.'_'.$code, $product_price );

		}

	}

	function replace_tm_editor_custom_fields_with_own_sections( $fields ){
		$fields[] = '_bto_data';
		$fields[] = '_bto_scenario_data';

		return $fields;
	}

}
