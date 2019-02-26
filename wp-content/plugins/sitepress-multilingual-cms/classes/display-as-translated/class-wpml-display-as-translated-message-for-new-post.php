<?php

class WPML_Display_As_Translated_Message_For_New_Post implements IWPML_Action {

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var WPML_Notices $notices */
	private $notices;

	public function __construct( SitePress $sitepress, WPML_Notices $notices ) {
		$this->sitepress = $sitepress;
		$this->notices   = $notices;
	}

	public function add_hooks() {
		add_action( 'wp_loaded', array( $this, 'init' ), 10 );
	}

	public function init() {

		if (
			$this->is_display_as_translated_mode() &&
			$this->current_language_is_not_default_language()
		) {
			$notice = $this->notices->create_notice( __CLASS__, $this->get_notice_content() );
			$notice->set_css_class_types( 'warning' );
			$notice->set_dismissible( true );

			$this->notices->add_notice( $notice );
		} else {
			$this->notices->remove_notice( WPML_Notices::DEFAULT_GROUP, __CLASS__ );
		}
	}

	public function get_notice_content() {
		$current_lang = $this->sitepress->get_language_details( $this->sitepress->get_current_language() );
		$default_lang = $this->sitepress->get_language_details( $this->sitepress->get_default_language() );

		$post_type_name = $this->get_post_type();
		$post_type      = get_post_type_object( $post_type_name );
		$singular_name  = strtolower( $post_type->labels->singular_name );
		$plural_name    = strtolower( $post_type->labels->name );

		$output = esc_html(
			sprintf( __( "You are creating a %1\$s in %3\$s and you've set %2\$s to display even when not translated. Please note that this %1\$s will only appear in %3\$s. Only %2\$s that you create in the site's default language (%4\$s) will appear in all the site's languages.", 'sitepress' ),
				$singular_name,
				$plural_name,
				$current_lang['display_name'],
				$default_lang['display_name']
			)
		);
		$output .= '<br /><br />';
		$output .= '<a href="https://wpml.org/?page_id=1451509" target="_blank">' . esc_html__( 'Read how this works', 'sitepress' ) . '</a>';

		return $output;

	}

	private function is_display_as_translated_mode() {
		return $this->sitepress->is_display_as_translated_post_type( $this->get_post_type() );
	}

	private function current_language_is_not_default_language() {
		return $this->sitepress->get_current_language() != $this->sitepress->get_default_language();
	}

	private function get_post_type() {
		return isset( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
	}

}
