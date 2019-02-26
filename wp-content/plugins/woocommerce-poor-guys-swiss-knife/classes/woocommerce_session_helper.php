<?php

/**
* Helper functions to simplify the code when supporting different versions of WooCommerce
*
* NOTE: This will only function correctly after plugins_loaded since it needs to detect
* the WooCommerce session class to decide which storage approach to use
* @author leewillis77
* @url https://github.com/leewillis77/WooCommerce_Session_Helper
* @copyright GPLv2
*/

if ( ! class_exists ( 'WooCommerce_Session_Helper' ) ) :

    class WooCommerce_Session_Helper {



/**
* Store a value in the session
* @param string $key The key to store
* @param mixed $value The value to store
*/
        public static function _set ( $key, $value ) {

            global $woocommerce;

            switch (self::storage_method()) {

                case 'wc_session':
                    $woocommerce->session->$key = $value;
                    break;
                
                case 'raw_session':
                default:
                    $_SESSION[$key] = $value;
                    break;

            }

        }


/**
* Retrieve a value from the session
* @param string $key The key to retrieve
* @return mixed The value of the key, or false if no value is stored
*/
        public static function _get ( $key ) {

            global $woocommerce;

            switch (self::storage_method()) {

                case 'wc_session':
                    return isset ( $woocommerce->session->$key ) ? $woocommerce->session->$key : false;
                    break;
                
                case 'raw_session':
                default:
                    return isset ( $_SESSION[$key] ) ? $_SESSION[$key] : false;
                    break;

            }

        }


/**
* Unset a value in the session
* @param string $key The key to unset
*/
        public static function _unset ( $key ) {

            global $woocommerce;

            switch (self::storage_method()) {

                case 'wc_session':
                    unset ( $woocommerce->session->$key );
                    break;
                
                case 'raw_session':
                default:
                    unset ( $_SESSION[$key] );
                    break;

            }

        }


/**
* Check whether a key is set in the session storage
* @param string $key The key to check
* @return boolean Returns true if the key isset, false if not
*/
        public static function _isset ( $key ) {

            global $woocommerce;

            switch (self::storage_method()) {

                case 'wc_session':
                    return isset ( $woocommerce->session->$key );
                    break;
                
                case 'raw_session':
                default:
                    return isset ( $_SESSION[$key] );
                    break;

            }

        }



/**
* Detects which storage engine is in use by checking for a session in the
* global $woocommerce object.
*
* NOTE: This will only return the correct engine reliably AFTER plugins_loaded
*
* @return string A string identifying which storage engine is in use.
*/
        private function storage_method() {

            global $woocommerce;
            //return ( 'raw_session' );
			
            if ( class_exists ( 'WC_Session' ) ) {
                return ( 'wc_session' );
            } else {
                return ( 'raw_session' );
            }
			
        }
        
        
    }

endif;