<?php

class WCML_Setup_Attributes_UI extends WPML_Templates_Factory {

    private $woocommerce_wpml;
    private $next_step_url;

    function __construct( &$woocommerce_wpml, $next_step_url ){
        parent::__construct();

        $this->woocommerce_wpml = &$woocommerce_wpml;
        $this->next_step_url = $next_step_url;

    }

    public function get_model(){

        $wc_attributes = wc_get_attribute_taxonomies();
        $wc_attributes_translated = $this->woocommerce_wpml->attributes->get_translatable_attributes();
        $attribute_names = array();
        foreach( $wc_attributes_translated as $attribute ){
            $attribute_names[] = $attribute->attribute_name;
        }

        $attributes = array();
        foreach($wc_attributes as $attribute){
            $attributes[] = array(
                'name'          => $attribute->attribute_name,
                'label'         => $attribute->attribute_label,
                'translated'    => in_array( $attribute->attribute_name, $attribute_names )
            );
        }

        $model = array(
            'strings' => array(
                'step_id'       => 'attributes_step',
                'heading'       => __('Select Translatable Attributes', 'woocommerce-multilingual'),
                'no_attributes' => __('There are no attributes defined', 'woocommerce-multilingual'),
                'continue'      => __('Continue', 'woocommerce-multilingual'),
                'later'         => __('Later', 'woocommerce-multilingual')
            ),
            'attributes'        => $attributes,
            'continue_url'  => $this->next_step_url
        );

        return $model;

    }

    private function is_translatable_attribute(){

    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return '/setup/attributes.twig';
    }


}