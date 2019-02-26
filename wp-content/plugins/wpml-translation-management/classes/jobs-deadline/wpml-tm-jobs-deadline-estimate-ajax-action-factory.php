<?php

class WPML_TM_Jobs_Deadline_Estimate_AJAX_Action_Factory extends WPML_AJAX_Base_Factory {

	/** @return null|WPML_TM_Jobs_Deadline_Estimate_AJAX_Action */
	public function create() {
		$hooks = null;

		if ( $this->is_valid_action( 'wpml-tm-jobs-deadline-estimate-ajax-action' ) ) {
			$deadline_estimate_factory = new WPML_TM_Jobs_Deadline_Estimate_Factory();

			$hooks = new WPML_TM_Jobs_Deadline_Estimate_AJAX_Action(
				$deadline_estimate_factory->create(),
				TranslationProxy_Basket::get_basket(),
				$_POST
			);
		}

		return $hooks;
	}
}
