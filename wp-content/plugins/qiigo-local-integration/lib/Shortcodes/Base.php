<?php

namespace Qiigo\Plugin\LocalIntegration\Shortcodes {
	abstract class Base {
		protected $shortcode_name;
		protected $content;
		
		public function __construct($name) {
			$this->shortcode_name = $name;

			if( function_exists('add_shortcode') ) {

                add_shortcode($this->shortcode_name, array($this, 'Run'));
            }
		}
		
		public function Run($atts, $content = null) {
			$this->content = $content;
			$this->ExtractArgs($atts);
			
			ob_start();
			$this->Render();
			$ret = ob_get_contents();
			ob_end_clean();
			
			return $ret;
		}
		public abstract function ExtractArgs($args);
		public abstract function Render();
	}
}