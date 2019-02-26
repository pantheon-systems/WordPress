<?php
  

class WCML_Reports{
    
    public $tab;
    public $report;
    
    public function __construct(){
        
        add_action('init', array($this, 'init'));
        
    }

    public function init(){

        if( isset($_GET['page']) && $_GET['page'] == 'wc-reports' ) {

            $this->tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'orders';
            $this->report = isset( $_GET['report'] ) ? $_GET['report'] : '';

            add_filter( 'woocommerce_reports_get_order_report_query', array( $this, 'filter_reports_query' ), 0 );

            if ( $this->tab == 'orders' && $this->report == 'sales_by_product' ) {
                add_filter( 'woocommerce_reports_get_order_report_data', array( $this, 'combine_report_by_languages' ) );
            }
	        if ( $this->tab == 'orders' && $this->report == 'sales_by_category' ) {
		        add_filter( 'woocommerce_report_sales_by_category_get_products_in_category', array(
			        $this,
			        'use_categories_in_all_languages'
		        ), 10, 2 );
	        }
        }

        add_filter( 'woocommerce_report_most_stocked_query_from', array( $this, 'filter_reports_stock_query' ) );
        add_filter( 'woocommerce_report_out_of_stock_query_from', array( $this, 'filter_reports_stock_query' ) );
        add_filter( 'woocommerce_report_low_in_stock_query_from', array( $this, 'filter_reports_stock_query' ) );

    }

    public function filter_reports_query($query){
        global $wpdb, $sitepress;
        
        $current_language = $sitepress->get_current_language();
        $active_languages = $sitepress->get_active_languages();

        if($this->tab == 'orders' && $this->report == 'sales_by_product'){
            
            $sparkline_query = strpos( $query[ 'select'], 'sparkline_value' ) !== false;
            
            if( 
            
                $sparkline_query ||                 
                isset( $query[ 'order_by' ] ) && ( $query[ 'order_by' ] == 'ORDER BY order_item_qty DESC' || $query[ 'order_by' ] == 'ORDER BY order_item_total DESC' ) 
            
            ){
                
                
                $query[ 'select' ] .= " , order_language.meta_value AS order_language , translations.trid";
                
                $query[ 'join' ] .= " LEFT JOIN {$wpdb->postmeta} order_language ON posts.ID = order_language.post_id";
                $query[ 'where' ] .= " AND order_language.meta_key = 'wpml_language' ";
                
                $query[ 'join' ] .= " LEFT JOIN {$wpdb->prefix}icl_translations translations ON translations.element_id = order_item_meta__product_id.meta_value";
                $query[ 'where' ] .= " AND translations.element_type IN ('post_product','post_product_variation') ";
                
                if(!$sparkline_query){
                    $limit = str_replace('LIMIT ', '', trim($query[ 'limit' ]));
                    $query[ 'limit' ] = sprintf(" LIMIT %d ", $limit * count($active_languages));
                }
                
                
                if($sparkline_query){
                    preg_match("#order_item_meta__product_id\.meta_value = '([0-9]+)'#", $query[ 'where' ], $matches);
                    $product_id = $matches[1];
                    $post_type = get_post_type($product_id);
                    $trid = $sitepress->get_element_trid($product_id, 'post_'.$post_type);
                    $translations = $sitepress->get_element_translations($trid, 'post_'.$post_type, true);
                    $product_ids = array();
                    foreach($translations as $translation){
                        $product_ids[] = $translation->element_id;
                    }
                        
                    $query[ 'where' ] = str_replace("order_item_meta__product_id.meta_value = '{$product_id}'", "order_item_meta__product_id.meta_value IN(" . join(',', $product_ids) . ")", $query[ 'where' ]);
                    
                }

                $query[ 'select' ] .= ', translations.language_code AS language_code_' . esc_sql( str_replace('-', '_', $current_language)  ); // user for per-language caching
                
            }elseif(
                $query[ 'select' ] == 'SELECT SUM( order_item_meta__line_total.meta_value) as order_item_amount' || //sales for the selected items
                $query[ 'select' ] == 'SELECT SUM( order_item_meta__qty.meta_value) as order_item_count'         || //purchases for the selected items                
                $query[ 'select' ] == 'SELECT SUM( order_item_meta__qty.meta_value) as order_item_count, posts.post_date as post_date, order_item_meta__product_id.meta_value as product_id' || //Get orders and dates in range - main chart: order_item_counts
                $query[ 'select' ] == 'SELECT SUM( order_item_meta__line_total.meta_value) as order_item_amount, posts.post_date as post_date, order_item_meta__product_id.meta_value as product_id' //Get orders and dates in range - main chart: order_item_amounts
                
            ){ 
                
                preg_match("#order_item_meta__product_id_array\.meta_value IN \(([^\)]+)\)#", $query[ 'where' ], $matches);
                $product_ids = array();
                $exp = array_map('trim', explode(',', $matches[1]));
                foreach($exp as $e){
                    $product_ids[] = trim($e, "'");
                }
                $all_product_ids = array();
                foreach($product_ids as $product_id){
                    $post_type = get_post_type($product_id);
                    $trid = $sitepress->get_element_trid($product_id, 'post_'.$post_type);
                    $translations = $sitepress->get_element_translations($trid, 'post_'.$post_type, true);
                    foreach($translations as $translation){
                        $all_product_ids[] = $translation->element_id;
                    }
                }
                
                $query[ 'where' ] = preg_replace("#order_item_meta__product_id_array\.meta_value IN \(([^\)]+)\)#", "order_item_meta__product_id_array.meta_value IN (" . join(',', $all_product_ids) . ")", $query[ 'where' ]);
            
                
            }
                
        }

        return $query;
    }


