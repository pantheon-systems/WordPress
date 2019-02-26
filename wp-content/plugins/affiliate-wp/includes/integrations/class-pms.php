<?php

class Affiliate_WP_PMS extends Affiliate_WP_Base {

    /**
     * Get things started
     *
     * @access public
     * @since  2.0
    */
    public function init() {

        $this->context = 'pms';

        // Subscription plan custom referral rate
        add_action( 'pms_view_meta_box_subscription_details_bottom', array( $this, 'add_subscription_view_rate_meta' ) );
        add_action( 'pms_save_meta_box_pms-subscription', array( $this, 'save_subscription_rate_meta' ) );

        // Reference link
        add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );

        // Discount codes affiliate tracking
        add_action( 'pms_view_meta_box_discount_codes_bottom', array( $this, 'add_discount_code_view_affiliate_tracking' ) );
        add_action( 'pms_save_meta_box_pms-discount-codes', array( $this, 'save_discount_code_affiliate_tracking_meta' ) );

        add_action( 'pms_register_payment', array( $this, 'add_pending_referral_on_register' ) );
        add_action( 'pms_payment_updated', array( $this, 'complete_pending_referral_on_payment_complete' ), 10, 3 );
        add_action( 'pms_payment_updated', array( $this, 'reject_referral_on_payment_refunded' ), 10, 3 );

    }

    /**
     * Adds the individual subscription plan referral rate field
     *
     * @access public
     * @since  2.0
     * @param  int $subscription_id
     */
    public function add_subscription_view_rate_meta( $subscription_id ) {

        if ( empty( $subscription_id ) ) {
            return;
        }

        $subscription_rate = get_post_meta( $subscription_id, '_affwp_pms_subscription_rate', true );
        ?>

        <div class="pms-meta-box-field-wrapper">
            <label for="affwp_subscription_rate" class="pms-meta-box-field-label"><?php echo __( 'Referral Rate', 'affiliate-wp' ); ?></label>
            <input type="text" id="affwp_subscription_rate" name="affwp_pms_subscription_rate" value="<?php echo esc_attr( $subscription_rate ) ?>" />
            <p class="description"><?php echo __( 'This rate will be used to calculate affiliate earnings when members register their account with this subscription. Leave blank to use the site default referral rate.', 'affiliate-wp' ); ?></p>
        </div>

        <?php
    }

    /**
     * Saves the individual subscription plan referral rate
     *
     * @access public
     * @since  2.0
     * @param  int $subscription_id
     */
    public function save_subscription_rate_meta( $subscription_id ) {

        if (  empty( $subscription_id ) ) {
            return;
        }

        $rate = isset( $_POST['affwp_pms_subscription_rate'] ) ? sanitize_text_field( $_POST['affwp_pms_subscription_rate'] ) : '';

        update_post_meta( $subscription_id, '_affwp_pms_subscription_rate', $rate );

    }

    /**
     * Adds the affiliate tracking field in the add/edit discount code meta-box
     *
     * @access public
     * @since  2.0
     * @param  int $discount_code_id
     */
    public function add_discount_code_view_affiliate_tracking( $discount_code_id ) {

        if ( empty( $discount_code_id ) ) {
            return;
        }

        add_filter( 'affwp_is_admin_page', '__return_true' );
        affwp_admin_scripts();

        $user_id      = '';
        $user_name    = '';
        $affiliate_id = get_post_meta( $discount_code_id, '_affwp_pms_affiliate_id', true );

        if ( $affiliate_id ) {
            $user_id   = affwp_get_affiliate_user_id( $affiliate_id );
            $user      = get_userdata( $user_id );
            $user_name = $user ? $user->user_login : '';
        }

        ?>

        <div class="pms-meta-box-field-wrapper">
            <label for="user_name" class="pms-meta-box-field-label"><?php echo __( 'Affiliate Discount?', 'affiliate-wp' ); ?></label>
            <input type="text" name="affwp_pms_user_name" id="user_name" value="<?php echo esc_attr( $user_name ); ?>" class="affwp-user-search" data-affwp-status="active" autocomplete="off" style="min-width: 250px;"/>
            <p class="description"><?php echo __( 'If you would like to connect this discount to an affiliate, enter the name of the affiliate it belongs to.', 'affiliate-wp' ); ?></p>
        </div>

        <?php
    }

    /**
     * Saves the affiliate id associated with the discount code
     *
     * @access public
     * @since  2.0
     * @param  int $discount_code_id
     */
    public function save_discount_code_affiliate_tracking_meta( $discount_code_id ) {

        if ( empty( $discount_code_id ) ) {
            return;
        }

        if ( empty( $_POST['affwp_pms_user_name'] ) ) {
            delete_post_meta( $discount_code_id, '_affwp_pms_affiliate_id' );
            return;
        }

        if ( empty( $_POST['affwp_pms_user_id'] ) && empty( $_POST['affwp_pms_user_name'] ) ) {
            return;
        }

        $data = affiliate_wp()->utils->process_request_data( $_POST, 'affwp_pms_user_name' );

        $affiliate_id = affwp_get_affiliate_id( $data['user_id'] );

        update_post_meta( $discount_code_id, '_affwp_pms_affiliate_id', $affiliate_id );

    }

    /**
     * Adds a pending referral when registering to a subscription plan
     * This handles only first time subscriptions, on the register form
     *
     * @access public
     * @since  2.0
     * @param  array $payment_data
     */
    public function add_pending_referral_on_register( $payment_data ) {

        // Allow referrals only on register subscription
        $allowed_form_locations = array( 'register', 'register_email_confirmation' );

        if ( ! in_array( $payment_data['form_location'], $allowed_form_locations ) ) {
            return;
        }

        $affiliate_discount = false;

        /*
         * Handle discount code referral
         */
        if ( ! empty( $payment_data['discount_code'] ) ) {

            $discount = pms_get_discount_by_code( $payment_data['discount_code'] );

            if ( $discount ) {

                $affiliate_id = get_post_meta( $discount->id, '_affwp_pms_affiliate_id', true );

                if ( ! empty( $affiliate_id ) ) {

                    if ( ! affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id ) ) {

						$this->log( 'Referral not created because affiliate is invalid.' );

                    } else {

                        $this->affiliate_id = $affiliate_id;
                        $affiliate_discount = true;

                        // Set base amount as the discounted value
                        $base_amount = $payment_data['amount'];

                    }

                }

            }

        }

        // Do nothing if we have no referral and no affiliate on discount
        if ( ! $this->was_referred() && ! $affiliate_discount ) {
            return;
        }

        if ( $this->was_referred() && ! $affiliate_discount ) {

            /**
             * Check to see if the subscriber is also an affiliate. Return if true.
             */
            if ( $this->is_affiliate_email( $payment_data['user_data']['user_id'] ) ) {

				$this->log( 'Referral not created because affiliate\'s own account was used.' );

                return;
            }

        }

        // Set base amount for referral
        if ( !isset( $base_amount ) ) {
            $base_amount = $payment_data['user_data']['subscription']->price;
        }

        // If the base amount is zero and it's set to ignore zero amounts, exit
        if ( 0 == $base_amount && affiliate_wp()->settings->get( 'ignore_zero_referrals' ) ) {

			$this->log( 'Referral not created due to 0.00 amount.' );

            return;
        }

        // Calculate the referral amount
        $referral_amount = $this->calculate_referral_amount( $base_amount, $payment_data['payment_id'], $payment_data['user_data']['subscription']->id, $this->affiliate_id );

        // Insert the pending referral
        $this->insert_pending_referral( $referral_amount, $payment_data['payment_id'], $payment_data['user_data']['subscription']->name );

    }

    /**
     * Completes the referral when the payment is updated
     *
     * @access public
     * @since  2.0
     * @param  bool $updated
     * @param  array $data
     * @param  int $payment_id
     */
    public function complete_pending_referral_on_payment_complete( $updated, $data, $payment_id ) {

        if ( ! $updated ) {
            return;
        }

        if ( empty( $data['status'] ) ) {
            return;
        }

        if ( $data['status'] == 'completed' ) {
            $this->complete_referral( $payment_id );
        }

    }

    /**
     * Rejects a referral when the status of the payment is set to refunded
     *
     * @access public
     * @since  2.0
     * @param  bool $updated
     * @param  array $data
     * @param  int $payment_id
     */
    public function reject_referral_on_payment_refunded( $updated, $data, $payment_id ) {

        if ( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
            return;
        }

        if ( ! $updated ) {
            return;
        }

        if ( empty( $data['status'] ) ) {
            return;
        }

        if ( $data['status'] == 'refunded' ) {
            $this->reject_referral( $payment_id );
        }

    }

    /**
     * Creates a link to the payment referenced in the referral
     * @since  2.0
     * @access public
     */
    public function reference_link( $reference = 0, $referral ) {

        if ( empty( $referral->context ) || 'pms' != $referral->context ) {
            return $reference;
        }

        $url = admin_url( 'admin.php?page=pms-payments-page&pms-action=edit_payment&payment_id=' . $reference );

        return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';

    }

    /**
     * Retrieves the rate and type for a specific subscription plan
     *
     * @access public
     * @since  2.0
     * @param  int $subscription_id
     * @param  array $args
     * @return float
     */
    public function get_product_rate( $subscription_id = 0, $args = array() ) {

        $rate = get_post_meta( $subscription_id, '_affwp_pms_subscription_rate', true );

        if ( empty( $rate ) || ! is_numeric( $rate ) ) {
            $rate = null;
        }

        /**
         * This filter is documented in the base class
         */
        return apply_filters( 'affwp_get_product_rate', $rate, $subscription_id, $args, $this->affiliate_id, $this->context );

    }

}

if ( function_exists( 'pms_get_discount_by_code' ) ) {
	new Affiliate_WP_PMS;
}
