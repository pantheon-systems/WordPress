<?php
/**
 * Objects: Creative
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements a creative object.
 *
 * @since 1,9
 *
 * @see AffWP\Base_Object
 * @see affwp_get_creative()
 *
 * @property-read int $ID Alias for `$creative_id`
 */
final class Creative extends Base_Object {

	/**
	 * Creative ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $creative_id = 0;

	/**
	 * Name of the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * Description for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * URL for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $url;

	/**
	 * Text for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $text;

	/**
	 * Image URL for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $image;

	/**
	 * Status for the creative.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Creation date for the creative.
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
	public static $cache_token = 'affwp_creatives';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Base_Object for accessing the creatives DB class methods.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public static $db_group = 'creatives';

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
	public static $object_type = 'creative';

	/**
	 * Sanitizes a creative object field.
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
		if ( in_array( $field, array( 'creative_id', 'ID' ) ) ) {
			$value = (int) $value;
		}
		return $value;
	}

}
