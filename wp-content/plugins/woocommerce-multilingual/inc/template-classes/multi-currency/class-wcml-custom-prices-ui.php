<?php

/**
 * Created by OnTheGo Systems
 */
class WCML_Custom_Prices_UI extends WPML_Templates_Factory {

	private $woocommerce_wpml;
	private $product_id;
	private $custom_prices;
	private $is_variation;


	function __construct( &$woocommerce_wpml, $product_id ){
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->product_id = $product_id;
		$this->is_variation = get_post_type( $product_id) == 'product_variation' ? true : false;
		$this->custom_prices = get_post_custom( $product_id );
		$this->custom_prices_fields = apply_filters( 'wcml_custom_prices_fields', array( '_regular_price', '_sale_price' ), $product_id );
		$this->custom_prices_fields_labels = apply_filters( 'wcml_custom_prices_fields_labels', array( '_regular_price' => __( 'Regular Price', 'woocommerce-multilingual' ), '_sale_price' => __( 'Sale Price', 'woocommerce-multilingual' ) ), $product_id );
	}

	public function get_model() {
		$model = array(
			'product_id' => $this->product_id,
			'currencies' => $this->get_currencies_info(),
			'checked_calc_auto' => !isset($this->custom_prices['_wcml_custom_prices_status']) || (isset($this->custom_prices['_wcml_custom_prices_status']) && $this->custom_prices['_wcml_custom_prices_status'][0] == 0)? 'checked="checked"' : '' ,
			'checked_calc_manually' => isset($this->custom_prices['_wcml_custom_prices_status']) && $this->custom_prices['_wcml_custom_prices_status'][0] == 1?'checked="checked"':'',
			'wc_currencies' => get_woocommerce_currencies(),
			'is_variation' => $this->is_variation,
			'html_id' => $this->is_variation ? '['.$this->product_id.']' : '',
			'strings' => apply_filters( 'wcml_custom_prices_strings', array(
				'not_set' => sprintf( __( 'Multi-currency is enabled, but no secondary currencies have been set. %sAdd secondary currency%s.',
					'woocommerce-multilingual' ), '<a href="' . admin_url('admin.php?page=wpml-wcml&tab=multi-currency') . '">', '</a>' ),
				'calc_auto' => __( 'Calculate prices in other currencies automatically', 'woocommerce-multilingual' ),
				'see_prices' => __( 'Click to see the prices in the other currencies as they are currently shown on the front end.', 'woocommerce-multilingual' ),
				'show' => __( 'Show', 'woocommerce-multilingual' ),
				'hide' => __( 'Hide', 'woocommerce-multilingual' ),
				'set_manually' => __( 'Set prices in other currencies manually', 'woocommerce-multilingual' ),
				'enter_prices' => __( 'Enter prices in other currencies', 'woocommerce-multilingual' ),
				'hide_prices' => __( 'Hide prices in other currencies', 'woocommerce-multilingual' ),
				'det_auto' => __( 'Determined automatically based on exchange rate', 'woocommerce-multilingual' ),
				'_regular_price' => __( 'Regular Price', 'woocommerce-multilingual' ),
				'_sale_price' => __( 'Sale Price', 'woocommerce-multilingual' ),
				'schedule' => __( 'Schedule', 'woocommerce-multilingual' ),
				'same_as_def' => __( 'Same as default currency', 'woocommerce-multilingual' ),
				'set_dates' => __( 'Set dates', 'woocommerce-multilingual' ),
				'collapse' => __( 'Collapse', 'woocommerce-multilingual' ),
				'from' => __( 'From&hellip;', 'woocommerce-multilingual' ),
				'to' => __( 'To&hellip;', 'woocommerce-multilingual' ),
				'enter_price' => __( 'Please enter in a value less than the regular price', 'woocommerce-multilingual' ) ),
				$this->product_id
			)
		);

		return $model;
	}


