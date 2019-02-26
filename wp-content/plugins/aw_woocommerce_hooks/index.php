<?php
/*
Plugin Name: Woocommerce Charities
Plugin URI: 
Description: To get the woocommerce categories and products
Version: 1.0
Author: Developer-AWS
Author URI: 
License:
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WoocommerceCharities {

	//** Constructor **//
	function __construct() {	
		add_action('admin_menu', array("WoocommerceCharities", "register_plugin_menu"),99 );
		add_action( "admin_enqueue_scripts", array( "WoocommerceCharities", "loadAdminAssets" ));
	}


	public static function loadAdminAssets(){
	    //** Load  Styling. **//
	    wp_enqueue_style( 'hp-style-data-tables', plugin_dir_url( __FILE__ ).'assets/css/woocommercecharities.css');
	    wp_enqueue_script( 'hp_display_jquery-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js',array('jquery'));
	    wp_enqueue_script( 'hp_custom_display_js', plugin_dir_url( __FILE__ ).'assets/js/woocommercecharities.js',array('jquery'));
    }

    public static function register_plugin_menu() {
        
        //add submenu under woocommerce plugin
        add_submenu_page('woocommerce', 'Charities', 'Charities', 'manage_options','wc-charities', array('WoocommerceCharities', 'charityViewDisplayHtml'));
    }

    //woocommerce order list
    public function get_total_order($input){
	 	$reports = new WC_Admin_Report();
		$args = array(
		    'data' => array(
		        '_order_total' => array(
		            'type'     => 'meta',
		            'function' => 'SUM',
		            'name'     => 'total_sales'
		        ),
		    ),
		    'where' => array(
		        array(
		            'key'      => 'post_date',
		            'value'    => $input['start_date'], // starting date
		            'operator' => '>='
		        ),
		        array(
		            'key'      => 'post_date',
		            'value'    => $input['end_date'], // end date...
		            'operator' => '<='
		        ),
		    ),
		    'where_meta' => array(
		        array(
		            'meta_key'   => '_billing_donations',
		            'meta_value' => $input['trust'], //  
		            'operator'   => '='
		        )
		    ),
		); 
	    $data = (array) $reports->get_order_report_data($args);
	    return round($data['total_sales'], 2);
	}

    //create view
    public static function charityViewDisplayHtml(){
		
    	//isset button
    	if(isset($_REQUEST['export_search'])){
    		include_once(ABSPATH . 'wp-admin/libraries/excel/export_to_xls.php');
    	}

    	//include libraries woocommerce
    	include_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/reports/class-wc-admin-report.php');

		global $wpdb,$woocommerce;
		$order_total_list = array();
		$order_per_trust_list = array();

		//form action
		$start_date = ($_REQUEST['start_date']!='') ? $_REQUEST['start_date'] : date('Y-m-01');
		$end_date = ($_REQUEST['end_date']!='') ? $_REQUEST['end_date'] : date('Y-m-d');

    	//get meta_value records from database
    	$metadata = "SELECT distinct(meta_value) FROM `wp_postmeta` where meta_key = '_billing_donations' and meta_value !='' group by meta_value";
    	$charitable_trust = $wpdb->get_results($metadata, ARRAY_A );

    	$view = '<h1 class="wp-heading-inline">Charities</h1>';
		$view .= '<ul class="charity-names">';
			foreach ($charitable_trust as $key => $value):

				$id = strtolower(str_replace(" ", "_", $value['meta_value']));
				$input = array();
		      	$input['start_date'] = date("Y-m-d",strtotime($start_date));
		      	$input['end_date'] = date("Y-m-d",strtotime($end_date));
		      	$input['trust'] = $value['meta_value'];
		      	$order_total_list[$id] = $this->get_total_order($input);

		      	$view .= '<li><a href="javascript:void(0);" class="charity" data-char="'.$id.'" >'.$value['meta_value'].'</a></li>';

			endforeach;
		$view .= '</ul>';

		//form search data
		$view .= '<form method="post" action="/wp-admin/admin.php?page=wc-charities" style="margin:10px 0;">
		From <input type="text"  value="'.$start_date.'" id="start_date" name="start_date" placeholder="yy-mm-dd"> 
		 - To <input  value="'.$end_date.'" type="text" id="end_date" name="end_date" placeholder="yy-mm-dd">
		<input class="button button-primary button-large" type="submit" name"date_search" value="GO" onclick="javascript: form.action=\'/wp-admin/admin.php?page=wc-charities\';" >
		<input class="button button-primary button-large" type="submit" name"export_search" value="Export to xls" onclick="javascript: form.action=\'/wp-admin/admin.php?page=wc-charities\';">   
		</form>';

		//isset button
    	if(isset($_REQUEST['date_search'])){
    		
    		$sql ="select p.ID as order_id, p.post_date,   
			    max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
			    max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,    
			    max( CASE WHEN pm.meta_key = '_order_total' and p.ID = pm.post_id THEN pm.meta_value END ) as order_total,    
			    max( CASE WHEN pm.meta_key = '_paid_date' and p.ID = pm.post_id THEN pm.meta_value END ) as paid_date,
			    max( CASE WHEN pm.meta_key = '_billing_donations' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_donations,
			    ( select group_concat( order_item_name separator '|' ) from wp_woocommerce_order_items where order_id = p.ID ) as order_items from wp_posts p join wp_postmeta pm on p.ID = pm.post_id
			    join wp_woocommerce_order_items oi on p.ID = oi.order_id where post_type = 'shop_order' and
			    post_date >= '".$start_date."' AND post_date <= '".$end_date."' and
			    post_status = 'wc-completed' group by p.ID";
			$order_data = $wpdb->get_results($sql, ARRAY_A);

			$trust_list = array();
			foreach ($order_data as $key => $list) {
			 	if($list['billing_donations']!=''){
			 		$order_per_trust_list[$list['billing_donations']][] = $list;	
			 	}
			}

			$count =0;
		   	$hide_class='';
		   	$order_total =0 ;
		   	foreach ($order_per_trust_list as $key => $orders) {
		   		$div_id = strtolower(str_replace(" ", "_", $key));
   				if($count>0){ $hide_class = 'hide_div'; }

   				$view .= '<div id="'.$div_id.'" class="ct '.$hide_class.'" >';
	   				$view .= '<table class="charity-table" style="border-collapse: collapse;">';
	   					$view .= '<tr>';
		   					$view .= '<td>Order Total:- </td>';
		   					$view .= '<td>'.$order_total_list[$div_id].'</td>';
		   					$view .= '<td>5 Percent of total:-</td>';
		   					$view .= '<td>'.round(($order_total_list[$div_id]*0.05),2).'</td>';
	   					$view .= '</tr>';
	   				$view .= '</table>';
	   				$view .= '<table class="charity-table"  style="border-collapse: collapse;">';
	   					$view .= '<tr class="charity-table-title">';
	   						$view .= '<th>Name</th>';
	   						$view .= '<th>Date</th>';						 
	   						$view .= '<th>Order total</th>';
	   						$view .= '<th>Charity trust</th>';
	   					$view .= '</tr>';
						foreach ($orders as $key => $orders_list):
							$view .= '<tr>';
								$view .= '<th>'.$orders_list['_billing_first_name']." ".$orders_list['_billing_last_name'].'</th>';
								$view .= '<th>'.$orders_list['paid_date'].'</th>';
								$view .= '<th>'.$orders_list['order_total'].'</th>';
								$view .= '<th>'.$orders_list['billing_donations'].'</th>';
							$view .= '</tr>';
						endforeach;
	   				$view .= '</table>';
	   			$view .= '</div>';

		   	}

    	}

		echo $view;
    }

}

//create object WoocommerceCharities
$WoocommerceCharities = new WoocommerceCharities;
