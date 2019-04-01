<?php

class WPML_LS_Render extends WPML_SP_User {

	const THE_CONTENT_FILTER_PRIORITY = 100;

	/* @var WPML_LS_Template $current_template */
	private $current_template;

	/* @var WPML_LS_Templates $templates */
	private $templates;

	/* @var WPML_LS_Settings $settings */
	private $settings;

	/* @var WPML_LS_Model_Build $model_build */
	private $model_build;

	/* @var WPML_LS_Inline_Styles &$inline_styles */
	private $inline_styles;

	/* @var WPML_LS_Assets $assets */
	private $assets;

	/**
	 * WPML_Language_Switcher_Menu constructor.
	 *
	 * @param WPML_LS_Templates     $templates
	 * @param WPML_LS_Settings      $settings
	 * @param WPML_LS_Model_Build   $model_build
	 * @param WPML_LS_Inline_Styles $inline_styles
	 * @param SitePress                            $sitepress
	 */
	public function __construct( $templates, $settings, $model_build, $inline_styles, $sitepress ) {
		$this->templates     = $templates;
		$this->settings      = $settings;
		$this->model_build   = $model_build;
		$this->inline_styles = $inline_styles;
		$this->assets        = new WPML_LS_Assets( $this->templates, $this->settings );
		parent::__construct( $sitepress );
	}

	public function init_hooks() {
		if ( $this->sitepress->get_setting( 'setup_complete' ) ) {

			if ( ! is_admin() ) {
				add_filter( 'wp_get_nav_menu_items', array( $this, 'wp_get_nav_menu_items_filter' ), 10, 2 );
				add_filter( 'wp_setup_nav_menu_item', array( $this, 'maybe_repair_menu_item' ), PHP_INT_MAX );
			}

			add_filter( 'the_content', array( $this, 'the_content_filter' ), self::THE_CONTENT_FILTER_PRIORITY );
			add_action( 'wp_footer', array( $this, 'wp_footer_action' ), 19 );

			$this->assets->init_hooks();
		}
	}

	/**
	 * @param WPML_LS_slot $slot
	 *
	 * @return string
	 */
	public function render( $slot ) {
		$html  = '';
		$model = array();

		if ( ! $this->is_hidden( $slot ) ) {

			$this->assets->maybe_late_enqueue_template( $slot->template() );
			$this->current_template = clone $this->templates->get_template( $slot->template() );

			if ( $slot->template_string() ) {
				$this->current_template->set_template_string( $slot->template_string() );
			}

			$model = $this->model_build->get( $slot, $this->current_template->get_template_data() );

			if ( $model['languages'] ) {
				$this->current_template->set_model( $model );
				$html = $this->current_template->get_html();
			}
		}

		return $this->filter_html( $html, $model, $slot );
	}

	/**
	 * @param string       $html
	 * @param array        $model
	 * @param WPML_LS_slot $slot
	 *
	 * @return string
	 */
	private function filter_html( $html, $model, $slot ) {
		/**
		 * @param string       $html   The HTML for the language switcher
		 * @param array        $model  The model passed to the template
		 * @param WPML_LS_slot $slot   The language switcher settings for this slot
		 */
		return apply_filters( 'wpml_ls_html', $html, $model, $slot );
	}

	/**
	 * @param WPML_LS_slot $slot
	 *
	 * @return array
	 */
	public function get_preview( $slot ) {
		$ret  = array();

		if ( $slot->is_menu() ) {
			$ret['html'] = $this->render_menu_preview( $slot );
		} else if ( $slot->is_post_translations() ) {
			$ret['html'] = $this->post_translations_label( $slot );
		} else {
			$ret['html'] = $this->render( $slot );
		}

		$ret['css']      = $this->current_template->get_styles( true );
		$ret['js']       = $this->current_template->get_scripts( true );
		$ret['styles']   = $this->inline_styles->get_slots_inline_styles( array( $slot ) );

		return $ret;
	}

	/**
	 * @param array   $items
	 * @param WP_Term $menu
	 *
	 * @return array
	 */
	public function wp_get_nav_menu_items_filter( $items, $menu ) {
		if ( $this->is_for_menu_panel_in_customizer() ) {
			return $items;
		}

		$slot = $this->settings->get_menu_settings_from_id( $menu->term_id );

		if( $slot->is_enabled() && ! $this->is_hidden( $slot ) ) {
			$is_before  = 'before' === $slot->get( 'position_in_menu' );
			$lang_items = $this->get_menu_items( $slot );

			if ( $items && $lang_items ) {
				$items = $this->merge_menu_items( $items, $lang_items, $is_before );
			} elseif ( ! $items && $lang_items ) {
				$items = $lang_items;
			}
		}

		return $items;
	}

	private function is_for_menu_panel_in_customizer() {
		return is_customize_preview() && ! did_action( 'template_redirect' );
	}

