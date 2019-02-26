<?php
/**
 * Extension-Boilerplate
 * @link https://github.com/ReduxFramework/extension-boilerplate
 *
 * Radium Importer - Modified For ReduxFramework
 * @link https://github.com/FrankM1/radium-one-click-demo-install
 *
 * @package     WBC_Importer - Extension for Importing demo content
 * @author      Webcreations907
 * @version     1.0.1
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// Don't duplicate me!
if ( !class_exists( 'Radium_Theme_Demo_Data_Importer' ) ) {

	require_once dirname( __FILE__ ) .'/importer/radium-importer.php'; //load admin theme data importer

	class Radium_Theme_Demo_Data_Importer extends Radium_Theme_Importer {

		protected $ReduxParent;

		// Protected vars
		protected $parent;


		/**
		 * Holds a copy of the object for easy reference.
		 *
		 * @since 0.0.1
		 *
		 * @var object
		 */
		private static $instance;

		/**
		 * Theme Option name from ReduxFramework
		 *
		 * @var string
		 */
		public $theme_option_name       = '';


		/**
		 * themeoptions file name for ReduxFramework import
		 *
		 * @var string
		 */
		public $theme_options_file_name = '';

		/**
		 * Widgets.json file name
		 *
		 * @var string
		 */
		public $widgets_file_name   =  '';

		/**
		 * Content.xml file for importing sample content
		 *
		 * @var string
		 */
		public $content_demo_file_name  =  '';


		/**
		 * Holds a copy of the widget settings
		 *
		 * @since 0.0.2
		 *
		 * @var object
		 */
		public $widget_import_results;

		/**
		 * Constructor. Hooks all interactions to initialize the class.
		 *
		 * @since 0.0.1
		 */
		public function __construct( $parent, $redux_instance ) {
			$this->parent      = $parent;
			$this->ReduxParent = $redux_instance;

			$this->active_import = $this->parent->active_import;
			$this->active_import_id = $this->parent->active_import_id;
			
			$this->nectar_import_demo_content = $this->parent->nectar_import_demo_content;
			$this->nectar_import_theme_option_settings = $this->parent->nectar_import_theme_option_settings;
			$this->nectar_import_demo_widgets = $this->parent->nectar_import_demo_widgets;
			
			$this->initSettings();

			self::$instance = $this;


			parent::__construct();

		}

		public function initSettings() {

			if ( is_dir( $this->parent->demo_data_dir ) ) {
				$this->demo_files_path = $this->parent->demo_data_dir;
			}

			if ( isset( $this->active_import_id ) && !empty( $this->active_import_id ) ) {

				$demo_import_array             = $this->parent->wbc_import_files[$this->active_import_id];
				
				$this->content_demo_file_name  = ( isset( $demo_import_array['content_file'] ) ) ? $demo_import_array['content_file'] : '';
			  //chosen to skip in import
				if($this->nectar_import_demo_content == 'false') {
					$this->content_demo_file_name  = '';
				}
				
				
				$this->demo_files_path         = ( isset( $this->demo_files_path ) && !empty( $this->demo_files_path ) ) ? $this->demo_files_path.$demo_import_array['directory'].'/' : '';


				$this->theme_options_file_name = ( isset( $demo_import_array['theme_options'] ) ) ? $demo_import_array['theme_options'] : '';
				//chosen to skip in import
				if($this->nectar_import_theme_option_settings == 'false') {
					$this->theme_options_file_name  = '';
				}
				
				
				$this->widgets_file_name       = ( isset( $demo_import_array['widgets'] ) ) ? $demo_import_array['widgets'] : '';
				//chosen to skip in import
				if($this->nectar_import_demo_widgets == 'false') {
					$this->widgets_file_name = '';
				}
				
				$this->theme_option_name       = $this->ReduxParent->args['opt_name'];
			}

		}

		/**
		 * Add menus
		 *
		 * @since 0.0.1
		 */
		public function set_demo_menus() {
			do_action( 'wbc_importer_before_widget_import', $this->active_import, $this->demo_files_path );

		}

	}//class
}//function_exists
?>
