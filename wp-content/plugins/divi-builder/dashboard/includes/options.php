<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//Array of all sections. All sections will be added into sidebar navigation except for the 'header' section.
$all_sections = array(
	'newsletter'  => array(
		'title'    => esc_html__( 'Newsletter Settings', 'et_builder_plugin' ),
		'contents' => array(
			'main'   => esc_html__( 'Main', 'bloom' ),
		),
	),
	'updates'  => array(
		'title'    => esc_html__( 'Updates', 'et_builder_plugin' ),
		'contents' => array(
			'main'   => esc_html__( 'Main', 'bloom' ),
		),
	),
);

/**
 * Array of all options
 * General format for options:
 * '<option_name>' => array(
 *							'type' => ...,
 *							'name' => ...,
 *							'default' => ...,
 *							'validation_type' => ...,
 *							etc
 *						)
 * <option_name> - just an identifier to add the option into $assigned_options array
 * Array of parameters may contain diffrent attributes depending on option type.
 * 'type' is the required attribute for all options. All other attributes depends on the option type.
 * 'validation_type' and 'name' are required attribute for the option which should be saved into DataBase.
 *
 */

$dashboard_options_all = array(

	'mailchimp' => array(
		'section_start' => array(
			'type'     => 'section_start',
			'title'    => esc_html__( 'MailChimp', 'et_builder' ),
		),

		'option' => array(
			'type'                 => 'input_field',
			'subtype'              => 'text',
			'placeholder'          => '',
			'title'                => esc_html__( 'MailChimp API Key:', 'et_builder_plugin' ),
			'name'                 => 'mailchimp_key',
			'hide_contents'        => true,
			'validation_type'      => 'simple_text',
			'hide_contents'        => true,
			'hint_text'            => sprintf(
				'%1$s<a href="%3$s" target="_blank">%2$s</a>',
				esc_html__( 'Enter your MailChimp API key. You can create an api key ', 'et_builder_plugin' ),
				esc_html__( 'here', 'et_builder_plugin' ),
				esc_url( 'https://us3.admin.mailchimp.com/account/api/' )
			),
		),

		'regenerate_lists' => array(
			'type'            => 'button',
			'title'           => esc_html__( 'Regenerate MailChimp Lists', 'et_builder_plugin' ),
			'link'            => '#',
			'class'           => 'et_dashboard_get_lists et_pb_mailchimp',
			'authorize'       => false,
		),
	),

	'aweber' => array(
		'section_start' => array(
			'type'     => 'section_start',
			'title'    => esc_html__( 'Aweber', 'et_builder' ),
		),
		'aweber_key' => array(
			'type'                 => 'input_field',
			'subtype'              => 'text',
			'placeholder'          => '',
			'name'                 => 'aweber_key',
			'title'                => esc_html__( 'AWeber code:', 'et_builder_plugin' ),
			'default'              => '',
			'class'                => 'api_option api_option_key',
			'hide_contents'        => true,
			'hint_text'            => sprintf(
				'%3$s <a href="%2$s" target="_blank">%1$s</a> %4$s',
				esc_html__( 'here', 'et_builder' ),
				esc_url( 'https://auth.aweber.com/1.0/oauth/authorize_app/b17f3351' ),
				esc_html__( 'Generate authorization code', 'et_builder_plugin' ),
				esc_html__( ' then paste in the authorization code and click authorize button', 'et_builder_plugin' )
			),
			'validation_type'      => 'simple_text',
		),
		'aweber_button' => array(
			'type'      => 'button',
			'title'     => esc_html__( 'Authorize AWeber', 'et_builder_plugin' ),
			'link'      => '#',
			'class'     => 'et_dashboard_authorize',
			'action'    => 'aweber',
			'authorize' => true,
		),
		'regenerate_lists' => array(
			'type'            => 'button',
			'title'           => esc_html__( 'Regenerate AWeber Lists', 'et_builder_plugin' ),
			'link'            => '#',
			'class'           => 'et_dashboard_get_lists et_pb_aweber',
			'authorize'       => false,
		),
	),

	'updates' => array(
		'section_start' => array(
			'type'     => 'section_start',
			'title'    => esc_html__( 'Enable Updates', 'et_builder' ),
			'subtitle' => sprintf( esc_html__( 'Keeping your plugins updated is important. To %1$s for the Divi Builder, you must first authenticate your Elegant Themes account by inputting your account Username and API Key below. Your username is the same username you use when logging into your Elegant Themes account, and your API Key can be found by logging into your account and navigating to the Account > API Key page.', 'et_builder' ),
				sprintf( '<a href="%1$s" target="_blank">%2$s</a>',
					esc_attr( 'http://www.elegantthemes.com/plugins/divi-builder/documentation/update/' ),
					esc_html__( 'enable updates', 'et_builder' )
				)
			),
			'no_escape' => true,
		),

		'option_1' => array(
			'type'                 => 'input_field',
			'subtype'              => 'text',
			'placeholder'          => '',
			'title'                => esc_html__( 'Username:', 'et_builder_plugin' ),
			'name'                 => 'updates_username',
			'class'                => 'updates_option updates_option_username',
			'validation_type'      => 'simple_text',
			'hide_contents'        => true,
			'hint_text'            => esc_html__( 'Please enter your ElegantThemes.com username', 'et_builder_plugin' ),
		),

		'option_2' => array(
			'type'                 => 'input_field',
			'subtype'              => 'text',
			'placeholder'          => '',
			'title'                => esc_html__( 'API Key:', 'et_builder_plugin' ),
			'name'                 => 'updates_api_key',
			'class'                => 'updates_option updates_option_api_key',
			'validation_type'      => 'simple_text',
			'hide_contents'        => true,
			'hint_text'            => sprintf( esc_html__( 'Enter your %1$s here.', 'Monarch' ),
				sprintf( '<a href="%1$s" target="_blank">%2$s</a>',
					esc_attr( 'https://www.elegantthemes.com/members-area/api-key.php' ),
					esc_html__( 'Elegant Themes API Key', 'Monarch' )
				)
			),
		),

		'update_button' => array(
			'type'            => 'button',
			'title'           => esc_html__( 'Save', 'et_builder_plugin' ),
			'link'            => '#',
			'authorize'       => false,
			'class'           => 'et_dashboard_updates_save',
		),
	),

	'newsletter_title' => array(
		'type'  => 'main_title',
		'title' => esc_html__( 'Newsletter Settings', 'et_builder_plugin' ),
	),

	'updates_title' => array(
		'type'  => 'main_title',
		'title' => esc_html__( 'Authenticate Your Subscription', 'et_builder_plugin' ),
	),

	'end_of_section' => array(
		'type' => 'section_end',
	),

	'end_of_sub_section' => array(
		'type'        => 'section_end',
		'sub_section' => 'true',
	),
);

