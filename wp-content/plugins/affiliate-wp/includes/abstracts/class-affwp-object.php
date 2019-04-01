<?php
/**
 * Objects: Base Object
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 1.9
 */

namespace AffWP;

/**
 * Implements a base object to be extended by core objects.
 *
 * @since 1.9
 * @abstract
 */
abstract class Base_Object {

	/**
	 * Whether the object members have been filled.
	 *
	 * @access protected
	 * @since  1.9
	 * @var    bool|null
	 */
	protected $filled = null;

	/**
	 * Retrieves the object instance.
	 *
	 * @access public
	 * @since  1.9
	 * @static
	 *
	 * @param int $object Object ID.
	 * @return object|false Object instance or false.
	 */
	public static function get_instance( $object_id ) {
		if ( ! (int) $object_id ) {
			return false;
		}

		$Sub_Class   = get_called_class();
		$cache_key   = self::get_cache_key( $object_id );
		$cache_group = static::$object_type;

		$_object = wp_cache_get( $cache_key, $cache_group );

		if ( false === $_object ) {
			$db_groups = self::get_db_groups();

			if ( isset( $db_groups->secondary ) ) {
				$_object = affiliate_wp()->{$db_groups->primary}->{$db_groups->secondary}->get( $object_id );
			} else {
				$_object = affiliate_wp()->{$db_groups->primary}->get( $object_id );
			}

			if ( ! $_object ) {
				return false;
			}

			$_object = self::fill_vars( $_object );

			wp_cache_add( $cache_key, $_object, $cache_group );
		} elseif ( empty( $_object->filled ) ) {
			$_object = self::fill_vars( $_object );
		}
		return new $Sub_Class( $_object );
	}

	/**
	 * Retrieves the built cache key for the given single object.
	 *
	 * @access public
	 * @since  1.9
	 * @static
	 *
	 * @see Object::get_instance()
	 * @see affwp_clean_item_cache()
	 *
	 * @param int $object_id Object ID.
	 * @return string Cache key for the object type and ID.
	 */
	public static function get_cache_key( $object_id ) {
		return md5( static::$cache_token . ':' . $object_id );
	}

