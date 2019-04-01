<?php

/**
 * Class WPML_TF_Backend_Document_Information
 *
 * @author OnTheGoSystems
 */
class WPML_TF_Backend_Document_Information extends WPML_TF_Document_Information {

	/** @var WPML_TP_Client_Factory|null $tp_client_factory */
	private $tp_client_factory;

	/** @var WPML_TP_Client|null $tp_client */
	private $tp_client;

	/**
	 * WPML_TF_Backend_Document_Information constructor.
	 *
	 * @param SitePress                   $sitepress
	 * @param WPML_TP_Client_Factory|null $tp_client_factory
	 */
	public function __construct( SitePress $sitepress, WPML_TP_Client_Factory $tp_client_factory = null ) {
		parent::__construct( $sitepress );
		$this->tp_client_factory = $tp_client_factory;
	}

	/**
	 * @return false|null|string
	 */
	public function get_url() {
		$url = null;

		if ( $this->is_post_document() ) {
			$url = get_permalink( $this->id );
		}

		return $url;
	}

	/**
	 * @return null|string
	 */
	public function get_title() {
		$title = null;

		if ( $this->is_post_document() ) {
			$title = $this->get_post_title( $this->id );
		}

		return $title;
	}

	/**
	 * @return bool
	 */
	private function is_post_document() {
		return 0 === strpos( $this->type, 'post_' );
	}

	/**
	 * @param string $language_code
	 *
	 * @return string
	 */
	public function get_flag_url( $language_code ) {
		return $this->sitepress->get_flag_url( $language_code );
	}

	/**
	 * @return string
	 */
	public function get_edit_url() {
		$this->load_link_to_translation_tm_filters();

		$url = 'post.php?' . http_build_query(
			array(
				'lang'      => $this->get_language(),
				'action'    => 'edit',
				'post_type' => $this->type,
				'post'      => $this->id,
			)
		);

		return apply_filters( 'wpml_link_to_translation', $url, $this->get_source_id(), $this->get_language(), $this->get_trid() );
	}

	private function load_link_to_translation_tm_filters() {
		if ( ! did_action( 'wpml_pre_status_icon_display' ) ) {
			do_action( 'wpml_pre_status_icon_display' );
		}
	}

	/**
	 * @return null|int
	 */
	public function get_source_id() {
		$source_id    = null;
		$translations = $this->get_translations();

		if ( isset( $translations[ $this->get_source_language() ]->element_id ) ) {
			$source_id = (int) $translations[ $this->get_source_language() ]->element_id;
		}

		return $source_id;
	}

	/**
	 * @return null|string
	 */
	public function get_source_url() {
		$url = null;

		if ( $this->get_source_id() ) {
			$url = get_the_permalink( $this->get_source_id() );
		}

		return $url;
	}

	/**
	 * @return null|string
	 */
	public function get_source_title() {
		$title = null;

		if ( $this->get_source_id() ) {
			$title = $this->get_post_title( $this->get_source_id() );
		}

		return $title;
	}

	/**
	 * @return array|bool|mixed
	 */
	private function get_translations() {
		return $this->sitepress->get_element_translations( $this->get_trid(), $this->type );
	}

	/**
	 * @param int $job_id
	 *
	 * @return string
	 */
	public function get_translator_name( $job_id ) {
		$translation_job = $this->get_translation_job( $job_id );

		if ( isset( $translation_job->translation_service ) ) {

			if ( 'local' === $translation_job->translation_service && isset( $translation_job->translator_id ) ) {
				$translator_name = get_the_author_meta( 'display_name', $translation_job->translator_id );
				$translator_name .= ' (' . __( 'local translator', 'sitepress' ) . ')';
			} else {
				$translator_name = __( 'Unknown remote translation service', 'sitepress' );
				$tp_client       = $this->get_tp_client();

				if ( $tp_client ) {
					$translator_name = $tp_client->services()->get_name( $translation_job->translation_service );
				}
			}
		} else {
			$post_author_id  = get_post_field( 'post_author', $this->id );
			$translator_name = get_the_author_meta( 'display_name', $post_author_id );
		}

		return $translator_name;
	}

	/**
	 * @param string $from
	 * @param string $to
	 *
	 * @return array
	 */
	public function get_available_translators( $from, $to ) {
		$translators = array();

		if ( function_exists( 'wpml_tm_load_blog_translators' ) ) {
			$args = array(
				'from' => $from,
				'to'   => $to,
			);

			$users       = wpml_tm_load_blog_translators()->get_blog_translators( $args );
			$translators = wp_list_pluck( $users, 'display_name', 'ID' );
		}

		return $translators;
	}

	/**
	 * @param int $post_id
	 *
	 * @return null|string
	 */
	private function get_post_title( $post_id ) {
		$title = null;
		$post  = get_post( $post_id );

		if ( isset( $post->post_title ) ) {
			$title = $post->post_title;
		}

		return $title;
	}

	/** @return null|WPML_TP_Client */
	private function get_tp_client() {
		if ( ! $this->tp_client && $this->tp_client_factory ) {
			$this->tp_client = $this->tp_client_factory->create();
		}

		return $this->tp_client;
	}
}
