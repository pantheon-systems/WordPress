<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'paginator/class-vc-pageable.php' );
require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-btn.php' );

class WPBakeryShortCode_VC_Basic_Grid extends WPBakeryShortCode_Vc_Pageable {
	public $pagable_type = 'grid';
	public $items = array();
	public static $excluded_ids = array();
	protected $element_template = '';
	protected static $default_max_items = 1000;
	public $post_id = false;
	/** @var \Vc_Grid_Item $grid_item */
	public $grid_item = false;
	protected $filter_terms;
	public $attributes_defaults = array(
		'initial_loading_animation' => 'zoomIn',
		'full_width' => '',
		'layout' => '',
		'element_width' => '4',
		'items_per_page' => '5',
		'gap' => '',
		'style' => 'all',
		'show_filter' => '',
		'filter_default_title' => 'all',
		'exclude_filter' => '',
		'filter_style' => '',
		'filter_size' => 'md',
		'filter_align' => '',
		'filter_color' => '',
		'arrows_design' => '',
		'arrows_position' => '',
		'arrows_color' => '',
		'paging_design' => '',
		'paging_color' => '',
		'paging_animation_in' => '',
		'paging_animation_out' => '',
		'loop' => '',
		'autoplay' => '',
		'post_type' => 'post',
		'filter_source' => 'category',
		'orderby' => '',
		'order' => 'DESC',
		'meta_key' => '',
		'max_items' => '10',
		'offset' => '0',
		'taxonomies' => '',
		'custom_query' => '',
		'data_type' => 'query',
		'include' => '',
		'exclude' => '',
		'item' => 'none',
		'grid_id' => '',
		// disabled, needed for-BC:
		'button_style' => '',
		'button_color' => '',
		'button_size' => '',
		// New button3:
		'btn_title' => '',
		'btn_style' => 'modern',
		'btn_el_id' => '',
		'btn_custom_background' => '#ededed',
		'btn_custom_text' => '#666',
		'btn_outline_custom_color' => '#666',
		'btn_outline_custom_hover_background' => '#666',
		'btn_outline_custom_hover_text' => '#fff',
		'btn_shape' => 'rounded',
		'btn_color' => 'blue',
		'btn_size' => 'md',
		'btn_align' => 'inline',
		'btn_button_block' => '',
		'btn_add_icon' => '',
		'btn_i_align' => 'left',
		'btn_i_type' => 'fontawesome',
		'btn_i_icon_fontawesome' => 'fa fa-adjust',
		'btn_i_icon_openiconic' => 'vc-oi vc-oi-dial',
		'btn_i_icon_typicons' => 'typcn typcn-adjust-brightness',
		'btn_i_icon_entypo' => 'entypo-icon entypo-icon-note',
		'btn_i_icon_linecons' => 'vc_li vc_li-heart',
		'btn_i_icon_pixelicons' => 'vc_pixel_icon vc_pixel_icon-alert',
		'btn_custom_onclick' => '',
		'btn_custom_onclick_code' => '',
		// fix template
		'page_id' => '',
	);
	protected $grid_settings = array();
	protected $grid_id_unique_name = 'vc_gid'; // if you change this also change in hook-vc-grid.php

	/**
	 * @var \WP_Query
	 */
	protected $query;

	function __construct( $settings ) {
		parent::__construct( $settings );
		$this->attributes_defaults['btn_title'] = __( 'Load more', 'js_composer' );
		$this->shortcodeScripts();
	}

	public function shortcodeScripts() {
		parent::shortcodeScripts();

		wp_register_script( 'vc_grid-js-imagesloaded', vc_asset_url( 'lib/bower/imagesloaded/imagesloaded.pkgd.min.js' ) );
		wp_register_script( 'vc_grid', vc_asset_url( 'js/dist/vc_grid.min.js' ), array(
			'jquery',
			'underscore',
			'vc_pageable_owl-carousel',
			'waypoints',
			//'isotope',
			'vc_grid-js-imagesloaded',
		), WPB_VC_VERSION, true );
	}

