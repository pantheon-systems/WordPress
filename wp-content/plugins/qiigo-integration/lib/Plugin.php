<?php

namespace Qiigo\Plugin\Integration {
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
		
		public $plugin_basename = 'qiigo-integration';
		public $shortcodes = array();
		
		protected function __construct() {
			add_action('init', array($this, 'Init'));
			
			$this->shortcodes[] = new Shortcodes\CountryDDL();
		}
		
		public function Init() {
			if( !wp_next_scheduled( 'qiigo_integration_daily' ) ) {
				// Must be daily cause WP doesn't support anything longer. Weekly would be nice...
				wp_schedule_event(time(), 'daily', 'qiigo_integration_daily');
			}
			
			add_action('qiigo_integration_daily', array($this, 'DailyEvent'));
			
			$this->RegisterPostTypes();
		}
		
		public function DailyEvent() {
			$dir_path = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR;
			$path = $dir_path.'123contact.log';
			$created = filectime($path);

			if( (time() - (7*24*60*60)) > $created )
				return;
			
			$archive_name = '123contact-'.date('Y-m-d', $created).'.log';
			rename($path, $dir_path.$archive_name);
			touch($path);
			
			$url = plugins_url().'/qiigo-integration/'.$archive_name;
			
			$from = get_option('gen_from_email');
			$to = get_option('gen_error_email');
			$subject = 'Weekly Archive for JanPro';
			
			$body = '<br /><br /><a href="'.$url.'">Weekly Archive of Leads</a>';
			
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: ' . $from . "\r\n";
			
			\wp_mail($to, $subject, $body, $headers);
		}
		
		protected function RegisterPostTypes() {
			PostTypes\Location::Register();
			PostTypes\Country::Register();
		}
	}
}