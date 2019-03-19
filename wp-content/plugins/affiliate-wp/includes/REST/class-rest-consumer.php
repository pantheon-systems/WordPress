<?php
/**
 * Objects: REST API Consumer
 *
 * @package AffiliateWP\REST
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP\REST;

/**
 * Implements a REST API Consumer object.
 *
 * @since 1.9
 *
 * @see \AffWP\Base_Object
 *
 * @property-read int $ID Alias for `$user_id`.
 */
final class Consumer extends \AffWP\Base_Object {

	/**
	 * API consumer ID.
	 *
	 * @access public
	 * @since  1.9
	 * @var    int
	 */
	public $consumer_id = 0;

	/**
	 * API consumer user ID.
	 *
	 * @access public
	 * @since  1.9
	 * @var    int
	 */
	public $user_id = 0;

	/**
	 * REST ID (ironically unused for Consumers).
	 *
	 * @since 2.2.2
	 * @var   null|string
	 */
	public $rest_id;

	/**
	 * API consumer token.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $token = '';

	/**
	 * API consumer public key.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $public_key = '';

	/**
	 * API consumer secret key.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 */
	public $secret_key = '';

	/**
	 * Consumer status.
	 *
	 * @since 2.2.2
	 * @var   string
	 */
	public $status;

	/**
	 * Date the consumer was created.
	 *
	 * @since 2.2.2
	 * @var   string
	 */
	public $date;

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
	public static $cache_token = 'affwp_consumers';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Base_Object for accessing the consumers DB class methods.
	 *
	 * Note the use of primary and secondary db groups separated with a colon.
	 *
	 * @access public
	 * @since  1.9
	 * @var    string
	 * @static
	 */
	public static $db_group = 'REST:consumers';

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
	public static $object_type = 'consumer';

	/**
	 * Sanitizes a consumer object field.
	 *
	 * @access public
	 * @since  1.9
	 * @static
	 *
	 * @param string $field Object field.
	 * @param mixed  $value Field value.
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize_field( $field, $value ) {
		if ( in_array( $field, array( 'consumer_id', 'user_id', 'ID' ), true ) ) {
			$value = (int) $value;
		}

		if ( in_array( $field, array( 'token', 'public_key', 'secrete_key' ), true ) ) {
			$value = sanitize_text_field( $value );
		}

		return $value;
	}

	/**
	 * Overrides the parent date() and date_i18n() methods to ensure no errors are thrown
	 * when accessing a non-existent date property.
	 *
	 * @since 2.1.9
	 *
	 * @param string $name      Method name.
	 * @param array  $arguments Method arguments.
	 * @return string Empty string.
	 */
	public function __call( $name, $arguments ) {
		if ( in_array( $name, array( 'date', 'date_i18n' ) ) ) {
			return '';
		}
	}

}
