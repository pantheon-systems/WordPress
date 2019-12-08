<?php
/**
 * Base Solr Power controller.
 *
 * @package Solr_Power
 */

/**
 * Base Solr Power controller.
 */
class SolrPower {

	/**
	 * Singleton instance
	 *
	 * @var SolrPower|Bool
	 */
	private static $instance = false;

	/**
	 * Grab instance of object.
	 *
	 * @return SolrPower
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instantiate the Solr Power class
	 */
	public function __construct() {
		$method = filter_input( INPUT_GET, 'method', FILTER_SANITIZE_STRING );
		if ( 'autocomplete' === $method ) {
			add_action( 'template_redirect', array( $this, 'template_redirect' ), 1 );
			add_action( 'wp_enqueue_scripts', array( $this, 'autosuggest_head' ) );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_head' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_filter( 'plugin_action_links', array( $this, 'plugin_settings_link' ), 10, 2 );
		add_filter( 'debug_bar_panels', array( $this, 'add_panel' ) );
		add_action(
			'widgets_init',
			function () {
				register_widget( 'SolrPower_Facet_Widget' );
			}
		);

		add_action( 'wp_ajax_nopriv_solr_search', array( $this, 'ajax_search' ) );
		add_action( 'wp_ajax_solr_search', array( $this, 'ajax_search' ) );
	}

	/**
	 * Handles actions needed on activation.
	 */
	public function activate() {

		// Check to see if we have  environment variables. If not, bail. If so, create the initial options.
		$error_message = SolrPower::get_instance()->sanity_check();
		if ( $error_message ) {
			wp_die( esc_html( $error_message ) );
		}

		// Don't try to send a schema if we're not on Pantheon servers.
		if ( ! defined( 'SOLR_PATH' ) ) {
			$schema_message = SolrPower_Api::get_instance()->submit_schema();
			if ( strpos( $schema_message, 'Error' ) ) {
				wp_die( 'Submitting the schema failed with the message ' . esc_html( $schema_message ) );
			}
		}
		SolrPower_Options::get_instance()->initalize_options();

		return;
	}

	/**
	 * Verify this plugin will work as expected in the WordPress instance.
	 *
	 * @return string
	 */
	public function sanity_check() {
		$return_value = '';
		$wp_version   = get_bloginfo( 'version' );

		if ( getenv( 'PANTHEON_ENVIRONMENT' ) !== false && getenv( 'PANTHEON_INDEX_HOST' ) === false ) {
			$return_value = wp_kses(
				__( 'Before you can activate this plugin, you must first <a href="https://pantheon.io/docs/articles/sites/apache-solr/">activate Solr</a> in your Pantheon Dashboard.', 'solr-for-wordpress-on-pantheon' ),
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);
		} elseif ( version_compare( $wp_version, '3.0', '<' ) ) {
			$return_value = esc_html__( 'This plugin requires WordPress 3.0 or greater.', 'solr-for-wordpress-on-pantheon' );
		}

		return $return_value;
	}

	/**
	 * Load assets into the admin.
	 *
	 * @param string $hook Hook representing the current page.
	 */
	public function admin_head( $hook ) {

		if ( ! in_array( $hook, array( 'toplevel_page_solr-power', 'solr-options_page_solr-power-facet', 'solr-options_page_solr-power-index' ), true ) ) {
			return;
		}
		$style_path = 'assets/css/admin.min.css';
		$mtime      = filemtime( SOLR_POWER_PATH . '/' . $style_path );
		wp_enqueue_style( 'solr-admin-css', add_query_arg( 'mtime', $mtime, SOLR_POWER_URL . $style_path ) );
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$script_path = 'assets/js/src/admin.js';
		} else {
			$script_path = 'assets/js/admin.min.js';
		}
		$mtime = filemtime( SOLR_POWER_PATH . '/' . $script_path );
		wp_enqueue_script( 'solr-admin-js', add_query_arg( 'mtime', $mtime, SOLR_POWER_URL . $script_path ), array( 'jquery', 'wp-util' ) );

		// include our default css.
		if ( file_exists( SOLR_POWER_PATH . '/template/search.css' ) ) {
			wp_enqueue_style( 'solr-search', SOLR_POWER_URL . 'template/search.css' );
		}
		wp_enqueue_script( 'solr-js', SOLR_POWER_URL . 'template/script.js', false );
		$solr_js = array(
			'ajax_url'   => admin_url( 'admin-ajax.php' ),

			/**
			 * Filter indexed post types
			 *
			 * Filter the list of post types available to index.
			 *
			 * @param array $post_types Array of post type names for indexing.
			 */

			'post_types' => self::get_post_types(),
			'security'   => wp_create_nonce( 'solr_security' ),
		);
		wp_localize_script( 'solr-js', 'solr', $solr_js );
	}