/**
 * Array of options assigned to sections. Format of option key is following:
 * 	<section>_<sub_section>_options
 * where:
 *	<section> = $all_sections -> $key
 *	<sub_section> = $all_sections -> $value['contents'] -> $key
 *
 * Note: name of this array shouldn't be changed. $assigned_options variable is being used in ET_Dashboard class as options container.
 */
$assigned_options = array(
	'newsletter_main_options' => array(
		$dashboard_options_all[ 'newsletter_title' ],
		$dashboard_options_all[ 'mailchimp' ][ 'section_start' ],
			$dashboard_options_all[ 'mailchimp' ][ 'option' ],
			$dashboard_options_all[ 'mailchimp' ][ 'regenerate_lists' ],
			$dashboard_options_all[ 'end_of_section' ],
		$dashboard_options_all[ 'aweber' ][ 'section_start' ],
			$dashboard_options_all[ 'aweber' ][ 'aweber_key' ],
			$dashboard_options_all[ 'aweber' ][ 'aweber_button' ],
			$dashboard_options_all[ 'aweber' ][ 'regenerate_lists' ],
			$dashboard_options_all[ 'end_of_section' ],
	),
	'updates_main_options' => array(
		$dashboard_options_all[ 'updates_title' ],
		$dashboard_options_all[ 'updates' ][ 'section_start' ],
			$dashboard_options_all[ 'updates' ][ 'option_1' ],
			$dashboard_options_all[ 'updates' ][ 'option_2' ],
			$dashboard_options_all[ 'updates' ][ 'update_button' ],
			$dashboard_options_all[ 'end_of_section' ],
	),
);