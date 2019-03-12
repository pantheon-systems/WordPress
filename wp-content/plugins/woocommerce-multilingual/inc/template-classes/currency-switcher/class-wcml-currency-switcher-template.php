<?php

class WCML_Currency_Switcher_Template extends WPML_Templates_Factory {

    const FILENAME = 'template.twig';

    /* @var array $template */
    private $template;

    /* @var string $prefix */
    private $prefix = 'wcml-cs-';

    /**
     * @var woocommerce_wpml
     */
    private $woocommerce_wpml;


    function __construct( &$woocommerce_wpml, $template_data ){

        $this->woocommerce_wpml =& $woocommerce_wpml;

        $this->template = $this->format_data( $template_data );

        if ( array_key_exists( 'template_string', $this->template ) ) {
            $this->template_string = $this->template['template_string'];
        }

        $functions = array(
            new Twig_SimpleFunction( 'get_formatted_price', array( $this, 'get_formatted_price' ) )
        );

        parent::__construct( $functions );

    }

    /**
     * @param array $model
     */
    public function set_model( $model ) {
        $this->model = is_array( $model ) ? $model : array( $model );
    }

    /**
     * @return array
     */
    public function get_model() {
        return $this->model;
    }

    public function render(){
        echo $this->get_view();
    }

    public function get_formatted_price( $currency, $format ){
        $wc_currencies = get_woocommerce_currencies();

        $wcml_settings  =  $this->woocommerce_wpml->get_settings();
        $multi_currency = $this->woocommerce_wpml->multi_currency;

        $currency_format = preg_replace( array('#%name%#', '#%symbol%#', '#%code%#' ),
            array(
                $wc_currencies[$currency],
                get_woocommerce_currency_symbol( $currency ),
                $currency

            ), $format );

        return $currency_format;
    }

    /**
     * Make sure some elements are of array type
     *
     * @param array $template_data
     *
     * @return array
     */
    private function format_data( $template_data ) {
        foreach ( array( 'path', 'js', 'css' ) as $k ) {
            $template_data[ $k ] = isset( $template_data[ $k ] ) ? $template_data[ $k ] : array();
            $template_data[ $k ] = is_array( $template_data[ $k ] ) ? $template_data[ $k ] : array( $template_data[ $k ] );
        }

        return $template_data;
    }


    /**
     * @param bool $with_version
     *
     * @return array
     */
    public function get_styles( $with_version = false ) {
        return $with_version
            ? array_map( array( $this, 'add_resource_version' ), $this->template['css'] )
            : $this->template['css'];
    }

    /**
     * @return bool
     */
    public function has_styles() {
        return ! empty( $this->template['css'] );
    }

    /**
     * @param bool $with_version
     *
     * @return array
     */
    public function get_scripts( $with_version = false ) {
        return $with_version
            ? array_map( array( $this, 'add_resource_version' ), $this->template['js'] )
            : $this->template['js'];
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function add_resource_version( $url ) {
        return $url . '?ver=' . WCML_VERSION;
    }

    /**
     * @param int $index
     *
     * @return string
     */
    public function get_resource_handler( $index ) {
        $slug   = isset( $this->template['slug'] ) ? $this->template['slug'] : '';
        $prefix = $this->is_core() ? '' : $this->prefix;
        return $prefix . $slug . '-' . $index;
    }

    public function get_inline_style_handler() {
        $count = count( $this->template['css'] );
        return $count > 0 ? $this->get_resource_handler( $count - 1 ) : null;
    }

    protected function init_template_base_dir() {
        $this->template_paths = $this->template['path'];
    }

    /**
     * @return string Template filename
     */
    public function get_template() {
        $template = self::FILENAME;

        if ( isset( $this->template_string ) ) {
            $template = $this->template_string;
        } elseif ( array_key_exists( 'filename', $this->template ) ) {
            $template = $this->template['filename'];
        }

        return $template;
    }

    /**
     * @return array
     */
    public function get_template_data() {
        return $this->template;
    }

    /**
     * return bool
     */
    public function is_core() {
        return isset( $this->template['is_core'] ) ? (bool) $this->template['is_core'] : false;
    }

	public function is_path_valid() {
		$valid = true;
		foreach ( $this->template_paths as $path ) {
			if ( ! file_exists( $path ) ) {
				$valid = false;
				break;
			}
		}
		return $valid;
	}
}
