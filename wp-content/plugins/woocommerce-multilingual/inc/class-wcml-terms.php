<?php
  
class WCML_Terms{
    
    private $ALL_TAXONOMY_TERMS_TRANSLATED = 0;
    private $NEW_TAXONOMY_TERMS = 1;
    private $NEW_TAXONOMY_IGNORED = 2;

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var SitePress */
	private $sitepress;
	/** @var wpdb */
    private $wpdb;

	/**
	 * WCML_Terms constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 * @param wpdb $wpdb
	 */
    function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, wpdb $wpdb ){
	    $this->woocommerce_wpml = $woocommerce_wpml;
	    $this->sitepress        = $sitepress;
	    $this->wpdb             = $wpdb;
    }

	public function add_hooks() {

		add_action( 'updated_woocommerce_term_meta', array( $this, 'sync_term_order' ), 100, 4 );

		add_filter( 'wp_get_object_terms', array( $this->sitepress, 'get_terms_filter' ) );

		add_action( 'icl_save_term_translation', array( $this, 'save_wc_term_meta' ), 100, 4 );

		add_action( 'created_term', array( $this, 'translated_terms_status_update' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'translated_terms_status_update' ), 10, 3 );
		add_action( 'wp_ajax_wcml_update_term_translated_warnings', array(
			$this,
			'wcml_update_term_translated_warnings'
		) );

		add_action( 'created_term', array( $this, 'set_flag_for_variation_on_attribute_update' ), 10, 3 );

		add_filter( 'wpml_taxonomy_translation_bottom', array( $this, 'sync_taxonomy_translations' ), 10, 3 );

		add_action( 'wp_ajax_wcml_sync_product_variations', array( $this, 'wcml_sync_product_variations' ) );
		add_action( 'wp_ajax_wcml_tt_sync_taxonomies_in_content', array( $this, 'wcml_sync_taxonomies_in_content' ) );
		add_action( 'wp_ajax_wcml_tt_sync_taxonomies_in_content_preview', array(
			$this,
			'wcml_sync_taxonomies_in_content_preview'
		) );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'admin_menu_setup' ) );

			add_filter( 'pre_option_default_product_cat', array( $this, 'pre_option_default_product_cat' ) );
			add_filter( 'update_option_default_product_cat', array( $this, 'update_option_default_product_cat' ), 1, 2 );
		}

		add_action( 'delete_term', array( $this, 'wcml_delete_term' ), 10, 4 );
		add_filter( 'get_the_terms', array( $this, 'shipping_terms' ), 10, 3 );
		add_filter( 'get_terms', array( $this, 'filter_shipping_classes_terms' ), 10, 3 );

		add_filter( 'woocommerce_get_product_terms', array( $this, 'get_product_terms_filter' ), 10, 4 );
		add_action( 'created_term_translation', array( $this, 'set_flag_to_sync' ), 10, 3 );
	}
    
    function admin_menu_setup(){
        global $pagenow;
        if($pagenow == 'edit-tags.php' && isset($_GET['action']) && $_GET['action'] == 'edit'){
            add_action('admin_notices', array($this, 'show_term_translation_screen_notices'));    
        }

        $page = isset( $_GET['page'] )? $_GET['page'] : '';
        if ( $page === ICL_PLUGIN_FOLDER . '/menu/taxonomy-translation.php' ) {
            WCML_Resources::load_management_css();
	        WCML_Resources::load_taxonomy_translation_scripts();
        }
        
    }
            
    function save_wc_term_meta($original_tax, $result){


        // WooCommerce before termmeta table migration
        $wc_before_term_meta = get_option( 'db_version' ) < 34370;

        // backwards compatibility - before the termmeta table was added
        if( $wc_before_term_meta ){

            $term_wc_meta = $this->wpdb->get_results($this->wpdb->prepare("SELECT * FROM {$this->wpdb->woocommerce_termmeta} WHERE woocommerce_term_id=%s", $original_tax->term_id));
            foreach ( $term_wc_meta as $wc_meta ){
                $wc_original_metakey = $wc_meta->meta_key;
                $wc_original_metavalue = $wc_meta->meta_value;
                update_woocommerce_term_meta($result['term_id'], $wc_original_metakey, $wc_original_metavalue);
            }
        // End of backwards compatibility - before the termmeta table was added
        }else{

            $term_wc_meta = get_term_meta( $original_tax->term_id, false, true );
            foreach ( $term_wc_meta as $key => $values ) {
                update_term_meta( $result['term_id'], $key, maybe_unserialize( array_pop( $values ) ) );
            }

        }
    }

    function show_term_translation_screen_notices(){
 
        $taxonomies = array_keys(get_taxonomies(array('object_type'=>array('product')),'objects'));
        $taxonomies = $taxonomies + array_keys(get_taxonomies(array('object_type'=>array('product_variations')),'objects'));
        $taxonomies = array_unique($taxonomies);
        $taxonomy = isset($_GET['taxonomy']) ? $_GET['taxonomy'] : false;
        if( $taxonomy && in_array( $taxonomy, $taxonomies ) ){
            $taxonomy_obj = get_taxonomy($taxonomy);
            $message = sprintf(__('To translate %s please use the %s translation%s page, inside the %sWooCommerce Multilingual admin%s.', 'woocommerce-multilingual'),
            $taxonomy_obj->labels->name,
            '<strong><a href="' . admin_url('admin.php?page=wpml-wcml&tab=' . $taxonomy ) . '">' . $taxonomy_obj->labels->singular_name,  '</a></strong>',
                '<strong><a href="' . admin_url('admin.php?page=wpml-wcml">'), '</a></strong>');

            echo '<div class="updated"><p>' . $message . '</p></div>';
        }
        
    } 
    
    function sync_term_order_globally() {
        //syncs the term order of any taxonomy in $this->wpdb->prefix.'woocommerce_attribute_taxonomies'
        //use it when term orderings have become unsynched, e.g. before WCML 3.3.

        if(!defined('WOOCOMMERCE_VERSION')){
            return;
        }

        $cur_lang = $this->sitepress->get_current_language();
        $lang = $this->sitepress->get_default_language();
        $this->sitepress->switch_lang($lang);

        $taxes = wc_get_attribute_taxonomies ();

        if ($taxes) foreach ($taxes as $woo_tax) {
            $tax = 'pa_'.$woo_tax->attribute_name;
            $meta_key = 'order_'.$tax;
            //if ($tax != 'pa_frame') continue;
            $terms = get_terms($tax);
            if ($terms)foreach ($terms as $term) {
                $term_order = get_woocommerce_term_meta($term->term_id,$meta_key);
                $trid = $this->sitepress->get_element_trid($term->term_taxonomy_id,'tax_'.$tax);
                $translations = $this->sitepress->get_element_translations($trid,'tax_' . $tax);
                if ($translations) foreach ($translations as $trans) {
                    if ($trans->language_code != $lang) {
                        update_woocommerce_term_meta( $trans->term_id, $meta_key, $term_order);
                    }
                }
            }
        }
        
        //sync product categories ordering
        $terms = get_terms('product_cat');
        if ($terms) foreach($terms as $term) {
            $term_order = get_woocommerce_term_meta($term->term_id,'order');
            $trid = $this->sitepress->get_element_trid($term->term_taxonomy_id,'tax_product_cat');
            $translations = $this->sitepress->get_element_translations($trid,'tax_product_cat');
            if ($translations) foreach ($translations as $trans) {
                if ($trans->language_code != $lang) {
                    update_woocommerce_term_meta( $trans->term_id, 'order', $term_order);
                }
            }
        }

        $this->sitepress->switch_lang($cur_lang);
        
        $this->woocommerce_wpml->settings['is_term_order_synced'] = 'yes';
        $this->woocommerce_wpml->update_settings();
        
    }    
    
    function sync_term_order($meta_id, $object_id, $meta_key, $meta_value) {

        // WooCommerce before termmeta table migration
        $wc_before_term_meta = get_option( 'db_version' ) < 34370;

        if (!isset($_POST['thetaxonomy']) || !taxonomy_exists($_POST['thetaxonomy']) || substr($meta_key,0,5) != 'order') 
            return;

        $tax = filter_input( INPUT_POST, 'thetaxonomy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        
        $term_taxonomy_id = $this->wpdb->get_var($this->wpdb->prepare("SELECT term_taxonomy_id FROM {$this->wpdb->term_taxonomy} WHERE term_id=%d AND taxonomy=%s", $object_id, $tax));
        $trid = $this->sitepress->get_element_trid($term_taxonomy_id, 'tax_' . $tax);
        $translations = $this->sitepress->get_element_translations($trid,'tax_' . $tax);
        if ($translations) foreach ($translations as $trans) {
            if ($trans->element_id != $term_taxonomy_id) {

                // Backwards compatibility - WooCommerce termmeta table
                if( $wc_before_term_meta ) {
                    $this->wpdb->update( $this->wpdb->prefix . 'woocommerce_termmeta',
                        array('meta_value' => $meta_value),
                        array('woocommerce_term_id' => $trans->term_id, 'meta_key' => $meta_key) );
                // END Backwards compatibility - WooCommerce termmeta table
                } else{
                    update_term_meta( $trans->term_id, $meta_key, $meta_value);
                }

            }
        }
        
    }
    
    function translated_terms_status_update($term_id, $tt_id, $taxonomy){

        if ( isset( $_POST['product_cat_thumbnail_id'] ) || isset( $_POST['display_type'] ) ){
            global $sitepress_settings;

            if( $this->is_original_category($tt_id,'tax_'.$taxonomy) ){
                $trid = $this->sitepress->get_element_trid($tt_id,'tax_'.$taxonomy);
                $translations = $this->sitepress->get_element_translations($trid,'tax_'.$taxonomy);
                
                foreach($translations as $translation){
                    if(!$translation->original){
                        if(isset($_POST['display_type'])){
                            update_woocommerce_term_meta( $translation->term_id, 'display_type', esc_attr( $_POST['display_type'] ) );
                        }
                        update_woocommerce_term_meta( $translation->term_id, 'thumbnail_id', apply_filters( 'translate_object_id',esc_attr( $_POST['product_cat_thumbnail_id'] ),'attachment',true,$translation->language_code));
                    }
                }
            }
        }

        global $wp_taxonomies;
        if(in_array('product', $wp_taxonomies[$taxonomy]->object_type) || in_array('product_variation', $wp_taxonomies[$taxonomy]->object_type)){
            $this->update_terms_translated_status($taxonomy);
        }

    }

    function is_original_category( $tt_id, $taxonomy ){
        $is_original = $this->wpdb->get_var($this->wpdb->prepare("SELECT source_language_code IS NULL FROM {$this->wpdb->prefix}icl_translations WHERE element_id=%d AND element_type=%s", $tt_id, $taxonomy ));
        return $is_original ? true : false;
    }
    
    public function wcml_update_term_translated_warnings(){
        $ret = array();

        $taxonomy = filter_input( INPUT_POST, 'taxonomy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

        $wcml_settings = $this->woocommerce_wpml->get_settings();

        $attribute_taxonomies = $this->woocommerce_wpml->attributes->get_translatable_attributes();

        $attribute_taxonomies_arr = array();
        foreach($attribute_taxonomies as $a){
            $attribute_taxonomies_arr[] = 'pa_' . $a->attribute_name;
        }

        $ret['is_attribute'] = intval( in_array( $taxonomy, $attribute_taxonomies_arr ) );

        if( isset( $wcml_settings['untranstaled_terms'][$taxonomy] ) &&
            (
                $wcml_settings['untranstaled_terms'][$taxonomy]['status'] == $this->ALL_TAXONOMY_TERMS_TRANSLATED ||
                $wcml_settings['untranstaled_terms'][$taxonomy]['status'] == $this->NEW_TAXONOMY_IGNORED
            )
        ){

            $ret['hide'] = 1;
        }else{

            $ret['hide'] = 0;

            if( isset( $wcml_settings[ 'sync_'.$taxonomy ]) ){
                $ret[ 'show_button' ] = $wcml_settings[ 'sync_'.$taxonomy ];
            }elseif( in_array( $taxonomy, $attribute_taxonomies_arr ) ) {
                $ret[ 'show_button' ] = $wcml_settings[ 'sync_variations' ];
            }
        }

        if( $ret[ 'is_attribute' ] ){
            $ret['hide'] = $this->woocommerce_wpml->attributes->is_attributes_fully_translated();
        }

        echo json_encode($ret);
        exit;
        
    }
    
    public function update_terms_translated_status($taxonomy){
        
        $wcml_settings = $this->woocommerce_wpml->get_settings();
        $is_translatable= 1;
        $not_translated_count = 0;
        $original_terms = array();

        if( isset( $wcml_settings[ 'attributes_settings' ][ $taxonomy ] ) && !$wcml_settings[ 'attributes_settings' ][ $taxonomy ] ){
            $is_translatable = 0;
        }

        if( $is_translatable ){

            $active_languages = $this->sitepress->get_active_languages();

            foreach($active_languages as $language){
                $terms = $this->wpdb->get_results($this->wpdb->prepare("
                    SELECT t1.element_id AS e1, t2.element_id AS e2 FROM {$this->wpdb->term_taxonomy} x
                    JOIN {$this->wpdb->prefix}icl_translations t1 ON x.term_taxonomy_id = t1.element_id AND t1.element_type = %s AND t1.source_language_code IS NULL
                    LEFT JOIN {$this->wpdb->prefix}icl_translations t2 ON t2.trid = t1.trid AND t2.language_code = %s
                ", 'tax_' . $taxonomy, $language['code']));
                foreach($terms as $term){
                    if( empty( $term->e2 ) && !in_array( $term->e1, $original_terms ) ){
                        $original_terms[] = $term->e1;
                        $not_translated_count ++;
                    }
                }
            }
        }

        $status = $not_translated_count ? $this->NEW_TAXONOMY_TERMS : $this->ALL_TAXONOMY_TERMS_TRANSLATED;
        
        if( isset( $wcml_settings[ 'untranstaled_terms' ][ $taxonomy ] ) && $wcml_settings[ 'untranstaled_terms' ][ $taxonomy ] === $this->NEW_TAXONOMY_IGNORED ){
            $status = $this->NEW_TAXONOMY_IGNORED;
        }
        
        $wcml_settings[ 'untranstaled_terms' ][ $taxonomy ] = array( 'count' => $not_translated_count , 'status' => $status );
                
        $this->woocommerce_wpml->update_settings( $wcml_settings );

        return $wcml_settings[ 'untranstaled_terms' ][ $taxonomy ];
        
    }
    
    public function is_fully_translated($taxonomy){
        
        $wcml_settings = $this->woocommerce_wpml->get_settings();
        
        $return = true;

        if( !isset($wcml_settings['untranstaled_terms'][$taxonomy]) ){
            $wcml_settings['untranstaled_terms'][$taxonomy] = $this->update_terms_translated_status($taxonomy);
        }

        if($wcml_settings['untranstaled_terms'][$taxonomy]['status'] == $this->NEW_TAXONOMY_TERMS){
            $return = false;
        }
        
        
        return $return;
    }
    
    public function get_untranslated_terms_number( $taxonomy, $force_update = false ){
        
        $wcml_settings = $this->woocommerce_wpml->get_settings();

        if( $force_update || !isset($wcml_settings['untranstaled_terms'][$taxonomy] ) ){
            $wcml_settings['untranstaled_terms'][$taxonomy] = $this->update_terms_translated_status($taxonomy);
        }
        
        return $wcml_settings['untranstaled_terms'][$taxonomy]['count'];
        
    }
    
    public function set_flag_for_variation_on_attribute_update($term_id, $tt_id, $taxonomy){

        $attribute_taxonomies = wc_get_attribute_taxonomies();        
        foreach($attribute_taxonomies as $a){
            $attribute_taxonomies_arr[] = 'pa_' . $a->attribute_name;
        }

        if(isset( $attribute_taxonomies_arr ) && in_array($taxonomy, $attribute_taxonomies_arr)){

				$wcml_settings = $this->woocommerce_wpml->get_settings();

				// get term language
				$term_language = $this->sitepress->get_element_language_details($tt_id, 'tax_' . $taxonomy);

				if( isset( $term_language->language_code ) && $term_language->language_code != $this->sitepress->get_default_language()){
					// get term in the default language
					$term_id = apply_filters( 'translate_object_id',$term_id, $taxonomy, false, $this->sitepress->get_default_language());

					//does it belong to any posts (variations)
					$objects = get_objects_in_term($term_id, $taxonomy);

					if(!isset($wcml_settings['variations_needed'][$taxonomy])){
						$wcml_settings['variations_needed'][$taxonomy] = 0;
					}
					$wcml_settings['variations_needed'][$taxonomy] += count($objects);

					$this->woocommerce_wpml->update_settings($wcml_settings);

				}
		}
        
    }

    public function sync_taxonomy_translations( $html, $taxonomy, $taxonomy_obj ){

        $is_wcml = is_admin() && $taxonomy && isset($_GET['page']) && $_GET['page'] == 'wpml-wcml' && isset($_GET['tab']);
        $is_ajax = is_ajax() && $taxonomy && isset( $_POST['action'] ) && $_POST['action'] === 'wpml_get_terms_and_labels_for_taxonomy_table';

        if( $is_wcml || $is_ajax ){

            $sync_tax = new WCML_Sync_Taxonomy( $this->woocommerce_wpml, $taxonomy, $taxonomy_obj );
            $html = $sync_tax->get_view();
        }

        return $html;
    }
    
    public function wcml_sync_product_variations($taxonomy){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'wcml_sync_product_variations')){
            die('Invalid nonce');
        }
        
        $VARIATIONS_THRESHOLD = 20;
        
        $wcml_settings = $this->woocommerce_wpml->get_settings();
        $response = array();
        
        $taxonomy = filter_input( INPUT_POST, 'taxonomy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        
        $languages_processed = intval( $_POST['languages_processed']);

        $condition = $languages_processed?'>=':'>';

        $where = isset($_POST['last_post_id']) && $_POST['last_post_id'] ? ' ID '.$condition.' ' . intval($_POST['last_post_id']) . ' AND ' : '';
        
        $post_ids = $this->wpdb->get_col($this->wpdb->prepare("                
                SELECT DISTINCT tr.object_id 
                FROM {$this->wpdb->term_relationships} tr
                JOIN {$this->wpdb->term_taxonomy} tx on tr.term_taxonomy_id = tx.term_taxonomy_id
                JOIN {$this->wpdb->posts} p ON tr.object_id = p.ID
                JOIN {$this->wpdb->prefix}icl_translations t ON t.element_id = p.ID 
                WHERE {$where} tx.taxonomy = %s AND p.post_type = 'product' AND t.element_type='post_product' AND t.language_code = %s 
                ORDER BY ID ASC
                
        ", $taxonomy, $this->sitepress->get_default_language()));

        if($post_ids){
            
            $variations_processed = 0;
            $posts_processed = 0;
            foreach($post_ids as $post_id){
                $terms = wp_get_post_terms($post_id, $taxonomy);    
                $terms_count = count($terms);
                
                $trid = $this->sitepress->get_element_trid($post_id, 'post_product');
                $translations = $this->sitepress->get_element_translations($trid, 'post_product');
                
                $i = 1;

                foreach($translations as $translation){

                    if($i > $languages_processed && $translation->element_id != $post_id){
                        $this->woocommerce_wpml->sync_product_data->sync_product_taxonomies($post_id, $translation->element_id, $translation->language_code);
                        $this->woocommerce_wpml->sync_variations_data->sync_product_variations($post_id, $translation->element_id, $translation->language_code, false, true);
                        $this->woocommerce_wpml->translation_editor->create_product_translation_package($post_id,$trid, $translation->language_code,ICL_TM_COMPLETE);
                        $variations_processed += $terms_count*2;
                        $response['languages_processed'] = $i;
                        $i++;
                        //check if sum of 2 iterations doesn't exceed $VARIATIONS_THRESHOLD
                        if($variations_processed >= $VARIATIONS_THRESHOLD){                    
                            break;
                        }
                    }else{
                        $i++;
                    }
                }
                $response['last_post_id'] = $post_id;
                if(--$i == count($translations)){
                    $response['languages_processed'] = 0;
                    $languages_processed = 0;
                }else{
                    break;
                }
                
                $posts_processed ++;
                
            }

            $response['go'] = 1;
            
        }else{
            
            $response['go'] = 0;
            
        }
        
        $response['progress']   = $response['go'] ? sprintf(__('%d products left', 'woocommerce-multilingual'), count($post_ids) - $posts_processed) : __('Synchronization complete!', 'woocommerce-multilingual');
        
        if($response['go'] && isset($wcml_settings['variations_needed'][$taxonomy]) && !empty($variations_processed)){
            $wcml_settings['variations_needed'][$taxonomy] = max($wcml_settings['variations_needed'][$taxonomy] - $variations_processed, 0);            
        }else{
            if($response['go'] == 0){
                $wcml_settings['variations_needed'][$taxonomy] = 0;    
            }            
        }
        $wcml_settings['sync_variations'] = 0;

        $this->woocommerce_wpml->update_settings($wcml_settings);                       
        
        echo json_encode($response);
        exit;
    }

    public function wcml_sync_taxonomies_in_content_preview(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'wcml_sync_taxonomies_in_content_preview')){
            die('Invalid nonce');
        }

        global $wp_taxonomies;

        $html = $message = $errors = '';


        if(isset($wp_taxonomies[$_POST['taxonomy']])){
            $object_types = $wp_taxonomies[$_POST['taxonomy']]->object_type;

            foreach($object_types as $object_type){

                $html .= $this->render_assignment_status($object_type, $_POST['taxonomy'], $preview = true);

            }

        }else{
            $errors = sprintf(__('Invalid taxonomy %s', 'woocommerce-multilingual'), $_POST['taxonomy']);
        }


        echo json_encode(array('html' => $html, 'message'=> $message, 'errors' => $errors));
        exit;
    }

    public function wcml_sync_taxonomies_in_content(){
        $nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
        if(!$nonce || !wp_verify_nonce($nonce, 'wcml_sync_taxonomies_in_content')){
            die('Invalid nonce');
        }

        global $wp_taxonomies;

        $html = $message = $errors = '';

        if(isset($wp_taxonomies[$_POST['taxonomy']])){
            $html .= $this->render_assignment_status($_POST['post'], $_POST['taxonomy'], $preview = false);

        }else{
            $errors .= sprintf(__('Invalid taxonomy %s', 'woocommerce-multilingual'), $_POST['taxonomy']);
        }


        echo json_encode(array('html' => $html, 'errors' => $errors));
        exit;
    }

    public function render_assignment_status($object_type, $taxonomy, $preview = true){
        global $wp_post_types, $wp_taxonomies;

        $default_language = $this->sitepress->get_default_language();
	    $is_taxonomy_translatable = $this->is_translatable_wc_taxonomy( $taxonomy );

        $posts = $this->wpdb->get_results($this->wpdb->prepare( "SELECT * FROM {$this->wpdb->posts} AS p LEFT JOIN {$this->wpdb->prefix}icl_translations AS tr ON tr.element_id = p.ID WHERE p.post_status = 'publish' AND p.post_type = %s AND tr.source_language_code is NULL", $object_type ) );

        foreach($posts as $post){

            $terms = wp_get_post_terms($post->ID, $taxonomy);

            $term_ids = array();
            foreach($terms as $term){
                $term_ids[] = $term->term_id;
            }

            $trid = $this->sitepress->get_element_trid($post->ID, 'post_' . $post->post_type);
            $translations = $this->sitepress->get_element_translations($trid, 'post_' . $post->post_type, true, true);

            foreach($translations as $language => $translation){

                if($language != $default_language && $translation->element_id){

                    $terms_of_translation =  wp_get_post_terms($translation->element_id, $taxonomy);

                    $translation_term_ids = array();
                    foreach($terms_of_translation as $term){

                        $term_id_original = apply_filters( 'translate_object_id',$term->term_id, $taxonomy, false, $default_language );
                        if(!$term_id_original || !in_array($term_id_original, $term_ids)){
                            // remove term

                            if($preview){
                                $needs_sync = true;
                                break(3);
                            }

                            $current_terms = wp_get_post_terms($translation->element_id, $taxonomy);
                            $updated_terms = array();
                            foreach($current_terms as $cterm){
                                if($cterm->term_id != $term->term_id){
                                    $updated_terms[] = $is_taxonomy_translatable ? $term->term_id : $term->name;
                                }
                                if(!$preview){

                                    if( $is_taxonomy_translatable && !is_taxonomy_hierarchical($taxonomy)){
                                        $updated_terms = array_unique( array_map( 'intval', $updated_terms ) );
                                    }

                                    wp_set_post_terms($translation->element_id, $updated_terms, $taxonomy);
                                }

                            }

                        }else{
                            $translation_term_ids[] = $term_id_original;
                        }

                    }

                    foreach($term_ids as $term_id){

                        if(!in_array($term_id, $translation_term_ids)){
                            // add term

                            if($preview){
                                $needs_sync = true;
                                break(3);
                            }
                            $terms_array = array();
                            $term_id_translated = apply_filters( 'translate_object_id',$term_id, $taxonomy, false, $language);

                            // not using get_term
                            $translated_term = $this->wpdb->get_row($this->wpdb->prepare("
                            SELECT * FROM {$this->wpdb->terms} t JOIN {$this->wpdb->term_taxonomy} x ON x.term_id = t.term_id WHERE t.term_id = %d AND x.taxonomy = %s", $term_id_translated, $taxonomy));

                            if( $translated_term ){
                                $terms_array[] = $translated_term->term_id;
                            }

                            if(!$preview){

                                if( $is_taxonomy_translatable && !is_taxonomy_hierarchical($taxonomy)){
                                    $terms_array = array_unique( array_map( 'intval', $terms_array ) );
                                }

                                wp_set_post_terms($translation->element_id, $terms_array, $taxonomy, true);
                            }

                        }

                    }

                }

            }

        }

        $wcml_settings = $this->woocommerce_wpml->get_settings();
        $wcml_settings['sync_'.$taxonomy] = 0;
        $this->woocommerce_wpml->update_settings($wcml_settings);

        $out = '';

        if($preview){

            $out .= '<div class="wcml_tt_sync_row">';
            if(!empty($needs_sync)){
                $out .= '<form class="wcml_tt_do_sync">';
                $out .= '<input type="hidden" name="post" value="' . $object_type . '" />';
                $out .= wp_nonce_field('wcml_sync_taxonomies_in_content', 'wcml_sync_taxonomies_in_content_nonce',true,false);
                $out .= '<input type="hidden" name="taxonomy" value="' . $taxonomy . '" />';
                $out .= sprintf(__('Some translated %s have different %s assignments.', 'woocommerce-multilingual'),
                    '<strong>' . mb_strtolower($wp_post_types[$object_type]->labels->name) . '</strong>',
                    '<strong>' . mb_strtolower($wp_taxonomies[$taxonomy]->labels->name) . '</strong>');
                $out .= '&nbsp;<a class="submit button-secondary" href="#">' . sprintf(__('Update %s for all translated %s', 'woocommerce-multilingual'),
                        '<strong>' . mb_strtolower($wp_taxonomies[$taxonomy]->labels->name) . '</strong>',
                        '<strong>' . mb_strtolower($wp_post_types[$object_type]->labels->name) . '</strong>') . '</a>' .
                    '&nbsp;<img src="'. ICL_PLUGIN_URL . '/res/img/ajax-loader.gif" alt="loading" height="16" width="16" class="wcml_tt_spinner" />';
                $out .= "</form>";
            }else{
                $out .= sprintf(__('All %s have the same %s assignments.', 'woocommerce-multilingual'),
                    '<strong>' . mb_strtolower($wp_taxonomies[$taxonomy]->labels->name) . '</strong>',
                    '<strong>' . mb_strtolower($wp_post_types[$object_type]->labels->name) . '</strong>');
            }
            $out .= "</div>";

        }else{

            $out .= sprintf(__('Successfully updated %s for all translated %s.', 'woocommerce-multilingual'), $wp_taxonomies[$taxonomy]->labels->name, $wp_post_types[$object_type]->labels->name);

        }

        return $out;
    }

    function shipping_terms($terms, $post_id, $taxonomy){
        global $pagenow;

        if( isset( $_POST['action'] ) && $_POST['action'] == 'woocommerce_load_variations' ){
            return $terms;
        }

        if( $pagenow != 'post.php' && ( get_post_type($post_id) == 'product' || get_post_type($post_id) == 'product_variation' ) && $taxonomy == 'product_shipping_class'){

            remove_filter('get_the_terms',array($this,'shipping_terms'), 10, 3);
            $terms = get_the_terms( apply_filters( 'translate_object_id', $post_id, get_post_type($post_id), true, $this->sitepress->get_current_language() ),'product_shipping_class');
            add_filter('get_the_terms',array($this,'shipping_terms'), 10, 3);
            return $terms;
        }

        return $terms;
    }

    function filter_shipping_classes_terms( $terms, $taxonomies, $args ){

        $on_wc_settings_page = isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] === 'wc-settings';
        $on_shipping_tab = isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] === 'shipping';
        $on_classes_section = isset( $_GET[ 'section' ] ) && $_GET[ 'section' ] === 'classes';

        if( is_admin() && in_array( 'product_shipping_class', (array) $taxonomies ) && $on_wc_settings_page && $on_shipping_tab && !$on_classes_section ){
            remove_filter('get_terms',array($this,'filter_shipping_classes_terms'));
            $current_language = $this->sitepress->get_current_language();
            $this->sitepress->switch_lang($this->sitepress->get_default_language());
            add_filter( 'get_terms', array( 'WPML_Terms_Translations', 'get_terms_filter' ), 10, 2 );
            $terms = get_terms( $taxonomies, $args );
            remove_filter( 'get_terms', array( 'WPML_Terms_Translations', 'get_terms_filter' ), 10, 2 );
            add_filter( 'get_terms', array( $this, 'filter_shipping_classes_terms' ), 10, 3 );
            $this->sitepress->switch_lang($current_language);
        }

        return $terms;
    }

    function wcml_delete_term($term, $tt_id, $taxonomy, $deleted_term){
        global $wp_taxonomies;

        foreach($wp_taxonomies as $key=>$taxonomy_obj){
            if((in_array('product',$taxonomy_obj->object_type) || in_array('product_variation',$taxonomy_obj->object_type) ) && $key==$taxonomy){
                $this->update_terms_translated_status($taxonomy);
                break;
            }
        }

    }

	function get_product_terms_filter( $terms, $product_id, $taxonomy, $args ) {

		$language = $this->sitepress->get_language_for_element( $product_id, 'post_' . get_post_type( $product_id ) );

		$is_objects_array = is_object( current( $terms ) );

		$filtered_terms = array();

		foreach ( $terms as $term ) {

			if ( ! $is_objects_array ) {
				$term_obj = get_term_by( 'name', $term, $taxonomy );

				$is_wc_filtering_by_slug = isset( $args['fields'] ) && 'id=>slug' === $args['fields'];
				if ( $is_wc_filtering_by_slug || ! $term_obj ) {
					$term_obj = get_term_by( 'slug', $term, $taxonomy );
					$is_slug  = true;
				}
			}

			if ( empty( $term_obj ) ) {
				$filtered_terms[] = $term;
				continue;
			}

			$trnsl_term_id = apply_filters( 'translate_object_id', $term_obj->term_id, $taxonomy, true, $language );

			if ( $is_objects_array ) {
				$filtered_terms[] = get_term( $trnsl_term_id, $taxonomy );
			} else {
				if ( isset( $is_slug ) ) {
					$filtered_terms[] = get_term( $trnsl_term_id, $taxonomy )->slug;
				} else {
					$filtered_terms[] = ( is_ajax() && isset( $_POST['action'] ) && in_array( $_POST['action'], array(
							'woocommerce_add_variation',
							'woocommerce_link_all_variations'
						) ) ) ? strtolower( get_term( $trnsl_term_id, $taxonomy )->name ) : get_term( $trnsl_term_id, $taxonomy )->name;
				}
			}
		}

		return $filtered_terms;
	}

	function set_flag_to_sync( $taxonomy, $el_id, $language_code ) {
		if ( $el_id ) {
			$elem_details = $this->sitepress->get_element_language_details( $el_id, 'tax_' . $taxonomy );
			if ( null !== $elem_details->source_language_code ) {
				$this->check_if_sync_term_translation_needed( $el_id, $taxonomy );
			}
		}
	}

    function check_if_sync_terms_needed(){

        $wcml_settings = $this->woocommerce_wpml->get_settings();
        $wcml_settings['sync_variations'] = 0;
        $wcml_settings['sync_product_cat'] = 0;
        $wcml_settings['sync_product_tag'] = 0;
        $wcml_settings['sync_product_shipping_class'] = 0;
        $this->woocommerce_wpml->update_settings( $wcml_settings );

        $taxonomies_to_check = array( 'product_cat', 'product_tag', 'product_shipping_class' );

        foreach( $taxonomies_to_check as $check_taxonomy ){
            $terms = get_terms( $check_taxonomy, array( 'hide_empty' => false, 'fields' => 'ids' ) );
            if (is_array($terms)){
                foreach( $terms as $term ){
                    if( $this->check_if_sync_term_translation_needed( $term[ 'term_taxonomy_id' ], $check_taxonomy ) ){
                        break;
                    }
                }
            }
        }

        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $flag_set = false;
        foreach( $attribute_taxonomies as $a ){

            $terms = get_terms( 'pa_' . $a->attribute_name, array( 'hide_empty' => false, 'fields' => 'ids' ) );
            if (is_array($terms)){
                foreach( $terms as $term ){
                    $flag_set = $this->check_if_sync_term_translation_needed( $term[ 'term_taxonomy_id' ], 'pa_' . $a->attribute_name );
                    if( $flag_set ){
                        break;
                    }
                }
            }

            if( $flag_set ){
                break;
            }
        }

    }

    function check_if_sync_term_translation_needed( $t_id, $taxonomy ){

        $wcml_settings = $this->woocommerce_wpml->get_settings();

        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $attribute_taxonomies_arr = array();
        foreach($attribute_taxonomies as $a){
            $attribute_taxonomies_arr[] = 'pa_' . $a->attribute_name;
        }

        if( ( isset( $wcml_settings[ 'sync_'.$taxonomy ]) && $wcml_settings[ 'sync_'.$taxonomy ] ) || ( in_array( $taxonomy, $attribute_taxonomies_arr ) && isset( $wcml_settings[ 'sync_variations' ]) && $wcml_settings['sync_variations'] ) ){
            return true;
        }

        $translations = $this->wpdb->get_results( $this->wpdb->prepare( "SELECT t2.element_id, t2.source_language_code FROM {$this->wpdb->prefix}icl_translations AS t1 LEFT JOIN {$this->wpdb->prefix}icl_translations AS t2 ON t1.trid = t2.trid WHERE t1.element_id = %d AND t1.element_type = %s ", $t_id, 'tax_'.$taxonomy ) );

        foreach( $translations as $key => $translation ){
            if ( is_null( $translation->source_language_code ) ) {
                $original_count = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT count( object_id ) FROM {$this->wpdb->term_relationships} WHERE term_taxonomy_id = %d ", $translation->element_id ) );
                unset( $translations[ $key ] );
            }
        }

        foreach( $translations as $translation ){

            $count = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT count( object_id ) FROM {$this->wpdb->term_relationships} WHERE term_taxonomy_id = %d ", $translation->element_id ) );
            if( $original_count != $count ){

                if( in_array( $taxonomy, array( 'product_cat', 'product_tag', 'product_shipping_class' ) ) ){
                    $wcml_settings[ 'sync_'.$taxonomy ] = 1;
                    $this->woocommerce_wpml->update_settings($wcml_settings);
                    return true;
                }

                if( isset( $attribute_taxonomies_arr ) && in_array( $taxonomy, $attribute_taxonomies_arr ) ){
                    $wcml_settings['sync_variations'] = 1;
                    $this->woocommerce_wpml->update_settings($wcml_settings);
                    return true;
                }

            }

        }

    }

    function get_table_taxonomies( $taxonomies ){

        foreach( $taxonomies as $key => $taxonomy ){
            if (substr($key, 0, 3) != 'pa_') {
                unset( $taxonomies[$key]);
            }
        }

        return $taxonomies;
    }

    function get_wc_taxonomies(){

        global $wp_taxonomies;
        $taxonomies = array();

        //don't use get_taxonomies for product, because when one more post type registered for product taxonomy functions returned taxonomies only for product type
        foreach ( $wp_taxonomies as $key => $taxonomy ) {

            if (
                ( in_array( 'product', $taxonomy->object_type ) || in_array( 'product_variation', $taxonomy->object_type ) ) &&
                ! in_array( $key, $taxonomies )
            ) {

                if( substr( $key, 0, 3 ) == 'pa_' && !$this->woocommerce_wpml->attributes->is_translatable_attribute( $key ) ){
                    continue;
                }

                $taxonomies[] = $key;
            }
        }

        return $taxonomies;

    }

    function has_wc_taxonomies_to_translate(){

        $taxonomies = $this->get_wc_taxonomies();

        $no_tax_to_trnls = false;
        foreach ( $taxonomies as $taxonomy ){

        	$is_fully_translated = 0 === $this->get_untranslated_terms_number( $taxonomy );
	        if (
		        ! $this->is_translatable_wc_taxonomy( $taxonomy ) ||
		        $is_fully_translated
	        ) {
		        continue;
	        } else {
		        $no_tax_to_trnls = true;
	        }
        }

        return $no_tax_to_trnls;

    }

    /*
    * Use custom query, because get_term_by function return false for terms with "0" slug      *
    */
    public function wcml_get_term_id_by_slug( $taxonomy, $slug ){

        return $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT tt.term_id FROM {$this->wpdb->terms} AS t
                        INNER JOIN {$this->wpdb->term_taxonomy} AS tt
                        ON t.term_id = tt.term_id
                        WHERE tt.taxonomy = %s AND t.slug = %s LIMIT 1",
                $taxonomy, sanitize_title( $slug ) )
        );
    }

    public function wcml_get_term_by_id( $term_id, $taxonomy ){

        return $this->wpdb->get_row(
            $this->wpdb->prepare("
                        SELECT * FROM {$this->wpdb->terms} t
                        JOIN {$this->wpdb->term_taxonomy} x
                        ON x.term_id = t.term_id
                        WHERE t.term_id = %d AND x.taxonomy = %s",
                $term_id, $taxonomy )
        );
    }

    public function wcml_get_translated_term( $term_id, $taxonomy, $language ){

        $tr_id = apply_filters( 'translate_object_id', $term_id, $taxonomy, false, $language );

        if( !is_null( $tr_id ) ) {
            $term_id = $tr_id;
        }

        return $this->wcml_get_term_by_id( $term_id, $taxonomy );
    }

	public function is_translatable_wc_taxonomy( $taxonomy ) {
		if ( in_array( $taxonomy, array( 'product_type', 'product_visibility' ), true ) ) {
			return false;
		}

		return true;
	}

	function pre_option_default_product_cat( ) {

		$lang = $this->sitepress->get_current_language();

		$lang          = $lang === 'all' ? $this->sitepress->get_default_language() : $lang;
		$wcml_settings = $this->woocommerce_wpml->get_settings();
		$ttid          = isset( $wcml_settings['default_categories'][ $lang ] ) ? (int) $wcml_settings['default_categories'][ $lang ] : 0;

		return $ttid === 0
			? false : $this->wpdb->get_var(
				$this->wpdb->prepare(
					"SELECT term_id
		                     FROM {$this->wpdb->term_taxonomy}
		                     WHERE term_taxonomy_id= %d
		                     AND taxonomy='product_cat'",
					$ttid
				)
			);
	}

	function update_option_default_product_cat( $oldvalue, $new_value ) {
		$new_value     = $this->wpdb->get_var( $this->wpdb->prepare( "SELECT term_taxonomy_id FROM {$this->wpdb->term_taxonomy} WHERE taxonomy='product_cat' AND term_id=%d", $new_value ) );
		$translations  = $this->sitepress->get_element_translations( $this->sitepress->get_element_trid( $new_value, 'tax_product_cat' ) );
		$wcml_settings = $this->woocommerce_wpml->get_settings();

		if ( ! empty( $translations ) ) {
			foreach ( $translations as $t ) {
				$wcml_settings['default_categories'][ $t->language_code ] = $t->element_id;
			}
			if ( isset( $wcml_settings ) ) {
				$this->woocommerce_wpml->update_settings( $wcml_settings );
			}
		}
	}

}
