<?php

class WPML_Post_Status_Display {
	const ICON_TRANSLATION_EDIT         = 'otgs-ico-edit';
	const ICON_TRANSLATION_NEEDS_UPDATE = 'otgs-ico-refresh';
	const ICON_TRANSLATION_ADD          = 'otgs-ico-add';

	private $active_langs;

	public function __construct( $active_languages ) {
		$this->active_langs = $active_languages;
	}

	/**
	 * Returns the html of a status icon.
	 *
	 * @param string $link Link the status icon is to point to.
	 * @param string $text Hover text for the status icon.
	 * @param string $img Name of the icon image file to be used.
	 * @param string $css_class
	 *
	 * @return string
	 */
	private function render_status_icon( $link, $text, $img, $css_class ) {

		$icon_html = '<a href="' . esc_url( $link ) . '" title="' . esc_attr( $text ) . '">';
		$icon_html .= $this->get_action_icon( $css_class, $text );
		$icon_html .= '</a>';

		return $icon_html;
	}

	private function get_action_icon( $css_class, $label ) {
		return '<span class="' . $css_class . '" title="' . esc_attr( $label ) . '"></span>';
	}

	/**
	 * This function takes a post ID and a language as input.
	 * It will always return the status icon,
	 * of the version of the input post ID in the language given as the second parameter.
	 *
	 * @param int    $post_id  original post ID
	 * @param string $lang     language of the translation
	 *
	 * @return string
	 */
	public function get_status_html( $post_id, $lang ) {
		list( $icon, $text, $link, $trid, $css_class ) = $this->get_status_data( $post_id, $lang );
		if ( ! did_action( 'wpml_pre_status_icon_display' ) ) {
			do_action( 'wpml_pre_status_icon_display' );
		}
		$link = apply_filters( 'wpml_link_to_translation', $link, $post_id, $lang, $trid, $css_class );
		$icon = apply_filters( 'wpml_icon_to_translation', $icon, $post_id, $lang, $trid, $css_class );
		$text = apply_filters( 'wpml_text_to_translation', $text, $post_id, $lang, $trid, $css_class );

		return $this->render_status_icon( $link, $text, $icon, $css_class );
	}

	public function get_status_data( $post_id, $lang ) {
		global $wpml_post_translations;

		$status_helper        = wpml_get_post_status_helper();
		$trid                 = $wpml_post_translations->get_element_trid( $post_id );
		$status               = $status_helper->get_status( false, $trid, $lang );
		$source_language_code = $wpml_post_translations->get_element_lang_code( $post_id );
		$correct_id           = $wpml_post_translations->element_id_in( $post_id, $lang );

		if ( $status && $correct_id ) {
			list( $icon, $text, $link, $css_class ) = $this->generate_edit_allowed_data( $correct_id, $status_helper->needs_update( $correct_id ) );
		} else {
			list( $icon, $text, $link, $css_class ) = $this->generate_add_data( $trid, $lang, $source_language_code, $post_id );
		}

		return array( $icon, $text, $link, $trid, $css_class );
	}

	/**
	 * @param      $post_id  int
	 * @param bool $update   true if the translation in questions is in need of an update,
	 *                       false otherwise.
	 *
	 * @return array
	 */
	private function generate_edit_allowed_data( $post_id, $update = false ) {
		global $wpml_post_translations;

		$lang_code    = $wpml_post_translations->get_element_lang_code( $post_id );
		$post_type    = $wpml_post_translations->get_type( $post_id );

		$icon      = 'edit_translation.png';
		$css_class = self::ICON_TRANSLATION_EDIT;
		if ( $update && ! $wpml_post_translations->is_a_duplicate( $post_id ) ) {
			$icon      = 'needs-update.png';
			$css_class = self::ICON_TRANSLATION_NEEDS_UPDATE;
		}

		if ( $update ) {
			$text = __( 'Update %s translation', 'sitepress' );
		} else {
			$text = __( 'Edit the %s translation', 'sitepress' );
		}

		$text = sprintf( $text, $this->active_langs[ $lang_code ]['display_name'] );

		$link = 'post.php?' . http_build_query (
				array( 'lang'      => $lang_code,
				       'action'    => 'edit',
				       'post_type' => $post_type,
				       'post'      => $post_id
				)
			);

		return array( $icon, $text, $link, $css_class );
	}

	/**
	 * Generates the data for displaying a link element pointing towards a translation, that the current user can
	 * create.
	 *
	 * @param int    $trid
	 * @param int    $original_id
	 * @param string $lang_code
	 * @param string $source_language
	 *
	 * @return array
	 */
	private function generate_add_data( $trid, $lang_code, $source_language, $original_id ) {
		$link = 'post-new.php?' . http_build_query (
				array(
					'lang'        => $lang_code,
					'post_type'   => get_post_type ( $original_id ),
					'trid'        => $trid,
					'source_lang' => $source_language
				)
			);

		return array(
			'add_translation.png',
			sprintf( __( 'Add translation to %s', 'sitepress' ), $this->active_langs[ $lang_code ]['display_name'] ),
			$link,
			self::ICON_TRANSLATION_ADD,
		);
	}
}
