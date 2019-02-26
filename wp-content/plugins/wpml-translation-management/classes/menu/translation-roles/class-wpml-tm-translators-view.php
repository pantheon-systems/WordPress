<?php

abstract class WPML_TM_Translators_View extends WPML_Twig_Template_Loader {

	protected $model = array();

	/** @var WPML_Translator_Records $user_records */
	private $user_records;

	/** @var WPML_Language_Collection $active_languages */
	private $active_languages;

	/** @var WPML_Language_Collection $source_languages */
	private $source_languages;

	/** @var WPML_TM_AMS_Translator_Activation_Records */
	private $translator_activation_records;

	public function __construct(
		WPML_Translator_Records $user_records,
		WPML_Language_Collection $active_languages,
		WPML_TM_AMS_Translator_Activation_Records $translator_activation_records = null
	) {
		parent::__construct( array_merge( array(
				WPML_TM_PATH . '/templates/translators',
				WPML_TM_PATH . '/templates/menus/translation-managers',
			), $this->get_template_paths() )
		);

		$this->user_records                  = $user_records;
		$this->active_languages              = $active_languages;
		$this->translator_activation_records = $translator_activation_records;

		$this->source_languages = apply_filters( 'wpml_tm_allowed_source_languages', clone $this->active_languages );
	}

	public function render() {
		$this->add_users();
		$this->add_strings();
		$this->add_add_translator_dialog();
		$this->add_edit_translator_languages_dialog();
		$this->add_roles();
		$this->add_languages();
		$this->add_nonce();
		$this->add_capability();

		return $this->get_template()->show( $this->model, $this->get_twig_template() );
	}

	public function add_strings() {

		$this->model['strings'] = array(
			'title'          => __( 'Set Local Translators', 'wpml-translation-management' ),
			'sub_title'      => __( 'WPML’s Translation Editor makes it easy for your own translators to translate content in your site. You can create accounts for new translators or use existing WordPress users as your translators.', 'wpml-translation-management' ),
			'columns'        => array(
				'name'           => __( 'Name', 'wpml-translation-management' ),
				'email'          => __( 'Email', 'wpml-translation-management' ),
				'subscription'   => __( 'Advanced Translation Editor subscription', 'wpml-translation-management' ),
				'language_pairs' => __( 'Language Pairs', 'wpml-translation-management' ),
			),
			'add_translator' => __( 'Add Translator', 'wpml-translation-management' ),
			'go_back'        => __( 'Go back', 'wpml-translation-management' ),
			'continue'       => __( 'Continue', 'wpml-translation-management' ),
			'first_name'     => __( 'First name:', 'wpml-translation-management' ),
			'last_name'      => __( 'Last name:', 'wpml-translation-management' ),
			'email'          => __( 'Email:', 'wpml-translation-management' ),
			'user_name'      => __( 'User name:', 'wpml-translation-management' ),
			'wp_role'        => __( 'WordPress role:', 'wpml-translation-management' ),
			'remove'         => __( 'Remove', 'wpml-translation-management' ),
			'edit_languages' => __( 'Edit Languages', 'wpml-translation-management' ),
			'skip'           => __( "Skip this step - I don't need to add more translators", 'wpml-translation-management' ),
			'no_capability'  => __( "Only Translation Managers can add translators to the site. You can assign a different WordPress user to be the site's Translation Manager or make yourself a Translation Manager.", 'wpml-translation-management' ),
			'yes'            => __( 'Yes', 'wpml-translation-management' ),
			'no'             => __( 'No', 'wpml-translation-management' ),

		);

		$this->model['strings'] = apply_filters(
			'wpml_tm_translators_view_strings',
			$this->model['strings'],
			$this->model['all_users_have_subscriptions']
		);
	}

	private function add_roles() {
		$this->model['wp_roles'] = WPML_WP_Roles::get_subscriber_roles();
	}

	public function add_add_translator_dialog() {
		$this->model['add_translator_dialog'] = array(
			'id'               => 'js-add-translator-dialog',
			'class'            => '',
			'strings'          => array(
				'title'               => __( 'Add new Translator', 'wpml-translation-management' ),
				'set_languages_text'  => __( 'Set language pair(s)', 'wpml-translation-management' ),
				'add_translator_text' => __( 'Add Translator', 'wpml-translation-management' ),
				'previous_text'       => __( 'Previous', 'wpml-translation-management' ),
				'cancel_text'         => __( 'Cancel', 'wpml-translation-management' ),
				'existing_user'       => __( 'Select an existing user and set as Translator', 'wpml-translation-management' ),
				'new_user'            => __( 'Create a new user and set as Translator', 'wpml-translation-management' ),
				'set_lang'            => __( 'Set language pair(s) for Translator %USERNAME%', 'wpml-translation-management' ),
				'from'                => __( 'From', 'wpml-translation-management' ),
				'to'                  => __( 'to', 'wpml-translation-management' ),
				'choose_language'     => __( '--Choose language--', 'wpml-translation-management' ),
				'add_lang_pair_text'  => __( 'Add another language pair', 'wpml-translation-management' ),
			),
			'languages'        => $this->active_languages,
			'source_languages' => $this->source_languages,
			'nonce'            => $this->get_nonce(),
		);
	}

	public function add_edit_translator_languages_dialog() {
		$this->model['edit_translator_languages_dialog'] = array(
			'id'               => 'js-edit-translator-languages-dialog',
			'class'            => '',
			'strings'          => array(
				'title'              => __( 'Edit language pair(s) for translator', 'wpml-translation-management' ),
				'save_text'          => __( 'Save', 'wpml-translation-management' ),
				'cancel_text'        => __( 'Cancel', 'wpml-translation-management' ),
				'from'               => __( 'From', 'wpml-translation-management' ),
				'to'                 => __( 'to', 'wpml-translation-management' ),
				'choose_language'    => __( '--Choose language--', 'wpml-translation-management' ),
				'add_lang_pair_text' => __( 'Add another language pair', 'wpml-translation-management' ),
			),
			'languages'        => $this->active_languages,
			'source_languages' => $this->source_languages,
			'nonce'            => $this->get_nonce(),
		);
	}

	public function add_users() {
		global $wpdb;
		$language_pair_records = new WPML_Language_Pair_Records( $wpdb, new WPML_Language_Records( $wpdb ) );

		$this->model['users']                        = array();
		$this->model['all_users_have_subscriptions'] = true;

		$users = $this->user_records->get_users_with_capability();
		foreach ( $users as $user ) {
			$user->language_pairs = $language_pair_records->get( $user->ID );
			$user->subscription   = false;
			if ( $this->translator_activation_records ) {
				$user->subscription = $this->translator_activation_records->is_activated( $user->user_email );
			}
			if ( ! $user->subscription ) {
				$this->model['all_users_have_subscriptions'] = false;
			}
			$this->model['users'][] = $user;
		}
	}

	public function add_languages() {
		$this->model['languages'] = $this->source_languages;
	}

	public function add_nonce() {
		$this->model['nonce'] = $this->get_nonce();
	}

	public function add_capability() {
		$this->model['can_add_translators'] = current_user_can( WPML_Manage_Translations_Role::CAPABILITY );
	}

	private function get_nonce() {
		return wp_create_nonce( WPML_Translator_Ajax::NONCE_ACTION );
	}

	abstract public function get_twig_template();

	abstract public function get_template_paths();
}