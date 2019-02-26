<?php

class WCML_Media{

    /** @var woocommerce_wpml */
    private $woocommerce_wpml;
    /** @var  SitePress */
    private $sitepress;
    /** @var  wpdb */
    private $wpdb;

    public $settings = array();

    private $products_being_synced = array();

    public function __construct( $woocommerce_wpml, $sitepress, $wpdb ){
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress        = $sitepress;
        $this->wpdb             = $wpdb;
    }

    public function add_hooks(){
	    //when save new attachment duplicate product gallery
	    add_action( 'wpml_media_create_duplicate_attachment', array( $this, 'sync_product_gallery_duplicate_attachment' ), 11, 2 );
    }

    public function product_images_ids( $product_id ){
        $product_images_ids = array();

        //thumbnail image
        $tmb = get_post_meta( $product_id, '_thumbnail_id', true );
        if( $tmb ) {
            $product_images_ids[] = $tmb;
        }

        //product gallery
        $product_gallery = get_post_meta( $product_id, '_product_image_gallery', true );
        if( $product_gallery ) {
            $product_gallery = explode( ',', $product_gallery );
            foreach( $product_gallery as $img ){
                if( !in_array( $img, $product_images_ids ) ){
                    $product_images_ids[] = $img;
                }
            }
        }

        foreach( wp_get_post_terms( $product_id, 'product_type', array( "fields" => "names" ) ) as $type ){
            $product_type = $type;
        }

        if( isset( $product_type ) && $product_type == 'variable' ){
            $get_post_variations_image = $this->wpdb->get_col(
                $this->wpdb->prepare(
                    "SELECT pm.meta_value FROM {$this->wpdb->posts} AS p
                                                LEFT JOIN {$this->wpdb->postmeta} AS pm ON p.ID = pm.post_id
                                                WHERE pm.meta_key='_thumbnail_id'
                                                  AND p.post_status IN ('publish','private')
                                                  AND p.post_type = 'product_variation'
                                                  AND p.post_parent = %d
                                                ORDER BY ID", $product_id )
            );
            foreach( $get_post_variations_image as $variation_image ){
                if( $variation_image && !in_array( $variation_image, $product_images_ids ) ){
                    $product_images_ids[] = $variation_image;
                }
            }
        }

        foreach( $product_images_ids as $key => $image ){
            if( ! get_post_status ( $image ) ){
                unset( $product_images_ids[ $key ] );
            }
        }

        return $product_images_ids;
    }

	public function sync_thumbnail_id( $orig_post_id, $trnsl_post_id, $lang ) {
    	if ( method_exists( 'WPML_Media_Attachments_Duplication', 'sync_post_thumbnail') ) {
		    $factory = new WPML_Media_Attachments_Duplication_Factory();
		    $media_duplicate = $factory->create();
		    if( $media_duplicate ){
			    $media_duplicate->sync_post_thumbnail( $orig_post_id );
		    }
	    }
	}

	public function sync_variation_thumbnail_id( $variation_id, $translated_variation_id, $lang ){
		$thumbnail_id = get_post_meta( $variation_id, '_thumbnail_id', true );
		$translated_thumbnail = apply_filters( 'translate_object_id', $thumbnail_id, 'attachment', false, $lang );

		if( is_null( $translated_thumbnail ) && $thumbnail_id ){
			$factory = new WPML_Media_Attachments_Duplication_Factory();
			$media_duplicate = $factory->create();
			$translated_thumbnail = $media_duplicate->create_duplicate_attachment( $thumbnail_id, wp_get_post_parent_id( $thumbnail_id ), $lang );
		}

		update_post_meta( $translated_variation_id, '_thumbnail_id', $translated_thumbnail );
		update_post_meta( $variation_id, '_wpml_media_duplicate', 1 );
		update_post_meta( $variation_id, '_wpml_media_featured', 1 );
	}


	public function sync_product_gallery( $product_id ) {

		$product_gallery = get_post_meta( $product_id, '_product_image_gallery', true );
		$gallery_ids     = explode( ',', $product_gallery );

		$trid         = $this->sitepress->get_element_trid( $product_id, 'post_product' );
		$translations = $this->sitepress->get_element_translations( $trid, 'post_product', true );
		foreach ( $translations as $translation ) {
			$duplicated_ids = '';
			if ( ! $translation->original ) {
				foreach ( $gallery_ids as $image_id ) {
					if ( get_post( $image_id ) ) {
						$duplicated_id = apply_filters( 'translate_object_id', $image_id, 'attachment', false, $translation->language_code );
						if ( is_null( $duplicated_id ) && $image_id ) {
							$duplicated_id = $this->create_base_media_translation( $image_id, $translation->element_id, $translation->language_code );
						}
						$duplicated_ids .= $duplicated_id . ',';
					}
				}
				$duplicated_ids = substr( $duplicated_ids, 0, strlen( $duplicated_ids ) - 1 );
				update_post_meta( $translation->element_id, '_product_image_gallery', $duplicated_ids );
			}
		}
	}

	public function create_base_media_translation( $attachment_id, $parent_id, $target_lang ) {

		$factory = new WPML_Media_Attachments_Duplication_Factory();
		$media_duplicate = $factory->create();
		$duplicated_id = $media_duplicate->create_duplicate_attachment( $attachment_id, $parent_id, $target_lang );

		return $duplicated_id;
	}

	public function sync_product_gallery_duplicate_attachment( $att_id, $dup_att_id ) {
		$product_id = wp_get_post_parent_id( $att_id );
		$post_type  = get_post_type( $product_id );
		if ( $post_type != 'product' || array_key_exists( $product_id, $this->products_being_synced ) ) {
			return;
		}
		$this->products_being_synced[ $product_id ] = 1;
		$this->sync_product_gallery( $product_id );
		unset( $this->products_being_synced[ $product_id ] );
	}

}