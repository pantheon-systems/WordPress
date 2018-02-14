<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Override parent 'MetaSlider' class with CoinSlider specific markup,
 * javascript, css and settings.
 */
class MetaCoinSlider extends MetaSlider {

    protected $js_function = 'coinslider';
    protected $js_path = 'sliders/coinslider/coin-slider.min.js';
    protected $css_path = 'sliders/coinslider/coin-slider-styles.css';

    /**
     * Enable the parameters that are accepted by the slider
     *
     * @param  array $param Parameters
     * @return boolean
     */
    protected function get_param( $param ) {
        $params = array(
            'effect' => 'animation',
            'width' => 'width',
            'height' => 'height',
            'sph' => 'sph',
            'spw' => 'spw',
            'delay' => 'delay',
            'sDelay' => 'sDelay',
            'opacity' => 'opacity',
            'titleSpeed' => 'titleSpeed',
            'hoverPause' => 'hoverPause',
            'navigation' => 'showNavigationButtons',
            'links' => 'showNavigationPrevNext',
            'prevText' => 'prevText',
            'nextText' => 'nextText'
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
        $return_value = "<div id='" . $this->get_identifier() . "' class='coin-slider'>";

        foreach ( $this->slides as $slide ) {
            $return_value .= "\n" . $slide;
        }

        $return_value .= "\n        </div>";

        return apply_filters( 'metaslider_coin_slider_get_html', $return_value, $this->id, $this->settings ); $retVal;
    }
}