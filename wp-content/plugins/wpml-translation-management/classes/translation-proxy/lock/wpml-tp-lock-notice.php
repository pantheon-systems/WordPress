<?php

class WPML_TP_Lock_Notice implements IWPML_Action {

	const NOTICE_GROUP  = 'tp-lock';
	const NOTICE_LOCKED = 'locked';

	/** @var WPML_TP_Lock $tp_lock */
	private $tp_lock;

	/** @var WPML_Notices $notices */
	private $notices;

	public function __construct( WPML_TP_Lock $tp_lock, WPML_Notices $notices ) {
		$this->tp_lock = $tp_lock;
		$this->notices = $notices;
	}

	public function add_hooks() {
		add_action( 'admin_init', array( $this, 'handle_notice' ) );
	}

	public function handle_notice() {
		$locker_reason = $this->tp_lock->get_locker_reason();

		if ( (bool) $locker_reason ) {
			$text = '<p>' . __( 'Some communications with the translation proxy are locked.', 'wpml-translation-management' ) . '</p>';
			$text .= '<p>' . $locker_reason . '</p>';
			$notice = $this->notices->create_notice( self::NOTICE_LOCKED, $text, self::NOTICE_GROUP );
			$notice->set_css_class_types( 'notice-warning' );
			$this->notices->add_notice( $notice );
		} else {
			$this->notices->remove_notice( self::NOTICE_GROUP, self::NOTICE_LOCKED );
		}
	}
}