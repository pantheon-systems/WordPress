<?php

namespace DeliciousBrains\WP_Offload_Media\Pro\Background_Processes;

use AS3CF_Background_Process;
use AS3CF_Error;
use AS3CF_Utils;
use DeliciousBrains\WP_Offload_Media\Pro\Tool;
use DeliciousBrains\WP_Offload_Media\Providers\Provider;

abstract class Background_Tool_Process extends AS3CF_Background_Process {

	/**
	 * @var Tool
	 */
	protected $tool;

	/**
	 * Default batch limit.
	 *
	 * @var int
	 */
	protected $limit = 100;

	/**
	 * Default chunk size.
	 *
	 * @var int
	 */
	protected $chunk = 10;

	/**
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Initiate new background tool process.
	 *
	 * @param object $as3cf Instance of calling class
	 * @param Tool   $tool
	 */
	public function __construct( $as3cf, $tool ) {
		parent::__construct( $as3cf );

		$this->tool = $tool;
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		if ( ! $item['blogs_processed'] ) {
			// Calculate how many attachments each blog has
			$item = $this->calculate_blog_attachments( $item );
		}

		if ( $this->all_blog_attachments_processed( $item ) ) {
			// Batch complete, remove from queue
			return false;
		}

		return $this->process_blogs( $item );
	}

	/**
	 * Calculate the number of attachments across all blogs.
	 *
	 * @param array $item
	 *
	 * @return array
	 */
	protected function calculate_blog_attachments( $item ) {
		foreach ( $item['blogs'] as $blog_id => $blog ) {
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				// Batch limits reached
				return $item;
			}

			if ( ! is_null( $blog['total_attachments'] ) ) {
				// Blog already processed, move on
				continue;
			}

			$count = $this->as3cf->count_attachments( $blog['prefix'] );

			if ( $count ) {
				$item['blogs'][ $blog_id ]['total_attachments']  = $count;
				$item['blogs'][ $blog_id ]['last_attachment_id'] = $this->get_blog_last_attachment_id( $blog ) + 1;
				$item['total_attachments']                       += $count;
			} else {
				$item['blogs'][ $blog_id ]['processed']         = true;
				$item['blogs'][ $blog_id ]['total_attachments'] = 0;
			}
		}

		$item['blogs_processed'] = true;

