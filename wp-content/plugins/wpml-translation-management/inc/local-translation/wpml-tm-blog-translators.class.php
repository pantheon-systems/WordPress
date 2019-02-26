<?php

class WPML_TM_Blog_Translators {

	/** @var WPML_TM_Records $tm_records */
	private $tm_records;

	/**
	 * @var SitePress;
	 */
	private $sitepress;

	/** @var WPML_Translator_Records $translator_records */
	private $translator_records;

	/** @var stdClass[] $all_translators */
	private $all_translators;

	/**
	 * @param SitePress               $sitepress
	 * @param WPML_TM_Records         $tm_records
	 * @param WPML_Translator_Records $translator_records
	 */
	public function __construct( SitePress $sitepress, WPML_TM_Records $tm_records, WPML_Translator_Records $translator_records ) {
		$this->sitepress          = $sitepress;
		$this->tm_records         = $tm_records;
		$this->translator_records = $translator_records;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	function get_blog_translators( $args = array() ) {
		$from = isset( $args['from'] ) ? $args['from'] : false;
		$to   = isset( $args['to'] ) ? $args['to'] : false;

		$all_translators = $this->get_raw_blog_translators();
		$translators     = array();

		foreach ( $all_translators as $key => $translator ) {
			if ( ! $from || ! $to ) {
				$translators[] = isset( $translator->data ) ? $translator->data : $translator;
			} elseif ( $this->translator_has_language_pair( $translator->ID, $from, $to ) ) {
				$translators[] = isset( $translator->data ) ? $translator->data : $translator;
			}
		}

		return apply_filters( 'blog_translators', $translators, $args );
	}

	/**
	 * @param int $translator_id
	 * @param string $from
	 * @param string $to
	 *
	 * @return bool
	 */
	private function translator_has_language_pair( $translator_id, $from, $to ) {
		$language_pairs = $this->get_language_pairs( $translator_id );

		if ( isset( $language_pairs[ $from ][ $to ] ) && (bool) $language_pairs[ $from ][ $to ] ) {
			return true;
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function get_raw_blog_translators() {
		if ( null === $this->all_translators ) {
			$this->all_translators = $this->translator_records->get_users_with_capability();
		}

		return $this->all_translators;
	}

	/**
	 * @param int   $user_id
	 * @param array $args
	 *
	 * @return bool
	 */
	function is_translator( $user_id, $args = array() ) {
		$admin_override = true;
		extract( $args, EXTR_OVERWRITE );
		$is_translator = $this->sitepress->get_wp_api()
		                                 ->user_can( $user_id, 'translate' );
		// check if user is administrator and return true if he is
		if ( $admin_override && $this->sitepress->get_wp_api()
		                                        ->user_can( $user_id, 'manage_options' )
		) {
			$is_translator = true;
			do_action( 'wpml_tm_ate_enable_subscription', $user_id );
		} else {
			if ( isset( $lang_from ) && isset( $lang_to ) ) {
				$user_language_pairs            = $this->get_language_pairs( $user_id );
				if ( ! empty( $user_language_pairs ) ) {
					foreach ( $user_language_pairs as $user_lang_from => $user_lang_to ) {
						if ( array_key_exists( $lang_to, $user_lang_to ) ) {
							$is_translator = true;
							break;
						} else {
							$is_translator = false;
						}
					}
				} else {
					$is_translator = false;
				}

			}
			if ( isset( $job_id ) ) {
				$job_record    = $this->tm_records->icl_translate_job_by_job_id( $job_id );
				$translator_id = in_array( $job_record->service(), array(
					'local',
					0,
				) ) ? $job_record->translator_id() : - 1;
				$is_translator = $translator_id == $user_id
				                 || ( $is_translator && empty( $translator_id ) );
			}
		}

		return apply_filters( 'wpml_override_is_translator', $is_translator, $user_id, $args );
	}

	/**
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_language_pairs( $user_id ) {

		return $this->sitepress->get_wp_api()
		                       ->get_user_meta(
			                       $user_id,
			                       $this->sitepress->wpdb()->prefix . 'language_pairs',
			                       true );
	}
}