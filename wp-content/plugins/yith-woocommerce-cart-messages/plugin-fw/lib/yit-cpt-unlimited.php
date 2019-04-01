<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if (!defined('ABSPATH')) {exit('Direct access forbidden.');
}

/**
 * Manage the custom post types as Portfolio, Contact Forms and similar (called CPTU)
 *
 * @class YIT_CPT_Unlimited
 * @package	YITH
 * @since 2.0.0
 * @author Your Inspiration Themes
 *
 */

class YIT_CPT_Unlimited {

    /**
     * @var string The name of main post type of CPTU
     * @since 1.0
     */
    protected $_name = '';

    /**
     * @var string The prefix of each post type created by the post of main CPTU
     * @since 1.0
     */
    protected $_prefix_cpt = '';

    /**
     * @var string The labels defined for the main CPTU
     * @since 1.0
     */
    protected $_labels = '';

    /**
     * @var string The configuration arguments of post type
     * @since 1.0
     */
    protected $_args = '';

    /**
     * @var array All post types created by the post of main CPTU
     * @since 1.0
     */
    public $post_types = array();

    /**
     * @var array $layouts Array with all portfolio layouts available for this site
     * @since 1.0
     */
    public $layouts = array();

    /**
     * @var string $template_path The pathname of template folder
     * @since 1.0
     */
    protected $template_path = '';

    /**
     * @var string $template_url The URL of template folder
     * @since 1.0
     */
    protected $template_url = '';

    /**
     * @var int $_index Unique sequential ID to differentiate same shortcodes in the same page
     */
    public $index = 0;

    /**
     * @var string $_layout Temporary attribute to load automatically the settings for each layout
     * @since 1.0
     */
    private $_layout = '';


    /**
     * Constructor
     *
     * Accept an array of arguments to define the characteristics of CPTU to register.
     *
     * @since 1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function __construct( $args = array() ) {

        $defaults = array(
            'name'              => '',
            'post_type_prefix'  => '',
            'labels'            => array(
                'main_name' => '',
                'singular'  => '',
                'plural'    => '',
                'menu'      => ''
            ),
            'manage_layouts'    => false,
            'add_multiuploader' => false,
			'sortable'          => false,
            'has_single'        => false,
            'has_taxonomy'      => false,
            'label_item_sing'   => '',
            'label_item_plur'   => '',
            'shortcode_name'    => '',
            'shortcode_icon'    => '',   // URL or icon name from http://melchoyce.github.io/dashicons/
            'layout_option'     => '_type' // the option ID of layout metabox
        );
        $this->_args = wp_parse_args( $args, $defaults );

        // fix labels
        if ( empty( $this->_args['labels']['main_name'] ) ) {
            $this->_args['labels']['main_name'] = $this->_args['labels']['singular'];
        }
        if ( empty( $this->_args['labels']['menu'] ) ) {
            $this->_args['labels']['menu'] = $this->_args['labels']['singular'];
        }

        /* populate */
        $this->_name = $this->_args['name'];
        $this->_prefix_cpt = $this->_args['post_type_prefix'];
        $this->_labels = $this->_args['labels'];

        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_cptu_post_types' ) );

        add_action( 'save_post', array( $this, 'rewrite_flush') );

