<?php

class WCML_TP_Support {

    /** @var woocommerce_wpml */
    private $woocommerce_wpml;
    /** @var  wpdb */
    private $wpdb;
    /** @var WPML_Element_Translation_Package */
    private $tp;

    /**
     * WCML_Attributes constructor.
     *
     * @param woocommerce_wpml $woocommerce_wpml
     * @param wpdb $wpdb
     * @param WPML_Element_Translation_Package $tp
     */
    public function __construct( woocommerce_wpml $woocommerce_wpml, wpdb $wpdb, WPML_Element_Translation_Package $tp ){

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->wpdb             = $wpdb;
        $this->tp               = $tp;
    }

    public function add_hooks(){
        add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_custom_attributes_to_translation_package' ), 10, 2 );
        add_action( 'wpml_translation_job_saved',   array( $this, 'save_custom_attribute_translations' ), 10, 2 );

        add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_variation_descriptions_translation_package' ), 10, 2 );
        add_action( 'wpml_pro_translation_completed', array( $this, 'save_variation_descriptions_translations' ), 20, 3 ); //after WCML_Products

        add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_slug_to_translation_package' ), 10, 2 );
        add_action( 'wpml_translation_job_saved',   array( $this, 'save_slug_translations' ), 10, 2 );

        add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_images_to_translation_package' ), 10, 2 );
        add_action( 'wpml_translation_job_saved',   array( $this, 'save_images_translations' ), 10, 3 );
    }
    
    public function append_custom_attributes_to_translation_package( $package, $post ) {

        if ( $post->post_type === 'product' ) {

            $product = wc_get_product( $post->ID );
            $product_type = WooCommerce_Functions_Wrapper::get_product_type( $post->ID );

            if ( ! empty( $product ) && $product_type === 'variable' ) {

                $attributes = $product->get_attributes();

                foreach ( $attributes as $attribute_key => $attribute ) {

                    if( $this->woocommerce_wpml->attributes->is_a_taxonomy( $attribute ) ){
                        continue;
                    }

                    $package[ 'contents' ][ 'wc_attribute_name:' . $attribute_key ] = array(
                        'translate' => 1,
                        'data'      => $this->tp->encode_field_data( $attribute[ 'name' ], 'base64' ),
                        'format'    => 'base64'
                    );
                    $values = explode( '|', $attribute[ 'value' ] );
                    $values = array_map( 'trim', $values );

                    foreach ( $values as $value_key => $value ) {
                        $package[ 'contents' ][ 'wc_attribute_value:' . $value_key . ':' . $attribute_key ] = array(
                            'translate' => 1,
                            'data'      => $this->tp->encode_field_data( $value, 'base64' ),
                            'format'    => 'base64'
                        );
                    }
                }
            }
        }

        return $package;
    }

    public function save_custom_attribute_translations( $post_id, $data ) {

        $translated_attributes = array();

        foreach ( $data as $data_key => $value ) {

            if ( $value['finished'] && isset( $value['field_type'] ) && strpos( $value['field_type'], 'wc_attribute_' ) === 0 ) {

                if ( strpos( $value['field_type'], 'wc_attribute_name:' ) === 0 ) {

                    $exp           = explode( ':', $value['field_type'], 2 );
                    $attribute_key = $exp[1];

                    $translated_attributes[ $attribute_key ]['name'] = $value['data'];

                } else if ( strpos( $value['field_type'], 'wc_attribute_value:' ) === 0 ) {

                    $exp           = explode( ':', $value['field_type'], 3 );
                    $value_key     = $exp[1];
                    $attribute_key = $exp[2];

                    $translated_attributes[ $attribute_key ]['values'][ $value_key ] = $value['data'];

                }

            }

        }

        if ( $translated_attributes ) {

            $product_attributes = get_post_meta( $post_id, '_product_attributes', true );

            $original_post_language = $this->woocommerce_wpml->products->get_original_product_language( $post_id );
            $original_post_id       = apply_filters( 'translate_object_id', $post_id, 'product', false, $original_post_language );

            $original_attributes = get_post_meta( $original_post_id, '_product_attributes', true );

            foreach ( $translated_attributes as $attribute_key => $attribute ) {

                $product_attributes[ $attribute_key ] = array(
                    'name'        => $attribute['name'],
                    'value'       => join( ' | ', $attribute['values'] ),
                    'is_taxonomy' => 0,
                    'is_visible'  => $original_attributes[ $attribute_key ]['is_visible'],
                    'position'    => $original_attributes[ $attribute_key ]['position']
                );


            }

            update_post_meta( $post_id, '_product_attributes', $product_attributes );

        }

    }

    public function append_variation_descriptions_translation_package( $package, $post ) {

        if ( $post->post_type == 'product' ) {

            /** @var WC_Product_Variable $product */
            $product = wc_get_product( $post->ID );

            $product_type = WooCommerce_Functions_Wrapper::get_product_type( $post->ID );

            if ( ! empty( $product ) && $product_type === 'variable' ) {

                $variations = $product->get_available_variations();

                foreach ( $variations as $variation ) {

                    if ( ! empty( $variation['variation_description'] ) ) {

                        $package['contents'][ 'wc_variation_description:' . $variation['variation_id'] ] = array(
                            'translate' => 1,
                            'data'      => $this->tp->encode_field_data( $variation['variation_description'], 'base64' ),
                            'format'    => 'base64'
                        );

                    }

                }

            }

        }

        return $package;

    }