	/**
	 * Display a settings link on the plugins page.
	 *
	 * @param array  $links Plugin links.
	 * @param string $file  Current plugin file.
	 *
	 * @return array
	 */
	public function plugin_settings_link( $links, $file ) {

		if ( plugin_basename( SOLR_POWER_PATH . '/solr-power.php' ) !== $file ) {
			return $links;
		}

		$base_link     = is_multisite() ? network_admin_url( 'admin.php' ) : admin_url( 'admin.php' );
		$settings_link = add_query_arg( 'page', 'solr-power', $base_link );
		array_unshift( $links, '<a href="' . esc_url( $settings_link ) . '">' . esc_html__( 'Settings', 'solr-for-wordpress-on-pantheon' ) . '</a>' );

		return $links;
	}

	/**
	 * Load autosuggest scripts into the head.
	 */
	public function autosuggest_head() {
		if ( file_exists( SOLR_POWER_PATH . '/template/autocomplete.css' ) ) {
			wp_enqueue_style( 'solr-autocomplete', SOLR_POWER_URL . 'template/autocomplete.css' );
		}
		wp_enqueue_script( 'solr-suggest', SOLR_POWER_URL . 'template/autocomplete.js', false );
	}

	/**
	 * Load the custom search template if necessary
	 */
	public function template_redirect() {
		wp_enqueue_script( 'suggest' );

		// not a search page; don't do anything and return
		// thanks to the Better Search plugin for the idea:  http://wordpress.org/extend/plugins/better-search/.
		$search = filter_input( INPUT_GET, 'ssearch', FILTER_SANITIZE_STRING );
		$method = filter_input( INPUT_GET, 'method', FILTER_SANITIZE_STRING );
		if ( ( $search || $method ) === false ) {
			return;
		}

		if ( 'autocomplete' === $method ) {
			$q     = filter_input( INPUT_GET, 'q', FILTER_SANITIZE_STRING );
			$limit = filter_input( INPUT_GET, 'limit', FILTER_SANITIZE_STRING );

			$this->autocomplete( $q, $limit );
			exit;
		}

		// If there is a template file then we use it.
		if ( file_exists( TEMPLATEPATH . '/s4wp_search.php' ) ) {
			// use theme file.
			include_once( TEMPLATEPATH . '/s4wp_search.php' );
		} elseif ( file_exists( dirname( __FILE__ ) . '/template/s4wp_search.php' ) ) {
			// use plugin supplied file.
			add_action( 'wp_head', array( $this, 'default_head' ) );
			include_once( dirname( __FILE__ ) . '/template/s4wp_search.php' );
		} else {
			// no template files found, just continue on like normal
			// this should get to the normal WordPress search results.
			return;
		}

		exit;
	}

	/**
	 * Return autocomplete results based on some query.
	 *
	 * @param string  $q     Search query.
	 * @param integer $limit Number of results to return.
	 */
	public function autocomplete( $q, $limit ) {
		$solr     = get_solr();
		$response = null;

		if ( ! $solr ) {
			return;
		}

		$query = $solr->createTerms();
		$query->setFields( 'spell' );
		$query->setPrefix( $q );
		$query->setLowerbound( $q );
		$query->setLowerboundInclude( false );
		$query->setLimit( $limit );

		$response = $solr->terms( $query );
		if ( ! $response->getResponse()->getStatusCode() === 200 ) {
			return;
		}
		$terms = $response->getResults();
		foreach ( $terms['spell'] as $term => $count ) {
			printf( "%s\n", esc_attr( $term ) );
		}
	}

	/**
	 * Include default css when using the search template.
	 */
	public function default_head() {
		if ( file_exists( dirname( __FILE__ ) . '/template/search.css' ) ) {
			wp_enqueue_style( 'solr-search', plugins_url( '/template/search.css', __FILE__ ) );
		}
	}

