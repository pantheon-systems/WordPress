<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Contact form7 vendor
 * =======
 * Plugin Contact form 7 vendor
 * To fix issues when shortcode doesn't exists in frontend editor. #1053, #1054 etc.
 * @since 4.3
 */
class Vc_Vendor_ContactForm7 implements Vc_Vendor_Interface {

	/**
	 * Add action when contact form 7 is initialized to add shortcode.
	 * @since 4.3
	 */
	public function load() {

		vc_lean_map( 'contact-form-7',
			array(
				$this,
				'addShortcodeSettings',
			) );
	}

	/**
	 * Mapping settings for lean method.
	 *
	 * @since 4.9
	 *
	 * @param $tag
	 *
	 * @return array
	 */
	public function addShortcodeSettings( $tag ) {
		/**
		 * Add Shortcode To WPBakery Page Builder
		 */
		$cf7 = get_posts( 'post_type="wpcf7_contact_form"&numberposts=-1' );

		$contact_forms = array();
		if ( $cf7 ) {
			foreach ( $cf7 as $cform ) {
				$contact_forms[ $cform->post_title ] = $cform->ID;
			}
		} else {
			$contact_forms[ __( 'No contact forms found', 'js_composer' ) ] = 0;
		}

		return array(
			'base' => $tag,
			'name' => __( 'Contact Form 7', 'js_composer' ),
			'icon' => 'icon-wpb-contactform7',
			'category' => __( 'Content', 'js_composer' ),
			'description' => __( 'Place Contact Form7', 'js_composer' ),
			'params' => array(
				array(
					'type' => 'dropdown',
					'heading' => __( 'Select contact form', 'js_composer' ),
					'param_name' => 'id',
					'value' => $contact_forms,
					'save_always' => true,
					'description' => __( 'Choose previously created contact form from the drop down list.', 'js_composer' ),
				),
				array(
					'type' => 'textfield',
					'heading' => __( 'Search title', 'js_composer' ),
					'param_name' => 'title',
					'admin_label' => true,
					'description' => __( 'Enter optional title to search if no ID selected or cannot find by ID.', 'js_composer' ),
				),
			),
		);
	}
}
