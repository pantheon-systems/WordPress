<?php

namespace DeliciousBrains\WP_Offload_Media\Pro;

use AS3CF_Utils;

abstract class Modal_Tool extends Tool {

	/**
	 * @var string
	 */
	protected $type = 'modal-tool';

	/**
	 * @var string
	 */
	protected $lock_key;

	/**
	 * @var array
	 */
	protected $nonce_keys = array();

	/**
	 * Store errors throughout the tool process
	 *
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Store progress throughout the tool process
	 *
	 * @var array
	 */
	protected $progress = array();

	/**
	 * @var array
	 */
	protected $process_errors = array();

	/**
	 * Calculate and display the file size progress
	 *
	 * @var bool
	 */
	protected $show_file_size = true;

	/**
	 * Cache the total attachments in the Media Library
	 *
	 * @var array
	 */
	protected static $total_attachments = array();

	/**
	 * @var array
	 */
	public static $views_rendered = array();

	/**
	 * @var bool
	 */
	public static $assets_loaded = false;

	/**
	 * Modal_Tool constructor.
	 *
	 * @param \Amazon_S3_And_CloudFront_Pro $as3cf
	 */
	public function __construct( \Amazon_S3_And_CloudFront_Pro $as3cf ) {
		parent::__construct( $as3cf );

		$this->lock_key = $this->prefix . '_' . $this->tool_key;
	}

	/**
	 * Initialize the tool.
	 */
	public function init() {
		// JS data
		add_filter( 'as3cfpro_js_settings', array( $this, 'add_js_settings' ) );
		add_filter( 'as3cfpro_js_strings', array( $this, 'add_js_strings' ) );
		add_filter( 'as3cfpro_js_nonces', array( $this, 'add_js_nonces' ) );

		// AJAX
		add_action( 'wp_ajax_as3cfpro_initiate_' . $this->tool_key, array( $this, 'ajax_initiate_process' ) );
		add_action( 'wp_ajax_as3cfpro_calculate_items_' . $this->tool_key, array( $this, 'ajax_calculate_items' ) );
		add_action( 'wp_ajax_as3cfpro_process_items_' . $this->tool_key, array( $this, 'ajax_process_items' ) );
		add_action( 'wp_ajax_as3cfpro_finish_' . $this->tool_key, array( $this, 'ajax_finish_process' ) );

		// Views
		add_action( 'as3cf_post_settings_render', array( $this, 'render_modal' ) );

		// Assets
		add_action( 'as3cfpro_load_assets', array( $this, 'load_assets' ) );

		parent::init();
	}

	/**
	 * Add settings for the Tools to the Javascript
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function add_js_settings( $settings ) {
		// Global settings
		$settings['errors_key_prefix'] = $this->errors_key_prefix;

		// Per tool settings
		$defaults = array(
			'show_file_size' => $this->show_file_size,
		);

		$tool_settings = $this->get_tool_js_settings( $defaults );

		$settings['tools'][ $this->tool_key ] = $tool_settings;

		return $settings;
	}

	/**
	 * Add localized strings to the Javascript
	 *
	 * @param $strings
	 *
	 * @return array
	 */
	public function add_js_strings( $strings ) {
		// Global tool strings
		$defaults = array(
			'pause'    => _x( 'Pause', 'Temporarily stop process', 'amazon-s3-and-cloudfront' ),
			'complete' => _x( 'Complete', 'Process finished', 'amazon-s3-and-cloudfront' ),
			'resume'   => _x( 'Resume', 'Restart process after it was paused', 'amazon-s3-and-cloudfront' ),
			'hide'     => _x( 'Hide', 'Hide process errors', 'amazon-s3-and-cloudfront' ),
			'show'     => _x( 'Show', 'Show process errors', 'amazon-s3-and-cloudfront' ),
			'errors'   => _x( 'Errors', 'Process errors', 'amazon-s3-and-cloudfront' ),
			'error'    => _x( 'Error', 'Process error', 'amazon-s3-and-cloudfront' ),
		);

		$strings = array_merge( $strings, $defaults );

		// Tool specific strings
		$strings['tools'][ $this->tool_key ] = $this->get_tool_js_strings();

		return $strings;
	}

	/**
	 * Specific tool strings for Javascript
	 *
	 * @return array
	 */
	protected function get_tool_js_strings() {
		return array();
	}

	/**
	 * Specific tool settings for Javascript
	 *
	 * @param array $settings
	 *
	 * @return array
	 */
	function get_tool_js_settings( $settings ) {
		return $settings;
	}

