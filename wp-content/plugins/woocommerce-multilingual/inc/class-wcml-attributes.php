<?php

class WCML_Attributes{

    /** @var woocommerce_wpml */
    private $woocommerce_wpml;
    /** @var Sitepress */
    private $sitepress;
    /** @var wpdb */
    private $wpdb;

    /**
     * WCML_Attributes constructor.
     *
     * @param woocommerce_wpml $woocommerce_wpml
     * @param SitePress $sitepress
     * @param wpdb $wpdb
     */
    public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, wpdb $wpdb ){
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress = $sitepress;
        $this->wpdb = $wpdb;
    }

    public function add_hooks(){

        add_action( 'init', array( $this, 'init' ) );

        add_action( 'woocommerce_attribute_added', array( $this, 'set_attribute_readonly_config' ), 100, 2 );
        add_filter( 'wpml_translation_job_post_meta_value_translated', array($this, 'filter_product_attributes_for_translation'), 10, 2 );
        add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array($this, 'filter_dropdown_variation_attribute_options_args') );

        if( isset( $_POST['icl_ajx_action'] ) && $_POST['icl_ajx_action'] == 'icl_custom_tax_sync_options' ){
            $this->icl_custom_tax_sync_options();
        }

        add_action( 'woocommerce_before_attribute_delete', array( $this, 'refresh_taxonomy_translations_cache' ), 10, 3 );

        $deprecated_wc = $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'WC_VERSION' ), '3.0.0', '<' );
        if ( $deprecated_wc ) {
            add_filter( 'woocommerce_get_product_attributes', array( $this, 'filter_adding_to_cart_product_attributes_names' ) );
        }else{
            add_filter( 'woocommerce_product_get_attributes', array( $this, 'filter_adding_to_cart_product_attributes_names' ) );
        }

	    if ( $this->woocommerce_wpml->products->is_product_display_as_translated_post_type() ) {
		    add_filter( 'woocommerce_available_variation', array(
			    $this,
			    'filter_available_variation_attribute_values_in_current_language'
		    ) );
		    add_filter( 'get_post_metadata', array(
			    $this,
			    'filter_product_variation_post_meta_attribute_values_in_current_language'
		    ), 10, 4 );
		    add_filter( 'woocommerce_product_get_default_attributes', array(
			    $this,
			    'filter_product_variation_default_attributes'
		    ) );
	    }
	    add_action( 'update_post_meta', array( $this, 'set_translation_status_as_needs_update' ), 10, 3 );
    }

    public function init(){

        $is_attr_page = apply_filters( 'wcml_is_attributes_page', isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'product_attributes' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'product' );

        if( $is_attr_page ){

            add_action( 'admin_init', array( $this, 'not_translatable_html' ) );

            if( isset( $_POST[ 'save_attribute' ] ) && isset( $_GET[ 'edit' ] ) ){
                $this->set_attribute_readonly_config( $_GET[ 'edit' ], $_POST );
            }
        }

    }

    /*
     * This creates the terms translation cache so the translations can be deleted via the 'delete_term' hook
     * after the original term was deleted and getting the translations directly from the db is not possible
     */
    public function refresh_taxonomy_translations_cache( $attribute_id, $attribute_name, $taxonomy ){

	    $terms = get_terms( $taxonomy, 'orderby=name&hide_empty=0' );
	    foreach ( $terms as $term ) {
		    $trid = $this->sitepress->get_element_trid( $term->term_taxonomy_id, 'tax_' . $taxonomy );
	    }

    }

    public function not_translatable_html(){
        $attr_id = isset( $_GET[ 'edit' ] ) ? absint( $_GET[ 'edit' ] ) : false;

        $attr_is_tnaslt = new WCML_Not_Translatable_Attributes( $attr_id, $this->woocommerce_wpml );
        $attr_is_tnaslt->show();
    }

    public function get_attribute_terms( $attribute ){

        return $this->wpdb->get_results($this->wpdb->prepare("
                        SELECT * FROM {$this->wpdb->term_taxonomy} x JOIN {$this->wpdb->terms} t ON x.term_id = t.term_id WHERE x.taxonomy = %s", $attribute ) );

    }

    public function set_attribute_readonly_config( $id, $attribute ){

        $is_translatable = isset( $_POST[ 'wcml-is-translatable-attr' ] ) ? 1 : 0;
        $attribute_name = wc_attribute_taxonomy_name( $attribute['attribute_name'] );
        if( $is_translatable === 0 ){
            //delete all translated attributes terms if "Translatable?" option un-checked
            $this->delete_translated_attribute_terms( $attribute_name );
            $this->set_variations_to_use_original_attributes( $attribute_name );
            $this->set_original_attributes_for_products( $attribute_name );
        }
        $this->set_attribute_config_in_settings( $attribute_name, $is_translatable );
    }

    public function set_attribute_config_in_settings( $attribute_name, $is_translatable ){
        $this->set_attribute_config_in_wcml_settings( $attribute_name, $is_translatable );
        $this->set_attribute_config_in_wpml_settings( $attribute_name, $is_translatable );

        $this->woocommerce_wpml->terms->update_terms_translated_status( $attribute_name );
    }

    public function set_attribute_config_in_wcml_settings( $attribute_name, $is_translatable ){
        $wcml_settings = $this->woocommerce_wpml->get_settings();
        $wcml_settings[ 'attributes_settings' ][ $attribute_name ] = $is_translatable;
        $this->woocommerce_wpml->update_settings( $wcml_settings );
    }

    public function set_attribute_config_in_wpml_settings( $attribute_name, $is_translatable ){

        $sync_settings = $this->sitepress->get_setting( 'taxonomies_sync_option', array() );
        $sync_settings[ $attribute_name ] = $is_translatable;
        $this->sitepress->set_setting( 'taxonomies_sync_option', $sync_settings, true );
        $this->sitepress->verify_taxonomy_translations( $attribute_name );
    }

    public function delete_translated_attribute_terms( $attribute ){
        $terms = $this->get_attribute_terms( $attribute );

        foreach( $terms as $term ){
            $term_language_details = $this->sitepress->get_element_language_details( $term->term_id, 'tax_'.$attribute );
            if( $term_language_details && $term_language_details->source_language_code ){
                wp_delete_term( $term->term_id, $attribute );
            }
        }

    }

    public function set_variations_to_use_original_attributes( $attribute ){
        $terms = $this->get_attribute_terms( $attribute );

        foreach( $terms as $term ){
            $term_language_details = $this->sitepress->get_element_language_details( $term->term_id, 'tax_'.$attribute );
            if( $term_language_details && is_null( $term_language_details->source_language_code ) ){
                $variations = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT post_id FROM {$this->wpdb->postmeta} WHERE meta_key=%s AND meta_value = %s",  'attribute_'.$attribute, $term->slug ) );

                foreach( $variations as $variation ){
                    //update taxonomy in translation of variation
                    foreach( $this->sitepress->get_active_languages() as $language ){

                        $trnsl_variation_id = apply_filters( 'translate_object_id', $variation->post_id, 'product_variation', false, $language['code'] );
                        if( !is_null( $trnsl_variation_id ) ){
                            update_post_meta( $trnsl_variation_id, 'attribute_'.$attribute, $term->slug );
                        }
                    }
                }
            }
        }
    }

    public function set_original_attributes_for_products( $attribute ){

        $terms = $this->get_attribute_terms( $attribute );
        $cleared_products = array();
        foreach( $terms as $term ) {
            $term_language_details = $this->sitepress->get_element_language_details( $term->term_id, 'tax_'.$attribute );
            if( $term_language_details && is_null( $term_language_details->source_language_code ) ){
                $args = array(
                    'tax_query' => array(
                        array(
                            'taxonomy' => $attribute,
                            'field' => 'slug',
                            'terms' => $term->slug
                        )
                    )
                );

                $products = get_posts($args);

                foreach( $products as $product ){

                    foreach( $this->sitepress->get_active_languages() as $language ) {

                        $trnsl_product_id = apply_filters( 'translate_object_id', $product->ID, 'product', false, $language['code'] );

                        if ( !is_null( $trnsl_product_id ) ) {
                            if( !in_array( $trnsl_product_id, $trnsl_product_id ) ){
                                wp_delete_object_term_relationships( $trnsl_product_id, $attribute );
                                $cleared_products[] = $trnsl_product_id;
                            }
                            wp_set_object_terms( $trnsl_product_id, $term->slug, $attribute, true );
                        }
                    }
                }
            }
        }
    }


    public function is_translatable_attribute( $attr_name ){

        if( !isset( $this->woocommerce_wpml->settings[ 'attributes_settings' ][ $attr_name ] ) ){
            $this->set_attribute_config_in_settings( $attr_name, 1 );
        }

        return isset( $this->woocommerce_wpml->settings[ 'attributes_settings' ][ $attr_name ] ) ? $this->woocommerce_wpml->settings[ 'attributes_settings' ][ $attr_name ] : 1;
    }

    public function get_translatable_attributes(){
        $attributes = wc_get_attribute_taxonomies();

        $translatable_attributes = array();
        foreach( $attributes as $attribute ){
            if( $this->is_translatable_attribute( wc_attribute_taxonomy_name( $attribute->attribute_name ) ) ){
                $translatable_attributes[] = $attribute;
            }
        }

        return $translatable_attributes;
    }

    public function set_translatable_attributes( $attributes ){

        foreach( $attributes as $name => $is_translatable ){

            $attribute_name = wc_attribute_taxonomy_name( $name );
            $this->set_attribute_config_in_settings( $attribute_name, $is_translatable );

        }
    }

    public function sync_product_attr( $original_product_id, $tr_product_id, $language = false, $data = false ){

        //get "_product_attributes" from original product
        $orig_product_attrs = $this->get_product_atributes( $original_product_id );
        $trnsl_product_attrs = $this->get_product_atributes( $tr_product_id );

        $trnsl_labels = $this->get_attr_label_translations( $tr_product_id );

        foreach ( $orig_product_attrs as $key => $orig_product_attr ) {
            $sanitized_key = sanitize_title( $orig_product_attr[ 'name' ] );
            if( $sanitized_key != $key ) {
                $orig_product_attrs_buff = $orig_product_attrs[ $key ];
                unset( $orig_product_attrs[ $key ] );
                $orig_product_attrs[ $sanitized_key ] = $orig_product_attrs_buff;
                $key_to_save = $sanitized_key;
            }else{
                $key_to_save = $key;
            }
            if ( $data ){
                if ( isset( $data[ md5( $key ) ] ) && !empty( $data[ md5( $key ) ] ) && !is_array( $data[ md5( $key ) ] ) ) {
                    //get translation values from $data
                    $orig_product_attrs[ $key_to_save ][ 'value' ] = $data[ md5( $key ) ];
                } else {
                    $orig_product_attrs[ $key_to_save ][ 'value' ] = '';
                }

                if ( isset( $data[ md5( $key . '_name' ) ] ) && !empty( $data[ md5( $key . '_name' ) ] ) && !is_array( $data[ md5( $key . '_name' ) ] ) ) {
                    //get translation values from $data
                    $trnsl_labels[ $language ][ $key_to_save ] = stripslashes( $data[ md5( $key . '_name' ) ] );
                } else {
                    $trnsl_labels[ $language ][ $key_to_save ] = '';
                }
            }elseif( !$orig_product_attr[ 'is_taxonomy' ] ){
	            $duplicate_of = get_post_meta( $tr_product_id, '_icl_lang_duplicate_of', true );

	            if( !$duplicate_of ){
		            if( isset( $trnsl_product_attrs[ $key ] ) ){
			            $orig_product_attrs[ $key_to_save ][ 'value' ] = $trnsl_product_attrs[ $key ][ 'value' ];
		            }elseif( !empty( $trnsl_product_attrs ) ){
			            unset ( $orig_product_attrs[ $key_to_save ] );
		            }
	            }
            }
        }

        update_post_meta( $tr_product_id, 'attr_label_translations', $trnsl_labels );
        //update "_product_attributes"
        update_post_meta( $tr_product_id, '_product_attributes', $orig_product_attrs );
    }

    public function get_product_atributes( $product_id ){
        $attributes = get_post_meta( $product_id, '_product_attributes', true );
        if( !is_array( $attributes ) ){
            $attributes = array();
        }
        return $attributes;
    }

    public function get_attr_label_translations( $product_id, $lang = false ){
        $trnsl_labels = get_post_meta( $product_id, 'attr_label_translations', true );

        if( !is_array( $trnsl_labels ) ){
            $trnsl_labels = array();
        }

        if( isset( $trnsl_labels[ $lang ] ) ){
            return $trnsl_labels[ $lang ];
        }

        return $trnsl_labels;
    }

    public function sync_default_product_attr( $orig_post_id, $transl_post_id, $lang ){
        $original_default_attributes = get_post_meta( $orig_post_id, '_default_attributes', true );

        if( !empty( $original_default_attributes ) ){
            $unserialized_default_attributes = array();
            foreach(maybe_unserialize( $original_default_attributes ) as $attribute => $default_term_slug ){
                // get the correct language
                if ( substr( $attribute, 0, 3 ) == 'pa_' ) {
                    //attr is taxonomy
                    if( $this->is_translatable_attribute( $attribute ) ){
                        $default_term_id = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $attribute, $default_term_slug );
                        $tr_id = apply_filters( 'translate_object_id', $default_term_id, $attribute, false, $lang );

                        if( $tr_id ){
                            $translated_term = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $tr_id, $attribute );
                            $unserialized_default_attributes[ $attribute ] = $translated_term->slug;
                        }
                    }else{
                        $unserialized_default_attributes[ $attribute ] = $default_term_slug;
                    }
                }else{
                    //custom attr
                    $orig_product_attributes = get_post_meta( $orig_post_id, '_product_attributes', true );
                    $unserialized_orig_product_attributes = maybe_unserialize( $orig_product_attributes );

                    if( isset( $unserialized_orig_product_attributes[ $attribute ] ) ){
                        $orig_attr_values = explode( '|', $unserialized_orig_product_attributes[ $attribute ][ 'value' ] );
	                    $orig_attr_values = array_map( 'trim', $orig_attr_values );

                        foreach( $orig_attr_values as $key => $orig_attr_value ){
                            $orig_attr_value_sanitized = strtolower( sanitize_title ( $orig_attr_value ) );

                            if( $orig_attr_value_sanitized == $default_term_slug || trim( $orig_attr_value ) == trim( $default_term_slug ) ){
                                $tnsl_product_attributes = get_post_meta( $transl_post_id, '_product_attributes', true );
                                $unserialized_tnsl_product_attributes = maybe_unserialize( $tnsl_product_attributes );

                                if( isset( $unserialized_tnsl_product_attributes[ $attribute ] ) ){
                                    $trnsl_attr_values = explode( '|', $unserialized_tnsl_product_attributes[ $attribute ][ 'value' ] );

                                    if( $orig_attr_value_sanitized == $default_term_slug ){
                                        $trnsl_attr_value = strtolower( sanitize_title( trim( $trnsl_attr_values[ $key ] ) ) );
                                    }else{
                                        $trnsl_attr_value = trim( $trnsl_attr_values[ $key ] );
                                    }
                                    $unserialized_default_attributes[ $attribute ] = $trnsl_attr_value;
                                }
                            }
                        }
                    }
                }
            }

            $data = array( 'meta_value' => maybe_serialize( $unserialized_default_attributes ) );
        }else{
            $data = array( 'meta_value' => maybe_serialize( array() ) );
        }

        $where = array( 'post_id' => $transl_post_id, 'meta_key' => '_default_attributes' );

        $translated_product_meta = get_post_meta( $transl_post_id );
	    if ( isset( $translated_product_meta['_default_attributes'] ) ) {
		    $this->wpdb->update( $this->wpdb->postmeta, $data, $where );
	    } else {
		    $this->wpdb->insert( $this->wpdb->postmeta, array_merge( $data, $where ) );
	    }

    }

    /*
     * get attribute translation
     */
    public function get_custom_attribute_translation( $product_id, $attribute_key, $attribute, $lang_code ){
        $tr_post_id = apply_filters( 'translate_object_id', $product_id, 'product', false, $lang_code );
        $transl = array();
        if( $tr_post_id ){
            if( !$attribute[ 'is_taxonomy' ] ){
                $tr_attrs = get_post_meta($tr_post_id, '_product_attributes', true);
                if( $tr_attrs ){
                    foreach( $tr_attrs as $key => $tr_attr ) {
                        if( $attribute_key == $key ){
                            $transl[ 'value' ] = $tr_attr[ 'value' ];
                            $trnsl_labels = $this->get_attr_label_translations( $tr_post_id );

                            if( isset( $trnsl_labels[ $lang_code ][ $attribute_key ] ) ){
                                $transl[ 'name' ] = $trnsl_labels[ $lang_code ][ $attribute_key ];
                            }else{
                                $transl[ 'name' ] = $tr_attr[ 'name' ];
                            }
                            return $transl;
                        }
                    }
                }
                return false;
            }
        }
        return false;
    }

    /*
    * Get custom attribute translation
    * Returned translated attribute or original if missed
    */
    public function get_custom_attr_translation( $product_id, $tr_product_id, $taxonomy, $attribute ){
        $orig_product_attributes = get_post_meta( $product_id, '_product_attributes', true );
        $unserialized_orig_product_attributes = maybe_unserialize( $orig_product_attributes );

        foreach( $unserialized_orig_product_attributes as $orig_attr_key => $orig_product_attribute ){
            $orig_attr_key = urldecode( $orig_attr_key );
            if( strtolower( $taxonomy ) == $orig_attr_key ){
                $values = explode( '|', $orig_product_attribute[ 'value' ] );

                foreach( $values as $key_id => $value ){
                    if( trim( $value," " ) == $attribute ){
                        $attr_key_id = $key_id;
                    }
                }
            }
        }

        $trnsl_product_attributes = get_post_meta( $tr_product_id, '_product_attributes', true );
        $unserialized_trnsl_product_attributes = maybe_unserialize( $trnsl_product_attributes );
        $taxonomy = sanitize_title( $taxonomy );
        $trnsl_attr_values = explode( '|', $unserialized_trnsl_product_attributes[ $taxonomy ][ 'value' ] );

        if( isset( $attr_key_id ) && isset( $trnsl_attr_values[ $attr_key_id ] ) ){
            return trim( $trnsl_attr_values[ $attr_key_id ] );
        }

        return $attribute;
    }

    public function filter_product_attributes_for_translation( $translated, $key ){
        $translated = $translated
            ? preg_match('#^(?!field-_product_attributes-(.+)-(.+)-(?!value|name))#', $key) : 0;

        return $translated;
    }

    public function icl_custom_tax_sync_options(){
        foreach( $_POST['icl_sync_tax'] as $taxonomy => $value){
            if ( substr( $taxonomy, 0, 3 ) == 'pa_' ) {
                $this->set_attribute_config_in_wcml_settings( $taxonomy , $value);
            }
        }

    }

    public function is_attributes_fully_translated(){

        $product_attributes = $this->get_translatable_attributes();

        $fully_translated = true;

        if( $product_attributes ){
            foreach( $product_attributes as $attribute ){
                $is_fully_translated = $this->woocommerce_wpml->terms->is_fully_translated( 'pa_' . $attribute->attribute_name );
                if( !$is_fully_translated ){
                    $fully_translated = false;
                    break;
                }
            }
        }

        return $fully_translated;
    }

    public function get_translated_variation_attribute_post_meta( $meta_value, $meta_key, $original_variation_id, $variation_id, $lang ){

        $original_product_attr = get_post_meta( wp_get_post_parent_id( $original_variation_id ), '_product_attributes', true );
        $tr_product_attr = get_post_meta( wp_get_post_parent_id( $variation_id ), '_product_attributes', true );

        $tax = wc_sanitize_taxonomy_name ( substr( $meta_key, 10 ) );
        if( taxonomy_exists( $tax ) ){
            $attid = $this->woocommerce_wpml->terms->wcml_get_term_id_by_slug( $tax, $meta_value );
            if( $this->is_translatable_attribute( $tax ) && $attid ){

                $term_obj = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $attid, $tax );
                $trnsl_term_id = apply_filters( 'translate_object_id', $term_obj->term_id, $tax, false, $lang );

                if( $trnsl_term_id ) {
                    $trnsl_term_obj = $this->woocommerce_wpml->terms->wcml_get_term_by_id( $trnsl_term_id, $tax );
                    $meta_value = $trnsl_term_obj->slug;
                }
            }
        }else{
            if( !isset( $original_product_attr[ $tax ] ) ){
                $tax = sanitize_title( $tax );
            }

            if( isset( $original_product_attr[ $tax ] ) ){
                if( isset( $tr_product_attr[ $tax ] ) ){
                    $values_arrs = array_map( 'trim', explode( '|', $original_product_attr[ $tax ][ 'value' ] ) );
                    $values_arrs_tr = array_map( 'trim', explode( '|', $tr_product_attr[ $tax ][ 'value' ] ) );

                    foreach( $values_arrs as $key => $value ){
                        $value_sanitized = sanitize_title( $value );
                        if(
                            ( $value_sanitized == strtolower( urldecode( $meta_value ) ) ||
                                strtolower( $value_sanitized ) == $meta_value ||
                                $value == $meta_value )
                            && isset( $values_arrs_tr[ $key ] ) )
                        {
                            $meta_value = $values_arrs_tr[ $key ];
                        }
                    }
                }
            }
            $meta_key = 'attribute_'.$tax;
        }

        return array(
            'meta_value' => $meta_value,
            'meta_key' => $meta_key
        );
    }

    function filter_dropdown_variation_attribute_options_args( $args ){

        if( isset( $args['attribute'] ) && isset( $args['product'] ) ){
            $args['attribute'] = $this->filter_attribute_name( $args['attribute'],  WooCommerce_Functions_Wrapper::get_product_id( $args['product'] ) );

            if( $this->woocommerce_wpml->products->is_product_display_as_translated_post_type() ){
	            foreach( $args[ 'options' ] as $key => $attribute_value ){
		            $args[ 'options' ][ $key ] = $this->get_attribute_term_translation_in_current_language( $args[ 'attribute' ], $attribute_value );
	            }
            }
        }

        return $args;
    }

    /*
     * special case when original attribute language is German or Danish,
     * needs handle special chars accordingly
     * https://onthegosystems.myjetbrains.com/youtrack/issue/wcml-1785
     */
    function filter_attribute_name( $attribute_name, $product_id, $return_sanitized = false ) {

        if ( ! is_admin() && $product_id ) {
            $orig_lang = $this->woocommerce_wpml->products->get_original_product_language( $product_id );
            $current_language = $this->sitepress->get_current_language();

            if ( in_array( $orig_lang, array( 'de', 'da' ) ) && $current_language !== $orig_lang ) {
                $attribute_name = $this->sitepress->locale_utils->filter_sanitize_title( remove_accents( $attribute_name ), $attribute_name );
                remove_filter( 'sanitize_title', array( $this->sitepress->locale_utils, 'filter_sanitize_title' ), 10 );
            }
        }

        if ( $return_sanitized ) {
            $attribute_name = sanitize_title( $attribute_name );
        }

        return $attribute_name;
    }

    function filter_adding_to_cart_product_attributes_names( $attributes ){

        if( !is_admin() && isset( $_REQUEST['add-to-cart'] ) ){

            foreach( $attributes as $key => $attribute ){
                $attributes[ $key ]['name'] = $this->filter_attribute_name( $attributes[ $key ]['name'], $_REQUEST['add-to-cart'] );
            }

        }

        return $attributes;
    }

    public function is_a_taxonomy( $attribute ){

        if(
            (
                $attribute instanceof WC_Product_Attribute &&
                $attribute->is_taxonomy()
            ) ||
            (
                is_array( $attribute ) &&
                $attribute['is_taxonomy']
            )
        ){
            return true;
        }

        return false;
    }

	/**
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function filter_available_variation_attribute_values_in_current_language( $args ) {

		foreach ( $args['attributes'] as $attribute_key => $attribute_value ) {

			$args['attributes'][ $attribute_key ] = $this->get_attribute_term_translation_in_current_language( substr( $attribute_key, 10 ), $attribute_value );
		}

		return $args;
	}

	/**
	 * @param null $value
	 * @param int $object_id
	 * @param string $meta_key
	 * @param bool $single
	 *
	 * @return array
	 */
	public function filter_product_variation_post_meta_attribute_values_in_current_language( $value, $object_id, $meta_key, $single ) {

		if ( '' === $meta_key && 'product_variation' === get_post_type( $object_id ) ) {

			$cache_group  = 'wpml-all-meta-product-variation';
			$cache_key     = $this->sitepress->get_current_language() . $object_id;
			$cached_value = wp_cache_get( $cache_key, $cache_group );

			if ( $cached_value ) {
				return $cached_value;
			}

			remove_filter( 'get_post_metadata', array(
				$this,
				'filter_product_variation_post_meta_attribute_values_in_current_language'
			), 10 );

			$all_meta = get_post_meta( $object_id );

			add_filter( 'get_post_metadata', array(
				$this,
				'filter_product_variation_post_meta_attribute_values_in_current_language'
			), 10, 4 );

			if ( $all_meta ) {
				foreach ( $all_meta as $meta_key => $meta_value ) {
					if ( 'attribute_' === substr( $meta_key, 0, 10 ) ) {
						foreach ( $meta_value as $key => $value ) {
							$all_meta[ $meta_key ][ $key ] = $this->get_attribute_term_translation_in_current_language( substr( $meta_key, 10 ), $value );
						}
					}
				}

				wp_cache_add( $cache_key, $all_meta, $cache_group );

				return $all_meta;
			}

		}

		return $value;

	}


	/**
	 * @param array $default_attributes
	 *
	 * @return array
	 */
	public function filter_product_variation_default_attributes( $default_attributes ){

		if( $default_attributes ){

			foreach( $default_attributes as $attribute_key => $attribute_value ){

				$default_attributes[ $attribute_key ] = $this->get_attribute_term_translation_in_current_language( $attribute_key, $attribute_value );

			}

		}

		return $default_attributes;
	}

	/**
	 *
	 * @param string $attribute_taxonomy
	 * @param string $attribute_value
	 *
	 * @return string
	 */
	private function get_attribute_term_translation_in_current_language( $attribute_taxonomy, $attribute_value ) {

		if( taxonomy_exists( $attribute_taxonomy ) ){
			$term = get_term_by( 'slug', $attribute_value, $attribute_taxonomy );
			if( $term ){
				$attribute_value = $term->slug;
			}
		}

		return $attribute_value;
	}

	/**
	 * @param int $meta_id
	 * @param int $object_id
	 * @param string $meta_key
	 */
	public function set_translation_status_as_needs_update( $meta_id, $object_id, $meta_key ) {
		if ( $meta_key === '_product_attributes' ) {

			$status_helper               = wpml_get_post_status_helper();
			$translation_element_factory = new WPML_Translation_Element_Factory( $this->sitepress );
			$post_element                = $translation_element_factory->create_post( $object_id );

			if ( null === $post_element->get_source_language_code() ) {
				foreach ( $post_element->get_translations() as $translation ) {
					if ( null !== $translation->get_source_language_code() ) {
						$status_helper->set_update_status( $translation->get_id(), 1 );
					}
				}
			}
		}
	}

}