	/**
	 * Object constructor.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param mixed $object Object to populate members for.
	 */
	public function __construct( $object ) {
		foreach ( get_object_vars( $object ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Retrieves the value of a given property.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param string $key Property to retrieve a value for.
	 * @return mixed Otherwise, the value of the property if set.
	 */
	public function __get( $key ) {
		if ( 'ID' === $key ) {
			$db_groups = self::get_db_groups();

			if ( isset( $db_groups->secondary ) ) {
				$primary_key = affiliate_wp()->{$db_groups->primary}->{$db_groups->secondary}->primary_key;
			} else {
				$primary_key = affiliate_wp()->{$db_groups->primary}->primary_key;
			}

			return $this->{$primary_key};
		}

		if ( isset( $this->{$key} ) ) {
			return $this->{$key};
		}
	}

	/**
	 * Sets a property.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @see set()
	 *
	 * @param string $key   Property name.
	 * @param mixed  $value Property value.
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Sets an object property value and optionally save.
	 *
	 * @internal Note: Checking isset() on $this->{$key} is missing here because
	 *           this method is also used directly by __set() which is leveraged for
	 *           magic properties.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param string $key   Property name.
	 * @param mixed  $value Property value.
	 * @param bool   $save  Optional. Whether to save the new value in the database.
	 * @return int|false True if the value was set. If `$save` is true, true if the save was successful.
	 *                   False if `$save` is true and the save was unsuccessful. false otherwise.
	 */
	public function set( $key, $value, $save = false ) {
		$this->$key = static::sanitize_field( $key, $value );

		if ( true === $save ) {
			// Only real properties can be saved.
			$keys = array_keys( get_class_vars( get_called_class() ) );

			if ( ! in_array( $key, $keys ) ) {
				return false;
			}

			return $this->save();
		}

		return true;
	}

	/**
	 * Saves an object with current property values.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @return bool True on success, false on failure.
	 */
	public function save() {
		$object_type = static::$object_type;

		switch ( $object_type ) {
			case 'referral':
				$updated = affiliate_wp()->referrals->update_referral( $this->ID, $this->to_array() );
				break;

			case 'visit':
				$updated = affiliate_wp()->visits->update_visit( $this->ID, $this->to_array() );
				break;

			default:
				// Affiliates and Creatives have update() methods.
				$db_groups = self::get_db_groups();

				// Handle secondary groups.
				if ( isset( $db_groups->secondary ) ) {
					$updated = affiliate_wp()->{$db_groups->primary}->{$db_groups->secondary}->update( $this->ID, $this->to_array(), '', $object_type );
				} else {
					$updated = affiliate_wp()->{$db_groups->primary}->update( $this->ID, $this->to_array(), '', $object_type );
				}
				break;
		}

		if ( $updated ) {
			return true;
		}

		return false;
	}

	/**
	 * Splits the db groups if there is more than one.
	 *
	 * CURIE is ':'.
	 *
	 * @access public
	 * @since  1.9
	 * @static
	 *
	 * @return object Object containing the primary and secondary group values.
	 */
	public static function get_db_groups() {
		$groups = array(
			'primary' => static::$db_group
		);

		if ( false !== strpos( static::$db_group, ':' ) ) {
			$split = explode( ':', static::$db_group, 2 );

			if ( 2 == count( $split) ) {
				$groups['primary']   = $split[0];
				$groups['secondary'] = $split[1];
			}
		}

		return (object) $groups;
	}

	/**
	 * Converts the given object to an array.
	 *
	 * @access public
	 * @since  1.9
	 *
	 * @param mixed $object Object.
	 * @return array Array version of the given object.
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	/**
	 * Fills object members.
	 *
	 * @access public
	 * @since  1.9
	 * @since  1.9.5 Added the `$defaults` parameter.
	 * @static
	 *
	 * @param object|array $object_data Object or array of object data.
	 * @param array        $extra_vars  Optional. Additional vars to ensure get populated.
	 *                                  Default empty array.
	 * @return object|array Object or data array with filled members.
	 */
	public static function fill_vars( $object_data, $extra_vars = array() ) {
		if ( is_object( $object_data ) ) {
			if ( isset( $object_data->filled ) && empty( $extra_vars ) ) {
				return $object_data;
			}

			$vars   = get_object_vars( $object_data );
			$fields = empty( $extra_vars ) ? $vars : wp_parse_args( $extra_vars, $vars );

			foreach ( $fields as $field => $value ) {
				$object_data->$field = static::sanitize_field( $field, $value );

				$object_data->filled = true;
			}
		} elseif ( is_array( $object_data ) ) {
			if ( isset( $object_data['filled'] ) && empty( $extra_vars ) ) {
				return $object_data;
			}

			$fields = empty( $extra_vars ) ? $object_data : wp_parse_args( $extra_vars, $object_data );

			foreach ( $fields as $field => $value ) {
				$object_data[ $field ] = static::sanitize_field( $field, $value );

				$object_data['filled'] = true;
			}
		}
		return $object_data;
	}

	/**
	 * Sanitizes a given object field's value.
	 *
	 * Sub-class should override this method.
	 *
	 * @access public
	 * @since  1.9
	 * @static
	 *
	 * @param string $field Object field.
	 * @param mixed  $value Object field value.
	 * @return mixed Sanitized value for the given field.
	 */
	public static function sanitize_field( $field, $value ) {
		return $value;
	}

	/**
	 * Retrieves the (maybe formatted) date for the current object if set.
	 *
	 * @since 2.1.9
	 *
	 * @param true|string $date_format Optional. How to format the object date. Accepts 'date', 'time',
	 *                                 or 'datetime' shorthand formats. Also accepts 'object', 'timestamp',
	 *                                 'wp_timestamp', or any other valid date_format() string.
	 *                                 Default empty string.
	 * @return mixed Formatted object date, timestamp, or Date object.
	 *               If `$format` is empty, the un-formatted `$date` value
	 *               will be returned. If `$format` is 'object', a Date object
	 *               will be retrieved for further manipulation.
	 */
	public function date( $format = '' ) {

		if ( empty( $format ) ) {

			$date = $this->date;

		} else {

			$date = affiliate_wp()->utils->date( $this->date )->format( $format );

		}

		return $date;
	}

	/**
	 * Retrieves a localized version of the formatted date for the current object if set.
	 *
	 * @since 2.1.9
	 *
	 * @param true|string $date_format Optional. How to format the object date. Accepts 'date', 'time',
	 *                                 or 'datetime' shorthand formats. Also accepts 'object', 'timestamp',
	 *                                 'wp_timestamp', or any other valid date_format() string.
	 *                                 Default 'date', which represents the value of the 'date_format' option.
	 * @return mixed Localized, formatted object date, timestamp, or Date object.
	 *               If `$format` is empty, the un-formatted `$date` value
	 *               will be returned. If `$format` is 'object', a Date object
	 *               will be retrieved for further manipulation.
	 */
	public function date_i18n( $format = 'date' ) {
		$timestamp = affiliate_wp()->utils->date( $this->date )->getWPTimestamp();

		return affwp_date_i18n( $timestamp, $format );
	}

	/**
	 * Determines if the current object has a rest ID.
	 *
	 * @since 2.2.2
	 *
	 * @return bool True if the rest ID is set, otherwise false.
	 */
	public function has_rest_id() {
		return empty( $this->rest_id ) ? false : true;
	}

}
