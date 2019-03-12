<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://elixinol.com/
 * @since      1.0.0
 *
 * @package    Woo_Product_Feed
 * @subpackage Woo_Product_Feed/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woo_Product_Feed
 * @subpackage Woo_Product_Feed/public
 * @author     Christopher Cook <chris.cook@elixinol.com>
 */
class Woo_Product_Feed_Public {

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
   * The feed name.
   *
   * @since    1.0.1
   * @access   private
   * @var      string    $feed_names    The names of feeds added by this plugin.
   */
  private $feed_names;

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
    $this->feed_names = [
      'products',
      'products-brief',
    ];

  }

  /**
   * Add custom feeds.
   *
   * @since    1.0.0
   */
  public function init() {

    foreach ($this->feed_names as $feed) {
      add_feed( $feed, array( $this, 'render_feed' ) );
    }

  }

  /**
   * Set the content mime-type for the feed.
   *
   * @since   1.0.0
   * @access  public
   * @var     string    $content_type   The default mime type
   * @param   string    $type           The feed type
   */
  public function feed_content_type( $content_type, $type ) {

    if ( in_array( $type, $this->feed_names ) ) {
      $content_type = 'text/xml';
    }
    return $content_type;

  }

  /**
   * Render the feed.
   *
   * @since    1.0.0
   * @access   public
   */
  public function render_feed() {

    // Load feed template which matches the callback name and feed type
    $path = plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/';
    $feed = substr( current_filter(), strlen( 'do_feed_' ) );
    require $path . $feed . '.php';

  }

  /**
   * Modify the query for product feeds.
   *
   * @since    1.0.0
   * @access   public
   * @param    object    The query
   */
  public function pre_get_posts( $query ) {

    // Only trigger if feed type is one of our custom types
    if( $query->is_main_query() && is_feed() && in_array( $query->query['feed'], $this->feed_names ) ) {
      $query->set( 'post_type', 'product' );
      $query->set( 'posts_per_rss', 100 );
    }

  }

  /**
   * Display the primary product category.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_category() {

    // Display the primary product category according to wordpress-seo if it is active
    if ( class_exists('WPSEO_Primary_Term') ) {
      $wpseo = new WPSEO_Primary_Term( 'product_cat', get_the_ID() );
      $primary_term_id = $wpseo->get_primary_term();
      $term = get_term( $primary_term_id );
      if ( ! is_wp_error($term) ) {
        echo $term->name;
        return;
      }
    }

    // Display the first category if wordpress-seo is not activate
    $category = get_the_category();
    if ( ! empty($category) ) {
      echo $category[0]->name;
    }

  }

  /**
   * Display the product currency.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_currency() {

    echo get_woocommerce_currency();

  }

  /**
   * Display the product brand. Depends on a manually created custom attribute.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_brand() {

    global $product;
    if ( method_exists( $product, 'get_attributes' ) ) {
      $attributes = $product->get_attributes();
      if ( array_key_exists( 'pa_brand', $attributes ) ) {
        $brand_terms = $attributes['pa_brand']->get_terms();
        echo $brand_terms[0]->name;
      }
    }

  }

  /**
   * Display the product sale price.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_sale_price() {

    global $product;
    if ( method_exists( $product, 'get_sale_price' ) )
      echo $product->get_sale_price();

  }

  /**
   * Display the product regular price.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_regular_price() {

    global $product;
    if ( method_exists( $product, 'get_regular_price' ) )
      echo $product->get_regular_price();

  }

  /**
   * Display the product SKU.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_sku() {

    global $product;
    if ( method_exists( $product, 'get_sku' ) )
      echo $product->get_sku();

  }

  /**
   * Display the product description.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_description() {

    echo the_excerpt_rss();

  }

  /**
   * Display the product content.
   *
   * @since    1.0.1
   * @access   private
   */
  private function the_content() {

    $content = get_the_content_feed('rss2');
    if ( strlen( $content ) > 0 ) {
      echo $content;
    }
    else {
      echo the_excerpt_rss();
    }

  }


}
