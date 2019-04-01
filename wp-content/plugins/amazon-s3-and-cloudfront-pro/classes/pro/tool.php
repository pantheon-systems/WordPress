<?php

namespace DeliciousBrains\WP_Offload_Media\Pro;

use AS3CF_Utils;
use AS3CF_Pro_Utils;

abstract class Tool {

	/**
	 * @var \Amazon_S3_And_CloudFront_Pro
	 */
	protected $as3cf;

	/**
	 * @var string
	 */
	protected $prefix = 'as3cf';

	/**
	 * @var string
	 */
	protected $tab = 'media';

	/**
	 * @var string
	 */
	protected $type = 'tool';

	/**
	 * @var string
	 */
	protected $view = 'sidebar-block';

	/**
	 * @var int
	 */
	protected $priority = 0;

	/**
	 * @var string
	 */
	protected $tool_key;

	/**
	 * @var string
	 */
	protected $tool_slug;

	/**
	 * @var string
	 */
	protected $errors_key_prefix;

	/**
	 * @var string
	 */
	protected $errors_key;

	/**
	 * @var array
	 */
	protected static $show_tool_constants = array();

	/**
	 * AS3CF_Tool constructor.
	 *
	 * @param \Amazon_S3_And_CloudFront_Pro $as3cf
	 */
	public function __construct( $as3cf ) {
		$this->as3cf             = $as3cf;
		$this->tool_slug         = str_replace( array( ' ', '_' ), '-', $this->tool_key );
		$this->errors_key_prefix = 'as3cf_tool_errors_';
		$this->errors_key        = $this->errors_key_prefix . $this->tool_key;
	}

	/**
	 * Initialize the tool.
	 */
	public function init() {
		// Load sidebar block
		add_action( 'as3cfpro_sidebar', array( $this, 'render_sidebar_block' ), $this->priority );

		// Ajax notices
		add_action( 'wp_ajax_as3cfpro_dismiss_errors_' . $this->tool_key, array( $this, 'ajax_dismiss_errors' ) );
	}

	/**
	 * Priority
	 *
	 * @param int $priority
	 *
	 * @return $this
	 */
	public function priority( $priority ) {
		$this->priority = $priority;

		return $this;
	}

	/**
	 * Get tools key.
	 *
	 * @return string
	 */
	public function get_tool_key() {
		return $this->tool_key;
	}

	/**
	 * Get tab.
	 *
	 * @return string
	 */
	public function get_tab() {
		return $this->tab;
	}

	/**
	 * Render sidebar block.
	 */
	public function render_sidebar_block() {
		$args = $this->get_sidebar_block_args();

		if ( false !== $args ) {
			$args['id']       = $this->tool_key;
			$args['tab']      = $this->tab;
			$args['priority'] = $this->priority;
			$args['slug']     = $this->tool_slug;
			$args['type']     = $this->type;
			$args['render']   = $this->should_render();

			$this->as3cf->render_view( 'pre-sidebar-block', $args );
			$this->as3cf->render_view( $this->view, $args );
			$this->as3cf->render_view( 'post-sidebar-block', $args );
		}
	}

	/**
	 * Get sidebar block.
	 *
	 * @return string
	 */
	public function get_sidebar_block() {
		ob_start();
		$this->render_sidebar_block();
		$block = ob_get_contents();
		ob_end_clean();

		return $block;
	}

	/**
	 * Should we render the sidebar block?
	 *
	 * @return bool
	 */
	public function should_render() {
		return true;
	}

	/**
	 * Get the sidebar block args.
	 *
	 * @return false|array
	 */
	protected function get_sidebar_block_args() {
		return false;
	}

	/**
	 * Are we currently processing?
	 *
	 * @return bool
	 */
	protected function is_processing() {
		return false;
	}

	/**
	 * Get status.
	 *
	 * @return array
	 */
	public function get_status() {
		return array(
			'should_render' => $this->should_render(),
			'is_processing' => $this->is_processing(),
		);
	}

