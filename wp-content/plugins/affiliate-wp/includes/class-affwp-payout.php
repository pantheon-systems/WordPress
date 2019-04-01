<?php
/**
 * Objects: Payout
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP\Affiliate;

/**
 * Implements a payout object.
 *
 * @since 1,9
 *
 * @see AffWP\Base_Object
 * @see affwp_get_payouts()
 *
 * @property-read int $ID Alias for `$payout_id`.
 */
final class Payout extends \AffWP\Base_Object {

	/**
	 * Payout ID.
	 *
	 * @access public
	 * @since  1.9
	 * @var    int
	 */
	public $payout_id = 0;

	/**
	 * Affiliate ID.
	 *
	 * @access public
	 * @since  1.9
	 * @var    int
	 */
	public $affiliate_id = 0;

	/**
	 * IDs for referrals associated with the payout.
	 *
	 * @access public
	 * @since  1.9
	 * @var    array
	 */
	public $referrals = array();

	/**
	 * Payout amount.
	 *
	 * @access public
	 * @since  1.9
	 * @var    float
	 */
	public $amount;

	/**
	 * Payout method.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $payout_method;

	/**
	 * Payout status.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $status;

	/**
	 * Payout date.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $date;

	/**
	 * ID of the user who generated the payout.
	 *
	 * @access public
	 * @since  1.9.5
	 * @var    int
	 */
	public $owner = 0;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 * @static
	 *
	 * @see AffWP\Base_Object::get_cache_key()
	 */
	public static $cache_token = 'affwp_payouts';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Base_Object for accessing the affiliates DB class methods.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public static $db_group = 'affiliates:payouts';

	/**
	 * Object type.
	 *
	 * Used as the cache group and for accessing object DB classes in the parent.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 * @static
	 */
	public static $object_type = 'payouts';

	/**
	 * Sanitizes an affiliate object field.
	 *
	 * @access public
	 * @since  1.9
	 * @static
	 *
	 * @param string $field        Object field.
	 * @param mixed  $value        Field value.
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize_field( $field, $value ) {
		if ( in_array( $field, array( 'payout_id', 'affiliate_id', 'ID', 'owner' ) ) ) {
			$value = (int) $value;
		}

		if ( 'referrals' === $field ) {
			$value = implode( ',', wp_parse_id_list( $value ) );
		}

		if ( 'amount' === $field ) {
			$value = floatval( $value );
		}

		return $value;
	}

}
