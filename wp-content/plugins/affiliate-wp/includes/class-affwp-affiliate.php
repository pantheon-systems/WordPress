<?php
/**
 * Objects: Affiliate
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements an affiliate object.
 *
 * @since 1,9
 *
 * @see AffWP\Base_Object
 * @see affwp_get_affiliate()
 *
 * @property-read int      $ID   Alias for `$affiliate_id`.
 * @property      stdClass $user User object.
 * @property      array    $meta Meta array.
 * @property-read string   $date Alias for `$date_registered`.
 */
final class Affiliate extends Base_Object {

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Affiliate user ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Affiliate rate.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 *
	 * @see Affiliate::rate()
	 */
	public $rate;

	/**
	 * Affiliate rate type.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 *
	 * @see Affiliate::rate_type()
	 */
	public $rate_type;

	/**
	 * Affiliate payment email.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $payment_email;

	/**
	 * Affiliate status.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Affiliate earnings.
	 *
	 * @since 1.9
	 * @access public
	 * @var float
	 */
	public $earnings;

	/**
	 * Affiliate unpaid earnings.
	 *
	 * @access public
	 * @since  2.0
	 * @var    float
	 */
	public $unpaid_earnings;

	/**
	 * Affiliate referrals
	 *
	 * @since 1.8\9
	 * @access public
	 * @var int
	 */
	public $referrals;

	/**
	 * Affiliate referral visits.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visits;

	/**
	 * Affiliate registration date.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date_registered;

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
	public static $cache_token = 'affwp_affiliates';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Base_Object for accessing the affiliates DB class methods.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public static $db_group = 'affiliates';

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
	public static $object_type = 'affiliate';

	/**
	 * Retrieves the values of the given key.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param string $key Key to retrieve the value for.
	 * @return mixed|\WP_User Value.
	 */
	public function __get( $key ) {
		if ( 'user' === $key ) {
			return $this->get_user();
		}

		if ( 'date' === $key ) {
			return $this->date_registered;
		}

		return parent::__get( $key );
	}

	/**
	 * Builds the lazy-loaded user object with first and last name fields.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return false|\WP_User Built user object or false if it doesn't exist.
	 */
	public function get_user() {
		$user = get_user_by( 'id', $this->user_id );

		if ( $user ) {
			foreach ( array( 'first_name', 'last_name' ) as $field ) {
				$user->data->{$field} = get_user_meta( $this->user_id, $field, true );
			}
			// Exclude user pass, activation key, and email from the response.
			unset( $user->data->user_pass, $user->data->user_activation_key, $user->data->user_email );
			return $user->data;
		}
		return $user;
	}

	/**
	 * Retrieves the affiliate meta.
	 *
	 * @access public
	 * @since  1.9.5
	 *
	 * @param string $meta_key Optional. The meta key to retrieve a value for. Default empty.
	 * @param bool   $single   Optional. Whether to return a single value. Default false.
	 * @return mixed Meta value or false if `$meta_key` specified, array of meta otherwise.
	 */
	public function get_meta( $meta_key = '', $single = false ) {
		return affiliate_wp()->affiliate_meta->get_meta( $this->ID, $meta_key, $single );
	}

	/**
	 * Sanitizes an affiliate object field.
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
		if ( in_array( $field, array( 'affiliate_id', 'user_id', 'referrals', 'visits', 'ID' ) ) ) {
			$value = (int) $value;
		}

		if ( in_array( $field, array( 'earnings', 'unpaid_earnings' ) ) ) {
			$value = floatval( $value );
		}

		return $value;
	}

	/**
	 * Retrieves the affiliate rate type.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return string Rate type. If empty, defaults to the global referral rate type.
	 */
	public function rate_type() {
		if ( empty( $this->rate_type ) ) {
			return affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );
		}

		return $this->rate_type;
	}

	/**
	 * Retrieves the affiliate rate.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return int Rate. If empty, defaults to the global referral rate.
	 */
	public function rate() {
		return affwp_get_affiliate_rate( $this->ID );
	}

	/**
	 * Retrieves the payment email.
	 *
	 * If not set or invalid, the affiliate's account email is used instead.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return string Payment email.
	 */
	public function payment_email() {
		if ( empty( $this->payment_email ) || ! is_email( $this->payment_email ) ) {
			$email = affwp_get_affiliate_email( $this->ID );
		} else {
			$email = $this->payment_email;
		}

		return $email;
	}

	/**
	 * Determines if the current affiliate has a custom rate value.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return bool True if the affiliate has a custom rate, otherwise false.
	 */
	public function has_custom_rate() {
		return empty( $this->rate ) ? false : true;
	}
}
