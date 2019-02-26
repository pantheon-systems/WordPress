<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Tools;

use DeliciousBrains\WP_Offload_Media\Pro\Modal_Tool;

class Uploader extends Modal_Tool {

	/**
	 * @var string
	 */
	protected $tool_key = 'uploader';

	/**
	 * Initialize Downloader
	 */
	public function init() {
		parent::init();

		$this->error_setting_migration();
	}

	/**
	 * Migrate old upload errors to new setting key
	 */
	protected function error_setting_migration() {
		if ( false !== ( $errors = $this->as3cf->get_setting( 'bulk_upload_errors', false ) ) ) {
			$this->update_errors( $errors );
			$this->as3cf->remove_setting( 'bulk_upload_errors' );
			$this->as3cf->save_settings();
		}
	}

	/**
	 * Specific tool settings for Javascript
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	function get_tool_js_settings( $settings ) {
		$settings['remove_local_file'] = $this->as3cf->get_setting( 'remove-local-file' );

		return $settings;
	}

	/**
	 * Add our tools strings for Javascript
	 *
	 * @return array
	 */
	protected function get_tool_js_strings() {
		$strings = array(
			'tool_title'                        => __( 'Offloading Media Library', 'amazon-s3-and-cloudfront' ),
			'zero_files_processed'              => _x( 'Files Offloaded', 'Number of files offloaded to bucket', 'amazon-s3-and-cloudfront' ),
			'files_processed'                   => _x( '%1$d of %2$d Files Offloaded', 'Number of files out of total offloaded to bucket', 'amazon-s3-and-cloudfront' ),
			'completed_with_some_errors'        => __( 'Offload completed with some errors', 'amazon-s3-and-cloudfront' ),
			'partial_complete_with_some_errors' => __( 'Offload partially completed with some errors', 'amazon-s3-and-cloudfront' ),
			'cancelling_process'                => _x( 'Cancelling offload', 'The offload is being cancelled', 'amazon-s3-and-cloudfront' ),
			'completing_current_request'        => __( 'Completing current media offload batch', 'amazon-s3-and-cloudfront' ),
			'paused'                            => _x( 'Paused', 'The offload has been temporarily stopped', 'amazon-s3-and-cloudfront' ),
			'pausing'                           => _x( 'Pausing&hellip;', 'The offload is being paused', 'amazon-s3-and-cloudfront' ),
			'process_cancellation_failed'       => __( 'Offload cancellation failed', 'amazon-s3-and-cloudfront' ),
			'process_cancelled'                 => _x( 'Offload cancelled', 'The offload has been cancelled', 'amazon-s3-and-cloudfront' ),
			'finalizing_process'                => _x( 'Finalizing offload', 'The offload is in the last stages', 'amazon-s3-and-cloudfront' ),
			'sure'                              => _x( 'Are you sure you want to leave whilst offloading to bucket?', 'Confirmation required', 'amazon-s3-and-cloudfront' ),
			'process_failed'                    => _x( 'Offload failed', 'Copy of data to bucket did not complete', 'amazon-s3-and-cloudfront' ),
			'process_paused'                    => _x( 'Offload Paused', 'The offload has been temporarily stopped', 'amazon-s3-and-cloudfront' ),
		);

		return $strings;
	}

