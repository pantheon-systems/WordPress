<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Tools;

class Download_And_Remover extends Downloader {

	/**
	 * @var string
	 */
	protected $tool_key = 'download_and_remover';

	/**
	 * Get the details for the sidebar block
	 *
	 * @return array|bool
	 */
	protected function get_sidebar_block_args() {
		if ( ! $this->as3cf->is_plugin_setup( true ) ) {
			// Don't show tool if not setup
			return false;
		}

		$to_process_stats = $this->get_attachments_to_process_stats();

		// Don't show tool if media library empty
		if ( 0 === $to_process_stats['total_attachments'] ) {
			return false;
		}

		// Don't show tool if no attachments uploaded to bucket
		if ( 0 === $to_process_stats['total_to_process'] ) {
			return false;
		}

		$args = array(
			'title'        => __( 'Remove all files from bucket', 'amazon-s3-and-cloudfront' ),
			'button_title' => __( 'Remove Files', 'amazon-s3-and-cloudfront' ),
			'description'  => __( 'This tool goes through all your Media Library attachments and deletes files from the bucket. If the file doesn\'t exist on your server, it will download it before deleting.', 'amazon-s3-and-cloudfront' ),
		);

		return $args;
	}

	/**
	 * Add our tools strings for Javascript
	 *
	 * @return array
	 */
	protected function get_tool_js_strings() {
		$strings = array(
			'tool_title'                        => __( 'Removing Media Library from bucket', 'amazon-s3-and-cloudfront' ),
			'zero_files_processed'              => _x( 'Files Processed', 'Number of files removed from bucket', 'amazon-s3-and-cloudfront' ),
			'files_processed'                   => _x( '%1$d of %2$d Files Removed', 'Number of files out of total removed from bucket', 'amazon-s3-and-cloudfront' ),
			'completed_with_some_errors'        => __( 'Removal completed with some errors', 'amazon-s3-and-cloudfront' ),
			'partial_complete_with_some_errors' => __( 'Removal partially completed with some errors', 'amazon-s3-and-cloudfront' ),
			'cancelling_process'                => _x( 'Cancelling removal', 'The removal is being cancelled', 'amazon-s3-and-cloudfront' ),
			'completing_current_request'        => __( 'Completing current media removal batch', 'amazon-s3-and-cloudfront' ),
			'paused'                            => _x( 'Paused', 'The removal has been temporarily stopped', 'amazon-s3-and-cloudfront' ),
			'pausing'                           => _x( 'Pausing&hellip;', 'The removal is being paused', 'amazon-s3-and-cloudfront' ),
			'process_cancellation_failed'       => __( 'Removal cancellation failed', 'amazon-s3-and-cloudfront' ),
			'process_cancelled'                 => _x( 'Removal cancelled', 'The removal has been cancelled', 'amazon-s3-and-cloudfront' ),
			'finalizing_process'                => _x( 'Finalizing removal', 'The removal is in the last stages', 'amazon-s3-and-cloudfront' ),
			'sure'                              => _x( 'Are you sure you want to leave whilst removing from bucket?', 'Confirmation required', 'amazon-s3-and-cloudfront' ),
			'process_failed'                    => _x( 'Removal failed', 'Removal of attachments from the bucket did not complete', 'amazon-s3-and-cloudfront' ),
			'process_paused'                    => _x( 'Removal Paused', 'The removal has been temporarily stopped', 'amazon-s3-and-cloudfront' ),
		);

		return $strings;
	}

	/**
	 * Message for error notice
	 *
	 * @return string
	 */
	protected function get_error_notice_message() {
		$title   = __( 'Removal Errors', 'amazon-s3-and-cloudfront' );
		$message = __( 'Previous attempts at removing your media library from the bucket have resulted in errors.', 'amazon-s3-and-cloudfront' );

		return sprintf( '<strong>%s</strong> &mdash; %s', $title, $message );
	}

	/**
	 * Handle attachment success
	 *
	 * @param int $attachment_id
	 */
	protected function handle_attachment_success( $attachment_id ) {
		$this->as3cf->delete_attachment( $attachment_id, true );
	}
}