	public function enqueueScripts() {
		parent::enqueueScripts();
		wp_enqueue_script( 'vc_grid-js-imagesloaded' );
		wp_enqueue_script( 'vc_grid' );
	}

	public static function addExcludedId( $id ) {
		self::$excluded_ids[] = $id;
	}

	public static function excludedIds() {
		return self::$excluded_ids;
	}

	/**
	 * Get shortcode hash by it content and attributes
	 *
	 * @param $atts
	 * @param $content
	 *
	 * @deprecated 4.4.3
	 * @return string
	 */
	public function getHash( $atts, $content ) {
		if ( vc_is_page_editable() || is_preview() ) {
			_deprecated_function( 'WPBakeryShortCode_VC_Basic_Grid::getHash', '4.4.3 (will be removed in 4.10)', 'getId resave your grid' );

			/* We are in Frontend editor
			 * We need to send RAW shortcode data, so hash is just json_encode of atts and content
			 */

			return urlencode( json_encode( array(
				'tag' => $this->shortcode,
				'atts' => $atts,
				'content' => $content,
			) ) );
		}

		/** Else
		 * We are in preview mode (viewing page).
		 * So hash is shortcode atts and content hash
		 */

		return sha1( serialize( array(
			'tag' => $this->shortcode,
			'atts' => $atts,
			'content' => $content,
		) ) );

	}

	public function getId( $atts, $content ) {
		if ( vc_is_page_editable() || is_preview() ) {
			/* We are in Frontend editor
			 * We need to send RAW shortcode data, so hash is just json_encode of atts and content
			 */
			return urlencode( json_encode( array(
				'tag' => $this->shortcode,
				'atts' => $atts,
				'content' => $content,
			) ) );
		}

		$id_pattern = '/' . $this->grid_id_unique_name . '\:([\w-_]+)/';

		$id_value = isset( $atts['grid_id'] ) ? $atts['grid_id'] : '';

		preg_match( $id_pattern, $id_value, $id_matches );
		$id_to_save = json_encode( array( 'failed_to_get_id' => esc_attr( $id_value ) ) );

		if ( ! empty( $id_matches ) ) {
			$id_to_save = $id_matches[1];
		}

		return $id_to_save;
	}

	/**
	 * Search in post meta vc_post_settings value
	 * For shortcode data by hash
	 *
	 * @param $page_id
	 * @param $hash
	 *
	 * @deprecated 4.4.3
	 * @return bool|array
	 */
	public function findPostShortcodeByHash( $page_id, $hash ) {
		_deprecated_function( 'WPBakeryShortCode_VC_Basic_Grid::findPostShortcodeByHash', '4.4.3 (will be removed in 5.3)', 'findPostShortcodeById resave your grid to renew' );

		if ( $hash ) {
			if ( $this->currentUserCanManage( $page_id ) && preg_match( '/\"tag\"\:/', urldecode( $hash ) ) ) {
				return json_decode( urldecode( $hash ), true ); // if frontend, no hash exists - just RAW data
			}
			$post_meta = get_post_meta( (int) $page_id, '_vc_post_settings' );
			if ( is_array( $post_meta ) ) {
				foreach ( $post_meta as $meta ) {
					if ( isset( $meta['vc_grid'] ) && ! empty( $meta['vc_grid']['shortcodes'] ) && isset( $meta['vc_grid']['shortcodes'][ $hash ] ) ) {
						return $meta['vc_grid']['shortcodes'][ $hash ];
					}
				}
			}
		}

		return false;
	}

