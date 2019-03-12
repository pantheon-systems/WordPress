<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * This class is responsible for instantiating and storing WC_Zapier_Trigger objects.
 *
 * Class WC_Zapier_Trigger_Factory
 */
class WC_Zapier_Trigger_Factory {

	/**
	 * List of supported Zapier Triggers
	 */
	static $triggers = array();

	public static function load_triggers() {
		if ( empty( self::$triggers ) ) {
			// Initialise our triggers

			$directories = array(
					WC_Zapier::$plugin_path . 'includes/triggers',
			);
			/**
			 * Override the directories where WC Zapier Triggers PHP files are loaded from.
			 *
			 * @since 1.6.0
			 *
			 * @param array             $directories The array of directories to scan for PHP files.
			 */
			$directories = apply_filters( 'wc_zapier_trigger_directories', $directories );

			foreach ( $directories as $directory ) {
				foreach ( scandir( $directory ) as $trigger_file ) {
					// Only take into account PHP files (and not directories)
					if ( strpos( $trigger_file, '.php' ) !== false ) {
						$class_name = str_replace( '.php', '', $trigger_file );

						// Don't instantiate/initialise classes that are abstract
						$reflector = new ReflectionClass( $class_name );
						if ( !$reflector->IsInstantiable() )
							continue;

						$trigger                                     = new $class_name();
						self::$triggers[$trigger->get_trigger_key()] = $trigger;
					}
				}
			}
		}
	}

	/**
	 * Obtain the WC_Zapier_Trigger class that corresponds to the specified trigger key
	 *
	 * @param string $trigger_key
	 *
	 * @return WC_Zapier_Trigger
	 * @throws Exception
	 */
	public static function get_trigger_with_key( $trigger_key ) {
		if ( isset( self::$triggers[$trigger_key] ) )
			return self::$triggers[$trigger_key];
		else
			throw new Exception("Trigger not found with key: $trigger_key");
	}

	/**
	 * Whether or not a trigger exists with the specified key.
	 *
	 * @param string $trigger_key
	 *
	 * @return bool
	 */
	public static function trigger_exists( $trigger_key ) {
		try {
			$trigger_key = trim( (string) $trigger_key );
			if ( empty($trigger_key) )
				return false;
			self::get_trigger_with_key( $trigger_key );
			return true;
		} catch ( Exception $ex ) { }
		return false;
	}

	/**
	 * Return an array of supported triggers and title/description.
	 * Sorted in the correct sort order.
	 *
	 * @return array
	 */
	public static function get_triggers_for_display() {
		$triggers = array();
		foreach ( self::get_triggers_sorted() as $trigger ) {
			$triggers[$trigger->get_trigger_key()] = $trigger->get_trigger_title();
		}
		return $triggers;
	}

	/**
	 * Obtain a list of registered Triggers, sorted by the WC_Zapier_Trigger::sort_order property.
	 *
	 * @return WC_Zapier_Trigger[] array of WC_Zapier_Trigger objects
	 */
	public static function get_triggers_sorted() {
		$triggers = array();
		foreach ( self::$triggers as $trigger ) {
			$triggers[ $trigger->sort_order ] = $trigger;
		}
		ksort( $triggers, SORT_NUMERIC );
		return $triggers;
	}

	/**
	 * Obtain a list of trigger keys for all triggers.
	 *
	 * @return array
	 */
	public static function get_trigger_keys() {
		$triggers = array();
		foreach ( self::get_triggers_sorted() as $trigger ) {
			$triggers[] = $trigger->get_trigger_key();
		}
		return $triggers;
	}

	/**
	 * Obtain the name/title of a trigger based on it's internal key/slug.
	 *
	 * @param string $trigger_key
	 * @return string
	 */
	public static function get_trigger_name( $trigger_key ) {
		return isset( self::$triggers[$trigger_key] ) ? self::$triggers[$trigger_key]->get_trigger_title() : '';
	}


}