<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * WPBakery WPBakery Page Builder front end editor
 *
 * @package WPBakeryPageBuilder
 *
 */

/**
 * Add element for VC editors with a list of mapped shortcodes.
 *
 * @since 4.3
 */
class Vc_Add_Element_Box implements Vc_Render {
	/**
	 * Enable show empty message
	 *
	 * @since 4.8
	 * @var bool
	 */
	protected $show_empty_message = false;

	/**
	 * @param $params
	 *
	 * @return string
	 */
	protected function getIcon( $params ) {
		$data = '';
		if ( isset( $params['is_container'] ) && true === $params['is_container'] ) {
			$data = ' data-is-container="true"';
		}

		return '<i class="vc_general vc_element-icon' . ( ! empty( $params['icon'] ) ? ' ' . sanitize_text_field( $params['icon'] ) : '' ) . '"' . $data . '></i> ';
	}

	/**
	 * Single button html template
	 *
	 * @param $params
	 *
	 * @return string
	 */
	public function renderButton( $params ) {
		if ( ! is_array( $params ) || empty( $params ) ) {
			return '';
		}
		$output = $class = $class_out = $data = $category_css_classes = '';
		if ( ! empty( $params['class'] ) ) {
			$class_ar = $class_at_out = explode( ' ', $params['class'] );
			for ( $n = 0; $n < count( $class_ar ); $n ++ ) {
				$class_ar[ $n ] .= '_nav';
				$class_at_out[ $n ] .= '_o';
			}
			$class = ' ' . implode( ' ', $class_ar );
			$class_out = ' ' . implode( ' ', $class_at_out );
		}
		if ( isset( $params['_category_ids'] ) ) {
			foreach ( $params['_category_ids'] as $id ) {
				$category_css_classes .= ' js-category-' . $id;
			}
		}
		if ( isset( $params['is_container'] ) && true === $params['is_container'] ) {
			$data .= ' data-is-container="true"';
		}
		$data .= ' data-vc-ui-element="add-element-button"';
		$description = ! empty( $params['description'] ) ? '<span class="vc_element-description">' . htmlspecialchars( $params['description'] ) . '</span>' : '';
		$name = '<span data-vc-shortcode-name>' . htmlspecialchars( stripslashes( $params['name'] ) ) . '</span>';
		$output .= '<li data-element="' . $params['base'] . '"'. ( isset( $params['presetId'] ) ? ' data-preset="' . $params['presetId'] . '"' : '' ) .' class="wpb-layout-element-button vc_col-xs-12 vc_col-sm-4 vc_col-md-3 vc_col-lg-2' . ( isset( $params['deprecated'] ) ? ' vc_element-deprecated' : '' ) . $category_css_classes . $class_out . '"' . $data . '><div class="vc_el-container"><a id="' . $params['base'] . '" data-tag="' . $params['base'] . '" class="dropable_el vc_shortcode-link' . $class . '" href="#" data-vc-clickable>' . $this->getIcon( $params ) . $name . $description . '</a></div></li>';

		return $output;
	}

	/**
	 * Get mapped shortcodes list.
	 *
	 * @since 4.4
	 * @return array
	 */
	public function shortcodes() {
		return apply_filters( 'vc_add_new_elements_to_box', WPBMap::getSortedUserShortCodes() );
	}

	/**
	 * Render list of buttons for each mapped and allowed VC shortcodes.
	 * vc_filter: vc_add_element_box_buttons - hook to override output of getControls method
	 * @see WPBMap::getSortedUserShortCodes
	 * @return mixed|void
	 */
	public function getControls() {
		$output = '<ul class="wpb-content-layouts">';
		/** @var array $element */
		$buttons_count = 0;
		$shortcodes = $this->shortcodes();
		foreach ( $shortcodes as $element ) {
			if ( isset( $element['content_element'] ) && false === $element['content_element'] ) {
				continue;
			}
			$button = $this->renderButton( $element );
			if ( ! empty( $button ) ) {
				$buttons_count ++;
			}
			$output .= $button;
		}
		$output .= '</ul>';
		if ( 0 === $buttons_count ) {
			$this->show_empty_message = true;
		}

		return apply_filters( 'vc_add_element_box_buttons', $output );
	}

	/**
	 * Get categories list from mapping data.
	 * @since 4.5
	 *
	 * @return array
	 */
	public function getCategories() {
		return apply_filters( 'vc_add_new_category_filter', WPBMap::getUserCategories() );
	}

	public function render() {
		vc_include_template( 'editors/popups/vc_ui-panel-add-element.tpl.php', array(
			'box' => $this,
			'template_variables' => array(
				'categories' => $this->getCategories(),
			),
		) );
	}

	/**
	 * Render icon for shortcode
	 *
	 * @param $params
	 *
	 * @since 4.8
	 * @return string
	 */
	public function renderIcon( $params ) {
		return $this->getIcon( $params );
	}

	/**
	 * @return boolean
	 */
	public function isShowEmptyMessage() {
		return $this->show_empty_message;
	}

	public function getPartState() {
		return vc_user_access()->part( 'shortcodes' )->getState();
	}
}
