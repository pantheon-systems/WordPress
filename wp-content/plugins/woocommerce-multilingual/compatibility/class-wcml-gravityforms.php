<?php

class WCML_gravityforms{

	/**
	 * @param SitePress $sitepress
	 */
	private $sitepress;

	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;

	public function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml ) {
		$this->sitepress        = $sitepress;
		$this->woocommerce_wpml = $woocommerce_wpml;
	}

    public function add_hooks(){
        add_filter( 'gform_formatted_money', array( $this, 'wcml_convert_price' ), 10, 2 );
        add_filter( 'wcml_multi_currency_ajax_actions', array(  $this, 'add_ajax_action' ) );

        add_action( 'wcml_after_duplicate_product_post_meta', array( $this, 'sync_gf_data'), 10, 3 );
    }
    
    function wcml_convert_price($formatted, $unformatted){
        if ( ! is_admin() ) {
        	$currency = apply_filters('wcml_price_currency', get_woocommerce_currency());
        	$formatted = strip_tags(wc_price(apply_filters('wcml_raw_price_amount', $unformatted), array('currency'=>$currency)));
        }
        return $formatted;    
	}

	
	function add_ajax_action($actions){
		$actions[] = 'get_updated_price';
		return $actions;
	}

    function sync_gf_data($original_product_id, $trnsl_product_id, $data){
        // sync only if WCML editor is in use
	    if( $this->woocommerce_wpml->is_wpml_prior_4_2() ){
		    $wcml_settings = get_option('_wcml_settings');
		    $is_using_tm_editor = $wcml_settings['trnsl_interface'];
	    }else{
		    $is_using_tm_editor = WPML_TM_Post_Edit_TM_Editor_Mode::is_using_tm_editor( $this->sitepress, $original_product_id );
	    }

	    if( $is_using_tm_editor ){
		    $orig_gf = maybe_unserialize( get_post_meta( $original_product_id, '_gravity_form_data' , true ) );
		    $trnsl_gf = maybe_unserialize( get_post_meta( $trnsl_product_id, '_gravity_form_data' , true ) );

		    if( !$trnsl_gf ){
			    update_post_meta( $trnsl_product_id, '_gravity_form_data', $orig_gf );
		    }else{
			    $trnsl_gf['id'] = $orig_gf['id'];
			    $trnsl_gf['display_title'] = $orig_gf['display_title'];
			    $trnsl_gf['display_description'] = $orig_gf['display_description'];
			    $trnsl_gf['disable_woocommerce_price'] = $orig_gf['disable_woocommerce_price'];
			    $trnsl_gf['disable_calculations'] = $orig_gf['disable_calculations'];
			    $trnsl_gf['disable_label_subtotal'] = $orig_gf['disable_label_subtotal'];
			    $trnsl_gf['disable_label_options'] = $orig_gf['disable_label_options'];
			    $trnsl_gf['disable_label_total'] = $orig_gf['disable_label_total'];
			    $trnsl_gf['disable_anchor'] = $orig_gf['disable_anchor'];

			    update_post_meta( $trnsl_product_id, '_gravity_form_data', $trnsl_gf );
		    }
	    }
    }
   
}
