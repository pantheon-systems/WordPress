<?php

namespace Qiigo\Plugin\Integration\FrontEnd {
	class Plugin extends \Qiigo\Plugin\Integration\Plugin {
		protected function __construct() {
			parent::__construct();
			
			add_action('parse_request', array($this, 'ParseRequest'));
		}
		
		public function ParseRequest() {
			if( !isset($_POST['location_search']) )
				return;
				
			$loc = \Qiigo\Plugin\Integration\PostTypes\Location::Find($_POST['location_search']);
			$url = null;
			
			if( $loc != null )
				$url = get_field('url', $loc->ID, false);
			else
				$url = get_option('gen_default_location_url');
			
			wp_redirect($url);
			exit();
		}
	}
}
