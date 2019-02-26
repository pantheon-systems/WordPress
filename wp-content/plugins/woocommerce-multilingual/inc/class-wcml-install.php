<?php

class WCML_Install{

    public static function initialize( &$woocommerce_wpml, &$sitepress ) {

        if( is_admin() ) {

            // Install routine
            if ( empty( $woocommerce_wpml->settings['set_up'] ) ) { // from 3.2

                if ( $woocommerce_wpml->settings['is_term_order_synced'] !== 'yes' ) {
                    //global term ordering resync when moving to >= 3.3.x
                    add_action( 'init', array( $woocommerce_wpml->terms, 'sync_term_order_globally' ), 20 );
                }

                if ( ! isset( $woocommerce_wpml->settings['wc_admin_options_saved'] ) ) {
                    self::handle_admin_texts();
                    $woocommerce_wpml->settings['wc_admin_options_saved'] = 1;
                }

                if ( ! isset( $woocommerce_wpml->settings['trnsl_interface'] ) ) {
                    $woocommerce_wpml->settings['trnsl_interface'] = 1;
                }

                if ( ! isset( $woocommerce_wpml->settings['products_sync_date'] ) ) {
                    $woocommerce_wpml->settings['products_sync_date'] = 1;
                }

                if ( ! isset( $woocommerce_wpml->settings['products_sync_order'] ) ) {
                    $woocommerce_wpml->settings['products_sync_order'] = 1;
                }

                if ( ! isset( $woocommerce_wpml->settings['display_custom_prices'] ) ) {
                    $woocommerce_wpml->settings['display_custom_prices'] = 0;
                }

                if ( ! isset( $woocommerce_wpml->settings['sync_taxonomies_checked'] ) ) {
                    $woocommerce_wpml->terms->check_if_sync_terms_needed();
                    $woocommerce_wpml->settings['sync_taxonomies_checked'] = 1;
                }

                WCML_Capabilities::set_up_capabilities();

                self:: set_language_information( $sitepress );
                self:: check_product_type_terms();

                set_transient( '_wcml_activation_redirect', 1, 30 );

                // Before the setup wizard redirects from plugins.php, allow WPML to scan the wpml-config.xml file
                WPML_Config::load_config_run();

	            add_action( 'init', array( __CLASS__, 'insert_default_categories' ) );

                $woocommerce_wpml->settings['set_up'] = 1;
                $woocommerce_wpml->update_settings();

            }

            if ( empty( $woocommerce_wpml->settings['downloaded_translations_for_wc'] ) ) { //from 3.3.3
                $woocommerce_wpml->languages_upgrader->download_woocommerce_translations_for_active_languages();
                $woocommerce_wpml->settings['downloaded_translations_for_wc'] = 1;
                $woocommerce_wpml->update_settings();
            }

            if ( empty( $woocommerce_wpml->settings[ 'rewrite_rules_flashed' ] ) ) {
                flush_rewrite_rules();
                $woocommerce_wpml->settings['rewrite_rules_flashed'] = 1;
            }

            add_filter( 'wpml_tm_dashboard_translatable_types', array(
                __CLASS__,
                'hide_variation_type_on_tm_dashboard'
            ) );

	        $WCML_Setup_UI       = new WCML_Setup_UI( $woocommerce_wpml );
	        $WCML_Setup_UI->add_hooks();
	        $WCML_Setup_Handlers = new WCML_Setup_Handlers( $woocommerce_wpml );
	        $WCML_Setup          = new WCML_Setup( $WCML_Setup_UI, $WCML_Setup_Handlers, $woocommerce_wpml, $sitepress );
	        $WCML_Setup->setup_redirect();
	        $WCML_Setup->add_hooks();

            if ( ! empty( $woocommerce_wpml->settings['set_up_wizard_run'] ) ) {
                add_action( 'admin_notices', array( __CLASS__, 'admin_notice_after_install' ) );
            }

            $translated_product_type_terms = WCML_Install::translated_product_type_terms();
            if ( !empty( $translated_product_type_terms ) ) {
                add_action( 'admin_notices', array( __CLASS__, 'admin_translated_product_type_terms_notice' ) );
            }elseif( $sitepress->is_translated_taxonomy( 'product_type' ) ){
                add_action( 'admin_notices', array( __CLASS__, 'admin_translated_product_type_notice' ) );
            }
        }

    }

