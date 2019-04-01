<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TM_Custom_XML_UI_Resources {
	private $wpml_wp_api;

	private $wpml_tm_path;
	private $wpml_tm_url;

	private $code_mirror_installed;
	private $vk_beautify_installed;

	function __construct( WPML_WP_API $wpml_wp_api ) {
		$this->wpml_wp_api  = $wpml_wp_api;
		$this->wpml_tm_path = $this->wpml_wp_api->constant( 'WPML_TM_PATH' );
		$this->wpml_tm_url  = $this->wpml_wp_api->constant( 'WPML_TM_URL' );
	}

	function admin_enqueue_scripts() {
		if ( $this->wpml_wp_api->is_tm_page( 'custom-xml-config', 'settings' ) ) {
			$this->add_vk_beautify();
			$this->add_code_mirror();

			$wpml_tm_custom_xml_config_dependencies = array( 'jquery' );
			if ( $this->is_code_mirror_installed() && $this->is_syntax_highlighting_enabled() ) {
				$wpml_tm_custom_xml_config_dependencies[] = 'wpml-tm-custom-xml-editor';
			}

			wp_register_script( 'wpml-tm-custom-xml-config', $this->wpml_tm_url . '/res/js/custom-xml-config/wpml-tm-custom-xml.js', $wpml_tm_custom_xml_config_dependencies, WPML_TM_VERSION );
			wp_register_style( 'wpml-tm-custom-xml-config', $this->wpml_tm_url . '/res/css/custom-xml.css', array(), WPML_TM_VERSION );

			wp_enqueue_style( 'wpml-tm-custom-xml-config' );
			wp_enqueue_script( 'wpml-tm-custom-xml-config' );
		}
	}

	private function add_code_mirror() {
		if ( $this->is_code_mirror_installed() && $this->is_syntax_highlighting_enabled() ) {
			$this->add_code_mirror_scripts();
			$this->add_code_mirror_styles();
		}
	}

	private function add_code_mirror_scripts() {
		$this->register_main_codemirror_script();

		$wpml_tm_custom_xml_editor_dependencies = array( $this->get_codemirror_resource_handle() );
		if ( $this->is_vk_beautify_installed() ) {
			$wpml_tm_custom_xml_editor_dependencies[] = 'vkbeautify';
		}

		wp_register_script( 'wpml-tm-custom-xml-editor', $this->wpml_tm_url . '/res/js/custom-xml-config/wpml-tm-custom-xml-editor.js', $wpml_tm_custom_xml_editor_dependencies, WPML_TM_VERSION );

		$modes = array(
			'xml' => 'xml/xml.js',
		);

		$addons = array(
			'foldcode'      => 'fold/foldcode.js',
			'foldgutter'    => 'fold/foldgutter.js',
			'brace-fold'    => 'fold/brace-fold.js',
			'xml-fold'      => 'fold/xml-fold.js',
			'matchtag'      => 'edit/matchtags.js',
			'matchbrackets' => 'edit/matchbrackets.js',
			'closebrackets' => 'edit/closebrackets.js',
			'closetag'      => 'edit/closetag.js',
			'show-hint'     => 'hint/show-hint.js',
			'xml-hint'      => 'hint/xml-hint.js',
			'display-panel' => 'display/panel.js',
		);

		foreach ( $modes as $mode => $path ) {
			wp_enqueue_script( 'codemirror-mode-' . $mode, $this->get_code_mirror_directory_url() . '/mode/' . $path, array( 'wpml-tm-custom-xml-config' ), WPML_TM_VERSION );
		}

		foreach ( $addons as $addon => $path ) {
			wp_enqueue_script( 'codemirror-addon-' . $addon, $this->get_code_mirror_directory_url() . '/addon/' . $path, array( 'wpml-tm-custom-xml-config' ), WPML_TM_VERSION );
		}
	}

	private function add_code_mirror_styles() {
		$this->register_main_codemirror_style();

		$addons = array(
			'foldgutter' => 'fold/foldgutter.css',
			'xml-hint'   => 'hint/show-hint.css',
		);
		foreach ( $addons as $addon => $path ) {
			wp_enqueue_style( 'codemirror-addon-' . $addon, $this->get_code_mirror_directory_url() . '/addon/' . $path, array( $this->get_codemirror_resource_handle() ), WPML_TM_VERSION );
		}
	}

	private function add_vk_beautify() {
		if ( $this->is_vk_beautify_installed() && $this->is_syntax_highlighting_enabled() ) {
			wp_enqueue_script( 'vkbeautify', $this->wpml_tm_url . '/libraries/vkBeautify/vkbeautify.js', array(), WPML_TM_VERSION );
		}
	}

	private function register_main_codemirror_style() {
		if ( ! $this->is_codemirror_in_wp_core() ) {
			wp_register_style( $this->get_codemirror_resource_handle(), $this->get_code_mirror_directory_url() . '/lib/codemirror.css', array( 'wpml-tm-custom-xml-config' ), WPML_TM_VERSION );
		}
		wp_enqueue_style( 'codemirror-theme', $this->get_code_mirror_directory_url() . '/theme/dracula.css', array( $this->get_codemirror_resource_handle() ), WPML_TM_VERSION );
	}

	private function register_main_codemirror_script() {
		if ( ! $this->is_codemirror_in_wp_core() ) {
			wp_register_script( $this->get_codemirror_resource_handle(), $this->get_code_mirror_directory_url() . '/lib/codemirror.js', array(), WPML_TM_VERSION );
		}
	}

	/**
	 * @return string
	 */
	private function get_code_mirror_directory_url() {
		return $this->wpml_tm_url . '/libraries/CodeMirror';
	}

	/**
	 * @return string
	 */
	private function get_codemirror_resource_handle() {
		if ( $this->is_codemirror_in_wp_core() ) {
			return 'wp-codemirror';
		}

		return 'codemirror';
	}

	/**
	 * @return bool
	 */
	private function is_code_mirror_installed() {
		if ( null === $this->code_mirror_installed ) {
			$this->code_mirror_installed = $this->is_codemirror_in_wp_core() || file_exists( $this->wpml_tm_path . '/libraries/CodeMirror/lib/codemirror.js' );
		}

		return $this->code_mirror_installed;
	}

	/**
	 * @return bool
	 */
	private function is_vk_beautify_installed() {
		if ( null === $this->vk_beautify_installed ) {
			$this->vk_beautify_installed = file_exists( $this->wpml_tm_path . '/libraries/vkBeautify/vkbeautify.js' );
		}

		return $this->vk_beautify_installed;
	}

	/**
	 * @return mixed
	 */
	private function is_codemirror_in_wp_core() {
		global $wp_version;

		return $this->wpml_wp_api->version_compare_naked( $wp_version, '4.9', '>=' );
	}

	/**
	 * @return bool
	 */
	private function is_syntax_highlighting_enabled() {
		return 'true' === get_user_meta( get_current_user_id(), 'syntax_highlighting', true );
	}
}
