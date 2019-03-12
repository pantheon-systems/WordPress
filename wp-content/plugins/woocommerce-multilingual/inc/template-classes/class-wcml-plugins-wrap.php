<?php

class WCML_Plugins_Wrap {

    private $woocommerce_wpml;
    private $sitepress;

	private $twig;

	/** @var WCML_Tracking_Link */
	private $tracking_link;

    function __construct( &$woocommerce_wpml, &$sitepress ){

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;

	    $loader = new Twig_Loader_Filesystem( WCML_PLUGIN_PATH . '/templates' );
	    $this->twig = new Twig_Environment( $loader );

	    $this->tracking_link = new WCML_Tracking_Link();
    }

    public function get_model(){

        $model = array(
            'link_url' => admin_url('admin.php?page=wpml-wcml'),
            'old_wpml' => defined('ICL_SITEPRESS_VERSION') && version_compare( ICL_SITEPRESS_VERSION, '3.4', '<' ),
            'tracking_link' => $this->tracking_link->generate( 'https://wpml.org/shop/account/', false, 'account' ),
            'install_wpml_link' => $this->woocommerce_wpml->dependencies->required_plugin_install_link( 'wpml' ),
            'icl_version' => defined('ICL_SITEPRESS_VERSION'),
            'icl_setup' => $this->sitepress ? $this->sitepress->setup() : false,
            'media_version' => defined( 'WPML_MEDIA_VERSION' ),
            'tm_version' => defined( 'WPML_TM_VERSION' ),
            'st_version' => defined( 'WPML_ST_VERSION' ),
            'wc' => class_exists('WooCommerce') ,
            'old_wc' => class_exists('WooCommerce') && version_compare( WC_VERSION, '2.0', '<'),
            'wc_link' => 'http://wordpress.org/extend/plugins/woocommerce/',
            'strings' => array(
                'title'             => __('WooCommerce Multilingual', 'woocommerce-multilingual'),
                'required'=> __('Required plugins', 'woocommerce-multilingual'),
                'plugins'=> __('Plugins Status', 'woocommerce-multilingual'),
                'depends'=> __('WooCommerce Multilingual depends on several plugins to work. If any required plugin is missing, you should install and activate it.', 'woocommerce-multilingual'),
                'old_wpml_link'=> sprintf( __( 'WooCommerce Multilingual is enabled but not effective. It is not compatible with  <a href="%s">WPML</a> versions prior 2.0.5.', 'woocommerce-multilingual' ), $this->tracking_link->generate( 'https://wpml.org/' ) ),
                'update_wpml'=> __( 'Update WPML', 'woocommerce-multilingual' ),
                'upgrade_wpml'=> __( 'Upgrade WPML', 'woocommerce-multilingual' ),
                'get_wpml'=> __( 'Get WPML', 'woocommerce-multilingual' ),
                'get_wpml_media'=> __( 'Get WPML Media', 'woocommerce-multilingual' ),
                'get_wpml_tm'=> __( 'Get WPML Translation Management', 'woocommerce-multilingual' ),
                'get_wpml_st'=> __( 'Get WPML String Translation', 'woocommerce-multilingual' ),
                'new_design_wpml_link'=> sprintf( __( 'You are using WooCommerce Multilingual %s. This version includes an important UI redesign for the configuration screens and it requires <a href="%s">WPML %s</a> or higher. Everything still works on the front end now but, in order to configure options for WooCommerce Multilingual, you need to upgrade WPML.', 'woocommerce-multilingual' ), WCML_VERSION, $this->tracking_link->generate( 'https://wpml.org/' ), '3.4' ),
                'wpml' => '<strong>WPML</strong>',
                'media' => '<strong>WPML Media</strong>',
                'tm' => '<strong>WPML Translation Management</strong>',
                'st' => '<strong>WPML String Translation</strong>',
                'wc' => '<strong>WooCommerce</strong>',
                'inst_active' => __( '%s is installed and active.', 'woocommerce-multilingual' ),
                'is_setup' => __( '%s is set up.', 'woocommerce-multilingual' ),
                'not_setup' => __( '%s is not set up.', 'woocommerce-multilingual' ),
                'not_inst' => __( '%s is either not installed or not active.', 'woocommerce-multilingual' ),
                'wpml_not_inst' => sprintf( __( '%s is either not installed or not active.', 'woocommerce-multilingual' ),'<strong><a title="' . esc_attr__('The WordPress Multilingual Plugin', 'woocommerce-multilingual') .'" href="' . $this->tracking_link->generate( 'https://wpml.org/' ) . '">WPML</a></strong>' ),
                'old_wc' => sprintf( __( '%1$s  is installed, but with incorrect version. You need %1$s %2$s or higher. ', 'woocommerce-multilingual' ), '<strong>WooCommerce</strong>', '2.0' ),
                'download_wc' => __( 'Download WooCommerce', 'woocommerce-multilingual' ),
            )
        );

        return $model;

    }

    public function show(){
	    $template = $this->twig->load( 'plugins-wrap.twig' );
	    echo $template->render( $this->get_model() );
    }

}