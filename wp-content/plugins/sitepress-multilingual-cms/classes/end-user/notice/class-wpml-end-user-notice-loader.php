<?php

class WPML_End_User_Notice_Loader implements IWPML_Action {

	/** @var  WPML_End_User_Notice_Validate */
	private $validator;

	/** @var  WPML_End_User_Notice_Collection */
	private $notice_collection;

	/**
	 * @param WPML_End_User_Notice_Validate $validator
	 * @param WPML_End_User_Notice_Collection $notice_collection
	 */
	public function __construct(
		WPML_End_User_Notice_Validate $validator,
		WPML_End_User_Notice_Collection $notice_collection
	) {
		$this->validator = $validator;
		$this->notice_collection = $notice_collection;
	}


	public function add_hooks() {
		$user_id = get_current_user_id();

		if ( ! $this->validator->is_valid( $user_id ) ) {
			$this->notice_collection->remove( $user_id );
			return;
		}

		if ( $this->notice_collection->is_dismissed( $user_id ) ) {
			return;
		}

		$this->notice_collection->add( $user_id );
	}
}
