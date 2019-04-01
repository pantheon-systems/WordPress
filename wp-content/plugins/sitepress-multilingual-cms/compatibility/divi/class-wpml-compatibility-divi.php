<?php

class WPML_Compatibility_Divi {

	const REGEX_REMOVE_OPENING_PARAGRAPH = '/(<p>[\n\r]*)([\n\r]{1}\[\/et_)/m';
	const REGEX_REMOVE_CLOSING_PARAGRAPH = '/(\[et_.*\][\n\r]{1})([\n\r]*<\/p>)/m';

	/** @var SitePress */
	private $sitepress;

	/**
	 * @param SitePress $sitepress
	 */
	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_action( 'init', array( $this, 'load_resources_if_they_are_required' ), 10, 0 );

		if ( $this->sitepress->is_setup_complete() ) {
			add_action( 'admin_init', array( $this, 'display_warning_notice' ), 10, 0 );
			add_filter( 'wpml_pb_should_handle_content', array( $this, 'should_handle_shortcode_content' ), 10, 2 );
		}

		add_filter( 'wpml_pb_shortcode_content_for_translation', array( $this, 'cleanup_global_layout_content' ), 10, 2 );
	}

	/**
	 * @return bool
	 */
	private function is_standard_editor_used() {
		$tm_settings = $this->sitepress->get_setting( 'translation-management', array() );

		return ! isset( $tm_settings['doc_translation_method'] ) ||
		       ICL_TM_TMETHOD_MANUAL === $tm_settings['doc_translation_method'];
	}

	public function display_warning_notice() {
		$notices = wpml_get_admin_notices();

		if ( $this->is_standard_editor_used() ) {
			$notices->add_notice( new WPML_Compatibility_Divi_Notice() );
		} elseif ( $notices->get_notice( WPML_Compatibility_Divi_Notice::ID, WPML_Compatibility_Divi_Notice::GROUP ) ) {
			$notices->remove_notice( WPML_Compatibility_Divi_Notice::GROUP, WPML_Compatibility_Divi_Notice::ID );
		}
	}

	public function load_resources_if_they_are_required() {
		if ( ! isset( $_GET['page'] ) || ! is_admin() ) {
			return;
		}

		$pages = array( self::get_duplication_action_page() );
		if ( $this->is_tm_active() ) {
			$pages[] = self::get_translation_editor_page();
		}

		if ( in_array( $_GET['page'], $pages, true ) ) {
			$this->register_layouts();
		}
	}

	private static function get_translation_editor_page() {
		return WPML_TM_FOLDER . '/menu/translations-queue.php';
	}

	private static function get_duplication_action_page() {
		return WPML_PLUGIN_FOLDER . '/menu/languages.php';
	}

	private function is_tm_active() {
		return defined( 'WPML_TM_FOLDER' );
	}

	private function register_layouts() {
		if ( function_exists( 'et_builder_should_load_framework' ) && ! et_builder_should_load_framework() ) {
			if ( function_exists( 'et_builder_register_layouts' ) ) {
				et_builder_register_layouts();
			} else {
				$lib_file = ET_BUILDER_DIR . 'feature/Library.php';

				if ( ! class_exists( 'ET_Builder_Library' )
				     && defined( 'ET_BUILDER_DIR' )
				     && file_exists( $lib_file )
				) {
					require_once $lib_file;
				}


				if ( class_exists( 'ET_Builder_Library' ) ) {
					ET_Builder_Library::instance();
				}
			}
		}
	}

	/**
	 * The global layout is not properly extracted from the page
	 * because it adds <p> tags either not opened or not closed.
	 *
	 * See the global content below as an example:
	 *
	 * [et_pb_section prev_background_color="#000000" next_background_color="#000000"][et_pb_text]
	 *
	 * </p>
	 * <p>Global text 1 EN5</p>
	 * <p>
     *
	 * [/et_pb_text][/et_pb_section]
	 *
	 * We also need to remove `prev_background` and `next_background` attributes which are added from the page.
	 *
	 * @param string $content
	 * @param int    $post_id
	 */
	public function cleanup_global_layout_content( $content, $post_id ) {
		if ( 'et_pb_layout' === get_post_type( $post_id ) ) {
			$content = preg_replace( self::REGEX_REMOVE_OPENING_PARAGRAPH, "$2", $content );
			$content = preg_replace( self::REGEX_REMOVE_CLOSING_PARAGRAPH, "$1", $content );
			$content = preg_replace( '/( prev_background_color="#[0-9a-f]*")/', '', $content );
			$content = preg_replace( '/( next_background_color="#[0-9a-f]*")/', '', $content );
		}

		return $content;
	}

	public function should_handle_shortcode_content( $handle_content, $shortcode ) {
		if (
			strpos( $shortcode['tag'], 'et_' ) === 0 &&
			strpos( $shortcode['attributes'], 'global_module=' ) !== false
		) {
			$handle_content = false;
		}
		return $handle_content;
	}
}