    public function combine_report_by_languages($results){
        global $sitepress, $wpdb;
        
        if(is_array($results) && isset($results['0']->order_item_qty)){
            $mode = 'top_sellers';
        }elseif(is_array($results) && isset($results['0']->order_item_total)){
            $mode = 'top_earners';
        }elseif(isset($results['0']->sparkline_value)){
            $mode = 'top_sellers_spark';            
        }else{
            return $results;
        }
        
        if(!isset($results['0']->trid)) return $results;
        
        $current_language = $sitepress->get_current_language();

        $combined_results = array();
        
        foreach($results as $k => $row){
            
            switch($mode){
                case 'top_sellers':
                case 'top_earners':
                    $key = $row->trid;
                    break;
                case 'top_sellers_spark':
                    $key = $row->trid . '#' . substr($row->post_date, 0, 10);
                    break;
            }
            
            if($row->order_language == $current_language){
                
                $combined_results[$key] = $row;
                
            }
            
        }

        foreach($results as $k => $row){
            
            if($row->order_language != $current_language){
                
                switch($mode){
                    case 'top_sellers':
                    case 'top_earners':
                        $key = $row->trid;
                        break;
                    case 'top_sellers_spark':
                        $key = $row->trid . '#' . substr($row->post_date, 0, 10);
                        break;
                }
                
                if(isset($combined_results[$key])){
                    
                    switch($mode){
                        case 'top_sellers':
                            $combined_results[$key]->order_item_qty += $row->order_item_qty;
                            break;
                        case 'top_earners':
                            $combined_results[$key]->order_item_total += $row->order_item_total;
                            break;
                        case 'top_sellers_spark':
                            $combined_results[$key]->sparkline_value += $row->sparkline_value;
                            break;
                            
                    }
                        
                }else{
                    
                    $default_product_id = apply_filters( 'translate_object_id',$row->product_id, 'product', false, $current_language);
                    
                    if($default_product_id){
                        $combined_results[$key] = new stdClass();
                        $combined_results[$key]->product_id = $default_product_id;

                        switch($mode){
                            case 'top_sellers':
                                $combined_results[$key]->order_item_qty = $row->order_item_qty;
                                break;
                            case 'top_earners':
                                $combined_results[$key]->order_item_total = $row->order_item_total;
                                break;
                            case 'top_sellers_spark':
                                $combined_results[$key]->sparkline_value = $row->sparkline_value;
                                $combined_results[$key]->post_date = $row->post_date;
                                break;                                
                        }
                        
                    }
                    
                }
                 
                
            }
            
        }

        switch($mode){
            case 'top_sellers':
                usort($combined_results, array(__CLASS__, '_order_by_quantity'));
                array_slice($combined_results, 0, 12);
                break;    
            case 'top_earners':
                usort($combined_results, array(__CLASS__, '_order_by_total'));
                array_slice($combined_results, 0, 12);
                break;    
            case 'top_sellers_spark':
                break;
                
            
        }
        
        foreach($combined_results as $k => $row){
            unset($combined_results[$k]->trid, $combined_results[$k]->order_language);
        }
        
        
        return $combined_results;
    }

    private static function _order_by_quantity($a, $b){
        return $a->order_item_qty < $b->order_item_qty;
    }

    private static function _order_by_total($a, $b){
        return $a->order_item_total < $b->order_item_total;
    }

    public function filter_reports_stock_query( $query_from ){
        global $wpdb, $sitepress;

        $current_language = $sitepress->get_current_language();

        if( $current_language !== 'all' ){
            $query_from = preg_replace("/WHERE/",
                "LEFT JOIN {$wpdb->prefix}icl_translations AS t
                ON posts.ID = t.element_id
                WHERE", $query_from);

            $query_from .= " AND t.element_type IN ( 'post_product', 'post_product_variation' ) AND t.language_code = '".$current_language."'";
        }


        return $query_from;
    }

	public function use_categories_in_all_languages( $product_ids, $category_id ) {
		global $woocommerce_wpml, $sitepress;

		$category_term = $woocommerce_wpml->terms->wcml_get_term_by_id( $category_id, 'product_cat' );

		if ( ! is_wp_error( $category_term ) ) {
			$trid         = $sitepress->get_element_trid( $category_term->term_taxonomy_id, 'tax_product_cat' );
			$translations = $sitepress->get_element_translations( $trid, 'tax_product_cat', true );

			foreach ( $translations as $translation ) {
				if ( $translation->term_id != $category_id ) {
					$term_ids    = get_term_children( $translation->term_id, 'product_cat' );
					$term_ids[]  = $translation->term_id;
					$product_ids = array_merge( array_unique( $product_ids ), get_objects_in_term( $term_ids, 'product_cat' ) );
				}
			}
		}

		return $product_ids;
	}
    
}
