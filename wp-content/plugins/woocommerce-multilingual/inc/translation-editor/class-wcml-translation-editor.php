<?php

class WCML_Translation_Editor{

    /** @var woocommerce_wpml */
    private $woocommerce_wpml;
	/** @var SitePress */
	private $sitepress;
	/** @var  wpdb */
	private $wpdb;

    public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress, wpdb $wpdb ) {

	    $this->woocommerce_wpml = $woocommerce_wpml;
	    $this->sitepress        = $sitepress;
	    $this->wpdb             = $wpdb;

    }

    public function add_hooks(){

        add_filter( 'wpml-translation-editor-fetch-job', array( $this, 'fetch_translation_job_for_editor' ), 10, 2 );
        add_filter( 'wpml-translation-editor-job-data', array( $this, 'get_translation_job_data_for_editor' ), 10, 2 );
        add_action( 'admin_print_scripts', array( $this, 'preselect_product_type_in_admin_screen' ), 11 );

        add_filter( 'icl_post_alternative_languages', array( $this, 'hide_post_translation_links' ) );

        add_filter( 'manage_product_posts_columns', array( $this, 'add_languages_column' ), 100 );
        add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'lock_variable_fields' ), 10 );

        if( is_admin() ){
            add_filter( 'wpml_use_tm_editor', array( $this, 'force_woocommerce_native_editor'), 100 );
            add_action( 'wpml_pre_status_icon_display', array( $this, 'force_remove_wpml_translation_editor_links'), 100 );
            add_action( 'wp_ajax_wcml_editor_auto_slug', array( $this, 'auto_generate_slug' ) );

            add_action('wpml_doc_translation_method_below', array( $this, 'wpml_translation_editor_override_notice') );

            add_filter( 'wpml_tm_show_page_builders_translation_editor_warning', array( $this, 'show_page_builders_translation_editor_warning' ), 10, 2 );
        }

    }

    public function fetch_translation_job_for_editor( $job, $job_details ) {

	    if ( $job_details['job_type'] === 'post_product' ) {
            $job = new WCML_Editor_UI_Product_Job( $job_details, $this->woocommerce_wpml, $this->sitepress, $this->wpdb );
        }

        return $job;
    }

    public function get_translation_job_data_for_editor( $job_data ) {
	    /** @var TranslationManagement $iclTranslationManagement */
        global $iclTranslationManagement;

        // See if it's a WooCommerce product.
        $job = $iclTranslationManagement->get_translation_job ( $job_data['job_id'] );
	    if ( $job && $job->original_post_type === 'post_product' ) {
            $job_data['job_type'] = 'wc_product';
            $job_data['job_id']   = $job->original_doc_id;
        }

        return $job_data;
    }

    public function preselect_product_type_in_admin_screen(){
        global $pagenow;
	    if ( 'post-new.php' === $pagenow && isset( $_GET['post_type'], $_GET['trid'] ) && $_GET['post_type'] === 'product'  ) {
                $translations = $this->sitepress->get_element_translations( $_GET[ 'trid' ], 'post_product_type' );
                foreach( $translations as $translation ){
                    if( $translation->original ) {
                        $source_lang = $translation->language_code;
                        break;
                    }
                }
                $terms = get_the_terms( $translations[ $source_lang ]->element_id, 'product_type' );
                echo '<script type="text/javascript">';
                echo PHP_EOL . '// <![CDATA[' . PHP_EOL;
                echo 'addLoadEvent(function(){'. PHP_EOL;
                echo "jQuery('#product-type option').removeAttr('selected');" . PHP_EOL;
                echo "jQuery('#product-type option[value=\"" . $terms[0]->slug . "\"]').attr('selected', 'selected');" . PHP_EOL;
                echo '});'. PHP_EOL;
                echo PHP_EOL . '// ]]>' . PHP_EOL;
                echo '</script>';
            }
    }

	/**
	 * Avoids the post translation links on the product post type.
	 *
	 * @param string $output
	 *
	 * @return string
	 */
    public function hide_post_translation_links($output){
        global $post;

	    if ( null === $post ){
            return $output;
        }

        $post_type = get_post_type( $post->ID );
        $checkout_page_id = get_option( 'woocommerce_checkout_page_id' );

	    if ( $post_type === 'product' || is_page( $checkout_page_id ) ){
            $output = '';
        }

        return $output;
    }

    public function create_product_translation_package( $product_id, $trid, $language, $status ) {
	    /** @var TranslationManagement $iclTranslationManagement */
	    global $iclTranslationManagement;
        //create translation package
        $translation_id = $this->wpdb->get_var( $this->wpdb->prepare("
                                SELECT translation_id FROM {$this->wpdb->prefix}icl_translations WHERE trid=%d AND language_code='%s'
                            ", $trid, $language));

        $md5 = $iclTranslationManagement->post_md5( get_post( $product_id ) );
        $translation_package = $iclTranslationManagement->create_translation_package( $product_id );

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        list( $rid, $update ) = $iclTranslationManagement->update_translation_status( array(
            'translation_id'        => $translation_id,
            'status'                => $status,
            'translator_id'         => $user_id,
            'needs_update'          => 0,
            'md5'                   => $md5,
            'translation_service'   => 'local',
            'translation_package'   => serialize( $translation_package )
        ));

        if( !$update ){
            $job_id = $iclTranslationManagement->add_translation_job($rid, $user_id , $translation_package );
        }

    }

    public function add_languages_column( $columns ){

	    if ( array_key_exists( 'icl_translations', $columns ) || ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) ){
            return $columns;
        }
        $active_languages = $this->sitepress->get_active_languages();

	    if ( count( $active_languages ) <= 1 || get_query_var( 'post_status' ) === 'trash' ) {
            return $columns;
        }
        $languages = array();

        foreach ( $active_languages as $v ) {
	        if ( $v['code'] === $this->sitepress->get_current_language() )
                continue;
            $languages[ ] = $v[ 'code' ];
        }
        $res = $this->wpdb->get_results( $this->wpdb->prepare ( "
			SELECT f.lang_code, f.flag, f.from_template, l.name
			FROM {$this->wpdb->prefix}icl_flags f
				JOIN {$this->wpdb->prefix}icl_languages_translations l ON f.lang_code = l.language_code
			WHERE l.display_language_code = %s AND f.lang_code IN (%s)
		", $this->sitepress->get_admin_language(), implode( "','", $languages ) ) );

        foreach ( $res as $r ) {
            if ( $r->from_template ) {
                $wp_upload_dir = wp_upload_dir();
                $flag_path     = $wp_upload_dir[ 'baseurl' ] . '/flags/';
            } else {
                $flag_path = ICL_PLUGIN_URL . '/res/flags/';
            }
            $flags[ $r->lang_code ] = '<img src="' . $flag_path . $r->flag . '" width="18" height="12" alt="' . $r->name . '" title="' . $r->name . '" />';
        }

        $flags_column = '';
        foreach ( $active_languages as $v ) {
            if ( isset( $flags[ $v[ 'code' ] ] ) )
                $flags_column .= $flags[ $v[ 'code' ] ];
        }

        $new_columns = array();
        $added = false;
        foreach ( $columns as $k => $v ) {
            $new_columns[ $k ] = $v;
	        if ( $k === 'name' ) {
                $new_columns[ 'icl_translations' ] = $flags_column;
                $added = true;
            }
        }

        if( !$added ){
            $new_columns[ 'icl_translations' ] = $flags_column;
        }

        return $new_columns;
    }

    public function lock_variable_fields( $loop ){

        $product_id = false;

	    if ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) === 'product' ){
            $product_id = $_GET[ 'post' ];
	    } elseif ( isset( $_POST['action'], $_POST['product_id'] ) && $_POST['action'] === 'woocommerce_load_variations' ){
            $product_id = $_POST[ 'product_id' ];
        }

        if( !$product_id ){
            return;
        }elseif( ! $this->woocommerce_wpml->products->is_original_product( $product_id ) && get_post_status( $product_id ) != 'auto-draft' ){

            $args = array(
                'post_type'      => 'product_variation',
                'post_status'    => array( 'private', 'publish' ),
                'posts_per_page' => ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10,
                'paged'          => ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1,
                'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
                'post_parent'    => $product_id,
            );

            $variations = get_posts( $args );
            $file_path_sync = array();

            if ( $variations ) {
                foreach ($variations as $variation) {
                    $variation_id = absint( $variation->ID );
                    $original_id = $this->woocommerce_wpml->products->get_original_product_id( $variation_id );
                    $custom_product_sync = get_post_meta( $original_id, 'wcml_sync_files', true );
                    if( $custom_product_sync && $custom_product_sync == 'self' ) {
                        $file_path_sync[ $variation_id ] = false;
                    }else{
                        $file_path_sync[ $variation_id ] = true;
                    }
                }
            }

            ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    wcml_lock_variation_fields( <?php echo json_encode( $file_path_sync ) ?> );
                });
            </script>
            <?php
        }
    }

    /**
     * Forces the translation editor to be used for products when enabled in WCML
     *
     * @param $use_tm_editor
     * @return int
     */
    public function force_woocommerce_native_editor( $use_tm_editor ){

        if( function_exists('get_current_screen') ) {

            $current_screen = get_current_screen();
            if ( !is_null( $current_screen ) ) {
                if ( $current_screen->id === 'edit-product' || $current_screen->id === 'product' ) {
                    $use_tm_editor = 1;
                    if ( !$this->woocommerce_wpml->settings['trnsl_interface'] ) {
                        $use_tm_editor = 0;
                    }
                } elseif ( $current_screen->id === 'woocommerce_page_wpml-wcml' ) {
                    $use_tm_editor = 1;
                }
            }
            
        }

        return $use_tm_editor;
    }

    /**
     * Removes the translation editor links when the WooCommerce native products editor is used in WCML
     */
    public function force_remove_wpml_translation_editor_links(){
        global $wpml_tm_status_display_filter;

        if( function_exists('get_current_screen') ) {
            $current_screen = get_current_screen();

            $is_edit_product = !is_null( $current_screen ) && ( $current_screen->id === 'edit-product' || $current_screen->id === 'product' );

            if ( $is_edit_product && !$this->woocommerce_wpml->settings['trnsl_interface'] ) {
                remove_filter( 'wpml_link_to_translation', array( $wpml_tm_status_display_filter, 'filter_status_link' ), 10 );
            }
        }

    }

    public function auto_generate_slug(){
        global $wpdb;

        $title = filter_input( INPUT_POST, 'title');

        $post_name = urldecode( sanitize_title( $title ) );
        $job_id    = intval( $_POST['job_id'] );

        $post_id_sql = "
            SELECT t.element_id
            FROM {$wpdb->prefix}icl_translations t
                LEFT JOIN {$wpdb->prefix}icl_translation_status s ON t.translation_id = s.translation_id
                LEFT JOIN {$wpdb->prefix}icl_translate_job j ON s.rid = j.rid
            WHERE j.job_id = %d
        ";
        $post_id = $wpdb->get_var( $wpdb->prepare( $post_id_sql, $job_id ) );

        if( $post_id ) {
            $slug = wp_unique_post_slug( $post_name, $post_id, 'publish', 'product', 0 );
        }else{
            // handle the special case when the post for this slug has not been created yet
            $lang_sql = "
                SELECT t.language_code
                FROM {$wpdb->prefix}icl_translations t
                    LEFT JOIN {$wpdb->prefix}icl_translation_status s ON t.translation_id = s.translation_id
                    LEFT JOIN {$wpdb->prefix}icl_translate_job j ON s.rid = j.rid
                WHERE j.job_id = %d
            ";
            $lang = $wpdb->get_var( $wpdb->prepare( $lang_sql, $job_id ) );

            $check_sql = "
                SELECT p.post_name 
                FROM {$wpdb->posts} p 
                    LEFT JOIN {$wpdb->prefix}icl_translations t 
                        ON t.element_id = p.ID AND t.element_type='post_product'   
                WHERE p.post_name = %s AND t.language_code = %s LIMIT 1
            ";
            $post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $post_name, $lang) );

            if( $post_name_check ) {
                $suffix = 2;
                do {

                    $alt_post_name = _truncate_post_slug( $post_name, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
                    $post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $alt_post_name, $lang ) );
                    $suffix++;

                } while ( $post_name_check );
                $slug = $alt_post_name;
            } else{
                $slug = $post_name;
            }

        }

        echo json_encode( array('slug' => $slug) );
        exit;

    }

    /**
     * Shows a note on the WPML Multilingual Setup screen explaining that WCML overrides those settings for products
     */
    public function wpml_translation_editor_override_notice(){

        ?>
        <p class="icl-admin-message" style="margin-top: 0">
            <?php printf( __('To configure how the %sWooCommerce products%s are translated, visit the WooCommerce Multiligual %ssettings%s page.',
                'woocommerce-multilingual'), '<strong>', '</strong>', '<a href="' . admin_url('admin.php?page=wpml-wcml&tab=settings') . '">', '</a>' ); ?>
        </p>
        <?php

    }

	/**
     * Don't show Page builders Translation editor warning for products
     *
	 * @param bool $display
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public function show_page_builders_translation_editor_warning( $display, $post_id ){

		if( 'product' === get_post_type( $post_id ) ){
			$display = false;
		}

		return $display;
	}

}