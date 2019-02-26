<?php

/**
 * @link https://git.onthegosystems.com/tp/translation-proxy/wikis/translation_services
 */
class WPML_TP_Service extends WPML_TP_REST_Object implements Serializable {

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $logo_url;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $doc_url;

	/**
	 * @var bool
	 */
	public $tms;

	/**
	 * @var bool
	 */
	public $partner;

	/**
	 * @var stdClass
	 */
	public $custom_fields;

	/**
	 * @var array
	 * @deprecated
	 */
	public $custom_fields_data;

	/**
	 * @var bool
	 * @deprecated
	 */
	public $requires_authentication;

	/**
	 * @var stdClass
	 */
	public $rankings;

	/**
	 * @var bool
	 */
	public $has_language_pairs;

	/**
	 * @var string
	 */
	public $url;

	/** @var string */
	public $project_details_url;
	/** @var string */
	public $add_language_pair_url;
	/** @var string */
	public $custom_text_url;
	/** @var string */
	public $select_translator_iframe_url;
	/** @var string */
	public $translator_contact_iframe_url;
	/** @var string */
	public $quote_iframe_url;
	/** @var bool */
	public $has_translator_selection;
	/** @var int */
	public $project_name_length;
	/** @var string */
	public $suid;
	/** @var bool */
	public $notification;
	/** @var bool */
	public $preview_bundle;
	/** @var bool */
	public $deadline;
	/** @var bool */
	public $oauth;
	/** @var string */
	public $oauth_url;
	/** @var int */
	public $default_service;
	/** @var bool */
	public $translation_feedback;
	/** @var string */
	public $feedback_forward_method;
	/** @var int */
	public $last_refresh;

	/** @var string */
	public $popup_message;

	/** @var string */
	public $how_to_get_credentials_desc;

	/** @var string */
	public $how_to_get_credentials_url;

	/** @var string */
	public $client_create_account_page_url;

