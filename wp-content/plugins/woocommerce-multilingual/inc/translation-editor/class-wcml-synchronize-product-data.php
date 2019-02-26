<?php

class WCML_Synchronize_Product_Data{

    const CUSTOM_FIELD_KEY_SEPARATOR = ':::';

    /** @var woocommerce_wpml */
    private $woocommerce_wpml;
    /**
     * @var SitePress
     */
    private $sitepress;
    /** @var wpdb */
    private $wpdb;


    /**
     * WCML_Synchronize_Product_Data constructor.
     *
     * @param woocommerce_wpml $woocommerce_wpml
     * @param SitePress $sitepress
     * @param wpdb $wpdb
     */
    public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, wpdb $wpdb ) {
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress        = $sitepress;
        $this->wpdb             = $wpdb;
    }

    public function add_hooks(){
        if( is_admin() ){
            // filters to sync variable products
            add_action( 'save_post', array( $this, 'synchronize_products' ), PHP_INT_MAX, 2 ); // After WPML

            add_action( 'icl_pro_translation_completed', array( $this, 'icl_pro_translation_completed' ) );

            add_filter( 'icl_make_duplicate', array( $this, 'icl_make_duplicate'), 110, 4 );

            //quick & bulk edit
            add_action( 'woocommerce_product_quick_edit_save', array( $this, 'woocommerce_product_quick_edit_save' ) );
            add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'woocommerce_product_quick_edit_save' ) );

            add_action( 'wpml_translation_update', array( $this, 'icl_connect_translations_action' ) );

            add_action( 'deleted_term_relationships', array( $this, 'delete_term_relationships_update_term_count' ), 10, 2 );

            add_action( 'woocommerce_product_set_visibility', array( $this, 'sync_product_translations_visibility' ) );
        }

	    add_action( 'woocommerce_reduce_order_stock', array( $this, 'sync_product_stocks_reduce' ) );
	    add_action( 'woocommerce_restore_order_stock', array( $this, 'sync_product_stocks_restore' ) );

        add_action( 'woocommerce_product_set_stock_status', array($this, 'sync_stock_status_for_translations' ), 100, 2);
        add_action( 'woocommerce_variation_set_stock_status', array($this, 'sync_stock_status_for_translations' ), 10, 2);

        add_filter( 'future_product', array( $this, 'set_schedule_for_translations'), 10, 2 );
    }

    /**
     * This function takes care of synchronizing products
     */
    public function synchronize_products( $post_id, $post ){
        global $pagenow, $wp;

        $original_language  = $this->woocommerce_wpml->products->get_original_product_language( $post_id );
        $current_language   = $this->sitepress->get_current_language();
        $original_product_id = $this->woocommerce_wpml->products->get_original_product_id( $post_id );

        // check its a product
        $post_type = get_post_type( $post_id );
        //set trid for variations
        if ( $post_type == 'product_variation' ) {
            $var_lang = $this->sitepress->get_language_for_element( wp_get_post_parent_id( $post_id ), 'post_product' );
            $is_parent_original = $this->woocommerce_wpml->products->is_original_product( wp_get_post_parent_id( $post_id ) );
            $variation_language_details = $this->sitepress->get_element_language_details( $post_id, 'post_product_variation' );
            if( $is_parent_original && !$variation_language_details ){
                $this->sitepress->set_element_language_details( $post_id, 'post_product_variation', false, $var_lang );
            }
        }

        // exceptions
	    $ajax_call  = ( ! empty( $_POST['icl_ajx_action'] ) && 'make_duplicates' === $_POST['icl_ajx_action'] );
	    $api_call   = ! empty( $wp->query_vars['wc-api-version'] );
	    $auto_draft = 'auto-draft' === $post->post_status;
	    $trashing = isset( $_GET['action'] ) && 'trash' === $_GET['action'];
	    if (
		    $post_type !== 'product' ||
		    empty( $original_product_id ) ||
		    isset( $_POST['autosave'] )  ||
		    ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' && $pagenow != 'admin.php' && ! $ajax_call && ! $api_call ) ||
		    $trashing ||
		    $auto_draft
	    ) {
		    return;
	    }
        // Remove filter to avoid double sync
        remove_action( 'save_post', array( $this, 'synchronize_products' ), PHP_INT_MAX, 2 );

        do_action( 'wcml_before_sync_product', $original_product_id, $post_id );

        //trnsl_interface option
        if ( !$this->woocommerce_wpml->settings['trnsl_interface'] && $original_language != $current_language ) {
            if( !isset( $_POST[ 'wp-preview' ] ) || empty( $_POST[ 'wp-preview' ] ) ){
                //make sure we sync post in current language
                $post_id = apply_filters( 'translate_object_id', $post_id, 'product', false, $current_language );
                $this->sync_product_data( $original_product_id, $post_id, $current_language );
            }
            return;
        }

        //update products order
        $this->woocommerce_wpml->products->update_order_for_product_translations( $original_product_id );

        // pick posts to sync
        $trid = $this->sitepress->get_element_trid( $original_product_id, 'post_product' );
        $translations = $this->sitepress->get_element_translations( $trid, 'post_product', false, true );

        foreach( $translations as $translation ) {
            if ( !$translation->original ) {
                $this->sync_product_data( $original_product_id, $translation->element_id, $translation->language_code );
            }
        }

        //save custom options for variations
        $this->woocommerce_wpml->sync_variations_data->sync_product_variations_custom_data( $original_product_id );

        if( $this->woocommerce_wpml->settings[ 'enable_multi_currency' ] == WCML_MULTI_CURRENCIES_INDEPENDENT ) {
            //save custom prices
            $this->woocommerce_wpml->multi_currency->custom_prices->save_custom_prices( $original_product_id );
        }

        //save files option
        $this->woocommerce_wpml->downloadable->save_files_option( $original_product_id );

    }

    public function sync_product_data( $original_product_id, $tr_product_id, $lang ){

        do_action( 'wcml_before_sync_product_data', $original_product_id, $tr_product_id, $lang );

        $this->duplicate_product_post_meta( $original_product_id, $tr_product_id );

        $this->sync_date_and_parent( $original_product_id, $tr_product_id, $lang );

        $this->woocommerce_wpml->attributes->sync_product_attr( $original_product_id, $tr_product_id );

        $this->woocommerce_wpml->media->sync_thumbnail_id( $original_product_id, $tr_product_id, $lang );

        $this->woocommerce_wpml->media->sync_product_gallery( $original_product_id );

        $this->woocommerce_wpml->attributes->sync_default_product_attr( $original_product_id, $tr_product_id, $lang );

        //sync taxonomies
        $this->sync_product_taxonomies( $original_product_id, $tr_product_id, $lang );

        //duplicate variations
        $this->woocommerce_wpml->sync_variations_data->sync_product_variations( $original_product_id, $tr_product_id, $lang );

        $this->sync_linked_products( $original_product_id, $tr_product_id, $lang );

        // Clear any unwanted data
        wc_delete_product_transients( $tr_product_id );
    }

    public function sync_product_taxonomies( $original_product_id, $tr_product_id, $lang ){
        $taxonomies = get_object_taxonomies( 'product' );
        remove_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10, 4 );
        foreach( $taxonomies as $taxonomy ) {

            $terms = wp_get_object_terms( $original_product_id, $taxonomy );
            $tt_ids = array();
            $tt_names = array();
            if ($terms) {
                foreach ($terms as $term) {
	                if ( in_array( $term->taxonomy, array( 'product_type', 'product_visibility' ) ) ) {
                        $tt_names[] = $term->name;
                        continue;
                    }
                    $tt_ids[] = $term->term_id;
                }

	            if ( ! $this->woocommerce_wpml->terms->is_translatable_wc_taxonomy( $taxonomy ) ) {
                    wp_set_post_terms( $tr_product_id, $tt_names, $taxonomy );
                }else{
                    $this->wcml_update_term_count_by_ids( $tt_ids, $lang, $taxonomy, $tr_product_id );
                }
            }
        }
        add_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10, 4 );
    }

    public function delete_term_relationships_update_term_count( $object_id, $tt_ids ){

        if( get_post_type( $object_id ) == 'product' ){

            $language_details = $this->sitepress->get_element_language_details( $object_id, 'post_product' );
            $translations = $this->sitepress->get_element_translations( $language_details->trid, 'post_product', false, true );

            foreach( $translations as $translation ) {
                if ( !$translation->original ) {
                    $this->wcml_update_term_count_by_ids( $tt_ids, $translation->language_code );
                }
            }
        }

    }
    public function wcml_update_term_count_by_ids( $tt_ids, $language, $taxonomy = '', $tr_product_id = false ){
        $terms_array = array();
        $terms_to_insert = array();

        foreach( $tt_ids as $tt_id ){

            $tr_id = apply_filters( 'translate_object_id', $tt_id, $taxonomy, false, $language );

            if( !is_null( $tr_id ) ){
                // not using get_term - unfiltered get_term
                $translated_term = $this->wpdb->get_row( $this->wpdb->prepare( "
                            SELECT * FROM {$this->wpdb->terms} t JOIN {$this->wpdb->term_taxonomy} x ON x.term_id = t.term_id WHERE t.term_id = %d", $tr_id ) );
                if( is_taxonomy_hierarchical( $taxonomy ) ){
                    $terms_to_insert[] = (int)$translated_term->term_id;
                }else{
                    $terms_to_insert[] = $translated_term->slug;
                }

                $terms_array[] = $translated_term->term_taxonomy_id;
            }

        }

        if( $tr_product_id ){
            wp_set_post_terms( $tr_product_id, $terms_to_insert, $taxonomy );
        }

        if( in_array( $taxonomy, array( 'product_cat', 'product_tag' ) ) ) {
            $this->sitepress->switch_lang( $language );
            wp_update_term_count( $terms_array, $taxonomy );
            $this->sitepress->switch_lang( );
        }
    }

    public function sync_linked_products( $product_id, $translated_product_id, $lang ){

        $this->sync_up_sells_products( $product_id, $translated_product_id, $lang );
        $this->sync_cross_sells_products( $product_id, $translated_product_id, $lang );
        $this->sync_grouped_products( $product_id, $translated_product_id, $lang );

        // refresh parent-children transients (e.g. this child goes to private or draft)
        $translated_product_parent_id = wp_get_post_parent_id( $translated_product_id );
        if ( $translated_product_parent_id ) {
            delete_transient( 'wc_product_children_' . $translated_product_parent_id );
            delete_transient( '_transient_wc_product_children_ids_' . $translated_product_parent_id );
        }

    }

    public function sync_up_sells_products( $product_id, $translated_product_id, $lang ){

        $original_up_sells = maybe_unserialize( get_post_meta( $product_id, '_upsell_ids', true ) );
        $trnsl_up_sells = array();
        if( $original_up_sells ){
            foreach( $original_up_sells as $original_up_sell_product ) {
                $trnsl_up_sells[] = apply_filters( 'translate_object_id', $original_up_sell_product, get_post_type( $original_up_sell_product ), false, $lang );
            }
        }
        update_post_meta( $translated_product_id, '_upsell_ids', $trnsl_up_sells );

    }

    public function sync_cross_sells_products( $product_id, $translated_product_id, $lang ){

        $original_cross_sells = maybe_unserialize( get_post_meta( $product_id, '_crosssell_ids', true ) );
        $trnsl_cross_sells = array();
        if( $original_cross_sells )
            foreach( $original_cross_sells as $original_cross_sell_product ){
                $trnsl_cross_sells[] = apply_filters( 'translate_object_id', $original_cross_sell_product, get_post_type( $original_cross_sell_product ), false, $lang );
            }
        update_post_meta( $translated_product_id, '_crosssell_ids', $trnsl_cross_sells );

    }

    public function sync_grouped_products( $product_id, $translated_product_id, $lang ){

        $original_children = maybe_unserialize( get_post_meta( $product_id, '_children', true ) );
        $translated_children = array();
        if( $original_children )
            foreach( $original_children as $original_children_product ){
                $translated_children[] = apply_filters( 'translate_object_id', $original_children_product, get_post_type( $original_children_product ), false, $lang );
            }
        update_post_meta( $translated_product_id, '_children', $translated_children );

    }

    public function sync_product_stocks_reduce( $order ){
        return $this->sync_product_stocks( $order, 'reduce' );
    }

    public function sync_product_stocks_restore( $order ){
        return $this->sync_product_stocks( $order, 'restore' );
    }

    /**
     * @param $order WC_Order
     * @param $action
     */
    public function sync_product_stocks( $order, $action ){

        foreach( $order->get_items() as $item ) {

            if( $item instanceof WC_Order_Item_Product ){

                $variation_id = $item->get_variation_id();
                $product_id = $item->get_product_id();
                $qty = $item->get_quantity();
            }else{
                $variation_id = isset( $item[ 'variation_id' ] ) ? $item[ 'variation_id' ] : 0;
                $product_id = $item[ 'product_id' ];
                $qty = $item[ 'qty' ];
            }

	        $is_original = $this->woocommerce_wpml->products->is_original_product( $product_id );

	        if ( $is_original && $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'WC_VERSION' ), '3.0.0', '>=' ) ) {
		        return;
	        }

	        $qty = apply_filters( 'wcml_order_item_quantity', $qty, $order, $item );

            if( $variation_id > 0 ){
                $trid = $this->sitepress->get_element_trid( $variation_id, 'post_product_variation' );
                $translations = $this->sitepress->get_element_translations( $trid, 'post_product_variation' );
                $ld = $this->sitepress->get_element_language_details( $variation_id, 'post_product_variation' );
            } else {
                $trid = $this->sitepress->get_element_trid( $product_id, 'post_product' );
                $translations = $this->sitepress->get_element_translations( $trid, 'post_product' );
                $ld = $this->sitepress->get_element_language_details( $product_id, 'post_product' );
            }

            // Process for non-current languages
            foreach( $translations as $translation ){
                if ( !$is_original || $ld->language_code != $translation->language_code ) {

	                $translation_product_id = $translation->element_id;
	                //check if product exist
	                if ( get_post_type( $translation->element_id ) === 'product_variation' ) {
		                if ( ! get_post( wp_get_post_parent_id( $translation->element_id ) ) ) {
			                continue;
		                }
		                $translation_product_id = wp_get_post_parent_id( $translation->element_id );
	                }

                    $_product = wc_get_product( $translation->element_id );

                    $managing_stock = $_product && $_product->exists() && $_product->managing_stock();

	                $total_sales = get_post_meta( $translation_product_id, 'total_sales', true );

	                if ( $action === 'reduce' ) {
		                $total_sales += $qty;

	                	if( $managing_stock ){
			                WooCommerce_Functions_Wrapper::reduce_stock( $translation->element_id, $qty );
		                }
	                } else {
		                $total_sales -= $qty;

		                if( $managing_stock ){
		                    WooCommerce_Functions_Wrapper::increase_stock( $translation->element_id, $qty );
		                }
	                }

	                update_post_meta( $translation_product_id, 'total_sales', $total_sales );

	                $this->wc_taxonomies_recount_after_stock_change( (int)$translation_product_id );
                }
            }
        }
    }

    public function sync_stock_status_for_translations( $id, $status ) {

	    $type         = get_post_type( $id );
	    $trid         = $this->sitepress->get_element_trid( $id, 'post_' . $type );
	    $translations = $this->sitepress->get_element_translations( $trid, 'post_' . $type, true );

	    foreach ( $translations as $translation ) {
		    if ( $id !== (int) $translation->element_id ) {
			    update_post_meta( $translation->element_id, '_stock_status', $status );
			    $this->wc_taxonomies_recount_after_stock_change( (int) $translation->element_id );
		    }
	    }
    }

	/**
	 * @param int $product_id
	 */
    private function wc_taxonomies_recount_after_stock_change( $product_id ){

	    remove_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1, 1 );

	    wp_cache_delete( $product_id, 'product_cat_relationships' );
	    wp_cache_delete( $product_id, 'product_tag_relationships' );

	    wc_recount_after_stock_change( $product_id );

	    add_filter( 'get_term', array( $this->sitepress, 'get_term_adjust_id' ), 1, 1 );

    }

    //sync product parent & post_status
    public function sync_date_and_parent( $duplicated_post_id, $post_id, $lang ){
        $tr_parent_id = apply_filters( 'translate_object_id', wp_get_post_parent_id( $duplicated_post_id ), 'product', false, $lang );
        $orig_product = get_post( $duplicated_post_id );
        $args = array();
        $args[ 'post_parent' ] = is_null( $tr_parent_id )? 0 : $tr_parent_id;
        //sync product date

        if( !empty( $this->woocommerce_wpml->settings[ 'products_sync_date' ] ) ){
            $args[ 'post_date' ] = $orig_product->post_date;
        }
        $this->wpdb->update(
            $this->wpdb->posts,
            $args,
            array( 'id' => $post_id )
        );
    }

    public function set_schedule_for_translations( $deprecated, $post ){

        if( $this->woocommerce_wpml->products->is_original_product( $post->ID ) ) {
            $trid = $this->sitepress->get_element_trid( $post->ID, 'post_product');
            $translations = $this->sitepress->get_element_translations( $trid, 'post_product', true);
            foreach( $translations as $translation ) {
                if( !$translation->original ){
                    wp_clear_scheduled_hook( 'publish_future_post', array( $translation->element_id ) );
                    wp_schedule_single_event( strtotime( get_gmt_from_date( $post->post_date) . ' GMT' ), 'publish_future_post', array( $translation->element_id ) );
                }
            }
        }
    }

    public function icl_pro_translation_completed( $tr_product_id ){

        if( get_post_type( $tr_product_id ) == 'product' ){

            $trid = $this->sitepress->get_element_trid( $tr_product_id, 'post_product' );
            $translations = $this->sitepress->get_element_translations( $trid, 'post_product' );

            foreach( $translations as $translation ){
                if( $translation->original ){
                    $original_product_id = $translation->element_id;
                }
            }

            if( !isset( $original_product_id ) ){
                return;
            }

            $lang = $this->sitepress->get_language_for_element( $tr_product_id, 'post_product' );
            $this->sync_product_data( $original_product_id, $tr_product_id, $lang );

        }

    }

    public function icl_make_duplicate( $master_post_id, $lang, $postarr, $id ){
        if( get_post_type( $master_post_id ) == 'product' ){

            $master_post_id = $this->woocommerce_wpml->products->get_original_product_id( $master_post_id );

            $this->sync_product_data( $master_post_id, $id, $lang );
        }
    }

    public function woocommerce_product_quick_edit_save( $product ){

        $product_id =  WooCommerce_Functions_Wrapper::get_product_id( $product );
        $is_original = $this->woocommerce_wpml->products->is_original_product( $product_id );
        $trid = $this->sitepress->get_element_trid( $product_id, 'post_product' );

        if( $trid ){
            $translations = $this->sitepress->get_element_translations( $trid, 'post_product' );
            if( $translations ){
                foreach( $translations as $translation ){
                    if( $is_original ){
                        if( !$translation->original ){
                            $this->sync_product_data( $product_id, $translation->element_id, $translation->language_code );
                            $this->sync_date_and_parent( $product_id, $translation->element_id, $translation->language_code );
                        }
                    }elseif( $translation->original ){
                        $this->sync_product_data( $translation->element_id, $product_id, $this->sitepress->get_language_for_element( $product_id, 'post_product' ) );
                        $this->sync_date_and_parent( $translation->element_id, $product_id, $this->sitepress->get_language_for_element( $product_id, 'post_product' ) );
                    }
                }
            }
        }
    }

    //duplicate product post meta
    public function duplicate_product_post_meta( $original_product_id, $trnsl_product_id, $data = false ){
        global $iclTranslationManagement;

        if( $this->check_if_product_fields_sync_needed( $original_product_id, $trnsl_product_id, 'postmeta_fields' ) || $data ){
            $all_meta = get_post_custom( $original_product_id );
            $post_fields = null;

	        $settings_factory = new WPML_Custom_Field_Setting_Factory( $iclTranslationManagement );
            unset( $all_meta[ '_thumbnail_id' ] );

            foreach ( $all_meta as $key => $meta ) {

            	$setting = $settings_factory->post_meta_setting( $key );

                if ( WPML_IGNORE_CUSTOM_FIELD === $setting->status() ) {
                    continue;
                }

                foreach ( $meta as $meta_value ) {
                    if( $key == '_downloadable_files' ){
                        $this->woocommerce_wpml->downloadable->sync_files_to_translations( $original_product_id, $trnsl_product_id, $data );
                    }elseif ( $data ) {
                        if ( WPML_TRANSLATE_CUSTOM_FIELD  === $setting->status() ) {
                            $post_fields = $this->sync_custom_field_value( $key, $data, $trnsl_product_id, $post_fields, $original_product_id );
                        }
                    }
                }
            }
        }

        do_action( 'wcml_after_duplicate_product_post_meta', $original_product_id, $trnsl_product_id, $data );
    }

    public function sync_custom_field_value( $custom_field, $translation_data, $trnsl_product_id, $post_fields,  $original_product_id = false, $is_variation = false ){

        if( is_null( $post_fields ) ){
            $post_fields = array();
            if( isset( $_POST['data'] ) ){
                $post_args = wp_parse_args( $_POST['data'] );
                $post_fields = $post_args[ 'fields' ];
            }
        }

        $custom_filed_key = $is_variation && $original_product_id ? $custom_field.$original_product_id : $custom_field;

        if( isset( $translation_data[ md5( $custom_filed_key ) ] ) ){
            $meta_value = $translation_data[ md5( $custom_filed_key ) ];
            $meta_value = apply_filters( 'wcml_meta_value_before_add', $meta_value, $custom_filed_key );
            update_post_meta( $trnsl_product_id, $custom_field, $meta_value );
            unset( $post_fields[ $custom_filed_key ] );
        }else{
            foreach( $post_fields as $post_field_key => $post_field ){

                if( 1 === preg_match( '/field-' . $custom_field . '-.*?/', $post_field_key ) ){
	                $custom_fields        = array_filter( get_post_meta( $original_product_id, $custom_field, true ) );
	                $custom_fields_values = array_values( $custom_fields );
	                $custom_fields_keys   = array_keys( $custom_fields );
	                foreach ( $custom_fields_values as $custom_field_index => $custom_field_value ) {
		                $custom_fields_values =
			                $this->get_translated_custom_field_values(
				                $custom_fields_values,
				                $translation_data,
				                $custom_field,
				                $custom_field_value,
				                $custom_field_index
			                );
	                }

	                $custom_fields_translated = array();
	                foreach ( $custom_fields_values as $index => $value ) {
		                $custom_fields_translated[ $custom_fields_keys[ $index ] ] = $value;
	                }

                    update_post_meta( $trnsl_product_id, $custom_field, $custom_fields_translated );
                }else{
                    $meta_value = $translation_data[ md5( $post_field_key ) ];
                    $field_key = explode( ':', $post_field_key );
                    if( $field_key[0] == $custom_filed_key ){
                        if( 'new' === substr( $field_key[1], 0, 3 ) ){
                            add_post_meta( $trnsl_product_id, $custom_field, $meta_value );
                        }else{
                            update_meta( $field_key[1], $custom_field, $meta_value );
                        }
                        unset( $post_fields[ $post_field_key ] );
                    }
                }
            }
        }

        return $post_fields;
    }

    public function get_translated_custom_field_values( $custom_fields_values, $translation_data, $custom_field, $custom_field_value, $custom_field_index ){

        if( is_scalar( $custom_field_value ) ){
            $key_index = $custom_field . '-' .$custom_field_index;
            $cf        = 'field-' . $key_index;
            $meta_keys = explode( '-', $custom_field_index );
            $meta_keys = array_map( array( $this, 'replace_separator' ), $meta_keys );
            $custom_fields_values = $this->insert_under_keys(
               $meta_keys, $custom_fields_values, $translation_data[ md5( $cf ) ]
            );
        }else{
            foreach ( $custom_field_value as $ind => $value ) {
                $field_index = $custom_field_index. '-' . str_replace( '-', self::CUSTOM_FIELD_KEY_SEPARATOR, $ind );
                $custom_fields_values = $this->get_translated_custom_field_values( $custom_fields_values, $translation_data, $custom_field, $value, $field_index );
            }
        }

        return $custom_fields_values;

    }

    private function replace_separator( $el ) {
        return str_replace( self::CUSTOM_FIELD_KEY_SEPARATOR, '-', $el );
    }

    /**
     * Inserts an element into an array, nested by keys.
     * Input ['a', 'b'] for the keys, an empty array for $array and $x for the value would lead to
     * [ 'a' => ['b' => $x ] ] being returned.
     *
     * @param array $keys indexes ordered from highest to lowest level
     * @param array $array array into which the value is to be inserted
     * @param mixed $value to be inserted
     *
     * @return array
     */
    private function insert_under_keys( $keys, $array, $value ) {
        $array[ $keys[0] ] = count( $keys ) === 1
            ? $value
            : $this->insert_under_keys(
                array_slice($keys, 1),
                ( isset( $array[ $keys[0] ] ) ? $array[ $keys[0] ] : array() ),
                $value );

        return $array;
    }

    public function icl_connect_translations_action(){
        if( isset( $_POST[ 'icl_ajx_action' ] ) && $_POST[ 'icl_ajx_action' ] == 'connect_translations' ) {
            $new_trid = $_POST['new_trid'];
            $post_type = $_POST['post_type'];
            $post_id = $_POST['post_id'];
            $set_as_source = $_POST['set_as_source'];

            if ($post_type == 'product') {

                $translations = $this->sitepress->get_element_translations($new_trid, 'post_' . $post_type);

                if ($translations) {
                    foreach ($translations as $translation) {
                        //current as original need sync translation
                        if ($translation->original) {
                            if ($set_as_source) {
                                $orig_id = $post_id;
                                $trnsl_id = $translation->element_id;
                                $lang = $translation->language_code;
                            } else {
                                $orig_id = $translation->element_id;
                                $trnsl_id = $post_id;
                                $lang = $this->sitepress->get_current_language();
                            }
                            $this->sync_product_data($orig_id, $trnsl_id, $lang);
                            $this->sync_date_and_parent($orig_id, $trnsl_id, $lang);
                            $this->sitepress->copy_custom_fields($orig_id, $trnsl_id);
                            $this->woocommerce_wpml->translation_editor->create_product_translation_package($orig_id, $new_trid, $lang, ICL_TM_COMPLETE);
                        } else {
                            if ( $set_as_source ) {
                                $this->sync_product_data($post_id, $translation->element_id, $translation->language_code);
                                $this->sync_date_and_parent($post_id, $translation->element_id, $translation->language_code);
                                $this->sitepress->copy_custom_fields($post_id, $translation->element_id);
                                $this->woocommerce_wpml->translation_editor->create_product_translation_package($post_id, $new_trid, $translation->language_code, ICL_TM_COMPLETE);
                            }
                        }
                    }
                }
            }
        }
    }

    public function check_if_product_fields_sync_needed( $original_id, $trnsl_post_id, $fields_group ){

        $cache_group = 'is_product_fields_sync_needed';
        $cache_key = $trnsl_post_id.$fields_group;
        $temp_is_sync_needed = wp_cache_get( $cache_key, $cache_group );

        if( $temp_is_sync_needed !== false ) return (bool) $temp_is_sync_needed;

        $is_sync_needed = true;
        $hash = '';

        switch( $fields_group ){
            case 'postmeta_fields':
                $custom_fields = get_post_custom( $original_id );
                unset( $custom_fields[ 'wcml_sync_hash' ] );
                $hash = md5( serialize( $custom_fields ) );
                break;
            case 'taxonomies':
                $all_taxs = get_object_taxonomies( get_post_type( $original_id ) );
                $taxs = array();

                if ( !empty( $all_taxs ) ) {
                    foreach ($all_taxs as $tt) {
                        $terms = get_the_terms( $original_id, $tt );
                        if( !empty( $terms ) ) {
                            foreach ( $terms as $term ) {
                                $taxs[] = $term->term_id;
                            }
                        }
                    }
                }

                $hash = md5( join( ',', $taxs ) );
                break;
            case 'default_attributes':
                $hash = md5( get_post_meta( $original_id, '_default_attributes', true ) );
                break;
        }

	    $wcml_sync_hash = get_post_meta( $trnsl_post_id, 'wcml_sync_hash', true );
	    $post_md5 = $wcml_sync_hash === '' ? array() : maybe_unserialize( $wcml_sync_hash );

        if( isset( $post_md5[ $fields_group ] ) && $post_md5[ $fields_group ] == $hash ){
            $is_sync_needed = false;
        }else{
            $post_md5[ $fields_group ] = $hash;
            update_post_meta( $trnsl_post_id, 'wcml_sync_hash', $post_md5 );
        }

        wp_cache_set( $cache_key, intval( $is_sync_needed ), $cache_group );

        return $is_sync_needed;
    }

	public function sync_product_translations_visibility( $product_id ) {

		if ( $this->woocommerce_wpml->products->is_original_product( $product_id ) ) {

			$trid         = $this->sitepress->get_element_trid( $product_id, 'post_product' );
			$translations = $this->sitepress->get_element_translations( $trid, 'post_product' );

			if ( $translations ) {

				$product = wc_get_product( $product_id );
				$terms   = array();
				if ( $product->is_featured() ) {
					$terms[] = 'featured';
				}

				foreach ( $translations as $translation ) {
					if ( ! $translation->original ) {
						wp_set_post_terms( $translation->element_id, $terms, 'product_visibility', false );
					}
				}
			}
		}

	}

}