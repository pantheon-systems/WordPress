<?php

class WCML_Editor_UI_WYSIWYG_Field extends WPML_Editor_UI_WYSIWYG_Field{

	public function __construct( $id, $title, $data, $include_copy_button, $requires_complete = false ) {

		$data[$id]['original'] = strpos( $data[$id]['original'], "\n" ) !== false ?
			wpautop( $data[$id]['original'] ) : $data[$id]['original'];

		parent::__construct( $id, $title, $data, $include_copy_button, $requires_complete );
	}

}