	/**
	 * Add the nonces to the Javascript
	 *
	 * @param array $js_nonces
	 *
	 * @return array
	 */
	public function add_js_nonces( $js_nonces ) {
		$nonce_keys = array(
			'initiate',
			'calculate_items',
			'process_items',
			'finish',
			'update_notice',
			'dismiss_errors',
		);

		// Add defaults to any class specific keys
		$nonce_keys = array_merge( $nonce_keys, $this->nonce_keys );

		foreach ( $nonce_keys as $key ) {
			$nonce_slug = str_replace( '_', '-', $key ) . '-' . $this->tool_slug;

			$js_nonces[ $key . '_' . $this->tool_key ] = wp_create_nonce( $nonce_slug );
		}

		return $js_nonces;
	}

	/**
	 * Wrapper for getting all the sites in a network
	 *
	 * @return array
	 */
	protected function get_blogs_data() {
		return $this->as3cf->get_blogs_data();
	}

	/**
	 * Allow child classes to inject other data to the Initiate AJAX callback
	 *
	 * @return array
	 */
	protected function get_ajax_initiate_data() {
		return array(
			'error_count' => 0,
		);
	}

	/**
	 * AJAX handler for initiating the process
	 *
	 * @return array $return
	 */
	public function ajax_initiate_process() {
		check_ajax_referer( 'initiate-' . $this->tool_slug, 'nonce' );

		// Check for the lock
		if ( $this->is_processing() ) {
			wp_send_json_error( __( 'Tool already in progress.', 'amazon-s3-and-cloudfront' ) );
		}

		// Lock and cleanup after 5 minutes
		$this->lock_processing();

		// Clear previous queue items
		\AS3CF_Pro_Utils::delete_wildcard_options( $this->lock_key . '_%' );

		// Clear previous errors
		$this->clear_errors();

		$blogs    = $this->get_blogs_data();
		$defaults = array(
			'blogs' => $blogs,
		);

		$data = $this->get_ajax_initiate_data();

		if ( ! empty( $data ) ) {
			$data = array( 'progress' => $data );
		}

		$data = array_merge( $defaults, $data );

		wp_send_json( $data );
	}

	/**
	 * AJAX handler for the recursive calculation of attachments
	 */
	public function ajax_calculate_items() {
		check_ajax_referer( 'calculate-items-' . $this->tool_slug, 'nonce' );

		if ( ! isset( $_POST['blogs'] ) || ! isset( $_POST['progress'] ) ) {
			wp_die();
		}

		$blogs          = $_POST['blogs'];
		$this->progress = $_POST['progress'];
		$limit          = apply_filters( 'as3cfpro_calculate_batch_limit', 100 );
		$finish_time    = time() + apply_filters( 'as3cfpro_calculate_batch_time', 5 ); // Seconds;
		$files          = array();

		// Loop over each blog
		foreach ( $blogs as $id => $blog ) {
			$this->as3cf->switch_to_blog( $id );

			$count = 0;
			$total = $this->get_attachments_to_process( $blogs[ $id ]['prefix'], $id, true );

			if ( ! isset( $blogs[ $id ]['last_attachment'] ) ) {
				$blogs[ $id ]['last_attachment'] = null;
			}

			// Process attachments in batches
			do {
				$attachments = $this->get_attachments_to_process( $blogs[ $id ]['prefix'], $id, false, $limit, $blogs[ $id ]['last_attachment'] );

				if ( empty( $attachments ) ) {
					// No attachments remaining to process, remove blog from queue
					unset( $blogs[ $id ] );

					break;
				}

				foreach ( $attachments as $attachment ) {
					$data = maybe_unserialize( $attachment->data );
					if ( $this->should_process_attachment( $attachment->ID, $data ) ) {
						$size = 0;
						if ( $this->show_file_size ) {
							$size = $this->get_attachment_file_size( $attachment->ID, $data );
						}

						$files[ $id ][ $attachment->ID ] = $size;
						$this->progress['total_bytes']   += $size;

						$this->progress['total_files']++;
						$count++;

					} else {
						// Remove it from the grand total
						$total--;
					}

					$blogs[ $id ]['last_attachment'] = $attachment->ID;

					if ( time() >= $finish_time || $this->as3cf->memory_exceeded( $this->lock_key ) ) {
						// Time limit exceeded
						$this->as3cf->restore_current_blog();

						break 3;
					}
				}
			} while ( $count <= $total );

			$this->as3cf->restore_current_blog();
		}

		// No files to process, gracefully die
		if ( 0 === (int) $this->progress['total_files'] && empty( $this->progress['more_blogs'] ) ) {
			wp_send_json_error( __( 'No files to process.', 'amazon-s3-and-cloudfront' ) );
		}

		$data = array(
			'blogs'    => $blogs,
			'progress' => $this->progress,
		);

		// Save to options table
		$this->save_items_to_process( $files );

		wp_send_json( $data );
	}

