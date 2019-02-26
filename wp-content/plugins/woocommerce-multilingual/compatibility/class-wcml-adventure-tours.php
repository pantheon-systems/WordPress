<?php

class WCML_Adventure_tours{

    /**
     * @var woocommerce_wpml
     */
    private $woocommerce_wpml;
    /**
     * @var SitePress
     */
    private $sitepress;

    /**
     * @var WPML_Element_Translation_Package
     */
    private $tp;

    /**
     * WCML_Adventure_tours constructor.
     * @param woocommerce_wpml $woocommerce_wpml
     * @param SitePress $sitepress
     * @param WPML_Element_Translation_Package $tp
     */
    function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, WPML_Element_Translation_Package $tp ) {
        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->sitepress        = $sitepress;
        $this->tp               = $tp;
    }

    public function add_hooks(){
        add_action( 'updated_post_meta', array( $this, 'sync_tour_data_across_translations' ), 10, 4 );
        add_filter( 'get_post_metadata', array( $this, 'product_price_filter'), 9, 4 );

        if ( is_admin() ) {

            add_action( 'wcml_gui_additional_box_html', array( $this, 'custom_box_html' ), 10, 3 );
            add_filter( 'wcml_gui_additional_box_data', array( $this, 'custom_box_html_data' ), 10, 4 );
            add_action( 'wcml_update_extra_fields', array( $this, 'tour_data_update' ), 10, 3 );

            add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_tour_data_translation_package' ), 10, 2 );
            add_action( 'wpml_translation_job_saved', array( $this, 'save_tour_data_translation' ), 10, 3 );

            add_action( 'admin_footer', array( $this, 'load_assets' ) );
            add_action( 'wcml_after_custom_prices_block', array( $this, 'add_custom_prices_block' ) );
            add_action( 'wcml_after_save_custom_prices', array( $this, 'save_custom_costs' ) );

            add_filter( 'wcml_is_variable_product', array( $this, 'is_variable_tour' ), 10, 2 );
            add_filter( 'wcml_variation_term_taxonomy_ids', array( $this, 'add_tour_tax_id' ) );
            add_filter( 'wcml_is_attributes_page', array( $this, 'is_attributes_page' ) );
            add_filter( 'wcml_is_attributes_page', array( $this, 'is_attributes_page' ) );

            add_filter( 'wcml_do_not_display_custom_fields_for_product', array(
                $this,
                'replace_tm_editor_custom_fields_with_own_sections'
            ) );
        }
    }


    function sync_tour_data_across_translations($meta_id, $post_id, $meta_key, $tour_tabs_meta)
    {
        if ($meta_key != 'tour_tabs_meta')
            return false;


        $post = get_post($post_id);

        // skip auto-drafts // skip autosave
        if ( $post->post_status == 'auto-draft' || isset( $_POST['autosave'] ) ) {
            return false;
        }

        if ($post->post_type == 'product') {

            remove_action('updated_post_meta', array($this, 'sync_tour_data_across_translations'), 10, 4);

            $original_product_id = $post_id;
            if ( ! $this->woocommerce_wpml->products->is_original_product( $post_id ) ) {
                $original_product_language = $this->woocommerce_wpml->products->get_original_product_language( $post_id );
                $original_product_id       = apply_filters( 'translate_object_id', $post_id, 'product', true, $original_product_language );
            }

            $product_trid = $this->sitepress->get_element_trid( $original_product_id, 'post_product' );
            $product_translations = $this->sitepress->get_element_translations( $product_trid, 'post_product' );

            foreach ($product_translations as $product_translation) {

                if ( empty( $product_translation->original ) ) {

                    $trnsl_tour_tabs_meta = get_post_meta( $product_translation->element_id, 'tour_tabs_meta', true );

                    $trnsl_tour_tabs_meta['tour_badge'] = $tour_tabs_meta['tour_badge'];

                    update_post_meta( $product_translation->element_id, 'tour_tabs_meta', $trnsl_tour_tabs_meta);

                }

            }

            add_action('updated_post_meta', array($this, 'sync_tour_data_across_translations'), 10, 4);

        }

    }

    function custom_box_html($obj, $product_id, $data)
    {

        if ( $tour_tabs_meta = get_post_meta( $product_id, 'tour_tabs_meta', true ) ) {

            $tour_section = new WPML_Editor_UI_Field_Section( __('Tour Data', 'woocommerce-multilingual'));

            $divider = true;

            foreach( $tour_tabs_meta['tabs'] as $tour_tab_id => $tour_tab_meta) {

                $group = new WPML_Editor_UI_Field_Group('', $divider);
                $composite_field = new WPML_Editor_UI_Single_Line_Field('adventure_tour_' . $tour_tab_id . '_title', __('Title', 'woocommerce-multilingual'), $data, false);
                $group->add_field($composite_field);
                $composite_field = new WPML_Editor_UI_Single_Line_Field('adventure_tour_' . $tour_tab_id . '_content', __('Content', 'woocommerce-multilingual'), $data, false);
                $group->add_field($composite_field);
                $tour_section->add_field($group);

            }

            if ( !empty( $tour_tabs_meta['tabs'] ) ) {
                $obj->add_field( $tour_section );
            }

        }

    }

    function custom_box_html_data($data, $product_id, $translation, $lang){

        if ( $tour_tabs_meta = get_post_meta( $product_id, 'tour_tabs_meta', true ) ) {

            foreach ( $tour_tabs_meta['tabs'] as $tour_tab_id => $tour_tab_meta ) {
                $data['adventure_tour_' . $tour_tab_id . '_title'] = array('original' => $tour_tab_meta['title'] );
                $data['adventure_tour_' . $tour_tab_id . '_content'] = array('original' => $tour_tab_meta['content'] );
            }

            if ($translation) {
                $translated_tour_tabs_meta = get_post_meta(  $translation->ID, 'tour_tabs_meta', true );

                if( $translated_tour_tabs_meta ){
                    foreach ( $translated_tour_tabs_meta['tabs'] as $tour_tab_id => $tour_tab_meta) {
                        $data['adventure_tour_' . $tour_tab_id . '_title']['translation'] = $tour_tab_meta['title'];
                        $data['adventure_tour_' . $tour_tab_id . '_content']['translation'] = $tour_tab_meta['content'];
                    }
                }
            }

        }

        return $data;
    }

    function tour_data_update( $original_product_id, $product_id, $data)
    {

        $tour_tabs_meta = get_post_meta( $original_product_id, 'tour_tabs_meta', true );

        if( isset( $tour_tabs_meta['tabs'] ) && is_array( $tour_tabs_meta['tabs'] ) ){
            foreach ( $tour_tabs_meta['tabs'] as $tour_tab_id => $tour_tab_meta ) {

                if (!empty($data[md5('adventure_tour_' . $tour_tab_id . '_title')])) {
                    $tour_tabs_meta['tabs'][$tour_tab_id]['title'] = $data[md5('adventure_tour_' . $tour_tab_id . '_title')];
                }

                if (!empty($data[md5('adventure_tour_' . $tour_tab_id . '_content')])) {
                    $tour_tabs_meta['tabs'][$tour_tab_id]['content'] = $data[md5('adventure_tour_' . $tour_tab_id . '_content')];
                }
            }
            remove_action('updated_post_meta', array($this, 'sync_tour_data_across_translations'), 10, 4);

            update_post_meta($product_id, 'tour_tabs_meta', $tour_tabs_meta);

            add_action('updated_post_meta', array($this, 'sync_tour_data_across_translations'), 10, 4);
        }

    }

    function append_tour_data_translation_package($package, $post)
    {

        if ($post->post_type == 'product') {

            $tour_tabs_meta = get_post_meta( $post->ID, 'tour_tabs_meta', true );

            if ($tour_tabs_meta) {

                $fields = array('title', 'content');

                foreach ( $tour_tabs_meta['tabs'] as $tour_tab_id => $tour_tab_meta) {

                    foreach ($fields as $field) {
                        if (!empty($tour_tab_meta[$field])) {

                            $package['contents']['wc_adventure_tour:' . $tour_tab_id . ':' . $field] = array(
                                'translate' => 1,
                                'data' => $this->tp->encode_field_data($tour_tab_meta[$field], 'base64'),
                                'format' => 'base64'
                            );

                        }
                    }

                }

            }

        }

        return $package;

    }

    function save_tour_data_translation($post_id, $data, $job)
    {


        $translated_tour_data = array();
        foreach ($data as $value) {

            if (preg_match('/wc_adventure_tour:([0-9]+):(.+)/', $value['field_type'], $matches)) {

                $tour_tab_id = $matches[1];
                $field = $matches[2];

                $translated_tour_data[$tour_tab_id][$field] = $value['data'];

            }

        }

        if ($translated_tour_data) {

            $tour_tabs_meta = get_post_meta( $job->original_doc_id, 'tour_tabs_meta', true );


            foreach ( $tour_tabs_meta['tabs'] as $tour_tab_id => $tour_tab_meta) {

                if (isset($translated_tour_data[$tour_tab_id]['title'])) {
                    $tour_tabs_meta['tabs'][$tour_tab_id]['title'] = $translated_tour_data[$tour_tab_id]['title'];
                }

                if (isset($translated_tour_data[$tour_tab_id]['content'])) {
                    $tour_tabs_meta['tabs'][$tour_tab_id]['content'] = $translated_tour_data[$tour_tab_id]['content'];
                }
            }

        }
        remove_action('updated_post_meta', array($this, 'sync_tour_data_across_translations'), 10, 4);
        update_post_meta($post_id, 'tour_tabs_meta', $tour_tabs_meta);
        add_action('updated_post_meta', array($this, 'sync_tour_data_across_translations'), 10, 4);

    }

    function load_assets(){
        global $pagenow;

        if( $pagenow == 'post.php' || $pagenow == 'post-new.php' ){
            wp_register_script( 'wcml-adventure-tours', WCML_PLUGIN_URL . '/compatibility/res/js/wcml-adventure-tours.js', array( 'jquery' ), WCML_VERSION );
            wp_enqueue_script( 'wcml-adventure-tours' );
        }
    }

    function add_custom_prices_block( $product_id ){

        if( $product_id != 'new' ){
            $currencies = $this->woocommerce_wpml->multi_currency->get_currencies();
            $tour_booking_periods = get_post_meta( $product_id, 'tour_booking_periods', true );
            $custom_periods_prices = get_post_meta( $product_id, 'custom_booking_periods_prices', true );
            if( $tour_booking_periods ){
                foreach( $tour_booking_periods as $per_key => $tour_booking_period ){
                    foreach( $currencies as $key => $currency ){

                        $value = isset($custom_periods_prices[ $per_key ][ $key ]) ? $custom_periods_prices[ $per_key ][ $key ]: '';

                        echo '<div class="wcml_custom_cost_field" data-tour="'.$per_key.'" style="display: none;">';
                        echo '<div>'.get_woocommerce_currency_symbol($key).'</div>';
                        echo '<input type="text" class="wc_input_price" style="width: 60px;" name="tour_spec_price['.$per_key.']['.$key.']" value="'.$value.'" />';
                        echo '</div>';
                    }
                }
            }

            echo '<div class="wcml_custom_cost_field_empty"  style="display: none;">';
                echo '<div></div>';
                echo '<input type="text" class="wc_input_price" style="width: 60px;" name="tour_spec_price" value="" />';
            echo '</div>';
        }
    }

    function save_custom_costs( $post_id ){

        $tour_spec_price = array();
        $currencies = $this->woocommerce_wpml->multi_currency->get_currencies();

        if( isset( $_POST[ 'tour_spec_price' ] ) && is_array( $_POST[ 'tour_spec_price' ] ) ) {

            foreach( $_POST[ 'tour_spec_price' ] as $per_key => $costs ) {

                foreach( $currencies as $code => $currency ) {

                    $tour_spec_price[ $per_key ][ $code ] = $costs[ $code ];

                }
            }

            update_post_meta( $post_id, 'custom_booking_periods_prices', $tour_spec_price );
        }
    }

    function product_price_filter( $value, $object_id, $meta_key, $single ){

	    if(
		    $meta_key === 'tour_booking_periods' &&
		    $this->woocommerce_wpml->settings[ 'enable_multi_currency' ] === WCML_MULTI_CURRENCIES_INDEPENDENT &&
		    !is_admin() &&
		    get_post_type( $object_id ) === 'product' &&
		    ( $currency = $this->woocommerce_wpml->multi_currency->get_client_currency() ) !== get_option( 'woocommerce_currency' )
	    ) {

            remove_filter( 'get_post_metadata', array( $this, 'product_price_filter' ), 9, 4 );

            $original_language = $this->woocommerce_wpml->products->get_original_product_language( $object_id );
            $original_product = apply_filters( 'translate_object_id', $object_id, 'product', true, $original_language );

            if ( get_post_meta( $original_product, '_wcml_custom_prices_status' ) ) {
                $custom_periods_prices = get_post_meta( $object_id, 'custom_booking_periods_prices', true );
                $tours_data = get_post_meta( $object_id, 'tour_booking_periods', true );
                if( $tours_data ){
                    foreach( $tours_data as $key => $periods ){
                        if( isset( $custom_periods_prices[ $key ][ $currency ] ) ){
                            $tours_data[ $key ][ 'spec_price' ] = $custom_periods_prices[ $key ][ $currency ];
                        }
                    }

                    if( $single ){
                        $value[0] = $tours_data;
                    }else{
                        $value = $tours_data;
                    }

                }
            }
            add_filter( 'get_post_metadata', array( $this, 'product_price_filter' ), 9, 4 );
        }

        return $value;
    }

    function add_tour_tax_id( $variation_term_taxonomy_ids ){
        global $wpdb;
        $tour_taxonomy_id = $wpdb->get_var( "SELECT tt.term_taxonomy_id FROM {$wpdb->terms} AS t LEFT JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id WHERE t.name = 'tour' AND tt.taxonomy = 'product_type'" );

        if( $tour_taxonomy_id ){
            $variation_term_taxonomy_ids[] = $tour_taxonomy_id;
        }

        return $variation_term_taxonomy_ids;

    }

    function is_variable_tour( $is_variable, $product_id ){
        $var_tour_meta = get_post_meta( $product_id, '_variable_tour', true );

        if( $is_variable && $var_tour_meta == 'yes' ){
            $is_variable = true;
        }elseif( $var_tour_meta == 'no' ){
            $is_variable = false;
        }

        return $is_variable;
    }

    function is_attributes_page( $is_attributes_page ){

        if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'product_attributes_extended' && isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == 'product' ){
            $is_attributes_page = true;
        }

        return $is_attributes_page;
    }

    function replace_tm_editor_custom_fields_with_own_sections( $fields ){
        $fields[] = 'tour_tabs_meta';

        return $fields;
    }

}