	/**
	 * Get the attachments not uploaded to S3
	 *
	 * @param string     $prefix
	 * @param int        $blog_id
	 * @param bool       $count
	 * @param null|int   $limit
	 * @param null|int   $offset
	 * @param null|array $exclude
	 *
	 * @return array|int
	 */
	protected function get_attachments_to_process( $prefix, $blog_id, $count = false, $limit = null, $offset = null, $exclude = null ) {
		global $wpdb;

		$limit_sql = $offset_sql = $join_sql = $where_sql = '';

		if ( $count ) {
			$select_sql = 'SELECT COUNT(*)';
			$action     = 'get_var';
		} else {
			$select_sql = "SELECT p.`ID`, pm2.`meta_value` as 'data', {$blog_id} AS 'blog_id'";
			$join_sql   = "LEFT OUTER JOIN `{$prefix}postmeta` pm2
			            ON p.`ID` = pm2.`post_id`
			            AND pm2.`meta_key` = '_wp_attachment_metadata'";
			if ( ! is_null( $offset ) ) {
				$offset     = absint( $offset );
				$offset_sql .= "AND p.`ID` < {$offset}";
			}
			if ( ! is_null( $limit ) ) {
				$limit     = absint( $limit );
				$limit_sql = "LIMIT {$limit}";
			}
			if ( ! is_null( $exclude ) ) {
				$post__not_in = implode( ',', array_map( 'absint', (array) $exclude ) );
				$where_sql    = "AND p.`ID` not in ({$post__not_in})";
			}

			$action = 'get_results';
		}

		/**
		 * Allow users to exclude certain MIME types from attachments to upload.
		 *
		 * @param array
		 */
		$ignored_mime_types = apply_filters( 'as3cf_ignored_mime_types', array() );
		if ( is_array( $ignored_mime_types ) && ! empty( $ignored_mime_types ) ) {
			$ignored_mime_types = array_map( 'sanitize_text_field', $ignored_mime_types );
			$exclude_mime_types = ' AND p.post_mime_type NOT IN ("' . implode( '", "', $ignored_mime_types ) . '")';
		} else {
			$exclude_mime_types = '';
		}

		$sql = $select_sql . ' ';
		$sql .= "FROM `{$prefix}posts` p
				LEFT OUTER JOIN `{$prefix}postmeta` pm
				ON p.`ID` = pm.`post_id`
				AND pm.`meta_key` = 'amazonS3_info'";
		$sql .= ' ' . $join_sql . ' ';
		$sql .= "WHERE p.`post_type` = 'attachment'
				AND pm.`post_id` IS NULL";
		$sql .= ' ' . $where_sql;
		$sql .= $exclude_mime_types;
		$sql .= ' ' . $offset_sql;
		$sql .= ' ORDER BY p.`ID` DESC';
		$sql .= ' ' . $limit_sql;

		$results = $wpdb->$action( $sql );

		return $results;
	}

	/**
	 * Get the allowed number of items to upload and pass to the AJAX callback
	 *
	 * @return array
	 */
	protected function get_ajax_initiate_data() {
		$total_allowed_items_to_upload = $this->as3cf->get_total_allowed_media_items_to_upload();

		if ( false === $total_allowed_items_to_upload ) {
			$response = array(
				'success' => false,
				'data'    => __( 'Unable to reach deliciousbrains.com ', 'amazon-s3-and-cloudfront' ),
				'errors'  => array( __( 'We have been unable to retrieve the number of items you can offload. Please try again later, or contact support if the issue persists.', 'amazon-s3-and-cloudfront' ) ),
			);

			wp_send_json( $response );
		}

		$data = array(
			'total_allowed_items' => $total_allowed_items_to_upload,
			'error_count'         => 0,
		);

		return $data;
	}

	/**
	 * Get the details for the sidebar block
	 *
	 * @return array|false
	 */
	protected function get_sidebar_block_args() {
		if ( ! $this->as3cf->is_pro_plugin_setup() ) {
			// Don't show tool if pro not setup
			return false;
		}

		// Don't show upload tool if bucket isn't writable
		$can_write = $this->as3cf->check_write_permission();
		if ( ! $can_write || is_wp_error( $can_write ) ) {
			return false;
		}

		$stats = $this->get_attachments_to_process_stats();

		// Don't show upload banner if media library empty
		if ( 0 === $stats['total_attachments'] ) {
			return false;
		}

		$uploaded_percentage = (float) ( $stats['total_attachments'] - $stats['total_to_process'] ) / $stats['total_attachments'];
		$human_percentage    = (int) floor( $uploaded_percentage * 100 );

		// Percentage of library needs uploading
		if ( 0 === $human_percentage && $uploaded_percentage > 0 ) {
			$human_percentage = 1;
		}

		$states = array(
			0   => 'initial',
			1   => 'partial_complete',
			100 => 'complete',
		);

		$i18n = array(
			'title_initial'           => __( 'Your Media Library needs to be offloaded', 'amazon-s3-and-cloudfront' ),
			'title_partial_complete'  => __( "%s%% of your Media Library has been offloaded", 'amazon-s3-and-cloudfront' ),
			'title_complete'          => __( '100% of your Media Library has been offloaded, congratulations!', 'amazon-s3-and-cloudfront' ),
			'upload_initial'          => __( 'Offload Now', 'amazon-s3-and-cloudfront' ),
			'upload_partial_complete' => __( 'Offload Remaining Now', 'amazon-s3-and-cloudfront' ),
		);

		switch ( $human_percentage ) {
			case 0 : // Entire library needs uploading
				$title              = $i18n['title_initial'];
				$upload_button_text = $i18n['upload_initial'];
				break;

			case 100 : // Entire media library uploaded
				$title              = $i18n['title_complete'];
				$upload_button_text = $i18n['upload_partial_complete'];

				// Remove previous errors
				$this->clear_errors();
				$this->as3cf->notices->remove_notice_by_id( $this->errors_key );
				break;

			default: // Media library upload partially complete
				$title              = sprintf( $i18n['title_partial_complete'], $human_percentage );
				$upload_button_text = $i18n['upload_partial_complete'];
		}

		$args = array(
			'title'            => $title,
			'progress_percent' => $human_percentage,
			'button_title'     => $upload_button_text,
			'pie_chart'        => 1,
			'i18n'             => $i18n,
			'states'           => $states,
		);

		return $args;
	}