	/**
	 * AJAX handler for the recursive process of items
	 */
	public function ajax_process_items() {
		check_ajax_referer( 'process-items-' . $this->tool_slug, 'nonce' );

		if ( ! isset( $_POST['progress'] ) ) {
			return;
		}

		// Update the lock transient expiry for the batch
		$this->lock_processing();

		$this->progress = $_POST['progress'];

		$batch_limit    = apply_filters( 'as3cfpro_' . $this->tool_key . '_batch_limit', 10 ); // number of attachments
		$batch_time     = apply_filters( 'as3cfpro_' . $this->tool_key . '_batch_time', 10 ); // seconds
		$batch_count    = 0;
		$finish_time    = time() + $batch_time;
		$limit_exceeded = false;

		$this->errors         = array();
		$this->process_errors = $this->get_errors();

		// Count queue items
		$queues      = $this->count_items_to_process();
		$queue_count = 0;

		// Loop over each batch
		do {
			$items = $this->get_items_to_process();

			if ( ! $items ) {
				// Queue empty
				break;
			}

			// Loop over each blog
			foreach ( $items->data as $blog_id => $attachments ) {
				$this->as3cf->switch_to_blog( $blog_id );

				// Loop over each attachment
				foreach ( $attachments as $attachment_id => $size ) {

					// Process the attachment
					if ( $this->handle_attachment( $attachment_id, $blog_id ) ) {
						$this->progress['total_done']++;
					}

					$this->progress['bytes'] += $size;
					$this->progress['files']++;
					$batch_count++;

					// Remove attachment from queue
					unset( $items->data[ $blog_id ][ $attachment_id ] );

					if ( time() >= $finish_time || $this->as3cf->memory_exceeded( $this->lock_key ) || $batch_count >= $batch_limit ) {
						// Time or memory limit exceeded or attachment limit exceeded
						$this->as3cf->restore_current_blog();
						$limit_exceeded = true;
						break 2;
					}
				}

				// Remove blog from queue
				unset( $items->data[ $blog_id ] );

				$this->as3cf->restore_current_blog();
			}

			if ( ! empty( $items->data ) ) {
				update_site_option( $items->key, $items->data );
			} else {
				delete_site_option( $items->key );
			}

			$queue_count++;
		} while ( ( $queue_count < $queues ) && ( ! $limit_exceeded ) );

		// Un-hide errors notice if new errors have occurred
		if ( count( $this->errors ) ) {
			$this->undismiss_error_notice();
		}

		// Save errors
		$this->update_errors( $this->process_errors );

		$this->progress['errors']     = $this->errors;
		$this->progress['errorsHtml'] = $this->capture_error_html();

		wp_send_json( $this->progress );
	}

	/**
	 * Perform any actions when the process has finished:
	 *      success
	 *      error
	 *      cancel
	 */
	public function ajax_finish_process() {
		check_ajax_referer( 'finish-' . $this->tool_slug, 'nonce' );

		$this->unlock_processing();

		$this->update_error_notice();
	}

	/**
	 * Allow child classes to inject custom notices to be updated in the DOM
	 *
	 * @return array
	 */
	protected function get_custom_notices_to_update() {
		return array();
	}

