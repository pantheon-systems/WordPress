<?php

class WCML_WC_Gateways{

    private $current_language;
    private $sitepress;

    function __construct( &$woocommerce_wpml, &$sitepress ){

        $this->sitepress = $sitepress;
        $this->woocommerce_wpml = $woocommerce_wpml;

        add_action( 'init', array( $this, 'init' ), 11 );

        $this->current_language = $sitepress->get_current_language();
        if( $this->current_language == 'all' ){
            $this->current_language = $sitepress->get_default_language();
        }

        add_filter( 'woocommerce_payment_gateways', array( $this, 'loaded_woocommerce_payment_gateways' ) );

    }

    function init(){
        global $pagenow;

        add_filter('woocommerce_gateway_title', array($this, 'translate_gateway_title'), 10, 2);
        add_filter('woocommerce_gateway_description', array($this, 'translate_gateway_description'), 10, 2);

        if( is_admin() && $pagenow == 'admin.php' && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wc-settings' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'checkout' ){
            add_action( 'admin_footer', array($this, 'show_language_links_for_gateways' ) );
            $this->register_and_set_gateway_strings_language();
        }

    }


    function loaded_woocommerce_payment_gateways( $load_gateways ){

        foreach( $load_gateways as $key => $gateway ){

            $load_gateway = is_string( $gateway ) ? new $gateway() : $gateway;
            $this->payment_gateways_filters( $load_gateway );
            $load_gateways[ $key ] = $load_gateway;

        }

        return $load_gateways;
    }

    function payment_gateways_filters( $gateway ){

        if( isset( $gateway->id ) ){
            $gateway_id = $gateway->id;
            $this->translate_gateway_strings( $gateway );
        }

    }

    function translate_gateway_strings( $gateway ){

        if( isset( $gateway->enabled ) && $gateway->enabled != 'no' ){

            if( isset( $gateway->instructions ) ){
                $gateway->instructions = $this->translate_gateway_instructions( $gateway->instructions, $gateway->id );
            }

            if( isset( $gateway->description ) ){
                $gateway->description = $this->translate_gateway_description( $gateway->description, $gateway->id );
            }

            if( isset( $gateway->title ) ){
                $gateway->title = $this->translate_gateway_title( $gateway->title, $gateway->id );
            }
        }

        return $gateway;

    }

    function translate_gateway_title( $title, $gateway_id, $language = false ) {
        $title = apply_filters( 'wpml_translate_single_string', $title, 'woocommerce', $gateway_id .'_gateway_title', $language ? $language : $this->current_language );
        return $title;
    }

    function translate_gateway_description( $description, $gateway_id) {
        $description = apply_filters( 'wpml_translate_single_string', $description, 'woocommerce', $gateway_id . '_gateway_description', $this->current_language );
        return $description;
    }

    function translate_gateway_instructions( $instructions, $gateway_id ){
        $instructions = apply_filters( 'wpml_translate_single_string', $instructions, 'woocommerce', $gateway_id . '_gateway_instructions', $this->current_language );
        return $instructions;
    }

    function show_language_links_for_gateways(){

        $text_keys = $this->get_gateway_text_keys_to_translate();

        $wc_payment_gateways = WC_Payment_Gateways::instance();

        foreach( $wc_payment_gateways->payment_gateways() as $payment_gateway ) {

            if( isset( $_GET['section'] ) && $_GET['section'] == $payment_gateway->id ){

                foreach( $text_keys as $text_key ) {

                    if ( isset( $payment_gateway->settings[ $text_key ] ) ) {
                        $setting_value = $payment_gateway->settings[ $text_key ];
                    }elseif( $text_key === 'instructions' ){
                        $setting_value = $payment_gateway->description;
                    }else{
                        $setting_value = $payment_gateway->$text_key;
                    }

                    $input_name = $payment_gateway->plugin_id.$payment_gateway->id.'_'.$text_key;
                    $gateway_option = $payment_gateway->plugin_id.$payment_gateway->id.'_settings';

                    $lang_selector = new WPML_Simple_Language_Selector( $this->sitepress );
                    $language = $this->woocommerce_wpml->strings->get_string_language( $setting_value, 'woocommerce', $payment_gateway->id .'_gateway_'. $text_key );
                    if( is_null( $language ) ) {
                        $language = $this->sitepress->get_default_language();
                    }

                    $lang_selector->render( array(
                            'id' => $gateway_option.'_'.$text_key.'_language_selector',
                            'name' => 'wcml_lang-'.$gateway_option.'-'.$text_key,
                            'selected' => $language,
                            'show_please_select' => false,
                            'echo' => true,
                            'style' => 'width: 18%;float: left;margin-top: 3px;'
                        )
                    );

                    $st_page = admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php&context=woocommerce&search='.esc_attr( preg_replace("/[\n\r]/","",$setting_value) ) );
                    ?>
                    <script>
                        var input = jQuery('#<?php echo esc_js( $input_name ); ?>');
                        if ( input.length > 0 ) {
                            input.parent().append('<div class="translation_controls"></div>');
                            input.parent().find('.translation_controls').append('<a href="<?php echo $st_page ?>" style="margin-left: 10px"><?php _e('translations', 'woocommerce-multilingual') ?></a>');
                            jQuery('#<?php echo $gateway_option.'_'.$text_key.'_language_selector' ?>').prependTo( input.parent().find('.translation_controls') );
                        }else{
                            jQuery('#<?php echo $gateway_option.'_'.$text_key.'_language_selector' ?>').remove();
                        }
                    </script>
                <?php }
            }
        }
    }

    function register_and_set_gateway_strings_language(){

	    foreach( $_POST as $key => $value ){

		    if( '_enabled' === substr( $key, -8 ) ){
			    $gateway = str_replace( '_enabled', '',  $key );
			    $gateway_settings = get_option( $gateway.'_settings', array() );
            }
	    }

	    if ( isset( $gateway ) ) {
		    $text_keys = $this->get_gateway_text_keys_to_translate();

		    foreach ( $text_keys as $text_key ) {
			    $gateway_string_name = str_replace( 'woocommerce_', '', $gateway ) . '_gateway_' . $text_key;
			    $gateway_key         = $gateway . '_' . $text_key;
			    $context = 'woocommerce';

			    $string_value     = isset( $_POST[ $gateway_key ] ) ? $_POST[ $gateway_key ] : '';
			    $opt_string_value = isset( $gateway_settings[ $text_key ] ) ? $gateway_settings[ $text_key ] : $string_value;

			    $language_key = 'wcml_lang-' . $gateway . '_settings-' . $text_key;
			    $language     = isset( $_POST[ $language_key ] ) ? $_POST[ $language_key ] : $this->woocommerce_wpml->strings->get_string_language( $opt_string_value, $context );

			    do_action( 'wpml_register_single_string', $context, $gateway_string_name, $string_value, false, $language );

			    $this->woocommerce_wpml->strings->set_string_language( $string_value, $context, $gateway_string_name, $language );
		    }
	    }

    }

    public function get_gateway_text_keys_to_translate(){

	    $text_keys = array(
		    'title',
		    'description',
		    'instructions'
	    );

        return apply_filters( 'wcml_gateway_text_keys_to_translate', $text_keys );
    }

}