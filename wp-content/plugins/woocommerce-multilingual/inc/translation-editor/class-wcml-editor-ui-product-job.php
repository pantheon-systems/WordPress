<?php

class WCML_Editor_UI_Product_Job extends WPML_Editor_UI_Job {
    
    private $data = array();
    private $woocommerce_wpml;
	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var TranslationManagement
	 */
	private $tm_instance;
	/**
	 * @var WPDB
	 */
	private $wpdb;
    private $job_details;
    private $product;
    private $product_id;
    private $product_type;
    private $not_display_fields_for_variables_product;
    private $not_display_custom_fields_for_product;

	function __construct( $job_details, &$woocommerce_wpml, &$sitepress, &$wpdb  ) {
		global $iclTranslationManagement;

        $this->woocommerce_wpml =& $woocommerce_wpml;
        $this->sitepress        =& $sitepress;
        $this->tm_instance      =& $iclTranslationManagement;
        $this->wpdb             =& $wpdb;
        $this->not_display_fields_for_variables_product = array( '_purchase_note', '_regular_price', '_sale_price',
                                                                 '_price', '_min_variation_price', '_max_variation_price',
                                                                 '_min_variation_regular_price', '_max_variation_regular_price',
                                                                 '_min_variation_sale_price', '_max_variation_sale_price','_downloadable_files' );

        $this->not_display_custom_fields_for_product = array( '_upsell_ids', '_crosssell_ids', '_children', '_downloadable_files' );

        $this->job_details = $job_details;
        $this->product = wc_get_product( $job_details[ 'job_id' ] );
        $this->original_post = get_post( $job_details[ 'job_id' ] );
        $this->product_id = WooCommerce_Functions_Wrapper::get_product_id( $this->product );
        $this->product_type = WooCommerce_Functions_Wrapper::get_product_type( $this->product_id );

        $source_lang = $this->sitepress->get_language_for_element( $job_details[ 'job_id' ], 'post_product' );
        $target_lang = $job_details[ 'target'];
        $translation_complete = $this->is_translation_complete();
        $duplicate = $this->is_duplicate();

        parent::__construct(
            $job_details[ 'job_id' ],
            'wc_product', __( 'Product', 'woocommerce-multilingual' ),
            $this->original_post->post_title,
            get_post_permalink( WooCommerce_Functions_Wrapper::get_product_id( $this->product ) ),
            $source_lang,
            $target_lang,
            $translation_complete,
            $duplicate
        );

        $this->data = $this->get_data();

        $this->add_elements();
    }

    public function is_translation_complete(){

        $translation_complete = false;

        $product_trid         = $this->sitepress->get_element_trid( $this->product_id, 'post_product' );
        $product_translations = $this->sitepress->get_element_translations( $product_trid, 'post_product', false, false, true );
        if ( isset( $this->job_details[ 'translation_complete' ] ) ) {
            $translation_complete = $this->job_details[ 'translation_complete' ];
        } else if ( isset( $product_translations[ $this->get_target_language() ] ) ) {
            $tr_status = $this->wpdb->get_var(
                            $this->wpdb->prepare("
                                SELECT status FROM {$this->wpdb->prefix}icl_translation_status
                                WHERE translation_id = %d", $product_translations[ $this->get_target_language() ]->translation_id
                            )
            );

	        $translation_complete = $tr_status === ICL_TM_COMPLETE;
        }

        return $translation_complete;
    }

    public function is_duplicate(){

        $is_duplicate_product = false;

        $product_trid         = $this->sitepress->get_element_trid( $this->product_id, 'post_product' );
        $product_translations = $this->sitepress->get_element_translations( $product_trid, 'post_product', false, false, true );
	    if ( isset( $product_translations[ $this->get_target_language() ] ) && get_post_meta( $product_translations[ $this->get_target_language() ]->element_id, '_icl_lang_duplicate_of', true ) === $this->product_id ) {
            $is_duplicate_product = true;
        }

        return $is_duplicate_product;
    }

