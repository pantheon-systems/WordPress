<?php

class WPML_Upgrade_Admins_To_Manage_Translations implements IWPML_Action {

	/** @var bool $wpml_is_setup */
	private $wpml_is_setup;

	/** @var wpdb $wpdb */
	private $wpdb;

	public function __construct( $wpml_is_setup, wpdb $wpdb ) {
		$this->wpml_is_setup = $wpml_is_setup;
		$this->wpdb          = $wpdb;
	}

	public function add_hooks() {
		if ( $this->wpml_is_setup ) { // Only upgrade if wpml was previously setup
			if( ! did_action( 'wpml_loaded' ) ) {
				add_action( 'wpml_loaded', array( $this, 'upgrade_manager_caps' ) );
			} else {
				$this->upgrade_manager_caps();
			}
		}
		update_option( WPML_Upgrade_Admins_To_Manage_Translations_Factory::HAS_RUN_OPTION, true );
	}

	public function upgrade_manager_caps() {
		$translation_managers = $this->wpdb->get_col(
			"SELECT DISTINCT manager_id FROM {$this->wpdb->prefix}icl_translate_job"
		);
		foreach ( $translation_managers as $user_id ) {
			$user = get_user_by( 'ID', $user_id );

			if ( $user ) {
			    $user->add_cap( WPML_Manage_Translations_Role::CAPABILITY );
			}
		}
	}
}
