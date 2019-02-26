<?php

/**
 * Class WPML_TF_Backend_Hooks
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Hooks implements IWPML_Action {

	const PAGE_HOOK = 'wpml-translation-feedback-list';

	/** @var WPML_TF_Backend_Bulk_Actions_Factory  */
	private $bulk_actions_factory;

	/** @var WPML_TF_Backend_Feedback_List_View_Factory feedback_list_view_factory */
	private $feedback_list_view_factory;

	/** @var  WPML_TF_Backend_Styles $backend_styles */
	private $backend_styles;

	/** @var WPML_TF_Backend_Scripts */
	private $backend_scripts;

	/** @var wpdb $wpdb */
	private $wpdb;

	/**
	 * WPML_TF_Backend_Hooks constructor.
	 *
	 * @param WPML_TF_Backend_Bulk_Actions_Factory       $bulk_actions_factory
	 * @param WPML_TF_Backend_Feedback_List_View_Factory $feedback_list_view_factory
	 * @param WPML_TF_Backend_Styles                     $backend_styles
	 * @param WPML_TF_Backend_Scripts                    $backend_scripts
	 * @param wpdb                                       $wpdb
	 */
	public function __construct(
		WPML_TF_Backend_Bulk_Actions_Factory $bulk_actions_factory,
		WPML_TF_Backend_Feedback_List_View_Factory $feedback_list_view_factory,
		WPML_TF_Backend_Styles $backend_styles,
		WPML_TF_Backend_Scripts $backend_scripts,
		wpdb $wpdb
	) {
		$this->bulk_actions_factory       = $bulk_actions_factory;
		$this->feedback_list_view_factory = $feedback_list_view_factory;
		$this->backend_styles             = $backend_styles;
		$this->backend_scripts            = $backend_scripts;
		$this->wpdb                       = $wpdb;
	}

	/**
	 * method add_hooks
	 */
	public function add_hooks() {
		add_action( 'wpml_admin_menu_configure', array( $this, 'add_translation_feedback_list_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_action' ) );
		add_action( 'current_screen', array( $this, 'bulk_actions_callback' ) );
		add_filter( 'posts_where', array( $this, 'maybe_exclude_rating_only_status' ), 10, 2 );
	}

	/**
	 * Define translation feedback list menu and callback
	 *
	 * @param string $menu_id
	 */
	public function add_translation_feedback_list_menu( $menu_id ) {
		if ( 'WPML' !== $menu_id ) {
			return;
		}

		if ( current_user_can( 'manage_options' ) ) {
			$menu               = array();
			$menu['order']      = 1200;
			$menu['page_title'] = __( 'Translation Feedback', 'sitepress' );
			$menu['menu_title'] = __( 'Translation Feedback', 'sitepress' );
			$menu['capability'] = 'manage_options';
			$menu['menu_slug']  = self::PAGE_HOOK;
			$menu['function']   = array( $this, 'translation_feedback_list_display' );

			do_action( 'wpml_admin_menu_register_item', $menu );

		} else {
			add_menu_page(
				__( 'Translation Feedback', 'sitepress' ),
				__( 'Translation Feedback', 'sitepress' ),
				'read',
				self::PAGE_HOOK,
				array( $this, 'translation_feedback_list_display' )
			);
		}
	}

	/**
	 * Callback to display the feedback list page
	 */
	public function translation_feedback_list_display() {
		$view = $this->feedback_list_view_factory->create();
		echo $view->render_page();
	}

	/**
	 * @param string $hook
	 */
	public function admin_enqueue_scripts_action( $hook ) {
		if ( $this->is_page_hook( $hook ) ) {
			$this->backend_styles->enqueue();
			$this->backend_scripts->enqueue();
		}
	}

	public function bulk_actions_callback( $current_screen ) {
		$hook = isset( $current_screen->id ) ? $current_screen->id : null;

		if ( $this->is_page_hook( $hook ) ) {
			$bulk_actions = $this->bulk_actions_factory->create();
			$bulk_actions->process();
		}
	}

	/**
	 * @param string $hook
	 *
	 * @return bool
	 */
	private function is_page_hook( $hook ) {
		return strpos( $hook, self::PAGE_HOOK ) !== false;
	}

	/**
	 * @param string   $where
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public function maybe_exclude_rating_only_status( $where, $query ) {
		if ( isset( $query->query_vars['exclude_tf_rating_only'] ) && $query->query_vars['exclude_tf_rating_only'] ) {
			$where .= " AND {$this->wpdb->posts}.post_status <> 'rating_only'";
		}

		return $where;
	}
}
