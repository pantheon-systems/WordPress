<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Generic Slider super class. Extended by library specific classes.
 *
 * This class handles all slider related functionality, including saving settings and outputting
 * the slider HTML (front end and back end)
 */
class MetaSlider {

    public $id = 0; // slider ID
    public $identifier = 0; // unique identifier
    public $slides = array(); // slides belonging to this slider
    public $settings = array(); // slider settings

    /**
     * Constructor
     *
     * @param int   $id                 Slider ID
     * @param array $shortcode_settings Short code settings
     */
    public function __construct( $id, $shortcode_settings ) {
        $this->id = $id;
        $this->settings = array_merge( $shortcode_settings, $this->get_settings() );
        $this->identifier = 'metaslider_' . $this->id;
        $this->populate_slides();
    }

    /**
     * Return the unique identifier for the slider (used to avoid javascript conflicts)
     *
     * @return string unique identifier for slider
     */
    protected function get_identifier() {
        return $this->identifier;
    }

    /**
     * Get settings for the current slider
     *
     * @return array slider settings
     */
    private function get_settings() {
        $settings = get_post_meta( $this->id, 'ml-slider_settings', true );

        if ( is_array( $settings ) &&
            isset( $settings['type'] ) &&
            in_array( $settings['type'], array( 'flex', 'coin', 'nivo', 'responsive' ) ) ) {
            return $settings;
        } else {
            return $this->get_default_parameters();
        }
    }

    /**
     * Return an individual setting
     *
     * @param string $name Name of the setting
     * @return string setting value or 'false'
     */
    public function get_setting( $name ) {
        if ( !isset( $this->settings[$name] ) ) {
            $defaults = $this->get_default_parameters();

            if ( isset( $defaults[$name] ) ) {
                return $defaults[$name] ? $defaults[$name] : 'false';
            }
        } else {
            if ( strlen( $this->settings[$name] ) > 0 ) {
                return $this->settings[$name];
            }
        }

        return 'false';
    }

    /**
     * Get the slider libary parameters, this lists all possible parameters and their
     * default values. Slider subclasses override this and disable/rename parameters
     * appropriately.
     *
     * @return string javascript options
     */
    public function get_default_parameters() {
        $params = array(
            'type' => 'flex',
            'random' => false,
            'cssClass' => '',
            'printCss' => true,
            'printJs' => true,
            'width' => 700,
            'height' => 300,
            'spw' => 7,
            'sph' => 5,
            'delay' => 3000,
            'sDelay' => 30,
            'opacity' => 0.7,
            'titleSpeed' => 500,
            'effect' => 'random',
            'navigation' => true,
            'links' => true,
            'hoverPause' => true,
            'theme' => 'default',
            'direction' => 'horizontal',
            'reverse' => false,
            'animationSpeed' => 600,
            'prevText' => __('Previous', 'ml-slider'),
            'nextText' => __('Next', 'ml-slider'),
            'slices' => 15,
            'center' => false,
            'smartCrop' => true,
            'carouselMode' => false,
            'carouselMargin' => 5,
            'easing' => 'linear',
            'autoPlay' => true,
            'thumb_width' => 150,
            'thumb_height' => 100,
            'fullWidth' => false,
            'noConflict' => true
        );

        $params = apply_filters( 'metaslider_default_parameters', $params );

        return $params;
    }


