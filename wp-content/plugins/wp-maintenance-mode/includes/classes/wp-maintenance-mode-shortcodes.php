<?php

if (!class_exists('WP_Maintenance_Mode_Shortcodes')) {
    class WP_Maintenance_Mode_Shortcodes {

        /**
         * Add shortcodes
         * 
         * @since 2.0.3
         */
        public static function init() {
            $shortcodes = array(
                'loginform' => __CLASS__ . '::loginform'
            );

            foreach ($shortcodes as $shortcode => $method) {
                add_shortcode($shortcode, $method);
            }
        }

        /**
         * Shortcode Wrapper
         * 
         * @since 2.0.3
         * @param string $function
         * @param array $atts
         * @param array $wrapper
         * @return string
         */
        public static function shortcode_wrapper($function, $atts = array(), $wrapper = array('before' => null, 'after' => null)) {
            ob_start();

            echo $wrapper['before'];
            call_user_func($function, $atts);
            echo $wrapper['after'];

            return ob_get_clean();
        }

        /**
         * Login form shortcode.
         *
         * @since 2.0.3
         * @param array $atts
         * @return string
         */
        public static function loginform($atts) {
            return self::shortcode_wrapper(array('WP_Maintenance_Mode_Shortcode_Login', 'display'), $atts);
        }

    }

}

if (!class_exists('WP_Maintenance_Mode_Shortcode_Login')) {

    class WP_Maintenance_Mode_Shortcode_Login {

        public function __construct() { }

        /**
         * Show login form
         * 
         * @since 2.0.3
         * @param array $atts
         * @param string $content
         */
        public static function display($atts) {
            extract(shortcode_atts(array('redirect' => ''), $atts));

            include_once(WPMM_VIEWS_PATH . 'loginform.php');
        }

    }

}
