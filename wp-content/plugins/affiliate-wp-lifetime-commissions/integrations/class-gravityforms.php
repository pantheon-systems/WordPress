<?php

class Affiliate_WP_Lifetime_Commissions_Gravity_Forms extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
    public function init() {
        $this->context = 'gravityforms';
    }

    /**
     * Retrieves the user's email or ID
     *
     * @param string $get what to retrieve
     * @param int $reference Payment reference number
     *
     * @since 1.1
     */
    public function get( $get = '', $reference = 0, $context ) {

        if ( ! $get ) {
            return false;
        }

        if( empty( $reference ) ) {
            return false;
        }

        $entry = GFFormsModel::get_lead( $reference );

        if( empty( $entry ) ) {
            return false;
        }

        $form  = GFAPI::get_form( $entry['form_id'] );

        // get email field
        $email_fields = GFCommon::get_email_fields( $form );

        $field_id = '';

        // get value of first email field. The form should only have 1 email field if it's a product form
        if ( $email_fields ) {
            foreach ( $email_fields as $email_field ) {
                $field_id = $email_field['id'];
                break;
            }
        }

        if ( 'email' === $get ) {
            return $entry[$field_id];
        } elseif ( 'user_id' === $get ) {
            return $entry['created_by'];
        }

        return false;

    }

}
new Affiliate_WP_Lifetime_Commissions_Gravity_Forms;
