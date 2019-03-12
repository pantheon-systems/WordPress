<?php

class WCML_St_Taxonomy_UI extends WPML_Templates_Factory {

    private $taxonomy_obj;

    function __construct( $taxonomy_obj ){
        parent::__construct();

        $this->taxonomy_obj = $taxonomy_obj;
    }

    public function get_model(){

        $model = array(
        	'link_url' => admin_url( 'admin.php?page=wpml-wcml&tab=slugs' ),
        	'link_label' => sprintf( __( 'Set different slugs in different languages for %s on WooCommerce Multilingual URLs translations page', 'woocommerce-multilingual' ), $this->taxonomy_obj->labels->name )
        );

        return $model;

    }

    public function render(){

    	return $this->show();
    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return 'st-taxonomy-ui.twig';
    }

}