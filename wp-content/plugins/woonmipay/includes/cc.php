<?php

add_action( 'woocommerce_payment_token_deleted', 'nmipay_payment_token_deleted', 10, 2 );

/**
 * Delete token from NMI
 */
function nmipay_payment_token_deleted( $token_id, $token ) {

  $gateway = new woocommerce_nmipay();

  if ( 'nmipay' === $token->get_gateway_id() ) {

    $provider = $gateway->provider;

    $nmi_adr = $gateway->$provider . '?';

    $nmipay_args['username'] = $gateway->username;
    $nmipay_args['password'] = $gateway->password;
    $nmipay_args['customer_vault'] = 'delete_customer';
    $nmipay_args['customer_vault_id'] = $token->get_token();

    $name_value_pairs = array();
    foreach ($nmipay_args as $key => $value) {
      $name_value_pairs[] = $key . '=' . urlencode($value);
    }
    $gateway_values =	implode('&', $name_value_pairs);

    $response = wp_remote_post( $nmi_adr.$gateway_values, array( 'sslverify' => false, 'timeout' => 60 ) );

    if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
      parse_str($response['body'], $response);
      if($response['response']== '1'){
        return true;
      }else{
        if( strpos($response['responsetext'], __('Invalid Customer Vault Id', 'woo-nmi-patsatech') ) !== false ){
          return false;
        }else{
          wc_add_notice( sprintf( __('Deleting card failed. %s-%s', 'woo-nmi-patsatech'), $response['response_code'], $response['responsetext'] ), $notice_type = 'error' );
          return false;
        }
      }
    }else{
      wc_add_notice( __('There was error processing your request.', 'woo-nmi-patsatech'), $notice_type = 'error' );
      return false;
    }
  }
}

class woocommerce_nmipay extends WC_Payment_Gateway_CC {

  public function __construct() {
    global $woocommerce;
    $this->id			= 'nmipay';
    $this->method_title = __( 'Network Merchants Inc - CC', 'woo-nmi-patsatech' );
    $this->icon			= apply_filters( 'woocommerce_nmipay_icon', '' );
    $this->has_fields 	= TRUE;

    $this->nmi				= 'https://secure.networkmerchants.com/api/transact.php';
    $this->alphacard 	= 'https://secure.alphacardgateway.com/api/transact.php';
    $this->llms			 = 'https://secure.llmsgateway.com/api/transact.php';
    $this->powerpay		= 'https://verifi.powerpay.biz/api/transact.php';
    $this->sbg				= 'https://secure.skybankgateway.com/api/transact.php';
    $this->mgg				= 'https://secure.merchantguygateway.com/api/transact.php';
    $this->durango 		 = 'https://secure.durango-direct.com/api/transact.php';
    $this->tnbci				= 'https://secure.tnbcigateway.com/api/transact.php';
    $this->payscape 		 = 'https://secure.payscapegateway.com/api/transact.php';
    $this->paylinedata	 = 'https://secure.paylinedatagateway.com/api/transact.php';
    $this->inspire 		= 'https://secure.inspiregateway.net/api/transact.php';
    $this->safesave		= 'https://secure.safesavegateway.com/api/transact.php';
    $this->tranzcrypt	= 'https://secure.tranzcrypt.com/api/transact.php';
    $this->planetauth	= 'https://secure.planetauthorizegateway.com/api/transact.php';
    $this->cyogate 		= 'https://secure.cyogate.net/api/transact.php';
    $this->supports  = array(
      'products',
      'default_credit_card_form',
      'refunds',
      'tokenization',
      'subscriptions',
      'subscription_cancellation',
      'subscription_suspension',
      'subscription_reactivation',
      'subscription_payment_method_change'
    );

    $default_card_type_options = array(
      'VISA' => 'VISA',
      'MC' => 'MasterCard',
      'AMEX' => 'American Express',
      'DISC' => 'Discover',
      'DC' => 'Diner\'s Club',
      'JCB' => 'JCB Card'
    );

    $this->card_type_options = apply_filters( 'woocommerce_nmipay_card_types', $default_card_type_options );

    // Load the form fields.
    $this->init_form_fields();

    // Load the settings.
    $this->init_settings();

    // Define user set variables
    $this->title       = $this->get_option('title');
    $this->description = $this->get_option('description');
    $this->username    = $this->get_option('username');
    $this->password    = $this->get_option('password');
    $this->provider    = $this->get_option('provider');
    $this->transtype   = $this->get_option('transtype');
    $this->cardtypes   = $this->get_option('cardtypes');
    $this->saved_cards = 'yes' === $this->get_option( 'saved_cards' );
    $this->receipt     = 'yes' === $this->get_option( 'receipt' );
    $this->debug       = $this->get_option('debug');

    if( $this->provider == ''){
      $this->provider = 'nmi';
    }

    if( $this->transtype == ''){
      $this->transtype = 'sale';
    }

    // Logs
    if ($this->debug=='yes'){
      $this->log = new WC_Logger();
    }

    // Actions
    add_action( 'woocommerce_update_options_payment_gateways', array( $this, 'process_admin_options' ) );
    add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(	$this, 'process_admin_options' ) );

    if ( class_exists( 'WC_Subscriptions_Order' ) ) {
      add_action( 'cancelled_subscription_' . $this->id, array( $this, 'cancelled_subscription' ), 10, 2 );
    }

