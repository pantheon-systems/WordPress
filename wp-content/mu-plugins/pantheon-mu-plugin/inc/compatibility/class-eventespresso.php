<?php
/**
 * Compatibility fix for Event Espresso
 *
 * @link https://docs.pantheon.io/plugins-known-issues#event-espresso
 * @package Pantheon\Compatibility
 */

namespace Pantheon\Compatibility;

/**
 * Class EventEspresso
 */
class EventEspresso extends Base {
	/**
	 * Run fix on each request.
	 *
	 * @var bool
	 */
	protected $run_fix_everytime = true;

	/**
	 * @return void
	 */
	public function apply_fix() {
		add_filter( 'FHEE_load_EE_Session', '__return_false' );
	}

	/**
	 * @return void
	 */
	public function remove_fix() {
		remove_filter( 'FHEE_load_EE_Session', '__return_false' );
	}
}
