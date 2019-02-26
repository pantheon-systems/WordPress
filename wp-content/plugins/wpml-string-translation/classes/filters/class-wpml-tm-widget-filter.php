<?php

class WPML_TM_Widget_Filter {

	/** @var WPML_ST_Post_Slug_Translation_Settings $post_slug_translation */
	private $post_slug_translation;

	/** @var  WPML_ST_String_Factory $string_factory */
	private $string_factory;

	public function __construct(
		WPML_ST_Post_Slug_Translation_Settings $post_slug_translation,
		WPML_ST_String_Factory $string_factory
	) {
		$this->post_slug_translation = $post_slug_translation;
		$this->string_factory        = $string_factory;
	}

	/**
	 * @param string              $notice
	 * @param array               $custom_posts
	 * @param WPML_Admin_Notifier $admin_notifier
	 *
	 * @return string
	 */
	public function filter_cpt_dashboard_notice( $notice, $custom_posts, $admin_notifier ) {
		foreach ( $custom_posts as $post_type_name => $custom_post ) {
			$_has_slug = isset( $custom_post->rewrite['slug'] ) && $custom_post->rewrite['slug'];
			if ( $_has_slug ) {
				if ( $this->post_slug_translation->is_enabled()
				     && $this->post_slug_translation->is_translated( $post_type_name )
				     && ICL_TM_COMPLETE !== $this->string_factory->find_by_name( 'Url slug: ' . $post_type_name )->get_status()
				) {
					$message = sprintf( __( "%s slugs are set to be translated, but they are missing their translation", 'wpml-string-translation' ), $custom_post->labels->name );
					$notice .= $admin_notifier->display_instant_message( $message, 'error', 'below-h2', true );
				}
			}
		}

		return $notice;
	}
}