    public function add_elements( ){

        $this->add_field( new WPML_Editor_UI_Single_Line_Field( 'title', __( 'Title', 'woocommerce-multilingual' ), $this->data, true ) );
        $this->add_field( new WPML_Editor_UI_Single_Line_Field( 'slug', __( 'Slug', 'woocommerce-multilingual' ), $this->data, true ) );

        if( $this->woocommerce_wpml->page_builders->get_page_builders_string_packages( $this->product_id ) ){
            $page_builders_strings_section = $this->woocommerce_wpml->page_builders->get_page_builders_strings_section( $this->data, $this->product_id, $this->get_target_language() );

            if( $page_builders_strings_section ){
                $this->add_field( $page_builders_strings_section );
            }
        }else{
            $this->add_field( new WCML_Editor_UI_WYSIWYG_Field( 'product_content', __( 'Content / Description', 'woocommerce-multilingual' ), $this->data, true ) );
        }

        $excerpt_section = new WPML_Editor_UI_Field_Section( __( 'Excerpt', 'woocommerce-multilingual' ) );
        $excerpt_section->add_field( new WCML_Editor_UI_WYSIWYG_Field( 'product_excerpt', null, $this->data, true ) );
        $this->add_field( $excerpt_section );

        $purchase_note_section = new WPML_Editor_UI_Field_Section( __( 'Purchase note', 'woocommerce-multilingual' ) );
        $purchase_note_section->add_field( new WPML_Editor_UI_TextArea_Field( '_purchase_note', null, $this->data, true ) );
        $this->add_field( $purchase_note_section );

        /*
         * Images
         */
        $product_images = $this->woocommerce_wpml->media->product_images_ids( $this->product_id );

	    if ( count( $product_images ) ) {

            $images_section = new WPML_Editor_UI_Field_Section( __( 'Images', 'woocommerce-multilingual' ) );
            foreach( $product_images as $image_id ) {
                $image = new WPML_Editor_UI_Field_Image( 'image-id-' . $image_id, $image_id, $this->data, true );
                $images_section->add_field( $image );
            }
            $this->add_field( $images_section );
        }

        $attributes = $this->get_custom_product_atributes( );
        if ( $attributes ){
            $attributes_section = new WPML_Editor_UI_Field_Section( __( 'Custom Product attributes', 'woocommerce-multilingual' ) );
            foreach( $attributes as $attr_key => $attribute ) {
                $group = new WPML_Editor_UI_Field_Group( '', true );
                $attribute_field = new WPML_Editor_UI_Single_Line_Field( $attr_key . '_name', __( 'Name', 'woocommerce-multilingual' ), $this->data, false );
                $group->add_field( $attribute_field );
                $attribute_field = new WPML_Editor_UI_TextArea_Field( $attr_key , __( 'Value(s)', 'woocommerce-multilingual' ), $this->data, false );
                $group->add_field( $attribute_field );
                $attributes_section->add_field( $group );
            }
            $this->add_field( $attributes_section );
        }

        $custom_fields = $this->get_product_custom_fields_to_translate( $this->product_id );

        if( $this->product_type === 'external' ){
            $custom_fields = array_diff( $custom_fields, array( '_product_url', '_button_text' ) );
        }

        if( $custom_fields ) {

            $custom_fields_section = new WPML_Editor_UI_Field_Section( __( 'Custom Fields', 'woocommerce-multilingual' ) );

            foreach( $custom_fields as $custom_field ){

                if( $this->check_custom_field_is_single_value( $this->product_id, $custom_field ) ){

                    $cf_settings = new WPML_Post_Custom_Field_Setting( $this->tm_instance, $custom_field );

	                $field_style = (string) apply_filters( 'wpml_tm_editor_string_style', $cf_settings->get_editor_style(), 'field-'.$custom_field.'-0', get_post( $this->product_id ) );

                    switch( $field_style ){
                        case 'visual':
	                    case '2':
                            $cf_field = new WPML_Editor_UI_WYSIWYG_Field( $custom_field, $this->get_product_custom_field_label( $custom_field ), $this->data, true );
                            break;
                        case 'textarea':
	                    case '1':
                            $cf_field = new WPML_Editor_UI_TextArea_Field( $custom_field, $this->get_product_custom_field_label( $custom_field ), $this->data, true );
                            break;
                        default: //line
                            $cf_field = new WPML_Editor_UI_Single_Line_Field( $custom_field, $this->get_product_custom_field_label( $custom_field ), $this->data, true );
                    }

                    $custom_fields_section->add_field( $cf_field );

                }else{

                    $custom_fields_values = array_values( array_filter( maybe_unserialize( get_post_meta($this->product_id, $custom_field , true ) ) ) );

                    if( $custom_fields_values ){
                        $cf_fields_group = new WPML_Editor_UI_Field_Group();

                        foreach( $custom_fields_values as $custom_field_index => $custom_field_val ){
                            $cf_fields_group = $this->add_single_custom_field_content( $cf_fields_group, $custom_field, $custom_field_index, $custom_field_val );
                        }

                        $custom_fields_section->add_field( $cf_fields_group );
                    }

                }

            }

	        $this->add_field( $custom_fields_section );
        }

        if( $this->woocommerce_wpml->products->is_variable_product( $this->product_id ) ){
            $variations = $this->product->get_available_variations();

            if( !empty( $variations ) ){
                $variations_data_section = new WPML_Editor_UI_Field_Section( __( 'Variations data', 'woocommerce-multilingual' ) );
                foreach( $variations as $variation ){
                    $var_custom_fields = $this->get_product_custom_fields_to_translate( $variation[ 'variation_id' ] );
                    if( $var_custom_fields ){
                        $this->add_custom_fields_ui_section( $variations_data_section, $var_custom_fields, $variation[ 'variation_id' ] );
                    }
                }
                $this->add_field( $variations_data_section );
            }
        }

        if( $this->woocommerce_wpml->products->is_downloadable_product( $this->product ) ){
            $is_variable = false;
            if( $this->woocommerce_wpml->products->is_variable_product( $this->product_id ) ){
                $files_data = $this->get_files_for_variations();
                $is_variable = true;
            }else{
                $files_data = array( $this->product_id => $this->woocommerce_wpml->downloadable->get_files_data( $this->product_id ) );
            }

            foreach( $files_data as $post_id => $file_data ){
                $custom_product_sync = get_post_meta( $post_id, 'wcml_sync_files', true );
                if ( ( $custom_product_sync && $custom_product_sync === 'self' ) || ( ! $custom_product_sync && ! $this->woocommerce_wpml->settings['file_path_sync'] ) ) {

                    if( $is_variable ){
                        $files_section = new WPML_Editor_UI_Field_Section( sprintf( __( 'Download Files for Variation #%s', 'woocommerce-multilingual' ), $post_id ) );
                    }else{
                        $files_section = new WPML_Editor_UI_Field_Section( __( 'Download Files', 'woocommerce-multilingual' ) );
                    }

                    foreach( $file_data as $key => $file ) {
                        $sub_group = new WPML_Editor_UI_Field_Group();
                        $field_input = new WPML_Editor_UI_Single_Line_Field( 'file-name'.$key.$post_id, __( 'Name', 'woocommerce-multilingual' ), $this->data, false );
                        $sub_group->add_field( $field_input );
                        $field_input = new WPML_Editor_UI_Single_Line_Field( 'file-url'.$key.$post_id, __( 'File URL', 'woocommerce-multilingual' ), $this->data, false );
                        $sub_group->add_field( $field_input );

                        $files_section->add_field( $sub_group );
                    }

                    if( $is_variable ){
                        $this->add_field( $files_section );
                    }

                }
            }

            if( isset( $files_section ) && !$is_variable ){
                $this->add_field( $files_section );
            }
        }

        $this->add_taxonomies_ui_section();

        if( $this->product_type === 'external' ){
            $external_product_section = new WPML_Editor_UI_Field_Section( __( 'External Product', 'woocommerce-multilingual' ) );
            $external_product_section->add_field( new WPML_Editor_UI_Single_Line_Field( '_product_url', __( 'Product url', 'woocommerce-multilingual' ), $this->data, true ) );
            $external_product_section->add_field( new WPML_Editor_UI_Single_Line_Field( '_button_text', __( 'Button text', 'woocommerce-multilingual' ), $this->data, true ) );
            $this->add_field( $external_product_section );
        }

        do_action( 'wcml_gui_additional_box_html', $this, $this->product_id, $this->data );

    }