        // admin interface
        add_action( 'admin_head', array( $this, 'add_cptu_menu_item' ) );
        add_action( 'admin_init', array( $this, 'add_quick_links_metaboxes' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

        // metaboxes
        add_action( 'add_meta_boxes', array( $this, 'add_metabox_cptu' ), 2 );
        add_action( 'add_meta_boxes', array( $this, 'add_metabox_item_fields' ), 2 );

        // multiuploader
        if ( $this->_args['add_multiuploader'] ) {
            add_action( 'admin_footer', array( $this, 'add_button_multiuploader' ) );
            add_action( 'wp_ajax_yit_cptu_multiuploader', array( $this, 'post_multiuploader' ) );
        }

        // layouts
        if ( $this->_args['manage_layouts'] ) {
            // get all layouts available
            $this->get_layouts();
        }

        // single layout
        if ( $this->_args['has_single'] ) {
            add_action( 'yit_loop', array( $this, 'single_template' ) );
            add_action( 'wp', array( $this, 'single_template_config' ) );

            if ( defined('DOING_AJAX') && DOING_AJAX ) {
                add_action( 'init', array( $this, 'single_template_config' ) );
            }
        }

        // archive template
        add_action( 'wp', array( $this, 'archive_template' ) );

        // enqueue the assets of each layout
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_assets' ) );

        // add the shortcode, used to show the frontend
        if ( ! empty( $this->_args['shortcode_name'] ) ) {
            add_shortcode( $this->_args['shortcode_name'], array( &$this, 'add_shortcode' ) );
            add_filter( 'yit_shortcode_' . $this->_args['shortcode_name'] . '_icon', array( $this, 'shortcode_icon') );
            add_filter( 'yit-shortcode-plugin-init', array( $this, 'add_shortcode_to_box' ) );
        }

		// add sortable feature
		if ( $this->_args['sortable'] ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_sortable_assets' ) );
			add_action( 'wp_ajax_cpt_sort_posts', array( $this, 'sort_posts' ) );
			add_action( 'admin_init', array( $this, 'init_menu_order' ) );
			add_filter( 'pre_get_posts', array( $this, 'filter_active' ) );
			add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
            add_filter( 'get_next_post_where', array( $this, 'sorted_next_post_where' ) );
            add_filter( 'get_previous_post_where', array( $this, 'sorted_prev_post_where' ) );
            add_filter( 'get_next_post_sort', array( $this, 'sorted_next_post_sort' ) );
            add_filter( 'get_previous_post_sort', array( $this, 'sorted_prev_post_sort' ) );
		}

        // add default columns to post type table list
        add_filter( 'manage_edit-' . $this->_name . '_columns', array( $this, 'cptu_define_columns' ) );
        add_action( 'manage_' . $this->_name . '_posts_custom_column' , array( $this, 'cptu_change_columns' ), 10, 2 );

        // add required post type for wordpress importer
        add_filter( 'wp_import_post_data_raw', array( $this, 'add_importer_required_post_type' ) );
        add_filter( 'wp_import_terms', array( $this, 'add_importer_required_taxonomy' ) );
        add_action( 'wp_import_set_post_terms', array( $this, 'recount_terms_post' ), 10, 3 );

    }

	/**
	 * Enqueue the assets for the sortable feature
	 *
	 * @return void
	 * @since 1.0
	 * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
	 */
	public function admin_sortable_assets() {
		global $post;

		if ( ! isset( $post->post_type ) || ! $this->_is_valid( $post->post_type ) ) {
			return;
		}

		wp_enqueue_script( 'yit-cptu-sortable-posts', YIT_CORE_PLUGIN_URL . '/assets/js/yit-cptu-sortable-posts.js', array( 'jquery', 'jquery-ui-sortable' ), '1.0', true );
	}

	public function init_menu_order( $post_types = array() ) {
		global $wpdb;

		if ( empty( $post_types ) ) {
			$post_types = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_type FROM $wpdb->posts WHERE post_type LIKE %s", str_replace( '_', '\_', $this->_prefix_cpt ) . '%' ) );
		} elseif ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		foreach ( $post_types as $post_type ) {
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '{$post_type}' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future') AND menu_order = 0" );

			if ( empty( $count ) ) {
				continue;
			}

			$sql = "SELECT ID
					FROM $wpdb->posts
					WHERE post_type = '" . $post_type . "'
					AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
					ORDER BY post_date DESC
					";

			$results = $wpdb->get_results( $sql );

			foreach ( $results as $key => $result ) {
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $key + 1 ), array( 'ID' => $result->ID ) );
			}
		}
	}

	/**
	 * Save the order of posts from sortable feature
	 *
	 * @return void
	 * @since 1.0
	 * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
	 */
	public function sort_posts() {
		global $wpdb;

		parse_str( $_REQUEST['order'], $data );

		if ( is_array( $data ) ) {
			//$this->init_menu_order( $_REQUEST['post_type'] );

			$id_arr = array( );
			foreach ( $data as $key => $values ) {
				foreach ( $values as $position => $id ) {
					$id_arr[] = $id;
				}
			}


			$menu_order_arr = array( );
			foreach ( $id_arr as $key => $id ) {
				$results = $wpdb->get_results( "SELECT menu_order FROM $wpdb->posts WHERE ID = " . $id );
				foreach ( $results as $result ) {
					$menu_order_arr[] = $result->menu_order;
				}
			}

			sort( $menu_order_arr );

			foreach ( $data as $key => $values ) {
				foreach ( $values as $position => $id ) {
					$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_order_arr[$position] ), array( 'ID' => $id ) );
				}
			}
		}

		die();
	}

	public function filter_active( $wp_query ) {
		if ( is_admin() && isset( $wp_query->query['suppress_filters'] ) )
			$wp_query->query['suppress_filters'] = false;
		if ( is_admin() && isset( $wp_query->query_vars['suppress_filters'] ) )
			$wp_query->query_vars['suppress_filters'] = false;
		return $wp_query;
	}

	public function pre_get_posts( $wp_query ) {
		if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
			if ( isset( $wp_query->query['post_type'] ) ) {
				$post_types = (array) $wp_query->query['post_type'];
				foreach ( $post_types as $post_type ) {
					if ( $this->_is_valid( $post_type ) ) {
						$wp_query->set( 'orderby', 'menu_order' );
						$wp_query->set( 'order', 'ASC' );
					}
				}
			}

		} else {

			$active = false;

			if ( isset( $wp_query->query['suppress_filters'] ) || isset( $wp_query->query['post_type'] ) ) {
				$post_types = (array) $wp_query->query['post_type'];
				foreach ( $post_types as $post_type ) {
					if ( $this->_is_valid( $post_type ) ) {
						$active = true;
					}
				}
			}

			if ( $active ) {
				if ( !isset( $wp_query->query['orderby'] ) || $wp_query->query['orderby'] == 'post_date' )
					$wp_query->set( 'orderby', 'menu_order' );
				if ( !isset( $wp_query->query['order'] ) || $wp_query->query['order'] == 'DESC' )
					$wp_query->set( 'order', 'ASC' );
			}
		}
	}

    /**
     * Filters where clause for get next post
     *
     * @param $where
     *
     * @return string
     * @since  1.0
     * @author Antonio La Rocca <antonio.larocca@yithemes.com>
     */
    public function sorted_next_post_where( $where ){
        global $post;
        if( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['post_id'] ) ){
            $post = get_post( intval( $_REQUEST['post_id'] ) );
        }
        else{
            $post = get_post();
        }

        if( ! $post || ! $this->_is_valid( $post->post_type ) ){
            return $where;
        }

        $result = str_replace( "'" . $post->post_date . "'", $post->menu_order, $where );
        $result = str_replace( 'p.post_date', 'p.menu_order', $result );

        return $result;
    }

    /**
     * Filters where clause for get prev post
     *
     * @param $where
     *
     * @return string
     * @since  1.0
     * @author Antonio La Rocca <antonio.larocca@yithemes.com>
     */
    public function sorted_prev_post_where( $where ){
        global $post;

        if( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['post_id'] ) ){
            $post = get_post( intval( $_REQUEST['post_id'] ) );
        }
        else{
            $post = get_post();
        }

        if( ! $post || ! $this->_is_valid( $post->post_type ) ){
            return $where;
        }

        $result = str_replace( "'" . $post->post_date . "'", $post->menu_order, $where );
        $result = str_replace( 'p.post_date', 'p.menu_order', $result );

        return $result;
    }

    /**
     * Filters sort clause for get next post
     *
     * @param $sort
     *
     * @return string
     * @since    1.0
     * @author   Antonio La Rocca <antonio.larocca@yithemes.com>
     */
    public function sorted_next_post_sort( $sort ){
        global $post;

        if( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['post_id'] ) ){
            $post = get_post( intval( $_REQUEST['post_id'] ) );
        }
        else{
            $post = get_post();
        }

        if( ! $post || ! $this->_is_valid( $post->post_type ) ){
            return $sort;
        }

        $result = str_replace( 'p.post_date', 'p.menu_order', $sort );
        return $result;
    }

    /**
     * Filters sort clause for get prev post
     *
     * @param $sort
     *
     * @return string
     * @since    1.0
     * @author   Antonio La Rocca <antonio.larocca@yithemes.com>
     */
    public function sorted_prev_post_sort( $sort ){
        global $post;

        if( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['post_id'] ) ){
            $post = get_post( intval( $_REQUEST['post_id'] ) );
        }
        else{
            $post = get_post();
        }

        if( ! $post || ! $this->_is_valid( $post->post_type ) ){
            return $sort;
        }

        $result = str_replace( 'p.post_date', 'p.menu_order', $sort );
        return $result;
    }

    /**
     * Register post type
     *
     * Register the post type for the creation of portfolios
     *
     * @return void
     * @since 1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function register_post_type() {
        $labels = array(
            'name'               => ucfirst( $this->_labels['main_name'] ),
            'singular_name'      => ucfirst( $this->_labels['singular'] ),
            'add_new'            => sprintf( __( 'Add %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['singular'] ) ),
            'add_new_item'       => sprintf( __( 'Add New %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['singular'] ) ),
            'edit_item'          => sprintf( __( 'Edit %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['singular'] ) ),
            'new_item'           => sprintf( __( 'New %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['singular'] ) ),
            'all_items'          => sprintf( __( 'All %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['plural'] ) ),
            'view_item'          => sprintf( __( 'View %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['singular'] ) ),
            'search_items'       => sprintf( __( 'Search %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['plural'] ) ),
            'not_found'          => sprintf( __( 'No %s found', 'yith-plugin-fw' ), ucfirst( $this->_labels['plural'] ) ),
            'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'yith-plugin-fw' ), ucfirst( $this->_labels['plural'] ) ),
            'parent_item_colon'  => '',
            'menu_name'          => ucfirst( $this->_labels['menu'] )
        );

        $args = array(
            'labels'             => apply_filters( 'yit_' . $this->_name . '_labels', $labels ),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'capability_type'    => 'post',
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title' )
        );

        if ( ! empty( $this->_args['menu_icon'] ) ) {
            $args['menu_icon'] = $this->_args['menu_icon'];
        }

        register_post_type( $this->_name, apply_filters( 'yit_' . $this->_name . '_args', $args ) );
    }

    /**
     * Retrieve the values configured inside the custom post type
     *
     * @param $post /WP_Query The post where get the arguments configured in the cpt
     *
     * @return array
     * @since 1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    protected function _cpt_args( $post ) {
        if ( ! isset( $post->ID ) ) {
            return;
        }

        $args = apply_filters( 'yit_cptu_register_post_type_args', array(
            'layout'           => get_post_meta( $post->ID, $this->_args['layout_option'],          true ),
            'rewrite'          => get_post_meta( $post->ID, '_rewrite',          true ),
            'label_singular'   => ! empty( $this->_args['label_item_sing'] ) ? $this->_args['label_item_sing'] : get_post_meta( $post->ID, '_label_singular',   true ),
            'label_plural'     => ! empty( $this->_args['label_item_plur'] ) ? $this->_args['label_item_plur'] : get_post_meta( $post->ID, '_label_plural',     true ),
            'taxonomy'         => get_post_meta( $post->ID, '_taxonomy',         true ),
            'taxonomy_rewrite' => get_post_meta( $post->ID, '_taxonomy_rewrite', true ),
        ), $this->_name, $post );

        $title = $post->post_title;

        if ( empty( $args['label_singular'] ) ) {
            $args['label_singular'] = $title;
        }

        if ( empty( $args['label_plural'] ) ) {
            $args['label_plural'] = $title;
        }

        return $args;
    }

    /**
     * Retrieve the post types created for this CPTU
     *
     * @return array The link changed
     * @since 1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function get_post_types() {
        if ( ! empty( $this->post_types ) ) {
            return $this->post_types;
        }

        $args = array(
            'post_type' => $this->_name,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        $this->post_types = get_posts( $args );

        return $this->post_types;
    }

    /**
     * Register portfolio post types
     *
     * Register the post types for each portfolio created by admin
     *
     * @return void
     * @since 1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function register_cptu_post_types() {
        $post_types = $this->get_post_types();
        $pts = array();

        foreach ( $post_types as $pt ) {

            extract( $this->_cpt_args( $pt ) );

            $name  = $pt->post_name;
            $title = $pt->post_title;

            $labels = array(
                'name'               => $title,
                'singular_name'      => $label_singular,
                'add_new'            => sprintf( __( 'Add %s', 'yith-plugin-fw' ), $label_singular ),
                'add_new_item'       => sprintf( __( 'Add New %s', 'yith-plugin-fw' ), $label_singular ),
                'edit_item'          => sprintf( __( 'Edit %s', 'yith-plugin-fw' ), $label_singular ),
                'new_item'           => sprintf( __( 'New %s', 'yith-plugin-fw' ), $label_singular ),
                'all_items'          => sprintf( __( 'All %s', 'yith-plugin-fw' ), $label_plural ),
                'view_item'          => sprintf( __( 'View %s', 'yith-plugin-fw' ), $label_singular ),
                'search_items'       => sprintf( __( 'Search %s', 'yith-plugin-fw' ), $label_plural ),
                'not_found'          => sprintf( __( 'No %s found', 'yith-plugin-fw' ), $label_plural ),
                'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'yith-plugin-fw' ), $label_plural ),
                'parent_item_colon'  => '',
                'menu_name'          => $title
            );

            $args = array(
                'labels'             => apply_filters( 'yit_' . $this->_prefix_cpt . $name . '_labels', $labels ),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => false,
                'query_var'          => true,
                'capability_type'    => 'post',
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title', 'editor', 'thumbnail' )
            );

            if ( ! $this->_args['has_single'] ) {
                $args['public'] = false;
                $args['publicly_queryable'] = false;
                $args['query_var'] = false;
            }

            if ( $this->_args['manage_layouts'] && isset($this->layouts[ $layout ]) && ! $this->layouts[ $layout ]['support']['description'] ) {
                unset( $args['supports'][1] );  // remove 'editor'
            }

            if ( ! empty( $rewrite ) ) {
                $args['rewrite'] = array( 'slug' => $rewrite );
            }

            // register post type
            $post_type = yit_avoid_duplicate( str_replace( '-', '_', substr( $this->_prefix_cpt . $name, 0, 16) ), $post_types );
            register_post_type( $post_type, apply_filters( 'yit_' . $this->_prefix_cpt . $name . '_args', $args, $pt ) );  // save the post type in post meta

            update_post_meta( $pt->ID, '_post_type', $post_type );
            $pts[] = $post_type;

            // register taxonomy
            if ( $this->_args['has_taxonomy'] && ! empty( $taxonomy ) ) {

                $labels = array(
                    'name'              => sprintf( _x( '%s Categories', 'taxonomy general name', 'yith-plugin-fw' ), $label_singular ),
                    'singular_name'     => _x( 'Category', 'taxonomy singular name', 'yith-plugin-fw' ),
                    'search_items'      => __( 'Search Categories', 'yith-plugin-fw' ),
                    'all_items'         => __( 'All Categories', 'yith-plugin-fw' ),
                    'parent_item'       => __( 'Parent Category', 'yith-plugin-fw' ),
                    'parent_item_colon' => __( 'Parent Category:', 'yith-plugin-fw' ),
                    'edit_item'         => __( 'Edit Category', 'yith-plugin-fw' ),
                    'update_item'       => __( 'Update Category', 'yith-plugin-fw' ),
                    'add_new_item'      => __( 'Add New Category', 'yith-plugin-fw' ),
                    'new_item_name'     => __( 'New Category Name', 'yith-plugin-fw' ),
                    'menu_name'         => __( 'Category', 'yith-plugin-fw' ),
                );

                $args = array(
                    'hierarchical'      => true,
                    'labels'            => $labels,
                    'show_ui'           => true,
                    'show_admin_column' => true,
                    'query_var'         => true,
                );

                if ( ! empty( $taxonomy_rewrite ) ) {
                    $args['rewrite'] = array( 'slug' => $taxonomy_rewrite );
                }

                register_taxonomy( substr( $taxonomy, 0, 32 ), $post_type, $args );

            }

        }

        wp_cache_set( 'yit_cptu_post_types', $post_types );
    }

    /**
     * Flush Rewrite Rules
     *
     * rewrite rules when a cpt unlimited is saved
     *
     * @return void
     * @since 1.0
     * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
     */

    public function rewrite_flush( $post ){

        if ( isset( $post ) && $this->_is_valid( get_post_type( intval( $post ) ) ) ) {
            flush_rewrite_rules();
        }

    }

    /**
     * Add the item for each portfolio under "Portfolios"
     *
     * @return void
     * @since 1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_cptu_menu_item() {
        global $submenu, $post, $parent_file, $pagenow;

        // get current post type
        if ( isset( $post->post_type ) ) {
            $post_type = $post->post_type;
        } else if ( isset( $_REQUEST['post_type'] ) ) {
            $post_type = $_REQUEST['post_type'];
        } else {
            return;
        }

        $item = 'edit.php?post_type=' . $this->_name;

        // add new items
        if ( strpos( $post_type, $this->_prefix_cpt ) !== false ) {
            global $wpdb;
            $portfolio = $wpdb->get_row( $wpdb->prepare( "SELECT p.* FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND pm.meta_value = %s AND p.post_type = %s", '_post_type', $post_type, $this->_name ) );

            if ( ! isset( $portfolio->ID ) ) {
                return;
            }

            $label_singular   = ! empty( $this->_args['label_item_sing'] ) ? $this->_args['label_item_sing'] : get_post_meta( $portfolio->ID, '_label_singular',   true );
            $label_plural     = ! empty( $this->_args['label_item_plur'] ) ? $this->_args['label_item_plur'] : get_post_meta( $portfolio->ID, '_label_plural',     true );

            if ( empty( $label_plural ) ) {
                $label_plural = $portfolio->post_title;
            }

            if ( empty( $label_singular ) ) {
                $label_singular = $portfolio->post_title;
            }

            $submenu[ $item ][15] = array( ucfirst( $label_plural ), 'edit_posts', 'edit.php?post_type=' . $post_type );
            $submenu[ $item ][20] = array( sprintf( __('Add %s', 'yith-plugin-fw'), ucfirst( $label_singular ) ), 'edit_posts', 'post-new.php?post_type=' . $post_type );

            global $wp_taxonomies;
            $taxonomy = get_post_meta( $portfolio->ID, '_taxonomy', true );
            if ( isset( $wp_taxonomies[ $taxonomy ] ) ) {
                $submenu[ $item ][25] = array( __('Categories', 'yith-plugin-fw'), 'edit_posts', 'edit-tags.php?taxonomy=' . $taxonomy . '&post_type=' . $post_type );
            }
        }

        // set the parent item inside the single of each post type
        if ( $pagenow == 'post.php' && isset( $_GET['post'] ) && $this->_is_valid( get_post_type( intval( $_GET['post'] ) ) ) ) {
            $parent_file = 'edit.php?post_type=' . $this->_name;
        }
    }

    /**
     * Locate folder of CPTU templates, if there isn't a layouts management
     *
     * @return string
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function template_path() {
        if ( ! empty( $this->template_path ) ) {
            return $this->template_path;
        }

        // paths
        $stylesheet_path_1 = get_stylesheet_directory() . '/theme/templates/' . $this->_name . '/';
        $stylesheet_path_2 = get_template_directory()   . '/theme/templates/' . $this->_name . '/';
        $template_path_1   = get_stylesheet_directory() . '/' . $this->_name . '/';
        $template_path_2   = get_template_directory()   . '/' . $this->_name . '/';
        $plugin_path       = $this->_args['plugin_path'] . '/templates/';

        foreach ( array( 'stylesheet_path_1', 'stylesheet_path_2', 'template_path_1', 'template_path_2', 'plugin_path' ) as $var ) {
            $path = ${$var};

            if ( file_exists( $path ) ) {
                $this->template_path = $path;
            }
        }

        return $this->template_path;
    }

    /**
     * Locate folder of CPTU templates, if there isn't a layouts management
     *
     * @return string
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function template_url() {
        if ( ! empty( $this->template_url ) ) {
            return $this->template_path;
        }

        $this->template_url = str_replace( array(
            get_stylesheet_directory(),
            get_template_directory(),
            $this->_args['plugin_path']
        ), array(
            get_stylesheet_directory_uri(),
            get_template_directory_uri(),
            $this->_args['plugin_url']
        ), $this->template_path() );

        return $this->template_url;
    }

    /**
     * Retrieve all layouts to manage by custom post type added in the site in this order:
     * 1. Child theme (if exists)
     * 2. Theme
     * 3. Plugin
     *
     * It also load the config.php file of each layout
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function get_layouts() {

        // paths
        $stylesheet_path_1 = get_stylesheet_directory() . '/theme/templates/' . $this->_name . '/';
        $stylesheet_path_2 = get_template_directory()   . '/theme/templates/' . $this->_name . '/';
        $template_path_1   = get_stylesheet_directory() . '/' . $this->_name . '/';
        $template_path_2   = get_template_directory()   . '/' . $this->_name . '/';
        $plugin_path       = $this->_args['plugin_path'] . '/templates/';

        foreach ( array( 'stylesheet_path_1', 'stylesheet_path_2', 'template_path_1', 'template_path_2', 'plugin_path' ) as $var ) {
            $path = ${$var};

            if ( file_exists( $path ) ) {
                foreach ( scandir( $path ) as $scan ) {
                    if ( ! isset( $this->layouts[$scan] ) && is_dir( $path . $scan ) && ! in_array( $scan, array( '.', '..', '.svn' ) ) && $scan[0] != '_' ) {
                        $this->layouts[$scan] = array(
                            'name' => ucfirst( str_replace( '-', ' ', $scan ) ),
                            'path' => $path . $scan,
                            'url'  => str_replace( array(
                                get_stylesheet_directory(),
                                get_template_directory(),
                                $this->_args['plugin_path']
                            ), array(
                                get_stylesheet_directory_uri(),
                                get_template_directory_uri(),
                                $this->_args['plugin_url']
                            ), $path . $scan ),
                            'css'  => array(),
                            'js'   => array(),
                            'support' => array(
                                'description' => true
                            ),
							'columns' => array()
                        );

                        // set the vars for config.php
                        $layout = $scan;
                        $this->_layout = $layout;   // temporary attribute to load automatically the configuration inside the config.php, for this layout

                        // TODO Fare in modo di caricare il file config.php soltanto quando realmente serve
                        if ( ! in_array( $scan, array( 'single' ) ) && file_exists( $path . $scan . '/config.php' ) ) {
                            include_once( $path . $scan . '/config.php' );
                        }
                    }
                }
            }

        }
    }

    /**
     * Say if you want to set description for the current layout or not. This method must be used only inside the
     * config.php file of layout
     *
     * @param $v string 'yes' or 'no'
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_description_field( $v ) {
        $this->layouts[ $this->_layout ]['support']['description'] = $v == 'yes' ? true : false;
    }

    /**
     * Add the extra fields for the specific layout type of portfolio
     *
     * @param array $fields The fields to add
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_layout_fields( $fields = array() ) {
        // change the ID
        foreach ( $fields as $id => $val ) {
            unset( $fields[ $id ] );
            $id = $this->_layout . '_' . $id;
            $fields[ $id ] = $val;
        }

        $this->layouts[ $this->_layout ]['fields'] = $fields;
    }

	/**
	 * Add fields to add to the metabox of each item of each post type created
	 *
	 * @param array $fields The fields to add
	 *
	 * @return void
	 * @since  1.0
	 * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
	 */
	public function add_item_fields( $fields = array() ) {
		// change the ID
		foreach ( $fields as $id => $val ) {
			unset( $fields[ $id ] );
			//$id = $this->_layout . '_' . $id;
			$fields[ $id ] = $val;
		}
		$this->layouts[ $this->_layout ]['item_fields'] = $fields;
	}

	/**
	 * Add columns to the table list
	 *
	 * @param array $columns The columns to add in the table list
	 *
	 * @return void
	 * @since  1.0
	 * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
	 */
	public function add_table_columns( $columns ) {
		$this->layouts[ $this->_layout ]['columns'] = $columns;
	}

    /**
     * Enqueue the css files of layout
     *
     * @param string      $handle Name of the stylesheet.
     * @param string|bool $src    Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
     * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
     * @param string|bool $ver    String specifying the stylesheet version number, if it has one. This parameter is used
     *                            to ensure that the correct version is sent to the client regardless of caching, and so
     *                            should be included if a version number is available and makes sense for the stylesheet.
     * @param string      $media  Optional. The media for which this stylesheet has been defined.
     *                            Default 'all'. Accepts 'all', 'aural', 'braille', 'handheld', 'projection', 'print',
     *                            'screen', 'tty', or 'tv'.
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
        $this->layouts[ $this->_layout ]['css'][] = compact( 'handle', 'src', 'deps', 'ver', 'media' );
    }

    /**
     * Enqueue the js files of layout
     *
     * @param string      $handle    Name of the script.
     * @param string|bool $src       Path to the script from the root directory of WordPress. Example: '/js/myscript.js'.
     * @param array       $deps      An array of registered handles this script depends on. Default empty array.
     * @param string|bool $ver       Optional. String specifying the script version number, if it has one. This parameter
     *                               is used to ensure that the correct version is sent to the client regardless of caching,
     *                               and so should be included if a version number is available and makes sense for the script.
     * @param bool        $in_footer Optional. Whether to enqueue the script before </head> or before </body>.
     *                               Default 'false'. Accepts 'false' or 'true'.
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function enqueue_script( $handle, $src = false, $deps = array(), $ver = false, $in_footer = false ) {
        $this->layouts[ $this->_layout ]['js'][] = compact( 'handle', 'src', 'deps', 'ver', 'in_footer' );
    }

    /**
     * Enqueue the assets for the frontend
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function frontend_assets() {
        global $post;

        // not single
        if ( ! is_single() || ! isset( $post->post_type ) || ! $this->_is_valid( $post->post_type ) ) {
            $posts = get_posts(array(
                'post_type' => $this->_name,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'fields' => 'ids'
            ));

            $enqueued = array();

            foreach ( $posts as $post_id ) {
                $layout = get_post_meta( $post_id, $this->_args['layout_option'], true );

                if ( in_array( $layout, array( $enqueued ) ) || ! isset( $this->layouts[ $layout ]['css'] ) ) {
                    continue;
                }

                foreach ( $this->layouts[ $layout ]['css'] as $asset ) {
                    if ( empty( $asset ) ) {
                        continue;
                    }
                    yit_enqueue_style( $asset['handle'], empty( $asset['src'] ) ? false : $this->locate_url( $layout ) . $asset['src'], $asset['deps'], $asset['ver'], $asset['media'] );
                }

                $enqueued[] = $layout;
            }
        }

        // load assets of single template
        else {
            $layout = 'single';

            if ( ! isset( $this->layouts[ $layout ]['css'] ) ) {
                return;
            }

            foreach ( $this->layouts[ $layout ]['css'] as $asset ) {
                if ( empty( $asset ) ) {
                    continue;
                }
                yit_enqueue_style( $asset['handle'], $this->locate_url( $layout ) . $asset['src'], $asset['deps'], $asset['ver'], $asset['media'] );
            }
        }

    }

    /**
     * Register Metaboxes options
     *
     * Add the metabox for the portfolio settings
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_metabox_cptu() {

        // Reorganize layouts
        if ( $this->_args['manage_layouts'] ) {
            $layouts = array();
            foreach ( $this->layouts as $layout_id => $layout ) {
                if ( 'single' == $layout_id ) {
                    continue;
                }
                $layouts[ $layout_id ] = $layout['name'];
            }

            $layouts = apply_filters( 'yit_cptu_' . $this->_name . '_layout_values', $layouts );
        }

        $single_layouts = apply_filters( 'yit_cptu_' . $this->_name . '_single_layout_values', array() );

        $metabox_args = array(
            'label'    => sprintf( __( '%s Settings', 'yith-plugin-fw' ), $this->_labels['singular'] ),
            'pages'    => $this->_name, //or array( 'post-type1', 'post-type2')
            'context'  => 'normal', //('normal', 'advanced', or 'side')
            'priority' => 'default',
            'tabs'     => array(
                'settings' => array(
                    'label'  => __( 'Settings', 'yith-plugin-fw' ),
                    'fields' => apply_filters( 'yit_cptu_fields', array(
                        'type'      => array(
                            'label' => __( 'Type', 'yith-plugin-fw' ),
                            'desc'  => sprintf( __( 'Layout for this %s' , 'yith-plugin-fw' ), strtolower( $this->_labels['singular'] ) ),
                            'type'  => 'select',
                            'options' => isset( $layouts ) ? $layouts : array(),
                            'std'   => '' ),

                        'rewrite'     => array(
                            'label' => __( 'Rewrite', 'yith-plugin-fw' ),
                            'desc'  => __( 'Univocal identification name in the URL for each product (slug from post if empty)', 'yith-plugin-fw' ),
                            'type'  => 'text',
                            'std'   => '' ),

                        'label_singular' => array(
                            'label' => __( 'Label in Singular', 'yith-plugin-fw' ),
                            'desc'  => __( 'Set a label in singular (title of portfolio if empty)', 'yith-plugin-fw' ),
                            'type'  => 'text',
                            'std'   => '' ),

                        'label_plural' => array(
                            'label' => __( 'Label in Plural', 'yith-plugin-fw' ),
                            'desc'  => __( 'Set a label in plural (title of portfolio if empty)', 'yith-plugin-fw' ),
                            'type'  => 'text',
                            'std'   => '' ),

                        'taxonomy' => array(
                            'label' => __( 'Taxonomy', 'yith-plugin-fw' ),
                            'desc'  => __( 'If you want to use categories in the portfolio, set a name for taxonomy. Name should be a slug (must not contain capital letters nor spaces) and must not be more than 32 characters long (database structure restriction).', 'yith-plugin-fw' ),
                            'type'  => 'text',
                            'std'   => '' ),

                        'taxonomy_rewrite' => array(
                            'label' => __( 'Taxonomy Rewrite', 'yith-plugin-fw' ),
                            'desc'  => __( 'Set univocal name for each category page URL.', 'yith-plugin-fw' ),
                            'type'  => 'text',
                            'std'   => '' ),

                        'single_layout' => array(
                            'label' => __( 'Single layout', 'yith-plugin-fw' ),
                            'desc'  => __( 'Layout for single page of this portfolio', 'yith-plugin-fw' ),
                            'type'  => 'select',
                            'options' => $single_layouts,
                            'std'   => '' ),
                    ) )
                )
            )

        );

        if ( ! $this->_args['has_single'] ) {
            unset( $metabox_args['tabs']['settings']['fields']['rewrite'] );
        }

        if ( ! $this->_args['has_taxonomy'] ) {
            unset( $metabox_args['tabs']['settings']['fields']['taxonomy'] );
            unset( $metabox_args['tabs']['settings']['fields']['taxonomy_rewrite'] );
        }

        if ( ! empty( $this->_args['label_item_sing'] ) ) {
            unset( $metabox_args['tabs']['settings']['fields']['label_singular'] );
        }

        if ( ! empty( $this->_args['label_item_plur'] ) ) {
            unset( $metabox_args['tabs']['settings']['fields']['label_plural'] );
        }

        if ( $this->_args['manage_layouts'] ) {

            if ( count( $layouts ) < 1 ) {
                unset( $metabox_args['tabs']['settings']['fields']['type'] );
            }

            // Layouts options
            foreach ( $this->layouts as $layout => $args ) {
                if ( ! isset( $args['fields'] ) ) {
                    continue;
                }

                // Section title
                $metabox_args['tabs']['settings']['fields'][ $layout . '_title' ] = array(
                    'desc' => $args['name'] . ' ' . __( 'layout settings', 'yith-plugin-fw' ),
                    'type' => 'title',
                    'deps' => array(
                        'ids' => '_type',
                        'values' => $layout
                    )
                );

                // Options
                foreach( $args['fields'] as $field_id => $field ) {
                    $metabox_args['tabs']['settings']['fields'][ $field_id ] = $field;
                    $metabox_args['tabs']['settings']['fields'][ $field_id ]['deps'] = array(
                        'ids' => '_type',
                        'values' => $layout
                    );
                }
            }
        }else {
            unset( $metabox_args['tabs']['settings']['fields']['type'] );
        }

        if( count( $single_layouts ) < 1 ){
            unset( $metabox_args['tabs']['settings']['fields']['single_layout'] );
        }

        // undo if tab empty
        if ( empty( $metabox_args['tabs']['settings']['fields'] ) ) {
            return;
        }

        $metabox = YIT_Metabox( $this->_name . '_cptu_settings' );
        $metabox->init( $metabox_args );
    }

    /**
     * Register Metaboxes options
     *
     * Add the metabox for the portfolio settings
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_metabox_item_fields() {
        global $pagenow, $post_type;

        // get the actual post type, to add the metabox only if necessary
        if ( $pagenow == 'post.php' && isset( $_REQUEST['post'] ) ) {
            $post_type = get_post_type( intval( $_REQUEST['post'] ) );
        }
        elseif( $pagenow == 'post.php' && isset( $_REQUEST['post_ID'] ) ){
            $post_type = get_post_type( intval( $_REQUEST['post_ID'] ) );
        }
        elseif ( $pagenow == 'post-new.php' && isset( $_REQUEST['post_type'] ) ) {
            $post_type = $_REQUEST['post_type'];
        } else {
            return;
        }

        $layout = get_post_meta( $this->_get_id_by_name( $post_type ), $this->_args['layout_option'], true );

        if ( empty( $this->layouts[ $layout ]['item_fields'] ) ) {
            return;
        }

        $metabox_args = array(
            'label'    => __( 'Settings', 'yith-plugin-fw' ),
            'pages'    => $post_type, //or array( 'post-type1', 'post-type2')
            'context'  => 'normal', //('normal', 'advanced', or 'side')
            'priority' => 'default',
            'tabs'     => array(
                'settings' => array(
                    'label'  => __( 'Settings', 'yith-plugin-fw' ),
                    'fields' => $this->layouts[ $layout ]['item_fields']
                )
            )
        );

        $metabox = YIT_Metabox( $post_type . '_item_fields' );
        $metabox->init( $metabox_args );

    }

    /**
     * Add quick links inside the editing page of CPTU and Custom Post Types
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_quick_links_metaboxes() {
        // CPTU
        add_meta_box( $this->_name . '_quick_links', __( 'Quick links', 'yith-plugin-fw' ), array( $this, 'quick_links_cptu_inner' ), $this->_name, 'side', 'high' );

        // CPTs
        $args = array(
            'post_type' => $this->_name,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );
        $post_types = get_posts( $args );

        foreach ( $post_types as $post ) {
            $post_type = get_post_meta( $post->ID, '_post_type', true );
            extract( $this->_cpt_args( $post ) );
            add_meta_box( $post->post_type . '_quick_links', __( 'Quick links', 'yith-plugin-fw' ), array( $this, 'quick_links_cpt_inner' ), $post_type, 'side', 'high' );
        }
    }

    /**
     * Link to: "View Items", inside the CPTU
     *
     * @param $post
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function quick_links_cptu_inner( $post ) {
        extract( $this->_cpt_args( $post ) );
        ?>
        <a href="<?php echo admin_url( 'edit.php?post_type=' . get_post_meta( $post->ID, '_post_type', true ) ) ?>"><?php printf( __( 'View %s', 'yith-plugin-fw' ), $label_plural ) ?></a>
        <?php
    }

    /**
     * Link to: "Edit %s", inside the CPTU
     *
     * @param $post
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function quick_links_cpt_inner( $post ) {
        $post = get_post( $this->_get_id_by_name( $post->post_type ) );
        ?>
        <a href="<?php echo admin_url( "post.php?post={$post->ID}&action=edit" ) ?>"><?php printf( __( 'Edit %s', 'yith-plugin-fw' ), $post->post_title ) ?></a>
        <?php
    }

    /**
     * Define the columns to use in the list table of main sliders post type
     *
     * @param $columns array The columns used in the list table
     *
     * @return array
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function cptu_define_columns( $columns ) {
        unset( $columns['date'] );

        $columns['actions']    = '';

        return $columns;
    }

    /**
     * Change the content of each column of the table list
     *
     * @param $column string The current column
     * @param $post_id int The current post ID
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function cptu_change_columns( $column, $post_id ) {
        $post = get_post( $post_id );
        extract( $this->_cpt_args( $post ) );

        switch ( $column ) {
            case 'actions' :
                echo '<a href="' . admin_url( "post.php?post={$post_id}&action=edit" ) . '" class="button-secondary">' . sprintf( __( 'Edit %s', 'yith-plugin-fw' ), ucfirst( $this->_labels['singular'] ) ) . '</a> ';
                echo '<a href="' . admin_url( 'edit.php?post_type=' . get_post_meta( $post_id, '_post_type', true ) ) . '" class="button-secondary">' . sprintf( __( 'View %s', 'yith-plugin-fw' ), $label_plural ) . '</a> ';
                break;
        }
    }

    /**
     * Retrieve the path of layout specified in parameter
     *
     * @param $layout
     * @param $file string The file to find
     *
     * @return bool|string
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function locate_file( $layout, $file = '' ) {
        if ( ! $this->_args['manage_layouts'] ) {
            return $this->template_path(). '/' . ( ! empty( $file ) ? $file . '.php' : '' );
        }

        if ( ! isset( $this->layouts[ $layout ] ) ) {
            $layout = 'default';
        }

        return $this->layouts[ $layout ]['path'] . '/' . ( ! empty( $file ) ? $file . '.php' : '' );
    }

    /**
     * Retrieve the URL of  layout specified in parameter
     *
     * @param $layout
     * @param $file string The file to find
     *
     * @return bool|string
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function locate_url( $layout, $file = '' ) {
        if ( ! $this->_args['manage_layouts'] ) {
            return $this->template_url();

        }

        if ( ! isset( $this->layouts[ $layout ] ) ) {
            $layout = 'default';
        }

        return $this->layouts[ $layout ]['url'] . '/' . ( ! empty( $file ) ? $file . '.php' : '' );
    }

    /**
     * Retrieve the post ID relative to the post of post type
     *
     * @param $name string
     *
     * @return mixed
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    protected function _get_id_by_name( $name ) {
        global $wpdb;
        return $wpdb->get_var( $wpdb->prepare( "SELECT pm.post_id FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND pm.meta_value = %s AND p.post_type = %s", '_post_type', $name, $this->_name ) );
    }

    /**
     * Retrieve the post_type of portfolio by portfolio name
     *
     * @param $name string
     *
     * @return mixed
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    protected function _get_post_type_by_name( $name ) {
        global $wpdb;
        return $wpdb->get_var( $wpdb->prepare( "SELECT pm.meta_value FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND p.post_name = %s AND p.post_type = %s", '_post_type', $name, $this->_name ) );
    }

    /**
     * The shortcode used to show the frontend
     *
     * @param array $atts
     * @param null $content
     *
     * @return string|null
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_shortcode( $atts, $content = null ) {
        $atts = wp_parse_args( $atts, array(
            'name' => null,
            'cat' => array(),
            'posts_per_page' => false,
            'style' => null,
        ) );

        // don't show the slider if 'name' is empty or is 'none'
        if ( empty( $atts['name'] ) || 'none' == $atts['name'] ) return null;

        // compatibility fix: remove prefix if exists in portfolio object
        if( function_exists( 'YIT_Portfolio' ) && method_exists( YIT_Portfolio(), 'is' ) && YIT_Portfolio()->is( $atts['name'] ) ){
            $atts['name'] = str_replace( YIT_Portfolio()->post_type_prefix, '', $atts['name'] );
        }

        // pass vars to template
        $atts['post_type'] = $this->_get_post_type_by_name( $atts['name'] );
        $atts['layout'] = $this->_args['manage_layouts'] ? get_post_meta( $this->_get_id_by_name( $atts['post_type'] ), $this->_args['layout_option'], true ) : '';
        extract( apply_filters( 'yit_cptu_frontend_vars', $atts, $this->_name ) );

        // add the javascript assets
        if ( $this->_args['manage_layouts'] && isset( $this->layouts[ $layout ]['js'] ) && ! empty( $this->layouts[ $layout ]['js'] ) ) {
            foreach ( $this->layouts[ $layout ]['js'] as $asset ) {
				if ( empty( $asset ) ) continue;

                if ( empty( $asset['src'] ) ) {
                    wp_enqueue_script( $asset['handle'] );
                    continue;
                }

                yit_enqueue_script( $asset['handle'], $this->locate_url( $layout ) . $asset['src'], $asset['deps'], $asset['ver'], $asset['in_footer'] );
            }
        }

        // Unique sequential index to differentiate more cpt in the same page
        ++$this->index;

        ob_start();

        include( $this->locate_file( $layout, 'markup' ) );

        return ob_get_clean();

    }

    /**
     * Shortcode icon
     *
     * Return the shortcode icone to display on shortcode panel
     *
     * @param $icon_url string Icone url found by yit_shortcode plugin
     *
     * @return string
     * @since 1.0.0
     * @author Antonino Scarfi' <antonio.scarfi@yithemes.it>
     */
    public function shortcode_icon( $icon_url ) {
        return ! empty( $this->_args['shortcode_icon'] ) ? $this->_args['shortcode_icon'] : $icon_url;
    }

    /**
     * Return an array with cptu options to shortcode panel
     *
     * All definition settings to add cptu shortcode to Yit Shortcode Panel
     *
     * @param array $shortcodes
     *
     * @return array
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_shortcode_to_box( $shortcodes ) {
        $post_types = array();

        foreach ( $this->get_post_types() as $post ) {
            $post_types[ $post->post_name ] = $post->post_title;
        }

        $args = array(
            $this->_args['shortcode_name'] => array(
                'title'       => $this->_labels['singular'],
                'description' => sprintf( __( 'Show frontend of the %s', 'yith-plugin-fw' ), $this->_labels['main_name'] ),
                'tab'         => 'cpt',
                'create'      => false,
                'has_content' => false,
                'in_visual_composer' => true,
                'attributes'  => array(
                    'name'        => array(
                        'title'   => __( 'Name', 'yith-plugin-fw' ),
                        'type'    => 'select',
                        'options' => $post_types,
                        'std'     => ''
                    ),
                )
            )
        );

        return array_merge( $shortcodes, $args );
    }

    /**
     * Check the post type passed in parameter, if is generated by this CPTU
     *
     * @param $post_type string The post type to check
     *
     * @return bool
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    protected function _is_valid( $post_type ) {
        return (bool)( strpos( $post_type, $this->_args['post_type_prefix'] ) !== false );
    }

    /**
     * Add as a valid post type all cptu when importing dummy data
     *
     * @param $post array The post object
     *
     * @return array
     * @since  1.0
     * @author Antonio La Rocca <antonio.larocca@yithemes.com>
    */
    public function add_importer_required_post_type( $post ){
        global $wp_post_types, $wp_taxonomies;

        if( strpos( $post['post_type'], $this->_prefix_cpt ) === FALSE ){
            return $post;
        }

        if( ! isset( $wp_post_types[ $post['post_type'] ] ) ){
            $wp_post_types[ $post['post_type'] ] = array(
                'name' => ''
            );
        }

        if( ! empty( $post['terms'] ) ){
            foreach( $post['terms'] as $term ){
                if( ! isset( $wp_taxonomies[ $term['domain'] ] ) ){
                    $wp_taxonomies[ $term['domain'] ] = array(
                        'name' => ''
                    );
                }
            }
        }


        return $post;
    }

    /**
     * Add taxonomy when importing dummy data
     *
     * @param $terms array Array of terms
     *
     * @return array
     * @since 1.0
     * @author Antonio La Rocca <antonio.larocca@yithemes.com>
    */
    public function add_importer_required_taxonomy( $terms ){
        global $wp_taxonomies;

        if( ! empty( $terms ) ){
            foreach ( $terms as $term ) {
                if( isset( $term['domain'] ) &&  ! isset( $wp_taxonomies[ $term['domain'] ] ) ){
                    $wp_taxonomies[ $term['domain'] ] = array(
                        'name' => ''
                    );
                }
            }
        }

        return $terms;
    }

    /**
     * Force terms recount for imported taxonomy
     *
     * @param $tt_ids array Terms ids
     * @param $ids array Post ids
     * @param $tax string Taxonomy name
     *
     * @return void
     * @since  1.0
     * @author Antonio La Rocca <antonio.larocca@yithemes.com>
    */
    public function recount_terms_post( $tt_ids, $ids, $tax ){
        wp_update_term_count( $tt_ids, $tax );
    }

    // ### ASSETS ###

    /**
     * Enqueue the assets for the admin
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function admin_assets() {
        wp_enqueue_media();
        wp_enqueue_script( 'yit-cptu', YIT_CORE_PLUGIN_URL . '/assets/js/yit-cpt-unlimited.js', array('jquery'), '', true );
    }

    /**
     * Add the button to the top of the list table page of CPTU
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function add_button_multiuploader() {
        global $pagenow, $post_type, $wpdb;

        if( $pagenow != 'edit.php' ){
            return;
        }

        $cptu = $wpdb->get_var( $wpdb->prepare( "SELECT p.post_type FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND pm.meta_value = %s", '_post_type', $post_type ) );

        $post = get_post( $this->_get_id_by_name( $post_type ) );
        if ( empty( $post ) ) {
            return;
        }
        extract( $this->_cpt_args( $post ) );

        if ( $cptu != $this->_name || ! $this->_is_valid( $post_type ) ) {
            return;
        }
        ?>
        <script>
            (function($) {
                "use strict";
                // Author code here

                var button = $('<a />', {
                    href: '#',
                    class: 'multi-uploader add-new-h2',
                    'data-uploader_title': '<?php printf( __( 'Add %s from images', 'yith-plugin-fw' ), $label_plural ) ?>',
                    'data-uploader_button_text': '<?php printf( __( 'Add %s', 'yith-plugin-fw' ), $label_plural ) ?>'
                }).text('<?php _e( 'Upload multiple files', 'yith-plugin-fw' ) ?>');

                var spinner = $('<span />', {
                    class: 'spinner',
                    style: 'float: none;'
                });

                button.appendTo('.wrap h2, .wrap h1').after(spinner);

            })(jQuery);
        </script>
        <?php
    }

    /**
     * Add more posts by multiupload
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function post_multiuploader() {
        if ( ! isset( $_REQUEST['images'] ) || ! isset( $_REQUEST['post_type'] ) && $this->_is_valid( $_REQUEST['post_type'] ) ) {
            return;
        }

        foreach ( $_REQUEST['images'] as $the ) {

            // Create post object
            $arg = array(
                'post_title' => $the['title'],
                'post_type'  => $_REQUEST['post_type']
            );
            $post_id = wp_insert_post( $arg );

            set_post_thumbnail( $post_id, $the['id'] );

        }

        die();
    }


    // ###### SINGLE TEMPLATE ######

    /**
     * Load the single template file
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function single_template() {
        global $post, $wpdb;

//        if ( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['post_id'] ) ) {
//            $post = get_post( $_REQUEST['post_id'] );
//        }

        if ( ( ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ! is_single() ) || ! isset( $post->post_type ) || ! $this->_is_valid( $post->post_type ) ) {
            return;
        }

        // add the javascript assets
        if ( $this->_args['manage_layouts'] ) {
            foreach ( $this->layouts[ 'single' ]['js'] as $asset ) {
                yit_enqueue_script( $asset['handle'], $this->locate_url( 'single' ) . $asset['src'], $asset['deps'], $asset['ver'], $asset['in_footer'] );
            }
        }

        $post_name = $wpdb->get_var( $wpdb->prepare( "SELECT p.post_name FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND pm.meta_value = %s AND p.post_type = %s", '_post_type', $post->post_type, $this->_name ) );
        extract( apply_filters( 'yit_cptu_frontend_vars', array( 'name' => $post_name ), $this->_name ) );

        include( $this->locate_file( 'single', 'markup' ) );
    }

    /**
     * Load a file for the configuration of single template page of portfolio
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function single_template_config() {
        global $post, $wpdb;

        if ( defined('DOING_AJAX') && DOING_AJAX && isset( $_REQUEST['post_id'] ) ) {
            $post = get_post( $_REQUEST['post_id'] );
        }

        if ( ( ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ! is_single() ) || ! isset( $post->post_type ) || ! $this->_is_valid( $post->post_type ) ) {
            return;
        }

        $this->_layout = 'single';
        $path = $this->locate_file( 'single', 'config' );

        if ( file_exists( $path ) ) {
            $post_name = $wpdb->get_var( $wpdb->prepare( "SELECT p.post_name FROM $wpdb->postmeta AS pm INNER JOIN $wpdb->posts AS p ON p.ID = pm.post_id WHERE pm.meta_key = %s AND pm.meta_value = %s AND p.post_type = %s", '_post_type', $post->post_type, $this->_name ) );
            extract( apply_filters( 'yit_cptu_frontend_vars', array( 'name' => $post_name ), $this->_name ) );

            include( $path );
        }
    }


    // ########################## ARCHIVE TEMPLATE ###############################


    /**
     * Load the template for archive page
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function archive_template() {
        global $wp_query;


        // check if we are in archive template
         if ( !( ! is_admin() && is_archive() && isset($wp_query->post) && $this->_is_valid( $wp_query->post->post_type ) ) ) {
            return;
        }

        // remove the action from loop of theme
        remove_action( 'yit_content_loop', 'yit_content_loop', 10 );
        add_action( 'yit_content_loop', array( $this, 'archive_template_loop' ), 10 );

    }

    /**
     * Load loop for the archive template
     *
     * @return void
     * @since  1.0
     * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
     */
    public function archive_template_loop() {
        echo $this->add_shortcode( array( 'name' => $GLOBALS['wp_query']->post->post_type ) );
    }

}