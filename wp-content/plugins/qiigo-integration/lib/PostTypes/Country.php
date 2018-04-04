<?php

namespace Qiigo\Plugin\Integration\PostTypes {
	class Country extends Base {
		public static function Register() {
			parent::RegisterCPT('country', array(
				'labels' => array(
					'name' => __( 'Countries' ),
					'singular_name' => __( 'Country' )
				),
				'public' => true,
				'has_archive' => false,
				'menu_position' => 48,
				'menu_icon' => 'dashicons-admin-site',
				'show_in_menu' => true,
				'show_ui' => true,
				'supports' => array(
					'title', 'custom-fields'
				)
			));
		}
		
		public static function GetAll() {
			return get_posts(array(
				'numberposts'	=> -1,
				'post_type'		=> 'country',
				'post_status' => 'publish',
			));
		}
	}
}