	/**
	 * @param WP_Post[]           $items
	 * @param WPML_LS_Menu_Item[] $lang_items
	 * @param bool                $is_before
	 *
	 * @return array
	 */
	private function merge_menu_items( $items, $lang_items, $is_before ) {
		if ( $is_before ) {
			$items_to_prepend = $lang_items;
			$items_to_append  = $items;
		} else {
			$items_to_prepend = $items;
			$items_to_append  = $lang_items;
		}

		$menu_orders = wp_list_pluck( $items_to_prepend, 'menu_order' );
		$offset      = max( $menu_orders );

		foreach ( $items_to_append as $item ) {
			$item->menu_order = $item->menu_order + $offset;
		}

		return array_merge( $items_to_prepend, $items_to_append );
	}

	/**
	 * @param WPML_LS_Slot $slot
	 *
	 * @return array
	 */
	private function get_menu_items( $slot ) {
		$lang_items = array();
		$model      = $this->model_build->get( $slot );

		if ( isset( $model['languages'] ) ) {

			$this->current_template = $this->templates->get_template( $slot->template() );
			$menu_order             = 1;

			foreach ( $model['languages'] as $language_model ) {
				$this->current_template->set_model( $language_model );
				$item_content = $this->filter_html( $this->current_template->get_html(), $language_model, $slot );
				$ls_menu_item = new WPML_LS_Menu_Item( $language_model, $item_content );
				$ls_menu_item->menu_order = $menu_order;
				$menu_order++;

				$lang_items[] = $ls_menu_item;
			}
		}

		return $lang_items;
	}

	/**
	 * @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-4706#comment=102-231339
	 *
	 * @param WP_Post|WPML_LS_Menu_Item|object $item
	 *
	 * @return object $item
	 */
	public function maybe_repair_menu_item( $item ) {
		if ( ! $item instanceof WPML_LS_Menu_Item ) {
			return $item;
		}

		if ( ! $item->db_id ) {
			$item->db_id = $item->ID;
		}

		return $item;
	}

	/**
	 * @param WPML_LS_Slot $slot
	 *
	 * @return string
	 */
	private function render_menu_preview( $slot ) {
		$items    = $this->get_menu_items( $slot );
		$class    = $slot->get( 'is_hierarchical' ) ? 'wpml-ls-menu-hierarchical-preview' : 'wpml-ls-menu-flat-preview';
		$defaults = array( 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '', 'theme_location' => '' );
		$defaults = (object) $defaults;
		$output   = walk_nav_menu_tree( $items, 2, $defaults );

		$dummy_item = esc_html__( 'menu items', 'sitepress' );

		if ( $slot->get( 'position_in_menu' ) === 'before' ) {
			$output .= '<li class="dummy-menu-item"><a href="#">' . $dummy_item . '...</a></li>';
		} else {
			$output = '<li class="dummy-menu-item"><a href="#">...' . $dummy_item . '</a></li>' . $output;
		}

		return '<div><ul class="wpml-ls-menu-preview ' . $class . '">' . $output . '</ul></div>';
	}

	/**
	 * @param WPML_LS_Slot $slot
	 *
	 * @return bool true if the switcher is to be hidden
	 */
	private function is_hidden( $slot ) {
		if ( ! function_exists( 'wpml_home_url_ls_hide_check' ) ) {
			require WPML_PLUGIN_PATH . '/inc/post-translation/wpml-root-page-actions.class.php';
		}

		return wpml_home_url_ls_hide_check() && ! $slot->is_shortcode_actions();
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	public function the_content_filter( $content ) {
		$post_translations = '';
		$slot              = $this->settings->get_slot( 'statics', 'post_translations' );

		if ( $slot->is_enabled() && $this->sitepress->get_wp_api()->is_singular() ) {
			$post_translations = $this->post_translations_label( $slot	);
		}

		if ( $post_translations ) {
			if ( $slot->get( 'display_before_content' ) ) {
				$content = $post_translations . $content;
			}

			if ( $slot->get( 'display_after_content' ) ) {
				$content = $content . $post_translations;
			}
		}

		return $content;
	}

	/**
	 * @param WPML_LS_Slot $slot
	 *
	 * @return mixed|string|void
	 */
	public function post_translations_label( $slot ) {
		$css_classes = $this->model_build->get_slot_css_classes( $slot );
		$html        = $this->render( $slot );
		if ( $html ) {
			$html = sprintf( $slot->get( 'availability_text' ), $html );
			$html = '<p class="' . $css_classes . '">' . $html . '</p>';


			/* @deprecated use 'wpml_ls_post_alternative_languages' instead */
			$html = apply_filters( 'icl_post_alternative_languages', $html );

			/**
			 * @param string $html
			 */
			$html = apply_filters( 'wpml_ls_post_alternative_languages', $html );
		}

		return $html;
	}

	public function wp_footer_action() {
		$slot = $this->settings->get_slot( 'statics', 'footer' );
		if( $slot->is_enabled() ) {
			echo $this->render( $slot );
		}
	}
}
