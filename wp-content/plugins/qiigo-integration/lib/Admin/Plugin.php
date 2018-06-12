<?php

namespace Qiigo\Plugin\Integration\Admin {
	class Plugin extends \Qiigo\Plugin\Integration\Plugin {
		
		protected $settings;
		
		protected function __construct() {
			parent::__construct();
			
			add_action('admin_init', array($this, 'AdminInit'));
			add_action('admin_menu', array($this, 'AdminMenu'));
			add_filter('plugin_action_links_'.$this->plugin_basename.'/'.$this->plugin_basename.'.php', array($this, 'PluginActionLinks'));
		}
		
		public function AdminMenu() {
			if( !isset($this->settings) )
				$this->settings = \Qiigo\Plugin\Integration\Pages\Settings::GetInstance();
			$this->settings->RegisterPage();
		}
		
		public function AdminInit() {
			if( !isset($this->settings) )
				$this->settings = \Qiigo\Plugin\Integration\Pages\Settings::GetInstance();
			$this->settings->RegisterSettings();
		}
		
		public function PluginActionLinks($links) {
			$mylinks = array();
			if(isset($this->page)){
				$mylinks = array(
					'<a href="' . admin_url( 'options-general.php?page='.$this->page->slug ) . '">Settings</a>',
				);
			}
			return array_merge( $links, $mylinks );
		}
	}
}