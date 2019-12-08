<?php
/*
Plugin Name: Fastly
Plugin URI: http://fastly.com/
Description: Configuration and cache purging for the Fastly CDN.
Authors: Zack Tollman (github.com/tollmanz), WIRED Tech Team (github.com/CondeNast) & Fastly
Version: 1.2.11
Author URI: http://fastly.com/
*/

/**
 * Singleton for kicking off functionality for this plugin.
 */
class Purgely
{
    /**
     * The one instance of Purgely.
     *
     * @var Purgely
     */
    private static $instance;

    /**
     * The Purgely_Surrogate_Key_Collection object to manage Surrogate Key Collection.
     *
     * @var Purgely_Surrogate_Key_Collection    The Purgely_Surrogate_Key_Collection object to manage Surrogate Key Collection.
     */
    private static $surrogate_keys_collection;

    /**
     * The Purgely_Surrogate_Key object to manage Surrogate Keys.
     *
     * @var Purgely_Surrogate_Keys_Header    The Purgely_Surrogate_Key object to manage Surrogate Keys.
     */
    private static $surrogate_keys_header;

    /**
     * The Purgely_Surrogate_Control_Header to manage the TTL.
     *
     * @var Purgely_Surrogate_Control_Header    The Purgely_Surrogate_Control_Header to manage the TTL.
     */
    private static $surrogate_control_header;

    /**
     * An array of cache control headers to manage the caching behavior.
     *
     * @var Purgely_Cache_Control_Header[]    An array of cache control headers to manage the caching behavior.
     */
    private static $cache_control_headers = array();

    /**
     * Last plugin version.
     * Increment when making schema and code changes. Also change version in comment above class.
     * If there are schema changes, create function in upgrades class and increment.
     * If just code changes, only increment.
     *
     * @var   string    Plugin version.
     */
    var $version = '1.2.11';

    /**
     * Currently installed plugin version.
     *
     * @var   string    Currently installed plugin version number.
     */
    var $current_version = null;

    /**
     * Last vcl version.
     *
     * @var   string    Last updated vcl version. Increment when making vcl changes
     */
    var $vcl_last_version = '1.1.1';

    /**
     * File path to the plugin dir (e.g., /var/www/mysite/wp-content/plugins/purgely).
     *
     * @var   string    Path to the root of this plugin.
     */
    var $root_dir = '';

    /**
     * File path to the plugin src files (e.g., /var/www/mysite/wp-content/plugins/purgely/src).
     *
     * @var   string    Path to the root of this plugin.
     */
    var $src_dir = '';

    /**
     * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/mixed-content-detector/purgely.php).
     *
     * @var   string    Path to the plugin's main file.
     */
    var $file_path = '';

    /**
     * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/purgely).
     *
     * @var   string    The URI base for the plugin.
     */
    var $url_base = '';

    /**
     * Status of connection
     * @var bool
     */
    var $connection_status = array();

    /**
     * Name of currentyl used service
     * @var string
     */
    var $service_name = '';

