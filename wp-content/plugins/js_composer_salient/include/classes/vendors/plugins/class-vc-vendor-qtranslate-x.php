<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Vendor_QtranslateX
 * @since 4.12
 */
class Vc_Vendor_QtranslateX implements Vc_Vendor_Interface {

	public function load() {
		add_action( 'vc_backend_editor_render', array(
			$this,
			'enqueueJsBackend',
		) );
		add_action( 'vc_frontend_editor_render', array(
			$this,
			'enqueueJsFrontend',
		) );
		add_filter( 'vc_frontend_editor_iframe_url', array(
			$this,
			'appendLangToUrl',
		) );

		add_filter( 'vc_nav_front_controls', array(
			$this,
			'vcNavControlsFrontend',
		) );

		if ( ! vc_is_frontend_editor() ) {
			add_filter( 'vc_get_inline_url', array(
				$this,
				'vcRenderEditButtonLink',
			) );
		}
	}

	public function enqueueJsBackend() {
		wp_enqueue_script( 'vc_vendor_qtranslatex_backend', vc_asset_url( 'js/vendors/qtranslatex_backend.js' ), array(
			'vc-backend-min-js',
			'jquery',
		), '1.0', true );
	}

	public function appendLangToUrl( $link ) {
		global $q_config;
		if ( $q_config && isset( $q_config['language'] ) ) {
			return add_query_arg( array( 'lang' => ( $q_config['language'] ) ), $link );
		}

		return $link;
	}

	public function enqueueJsFrontend() {
		wp_enqueue_script( 'vc_vendor_qtranslatex_frontend', vc_asset_url( 'js/vendors/qtranslatex_frontend.js' ), array(
			'vc-frontend-editor-min-js',
			'jquery',
		), '1.0', true );
	}

	/**
	 * @return string
	 */
	public function generateSelectFrontend() {
		global $q_config;
		$output = '';
		$output .= '<select id="vc_vendor_qtranslatex_langs_front" class="vc_select vc_select-navbar">';
		$inline_url = vc_frontend_editor()->getInlineUrl();
		$activeLanguage = $q_config['language'];
		$availableLanguages = $q_config['enabled_languages'];
		foreach ( $availableLanguages as $lang ) {
			$output .= '<option value="' . add_query_arg( array( 'lang' => $lang ), $inline_url ) . '"' . ( $activeLanguage == $lang ? ' selected' : '' ) . ' > ' . qtranxf_getLanguageNameNative( $lang ) . '</option > ';
		}
		$output .= '</select > ';

		return $output;
	}

	/**
	 * @param $list
	 *
	 * @return array
	 */
	public function vcNavControlsFrontend( $list ) {
		if ( is_array( $list ) ) {
			$list[] = array(
				'qtranslatex',
				'<li class="vc_pull-right" > ' . $this->generateSelectFrontend() . '</li > ',
			);
		}

		return $list;
	}

	/**
	 * @param $link
	 *
	 * @return string
	 */
	public function vcRenderEditButtonLink( $link ) {
		global $q_config;
		$activeLanguage = $q_config['language'];

		return add_query_arg( array( 'lang' => $activeLanguage ), $link );
	}
}