	/**
	 * Get the errors created by the tool
	 *
	 * @param array $default
	 *
	 * @return array
	 */
	public function get_errors( $default = array() ) {
		return get_site_option( $this->errors_key, $default );
	}

	/**
	 * Update the saved errors for the tool
	 *
	 * @param array $errors
	 */
	public function update_errors( $errors ) {
		update_site_option( $this->errors_key, $errors );
	}

	/**
	 * Clear all errors created by the tool
	 */
	protected function clear_errors() {
		delete_site_option( $this->errors_key );
	}

	/**
	 * Update the error notice
	 *
	 * @param array $errors
	 */
	public function update_error_notice( $errors = array() ) {
		if ( empty( $errors ) ) {
			$errors = $this->get_errors();
		}

		if ( ! empty ( $errors ) ) {
			$args = array(
				'type'              => 'error',
				'class'             => 'tool-error',
				'flash'             => false,
				'only_show_to_user' => false,
				'only_show_on_tab'  => $this->tab,
				'custom_id'         => $this->errors_key,
				'user_capabilities' => array( 'as3cfpro', 'is_plugin_setup' ),
				'show_callback'     => array( 'as3cfpro', 'render_tool_errors_callback' ),
				'callback_args'     => array( $this->tool_key ),
			);

			$message = $this->get_error_notice_message();

			$this->as3cf->notices->add_notice( $message, $args );
		} else {
			$this->as3cf->notices->remove_notice_by_id( $this->errors_key );
		}
	}

	/**
	 * Undismiss error notice for all users.
	 */
	public function undismiss_error_notice() {
		$this->as3cf->notices->undismiss_notice_for_all( $this->errors_key );
	}

	/**
	 * Dismiss one or more errors.
	 */
	public function ajax_dismiss_errors() {
		check_ajax_referer( 'dismiss-errors-' . $this->tool_slug, 'nonce' );

		$blog_id  = filter_input( INPUT_POST, 'blog_id' );
		$media_id = filter_input( INPUT_POST, 'media_id' );
		$errors   = filter_input( INPUT_POST, 'errors' );
		$saved    = $this->get_errors();

		if ( empty( $saved[ $blog_id ][ $media_id ] ) ) {
			$this->as3cf->end_ajax( array(
				'success' => true,
			) );
		}

		if ( $errors == 'all' ) {
			unset( $saved[ $blog_id ][ $media_id ] );
		} elseif ( is_array( $saved[ $blog_id ][ $media_id ] ) ) {
			unset( $saved[ $blog_id ][ $media_id ][ $errors ] );
		}

		$updated = AS3CF_Pro_Utils::array_prune_recursive( $saved );
		$this->update_errors( $updated );
		$this->update_error_notice();

		$this->as3cf->end_ajax( array(
			'success' => true,
		) );
	}

	/**
	 * Get error notices.
	 *
	 * @return bool|array
	 */
	public function get_error_notices() {
		$notice = $this->as3cf->notices->find_notice_by_id( $this->errors_key );

		if ( ! $notice ) {
			return false;
		}

		$data = array();

		ob_start();
		$this->as3cf->render_view( 'notice', $notice );
		$data['error_notice'] = ob_get_contents();
		ob_end_clean();

		$custom_notices = $this->get_custom_notices_to_update();

		if ( ! empty( $custom_notices ) ) {
			$data['custom_notices'] = $custom_notices;
		}

		return $data;
	}

	/**
	 * Tool specific message for error notice.
	 *
	 * @return string
	 */
	protected function get_error_notice_message() {
		return '';
	}

	/**
	 * Get the constant used to define whether tool should always be shown (implemented as required by subclass).
	 *
	 * @return string|false Constant name if defined, otherwise false
	 */
	public static function show_tool_constant() {
		return AS3CF_Utils::get_first_defined_constant( static::$show_tool_constants );
	}

	/**
	 * Count media files in bucket.
	 *
	 * @return int
	 */
	protected function count_media_files() {
		static $count;

		if ( is_null( $count ) ) {
			$count = $this->as3cf->get_media_library_provider_total( true );
		}

		return $count;
	}
}