    public function add_taxonomies_ui_section(){

        $taxonomies = get_object_taxonomies( 'product', 'objects' );
        foreach( $taxonomies as $taxonomy => $taxonomy_obj ){
            if( $this->woocommerce_wpml->terms->is_translatable_wc_taxonomy( $taxonomy ) && is_taxonomy_translated( $taxonomy ) ){
                $product_terms = wp_get_post_terms( $this->product_id, $taxonomy );
                if( $product_terms ){
                    $tax_section = new WPML_Editor_UI_Field_Section( $taxonomy_obj->label, __( 'Changes in these translations will affect terms in general! ( Not only for this product )', 'woocommerce-multilingual' ));
                    foreach( $product_terms as $term ){
                        if(
                            $this->sitepress->get_setting( 'tm_block_retranslating_terms' ) &&
                            !is_null( apply_filters( 'translate_object_id', $term->term_id, $taxonomy, false, $this->get_target_language() ) )
                        ){
                            continue;
                        }
                        $term_field = new WPML_Editor_UI_Single_Line_Field( 't_'. $term->term_taxonomy_id, '', $this->data, false );
                        $tax_section->add_field( $term_field );
                    }
                    if( isset( $term_field ) ){
                        $this->add_field( $tax_section );
                    }
                }
            }
        }

    }

    public function add_single_custom_field_content( $cf_fields_group, $custom_field, $custom_field_index, $custom_field_val ){

        if( is_scalar( $custom_field_val ) ){
            $key_index = $custom_field . '-' . $custom_field_index;
            $cf        = 'field-' . $key_index;

            $cf_field = new WPML_Editor_UI_Single_Line_Field( $cf, $cf, $this->data, false );
            $cf_fields_group->add_field( $cf_field );

        }else{
            foreach ( $custom_field_val as $ind => $value ) {
                $cf_fields_group = $this->add_single_custom_field_content( $cf_fields_group, $custom_field, $custom_field_index . '-' . str_replace( '-', ':::', $ind ), $value );
            }
        }

        return $cf_fields_group;

    }

    public function add_single_custom_field_content_value( $element_data, $custom_field, $custom_field_index, $custom_field_val, $trnsl_custom_field_value ){

        if( is_scalar( $custom_field_val ) ){
            $key_index = $custom_field . '-' . $custom_field_index;
            $cf        = 'field-' . $key_index;
            $element_data[ $cf ] = array( 'original' => $custom_field_val );
            $element_data[ $cf ][ 'translation' ] = ( $trnsl_custom_field_value ) ? $trnsl_custom_field_value : '';

        }else{
            foreach ( $custom_field_val as $ind => $value ) {
                $translated_value = isset( $trnsl_custom_field_value[ $ind ] ) ? $trnsl_custom_field_value[ $ind ] : '';
                $element_data = $this->add_single_custom_field_content_value( $element_data, $custom_field, $custom_field_index . '-' . str_replace( '-', ':::', $ind ), $value, $translated_value );
            }
        }

        return $element_data;

    }

