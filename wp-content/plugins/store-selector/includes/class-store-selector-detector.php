<?php

/**
 * Detect the visitor's country.
 *
 * Tries to detect the visitor's location and uses rules to determine a
 * potential suggested alternate site.
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Store_Selector
 * @subpackage Store_Selector/includes
 */

/**
 * Detect the visitor's country.
 *
 * Tries to detect the visitor's location and uses rules to determine a
 * potential suggested alternate site.
 *
 * @since      1.0.0
 * @package    Store_Selector
 * @subpackage Store_Selector/includes
 * @author     Christopher Cook <chris.cook@elixinol.com>
 */
class Store_Selector_Detector {

  /**
   * Country detected by viewer geolocation.
   *
   * @since   1.0.1
   */
  private $country_code;

  /**
   * Return ISO country code if viewer can be geolocated.
   *
   * @since   1.0.1
   */
  public function get_country_code() {

    if ( ! isset( $this->country_code ) ) {

      // Try to detect country using CloudFront-Viewer-Country HTTP header
      if ( isset( $_SERVER['CloudFront-Viewer-Country']) ) {
        $this->country_code = $_SERVER['CloudFront-Viewer-Country'];
      }
      // Try to detect country using WooCommerce geolocation
      elseif ( defined( 'WC_ABSPATH' ) ) {
        require_once WC_ABSPATH . 'includes/class-wc-geolocation.php';
        $geo = new WC_Geolocation();
        $result = $geo->geolocate_ip();

        if ($result) {
          $this->country_code = $result['country'];
        }
      }
      else {
        $this->country_code = '';
      }

    }

    return $this->country_code;
  }


}
