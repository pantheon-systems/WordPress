<?php

/**
 * Created by OnTheGo Systems
 */
class WCML_Products_UI extends WPML_Templates_Factory {

	private $woocommerce_wpml;
	private $sitepress;

	function __construct( &$woocommerce_wpml, &$sitepress ){
		parent::__construct();

		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress = $sitepress;
	}

	public function get_model() {

		$active_languages = $this->sitepress->get_active_languages();
		$product_data = $this->get_products_data();
		$pagination_url = $this->get_pagination_url();
		$filter_url = $this->get_filter_url();
		$pn = $this->get_current_page_number();
		$last = $this->get_last_page_num( $product_data[ 'products_count' ] );
		$slang = $this->get_source_language();
		$search = isset( $_GET[ 'cat' ] ) ? true : false;
		$title_sort = isset( $_GET['ts'] ) ? $_GET['ts'] == 'asc' ? 'desc' : 'asc' : 'asc';
		$date_sort = isset( $_GET['ds'] ) ? $_GET['ds'] == 'asc' ? 'desc' : 'asc' : 'asc';

		$model = array(
			'data' => array(
				'products' => $product_data[ 'products' ],
				'slang' => $slang,
				'search' => $search
			),
			'strings' => array(
				'image' => __( 'Image', 'woocommerce-multilingual' ),
				'product' => __( 'Product', 'woocommerce-multilingual' ),
				'type' => __( 'Type', 'woocommerce-multilingual' ),
				'date' => __( 'Date', 'woocommerce-multilingual' ),
				'categories' => __( 'Categories', 'woocommerce-multilingual' ),
				'no_products' => __( 'No products found', 'woocommerce-multilingual' ),
				'draft' => __( 'Draft', 'woocommerce-multilingual' ),
				'private' => __( 'Private', 'woocommerce-multilingual' ),
				'pending' => __( 'Pending', 'woocommerce-multilingual' ),
				'future' => __( 'Scheduled', 'woocommerce-multilingual' ),
				'parent' => __( 'Parent product: %s', 'woocommerce-multilingual' ),
				'edit_item' => __( 'Edit this item', 'woocommerce-multilingual' ),
				'edit' => __( 'Edit', 'woocommerce-multilingual' ),
				'view_link' => __( 'View "%s"', 'woocommerce-multilingual' ),
				'view' => __( 'View', 'woocommerce-multilingual' ),
				'published' => __( 'Published', 'woocommerce-multilingual' ),
				'modified' => __( 'Last Modified', 'woocommerce-multilingual' ),
			),
			'filter_urls' => array(
				'product' => $filter_url.'&ts='.$title_sort,
				'product_sorted' => isset( $_GET['ts']) ? ' sorted '.$_GET['ts'] : '',
				'date' => $filter_url.'&ds='.$date_sort,
				'date_sorted' => isset( $_GET['ds'] ) ? ' sorted '.$_GET['ds'] : '',
			),
			'filter' => array(
				'display' => ! isset( $_GET['prid'] ) && ! $this->get_current_translator_id() ? true : false,
				'slang' => $slang,
				'active_languages' => $active_languages,
				'categories' => $this->get_products_categories( $slang ),
				'category_from_filter' => isset( $_GET['cat'] ) ? $_GET['cat'] : false,
				'trst' => isset( $_GET['trst'] ) ? $_GET['trst'] : false,
				'st' => isset( $_GET['st'] ) ? $_GET['st'] : false,
				'search_text' => isset( $_GET['s'] ) ? $_GET['s'] : '',
				'all_statuses' =>  $this->get_wc_statuses(),
				'products_admin_url' =>  admin_url( 'admin.php?page=wpml-wcml&tab=products' ),
				'pagination_url' =>  $pagination_url,
				'search' =>  $search,
				'strings' => array(
					'all_lang' => __( 'All languages', 'woocommerce-multilingual' ),
					'all_cats' => __( 'All categories', 'woocommerce-multilingual' ),
					'all_trnsl_stats' => __( 'All translation statuses', 'woocommerce-multilingual' ),
					'not_trnsl' => __( 'Not translated or needs updating', 'woocommerce-multilingual' ),
					'need_upd' => __( 'Needs updating', 'woocommerce-multilingual' ),
					'in_progress' => __( 'Translation in progress', 'woocommerce-multilingual' ),
					'complete' => __( 'Translation complete', 'woocommerce-multilingual' ),
					'all_stats' => __( 'All statuses', 'woocommerce-multilingual' ),
					'filter' => __( 'Filter', 'woocommerce-multilingual' ),
					'reset' => __( 'Reset', 'woocommerce-multilingual' ),
					'search' => __( 'Search', 'woocommerce-multilingual' ),
				),
			),
			'languages_flags' => $this->woocommerce_wpml->products->get_translation_flags( $active_languages, $slang, $this->get_current_job_language() ),
			'request_uri' => $_SERVER["REQUEST_URI"],
			'nonces'  => array(
				'upd_product' => wp_create_nonce( 'update_product_actions' ),
				'get_product_data' => wp_create_nonce( 'wcml_product_data' ),
			),
			'pagination'  => array(
				'display' => $product_data[ 'products' ] && ! isset( $_GET['prid'] ) && $last > 1 ? true: false,
				'products_count' => $product_data[ 'products_count' ],
				'pn' => $pn,
				'last' => $last,
				'pagination_url' => $pagination_url,
				'pagination_first' => $pagination_url.'1',
				'pagination_prev' => $pagination_url . ( (int) $pn > 1 ? $pn - 1 : $pn ),
				'pagination_next' => $pagination_url . ( (int) $pn < $last ? $pn + 1 : $last ),
				'pagination_last' => $pagination_url . $last,
				'show' => !isset( $_GET['prid'] ) && isset( $last ) && $last > 1 ? true : false,
				'strings' => array(
					'items' =>  __( '%d items', 'woocommerce-multilingual' ),
					'first' =>  __( 'Go to the first page', 'woocommerce-multilingual' ),
					'previous' =>  __( 'Go to the previous page', 'woocommerce-multilingual' ),
					'select' =>  __( 'Select Page', 'woocommerce-multilingual' ),
					'current' =>  __( 'Current page', 'woocommerce-multilingual' ),
					'of' =>  __( 'of', 'woocommerce-multilingual' ),
					'next' =>  __( 'Go to the next page', 'woocommerce-multilingual' ),
					'last' =>  __( 'Go to the last page', 'woocommerce-multilingual' ),
				)
			)
		);

		return $model;
	}

