<?php
/**
 * Sensor: Files
 *
 * Files sensors class file.
 *
 * @since 1.0.0
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Files sensor.
 *
 * 2010 User uploaded file in Uploads directory
 * 2011 User deleted file from Uploads directory
 * 2046 User changed a file using the theme editor
 * 2051 User changed a file using the plugin editor
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_Files extends WSAL_AbstractSensor {

	/**
	 * File uploaded.
	 *
	 * @var boolean
	 */
	protected $is_file_uploaded = false;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		add_action( 'add_attachment', array( $this, 'EventFileUploaded' ) );
		add_action( 'delete_attachment', array( $this, 'EventFileUploadedDeleted' ) );

		/**
		 * Commenting the code to detect file changes in plugins and themes.
		 *
		 * @todo Figure out a way to detect changes in files of plugins and themes.
		 * With the introduction of the new code editor in 4.9 the previous code
		 * stopped working.
		 */
		// add_action( 'admin_init', array( $this, 'EventAdminInit' ) );
	}

	/**
	 * File uploaded.
	 *
	 * @param integer $attachment_id - Attachment ID.
	 */
	public function EventFileUploaded( $attachment_id ) {
		// Filter $_POST array for security.
		$post_array = filter_input_array( INPUT_POST );

		$action = isset( $post_array['action'] ) ? $post_array['action'] : '';
		if ( 'upload-theme' !== $action && 'upload-plugin' !== $action ) {
			$file = get_attached_file( $attachment_id );
			$this->plugin->alerts->Trigger(
				2010, array(
					'AttachmentID' => $attachment_id,
					'FileName' => basename( $file ),
					'FilePath' => dirname( $file ),
				)
			);
		}
		$this->is_file_uploaded = true;
	}

	/**
	 * Deleted file from uploads directory.
	 *
	 * @param integer $attachment_id - Attachment ID.
	 */
	public function EventFileUploadedDeleted( $attachment_id ) {
		if ( $this->is_file_uploaded ) {
			return;
		}
		$file = get_attached_file( $attachment_id );
		$this->plugin->alerts->Trigger(
			2011, array(
				'AttachmentID' => $attachment_id,
				'FileName' => basename( $file ),
				'FilePath' => dirname( $file ),
			)
		);
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		// Filter global arrays for security.
		$post_array = filter_input_array( INPUT_POST );
		$server_array = filter_input_array( INPUT_SERVER );

		$action = isset( $post_array['action'] ) ? $post_array['action'] : '';
		$script_name = isset( $server_array['SCRIPT_NAME'] ) ? basename( $server_array['SCRIPT_NAME'] ) : false;
		$is_theme_editor = 'theme-editor.php' == $script_name;
		$is_plugin_editor = 'plugin-editor.php' == $script_name;

		if ( $is_theme_editor && 'update' === $action ) {
			$this->plugin->alerts->Trigger(
				2046, array(
					'File' => $post_array['file'],
					'Theme' => $post_array['theme'],
				)
			);
		}

		if ( $is_plugin_editor && 'update' === $action ) {
			$this->plugin->alerts->Trigger(
				2051, array(
					'File' => $post_array['file'],
					'Plugin' => $post_array['plugin'],
				)
			);
		}
	}
}
