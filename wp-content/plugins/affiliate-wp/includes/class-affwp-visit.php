<?php
/**
 * Objects: Visit
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements a visit object.
 *
 * @since 1,9
 *
 * @see AffWP\Base_Object
 * @see affwp_get_visit()
 * 
 * @property-read int $ID Alias for `$visit_id`
 */
final class Visit extends Base_Object {

	/**
	 * Visit ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visit_id = 0;

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Referral ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $referral_id = 0;

	/**
	 * REST ID (site:visit ID combination).
	 *
	 * @since 2.2.2
	 * @var   string
	 */
	public $rest_id = '';

	/**
	 * Visit URL.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $url;

	/**
	 * Visit referrer.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $referrer;

	/**
	 * Referral campaign name.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $campaign;

	/**
	 * Visit context.
	 *
	 * @since  2.0.2
	 * @access public
	 * @var    string
	 */
	public $context;

	/**
	 * Visit IP address (IPv4).
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $ip;

	/**
	 * Date the visit was created.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 *
	 * @see AffWP\Base_Object::get_cache_key()
	 */
	public static $cache_token = 'affwp_visits';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Base_Object for accessing the visits DB class methods.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public static $db_group = 'visits';

	/**
	 * Object type.
	 *
	 * Used as the cache group and for accessing object DB classes in the parent.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $object_type = 'visit';

	/**
	 * Sanitizes a visit object field.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param string $field        Object field.
	 * @param mixed  $value        Field value.
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize_field( $field, $value ) {
		if ( in_array( $field, array( 'visit_id', 'affiliate_id', 'referral_id', 'ID' ) ) ) {
			$value = (int) $value;
		}

		if ( in_array( $field, array( 'rest_id' ) ) ) {
			$value = sanitize_text_field( $value );
		}

		return $value;
	}

}
