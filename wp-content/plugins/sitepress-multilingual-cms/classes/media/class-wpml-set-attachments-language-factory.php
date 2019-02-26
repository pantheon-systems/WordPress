<?php

class WPML_Set_Attachments_Language_Factory implements IWPML_Backend_Action_Loader, IWPML_Deferred_Action_Loader {

	const MEDIA_OPTION_KEY = '_wpml_media';

	private $wpml_media_settings;

	public function get_load_action() {
		return 'wpml_loaded';
	}

	public function create() {
		global $sitepress;

		$this->wpml_media_settings = get_option( self::MEDIA_OPTION_KEY, array() );

		if ( $this->needs_starting_help() && $this->is_not_media_settings_page() ) {

			$active_languages = $sitepress->get_active_languages();
			if ( count( $active_languages ) > 1 ) {

				if ( $this->is_wpml_media_not_set_up() && $this->has_unprocessed_attachments() ) {
					return new WPML_Set_Attachments_Language( $sitepress );
				} else {
					$this->wpml_media_settings['starting_help'] = 1;
					update_option( self::MEDIA_OPTION_KEY, $this->wpml_media_settings );
				}
			}
		}

		return null;
	}

	private function needs_starting_help() {
		return empty( $this->wpml_media_settings['starting_help'] );
	}

	private function is_not_media_settings_page() {
		return ! ( isset( $_GET['page'] ) && self::get_media_settings_page() === $_GET['page'] );
	}

	private function is_wpml_media_not_set_up() {
		return empty( $this->wpml_media_settings['setup_run'] );
	}

	private function has_unprocessed_attachments() {
		global $wpdb;

		$total_attachments_prepared = $wpdb->prepare( "
		                SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND ID NOT IN
		                (SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s)", array(
			'attachment',
			'wpml_media_processed'
		) );

		return (bool) $wpdb->get_var( $total_attachments_prepared );
	}

	public static function get_media_settings_page() {
		return defined( 'WPML_TM_FOLDER' )
			? WPML_TM_FOLDER . '/menu/settings'
			: WPML_PLUGIN_FOLDER . '/menu/translation-options.php';
	}
}