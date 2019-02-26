<?php 

if (!defined( 'ABSPATH')) exit;

class Nectar_Woo_Quickview {
  
  function __construct() {

      add_action( 'wp_ajax_nectar_woo_get_product', array($this,'nectar_woo_get_product_info') );
      add_action( 'wp_ajax_nopriv_nectar_woo_get_product', array($this,'nectar_woo_get_product_info') );
      add_action( 'nectar_woocommerce_before_add_to_cart', array($this,'nectar_woo_add_quick_view_button') );
      add_action( 'wp_enqueue_scripts', array($this,'enqueue_scripts'));
      add_action( 'wp_footer', array($this, 'nectar_quick_view_markup'));
      
      $this->nectar_add_template_actions();
  }
  
  
  function enqueue_scripts() {
    
    wp_register_script('nectar_woo_quick_view_js', get_template_directory_uri() . '/nectar/woo/js/quick_view_actions.js', array('jquery'), '1.1', true);
    wp_enqueue_script( 'wc-add-to-cart-variation' );
    wp_enqueue_script('nectar_woo_quick_view_js');
    wp_enqueue_script('flickity');
  }
  
  public function nectar_woo_add_quick_view_button() {
    
    global $nectar_options;
		global $post;
    $product_style = (!empty($nectar_options['product_style'])) ? $nectar_options['product_style'] : 'classic';
    $button_class = ($product_style == 'classic') ? 'button' : '';
    $button_icon = ($product_style != 'material') ? '<i class="normal icon-salient-m-eye"></i>' : '';
    
    $get_product = wc_get_product( $post->ID );
    
    if($get_product->is_type( 'grouped' ) || $get_product->is_type( 'external' ) ) { return; }
    
    echo '<a class="nectar_quick_view no-ajaxy '.$button_class.'" data-product-id="'.$post->ID.'"> '.$button_icon.'
    <span>' . esc_html__('Quick View', 'salient') . '</span></a>';
    
	}
  
  public function nectar_quick_view_markup() {
    
    global $nectar_options;
    $quick_view_sizing = 'cropped';
    
		echo '<div class="nectar-quick-view-box-backdrop"></div>
    <div class="nectar-quick-view-box" data-image-sizing="'.$quick_view_sizing.'">
    <div class="inner-wrap">
    
    <div class="close">
      <a href="#" class="no-ajaxy">
        <span class="close-wrap"> <span class="close-line close-line1"></span> <span class="close-line close-line2"></span> </span>		     	
      </a>
    </div>
        
        <div class="product-loading">
          <span class="dot"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
        
        <div class="preview_image"></div>
        
		    <div class="inner-content">
        
          <div class="product">  
             <div class="product type-product"> 
                  
                  <div class="woocommerce-product-gallery">
                  
                  </div>
                  
                  <div class="summary entry-summary scrollable">
                     <div class="summary-content">   
                     </div>
                  </div>
                  
             </div>
          </div>
          
        </div>
      </div>
		</div>';

		 
	}
  
  public function nectar_add_template_actions() {
    
    add_action('nectar_quick_view_summary_content','woocommerce_template_single_title');
    add_action('nectar_quick_view_summary_content','woocommerce_template_single_rating');
    add_action('nectar_quick_view_summary_content','woocommerce_template_single_price');
    add_action('nectar_quick_view_summary_content','woocommerce_template_single_excerpt');
    add_action('nectar_quick_view_summary_content','woocommerce_template_single_add_to_cart');
    
    add_action('nectar_quick_view_sale_content','woocommerce_show_product_sale_flash');

  }
  
  
  public function nectar_woo_get_product_info() {
    
    
		global $woocommerce;
    global $post;
    
		$product_id = intval($_POST['product_id']);
    
		if(intval($product_id)){
      
     //set the wp query for the product based on ID
		 wp('p=' . $product_id . '&post_type=product');
     
	   ob_start();
 	   
		 	while ( have_posts() ) : the_post(); ?>
      
	 	    <script>
          var wc_add_to_cart_variation_params = {};     
	 	    </script>
        
	        <div class="product">  
            
	                <div itemscope id="product-<?php the_ID(); ?>" <?php post_class('product'); ?> >  
                  
	                      <?php 
                        
                        do_action('nectar_quick_view_sale_content');

                         global $product;
                         if ( has_post_thumbnail() ) { ?>
                          <div class="images"> 
                          <div class="nectar-product-slider generate-markup">
                             
                           <div class="carousel-cell woocommerce-product-gallery__image">
           	                	<a href="#">
           	                		<?php echo get_the_post_thumbnail( $post->ID, 'large'); ?>
           	                	</a>
                           </div>
                           
                           <?php 
                           	$product_attach_ids = $product->get_gallery_image_ids(); 
                            if ( $product_attach_ids ) {
                    
                    					foreach ($product_attach_ids as $product_attach_id) {
                    
                    						$img_link = wp_get_attachment_url( $product_attach_id );
                    			
                    						if (!$img_link)
                    							continue;
                    
                    						printf( '<div class="carousel-cell woocommerce-product-gallery__image"><a href="%s" title="%s"> %s </a></div>', wp_get_attachment_url($product_attach_id),esc_attr( get_post($product_attach_id)->post_title ), wp_get_attachment_image($product_attach_id, 'large'));
                              
                    					}// foreach
                              
                    				} //if attach ids
                            
                            echo '</div> <!--nectar-product-slider--> </div>';
                            
                         } else {
                           $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
                           $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
                           $html .= '</div>';
                         }

 
                         ?>
                         
                    
 	                        <div class="summary entry-summary scrollable">
 	                                <div class="summary-content">   
                                       <?php
                                       
                                       echo '<div class="nectar-full-product-link"><a href="'.esc_url(get_permalink()).'"><span>'. esc_html__('More Information', 'salient') .'</span></a></div>';
                                       
                                       do_action('nectar_quick_view_summary_content');
      
                                      ?>
 	                                </div>
 	                        </div>
                          
 	                </div> 
 	        </div>
 	       
 	        <?php endwhile;

 	                  
 	        echo  ob_get_clean();
 	
 	        exit();
            
			
	    }
	}
  
  
  
}



$nectar_quick_view = new Nectar_Woo_Quickview();

?>