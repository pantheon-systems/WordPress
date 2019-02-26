<?php

class WPML_Theme_Localization_Type extends WPML_Ajax_Factory implements IWPML_AJAX_Action {
	const USE_ST                 = 1;
	const USE_MO_FILES           = 2;
	const USE_ST_AND_NO_MO_FILES = 3;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var WPML_MO_File_Search
	 */
	private $mo_file_search;

	/**
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		if ( is_admin() ) {
			$this->init_ajax_actions();
		}
	}

	public function run() {
		$type       = $this->retrieve_theme_localization_type();
		$textdomain = $this->retrieve_theme_localization_load_textdomain();

		$this->save_localization_type( $type, $textdomain );

		return new WPML_Ajax_Response( true, $type );
	}

	public function save_localization_type( $type, $textdomain = 0 ) {
		$iclsettings = $this->sitepress->get_settings();

		$iclsettings['theme_localization_type']            = $type;
		$iclsettings['theme_localization_load_textdomain'] = $textdomain;
		$iclsettings['gettext_theme_domain_name']          = array_key_exists( 'textdomain_value', $_POST ) ? filter_var( $_POST['textdomain_value'], FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE ) : false;

		if ( $this->get_use_mo_files_value() === $iclsettings['theme_localization_type'] ) {
			$iclsettings['theme_language_folders'] = $this->get_mo_file_search()->find_theme_mo_dirs();
		}

		$this->sitepress->save_settings( $iclsettings );

		do_action( 'wpml_post_save_theme_localization_type', $iclsettings );
	}

	public function get_class_names() {
		return array( __CLASS__ );
	}

	public function create( $class_name ) {
		return $this;
	}

	public function init_ajax_actions() {
		new WPML_Ajax_Route( $this );
	}

	/**
	 * @return int
	 */
	private function retrieve_theme_localization_type() {
		$result = $this->get_use_mo_files_value();
		if ( array_key_exists( 'icl_theme_localization_type', $_POST ) ) {
			$var     = filter_var( $_POST['icl_theme_localization_type'], FILTER_VALIDATE_INT );
			$options = array( $this->get_use_st_value(), $this->get_use_mo_files_value(), $this->get_use_st_and_no_mo_files_value() );
			if ( in_array( $var, $options, true ) ) {
				$result = $var;
			}
		}

		return $result;
	}

	/**
	 * @return int
	 */
	private function retrieve_theme_localization_load_textdomain() {
		$result = 0;
		if ( array_key_exists( 'icl_theme_localization_load_td', $_POST ) ) {
			$var = filter_var( $_POST['icl_theme_localization_load_td'], FILTER_VALIDATE_INT );
			if ( false !== $var ) {
				$result = $var;
			}
		}

		return $result;
	}

	/**
	 * @return int
	 */
	public function get_theme_localization_type() {
		$settings = $this->sitepress->get_settings();
		if ( isset( $settings['theme_localization_type'] ) ) {
			return (int) $settings['theme_localization_type'];
		}
		return $this->get_use_mo_files_value();
	}

	/**
	 * @return bool
	 */
	public function is_st_type() {
		return in_array( $this->get_theme_localization_type(), array( $this->get_use_st_value(), $this->get_use_st_and_no_mo_files_value() ), true );
	}

	/**
	 * @return WPML_MO_File_Search
	 */
	public function get_mo_file_search() {
		if ( ! $this->mo_file_search ) {
			$this->mo_file_search = new WPML_MO_File_Search( $this->sitepress );
		}

		return $this->mo_file_search;
	}

	/**
	 * @param WPML_MO_File_Search $mo_file_search
	 *
	 * @return WPML_Theme_Localization_Type
	 */
	public function set_mo_file_search( WPML_MO_File_Search $mo_file_search ) {
		$this->mo_file_search = $mo_file_search;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_use_st_and_no_mo_files_value() {
		return self::USE_ST_AND_NO_MO_FILES;
	}

	/**
	 * @return int
	 */
	public function get_use_st_value() {
		return self::USE_ST;
	}

	/**
	 * @return int
	 */
	public function get_use_mo_files_value() {
		return self::USE_MO_FILES;
	}

	/** @return bool */
	public function is_mo_loading_disabled() {
		return self::USE_ST_AND_NO_MO_FILES === $this->get_theme_localization_type();
	}
}