		return $item;
	}

	/**
	 * Loop over each blog and process attachments.
	 *
	 * @param array $item
	 *
	 * @return array
	 */
	protected function process_blogs( $item ) {
		$this->errors = $this->tool->get_errors();

		foreach ( $item['blogs'] as $blog_id => $blog ) {
			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				// Batch limits reached
				break;
			}

			if ( $blog['processed'] ) {
				// Blog processed, move onto the next
				continue;
			}

			$this->as3cf->switch_to_blog( $blog_id );

			$limit       = apply_filters( "as3cf_tool_{$this->action}_batch_size", $this->limit );
			$attachments = $this->get_blog_attachments( $blog, $limit );
			$item        = $this->process_blog_attachments( $item, $blog_id, $attachments );

			$this->as3cf->restore_current_blog();
		}

		if ( count( $this->errors ) ) {
			$this->tool->update_errors( $this->errors );
			$this->tool->update_error_notice( $this->errors );
			$this->tool->undismiss_error_notice();
		}

		return $item;
	}

	/**
	 * Process blog attachments.
	 *
	 * @param array $item
	 * @param int   $blog_id
	 * @param array $attachments
	 *
	 * @return array
	 */
	protected function process_blog_attachments( $item, $blog_id, $attachments ) {
		$chunks = array_chunk( $attachments, $this->chunk );

		foreach ( $chunks as $chunk ) {
			$this->process_attachments_chunk( $chunk, $blog_id );

			$item['processed_attachments']                   += count( $chunk );
			$item['blogs'][ $blog_id ]['last_attachment_id'] = end( $chunk );

			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				break;
			}
		}

		if ( empty( $attachments ) ) {
			$item['blogs'][ $blog_id ]['processed'] = true;
		}

		return $item;
	}

	/**
	 * Get blog attachments to process.
	 *
	 * @param array $blog
	 * @param int   $limit
	 *
	 * @return array|null|object
	 */
	protected function get_blog_attachments( $blog, $limit ) {
		global $wpdb;

		$sql = "SELECT posts.ID FROM `{$blog['prefix']}posts` AS posts
		        INNER JOIN `{$blog['prefix']}postmeta` AS postmeta
		        ON posts.ID = postmeta.post_id
		        WHERE posts.post_type = 'attachment'
		        AND postmeta.meta_key = 'amazonS3_info'";

		if ( ! empty( $blog['last_attachment_id'] ) ) {
			$sql .= " AND posts.ID < '{$blog['last_attachment_id']}'";
		}

		$sql .= " ORDER BY posts.ID DESC LIMIT {$limit}";

		return array_map( 'intval', $wpdb->get_col( $sql ) );
	}

	/**
	 * Get blog last attachment ID.
	 *
	 * @param array $blog
	 *
	 * @return int
	 */
	protected function get_blog_last_attachment_id( $blog ) {
		$attachments = $this->get_blog_attachments( $blog, 1 );

		return empty( $attachments ) ? 0 : reset( $attachments );
	}

	/**
	 * Have all blog attachments been processed?
	 *
	 * @param array $item
	 *
	 * @return bool
	 */
	protected function all_blog_attachments_processed( $item ) {
		foreach ( $item['blogs'] as $blog ) {
			if ( ! $blog['processed'] ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Get object keys that exist on S3 for attachments.
	 *
	 * It's possible that attachments belong to different buckets therefore they could have
	 * different regions, so we have to build an array of clients and commands.
	 *
	 * @param array $attachments
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function get_provider_keys( $attachments ) {
		$regions = array();

		foreach ( $attachments as $attachment_id ) {
			if ( ! $this->as3cf->is_attachment_served_by_provider( $attachment_id, true ) ) {
				continue;
			}

			$provider_info = $this->as3cf->get_attachment_provider_info( $attachment_id );

			$region = empty( $provider_info['region'] ) ? 'us-east-1' : $provider_info['region'];

			if ( ! isset( $regions[ $region ]['provider_client'] ) ) {
				$regions[ $region ]['provider_client'] = $this->as3cf->get_provider_client( $region, true );
			}

			$regions[ $region ]['locations'][ $attachment_id ] = array(
				'Bucket' => $provider_info['bucket'],
				'Prefix' => AS3CF_Utils::strip_image_edit_suffix_and_extension( $provider_info['key'] ),
			);
		}

		return Provider::get_keys_from_regions( $regions );
	}

	/**
	 * Record new error.
	 *
	 * @param int    $blog_id
	 * @param int    $attachment_id
	 * @param string $message
	 */
	protected function record_error( $blog_id, $attachment_id, $message ) {
		$this->errors[ $blog_id ][ $attachment_id ][] = $message;

		AS3CF_Error::log( $message );
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		$notice_id = $this->tool->get_tool_key() . '_completed';

		$this->as3cf->notices->undismiss_notice_for_all( $notice_id );

		$message = $this->get_complete_message();
		$args    = array(
			'custom_id'         => $notice_id,
			'type'              => 'updated',
			'flash'             => false,
			'only_show_to_user' => false,
		);

		$this->as3cf->notices->add_notice( $message, $args );
	}

	/**
	 * Process attachments chunk.
	 *
	 * @param array $attachments
	 * @param int   $blog_id
	 */
	abstract protected function process_attachments_chunk( $attachments, $blog_id );

	/**
	 * Get complete notice message.
	 *
	 * @return string
	 */
	abstract protected function get_complete_message();
}
