<?php

/**
 * Class WPML_Custom_Columns
 */
class WPML_Custom_Columns implements IWPML_Action {
	const COLUMN_KEY              = 'icl_translations';
	const CUSTOM_COLUMNS_PRIORITY = 1010;

	/**
	 * @param SitePress $sitepress
	 */
	private $sitepress;
	/**
	 * @var WPML_Post_Status_Display
	 */
	public $post_status_display;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_posts_management_column( $columns ) {
		$new_columns = $columns;
		$active_languages = $this->get_filtered_active_languages();
		if ( count( $active_languages ) <= 1 || 'trash' === get_query_var( 'post_status' ) ) {
			return $columns;
		}

		$current_language = $this->sitepress->get_current_language();
		unset( $active_languages[ $current_language ] );

		if ( count( $active_languages ) > 0 ) {
			$flags_column = '<span class="screen-reader-text">' . esc_html__( 'Languages', 'sitepress' ) . '</span>';
			foreach ( $active_languages as $language_data ) {
				$flags_column .= '<img src="' . esc_url( $this->sitepress->get_flag_url( $language_data['code'] ) ). '" width="18" height="12" alt="' . esc_attr( $language_data['display_name'] ) . '" title="' . esc_attr( $language_data['display_name'] ) . '" style="margin:2px" />';
			}

			$new_columns = array();
			foreach ( $columns as $column_key => $column_content ) {
				$new_columns[ $column_key ] = $column_content;
				if ( ( 'title' === $column_key || 'name' === $column_key )
				     && ! isset( $new_columns[ self::COLUMN_KEY ] ) ) {
					$new_columns[ self::COLUMN_KEY ] = $flags_column;
				}
			}
		}

		return $new_columns;
	}

	/**
	 * Add posts management column.
	 *
	 * @param $column_name
	 */
	public function add_content_for_posts_management_column( $column_name ) {
		global $post;

		if ( self::COLUMN_KEY !== $column_name ) {
			return;
		}

		$active_languages = $this->get_filtered_active_languages();
		if ( null === $this->post_status_display ) {
			$this->post_status_display = new WPML_Post_Status_Display( $active_languages );
		}
		unset( $active_languages[ $this->sitepress->get_current_language() ] );
		foreach ( $active_languages as $language_data ) {
			$icon_html = $this->post_status_display->get_status_html( $post->ID, $language_data['code'] );
			echo $icon_html;
		}
	}

	/**
	 * Check translation management column screen option.
	 *
	 * @param string $post_type Current post type.
	 *
	 * @return bool
	 */
	public function show_management_column_content( $post_type ) {
		$user = get_current_user_id();
		$hidden_columns = get_user_meta( $user, 'manageedit-' . $post_type . 'columnshidden', true );
		if ( '' === $hidden_columns ) {
			$is_visible = (bool) apply_filters( 'wpml_hide_management_column', true, $post_type );
			if ( false === $is_visible ) {
				update_user_meta( $user, 'manageedit-' . $post_type . 'columnshidden', array( self::COLUMN_KEY ) );
			}
			return $is_visible;
		}

		return ! is_array( $hidden_columns ) || ! in_array( self::COLUMN_KEY, $hidden_columns, true );
	}

	/**
	 * Get list of active languages.
	 *
	 * @return array
	 */
	private function get_filtered_active_languages() {
		$active_languages = $this->sitepress->get_active_languages();
		return apply_filters( 'wpml_active_languages_access', $active_languages, array( 'action' => 'edit' ) );
	}

	public function add_hooks() {
		add_action( 'admin_init', array(
			$this,
			'add_custom_columns_hooks'
		), self::CUSTOM_COLUMNS_PRIORITY ); // accommodate Types init@999
	}

	/**
	 * Add custom columns hooks.
	 */
	public function add_custom_columns_hooks() {
		$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : 'post';
		if (
			$post_type && $this->has_custom_columns()
			&& array_key_exists( $post_type, $this->sitepress->get_translatable_documents() )
		) {

			add_filter( 'manage_' . $post_type . '_posts_columns', array(
				$this,
				'add_posts_management_column'
			) );

			$show_management_column_content = $this->show_management_column_content( $post_type );
			if ( $show_management_column_content ) {
				if ( is_post_type_hierarchical( $post_type ) ) {
					add_action( 'manage_pages_custom_column', array(
						$this,
						'add_content_for_posts_management_column'
					) );
				}
				add_action( 'manage_posts_custom_column', array(
					$this,
					'add_content_for_posts_management_column'
				) );
			}
		}
	}

	/**
	 * Check if we need to add custom columns on page.
	 *
	 * @return bool
	 */
	private function has_custom_columns() {
		global $pagenow;
		if ( 'edit.php' === $pagenow
		     || 'edit-pages.php' === $pagenow
		     || (
			     'admin-ajax.php' === $pagenow
			     && (
				     (array_key_exists( 'action', $_POST ) && 'inline-save' === filter_var( $_POST['action'] ))
				     || (array_key_exists( 'action', $_GET ) && 'fetch-list' === filter_var( $_GET['action'] )
				     )
			     )
		     )
		) {
			return true;
		}

		return false;
	}
}
