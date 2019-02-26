<?php

class WPML_ST_Slug_Translation_UI_Save implements IWPML_Action {

	const ACTION_HOOK_FOR_POST = 'wpml_save_cpt_sync_settings';
	const ACTION_HOOK_FOR_TAX  = 'wpml_save_taxonomy_sync_settings';

	/** @var WPML_ST_Slug_Translation_Settings $settings */
	private $settings;

	/** @var WPML_Slug_Translation_Records $records */
	private $records;

	/** @var SitePress $sitepress */
	private $sitepress;

	/** @var IWPML_WP_Element_Type $wp_element_type */
	private $wp_element_type;

	/**
	 * @var string $action_hook either WPML_ST_Slug_Translation_UI_Save::ACTION_HOOK_FOR_POST
	 *                          or WPML_ST_Slug_Translation_UI_Save::ACTION_HOOK_FOR_TAX
	 */
	private $action_hook;

	public function __construct(
		WPML_ST_Slug_Translation_Settings $settings,
		WPML_Slug_Translation_Records $records,
		SitePress $sitepress,
		IWPML_WP_Element_Type $wp_element_type,
		$action_hook
	) {
		$this->settings        = $settings;
		$this->records         = $records;
		$this->sitepress       = $sitepress;
		$this->wp_element_type = $wp_element_type;
		$this->action_hook     = $action_hook;
	}

	public function add_hooks() {
		add_action( $this->action_hook, array( $this, 'save_element_type_slug_translation_options' ), 1 );
	}

	public function save_element_type_slug_translation_options() {
		if ( $this->settings->is_enabled() && ! empty( $_POST['translate_slugs'] ) ) {

			foreach ( $_POST['translate_slugs'] as $type => $data ) {
				$is_type_enabled = $this->has_translation( $data );
				$this->settings->set_type( $type, $is_type_enabled );
				$this->update_slug_translations( $type, $data );
			}

			$this->settings->save();
		}
	}

	private function has_translation( array $data ) {
		$slug_translations = $this->get_slug_translations( $data );

		foreach ( $slug_translations as $slug ) {
			if ( trim( $slug ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	private function get_slug_translations( array $data ) {
		$slugs              = $data['langs'];
		$original_slug_lang = $data['original'];
		unset( $slugs[ $original_slug_lang ] );
		return $slugs;
	}

	/**
	 * @param string $type
	 * @param array  $data
	 */
	private function update_slug_translations( $type, array $data ) {
		$string = $this->records->get_slug_string( $type );

		if ( ! $string ) {
			$string = $this->register_string_if_not_exit( $type );
		}

		if ( $string ) {

			$original_lang = $this->sitepress->get_default_language();

			if ( isset( $data['original'] ) ) {
				$original_lang = $data['original'];
			}

			if ( $string->get_language() !== $original_lang ) {
				$string->set_language( $original_lang );
			}

			if ( isset( $data['langs'] ) ) {

				foreach ( $this->sitepress->get_active_languages() as $code => $lang ) {

					if ( $code !== $original_lang ) {
						$translation_value = $this->sanitize_slug( $data['langs'][ $code ] );
						$translation_value = urldecode( $translation_value );
						$string->set_translation( $code, $translation_value, ICL_TM_COMPLETE );
					}
				}
			}

			$string->update_status();
		}
	}

	/**
	 * @param string $type
	 *
	 * @return null|WPML_ST_String
	 */
	private function register_string_if_not_exit( $type ) {
		$slug = $this->get_registered_slug( $type );
		$this->records->register_slug( $type, $slug );
		return $this->records->get_slug_string( $type );
	}

	/**
	 * @param string $slug
	 *
	 * @return string
	 */
	private function sanitize_slug( $slug ) {
		return implode( '/', array_map( array( 'WPML_Slug_Translation', 'sanitize' ), explode( '/', $slug ) ) );
	}

	/**
	 * @param string $type_name
	 *
	 * @return string
	 */
	private function get_registered_slug( $type_name ) {
		$wp_element = $this->wp_element_type->get_wp_element_type_object( $type_name );
		return $wp_element ? trim( $wp_element->rewrite['slug'], '/' ) : false;
	}
}