	/**
	 * Register a debug bar panel.
	 *
	 * @param array $panels Existing panels.
	 * @return array
	 */
	public function add_panel( $panels ) {
		require_once( SOLR_POWER_PATH . '/includes/class-solrpower-debug.php' );
		array_push( $panels, new SolrPower_Debug() );

		return $panels;
	}

	/**
	 * Enqueue and localize scripts.
	 */
	public function add_scripts() {
		if ( ! is_search() ) {
			return;
		}
		wp_enqueue_script( 'Solr_Facet', SOLR_POWER_URL . 'assets/js/facet.min.js', array( 'jquery' ) );
		$solr_options = solr_options();
		$allow_ajax   = isset( $solr_options['allow_ajax'] ) ? boolval( $solr_options['allow_ajax'] ) : false;
		$div_id       = isset( $solr_options['ajax_div_id'] ) ? esc_html( $solr_options['ajax_div_id'] ) : false;
		wp_localize_script(
			'Solr_Facet',
			'solr',
			array(
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'allow_ajax'        => $allow_ajax,
				'search_results_id' => $div_id,
			)
		);

		wp_enqueue_style( 'Solr_Facet', SOLR_POWER_URL . 'assets/css/facet.css' );
	}

	/**
	 * AJAX Callback for Facet Search
	 */
	public function ajax_search() {
		// Strip out admin-ajax from pagination links.
		add_filter(
			'paginate_links',
			function ( $url ) {
				$url = str_replace( 'wp-admin/admin-ajax.php', '', $url );
				$url = remove_query_arg( 'action', $url );

				return $url;
			}
		);

		// Add __return_true to the admin/ajax filters so the widget can access admin ajax.
		add_filter( 'solr_allow_ajax', '__return_true' );
		add_filter( 'solr_allow_admin', '__return_true' );

		// Ensure Solr is set to filter the query properly.
		SolrPower_WP_Query::get_instance()->setup();

		$paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_STRING );
		$paged = ( false === $paged || null === $paged ) ? 1 : absint( $paged );

		$args  = array(
			's'              => filter_input( INPUT_GET, 's', FILTER_SANITIZE_STRING ),
			'facets'         => filter_input( INPUT_GET, 'facet', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY ),
			'posts_per_page' => get_option( 'posts_per_page' ),
			'paged'          => $paged,
		);
		$args  = apply_filters( 'solr_power_ajax_search_query_args', $args );
		$query = new WP_Query( $args );
		$query->get_posts();

		$template_name = apply_filters( 'solr_power_search_template', 'solr-search-results.php' );
		$template_dir  = apply_filters( 'solr_power_search_tempplate_dir', 'templates' );
		if ( ! empty( $template_dir ) && false !== $template_dir ) {
			$template_dir = trailingslashit( $template_dir );
		}
		$template_path = $template_dir . $template_name;
		$template_file = false;

		// check the child theme first.
		$maybe_child_theme = trailingslashit( get_stylesheet_directory() ) . $template_path;
		if ( file_exists( $maybe_child_theme ) ) {
			$template_file = $maybe_child_theme;
		}

		// check parent theme.
		if ( false === $template_file ) {
			$maybe_parent_theme = trailingslashit( get_template_directory() ) . $template_path;
			if ( file_exists( $maybe_parent_theme ) ) {
				$template_file = $maybe_parent_theme;
			}
		}

		ob_start();
		if ( false === $template_file ) {
			include trailingslashit( SOLR_POWER_PATH ) . $template_path;
		} else {
			include( $template_file );
		}
		$the_posts    = ob_get_clean();
		$facet_widget = new SolrPower_Facet_Widget();

		$return = array(
			'posts'  => $the_posts,
			'facets' => $facet_widget->fetch_facets( false ),
		);

		echo wp_json_encode( $return );

		// Remove __return_true from the admin/ajax filters so they don't impact other areas.
		remove_filter( 'solr_allow_ajax', '__return_true' );
		remove_filter( 'solr_allow_admin', '__return_true' );

		wp_die();
	}

	/**
	 * Returns the post types used for Solr after applying the solr_post_types filter
	 *
	 * @return array
	 */
	public static function get_post_types() {
		return apply_filters(
			'solr_post_types',
			get_post_types(
				array(
					'exclude_from_search' => false,
				)
			)
		);
	}

	/**
	 * Returns the post statuses used for Solr after applying the solr_post_status filter
	 *
	 * @return array
	 */
	public static function get_post_statuses() {
		return apply_filters( 'solr_post_status', array( 'publish' ) );
	}

}
