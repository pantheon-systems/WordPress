<?php

/**
 * Use shipping rules to determine if the viewer should shop at another site.
 *
 * Uses the visitor's location to determine a potential suggested alternate site.
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Store_Selector
 * @subpackage Store_Selector/includes
 */

/**
 * Use shipping rules to determine if the viewer should shop at another site.
 *
 * Uses the visitor's location to determine a potential suggested alternate site.
 *
 * @since      1.0.0
 * @package    Store_Selector
 * @subpackage Store_Selector/includes
 * @author     Christopher Cook <chris.cook@elixinol.com>
 */
class Store_Selector_Rules {

  /**
   * Country detected by viewer geolocation.
   *
   * @since   1.0.0
   */
  private $country_code;

  /**
   * List of sites which a viewer might be recommended.
   *
   * @since   1.0.0
   */
  private $sites;

  /**
   * The active site being viewed.
   *
   * @since   1.0.0
   */
  private $current_site;

  /**
   * The alternate site which the viewer is suggested to visit.
   *
   * @since   1.0.0
   */
  public $recommended_site;

  /**
   * If the detected country of the viewer is impossible to ship to from the current site.
   *
   * @since   1.0.0
   */
  public $forced;

  /**
   * Initialize variables.
   *
   * Shipping data is hard-coded below for simplicity though it may be
   * feasible to look up the shipping zones and restrictions for all sites in the network
   * and store then in a central table if shipping restrictions change frequently.
   */
  public function __construct($country_code) {
    $this->country_code = $country_code;

    $this->current_site = $_SERVER['HTTP_HOST'];

    // Initialize the list of available sites and shipping rules
    // TODO: Move this to a configurable option in admin console
    $this->sites = array(
      'elixinol.com' => array(
        'blocked' => array(
          'AT', // Austria
          'AU', // Australia
          'CA', // Canada
          'CH', // Switzerland
          'CN', // China
          'GB', // Great Britain
          'JP', // Japan
          'MX', // Mexico
          'RU', // Russia
          'ZA', // South Africa
        ),
        'recommended' => array(
          'US', // USA
        ),
      ),
      'elixinol.eu' => array(
        'blocked' => array(
          'AT', // Austria
          'AU', // Australia
          'BR', // Brazil
          'CA', // Canada
          'CH', // Switzerland
          'CN', // China
          'JP', // Japan
          'MX', // Mexico
          'NZ', // New Zealand
          'RU', // Russia
          'ZA', // South Africa
        ),
        'recommended' => array(
          'AD', 'AL', 'AM', // Andorra, Albania, Armenia
          'BA', 'BE', 'BG', 'BY', // Bosnia, Belgium, Bulgaria
          'CH', 'CY', 'CZ', // Switzerland, Cyprus, Czech Republic
          'DE', 'DK', // Germany, Denmark
          'EE', 'ES', // Estonia, Spain
          'FI', 'FO', 'FR', // Finland, Faeroe Islands, France
          'GB', 'GE', 'GI', 'GR', // United Kingdom, Georgia, Gibralter, Greece
          'HU', 'HR', // Hungary, Croatia
          'IE', 'IS', 'IT', // Ireland, Iceland, Italy
          'LT', 'LU', 'LV', // Lithuania, Luxembourg, Latvia
          'MC', 'MK', 'MT', // Monaco, Macedonia, Malta
          'NL', 'NO', // Netherlands, Norway
          'PO', 'PT', // Poland, Portugal
          'RO', 'RU', // Romania, Russia
          'SE', 'SI', 'SK', 'SM', // Sweden, Slovenia, Slovakia, San Marino
          'TR', // Turkey
          'UA', // Ukraine
          'VA', // Vatican City State
        ),
      ),
      'elixinol.co.jp' => array(
        'recommended' => array('JP'), // Japan
        'blocked' => array('*'),      // All countries
      ),
      'elixinol.co.za' => array(
        'recommended' => array('ZA'), // South Africa
        'blocked' => array('*'),      // All countries
      )
    );

    $this->forced = false;

    $this->recommended_site = $this->init_recommended();
  }

  /**
   * Determine if there is a recommended site based on the viewers detected country
   *
   * @since 1.0.0
   */
  private function init_recommended() {

    // If current site is unknown give up
    if ( ! isset( $this->sites[ $this->current_site ] ) )
      return false;

    $blocked = false;

    $country = $this->country_code;
    $rules = $this->sites[ $this->current_site ];

    // Determine if current site is not automatically recommended for visitor country
    if ( ! in_array( $country, $rules['recommended'] ) ) {

      // Determine if current site is prohibited for visitor country
      if ( in_array( $country, $rules['blocked'] ) || in_array( '*', $rules['blocked'] ) ) {
        $blocked = true;
        $this->forced = true;
      }

      // Iterate over sites looking for best match for shipping destination
      foreach ( $this->sites as $site => $rules ) {

        // Skip current site
        if ( $site != $this->current_site ) {

          // If site lists destination as recommended, we're done
          if ( in_array( $country, $rules['recommended'] ) ) {
            return $site;
          }
          // If destination has been blocked from current site but not blocked from another site
          elseif ( $blocked && ! in_array($country, $rules['blocked']) && ! in_array( '*', $rules['blocked'] ) ) {
            return $site;
          }

        }

      }

    }

    return false;
  }

  /**
   * Get the recommended site data.
   *
   * @since 1.0.0
   */
  public function get_recommended() {

    // Try to localize country name
    if ( function_exists( 'locale_get_display_region' ) ) {
      $language_code = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'EN';
      $country_name = locale_get_display_region( '-' . $this->country_code, $language_code );
    }
    else {
      // Oops, php-intl extension is not installed
      $country_name = $this->country_code;
    }

    // Define vars for dialog text
    $vars = array( '%country' => $country_name, '%recommended_site' => $this->recommended_site );

    if ( $this->forced ) {
      $s = __('It looks like you are shopping from %country. It is only possible to ship to %country from <b>%recommended_site</b>.', 'store-selector');
    }
    else {
      $s = __('It looks like you are shopping from %country. You may prefer to shop at <b>%recommended_site</b> for that shipping destination.', 'store-selector');
    }
    $explanation = str_replace( array_keys($vars), array_values($vars), $s );

    $recommended = array(
      'site'        => $this->recommended_site,
      'explanation' => $explanation,
    );

    return $recommended;
  }


}
