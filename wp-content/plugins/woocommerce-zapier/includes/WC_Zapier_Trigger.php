<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Represents a supported Zapier trigger.
 *
 * A trigger is an "event" that can be acted on in Zapier.
 *
 * Class WC_Zapier_Trigger
 */
abstract class WC_Zapier_Trigger {

	/**
	 * Trigger name/key.
	 * Used internally, so should only contain alphanumeric characters and underscores.
	 *
	 * @var string
	 */
	protected $trigger_key = '';

	/**
	 * User-friendly title that describes the trigger.
	 * Used in the WordPress dashboard.
	 *
	 * @var string
	 */
	protected $trigger_title = '';

	/**
	 * User-friendly description that describes the trigger.
	 * Used in the WordPress dashboard.
	 * No longer used in v1.7 and above.
	 *
	 * @var string
	 */
	protected $trigger_description = '';

	/**
	 * List of WooCommerce hooks/actions that this trigger should fire on.
	 * @var array
	 */
	protected $actions = array();

	/**
	 * Stores the list of Zapier feeds that apply to this trigger.
	 *
	 * @var WC_Zapier_Feed[] Array of WC_Zapier_Feed objects
	 */
	protected $feeds;

	/**
	 * Stores the raw data that is send to Zapier when this feed executes.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Whether or not we are sending sample data or real live data.
	 *
	 * @var bool
	 */
	protected $is_sample = false;

	/**
	 * The numeric sort order for this Trigger.
	 * Should be a unique integer greater than zero.
	 *
	 * This sort order is used when listing the Triggers on the Add/Edit Zapier Feed dashboard screen.
	 *
	 * @var int
	 */
	public $sort_order;

	/**
	 * Whether or not data should be sent to Zapier asynchronously (using WP Cron)
	 * @var bool
	 */
	protected $send_asynchronously = false;

	/**
	 * Constructor.
	 *
	 * Important: any child classes *must* call this constructor!
	 *
	 * @throws Exception
	 */
	public function __construct() {

		if ( is_null( $this->sort_order ) )
			throw new Exception( 'WC_Zapier_Trigger::sort_order must be specified' );

		foreach ( $this->actions as $action_name => $action_num_args ) {
			// When the specified action occurs, schedule it to be acted on during the next page load. Intercepted by the __call() function below.
			add_action( $action_name, array( $this, $action_name ), 10, $action_num_args );

			// The action name that is executed on the next page load. Intercepted by the __call() function below.
			// The number of accepted arguments is increased by 1 to allow for the retrying of failed attempts to contact the Zapier Webhook URL.
			add_action( "zapier_triggered_{$action_name}", array( $this, "zapier_triggered_{$action_name}" ), 10, $action_num_args + 1 );
		}
	}

	/**
	 * Executed when WooCommerce executes one of the defined hooks/actions.
	 *
	 * Should gather the necessary data, in preparation for being sent to Zapier.
	 *
	 * Important: the WC_Zapier_Trigger->is_sample() method should be used to determine whether to use real or sample data.
	 *
	 * @param array  $args        The arguments to the hook/action.
	 * @param string $action_name The name of the WordPress action/hook
	 *
	 * @return array|false Array of data that is to be sent to Zapier, or false on failure
	 */
	public abstract function assemble_data( $args, $action_name );

	/**
	 * Obtain the key/slug of this trigger.
	 *
	 * @return string
	 */
	public function get_trigger_key() {
		return $this->trigger_key;
	}

	/**
	 * Obtain the title of this trigger.
	 *
	 * @return string
	 */
	public function get_trigger_title() {
		return $this->trigger_title;
	}

	/**
	 * Obtain the description of this trigger.
	 * @return string
	 */
	public function get_trigger_description() {
		return $this->trigger_description;
	}

	/**
	 * Obtain the Zapier Feeds that are configured for the specified trigger.
	 * The oldest feeds are first.
	 *
	 * Note: multiple calls to this function will simply return the cached result.
	 *
	 * @return WC_Zapier_Feed[] Array of WC_Zapier_Feed objects
	 */
	protected function get_feeds_for_this_trigger() {
		if ( is_null( $this->feeds ) ) {
			$this->feeds = WC_Zapier_Feed_Factory::get_feeds_for_trigger( $this );
		}
		return $this->feeds;
	}

	/**
	 * Clear the cached list of feeds that use this trigger.
	 */
	public function clear_feed_cache() {
		$this->feeds = null;
	}

