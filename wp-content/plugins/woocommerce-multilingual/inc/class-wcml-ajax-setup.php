<?php
  
  
class WCML_Ajax_Setup{

	/**
	 * @var SitePress
	 */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {

		$this->sitepress = $sitepress;
	}

    public function add_hooks(){

	    add_action( 'init', array( $this, 'init' ) );
	    add_action( 'wcml_localize_woocommerce_on_ajax', array( $this, 'wcml_localize_woocommerce_on_ajax' ) );

	    //@deprecated 3.9 Use 'wcml_localize_woocommerce_on_ajax' instead
	    add_action( 'localize_woocommerce_on_ajax', array( $this, 'localize_woocommerce_on_ajax' ) );

	    add_action( 'woocommerce_ajax_get_endpoint', array( $this, 'add_language_to_endpoint' ) );
    }

	public function init() {
		if ( wpml_is_ajax() ) {
			do_action( 'wcml_localize_woocommerce_on_ajax' );
		}

		if ( $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'WC_VERSION' ), '3.3', '<' ) ) {
			add_filter( 'woocommerce_params', array( $this, 'filter_woocommerce_ajax_params' ) );

			add_filter( 'wc_checkout_params', array( $this, 'add_language_parameter_to_ajax_url' ) );
			add_filter( 'wc_cart', array( $this, 'add_language_parameter_to_ajax_url' ) );
			add_filter( 'wc_cart_fragments_params', array( $this, 'add_language_parameter_to_ajax_url' ) );
			add_filter( 'wc_add_to_cart_params', array( $this, 'add_language_parameter_to_ajax_url' ) );
		} else {
			add_filter( 'woocommerce_get_script_data', array( $this, 'add_language_parameter_to_ajax_url' ) );
		}

		add_action( 'woocommerce_checkout_order_review', array( $this, 'filter_woocommerce_order_review' ), 9 );
		add_action( 'woocommerce_checkout_order_review', array( $this, 'add_hidden_language_field' ) );
		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'filter_woocommerce_order_review' ), 9 );

	}
    
    function filter_woocommerce_order_review(){                
        global $woocommerce;
        unload_textdomain('woocommerce');
        $woocommerce->load_plugin_textdomain();
    }

	function add_hidden_language_field() {
		do_action( 'wpml_add_language_form_field' );
	}

	function add_language_parameter_to_ajax_url( $woocommerce_params ) {

		if ( isset( $woocommerce_params['ajax_url'] ) && $this->sitepress->get_current_language() !== $this->sitepress->get_default_language() ) {
			$woocommerce_params['ajax_url'] = add_query_arg( 'lang', $this->sitepress->get_wp_api()->constant( 'ICL_LANGUAGE_CODE' ), $woocommerce_params['ajax_url'] );
		}

		return $woocommerce_params;
	}
    
    function filter_woocommerce_ajax_params($woocommerce_params){
        global $post;

        $value = $woocommerce_params;

        if($this->sitepress->get_current_language() !== $this->sitepress->get_default_language()){
        	if( isset( $value['ajax_url'] ) ){
		        $value['ajax_url'] = add_query_arg('lang', ICL_LANGUAGE_CODE, $woocommerce_params['ajax_url']);
		        if( isset( $value['checkout_url'] ) ){
			        $value['checkout_url'] = add_query_arg('action', 'woocommerce-checkout', $value['ajax_url']);
		        }
	        }
        }

        if(!isset($post->ID)){
            return $value;
        }

        $ch_pages = wp_cache_get('ch_pages', 'wcml_ch_pages');

        if(empty($ch_pages)){

            $ch_pages = array(

                'checkout_page_id' => get_option('woocommerce_checkout_page_id'),
                'pay_page_id' => get_option('woocommerce_pay_page_id'),
                'cart_page_id' => get_option('woocommerce_cart_page_id'));

            $ch_pages['translated_checkout_page_id'] = apply_filters( 'translate_object_id',$ch_pages['checkout_page_id'], 'page', false);
            $ch_pages['translated_pay_page_id'] = apply_filters( 'translate_object_id',$ch_pages['pay_page_id'], 'page', false);
            $ch_pages['translated_cart_page_id'] = apply_filters( 'translate_object_id',$ch_pages['cart_page_id'], 'page', false);

        }

        wp_cache_set( 'ch_pages', $ch_pages, 'wcml_ch_pages' );

        if($ch_pages['translated_cart_page_id'] == $post->ID){
            $value['is_cart'] = 1;
            $value['cart_url'] = get_permalink($ch_pages['translated_cart_page_id']);
        } else if($ch_pages['translated_checkout_page_id'] == $post->ID || $ch_pages['checkout_page_id'] == $post->ID){
            $value['is_checkout'] = 1;

            $_SESSION['wpml_globalcart_language'] = $this->sitepress->get_current_language();

        } else if($ch_pages['translated_pay_page_id'] == $post->ID){
            $value['is_pay_page'] = 1;
        }

        return $value;
    }

	public function wcml_localize_woocommerce_on_ajax() {
		$action         = isset( $_POST['action'] ) ? filter_var( $_POST['action'], FILTER_SANITIZE_STRING ) : false;
		$is_ajax_action = $action
		                  && in_array( $action,
				array(
					'wcml_product_data',
					'wpml_translation_dialog_save_job',
					'edit-theme-plugin-file',
					'search-install-plugins'
				),
				true );
		if ( $action && ( $is_ajax_action || ! apply_filters( 'wcml_is_localize_woocommerce_on_ajax', true, $action ) ) ) {
			return;
		}

        $current_language = $this->sitepress->get_current_language();

	    $this->sitepress->switch_lang($current_language, true);
    }

	/**
	 * @param $endpoint string
	 *
	 * Adds a language parameter to the url when different domains for each language are used
	 *
	 * @return string
	 */
	public function add_language_to_endpoint( $endpoint ){

		$is_per_domain = WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN === (int) $this->sitepress->get_setting( 'language_negotiation_type' );
		if( $is_per_domain && $this->sitepress->get_current_language() != $this->sitepress->get_default_language() ){

			$endpoint = add_query_arg('lang',  $this->sitepress->get_current_language(), remove_query_arg( 'lang', $endpoint ) );
            $endpoint = urldecode($endpoint);

		}

		return $endpoint;
	}


	/**
     * @deprecated 3.9
     */
    function localize_woocommerce_on_ajax(){
        $this->wcml_localize_woocommerce_on_ajax();
    }
    
    
} 
