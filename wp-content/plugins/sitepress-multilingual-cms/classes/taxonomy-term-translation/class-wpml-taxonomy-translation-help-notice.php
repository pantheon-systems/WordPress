<?php

/**
 * Class WPML_Taxonomy_Translation_Help_Notice
 */
class WPML_Taxonomy_Translation_Help_Notice {

	const NOTICE_GROUP = 'taxonomy-term-help-notices';

	/**
	 * @var WPML_Notices
	 */
	private $wpml_admin_notices;

	/**
	 * @var WPML_Notice
	 */
	private $notice = false;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WPML_Taxonomy_Translation_Help_Notice constructor.
	 *
	 * @param WPML_Notices $wpml_admin_notices
	 * @param SitePress $sitepress
	 */
	public function __construct( WPML_Notices $wpml_admin_notices, SitePress $sitepress ) {
		$this->wpml_admin_notices = $wpml_admin_notices;
		$this->sitepress = $sitepress;
	}

	public function __sleep() {
		return array( 'wpml_admin_notices', 'notice' );
	}

	public function __wakeup() {
		global $sitepress;
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'add_help_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * @return bool
	 */
	public function should_display_help_notice() {
		$display_notice = false;
		$taxonomy       = $this->get_current_translatable_taxonomy();
		$notice         = $this->get_notice();
		if ( $notice && $taxonomy && $taxonomy->name === $notice->get_id() && $this->taxonomy_term_screen() ) {
			$display_notice = true;
		}

		return $display_notice;
	}

	/**
	 * Create and add notice.
	 */
	public function add_help_notice() {
		$notice = $this->create_and_set_term_translation_help_notice();
		if ( false !== $notice ) {
			$this->add_term_help_notice_to_admin_notices();
		}
	}

	/**
	 * @return bool|WP_Taxonomy
	 */
	private function get_current_translatable_taxonomy() {
		$taxonomy = false;
		if ( array_key_exists( 'taxonomy', $_GET )
		     && ! empty( $_GET['taxonomy'] )
		     && $this->is_translatable_taxonomy( $_GET['taxonomy'] )
		) {
			$taxonomy = get_taxonomy( $_GET['taxonomy'] );
		}

		return $taxonomy;
	}

	/**
	 * @return WPML_Notice
	 */
	private function create_and_set_term_translation_help_notice() {
		$taxonomy = $this->get_current_translatable_taxonomy();
		if ( false !== $taxonomy ) {
			$link_to_taxonomy_translation_screen = $this->build_tag_to_taxonomy_translation( $taxonomy );
			$text   = sprintf( esc_html__( 'Translating %s? Use the %s table for easier translation.', 'sitepress' ), $taxonomy->labels->name, $link_to_taxonomy_translation_screen );
			$this->set_notice( new WPML_Notice( $taxonomy->name, $text, self::NOTICE_GROUP ) );
		}

		return $this->get_notice();
	}

	private function add_term_help_notice_to_admin_notices() {
		$notice = $this->get_notice();
		$notice->set_css_class_types( 'info' );
		$notice->add_display_callback( array( $this, 'should_display_help_notice' ) );
		$action = $this->wpml_admin_notices->get_new_notice_action( esc_html__( 'Dismiss', 'sitepress' ), '#', false, false, false );
		$action->set_js_callback( 'wpml_dismiss_taxonomy_translation_notice' );
		$action->set_group_to_dismiss( $notice->get_group() );
		$notice->add_action( $action );
		$this->wpml_admin_notices->add_notice( $notice );
	}

	/**
	 * @param $taxonomy
	 *
	 * @return string
	 */
	private function build_tag_to_taxonomy_translation( $taxonomy ) {

		$url = add_query_arg(
			array(
				'page' => WPML_PLUGIN_FOLDER . '/menu/taxonomy-translation.php',
				'taxonomy' => $taxonomy->name,
				),
			admin_url('admin.php')
		);
		$url = apply_filters( 'wpml_taxonomy_term_translation_url', $url, $taxonomy->name );

		return '<a href="' . esc_url( $url ) . '">' .
		       sprintf( esc_html__( ' %s translation', 'sitepress' ), $taxonomy->labels->singular_name ) . '</a>';
	}

	private function taxonomy_term_screen() {
		$screen = get_current_screen();
		$is_taxonomy_term_screen = false;
		if ( 'edit-tags' === $screen->base || 'term' === $screen->base ) {
			$is_taxonomy_term_screen = true;
		}

		return $is_taxonomy_term_screen;
	}

	/**
	 * @param WPML_Notice $notice
	 */
	public function set_notice( WPML_Notice $notice ) {
		$this->notice = $notice;
	}

	/**
	 * @return WPML_Notice
	 */
	public function get_notice() {
		return $this->notice;
	}

	/**
	 * Enqueue JS callback script.
	 */
	public function enqueue_scripts() {
		$notice = $this->get_notice();
		if ( $notice ) {
			wp_register_script( 'wpml-dismiss-taxonomy-help-notice', ICL_PLUGIN_URL . '/res/js/dismiss-taxonomy-help-notice.js', array( 'jquery' ) );
			wp_localize_script( 'wpml-dismiss-taxonomy-help-notice', 'wpml_notice_information', array( 'notice_id' => $notice->get_id(), 'notice_group' => $notice->get_group() ) );
			wp_enqueue_script( 'wpml-dismiss-taxonomy-help-notice' );
		}
	}

	/**
	 * @param string $taxonomy
	 *
	 * @return bool
	 */
	private function is_translatable_taxonomy( $taxonomy ) {
		$is_translatable = false;

		$taxonomy_object = get_taxonomy( $taxonomy );
		if( $taxonomy_object ){
			$post_type = isset( $taxonomy_object->object_type[0] ) ? $taxonomy_object->object_type[0] : 'post';
			$translatable_taxonomies = $this->sitepress->get_translatable_taxonomies( true, $post_type );
			$is_translatable = in_array( $taxonomy, $translatable_taxonomies );
		}

		return $is_translatable;
	}
}
