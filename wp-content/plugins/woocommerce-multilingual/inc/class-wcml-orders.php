<?php

class WCML_Orders {

    private $woocommerce_wpml;
    private $sitepress;

    private $standard_order_notes = array(
            'Order status changed from %s to %s.',
            'Order item stock reduced successfully.',
            'Item #%s stock reduced from %s to %s.',
            'Item #%s stock increased from %s to %s.',
            'Awaiting BACS payment','Awaiting cheque payment',
            'Payment to be made upon delivery.',
            'Validation error: PayPal amounts do not match (gross %s).',
            'Validation error: PayPal IPN response from a different email address (%s).',
            'Payment pending: %s',
            'Payment %s via IPN.',
            'Validation error: PayPal amounts do not match (amt %s).',
            'IPN payment completed','PDT payment completed'
    );

    public function __construct( &$woocommerce_wpml, &$sitepress ){
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;

        add_action('init', array($this, 'init'));

        //checkout page
        add_action( 'wp_ajax_woocommerce_checkout',array($this,'switch_to_current'),9);
        add_action( 'wp_ajax_nopriv_woocommerce_checkout',array($this,'switch_to_current'),9);

        add_action( 'wp_ajax_wcml_order_delete_items', array( $this, 'order_delete_items' ) );
    }

