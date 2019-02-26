<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Pageable
 */
abstract class WPBakeryShortCode_Vc_Pageable extends WPBakeryShortCode {
	/**
	 * @param $settings
	 */
	public function __construct( $settings ) {
		parent::__construct( $settings );
		$this->shortcodeScripts();
	}

	/**
	 * Register scripts and styles for pager
	 */
	public function shortcodeScripts() {
		wp_register_script( 'vc_pageable_owl-carousel', vc_asset_url( 'lib/owl-carousel2-dist/owl.carousel.min.js' ), array(
			'jquery',
		), WPB_VC_VERSION, true );
		wp_register_script( 'waypoints', vc_asset_url( 'lib/waypoints/waypoints.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );

		wp_register_style( 'vc_pageable_owl-carousel-css', vc_asset_url( 'lib/owl-carousel2-dist/assets/owl.min.css' ), array(), WPB_VC_VERSION );
	}

	/**
	 * @param $grid_style
	 * @param $settings
	 * @param $content
	 *
	 * @return string
	 */
	protected function contentAll( $grid_style, $settings, $content ) {
		return '<div class="vc_pageable-slide-wrapper vc_clearfix" data-vc-grid-content="true">' . $content . '</div>';
	}

	/**
	 * @param $grid_style
	 * @param $settings
	 * @param $content
	 *
	 * @return string
	 */
	protected function contentLoadMore( $grid_style, $settings, $content ) {
		if ( ! isset( $settings['btn_data'] ) && isset( $settings['button_style'] ) && isset( $settings['button_size'] ) && isset( $settings['button_color'] ) ) {
			// BC: for those who overrided
			return '<div class="vc_pageable-slide-wrapper vc_clearfix" data-vc-grid-content="true">'
			. $content
			. '</div>'
			. '<div class="vc_pageable-load-more-btn" data-vc-grid-load-more-btn="true">'
			. do_shortcode( '[vc_button2 size="' . $settings['button_size'] . '" title="' . __( 'Load more', 'js_composer' ) . '" style="' . $settings['button_style'] . '" color="' . $settings['button_color'] . '" el_class="vc_grid-btn-load_more"]' )
			. '</div>';
		} elseif ( isset( $settings['btn_data'] ) ) {
			$data = $settings['btn_data'];
			$data['el_class'] = 'vc_grid-btn-load_more';
			$data['link'] = 'load-more-grid';
			$button3 = new WPBakeryShortCode_VC_Btn( array( 'base' => 'vc_btn' ) );

			return '<div class="vc_pageable-slide-wrapper vc_clearfix" data-vc-grid-content="true">'
			. $content
			. '</div>'
			. '<div class="vc_pageable-load-more-btn" data-vc-grid-load-more-btn="true">'
			. apply_filters( 'vc_gitem_template_attribute_' . 'vc_btn', '', array(
				'post' => new stdClass(),
				'data' => str_replace( array( '{{ vc_btn:', '}}' ), '', $button3->output( $data ) ),
			) )
			. '</div>';
		}

		return '';
	}

	/**
	 * @param $grid_style
	 * @param $settings
	 * @param $content
	 *
	 * @return string
	 */
	protected function contentLazy( $grid_style, $settings, $content ) {
		return '<div class="vc_pageable-slide-wrapper vc_clearfix" data-vc-grid-content="true">'
		. $content
		. '</div><div data-lazy-loading-btn="true" style="display: none;"><a href="' . get_permalink( $settings['page_id'] ) . '"></a></div>';
	}

	/**
	 * @param $grid_style
	 * @param $settings
	 * @param string $content
	 *
	 * @param string $css_class
	 *
	 * @return string
	 */
	public function renderPagination( $grid_style, $settings, $content = '', $css_class = '' ) {
		$css_class .= empty( $css_class ) ? '' : ' ' . 'vc_pageable-wrapper vc_hook_hover';
		$content_method = vc_camel_case( 'content-' . $grid_style );
		$content = method_exists( $this, $content_method ) ? $this->$content_method( $grid_style, $settings, $content ) : $content;

		$output = '<div class="' . esc_attr( $css_class ) . '" data-vc-pageable-content="true">'
			. $content . '</div>';

		return $output;

	}

	public function enqueueScripts() {
		wp_enqueue_script( 'vc_pageable_owl-carousel' );
		wp_enqueue_style( 'vc_pageable_owl-carousel-css' );
		wp_enqueue_style( 'animate-css' );
	}

	/**
	 * Find pager data for ajax, must be overwritten
	 *
	 * @param array $vc_request_param request param [data]
	 *
	 * @return string - rendered content for ajax
	 */
	abstract function renderAjax( $vc_request_param );

	/**
	 * Check is pageable
	 * @since 4.7.4
	 * @return bool
	 */
	public function isObjectPageable() {
		return true;
	}

	/**
	 * Check can user manage post.
	 *
	 * @param int $page_id
	 *
	 * @return bool
	 */
	public function currentUserCanManage( $page_id ) {
		return vc_user_access()
			->wpAny( array( 'edit_post', (int) $page_id ) )
			->get();
	}
}
