<?php

class WCML_Setup_Translation_Options_UI extends WPML_Templates_Factory {

    private $woocommerce_wpml;
    private $next_step_url;

	function __construct( $woocommerce_wpml, $next_step_url ) {
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->next_step_url    = $next_step_url;

	}

    public function get_model(){

	    $custom_posts_unlocked = apply_filters( 'wpml_get_setting', false, 'custom_posts_unlocked_option' );
	    $custom_posts_sync     = apply_filters( 'wpml_get_setting', false, 'custom_posts_sync_option' );

		$is_display_as_translated_checked = 1 === $custom_posts_unlocked[ 'product' ] && WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED === $custom_posts_sync[ 'product' ];

        $model = array(
            'strings' => array(
                'step_id'          => 'translation_options_step',
                'heading'          => __( 'Translation Options', 'woocommerce-multilingual' ),
                'description'      => __( "Normally, you first create products in the site's default language and then you translate them. If products are not translated, do you want to show them on other languages?", 'woocommerce-multilingual' ),
                'label_display_as_translated' => sprintf( __( 'Yes - show products even if they are not yet translated (%smore on how this will work%s)',
                    'woocommerce-multilingual' ),
                    '<a target="blank" href="https://wpml.org/documentation/related-projects/woocommerce-multilingual/displaying-untranslated-products-in-secondary-languages/">', '</a>'
                ),
                'label_translated'        => __( 'No - only display products on other languages once they are translated', 'woocommerce-multilingual' ),
                'description_footer'        => __( 'Note, to change this later, go to %sWPML &raquo; Settings &raquo; Post Types Translation.%s', 'woocommerce-multilingual' ),
                'continue'         => __( 'Continue', 'woocommerce-multilingual' ),
            ),
            'is_display_as_translated_checked' => $is_display_as_translated_checked,
            'continue_url'  => $this->next_step_url
        );

        return $model;

    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return '/setup/translation-options.twig';
    }


}