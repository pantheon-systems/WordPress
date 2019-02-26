<?php

class AffiliateWP_Caldera_Forms_Processor extends Caldera_Forms_Processor_Processor {

	/**
	 * Pre Processor
	 *
	 * @access public
	 * @since 2.0
	 */
	public function pre_processor( array $config, array $form, $process_id ) {

		$integration  = new Affiliate_WP_Caldera_Forms;

		// Get entry ID
		$submission_data = Caldera_Forms::get_submission_data( $form );

		// Set the entry ID to the processor ID
		$entry_id = $process_id;

		// Get values of all settings for this processor
		$this->set_data_object_initial( $config, $form );

		// Get total conversion value set in processor whether it is hardcoded or field value
		$total = $this->data_object->get_value( 'total' );

		// Get referral total
		$referral_total = $integration->calculate_referral_amount( $total, $entry_id );

		// Set the arguments
		$args = array(
			'entry_id'               => $entry_id,
			'referral_total'         => $referral_total,
			'mark_referral_complete' => false
		);

		/**
		 * Add a pending referral
		 * The processor() method marks the referral as complete
		 */
		$integration->add_pending_referral( $args, $form );

	}

	/**
	 * Processor
	 *
	 * Payments will have been processed by now. If they generate an error, this method will not run.
	 *
	 * @access public
	 * @since 2.0
	 */
	public function processor( array $config, array $form, $process_id ) {

		$integration = new Affiliate_WP_Caldera_Forms;
		$integration->mark_referral_complete( '', $form, $process_id );

    }

}
