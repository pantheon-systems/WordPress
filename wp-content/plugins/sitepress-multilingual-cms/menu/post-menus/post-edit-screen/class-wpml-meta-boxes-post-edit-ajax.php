<?php

class WPML_Meta_Boxes_Post_Edit_Ajax implements IWPML_Action {

	const AJAX_ACTION = 'wpml_get_meta_boxes_html';

	private $meta_boxes_post_edit_html;

	public function __construct( WPML_Meta_Boxes_Post_Edit_HTML $meta_boxes_post_edit_html ) {
		$this->meta_boxes_post_edit_html = $meta_boxes_post_edit_html;
	}

	public function add_hooks() {
		add_action( 'wp_ajax_' . self::AJAX_ACTION, array( $this, 'render_meta_boxes_html' ) );
		add_filter( 'wpml_post_edit_can_translate', array( $this, 'force_post_edit_when_refreshing_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * @param string $hook
	 */
	public function enqueue_scripts( $hook ) {
		if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
			wp_enqueue_script( 'wpml-meta-box', ICL_PLUGIN_URL . '/dist/js/wpml-meta-box/wpml-meta-box.js' );
		}
	}

	public function render_meta_boxes_html() {
		if ( $this->is_valid_request() ) {
			$post_id = (int) $_POST['post_id'];
			$this->meta_boxes_post_edit_html->render_languages( get_post( $post_id ) );
			wp_die();
		}
	}


	/**
	 * @param bool $is_edit_page
	 *
	 * @return bool
	 */
	public function force_post_edit_when_refreshing_meta_boxes( $is_edit_page ) {
		return isset( $_POST['action'] ) && self::AJAX_ACTION === $_POST['action'] ? true : $is_edit_page;
	}

	/**
	 * @return bool
	 */
	private function is_valid_request() {
		return isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], self::AJAX_ACTION );
	}
}