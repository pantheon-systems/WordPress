<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Store_Selector
 * @subpackage Store_Selector/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Store_Selector
 * @subpackage Store_Selector/public
 * @author     Christopher Cook <chris.cook@elixinol.com>
 */
class Store_Selector_Public {

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $plugin_name    The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * A flag to indicate if the plugin should operate.
   *
   * @since    1.0.0
   * @access   public
   * @var      string    $is_active    A flag to indicate if the plugin should operate.
   */
  public $is_active;

  /**
   * A handle for the geolocation detector object.
   *
   * @since   1.0.0
   * @access  private
   * @var     object    $detector     A handle for the geolocation detector object.
   */
  private $detector;

  /**
   * A handle for the shipping rules object.
   *
   * @since   1.0.0
   * @access  private
   * @var     object    $rules        A handle for the shipping rules object.
   */
  private $rules;


  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param    string    $plugin_name  The name of the plugin.
   * @param    string    $version      The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;

    require_once plugin_dir_path( __DIR__ ) . 'includes/class-store-selector-detector.php';
    $this->detector = new Store_Selector_Detector();

    $this->is_active = $this->check_conditions();

  }

  /**
   * Perform an early check if the notification will be needed.
   *
   * @since 1.0.0
   */
  private function check_conditions() {

    // If visitor has a cookie set then exit
    if ( isset($_COOKIE['ssov']) ) {
      return false;
    }

    $country_code = $this->detector->get_country_code();

    if ( $country_code != '' ) {
      require_once plugin_dir_path( __DIR__ ) . 'includes/class-store-selector-rules.php';
      $this->rules = new Store_Selector_Rules( $country_code );
      if ( $this->rules->recommended_site ) {
        return true;
      }
    }

    return false;
  }

  /**
   * Register the stylesheets for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {

    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ssm.css', array(), $this->version, 'all' );

  }

  /**
   * Register the JavaScript for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {

    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ssm.js', array( 'js-cookie' ), $this->version, true );

  }

  /**
   * Render the notification content.
   *
   * @since    1.0.0
   */
  public function notification_render() {
    $recommended = $this->rules->get_recommended();

    $flag_url_base = 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.1/flags/1x1/';
    $flag_ext = '.svg';

    // Define template vars
    $title = __('Go To Suggested Site?', 'store-selector');

    $suggested_site_name = $recommended['site'];
    $suggested_site_href = 'https://' . $suggested_site_name . '/';
    $suggested_site_img = $flag_url_base . strtolower( $this->detector->get_country_code() ) . $flag_ext;

    $explanation = $recommended['explanation'];

    $cancel_label = __('Stay', 'store-selector');
    $accept_label = __('Go', 'store-selector');

    // Load template
    include( 'partials/public-modal.php' );

  }


}