    /**
     * Instantiate or return the one Purgely instance.
     *
     * @return Purgely
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initiate actions.
     *
     * @return Purgely
     */
    public function __construct()
    {
        self::$instance = $this;

        // Set the main paths for the plugin.
        $this->root_dir = dirname(__FILE__);
        $this->src_dir = $this->root_dir . '/src';
        $this->vcl_dir = $this->root_dir . '/vcl_snippets';
        $this->file_path = $this->root_dir . '/' . basename(__FILE__);
        $this->url_base = untrailingslashit(plugins_url('/', __FILE__));
        $this->current_version = get_option("fastly-schema-version", false);

        $include_files = array(
            $this->src_dir . '/config.php',
            $this->src_dir . '/utils.php',
            $this->src_dir . '/classes/settings.php',
            $this->src_dir . '/classes/upgrades.php',
            $this->src_dir . '/classes/vcl-handler.php',
            $this->src_dir . '/classes/related-surrogate-keys.php',
            $this->src_dir . '/classes/purge-request.php',
            $this->src_dir . '/classes/surrogate-key-collection.php',
            $this->src_dir . '/classes/header.php',
            $this->src_dir . '/classes/header-surrogate-control.php',
            $this->src_dir . '/classes/header-cache-control.php',
            $this->src_dir . '/classes/header-surrogate-keys.php',
            $this->src_dir . '/wp-purges.php'
        );

        // Include dependent files.
        $currently_included = get_included_files();
        foreach($include_files as $file) {
            if(!in_array($file, $currently_included)) {
                include $file;
            }
        }

        if (is_admin()) {
            include $this->src_dir . '/settings-page.php';
        }

        // First install DB schema changes
        $upgrades = new Upgrades($this);
        $upgrades->check_and_run_upgrades();

        // Initialize custom cache taxonomy if activated
        if(Purgely_Settings::get_setting('use_fastly_cache_tags')) {
            add_action( 'init', array($this, 'init_fastly_cache_taxonomy'));
        }

        // Initialize the key collector.
        $this::$surrogate_keys_header = new Purgely_Surrogate_Keys_Header();

        // Initialize cache control header.
        $this::$cache_control_headers = new Purgely_Cache_Control_Header();

        // Initialize the surrogate control header.
        $this::$surrogate_control_header = new Purgely_Surrogate_Control_Header();

        // Add the surrogate keys.
        add_action('wp', array($this, 'set_standard_keys'), 100);

        // Send the surrogate keys.
        add_action('wp', array($this, 'send_surrogate_keys'), 101);

        // Set and send the surrogate control header.
        add_action('wp', array($this, 'send_surrogate_control'), 101);

        // Set and send the surrogate control header.
        add_action('wp', array($this, 'send_cache_control'), 101);

        //Image optimization
        if (!is_admin() && Purgely_Settings::get_setting('io_enable_wp')) {
            if(Purgely_Settings::get_setting('io_adaptive_pixel_ratios')) {
                add_action('wp', array($this, 'image_optimization_device_pixel_ratios'), 101);
            }
        }

        // Load in WP CLI.
        if (defined('WP_CLI') && WP_CLI) {
            include $this->src_dir . '/wp-cli.php';
        }

        // Set plugin url
        if (!defined('FASTLY_PLUGIN_URL')) {
            define('FASTLY_PLUGIN_URL', plugin_dir_url(__FILE__));
        }

        // Set version
        if (!defined('FASTLY_VERSION')) {
            define('FASTLY_VERSION', $this->version);
        }

        // Load the textdomain.
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    /**
     * Set all the surrogate keys for the requests.
     *
     * @return void
     */
    public function set_standard_keys()
    {

        if (is_user_logged_in()) {
            return;
        }

        global $wp_query;
        $key_collection = new Purgely_Surrogate_Key_Collection($wp_query);

        $this::$surrogate_keys_collection = $key_collection;

        $keys = $key_collection->get_keys();
        $this::$surrogate_keys_header->add_keys($keys);
    }

    /**
     * Send the currently registered surrogate keys.
     *
     * This function takes all of the surrogate keys that are currently recorded and flattens them into a single header
     * and sends the header. Any other keys need to be set by 3rd party code before "init", 101.
     *
     * This function does allow for a filtering of the keys before they are sent, to allow for the keys to be
     * de-registered when and if necessary.
     *
     * @return void
     */
    public function send_surrogate_keys()
    {

        if (is_user_logged_in()) {
            return;
        }

        $keys_header = $this::$surrogate_keys_header;
        $keys = apply_filters('purgely_surrogate_keys', $keys_header->get_keys());
        $keys_header->set_keys($keys);

        do_action('purgely_pre_send_keys', $keys_header);
        $keys_header->send_header();
        do_action('purgely_post_send_keys', $keys_header);
    }

    /**
     * Set the TTL for the object and send the header.
     *
     * This is the main function for setting the TTL for the page.
     * To change it, use the "purgely_pre_send_surrogate_control" and "purgely_post_send_surrogate_control"
     * actions.
     *
     * Note that any alterations must be done before init, 101.
     *
     * The default set here is 5 minutes. This has proven to be a reasonable default for caches for WordPress pages.
     *
     * @return void
     */
    public function send_surrogate_control()
    {
        /**
         * If a user is logged in, surrogate control headers should be ignored. We do not want to cache any logged in
         * user views. WordPress sets a "Cache-Control:no-cache, must-revalidate, max-age=0" header for logged in views
         * and this should be sufficient for keeping logged in views uncached.
         */
        if (is_user_logged_in()) {
            return;
        }

        $surrogate_control = $this::$surrogate_control_header;

        $custom_ttl = $this::$surrogate_keys_collection->get_custom_ttl();
        if($custom_ttl) {
            $surrogate_control->edit_headers(array('max-age' => $custom_ttl));
        }

        do_action('purgely_pre_send_surrogate_control', $surrogate_control);
        $surrogate_control->send_header();
        do_action('purgely_post_send_surrogate_control', $surrogate_control);
    }

    /**
     * Send each of the control control headers.
     *
     * @return void
     */
    public function send_cache_control()
    {
        /**
         * If a user is logged in, surrogate control headers should be ignored. We do not want to cache any logged in
         * user views. WordPress sets a "Cache-Control:no-cache, must-revalidate, max-age=0" header for logged in views
         * and this should be sufficient for keeping logged in views uncached.
         */
        if (is_user_logged_in()) {
            return;
        }

        $cache_control = $this::$cache_control_headers;

        do_action('purgely_pre_send_cache_control', $cache_control);
        $cache_control->send_header();
        do_action('purgely_post_send_cache_control', $cache_control);
    }

    /**
     * Load the plugin text domain.
     *
     * @return void
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain('purgely', false, basename(dirname(__FILE__)) . '/languages/');
    }

    /**
     * Initialize Fastly custom cache tags taxonomy
     */
    public function init_fastly_cache_taxonomy()
    {
        $labels = array(
            'name'                       => _x( 'Fastly Cache Tags', 'taxonomy general name', 'purgely' ),
            'singular_name'              => _x( 'Fastly Cache Tag', 'taxonomy singular name', 'purgely' ),
            'search_items'               => __( 'Search Fastly Tags', 'purgely' ),
            'popular_items'              => __( 'Popular Fastly Tags', 'purgely' ),
            'all_items'                  => __( 'All Fastly Tags', 'purgely' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Edit Fastly Tags', 'purgely' ),
            'update_item'                => __( 'Update Fastly Tags', 'purgely' ),
            'add_new_item'               => __( 'Add New Fastly Tags', 'purgely' ),
            'new_item_name'              => __( 'New Fastly Tags Name', 'purgely' ),
            'separate_items_with_commas' => __( 'Separate Fastly Tags with commas', 'purgely' ),
            'add_or_remove_items'        => __( 'Add or remove Fastly Tags', 'purgely' ),
            'choose_from_most_used'      => __( 'Choose from the most used Fastly Tags', 'purgely' )
        );

        // create a new taxonomy
        register_taxonomy(
            'fastly_cache_tag',
            array('post', 'page', 'attachment', 'nav_menu_item'),
            array(
                'labels' => $labels,
                'rewrite' => array( 'slug' => 'fastly_cache_tag' ),
                'capabilities' => array(
                    'manage_terms' => 'manage_categories',
                    'edit_terms' => 'manage_categories',
                    'delete_terms' => 'manage_categories',
                    'assign_terms' => 'edit_posts',
                )
            )
        );

        // Include custom post types if activated
        if(Purgely_Settings::get_setting('use_fastly_cache_tags_for_custom_post_type')) {
            $custom_post_types = get_post_types(array('_builtin' => false));
            if(is_array($custom_post_types) && !empty($custom_post_types)) {
                foreach ($custom_post_types  as $post_type) {
                    $result = register_taxonomy_for_object_type( 'fastly_cache_tag', $post_type );
                    if(!$result && Purgely_Settings::get_setting('fastly_debug_mode')) {
                        error_log('Error when registering Fastly cache tags to custom post type: ' . $post_type);
                    }
                }
            }
        }
    }

    public function image_optimization_device_pixel_ratios() {
        add_filter( 'wp_get_attachment_image_attributes', array($this, 'fastly_io_pixel_ratios_attachment'), 100 , 3 );
        add_filter('wp_calculate_image_sizes', array($this, 'fastly_io_pixel_ratios_sizes'), 100);

        if(Purgely_Settings::get_setting('io_adaptive_pixel_ratios_content')) {
            add_filter( 'wp_calculate_image_srcset', array($this, 'fastly_io_pixel_ratios_content'), 100 , 5 );
            add_filter( 'the_content', array($this, 'fastly_io_pixel_ratios_the_content_filter'));
        }
    }

    /**
     * Set attachment srcset and unset sizes for Fastly IO pixels
     * @param $attr
     * @return mixed
     */
    function fastly_io_pixel_ratios_attachment($attr, $attachment, $size) {

        $image_data = wp_get_attachment_image_src($attachment->ID, $size);
        $width = $image_data[1];
        $src = $attachment->guid;
        $query = '?width=' . $width;
        $attr['src'] = $src . $query;
        $sizes = Purgely_Settings::get_setting('io_adaptive_pixel_ratio_sizes');

        $dprCount = count($sizes);
        $attr['srcset'] = '';
        foreach($sizes as $key => $size) {
            $size_int = trim($size, 'x');
            $attr['srcset'] .= $src . $query . "&dpr=$size_int {$size}";
            if(($key+1) < $dprCount) {
                $attr['srcset'] .= ', ';
            }
        }

        unset($attr['sizes']);
        $attr['alt'] = isset($attachment->post_name) ? $attachment->post_name : 'image';

        return $attr;
    }

    /**
     * Set pixel ratios srcset on content images
     * @param $size_array
     * @param $image_src
     * @param $src
     * @param $attachment_id
     * @return array
     */
    function fastly_io_pixel_ratios_content( $size_array, $image_src, $src, $attachment_id, $id ) {

        $attachment_original = wp_get_attachment_url($id);
        $width = isset($image_src[0]) ? $image_src[0] : $attachment_id['width'];
        $query = "?width={$width}";
        $sizes = Purgely_Settings::get_setting('io_adaptive_pixel_ratio_sizes');
        $srcset = array();
        $main = array();
        foreach($sizes as $size) {
            $size_int = trim($size, 'x');
            $srcset['url'] = $attachment_original . $query . "&dpr=$size_int";
            $srcset['value'] = $size;
            $main[] = $srcset;
        }

        return $main;
    }

    /**
     * Fake sizes to unset it
     * @return bool
     */
    function fastly_io_pixel_ratios_sizes() {
        return true;
    }

    /**
     * Adjust content images to IO format
     * @param $content
     * @return mixed
     */
    function fastly_io_pixel_ratios_the_content_filter($content) {
        if ( ! preg_match_all( '/<img [^>]+>/', $content, $matches ) ) {
            return $content;
        }

        $selected_images = $attachment_ids = array();

        foreach( $matches[0] as $image ) {
            if ( true == strpos( $image, ' src=' ) && preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) &&
                ( $attachment_id = absint( $class_id[1] ) ) ) {

                /*
                 * If exactly the same image tag is used more than once, overwrite it.
                 * All identical tags will be replaced later with 'str_replace()'.
                 */
                $selected_images[ $image ] = $attachment_id;
                // Overwrite the ID when the same image is included more than once.
                $attachment_ids[ $attachment_id ] = true;
            }
        }

        if ( count( $attachment_ids ) > 1 ) {
            /*
             * Warm the object cache with post and meta information for all found
             * images to avoid making individual database calls.
             */
            _prime_post_caches( array_keys( $attachment_ids ), false, true );
        }

        foreach ( $selected_images as $image => $attachment_id ) {

            $image_src = preg_match( '/src="([^"]+)"/', $image, $match_src ) ? $match_src[1] : '';
            list( $image_src ) = explode( '?', $image_src );

            // Return early if we couldn't get the image source.
            if ( ! $image_src ) {
                continue;
            }

            // Extract width
            $width  = preg_match( '/ width="([0-9]+)"/',  $image, $match_width  ) ? (int) $match_width[1]  : 0;

            $image_src_param = 'src="' . $image_src . '?width=' . $width . '"';
            $image_src = 'src="' . $image_src . '"';

            // Replace edited image src with src containing parameter
            $replacement = str_replace( $image_src, $image_src_param, $image );
            // Replace edited IO image in content
            $content = str_replace( $image, $replacement, $content );
        }
        return $content;
    }
}

/**
 * Instantiate or return the one Purgely instance.
 *
 * @return Purgely
 */
function get_purgely_instance()
{
    return Purgely::instance();
}

get_purgely_instance();
