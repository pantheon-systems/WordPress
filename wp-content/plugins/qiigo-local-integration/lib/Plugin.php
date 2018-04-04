<?php

namespace Qiigo\Plugin\LocalIntegration {
	abstract class Plugin {
		protected static $_inst = null;
		public static function Get() {
			return static::$_inst;
		}
		
		public static function Setup() {
			if( !isset(static::$_inst) ) {
				if( is_admin() )
					static::$_inst = new Admin\Plugin();
				else
					static::$_inst = new FrontEnd\Plugin();
			}
			
			return static::$_inst;
		}
		
		public $plugin_basename = 'qiigo-local-integration';
		public $shortcodes = array();
		
		protected function __construct() {

			add_action('init', array($this, 'Init'));
			
			$this->shortcodes[] = new Shortcodes\ContactForm();
		}
		
		public function Init() {
			$this->RegisterPostTypes();
		}
		
		protected function RegisterPostTypes() {
		}
	}
}