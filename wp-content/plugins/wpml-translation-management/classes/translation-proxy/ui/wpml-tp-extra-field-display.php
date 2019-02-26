<?php

/**
 * @author OnTheGo Systems
 */
class WPML_TP_Extra_Field_Display {
	private $fields_with_items = array( 'select', 'radio', 'checkbox' );

	/**
	 * WPML_TP_Extra_Field_Display constructor.
	 */
	public function __construct() {
	}

	public function render( $field ) {
		if ( $this->must_render( $field ) ) {
			$field_id = 'wpml-tp-extra-field-' . $field->name;
			$row      = '<tr>';
			$row .= '<th scope="row">';
			$row .= '<label for="' . esc_attr( $field_id ) . '">' . esc_html( $field->label ) . '</label>';
			$row .= '</th>';
			$row .= '<td>';
			switch ( $field->type ) {
				case 'select':
					$row .= '<select id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field->name ) . '">';
					foreach ( $field->items as $id => $name ) {
						$row .= '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>';
					}
					$row .= '</select>';
					break;
				case 'textarea':
					$row .= '<textarea id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field->name ) . '"></textarea>';
					break;
				case 'radio':
				case 'checkbox':
					$row .= '<ol>';
					foreach ( $field->items as $id => $name ) {
						$row .= '<li>';
						$row .= '<input id="' . esc_attr( $field_id . '-' . $id ) . '" type="' . esc_attr( $field->type ) . '" name="' . esc_attr( $field->name ) . '" value="' . esc_attr( $id ) . '"> ';
						$row .= '<label for="' . esc_attr( $field_id . '-' . $id ) . '">' . esc_html( $name ) . '</label>';
						$row .= '</li>';
					}
						$row .= '</ol>';
					break;
				case 'text':
				default:
					$type = null !== $field->type ? $field->type : 'text';
					$row .= '<input id="' . esc_attr( $field_id ) . '" type="' . esc_attr( $type ). '" name="' . esc_attr( $field->name ) . '">';
					break;
			}
			$row .= '</td>';
			$row .= '</tr>';
			return $row;
		}
		return '';
	}

	/**
	 * @param $field
	 *
	 * @return bool
	 */
	private function must_render( $field ) {
		$must_render = isset( $field->type ) && $field->type;

		if ( $must_render && in_array( $field->type, $this->fields_with_items, true ) ) {
			$must_render = isset( $field->items ) && $field->items;
		}

		return $must_render;
	}
}