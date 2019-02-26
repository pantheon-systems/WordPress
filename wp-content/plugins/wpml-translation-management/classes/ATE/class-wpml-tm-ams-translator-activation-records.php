<?php

class WPML_TM_AMS_Translator_Activation_Records {

	const USER_META = 'ate_activated';

	/** @var WPML_WP_User_Factory $user_factory */
	private $user_factory;

	public function __construct( WPML_WP_User_Factory $user_factory ) {
		$this->user_factory = $user_factory;
	}

	public function is_activated( $user_email ) {
		return $this->is_user_activated( $this->user_factory->create_by_email( $user_email ) );
	}

	public function  is_current_user_activated() {
		return $this->is_user_activated( $this->user_factory->create_current() );
	}

	public function  is_user_activated( WPML_User $user ) {
		return (bool) $user->get_option( self::USER_META );
	}

	public function set_activated( $user_email, $state ) {
		$user = $this->user_factory->create_by_email( $user_email );
		if ( $user->ID ) {
			return $user->update_option( self::USER_META, $state );
		}
	}

	public function update( array $translators ) {
		foreach ( $translators as $translator ) {
			$this->set_activated( $translator['email'], $translator['subscription'] );
		}
	}

}