	public function findPostShortcodeById( $page_id, $grid_id ) {
		if ( $this->currentUserCanManage( $page_id ) && preg_match( '/\"tag\"\:/', urldecode( $grid_id ) ) ) {
			return json_decode( urldecode( $grid_id ), true ); // if frontend, no hash exists - just RAW data
		}
		$post_meta = get_post_meta( (int) $page_id, '_vc_post_settings' );
		$shortcode = false;
		if ( is_array( $post_meta ) ) {
			foreach ( $post_meta as $meta ) {
				if ( isset( $meta['vc_grid_id'] ) && ! empty( $meta['vc_grid_id']['shortcodes'] ) && isset( $meta['vc_grid_id']['shortcodes'][ $grid_id ] ) ) {
					$shortcode = $meta['vc_grid_id']['shortcodes'][ $grid_id ];
					break;
				}
			}
		}

		return apply_filters( 'vc_basic_grid_find_post_shortcode', $shortcode, $page_id, $grid_id );
	}

	public function renderItems() {
		$output = $items = '';
		$this->buildGridSettings();
		$atts = $this->atts;
		$settings = $this->grid_settings;
		$filter_terms = $this->filter_terms;
		$is_end = isset( $this->is_end ) && $this->is_end;
		$css_classes = 'vc_grid vc_row' . esc_attr( $atts['gap'] > 0 ? ' vc_grid-gutter-' . (int) $atts['gap'] . 'px' : '' );
		$currentScope = WPBMap::getScope();
		if ( is_array( $this->items ) && ! empty( $this->items ) ) {
			// Adding before vc_map
			WPBMap::setScope( Vc_Grid_Item_Editor::postType() );
			require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
			$this->grid_item = new Vc_Grid_Item();
			$this->grid_item->setGridAttributes( $atts );
			$this->grid_item->setIsEnd( $is_end );
			$this->grid_item->setTemplateById( $atts['item'] );
			$output .= $this->grid_item->addShortcodesCustomCss();
			ob_start();
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				wp_print_styles();
			}
			$output .= ob_get_clean();
			$attributes = array(
				'filter_terms' => $filter_terms,
				'atts' => $atts,
				'grid_item',
				$this->grid_item,
			);
			$output .= apply_filters( 'vc_basic_grid_template_filter', vc_get_template( 'shortcodes/vc_basic_grid_filter.php', $attributes ), $attributes );
			global $post;
			foreach ( $this->items as $postItem ) {
				$this->query->setup_postdata( $postItem );
				$post = $postItem;
				$items .= $this->grid_item->renderItem( $postItem );
			}
			wp_reset_postdata();
		} else {
			return '';
		}
		$items = apply_filters( $this->shortcode . '_items_list', $items );
		$output .= $this->renderPagination( $atts['style'], $settings, $items, $css_classes );
		WPBMap::setScope( $currentScope );

