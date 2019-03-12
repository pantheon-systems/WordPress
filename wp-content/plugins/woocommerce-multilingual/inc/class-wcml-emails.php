<?php
class WCML_Emails{

    private $order_id = false;
    private $locale = false;
    /** @var woocommerce_wpml */
    private $woocommerce_wpml;
    /** @var Sitepress */
    private $sitepress;
    /** @var WooCommerce */
    private $woocommerce;
	/** @var wpdb */
	private $wpdb;

	/**
	 * WCML_Emails constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 * @param WooCommerce $woocommerce
	 * @param wpdb $wpdb
	 */
    function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, WooCommerce $woocommerce, wpdb $wpdb ) {
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress        = $sitepress;
        $this->woocommerce      = $woocommerce;
	    $this->wpdb             = $wpdb;
    }

    function add_hooks(){
        global $pagenow;
        //wrappers for email's header
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action( 'woocommerce_order_status_completed_notification', array(
                $this,
                'email_heading_completed'
            ), 9 );
            add_action( 'woocommerce_order_status_changed', array( $this, 'comments_language' ), 10 );
        }

        add_action( 'woocommerce_new_customer_note_notification', array( $this, 'email_heading_note' ), 9 );

        add_action( 'wp_ajax_woocommerce_mark_order_status', array( $this, 'email_refresh_in_ajax' ), 9 );

        add_action( 'woocommerce_order_status_pending_to_processing_notification', array(
            $this,
            'email_heading_processing'
        ), 9 );
        add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array(
            $this,
            'email_heading_processing'
        ), 9 );

        //wrappers for email's body
        add_action( 'woocommerce_before_resend_order_emails', array( $this, 'email_header' ) );
        add_action( 'woocommerce_after_resend_order_email', array( $this, 'email_footer' ) );

        //filter string language before for emails
        add_filter( 'icl_current_string_language', array( $this, 'icl_current_string_language' ), 10, 2 );

        //change order status
        add_action( 'woocommerce_order_status_completed', array( $this, 'refresh_email_lang_complete' ), 9 );
        add_action( 'woocommerce_order_status_pending_to_processing_notification', array(
            $this,
            'refresh_email_lang'
        ), 9 );
        add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array(
            $this,
            'refresh_email_lang'
        ), 9 );
        add_action( 'woocommerce_new_customer_note', array( $this, 'refresh_email_lang' ), 9 );

        //new order admins email
        add_action( 'woocommerce_order_status_pending_to_processing_notification', array(
            $this,
            'new_order_admin_email'
        ), 9 );
        add_action( 'woocommerce_order_status_pending_to_completed_notification', array(
            $this,
            'new_order_admin_email'
        ), 9 );
        add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array(
            $this,
            'new_order_admin_email'
        ), 9 );
        add_action( 'woocommerce_order_status_failed_to_processing_notification', array(
            $this,
            'new_order_admin_email'
        ), 9 );
        add_action( 'woocommerce_order_status_failed_to_completed_notification', array(
            $this,
            'new_order_admin_email'
        ), 9 );
        add_action( 'woocommerce_order_status_failed_to_on-hold_notification', array(
            $this,
            'new_order_admin_email'
        ), 9 );
        add_action( 'woocommerce_before_resend_order_emails', array( $this, 'backend_new_order_admin_email' ), 9 );

        add_filter( 'icl_st_admin_string_return_cached', array( $this, 'admin_string_return_cached' ), 10, 2 );

        add_filter( 'plugin_locale', array( $this, 'set_locale_for_emails' ), 10, 2 );
        add_filter( 'woocommerce_countries', array( $this, 'translate_woocommerce_countries' ) );

        if ( is_admin() && $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings' && isset( $_GET['tab'] ) && $_GET['tab'] == 'email' ) {
            add_action( 'admin_footer', array( $this, 'show_language_links_for_wc_emails' ) );
            $this->set_emails_string_language();
        }

        if (
            (
                ! isset( $_GET['post_type'] ) ||
                $_GET['post_type'] != 'shop_order'
            ) &&
            (
                ! isset( $_GET['action'] ) ||
                ! in_array( $_GET['action'], array(
                    'woocommerce_mark_order_status'
                ) )
            )
        ) {
            add_filter( 'woocommerce_order_items_meta_get_formatted', array( $this, 'filter_formatted_items' ), 10, 2 );
        }

        add_filter( 'woocommerce_allow_send_queued_transactional_email', array(
            $this,
            'send_queued_transactional_email'
        ), 10, 3 );

	    add_action( 'woocommerce_order_partially_refunded_notification', array( $this, 'refresh_email_lang' ), 9 );
	    add_action( 'woocommerce_order_fully_refunded_notification', array( $this, 'refresh_email_lang' ), 9 );
	    add_filter( 'woocommerce_email_get_option', array( $this, 'filter_refund_emails_strings' ), 10, 4 );

	    add_filter( 'woocommerce_email_setup_locale', '__return_false' );
	    add_filter( 'woocommerce_email_restore_locale', '__return_false' );


	    add_filter( 'woocommerce_email_heading_new_order',  array( $this, 'new_order_email_heading' ) );
	    add_filter( 'woocommerce_email_subject_new_order',  array( $this, 'new_order_email_subject' ) );
    }

    function email_refresh_in_ajax() {
        if ( isset( $_GET['order_id'] ) ) {
            $this->refresh_email_lang( $_GET['order_id'] );

            if ( isset( $_GET['status'] ) && 'completed' === $_GET['status'] ) {
                $this->email_heading_completed( $_GET['order_id'], true );
            }

            return true;
        }
    }

    function refresh_email_lang_complete( $order_id ){

        $this->order_id = $order_id;
        $this->refresh_email_lang( $order_id );
        $this->email_heading_completed( $order_id, true );
    }

    /**
     * Translate WooCommerce emails.
     *
     * @global type $sitepress
     * @global type $order_id
     * @return type
     */
    function email_header( $order ) {

        if( is_array( $order ) ) {
            $order = $order[ 'order_id' ];
        } elseif( is_object( $order ) ) {
	        $order = method_exists( 'WC_Order', 'get_id' ) ? $order->get_id() : $order->id;
        }

        $this->refresh_email_lang( $order );
    }


    function refresh_email_lang( $order_id ){

        if ( is_array( $order_id ) ) {
            if ( isset( $order_id[ 'order_id' ] ) ) {
                $order_id = $order_id[ 'order_id' ];
            } else {
                return;
            }
        }

        $lang = get_post_meta( $order_id, 'wpml_language', true );
        if( !empty( $lang ) ){
            $this->change_email_language( $lang );
        }
    }

    /**
     * After email translation switch language to default.
     */
    function email_footer() {
        $this->sitepress->switch_lang( $this->sitepress->get_default_language() );
    }

    function comments_language(){
        $this->change_email_language( $this->woocommerce_wpml->strings->get_domain_language( 'woocommerce' ) );

    }

    function email_heading_completed( $order_id, $no_checking = false ){

        if( ( class_exists( 'WC_Email_Customer_Completed_Order' ) || $no_checking ) && isset( $this->woocommerce->mailer()->emails[ 'WC_Email_Customer_Completed_Order' ] ) ){

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->heading = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_completed_order_settings', '[woocommerce_customer_completed_order_settings]heading' );

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->subject = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_completed_order_settings', '[woocommerce_customer_completed_order_settings]subject' );

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->heading_downloadable = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_completed_order_settings', '[woocommerce_customer_completed_order_settings]heading_downloadable' );

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->subject_downloadable = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_completed_order_settings', '[woocommerce_customer_completed_order_settings]subject_downloadable' );

            $enabled = $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->enabled;
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->enabled = false;
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->trigger($order_id);
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Completed_Order']->enabled = $enabled;
        }
    }

    function email_heading_processing($order_id){

        if( class_exists( 'WC_Email_Customer_Processing_Order' ) && isset( $this->woocommerce->mailer()->emails[ 'WC_Email_Customer_Processing_Order' ] ) ){

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->heading = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_processing_order_settings', '[woocommerce_customer_processing_order_settings]heading', $order_id );

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->subject = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_processing_order_settings', '[woocommerce_customer_processing_order_settings]subject', $order_id );
            $enabled = $this->woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->enabled;
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->enabled = false;
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->trigger($order_id);
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Processing_Order']->enabled = $enabled;

        }
    }

    function email_heading_note($args){

        if( class_exists( 'WC_Email_Customer_Note' ) && isset( $this->woocommerce->mailer()->emails[ 'WC_Email_Customer_Note' ] ) ){

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Note']->heading = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_note_settings', '[woocommerce_customer_note_settings]heading' );

            $this->woocommerce->mailer()->emails['WC_Email_Customer_Note']->subject = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_note_settings', '[woocommerce_customer_note_settings]subject' );

            $enabled = $this->woocommerce->mailer()->emails['WC_Email_Customer_Note']->enabled;
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Note']->enabled = false;
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Note']->trigger($args);
            $this->woocommerce->mailer()->emails['WC_Email_Customer_Note']->enabled = $enabled;
        }
    }

    public function filter_refund_emails_strings( $value, $object , $old_value, $key ){

        if( in_array( $key, array( 'subject_partial', 'subject_full', 'heading_partial', 'heading_full' ) ) && $object->object ){
	        $translated_value = $this->get_refund_email_translated_string( $key, $object );
        }

        return !empty( $translated_value ) ? $translated_value : $value;
    }

	public function get_refund_email_translated_string( $key, $object){

		return	$this->wcml_get_translated_email_string( 'admin_texts_woocommerce_customer_refunded_order_settings',
					'[woocommerce_customer_refunded_order_settings]'.$key, $object->object->get_id() );

	}

    function new_order_admin_email($order_id){

        if( isset( $this->woocommerce->mailer()->emails[ 'WC_Email_New_Order' ] ) ){
            $recipients = explode(',',$this->woocommerce->mailer()->emails['WC_Email_New_Order']->get_recipient());
            foreach($recipients as $recipient){
                $user = get_user_by('email',$recipient);
                if($user){
                    $user_lang = $this->sitepress->get_user_admin_language($user->ID, true );
                }else{
                    $user_lang = get_post_meta($order_id, 'wpml_language', true );
                }

                $this->change_email_language( $user_lang );

                $this->woocommerce->mailer()->emails['WC_Email_New_Order']->heading = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_new_order_settings', '[woocommerce_new_order_settings]heading', $order_id, $user_lang );

                $this->woocommerce->mailer()->emails['WC_Email_New_Order']->subject = $this->wcml_get_translated_email_string( 'admin_texts_woocommerce_new_order_settings', '[woocommerce_new_order_settings]subject', $order_id, $user_lang );

                $this->woocommerce->mailer()->emails['WC_Email_New_Order']->recipient = $recipient;

                $this->woocommerce->mailer()->emails['WC_Email_New_Order']->trigger($order_id);
            }
            $this->woocommerce->mailer()->emails['WC_Email_New_Order']->enabled = false;
            $this->refresh_email_lang($order_id);
        }
    }

    public function new_order_email_heading( $heading ){
	    $translated_heading = $this->woocommerce->mailer()->emails['WC_Email_New_Order']->heading;
	    if( $translated_heading ){
		    $heading = $this->woocommerce->mailer()->emails['WC_Email_New_Order']->format_string( $translated_heading );
	    }

        return $heading;
    }

    public function new_order_email_subject( $subject ){
	    $translated_subject = $this->woocommerce->mailer()->emails['WC_Email_New_Order']->subject;
	    if( $translated_subject ){
		    $subject = $this->woocommerce->mailer()->emails['WC_Email_New_Order']->format_string( $translated_subject );
	    }

	    return $subject;
    }

    public function backend_new_order_admin_email( $order_id ){
        if( isset( $_POST[ 'wc_order_action' ] ) && in_array( $_POST[ 'wc_order_action' ], array( 'send_email_new_order', 'send_order_details_admin' ) ) ){
            $this->new_order_admin_email( $order_id );
        }
    }

    function filter_formatted_items( $formatted_meta, $object ){

        if( isset( $object->product->variation_id ) ){

            $current_prod_variation_id = apply_filters( 'translate_object_id', $object->product->variation_id, 'product_variation', false );

            if( !is_null( $current_prod_variation_id ) ) {

                foreach( $formatted_meta as $key => $formatted_var ){

                    if( substr( $formatted_var[ 'key' ], 0, 3 ) ){

                        $attribute = wc_sanitize_taxonomy_name( $formatted_var[ 'key' ] );

                        if( taxonomy_exists( $attribute ) ){
                            $attr_term = get_term_by( 'name', $formatted_meta[ $key ][ 'value' ], $attribute );
                            $tr_id = apply_filters( 'translate_object_id', $attr_term->term_id, $attribute, false, $this->sitepress->get_current_language() );

                            if( $tr_id ){
                                $translated_term = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $tr_id, $attribute );
                                $formatted_meta[ $key ][ 'value' ] = $translated_term->name;
                            }

                        }else{

                            $custom_attr_trnsl = $this->woocommerce_wpml->attributes->get_custom_attribute_translation( $object->product->id, $formatted_var[ 'key' ], array('is_taxonomy' => false), $this->sitepress->get_current_language() );

                            if ( false !== $custom_attr_trnsl ) {
                                $formatted_meta[ $key ][ 'label' ] = $custom_attr_trnsl['name'];
                            }
                        }
                    }
                }
            }
        }

        return $formatted_meta;

    }

    function change_email_language( $lang ) {

        if ( isset( $_POST['post_type'] ) && 'shop_order' === $_POST['post_type'] ) {
            return;
        }

        $this->sitepress->switch_lang( $lang, true );
        $this->locale = $this->sitepress->get_locale( $lang );
        unload_textdomain( 'woocommerce' );
        unload_textdomain( 'default' );
        global $wp_locale;
        $wp_locale = new WP_Locale();

        $this->woocommerce->load_plugin_textdomain();
        load_default_textdomain( $this->locale );
    }

    function admin_string_return_cached( $value, $option ){
        if( in_array( $option, array ( 'woocommerce_email_from_address', 'woocommerce_email_from_name' ) ) )
            return false;

        return $value;
    }

    function wcml_get_translated_email_string( $context, $name, $order_id = false, $language_code = null ){

        if( $order_id && !$language_code ){
            $order_language = get_post_meta( $order_id, 'wpml_language', true );
            if( $order_language ){
                $language_code = $order_language;
            }
        }
        $result = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT value FROM {$this->wpdb->prefix}icl_strings WHERE context = %s AND name = %s ", $context, $name ) );

        return apply_filters( 'wpml_translate_single_string', $result, $context, $name, $language_code );
    }

    function icl_current_string_language(  $current_language, $name ){
        $order_id = false;

        if( isset($_POST['action']) && $_POST['action'] == 'editpost' && isset($_POST['post_type']) && $_POST['post_type'] == 'shop_order' && isset( $_POST[ 'wc_order_action' ] ) && $_POST[ 'wc_order_action' ] != 'send_email_new_order' ){
            $order_id = filter_input( INPUT_POST, 'post_ID', FILTER_SANITIZE_NUMBER_INT );
        }elseif( isset($_POST['action']) && $_POST['action'] == 'woocommerce_add_order_note' && isset($_POST['note_type']) && $_POST['note_type'] == 'customer' ) {
            $order_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );
        }elseif( isset($_GET['action']) && isset($_GET['order_id']) && ( $_GET['action'] == 'woocommerce_mark_order_complete' || $_GET['action'] == 'woocommerce_mark_order_status') ){
            $order_id = filter_input( INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT );
        }elseif(isset($_GET['action']) && $_GET['action'] == 'mark_completed' && $this->order_id){
            $order_id = $this->order_id;
        }elseif(isset($_POST['action']) && $_POST['action'] == 'woocommerce_refund_line_items'){
            $order_id = filter_input( INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT );
        }elseif( empty( $_POST ) && isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'wc-settings' && isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'email' && substr( $name, 0, 12 ) == '[woocommerce' ){
            $email_string = explode( ']', str_replace( '[', '', $name ) );
            $email_option = get_option( $email_string[ 0 ], true );
            $context = 'admin_texts_'.$email_string[ 0 ];

            $current_language = $this->woocommerce_wpml->strings->get_string_language( $email_option[ $email_string[ 1 ] ], $context, $name );
        }elseif( $this->order_id ){
            $order_id = $this->order_id;
        }

        $order_id = apply_filters( 'wcml_send_email_order_id', $order_id );

        if( $order_id ){
            $order_language = get_post_meta( $order_id, 'wpml_language', true );
            if( $order_language ){
                $current_language = $order_language;
            }else{
                $current_language = $this->sitepress->get_current_language();
            }
        }

        return apply_filters( 'wcml_email_language', $current_language, $order_id );
    }

    // set correct locale code for emails
    function set_locale_for_emails(  $locale, $domain ){

        if( $domain == 'woocommerce' && $this->locale ){
            $locale = $this->locale;
        }

        return $locale;
    }

    function show_language_links_for_wc_emails(){

        $emails_options = array(
            'woocommerce_new_order_settings',
            'woocommerce_cancelled_order_settings',
            'woocommerce_failed_order_settings',
            'woocommerce_customer_on_hold_order_settings',
            'woocommerce_customer_processing_order_settings',
            'woocommerce_customer_completed_order_settings',
            'woocommerce_customer_refunded_order_settings',
            'woocommerce_customer_invoice_settings',
            'woocommerce_customer_note_settings',
            'woocommerce_customer_reset_password_settings',
            'woocommerce_customer_new_account_settings'
        );

	    $emails_options = apply_filters( 'wcml_emails_options_to_translate', $emails_options );

	    $text_keys = array(
		    'subject',
		    'heading',
		    'subject_downloadable',
		    'heading_downloadable',
		    'subject_full',
		    'subject_partial',
		    'heading_full',
		    'heading_partial',
		    'subject_paid',
		    'heading_paid'
	    );

	    $text_keys = apply_filters( 'wcml_emails_text_keys_to_translate', $text_keys );


        foreach( $emails_options as $emails_option ) {

            $section_name = str_replace( 'woocommerce_', 'wc_email_', $emails_option );
            $section_name = str_replace( '_settings', '', $section_name );
            if( isset( $_GET['section'] ) && $_GET['section'] == $section_name ){

                $option_settings = get_option( $emails_option );
                if( $option_settings ){
	                foreach ($option_settings as $setting_key => $setting_value) {
		                if ( in_array( $setting_key, $text_keys ) ) {
			                $input_name = str_replace( '_settings', '', $emails_option ).'_'.$setting_key;

			                $lang_selector = new WPML_Simple_Language_Selector($this->sitepress);
			                $language = $this->woocommerce_wpml->strings->get_string_language( $setting_value, 'admin_texts_'.$emails_option, '['.$emails_option.']'.$setting_key );
			                if( is_null( $language ) ) {
				                $language = $this->sitepress->get_default_language();
			                }

			                $lang_selector->render( array(
					                'id' => $emails_option.'_'.$setting_key.'_language_selector',
					                'name' => 'wcml_lang-'.$emails_option.'-'.$setting_key,
					                'selected' => $language,
					                'show_please_select' => false,
					                'echo' => true,
					                'style' => 'width: 18%;float: left'
				                )
			                );

			                $st_page = admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php&context=admin_texts_'.$emails_option.'&search='.$setting_value );
			                ?>
                            <script>
                                var input = jQuery('input[name="<?php echo $input_name  ?>"]');
                                if (input.length) {
                                    input.parent().append('<div class="translation_controls"></div>');
                                    input.parent().find('.translation_controls').append('<a href="<?php echo $st_page ?>" style="margin-left: 10px"><?php _e('translations', 'woocommerce-multilingual') ?></a>');
                                    jQuery('#<?php echo $emails_option.'_'.$setting_key.'_language_selector' ?>').prependTo(input.parent().find('.translation_controls'));
                                }
                            </script>
		                <?php }
	                }
                }
            }
        }
    }

    function set_emails_string_language(){

        foreach( $_POST as $key => $language ){

            if( substr( $key, 0, 9 ) == 'wcml_lang' ){

                $email_string = explode( '-', $key );

                if( isset( $email_string[2] ) ){

                    $email_key = str_replace( '_settings', '',  $email_string[1] );
                    $email_key .= '_'.$email_string[2];

                    $email_settings = get_option( $email_string[1], true );
                    $opt_string_value =  $email_settings[ $email_string[2] ];

                    $string_value = isset( $_POST[ $email_key ] ) ? $_POST[ $email_key ] : $opt_string_value;

                    $context = 'admin_texts_'.$email_string[1];
                    $name =  '['.$email_string[1].']'.$email_string[2];

                    do_action('wpml_register_single_string', $context, $name, $string_value, false, $this->woocommerce_wpml->strings->get_string_language( $opt_string_value, $context ) );

                    $this->woocommerce_wpml->strings->set_string_language( $string_value, $context, $name, $language );
                }
            }
        }
    }

    function translate_woocommerce_countries( $countries ){

        if( isset( $_POST[ 'wc_order_action' ] ) && $_POST[ 'wc_order_action' ] !== 'send_email_new_order' && isset( $_POST[ 'post_ID' ] ) ){
            $this->refresh_email_lang( $_POST[ 'post_ID' ] );
            $countries = include( WC()->plugin_path() . '/i18n/countries.php' );
        }

        return $countries;
    }


    function send_queued_transactional_email( $allow, $filter, $args ){
        $this->order_id = $args[0];
        return $allow;
    }

}