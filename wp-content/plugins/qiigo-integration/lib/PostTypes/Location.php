<?php

namespace Qiigo\Plugin\Integration\PostTypes {
	class Location extends Base {
		public static function Register() {
			parent::RegisterCPT('location', array(
				'labels' => array(
					'name' => __( 'Locations' ),
					'singular_name' => __( 'Location' )
				),
				'public' => true,
				'has_archive' => false,
				'menu_position' => 49,
				'menu_icon' => 'dashicons-admin-multisite',
				'show_in_menu' => false,
				'show_ui' => false,
				'supports' => array(
					'title', 'custom-fields'
				)
			));
		}
		
		public static function Find($zip, $country=null) {
			$countries = array('US','CA','us','ca');
			if( isset($country) && ($l = strlen($country)) == 2 && !in_array($country, $countries) ) {
				if( preg_match('/^[a-zA-Z]{2}$/', $country) !== 1 )
					return null;
					
				$posts = get_posts(array(
					'numberposts'	=> -1,
					'post_type'		=> 'bng_type_location',
					'post_status' => 'publish',
					'meta_query'	=> array(
						array(
							'key'	 	=> 'country_code',
							'value'	  	=> $country,
							'compare' 	=> '='
						)
					)
				));
				
				if( count($posts) > 0 )
					return $posts[0];
			}
			
			$l = strlen($zip);
				
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			// !!! Validate chars and format, this is being used unescaped in SQL !!!
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			if( $l == 6 || $l == 7 )			// A1A2B2, A1A-2B2, A1A 2B2
				$zip = substr($zip,0,3);
			else if( $l == 9 || $l == 10 )		// 111112222, 11111-2222, 11111 2222
				$zip = substr($zip,0,5);
			else if( ($l != 3 && $l != 5) || preg_match('/^[a-zA-Z0-9]+$/', $zip) !== 1 )
				return null;
				
			$posts = get_posts(array(
				'numberposts'	=> -1,
				'post_type'		=> 'bng_type_location',
				'post_status' => 'publish',
				'meta_query'	=> array(
					array(
						'key'	 	=> 'zips',
						'value'	  	=> ','.$zip.',',
						'compare' 	=> 'LIKE',
					)
				)
			));
			
			if( count($posts) <= 0 )
				return null;
			
			return $posts[0];
			
			/*foreach( $posts as $loc ) {
				$zips_str = get_field('zips', $loc->ID, false);
				if( !isset($zips_str) || trim($zips_str) == '' )
					continue;
					
				$zips = explode(',',$zips_str);
				if( in_array($zip, $zips) )
					return $loc;
			}
				
			return null;*/
		}
	}
}