    public function add_custom_fields_ui_section( $custom_fields_section, $custom_fields, $variation_id = false ){

        foreach( $custom_fields as $custom_field ) {

            $custom_field_id = $variation_id ? $custom_field.$variation_id : $custom_field;

            if( key( $this->data[ $custom_field_id ] ) != 'original' ){
                $group = new WPML_Editor_UI_Field_Group(  $this->get_product_custom_field_label( $custom_field, $variation_id ), true );
                foreach( $this->data[ $custom_field_id ] as $custom_field_key => $custom_field_array ){
                    if( $custom_field == '_variation_description' ){
                        $custom_field_input = new WPML_Editor_UI_TextArea_Field( $custom_field_key, '', $this->data[ $custom_field_id ], false );
                    }else{
                        $custom_field_input = new WPML_Editor_UI_Single_Line_Field( $custom_field_key, '', $this->data[ $custom_field_id ], false );
                    }

                    $group->add_field( $custom_field_input );
                }
                $custom_fields_section->add_field( $group );
            }else{
                if( $custom_field == '_variation_description' ){
                    $custom_field_input = new WPML_Editor_UI_TextArea_Field( $custom_field_id, $this->get_product_custom_field_label( $custom_field, $variation_id ), $this->data, true );
                }else{
                    $custom_field_input = new WPML_Editor_UI_Single_Line_Field( $custom_field_id, $this->get_product_custom_field_label( $custom_field, $variation_id ), $this->data, true );
                }
                $custom_fields_section->add_field( $custom_field_input );
            }
        }

    }

    /**
     * @return array
     */
    function get_data() {

        $trn_product_id = apply_filters( 'translate_object_id', $this->product_id, 'product', false, $this->get_target_language() );
        $translation = false;
        $is_variable_product = $this->woocommerce_wpml->products->is_variable_product( $this->product_id );
	    if ( null !== $trn_product_id ) {
            $translation = get_post( $trn_product_id );
        }

		$element_data = array( 'title'    => array( 'original' => $this->original_post->post_title ),
					   'slug'     => array( 'original' => urldecode( $this->original_post->post_name ) ),
                       'product_excerpt'  => array( 'original' => $this->original_post->post_excerpt ),
                        '_purchase_note' => array( 'original' => get_post_meta( $this->product_id, '_purchase_note', true ) )
                     );

        if ( $translation ) {
            $element_data[ 'title' ][ 'translation' ]   = $translation->post_title;
            $element_data[ 'slug' ][ 'translation' ]    = urldecode( $translation->post_name );
            $element_data[ 'product_excerpt' ][ 'translation' ] = $translation->post_excerpt;
            $element_data[ '_purchase_note' ][ 'translation' ] = get_post_meta( $translation->ID, '_purchase_note', true );
        }

        if( $this->woocommerce_wpml->page_builders->get_page_builders_string_packages( $this->product_id ) ){
            $element_data = $this->woocommerce_wpml->page_builders->page_builders_data( $element_data, $this->product_id, $this->get_target_language() );
        }else{
            $element_data[ 'product_content' ] = array(
                'original' => $this->original_post->post_content
            );
            if ( $translation ) {
                $element_data[ 'product_content' ][ 'translation' ] = $translation->post_content;
            }
        }

        $product_images = $this->woocommerce_wpml->media->product_images_ids( $this->product_id );
        foreach( $product_images as $image_id ) {
            $attachment_data = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT post_title,post_excerpt,post_content FROM {$this->wpdb->posts} WHERE ID = %d", $image_id ) );
            if( !$attachment_data ) continue;
			$alt_text                                                 = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			$alt_text                                                 = $alt_text ? $alt_text : '';
            $element_data[ 'image-id-' . $image_id . '-title' ]       = array( 'original' => $attachment_data->post_title );
            $element_data[ 'image-id-' . $image_id . '-caption' ]     = array( 'original' => $attachment_data->post_excerpt );
            $element_data[ 'image-id-' . $image_id . '-description' ] = array( 'original' => $attachment_data->post_content );
            $element_data[ 'image-id-' . $image_id . '-alt-text' ]    = array( 'original' => $alt_text );
            
            $trnsl_prod_image = apply_filters( 'translate_object_id', $image_id, 'attachment', false, $this->get_target_language() );
	        if ( null !== $trnsl_prod_image ) {
                $trnsl_attachment_data = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT post_title,post_excerpt,post_content FROM {$this->wpdb->posts} WHERE ID = %d", $trnsl_prod_image ) );
				$alt_text                                                                  = get_post_meta( $trnsl_prod_image, '_wp_attachment_image_alt', true );
				$alt_text                                                                  = $alt_text ? $alt_text : '';
                $element_data[ 'image-id-' . $image_id . '-title' ][ 'translation' ]       = $trnsl_attachment_data->post_title;
                $element_data[ 'image-id-' . $image_id . '-caption' ][ 'translation' ]     = $trnsl_attachment_data->post_excerpt;
                $element_data[ 'image-id-' . $image_id . '-description' ][ 'translation' ] = $trnsl_attachment_data->post_content;
                $element_data[ 'image-id-' . $image_id . '-alt-text' ][ 'translation' ]    = $alt_text;
            }
        }

        $attributes = $this->get_custom_product_atributes( );
        if( $attributes ){
            foreach( $attributes as $attr_key => $attribute ){
                $element_data[ $attr_key.'_name' ]       = array( 'original' => $attribute['name'] );
                $element_data[ $attr_key ]       = array( 'original' => $attribute['value'] );

                $trn_attribute = $this->woocommerce_wpml->attributes->get_custom_attribute_translation( $this->product_id, $attr_key, $attribute, $this->get_target_language() );

                $element_data[ $attr_key.'_name' ][ 'translation' ]       = $trn_attribute['name'] ? $trn_attribute['name'] : '';
                $element_data[ $attr_key ][ 'translation' ]       = $trn_attribute['value'] ? $trn_attribute['value'] : '';
            }
        }

        $element_data = $this->add_taxonomies_to_element_data( $element_data );

        $element_data = $this->add_custom_field_to_element_data( $element_data, $this->product_id, isset( $translation->ID ) ? $translation->ID : false, false );

        if( $is_variable_product ){
            $variations = $this->product->get_available_variations();

            if( !empty( $variations ) ){
                foreach( $variations as $variation ){
                    $element_data[ '_variation_description'.$variation[ 'variation_id' ] ] = array( 'original' => strip_tags( $variation[ 'variation_description' ] ) );
                    $translated_variation_id = apply_filters( 'translate_object_id', $variation[ 'variation_id' ], 'product_variation', false, $this->get_target_language() );
                    $element_data[ '_variation_description'.$variation[ 'variation_id' ] ][ 'translation' ]  =   $translated_variation_id ? get_post_meta( $translated_variation_id, '_variation_description', true ) : '';

                    $element_data = $this->add_custom_field_to_element_data( $element_data, $variation[ 'variation_id' ], $translated_variation_id, true );
                }
            }
        }


	    $files_data = array( $this->product_id => $this->woocommerce_wpml->downloadable->get_files_data( $this->product_id ) );
        if( $is_variable_product ){
            $files_data = $this->get_files_for_variations();
        }

        foreach( $files_data as $post_id => $file_data ) {

            $custom_product_sync = get_post_meta( $post_id, 'wcml_sync_files', true);
	        if ( ( $custom_product_sync && $custom_product_sync === 'self' ) || ( ! $custom_product_sync && ! $this->woocommerce_wpml->settings['file_path_sync'] ) ) {
                $orig_product_files = $file_data;
                $trnsl_product_files = array();
                if( $is_variable_product ){
                    $trnsl_variation_id = apply_filters( 'translate_object_id', $post_id, 'product_variation', false, $this->get_target_language() );
                    if( $trnsl_variation_id ){
                        $trnsl_product_files = $this->woocommerce_wpml->downloadable->get_files_data( $trnsl_variation_id );
                    }
                }elseif ( isset( $translation->ID ) && $translation->ID ) {
                    $trnsl_product_files = $this->woocommerce_wpml->downloadable->get_files_data( $translation->ID );
                }

                foreach ($orig_product_files as $key => $product_file) {

                    $element_data['file-name' . $key.$post_id] = array('original' => $product_file['label']);
                    $element_data['file-url' . $key.$post_id] = array('original' => $product_file['value']);


                    $element_data['file-name' . $key.$post_id]['translation'] = isset( $trnsl_product_files[$key]  ) ? $trnsl_product_files[$key]['label'] : '';
                    $element_data['file-url' . $key.$post_id]['translation'] = isset( $trnsl_product_files[$key] ) ? $trnsl_product_files[$key]['value'] : '';
                }
            }
        }

        $element_data = apply_filters( 'wcml_gui_additional_box_data', $element_data, $this->product_id, $translation, $this->get_target_language() );

        return $element_data;
    }

