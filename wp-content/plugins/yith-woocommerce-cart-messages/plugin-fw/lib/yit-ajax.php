<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YIT_Ajax' ) ) {
    /**
     * YIT Ajax
     *
     * @class      YIT_Ajax
     * @package    YITH
     * @since      1.0
     * @author     Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YIT_Ajax {
        /**
         * @var string version of class
         */
        public $version = '1.0.0';

        /**
         * @var object The single instance of the class
         * @since 1.0
         */
        protected static $_instance = null;
        
        /**
         * get single instance
         *
         * @static
         * @return YIT_Ajax
         *
         * @since  1.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /**
         * Constructor
         *
         * @since      1.0
         * @author     Leanza Francesco <leanzafrancesco@gmail.com>
         */
        private function __construct() {
            $ajax_actions = array(
                'json_search_posts',
                'json_search_products',
                'json_search_orders',
                'json_search_terms'
            );

            foreach ( $ajax_actions as $ajax_action ) {
                add_action( 'wp_ajax_yith_plugin_fw_' . $ajax_action, array( $this, $ajax_action ) );
                add_action( 'wp_ajax_nopriv_yith_plugin_fw_' . $ajax_action, array( $this, $ajax_action ) );
            }
        }

        /**
         * Post Search
         *
         * @param bool|array $request
         */
        public function json_search_posts( $request = false ) {
            ob_start();

            if ( !$request )
                check_ajax_referer( 'search-posts', 'security' );

            $request = $request ? $request : $_REQUEST;

            $term = (string) sanitize_text_field( stripslashes( $request[ 'term' ] ) );
            if ( empty( $term ) ) {
                die();
            }

            $found_posts = array();

            $args = array(
                'post_type'        => 'post',
                'post_status'      => 'publish',
                'numberposts'      => -1,
                'orderby'          => 'title',
                'order'            => 'asc',
                'post_parent'      => 0,
                'suppress_filters' => 0,
                'include'          => '',
                'exclude'          => '',
            );

            foreach ( $args as $key => $default_value ) {
                if ( !empty( $request[ $key ] ) ) {
                    $args[ $key ] = $request[ $key ];
                }
            }

            $show_id = isset( $request[ 'show_id' ] ) && $request[ 'show_id' ];

            $args[ 's' ]      = $term;
            $args[ 'fields' ] = 'ids';

            $posts = get_posts( $args );

            if ( !empty( $posts ) ) {
                foreach ( $posts as $post_id ) {
                    if ( !current_user_can( 'read_product', $post_id ) ) {
                        continue;
                    }
                    $title                   = get_the_title( $post_id ) . ( $show_id ? " (#{$post_id})" : '' );
                    $found_posts[ $post_id ] = apply_filters( 'yith_plugin_fw_json_search_found_post_title', rawurldecode( $title ), $post_id, $request );
                }
            }
            $found_posts = apply_filters( 'yith_plugin_fw_json_search_found_posts', $found_posts, $request );

            wp_send_json( $found_posts );
        }

        /**
         * Product Search
         */
        public function json_search_products() {
            check_ajax_referer( 'search-posts', 'security' );

            $term = (string) wc_clean( stripslashes( $_REQUEST[ 'term' ] ) );
            if ( empty( $term ) ) {
                die();
            }

            $request                = $_REQUEST;
            $request[ 'post_type' ] = 'product';

            $request_include = isset( $request[ 'include' ] ) && !is_array( $request[ 'include' ] ) ? explode( ',', $request[ 'include' ] ) : array();

            if ( !empty( $request[ 'product_type' ] ) ) {
                if ( $product_type_term = get_term_by( 'slug', $request[ 'product_type' ], 'product_type' ) ) {
                    $posts_in = array_unique( (array) get_objects_in_term( $product_type_term->term_id, 'product_type' ) );
                    if ( !!$request_include )
                        $posts_in = array_intersect( $posts_in, $request_include );

                    if ( !!$posts_in ) {
                        $request[ 'include' ] = implode( ',', $posts_in );
                    } else {
                        $request[ 'include' ] = '-1';
                    }
                }
            }

            $request = apply_filters( 'yith_plugin_fw_json_search_products_request', $request );

            $this->json_search_posts( $request );
        }

        /**
         * Order Search
         */
        public function json_search_orders() {
            global $wpdb;
            ob_start();

            check_ajax_referer( 'search-posts', 'security' );

            $term = wc_clean( stripslashes( $_REQUEST[ 'term' ] ) );

            if ( empty( $term ) ) {
                die();
            }

            $found_orders = array();

            $term = apply_filters( 'yith_plugin_fw_json_search_order_number', $term );

            $query_orders = $wpdb->get_results( $wpdb->prepare( "
			SELECT ID, post_title FROM {$wpdb->posts} AS posts
			WHERE posts.post_type = 'shop_order'
			AND posts.ID LIKE %s
		", '%' . $term . '%' ) );

            if ( $query_orders ) {
                foreach ( $query_orders as $item ) {
                    $order_number              = apply_filters( 'yith_plugin_fw_order_number', '#' . $item->ID, $item->ID );
                    $found_orders[ $item->ID ] = $order_number . ' &ndash; ' . esc_html( $item->post_title );
                }
            }

            wp_send_json( $found_orders );
        }

        /**
         * Order Search
         */
        public function json_search_terms() {
            global $wpdb;
            ob_start();

            check_ajax_referer( 'search-terms', 'security' );

            $term = (string) sanitize_text_field( stripslashes( $_REQUEST[ 'term' ] ) );

            if ( empty( $term ) ) {
                die();
            }

            $request = $_REQUEST;

            $args = array(
                'taxonomy'     => 'category',
                'hide_empty'   => false,
                'order'        => 'ASC',
                'orderby'      => 'name',
                'include'      => '',
                'exclude'      => '',
                'exclude_tree' => '',
                'number'       => '',
                'hierarchical' => true,
                'child_of'     => 0,
                'parent'       => '',
                'term_field'   => 'id'
            );

            $args = apply_filters( 'yith_plugin_fw_json_search_terms_default_args', $args, $request );

            foreach ( $args as $key => $default_value ) {
                if ( !empty( $request[ $key ] ) ) {
                    $args[ $key ] = $request[ $key ];
                }
            }

            $args = apply_filters( 'yith_plugin_fw_json_search_terms_args', $args, $request );

            $args[ 'name__like' ] = $term;
            $args[ 'fields' ]     = 'id=>name';

            if ( !taxonomy_exists( $args[ 'taxonomy' ] ) )
                die();

            $terms = yith_get_terms( $args );

            if ( $args[ 'term_field' ] !== 'id' ) {
                $temp_terms = $terms;
                $terms      = array();
                foreach ( $temp_terms as $term_id => $term_name ) {
                    $current_term_field           = get_term_field( $args[ 'term_field' ], $term_id, $args[ 'taxonomy' ] );
                    $terms[ $current_term_field ] = $term_name;
                }
            }

            wp_send_json( $terms );
        }
    }
}

YIT_Ajax::instance();