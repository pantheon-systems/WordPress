<?php

class WPML_Media_Help_Tab implements IWPML_Action {

	public function add_hooks() {

		add_action( 'admin_head', array( $this, 'add' ) );
	}

	public function add() {
		$current_screen = get_current_screen();

		if ( $this->is_media_related_screen( $current_screen ) ) {
			$media_translation_dashboard = esc_html__( 'WPML &raquo; Media Translation', 'wpml-media' );
			$wpml_translation_dashboard  = esc_html__( 'WPML &raquo; Translation Management', 'wpml-media' );
			$current_screen->add_help_tab( array(
				'id'      => 'wpml-media-translation',
				'title'   => esc_html__( 'Translating Media', 'wpml-media' ),
				'content' =>
					'<p>' . esc_html__( 'There are two ways for you to translate Media:', 'wpml-media' ) . '</p>' .
					'<ul>' .
					'<li>' . sprintf(
						esc_html__( 'Use the dashboard on the %s page to translate your images and other media files.', 'wpml-media' ),
						$media_translation_dashboard
					) . '</li>' .
					'<li>' . sprintf(
						esc_html__( 'Use the dashboard on the %s page to send pages that contain media, for translation.', 'wpml-media' ),
						$wpml_translation_dashboard
					) . '</li>' .
					'</ul>' .
					'</ul>' .
					'<a href="https://wpml.org/?page_id=113610">' . esc_html__( 'Learn more about WPML Media Translation', 'wpml-media' ) . '</a>'
			) );
		}

	}

	private function is_media_related_screen( $current_screen ) {
		$accepted_bases = array(
			'wpml_page_wpml-media',
			'upload',
			'media',
			'wpml_page_wpml-translation-management/menu/main'
		);

		return $current_screen && in_array( $current_screen->base, $accepted_bases );
	}

}