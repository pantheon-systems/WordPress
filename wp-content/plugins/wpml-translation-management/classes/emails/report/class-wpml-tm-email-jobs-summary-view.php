<?php

/**
 * Class WPML_TM_Email_Jobs_Summary_View
 */
class WPML_TM_Email_Jobs_Summary_View extends WPML_TM_Email_View {

	const JOBS_TEMPLATE   = 'batch-report/email-job-pairs.twig';

	/**
	 * @var WPML_TM_Blog_Translators
	 */
	private $blog_translators;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var array
	 */
	private $assigned_jobs;

	/**
	 * WPML_TM_Batch_Report_Email_Template constructor.
	 *
	 * @param WPML_Twig_Template $template_service
	 * @param WPML_TM_Blog_Translators $blog_translators
	 * @param SitePress $sitepress
	 */
	public function __construct(
		WPML_Twig_Template $template_service,
		WPML_TM_Blog_Translators $blog_translators,
		SitePress $sitepress
	) {
		parent::__construct( $template_service );
		$this->blog_translators = $blog_translators;
		$this->sitepress        = $sitepress;
	}

	/**
	 * @param array $language_pairs
	 * @param int $translator_id
	 * @param string $title_singular
	 * @param string $title_plural
	 *
	 * @return null|string
	 */
	public function render_jobs_list( $language_pairs, $translator_id, $title_singular, $title_plural = '' ) {

		$this->empty_assigned_jobs();

		$model = array(
			'strings' => array(
				'strings_text' => __( 'Strings', 'wpml-translation-management' ),
				'start_translating_text' => __( 'start translating', 'wpml-translation-management' ),
				'take' => _x( 'take it', 'Take a translation job waiting for a translator', 'wpml-translation-management' ),
				'strings_link' => admin_url(
					'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php'
				),
			),
		);

		foreach ( $language_pairs as $lang_pair => $elements ) {

			$languages = explode( '|', $lang_pair );
			$args      = array(
				'lang_from' => $languages[0],
				'lang_to' => $languages[1]
			);

			if ( $this->blog_translators->is_translator( $translator_id, $args ) &&
			     WPML_User_Jobs_Notification_Settings::is_new_job_notification_enabled( $translator_id ) ) {

				$model_elements = array();
				$string_added   = false;

				foreach ( $elements as $element ) {

					if ( ! $string_added || 'string' !== $element['type'] ) {
						$model_elements[] = array(
							'original_link'          => get_permalink( $element['element_id'] ),
							'original_text'          => sprintf( __( 'Link to original document %d', 'wpml-translation-management' ), $element['element_id'] ),
							'start_translating_link' => admin_url(
								'admin.php?page=' . WPML_TM_FOLDER . '%2Fmenu%2Ftranslations-queue.php'
							),
							'type' => $element['type'],
						);

						if ( 'string' === $element['type'] ) {
							$string_added = true;
						}
					}

					$this->add_assigned_job( $element['job_id'], $element['type'] );
				}

				$source_lang                     = $this->sitepress->get_language_details( $languages[0] );
				$target_lang                     = $this->sitepress->get_language_details( $languages[1] );
				$model['lang_pairs'][$lang_pair] = array(
					'title'    => sprintf( __( 'From %1$s to %2$s:', 'wpml-translation-management' ), $source_lang['english_name'], $target_lang['english_name'] ),
					'elements' => $model_elements,
				);
			}
		}

		$model['strings']['title'] = $title_singular;
		if ( 1 < count( $this->get_assigned_jobs() ) ) {
			$model['strings']['title'] = sprintf( $title_plural, count( $this->get_assigned_jobs() ) );
		}

		return count( $this->get_assigned_jobs() ) ? $this->template_service->show( $model, self::JOBS_TEMPLATE ) : null;
	}

	/** @return string */
	public function render_footer() {
		$site_url     = get_bloginfo( 'url' );
		$profile_link = '<a href="' . admin_url( 'profile.php' ) . '" style="color: #ffffff;">' . esc_html__( 'Your Profile', '' ) .'</a>';

		$bottom_text = sprintf(
			__(
				'You are receiving this email because you have a translator 
			account in %1$s. To stop receiving notifications, 
			log-in to %2$s and unselect "Send me a notification email 
			when there is something new to translate". Please note that 
			this will take you out of the translators pool.', 'wpml-translation-management'
			),
			$site_url,
			$profile_link
		);

		return $this->render_email_footer( $bottom_text );
	}

	/**
	 * @param int $jobs_count
	 *
	 * @return string
	 */
	private function get_jobs_count_text( $jobs_count ) {
		$jobs_count_text = ' is 1 job';
		if ( 1 < $jobs_count ) {
			$jobs_count_text = ' are ' . $jobs_count . ' jobs';
		}

		return $jobs_count_text;
	}

	/**
	 * @param int $job_id
	 * @param string $type
	 */
	private function add_assigned_job( $job_id, $type ) {
		$this->assigned_jobs[] = array(
			'job_id' => $job_id,
			'type'   => $type,
		);
	}

	/**
	 * @return array
	 */
	public function get_assigned_jobs() {
		$string_counted = false;
		foreach ( $this->assigned_jobs as $key => $assigned_job ) {
			if ( 'string' === $assigned_job['type'] ) {
				if ( $string_counted ) {
					unset( $this->assigned_jobs[$key] );
				}
				$string_counted = true;
			}
		}

		return $this->assigned_jobs;
	}

	private function empty_assigned_jobs() {
		$this->assigned_jobs = array();
	}
}
