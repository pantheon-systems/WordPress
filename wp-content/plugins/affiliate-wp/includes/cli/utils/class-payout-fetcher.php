<?php
namespace AffWP\Affiliate\Payout\CLI;

use \WP_CLI\Fetchers\Base;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Implements a single object fetcher for payouts.
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
	protected $msg = "Could not find the payout with ID %s.";

	/**
	 * Retrieves a payout by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int $arg Payout ID.
	 * @return \AffWP\Affiliate\Payout|false Payout object, false otherwise.
	 */
	public function get( $arg ) {
		return affwp_get_payout( $arg );
	}
}
