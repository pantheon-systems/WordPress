<?php

class WPML_TM_Word_Count_Report_View {

	const TEMPLATE_PATH = '/templates/words-count';
	const TEMPLATE_FILE = 'report.twig';

	/** @var WPML_Twig_Template_Loader $loader */
	private $loader;

	/** @var WPML_WP_Cron_Check $cron_check */
	private $cron_check;

	public function __construct( WPML_Twig_Template_Loader $loader, WPML_WP_Cron_Check $cron_check ) {
		$this->loader     = $loader;
		$this->cron_check = $cron_check;
	}

	public function show( array $model ) {
		$model['strings']    = self::get_strings();
		$model['cron_is_on'] = $this->cron_check->verify();
		return $this->loader->get_template()->show( $model, self::TEMPLATE_FILE );
	}

	public static function get_strings() {
		return array(
			'contentType'   => __( 'Type', 'wpml-translation-management' ),
			'itemsCount'    => __( 'Items', 'wpml-translation-management' ),
			'wordCount'     => __( 'Words', 'wpml-translation-management' ),
			'estimatedTime' => __( 'Count time', 'wpml-translation-management' ),
			'total'         => __( 'Total', 'wpml-translation-management' ),
			'recalculate'   => __( 'Recalculate', 'wpml-translation-management' ),
			'cancel'        => __( 'Cancel', 'wpml-translation-management' ),
			'needsRefresh'  => __( 'Needs refresh - Some items of this type are not counted', 'wpml-translation-management' ),
			'inMinute'      => __( '%d minute', 'wpml-translation-management' ),
			'inMinutes'     => __( '%d minutes', 'wpml-translation-management' ),
			'cronWarning'   => __( 'We detected a possible issue blocking the word count process. Please verify the following settings:', 'wpml-translation-management' ),
			'cronTips'      => array(
				__( 'Your site should be publicly accessible or the server should have access to the site.', 'wpml-translation-management' ),
				__( 'The constant DISABLE_WP_CRON should not be set to true.', 'wpml-translation-management' ),
			),
		);
	}
}