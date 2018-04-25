<?php
/**
 * Class Strong_Testimonials_Order
 *
 * @since 1.16
 */
class Strong_Testimonials_Order {

	/**
	 * Strong_Testimonials_Order constructor.
	 */
	public function __construct() {}

	/**
	 * Initialize.
	 */
	public static function init() {
		self::add_actions();
	}

	/**
	 * Add actions and filters.
	 */
	public static function add_actions() {

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );

		add_action( 'load-edit.php', array( __CLASS__, 'refresh' ) );

		add_action( 'wp_ajax_update-menu-order', array( __CLASS__, 'update_menu_order' ) );

		add_action( 'pre_get_posts', array( __CLASS__, 'store_query_vars' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'pre_get_posts' ), 500 );

		add_filter( 'posts_orderby', array( __CLASS__, 'posts_orderby' ), 500, 2 );

		add_filter( 'get_previous_post_where', array( __CLASS__, 'get_previous_post_where' ) );
		add_filter( 'get_previous_post_sort', array( __CLASS__, 'get_previous_post_sort' ) );

		add_filter( 'get_next_post_where', array( __CLASS__, 'get_next_post_where' ) );
		add_filter( 'get_next_post_sort', array( __CLASS__, 'get_next_post_sort' ) );

	}

	/**
	 * Load admin scripts and styles.
	 */
	public static function load_scripts() {

		$screen = get_current_screen();
		if ( ! $screen || 'edit-wpm-testimonial' != $screen->id ) {
			return;
		}

		if ( Strong_Testimonials_Admin_list::is_column_sorted() ) {
			return;
		}

		wp_enqueue_script( 'wpmtst-admin-order-script', WPMTST_ADMIN_URL . 'js/admin-order.js', array(
			'jquery-effects-highlight',
			'jquery-ui-sortable',
		), null, true );
		//wp_localize_script( 'wpmtst-admin-order-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

		wp_enqueue_style( 'wpmtst-admin-order-style', WPMTST_ADMIN_URL . '/css/order.css', array(), null );

	}

	/**
	 * Store query vars in transient because they are not available in update_menu_order callback.
	 */
	public static function store_query_vars() {
		if ( is_admin() ) {
			set_transient( 'wpmtst_order_query',
				array(
					'paged'          => get_query_var( 'paged' ),
					'posts_per_page' => get_query_var( 'posts_per_page' ),
				),
				24 * HOUR_IN_SECONDS );
		}
	}

	/**
	 * Refresh the post list.
	 */
	public static function refresh() {

		global $wpdb;

		if ( ! wpmtst_is_testimonial_screen() ) {
			return;
		}

		$result = $wpdb->get_results( "SELECT count(*) as cnt, max(menu_order) as max, min(menu_order) as min FROM $wpdb->posts WHERE post_type = 'wpm-testimonial' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')" );

		$count = $result[0];

		// Exit if already one for one.
		if ( 0 == $count->cnt || $count->cnt == $count->max ) {
			return;
		}

		// Initial or reset
		if ( 0 == $count->min && 0 == $count->max ) {

			// Order by descending post date.
			$results = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'wpm-testimonial' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future') ORDER BY post_date DESC" );

			foreach ( $results as $key => $result ) {
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $key + 1 ), array( 'ID' => $result->ID ) );
			}

		} else {

			// Consecutive reorder with new posts at top.
			$results = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'wpm-testimonial' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future') ORDER BY menu_order ASC, post_date DESC" );

			foreach ( $results as $key => $result ) {
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $key + 1 ), array( 'ID' => $result->ID ) );
			}

		}

	}

	/**
	 * Suppress filters on testimonials.
	 *
	 * @param $query
	 */
	public static function pre_get_posts( $query ) {

		if ( is_admin() ) {
			return;
		}

		if ( ! isset( $query->query['post_type'] ) ) {
			return;
		}

		if ( is_array( $query->query['post_type'] ) ) {

			if ( ! in_array( 'wpm-testimonial', $query->query['post_type'] ) ) {
				return;
			}

		} else {

			if ( 'wpm-testimonial' != $query->query['post_type'] ) {
				return;
			}

		}

		// disable filter suppression
		if ( isset( $query->query['suppress_filters'] ) ) {
			$query->query['suppress_filters'] = false;
		}

		if ( isset( $query->query_vars['suppress_filters'] ) ) {
			$query->query_vars['suppress_filters'] = false;
		}

	}

	/**
	 * Filter sort parameter.
	 *
	 * @param $orderby
	 * @param $query
	 *
	 * @return string
	 */
	public static function posts_orderby( $orderby, $query ) {
		/**
		 * Default query sort with no parameters is post_date descending.
		 *
		 * If given a query sort parameter, e.g. sorting a column in post list
		 * table, then do nothing.
		 *
		 * If no sort parameter given, then add menu_order to default sort.
		 * (This class is not loaded if reordering is disabled in this plugin
		 * so no need to check that option before adding menu_order.)
		 */
		if ( 'wpm-testimonial' == $query->get( 'post_type' ) ) {
			if ( ! $query->get( 'orderby' ) ) {
				global $wpdb;
				$orderby = "{$wpdb->posts}.menu_order ASC, {$wpdb->posts}.post_date DESC";
			}
		}

		return $orderby;
	}

	/**
	 * Add menu order to previous post navigation.
	 *
	 * @param $where
	 *
	 * @return string
	 */
	public static function get_previous_post_where( $where ) {
		global $post;
		if ( isset( $post->post_type ) && 'wpm-testimonial' == $post->post_type ) {
			$where = "WHERE p.menu_order > '{$post->menu_order}' AND p.post_type = '{$post->post_type}' AND p.post_status = 'publish'";
		}

		return $where;
	}

	/**
	 * Add menu order to next post navigation.
	 *
	 * @param $where
	 *
	 * @return string
	 */
	public static function get_next_post_where( $where ) {
		global $post;
		if ( isset( $post->post_type ) && 'wpm-testimonial' == $post->post_type ) {
			$where = "WHERE p.menu_order < '{$post->menu_order}' AND p.post_type = '{$post->post_type}' AND p.post_status = 'publish'";
		}

		return $where;
	}

	/**
	 * Add menu order to previous post sort.
	 *
	 * @param $sort
	 *
	 * @return string
	 */
	public static function get_previous_post_sort( $sort ) {
		global $post;
		if ( isset( $post->post_type ) && 'wpm-testimonial' == $post->post_type ) {
			$sort = 'ORDER BY p.menu_order ASC, p.post_date DESC LIMIT 1';
		}

		return $sort;
	}

	/**
	 * Add menu order to next post sort.
	 *
	 * @param $sort
	 *
	 * @return string
	 */
	public static function get_next_post_sort( $sort ) {
		global $post;
		if ( isset( $post->post_type ) && 'wpm-testimonial' == $post->post_type ) {
			$sort = 'ORDER BY p.menu_order DESC, p.post_date ASC LIMIT 1';
		}

		return $sort;
	}

	/**
	 * Update menu order in back end.
	 */
	public static function update_menu_order() {
		global $wpdb;

		parse_str( $_POST['order'], $data );
		if ( ! is_array( $data ) ) {
			wp_die();
		}

		$id_arr         = $data['post'];
		$menu_order_arr = array();

		// Fetch query vars for pagination.
		$query_vars = get_transient( 'wpmtst_order_query' );
		delete_transient( 'wpmtst_order_query' );
		$paged          = $query_vars['paged'] ? $query_vars['paged'] : 1;
		$posts_per_page = $query_vars['posts_per_page'];

		// Reorder
		foreach ( $id_arr as $key => $id ) {
			$pos = ( $paged - 1 ) * $posts_per_page + ( $key + 1 );
			$wpdb->update( $wpdb->posts, array( 'menu_order' => $pos ), array( 'ID' => intval( $id ) ) );
			$menu_order_arr[] = $pos;
		}

		echo json_encode( $menu_order_arr );
		wp_die();
	}

}

Strong_Testimonials_Order::init();