	public function get_products_data(){

		$active_languages = $this->sitepress->get_active_languages();

		$products_info = $this->get_product_info_from_self_edit_mode();
		if( !$products_info ){
			$products_info = $this->get_product_info_for_translators();
		}
		if( !$products_info ){
			$products_info = $this->get_products_from_filter();
		}

		if( !$products_info && current_user_can( 'wpml_operate_woocommerce_multilingual' ) ){
			$products_info = array();
			$products_info[ 'products' ] = $this->get_product_list( $this->get_current_page_number(), $this->get_page_limit(), $this->get_source_language() );
			$products_info[ 'products_count' ] = $this->get_products_count( $this->get_source_language() );
		}

		$slang = $this->get_source_language();
		$products = $products_info[ 'products' ];

		foreach( $products as $key => $product ){

			$products[ $key ]->edit_post_link = get_edit_post_link( $product->ID );

			if( has_post_thumbnail( $product->ID ) ){
				$products[ $key ]->post_thumbnail = get_the_post_thumbnail_url( $product->ID, array( 150, 150 ) );
			}else{
				$products[ $key ]->post_thumbnail = wc_placeholder_img_src();
			}

			$original_product_lang = $this->woocommerce_wpml->products->get_original_product_language( $product->ID );

			if ( ! $slang ) {
				$original_lang = $original_product_lang;
			} else {
				$original_lang = $slang;
			}

			$products[ $key ]->orig_flag_url = $this->sitepress->get_flag_url( $original_lang );

			if( $product->post_parent != 0 ){
				$products[ $key ]->parent_title = get_the_title( $product->post_parent );
			}

			$products[ $key ]->view_link = get_post_permalink( $product->ID );


			if ( isset( $current_translator ) ) {
				$prod_lang = $original_product_lang;
			} else {
				$prod_lang = $slang;
			}

			$trid       = $this->sitepress->get_element_trid( $product->ID, 'post_' . $product->post_type );
			$product_translations = $this->sitepress->get_element_translations( $trid, 'post_' . $product->post_type, true, true );

			ob_start();
			$this->woocommerce_wpml->products->get_translation_statuses( $product->ID, $product_translations, $active_languages, $prod_lang, $trid, $this->get_current_job_language() );
			$products[ $key ]->translation_statuses = ob_get_contents();
			ob_end_clean();

			$products[ $key ]->categories_list = $this->get_categories_list( $product->ID, $this->get_cat_url() );

			$prod = wc_get_product( $product->ID );
			$products[ $key ]->icon_class = WooCommerce_Functions_Wrapper::get_product_type( $product->ID );

			if ( $prod->is_virtual() ) {
				$products[ $key ]->icon_class = 'virtual';
			} else if ( $prod->is_downloadable() ) {
				$products[ $key ]->icon_class = 'downloadable';
			}

			if ( $product->post_status == "publish" ) {
				$products[ $key ]->formated_date = date(' Y/m/d', strtotime( $product->post_date ) );
			}else{
				$products[ $key ]->formated_date = date(' Y/m/d', strtotime( $product->post_modified ) );
			}

		}

		return array( 'products' => $products, 'products_count' => $products_info[ 'products_count' ] );
	}

