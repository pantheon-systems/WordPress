<?php

class WPML_Display_As_Translated_Default_Lang_Messages {

	const PREVIOUS_LANG_KEY = 'wpml-previous-default-language';

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var WPML_Display_As_Translated_Default_Lang_Messages_View
	 */
	private $view;

	public function __construct( SitePress $sitepress, WPML_Display_As_Translated_Default_Lang_Messages_View $view ) {
		$this->sitepress = $sitepress;
		$this->view      = $view;
	}

	public function add_hooks() {
		if ( $this->should_display_message() ) {
			add_action( 'wpml_after_active_languages_display', array( $this, 'display_messages' ) );
			add_action( 'icl_after_set_default_language', array( $this, 'save_previous_lang' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			'wpml-default-lang-change-message',
			ICL_PLUGIN_URL . '/res/js/display-as-translated/toggle-default-lang-change-message.js',
			array( 'jquery' )
		);
	}

	/**
	 * @param string $prev_lang
	 */
	public function save_previous_lang( $prev_lang ) {
		update_option( self::PREVIOUS_LANG_KEY, $prev_lang );
	}

	public function display_messages() {
		$previous_lang = get_option( self::PREVIOUS_LANG_KEY );
		$this->view->display(
			$this->sitepress->get_display_language_name( $previous_lang ? $previous_lang : $this->sitepress->get_default_language() ),
			$this->sitepress->get_display_language_name( $this->sitepress->get_default_language() )
		);
		update_option( self::PREVIOUS_LANG_KEY, $this->sitepress->get_default_language() );
	}

	/**
	 * @return bool
	 */
	private function should_display_message() {
		$post_types = get_post_types();
		$taxonomies = get_taxonomies();

		foreach ( $post_types as $post_type ) {
			if ( $this->sitepress->is_display_as_translated_post_type( $post_type ) && get_posts( array( 'post_type' => $post_type, 'posts_per_page' => 1 ) ) ) {
				return true;
			}
		}

		foreach ( $taxonomies as $taxonomy ) {
			if ( $this->sitepress->is_display_as_translated_taxonomy( $taxonomy ) && get_terms( array( 'taxonomy' => $taxonomy ) ) ) {
				return true;
			}
		}

		return false;
	}
}