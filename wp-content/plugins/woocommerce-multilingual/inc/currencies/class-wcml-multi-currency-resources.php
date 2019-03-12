<?php

class WCML_Multi_Currency_Resources{

    /**
     * @var WCML_Multi_Currency
     */
    static $multi_currency;
    /**
     * @var woocommerce_wpml
     */
    static $woocommerce_wpml;

    public static function set_up( WCML_Multi_Currency $multi_currency, woocommerce_wpml $woocommerce_wpml ){
        global $pagenow;

        self::$multi_currency = $multi_currency;
        self::$woocommerce_wpml = $woocommerce_wpml;

        if( !is_admin() && $pagenow != 'wp-login.php' && $woocommerce_wpml->cs_templates->get_active_templates( true ) ){
            self::load_inline_js();
        }

    }

    private static function load_inline_js(){

        wp_register_script('wcml-mc-scripts', WCML_PLUGIN_URL . '/res/js/wcml-multi-currency' . WCML_JS_MIN . '.js', array('jquery'), WCML_VERSION, true);


        $script_vars['wcml_spinner']    =  ICL_PLUGIN_URL . '/res/img/ajax-loader.gif';
        $script_vars['current_currency']= array(
            'code'  => self::$multi_currency->get_client_currency(),
            'symbol'=> get_woocommerce_currency_symbol( self::$multi_currency->get_client_currency() )
        );

	    $script_vars = self::set_cache_compatibility_variables( $script_vars );

	    wp_localize_script('wcml-mc-scripts', 'wcml_mc_settings', $script_vars );

	    wp_enqueue_script('wcml-mc-scripts');

    }

	private static function set_cache_compatibility_variables( $script_vars ) {

		$script_vars['cache_enabled'] = false;

		if (
			(int) ! empty( self::$multi_currency->W3TC ) ||
			( function_exists( 'wp_cache_is_enabled' ) && wp_cache_is_enabled() )
		) {
			$script_vars['cache_enabled'] = true;
		}else{
			global $sg_cachepress_environment;
			if ( $sg_cachepress_environment && $sg_cachepress_environment->cache_is_enabled() ) {
				$script_vars['cache_enabled'] = true;
			}
		}

		$script_vars['cache_enabled'] = apply_filters( 'wcml_is_cache_enabled_for_switching_currency', $script_vars['cache_enabled'] );

		return $script_vars;
	}

}