	/**
	 * Send the data to Zapier if there are one or more feeds with this trigger.
	 *
	 * @param string $action_name
	 * @param array $arguments
	 *
	 * @return bool
	 */
	public function do_send( $action_name, $arguments ) {

		if ( ! $this->has_feeds() ) {
			return false;
		}

		WC_Zapier()->log( 'Assembling data...' );
		$this->data = $this->assemble_data( $arguments, $action_name );

		// Only send the data to Zapier if the assembled data wasn't false.
		if ( false !== $this->data ) {
			WC_Zapier()->log( 'Sending to Zapier:' );
			WC_Zapier()->log( "  action_name: $action_name" );
			WC_Zapier()->log( "  arguments: " . print_r( $arguments, true ) );
			$result = $this->send_to_zapier( $action_name, $arguments );
			WC_Zapier()->log( "  result: $result" );
		} else {
			WC_Zapier()->log( "Assembled data was false. Aborting.\n\n" );
		}
	}

	/**
	 * Whether or not this Trigger has any active Zapier Feeds.
	 *
	 * @return bool
	 */
	public function has_feeds() {
		// Make sure there is at least one active feed configured for this trigger, otherwise there is no point continuing.
		$feeds = $this->get_feeds_for_this_trigger();

		WC_Zapier()->log( count( $feeds ) . " active feed(s) found for trigger '$this->trigger_title'." );

		if ( empty( $feeds ) ) {
			WC_Zapier()->log( "No feeds. Aborting.\n\n" );
			return false;
		}
		return true;
	}