	public function get_product_info_from_self_edit_mode(){

		if ( isset( $_GET[ 'prid' ] ) ) {

			$prid = sanitize_text_field( $_GET[ 'prid' ] );

			if ( ! $this->woocommerce_wpml->products->is_original_product( $prid ) ){
				$products[]        = get_post( $this->woocommerce_wpml->products->get_original_product_id( $prid ) );
			} else {
				$products[] = get_post( $prid );
			}
			$products_count = 1;

			return array( 'products' => $products, 'products_count' => $products_count );
		}

		return false;

	}

	public function get_product_info_for_translators(){
		global $iclTranslationManagement;

		if ( ! current_user_can( 'wpml_operate_woocommerce_multilingual' ) ) {

			$icl_translation_filter[ 'translator_id' ]      = $this->get_current_translator_id();
			$icl_translation_filter[ 'include_unassigned' ] = true;
			$icl_translation_filter[ 'limit_no' ]           = $this->get_page_limit();
			$translation_jobs                             = $iclTranslationManagement->get_translation_jobs( (array) $icl_translation_filter );
			$products                                     = array();
			$products_count                               = 0;

			foreach ( $translation_jobs as $translation_job ) {
				if ( $translation_job->original_post_type == 'post_product' && ! array_key_exists( $translation_job->original_doc_id, $products ) ) {
					$products[ $translation_job->original_doc_id ] = get_post( $translation_job->original_doc_id );
					$products_count ++;
				}
			}

			return array( 'products' => $products, 'products_count' => $products_count );
		}

		return false;
	}

	/*
     * get list of products
     * $page - number of page;
     * $limit - limit product on one page;
     * if($page = 0 && $limit=0) return all products;
     * return array;
    */
	public function get_product_list( $page = 1, $limit = 20, $slang ){
		global $wpdb;

		$sql = "SELECT p.ID,p.post_parent FROM $wpdb->posts AS p
                  LEFT JOIN {$wpdb->prefix}icl_translations AS icl ON icl.element_id = p.id
                WHERE p.post_type = 'product' AND p.post_status IN ('publish','future','draft','pending','private')
                AND icl.element_type= 'post_product' AND icl.source_language_code IS NULL";

		if($slang){
			$sql .= " AND icl.language_code = %s ";
			$sql = $wpdb->prepare( $sql, $slang );
		}

		$sql .= ' ORDER BY p.id DESC';

		$products = $wpdb->get_results( $sql );

		return $this->display_hierarchical( $products, $page, $limit);
	}