    /**
     * The main query for extracting the slides for the slideshow
     *
     * @return WP_Query
     */
    public function get_slides() {
        $args = array(
            'force_no_custom_order' => true,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_type' => array('attachment', 'ml-slide'),
            'post_status' => array('inherit', 'publish'),
            'lang' => '', // polylang, ingore language filter
            'suppress_filters' => 1, // wpml, ignore language filter
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'ml-slider',
                    'field' => 'slug',
                    'terms' => $this->id
                )
            )
        );

        // if there is a var set to include the trashed slides, then include it
        if (metaslider_viewing_trashed_slides($this->id)) {
            $args['post_status'] = array('trash');
        }

        $args = apply_filters('metaslider_populate_slides_args', $args, $this->id, $this->settings);
        return new WP_Query($args);
    }

    /**
     * Return slides for the current slider
     *
     * @return array collection of slides belonging to the current slider
     */
    private function populate_slides() {
        $slides = array();

        $query = $this->get_slides();

        while ( $query->have_posts() ) {
            $query->next_post();

            $type = get_post_meta( $query->post->ID, 'ml-slider_type', true );
            $type = $type ? $type : 'image'; // backwards compatibility, fall back to 'image'

            // skip over deleted media files
            if ( $type == 'image' && get_post_type( $query->post->ID ) == 'ml-slide' && ! get_post_thumbnail_id( $query->post->ID ) ) {
                continue;
            }

            if ( has_filter( "metaslider_get_{$type}_slide" ) ) {
                $return = apply_filters( "metaslider_get_{$type}_slide", $query->post->ID, $this->id );

                if ( is_array( $return ) ) {
                    $slides = array_merge( $slides, $return );
                } else {
                    $slides[] = $return;
                }
            }
        }

        // apply random setting
        if ( $this->get_setting( 'random' ) == 'true' && !is_admin() ) {
            shuffle( $slides );
        }

        $this->slides = $slides;

        return $this->slides;
    }

    /**
     * Render each slide belonging to the slider out to the screen
     */
    public function render_admin_slides() {
        foreach ( $this->slides as $slide ) {
            echo $slide;
        }
    }

    /**
     * Output the HTML and Javascript for this slider
     *
     * @return string HTML & Javascrpt
     */
    public function render_public_slides() {
        $html[] = '<!-- MetaSlider -->';
        $html[] = '<div style="' . $this->get_container_style() . '" class="' . esc_attr($this->get_container_class()) .'">';
        $html[] = '    ' . $this->get_inline_css();
        $html[] = '    <div id="' . $this->get_container_id() . '">';
        $html[] = '        ' . $this->get_html();
        $html[] = '        ' . $this->get_html_after();
        $html[] = '    </div>';
        $html[] = '</div>';
        $html[] = '<!--// MetaSlider-->';

        $slideshow = implode( "\n", $html );

        $slideshow = apply_filters( 'metaslider_slideshow_output', $slideshow, $this->id, $this->settings );

        return $slideshow;
    }

    /**
     * Return the ID to use for the container
     */
    private function get_container_id() {
        $container_id = 'metaslider_container_' . $this->id;

        $id = apply_filters( 'metaslider_container_id', $container_id, $this->id, $this->settings );

        return $id;
    }

    /**
     * Return the classes to use for the slidehsow container
     */
    private function get_container_class() {

        // Add the version to the class name (if possible)
        $version_string = str_replace('.', '-', urlencode(METASLIDER_VERSION));
        $version_string .= defined('METASLIDERPRO_VERSION') ? ' ml-slider-pro-' . str_replace('.', '-', urlencode(METASLIDERPRO_VERSION)) : '';
        $class = "ml-slider-{$version_string} metaslider metaslider-{$this->get_setting('type')} metaslider-{$this->id} ml-slider";

        // apply the css class setting
        if ('false' != $this->get_setting('cssClass')) {
            $class .= " " . $this->get_setting('cssClass');
        }

        // handle any custom classes
        $class = apply_filters('metaslider_css_classes', $class, $this->id, $this->settings);
        return $class;
    }

    /**
     * Return the inline CSS style for the slideshow container.
     */
    private function get_container_style() {
        // default
        $style = "max-width: {$this->get_setting( 'width' )}px;";

        // carousels are always 100% wide
        if ( $this->get_setting( 'carouselMode' ) == 'true' || ( $this->get_setting( 'fullWidth' ) == 'true' ) && $this->get_setting( 'type' ) != 'coin' ) {
            $style = "width: 100%;";
        }

        // percentWidth showcode parameter takes precedence
        if ( $this->get_setting( 'percentwidth' ) != 'false' && $this->get_setting( 'percentwidth' ) > 0 ) {
            $style = "width: {$this->get_setting( 'percentwidth' )}%;";
        }

        // center align the slideshow
        if ( $this->get_setting( 'center' ) != 'false' ) {
            $style .= " margin: 0 auto;";
        }

        // handle any custom container styles
        $style = apply_filters( 'metaslider_container_style', $style, $this->id, $this->settings );

        return $style;
    }

    /**
     * Return the Javascript to kick off the slider. Code is wrapped in a timer
     * to allow for themes that load jQuery at the bottom of the page.
     *
     * Delay execution of slider code until jQuery is ready (supports themes where
     * jQuery is loaded at the bottom of the page)
     *
     * @return string javascript
     */
    private function get_inline_javascript() {
        $custom_js_before = $this->get_custom_javascript_before();
        $custom_js_after = $this->get_custom_javascript_after();

        $identifier = $this->get_identifier();

        $script = "var " . $identifier . " = function($) {";
        $script .= $custom_js_before;
        $script .= "\n            $('#" . $identifier . "')." . $this->js_function . "({ ";
        $script .= "\n                " . $this->get_javascript_parameters();
        $script .= "\n            });";
        $script .= $custom_js_after;
        $script .= "\n        };";

        $timer = "\n        var timer_" . $identifier . " = function() {";
        // this would be the sensible way to do it, but WordPress sometimes converts && to &#038;&
        // window.jQuery && jQuery.isReady ? {$identifier}(window.jQuery) : window.setTimeout(timer_{$identifier}, 1);";
        $timer .= "\n            var slider = !window.jQuery ? window.setTimeout(timer_{$this->identifier}, 100) : !jQuery.isReady ? window.setTimeout(timer_{$this->identifier}, 1) : {$this->identifier}(window.jQuery);";
        $timer .= "\n        };";
        $timer .= "\n        timer_" . $identifier . "();";

        $init = apply_filters("metaslider_timer", $timer, $this->identifier);

        return $script . $init;
    }

    /**
     * Custom HTML to add immediately below the markup
     */
    private function get_html_after() {
        $type = $this->get_setting( 'type' );

        $html = apply_filters( "metaslider_{$type}_slider_html_after", "", $this->id, $this->settings );

        if ( strlen( $html ) ) {
            return "        {$html}";
        }

        return "";
    }

    /**
     * Custom JavaScript to execute immediately before the slideshow is initialized
     */
    private function get_custom_javascript_before() {
        $type = $this->get_setting( 'type' );

        $javascript = "";

        if ( $this->get_setting( 'noConflict' ) == 'true' && $type == 'flex' ) {
            $javascript = "$('#metaslider_{$this->id}').addClass('flexslider'); // theme/plugin conflict avoidance";
        }

        $custom_js = apply_filters( "metaslider_{$type}_slider_javascript_before", $javascript, $this->id );

        if ( strlen( $custom_js ) ) {
            return "\n            {$custom_js}";
        }

        return "";
    }

    /**
     * Custom Javascript to execute immediately after the slideshow is initialized
     */
    private function get_custom_javascript_after() {
        $type = $this->get_setting( 'type' );

        $custom_js = apply_filters( "metaslider_{$type}_slider_javascript", "", $this->id );

        if ( strlen( $custom_js ) ) {
            return "            {$custom_js}";
        }

        return "";
    }

    /**
     * Build the javascript parameter arguments for the slider.
     *
     * @return string parameters
     */
    private function get_javascript_parameters() {
        $options = array();

        // construct an array of all parameters
        foreach ( $this->get_default_parameters() as $name => $default ) {
            if ( $param = $this->get_param( $name ) ) {
                $val = $this->get_setting( $name );

                if ( gettype( $default ) == 'integer' || $val == 'true' || $val == 'false' ) {
                    $options[$param] = $val;
                } else {
                    $options[$param] = '"' . esc_js($val) . '"';
                }
            }
        }

        // deal with any customised parameters
        $type = $this->get_setting( 'type' );
        $options = apply_filters( "metaslider_{$type}_slider_parameters", $options, $this->id, $this->settings );
        $arg = $type == 'flex' ? 'slider' : '';

        // create key:value strings
        foreach ( $options as $key => $value ) {
            if ( is_array( $value ) ) {
                $pairs[] = "{$key}: function($arg) {\n                "
                    . implode( "\n                ", $value )
                    . "\n                }";
            } else {
                $pairs[] = "{$key}:{$value}";
            }
        }

        return implode( ",\n                ", $pairs );
    }

    /**
     * Apply any custom inline styling
     *
     * @return string
     */
    private function get_inline_css() {
        $css = apply_filters( "metaslider_css", "", $this->settings, $this->id );

        // use this to add the scoped attribute for HTML5 validation (if needed)
        $attributes = apply_filters( "metaslider_style_attributes", "", $this->settings, $this->id );

        if ( strlen( $css ) ) {
            return "<style type=\"text/css\"{$attributes} id=\"metaslider-css-{$this->id}\">{$css}\n    </style>";
        }

        return "";
    }

    /**
     * Polyfill to handle the wp_add_inline_script() function.
     *
     * @param  string $handle   [description]
     * @param  array  $data     [description]
     * @param  string $position [description]
     * @return array
     */
    public function wp_add_inline_script($handle, $data, $position = 'after') {
        if (function_exists('wp_add_inline_script')) return wp_add_inline_script($handle, $data, $position);
        global $wp_scripts;
        if (!$data) return false;

        // First fetch any existing scripts
        $script = $wp_scripts->get_data($handle, 'data');

        // Append to the end
        $script .= $data;

        return $wp_scripts->add_data($handle, 'data', $script);
    }

    /**
     * Include slider assets, JS and CSS paths are specified by child classes.
     */
    public function enqueue_scripts() {
        if ('true' == $this->get_setting('printJs')) {
            $handle = 'metaslider-' . $this->get_setting('type') . '-slider';
            wp_enqueue_script($handle, METASLIDER_ASSETS_URL . $this->js_path, array('jquery'), METASLIDER_VERSION);
            $this->wp_add_inline_script($handle, $this->get_inline_javascript());
        }

        if ( $this->get_setting( 'printCss' ) == 'true' ) {
            // this will be added to the bottom of the page as <head> has already been processed by WordPress.
            // For HTML5 compatibility, use a minification plugin to move the CSS to the <head>
            wp_enqueue_style( 'metaslider-' . $this->get_setting( 'type' ) . '-slider', METASLIDER_ASSETS_URL . $this->css_path, false, METASLIDER_VERSION );
            wp_enqueue_style( 'metaslider-public', METASLIDER_ASSETS_URL . 'metaslider/public.css', false, METASLIDER_VERSION );
        }

        do_action( 'metaslider_register_public_styles' );
    }


}