    public function add_taxonomies_to_element_data( $element_data ){

        $taxonomies = get_object_taxonomies( 'product', 'objects' );

        foreach( $taxonomies as $taxonomy => $taxonomy_obj ){
            if( $this->woocommerce_wpml->terms->is_translatable_wc_taxonomy( $taxonomy ) && is_taxonomy_translated( $taxonomy ) ){
                $product_terms = wp_get_post_terms( $this->product_id, $taxonomy );
                if( !is_wp_error( $product_terms ) ){
                    foreach( $product_terms as $term ){
                        $translated_term = $this->woocommerce_wpml->terms->wcml_get_translated_term( $term->term_id, $taxonomy, $this->get_target_language() );
                        if(
                            $this->sitepress->get_setting( 'tm_block_retranslating_terms' ) &&
                            $translated_term->term_taxonomy_id != $term->term_taxonomy_id
                        ){
                            continue;
                        }

                        $element_data[ 't_'.$term->term_taxonomy_id ] = array( 'original' => htmlspecialchars_decode( $term->name ) );
                        $element_data[ 't_'.$term->term_taxonomy_id ][ 'translation' ] = $translated_term->term_taxonomy_id != $term->term_taxonomy_id ? $translated_term->name : '';
                    }
                }
            }
        }

        return $element_data;
    }