    private static function set_language_information( &$sitepress ){
        global $wpdb;

        $def_lang = $sitepress->get_default_language();
        //set language info for products
        $products = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_status <> 'auto-draft'");
        foreach($products as $product){
            $exist = $sitepress->get_language_for_element($product->ID,'post_product');
            if(!$exist){
                $sitepress->set_element_language_details($product->ID, 'post_product',false,$def_lang);
            }
        }

        //set language info for taxonomies
        $terms = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat'");
        foreach($terms as $term){
            $exist = $sitepress->get_language_for_element($term->term_taxonomy_id, 'tax_product_cat');
            if(!$exist){
                $sitepress->set_element_language_details($term->term_taxonomy_id, 'tax_product_cat',false,$def_lang);
            }
        }
        $terms = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_tag'");
        foreach($terms as $term){
            $exist = $sitepress->get_language_for_element($term->term_taxonomy_id, 'tax_product_tag');
            if(!$exist){
                $sitepress->set_element_language_details($term->term_taxonomy_id, 'tax_product_tag',false,$def_lang);
            }
        }

        $terms = $wpdb->get_results("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_shipping_class'");
        foreach($terms as $term){
            $exist = $sitepress->get_language_for_element($term->term_taxonomy_id, 'tax_product_shipping_class');
            if(!$exist){
                $sitepress->set_element_language_details($term->term_taxonomy_id, 'tax_product_shipping_class',false,$def_lang);
            }
        }
    }

