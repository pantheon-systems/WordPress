<?php
namespace AffWP\Creative\CLI;

use \WP_CLI\Fetchers\Base;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Implements a single object fetcher for creatives.
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
	protected $msg = "Could not find a creative with ID %s.";

	/**
	 * Retrieves a creative by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int $arg Creative ID.
	 * @return \AffWP\Creative|false Creative object, false otherwise.
	 */
	public function get( $arg ) {
		return affwp_get_creative( $arg );
	}
}