    public function add_custom_field_to_element_data( $element_data, $element_id, $translation_id, $is_variation = false ){
        $custom_fields = $this->get_product_custom_fields_to_translate( $element_id );
        if( $custom_fields ){
            foreach( $custom_fields as $custom_field ) {
                $orig_custom_field_values = get_post_meta( $element_id, $custom_field );
                $translated_custom_field_values = array();
                $trnsl_mid_ids = array();

                if ( $translation_id ) {
                    $translated_custom_field_values = get_post_meta( $translation_id, $custom_field );
                    $trnsl_mid_ids = $this->woocommerce_wpml->products->get_mid_ids_by_key( $translation_id, $custom_field );
                }

                if( $is_variation ){
                    $custom_field .= $element_id;
                }

                foreach( $orig_custom_field_values as $val_key => $orig_custom_field_value ){

                    if( $this->check_custom_field_is_single_value( $element_id, $custom_field ) ){

                        if( count( $orig_custom_field_values ) == 1 ){
                            $element_data[ $custom_field ] = array( 'original' => $orig_custom_field_value );
                            $element_data[ $custom_field ][ 'translation' ] = ( $translation_id && isset( $translated_custom_field_values[ $val_key ] ) ) ? $translated_custom_field_values[ $val_key ] : '';
                        }else{

                            $custom_field_key = $custom_field.':'. ( isset( $trnsl_mid_ids[ $val_key ] ) ? $trnsl_mid_ids[ $val_key ] : 'new_'. $val_key );

                            $element_data[ $custom_field ][ $custom_field_key ] = array( 'original' => $orig_custom_field_value );
                            $element_data[ $custom_field ][ $custom_field_key ][ 'translation' ] = ($translation_id && isset( $translated_custom_field_values[ $val_key ] ) ) ? $translated_custom_field_values[ $val_key ] : '';
                        }

                    }else{

                        $custom_fields_values = array_values( array_filter( maybe_unserialize( get_post_meta($this->product_id, $custom_field, true ) ) ) );

                        if( $custom_fields_values ){
                            if ( $translation_id ) {
                                $translated_custom_field_values = array_values( array_filter( maybe_unserialize( get_post_meta( $translation_id, $custom_field , true ) ) ) );
                            }
                            foreach( $custom_fields_values as $custom_field_index => $custom_field_val ){

                                $translated_custom_field_value = isset( $translated_custom_field_values[ $custom_field_index ] )? $translated_custom_field_values[ $custom_field_index ] : '';

                                    $element_data = $this->add_single_custom_field_content_value( $element_data, $custom_field, $custom_field_index, $custom_field_val, $translated_custom_field_value );

                            }
                        }
                    }
                }
            }
        }

        return $element_data;
    }