	/**
	 * Sends the assembled data to Zapier.
	 *
	 * If an error occurs, the request is retried using a truncated exponential backoff strategy: http://en.wikipedia.org/wiki/Exponential_backoff
	 *
	 * For sample data, there is no retry mechanism for failed requests.
	 *
	 * @param string $action_name Hook/action name (needed to be able to retry failed attempts)
	 * @param array  $arguments   Hook/action arguments (needed to be able to retry failed attempts)
	 *
	 * @return bool|string true on success, error message (string) on failure
	 */
	protected function send_to_zapier( $action_name, $arguments ) {

		if ( is_null( $this->data ) || ! $this->data )
			return;

		$data = $this->data;

		/**
		 * Override the WooCommerce data that is about to be sent to Zapier.
		 *
		 * Occurs just before the data is converted to JSON.
		 *
		 * Applies to all Zapier Triggers.
		 *
		 * Important: WC_Zapier_Trigger->is_sample() should be used to distinguish between sample and real data being sent to Zapier.
		 *
		 * @since 1.1.0
		 *
		 * @param array             $data The WooCommerce data about to be sent to Zapier.
		 * @param WC_Zapier_Trigger $this The Trigger instance that is causing the data to be sent to Zapier.
		 * @param string            $action_name Hook/action name that initiated this data send to Zapier.
		 * @param array             $arguments Arguments for the hook/action that initiated this data send to Zapier.
		 */
		$data = apply_filters( 'wc_zapier_data', $data, $this, $action_name, $arguments );

		/**
		 * Override the WooCommerce data that is about to be sent to Zapier.
		 *
		 * Occurs just before the data is converted to JSON.
		 *
		 * The dynamic portion of the filter name, $this->trigger_key,
		 * refers to the unique key of the trigger being executed.
		 *
		 * This filter allows you to override data for a specific Zapier trigger.
		 *
		 * Important: WC_Zapier_Trigger->is_sample() should be used to distinguish between sample and real data being sent to Zapier.
		 *
		 * @since 1.1.0
		 *
		 * @param array             $data The WooCommerce data about to be sent to Zapier.
		 * @param WC_Zapier_Trigger $this The Trigger instance that is causing the data to be sent to Zapier.
		 * @param string            $action_name Hook/action name that initiated this data send to Zapier.
		 * @param array             $arguments Arguments for the hook/action that initiated this data send to Zapier.
		 */
		$data = apply_filters( "wc_zapier_data_{$this->trigger_key}", $data, $this, $action_name, $arguments );

		$json_data = json_encode( $data );

		/**
		 * Override the JSON data that is about to be sent to Zapier.
		 *
		 * Applies to all Zapier Triggers.
		 *
		 * Important: $this->is_sample() should be used to distinguish between sample and real data being sent to Zapier.
		 *
		 * @since 1.1.0
		 *
		 * @param string            $json_data The JSON-encoded data about to be sent to Zapier.
		 * @param WC_Zapier_Trigger $this The Trigger instance that is causing the data to be sent to Zapier.
		 * @param string            $action_name Hook/action name that initiated this data send to Zapier.
		 * @param array             $arguments Arguments for the hook/action that initiated this data send to Zapier.
		 */
		$json_data = apply_filters( 'wc_zapier_data_json', $json_data, $this, $action_name, $arguments );

		/**
		 * Override the JSON data that is about to be sent to Zapier.
		 *
		 * The dynamic portion of the filter name, $this->trigger_key,
		 * refers to the unique key of the trigger being executed.
		 *
		 * This filter allows you to override data for a specific Zapier trigger.
		 *
		 * Important: $this->is_sample() should be used to distinguish between sample and real data being sent to Zapier.
		 *
		 * @since 1.1.0
		 *
		 * @param string            $json_data The JSON-encoded data about to be sent to Zapier.
		 * @param WC_Zapier_Trigger $this The Trigger instance that is causing the data to be sent to Zapier.
		 * @param string            $action_name Hook/action name that initiated this data send to Zapier.
		 * @param array             $arguments Arguments for the hook/action that initiated this data send to Zapier.
		 */
		$json_data = apply_filters( "wc_zapier_data_json_{$this->trigger_key}", $json_data, $this, $action_name, $arguments );

		foreach ( $this->get_feeds_for_this_trigger() as $feed ) {

			$args = array(
				'sslverify' => false,
				'ssl'       => true,
				'timeout'   => 10, // Use a 10 second timeout instead of 5 seconds
				'body'      => $json_data,
				'headers'   => array(
					'Content-Type' => 'application/json', // We're sending JSON data to Zapier
				),
			);

			$webhook_url = $feed->webhook_url();

			if ( $this->is_sample() ) {
				// Testing the webhook - we're not sending real data
				// Send X-Hook-Test header as per https://zapier.com/developer/reference/#static-webhooks and https://zapier.com/support/questions/1125/validating-urls/
				// This will never trigger an action for real - Zapier will just cache the payload in their UI
				$args['headers']['X-Hook-Test'] = 'true';
			}

			$num_attempts = 0;

			if ( ! $this->is_sample() ) {
				// For real data, keep track of the number of retries
				if ( $this->actions[ $action_name ] == count( $arguments ) ) {
					// No num_retries parameter specified
					$num_attempts = 0;
				} else {
					// We've retried at least once
					$num_attempts = array_pop( $arguments );
				}
				$num_attempts++;
			}

			$sampletext = $this->is_sample() ? 'Sample ' : '';

			WC_Zapier()->log( " Attempting to send {$sampletext}data to Zapier Feed:");
			WC_Zapier()->log( "  - Feed ID: " . $feed->id() );
			WC_Zapier()->log( "  - Feed Title: " . $feed->title() );
			WC_Zapier()->log( "  - Trigger: " . $this->get_trigger_title() );
			WC_Zapier()->log( "  - Webhook URL: $webhook_url" );
			WC_Zapier()->log( "" );
			WC_Zapier()->log( "{$sampletext}JSON Data: " . substr( $json_data, 0, 15 ) . " ..." ); // Logs the id but not much else

			$result = wp_remote_post( $webhook_url, $args );

			// Was there was an error communicating with the Zapier webhook?
			if ( 200 != wp_remote_retrieve_response_code( $result ) ) {

				$error_message = 'Unknown error';
				if ( is_wp_error($result) ) {
					$error_message = $result->get_error_messages();
					$error_message = implode( ', ', $error_message );
				} else {
					// Non HTTP 200 response
					$error_message = sprintf( __( 'HTTP %1$d (%2$s)' , 'wc_zapier' ), wp_remote_retrieve_response_code( $result ), esc_html( wp_remote_retrieve_response_message($result) ) );
				}

				WC_Zapier()->log( "Error: $error_message" );

				if ( $this->is_sample() ) {
					// When sending sample data, don't automatically retry
					return $error_message;
				}

				$arguments['num_attempts'] = $num_attempts;

				// Retry the request at a later date.
				// Use a Truncated exponential backoff strategy: http://en.wikipedia.org/wiki/Exponential_backoff
				// (with a maximum retry retry time of 1 hour)
				$retry_seconds =  min ( (int) pow( 2, $arguments['num_attempts'] ), 3600 );

				WC_Zapier()->log( "Attempt #$num_attempts failed. Scheduling retry to occur in $retry_seconds second(s) from now." );

				$result = $this->schedule_event( $action_name, $arguments, $retry_seconds );
				if ( false === $result ) {
					WC_Zapier()->log( "Warning: WC_Zapier_Trigger::schedule_event() returned false when rescheduling." );
				}
				return $error_message;
			}
			// The request was a success
			WC_Zapier()->log( "Success - HTTP 200 response code." );
			WC_Zapier()->log( "Zapier Response: " . wp_remote_retrieve_body( $result ) );

			if ( ! $this->is_sample() ) {
				// Only do this for real data
				$this->data_sent_to_feed( $feed, $result, $action_name, $arguments, $num_attempts );
			}

			/**
			 * After data is sent to Zapier successfully.
			 *
			 * Important: $this->is_sample() should be used to distinguish between sample and real data being sent to Zapier.
			 *
			 * @since 1.6.2
			 *
			 * @param string            $json_data The JSON-encoded data that was sent to Zapier.
			 * @param WC_Zapier_Trigger $this The Trigger instance that caused the data to be sent to Zapier.
			 * @param string            $action_name Hook/action name that initiated this data send to Zapier.
			 * @param array             $arguments Arguments for the hook/action that initiated this data send to Zapier.
			 */
			do_action( 'wc_zapier_data_sent_to_zapier_successfully', $json_data, $this, $action_name, $arguments );

		}
		return true;
	}

