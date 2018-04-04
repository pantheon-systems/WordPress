<?php

namespace Qiigo\Plugin\LocalIntegration\Admin {
	class Plugin extends \Qiigo\Plugin\LocalIntegration\Plugin {
		
		protected $settings;
		
		protected function __construct() {
			parent::__construct();
			
			add_action('admin_init', array($this, 'AdminInit'));
			add_action('admin_menu', array($this, 'AdminMenu'));
		}
		
		public function AdminMenu() {
		}
		
		public function AdminInit() {
		}
	}
}