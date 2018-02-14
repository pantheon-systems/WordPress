<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Flex Slider specific markup, javascript, css and settings.
 */
class MetaFlexSlider extends MetaSlider {

    protected $js_function = 'flexslider';
    protected $js_path = 'sliders/flexslider/jquery.flexslider.min.js';
    protected $css_path = 'sliders/flexslider/flexslider.css';

    /**
     * Constructor
     *
     * @param integer $id slideshow ID
     */
    /**
     * Constructor
     *
     * @param int   $id                 ID
     * @param array $shortcode_settings Short code settings
     */
    public function __construct( $id, $shortcode_settings ) {
        parent::__construct( $id, $shortcode_settings );

        add_filter( 'metaslider_flex_slider_parameters', array( $this, 'enable_carousel_mode' ), 10, 2 );
        add_filter( 'metaslider_flex_slider_parameters', array( $this, 'manage_easing' ), 10, 2 );
        add_filter( 'metaslider_css', array( $this, 'get_carousel_css' ), 11, 3 );
        add_filter( 'metaslider_css_classes', array( $this, 'remove_bottom_margin' ), 11, 3 );
    }

    /**
     * Adjust the slider parameters so they're comparible with the carousel mode
     *
     * @param array   $options   Slider options
     * @param integer $slider_id Slider ID
     * @return array $options
     */
    public function enable_carousel_mode( $options, $slider_id ) {
        if ( isset( $options["carouselMode"] ) ) {
            if ( $options["carouselMode"] == "true" ) {
                $options["itemWidth"] = $this->get_setting( 'width' );
                $options["animation"] = "'slide'";
                $options["direction"] = "'horizontal'";
                $options["minItems"] = 1;
                $options["itemMargin"] = apply_filters( 'metaslider_carousel_margin', $this->get_setting( 'carouselMargin' ), $slider_id );
            }

            unset( $options["carouselMode"] );
        }

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_flex_slider_parameters', array( $this, 'enable_carousel_mode' ), 10, 2 );

        return $options;
    }

    /**
     * Ensure CSS transitions are disabled when easing is enabled.
     *
     * @param array   $options   Slider options
     * @param integer $slider_id Slider ID
     * @return array $options
     */
    public function manage_easing( $options, $slider_id ) {

        if ( $options["animation"] == '"fade"' ) {
            unset( $options['easing'] );
        }

        if ( isset( $options["easing"] ) && $options["easing"] != '"linear"' ) {
            $options['useCSS'] = 'false';
        }


        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_flex_slider_parameters', array( $this, 'manage_easing' ), 10, 2 );

        return $options;
    }

    /**
     * Add a 'nav-hidden' class to slideshows where the navigation is hidde
     *
     * @param  string $class    Slider class
     * @param  int    $id       Slider ID
     * @param  array  $settings Slider Settings
     * @return string
     */
    public function remove_bottom_margin( $class, $id, $settings ) {
        if ( isset( $settings["navigation"] ) && $settings['navigation'] == 'false' ) {
            return $class .= " nav-hidden";
        }

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_css_classes', array( $this, 'remove_bottom_margin' ), 11, 3 );

        return $class;
    }

    /**
     * Return css to ensure our slides are rendered correctly in the carousel
     *
     * @param string  $css       Css
     * @param array   $settings  Css settings
     * @param integer $slider_id SliderID
     * @return string $css
     */
    public function get_carousel_css( $css, $settings, $slider_id ) {
        if ( isset( $settings["carouselMode"] ) && $settings['carouselMode'] == 'true' ) {
            $margin = apply_filters( 'metaslider_carousel_margin', $this->get_setting( 'carouselMargin' ), $slider_id );
            $css .= "\n        #metaslider_{$slider_id}.flexslider .slides li {margin-right: {$margin}px !important;}";
        }

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_css', array( $this, 'get_carousel_css' ), 11, 3 );

        return $css;
    }

    /**
     * Enable the parameters that are accepted by the slider
     *
     * @param  string $param Parameters
     * @return array|boolean enabled parameters (false if parameter doesn't exist)
     */
    protected function get_param( $param ) {
        $params = array(
            'effect' => 'animation',
            'direction' => 'direction',
            'prevText' => 'prevText',
            'nextText' => 'nextText',
            'delay' => 'slideshowSpeed',
            'animationSpeed' => 'animationSpeed',
            'hoverPause' => 'pauseOnHover',
            'reverse' => 'reverse',
            'navigation' => 'controlNav',
            'links' =>'directionNav',
            'carouselMode' => 'carouselMode',
            'easing' => 'easing',
            'autoPlay' => 'slideshow'
        );

        if ( isset( $params[$param] ) ) {
            return $params[$param];
        }

        return false;
    }

    /**
     * Include slider assets
     */
    public function enqueue_scripts() {
        parent::enqueue_scripts();

        if ( $this->get_setting( 'printJs' ) == 'true' && ( $this->get_setting( 'effect' ) == 'slide' || $this->get_setting( 'carouselMode' ) == 'true' ) ) {
            wp_enqueue_script( 'metaslider-easing', METASLIDER_ASSETS_URL . 'easing/jQuery.easing.min.js', array( 'jquery' ), METASLIDER_VERSION );
        }
    }

    /**
     * Build the HTML for a slider.
     *
     * @return string slider markup.
     */
    protected function get_html() {
        $class = $this->get_setting( 'noConflict' ) == 'true' ? "" : ' class="flexslider"';

        $return_value = '<div id="' . $this->get_identifier() . '"' . $class . '>';
        $return_value .= "\n            <ul class=\"slides\">";

        foreach ( $this->slides as $slide ) {
            // backwards compatibility with older versions of MetaSlider Pro (< v2.0)
            // MS Pro < 2.0 does not include the <li>
            // MS Pro 2.0+ returns the <li>
            if ( strpos( $slide, '<li' ) === 0 ) {
                $return_value .= "\n                " . $slide;
            } else {
                $return_value .= "\n                <li style=\"display: none;\">" . $slide . "</li>";
            }
        }

        $return_value .= "\n            </ul>";
        $return_value .= "\n        </div>";

        // show the first slide
        if ($this->get_setting('carouselMode') != 'true') {
            $return_value =  preg_replace('/none/', 'block', $return_value, 1);
        }

        return apply_filters( 'metaslider_flex_slider_get_html', $return_value, $this->id, $this->settings );
    }
}