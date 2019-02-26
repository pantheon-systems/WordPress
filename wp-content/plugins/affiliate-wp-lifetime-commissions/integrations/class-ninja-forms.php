<?php

class Affiliate_WP_Lifetime_Commissions_Ninja_Forms extends Affiliate_WP_Lifetime_Commissions_Base {

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
    public function init() {
        $this->context = 'ninja-forms';
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

        global $ninja_forms_processing;

        if ( ! $get ) {
            return false;
        }

        $user_info = $ninja_forms_processing->get_user_info();

        if ( 'email' === $get ) {

            if ( isset( $user_info['billing']['email'] ) ) {
                return $user_info['billing']['email'];
            } else {
                return $user_info['email'];
            }

        } elseif ( 'user_id' === $get ) {
            $post = get_post( $reference );
            return $post->post_author;
        }
        return false;

    }

}
new Affiliate_WP_Lifetime_Commissions_Ninja_Forms;
