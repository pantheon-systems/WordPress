<?php

class WPML_End_User_Notice_Collection {
	/** @var WPML_Notices */
	private $notices;

	/** @var array  */
	private $cache = array();

	/** @var WPML_Twig_Template */
	private $twig_service;

	/**
	 * @param WPML_Notices $notices
	 * @param WPML_Twig_Template $twig_service
	 */
	public function __construct( WPML_Notices $notices, WPML_Twig_Template $twig_service ) {
		$this->notices = $notices;
		$this->twig_service = $twig_service;
	}

	/**
	 * @param $user_id
	 */
	public function add( $user_id ) {
		$this->notices->add_notice( $this->get_notice_by_user_id( $user_id ) );
	}

	/**
	 * @param $user_id
	 */
	public function remove( $user_id ) {
		$this->notices->remove_notice( WPML_End_User_Notice::NOTICE_GROUP, $user_id );
	}

	/**
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function is_dismissed( $user_id ) {
		return $this->notices->is_notice_dismissed( $this->get_notice_by_user_id( $user_id ) );
	}

	/**
	 * @param int $user_id
	 *
	 * @return WPML_End_User_Notice
	 */
	private function get_notice_by_user_id( $user_id ) {
		if ( ! array_key_exists( $user_id, $this->cache ) ) {
			$this->cache[ $user_id ] = new WPML_End_User_Notice( $user_id, $this->twig_service );
		}

		return $this->cache[ $user_id ];
	}
}