	/**
	 * Check there is enough allowed items for the license before uploading
	 *
	 * @param int $attachment_id
	 * @param int $blog_id
	 */
	protected function should_upload_attachment( $attachment_id, $blog_id ) {
		$total_allowed = (int) $this->progress['total_allowed_items'];

		if ( -1 === $total_allowed || $total_allowed > 0 ) {
			return;
		}

		// Throw fatal error as we have run out of allowed items to upload for the license
		$account_link = sprintf( '<a href="%s" target="_blank">%s</a>', $this->as3cf->get_my_account_url(), __( 'My Account', 'amazon-s3-and-cloudfront' ) );
		$notice_msg   = __( "You've reached your license limit so we've had to stop your upload. To upload the rest of your Media Library, please upgrade your license from %s and simply run the uploader again. It will pick up where it stopped.", 'amazon-s3-and-cloudfront' );
		$error_msg    = sprintf( $notice_msg, $account_link );

		$this->process_errors[ $blog_id ][ $attachment_id ][] = $error_msg;
		$this->update_errors( $this->process_errors );

		$response = array(
			'success' => false,
			'data'    => __( 'Offload has been stopped', 'amazon-s3-and-cloudfront' ),
			'errors'  => array( $error_msg ),
		);

		wp_send_json( $response );
	}

	/**
	 * Upload the attachment to S3
	 *
	 * @param int $attachment_id
	 * @param int $blog_id
	 *
	 * @return bool
	 */
	protected function handle_attachment( $attachment_id, $blog_id ) {
		// Check we are allowed to upload
		$this->should_upload_attachment( $attachment_id, $blog_id );

		// Skip item if attachment already on S3
		if ( $this->as3cf->get_attachment_provider_info( $attachment_id ) ) {
			return false;
		}

		$provider_object = $this->as3cf->upload_attachment( $attachment_id, null, null, false );

		// Build error message
		if ( is_wp_error( $provider_object ) ) {
			$this->progress['error_count']++;

			if ( $this->progress['error_count'] <= 100 ) {
				foreach ( $provider_object->get_error_messages() as $error_message ) {
					$error_msg                                            = sprintf( __( 'Error offloading to bucket - %s', 'amazon-s3-and-cloudfront' ), $error_message );
					$this->errors[]                                       = $error_msg;
					$this->process_errors[ $blog_id ][ $attachment_id ][] = $error_msg;
				}
			}

			return false;
		}

		if ( $this->progress['total_allowed_items'] > 0 ) {
			$this->progress['total_allowed_items']--;
		}

		return true;
	}

	/**
	 * Update the Delicious Brains API with the latest total of items on S3 for site
	 */
	public function ajax_finish_process() {
		parent::ajax_finish_process();

		$this->as3cf->update_media_library_total();
	}

	/**
	 * Retrieve the license notice so it can be refreshed behind the tool modal
	 *
	 * @return array
	 */
	protected function get_custom_notices_to_update() {
		$notices = array();

		ob_start();
		$this->as3cf->render_licence_issue_notice();
		$license_issue_notice_html = ob_get_contents();
		ob_end_clean();

		$notices[] = array(
			'id'   => 'as3cf-pro-license-notice',
			'html' => $license_issue_notice_html,
		);

		return $notices;
	}

	/**
	 * Message for error notice
	 *
	 * @return string
	 */
	protected function get_error_notice_message() {
		$title   = __( 'Offload Errors', 'amazon-s3-and-cloudfront' );
		$message = __( 'Previous attempts at offloading your media library have resulted in errors.', 'amazon-s3-and-cloudfront' );

		return sprintf( '<strong>%s</strong> &mdash; %s', $title, $message );
	}

	/**
	 * Get status.
	 *
	 * @return array
	 */
	public function get_status() {
		$stats = $this->get_attachments_to_process_stats();

		return array_merge( parent::get_status(), array(
			'total_on_provider' => (int) $this->as3cf->get_media_library_provider_total( true ),
			'total_items'       => (int) $stats['total_attachments'],
		) );
	}
}
