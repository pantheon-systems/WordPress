<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Vendor_Qtranslate
 * @since 4.3
 */
class Vc_Vendor_Qtranslate implements Vc_Vendor_Interface {

	/**
	 * @since 4.3
	 * @var array
	 */
	protected $languages = array();

	/**
	 * @since 4.3
	 */
	public function setLanguages() {
		global $q_config;
		$languages = get_option( 'qtranslate_enabled_languages' );
		if ( ! is_array( $languages ) ) {
			$languages = $q_config['enabled_languages'];
		}
		$this->languages = $languages;
	}

	public function isValidPostType() {
		return in_array( get_post_type(), vc_editor_post_types() );
	}

	/**
	 * @since 4.3
	 */
	public function load() {
		$this->setLanguages();
		global $q_config;
		add_filter( 'vc_frontend_get_page_shortcodes_post_content', array(
			$this,
			'filterPostContent',
		) );

		add_action( 'vc_backend_editor_render', array(
			$this,
			'enqueueJsBackend',
		) );

		add_action( 'vc_frontend_editor_render', array(
			$this,
			'enqueueJsFrontend',
		) );

		add_action( 'vc_frontend_editor_render_template', array(
			$this,
			'vcFrontEndEditorRender',
		) );
		add_filter( 'vc_nav_controls', array(
			$this,
			'vcNavControls',
		) );

		add_filter( 'vc_nav_front_controls', array(
			$this,
			'vcNavControlsFrontend',
		) );

		add_filter( 'vc_frontend_editor_iframe_url', array(
			$this,
			'vcRenderEditButtonLink',
		) );
		if ( ! vc_is_frontend_editor() ) {
			add_filter( 'vc_get_inline_url', array(
				$this,
				'vcRenderEditButtonLink',
			) );
		}
		$q_lang = vc_get_param( 'qlang' );
		if ( is_string( $q_lang ) ) {
			$q_config['language'] = $q_lang;
		}

		add_action( 'init', array(
			$this,
			'qtransPostInit',
		), 1000 );

	}

	/**
	 * @since 4.3
	 */
	public function qtransPostInit() {
		global $q_config;

		$q_config['js']['qtrans_switch'] = "
		var swtg= jQuery.extend(true, {}, switchEditors);
		switchEditors.go = function(id, lang) {
		    if ('content' !== id && 'qtrans_textarea_content' !== id && -1 === id.indexOf('qtrans')) {
		      return swtg.go(id,lang);
		    }
			id = id || 'qtrans_textarea_content';
			lang = lang || 'toggle';

			if ( 'toggle' === lang ) {
				if ( ed && !ed.isHidden() )
					lang = 'html';
				else
					lang = 'tmce';
			} else if ( 'tinymce' === lang )
				lang = 'tmce';

			var inst = tinyMCE.get('qtrans_textarea_' + id);
			var vta = document.getElementById('qtrans_textarea_' + id);
			var ta = document.getElementById(id);
			var dom = tinymce.DOM;
			var wrap_id = 'wp-'+id+'-wrap';
			var wrap_id2 = 'wp-qtrans_textarea_content-wrap';

			// update merged content
			if (inst && ! inst.isHidden()) {
				tinyMCE.triggerSave();
			} else {
				qtrans_save(vta.value);
			}

			// check if language is already active
			if (lang !== 'tmce' && lang !== 'html' && document.getElementById('qtrans_select_'+lang).className === 'wp-switch-editor switch-tmce switch-html') {
				return;
			}

			if (lang !== 'tmce' && lang !== 'html') {
				document.getElementById('qtrans_select_'+qtrans_get_active_language()).className='wp-switch-editor';
				document.getElementById('qtrans_select_'+lang).className='wp-switch-editor switch-tmce switch-html';
			}

			if (lang === 'html') {
				if ( inst && inst.isHidden() )
					return false;
				if ( inst ) {
					vta.style.height = inst.getContentAreaContainer().offsetHeight + 20 + 'px';
					inst.hide();
				}

				dom.removeClass(wrap_id, 'tmce-active');
				dom.addClass(wrap_id, 'html-active');
				dom.removeClass(wrap_id2, 'tmce-active');
				dom.addClass(wrap_id2, 'html-active');
				setUserSetting( 'editor', 'html' );
			} else if (lang === 'tmce') {
				if (inst && ! inst.isHidden())
					return false;
				if ( 'undefined' !== typeof(QTags) )
					QTags.closeAllTags('qtrans_textarea_' + id);
				if ( tinyMCEPreInit.mceInit['qtrans_textarea_'+id] && tinyMCEPreInit.mceInit['qtrans_textarea_'+id].wpautop )
					vta.value = this.wpautop(qtrans_use(qtrans_get_active_language(),ta.value));
				if (inst) {
					inst.show();
				} else {
					qtrans_hook_on_tinyMCE('qtrans_textarea_'+id, true);
				}

				dom.removeClass(wrap_id, 'html-active');
				dom.addClass(wrap_id, 'tmce-active');
				dom.removeClass(wrap_id2, 'html-active');
				dom.addClass(wrap_id2, 'tmce-active');
				setUserSetting('editor', 'tinymce');
			} else {
				// switch content
				qtrans_assign('qtrans_textarea_'+id,qtrans_use(lang,ta.value));
			}
		}
		";
		$this->qtransSwitch();

	}

	/**
	 * @since 4.3
	 */
	public function qtransSwitch() {
		global $q_config;
		$q_config['js']['qtrans_switch'] .= '
			jQuery(document).ready(function(){ switchEditors.switchto(document.getElementById("content-html")); });
		';
	}

