<?php
/**
 * Objects: Referral
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements a referral object.
 *
 * @since 1,9
 *
 * @see AffWP\Base_Object
 * @see affwp_get_referral()
 *
 * @property-read int $ID Alias for `$referral_id`
 */
final class Referral extends Base_Object {

	/**
	 * Referral ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $referral_id = 0;

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Visit ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visit_id = 0;

	/**
	 * REST ID (site:referral ID combination).
	 *
	 * @since 2.2.2
	 * @var   string
	 */
	public $rest_id = '';

	/**
	 * Customer ID.
	 *
	 * @since 2.2
	 * @access public
	 * @var int
	 */
	public $customer_id = 0;

	/**
	 * Parent ID.
	 *
	 * @since 2.2.9
	 * @access public
	 * @var int
	 */
	public $parent_id = 0;

	/**
	 * Referral description.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $description;

	/**
	 * Referral status.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $status;

	/**
	 * Referral amount.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $amount;

	/**
	 * Referral currency.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $currency;

	/**
	 * Custom referral data.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $custom;

	/**
	 * Referral context (usually integration).
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $context;

	/**
	 * Referral campaign name.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $campaign;

	/**
	 * Referral reference.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $reference;

	/**
	 * Products associated with the referral.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $products;

	/**
	 * Date the referral was created.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date;

	/**
	 * Referral type.
	 *
	 * @since 2.2
	 * @access public
	 * @var string
	 */
	public $type;

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
	public static $cache_token = 'affwp_referrals';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Base_Object for accessing the referrals DB class methods.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public static $db_group = 'referrals';

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
	public static $object_type = 'referral';

	/**
	 * Sanitizes a referral object field.
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
		if ( in_array( $field, array( 'referral_id', 'affiliate_id', 'visit_id', 'ID', 'parent_id' ) ) ) {
			$value = (int) $value;
		}

		if ( 'custom' === $field ) {
			$value = affwp_maybe_unserialize( affwp_maybe_unserialize( $value ) );
		}

		if ( in_array( $field, array( 'rest_id' ) ) ) {
			$value = sanitize_text_field( $value );
		}

		return $value;
	}

	/**
	 * Retrieves the referral type name
	 *
	 * @since 2.2
	 * @access public
	 * @static
	 *
	 * @return string Nice name of the referral type.
	 */
	public function type() {

		$value = empty( $this->type ) ? 'sale' : $this->type;
		$type  = affiliate_wp()->referrals->types_registry->get_type( $value );

		if( $type ) {
			$value = $type['label'];
		}

		return $value;
	}

}
