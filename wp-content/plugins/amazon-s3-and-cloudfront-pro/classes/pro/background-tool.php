<?php

namespace DeliciousBrains\WP_Offload_Media\Pro;

use DeliciousBrains\WP_Offload_Media\Pro\Background_Processes\Background_Tool_Process;

abstract class Background_Tool extends Tool {

	/**
	 * @var string
	 */
	protected $type = 'background-tool';

	/**
	 * @var string
	 */
	protected $view = 'background-tool';

	/**
	 * @var Background_Tool_Process
	 */
	protected $background_process;

	/**
	 * Initialize the tool.
	 */
	public function init() {
		parent::init();

		// JS data
		add_filter( 'as3cfpro_js_nonces', array( $this, 'add_js_nonces' ) );
		add_action( "wp_ajax_as3cfpro_{$this->tool_key}_start", array( $this, 'ajax_handle_start' ) );
		add_action( "wp_ajax_as3cfpro_{$this->tool_key}_cancel", array( $this, 'ajax_handle_cancel' ) );
		add_action( "wp_ajax_as3cfpro_{$this->tool_key}_pause_resume", array( $this, 'ajax_handle_pause_resume' ) );

		$this->background_process = $this->get_background_process_class();
	}

	/**
	 * Get the sidebar notice details
	 *
	 * @return false|array
	 */
	protected function get_sidebar_block_args() {
		return array(
			'title'              => $this->get_title_text(),
			'more_info'          => $this->get_more_info_text(),
			'status_description' => $this->get_status_description(),
			'busy_description'   => $this->get_busy_description(),
			'button'             => $this->get_button_text(),
			'is_queued'          => $this->is_queued(),
			'is_paused'          => $this->is_paused(),
			'is_cancelled'       => $this->is_cancelled(),
			'progress_percent'   => $this->get_progress(),
		);
	}

	/**
	 * Get status.
	 *
	 * @return array
	 */
	public function get_status() {
		return array(
			'should_render' => $this->should_render(),
			'progress'      => $this->get_progress(),
			'is_queued'     => $this->is_queued(),
			'is_processing' => $this->is_processing(),
			'is_paused'     => $this->is_paused(),
			'is_cancelled'  => $this->is_cancelled(),
			'description'   => $this->get_status_description(),
		);
	}

	/**
	 * Get more info text.
	 *
	 * @return string
	 */
	public function get_more_info_text() {
		return '';
	}

	/**
	 * Get status description.
	 *
	 * @return string
	 */
	public function get_status_description() {
		if ( $this->is_processing() && ( $this->is_cancelled() || $this->is_paused() ) ) {
			return __( 'Completing current batch.', 'amazon-s3-and-cloudfront' );
		}

		if ( $this->is_paused() ) {
			return __( 'Paused', 'amazon-s3-and-cloudfront' );
		}

		if ( $this->is_queued() ) {
			return $this->get_queued_status();
		}

		return '';
	}

	/**
	 * Get busy description.
	 *
	 * @return string
	 */
	public function get_busy_description() {
		return __( 'Initiating&hellip;', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Add the nonces to the JavaScript.
	 *
	 * @param array $js_nonces
	 *
	 * @return array
	 */
	public function add_js_nonces( $js_nonces ) {
		$js_nonces['tools'][ $this->tool_key ]            = $this->create_tool_nonces();
		$js_nonces[ 'dismiss_errors_' . $this->tool_key ] = wp_create_nonce( 'dismiss-errors-' . $this->tool_slug );

		return $js_nonces;
	}

	/**
	 * Create tool nonces.
	 *
	 * @param array $actions
	 *
	 * @return array
	 */
	protected function create_tool_nonces( $actions = array( 'start', 'pause_resume', 'cancel' ) ) {
		$nonces = array();

		foreach ( $actions as $action ) {
			$nonces[ $action ] = wp_create_nonce( $this->tool_key . '_' . $action );
		}

		return $nonces;
	}

	/**
	 * AJAX handle start.
	 */
	public function ajax_handle_start() {
		check_ajax_referer( $this->tool_key . '_start', 'nonce' );

		$this->handle_start();
	}

	/**
	 * Handle start.
	 */
	public function handle_start() {
		if ( $this->is_queued() ) {
			return;
		}

		$this->clear_errors();

		$session = $this->create_session();

		$this->background_process->push_to_queue( $session )->save()->dispatch();
	}

	/**
	 * AJAX handle cancel.
	 */
	public function ajax_handle_cancel() {
		check_ajax_referer( $this->tool_key . '_cancel', 'nonce' );

		if ( ! $this->is_queued() ) {
			return;
		}

		$this->background_process->cancel();
	}

	/**
	 * AJAX handle pause resume.
	 */
	public function ajax_handle_pause_resume() {
		check_ajax_referer( $this->tool_key . '_pause_resume', 'nonce' );

		if ( ! $this->is_queued() || $this->is_cancelled() ) {
			return;
		}

		if ( $this->is_paused() ) {
			$this->background_process->resume();
		} else {
			$this->background_process->pause();
		}
	}

	/**
	 * Create session.
	 *
	 * @return array
	 */
	protected function create_session() {
		$session = array(
			'total_attachments'     => 0,
			'processed_attachments' => 0,
			'blogs_processed'       => false,
			'blogs'                 => array(),
		);

		foreach ( $this->as3cf->get_all_blog_table_prefixes() as $blog_id => $prefix ) {
			$session['blogs'][ $blog_id ] = array(
				'prefix'             => $prefix,
				'processed'          => false,
				'total_attachments'  => null,
				'last_attachment_id' => null,
			);
		}

		return $session;
	}

	/**
	 * Get progress.
	 *
	 * @return int
	 */
	public function get_progress() {
		$batch = $this->get_batch();

		if ( empty( $batch ) ) {
			return 0;
		}

		$data = array_shift( $batch->data );

		if ( empty( $data['total_attachments'] ) || ! isset( $data['processed_attachments'] ) ) {
			return 0;
		}

		return absint( $data['processed_attachments'] / $data['total_attachments'] * 100 );
	}

	/**
	 * Is queued?
	 *
	 * @return bool
	 */
	public function is_queued() {
		$batch = $this->get_batch();

		if ( empty( $batch ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is the tool paused?
	 *
	 * @return bool
	 */
	public function is_paused() {
		return $this->background_process->is_paused();
	}

	/**
	 * Has the tool been cancelled?
	 *
	 * @return bool
	 */
	public function is_cancelled() {
		return $this->background_process->is_cancelled();
	}

	/**
	 * Is the background process currently running?
	 *
	 * @return bool
	 */
	public function is_processing() {
		return $this->background_process->is_process_running();
	}

	/**
	 * Get background process batch.
	 *
	 * @return array
	 */
	protected function get_batch() {
		static $batch;

		if ( is_null( $batch ) ) {
			$batch = $this->background_process->get_batches( 1 );
			$batch = array_shift( $batch );
		}

		return $batch;
	}

	/**
	 * Get title text.
	 *
	 * @return string
	 */
	abstract public function get_title_text();

	/**
	 * Get button text.
	 *
	 * @return string
	 */
	abstract public function get_button_text();

	/**
	 * Get queued status text.
	 *
	 * @return string
	 */
	abstract public function get_queued_status();

	/**
	 * Get background process class.
	 *
	 * @return Background_Tool_Process|null
	 */
	abstract protected function get_background_process_class();

}