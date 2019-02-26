<?php


//post

/**
 *Add meta box to the term category page.
 */
function nectar_taxonomy_edit_meta_field($term) {

	wp_nonce_field( basename( __FILE__ ), 'nectar_post_cat_details_nonce' );

	// Put the term ID into a variable.
	$t_id = $term->term_id;
 
	// Retrieve the existing value(s) for this meta field. This returns an array.
	$term_meta = get_option( "taxonomy_$t_id" );
	ob_start(); ?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[category_thumbnail_image]"><?php _e( 'Category Thumbnail Image', 'salient' ); ?></label></th>
		<td>
			
				<input type="hidden" id="category_thumbnail_image" name="term_meta[category_thumbnail_image]" value="<?php echo esc_attr( $term_meta['category_thumbnail_image'] ) ? esc_attr( $term_meta['category_thumbnail_image'] ) : ''; ?>" />
		        <img class="redux-opts-screenshot" id="redux-opts-screenshot-category_thumbnail_image" src="<?php echo esc_attr( $term_meta['category_thumbnail_image'] ) ? esc_attr( $term_meta['category_thumbnail_image'] ) : ''; ?>" />
		        <?php if(empty($term_meta['category_thumbnail_image'])) { $remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; } ?>
		        <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary" <?php echo $upload; // WPCS: XSS ok. ?>  rel-id="category_thumbnail_image"> <?php echo esc_html__('Upload', 'salient'); ?> </a>
		        <a href="javascript:void(0);" class="redux-opts-upload-remove" <?php echo $remove; // WPCS: XSS ok. ?> rel-id="category_thumbnail_image"> <?php echo esc_html__('Remove Upload', 'salient'); ?> </a>
		
		</td>
	</tr>
	
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[category_image]"><?php _e( 'Category Header Image', 'salient' ); ?></label></th>
		<td>
			
				<input type="hidden" id="category_image" name="term_meta[category_image]" value="<?php echo esc_attr( $term_meta['category_image'] ) ? esc_attr( $term_meta['category_image'] ) : ''; ?>" />
		        <img class="redux-opts-screenshot" id="redux-opts-screenshot-category_image" src="<?php echo esc_attr( $term_meta['category_image'] ) ? esc_attr( $term_meta['category_image'] ) : ''; ?>" />
		        <?php if(empty($term_meta['category_image'])) { $remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; } ?>
		        <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary" <?php echo $upload; // WPCS: XSS ok. ?>  rel-id="category_image"> <?php echo esc_html__('Upload', 'salient'); ?> </a>
		        <a href="javascript:void(0);" class="redux-opts-upload-remove" <?php echo $remove; // WPCS: XSS ok. ?> rel-id="category_image"> <?php echo esc_html__('Remove Upload', 'salient'); ?> </a>
		
		</td>
	</tr>
	<tr class="form-field">

	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[category_color]"><?php _e( 'Category Color', 'salient' ); ?></label></th>
		<td>	
			<?php 
			 if(get_bloginfo('version') >= '3.5') {
		            wp_enqueue_style('wp-color-picker');
		            wp_enqueue_script(
		                'redux-opts-field-color-js',
		                NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/color/field_color.js',
		                array('wp-color-picker'),
		                time(),
		                true
		            );
		        } else {
		            wp_enqueue_script(
		                'redux-opts-field-color-js', 
		                NECTAR_FRAMEWORK_DIRECTORY . 'options/fields/color/field_color_farb.js', 
		                array('jquery', 'farbtastic'),
		                time(),
		                true
		            );
		        }
				
				if(get_bloginfo('version') >= '3.5') { ?>
		          <input type="text" id="term_meta[category_color]" name="term_meta[category_color]" value="<?php echo esc_attr( $term_meta['category_color'] ) ? esc_attr( $term_meta['category_color'] ) : ''; ?>" class=" popup-colorpicker" style="width: 70px;" data-default-color=""/>
		        <?php } else { ?>
		          <div class="farb-popup-wrapper">
		          <input type="text" id="term_meta[category_color]" name="term_meta[category_color]" value="<?php echo esc_attr( $term_meta['category_color'] ) ? esc_attr( $term_meta['category_color'] ) : ''; ?>" class=" popup-colorpicker" style="width:70px;"/>
		          <div class="farb-popup"><div class="farb-popup-inside"><div id="term_meta[category_color]" class="color-picker"></div></div></div>
		          </div>
		       <?php  }
		        ?>
		    </td>
		</tr>



	<?php ob_end_flush();
}
add_action( 'category_edit_form_fields', 'nectar_taxonomy_edit_meta_field', 10, 2 );

