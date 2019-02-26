<?php

class WPML_Translator_Ajax extends WPML_Translation_Roles_Ajax {

	const NONCE_ACTION = 'wpml_translator_actions';

	/** @var WPML_Language_Pair_Records $language_pair_records */
	private $language_pair_records;

	public function __construct(
		IWPML_Translation_Roles_View $view,
		WPML_Translation_Roles_Records $records,
		WPML_Super_Globals_Validation $post_vars,
		WPML_WP_User_Factory $user_factory,
		WPML_Language_Pair_Records $language_pair_records
	) {
		parent::__construct( $view, $records, $post_vars, $user_factory );
		$this->language_pair_records = $language_pair_records;
	}

	public function get_role() {
		return 'translator';
	}

	public function get_nonce() {
		return self::NONCE_ACTION;
	}

	public function get_capability() {
		return WPML_Translator_Role::CAPABILITY;
	}

	public function get_user_row_template() {
		return 'translators-row.twig';
	}

	public function on_user_created( WP_User $user ) {
		$language_pairs = $this->post_vars->post('languagePairs' );
		$this->language_pair_records->store( $user->ID, $language_pairs );
		$user->data->language_pairs = $this->post_vars->post('languagePairs' );
	}

	public function send_instructions_to_user( WP_User $user ) {
		// Not needed at this stage.
	}

}