<?php
/*
   Plugin Name: Authorize.net Payment Gateway For WooCommerce
   Description: Extends WooCommerce to Process Payments with Authorize.net gateway. HMAC-SHA512 Compatible version.
   Version: 5.0
   Plugin URI: http://www.indatos.com?source=woocomautho
   Author: Ishan Verma 
   Author URI: https://twitter.com/justishan
   License: Under GPL2
   WC requires at least: 3.0.0
   WC tested up to: 3.5.3
*/

add_action('plugins_loaded', 'woocommerce_tech_autho_init', 0);

function woocommerce_tech_autho_init() {

   if ( !class_exists( 'WC_Payment_Gateway' ) ) 
      return;

   /**
   * Localisation
   */
   load_plugin_textdomain('wc-tech-autho', false, dirname( plugin_basename( __FILE__ ) ) . '/languages');
   
   /**
   * Authorize.net Payment Gateway class
   */
   class WC_Tech_Autho extends WC_Payment_Gateway 
   {
      protected $msg = array();
 
      public function __construct(){

         $this->id               = 'authorize';
         $this->method_title     = __('Authorize.net', 'tech');
         $this->icon             = WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/images/logo.gif';
         $this->has_fields       = false;
         $this->init_form_fields();
         $this->init_settings();
         $this->title            = $this->settings['title'];
         $this->description      = $this->settings['description'];
         $this->login            = $this->settings['login_id'];
         $this->mode             = $this->settings['working_mode'];
         $this->transaction_mode = $this->settings['transaction_mode'];
         $this->transaction_key  = $this->settings['transaction_key'];
         $this->signature_key    = $this->settings['signature_key'];
         $this->success_message  = $this->settings['success_message'];
         $this->failed_message   = $this->settings['failed_message'];
         $this->liveurl          = 'https://secure2.authorize.net/gateway/transact.dll';
         $this->testurl          = 'https://test.authorize.net/gateway/transact.dll';
         $this->powerpay         = 'https://verifi.powerpay.biz/cart/ausi.php';
         $this->msg['message']   = "";
         $this->msg['class']     = "";
        

         add_action('init', array(&$this, 'check_authorize_response'));
         //update for woocommerce >2.0
         add_action( 'woocommerce_api_wc_tech_autho' , array( $this, 'check_authorize_response' ) );
         add_action('valid-authorize-request', array(&$this, 'successful_request'));
         
         
         if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
             add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
          } else {
             add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
         }

         add_action('woocommerce_receipt_authorize', array(&$this, 'receipt_page'));
         add_action('woocommerce_thankyou_authorize',array(&$this, 'thankyou_page'));
         
         if( function_exists('indatos_woo_auth_process_refund') ){
            $this->supports = array(
              'products',
              'refunds'
            );
         }else{
            
         }
      }
      
      function process_refund($order_id, $amount = null, $reason = '')
      {
         return indatos_woo_auth_process_refund($order_id, $amount = null, $reason = '');
      }
          
      function init_form_fields()
      {

         $this->form_fields = array(
            'enabled'      => array(
                  'title'        => __('Enable/Disable', 'tech'),
                  'type'         => 'checkbox',
                  'label'        => __('Enable Authorize.net Payment Module.', 'tech'),
                  'default'      => 'no'),
            'title'        => array(
                  'title'        => __('Title:', 'tech'),
                  'type'         => 'text',
                  'description'  => __('This controls the title which the user sees during checkout.', 'tech'),
                  'default'      => __('Authorize.net', 'tech')),
            'description'  => array(
                  'title'        => __('Description:', 'tech'),
                  'type'         => 'textarea',
                  'description'  => __('This controls the description which the user sees during checkout.', 'tech'),
                  'default'      => __('Pay securely by Credit or Debit Card through Authorize.net Secure Servers.', 'tech')),
            'login_id'     => array(
                  'title'        => __('Login ID', 'tech'),
                  'type'         => 'password',
                  'description'  => __('This is API Login ID')),
            'transaction_key' => array(
                  'title'        => __('Transaction Key', 'tech'),
                  'type'         => 'password',
                  'description'  =>  __('API Transaction Key', 'tech')),
            'signature_key' => array(
                  'title'        => __('Signature Key', 'tech'),
                  'type'         => 'password',
                  'description'  =>  __('Signature Key is required to validate the response from Authorize.net. 
                  Paste 128 character key as it is. Refer to generate signature key<a href="https://support.authorize.net/s/article/What-is-a-Signature-Key">https://support.authorize.net/s/article/What-is-a-Signature-Key</a>. NOTE: make sure when you copy and paste signature there is no whitespace before after.', 'tech')),
            'success_message' => array(
                  'title'        => __('Transaction Success Message', 'tech'),
                  'type'         => 'textarea',
                  'description'=>  __('Message to be displayed on successful transaction.', 'tech'),
                  'default'      => __('Your payment has been procssed successfully.', 'tech')),
            'failed_message'  => array(
                  'title'        => __('Transaction Failed Message', 'tech'),
                  'type'         => 'textarea',
                  'description'  =>  __('Message to be displayed on failed transaction.', 'tech'),
                  'default'      => __('Your transaction has been declined.', 'tech')),
            'working_mode'    => array(
                  'title'        => __('API Mode'),
                  'type'         => 'select',
                  'options'      => array('false'=>'Live/Production Mode', 'false_test' => 'Live/Production API in Test Mode', 'true'=>'Sandbox/Developer API Mode'),
                  'description'  => "Live or Production / Sandbox Mode" ),
            'transaction_mode'    => array(
                  'title'        => __('Transaction Mode'),
                  'type'         => 'select',
                  'options'      => array( 'auth_capture'=>'Authorize and Capture', 'authorize'=>'Authorize Only'),
                  'description'  => "Transaction Mode. If you are not sure what to use set to Authorize and Capture" )
         );
      }
      
     
      /**
       * Admin Panel Options
       * - Options for bits like 'title' and availability on a country-by-country basis
      **/
      public function admin_options()
      {
         echo '<h3>'.__('Authorize.net Payment Gateway (Basic)', 'tech').'</h3>';
         echo '<p>'.__('Authorize.net is most popular payment gateway for online payment processing. For any support connect with Tech Support team on <a href="http://www.indatos.com/?ref=plugin-sim">Our Site</a> For GDPR details, contact support.').'</p><p style="background:#6f14f1; color:#fff; padding:20px; font-size:14px;">Limited Period Discount. <a href="https://www.indatos.com/products/authorize-net-woocommerce-plugin-certified-solution/?utm=sim" style="text-decoration:none; color:#fff; font-weight:500; border:1px solid #fff; padding: 5px;"> Download Now! Authorize.net Certified Version <strike> $49.00 </strike> $19.60 </a></p>
         <p><a href="https://www.indatos.com/wordpress-support/woocommerce-authorize-net-notification-form/">Fill in this to receive priority notification on updates for this plugin(optional).</a></p>
         <p>Thanks for your patience and support, HMAC-SHA512 update was hell of an update :) <a href="https://www.paypal.me/indatos">You can buy me a beer! Cheers!</a> </p>
         ';
         echo '<table class="form-table">';
         $this->generate_settings_html();
         echo '</table>';

      }
      
      /**
      *  There are no payment fields for Authorize.net, but want to show the description if set.
      **/
      function payment_fields()
      {
         if ( $this->description ) 
            echo wpautop(wptexturize($this->description));
      }
      
      public function thankyou_page($order_id) 
      {
       
      }
      /**
      * Receipt Page
      **/
      function receipt_page($order)
      {
         echo '<p>'.__('Thank you for your order, please click the button below to pay with Authorize.net.', 'tech').'</p>';
         echo $this->generate_authorize_form($order);
      }
      
      /**
       * Process the payment and return the result
      **/
      function process_payment($order_id)
      {
         $order = new WC_Order($order_id);
         return array(
         				'result' 	=> 'success',
         				'redirect'	=> $order->get_checkout_payment_url( true )
         			);
      }
      
      /**
       * Check for valid Authorize.net server callback to validate the transaction response.
      **/
      function check_authorize_response()
      {
        
         global $woocommerce;
         $temp_order            = new WC_Order();  
        
         if ( count($_POST) ){
         
            $redirect_url = '';
            $this->msg['class']     = 'error';
            $this->msg['message']   = $this->failed_message;
            $order                  = new WC_Order($_POST['x_invoice_num']);
            $signatureKey           = ($this->signature_key != '') ? $this->signature_key : '';
			 
			 
			
		
			
            $hashData = implode('^', [
                         $_POST['x_trans_id'],
                         $_POST['x_test_request'],
                         $_POST['x_response_code'],
                         $_POST['x_auth_code'],
                         $_POST['x_cvv2_resp_code'],
                         $_POST['x_cavv_response'],
                         $_POST['x_avs_code'],
                         $_POST['x_method'],
                         $_POST['x_account_number'],
                         $_POST['x_amount'],
                         $_POST['x_company'],
                         $_POST['x_first_name'],
                         $_POST['x_last_name'],
                         $_POST['x_address'],
                         $_POST['x_city'],
                         $_POST['x_state'],
                         $_POST['x_zip'],
                         $_POST['x_country'],
                         $_POST['x_phone'],
                         $_POST['x_fax'],
                         $_POST['x_email'],
                         $_POST['x_ship_to_company'],
                         $_POST['x_ship_to_first_name'],
                         $_POST['x_ship_to_last_name'],
                         $_POST['x_ship_to_address'],
                         $_POST['x_ship_to_city'],
                         $_POST['x_ship_to_state'],
                         $_POST['x_ship_to_zip'],
                         $_POST['x_ship_to_country'],
                         $_POST['x_invoice_num'],
                     ]);
			 
            $digest = strtoupper(HASH_HMAC('sha512',"^".$hashData."^",hex2bin($signatureKey)));
            
			 
            if ( $_POST['x_response_code'] != '' &&  ( strtoupper($_POST['x_SHA2_Hash']) ==  $digest ) ){
               try{                  
                  $amount           = $_POST['x_amount'];
                  $hash             = $_POST['x_SHA2_Hash'];
                  $transauthorised  = false;
                     
                  if ( $order->get_status() != 'completed'){
                     
                     if ( $_POST['x_response_code'] == 1 ){
                        $transauthorised        = true;
                        $this->msg['message']   = $this->success_message;
                        $this->msg['class']     = 'success';
                        
                        if ( $order->get_status() == 'processing' ){
                           
                        }
                        else{
                            $order->payment_complete($_REQUEST['x_trans_id']);
                            $order->add_order_note('Autorize.net payment successful<br/>Ref Number/Transaction ID: '.$_REQUEST['x_trans_id']);
                            $order->add_order_note($this->msg['message']);
                            $woocommerce->cart->empty_cart();

                        }
                     }
                     else{
                        $this->msg['class'] = 'error';
                        $this->msg['message'] = $this->failed_message;
                        $order->add_order_note($this->msg['message']);
                        $order->update_status('failed');
                        //extra code can be added here such as sending an email to customer on transaction fail
                     }
                  }
                  if ( $transauthorised==false ){
                    $order->update_status('failed');
                    $order->add_order_note($this->msg['message']);
                  }

               }
               catch(Exception $e){
                         // $errorOccurred = true;
                         $msg = "Error";

               }

            }else{
               $order->add_order_note('SHA hash did not matched for this transaction. Please contact support <a href="http://www.indatos.com/wordpress-support/">contact plugin support</a> for help.');
            }
            $redirect_url = $order->get_checkout_order_received_url();
            $this->web_redirect( $redirect_url); exit;
         }
         else{
            
            $redirect_url = $temp_order->get_checkout_order_received_url();
            $this->web_redirect($redirect_url.'?msg=Unknown_error_occured');
            exit;
         }
      }
      
      
      public function web_redirect($url){
      
         echo "<html><head><script language=\"javascript\">
                <!--
                window.location=\"{$url}\";
                //-->
                </script>
                </head><body><noscript><meta http-equiv=\"refresh\" content=\"0;url={$url}\"></noscript></body></html>";
      
      }
      /**
      * Generate authorize.net button link
      **/
      public function generate_authorize_form($order_id)
      {
         global $woocommerce;
         
         $order         = new WC_Order($order_id);
         $timeStamp     = time();
         $order_total   = $order->get_total();
         $signatureKey  = ($this->signature_key != '') ? $this->signature_key : '';
         
      
         $hash_d        = hash_hmac('sha512', sprintf('%s^%s^%s^%s^',
                           $this->login,      
                           $order_id,  
                           $timeStamp, 
                           $order_total      
                           ), hex2bin($signatureKey));
         
         
         $relay_url = get_site_url().'/wc-api/'.get_class( $this );
         $authorize_args = array(
            
            'x_login'                  => $this->login,
            'x_amount'                 => $order_total,
            'x_invoice_num'            => $order_id,
            'x_relay_response'         => "TRUE",
            'x_relay_url'              => $relay_url,
            'x_fp_sequence'            => $order_id,
            'x_fp_hash'                => $hash_d,
            'x_show_form'              => 'PAYMENT_FORM',
            'x_version'                => '3.1',
            'x_fp_timestamp'           => $timeStamp,
            'x_first_name'             => $order->get_billing_first_name() ,
            'x_last_name'              => $order->get_billing_last_name() ,
            'x_company'                => $order->get_billing_company() ,
            'x_address'                => $order->get_billing_address_1() .' '. $order->get_billing_address_2(),
            'x_country'                => $order->get_billing_country(),
            'x_state'                  => $order->get_billing_state(),
            'x_city'                   => $order->get_billing_city(),
            'x_zip'                    => $order->get_billing_postcode(),
            'x_phone'                  => $order->get_billing_phone(),
            'x_email'                  => $order->get_billing_email(),
            'x_ship_to_first_name'     => $order->get_shipping_first_name() ,
            'x_ship_to_last_name'      => $order->get_shipping_last_name() ,
            'x_ship_to_company'        => $order->get_shipping_company() ,
            'x_ship_to_address'        => $order->get_shipping_address_1() .' '. $order->get_shipping_address_2(),
            'x_ship_to_country'        => $order->get_shipping_country(),
            'x_ship_to_state'          => $order->get_shipping_state(),
            'x_ship_to_city'           => $order->get_shipping_city(),
            'x_ship_to_zip'            => $order->get_shipping_postcode(),
            'x_cancel_url'             => wc_get_checkout_url(),
            'x_freight'                => $order->get_total_shipping(),
            'x_cancel_url_text'        => 'Cancel Payment'
            );
            
         if( $this->transaction_mode == 'authorize' ){
            $authorize_args['x_type'] = 'AUTH_ONLY';
         }else{
            $authorize_args['x_type'] = 'AUTH_CAPTURE';
         
         }
         
         if( $this->mode == 'false_test' ){
            $authorize_args['x_test_request'] = 'TRUE';
         }else{
            $authorize_args['x_test_request'] = 'FALSE';
         
         }
         
         $authorize_args_array = array();
         
         foreach($authorize_args as $key => $value){
           $authorize_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
         }
         
        if($this->mode == 'true'){
           $processURI = $this->testurl;
         }
         else{
            $processURI = $this->liveurl;
         }
         
         $html_form    = '<form action="'.$processURI.'" method="post" id="authorize_payment_form">' 
               . implode('', $authorize_args_array) 
               . '<input type="submit" class="button" id="submit_authorize_payment_form" value="'.__('Pay via Authorize.net', 'tech').'" /> <a class="button cancel" href="'.$order->get_cancel_order_url().'">'.__('Cancel order &amp; restore cart', 'tech').'</a>'
               . '<script type="text/javascript">
                  jQuery(function(){
                     jQuery("body").block({
                           message: "<img src=\"'.$woocommerce->plugin_url().'/assets/images/wpspin-2x.gif\" alt=\"Redirecting…\" style=\"float:left; margin-right: 10px;\" />'.__('Thank you for your order. We are now redirecting you to Authorize.net to make payment.', 'tech').'",
                           overlayCSS:
                        {
                           background:       "#ccc",
                           opacity:          0.6,
                           "z-index": "99999999999999999999999999999999"
                        },
                     css: {
                           padding:          20,
                           textAlign:        "center",
                           color:            "#555",
                           border:           "3px solid #aaa",
                           backgroundColor:  "#fff",
                           cursor:           "wait",
                           lineHeight:       "32px",
                           "z-index": "999999999999999999999999999999999"
                     }
                     });
                  jQuery("#submit_authorize_payment_form").click();
               });
               </script>
               </form>';

         return $html_form;
      }

      
   }

   /**
    * Add this Gateway to WooCommerce
   **/
   function woocommerce_add_tech_autho_gateway($methods) 
   {
      $methods[] = 'WC_Tech_Autho';
      return $methods;
   }

   add_filter('woocommerce_payment_gateways', 'woocommerce_add_tech_autho_gateway' );
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'authsim_action_links' );
function authsim_action_links ( $links ) {
   $authsim_links = array(
      '<a href="admin.php?page=wc-settings&tab=checkout&section=authorize" >Settings</a>',
      '<a href="http://www.indatos.com/wordpress-support/?ref=plugin" target="_blank">Support</a>',
      '<a href="https://www.indatos.com/products/authorize-net-woocommerce-plugin-certified-solution/?utm=sim" >Buy Authorize.net Certified</a>'

   );
   return array_merge( $links, $authsim_links );
}


add_action( 'upgrader_process_complete', 'woosim_plugin_update',10, 2);

add_action('admin_init', 'woosim__plugin_redirect');

function woosim_plugin_update() {
 add_option('woosim__plugin_do_update_redirect', true);
}

function woosim__plugin_redirect() {
 if (get_option('woosim__plugin_do_update_redirect', false)) {
     delete_option('woosim__plugin_do_update_redirect');
     wp_redirect('admin.php?page=wc-settings&tab=checkout&section=authorize');
 }
}