	/**
	 * @since 4.3
	 */
	public function enqueueJsBackend() {

		if ( $this->isValidPostType() || apply_filters( 'vc_vendor_qtranslate_enqueue_js_backend', false ) ) {

			wp_enqueue_script( 'vc_vendor_qtranslate_backend', vc_asset_url( 'js/vendors/qtranslate_backend.js' ), array( 'vc-backend-min-js' ), '1.0', true );
		}
	}

	/**
	 * @since 4.3
	 */
	public function enqueueJsFrontend() {
		if ( $this->isValidPostType() ) {

			wp_enqueue_script( 'vc_vendor_qtranslate_frontend', vc_asset_url( 'js/vendors/qtranslate_frontend.js' ), array( 'vc-frontend-editor-min-js' ), '1.0', true );
			global $q_config;
			$q_config['js']['qtrans_save'] = '';
			$q_config['js']['qtrans_integrate_category'] = '';
			$q_config['js']['qtrans_integrate_title'] = '';
			$q_config['js']['qtrans_assign'] = '';
			$q_config['js']['qtrans_tinyMCEOverload'] = '';
			$q_config['js']['qtrans_wpActiveEditorOverload'] = '';
			$q_config['js']['qtrans_updateTinyMCE'] = '';
			$q_config['js']['qtrans_wpOnload'] = '';
			$q_config['js']['qtrans_editorInit'] = '';
			$q_config['js']['qtrans_hook_on_tinyMCE'] = '';
			$q_config['js']['qtrans_switch_postbox'] = '';
			$q_config['js']['qtrans_switch'] = '';
		}
	}

	/**
	 * @since 4.3
	 * @return string
	 */
	public function generateSelect() {
		$output = '';
		if ( is_array( $this->languages ) && ! empty( $this->languages ) ) {
			$output .= '<select id="vc_vendor_qtranslate_langs" class="vc_select vc_select-navbar" style="display:none;">';
			$inline_url = vc_frontend_editor()->getInlineUrl();
			foreach ( $this->languages as $lang ) {
				$output .= '<option value="' . $lang . '" link="' . add_query_arg( array( 'qlang' => $lang ), $inline_url ) . '">' . qtrans_getLanguageName( $lang ) . '</option>';
			}
			$output .= '</select>';
		}

		return $output;
	}

	/**
	 * @since 4.3
	 * @return string
	 */
	public function generateSelectFrontend() {
		$output = '';
		if ( is_array( $this->languages ) && ! empty( $this->languages ) ) {
			$output .= '<select id="vc_vendor_qtranslate_langs_front" class="vc_select vc_select-navbar">';
			$q_lang = vc_get_param( 'qlang' );
			$inline_url = vc_frontend_editor()->getInlineUrl();
			foreach ( $this->languages as $lang ) {
				$output .= '<option value="' . add_query_arg( array( 'qlang' => $lang ), $inline_url ) . '"' . ( $q_lang == $lang ? ' selected' : '' ) . ' > ' . qtrans_getLanguageName( $lang ) . '</option > ';
			}
			$output .= '</select > ';
		}

		return $output;
	}

	/**
	 * @since 4.3
	 *
	 * @param $list
	 *
	 * @return array
	 */
	public function vcNavControls( $list ) {
		if ( $this->isValidPostType() ) {

			if ( is_array( $list ) ) {
				$list[] = array(
					'qtranslate',
					$this->getControlSelectDropdown(),
				);
			}
		}

		return $list;
	}

	/**
	 * @since 4.3
	 *
	 * @param $list
	 *
	 * @return array
	 */
	public function vcNavControlsFrontend( $list ) {
		if ( $this->isValidPostType() ) {

			if ( is_array( $list ) ) {
				$list[] = array(
					'qtranslate',
					$this->getControlSelectDropdownFrontend(),
				);
			}
		}

		return $list;
	}

	/**
	 * @since 4.3
	 * @return string
	 */
	public function getControlSelectDropdown() {
		return '<li class="vc_pull-right" > ' . $this->generateSelect() . '</li > ';
	}

	/**
	 * @return string
	 */
	public function getControlSelectDropdownFrontend() {
		return '<li class="vc_pull-right" > ' . $this->generateSelectFrontend() . '</li > ';
	}

	/**
	 * @since 4.3
	 *
	 * @param $link
	 *
	 * @return string
	 */
	public function vcRenderEditButtonLink( $link ) {
		return add_query_arg( array( 'qlang' => qtrans_getLanguage() ), $link );
	}

	/**
	 * @since 4.3
	 */
	public function vcFrontendEditorRender() {
		global $q_config;
		$output = '';
		$q_lang = vc_get_param( 'qlang' );
		if ( ! is_string( $q_lang ) ) {
			$q_lang = $q_config['language'];
		}
		$output .= '<input type="hidden" id="vc_vendor_qtranslate_postcontent" value="' . esc_attr( vc_frontend_editor()->post()->post_content ) . '" data-lang="' . $q_lang . '"/>';

		$output .= '<input type="hidden" id="vc_vendor_qtranslate_posttitle" value="' . esc_attr( vc_frontend_editor()->post()->post_title ) . '" data-lang="' . $q_lang . '"/>';

		echo $output;
	}

	/**
	 * @since 4.3
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function filterPostContent( $content ) {
		return qtrans_useCurrentLanguageIfNotFoundShowAvailable( $content );
	}
}
