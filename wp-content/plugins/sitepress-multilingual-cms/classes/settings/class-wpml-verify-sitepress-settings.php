<?php
/**
 * Created by PhpStorm.
 * User: bruce
 * Date: 5/10/17
 * Time: 10:23 PM
 */

class WPML_Verify_SitePress_Settings {

	/** @var WPML_WP_API $wp_api */
	private $wp_api;

	public function __construct( WPML_WP_API $wp_api ) {
		$this->wp_api = $wp_api;
	}

	/**
	 * @param array $settings
	 */
	public function verify( $settings ) {
		$default_settings = array(
			'interview_translators'              => 1,
			'existing_content_language_verified' => 0,
			'language_negotiation_type'          => 3,
			'theme_localization_type'            => 1,
			'icl_lso_link_empty'                 => 0,
			'sync_page_ordering'                 => 1,
			'sync_page_parent'                   => 1,
			'sync_page_template'                 => 1,
			'sync_ping_status'                   => 1,
			'sync_comment_status'                => 1,
			'sync_sticky_flag'                   => 1,
			'sync_password'                      => 1,
			'sync_private_flag'                  => 1,
			'sync_post_format'                   => 1,
			'sync_delete'                        => 0,
			'sync_delete_tax'                    => 0,
			'sync_post_taxonomies'               => 1,
			'sync_post_date'                     => 0,
			'sync_taxonomy_parents'              => 0,
			'translation_pickup_method'          => 0,
			'notify_complete'                    => 1,
			'translated_document_status'         => 1,
			'remote_management'                  => 0,
			'auto_adjust_ids'                    => 1,
			'alert_delay'                        => 0,
			'promote_wpml'                       => 0,
			'automatic_redirect'                 => 0,
			'remember_language'                  => 24,
			'icl_lang_sel_copy_parameters'       => '',
			'translated_document_page_url'       => 'auto-generate',
			'sync_comments_on_duplicates '       => 0,
			'seo'                                => array(
				'head_langs'                  => 1,
				'canonicalization_duplicates' => 1,
				'head_langs_priority'         => 1
			),
			'posts_slug_translation'             => array(
				/** @deprected key `on`, use option `wpml_base_slug_translation` instead */
				'on' => 1,
			),
			'languages_order'                    => array(),
			'urls'                               => array(
				'directory_for_default_language' => 0,
				'show_on_root'                   => '',
				'root_html_file_path'            => '',
				'root_page'                      => 0,
				'hide_language_switchers'        => 1
			),
			'xdomain_data'                       => $this->wp_api->constant( 'WPML_XDOMAIN_DATA_GET' ),
			'custom_posts_sync_option'           => array(
				'post' => WPML_CONTENT_TYPE_TRANSLATE,
				'page' => WPML_CONTENT_TYPE_TRANSLATE
			),
			'taxonomies_sync_option'           => array(
				'category' => WPML_CONTENT_TYPE_TRANSLATE,
				'post_tag' => WPML_CONTENT_TYPE_TRANSLATE
			),
			'tm_block_retranslating_terms' => 1,
		);

		//configured for three levels
		$update_settings = false;
		foreach ( $default_settings as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $k2 => $v2 ) {
					if ( is_array( $v2 ) ) {
						foreach ( $v2 as $k3 => $v3 ) {
							if ( ! isset( $settings[ $key ][ $k2 ][ $k3 ] ) ) {
								$settings[ $key ][ $k2 ][ $k3 ] = $v3;
								$update_settings                = true;
							}
						}
					} else {
						if ( ! isset( $settings[ $key ][ $k2 ] ) ) {
							$settings[ $key ][ $k2 ] = $v2;
							$update_settings         = true;
						}
					}
				}
			} else {
				if ( ! isset( $settings[ $key ] ) ) {
					$settings[ $key ] = $value;
					$update_settings  = true;
				}
			}
		}

		return array( $settings, $update_settings );

	}


}