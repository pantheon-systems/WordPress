<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     Redux Framework
 * @subpackage  Ad Remover
 * @subpackage  Wordpress
 * @author      Kevin Provance (kprovance)
 * @version     1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if( !class_exists( 'ReduxFramework_extension_ad_remover' ) ) {


    /**
     * Main ReduxFramework_extension_ad_remove extension class
     *
     * @since       1.0.0
     */
    class ReduxFramework_extension_ad_remove {

        public static $version = '1.0.0';
        
        // Protected vars
        protected $parent;
        public $extension_url;
        public $extension_dir;
        public static $theInstance;
        public static $ext_url;
        public $field_id = '';
        private $class_css = '';

        /**
        * Class Constructor. Defines the args for the extions class
        *
        * @since       1.0.0
        * @access      public
        * @param       array $parent Parent settings.
        * @return      void
        */
        public function __construct( $parent ) {
           
            $redux_ver = ReduxFramework::$_version;
            
            // Set parent object
            $this->parent = $parent;

            if (version_compare($redux_ver, '3.5.8.15') < 0) {
              /* Nectar Addition Start 
                $this->parent->admin_notices[] = array(
                    'type'    => 'error',
                    'msg'     => 'The Redux Ad Removal extension required Redux Framework version 3.5.8.15 or higher and will disabled until a Redux update is applied.<br/><br/>You are running Redux Framework version ' . $redux_ver,
                    'id'      => 'r_ad1492',
                    'dismiss' => false,
                );   
                Nectar Addition End */
                
                return;
            }
            
            // Set extension dir
            if ( empty( $this->extension_dir ) ) {
                $this->extension_dir = trailingslashit( str_replace( '\\', '/', dirname( __FILE__ ) ) );
                $this->extension_url = site_url( str_replace( trailingslashit( str_replace( '\\', '/', ABSPATH ) ), '', $this->extension_dir ) );
                self::$ext_url = $this->extension_url;
            }
            
            // Set field name
            $this->field_name = 'ad_remove';

            // Set instance
            self::$theInstance = $this;
            
            // Adds the local field
            add_filter( 'redux/'.$this->parent->args['opt_name'].'/field/class/'. $this->field_name, array( &$this, 'overload_field_path' ) );
            
            add_filter('redux/' . $this->parent->args['opt_name'] . '/aDBW_filter', array($this, 'dashboard'));
            add_filter('redux/' . $this->parent->args['opt_name'] . '/aNF_filter', array($this, 'newsflash'));
            add_filter('redux/' . $this->parent->args['opt_name'] . '/aNFM_filter', array($this, 'newsflash'));
            add_filter('redux/' . $this->parent->args['opt_name'] . '/aURL_filter', array($this, 'ads'));
        }
        
        public function ads() {
            return '';
        }
        
        public function dashboard() {
            return 'dat';
        }
        
        public function newsflash() {
            return 'bub';
        }
        
        static public function getInstance() {
            return self::$theInstance;
        }

        static public function getExtURL() {
            return self::$ext_url;
        }
        
        // Forces the use of the embeded field path vs what the core typically would use
        public function overload_field_path($field) {
            return dirname(__FILE__).'/'.$this->field_name.'/field_'.$this->field_name.'.php';
        }
    }
}