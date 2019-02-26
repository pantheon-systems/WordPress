<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Vc_Grid_Item_Preview {
	protected $shortcodes_string = '';
	protected $post_id = false;

	public function render() {
		$this->post_id = (int) vc_request_param( 'post_id' );
		$this->shortcodes_string = stripslashes( vc_request_param( 'shortcodes_string', true ) );
		require_once vc_path_dir( 'PARAMS_DIR', 'vc_grid_item/class-vc-grid-item.php' );
		$grid_item = new Vc_Grid_Item();
		$grid_item->setIsEnd( false );
		$grid_item->setGridAttributes( array( 'element_width' => 4 ) );
		$grid_item->setTemplate( $this->shortcodes_string, $this->post_id );
		$this->enqueue();
		vc_include_template( 'params/vc_grid_item/preview.tpl.php', array(
			'preview' => $this,
			'grid_item' => $grid_item,
			'shortcodes_string' => $this->shortcodes_string,
			'post' => $this->mockingPost(),
			'default_width_value' => apply_filters( 'vc_grid_item_preview_render_default_width_value', 4 ),
		) );
	}

	public function addCssBackgroundImage( $css ) {
		if ( empty( $css ) ) {
			$css = 'background-image: url(' . vc_asset_url( 'vc/vc_gitem_image.png' ) . ') !important';
		}

		return $css;
	}

	public function addImageUrl( $url ) {
		if ( empty( $url ) ) {
			$url = vc_asset_url( 'vc/vc_gitem_image.png' );
		}

		return $url;
	}

	public function addImage( $img ) {
		if ( empty( $img ) ) {
			$img = '<img src="' . vc_asset_url( 'vc/vc_gitem_image.png' ) . '" alt="">';
		}

		return $img;
	}

	/**
	 *
	 * @since 4.5
	 *
	 * @param $link
	 *
	 * @param $atts
	 * @param $css_class
	 * @return string
	 */
	public function disableContentLink( $link, $atts, $css_class ) {
		return 'a' . ( strlen( $css_class ) > 0 ? ' class="' . esc_attr( $css_class ) . '"' : '' );
	}

	/**
	 *
	 * @since 4.5
	 *
	 * @param $link
	 *
	 * @param $atts
	 * @param $post
	 * @param $css_class
	 * @return string
	 */
	public function disableRealContentLink( $link, $atts, $post, $css_class ) {
		return 'a' . ( strlen( $css_class ) > 0 ? ' class="' . esc_attr( $css_class ) . '"' : '' );
	}

	/**
	 * Used for filter: vc_gitem_zone_image_block_link
	 * @since 4.5
	 *
	 * @param $link
	 *
	 * @return string
	 */
	public function disableGitemZoneLink( $link ) {
		return '';
	}

	public function enqueue() {
		visual_composer()->frontCss();
		visual_composer()->frontJsRegister();
		wp_enqueue_script( 'prettyphoto' );
		wp_enqueue_style( 'prettyphoto' );
		wp_enqueue_style( 'js_composer_front' );
		wp_enqueue_script( 'wpb_composer_front_js' );
		wp_enqueue_style( 'js_composer_custom_css' );

		VcShortcodeAutoloader::getInstance()->includeClass( 'WPBakeryShortCode_VC_Basic_Grid' );

		$grid = new WPBakeryShortCode_VC_Basic_Grid( array( 'base' => 'vc_basic_grid' ) );
		$grid->shortcodeScripts();
		$grid->enqueueScripts();
	}

	public function mockingPost() {
		$post = get_post( $this->post_id );
		setup_postdata( $post );
		$post->post_title = __( 'Post title', 'js_composer' );
		$post->post_content = __( 'The WordPress Excerpt is an optional summary or description of a post; in short, a post summary.', 'js_composer' );
		$post->post_excerpt = __( 'The WordPress Excerpt is an optional summary or description of a post; in short, a post summary.', 'js_composer' );
		add_filter( 'get_the_categories', array(
			$this,
			'getTheCategories',
		), 10, 2 );
		$GLOBALS['post'] = $post;

		return $post;
	}

	public function getTheCategories( $categories, $post_id ) {
		$ret = $categories;
		if ( ! $post_id || ( $post_id && $post_id == $this->post_id ) ) {
			$cat = get_categories( 'number=5' );
			if ( empty( $ret ) && ! empty( $cat ) ) {
				$ret += $cat;
			}
		}

		return $ret;
	}

	public function addPlaceholderImage( $img ) {
		if ( null === $img || false === $img ) {
			$img = array(
				'thumbnail' => '<img class="vc_img-placeholder vc_single_image-img" src="' . vc_asset_url( 'vc/vc_gitem_image.png' ) . '" />',
			);
		}

		return $img;
	}
}
