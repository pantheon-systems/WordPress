<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * JWPLayer loader.
 * @since 4.3
 */
class Vc_Vendor_Jwplayer implements Vc_Vendor_Interface {
	/**
	 * Dublicate jwplayer logic for editor, when used in frontend editor mode.
	 *
	 * @since 4.3
	 */
	public function load() {

		add_action( 'wp_enqueue_scripts', array(
			$this,
			'vc_load_iframe_jscss',
		) );
		add_filter( 'vc_front_render_shortcodes', array(
			$this,
			'renderShortcodes',
		) );
		add_filter( 'vc_frontend_template_the_content', array(
			$this,
			'wrapPlaceholder',
		) );

		// fix for #1065
		add_filter( 'vc_shortcode_content_filter_after', array(
			$this,
			'renderShortcodesPreview',
		) );
	}

	/**
	 * @param $output
	 *
	 * @since 4.3
	 *
	 * @return mixed|string
	 */
	public function renderShortcodes( $output ) {
		$output = str_replace( '][jwplayer', '] [jwplayer', $output ); // fixes jwplayer shortcode regex..
		$data = JWP6_Shortcode::the_content_filter( $output );
		preg_match_all( '/(jwplayer-\d+)/', $data, $matches );
		$pairs = array_unique( $matches[0] );

		if ( count( $pairs ) > 0 ) {
			$id_zero = time();
			foreach ( $pairs as $pair ) {
				$data = str_replace( $pair, 'jwplayer-' . $id_zero ++, $data );
			}
		}

		return $data;
	}

	public function wrapPlaceholder( $content ) {
		add_shortcode( 'jwplayer', array( $this, 'renderPlaceholder' ) );

		return $content;
	}

	public function renderPlaceholder() {
		return '<div class="vc_placeholder-jwplayer"></div>';
	}

	/**
	 * @param $output
	 *
	 * @since 4.3, due to #1065
	 *
	 * @return string
	 */
	public function renderShortcodesPreview( $output ) {
		$output = str_replace( '][jwplayer', '] [jwplayer', $output ); // fixes jwplayer shortcode regex..
		return $output;
	}

	/**
	 * @since 4.3
	 * @todo check it for preview mode (check is it needed)
	 */
	public function vc_load_iframe_jscss() {
		wp_enqueue_script( 'vc_vendor_jwplayer', vc_asset_url( 'js/frontend_editor/vendors/plugins/jwplayer.js' ), array( 'jquery' ), '1.0', true );
	}
}