    public function save_variation_descriptions_translations( $post_id, $data, $job ) {

        $language = $job->language_code;

        foreach ( $data as $data_key => $value ) {

            if ( $value['finished'] && isset( $value['field_type'] ) && strpos( $value['field_type'], 'wc_variation_description:' ) === 0 ) {

                $variation_id = substr( $value['field_type'], strpos( $value['field_type'], ':' ) + 1 );

                if ( is_post_type_translated( 'product_variation' ) ) {

                    $translated_variation_id = apply_filters( 'translate_object_id', $variation_id, 'product_variation', false, $language );

                } else {
                    global $sitepress;
                    $trid         = $sitepress->get_element_trid( $variation_id, 'post_product_variation' );
                    $translations = $sitepress->get_element_translations( $trid, 'post_product_variation', true, true, true );

                    $translated_variation_id = isset( $translations[ $language ] ) ? $translations[ $language ]->element_id : false;

                }

                if ( $translated_variation_id ) {
                    update_post_meta( $translated_variation_id, '_variation_description', $value['data'] );
                }


            }

        }

    }

    public function append_slug_to_translation_package( $package, $post ) {
        if ( $post->post_type == 'product' ) {

            $this->add_to_package( $package, 'slug', urldecode( $post->post_name ) );
        }

        return $package;
    }

    public function save_slug_translations( $post_id, $data ) {

        foreach ( $data as $data_key => $value ) {
            if ( $value['finished'] && isset( $value['field_type'] ) && 'slug' === $value['field_type'] ) {
                $product = get_post( $post_id );
                if ( $product->post_type == 'product' ) {
                    $new_slug = wp_unique_post_slug( sanitize_title( $value['data'] ), $post_id, $product->post_status, $product->post_type,  $product->post_parent );
                    $this->wpdb->update( $this->wpdb->posts, array( 'post_name' => $new_slug ), array( 'ID' => $post_id ) );
                    break;
                }
            }
        }
    }

    public function append_images_to_translation_package( $package, $post ) {

        if ( $post->post_type == 'product' ) {

            $product_images   = $this->woocommerce_wpml->media->product_images_ids( $post->ID );
            foreach ( $product_images as $image_id ) {
                $attachment_data = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT post_title,post_excerpt,post_content FROM {$this->wpdb->posts} WHERE ID = %d", $image_id ) );
                if ( ! $attachment_data ) {
                    continue;
                }
                $alt_text                                                 = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
                $alt_text                                                 = $alt_text ? $alt_text : '';
                $this->add_to_package( $package, 'image-id-' . $image_id . '-title', $attachment_data->post_title );
                $this->add_to_package( $package, 'image-id-' . $image_id . '-caption', $attachment_data->post_excerpt );
                $this->add_to_package( $package, 'image-id-' . $image_id . '-description', $attachment_data->post_content );
                $this->add_to_package( $package, 'image-id-' . $image_id . '-alt-text', $alt_text );

            }
        }
        return $package;
    }

    public function save_images_translations( $post_id, $data, $job ) {

        $language = $job->language_code;

        $product_images = $this->woocommerce_wpml->media->product_images_ids( $job->original_doc_id );
        foreach ( $product_images as $image_id ) {
            $translated_prod_image = apply_filters( 'translate_object_id', $image_id, 'attachment', false, $language );
            $image_data       = $this->get_image_data( $image_id, $data );
            if ( ! empty( $image_data ) ) {

                $translation = array();
                if( isset( $image_data['title'] ) ){
                    $translation['post_title'] = $image_data['title'];
                }
                if( isset( $image_data['description'] ) ){
                    $translation['post_content'] = $image_data['description'];
                }
                if( isset( $image_data['caption'] ) ){
                    $translation['post_excerpt'] = $image_data['caption'];
                }

                if( $translation ){
                    $this->wpdb->update( $this->wpdb->posts, $translation, array( 'id' => $translated_prod_image ) );
                }

                if ( isset( $image_data['alt-text'] ) ) {
                    update_post_meta( $translated_prod_image, '_wp_attachment_image_alt', $image_data['alt-text'] );
                }
            }
        }
    }

    private function get_image_data( $image_id, $data ) {
        $image_data = array();

        foreach ( $data as $data_key => $value ) {
            if ( $value['finished'] && isset( $value['field_type'] ) ) {
                if ( strpos( $value['field_type'], 'image-id-' . $image_id ) === 0 ) {
                    if ( $value['field_type'] === 'image-id-' . $image_id . '-title' ) {
                        $image_data['title'] = $value['data'];
                    }
                    if ( $value['field_type'] === 'image-id-' . $image_id . '-caption' ) {
                        $image_data['caption'] = $value['data'];
                    }
                    if ( $value['field_type'] === 'image-id-' . $image_id . '-description' ) {
                        $image_data['description'] = $value['data'];
                    }
                    if ( $value['field_type'] === 'image-id-' . $image_id . '-alt-text' ) {
                        $image_data['alt-text'] = $value['data'];
                    }
                }
            }
        }

        return $image_data;
    }

    private function add_to_package( &$package, $key, $data ) {
        $package['contents'][ $key ] = array(
            'translate' => 1,
            'data'      => $this->tp->encode_field_data( $data, 'base64' ),
            'format'    => 'base64'
        );

    }
}


