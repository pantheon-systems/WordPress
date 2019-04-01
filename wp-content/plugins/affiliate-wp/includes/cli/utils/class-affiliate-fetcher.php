<?php
namespace AffWP\Affiliate\CLI;

use \WP_CLI\Fetchers\Base;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Implements a single object fetcher for affiliates.
 *
 * @since 1.9
 *
 * @see \WP_CLI\Fetchers\Base
 */
class Fetcher extends Base {

	/**
	 * Not found message.
	 * 
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $msg = "Could not find the affiliate with ID %s.";

	/**
	 * Retrieves an affiliate by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int $arg Affiliate ID.
	 * @return \AffWP\Affiliate|false Affiliate object, false otherwise.
	 */
	public function get( $arg ) {
		return affwp_get_affiliate( $arg );
	}
}
