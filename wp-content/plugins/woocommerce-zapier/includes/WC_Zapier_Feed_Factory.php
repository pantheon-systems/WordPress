<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * This class is responsible for retrieving WC_Zapier_Feed objects.
 *
 * Class WC_Zapier_Feed_Factory
 */
class WC_Zapier_Feed_Factory {

	/**
	 * Obtain the (active and valid) Zapier Feeds that are configured for the specified trigger.
	 * The oldest feeds are first.
	 *
	 * Note: multiple calls to this function will simply return the cached result.
	 *
	 * @param WC_Zapier_Trigger $trigger
	 * @return WC_Zapier_Feed[] Array of WC_Zapier_Feed objects
	 */
	public static function get_feeds_for_trigger( WC_Zapier_Trigger $trigger ) {

		$feeds = array();
		// Strangely, WP_Query doesn't let us search by post_content so we need to do it manually
		$enabled_feeds = self::get_enabled_feeds();
		foreach ( $enabled_feeds as $feed ) {
			if ( get_class( $feed->trigger() ) === get_class( $trigger ) ) {
				$feeds[] = $feed;
			}
		}
		return $feeds;
	}

	/***
	 * Obtain the number of existing Zapier Feeds that have the specified webhook URL and trigger.
	 * This is used to help ensure that two Zapier Feeds can't exist with the same webhook URL and trigger combination.
	 *
	 * @param string $webhook_url        Zapier Webhook URL
	 * @param WC_Zapier_Trigger $trigger Trigger
	 * @param int|WC_Zapier_Feed $feed_to_exclude Optional feed not to include in the search
	 *
	 * @return int
	 */
	public static function get_number_of_feeds_with_webhook_url_and_trigger( $webhook_url, WC_Zapier_Trigger $trigger, $feed_to_exclude = null ) {

		$post_id_to_exclude = is_null( $feed_to_exclude ) ? 0 : $feed_to_exclude->id();

		$query       = array(
			'post_type'    => 'wc_zapier_feed'
		, 'nopaging'     => true
		, 'post_status'  => 'publish'
		, 'post__not_in' => array( $post_id_to_exclude )
		);
		$feeds_query = new WP_Query($query);
		$posts       = $feeds_query->get_posts();

		// Strangely, WP_Query doesn't let us search by post_content or post_excerpt so we need to do it manually
		foreach ( $posts as $index => $post ) {
			$feed = new WC_Zapier_Feed( $post );

			if ( get_class( $feed->trigger() ) != get_class( $trigger ) || $feed->webhook_url() !== $webhook_url ) {
				unset ($posts[$index]);
			}
		}
		wp_reset_postdata();
		return count( $posts );
	}

	/***
	 * Obtain the number of existing Zapier Feeds that have the specified title.
	 * This is used to help ensure that two Zapier Feeds can't exist with the same title.
	 *
	 * @param string             $title           Zapier Feed Title
	 * @param int|WC_Zapier_Feed $feed_to_exclude Optional feed not to include in the search
	 *
	 * @return int
	 */
	public static function get_number_of_feeds_with_title( $title, $feed_to_exclude = null ) {

		$post_id_to_exclude = is_null( $feed_to_exclude ) ? 0 : $feed_to_exclude->id();

		$query       = array(
			'post_type'    => 'wc_zapier_feed'
		, 'nopaging'     => true
		, 'post_status'  => 'publish'
		, 'post__not_in' => array( $post_id_to_exclude )
		);
		$feeds_query = new WP_Query($query);
		$posts       = $feeds_query->get_posts();

		// Strangely, WP_Query doesn't let us search by post_title so we need to do it manually
		foreach ( $posts as $index => $post ) {
			$feed = new WC_Zapier_Feed( $post );
			if ( $feed->title() !== $title ) {
				unset ($posts[$index]);
			}
		}
		wp_reset_postdata();
		return count( $posts );
	}


	/**
	 * Obtain the number of configured Zapier feeds.
	 * This only includes published (active) ones.
	 *
	 * @return int
	 */
	public static function get_number_of_enabled_feeds() {

		return count( self::get_enabled_feeds() );

	}

	/**
	 * Obtain all of the configured active and valid Zapier feeds. This only includes published (active) ones.
	 *
	 * @return WC_Zapier_Feed[]
	 */
	public static function get_enabled_feeds() {
		$query       = array(
			'post_type'   => 'wc_zapier_feed'
		, 'nopaging'    => true
		, 'post_status' => 'publish'
		, 'orderby'     => 'date'
		, 'order'       => 'ASC'
		);
		$feeds_query = new WP_Query($query);
		$feeds       = array();
		$posts       = $feeds_query->get_posts();

		foreach ( $posts as $post ) {
			$feed = new WC_Zapier_Feed( $post );
			// Ensure the active feed's trigger is valid.
			if ( $feed->is_valid_trigger() ) {
				$feeds[] = $feed;
			}
		}
		wp_reset_postdata();
		return $feeds;
	}

}