	public function display_hierarchical( $products, $pagenum, $per_page ){
		global $wpdb;

		$output_products = array();
		$top_level_products = array();
		$children_products = array();

		if( !empty( $products ) ) {

			foreach ($products as $product) {
				// catch and repair bad products
				if ($product->post_parent == $product->ID) {
					$product->post_parent = 0;
					$wpdb->update($wpdb->posts, array('post_parent' => 0), array('ID' => $product->ID));
				}

				if (0 == $product->post_parent) {
					$top_level_products[] = $product->ID;
				} else {
					$children_products[$product->post_parent][] = $product->ID;
				}
			}

			$count = 0;
			$start = ($pagenum - 1) * $per_page;
			$end = $start + $per_page;

			foreach ($top_level_products as $product_id) {
				if ($count >= $end)
					break;

				if ($count >= $start) {
					$output_products[] = get_post($product_id);

					if (isset($children_products[$product_id])) {
						foreach ($children_products[$product_id] as $children) {
							$output_products[] = get_post($children);
							$count++;
						}
						unset($children_products[$product_id]);
					}
				} else {
					if (isset($children_products[$product_id])) {
						$count += count($children_products[$product_id]);
					}
				}

				$count++;
			}
		}

		return $output_products;
	}

	/*
     * get products count
     */
	public function get_products_count( $slang ){
		global $wpdb;

		$sql = "SELECT count(p.id) FROM $wpdb->posts AS p
                LEFT JOIN {$wpdb->prefix}icl_translations AS icl ON icl.element_id = p.id
                WHERE p.post_type = 'product' AND p.post_status IN ('publish','future','draft','pending','private')
                    AND icl.element_type= 'post_product' AND icl.source_language_code IS NULL";

		if( $slang ){
			$sql .= " AND icl.language_code = %s ";
			$count = $wpdb->get_var( $wpdb->prepare( $sql, $slang ) );
		}else{
			$count = $wpdb->get_var( $sql );
		}

		return (int)$count;
	}