	public function __construct( stdClass $object = null ) {
		parent::__construct( $object );
		$this->set_custom_fields_data();
		$this->set_requires_authentication();
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_logo_url() {
		return $this->logo_url;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function get_doc_url() {
		return $this->doc_url;
	}

	/**
	 * @return string
	 */
	public function get_tms() {
		return $this->tms;
	}

	/**
	 * @return bool
	 */
	public function is_partner() {
		return $this->partner;
	}

	/**
	 * @param bool $partner
	 */
	public function set_partner( $partner ) {
		$this->partner = $partner;
	}

	/**
	 * @return array
	 */
	public function get_custom_fields() {
		$custom_fields = array();

		/** @TODO: This is odd. It appears that if it's the active service then it's
		 * stored with an extra custom_fields property eg. $this->custom_fields->custom_fields
		 * It looks like we call the api to get the custom field when we activate the service and store
		 * it directly here
		 */

		if ( is_object( $this->custom_fields ) && isset( $this->custom_fields->custom_fields ) ) {
			$custom_fields = $this->custom_fields->custom_fields;
		} elseif ( isset( $this->custom_fields ) ) {
			$custom_fields = $this->custom_fields;
		}

		return $custom_fields;
	}

	/**
	 * @return array
	 */
	public function get_custom_fields_data() {
		return $this->custom_fields_data;
	}

	/**
	 * @return bool
	 */
	public function get_requires_authentication() {
		return $this->requires_authentication;
	}

	/**
	 * @return bool
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @return bool
	 */
	public function get_has_language_pairs() {
		return (bool) $this->has_language_pairs;
	}

	/**
	 * @return stdClass
	 */
	public function get_rankings() {
		return $this->rankings;
	}

	/**
	 * @return string
	 */
	public function get_popup_message() {
		return $this->popup_message;
	}

	/**
	 * @param int $id
	 */
	public function set_id( $id ) {
		$this->id = (int) $id;
	}

	/**
	 * @param string $logo_url
	 */
	public function set_logo_url( $logo_url ) {
		$this->logo_url = $logo_url;
	}

	/**
	 * @param string $url
	 */
	public function set_url( $url ) {
		$this->url = $url;
	}

	/**
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->name = $name;
	}

	/**
	 * @param string $description
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * @param string $doc_url
	 */
	public function set_doc_url( $doc_url ) {
		$this->doc_url = $doc_url;
	}

	/**
	 * @param bool $tms
	 */
	public function set_tms( $tms ) {
		$this->tms = (bool) $tms;
	}

	/**
	 * @param stdClass $rankings
	 */
	public function set_rankings( $rankings ) {
		$this->rankings = $rankings;
	}

	/**
	 * @param stdClass $custom_fields
	 */
	public function set_custom_fields( $custom_fields ) {
		$this->custom_fields = $custom_fields;
	}

	/**
	 * @param string $popup_message
	 */
	public function set_popup_message( $popup_message ) {
		$this->popup_message = $popup_message;
	}

	public function set_custom_fields_data() {
		global $sitepress;

		$active_service = $sitepress->get_setting( 'translation_service' );
		if ( isset( $active_service->custom_fields_data, $active_service->id ) && $this->id === $active_service->id ) {
			$this->custom_fields_data = $active_service->custom_fields_data;
		}
	}

	public function set_requires_authentication() {
		$this->requires_authentication = (bool) $this->custom_fields;
	}

	/**
	 * @param bool $value
	 */
	public function set_has_language_pairs( $value  ) {
		$this->has_language_pairs = (bool) $value;
	}

	/**
	 * @return string
	 */
	public function get_project_details_url() {
		return $this->project_details_url;
	}

	/**
	 * @param string $project_details_url
	 */
	public function set_project_details_url( $project_details_url ) {
		$this->project_details_url = $project_details_url;
	}

	/**
	 * @return string
	 */
	public function get_add_language_pair_url() {
		return $this->add_language_pair_url;
	}

	/**
	 * @param string $add_language_pair_url
	 */
	public function set_add_language_pair_url( $add_language_pair_url ) {
		$this->add_language_pair_url = $add_language_pair_url;
	}

	/**
	 * @return string
	 */
	public function get_custom_text_url() {
		return $this->custom_text_url;
	}

	/**
	 * @param string $custom_text_url
	 */
	public function set_custom_text_url( $custom_text_url ) {
		$this->custom_text_url = $custom_text_url;
	}

	/**
	 * @return string
	 */
	public function get_select_translator_iframe_url() {
		return $this->select_translator_iframe_url;
	}

	/**
	 * @param string $select_translator_iframe_url
	 */
	public function set_select_translator_iframe_url( $select_translator_iframe_url ) {
		$this->select_translator_iframe_url = $select_translator_iframe_url;
	}

	/**
	 * @return string
	 */
	public function get_translator_contact_iframe_url() {
		return $this->translator_contact_iframe_url;
	}

	/**
	 * @param string $translator_contact_iframe_url
	 */
	public function set_translator_contact_iframe_url( $translator_contact_iframe_url ) {
		$this->translator_contact_iframe_url = $translator_contact_iframe_url;
	}

	/**
	 * @return string
	 */
	public function get_quote_iframe_url() {
		return $this->quote_iframe_url;
	}

	/**
	 * @param string $quote_iframe_url
	 */
	public function set_quote_iframe_url( $quote_iframe_url ) {
		$this->quote_iframe_url = $quote_iframe_url;
	}

	/**
	 * @return bool
	 */
	public function get_has_translator_selection() {
		return $this->has_translator_selection;
	}

	/**
	 * @param bool $has_translator_selection
	 */
	public function set_has_translator_selection( $has_translator_selection ) {
		$this->has_translator_selection = (bool) $has_translator_selection;
	}

	/**
	 * @return int
	 */
	public function get_project_name_length() {
		return $this->project_name_length;
	}

	/**
	 * @param int $project_name_length
	 */
	public function set_project_name_length( $project_name_length ) {
		$this->project_name_length = (int) $project_name_length;
	}

	/**
	 * @return string
	 */
	public function get_suid() {
		return $this->suid;
	}

	/**
	 * @param string $suid
	 */
	public function set_suid( $suid ) {
		$this->suid = $suid;
	}

	/**
	 * @return bool
	 */
	public function get_notification() {
		return $this->notification;
	}

	/**
	 * @param bool $notification
	 */
	public function set_notification( $notification ) {
		$this->notification = (bool) $notification;
	}

	/**
	 * @return bool
	 */
	public function get_preview_bundle() {
		return $this->preview_bundle;
	}

	/**
	 * @param bool $preview_bundle
	 */
	public function set_preview_bundle( $preview_bundle ) {
		$this->preview_bundle = (bool) $preview_bundle;
	}

	/**
	 * @return bool
	 */
	public function get_deadline() {
		return $this->deadline;
	}

	/**
	 * @param bool $deadline
	 */
	public function set_deadline( $deadline ) {
		$this->deadline = (bool) $deadline;
	}

	/**
	 * @return bool
	 */
	public function get_oauth() {
		return $this->oauth;
	}

	/**
	 * @param bool $oauth
	 */
	public function set_oauth( $oauth ) {
		$this->oauth = (bool) $oauth;
	}

	/**
	 * @return string
	 */
	public function get_oauth_url() {
		return $this->oauth_url;
	}

	/**
	 * @param string $oauth_url
	 */
	public function set_oauth_url( $oauth_url ) {
		$this->oauth_url = $oauth_url;
	}

	/**
	 * @return int
	 */
	public function get_default_service() {
		return $this->default_service;
	}

	/**
	 * @param int $default_service
	 */
	public function set_default_service( $default_service ) {
		$this->default_service = (int) $default_service;
	}

	/**
	 * @return bool
	 */
	public function get_translation_feedback() {
		return $this->translation_feedback;
	}

	/**
	 * @param bool $translation_feedback
	 */
	public function set_translation_feedback( $translation_feedback ) {
		$this->translation_feedback = (bool) $translation_feedback;
	}

	/**
	 * @return string
	 */
	public function get_feedback_forward_method() {
		return $this->feedback_forward_method;
	}

	/**
	 * @param string $feedback_forward_method
	 */
	public function set_feedback_forward_method( $feedback_forward_method ) {
		$this->feedback_forward_method = $feedback_forward_method;
	}

	/** @return null|int */
	public function get_last_refresh() {
		return $this->last_refresh;
	}

	/** @param int */
	public function set_last_refresh( $timestamp ) {
		$this->last_refresh = $timestamp;
	}

	/** @return null|string */
	public function get_how_to_get_credentials_desc() {
		return $this->how_to_get_credentials_desc;
	}

	/** @param string */
	public function set_how_to_get_credentials_desc( $desc ) {
		$this->how_to_get_credentials_desc = $desc;
	}

	/** @return null|string */
	public function get_how_to_get_credentials_url() {
		return $this->how_to_get_credentials_url;
	}

	/** @param string */
	public function set_how_to_get_credentials_url( $url ) {
		$this->how_to_get_credentials_url = $url;
	}

	/** @return null|string */
	public function get_client_create_account_page_url() {
		return $this->client_create_account_page_url;
	}

	/** @param string */
	public function set_client_create_account_page_url( $url ) {
		$this->client_create_account_page_url = $url;
	}

	public function serialize() {
		return serialize( get_object_vars( $this ) );
	}

	public function unserialize( $serialized ) {
		$simple_array = unserialize( $serialized );

		foreach ( $simple_array as $property => $value ) {
			if ( property_exists( $this, $property ) ) {
				$this->$property = $value;
			}
		}
	}

	/**
	 * @return array
	 */
	protected function get_properties() {
		return array(
			'id'                             => 'id',
			'logo_url'                       => 'logo_url',
			'url'                            => 'url',
			'name'                           => 'name',
			'description'                    => 'description',
			'doc_url'                        => 'doc_url',
			'tms'                            => 'tms',
			'partner'                        => 'partner',
			'custom_fields'                  => 'custom_fields',
			'custom_fields_data'             => 'custom_fields_data',
			'requires_authentication'        => 'requires_authentication',
			'has_language_pairs'             => 'has_language_pairs',
			'rankings'                       => 'rankings',
			'popup_message'                  => 'popup_message',
			'project_details_url'            => 'project_details_url',
			'add_language_pair_url'          => 'add_language_pair_url',
			'custom_text_url'                => 'custom_text_url',
			'select_translator_iframe_url'   => 'select_translator_iframe_url',
			'translator_contact_iframe_url'  => 'translator_contact_iframe_url',
			'quote_iframe_url'               => 'quote_iframe_url',
			'has_translator_selection'       => 'has_translator_selection',
			'project_name_length'            => 'project_name_length',
			'suid'                           => 'suid',
			'notification'                   => 'notification',
			'preview_bundle'                 => 'preview_bundle',
			'deadline'                       => 'deadline',
			'oauth'                          => 'oauth',
			'oauth_url'                      => 'oauth_url',
			'default_service'                => 'default_service',
			'translation_feedback'           => 'translation_feedback',
			'feedback_forward_method'        => 'feedback_forward_method',
			'last_refresh'                   => 'last_refresh',
			'how_to_get_credentials_desc'    => 'how_to_get_credentials_desc',
			'how_to_get_credentials_url'     => 'how_to_get_credentials_url',
			'client_create_account_page_url' => 'client_create_account_page_url',

		);
	}
}