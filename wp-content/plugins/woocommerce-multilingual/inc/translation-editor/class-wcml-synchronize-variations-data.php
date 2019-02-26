<?php

class WCML_Synchronize_Variations_Data{

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var SitePress */
	private $sitepress;
	/** @var wpdb */
    private $wpdb;

	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, wpdb $wpdb ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
		$this->wpdb             = $wpdb;
	}

    public function add_hooks(){

	    add_action( 'woocommerce_ajax_save_product_variations', array( $this, 'sync_product_variations_action' ), 11 );
	    add_action( 'wp_ajax_woocommerce_remove_variations', array( $this, 'remove_translations_for_variations' ), 9 );

	    //save taxonomy in WPML interface
	    add_action( 'wp_ajax_wpml_tt_save_term_translation', array( $this, 'update_taxonomy_in_variations' ), 7 );

	    add_action( 'wp_ajax_woocommerce_remove_variation', array( $this, 'remove_variation_ajax' ), 9 );

    }

    public function sync_product_variations_action( $product_id ){

        if( $this->woocommerce_wpml->products->is_original_product( $product_id ) ){

            $this->sync_product_variations_custom_data( $product_id );

            $trid = $this->sitepress->get_element_trid( $product_id, 'post_product' );

            if ( empty( $trid ) ) {
                $trid = $this->wpdb->get_var(
                    $this->wpdb->prepare(
                        "SELECT trid FROM {$this->wpdb->prefix}icl_translations
                                    WHERE element_id = %d AND element_type = 'post_product'",
                        $product_id )
                );
            }
            $translations = $this->sitepress->get_element_translations( $trid, 'post_product' );
            foreach ( $translations as $translation ) {
                if ( !$translation->original ) {
                    $this->sync_product_variations($product_id, $translation->element_id, $translation->language_code);
                    $this->woocommerce_wpml->attributes->sync_default_product_attr( $product_id, $translation->element_id, $translation->language_code );
                }
            }
        }
    }

    public function sync_product_variations_custom_data( $product_id ){

        $is_variable_product = $this->woocommerce_wpml->products->is_variable_product( $product_id );
        if( $is_variable_product ) {
            $get_all_post_variations = $this->wpdb->get_results(
                $this->wpdb->prepare(
                    "SELECT * FROM {$this->wpdb->posts}
                                                WHERE post_status IN ('publish','private')
                                                  AND post_type = 'product_variation'
                                                  AND post_parent = %d
                                                ORDER BY ID"
                    , $product_id)
            );

            foreach ( $get_all_post_variations as $k => $post_data ) {

                if ( $this->woocommerce_wpml->settings[ 'enable_multi_currency' ] == WCML_MULTI_CURRENCIES_INDEPENDENT ) {
                    $this->woocommerce_wpml->multi_currency->custom_prices->sync_product_variations_custom_prices( $post_data->ID );
                }

                //save files option
                $this->woocommerce_wpml->downloadable->save_files_option( $post_data->ID );

            }
        }
    }

    /*
    * sync product variations
    * $product_id - original product id
    * $tr_product_id - translated product id
    * $lang - trnsl language
    * $data - array of values (when we save original product this array is empty, but when we update translation in this array we have price values and etc.)     *
    * */
    public function sync_product_variations( $product_id, $tr_product_id, $lang, $data = false, $trbl = false ){
        global $wpml_post_translations;

        $is_variable_product = $this->woocommerce_wpml->products->is_variable_product( $product_id );

        if( $is_variable_product ){

            remove_action ( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100, 2 );

            $all_variations = $this->get_product_variations( $product_id );
            $current_variations = $this->get_product_variations( $tr_product_id );

            foreach( $all_variations as $key => $post_data ) {
                $original_variation_id = $post_data->ID;
                //save files option
                $this->woocommerce_wpml->downloadable->save_files_option( $original_variation_id );

                $variation_id = $this->get_variation_id_by_lang( $lang, $original_variation_id );

                if( !empty( $variation_id ) && !is_null( $variation_id ) ) {
                    //unset variation from array to delete variations that no longer exist
                    unset( $current_variations[ $key ] );
                    // Update variation
                    wp_update_post( array(
                        'ID' => $variation_id,
                        'post_status' => $post_data->post_status,
                        'post_modified' => $post_data->post_modified,
                        'post_modified_gmt' => $post_data->post_modified_gmt,
                        'post_parent' => $tr_product_id, // current post ID
                        'menu_order' => $post_data->menu_order,
                    ));
                } else {
                    // Add new variation
                    $guid = $post_data->guid;
                    $replaced_guid = str_replace( $product_id, $tr_product_id, $guid );
                    $slug = $post_data->post_name;
                    $replaced_slug = str_replace( $product_id, $tr_product_id, $slug );
                    $variation_id = wp_insert_post( array(
                        'post_author' => $post_data->post_author,
                        'post_date_gmt' => $post_data->post_date_gmt,
                        'post_content' => $post_data->post_content,
                        'post_title' => $post_data->post_title,
                        'post_excerpt' => $post_data->post_excerpt,
                        'post_status' => $post_data->post_status,
                        'comment_status' => $post_data->comment_status,
                        'ping_status' => $post_data->ping_status,
                        'post_password' => $post_data->post_password,
                        'post_name' => $replaced_slug,
                        'to_ping' => $post_data->to_ping,
                        'pinged' => $post_data->pinged,
                        'post_modified' => $post_data->post_modified,
                        'post_modified_gmt' => $post_data->post_modified_gmt,
                        'post_content_filtered' => $post_data->post_content_filtered,
                        'post_parent' => $tr_product_id, // current post ID
                        'guid' => $replaced_guid,
                        'menu_order' => $post_data->menu_order,
                        'post_type' => $post_data->post_type,
                        'post_mime_type' => $post_data->post_mime_type,
                        'comment_count' => $post_data->comment_count
                    ));
                    add_post_meta( $variation_id, '_wcml_duplicate_of_variation', $original_variation_id );
                    $trid = $this->sitepress->get_element_trid( $original_variation_id, 'post_product_variation' );
                    $this->sitepress->set_element_language_details( $variation_id, 'post_product_variation', $trid, $lang );
                }

                //sync description
                if( isset( $data[ md5( '_variation_description'.$original_variation_id ) ] ) ){
                    update_post_meta( $variation_id, '_variation_description', $data[ md5( '_variation_description'.$original_variation_id ) ] );
                }

	            //sync media
	            $this->woocommerce_wpml->media->sync_variation_thumbnail_id( $original_variation_id, $variation_id, $lang );

	            //sync file_paths
                $this->woocommerce_wpml->downloadable->sync_files_to_translations( $original_variation_id, $variation_id, $data );

                //sync taxonomies
                $this->sync_variations_taxonomies( $original_variation_id, $variation_id, $lang );

                $this->duplicate_variation_data( $original_variation_id, $variation_id, $data, $lang, $trbl );

                $this->delete_removed_variation_attributes( $product_id, $variation_id );

                //refresh parent-children transients
                delete_transient( 'wc_product_children_' . $tr_product_id );
                delete_transient( '_transient_wc_product_children_ids_' . $tr_product_id );

            }

            // Delete variations that no longer exist
            foreach( $current_variations as $key => $current_post_variation ){
                wp_delete_post( $current_post_variation->ID, true );
            }

	        $this->sync_prices_variation_ids( $product_id, $tr_product_id, $lang );

            add_action ( 'save_post', array( $wpml_post_translations, 'save_post_actions' ), 100, 2 );
        }
    }

    public function get_variation_id_by_lang( $lang, $original_variation_id ){
        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT post_id FROM {$this->wpdb->postmeta} AS pm
                                        JOIN {$this->wpdb->prefix}icl_translations AS tr ON tr.element_id = pm.post_id
                                        WHERE tr.element_type = 'post_product_variation'
                                          AND tr.language_code = %s
                                          AND pm.meta_key = '_wcml_duplicate_of_variation'
                                          AND pm.meta_value = %d",
                $lang, $original_variation_id )
        );
    }

    public function sync_variations_taxonomies( $original_variation_id, $tr_variation_id, $lang ){

       remove_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10, 4 );

       if( $this->woocommerce_wpml->sync_product_data->check_if_product_fields_sync_needed( $original_variation_id, $tr_variation_id, 'taxonomies' ) ){

            $all_taxs = get_object_taxonomies( 'product_variation' );

            if ( !empty( $all_taxs ) ) {
                foreach ( $all_taxs as $tt ) {
                    if( isset( $tt->name ) ){
                        $name = $tt->name;
                    }else{
                        $name = $tt;
                    }

	                $terms    = get_the_terms( $original_variation_id, $name );
	                $tax_sync = array();

                    if ( !empty( $terms ) ) {
                        foreach ( $terms as $term ) {
                            if ( $this->sitepress->is_translated_taxonomy( $tt ) ) {
                                $term_id = apply_filters( 'translate_object_id', $term->term_id, $name, false, $lang );
                            } else {
                                $term_id = $term->term_id;
                            }
                            if ( $term_id ) {
                                $tax_sync[] = intval( $term_id );
                            }
                        }
                        //set the fourth parameter in 'true' because we need to add new terms, instead of replacing all
                        wp_set_object_terms( $tr_variation_id, $tax_sync, $name, true );
                    }elseif( ! $this->woocommerce_wpml->terms->is_translatable_wc_taxonomy( $name ) ){
	                    wp_set_object_terms( $tr_variation_id, $tax_sync, $name );
                    }
                }
            }
        }

        add_filter( 'terms_clauses', array( $this->sitepress, 'terms_clauses' ), 10, 4 );
    }

    public function duplicate_variation_data( $original_variation_id, $variation_id, $data, $lang, $trbl ){
        global $iclTranslationManagement;

        if( $this->woocommerce_wpml->sync_product_data->check_if_product_fields_sync_needed( $original_variation_id, $variation_id, 'postmeta_fields' ) || $trbl ){
            // custom fields
            $settings = $iclTranslationManagement->settings[ 'custom_fields_translation' ];
            $all_meta = get_post_custom( $original_variation_id );

            $post_fields = null;
            foreach( $all_meta as $meta_key => $meta ){

                foreach ( $meta as $meta_value ) {
                    // update current post variations meta

                    if( ( substr( $meta_key, 0, 10 ) == 'attribute_' || isset( $settings[ $meta_key ] ) && $settings[ $meta_key ] == WPML_COPY_CUSTOM_FIELD ) ) {

                        // adjust the global attribute slug in the custom field
                        $attid = null;
                        if( substr( $meta_key, 0, 10 ) == 'attribute_' ) {
	                        if( $meta_value ){
		                        $trn_post_meta = $this->woocommerce_wpml->attributes->get_translated_variation_attribute_post_meta( $meta_value, $meta_key, $original_variation_id, $variation_id, $lang );
		                        $meta_value = $trn_post_meta['meta_value'];
		                        $meta_key = $trn_post_meta['meta_key'];
	                        }else{
		                        $meta_value = '';
	                        }
                        }

                        if( $meta_key == '_stock'){
                            $this->update_stock_quantity( $variation_id, $meta_value );
                        }else{
                            update_post_meta( $variation_id, $meta_key, maybe_unserialize( $meta_value ) );
                        }
                    }elseif ( !isset( $settings[ $meta_key ] ) || $settings[ $meta_key ] == WPML_IGNORE_CUSTOM_FIELD ) {
                        continue;
                    }

                    //sync variation prices
                    if(
                        ( $this->woocommerce_wpml->settings[ 'enable_multi_currency' ] == WCML_MULTI_CURRENCIES_INDEPENDENT || $trbl ) &&
                        in_array( $meta_key, array( '_sale_price', '_regular_price', '_price' ) )
                    ){
                        $meta_value = get_post_meta( $original_variation_id, $meta_key, true );
                        update_post_meta( $variation_id, $meta_key, $meta_value );
                    }

                    if( isset( $settings[ $meta_key ] ) && $settings[ $meta_key ] == WPML_TRANSLATE_CUSTOM_FIELD ){
                        //sync custom fields
                        $post_fields = $this->woocommerce_wpml->sync_product_data->sync_custom_field_value( $meta_key, $data, $variation_id, $post_fields, $original_variation_id, true );
                    }
                }
            }
        }
    }

    //use direct query to update '_stock' to not trigger additional filters
    public function update_stock_quantity( $variation_id, $meta_value ){

        if( !get_post_meta( $variation_id, '_stock' ) ){
            $this->wpdb->insert( $this->wpdb->postmeta,
                array(
                    'meta_value' => $meta_value,
                    'meta_key'   => '_stock',
                    'post_id'    => $variation_id
                )
            );
        }else{
            $this->wpdb->update( $this->wpdb->postmeta,
                array(
                    'meta_value' => $meta_value
                ),
                array(
                    'meta_key'   => '_stock',
                    'post_id'    => $variation_id
                )
            );
        }

    }

    public function delete_removed_variation_attributes( $orig_product_id, $variation_id ){

        $original_product_attr = get_post_meta( $orig_product_id, '_product_attributes', true );

        $get_all_variation_attributes = $this->wpdb->get_results(
            $this->wpdb->prepare( "SELECT * FROM {$this->wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE 'attribute_%%' ",
                $variation_id )
        );

        foreach( $get_all_variation_attributes as $variation_attribute ){
            $attribute_name = substr( $variation_attribute->meta_key, 10 );
            if( !isset( $original_product_attr[ $attribute_name ] ) ){
                delete_post_meta( $variation_id, $variation_attribute->meta_key );
            }
        }

    }

    public function get_product_variations( $product_id ){

        $cache_key = $product_id;
        $cache_group = 'product_variations';
        $temp_product_variations = wp_cache_get( $cache_key, $cache_group );
        if( $temp_product_variations ) return $temp_product_variations;

        $variations = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->posts}
                 WHERE post_status IN ('publish','private')
                  AND post_type = 'product_variation'
                  AND post_parent = %d ORDER BY ID",
                $product_id )
        );

        wp_cache_set( $cache_key, $variations, $cache_group );

        return $variations;
    }

    public function remove_translations_for_variations(){
        check_ajax_referer( 'delete-variations', 'security' );

        if ( ! current_user_can( 'edit_products' ) ) {
            die(-1);
        }
        $variation_ids = (array) $_POST['variation_ids'];

        foreach ( $variation_ids as $variation_id ) {
            $trid = $this->sitepress->get_element_trid( $variation_id, 'post_product_variation' );
            $translations = $this->sitepress->get_element_translations( $trid, 'post_product_variation' );

            foreach ( $translations as $translation ) {
                if ( !$translation->original ) {
                    wp_delete_post( $translation->element_id );
                }
            }
        }
    }

    //update taxonomy in variations
    public function update_taxonomy_in_variations(){
        $original_element   = filter_input( INPUT_POST, 'translation_of', FILTER_SANITIZE_NUMBER_INT );
        $taxonomy           = filter_input( INPUT_POST, 'taxonomy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $language           = filter_input( INPUT_POST, 'language', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $slug               = filter_input( INPUT_POST, 'slug', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $name               = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        $term_id = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT term_id FROM {$this->wpdb->term_taxonomy} WHERE term_taxonomy_id = %d"
                ,$original_element )
        );
        $original_term = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $term_id, $taxonomy );
        $original_slug = $original_term->slug;
        //get variations with original slug
        $variations = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT post_id FROM {$this->wpdb->postmeta} WHERE meta_key=%s AND meta_value = %s",
                'attribute_'.$taxonomy, $original_slug
            )
        );

        foreach( $variations as $variation ){
            //update taxonomy in translation of variation
            $trnsl_variation_id = apply_filters( 'translate_object_id', $variation->post_id, 'product_variation', false, $language );
            if( !is_null( $trnsl_variation_id ) ){
                if( !$slug ){
                    $slug = sanitize_title( $name );
                }
                update_post_meta( $trnsl_variation_id, 'attribute_'.$taxonomy, $slug );
            }
        }
    }

    public function remove_variation_ajax(){
        if( isset( $_POST[ 'variation_id' ] ) ){
            $trid = $this->sitepress->get_element_trid( filter_input( INPUT_POST, 'variation_id', FILTER_SANITIZE_NUMBER_INT ), 'post_product_variation' );
            if( $trid ){
                $translations = $this->sitepress->get_element_translations( $trid, 'post_product_variation' );
                if( $translations ){
                    foreach( $translations as $translation ){
                        if( !$translation->original ){
                            wp_delete_post( $translation->element_id, true );
                        }
                    }
                }
            }
        }
    }

	/**
	 * Synchronize prices variation ids for product
	 *
	 * @param int $product_id
	 * @param int $tr_product_id
	 * @param string $language
	 */
    public function sync_prices_variation_ids( $product_id, $tr_product_id, $language ){

	    $prices_variation_ids_fields = array(
	    	'_min_price_variation_id',
	    	'_min_regular_price_variation_id',
	    	'_min_sale_price_variation_id',
	    	'_max_price_variation_id',
	    	'_max_regular_price_variation_id',
	    	'_max_sale_price_variation_id'
	    );

	    foreach( $prices_variation_ids_fields as $price_field ){

	    	$original_price_variation_id = get_post_meta( $product_id, $price_field, true );

	    	if( $original_price_variation_id ){
			    $translated_price_variation_id = apply_filters( 'translate_object_id', $original_price_variation_id, 'product_variation', false, $language );
			    if( !is_null( $translated_price_variation_id ) ){
			    	update_post_meta( $tr_product_id, $price_field, $translated_price_variation_id );
			    }
		    }
	    }
    }

}