    public function save_translations( $translations ) {
	    /** @var TranslationManagement $iclTranslationManagement */
	    global $iclTranslationManagement;

	    $return = array();

	    if ( $this->original_post->post_type !== 'product' ) {
		    return $return;
        }

        $languages = $this->sitepress->get_active_languages();

        $product_trid                 = $this->sitepress->get_element_trid( $this->product_id, 'post_' . $this->original_post->post_type );
        $tr_product_id                = apply_filters( 'translate_object_id', $this->product_id, 'product', false, $this->get_target_language() );

	    new WCML_Editor_Save_Filters( $product_trid, $this->get_target_language() );
        if ( get_magic_quotes_gpc() ) {
            foreach ( $translations as $key => $data_item ) {
                if ( !is_array( $data_item ) ) {
                    $translations[$key] = stripslashes( $data_item );
                }
            }
        }

	    if ( null === $tr_product_id ) {

            //insert new post
            $args = array();
            $args[ 'post_title' ] = $translations[ md5( 'title' ) ];
		    $args[ 'post_name' ] = $translations[ md5( 'slug' ) ];
            $args[ 'post_type' ] = $this->original_post->post_type;
            $args[ 'post_content' ] = isset( $translations[ md5( 'product_content' ) ] ) ? $translations[ md5( 'product_content' ) ] : '';
            $args[ 'post_excerpt' ] = $translations[ md5( 'product_excerpt' ) ];

            if ( !$args[ 'post_title' ] && !$args[ 'post_content' ] && !$args[ 'post_excerpt' ] ){
                return new WP_Error( 'empty_content', __( 'Content, title, and excerpt are empty.' ) );
            }

            $args[ 'post_status' ]    = $this->original_post->post_status;
            $args[ 'menu_order ']     = $this->original_post->menu_order;
            $args[ 'ping_status' ]    = $this->original_post->ping_status;
            $args[ 'comment_status' ] = $this->original_post->comment_status;
            $product_parent           = apply_filters( 'translate_object_id', $this->original_post->post_parent, 'product', false, $this->get_target_language() );
		    $args['post_parent']      = null === $product_parent ? 0 : $product_parent;

            //TODO: remove after change required WPML version > 3.3
            $_POST[ 'to_lang' ] = $this->get_target_language();
            // for WPML > 3.3
            $_POST[ 'icl_post_language' ] = $this->get_target_language();

            if ( $this->woocommerce_wpml->settings[ 'products_sync_date' ] ) {
                $args[ 'post_date' ] = $this->original_post->post_date;
            }

            $this->sitepress->switch_lang( $this->get_target_language() );

		    $tr_product_id = wp_insert_post( $args );

		    $this->sitepress->switch_lang(); // switch back

            $translation_id = $this->wpdb->get_var( $this->wpdb->prepare(
            	"SELECT translation_id
                 FROM {$this->wpdb->prefix}icl_translations
                 WHERE element_type=%s AND trid=%d AND language_code=%s AND element_id IS NULL ",
            'post_product', $product_trid, $this->get_target_language() ) );

            if ( $translation_id ) {
                $this->wpdb->query(
                    $this->wpdb->prepare(
                        "DELETE FROM {$this->wpdb->prefix}icl_translations WHERE element_id=%d AND trid=%d",
                        $tr_product_id, $product_trid
                    )
                );

                $this->wpdb->update( $this->wpdb->prefix . 'icl_translations', array( 'element_id' => $tr_product_id ), array( 'translation_id' => $translation_id ) );
            } else {
                $this->sitepress->set_element_language_details( $tr_product_id, 'post_' . $this->original_post->post_type, $product_trid, $this->get_target_language() );
            }

        } else {
            //update post
            $args = array();
            $args[ 'ID' ] = $tr_product_id;
            $args[ 'post_title' ] = $translations[ md5( 'title' ) ];
            $args[ 'post_content' ]   = isset( $translations[ md5( 'product_content' ) ] ) ? $translations[ md5( 'product_content' ) ] : '';
            $args[ 'post_excerpt' ]   = $translations[ md5( 'product_excerpt' ) ];
            $args[ 'post_status' ]    = $this->original_post->post_status;
            $args[ 'ping_status' ]    = $this->original_post->ping_status;
            $args[ 'comment_status' ] = $this->original_post->comment_status;
            $product_parent           = apply_filters( 'translate_object_id', $this->original_post->post_parent, 'product', false, $this->get_target_language() );
		    $args['post_parent']      = null === $product_parent ? 0 : $product_parent;
            $_POST[ 'to_lang' ]       = $this->get_target_language();

            $this->sitepress->switch_lang( $this->get_target_language() );
            wp_update_post( $args );
            $this->sitepress->switch_lang();

            $post_name = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT post_name FROM {$this->wpdb->posts} WHERE ID=%d", $tr_product_id ) );
		    if ( isset( $translations[ md5( 'slug' ) ] ) && $translations[ md5( 'slug' ) ] !== $post_name ) {
                // update post_name
                // need set POST variable ( WPML used them when filtered this function)

                $new_post_name = sanitize_title( $translations[ md5( 'slug' ) ] ? $translations[ md5( 'slug' ) ] : $translations[ md5( 'title' ) ]);
                $_POST[ 'new_title' ] = $translations[ md5( 'title' ) ];
                $_POST[ 'new_slug' ] = $new_post_name;
                $new_slug = wp_unique_post_slug( $new_post_name, $tr_product_id, $this->original_post->post_status, $this->original_post->post_type, $args[ 'post_parent' ] );
                
			    wp_update_post( array(
				    'ID'        => $tr_product_id,
				    'post_name' => $new_slug
			    ) );
            }

            $this->sitepress->set_element_language_details( $tr_product_id, 'post_' . $this->original_post->post_type, $product_trid, $this->get_target_language() );

        }

        $product_translations = $this->sitepress->get_element_translations( $product_trid , 'post_product', false, false, true );

        do_action( 'wcml_before_sync_product_data', $this->product_id, $tr_product_id, $this->get_target_language() );

        $this->woocommerce_wpml->sync_product_data->duplicate_product_post_meta( $this->product_id, $tr_product_id, $translations );

        $this->woocommerce_wpml->page_builders->save_page_builders_strings( $translations, $this->product_id, $this->get_target_language() );

        $this->save_translated_terms( );
        //sync taxonomies
        $this->woocommerce_wpml->sync_product_data->sync_product_taxonomies( $this->product_id, $tr_product_id, $this->get_target_language() );

        do_action( 'wcml_update_extra_fields', $this->product_id, $tr_product_id, $translations, $this->get_target_language() );

        $this->woocommerce_wpml->attributes->sync_product_attr( $this->product_id, $tr_product_id, $this->get_target_language(), $translations );

        $this->woocommerce_wpml->attributes->sync_default_product_attr( $this->product_id, $tr_product_id, $this->get_target_language() );

        // synchronize post variations
        $this->woocommerce_wpml->sync_variations_data->sync_product_variations( $this->product_id, $tr_product_id, $this->get_target_language(), $translations, true );

	    $this->woocommerce_wpml->sync_product_data->sync_linked_products( $this->product_id, $tr_product_id, $this->get_target_language() );

	    //sync feature image
	    $this->woocommerce_wpml->media->sync_thumbnail_id( $this->product_id, $tr_product_id, $this->get_target_language() );
	    //sync product gallery
	    $this->woocommerce_wpml->media->sync_product_gallery( $this->product_id );

	    //save images texts
	    $product_images = $this->woocommerce_wpml->media->product_images_ids( $this->product_id );

        if ( $product_images ) {
            foreach ( $product_images as $image_id ) {
                $trnsl_prod_image = apply_filters( 'translate_object_id', $image_id, 'attachment', false, $this->get_target_language() );

                if ( ! $trnsl_prod_image ) {
	                $trnsl_prod_image = $this->woocommerce_wpml->media->create_base_media_translation( $image_id, $this->product_id, $this->get_target_language() );
                }

	            //update image texts
	            $this->wpdb->update(
	            	$this->wpdb->posts,
		            array(
			            'post_title'   => $translations[ md5( 'image-id-' . $image_id . '-title' ) ],
			            'post_content' => $translations[ md5( 'image-id-' . $image_id . '-description' ) ],
			            'post_excerpt' => $translations[ md5( 'image-id-' . $image_id . '-caption' ) ]
		            ),
		            array( 'id' => $trnsl_prod_image )
	            );

				if ( isset( $translations[ md5( 'image-id-' . $image_id . '-alt-text' ) ] ) ) {
					update_post_meta( $trnsl_prod_image, '_wp_attachment_image_alt', $translations[ md5( 'image-id-' . $image_id . '-alt-text' ) ] );
				}
			}
        }

        if( $product_translations ){
            $iclTranslationManagement->update_translation_status(
                array(
                    'status' => $this->is_translation_complete() ? ICL_TM_COMPLETE : ICL_TM_IN_PROGRESS,
                    'needs_update' => 0,
                    'translation_id' => $product_translations[ $this->get_target_language() ]->translation_id,
                    'translator_id' => get_current_user_id()
                ));
        }
		
        if ( ob_get_length() > 0 ) {
            ob_clean();
        }
        ob_start();

	    $this->woocommerce_wpml->products->get_translation_statuses( $this->product_id, $product_translations, $languages, isset( $translations['slang'] )
	                                                                                                                        && $translations['slang']
	                                                                                                                           !== 'all' ? $translations['slang'] : false, $product_trid, $this->get_target_language() );
        $return[ 'status_link' ] = ob_get_clean();

        // no longer a duplicate
        delete_post_meta( $tr_product_id, '_icl_lang_duplicate_of', $this->product_id );

        return $return;

    }

