<?php

namespace Qiigo\Plugin\Integration\Pages {
	class Settings {
		protected static $_inst = null;
		public static function GetInstance() {
			if( !isset(static::$_inst) )
				static::$_inst = new Settings();
			
			return static::$_inst;
		}
		
		protected $plugin;
		public $slug;
		public $title;
		
		protected function __construct() {
			$this->plugin = \Qiigo\Plugin\Integration\Plugin::Get();
			$this->slug = $this->plugin->plugin_basename.'-settings';
			$this->title = 'Qiigo Integration';
		}
		
		public function RegisterPage() {
			add_options_page( $this->title, $this->title, 'manage_options', $this->slug, array($this, 'RenderPage'));
		}
		
		public function RenderPage() {
			$path = dirname(dirname(dirname(__FILE__))).DS.'admin'.DS.'pages'.DS.'settings.phtml';
			require($path);
		}
		
		public function RegisterSettings() {
			$sec_gen = $this->RegisterSection('general', 'General Settings');
			$this->RegisterSetting('gen_default_location_url', 'Location Not Found URL', $sec_gen, 'text');
			$this->RegisterSetting('gen_default_email', 'Default Email', $sec_gen, 'text');
			$this->RegisterSetting('gen_default_country', 'Default Country', $sec_gen, 'text');
			$this->RegisterSetting('gen_from_email', 'From Email', $sec_gen, 'text');
			$this->RegisterSetting('gen_error_email', 'Error Email', $sec_gen, 'text');
			
			$sec_fc = $this->RegisterSection('franconnect', 'FranConnect');
			$this->RegisterSetting('fc_site', 'Site', $sec_fc, 'text');
			$this->RegisterSetting('fc_key', 'Key', $sec_fc, 'text');
			
			$sec_ev = $this->RegisterSection('extraview', 'ExtraView');
			$this->RegisterSetting('ev_site', 'Site', $sec_ev, 'text');
			$this->RegisterSetting('ev_username', 'Username', $sec_ev, 'text');
			$this->RegisterSetting('ev_password', 'Password', $sec_ev, 'password');
			$this->RegisterSetting('ev_default_office', 'Default Office', $sec_ev, 'text');
		}
		
		protected function RegisterSection($name, $label) {
			$n = $this->slug.'_section_'.$name;
			add_settings_section(
				$n,
				$label,
				array($this, 'RenderSection'),
				$this->slug
			);
			
			return $n;
		}
		
		protected function RegisterSetting($name, $label, $section, $type) {
			register_setting( $this->slug, $name );
			add_settings_field(
				$name,
				$label,
				array($this, 'RenderField'),
				$this->slug,
				$section,
				array(
					'name' => $name,
					'value' => get_option($name),
					'type' => $type
				)
			);
		}
		
		public function RenderSection($args) {
		}
		
		public function RenderField($args) {
?>
	<input type="<?=$args['type']?>" id="<?=$args['name']?>" name="<?=$args['name']?>" value="<?=$args['value']?>" />
<?php
		}
	}
}