	public function get_currencies_info( ){

		$currencies = $this->woocommerce_wpml->multi_currency->get_currencies();
		$wc_currencies = get_woocommerce_currencies();

		foreach( $currencies as $key => $currency ){

			$currencies[ $key ][ 'currency_code' ] = $key;

			foreach( $this->custom_prices_fields as $price_field ){
				$currencies[ $key ][ 'readonly_price' ][ $price_field ] = '';
				$currencies[ $key ][ 'custom_price' ][ $price_field ] = '';
			}

			if( $this->product_id ){

				foreach( $this->custom_prices_fields as $price_field ){
					$currencies[ $key ][ 'readonly_price' ][ $price_field ] = get_post_meta( $this->product_id, $price_field, true );
					if( $currencies[ $key ][ 'readonly_price' ][ $price_field ] ){
						$currencies[ $key ][ 'readonly_price' ][ $price_field ] = $currencies[ $key ][ 'readonly_price' ][ $price_field ]*$currency['rate'];
						$currencies[ $key ][ 'readonly_price' ][ $price_field ] = wc_format_localized_price(  $currencies[ $key ][ 'readonly_price' ][ $price_field ] );
					}
				}

			}

			if( isset( $this->custom_prices[ '_wcml_custom_prices_status' ] ) ){

				foreach( $this->custom_prices_fields as $price_field ){
					if( isset( $this->custom_prices[ $price_field.'_'.$key ][ 0 ] ) ){
						$currencies[ $key ][ 'custom_price' ][ $price_field ] = wc_format_localized_price( $this->custom_prices[ $price_field.'_'.$key ][ 0 ] );
					}
				}

			}

			$currencies[ $key ][ 'currency_format' ] = $wc_currencies[ $key ].' ( '.get_woocommerce_currency_symbol( $key ).' )';
			$currencies[ $key ][ 'currency_symbol' ] = get_woocommerce_currency_symbol( $key );

			if( $this->is_variation ){
				$currencies[ $key ][ 'custom_id' ] = '['.$key.']['.$this->product_id.']';
			}else{
				$currencies[ $key ][ 'custom_id' ] = '['.$key.']';

				$wc_input = array();

				$wc_input['custom_attributes'] = array() ;
				$wc_input['type_name'] = 'data_type';
				$wc_input['type_val'] = 'price';

				foreach( $this->custom_prices_fields as $price_field ){
					ob_start();
					woocommerce_wp_text_input(
						array(
							'id' => '_custom'.$price_field.'['.$key.']',
							'value'=> wc_format_localized_price( $currencies[ $key ][ 'custom_price' ][ $price_field ] ),
							'class' => 'wc_input_price wcml_input_price short wcml'.$price_field,
							'label' => $this->custom_prices_fields_labels[ $price_field ] . ' ('. $currencies[ $key ][ 'currency_symbol' ].')',
							$wc_input['type_name'] => $wc_input['type_val'],
							'custom_attributes' => $wc_input['custom_attributes']
						)
					);
					$currencies[ $key ][ 'custom_html' ][ $price_field ] = ob_get_contents();
					ob_end_clean();
				}

				$wc_input['custom_attributes'] = array( 'readonly' => 'readonly', 'rel'=> $currency['rate'] ) ;

				foreach( $this->custom_prices_fields as $price_field ){
					ob_start();
					woocommerce_wp_text_input(
						array(
							'id' => '_readonly'.$price_field,
							'value'=> wc_format_localized_price( $currencies[ $key ][ 'readonly_price' ][ $price_field ] ),
							'class' => 'wc_input_price short',
							'label' => $this->custom_prices_fields_labels[ $price_field ] . ' ('. $currencies[ $key ][ 'currency_symbol' ] .')',
							$wc_input['type_name'] => $wc_input['type_val'],
							'custom_attributes' => $wc_input['custom_attributes']
						)
					);
					$currencies[ $key ][ 'readonly_html' ][ $price_field ] = ob_get_contents();
					ob_end_clean();
				}
			}

			$currencies[ $key ][ 'schedule_auto_checked' ] = (!isset($this->custom_prices['_wcml_schedule_'.$key]) || (isset($this->custom_prices['_wcml_schedule_'.$key]) && $this->custom_prices['_wcml_schedule_'.$key][0] == 0))?'checked="checked"':' ';
			$currencies[ $key ][ 'schedule_man_checked' ] =  isset($this->custom_prices['_wcml_schedule_'.$key]) && $this->custom_prices['_wcml_schedule_'.$key][0] == 1?'checked="checked"':' ';


			$currencies[ $key ][ 'sale_price_dates_from' ] 	= (isset($this->custom_prices['_sale_price_dates_from_'.$key]) && $this->custom_prices['_sale_price_dates_from_'.$key][0] != '') ? date_i18n( 'Y-m-d', $this->custom_prices['_sale_price_dates_from_'.$key][0] ) : '';
			$currencies[ $key ][ 'sale_price_dates_to' ] 	= (isset($this->custom_prices['_sale_price_dates_to_'.$key])  && $this->custom_prices['_sale_price_dates_to_'.$key][0] != '') ? date_i18n( 'Y-m-d', $this->custom_prices['_sale_price_dates_to_'.$key][0] ) : '';

		}

		return $currencies;

	}


	public function init_template_base_dir() {
		$this->template_paths = array(
			WCML_PLUGIN_PATH . '/templates/multi-currency/',
		);
	}

	public function get_template() {
		return 'custom-prices.twig';
	}

}