    private function save_translated_terms() {
        $job_data    = array();
        parse_str( $_POST['data'], $job_data );

        foreach( $job_data['fields'] as $field_key => $field ){
           if( substr( $field_key, 0, 2 ) === 't_' ){
               $update = array();
               if ( isset( $field[ 'data' ] ) ) {
                   $update[ 'field_data_translated' ] = base64_encode( $field[ 'data' ] );

                   $update[ 'field_finished' ] = 1;

                   $this->wpdb->update(
                       $this->wpdb->prefix . 'icl_translate',
                       $update,
                       array( 'field_type' => $field_key )
                   );
               }
           }
        }

        $job = new WPML_Post_Translation_Job( $job_data[ 'job_id' ] );
        $job->save_terms_to_post();
    }

    public function get_custom_product_atributes( )
    {
        $attributes = get_post_meta( $this->product_id, '_product_attributes', true);
        if (!is_array($attributes)) {
            $attributes = array();
        }

        foreach ($attributes as $key => $attribute) {
            if ($attribute['is_taxonomy']) {
                unset($attributes[$key]);
            }
        }

        return $attributes;
    }

    //get product content labels
    public function get_product_custom_field_label( $field, $variation_id = false )
    {
	    global $sitepress;
        $settings = $sitepress->get_settings();
        $label = '';
	    if ( isset( $settings['translation-management']['custom_fields_translation'][ $field ] ) && $settings['translation-management']['custom_fields_translation'][ $field ] == WPML_TRANSLATE_CUSTOM_FIELD  ) {
		    if ( in_array( $field, apply_filters( 'wcml_not_display_single_fields_to_translate', $this->not_display_fields_for_variables_product ), true ) ) {
                return false;
            }

            if ( in_array( $field, apply_filters( 'wcml_do_not_display_custom_fields_for_product', $this->not_display_custom_fields_for_product ), true ) ) {
                return false;
            }

            $custom_key_label = apply_filters('wcml_product_content_label', $field, $this->product_id);
		    if ( $custom_key_label !== $field ) {
                $label = $custom_key_label;
                return $label;
            }

            $custom_key_label = str_replace('_', ' ', $field);
            $label = trim($custom_key_label[0]) ? ucfirst($custom_key_label) : ucfirst(substr($custom_key_label, 1));

            if( $variation_id ){
                $label .= ' #'.$variation_id;
            }

        }

        return $label;
    }

    public function get_files_for_variations( ){

        global $wpdb;

        $variations = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation'", $this->product_id));
        $variations_files = array();

        foreach ($variations as $variation) {

	        if ( get_post_meta( $variation->ID, '_downloadable', true ) === 'yes' ) {

                $variation_files = $this->woocommerce_wpml->downloadable->get_files_data( $variation->ID );

		        if ( count( $variation_files ) ) {
                    $variations_files[ $variation->ID ] = $variation_files;
                }

            }

        }

        return $variations_files;

    }

    //get product content
    public function get_product_custom_fields_to_translate( $product_id ){
        $settings = $this->sitepress->get_settings();
        $contents = array();

        foreach( get_post_custom_keys( $product_id ) as $meta_key ) {
	        if ( isset( $settings['translation-management']['custom_fields_translation'][ $meta_key ] ) && $settings['translation-management']['custom_fields_translation'][ $meta_key ] == WPML_TRANSLATE_CUSTOM_FIELD ) {
                if( $this->check_custom_field_is_single_value( $product_id, $meta_key ) ){
	                if ( in_array( $meta_key, apply_filters( 'wcml_not_display_single_fields_to_translate', $this->not_display_fields_for_variables_product ), true ) ) {
                        continue;
                    }
                }elseif( in_array( $meta_key, apply_filters( 'wcml_do_not_display_custom_fields_for_product', $this->not_display_custom_fields_for_product ), true ) ) {
                    continue;
                }
                $contents[] = $meta_key;
            }
        }
        return apply_filters('wcml_product_content_fields', $contents, $product_id);
    }

    public function check_custom_field_is_single_value( $product_id, $meta_key ){
        $meta_value = maybe_unserialize( get_post_meta( $product_id, $meta_key, true ) );

        if( is_array( $meta_value ) ){
            return false;
        } else {
            return apply_filters( 'wcml_check_is_single', true, $product_id, $meta_key );
        }
    }

	public function requires_translation_complete_for_each_field() {
		return false;
	}
	
	public function is_hide_empty_fields() {
		return false;
	}
	
	public function show_media_button() {
		return true;
	}
	
}