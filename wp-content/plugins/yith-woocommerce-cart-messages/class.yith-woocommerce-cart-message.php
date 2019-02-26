<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWCM_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of FREE version of Yit WooCommerce Cart Messages
 *
 * @class   YWCM_Cart_Message
 * @package YITH
 * @since   1.0.0
 * @author  Your Inspiration Themes
 */
if ( ! class_exists( 'YWCM_Cart_Message' ) ) {

	/**
	 * Class YWCM_Cart_Message
	 */
	class YWCM_Cart_Message {

		/**
		 * @var object The single instance of the class
		 * @since 1.0
		 */
		protected static $_instance = null;

		/**
		 * @var string
		 */
		public $post_type_name = 'ywcm_message';


		/**
		 * Main plugin Instance
		 *
		 * @static
		 * @return object Main instance
		 *
		 * @since  1.0
		 * @author Antonino Scarfì <antonino.scarfi@yithemes.com>
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
         * Initialize plugin and registers actions and filters to be used
         *
         * @since  1.0
         * @author Emanuela Castorina
         */
        public function __construct() {

            add_action( 'init', array( $this, 'message_post_type' ), 0 );
            add_action( 'admin_menu', array( $this, 'add_submenu_woocommerce' ) );


            add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( $this, 'edit_columns' ) );
            add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
            //register metabox to cart_messages
            add_action( 'admin_init', array( $this, 'add_metabox' ), 1 );

        }


        // Register Custom Post Type
        function message_post_type() {

            $labels = array(
                'name'               => _x( 'YITH Cart Messages', 'Post Type General Name', 'yith-woocommerce-cart-messages' ),
                'singular_name'      => _x( 'YITH Cart Message', 'Post Type Singular Name', 'yith-woocommerce-cart-messages' ),
                'menu_name'          => __( 'Cart Message', 'yith-woocommerce-cart-messages' ),
                'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-cart-messages' ),
                'all_items'          => __( 'All Messages', 'yith-woocommerce-cart-messages' ),
                'view_item'          => __( 'View Messages', 'yith-woocommerce-cart-messages' ),
                'add_new_item'       => __( 'Add New Message', 'yith-woocommerce-cart-messages' ),
                'add_new'            => __( 'Add New Message', 'yith-woocommerce-cart-messages' ),
                'edit_item'          => __( 'Edit Message', 'yith-woocommerce-cart-messages' ),
                'update_item'        => __( 'Update Message', 'yith-woocommerce-cart-messages' ),
                'search_items'       => __( 'Search Message', 'yith-woocommerce-cart-messages' ),
                'not_found'          => __( 'Not found', 'yith-woocommerce-cart-messages' ),
                'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-cart-messages' ),
            );
            $args   = array(
                'label'               => __( 'ywcm_message', 'yith-woocommerce-cart-messages' ),
                'description'         => __( 'YITH Cart Message Description', 'yith-woocommerce-cart-messages' ),
                'labels'              => $labels,
                'supports'            => array( 'title' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => false,
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => false,
                'menu_position'       => 5,
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => true,
                'publicly_queryable'  => false,
                'capability_type'     => 'post',
            );
            register_post_type( $this->post_type_name, $args );

        }


        public function add_submenu_woocommerce() {
            add_submenu_page( 'woocommerce',
                __( 'YITH Cart Messages', 'yith-woocommerce-cart-messages' ),
                __( 'YITH Cart Messages', 'yith-woocommerce-cart-messages' ),
                'manage_woocommerce',
                'edit.php?post_type=' . $this->post_type_name,
                false
            );
        }
        public function  add_metabox() {

            global $pagenow;
            
            $post = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : ( isset( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : 0 );
            $post = get_post( $post );
            
            if ( ( $post && $post->post_type == $this->post_type_name ) || ( $pagenow == 'post-new.php' && isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == $this->post_type_name ) ) {
                $args = require_once( 'plugin-options/metabox/ywcm_metabox.php' );
                if ( !function_exists( 'YIT_Metabox' ) ) {
                    require_once( 'plugin-fw/yit-plugin.php' );
                }
                $metabox = YIT_Metabox( 'yit-cart-messages-info' );
                $metabox->init( $args );
            }

        }



        public function get_messages( $args = array() ) {

            $defaults = array(
                'post_type'      => $this->post_type_name,
                'post_status'    => 'publish',
                'posts_per_page' => - 1,
                'suppress_filters' => false
            );

            $args = wp_parse_args($args, $defaults);

            return apply_filters( 'ywcm_get_messages', get_posts($args), $args ) ;
        }


        function edit_columns( $columns ) {

            $columns = array(
                'cb'          => '<input type="checkbox" />',
                'title'       => __( 'Title', 'yith-woocommerce-cart-messages' ),
                'type'        => __( 'Type', 'yith-woocommerce-cart-messages' ),
                'message'     => __( 'Message', 'yith-woocommerce-cart-messages' ),
                'button_text' => __( 'Button Text', 'yith-woocommerce-cart-messages' ),
                'button_url'  => __( 'Button Url', 'yith-woocommerce-cart-messages' ),
                'date'        => __( 'Date', 'yith-woocommerce-cart-messages' ),
            );

            return $columns;
        }

        public function custom_columns( $column, $post_id ) {

            $type = get_post_meta( $post_id, '_ywcm_message_type', true );

            switch ( $column ) {
                case 'type' :
                    $types = $this->get_types();
                    if ( isset( $types[$type] ) ) {
                        echo $types[$type];
                    }
                    break;
                case 'message' :
                    $message = get_post_meta( $post_id, '_ywcm_message_' . $type . '_text', true );
                    if ( is_string( $message ) ) {
                        echo $message;
                    }
                    break;
                case 'button_text' :
                    $button_text = get_post_meta( $post_id, '_ywcm_message_button', true );
                    if ( is_string( $button_text ) ) {
                        echo $button_text;
                    }
                    break;
                case 'button_url' :
                    $button_url = get_post_meta( $post_id, '_ywcm_message_button_url', true );
                    if ( is_string( $button_url ) ) {
                        echo $button_url;
                    }
                    break;
            }
        }

        public function get_types() {
            $types = array(
                'products_cart'   => __( 'Products in Cart', 'yith-woocommerce-cart-messages' ),
                'categories_cart' => __( 'Categories in Cart', 'yith-woocommerce-cart-messages' ),
                'simple_message'  => __( 'Simple Message', 'yith-woocommerce-cart-messages' ),
            );

            if ( defined( 'YITH_YWCM_PREMIUM' ) ) {
                $types['minimum_amount'] = __( 'Minimum Amount', 'yith-woocommerce-cart-messages' );
                $types['deadline']       = __( 'Deadline', 'yith-woocommerce-cart-messages' );
                $types['referer']        = __( 'Referer', 'yith-woocommerce-cart-messages' );
            }

            return apply_filters( 'ywcm_cart_message_type', $types );
        }

    }

    /**
     * Main instance of plugin
     *
     * @return object
     * @since  1.0
     * @author Emanuela Castorina <emanuela.castorina@yithemes.it>
     */
    function YWCM_Cart_Message() {
        return YWCM_Cart_Message::instance();
    }

    /**
     * Instantiate YWCM_Cart_Message class
     *
     * @since  1.0
     * @author Emanuela Castorina <emanuela.castorina@yithemes.it>
     */


}