		return $output;
	}

	public function setContentLimits() {
		$atts = $this->atts;
		if ( 'ids' === $this->atts['post_type'] ) {
			$this->atts['max_items'] = 0;
			$this->atts['offset'] = 0;
			$this->atts['items_per_page'] = apply_filters( 'vc_basic_grid_max_items', self::$default_max_items );
		} else {
			$this->atts['offset'] = $offset = isset( $atts['offset'] ) ? (int) $atts['offset'] : $this->attributes_defaults['offset'];
			$this->atts['max_items'] = isset( $atts['max_items'] ) ? (int) $atts['max_items'] : (int) $this->attributes_defaults['max_items'];
			$this->atts['items_per_page'] = ! isset( $atts['items_per_page'] ) ? (int) $this->attributes_defaults['items_per_page'] : (int) $atts['items_per_page'];
			if ( $this->atts['max_items'] < 1 ) {
				$this->atts['max_items'] = apply_filters( 'vc_basic_grid_max_items', self::$default_max_items );
			}
		}
		$this->setPagingAll( $this->atts['max_items'] );
	}

	protected function setPagingAll( $max_items ) {
		$atts = $this->atts;
		$this->atts['items_per_page'] = $this->atts['query_items_per_page'] = $max_items > 0 ? $max_items : apply_filters( 'vc_basic_grid_items_per_page_all_max_items', self::$default_max_items );
		$this->atts['query_offset'] = isset( $atts['offset'] ) ? (int) $atts['offset'] : $this->attributes_defaults['offset'];
	}

	public function renderAjax( $vc_request_param ) {
		$this->items = array(); // clear this items array (if used more than once);
		$id = isset( $vc_request_param['shortcode_id'] ) ? $vc_request_param['shortcode_id'] : false;
		if ( ! isset( $vc_request_param['page_id'] ) ) {
			return json_encode( array( 'status' => 'Nothing found' ) );
		}
		if ( $id ) {
			$shortcode = $this->findPostShortcodeById( $vc_request_param['page_id'], $id );
		} else {
			/**
			 * @deprecated since 4.4.3 due to invalid logic in hash algorithm
			 */
			$hash = isset( $vc_request_param['shortcode_hash'] ) ? $vc_request_param['shortcode_hash'] : false;
			$shortcode = $this->findPostShortcodeByHash( $vc_request_param['page_id'], $hash );
		}
		if ( ! is_array( $shortcode ) ) {
			return json_encode( array( 'status' => 'Nothing found' ) );
		}
		visual_composer()->registerAdminCss();
		visual_composer()->registerAdminJavascript();
		// Set post id
		$this->post_id = (int) $vc_request_param['page_id'];

		$shortcode_atts = $shortcode['atts'];
		$this->shortcode_content = $shortcode['content'];
		$this->buildAtts( $shortcode_atts, $shortcode['content'] );

		$this->buildItems();

		return $this->renderItems();
	}

	public function postID() {
		if ( false == $this->post_id ) {
			$this->post_id = get_the_ID();
		}

		return $this->post_id;
	}

	public function buildAtts( $atts, $content ) {
		$this->post_id = false;
		$this->grid_settings = array();
		$this->filter_terms = null;
		$this->items = array();
		$arr_keys = array_keys( $atts );
		for ( $i = 0; $i < count( $atts ); $i ++ ) {
			$atts[ $arr_keys[ $i ] ] = html_entity_decode( $atts[ $arr_keys[ $i ] ], ENT_QUOTES, 'utf-8' );
		}
		if ( isset( $atts['grid_id'] ) && ! empty( $atts['grid_id'] ) ) {
			$id_to_save = $this->getId( $atts, $content );
		} else {
			$hash = $this->getHash( $atts, $content );
		}

		$atts = $this->convertButton2ToButton3( $atts );
		$atts = shortcode_atts( $this->attributes_defaults, vc_map_get_attributes( $this->getShortcode(), $atts ) );
		$this->atts = $atts;
		if ( isset( $id_to_save ) ) {
			$this->atts['shortcode_id'] = $id_to_save;
		} else if ( isset( $hash ) ) {
			$this->atts['shortcode_hash'] = $hash;
		}
		$this->atts['page_id'] = $this->postID();

		$this->element_template = $content;
		// @since 4.4.3
		if ( 'custom' === $this->attr( 'post_type' ) ) {
			$this->atts['style'] = 'all';
		}
	}

	/**
	 * Getter attribute.
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function attr( $key ) {
		return isset( $this->atts[ $key ] ) ? $this->atts[ $key ] : null;
	}

	public function buildGridSettings() {
		$this->grid_settings = array(
			'page_id' => $this->atts['page_id'],
			// used in basic grid for initialization
			'style' => $this->atts['style'],
			'action' => 'vc_get_vc_grid_data',
		);
		// used in ajax request for items
		if ( isset( $this->atts['shortcode_id'] ) && ! empty( $this->atts['shortcode_id'] ) ) {
			$this->grid_settings['shortcode_id'] = $this->atts['shortcode_id'];
		} elseif ( isset( $this->atts['shortcode_hash'] ) && ! empty( $this->atts['shortcode_hash'] ) ) {
			// @deprecated since 4.4.3
			$this->grid_settings['shortcode_hash'] = $this->atts['shortcode_hash'];
		}
		if ( 'load-more' === $this->atts['style'] ) {
			$this->grid_settings = array_merge( $this->grid_settings, array(
				// used in dispaly style load more button, lazy, pagination
				'items_per_page' => $this->atts['items_per_page'],
				'btn_data' => vc_map_integrate_parse_atts( $this->shortcode, 'vc_btn', $this->atts, 'btn' . '_' ),
			) );
		} elseif ( 'lazy' === $this->atts['style'] ) {
			$this->grid_settings = array_merge( $this->grid_settings, array(
				'items_per_page' => $this->atts['items_per_page'],
			) );
		} elseif ( 'pagination' === $this->atts['style'] ) {
			$this->grid_settings = array_merge( $this->grid_settings, array(
				'items_per_page' => $this->atts['items_per_page'],
				// used in pagination style
				'auto_play' => $this->atts['autoplay'] > 0 ? true : false,
				'gap' => (int) $this->atts['gap'],
				// not used yet, but can be used in isotope..
				'speed' => (int) $this->atts['autoplay'] * 1000,
				'loop' => $this->atts['loop'],
				'animation_in' => $this->atts['paging_animation_in'],
				'animation_out' => $this->atts['paging_animation_out'],
				'arrows_design' => $this->atts['arrows_design'],
				'arrows_color' => $this->atts['arrows_color'],
				'arrows_position' => $this->atts['arrows_position'],
				'paging_design' => $this->atts['paging_design'],
				'paging_color' => $this->atts['paging_color'],
			) );
		}
		$this->grid_settings['tag'] = $this->shortcode;
	}

	// TODO: setter & getter to attributes
	public function buildQuery( $atts ) {
		// Set include & exclude
		if ( 'ids' !== $atts['post_type'] && ! empty( $atts['exclude'] ) ) {
			$atts['exclude'] .= ',' . implode( ',', $this->excludedIds() );
		} else {
			$atts['exclude'] = implode( ',', $this->excludedIds() );
		}
		if ( 'ids' !== $atts['post_type'] ) {
			$settings = array(
				'posts_per_page' => $atts['query_items_per_page'],
				'offset' => $atts['query_offset'],
				'orderby' => $atts['orderby'],
				'order' => $atts['order'],
				'meta_key' => in_array( $atts['orderby'], array(
					'meta_value',
					'meta_value_num',
				) ) ? $atts['meta_key'] : '',
				'post_type' => $atts['post_type'],
				'exclude' => $atts['exclude'],
			);
			if ( ! empty( $atts['taxonomies'] ) ) {
				$vc_taxonomies_types = get_taxonomies( array( 'public' => true ) );
				$terms = get_terms( array_keys( $vc_taxonomies_types ), array(
					'hide_empty' => false,
					'include' => $atts['taxonomies'],
				) );
				$settings['tax_query'] = array();
				$tax_queries = array(); // List of taxnonimes
				foreach ( $terms as $t ) {
					if ( ! isset( $tax_queries[ $t->taxonomy ] ) ) {
						$tax_queries[ $t->taxonomy ] = array(
							'taxonomy' => $t->taxonomy,
							'field' => 'id',
							'terms' => array( $t->term_id ),
							'relation' => 'IN',
						);
					} else {
						$tax_queries[ $t->taxonomy ]['terms'][] = $t->term_id;
					}
				}
				$settings['tax_query'] = array_values( $tax_queries );
				$settings['tax_query']['relation'] = 'OR';
			}
		} else {
			if ( empty( $atts['include'] ) ) {
				$atts['include'] = - 1;
			} elseif ( ! empty( $atts['exclude'] ) ) {
				$include = array_map( 'trim', explode( ',', $atts['include'] ) );
				$exclude = array_map( 'trim', explode( ',', $atts['exclude'] ) );
				$diff = array_diff( $include, $exclude );
				$atts['include'] = implode( ', ', $diff );
			}
			$settings = array(
				'include' => $atts['include'],
				'posts_per_page' => $atts['query_items_per_page'],
				'offset' => $atts['query_offset'],
				'post_type' => 'any',
				'orderby' => 'post__in',
			);
			$this->atts['items_per_page'] = - 1;
		}

		return $settings;
	}

	public function buildItems() {
		$this->filter_terms = $this->items = array();

		$this->query = new WP_Query();

		$this->setContentLimits();

		$this->addExcludedId( $this->postID() );
		if ( 'custom' === $this->atts['post_type'] && ! empty( $this->atts['custom_query'] ) ) {
			$query = html_entity_decode( vc_value_from_safe( $this->atts['custom_query'] ), ENT_QUOTES, 'utf-8' );
			$query = apply_filters( 'vc_basic_grid_filter_query_filters', $query, $this->atts, $this->shortcode );
			$post_data = $this->query->query( $query );
			$this->atts['items_per_page'] = - 1;
		} elseif ( false !== $this->atts['query_items_per_page'] ) {
			$settings = $this->filterQuerySettings( $this->buildQuery( $this->atts ) );
			$post_data = $this->query->query( $settings );
		} else {
			return;
		}
		if ( $this->atts['items_per_page'] > 0 && count( $post_data ) > $this->atts['items_per_page'] ) {
			$post_data = array_slice( $post_data, 0, $this->atts['items_per_page'] );
		}
		foreach ( $post_data as $post ) {
			$post->filter_terms = wp_get_object_terms( $post->ID, $this->atts['filter_source'], array( 'fields' => 'ids' ) );
			$this->filter_terms = wp_parse_args( $this->filter_terms, $post->filter_terms );
			$this->items[] = $post;
		}
	}

	public function filterQuerySettings( $args ) {
		$defaults = array(
			'numberposts' => 5,
			'offset' => 0,
			'category' => 0,
			'orderby' => 'date',
			'order' => 'DESC',
			'include' => array(),
			'exclude' => array(),
			'meta_key' => '',
			'meta_value' => '',
			'post_type' => 'post',
			'suppress_filters' => apply_filters( 'vc_basic_grid_filter_query_suppress_filters', true ),
			'public' => true,
		);

		$r = wp_parse_args( $args, $defaults );
		if ( empty( $r['post_status'] ) ) {
			$r['post_status'] = ( 'attachment' === $r['post_type'] ) ? 'inherit' : 'publish';
		}
		if ( ! empty( $r['numberposts'] ) && empty( $r['posts_per_page'] ) ) {
			$r['posts_per_page'] = $r['numberposts'];
		}
		if ( ! empty( $r['category'] ) ) {
			$r['cat'] = $r['category'];
		}
		if ( ! empty( $r['include'] ) ) {
			$incposts = wp_parse_id_list( $r['include'] );
			$r['posts_per_page'] = count( $incposts );  // only the number of posts included
			$r['post__in'] = $incposts;
		} elseif ( ! empty( $r['exclude'] ) ) {
			$r['post__not_in'] = wp_parse_id_list( $r['exclude'] );
		}

		$r['ignore_sticky_posts'] = true;
		$r['no_found_rows'] = true;

		return $r;
	}

	public static function convertButton2ToButton3( $atts ) {
		if ( isset( $atts['button_style'] ) || isset( $atts['button_size'] ) || isset( $atts['button_color'] ) ) {
			// we use old button 2 attributes:
			$style = isset( $atts['button_style'] ) ? $atts['button_style'] : 'rounded';
			$size = isset( $atts['button_size'] ) ? $atts['button_size'] : 'md';
			$color = isset( $atts['button_color'] ) ? $atts['button_color'] : 'blue';
			$oldData = array(
				'style' => $style,
				'size' => $size,
				'color' => str_replace( '_', '-', $color ),
			);
			// remove attributes on save
			$atts['button_style'] = '';
			$atts['button_size'] = '';
			$atts['button_color'] = '';
			$newData = WPBakeryShortCode_VC_Btn::convertAttributesToButton3( $oldData );
			foreach ( $newData as $key => $value ) {
				$atts[ 'btn_' . $key ] = $value;
			}
		}

		return $atts;
	}
}