	/**
	 * Check if the attachment should be processed by the tool. Allows checks to be
	 * performed that will happen within the batch execution time limit.
	 *
	 * @param int   $attachment_id
	 * @param array $provider_object
	 *
	 * @return bool
	 */
	protected function should_process_attachment( $attachment_id, $provider_object ) {
		if ( is_wp_error( $provider_object ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the attachments to process
	 *
	 * @param string     $prefix  Table prefix for multisite support
	 * @param int        $blog_id ID of blog
	 * @param bool       $count   If enabled just returns a count of attachments
	 * @param null|int   $limit   Limit number of attachments returned
	 * @param null|int   $offset  Set last attachment id from previous batch as a starting point
	 * @param null|array $exclude Array of attachment ids to exclude when they have previously had errors during the
	 *                            process
	 *
	 * @return mixed
	 */
	abstract protected function get_attachments_to_process( $prefix, $blog_id, $count = false, $limit = null, $offset = null, $exclude = null );

	/**
	 * Do what we need to an attachment as part of the tool
	 *
	 * @param int $attachment_id
	 * @param int $blog_id
	 *
	 * @return bool
	 */
	abstract protected function handle_attachment( $attachment_id, $blog_id );

	/**
	 * Get the total of attachments in the media library
	 *
	 * @param string $prefix table prefix for multisite support
	 *
	 * @return mixed
	 */
	protected function get_total_attachments( $prefix ) {
		if ( isset( self::$total_attachments[ $prefix ] ) ) {
			return self::$total_attachments[ $prefix ];
		}

		global $wpdb;

		$sql = "SELECT COUNT(*)
				FROM `{$prefix}posts`
				WHERE `{$prefix}posts`.`post_type` = 'attachment'";

		self::$total_attachments[ $prefix ] = (int) $wpdb->get_var( $sql );

		return self::$total_attachments[ $prefix ];
	}

	/**
	 * Find the counts of attachments to process and overall total of attachments
	 *
	 *  - total_to_process
	 *  - total_attachments
	 *
	 * @return array
	 */
	public function get_attachments_to_process_stats() {
		$blogs = $this->as3cf->get_blogs_data();

		$total_attachments = 0;
		$total_to_process  = 0;

		foreach ( $blogs as $blog_id => $blog ) {
			$total_attachments += $this->get_total_attachments( $blog['prefix'] );
			$total_to_process  += $this->get_attachments_to_process( $blog['prefix'], $blog_id, true, null, null );
		}

		return compact( 'total_to_process', 'total_attachments' );
	}

	/**
	 * Save items to process
	 *
	 * @param $files
	 */
	protected function save_items_to_process( $files ) {
		$unique = md5( microtime() . rand() );
		$key    = substr( $this->lock_key . '_' . $unique, 0, 64 );
		update_site_option( $key, $files );
	}

	/**
	 * Get the file size of an attachment and all it's versions.
	 *
	 * @param int        $attachment_id
	 * @param array|bool $file_meta
	 *
	 * @return int Bytes
	 */
	protected function get_attachment_file_size( $attachment_id, $file_meta = false ) {
		$bytes = 0;
		$paths = AS3CF_Utils::get_attachment_file_paths( $attachment_id, true, $file_meta );

		foreach ( $paths as $path ) {
			$bytes += filesize( $path );
		}

		return $bytes;
	}

	/**
	 * Get items to process from queue
	 *
	 * @return bool|\stdClass
	 */
	protected function get_items_to_process() {
		global $wpdb;

		$table        = $wpdb->options;
		$column       = 'option_name';
		$key_column   = 'option_id';
		$value_column = 'option_value';

		if ( is_multisite() ) {
			$table        = $wpdb->sitemeta;
			$column       = 'meta_key';
			$key_column   = 'meta_id';
			$value_column = 'meta_value';
		}

		$query = $wpdb->get_row( $wpdb->prepare( "
			SELECT *
			FROM {$table}
			WHERE {$column} LIKE %s
			ORDER BY {$key_column} ASC
			LIMIT 1
		", $this->lock_key . '_%' ) );

		if ( is_null( $query ) ) {
			return false;
		}

		$batch       = new \stdClass();
		$batch->key  = $query->$column;
		$batch->data = maybe_unserialize( $query->$value_column );

		return $batch;
	}

	/**
	 * Count items to queued to process
	 *
	 * @return null|string
	 */
	protected function count_items_to_process() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM {$table}
			WHERE {$column} LIKE %s
		", $this->lock_key . '_%' ) );

		return $count;
	}

	/**
	 * Are we currently processing?
	 *
	 * @return bool
	 */
	protected function is_processing() {
		return $this->lock_key_exists( $this->lock_key );
	}

	/**
	 * Does the given lock key exist?
	 *
	 * @param string $lock_key
	 *
	 * @return bool
	 */
	public static function lock_key_exists( $lock_key = '' ) {
		if ( empty( $lock_key ) ) {
			return false;
		}

		return (bool) get_site_transient( $lock_key );
	}

	/**
	 * Set the tool as processing
	 */
	protected function lock_processing() {
		set_site_transient( $this->lock_key, true, 20 );
	}

	/**
	 * Unlock the tool as processing
	 */
	protected function unlock_processing() {
		delete_site_transient( $this->lock_key );
	}

	/**
	 * Add the modal view to the settings page once
	 */
	public function render_modal() {
		if ( ! in_array( 'progress', self::$views_rendered ) ) {
			$this->as3cf->render_view( 'tool-progress' );
			self::$views_rendered[] = 'progress';
		}
	}

	/**
	 * Load the assets for the tool once
	 */
	public function load_assets() {
		if ( ! self::$assets_loaded ) {
			$this->as3cf->enqueue_style( 'as3cf-pro-tool-styles', 'assets/css/pro/tool', array( 'as3cf-pro-styles' ) );
			$this->as3cf->enqueue_script( 'as3cf-pro-tool-script', 'assets/js/pro/tool', array( 'as3cf-pro-script', 'underscore' ) );

			self::$assets_loaded = true;
		}
	}

	/**
	 * Capture the rendered HTML of tool errors from the process.
	 *
	 * @return string
	 */
	protected function capture_error_html() {
		ob_start();

		$this->as3cf->render_view( 'tool-errors', array(
			'tool'   => $this->tool_key,
			'errors' => $this->process_errors,
		) );

		return ob_get_clean();
	}
}