	/*
     * get products from search
     * $title - product name
     * $category - product category
     */
	public function get_products_from_filter( ){
		global $wpdb;

		$title = isset( $_GET['s'] ) ? $_GET['s'] : '';
		$category =	isset( $_GET['cat'] ) ? $_GET['cat'] : null;
		$translation_status = isset( $_GET['trst'] ) ? $_GET['trst'] : false;
		$product_status = isset( $_GET['st'] ) ? $_GET['st'] : false;
		$slang = $this->get_source_language();
		$page = $this->get_current_page_number();
		$limit = $this->get_page_limit();
		$title_sort = isset( $_GET['ts'] ) ? $_GET['ts'] : false;
		$date_sort = isset( $_GET['ds'] ) ? $_GET['ds'] : false;

		if( empty($title) && is_null( $category ) && !$title_sort && !$date_sort ){
			return false;
		}

		$current_language = $slang;
		$prepare_arg = array();
		$prepare_arg[] = '%'.$title.'%';
		if( $slang ) {
			$prepare_arg[] = $current_language;
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM $wpdb->posts AS p";

		if($category){
			$sql .= " LEFT JOIN $wpdb->term_relationships AS tx ON tx.object_id = p.id";
		}

		$sql .= " LEFT JOIN {$wpdb->prefix}icl_translations AS t ON t.element_id = p.id";

		if(in_array($translation_status,array('not','need_update','in_progress','complete'))){

			foreach($this->sitepress->get_active_languages() as $lang){

				if( $lang['code'] == $slang ) continue;

				$tbl_alias_suffix = str_replace('-','_',$lang['code']);
				$sql .= " LEFT JOIN {$wpdb->prefix}icl_translations iclt_{$tbl_alias_suffix}
                        ON iclt_{$tbl_alias_suffix}.trid=t.trid ";

				$sql .= " AND iclt_{$tbl_alias_suffix}.language_code='{$lang['code']}'\n";

				$sql   .= " LEFT JOIN {$wpdb->prefix}icl_translation_status iclts_{$tbl_alias_suffix}
                                ON iclts_{$tbl_alias_suffix}.translation_id=iclt_{$tbl_alias_suffix}.translation_id\n";
			}


		}

		$sql .= " WHERE p.post_title LIKE '%s' AND p.post_type = 'product' AND t.element_type = 'post_product' AND t.source_language_code IS NULL";

		if( $slang ){
			$sql .= " AND t.language_code = %s";
		}

		if( $product_status && $product_status != 'all' ){
			$sql .= " AND p.post_status = %s ";
			$prepare_arg[] = $product_status;
		}else{
			$sql .= " AND p.post_status NOT IN ( 'trash','auto-draft','inherit' ) ";
		}

		if($category){
			$sql .= " AND tx.term_taxonomy_id = %d ";
			$prepare_arg[] = $category;
		}

		if ( in_array( $translation_status, array( 'not', 'need_update', 'in_progress', 'complete' ) ) ) {
			$sql .= " AND (";
			switch ( $translation_status ) {
				case 'not':
					$sql .= $wpdb->prepare( " t.trid IN ( SELECT trid FROM {$wpdb->prefix}icl_translations iclt  
					LEFT OUTER JOIN {$wpdb->prefix}icl_translation_status tls ON iclt.translation_id = tls.translation_id 
					WHERE 
					element_type = 'post_product' 
					AND (
					tls.status IN (0) 
					OR tls.status IS NULL 
					AND (
							SELECT 
								COUNT(trid) 
							FROM 
								{$wpdb->prefix}icl_translations
							WHERE 
								trid = t.trid
							) < %d
						)
					) OR\n", count( $this->sitepress->get_active_languages() ) );
					break;
				case ( $translation_status == 'need_update' || $translation_status == 'not' ):
					foreach ( $this->sitepress->get_active_languages() as $lang ) {
						if ( $lang['code'] == $slang ) {
							continue;
						}
						$tbl_alias_suffix = str_replace( '-', '_', $lang['code'] );
						$sql              .= "( iclts_{$tbl_alias_suffix}.needs_update = 1 ) OR\n";
					}
					break;
				case 'in_progress':
					foreach ( $this->sitepress->get_active_languages() as $lang ) {
						if ( $lang['code'] == $slang ) {
							continue;
						}
						$tbl_alias_suffix = str_replace( '-', '_', $lang['code'] );
						$sql              .= "( iclts_{$tbl_alias_suffix}.status = " . ICL_TM_IN_PROGRESS . " OR iclts_{$tbl_alias_suffix}.status = " . ICL_TM_WAITING_FOR_TRANSLATOR . ") OR\n";
					}
					break;
				case 'complete':
					foreach ( $this->sitepress->get_active_languages() as $lang ) {
						if ( $lang['code'] == $slang ) {
							continue;
						}
						$tbl_alias_suffix = str_replace( '-', '_', $lang['code'] );
						$sql              .= "( (iclts_{$tbl_alias_suffix}.status = " . ICL_TM_COMPLETE . " OR iclts_{$tbl_alias_suffix}.status = " . ICL_TM_DUPLICATE . ") AND iclts_{$tbl_alias_suffix}.needs_update = 0 ) OR\n";
					}
					break;
			}
			$sql = substr( $sql, 0, - 3 );
			$sql .= " ) ";
		}

		if( $title_sort ){
			$sql .= " ORDER BY p.post_title ".$title_sort ;
		}elseif( $date_sort ){
			$sql .= " ORDER BY p.post_date ".$date_sort ;
		}else{
			$sql .= " ORDER BY p.id DESC ";
		}

		$sql .= " LIMIT ".($page-1)*$limit.",".$limit;

		$data = array();

		$data['products'] = $wpdb->get_results($wpdb->prepare($sql,$prepare_arg));
		$data['count'] = $wpdb->get_var("SELECT FOUND_ROWS()");

		return array( 'products' => $data['products'], 'products_count' => $data['count'] );
	}

	public function get_pagination_url(){

		$pagination_url = admin_url( 'admin.php?'. http_build_query( $_GET ). '&paged=' );

		return $pagination_url;
	}

	public function get_filter_url(){

		$filters_array = $_GET;
		if( isset($filters_array['ts'] ) ){
			unset( $filters_array['ts'] );
			unset( $filters_array['ds'] );
		}elseif( isset($filters_array['ds'] ) ){
			unset( $filters_array['ds'] );
			unset( $filters_array['ts'] );
		}

		$filter_url = admin_url( 'admin.php?'. http_build_query( $filters_array ) );

		return $filter_url;
	}

	public function get_cat_url(){

		$filters_array = $_GET;
		if( isset($filters_array['cat'] ) ){
			unset( $filters_array['cat'] );
		}

		$filter_cat_url = admin_url( 'admin.php?'. http_build_query( $filters_array ) );

		return $filter_cat_url;
	}

	public function get_current_job_language(){
		global $iclTranslationManagement;

		if ( isset( $_GET[ 'job_id' ] ) ) {
			$job_id = $_GET[ 'job_id' ];

			$job = $iclTranslationManagement->get_translation_job( $job_id );

			return $job->language_code;
		}else{
			return false;
		}
	}

	public function get_wc_statuses(){
		$all_statuses = get_post_stati();
		//unset unnecessary statuses
		unset( $all_statuses['trash'], $all_statuses['auto-draft'], $all_statuses['inherit'], $all_statuses['wc-pending'], $all_statuses['wc-processing'], $all_statuses['wc-on-hold'], $all_statuses['wc-completed'], $all_statuses['wc-cancelled'], $all_statuses['wc-refunded'], $all_statuses['wc-failed'], $all_statuses['wc-active'], $all_statuses['wc-switched'], $all_statuses['wc-expired'], $all_statuses['wc-pending-cancel'] );

		return $all_statuses;
	}

	public function get_current_page_number(){
		$pn = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;

		return $pn;
	}

	public function get_page_limit(){
		$lm = ( isset( $_GET['lm'] ) && $_GET['lm'] > 0 ) ? $_GET['lm'] : 20;

		return $lm;
	}

	public function get_source_language(){
		$slang = isset( $_GET['slang'] ) && $_GET['slang'] != 'all' ? $_GET['slang'] : false;

		return $slang;
	}

	public function get_last_page_num( $products_count ){

		$last = 1;

		if ( $this->get_page_limit() ) {
			$last = $this->get_product_last_page( $products_count, $this->get_page_limit() );
		}

		return $last;
	}


	/*
     * get pages count
     * $limit - limit product on one page;
     */
	public function get_product_last_page($count,$limit){
		$last = ceil((int)$count/(int)$limit);
		return (int)$last;
	}

	public function get_current_translator_id(){
		global $iclTranslationManagement;

		$translator_id = false;

		if ( ! current_user_can( 'wpml_operate_woocommerce_multilingual' ) ) {
			$current_translator = $iclTranslationManagement->get_current_translator();
			$translator_id = $current_translator->translator_id;
		}

		return $translator_id;
	}

	public function get_categories_list( $product_id, $filter_cat_url ){
		$product_categories = wp_get_object_terms( $product_id, 'product_cat' );
		$categories_list = array();
		foreach( $product_categories as $key => $product_category ){
			$categories_list[$key]['href'] = $filter_cat_url.'&cat='.$product_category->term_id;
			$categories_list[$key]['name'] = $product_category->name.( array_key_exists( $key+1, $product_categories ) ? ', ': '' );
		}

		return $categories_list;
	}

	public function get_products_categories( $slang = false ){
		global $wpdb;

		$sql = "SELECT tt.term_taxonomy_id,tt.term_id,t.name FROM $wpdb->term_taxonomy AS tt
				LEFT JOIN $wpdb->terms AS t ON tt.term_id = t.term_id
				LEFT JOIN {$wpdb->prefix}icl_translations AS icl ON icl.element_id = tt.term_taxonomy_id
				WHERE tt.taxonomy = 'product_cat' AND icl.element_type= 'tax_product_cat' ";

		if ( $slang ) {
			$sql .= " AND icl.language_code = %s ";
			$product_categories = $wpdb->get_results( $wpdb->prepare( $sql, $slang ) );
		} else {
			$sql .= "AND icl.source_language_code IS NULL";
			$product_categories = $wpdb->get_results( $sql );
		}

		return $product_categories;

	}

	public function init_template_base_dir() {
		$this->template_paths = array(
			WCML_PLUGIN_PATH . '/templates/products-list/',
		);
	}

	public function get_template() {
		return 'products.twig';
	}
}