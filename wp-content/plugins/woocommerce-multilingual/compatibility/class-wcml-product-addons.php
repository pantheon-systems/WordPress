<?php

/**
 * Class WCML_Product_Addons
 */
class WCML_Product_Addons {

	const TEMPLATE_FOLDER = '/templates/compatibility/';
	const DIALOG_TEMPLATE = 'product-addons-prices-dialog.twig';
	const SETTINGS_TEMPLATE = 'product-addons-prices-settings.twig';
	const PRICE_OPTION_KEY = '_product_addon_prices';

	/**
	 * @var SitePress
	 */
	public $sitepress;
	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;
	/**
	 * @var int
	 */
	private $multi_currency_mode;

	/**
	 * WCML_Product_Addons constructor.
	 * @param SitePress $sitepress
	 * @param woocommerce_wpml $woocommerce_wpml
	 */
	function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml ) {
		$this->sitepress           = $sitepress;
		$this->woocommerce_wpml    = $woocommerce_wpml;
		$this->multi_currency_mode = $woocommerce_wpml->settings['enable_multi_currency'];
	}

	public function add_hooks(){

		add_action( 'init', array( $this, 'load_assets' ) );
		add_filter( 'get_product_addons_product_terms', array( $this, 'addons_product_terms' ) );
		add_filter( 'get_product_addons_fields', array( $this, 'product_addons_price_filter' ), 10, 2 );

		add_action( 'updated_post_meta', array( $this, 'register_addons_strings' ), 10, 4 );
		add_action( 'added_post_meta', array( $this, 'register_addons_strings' ), 10, 4 );

		add_action( 'woocommerce-product-addons_panel_start', array( $this, 'show_pointer_info' ) );

		if ( is_admin() ) {

			if ( $this->is_global_addon_edit_page()	) {
				if( ! isset( $_GET['edit'] ) ){
					add_action( 'admin_notices', array( $this, 'inf_translate_strings' ) );
				}
			}

			add_action( 'wcml_gui_additional_box_html', array( $this, 'custom_box_html' ), 10, 3 );
			add_filter( 'wcml_gui_additional_box_data', array( $this, 'custom_box_html_data' ), 10, 3 );
			add_action( 'wcml_update_extra_fields', array( $this, 'addons_update' ), 10, 3 );

			add_action( 'woocommerce_product_data_panels',   array( $this, 'show_pointer_info' ) );

			add_filter( 'wcml_do_not_display_custom_fields_for_product', array( $this, 'replace_tm_editor_custom_fields_with_own_sections' ) );

			if( $this->is_multi_currency_on() ){
				add_action( 'woocommerce_product_addons_panel_start', array( $this, 'load_dialog_resources' ) );
				add_action( 'woocommerce_product_addons_panel_option_row', array( $this, 'dialog_button_after_option_row' ), 10, 4 );
				add_action( 'woocommerce_product_addons_panel_before_options', array( $this, 'dialog_button_before_options' ), 10, 3 );
				add_action( 'wcml_before_sync_product', array( $this, 'update_custom_prices_values' ) );
				add_action( 'woocommerce_product_addons_global_edit_objects', array( $this, 'custom_prices_settings_block' ) );
			}
		}else{
			add_filter( 'get_post_metadata', array( $this, 'translate_addons_strings' ), 10, 4 );
		}

		add_filter( 'wcml_cart_contents_not_changed', array(
			$this,
			'filter_booking_addon_product_in_cart_contents'
		), 20 );

		add_filter( 'get_product_addons_global_query_args', array(
			$this,
			'set_global_ids_in_query_args'
		) );
	}


	private function is_global_addon_edit_page() {
		global $pagenow;

		return 'edit.php' === $pagenow &&
		       isset( $_GET['post_type'] ) &&
		       'product' === $_GET['post_type'] &&
		       isset( $_GET['page'] ) &&
		       ( 'global_addons' === $_GET['page'] || 'addons' === $_GET['page'] );
	}

	/**
	 * @param string $product_id
	 *
	 * @return array
	 */
	private function get_product_addons( $product_id ) {
		return maybe_unserialize( get_post_meta( $product_id, '_product_addons', true ) );
	}

	/**
	 * @param $meta_id
	 * @param $id
	 * @param $meta_key
	 * @param $addons
	 */
	function register_addons_strings( $meta_id, $id, $meta_key, $addons ) {
		if ( '_product_addons' === $meta_key && 'global_product_addon' === get_post_type( $id ) ) {
			$this->update_custom_prices_values( $id );
			foreach ( $addons as $addon ) {
				//register name
				do_action( 'wpml_register_single_string', 'wc_product_addons_strings', $id . '_addon_' . $addon['type'] . '_' . $addon['position'] . '_name', $addon['name'] );
				//register description
				do_action( 'wpml_register_single_string', 'wc_product_addons_strings', $id . '_addon_' . $addon['type'] . '_' . $addon['position'] . '_description', $addon['description'] );
				//register options labels
				foreach ( $addon['options'] as $key => $option ) {
					do_action( 'wpml_register_single_string', 'wc_product_addons_strings', $id . '_addon_' . $addon['type'] . '_' . $addon['position'] . '_option_label_' . $key, $option['label'] );
				}
			}
		}
	}

	/**
	 * @param $null
	 * @param $object_id
	 * @param $meta_key
	 * @param $single
	 *
	 * @return array
	 */
	function translate_addons_strings( $null, $object_id, $meta_key, $single ) {

		if ( '_product_addons' === $meta_key && 'global_product_addon' === get_post_type( $object_id ) ) {

			remove_filter( 'get_post_metadata', array( $this, 'translate_addons_strings' ), 10, 4 );
			$addons = get_post_meta( $object_id, $meta_key, true );
			add_filter( 'get_post_metadata', array( $this, 'translate_addons_strings' ), 10, 4 );

			if ( is_array( $addons ) ) {
				foreach ( $addons as $key => $addon ) {
					//register name
					$addons[ $key ]['name'] = apply_filters( 'wpml_translate_single_string', $addon['name'], 'wc_product_addons_strings', $object_id . '_addon_' . $addon['type'] . '_' . $addon['position'] . '_name' );
					//register description
					$addons[ $key ]['description'] = apply_filters( 'wpml_translate_single_string', $addon['description'], 'wc_product_addons_strings', $object_id . '_addon_' . $addon['type'] . '_' . $addon['position'] . '_description' );
					//register options labels
					foreach ( $addon['options'] as $opt_key => $option ) {
						$addons[ $key ]['options'][ $opt_key ]['label'] = apply_filters( 'wpml_translate_single_string', $option['label'], 'wc_product_addons_strings', $object_id . '_addon_' . $addon['type'] . '_' . $addon['position'] . '_option_label_' . $opt_key );
					}
				}
			}

			return array( 0 => $addons );
		}

		return $null;

	}

	/**
	 * @param $addons
	 * @param $post_id
	 *
	 * @return mixed
	 */
	function product_addons_price_filter( $addons, $post_id ) {

		if ( $this->is_multi_currency_on() ) {

			$client_currency = $this->woocommerce_wpml->multi_currency->get_client_currency();
			$is_custom_prices_on = $this->is_product_custom_prices_on( $post_id );

			foreach ( $addons as $add_id => $addon ) {

				if ( isset( $addon['price'] ) && $addon['price'] ) {
					if (
						$is_custom_prices_on &&
						isset( $addon[ 'price_' . $client_currency ] ) &&
						$addon[ 'price_' . $client_currency ]
					) {
						$addons[ $add_id ]['price'] = $addon[ 'price_' . $client_currency ];
					} else {
						$addons[ $add_id ]['price'] = apply_filters( 'wcml_raw_price_amount', $addon['price'] );
					}
				}

				foreach ( $addon['options'] as $key => $option ) {
					if (
						$is_custom_prices_on &&
						isset( $option[ 'price_' . $client_currency ] ) &&
						$option[ 'price_' . $client_currency ]
					) {
						$addons[ $add_id ]['options'][ $key ]['price'] = $option[ 'price_' . $client_currency ];
					} else {
						$addons[ $add_id ]['options'][ $key ]['price'] = apply_filters( 'wcml_raw_price_amount', $option['price'] );
					}
				}
			}
		}

		return $addons;
	}

	/**
	 * @param $product_terms
	 *
	 * @return array
	 */
	function addons_product_terms( $product_terms ) {
		foreach ( $product_terms as $key => $product_term ) {
			$product_terms[ $key ] = apply_filters( 'translate_object_id', $product_term, 'product_cat', true, $this->sitepress->get_default_language() );
		}

		return $product_terms;
	}

	function inf_translate_strings() {

		$pointer_ui = new WCML_Pointer_UI(
			sprintf( __( 'You can translate strings related to global add-ons on the %sWPML String Translation page%s. Use the search on the top of that page to find the strings.', 'woocommerce-multilingual' ), '<a href="'.admin_url('admin.php?page='.WPML_ST_FOLDER.'/menu/string-translation.php&context=wc_product_addons_strings').'">', '</a>' ),
			'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-product-add-ons-woocommerce-multilingual/',
			'wpbody-content .woocommerce>h2'
		);

		$pointer_ui->show();
	}

	/**
	 * @param $obj
	 * @param $product_id
	 * @param $data
	 */
	function custom_box_html( $obj, $product_id, $data ) {

		$product_addons = $this->get_product_addons( $product_id );

		if ( ! empty( $product_addons ) ) {
			foreach ( $product_addons as $addon_id => $product_addon ) {

				$addons_section = new WPML_Editor_UI_Field_Section( sprintf( __( 'Product Add-ons Group "%s"', 'woocommerce-multilingual' ), $product_addon['name'] ) );

				$group = new WPML_Editor_UI_Field_Group( '' , true );
				$addon_field = new WPML_Editor_UI_Single_Line_Field( 'addon_'.$addon_id.'_name', __( 'Name', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $addon_field );
				$addon_field = new WPML_Editor_UI_Single_Line_Field( 'addon_'.$addon_id.'_description' , __( 'Description', 'woocommerce-multilingual' ), $data, false );
				$group->add_field( $addon_field );

				$addons_section->add_field( $group );

				if ( ! empty( $product_addon['options'] ) ) {

					$labels_group = new WPML_Editor_UI_Field_Group( __( 'Options', 'woocommerce-multilingual' ) , true );

					foreach ( $product_addon['options'] as $option_id => $option ) {
						$option_label_field = new WPML_Editor_UI_Single_Line_Field( 'addon_'.$addon_id.'_option_'.$option_id.'_label', __( 'Label', 'woocommerce-multilingual' ), $data, false );
						$labels_group->add_field( $option_label_field );
					}
					$addons_section->add_field( $labels_group );
				}
				$obj->add_field( $addons_section );
			}
		}
	}

	/**
	 * @param $data
	 * @param $product_id
	 * @param $translation
	 *
	 * @return mixed
	 */
	function custom_box_html_data( $data, $product_id, $translation ) {

		$product_addons = $this->get_product_addons( $product_id );

		if ( ! empty( $product_addons ) ) {
			foreach ( $product_addons as $addon_id => $product_addon ) {
				$data[ 'addon_' . $addon_id . '_name' ] = array( 'original' => $product_addon['name'] );
				$data[ 'addon_' . $addon_id . '_description' ] = array( 'original' => $product_addon['description'] );
				if ( ! empty( $product_addon['options'] ) ) {
					foreach ( $product_addon['options'] as $option_id => $option ) {
						$data[ 'addon_' . $addon_id . '_option_' . $option_id . '_label' ] = array( 'original' => $option['label'] );
					}
				}
			}

			if ( $translation ) {
				$translated_product_addons = $this->get_product_addons( $translation->ID );
				if ( ! empty( $translated_product_addons ) ) {
					foreach ( $translated_product_addons as $addon_id => $transalted_product_addon ) {
						$data[ 'addon_' . $addon_id . '_name' ]['translation'] = $transalted_product_addon['name'];
						$data[ 'addon_' . $addon_id . '_description' ]['translation'] = $transalted_product_addon['description'];
						if ( ! empty( $transalted_product_addon['options'] ) ) {
							foreach ( $transalted_product_addon['options'] as $option_id => $option ) {
								$data[ 'addon_' . $addon_id . '_option_' . $option_id . '_label' ]['translation'] = $option['label'];
							}
						}
					}
				}
			}
		}

		return $data;
	}

	/**
	 * @param $original_product_id
	 * @param $product_id
	 * @param $data
	 */
	function addons_update( $original_product_id, $product_id, $data ) {

		$product_addons = $this->get_product_addons( $original_product_id );

		if ( ! empty( $product_addons ) ) {

			foreach ( $product_addons as $addon_id => $product_addon ) {

				$product_addons[ $addon_id ]['name'] = $data[ md5( 'addon_' . $addon_id . '_name' ) ];
				$product_addons[ $addon_id ]['description'] = $data[ md5( 'addon_' . $addon_id . '_description' ) ];

				if ( ! empty( $product_addon['options'] ) ) {

					foreach ( $product_addon['options'] as $option_id => $option ) {
						$product_addons[ $addon_id ]['options'][ $option_id ]['label'] = $data[ md5( 'addon_'.$addon_id.'_option_'.$option_id.'_label' ) ];
					}
				}
			}
		}

		update_post_meta( $product_id, '_product_addons', $product_addons );
	}

	public function show_pointer_info(){

		$pointer_ui = new WCML_Pointer_UI(
			sprintf( __( 'You can translate the Group Name, Group Description and every Option Label of your product add-on on the %sWooCommerce product translation page%s', 'woocommerce-multilingual' ), '<a href="'.admin_url('admin.php?page=wpml-wcml').'">', '</a>' ),
			'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-product-add-ons-woocommerce-multilingual/',
			'product_addons_data>p'
		);

		$pointer_ui->show();
	}

	function replace_tm_editor_custom_fields_with_own_sections( $fields ){
		$fields[] = '_product_addons';

		return $fields;
	}

	// special case for WC Bookings plugin - need add addon cost after re-calculating booking costs #wcml-1877
	public function filter_booking_addon_product_in_cart_contents( $cart_item ) {

		$is_booking_product_with_addons = $cart_item['data'] instanceof WC_Product_Booking && isset( $cart_item['addons'] );

		if ( $this->is_multi_currency_on() && $is_booking_product_with_addons ) {
			$cost = $cart_item['data']->get_price();

			foreach( $cart_item['addons'] as $addon ){
				$cost += $addon['price'];
			}

			$cart_item['data']->set_price( $cost );
		}

		return $cart_item;
	}

	public function set_global_ids_in_query_args( $args ) {

		if ( !is_archive() ) {

			remove_filter( 'get_terms_args', array( $this->sitepress, 'get_terms_args_filter' ), 10, 2 );
			remove_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1 );
			remove_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10 );

			$matched_addons_ids = wp_list_pluck( get_posts( $args ), 'ID' );

			if ( $matched_addons_ids ) {
				$args['include'] = $matched_addons_ids;
				unset( $args['tax_query'] );
			}

			add_filter( 'get_terms_args', array( $this->sitepress, 'get_terms_args_filter' ), 10, 2 );
			add_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1 );
			add_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10, 3 );
		}

		return $args;
	}

	/**
	 * @return bool
	 */
	private function is_multi_currency_on(){
		return $this->multi_currency_mode === $this->sitepress->get_wp_api()->constant( 'WCML_MULTI_CURRENCIES_INDEPENDENT' );
	}

	public function load_dialog_resources(){
		wp_enqueue_script( 'wcml-dialogs', WCML_PLUGIN_URL . '/res/js/dialogs' . WCML_JS_MIN . '.js', array('jquery-ui-dialog'), WCML_VERSION );
	}

	/**
	 * @param WP_Post|null $product
	 * @param array $product_addons
	 * @param int $loop
	 * @param array $option
	 */
	public function dialog_button_after_option_row( $product, $product_addons, $loop, $option ){
		if( $option ){
			$this->render_edit_price_element( $this->get_prices_dialog_model( $product_addons, $option, $loop, $this->is_product_custom_prices_on( $product ? $product->ID : false ) ) );
		}

	}

	/**
	 * @param WP_Post|null $product
	 * @param array $product_addons
	 * @param int $loop
	 */
	public function dialog_button_before_options( $product, $product_addons, $loop ){
		$this->render_edit_price_element( $this->get_prices_dialog_model( array(), $product_addons, $loop, $this->is_product_custom_prices_on( $product ? $product->ID : false ) ) );
	}

	/**
	 * @param array $model
	 */
	private function render_edit_price_element( $model ){
		$twig_loader = $this->get_twig_loader();
		echo $twig_loader->get_template()->show( $model, self::DIALOG_TEMPLATE );
	}

	/**
	 * @return array
	 */
	private function get_one_price_types(){

		return array(
			'custom_text',
			'custom_textarea',
			'file_upload',
			'input_multiplier'
		);
	}

	/**
	 * @return WPML_Twig_Template_Loader
	 */
	private function get_twig_loader(){
		return new WPML_Twig_Template_Loader( array( $this->sitepress->get_wp_api()->constant( 'WCML_PLUGIN_PATH' ) . self::TEMPLATE_FOLDER ) );
	}

	/**
	 * @param int|false $product
	 *
	 * @return mixed
	 */
	private function is_product_custom_prices_on( $product_id ){

		if( $product_id ){
			return get_post_meta( $product_id, '_wcml_custom_prices_status', true );
		}

		if( $this->is_global_addon_edit_page() ){
			return $this->get_global_addon_prices_status();
		}

		return false;
	}

	/**
	 * @return bool|mixed
	 */
	private function get_global_addon_prices_status() {

		if ( isset( $_GET['edit'] ) ) {
			return get_post_meta( $_GET['edit'], '_wcml_custom_prices_status', true );
		} elseif ( isset( $_POST['_wcml_custom_prices'] ) ) {
			return $_POST['_wcml_custom_prices'];
		}

		return false;
	}

	public function load_assets() {
		global $pagenow;

		$is_product_page = 'post.php' === $pagenow && isset( $_GET['post'] );
		$is_product_new_page = 'post-new.php' === $pagenow && isset( $_GET['post_type'] ) && 'product' === $_GET['post_type'];

		if ( $is_product_page || $is_product_new_page || $this->is_global_addon_edit_page() ) {
			wp_enqueue_script( 'wcml-product-addons', WCML_PLUGIN_URL . '/compatibility/res/js/wcml-product-addons' . WCML_JS_MIN . '.js', array( 'jquery' ), WCML_VERSION );
			wp_enqueue_style( 'wcml-product-addons', WCML_PLUGIN_URL . '/compatibility/res/css/wcml-product-addons.css', '', WCML_VERSION );
		}

	}

	/**
	 * @param string $product_id
	 */
	public function update_custom_prices_values( $product_id ) {

		if( $this->is_multi_currency_on() ){
			$this->save_global_addon_prices_setting( $product_id );
			$product_addons = $this->get_product_addons( $product_id );

			if ( $product_addons ) {
				$active_currencies = $this->woocommerce_wpml->multi_currency->get_currencies();

				foreach ( $product_addons as $addon_key => $product_addon ) {

					foreach ( $active_currencies as $code => $currency ) {
						$price_option_key = self::PRICE_OPTION_KEY;

						if( in_array( $product_addon['type'], $this->get_one_price_types() ) ){
							$product_addons = $this->update_single_option_prices( $product_addons, $price_option_key, $addon_key, $code );
						}else{
							$product_addons = $this->update_multiple_options_prices( $product_addons, $price_option_key, $addon_key, $code );
						}
					}
				}

				update_post_meta( $product_id, '_product_addons', $product_addons );
			}
		}
	}

	/**
	 * @param array $product_addons
	 * @param string $price_option_key
	 * @param string $addon_key
	 * @param string $code
	 *
	 * @return array
	 */
	private function update_single_option_prices( $product_addons, $price_option_key, $addon_key, $code ){
		if ( isset( $_POST[ $price_option_key ][ $addon_key ][ 'price_'.$code ][ 0 ] ) ) {
			$product_addons[ $addon_key ][ 'price_' . $code ] = wc_format_decimal( $_POST[ $price_option_key ][ $addon_key ][ 'price_'.$code ][ 0 ] );
		}

		return $product_addons;
	}

	/**
	 * @param array $product_addons
	 * @param string $price_option_key
	 * @param string $addon_key
	 * @param string $code
	 *
	 * @return array
	 */
	private function update_multiple_options_prices( $product_addons, $price_option_key, $addon_key, $code ){
		foreach ( $product_addons[ $addon_key ]['options'] as $option_key => $option ) {
			if ( isset( $_POST[ $price_option_key ][ $addon_key ][ 'price_'.$code ][ $option_key ] ) ) {
				$product_addons[ $addon_key ]['options'][ $option_key ][ 'price_' . $code ] = wc_format_decimal( $_POST[ $price_option_key ][ $addon_key ][ 'price_'.$code ][ $option_key ] );
			}
		}

		return $product_addons;
	}

	/**
	 * @param array $product_addons
	 * @param array $option
	 * @param int $loop
	 * @param string|bool $custom_prices_on
	 *
	 * @return array
	 */
	private function get_prices_dialog_model( $product_addons, $option, $loop, $custom_prices_on ) {

		$label = isset( $option['label'] ) ? $option['label'] : $option['name'];

		return array(
			'strings'           => array(
				'dialog_title' =>  __( 'Multi-currency settings', 'woocommerce-multilingual' ),
				'description'  => sprintf(__( 'Here you can set different prices for the %s in multiple currencies:', 'woocommerce-multilingual' ), '<strong>' . $label . '</strong>' ),
				'apply'        => __( 'Apply', 'woocommerce-multilingual' ),
				'cancel'       => __( 'Cancel', 'woocommerce-multilingual' )
			),
			'custom_prices_on'  => $custom_prices_on,
			'dialog_id'         => '_product_addon_option_' . md5( uniqid( $loop . $label )  ),
			'option_id'         => isset( $product_addons[ $loop ]['options'] ) ? array_search( $option, $product_addons[ $loop ]['options'] ) : '',
			'addon_id'          => $loop,
			'option_details'    => $option,
			'default_currency'  => get_option( 'woocommerce_currency' ),
			'active_currencies' => $this->woocommerce_wpml->multi_currency->get_currencies(),
		);
	}

	public function custom_prices_settings_block() {
		$twig_loader = $this->get_twig_loader();
		echo $twig_loader->get_template()->show( $this->get_custom_prices_settings_model(), self::SETTINGS_TEMPLATE );
	}

	private function get_custom_prices_settings_model() {
		return array(
			'strings'          => array(
				'label'    => __( 'Multi-currency settings', 'woocommerce-multilingual' ),
				'auto'     => __( 'Calculate prices in other currencies automatically', 'woocommerce-multilingual' ),
				'manually' => __( 'Set prices in other currencies manually', 'woocommerce-multilingual' )
			),
			'custom_prices_on' => $this->get_global_addon_prices_status(),
			'nonce'            => wp_create_nonce( 'wcml_save_custom_prices' )
		);
	}

	/**
	 * @param int $global_addon_id
	 */
	private function save_global_addon_prices_setting( $global_addon_id ) {

		$nonce = filter_var( isset( $_POST['_wcml_custom_prices_nonce'] ) ? $_POST['_wcml_custom_prices_nonce'] : '', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( isset( $_POST['_wcml_custom_prices'] ) && isset( $nonce ) && wp_verify_nonce( $nonce, 'wcml_save_custom_prices' ) ) {
			update_post_meta( $global_addon_id, '_wcml_custom_prices_status', $_POST['_wcml_custom_prices'] );
		}

	}

}