    function init(){

        add_action('woocommerce_shipping_update_ajax', array($this, 'fix_shipping_update'));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'set_order_language'));

        add_filter('icl_lang_sel_copy_parameters', array($this, 'append_query_parameters'));

        add_filter('the_comments', array($this, 'get_filtered_comments'));

	    if ( $this->should_attach_new_order_note_data_filter() ) {
		    add_filter( 'gettext', array( $this, 'filtered_woocommerce_new_order_note_data' ), 10, 3 );
	    }

        add_filter( 'woocommerce_order_get_items', array( $this, 'woocommerce_order_get_items' ), 10, 2 );

        add_action( 'woocommerce_process_shop_order_meta', array( $this, 'set_order_language_backend'), 10, 2 );
        add_action( 'woocommerce_order_actions_start', array( $this, 'order_language_dropdown' ), 11 ); //after order currency drop-down

        //special case for wcml-741
        add_action('updated_post_meta', array($this,'update_order_currency'), 100,4);

        add_action( 'woocommerce_before_order_itemmeta', array( $this, 'backend_before_order_itemmeta' ), 100, 3 );
        add_action( 'woocommerce_after_order_itemmeta', array( $this, 'backend_after_order_itemmeta' ), 100, 3 );

        add_filter( 'woocommerce_get_item_downloads', array( $this, 'filter_downloadable_product_items' ), 10, 3 );
        add_filter( 'woocommerce_customer_get_downloadable_products', array( $this, 'filter_customer_get_downloadable_products' ), 10, 3 );
    }

    public function should_attach_new_order_note_data_filter() {
	    $admin_language = $this->sitepress->get_user_admin_language( get_current_user_id(), true );
	    $all_strings_in_english = get_option( 'wpml-st-all-strings-are-in-english' );

	    return 'en' !== $admin_language || ! $all_strings_in_english;
    }

    function filtered_woocommerce_new_order_note_data($translations, $text, $domain ){
        if(in_array($text,$this->standard_order_notes)){

            $language = $this->woocommerce_wpml->strings->get_string_language( $text, 'woocommerce' );

            if( $this->sitepress->get_user_admin_language( get_current_user_id(), true ) != $language ){

                $string_id = icl_get_string_id( $text, 'woocommerce');
                $strings = icl_get_string_translations_by_id( $string_id );
                if($strings){
                    $translations = $strings[ $this->sitepress->get_user_admin_language( get_current_user_id(), true ) ]['value'];
                }

            }else{
                return $text;
            }
        }

        return $translations;
    }

    function get_filtered_comments( $comments ){

        $user_id = get_current_user_id();

	    if ( $user_id ) {
		    $user_language = get_user_meta( $user_id, 'icl_admin_language', true );

		    foreach ( $comments as $key => $comment ) {
			    $comment_string_id = icl_get_string_id( $comment->comment_content, 'woocommerce' );

			    if ( $comment_string_id ) {
				    $comment_strings = icl_get_string_translations_by_id( $comment_string_id );

				    if ( $comment_strings && isset( $comment_strings[ $user_language ] ) ) {
					    $comments[ $key ]->comment_content = $comment_strings[ $user_language ][ 'value' ];
				    }
			    }
		    }

	    }

        return $comments;
    }

    function woocommerce_order_get_items( $items, $order ){

        if( isset( $_GET[ 'post' ] ) && get_post_type( $_GET[ 'post' ] ) == 'shop_order' ) {
            // on order edit page use admin default language
            $language_to_filter = $this->sitepress->get_user_admin_language( get_current_user_id(), true );
        }elseif( isset( $_GET[ 'action' ] ) && ( $_GET['action'] == 'woocommerce_mark_order_complete' || $_GET['action'] == 'woocommerce_mark_order_status' || $_GET['action'] == 'mark_processing') ){
            //backward compatibility for WC < 2.7
            $order_id = method_exists( 'WC_Order', 'get_id' ) ? $order->get_id() : $order->id;
            $order_language = get_post_meta( $order_id, 'wpml_language', true );
            $language_to_filter = $order_language ? $order_language : $this->sitepress->get_default_language();
        }else{
            $language_to_filter = $this->sitepress->get_current_language();
        }

        foreach( $items as $index => $item ){
            if( is_array( $item ) ){
                // WC < 2.7
                foreach( $item as $key => $item_data ){
                    if( $key == 'product_id' ){
                        $tr_product_id = apply_filters( 'translate_object_id', $item_data, 'product', false, $language_to_filter );
                        if( !is_null( $tr_product_id ) ){
                            $items[ $index ][ $key ] = $tr_product_id;
                            $items[ $index ][ 'name'] = get_post( $tr_product_id )->post_title;
                        }
                    }
                    if( $key == 'variation_id' ){
                        $tr_variation_id = apply_filters( 'translate_object_id', $item_data, 'product_variation', false, $language_to_filter );
                        if( !is_null($tr_variation_id)){
                            $items[$index][$key] = $tr_variation_id;
                        }
                    }

                    if (substr($key, 0, 3) == 'pa_') {
                        //attr is taxonomy

                        $term_id = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $key, $item_data );
                        $tr_id = apply_filters( 'translate_object_id', $term_id, $key, false, $language_to_filter );

                        if(!is_null($tr_id)){
                            $translated_term = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $tr_id, $key);
                            $items[$index][$key] = $translated_term->slug;
                        }
                    }

                    if( $key == 'type' && $item_data == 'shipping' && isset( $item[ 'method_id' ] ) ){

                        $items[ $index ][ 'name' ] = $this->woocommerce_wpml->shipping->translate_shipping_method_title( $item[ 'name' ], $item[ 'method_id' ], $language_to_filter );

                    }
                }
            }else{
                // WC >= 2.7
                if( $item instanceof WC_Order_Item_Product ){
                    if( $item->get_type() == 'line_item' ){
                        $item_product_id = $item->get_product_id();
                        if( get_post_type( $item_product_id ) == 'product_variation' ){
                            $item_product_id = wp_get_post_parent_id( $item_product_id );
                        }

                        $tr_product_id = apply_filters( 'translate_object_id', $item_product_id, 'product', false, $language_to_filter );

                        if( !is_null( $tr_product_id ) ){
                            $item->set_product_id( $tr_product_id );
                            $item->set_name( get_post( $tr_product_id )->post_title );
                        }

                        $tr_variation_id = apply_filters( 'translate_object_id', $item->get_variation_id(), 'product_variation', false, $language_to_filter );
	                    if ( ! is_null( $tr_variation_id ) ) {
		                    $item->set_variation_id( $tr_variation_id );
		                    $item->set_name( wc_get_product( $tr_variation_id )->get_name() );
	                    }
                    }
                }elseif( $item instanceof WC_Order_Item_Shipping ){
                    if( $item->get_method_id() ){
	                    $shipping_id = $item->get_method_id() . $item->get_instance_id();
                        $item->set_method_title(
                                $this->woocommerce_wpml->shipping->translate_shipping_method_title(
                                    $item->get_method_title(),
                                    $shipping_id,
                                    $language_to_filter
                                )
                        );
                    }
                }
            }
        }

        return $items;

    }

    public function backend_before_order_itemmeta( $item_id, $item, $product ){
        global $sitepress;

        if( $this->get_order_language_by_item_id( $item_id ) != $sitepress->get_user_admin_language( get_current_user_id(), true ) ){
            foreach( $item[ 'item_meta' ] as $key => $item_meta ){
                if ( taxonomy_exists( wc_attribute_taxonomy_name( $key ) ) || substr( $key, 0, 3 ) == 'pa_' ) {
                    //backward compatibility for WC < 2.7 - in WC 2.7 $item_meta is a single attribute value not an array
                    if( !is_array( $item_meta ) ){
                        $item_meta = array( $item_meta );
                    }

                    foreach( $item_meta as $value ){
                        $this->force_update_itemmeta( $item_id, $key, $value, $sitepress->get_user_admin_language( get_current_user_id(), true ) );
                    }
                }
            }
        }
    }

    public function backend_after_order_itemmeta( $item_id, $item, $product ){
        global $sitepress;

        $order_languge = $this->get_order_language_by_item_id( $item_id );
        if( $order_languge != $sitepress->get_user_admin_language( get_current_user_id(), true ) ){
            foreach( $item[ 'item_meta' ] as $key => $item_meta ){
                if ( taxonomy_exists( wc_attribute_taxonomy_name( $key ) ) || substr( $key, 0, 3 ) == 'pa_' ) {
                    //backward compatibility for WC < 2.7 - in WC 2.7 $item_meta is a single attribute value not an array
                    if( !is_array( $item_meta ) ){
                        $item_meta = array( $item_meta );
                    }

                    foreach( $item_meta as $value ){
                        $this->force_update_itemmeta( $item_id, $key, $value, $order_languge );
                    }
                }
            }
        }
    }

    public function get_order_language_by_item_id( $item_id ){
        global $wpdb;

        $order_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d", $item_id ) );

        return get_post_meta( $order_id, 'wpml_language', true );
    }

    //force update to display attribute in correct language on edit order page
    public function force_update_itemmeta( $item_id, $key, $value, $languge ){
        global $wpdb, $woocommerce_wpml;

        $taxonomy = substr( $key, 0, 3 ) != 'pa_' ? wc_attribute_taxonomy_name( $key ) : $key;
        $term_id = $woocommerce_wpml->terms->wcml_get_term_id_by_slug( $taxonomy, $value );
        $translated_term = $woocommerce_wpml->terms->wcml_get_translated_term( $term_id, $taxonomy, $languge );

        if( $translated_term ){
            $value = $translated_term->slug;
            $wpdb->update( $wpdb->prefix.'woocommerce_order_itemmeta', array( 'meta_value' => $value ), array( 'order_item_id' => $item_id, 'meta_key' => $key ) );
        }
    }

    // Fix for shipping update on the checkout page.
    function fix_shipping_update($amount){
        global $sitepress, $post;

        if($sitepress->get_current_language() !== $sitepress->get_default_language() && $post->ID == $this->checkout_page_id()){

            $_SESSION['icl_checkout_shipping_amount'] = $amount;

            $amount = $_SESSION['icl_checkout_shipping_amount'];

        }

        return $amount;
    }


    /**
     * Adds language to order post type.
     *
     * Language was stored in the session created on checkout page.
     * See params().
     *
     * @param type $order_id
     */
    function set_order_language($order_id) {
        if(!get_post_meta($order_id, 'wpml_language')){
            $language = isset($_SESSION['wpml_globalcart_language']) ? $_SESSION['wpml_globalcart_language'] : ICL_LANGUAGE_CODE;
            update_post_meta($order_id, 'wpml_language', $language);
        }
    }

    function append_query_parameters($parameters){

        if(is_order_received_page() || is_checkout()){
            if(!in_array('order', $parameters)) $parameters[] = 'order';
            if(!in_array('key', $parameters)) $parameters[] = 'key';
        }

        return $parameters;
    }

    function switch_to_current(){
        $this->woocommerce_wpml->emails->change_email_language($this->sitepress->get_current_language());
    }

    function order_language_dropdown( $order_id ){
        if( !get_post_meta( $order_id, '_order_currency') ) {
            $languages = apply_filters( 'wpml_active_languages', array(), array( 'skip_missing' => 0, 'orderby' => 'code' ) );
            $selected_lang =  isset( $_COOKIE [ '_wcml_dashboard_order_language' ] ) ?  $_COOKIE [ '_wcml_dashboard_order_language' ] : $this->sitepress->get_default_language();
            ?>
            <li class="wide">
                <label><?php _e('Order language:'); ?></label>
                <select id="dropdown_shop_order_language" name="wcml_shop_order_language">
                    <?php if (!empty($languages)): ?>

                        <?php foreach ($languages as $l): ?>

                            <option
                                value="<?php echo $l['language_code'] ?>" <?php echo $selected_lang == $l['language_code'] ? 'selected="selected"' : ''; ?>><?php echo $l['translated_name']; ?></option>

                        <?php endforeach; ?>

                    <?php endif; ?>
                </select>
            </li>
            <?php
            $wcml_set_dashboard_order_language_nonce = wp_create_nonce( 'set_dashboard_order_language' );
            wc_enqueue_js( "
                 var order_lang_current_value = jQuery('#dropdown_shop_order_language option:selected').val();

                 jQuery('#dropdown_shop_order_language').on('change', function(){
                    if(confirm('" . esc_js(__("All the products will be removed from the current order in order to change the language", 'woocommerce-multilingual')). "')){
                        var lang = jQuery(this).val();

                        jQuery.ajax({
                            url: ajaxurl,
                            type: 'post',
                            dataType: 'json',
                            data: {action: 'wcml_order_delete_items', order_id: woocommerce_admin_meta_boxes.post_id, lang: lang , wcml_nonce: '".$wcml_set_dashboard_order_language_nonce."' },
                            success: function( response ){
                                if(typeof response.error !== 'undefined'){
                                    alert(response.error);
                                }else{
                                    window.location = window.location.href;
                                }
                            }
                        });
                    }else{
                        jQuery(this).val( order_lang_current_value );
                        return false;
                    }
                });

            ");
        }
    }

    function order_delete_items(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'set_dashboard_order_language')){
            echo json_encode(array('error' => __('Invalid nonce', 'woocommerce-multilingual')));
            die();
        }

		$cookie_name = '_wcml_dashboard_order_language';
		// @todo uncomment or delete when #wpmlcore-5796 is resolved
		//do_action( 'wpsc_add_cookie', $cookie_name );
		setcookie( $cookie_name, filter_input( INPUT_POST, 'lang', FILTER_SANITIZE_FULL_SPECIAL_CHARS ), time() + 86400, COOKIEPATH, COOKIE_DOMAIN );

    }

    function set_order_language_backend( $post_id, $post ){

        if( isset( $_POST['wcml_shop_order_language'] ) ){
            update_post_meta( $post_id, 'wpml_language', filter_input( INPUT_POST, 'wcml_shop_order_language', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) );
        }

    }

    function update_order_currency( $meta_id, $object_id, $meta_key, $meta_value ){

        if( $this->woocommerce_wpml->settings['enable_multi_currency'] == WCML_MULTI_CURRENCIES_INDEPENDENT && get_post_type($object_id) == 'shop_order' && isset( $_GET['wc-ajax'] ) && $_GET['wc-ajax'] == 'checkout' ){
            update_post_meta( $object_id, '_order_currency', get_woocommerce_currency() );
        }

    }


    public function filter_downloadable_product_items( $files, $item, $object  ){

        $order_language = get_post_meta( WooCommerce_Functions_Wrapper::get_order_id( $object ), 'wpml_language', true );

        if( $item['variation_id'] > 0 ){
            $item['variation_id'] =  apply_filters( 'translate_object_id', $item['variation_id'], 'product_variation', false, $order_language );
        }else{
            $item['product_id'] = apply_filters( 'translate_object_id',  $item['product_id'], 'product', false, $order_language );
        }

        remove_filter( 'woocommerce_get_item_downloads', array( $this, 'filter_downloadable_product_items' ), 10, 3 );

        $files = WooCommerce_Functions_Wrapper::get_item_downloads( $object, $item );

        add_filter( 'woocommerce_get_item_downloads', array( $this, 'filter_downloadable_product_items' ), 10, 3 );

        return $files;
    }

    public function filter_customer_get_downloadable_products( $downloads ){

        foreach( $downloads as $key => $download ){

            $translated_id =  apply_filters( 'translate_object_id',  $download['product_id'], get_post_type( $download['product_id'] ), false, $this->sitepress->get_current_language() );

            if( $translated_id ){
                $downloads[ $key ][ 'product_name' ] = get_the_title( $translated_id );
            }

        }

        return $downloads;
    }


}
