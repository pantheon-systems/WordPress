<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Responsive Slider specific markup, javascript, css and settings.
 */
class MetaResponsiveSlider extends MetaSlider {

    protected $js_function = 'responsiveSlides';
    protected $js_path = 'sliders/responsiveslides/responsiveslides.min.js';
    protected $css_path = 'sliders/responsiveslides/responsiveslides.css';

    /**
     * Detect whether thie slide supports the requested setting,
     * and if so, the name to use for the setting in the Javascript parameters
     *
     * @param  array $param Parameters
     * @return false (parameter not supported) or parameter name (parameter supported)
     */
    protected function get_param( $param ) {
        $params = array(
            'prevText' => 'prevText',
            'nextText' => 'nextText',
            'delay' => 'timeout',
            'animationSpeed' => 'speed',
            'hoverPause' => 'pause',
            'navigation' => 'pager',
            'links' =>'nav',
            'autoPlay' => 'auto'
        );

        if ( isset( $params[$param] ) ) {
            return $params[$param];
        }

        return false;
    }

    /**
     * Build the HTML for a slider.
     *
     * @return string slider markup.
     */
    protected function get_html() {
        $return_value = "<ul id='" . $this->get_identifier() . "' class='rslides'>";

        $first = true;
        foreach ( $this->slides as $slide ) {
            $style = "";

            if ( !$first ) {
                $style = " style='display: none;'";
            }
            $return_value .= "\n            <li{$style}>" . $slide . "</li>";
            $first = false;
        }

        $return_value .= "\n        </ul>";

        return apply_filters( 'metaslider_responsive_slider_get_html', $return_value, $this->id, $this->settings );;
    }
}