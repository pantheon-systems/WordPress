<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Tools;

use DeliciousBrains\WP_Offload_Media\Pro\Background_Processes\Background_Tool_Process;
use DeliciousBrains\WP_Offload_Media\Pro\Background_Processes\Remove_Local_Files_Process;
use DeliciousBrains\WP_Offload_Media\Pro\Background_Tool;

class Remove_Local_Files extends Background_Tool {

	/**
	 * @var string
	 */
	protected $tool_key = 'remove_local_files';

	/**
	 * @var string
	 */
	protected $tab = 'media';

	/**
	 * Initialize the tool.
	 */
	public function init() {
		parent::init();

		if ( ! $this->as3cf->is_pro_plugin_setup() ) {
			return;
		}

		add_filter( 'as3cf_media_tab_storage_classes', array( $this, 'media_tab_storage_classes' ) );
		add_action( 'as3cf_pre_media_settings', array( $this, 'render_modal' ) );
		add_filter( 'as3cf_handle_post_request', array( $this, 'handle_post_request' ) );
		add_filter( 'as3cf_action_for_changed_settings_key', array( $this, 'action_for_changed_settings_key' ), 10, 2 );
	}

	/**
	 * Get the details for the sidebar block
	 *
	 * @return array|bool
	 */
	protected function get_sidebar_block_args() {
		if ( ! $this->as3cf->is_pro_plugin_setup() ) {
			return false;
		}

		return parent::get_sidebar_block_args();
	}

	/**
	 * Maybe start remove local files process via post request.
	 *
	 * @param array $changed_keys
	 *
	 * @return array
	 */
	public function handle_post_request( $changed_keys ) {
		if (
			! empty( $_GET['action'] ) &&
			'remove-local-files' === $_GET['action'] &&
			! $this->as3cf->get_provider()->needs_access_keys() &&
			$this->as3cf->get_setting( 'bucket' ) &&
			! empty( $_POST['remove-local-files'] )
		) {
			$this->handle_start();
		}

		return $changed_keys;
	}

	/**
	 * Should the Remove all files from server prompt be the next action?
	 *
	 * @param string $action
	 * @param string $key
	 *
	 * @return string
	 */
	public function action_for_changed_settings_key( $action, $key ) {
		if ( empty( $action ) && 'remove-local-file' === $key && $this->should_render() ) {
			return 'remove-local-files';
		}

		return $action;
	}

	/**
	 * Adjust media tab's class attribute.
	 *
	 * @param string $storage_classes Class names related to storage provider.
	 *
	 * @return string
	 */
	public function media_tab_storage_classes( $storage_classes ) {
		if ( ! empty( $_GET['action'] ) && 'remove-local-files' === $_GET['action'] ) {
			$storage_classes .= ' as3cf-remove-local-files';
		}

		return $storage_classes;
	}

	/**
	 * Render modal in footer.
	 */
	public function render_modal() {
		if ( ! $this->should_render() ) {
			return;
		}

		$this->as3cf->render_view( 'modals/remove-local-files' );
	}

	/**
	 * Should render.
	 *
	 * @return bool
	 */
	public function should_render() {
		return $this->count_media_files() && (bool) $this->as3cf->get_setting( 'remove-local-file', false );
	}

	/**
	 * Get title text.
	 *
	 * @return string
	 */
	public function get_title_text() {
		return __( 'Remove all files from server', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get more info text.
	 *
	 * @return string
	 */
	public function get_more_info_text() {
		return __( 'You can use this tool to delete all Media Library files from your local server that have already been offloaded.', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get button text.
	 *
	 * @return string
	 */
	public function get_button_text() {
		return __( 'Begin Removal', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get queued status text.
	 *
	 * @return string
	 */
	public function get_queued_status() {
		return __( 'Removing Media Library files from your local server.', 'amazon-s3-and-cloudfront' );
	}

	/**
	 * Get background process class.
	 *
	 * @return Background_Tool_Process|null
	 */
	protected function get_background_process_class() {
		return new Remove_Local_Files_Process( $this->as3cf, $this );
	}
}