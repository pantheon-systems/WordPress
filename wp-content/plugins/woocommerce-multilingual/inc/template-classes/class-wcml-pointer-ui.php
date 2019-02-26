<?php

class WCML_Pointer_UI extends WPML_Templates_Factory {

    private $content;
    private $doc_link;
    private $selector;
    private $insert_method;

    function __construct( $content, $doc_link, $insert_after_selector_id = false, $insert_method = false ){
        parent::__construct();

        $this->content = $content;
        $this->doc_link = $doc_link;
        $this->selector = $insert_after_selector_id;
        $this->insert_method = $insert_method;

    }

    public function get_model(){

        $model = array(
            'pointer' => md5( rand( 0, 100 ) ),
            'description' => array(
                'content'   => $this->content,
                'trnsl_title' => __( 'How to translate this?', 'woocommerce-multilingual' ),
                'doc_link'  => $this->doc_link,
                'doc_link_text'     => __( 'Learn more', 'woocommerce-multilingual' ),
            ),
            'selector' => $this->selector,
            'insert_method' => $this->insert_method
        );

        return $model;

    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return 'pointer-ui.twig';
    }


}