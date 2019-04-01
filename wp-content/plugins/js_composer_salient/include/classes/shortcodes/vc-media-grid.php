<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-basic-grid.php' );

class WPBakeryShortCode_VC_Media_Grid extends WPBakeryShortCode_VC_Basic_Grid {
	public function __construct( $settings ) {
		parent::__construct( $settings );
		add_filter( $this->shortcode . '_items_list', array( $this, 'setItemsIfEmpty' ) );
	}

	protected function getFileName() {
		return 'vc_basic_grid';
	}

	protected function setPagingAll( $max_items ) {
		$this->atts['items_per_page'] = $this->atts['query_items_per_page']
			= apply_filters( 'vc_basic_grid_items_per_page_all_max_items', self::$default_max_items );
	}

	public function buildQuery( $atts ) {
		if ( empty( $atts['include'] ) ) {
			$atts['include'] = - 1;
		}
		$settings = array(
			'include' => $atts['include'],
			'posts_per_page' => apply_filters( 'vc_basic_grid_max_items', self::$default_max_items ),
			'offset' => 0,
			'post_type' => 'attachment',
			'orderby' => 'post__in',
		);

		return $settings;
	}

	public function setItemsIfEmpty( $items ) {

		if ( empty( $items ) ) {
			require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
			$grid_item = new Vc_Grid_Item();
			$grid_item->setGridAttributes( $this->atts );
			$grid_item->shortcodes();
			$item = '[vc_gitem]<img src="' . vc_asset_url( 'vc/vc_gitem_image.png' ) . '">[/vc_gitem]';
			$grid_item->parseTemplate( $item );
			$items = str_repeat( $grid_item->renderItem( get_post( (int) vc_request_param( 'vc_post_id' ) ) ), 3 );
		}

		return $items;
	}

	public function singleParamHtmlHolder( $param, $value ) {
		$output = '';
		// Compatibility fixes
		// TODO: check $old_names & &new_names. Leftover from copypasting?
		$old_names = array(
			'yellow_message',
			'blue_message',
			'green_message',
			'button_green',
			'button_grey',
			'button_yellow',
			'button_blue',
			'button_red',
			'button_orange',
		);
		$new_names = array(
			'alert-block',
			'alert-info',
			'alert-success',
			'btn-success',
			'btn',
			'btn-info',
			'btn-primary',
			'btn-danger',
			'btn-warning',
		);
		$value = str_ireplace( $old_names, $new_names, $value );
		$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
		$type = isset( $param['type'] ) ? $param['type'] : '';
		$class = isset( $param['class'] ) ? $param['class'] : '';

		if ( isset( $param['holder'] ) && 'hidden' !== $param['holder'] ) {
			$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
		}

		if ( 'include' === $param_name ) {
			$images_ids = empty( $value ) ? array() : explode( ',', trim( $value ) );
			$output .= '<ul class="attachment-thumbnails' . ( empty( $images_ids ) ? ' image-exists' : '' ) . '" data-name="' . $param_name . '">';
			foreach ( $images_ids as $image ) {
				$img = wpb_getImageBySize( array( 'attach_id' => (int) $image, 'thumb_size' => 'thumbnail' ) );
				$output .= ( $img ? '<li>' . $img['thumbnail'] . '</li>' : '<li><img width="150" height="150" test="' . $image . '" src="' . vc_asset_url( 'vc/blank.gif' ) . '" class="attachment-thumbnail" alt="" title="" /></li>' );
			}
			$output .= '</ul>';
			$output .= '<a href="#" class="column_edit_trigger' . ( ! empty( $images_ids ) ? ' image-exists' : '' ) . '">' . __( 'Add images', 'js_composer' ) . '</a>';

		}

		return $output;
	}
}
