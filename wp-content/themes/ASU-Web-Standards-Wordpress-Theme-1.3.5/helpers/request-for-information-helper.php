<?php
/**
 * Request For Information Helper
 *
 * @see https://github.com/ASU/webspark-drops-drupal7/blob/79fc33b70336c152afc686666dad031dae3e9deb/profiles/openasu/modules/contrib/webform/webform.module
 * @see https://github.com/ASU/webspark-drops-drupal7/blob/5f8a994ad6e82ff1c782a69d22c1624848b33bcf/profiles/openasu/modules/custom/asu_rfi/asu_rfi.module
 * @see https://github.com/ASU/webspark-drops-drupal7/blob/5f8a994ad6e82ff1c782a69d22c1624848b33bcf/profiles/openasu/modules/custom/asu_rfi/data/asu_rfi_countries_data.inc
 * @see https://github.com/ASU/webspark-drops-drupal7/blob/5f8a994ad6e82ff1c782a69d22c1624848b33bcf/profiles/openasu/modules/custom/asu_rfi/data/asu_rfi_states_data.inc
 * @see https://github.com/ASU/webspark-drops-drupal7/blob/5f8a994ad6e82ff1c782a69d22c1624848b33bcf/profiles/openasu/modules/custom/asu_rfi/data/asu_rfi_sem_dates.inc
 */

namespace Wordspark;

class Request_For_Information_Helper {
  static $semester_dates = array(
    'type' => 'select',
    'name' => 'rfi_dedupe_list_terms',
    'host' => 'webforms.asu.edu',
    'api' => 'asu_saluesforce_query/api',
    // TODO should be loaded from a configuration file
    'authorization_token' => 'archana:3UIzHi6vMNXDTV9p',
  );

  static $states_data = array(
    'type' => 'fieldinfo',
    'name' => 'state_province',
    'host' => 'webforms.asu.edu',
    'api' => 'asu_salesforce_query/api',
    // TODO should be loaded from a configuration file
    'authorization_token' => 'archana:3UIzHi6vMNXDTV9p',
  );

  static $countries_data = array(
    'type' => 'fieldinfo',
    'name' => 'country',
    'host' => 'webforms.asu.edu',
    'api' => 'asu_salesforce_query/api',
    // TODO should be loaded froma configuration file
    'authorization_token' => 'archana:3UIzHi6vMNXDTV9p',
  );

  /**
   * @return JSON Object
   */
  public static function request( $information_type = '', $data = array(), $body = false ) {
    // TODO set $data defaults
    // TODO use vip_safe_wp_remote_get()  instead of curl: http://lobby.vip.wordpress.com/best-practices/fetching-remote-data/
    // @codingStandardsIgnoreStart

    $url = 'https://' .  $data['host'] . '/' . $data['api'] . '/' . $data['type'] . '/' . $data['name'];

    $curl = curl_init();
    $options = array();
    $options['CURLOPT_URL'] = $url;
    $options['CURLOPT_RETURNTRANSFER'] = 1; // TODO should this just be TRUE?
    $options['CURLOPT_HTTPHEADER'] = array( 'Content-Type: text/json', 'Authorization: Basic ' . base64_encode( $data['authorization_token'] ) );

    if ( $body ) {
      $options['CURL_POST_FIELDS'] = $body;
    }

    if ( 'semester_dates' == $information_type ) {
      $options['CURL_POST'] = 1; // TODO should this just be TRUE?
    }

    curl_setopt_array( $curl, $options );

    $response = curl_exec( $curl );
    $result = json_decode( $resonse );

    curl_close( $curl );
    // @codingStandardsIgnoreEnd
    return $result;
  }

  /**
   * Gets data in the form of: [ { ID, LABEL }, ... ].
   *
   * Example value: ('a0Jd000000CpGccEAF', '2014 Spring')
   *
   * @return JSON Object
   */
  public static function request_semester_dates() {
    $body = json_encode( array( 'condition_Term_Status__c' => true ) )

    return self::request( 'semester_dates', self::$semester_dates, $body );
  }

  /**
   * Gets data in the form of: [ { STATE_CODE, STATE_DESCRIPTION }, ... ].
   *
   * Example values: ('AZ', 'Arizona')
   *
   * @return JSON Object
   */
  public static function request_states_data() {
    return self::request( 'states_data', self::$states_data );
  }

  /**
   * Gets data in the form of: [ { COUNTRY_CODE, COUNTRY_DESCRIPTION }, ... ].
   *
   * Example values: ('US', 'United States')
   *
   * @return JSON Object
   */
  public static function request_countries_data() {
    return self::request( 'countries_data', self::$countries_data );
  }

  /*
   * TODO country data should be re-requested every 1 day.
   * Store transient that expires in 1 day for the country data
   */

  /*
   * TODO state data should be re-requested every 1 day.
   * Store transient that expires in 1 day for the state data
   */

  /*
   * TODO countries data should be re-requested every 1 day.
   * Store transient that expires in 1 day for the countries data
   */

  /**
   * TODO Check for unposted leads ( confirmation == -1 ) and add them to the cron queue
   */

  // TODO still need to implement:
  // https://github.com/ASU/webspark-drops-drupal7/blob/79fc33b70336c152afc686666dad031dae3e9deb/profiles/openasu/modules/contrib/webform/webform.module

  // TODO still need to implement:
  // https://github.com/ASU/webspark-drops-drupal7/blob/5f8a994ad6e82ff1c782a69d22c1624848b33bcf/profiles/openasu/modules/custom/asu_rfi/asu_rfi.module
}