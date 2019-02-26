<?php
!defined( 'ABSPATH' ) && exit; // Exit if accessed directly

if ( !class_exists( 'YITH_Privacy_Plugin_Abstract' ) ) {
    class YITH_Privacy_Plugin_Abstract {
        private $_name;

        public function __construct( $name ) {
            $this->_name = $name;
            $this->init();
        }

        protected function init() {
            add_filter( 'yith_plugin_fw_privacy_guide_content', array( $this, 'add_message_in_section' ), 10, 2 );
        }

        public function add_message_in_section( $html, $section ) {
            if ( $message = $this->get_privacy_message( $section ) ) {
                $html .= "<p class='privacy-policy-tutorial'><strong>{$this->_name}</strong></p>";
                $html .= $message;
            }
            return $html;
        }

        public function get_privacy_message( $section ) {
            return '';
        }
    }
}