	/**
	 * Executed every time data is sent to a Zapier feed.
	 *
	 * Not executed when sample data is sent.
	 *
	 * @param WC_Zapier_Feed $feed         Feed data
	 * @param array          $result       Response from the wp_remote_post() call
	 * @param string         $action_name  Hook/action name (needed to be able to retry failed attempts)
	 * @param array          $arguments    Hook/action arguments (needed to be able to retry failed attempts)
	 * @param int            $num_attempts The number of attempts it took to successfully send the data to Zapier.
	 */
	protected function data_sent_to_feed( WC_Zapier_Feed $feed, $result, $action_name, $arguments, $num_attempts = 0 ) {

	}

	/**
	 * Executed whenever one of the trigger's supported hooks/actions is called.
	 *
	 * Called initially when a supported hook/filter is called, in which case we
	 * schedule the data to be sent to Zapier on the next page load.
	 *
	 * Called again on the next page load (when WordPress cron is executing), in which case
	 * the data is assembled and send to Zapier.
	 *
	 * @param string $action_name hook/action name
	 * @param array  $arguments   hook/action arguments
	 *
	 * @throws Exception
	 */
	public function __call( $action_name, array $arguments ) {

		$pos = $this->send_asynchronously() ? strpos( $action_name, 'zapier_triggered_' ) : 0;

		WC_Zapier()->log( "WC_Zapier_Trigger::__call() : $this->trigger_title" );
		WC_Zapier()->log( "  action_name: $action_name " );
		WC_Zapier()->log( "  arguments: " . print_r( $arguments, true ) );

		if ( false === $pos ) {

			// One of the specified actions has just been triggered
			// Make sure it is an action/hook that we support and expect
			if ( ! array_key_exists( $action_name, $this->actions ) )
				return;

			if ( ! $this->has_feeds() ) {
				return;
			}

			// Rather than acting on this immediately, schedule an event to run on the next page load.
			// This also makes the Zapier API calls (somewhat) asynchronous, and allows us to retry if it fails.
			$result = $this->schedule_event( $action_name, $arguments );

			WC_Zapier()->log( "Scheduled the asynchronous event to occur via wp-cron: " );
			WC_Zapier()->log( "  result: $result" );
			WC_Zapier()->log( "  action_name: $action_name" );
			WC_Zapier()->log( "  arguments: " . print_r( $arguments, true ) );
			if ( false === $result ) {
				WC_Zapier()->log( "Warning: WC_Zapier_Trigger::schedule_event() returned false when scheduling." );
			}

		} else if ( 0 === $pos ) {

			if ( defined( 'DOING_CRON' ) || function_exists( '_get_cron_lock' ) ) {
				WC_Zapier()->log( "This is a cron request" );
			} else {
				WC_Zapier()->log( "This isn't a cron request" );
			}

			if ( ! $this->send_asynchronously() ) {
				// Sending synchronously (immediately)
				WC_Zapier()->log( "Adding the task to the queue..." );
				$result = WC_Zapier::$queue->add_to_queue( $this, $action_name, $arguments );
				return true;
			}

			if ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
				WC_Zapier()->log( "  url = $_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" );
			}

			$action_name_to_trigger = str_replace( 'zapier_triggered_', '', $action_name );

			WC_Zapier()->log( "  action_name_to_trigger: $action_name_to_trigger" );
			WC_Zapier()->log( "  arguments: " . print_r( $arguments, true ) );

			$this->do_send( $action_name_to_trigger, $arguments );

		} else {
			WC_Zapier()->log( "Action_name doesn't seem to be supported. action_name = $action_name" );
			// Silently ignore this request
		}
	}

	/**
	 * Send some sample data to the specified Zapier Feed.
	 *
	 * This is done so that Zapier knows what data field to expect.
	 *
	 * @param WC_Zapier_Feed $feed
	 * @return bool|string true on success, or an error message (string) on failure.
	 */
	public function send_sample_data_payload( WC_Zapier_Feed $feed ) {

		$this->is_sample = true;
		$this->feeds = array( $feed );

		$this->data = $this->assemble_data( array(), 'test' );
		$result = $this->send_to_zapier( 'test', array() );

		$this->is_sample = false;
		$this->feeds = null;

		return $result;
	}

	/**
	 * Schedules a single event to be executed on the next page load via WordPress' cron system.
	 *
	 * @param string $action_name
	 * @param array  $arguments
	 * @param int    $number_of_seconds_from_now
	 *
	 * @return bool
	 */
	protected function schedule_event( $action_name, $arguments, $number_of_seconds_from_now = 5 ) {
		$result = wp_schedule_single_event( time() + $number_of_seconds_from_now, "zapier_triggered_{$action_name}", $arguments );
		if ( is_null( $result ) ) {
			// wp_schedule_single_event() returns null on success, so convert it to true
			$result = true;
		} else {
			//  wp_schedule_single_event() returned false
		}
		/**
		 * After data is scheduled to be sent to Zapier (asynchronously via WP-cron).
		 *
		 * @since 1.6.3
		 *
		 * @param string            $action_name Hook/action name that initiated this data send to Zapier.
		 * @param array             $arguments Arguments for the hook/action that initiated this data send to Zapier.
		 * @param WC_Zapier_Trigger $this The Trigger instance that caused the data to be sent to Zapier.
		 * @param bool $result      The result of the wp_schedule_single_event() call (added in WC Zapier v1.6.10).
		 */
		do_action( 'wc_zapier_scheduled_event', $action_name, $arguments, $this, $result );
		return $result;
	}

	/**
	 * Whether or not we are sending sample data to Zapier.
	 *
	 * @return bool true if sending sample data, false if sending real live data.
	 */
	public function is_sample() {
		return $this->is_sample;
	}

	/**
	 * Given a stdClass object, empty out the values for all properties.
	 *
	 * @param stdClass $object
	 *
	 * @return stdClass
	 */
	protected function create_empty_object_recursive( stdClass $object ) {
		foreach ( $object as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $array_key => $array_item ) {
					$object->{$key}[$array_key] = $this->create_empty_object_recursive( $array_item );
				}
			} else if ( is_string( $value ) ) {
				$object->{$key} = '';
			} else if ( is_integer( $value ) ) {
				$object->{$key} = '';
			} else if ( is_bool( $value ) ) {
				$object->{$key} = '';
			}
		}
		return $object;
	}

	/**
	 * Send sample data to all active feeds that use this trigger event.
	 */
	public function send_sample_data_to_active_feeds_using_this_trigger() {
		foreach ( WC_Zapier_Feed_Factory::get_feeds_for_trigger( $this ) as $feed ) {
			$feed->trigger()->send_sample_data_payload( $feed );
		}
	}

	/**
	 * Whether or not the trigger event should send data to Zapier.
	 *
	 * Defaults to true, but allows specific triggers (sub-classes) to optionally prevent the data send from being scheduled.
	 *
	 * A trigger can override this function when/if required.
	 *
	 * @param string $action_name hook/action name
	 * @param array  $args        hook/action arguments
	 *
	 * @return bool True if the event should be scheduled, false if not
	 */
	protected function should_schedule_event( $action_name, $args ) {
		return true;
	}

	/**
	 * Whether or not to send data to Zapier asynchronously (using WP-Cron).
	 *
	 * @return bool
	 */
	public function send_asynchronously() {
		/**
		 * Override whether or not to send data to Zapier asynchronously (using WP-Cron).
		 *
		 * @since 1.6.0
		 *
		 * @param bool              $send_asynchronously true to send via WP-cron, false to send immediately.
		 * @param WC_Zapier_Trigger $this The Trigger instance that is causing the data to be sent to Zapier.
		 */
		$async = apply_filters( 'wc_zapier_send_asynchronously', $this->send_asynchronously, $this );
		if ( $async ) {
			_deprecated_hook( 'wc_zapier_send_asynchronously', '1.7', null, __( 'Asynchronous sending is no longer supported and will be removed in a future version', 'wc_zapier' ) );
		}
		return $async;
	}

}