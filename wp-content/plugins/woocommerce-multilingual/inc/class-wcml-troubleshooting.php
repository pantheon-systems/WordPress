<?php

class WCML_Troubleshooting{

    private $woocommerce_wpml;
    private $sitepress;
    private $wpdb;

    function __construct( &$woocommerce_wpml, &$sitepress, &$wpdb ){

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;
        $this->wpdb = $wpdb;
        
        add_action( 'init', array( $this, 'init' ) );
    }
    

    function init(){
        add_action('wp_ajax_trbl_sync_variations', array($this,'trbl_sync_variations'));
        add_action('wp_ajax_trbl_gallery_images', array($this,'trbl_gallery_images'));
        add_action('wp_ajax_trbl_update_count', array($this,'trbl_update_count'));
        add_action('wp_ajax_trbl_sync_categories', array($this,'trbl_sync_categories'));
        add_action('wp_ajax_trbl_duplicate_terms', array($this,'trbl_duplicate_terms'));
        add_action('wp_ajax_trbl_fix_product_type_terms', array($this,'trbl_fix_product_type_terms'));
        add_action( 'wp_ajax_trbl_sync_stock', array( $this, 'trbl_sync_stock' ) );

    }

    function wcml_count_products_with_variations(){
        return count(get_option('wcml_products_to_sync'));
    }

