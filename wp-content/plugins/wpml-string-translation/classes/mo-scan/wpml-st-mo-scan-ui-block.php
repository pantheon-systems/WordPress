<?php

class WPML_ST_MO_Scan_UI_Block {
	const NOTICES_GROUP = 'wpml-st-mo-scan';
	const NOTICES_MO_SCANNING_BLOCKED = 'mo-scanning-blocked';

	/** @var  WPML_Theme_Localization_Type */
	private $localization_type;

	/** @var WPML_Notices */
	private $notices;

	/** @var string  */
	private $link = 'https://wpml.org/forums/topic/wpml-is-telling-me-that-it-has-a-broken-table-that-needs-fixing/';

	/**
	 * @param WPML_Theme_Localization_Type $localization_type
	 * @param WPML_Notices $notices
	 */
	public function __construct( WPML_Theme_Localization_Type $localization_type, WPML_Notices $notices ) {
		$this->localization_type = $localization_type;
		$this->notices           = $notices;
	}

	public function block_ui() {
		$this->disable_option();
		$this->change_option_value_if_required();
		$this->remove_default_notice();
		$this->display_notice();
	}

    public function unblock_ui()
    {
        $this->notices->remove_notice(self::NOTICES_GROUP, self::NOTICES_MO_SCANNING_BLOCKED);
    }

	private function disable_option() {
		add_filter( 'wpml_localization_options_ui_model', array( $this, 'disable_option_handler' ) );
	}

	public function disable_option_handler( $model ) {
		if ( ! isset( $model['top_options'][0] ) ) {
			return $model;
		}

		$model['top_options'][0]['disabled'] = true;
		$model['top_options'][0]['message'] = $this->get_short_notice_message();

		return $model;
	}

	private function get_short_notice_message() {
		$message = _x( 'WPML cannot replace .mo files because of technical problems in the String Translation table.',
			'MO Import blocked short 1/3', 'wpml-string-translation' );

		$message .= ' ' . _x( 'WPML support team knows how to fix it.',
			'MO Import blocked short 2/3', 'wpml-string-translation' );

		$message .= ' ' . sprintf( _x( 'Please add a message in the relevant <a href="%s" target="_blank" >support thread</a> and we\'ll fix it for you.',
			'MO Import blocked short 3/3', 'wpml-string-translation' ), $this->link );

		return '<span class="icl_error_text" >' . $message . '</span>';
	}

	private function change_option_value_if_required() {
		if ( $this->localization_type->get_use_st_and_no_mo_files_value() === (int) $this->localization_type->get_theme_localization_type() ) {
			$this->localization_type->save_localization_type( $this->localization_type->get_use_st_value() );
		}
	}

	private function display_notice() {
		$message = _x( 'There is a problem with the String Translation table in your site.',
			'MO Import blocked 1/4', 'wpml-string-translation' );

		$message .= ' ' . _x( 'This problem is not causing a problem running the site right now, but can become a critical issue in the future.',
			'MO Import blocked 2/4', 'wpml-string-translation' );

		$message .= ' ' . _x( 'WPML support team knows how to fix it.',
			'MO Import blocked 3/4', 'wpml-string-translation' );


		$message .= ' ' . sprintf( _x( 'Please add a message in the relevant <a href="%s" target="_blank">support thread</a> and we\'ll fix it for you.',
			'MO Import blocked 4/4', 'wpml-string-translation' ), $this->link );

		$notice     = $this->notices->create_notice( self::NOTICES_MO_SCANNING_BLOCKED, $message, self::NOTICES_GROUP );

		$notice->set_css_class_types( 'error' );
		$notice->set_dismissible( false );
        $restricted_pages = array(
            'sitepress-multilingual-cms/menu/languages.php',
            'sitepress-multilingual-cms/menu/menu-sync/menus-sync.php',
            'sitepress-multilingual-cms/menu/support.php',
            'sitepress-multilingual-cms/menu/taxonomy-translation.php',
            'sitepress-multilingual-cms/menu/theme-localization.php',
            'wpml-media',
            'wpml-package-management',
            'wpml-string-translation/menu/string-translation.php',
            'wpml-sticky-links',
            'wpml-translation-management/menu/translations-queue.php',
            'wpml-translation-management/menu/main.php',
        );
        $notice->set_restrict_to_pages($restricted_pages);

		$this->notices->add_notice( $notice );
	}

	private function remove_default_notice() {
		$this->notices->remove_notice( WPML_ST_Themes_And_Plugins_Settings::NOTICES_GROUP, WPML_ST_Themes_And_Plugins_Updates::WPML_ST_FASTER_SETTINGS_NOTICE_ID );
	}
}