/**
 * Save meta data callback function.
 */
function nectar_save_taxonomy_custom_meta( $term_id ) {

	if ( ! isset( $_POST['nectar_post_cat_details_nonce'] ) || ! wp_verify_nonce( $_POST['nectar_post_cat_details_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = sanitize_text_field ( $_POST['term_meta'][$key] );
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  
add_action( 'edited_category', 'nectar_save_taxonomy_custom_meta', 10, 2 );  
add_action( 'create_category', 'nectar_save_taxonomy_custom_meta', 10, 2 );




//woocommerce
global $woocommerce;

function nectar_taxonomy_edit_product_meta_field($term) {

	wp_nonce_field( basename( __FILE__ ), 'nectar_product_cat_details_nonce' );

	// Put the term ID into a variable.
	$t_id = $term->term_id;
 
	// Retrieve the existing value(s) for this meta field. This returns an array.
	$term_meta = get_option( "taxonomy_$t_id" );
	ob_start(); ?>
	<table class="form-table">
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[product_category_image]"><?php _e( 'Category Header Image', 'salient' ); ?></label></th>
		<td>
			
				<input type="hidden" id="product_category_image" name="term_meta[product_category_image]" value="<?php echo esc_attr( $term_meta['product_category_image'] ) ? esc_attr( $term_meta['product_category_image'] ) : ''; ?>" />
		        <img class="redux-opts-screenshot" id="redux-opts-screenshot-category_image" src="<?php echo esc_attr( $term_meta['product_category_image'] ) ? esc_attr( $term_meta['product_category_image'] ) : ''; ?>" />
		        <?php if(empty($term_meta['product_category_image'])) { $remove = ' style="display:none;"'; $upload = ''; } else {$remove = ''; $upload = ' style="display:none;"'; } ?>
		        <a data-update="Select File" data-choose="Choose a File" href="javascript:void(0);"class="redux-opts-upload button-secondary" <?php echo $upload; ?>  rel-id="product_category_image"> <?php echo esc_html__('Upload', 'salient'); ?> </a>
		        <a href="javascript:void(0);" class="redux-opts-upload-remove" <?php echo $remove; // WPCS: XSS ok. ?> rel-id="product_category_image"> <?php echo esc_html__('Remove Upload', 'salient'); ?> </a>
		
		</td>
	</tr>
	</table>
	<?php ob_end_flush();
}


function nectar_save_taxonomy_product_custom_meta( $term_id ) {

	if ( ! isset( $_POST['nectar_product_cat_details_nonce'] ) || ! wp_verify_nonce( $_POST['nectar_product_cat_details_nonce'], basename( __FILE__ ) ) ) {
		return;
	}

	if ( isset( $_POST['term_meta'] ) ) {
		$t_id = $term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$cat_keys = array_keys( $_POST['term_meta'] );
		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['term_meta'][$key] ) ) {
				$term_meta[$key] = sanitize_text_field ( $_POST['term_meta'][$key] );
			}
		}
		// Save the option array.
		update_option( "taxonomy_$t_id", $term_meta );
	}
}  

if($woocommerce) {


	add_action( 'admin_init', 'nectar_product_cat_register_meta' );

	function nectar_product_cat_register_meta() {
		add_action( 'product_cat_edit_form_fields', 'nectar_taxonomy_edit_product_meta_field', 10, 2 );
		add_action( 'edit_product_cat', 'nectar_save_taxonomy_product_custom_meta', 10, 2 );  
		add_action( 'create_product_cat', 'nectar_save_taxonomy_product_custom_meta', 10, 2 );
	}
}
