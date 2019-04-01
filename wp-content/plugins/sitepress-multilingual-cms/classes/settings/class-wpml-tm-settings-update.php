<?php

class WPML_TM_Settings_Update extends WPML_SP_User {

	private $index_singular;
	private $index_ro;
	private $index_sync;
	private $index_plural;
	private $index_unlocked;
	/** @var  TranslationManagement $tm_instance */
	private $tm_instance;
	/** @var WPML_Settings_Helper $settings_helper */
	private $settings_helper;

	/**
	 * @param string                $index_singular
	 * @param string                $index_plural
	 * @param TranslationManagement $tm_instance
	 * @param SitePress             $sitepress
	 * @param WPML_Settings_Helper  $settings_helper
	 */
	public function __construct( $index_singular, $index_plural, &$tm_instance, &$sitepress, &$settings_helper ) {
		parent::__construct( $sitepress );
		$this->tm_instance     = &$tm_instance;
		$this->index_singular  = $index_singular;
		$this->index_plural    = $index_plural;
		$this->index_ro        = $index_plural . '_readonly_config';
		$this->index_sync      = $index_plural . '_sync_option';
		if ( 'custom-type' == $index_singular ) {
			$this->index_unlocked = 'custom_posts_unlocked_option';
		} else {
			$this->index_unlocked = 'taxonomies_unlocked_option';
		}
		$this->settings_helper = $settings_helper;
	}

	/**
	 * @param array $config
	 */
	public function update_from_config( array $config ) {
		$config[ $this->index_plural ] = isset( $config[ $this->index_plural ] ) ? $config[ $this->index_plural ] : array();
		$this->update_tm_settings( $config[ $this->index_plural ] );
	}

	private function sync_settings( array $config ) {
		$section_singular = $this->index_singular;
		$section_plural   = $this->index_plural;

		if ( ! empty( $config[ $section_singular ] ) ) {
			$sync_option     = $this->sitepress->get_setting( $this->index_sync, array() );
			$unlocked_option = $this->sitepress->get_setting( $this->index_unlocked, array() );
			if ( ! is_numeric( key( current( $config ) ) ) ) {
				$cf[0] = $config[ $section_singular ];
			} else {
				$cf = $config[ $section_singular ];
			}
			foreach ( $cf as $c ) {
				$val = $c['value'];

				if ( ! $this->is_unlocked_type( $val, $unlocked_option ) ) {

					$sync_existing_setting                                  = isset( $sync_option[ $val ] ) ? $sync_option[ $val ] : false;
					$sync_new_setting                                       = (int) $c['attr']['translate'];
					$this->tm_instance->settings[ $this->index_ro ][ $val ] = $sync_new_setting;
					$sync_option[ $val ]                                    = $sync_new_setting;

					if ( $this->is_making_type_translatable( $sync_new_setting, $sync_existing_setting ) ) {
						if ( $section_plural === 'taxonomies' ) {
							$this->sitepress->verify_taxonomy_translations( $val );
						} else {
							$this->sitepress->verify_post_translations( $val );
						}
						$this->tm_instance->save_settings();
					}
				}
			}

		$this->sitepress->set_setting( $this->index_sync, $sync_option );
		$this->settings_helper->maybe_add_filter( $section_plural );
		}
	}

	/**
	 * @param int $new_sync 0, 1 or 2
	 * @param int $old_sync 0, 1 or 2
	 *
	 * @return bool
	 */
	private function is_making_type_translatable( $new_sync, $old_sync ) {
		return in_array( $new_sync, array(
				WPML_CONTENT_TYPE_TRANSLATE,
				WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED
			) ) && WPML_CONTENT_TYPE_DONT_TRANSLATE === $old_sync;
	}

	private function update_tm_settings( array $config ) {
		$section_singular            = $this->index_singular;
		$config                      = array_filter( $config );
		$config[ $section_singular ] = isset( $config[ $section_singular ] ) ? $config[ $section_singular ] : array();
		$this->sync_settings( $config );

		// taxonomies - check what's been removed
		if ( ! empty( $this->tm_instance->settings[ $this->index_ro ] ) ) {
			$config_values = array();
			foreach ( $config[ $section_singular ] as $config_value ) {
				$config_values[ $config_value['value'] ] = $config_value['attr']['translate'];
			}
			foreach ( $this->tm_instance->settings[ $this->index_ro ] as $key => $translation_option ) {
				if ( ! isset( $config_values[ $key ] ) ) {
					unset( $this->tm_instance->settings[ $this->index_ro ][ $key ] );
				}
			}

			$this->tm_instance->save_settings();
		}
	}

	private function is_unlocked_type( $type, $unlocked_options ) {
		return isset( $unlocked_options[ $type ] ) && $unlocked_options[ $type ];
	}
}