    if ( !$this->is_valid_for_use() ) $this->enabled = false;

  }

  /**
   * cancelled_subscription function.
   *
   * @param float $amount_to_charge The amount to charge.
   * @param WC_Order $renewal_order A WC_Order object created to record the renewal payment.
   */
  public function cancelled_subscription( $order, $product_id ) {

    $profile_id = self::get_subscriptions_nmipay_id( $order, $product_id );

    // Make sure a subscriptions status is active with NMI
    $nmipay_args['username'] = $this->username;
    $nmipay_args['password'] = $this->password;
    $nmipay_args['recurring'] = 'delete_subscription';
    $nmipay_args['subscription_id'] = $profile_id;

    $name_value_pairs = array();
    foreach ($nmipay_args as $key => $value) {
      $name_value_pairs[] = $key . '=' . urlencode($value);
    }
    $gateway_values =  implode('&', $name_value_pairs);

    $provider = $this->provider;

    $nmi_adr = $this->$provider . '?';

    $response = wp_remote_post($nmi_adr.$gateway_values, array( 'sslverify' => false, 'timeout' => 60 ));

    if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
      parse_str($response['body'], $response);

      $item = WC_Subscriptions_Order::get_item_by_product_id( $order, $product_id );

      if($response['response']== '1'){
        $order->add_order_note( sprintf( __( 'Subscription "%s" cancelled with NMI', 'woo-nmi-patsatech' ), $item['name'] ) );
      }else {
        $order->add_order_note(  __( 'There was error cancelling the Subscription with NMI', 'woo-nmi-patsatech' ) );
      }

    }

  }

  /**
   * Returns a NMI Subscription ID/Recurring Payment Profile ID based on a user ID and subscription key
   *
   * @since 1.1
   */
  public static function get_subscriptions_nmipay_id( $order, $product_id ) {

    $profile_id = get_post_meta( $order->id, 'NMI Subscriber ID', true );

    return $profile_id;
  }

  /**
  * Check if this gateway is enabled and available in the user's country
  */
  function is_valid_for_use() {
      if ( !in_array( get_option('woocommerce_currency'), array('AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP') ) ) return false;

      return true;
  }

  /**
  * get_icon function.
  *
  * @access public
  * @return string
  */
  function get_icon() {
    global $woocommerce;

    $icon = '';
    if ( $this->icon ) {
      // default behavior
      $icon = '<img src="' . $this->force_ssl( $this->icon ) . '" alt="' . $this->title . '" />';
    } elseif ( $this->cardtypes ) {
      // display icons for the selected card types
      $icon = '';
      foreach ( $this->cardtypes as $cardtype ) {
        if ( file_exists( plugin_dir_path( __FILE__ ) . '/images/card-' . strtolower( $cardtype ) . '.png' ) ) {
          $icon .= '<img src="' . $this->force_ssl( plugins_url( '/images/card-' . strtolower( $cardtype ) . '.png', __FILE__ ) ) . '" alt="' . strtolower( $cardtype ) . '" />';
        }
      }
    }

    return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
  }

  private function force_ssl($url){

    if ( 'yes' == get_option( 'woocommerce_force_ssl_checkout' ) ) {
      $url = str_replace( 'http:', 'https:', $url );
    }

    return $url;
  }

  /**
   * Admin Panel Options
   * - Options for bits like 'title' and availability on a country-by-country basis
   *
   * @since 1.0.0
   */
  public function admin_options() {

    ?>
    <h3><?php _e('Network Merchants Inc', 'woo-nmi-patsatech'); ?></h3>
    <p><?php _e('Network Merchants Inc - CC works by processing the Credit Card Payments on your site.', 'woo-nmi-patsatech'); ?></p>
    <table class="form-table">
    <?php
      if ( $this->is_valid_for_use() ) :

        // Generate the HTML For the settings form.
        $this->generate_settings_html();

      else :

        ?>
              <div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'woo-nmi-patsatech' ); ?></strong>: <?php _e( 'NMI does not support your store currency.', 'woo-nmi-patsatech' ); ?></p></div>
          <?php

      endif;
      ?>
    </table><!--/.form-table-->
    <?php
  } // End admin_options()

  /**
   *  Initialise Gateway Settings Form Fields
   */
  function init_form_fields() {

    $this->form_fields = array(
      'enabled' => array(
        'title' => __( 'Enable/Disable', 'woo-nmi-patsatech' ),
        'type' => 'checkbox',
        'label' => __( 'Enable Network Merchants Inc - CC', 'woo-nmi-patsatech' ),
        'default' => 'yes',
        'desc_tip'    => true,
      ),
      'title' => array(
        'title' => __( 'Title', 'woo-nmi-patsatech' ),
        'type' => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'woo-nmi-patsatech' ),
        'default' => __( 'Credit Card', 'woo-nmi-patsatech' ),
        'desc_tip'    => true,
      ),
      'description' => array(
        'title' => __( 'Description', 'woo-nmi-patsatech' ),
        'type' => 'textarea',
        'description' => __( 'This controls the description which the user sees during checkout.', 'woo-nmi-patsatech' ),
        'default' => __("Pay via NMI; you can pay with your credit card.", 'woo-nmi-patsatech'),
        'desc_tip'    => true,
      ),
      'username' => array(
        'title' => __( 'UserName', 'woo-nmi-patsatech' ),
        'type' => 'text',
        'description' => __( 'Please enter your UserName; this is needed in order to take payment.', 'woo-nmi-patsatech' ),
        'default' => '',
        'desc_tip'    => true,
      ),
      'password' => array(
        'title' => __( 'Password', 'woo-nmi-patsatech' ),
        'type' => 'text',
        'description' => __( 'Please enter your Password; this is needed in order to take payment.', 'woo-nmi-patsatech' ),
        'default' => '',
        'desc_tip'    => true,
      ),
      'receipt' => array(
        'title' => __( 'Enable/Disable Customer Receipt', 'woo-nmi-patsatech' ),
        'type' => 'checkbox',
        'label' => __( 'Enable Customer Receipt', 'woo-nmi-patsatech' ),
        'description' => __( 'If enabled, when the customer is charged, they will be sent a transaction receipt.', 'woo-nmi-patsatech' ),
        'default' => 'yes',
        'desc_tip'    => true,
      ),
      'saved_cards' => array(
        'title'       => __( 'Save Cards', 'woo-nmi-patsatech' ),
        'label'       => __( 'Enable Payment via Saved Cards', 'woo-nmi-patsatech' ),
        'type'        => 'checkbox',
        'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on NMI servers, not on your store.', 'woo-nmi-patsatech' ),
        'default'     => 'no',
        'desc_tip'    => true,
      ),
      'transtype' => array(
        'title' => __('Transaction Type', 'woo-nmi-patsatech'),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'options' => array(
          'sale'			=> 'Sale (Authorize and Capture)',
          'auth'		=> 'Authorize Only'
        ),
        'description' => __( 'Select your Transaction Type.', 'woo-nmi-patsatech' ),
        'default' => 'sale',
        'desc_tip'    => true,
      ),
      'provider' => array(
        'title' => __('Payment Gateway', 'woo-nmi-patsatech'),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'options' => array(
          'nmi'			=> 'Network Merchants Inc',
          'alphacard'		=> 'Alpha Card Services',
          'llms'			=> 'LL Merchant Solutions',
          'powerpay'		=> 'PowerPay',
          'sbg'			=> 'SkyBank',
          'mgg'			=> 'MerchantGuy',
          'durango'		=> 'Durango Merchant Services',
          'tnbci'			=> 'TransNational Bankcard Inc',
          'payscape'		=> 'Payscape',
          'paylinedata'	=> 'Payline Data',
          'inspire'		=> 'Inspire Commerce',
          'safesave'		=> 'SafeSave Payments',
          'tranzcrypt'	=> 'Tranzcrypt',
          'planetauth'	=> 'PlanetAuthorize',
          'cyogate'		=> 'CyoGate'
        ),
        'description' => __( 'Select your Merchant Account Provider.', 'woo-nmi-patsatech' ),
        'default' => 'nmi',
        'desc_tip'    => true,
      ),
      'cardtypes'	=> array(
        'title' => __( 'Accepted Cards', 'woo-nmi-patsatech' ),
        'class' => 'wc-enhanced-select',
        'type' => 'multiselect',
        'description' => __( 'Select which card types to accept.', 'woo-nmi-patsatech' ),
        'default' => 'VISA',
        'options' => $this->card_type_options,
        'desc_tip'    => true,
      ),
      'debug' => array(
        'title'       => __( 'Debug log', 'woo-nmi-patsatech' ),
        'type'        => 'checkbox',
        'label'       => __( 'Enable logging', 'woo-nmi-patsatech' ),
        'default'     => 'no',
        'description' => sprintf( __( 'Log NMI requests, inside %s', 'woo-nmi-patsatech' ), '<code>' . WC_Log_Handler_File::get_log_file_path( 'nmipay' ) . '</code>' ),
      )
    );
  } // End init_form_fields()

  /**
   * There are no payment fields for nmi, but we want to show the description if set.
   **/
  function payment_fields() {
    $user                 = wp_get_current_user();
    $display_tokenization = $this->supports( 'tokenization' ) && is_checkout() && $this->saved_cards && $user->ID;

    if ( $user->ID ) {
      $user_email = get_user_meta( $user->ID, 'billing_email', true );
      $user_email = $user_email ? $user_email : $user->user_email;
    } else {
      $user_email = '';
    }

    if ( is_add_payment_method_page() ) {
      $pay_button_text = __( 'Add Card', 'woo-nmi-patsatech' );
    } else {
      $pay_button_text = '';
    }

    if ( $this->description ) {
      echo wpautop( wp_kses_post( $this->description ) );
    }

    if ( $display_tokenization ) {
      $this->tokenization_script();
      $this->saved_payment_methods();
    }
    wp_enqueue_script( 'wc-credit-card-form' );
    //$this->form();
    echo '<fieldset id="nmipay-cc-form" class="wc-credit-card-form wc-payment-form">
      <p class="form-row form-row-wide">
        <label for="nmipay-card-number">' . esc_html__( 'Card Number', 'woo-nmi-patsatech' ) . ' <span class="required">*</span></label>
        <input id="nmipay-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="nmipay-card-number" />
      </p>
      <p class="form-row form-row-first">
        <label for="nmipay-card-expiry">' . esc_html__( 'Expiry (MM/YY)', 'woo-nmi-patsatech' ) . ' <span class="required">*</span></label>
        <input id="nmipay-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" maxlength="7" autocomplete="off" placeholder="MM / YY" name="nmipay-card-expiry" />
      </p>
      <p class="form-row form-row-last">
        <label for="nmipay-card-cvc">' . esc_html__( 'Card Code', 'woo-nmi-patsatech' ) . ' <span class="required">*</span></label>
        <input id="nmipay-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" maxlength="4" placeholder="CVC" name="nmipay-card-cvc" style="width:100px"  />
      </p>
      <div class="clear"></div>
    </fieldset>';

    if ( $display_tokenization ) {
      $this->save_payment_method_checkbox();
    }

  }

  public function validate_fields(){
    global $woocommerce;

    if ( isset( $_POST['wc-nmipay-payment-token'] ) && 'new' !== $_POST['wc-nmipay-payment-token'] ) {
      $token_id = wc_clean( $_POST['wc-nmipay-payment-token'] );
      $token    = WC_Payment_Tokens::get( $token_id );
      if ( $token->get_user_id() !== get_current_user_id() ) {
          // Optionally display a notice with `wc_add_notice`
          wc_add_notice( __('There was error processing payment using token please use the card details to continue the checkout.', 'woo-nmi-patsatech'), $notice_type = 'error' );
      }
    }else{
      if ( !$this->is_empty_credit_card( $_POST['nmipay-card-number'] ) ){
        wc_add_notice( '<strong>Credit Card Number</strong> ' . __( 'is a required field.', 'woo-nmi-patsatech' ), 'error' );
      }elseif ( !$this->is_valid_credit_card( $_POST['nmipay-card-number'] ) ){
        wc_add_notice( '<strong>Credit Card Number</strong> ' . __( 'is not a valid credit card number.', 'woo-nmi-patsatech' ), 'error' );
      }

      if ( !$this->is_empty_expire_date( $_POST['nmipay-card-expiry'] ) ){
        wc_add_notice( '<strong>Card Expiry Date</strong> ' . __( 'is a required field.', 'woo-nmi-patsatech' ), 'error' );
      }elseif ( !$this->is_valid_expire_date( $_POST['nmipay-card-expiry'] ) ){
        wc_add_notice( '<strong>Card Expiry Date</strong> ' . __( 'is not a valid expiry date.', 'woo-nmi-patsatech' ), 'error' );
      }

      if ( !$this->is_empty_ccv_nmber( $_POST['nmipay-card-cvc'] ) ){
        wc_add_notice( '<strong>CCV Number</strong> ' . __( 'is a required field.', 'woo-nmi-patsatech' ), 'error' );
      }

    }

  }

  /**
   * Process the payment and return the result
   **/
  function process_payment( $order_id ) {
    global $woocommerce;

    $order = new WC_Order( $order_id );

    $credit_card = preg_replace( '/(?<=\d)\s+(?=\d)/', '', trim( $_POST['nmipay-card-number'] ) );
    $ccexp_expiry = str_replace(' ','',$_POST['nmipay-card-expiry']);
    $month = substr( $ccexp_expiry , 0, 2 );
    $year = substr( $ccexp_expiry , 3, 4 );
    $provider = $this->provider;
    $cardtype = $this->getCardType($credit_card);

    $nmi_adr = $this->$provider . '?';

    $tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'nmipay' );

    if ( $this->debug == 'yes' ){
      $this->log->add( 'nmipay', $order_id.' - CC Processing Started' );
    }

    if( isset($_POST['wc-nmipay-new-payment-method']) && count($tokens) == 0 ){

      $nmipay_args['customer_vault'] = 'add_customer';
      $nmipay_args['ccnumber'] = $credit_card;
      $nmipay_args['cvv'] = $_POST["nmipay-card-cvc"];
      $nmipay_args['ccexp'] = $month.'/'.$year;

      $last_four_digits = substr($nmipay_args['ccnumber'], -4);

      if ( $this->debug == 'yes' ){
        $this->log->add( 'nmipay', $order_id.' - Token Count: ' . count($tokens) . ' Token Param: ' . $_POST['wc-nmipay-new-payment-method'] );
      }

    }else if( isset($_POST['wc-nmipay-new-payment-method']) && count($tokens) > 0 ){

      $token = WC_Payment_Tokens::get( get_user_meta( get_current_user_id(), 'nmi_cc_token_id', true ) );

      $nmipay_args['customer_vault'] = 'update_customer';
      $nmipay_args['customer_vault_id'] = $token->get_token();
      $nmipay_args['ccnumber'] = $credit_card;
      $nmipay_args['cvv'] = $_POST["nmipay-card-cvc"];
      $nmipay_args['ccexp'] = $month.'/'.$year;

      $last_four_digits = substr($nmipay_args['ccnumber'], -4);

      if ( $this->debug == 'yes' ){
        $this->log->add( 'nmipay', $order_id.' - Custom Saved Token : ' . $token->get_id() . ' Token Param: ' . $_POST['wc-nmipay-new-payment-method'] );
      }

    }else if ( isset( $_POST['wc-nmipay-payment-token'] ) && 'new' !== $_POST['wc-nmipay-payment-token'] ) {

      $token_id = wc_clean( $_POST['wc-nmipay-payment-token'] );
      $token    = WC_Payment_Tokens::get( $token_id );
      $nmipay_args['customer_vault'] = 'update_customer';
      $nmipay_args['customer_vault_id'] = $token->get_token();

      if ( $this->debug == 'yes' ){
        $this->log->add( 'nmipay', $order_id.' - Token : ' . $token_id . ' Token Param: ' . $_POST['wc-nmipay-new-payment-method'] );
      }

    }else{

      $nmipay_args['ccnumber'] = $credit_card;
      $nmipay_args['cvv'] = $_POST["nmipay-card-cvc"];
      $nmipay_args['ccexp'] = $month.'/'.$year;

      $last_four_digits = substr($credit_card, -4);

      if ( $this->debug == 'yes' ){
        $this->log->add( 'nmipay', $order_id.' - New Card with no Tokenisation '  );
      }

    }

    if( $this->receipt ){
      $nmipay_args['customer_receipt'] = $this->receipt;
    }

      //wp_die("TETSTDTDTDTDTDTDT"); exit;

    // Processing subscription
    if ( function_exists( 'wcs_order_contains_subscription' ) || function_exists( 'wcs_is_subscription' )  ) {

      if( wcs_order_contains_subscription( $order_id ) || wcs_is_subscription( $order_id ) ){

        $nmipay_args['type'] = $this->transtype;
        $nmipay_args['payment'] = 'creditcard';
        $nmipay_args['ipaddress'] = $_SERVER['REMOTE_ADDR'];
        $nmipay_args['username'] = $this->username;
        $nmipay_args['password'] = $this->password;
        $nmipay_args['currency'] = get_woocommerce_currency();

        $nmipay_args['orderid'] = $order_id.'-'.time();

        $nmipay_args = $this->set_billing_and_shipping_address($nmipay_args,$order);

        $nmipay_args['email'] = $order->get_billing_email();

        $nmipay_args['invoice'] = $order->order_key;

        $AmountInput = number_format($order->order_total, 2, '.', '');

        $nmipay_args['amount'] = $AmountInput;

        if ( in_array( $order->billing_country, array( 'US','CA' ) ) ) {
          $order->billing_phone = str_replace( array( '( ', '-', ' ', ' )', '.' ), '', $order->get_billing_phone() );
          $nmipay_args['phone'] = $order->billing_phone;
        } else {
          $nmipay_args['phone'] = $order->get_billing_phone();
        }
        //var_dump($order->get_total_tax());die;
        // Tax
        $nmipay_args['tax'] = $order->get_total_tax();

        // Cart Contents
        $item_loop = 0;
        if ( sizeof( $order->get_items() ) > 0 ) {
          foreach ( $order->get_items() as $item ) {
            if ( $item->get_quantity() > 0 ) {

              $item_loop++;

              $item_name = $item->get_name();
        			$item_meta = strip_tags( wc_display_item_meta( $item, array(
        				'before'    => "",
        				'separator' => ", ",
        				'after'     => "",
        				'echo'      => false,
        				'autop'     => false,
        			) ) );
        			if ( $item_meta ) {
        				$item_name .= ' (' . $item_meta . ')';
        			}

              $nmipay_args[ 'item_description_' . $item_loop ] 	= $item_name;
              $nmipay_args[ 'item_quantity_' . $item_loop ] 	= $item->get_quantity();
              $nmipay_args[ 'item_unit_cost_' . $item_loop ] 		= $order->get_item_subtotal( $item, false );


            }
          }
        }

        // Discount
        if ( $order->get_total_discount() > 0 ){
          $nmipay_args['discount_amount'] = number_format( $order->get_total_discount(), 2, '.', '' );
        }

        // Shipping Cost item - nmipay only allows shipping per item, we want to send shipping for the order
        if ( $order->get_total_shipping() > 0 ) {
          $nmipay_args['shipping'] = number_format( $order->get_total_shipping(), 2, '.', '' );
        }


        $subscriptions = wcs_get_subscriptions_for_order( $order );

        $subscription = array_pop( $subscriptions );

        if ( !empty( $subscription ) ) {

          $order_items = $order->get_items();

                $unconverted_periods = array(
                  'billing_period' => $subscription->billing_period,
                  'trial_period'   => $subscription->trial_period,
                );

          $converted_periods = array();

          // Convert period strings into PayPay's format
          foreach ( $unconverted_periods as $key => $period ) {
            switch( strtolower( $period ) ) {
              case 'day':
                $converted_periods[$key] = 'day';
                break;
              case 'week':
                $converted_periods[$key] = 'week';
                break;
              case 'year':
                $converted_periods[$key] = 'year';
                break;
              case 'month':
              default:
                $converted_periods[$key] = 'month';
                break;
            }
          }

          $sign_up_fee            = $subscription->get_sign_up_fee();
          $price_per_period       = $subscription->get_total();
          $subscription_interval  = $subscription->billing_interval;
          $start_timestamp        = $subscription->get_time( 'start' );
          $trial_end_timestamp    = $subscription->get_time( 'trial_end' );
          $next_payment_timestamp = $subscription->get_time( 'next_payment' );

          $is_synced_subscription = WC_Subscriptions_Synchroniser::subscription_contains_synced_product( $subscription->id );

          if ( $is_synced_subscription ) {
            $length_from_timestamp = $next_payment_timestamp;
          } elseif ( $trial_end_timestamp > 0 ) {
            $length_from_timestamp = $trial_end_timestamp;
          } else {
            $length_from_timestamp = $start_timestamp;
          }

          $subscription_length = wcs_estimate_periods_between( $length_from_timestamp, $subscription->get_time( 'end' ), $subscription->billing_period );

          $subscription_installments = $subscription_length / $subscription_interval;

          $initial_payment = ( $is_payment_change ) ? 0 : $order->get_total();

          if($initial_payment == '0.00'){
            $initial_payment = '0.01';
          }

          if ( $subscription_trial_length > 0 ) {

            $trial_until = wcs_calculate_paypal_trial_periods_until( $next_payment_timestamp );

            $subscription_trial_length = $trial_until['first_trial_length'];
            $converted_periods['trial_period'] = $trial_until['first_trial_period'];

            $dateformat = "Ymd";
            $todayDate = date($dateformat);
            $startdate = date($dateformat, strtotime(date($dateformat, strtotime($todayDate)) . " +".$subscription_trial_length.' '.$converted_periods['trial_period']));

            $nmipay_args['plan_payments'] = $subscription_installments;

            $nmipay_args['amount'] = $initial_payment;

            $nmipay_args['plan_amount'] = $price_per_period;

            if($converted_periods['billing_period'] == 'day'){
              $nmipay_args['day_frequency'] = $subscription_interval;
            }else if($converted_periods['billing_period'] == 'week'){
              $nmipay_args['day_frequency'] = $subscription_interval*7;
            }else if($converted_periods['billing_period'] == 'year'){
              $nmipay_args['month_frequency'] = $subscription_interval*12;
              $timestamp = strtotime($startdate);
              $day = date('d', $timestamp);
              $nmipay_args['day_of_month'] = $day;
            }else{
              $nmipay_args['month_frequency'] = $subscription_interval;
              $timestamp = strtotime($startdate);
              $day = date('d', $timestamp);
              $nmipay_args['day_of_month'] = $day;
            }

          }else{
            $dateformat = "Ymd";
            $startdate = date($dateformat);

            $nmipay_args['plan_payments'] = $subscription_installments;

            $nmipay_args['amount'] = $initial_payment;

            $nmipay_args['plan_amount'] = $price_per_period;

            if($converted_periods['billing_period'] == 'day'){
              $nmipay_args['day_frequency'] = $subscription_interval;
              $startdate = date($dateformat, strtotime(date($dateformat, strtotime($startdate)) . ' +1 day'));
            }else if($converted_periods['billing_period'] == 'week'){
              $nmipay_args['day_frequency'] = $subscription_interval*7;
              $startdate = date($dateformat, strtotime(date($dateformat, strtotime($startdate)) . ' +1 week'));
            }else if($converted_periods['billing_period'] == 'year'){
              $nmipay_args['month_frequency'] = $subscription_interval*12;
              $startdate = date($dateformat, strtotime(date($dateformat, strtotime($startdate)) . ' +1 year'));
              $timestamp = strtotime($startdate);
              $day = date('d', $timestamp);
              $nmipay_args['day_of_month'] = $day;
            }else{
              $nmipay_args['month_frequency'] = $subscription_interval;
              $timestamp = strtotime($startdate);
              $day = date('d', $timestamp);
              $nmipay_args['day_of_month'] = $day;
              $startdate = date($dateformat, strtotime(date($dateformat, strtotime($startdate)) . ' +1 month'));
            }

          }

          $nmipay_args['start_date'] = $startdate;

          $nmipay_args['recurring'] = 'add_subscription';

          $nmipay_args['billing_method'] = 'recurring';

        }

      }else {
        $nmipay_args['type'] = $this->transtype;
        $nmipay_args['payment'] = 'creditcard';
        $nmipay_args['ipaddress'] = $_SERVER['REMOTE_ADDR'];
        $nmipay_args['username'] = $this->username;
        $nmipay_args['password'] = $this->password;
        $nmipay_args['currency'] = get_woocommerce_currency();

        $nmipay_args['orderid'] = $order_id.'-'.time();

        $nmipay_args = $this->set_billing_and_shipping_address($nmipay_args,$order);

        $nmipay_args['email'] = $order->get_billing_email();

        $nmipay_args['invoice'] = $order->order_key;

        $AmountInput = number_format($order->order_total, 2, '.', '');

        $nmipay_args['amount'] = $AmountInput;

        if ( in_array( $order->billing_country, array( 'US','CA' ) ) ) {
          $order->billing_phone = str_replace( array( '( ', '-', ' ', ' )', '.' ), '', $order->get_billing_phone() );
          $nmipay_args['phone'] = $order->billing_phone;
        } else {
          $nmipay_args['phone'] = $order->get_billing_phone();
        }
        //var_dump($order->get_total_tax());die;
        // Tax
        $nmipay_args['tax'] = $order->get_total_tax();

        // Cart Contents
        $item_loop = 0;
        if ( sizeof( $order->get_items() ) > 0 ) {
          foreach ( $order->get_items() as $item ) {
            if ( $item->get_quantity() > 0 ) {

              $item_loop++;

              $item_name = $item->get_name();
        			$item_meta = strip_tags( wc_display_item_meta( $item, array(
        				'before'    => "",
        				'separator' => ", ",
        				'after'     => "",
        				'echo'      => false,
        				'autop'     => false,
        			) ) );
        			if ( $item_meta ) {
        				$item_name .= ' (' . $item_meta . ')';
        			}

              $nmipay_args[ 'item_description_' . $item_loop ] 	= $item_name;
              $nmipay_args[ 'item_quantity_' . $item_loop ] 	= $item->get_quantity();
              $nmipay_args[ 'item_unit_cost_' . $item_loop ] 		= $order->get_item_subtotal( $item, false );


            }
          }
        }

        // Discount
        if ( $order->get_total_discount() > 0 ){
          $nmipay_args['discount_amount'] = number_format( $order->get_total_discount(), 2, '.', '' );
        }

        // Shipping Cost item - nmipay only allows shipping per item, we want to send shipping for the order
        if ( $order->get_total_shipping() > 0 ) {
          $nmipay_args['shipping'] = number_format( $order->get_total_shipping(), 2, '.', '' );
        }
      }

    // Processing standard
    } else {
      $nmipay_args['type'] = $this->transtype;
      $nmipay_args['payment'] = 'creditcard';
      $nmipay_args['ipaddress'] = $_SERVER['REMOTE_ADDR'];
      $nmipay_args['username'] = $this->username;
      $nmipay_args['password'] = $this->password;
      $nmipay_args['currency'] = get_woocommerce_currency();

      $nmipay_args['orderid'] = $order_id.'-'.time();

      $nmipay_args = $this->set_billing_and_shipping_address($nmipay_args,$order);

      $nmipay_args['email'] = $order->get_billing_email();

      $nmipay_args['invoice'] = $order->order_key;

      $AmountInput = number_format($order->order_total, 2, '.', '');

      $nmipay_args['amount'] = $AmountInput;

      if ( in_array( $order->billing_country, array( 'US','CA' ) ) ) {
        $order->billing_phone = str_replace( array( '( ', '-', ' ', ' )', '.' ), '', $order->get_billing_phone() );
        $nmipay_args['phone'] = $order->billing_phone;
      } else {
        $nmipay_args['phone'] = $order->get_billing_phone();
      }
      //var_dump($order->get_total_tax());die;
      // Tax
      $nmipay_args['tax'] = $order->get_total_tax();

      // Cart Contents
      $item_loop = 0;
      if ( sizeof( $order->get_items() ) > 0 ) {
        foreach ( $order->get_items() as $item ) {
          if ( $item->get_quantity() > 0 ) {

            $item_loop++;

            $item_name = $item->get_name();
      			$item_meta = strip_tags( wc_display_item_meta( $item, array(
      				'before'    => "",
      				'separator' => ", ",
      				'after'     => "",
      				'echo'      => false,
      				'autop'     => false,
      			) ) );
      			if ( $item_meta ) {
      				$item_name .= ' (' . $item_meta . ')';
      			}

            $nmipay_args[ 'item_description_' . $item_loop ] 	= $item_name;
            $nmipay_args[ 'item_quantity_' . $item_loop ] 	= $item->get_quantity();
            $nmipay_args[ 'item_unit_cost_' . $item_loop ] 		= $order->get_item_subtotal( $item, false );


          }
        }
      }

      // Discount
      if ( $order->get_total_discount() > 0 ){
        $nmipay_args['discount_amount'] = number_format( $order->get_total_discount(), 2, '.', '' );
      }

      // Shipping Cost item - nmipay only allows shipping per item, we want to send shipping for the order
      if ( $order->get_total_shipping() > 0 ) {
        $nmipay_args['shipping'] = number_format( $order->get_total_shipping(), 2, '.', '' );
      }

    }

    $name_value_pairs = array();
    foreach ($nmipay_args as $key => $value) {
      $name_value_pairs[] = $key . '=' . urlencode($value);
    }
    $gateway_values =	implode('&', $name_value_pairs);

    $response = wp_remote_post( $nmi_adr.$gateway_values, array( 'sslverify' => false, 'timeout' => 60 ) );

    if ( $this->debug == 'yes' ){
      $nmipay_args['ccnumber']= "XXXX-XXXX-XXXX-XXXX" ;
      $nmipay_args['cvv']="XXX";
      $this->log->add( 'nmipay', $order_id.' - NMI CC Order Request: ' . print_r( $nmipay_args, true ) );
    }

    if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
      parse_str($response['body'], $response);

      if ( $this->debug == 'yes' ){
        $this->log->add( 'nmipay', $order_id.' - NMI CC Order Response: ' . print_r( $response, true ) );
      }

      if($response['response']== '1'){
        // Payment completed
        $order->add_order_note( sprintf( __('The NMI Payment transaction is successful. The Transaction Id is %s.', 'woo-nmi-patsatech'), $response["transactionid"] ) );
        $order->payment_complete( $response["transactionid"] );

        if ( isset( $response['subscription_id'] ) ){
          update_post_meta( $order_id, 'NMI Subscriber ID', $response['subscription_id'] );
          WC_Subscriptions_Manager::activate_subscriptions_for_order( $order );
        }

        update_post_meta( $order_id, 'NMI Transaction ID', $response['transactionid'] );

        if( isset($response['customer_vault_id']) && isset($_POST['wc-nmipay-new-payment-method']) && count($tokens) == 0 ){
          // Build the token
          $token = new WC_Payment_Token_CC();
          $token->set_token( $response['customer_vault_id'] ); // Token comes from payment processor
          $token->set_gateway_id( 'nmipay' );
          $token->set_last4( $last_four_digits );
          $token->set_expiry_year( '20'.substr( $ccexp_expiry , 3, 7 ) );
          $token->set_expiry_month( $month );
          $token->set_card_type( $cardtype );
          $token->set_user_id( get_current_user_id() );
          // Save the new token to the database
          $token->save();

          update_user_meta( get_current_user_id(), 'nmi_cc_token_id', $token->get_id() );

          if ( $this->debug == 'yes' ){
            $this->log->add( 'nmipay', $order_id.' - New Token Saved : ' . $token->get_id() );
          }

        }else if( isset($response['customer_vault_id']) && isset($_POST['wc-nmipay-new-payment-method']) && count($tokens) > 0){

          $token_id = get_user_meta( get_current_user_id(), 'nmi_cc_token_id', true );
          $token = WC_Payment_Tokens::get( $token_id );
          $token->set_token( $response['customer_vault_id'] ); // Token comes from payment processor
          $token->set_gateway_id( 'nmipay' );
          $token->set_last4( $last_four_digits );
          $token->set_expiry_year( '20'.substr( $ccexp_expiry , 3, 7 ) );
          $token->set_expiry_month( $month );
          $token->set_card_type( $cardtype );
          $token->set_user_id( get_current_user_id() );
          // Save the new token to the database
          $token->save();

          update_user_meta( get_current_user_id(), 'nmi_cc_token_id', $token->get_id() );

          if ( $this->debug == 'yes' ){
            $this->log->add( 'nmipay', $order_id.' - Old Token Deleted : ' . $token_id.' - New Token Saved : ' . $token->get_id() );
          }

        }

        return array(
          'result' 	=> 'success',
          'redirect'	=>	$this->get_return_url($order)
        );
      }else{

        if( strpos($response['responsetext'], 'Invalid Customer Vault Id') !== false ){// Build the token
          $token_id = get_user_meta( get_current_user_id(), 'nmi_cc_token_id', true );
          if(!empty($token_id)){
            $token = new WC_Payment_Tokens();
            $token->delete( $token_id );
          }
          if ( $this->debug == 'yes' ){
            $this->log->add( 'nmipay', $order_id.' - Token Deleted : ' . $token_id.' - because of error : ' . $response['responsetext'] );
          }
          return array(
            'result' 	=> 'success',
            'redirect'	=>	$order->get_checkout_payment_url()
          );
        }else{
          $order->add_order_note( sprintf( __('Transaction Failed. %s-%s', 'woo-nmi-patsatech'), $response['response_code'], $response['responsetext'] ) );
          wc_add_notice( sprintf( __('Transaction Failed. %s-%s', 'woo-nmi-patsatech'), $response['response_code'], $response['responsetext'] ), $notice_type = 'error' );
          if ( $this->debug == 'yes' ){
            $this->log->add( 'nmipay', $order_id.' - ' . sprintf( __('Transaction Failed. %s-%s', 'woo-nmi-patsatech'), $response['response_code'], $response['responsetext'] ) );
          }
        }
      }
    }else{
      $order->add_order_note( sprintf( __('Gateway Error. Please Notify the Store Owner about this error. %s', 'woo-nmi-patsatech'), print_r($response,TRUE) ) );
      wc_add_notice( __('Gateway Error. Please Notify the Store Owner about this error. %s', 'woo-nmi-patsatech'), $notice_type = 'error' );
      if ( $this->debug == 'yes' ){
        $this->log->add( 'nmipay', $order_id.' - ' . sprintf( __('Gateway Error. Please Notify the Store Owner about this error. %s', 'woo-nmi-patsatech'), print_r($response,TRUE) ) );
      }
    }
  }

  /**
   * Set Billing and Shipping Address
   * @param	int $order_id
   * @param	array $nmipay_args
   * @return array $nmipay_args
   */
  function set_billing_and_shipping_address($nmipay_args, $order){

    $nmipay_args['firstname'] = $order->get_billing_first_name();
    $nmipay_args['lastname'] = $order->get_billing_last_name();
    $nmipay_args['company'] = $order->get_billing_company();
    $nmipay_args['address1'] = $order->get_billing_address_1();
    $nmipay_args['address2'] = $order->get_billing_address_2();
    $nmipay_args['city'] = $order->get_billing_city();
    $nmipay_args['state'] = $order->get_billing_state();
    $nmipay_args['zip'] = $order->get_billing_postcode();
    $nmipay_args['country'] = $order->get_billing_country();

    $ship_name = $order->get_shipping_first_name();

    if(empty($ship_name) ){
      $nmipay_args['shipping_firstname'] = $order->get_billing_first_name();
      $nmipay_args['shipping_lastname'] = $order->get_billing_last_name();
      $nmipay_args['shipping_company'] = $order->get_billing_company();
      $nmipay_args['shipping_address1'] = $order->get_billing_address_1();
      $nmipay_args['shipping_address2'] = $order->get_billing_address_2();
      $nmipay_args['shipping_city'] = $order->get_billing_city();
      $nmipay_args['shipping_state'] = $order->get_billing_state();
      $nmipay_args['shipping_zip'] = $order->get_billing_postcode();
      $nmipay_args['shipping_country'] = $order->get_billing_country();
    }else{
      $nmipay_args['shipping_firstname'] = $order->get_shipping_first_name();
      $nmipay_args['shipping_lastname'] = $order->get_shipping_last_name();
      $nmipay_args['shipping_company'] = $order->get_shipping_company();
      $nmipay_args['shipping_address1'] = $order->get_shipping_address_1();
      $nmipay_args['shipping_address2'] = $order->get_shipping_address_2();
      $nmipay_args['shipping_city'] = $order->get_shipping_city();
      $nmipay_args['shipping_state'] = $order->get_shipping_state();
      $nmipay_args['shipping_zip'] = $order->get_shipping_postcode();
      $nmipay_args['shipping_country'] = $order->get_shipping_country();
    }

    return $nmipay_args;

  }

  /**
   * Process a refund if supported
   * @param	int $order_id
   * @param	float $amount
   * @param	string $reason
   * @return	bool|wp_error True or false based on success, or a WP_Error object
   */
  public function process_refund( $order_id, $amount = null, $reason = '' ) {
    $order = wc_get_order( $order_id );

    if ( ! $order || ! $order->get_transaction_id() ) {
      return false;
    }

    $provider = $this->provider;

    $nmi_adr = $this->$provider . '?';

    if ( ! is_null( $amount ) ) {
      $nmipay_args['type'] = 'refund';
      $nmipay_args['username'] = $this->username;
      $nmipay_args['password'] = $this->password;
      $nmipay_args['transactionid'] = $order->get_transaction_id();
      $nmipay_args['amount'] = number_format( $amount, 2, '.', '' );
    }

    $name_value_pairs = array();
    foreach ($nmipay_args as $key => $value) {
      $name_value_pairs[] = $key . '=' . urlencode($value);
    }
    $gateway_values =	implode('&', $name_value_pairs);

    $response = wp_remote_post( $nmi_adr.$gateway_values, array( 'sslverify' => false, 'timeout' => 60 ) );

    if ( is_wp_error( $response ) ) {
      return $response;
    }

    if ( empty( $response['body'] ) ) {
      return new WP_Error( 'nmi-error', __( 'Empty NMI response.', 'woo-nmi-patsatech' ) );
    }

      parse_str($response['body'], $response);

    if($response['response']== '1'){
      $order->add_order_note( sprintf( __( 'Refund %s - Refund ID: %s', 'woo-nmi-patsatech' ), $response['responsetext'], $response['transactionid'] ) );
      return true;
    }else if($response['response']== '2'){
      $order->add_order_note( __( 'Transaction Declined', 'woo-nmi-patsatech' ) );
      return true;
    }else if($response['response']== '3'){
      $order->add_order_note( __( 'Error in transaction data or system error.', 'woo-nmi-patsatech' ) );
      return true;
    }

    return false;
  }

  /**
   * Add payment method via account screen.
   * We don't store the token locally, but to the NMI API.
   * @since 3.0.0
   */
  public function add_payment_method() {

    $credit_card = preg_replace( '/(?<=\d)\s+(?=\d)/', '', trim( $_POST['nmipay-card-number'] ) );
    $ccexp_expiry = str_replace(' ','',$_POST['nmipay-card-expiry']);
    $month = substr( $ccexp_expiry , 0, 2 );
    $year = substr( $ccexp_expiry , 3, 4 );
    $provider = $this->provider;

    $cardtype = $this->getCardType($credit_card);

    $nmi_adr = $this->$provider . '?';

    $tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), 'nmipay' );

    if( count($tokens) == 0 ){

      $nmipay_args['customer_vault'] = 'add_customer';
      $nmipay_args['ccnumber'] = $credit_card;
      $nmipay_args['cvv'] = $_POST["nmipay-card-cvc"];
      $nmipay_args['ccexp'] = $month.'/'.$year;

      $last_four_digits = substr($nmipay_args['ccnumber'], -4);


    }else if( count($tokens) > 0 ){

      $token = WC_Payment_Tokens::get( get_user_meta( get_current_user_id(), 'nmi_cc_token_id', true ) );

      $nmipay_args['customer_vault'] = 'update_customer';
      $nmipay_args['customer_vault_id'] = $token->get_token();
      $nmipay_args['ccnumber'] = $credit_card;
      $nmipay_args['cvv'] = $_POST["nmipay-card-cvc"];
      $nmipay_args['ccexp'] = $month.'/'.$year;

      $last_four_digits = substr($nmipay_args['ccnumber'], -4);

    }
    $nmipay_args['username'] = $this->username;
    $nmipay_args['password'] = $this->password;

    $name_value_pairs = array();
    foreach ($nmipay_args as $key => $value) {
      $name_value_pairs[] = $key . '=' . urlencode($value);
    }
    $gateway_values =	implode('&', $name_value_pairs);

    $response = wp_remote_post( $nmi_adr.$gateway_values, array( 'sslverify' => false, 'timeout' => 60 ) );

    if (!is_wp_error($response) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
      parse_str($response['body'], $response);
      if($response['response']== '1'){

        if( count($tokens) == 0 ){
          // Build the token
          $token = new WC_Payment_Token_CC();
          $token->set_token( $response['customer_vault_id'] ); // Token comes from payment processor
          $token->set_gateway_id( 'nmipay' );
          $token->set_last4( $last_four_digits );
          $token->set_expiry_year( '20'.substr( $ccexp_expiry , 3, 7 ) );
          $token->set_expiry_month( $month );
          $token->set_card_type( $cardtype );
          $token->set_user_id( get_current_user_id() );
          // Save the new token to the database
          $token->save();

          update_user_meta( get_current_user_id(), 'nmi_cc_token_id', $token->get_id() );

        }else if( count($tokens) > 0){

          $token_id = get_user_meta( get_current_user_id(), 'nmi_cc_token_id', true );
          $token = WC_Payment_Tokens::get( $token_id );
          $token->set_token( $response['customer_vault_id'] ); // Token comes from payment processor
          $token->set_gateway_id( 'nmipay' );
          $token->set_last4( $last_four_digits );
          $token->set_expiry_year( '20'.substr( $ccexp_expiry , 3, 7 ) );
          $token->set_expiry_month( $month );
          $token->set_card_type( $cardtype );
          $token->set_user_id( get_current_user_id() );
          // Save the new token to the database
          $token->save();

          update_user_meta( get_current_user_id(), 'nmi_cc_token_id', $token->get_id() );

        }

        return array(
          'result'   => 'success',
          'redirect' => wc_get_endpoint_url( 'payment-methods' ),
        );

      }else{
        wc_add_notice( sprintf( __('Transaction Failed. %s-%s', 'woo-nmi-patsatech'), $response['response_code'], $response['responsetext'] ), $notice_type = 'error' );
        return;
      }
    }else{
      wc_add_notice( __('PLease make sure you have entered the Credit Card details.'. print_r($response,TRUE), 'woo-nmi-patsatech'), $notice_type = 'error' );
      return;
    }

  }

  /*
  * Check whether the card number number is empty
  */
  private function is_empty_credit_card( $credit_card ) {

    if ( empty( $credit_card ) ){
      return false;
    }

    return true;
  }

  /*
  * Check whether the card number number is valid
  */
  private function is_valid_credit_card( $credit_card ) {

    $credit_card = preg_replace( '/(?<=\d)\s+(?=\d)/', '', trim( $credit_card ) );

    $number = preg_replace( '/[^0-9]+/', '', $credit_card );
    $strlen = strlen( $number );
    $sum    = 0;
    if ( $strlen < 13 ){
      return false;
    }
    for ( $i=0; $i < $strlen; $i++ ) {
      $digit = substr( $number, $strlen - $i - 1, 1 );

      if( $i % 2 == 1 ) {

        $sub_total = $digit * 2;

        if( $sub_total > 9 ) {
          $sub_total = 1 + ( $sub_total - 10 );
        }
      } else {
        $sub_total = $digit;
      }
      $sum += $sub_total;
    }

    if ( $sum > 0 AND $sum % 10 == 0 ){
      return true;
    }

    return false;
  }

  /*
  * Check expiry date is empty
  */
  private function is_empty_expire_date( $ccexp_expiry ) {

    $ccexp_expiry = str_replace( array(' / ','/',' '), '', $ccexp_expiry );

    if ( is_numeric( $ccexp_expiry ) && ( strlen( $ccexp_expiry ) == 4 ) ){
      return true;
    }

    return false;
  }

  /*
  * Check expiry date is valid
  */
  private function is_valid_expire_date( $ccexp_expiry ) {

    $month = $year = '';
    $month = substr( $ccexp_expiry , 0, 2 );
    $year = substr( $ccexp_expiry , 5, 7 );
    $year = '20'. $year;

    if( $month > 12 ) {
      return false;
    }

    if ( date( "Y-m-d", strtotime( $year . "-" . $month . "-01" ) ) > date( "Y-m-d" ) ) {
      return true;
    }

    return false;
  }

  /*
  * Check whether the ccv number is empty
  */
  private function is_empty_ccv_nmber( $ccv_number ) {

    $length = strlen( $ccv_number );

    return is_numeric( $ccv_number ) AND $length > 2 AND $length < 5;

  }

  private function getCardType($CCNumber){

    $creditcardTypes = array(
      array('Name'=>'AMEX','cardLength'=>array(15),'cardPrefix'=>array('34', '37')),
      array('Name'=>'Maestro','cardLength'=>array(12, 13, 14, 15, 16, 17, 18, 19),'cardPrefix'=>array('5018', '5020', '5038', '6304', '6759', '6761', '6763')),
      array('Name'=>'MasterCard','cardLength'=>array(16),'cardPrefix'=>array('51', '52', '53', '54', '55')),
      array('Name'=>'VISA','cardLength'=>array(13,16),'cardPrefix'=>array('4')),
      array('Name'=>'Discover','cardLength'=>array(13,16),'cardPrefix'=>array('6011', '65')),
      array('Name'=>'JCB','cardLength'=>array(16),'cardPrefix'=>array('3528', '3529', '353', '354', '355', '356', '357', '358')),
      array('Name'=>'Diners','cardLength'=>array(14),'cardPrefix'=>array('300', '301', '302', '303', '304', '305', '36')),
      array('Name'=>'Diners','cardLength'=>array(16),'cardPrefix'=>array('54', '55')),
      array('Name'=>'Diners','cardLength'=>array(14),'cardPrefix'=>array('300','305'))
    );

    $CCNumber= trim($CCNumber);
    $type='VISA-SSL';
    foreach ($creditcardTypes as $card){
      if (! in_array(strlen($CCNumber),$card['cardLength'])) {
        continue;
      }
      $prefixes = '/^('.implode('|',$card['cardPrefix']).')/';
      if(preg_match($prefixes,$CCNumber) == 1 ){
        $type= $card['Name'];
        break;
      }
    }
    return $type;
  }
}

?>
