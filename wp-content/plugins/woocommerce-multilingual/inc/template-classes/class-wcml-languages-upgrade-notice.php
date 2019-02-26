<?php

class WCML_Languages_Upgrade_Notice extends WPML_Templates_Factory {

    private $notices;

    function __construct( $notices ){
        parent::__construct();

        $this->notices = $notices;
    }

    public function get_model(){

        $model = array(
            'notices' => implode(', ', $this->notices ),
            'is_multisite' => is_multisite(),
            'strings' => array(
                'update_trnsl' => __('Update Translation', 'woocommerce-multilingual'),
                'hide' => __('Hide This Message', 'woocommerce-multilingual'),
                'trnsl_available' => sprintf( __( '<strong>WooCommerce Translation Available</strong> &#8211; Install or update your <code>%s</code> translations to version <code>%s</code>.', 'woocommerce-multilingual' ), implode(', ', $this->notices ), WC_VERSION )
            ),
            'nonces' => array(
                'debug_action' => wp_nonce_url( admin_url( 'admin.php?page=wc-status&tab=tools&action=translation_upgrade' ), 'debug_action' ),
                'upgrade_translations' => wp_nonce_url( add_query_arg( array( 'action' => 'do-translation-upgrade' ), admin_url( 'update-core.php' ) ), 'upgrade-translations' ),
                'hide_notice' => wp_create_nonce('hide_wcml_translations_message')
            )
        );

        return $model;

    }

    protected function init_template_base_dir() {
        $this->template_paths = array(
            WCML_PLUGIN_PATH . '/templates/',
        );
    }

    public function get_template() {
        return 'languages-notice.twig';
    }

}