<?php
/**
 * 
 */


//if(!isset($_SESSION)) 
//    { 
//        session_start(); 
//    } 

class BngShortcodes
{

	private $prefix;

	public function __construct($prefix)
	{
		$this->prefix = $prefix;
		add_shortcode( 'bng_location', array($this,'bng_shortcode_location') );

	}

	//RESULTS META KEY 
	private function locationResults($string,$id_post){
		$result = '';
		if(in_array($string, array(
			'location_code', 'brand_assigned_code',
			
			//
			'location_name',
			'google_map_embed',
			'zopim_code',
			'linkedin_url',
			//

			'business_license_id', 'formal_business_name',
			'business_dba', 'address_1', 'address_2', 
			'city',	'state', 'zip', 'primary_phone',
			'tracking_phone', 'fax', 'facebook_url', 
			'google_url', 'youtube_url', 'pinterest_url',
			'twitter_url', 'instagram_url', 'angieslist_url',
			'location_path', 'customvar1', 'customvar2', 
			'customvar3', 'customvar4', 'customvar5', 
			'customvar6', 'customvar7', 'customvar8', 
			'customvar9', 'customvar10', 'button_1_url', 
			'button_2_url', 'button_3_url', 'button_4_url', 
			'button_5_url' 
		))){
			$results = get_post_meta($id_post , $string);
			$result = (!empty($results))?$results[0]:'';
		}else{
			/*$group = get_post_meta($id_post , 'group_fields');

			foreach ($group as $keys => $value) :
				$count = count($value);	
				for ($i=0; $i <= $count ; $i++) { 
					if( in_array($string, (array)$value[$i] , true) ){
						foreach ($value[$i] as $val) {
							$result = $val;
						}	
					}
				}
			endforeach;*/

			
		}
		return $result;
		/*
		
		switch ($string) {
				case 'phone':
					$results = get_post_meta($id_post , 'phone');
					$result = $results[0];
					break;
				case 'city':
					$results = get_post_meta($id_post , 'city');
					$result = $results[0];
					break;
				case 'state':
					$results = get_post_meta($id_post , 'state');
					$result = $results[0];
					break;
				case 'address_1':
					$results = get_post_meta($id_post , 'address_1');
					$result = $results[0];
					break;
				case 'address_2':
					$results = get_post_meta($id_post , 'address_2');
					$result = $results[0];
					break;
				case 'location_path':
					$results = get_post_meta($id_post , 'location_path');
					$result = $results[0];
					break;
				case 'location_code':
					$results = get_post_meta($id_post , 'location_code');
					$result = $results[0];
					break;
				case 'zip':
					$results = get_post_meta($id_post , 'zip');
					$result = $results[0];
					break;
				default:
					$a=1;
					break;
			}*/
			//Meta Key Group
			
	}
	// GET POST TITLE
	private function get_posts_title(){
		$object = array(
			'post_type' => 'bng_type_location',
			'exclude'	=>	array(get_option('bng_location_default')),
			'posts_per_page'   => -1,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
			
		);

		$query = get_posts( $object );
		$args =array();
		foreach ($query as $key => $value) {
			//$args[$value->ID]= strtoupper($value->post_name);
			if($code = get_post_meta($value->ID, 'location_code', true)) {
	    		$args[$value->ID] = strtoupper($code);
	    	}
			//$args[$value->ID]= strtoupper($value->post_title);
		}
		return $args;
	}

	/*
		SHORTCODE
	*/
	public function bng_shortcode_location($atts){
		$view = '';
		//$result = '';
		extract( shortcode_atts( array(
        'id' => 'No especificado',
    	), $atts ) );
		$url = parse_url(get_permalink());
		$path = explode('/', $url['path']);
		$url_get = $_GET;

		$converted_path = array_map("strtoupper", $path);
		$converted_url_get = array_map("strtoupper", $url_get);

		$array_intersect_get = array_intersect($this->get_posts_title(), $converted_url_get);
		$array_intersect_path = array_intersect($this->get_posts_title(), $converted_path);

		
		if( !empty($array_intersect_get) ||  !empty($array_intersect_path) ){
			//search by Url path
			$_SESSION['location_save_session'] = !empty($array_intersect_get)? key($array_intersect_get) : key($array_intersect_path);
			//update_option('bng_post_id',$_SESSION['location_save_session']);
			$result = $this->locationResults($id, $_SESSION['location_save_session'] );

		}else if ( isset($_SESSION['location_save_session']) ) {
			//Search by session
			//$_SESSION['location_save_session'] = get_option('bng_post_id');

			$result = $this->locationResults($id,$_SESSION['location_save_session']);

		}else{
			//By Defautl
			$result = $this->locationResults( $id , get_option('bng_location_default' ));
		}
		
		//$view = "<div>".$result."</div>";
		$view =$result;
		return $view;
	}
	

}