    function trbl_update_count(){

        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'trbl_update_count')){
            wp_send_json_error('Invalid nonce');
        }

        $this->wcml_sync_variations_update_option();

        $result = array(
            'count' => $this->wcml_count_products_with_variations()
        );

        wp_send_json_success( $result );
    }

    function wcml_sync_variations_update_option(){
        
        $get_variation_term_taxonomy_ids = $this->wpdb->get_var("SELECT tt.term_taxonomy_id FROM {$this->wpdb->terms} AS t LEFT JOIN {$this->wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id WHERE t.name = 'variable'");
        $get_variation_term_taxonomy_ids = apply_filters('wcml_variation_term_taxonomy_ids',(array)$get_variation_term_taxonomy_ids);

        $get_variables_products = $this->wpdb->get_results($this->wpdb->prepare("SELECT tr.element_id as id,tr.language_code as lang FROM {$this->wpdb->prefix}icl_translations AS tr LEFT JOIN {$this->wpdb->term_relationships} as t ON tr.element_id = t.object_id LEFT JOIN {$this->wpdb->posts} AS p ON tr.element_id = p.ID
                                WHERE p.post_status = 'publish' AND tr.source_language_code is NULL AND tr.element_type = 'post_product' AND t.term_taxonomy_id IN (%s) ORDER BY tr.element_id",join(',',$get_variation_term_taxonomy_ids)),ARRAY_A);

        update_option('wcml_products_to_sync',$get_variables_products);
    }

    function wcml_count_products(){
        
        $get_products_count = $this->wpdb->get_var("SELECT count(ID) FROM {$this->wpdb->posts} AS p LEFT JOIN {$this->wpdb->prefix}icl_translations AS tr ON tr.element_id = p.ID WHERE p.post_status = 'publish' AND p.post_type =  'product' AND tr.source_language_code is NULL");
        return $get_products_count;
    }

    function wcml_count_products_for_gallery_sync(){
        $all_products = $this->get_products_needs_gallery_sync( false );

        return count($all_products);
    }

    function wcml_count_product_categories(){

        $get_product_categories =  $this->get_product_categories_needs_sync( );

        return count($get_product_categories);
    }


    function trbl_sync_variations(){

        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'trbl_sync_variations')){
            wp_send_json_error('Invalid nonce');
        }

        $get_variables_products = get_option('wcml_products_to_sync');
        $all_active_lang = $this->sitepress->get_active_languages();
        $unset_keys = array();
        $products_for_one_ajax = array_slice($get_variables_products,0,3,true);


        foreach ($products_for_one_ajax as $key => $product){
            foreach($all_active_lang as $language){
                if($language['code'] != $product['lang']){
                    $tr_product_id = apply_filters( 'translate_object_id',$product['id'],'product',false,$language['code']);

                    if(!is_null($tr_product_id)){
                        $this->woocommerce_wpml->sync_variations_data->sync_product_variations($product['id'],$tr_product_id,$language['code'],false,true);
                    }
                    if(!in_array($key,$unset_keys)){
                        $unset_keys[] = $key;
                    }
                }
            }
        }


        foreach($unset_keys as $unset_key){
            unset($get_variables_products[$unset_key]);
        }

        update_option('wcml_products_to_sync',$get_variables_products);

        $wcml_settings = get_option('_wcml_settings');
        if(isset($wcml_settings['notifications']) && isset($wcml_settings['notifications']['varimages'])){
            $wcml_settings['notifications']['varimages']['show'] = 0;
            update_option('_wcml_settings', $wcml_settings);
        }

        wp_send_json_success();
    }

	function trbl_gallery_images(){
		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if(!$nonce || !wp_verify_nonce($nonce, 'trbl_gallery_images')){
			wp_send_json_error('Invalid nonce');
		}

		$all_products = $this->get_products_needs_gallery_sync( true );

		foreach( $all_products as $product ){
			$this->woocommerce_wpml->media->sync_product_gallery($product->ID);
			add_post_meta($product->ID,'gallery_sync',true);
		}

		wp_send_json_success();

	}

	function get_products_needs_gallery_sync( $limit = false ){

        $sql = "SELECT p.ID FROM {$this->wpdb->posts} AS p
                 LEFT JOIN {$this->wpdb->prefix}icl_translations AS tr
                 ON tr.element_id = p.ID
                 WHERE p.post_status = 'publish' AND p.post_type = 'product' AND tr.source_language_code is NULL
                 AND ( SELECT COUNT( pm.meta_key ) FROM {$this->wpdb->postmeta} AS pm WHERE pm.post_id = p.ID AND pm.meta_key = 'gallery_sync' ) = 0 ";

        if( $limit ){
            $sql .= "ORDER BY p.ID LIMIT 5";
        }

        $all_products = $this->wpdb->get_results( $sql );

        return $all_products;
    }

    function trbl_sync_categories(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'trbl_sync_categories')){
            wp_send_json_error('Invalid nonce');
        }

        $all_categories = $this->get_product_categories_needs_sync( true );

        foreach($all_categories as $category){
            add_option('wcml_sync_category_'.$category->term_taxonomy_id,true);
            $trid = $this->sitepress->get_element_trid($category->term_taxonomy_id,'tax_product_cat');
            $translations = $this->sitepress->get_element_translations($trid,'tax_product_cat');
            $type = get_woocommerce_term_meta( $category->term_id, 'display_type',true);
            $thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id',true);
            foreach($translations as $translation){
                if($translation->language_code != $category->language_code ){
                    update_woocommerce_term_meta( $translation->term_id, 'display_type', $type );
                    update_woocommerce_term_meta( $translation->term_id, 'thumbnail_id', apply_filters( 'translate_object_id',$thumbnail_id,'attachment',true,$translation->language_code) );
                }
            }
        }

        wp_send_json_success();

    }


    function get_product_categories_needs_sync( $limit = false ){

        $sql = "SELECT t.term_taxonomy_id,t.term_id,tr.language_code FROM {$this->wpdb->term_taxonomy} AS t
                 LEFT JOIN {$this->wpdb->prefix}icl_translations AS tr
                 ON tr.element_id = t.term_taxonomy_id
                 WHERE t.taxonomy = 'product_cat' AND tr.element_type = 'tax_product_cat' AND tr.source_language_code is NULL
                 AND ( SELECT COUNT( option_id ) FROM {$this->wpdb->options} WHERE option_name = CONCAT( 'wcml_sync_category_',t.term_taxonomy_id ) ) = 0 ";

        if( $limit ){
            $sql .= "ORDER BY t.term_taxonomy_id LIMIT 5";
        }

        $all_categories = $this->wpdb->get_results( $sql );

        return $all_categories;
    }


    function trbl_duplicate_terms(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'trbl_duplicate_terms')){
            wp_send_json_error('Invalid nonce');
        }

        $attr = isset($_POST['attr'])?$_POST['attr']:false;

        $terms = get_terms($attr,'hide_empty=0');
        $i = 0;
        $languages = $this->sitepress->get_active_languages();
        foreach($terms as $term){
            foreach($languages as $language){
                $tr_id = apply_filters( 'translate_object_id',$term->term_id, $attr, false, $language['code']);

                if(is_null($tr_id)){
                    $term_args = array();
                    // hierarchy - parents
                    if ( is_taxonomy_hierarchical( $attr ) ) {
                        // fix hierarchy
                        if ( $term->parent ) {
                            $original_parent_translated = apply_filters( 'translate_object_id', $term->parent, $attr, false, $language['code'] );
                            if ( $original_parent_translated ) {
                                $term_args[ 'parent' ] = $original_parent_translated;
                            }
                        }
                    }

                    $term_name = $term->name;
                    $slug = $term->name.'-'.$language['code'];
                    $slug = WPML_Terms_Translations::term_unique_slug( $slug, $attr, $language['code'] );
                    $term_args[ 'slug' ] = $slug;

                    $new_term = wp_insert_term( $term_name , $attr, $term_args );
                    if ( $new_term && !is_wp_error( $new_term ) ) {
                        $tt_id = $this->sitepress->get_element_trid( $term->term_taxonomy_id, 'tax_' . $attr );
                        $this->sitepress->set_element_language_details( $new_term[ 'term_taxonomy_id' ], 'tax_' . $attr, $tt_id, $language['code'] );
                    }
                }
            }

        }

        wp_send_json_success();
    }

    function trbl_fix_product_type_terms(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'trbl_product_type_terms')){
            wp_send_json_error('Invalid nonce');
        }

        WCML_Install::check_product_type_terms();

        wp_send_json_success();
    }

    function wcml_count_product_stock_sync(){

        $results = $this->get_products_needs_stock_sync();

        return count( $results );
    }

    function trbl_sync_stock(){

        $nonce = array_key_exists( 'wcml_nonce', $_POST ) ? sanitize_text_field( $_POST[ 'wcml_nonce' ] ) : false;
        if(!$nonce || !wp_verify_nonce($nonce, 'trbl_sync_stock')){
            wp_send_json_error('Invalid nonce');
        }

        $results = $this->get_products_needs_stock_sync();

        foreach( $results as $product ){

            if( get_post_meta( $product->ID, '_manage_stock', true ) === 'yes' ){

                $translations = $this->sitepress->get_element_translations( $product->trid, $product->element_type );

                $min_stock = false;
                $stock_status = 'instock';

                //collect min stock
                foreach( $translations as $translation ){
                    $stock = get_post_meta( $translation->element_id, '_stock', true );
                    if( !$min_stock || $stock < $min_stock ){
                        $min_stock = $stock;
                        $stock_status = get_post_meta( $translation->element_id, '_stock_status', true );
                    }
                }

                //update stock value
                foreach( $translations as $translation ){
                    update_post_meta( $translation->element_id, '_stock', $min_stock );
                    update_post_meta( $translation->element_id, '_stock_status', $stock_status );
                }
            }
        }

        wp_send_json_success();
    }

    function get_products_needs_stock_sync(){

        $results = $this->wpdb->get_results("
                        SELECT p.ID, t.trid, t.element_type
                        FROM {$this->wpdb->posts} p
                        JOIN {$this->wpdb->prefix}icl_translations t ON t.element_id = p.ID AND t.element_type IN ('post_product', 'post_product_variation')
                        WHERE p.post_type in ('product', 'product_variation') AND t.source_language_code IS NULL
                    ");

        return $results;
    }


}