    //handle situation when product_type terms translated before activating WCML
    public static function check_product_type_terms(){
        global $wpdb;
        //check if terms were translated
        $translations = self::translated_product_type_terms();

        if( $translations ){
            foreach( $translations as $translation ){
                if( !is_null( $translation->source_language_code ) ){
                    //check relationships
                    $term_relationships = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d", $translation->element_id  ) );
                    if( $term_relationships ){
                        $orig_term = $wpdb->get_var( $wpdb->prepare( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE element_type = 'tax_product_type' AND trid = %d AND source_language_code IS NULL", $translation->trid ) );
                        if( $orig_term ){
                            foreach( $term_relationships as $term_relationship ){
                                $wpdb->update(
                                    $wpdb->term_relationships,
                                    array(
                                        'term_taxonomy_id' => $orig_term
                                    ),
                                    array(
                                        'object_id' => $term_relationship->object_id,
                                        'term_taxonomy_id' => $translation->element_id
                                    )
                                );
                            }
                        }
                    }
                    $term_id = $wpdb->get_var( $wpdb->prepare( "SELECT term_id FROM {$wpdb->term_taxonomy} WHERE term_taxonomy_id = %d", $translation->element_id  ) );

                    if( $term_id ){
                        $wpdb->delete(
                            $wpdb->terms,
                            array(
                                'term_id' => $term_id
                            )
                        );

                        $wpdb->delete(
                            $wpdb->term_taxonomy,
                            array(
                                'term_taxonomy_id' => $translation->element_id
                            )
                        );
                    }
                }
            }

            foreach( $translations as $translation ){
                $wpdb->delete(
                    $wpdb->prefix . 'icl_translations',
                    array(
                        'translation_id' => $translation->translation_id
                    )
                );
            }
        }
    }

    public static function translated_product_type_terms(){
        global $wpdb;
        //check if terms were translated
        $translations = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}icl_translations WHERE element_type = 'tax_product_type'" );

        return $translations;
    }

    private static function handle_admin_texts(){
        if(class_exists('WooCommerce')){
            //emails texts
            $emails = new WC_Emails();
            foreach($emails->emails as $email){
                $option_name  = $email->plugin_id.$email->id.'_settings';
                if(!get_option($option_name)){
                    add_option($option_name,$email->settings);
                }
            }
        }
    }

    public static function admin_notice_after_install(){
        global $woocommerce_wpml;

        $tracking_link = new WCML_Tracking_Link();
        if( !$woocommerce_wpml->settings['dismiss_doc_main'] ){

            $url = $_SERVER['REQUEST_URI'];
            $pos = strpos($url, '?');

            if($pos !== false){
                $url .= '&wcml_action=dismiss';
            } else {
                $url .= '?wcml_action=dismiss';
            }
            ?>
            <div id="message" class="updated message fade otgs-is-dismissible">
                <p>
                    <?php printf( esc_html__( "You've successfully installed %sWooCommerce Multilingual%s. Would you like to see a quick overview?", 'woocommerce-multilingual' ),
                        '<strong>', '</strong>' ); ?>
                </p>
                <p>
                    <a class="button-primary align-right" href="<?php echo esc_url( $tracking_link->generate(
                            'https://wpml.org/documentation/related-projects/woocommerce-multilingual/','woocommerce-multilingual','documentation') ); ?>" target="_blank">
                        <?php _e('Learn how to turn your e-commerce site multilingual', 'woocommerce-multilingual') ?>
                    </a>
                </p>
                <a class="notice-dismiss" href="<?php echo $url; ?>"><span class="screen-reader-text"><?php _e('Dismiss', 'woocommerce-multilingual') ?></span></a>
            </div>
            <?php
        }
    }

    public static function admin_translated_product_type_notice(){ ?>

        <div id="message" class="updated error">
            <p>
                <?php printf(__("We detected a problem in your WPML configuration: the %sproduct_type%s taxonomy is set as translatable and this would cause problems with translated products. You can fix this in the %sMultilingual Content Setup page%s.", 'woocommerce-multilingual'), '<i>', '</i>','<a href="' . admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=mcsetup#ml-content-setup-sec-8' ) . '">','</a>'); ?>
            </p>
        </div>

        <?php
    }

    public static function admin_translated_product_type_terms_notice(){ ?>

        <div id="message" class="updated error">
            <p>
                <?php printf(__("We detected that the %sproduct_type%s field was set incorrectly for some product translations. This happened because the product_type taxonomy was translated. You can fix this in the WooCommerce Multilingual %stroubleshooting page%s.", 'woocommerce-multilingual'), '<i>', '</i>','<a href="' . admin_url( 'admin.php?page=wpml-wcml&tab=troubleshooting' ) . '">','</a>'); ?>
            </p>
        </div>

        <?php
    }

    public static function hide_variation_type_on_tm_dashboard( $types ){
        unset( $types['product_variation'] );
        return $types;
    }

	public static function insert_default_categories() {
		global $sitepress, $woocommerce_wpml;

		$settings = $woocommerce_wpml->get_settings();

		$default_language   = $sitepress->get_default_language();
		$default_categories = isset( $settings['default_categories'] ) ? $settings['default_categories'] : array() ;

		foreach ( $sitepress->get_active_languages() as $language ) {
			if ( isset( $default_categories[ $language['code'] ] ) ) {
				continue;
			}

			$sitepress->switch_locale( $language['code'] );
			$translated_cat_name  = __( 'Uncategorized', 'sitepress' );
			$translated_cat_name  = $translated_cat_name === 'Uncategorized' && $language['code'] !== 'en' ? 'Uncategorized @' . $language['code'] : $translated_cat_name;
			$translated_term = get_term_by( 'name', $translated_cat_name, 'product_cat', ARRAY_A );
			$sitepress->switch_locale();

			// check if the term already exists
			if ( !$translated_term ) {
				$translated_term = wp_insert_term( $translated_cat_name, 'product_cat' );
			}

			if ( $translated_term && ! is_wp_error( $translated_term ) ) {
				// add it to settings
				$settings['default_categories'][ $language['code'] ] = $translated_term['term_taxonomy_id'];

				//update translations table
				$default_category_trid = $sitepress->get_element_trid(
					get_option( 'default_product_cat' ),
					'tax_product_cat'
				);
				$sitepress->set_element_language_details(
					$translated_term['term_taxonomy_id'],
					'tax_product_cat',
					$default_category_trid,
					$language['code'],
					$default_language
				);
			}

		}

		$woocommerce_wpml->update_settings( $settings );
	}

}