<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_Preset_Panel_Editor
 * @since 5.2
 */
class Vc_Preset_Panel_Editor {
	/**
	 * @since 5.2
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * @since 5.2
	 * Add ajax hooks, filters.
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}
		$this->initialized = true;

	}

	/**
	 * @since 5.2
	 */
	public function renderUIPreset() {
		vc_include_template( 'editors/popups/vc_ui-panel-preset.tpl.php', array(
			'box' => $this,
		) );

		return '';
	}

	/**
	 * Get list of all presets for specific shortcode
	 *
	 * @since 5.2
	 *
	 *
	 * @return array E.g. array(id1 => title1, id2 => title2, ...)
	 */
	public function listPresets() {
		$list = array();

		$args = array(
			'post_type' => 'vc_settings_preset',
			'orderby' => array( 'post_date' => 'DESC' ),
			'posts_per_page' => - 1,
		);

		$posts = get_posts( $args );
		foreach ( $posts as $post ) {

			$presetParentName = self::constructPresetParent( $post->post_mime_type );

			$list[ $post->ID ] = array(
				'title' => $post->post_title,
				'parent' => $presetParentName,
			);
		}

		return $list;
	}

	/**
	 * Single preset html
	 *
	 * @since 5.2
	 *
	 *
	 * @return string
	 */
	public function getPresets() {
		$listPresets = $this->listPresets();
		$output = '';

		foreach ( $listPresets as $presetId => $preset ) {
			$output .= '<div class="vc_ui-template">';
			$output .= '<div class="vc_ui-list-bar-item">';
			$output .= '<button type="button" class="vc_ui-list-bar-item-trigger" title="' . esc_attr( $preset['title'] ) . '"
						data-vc-ui-element="template-title">' . esc_html( $preset['title'] ) . '</button>';
			$output .= '<div class="vc_ui-list-bar-item-actions">';

			$output .= '<button id="' . esc_attr( $preset['parent'] ) . '" type="button" class="vc_general vc_ui-control-button" title="' . esc_attr( 'Add element', 'js_composer' ) . '" data-template-handler="" data-preset="' . esc_attr( $presetId ) . '" data-tag="' . esc_attr( $preset['parent'] ) . '" data-vc-ui-add-preset>';
			$output .= '<i class="vc-composer-icon vc-c-icon-add"></i>';
			$output .= '</button>';

			$output .= '<button type="button" class="vc_general vc_ui-control-button" data-vc-ui-delete="preset-title" data-preset="' . esc_attr( $presetId ) . '" data-preset-parent="' . esc_attr( $preset['parent'] ) . '" title="' . esc_attr( 'Delete element', 'js_composer' ) . '">';
			$output .= '<i class="vc-composer-icon vc-c-icon-delete_empty"></i>';
			$output .= '</button>';

			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Get preset parent shortcode name from post mime type
	 *
	 * @since 5.2
	 *
	 * @param $presetMimeType
	 *
	 * @return string
	 */
	public static function constructPresetParent( $presetMimeType ) {
		return str_replace( '-', '_', str_replace( 'vc-settings-preset/', '', $presetMimeType ) );
	}
}
