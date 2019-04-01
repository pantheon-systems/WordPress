<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Used to check for current less version during page open
 *
 * @since 4.5
 */
add_action( 'vc_before_init', 'vc_check_for_custom_css_build' );

/**
 * Function check is system has custom build of css
 *  and check it version in comparison with current VC version
 *
 * @since 4.5
 */
function vc_check_for_custom_css_build() {
	$version = vc_settings()->getCustomCssVersion();
	if ( vc_user_access()
			->wpAny( 'manage_options' )
			->part( 'settings' )
			->can( 'vc-color-tab' )
			->get() && vc_settings()->useCustomCss() && ( ! $version || version_compare( WPB_VC_VERSION, $version, '<>' ) )
	) {
		/* nectar addition */ 
		//add_action( 'admin_notices', 'vc_custom_css_admin_notice' );
		/* nectar addition end */ 
	}
}

/**
 * Display admin notice depending on current page
 *
 * @since 4.5
 */
function vc_custom_css_admin_notice() {
	global $current_screen;
	vc_settings()->set( 'compiled_js_composer_less', '' );
	$class = 'notice notice-warning vc_settings-custom-design-notice';
	$message_important = __( 'Important notice', 'js_composer' );
	if ( is_object( $current_screen ) && isset( $current_screen->id ) && 'visual-composer_page_vc-color' === $current_screen->id ) {
		$message = __( 'You have an outdated version of WPBakery Page Builder Design Options. It is required to review and save it.', 'js_composer' );
		$html = '<p><strong>' . esc_html( $message_important ) . '</strong>: ' . esc_html( $message ) . '</p>';
	} else {
		$message = __( 'You have an outdated version of WPBakery Page Builder Design Options. It is required to review and save it.', 'js_composer' );
		$btnClass = 'button button-primary button-large vc_button-settings-less';
		$btnAtts = array(
			'href="' . admin_url( 'admin.php?page=vc-color' ) . '"',
			'class="' . esc_attr( $btnClass ) . '"',
			'id="vc_less-save-button"',
			'style="vertical-align: baseline;"',
			// needed to fix ":active bug"
		);
		$html = '<p><strong>' . esc_html( $message_important ) . '</strong>: ' . esc_html( $message ) . '</p>'
		        . '<p><a ' . implode( ' ', $btnAtts ) . '>'
		        . __( 'Open Design Options', 'js_composer' ) . '</a></p>';
	}
	echo '<div class="' . esc_attr( $class ) . '">' . $html . '</div>';

}

function vc_page_settings_tab_color_submit_attributes( $submitButtonAttributes ) {
	$submitButtonAttributes['data-vc-less-path'] = vc_str_remove_protocol( vc_asset_url( 'less/js_composer.less' ) );
	$submitButtonAttributes['data-vc-less-root'] = vc_str_remove_protocol( vc_asset_url( 'less' ) );
	$submitButtonAttributes['data-vc-less-variables'] = json_encode( apply_filters( 'vc_settings-less-variables', array(
		// Main accent color:
		'vc_grey' => array(
			'key' => 'wpb_js_vc_color',
			'default' => vc_settings()->getDefault( 'vc_color' ),
		),
		// Hover color
		'vc_grey_hover' => array(
			'key' => 'wpb_js_vc_color_hover',
			'default' => vc_settings()->getDefault( 'vc_color_hover' ),
		),
		'vc_image_slider_link_active' => 'wpb_js_vc_color_hover',
		// Call to action background color
		'vc_call_to_action_bg' => 'wpb_js_vc_color_call_to_action_bg',
		'vc_call_to_action_2_bg' => 'wpb_js_vc_color_call_to_action_bg',
		'vc_call_to_action_border' => array(
			'key' => 'wpb_js_vc_color_call_to_action_border',
			// darken 5%
			'default_key' => 'wpb_js_vc_color',
			'modify_output' => array(
				array(
					'plain' => array(
						'darken({{ value }}, 5%)',
					),
				),
			),
		),
		// Google maps background color
		'vc_google_maps_bg' => 'wpb_js_vc_color_google_maps_bg',
		// Post slider caption background color
		'vc_post_slider_caption_bg' => 'wpb_js_vc_color_post_slider_caption_bg',
		// Progress bar background color
		'vc_progress_bar_bg' => 'wpb_js_vc_color_progress_bar_bg',
		// Separator border color
		'vc_separator_border' => 'wpb_js_vc_color_separator_border',
		// Tabs navigation background color
		'vc_tab_bg' => 'wpb_js_vc_color_tab_bg',
		// Active tab background color
		'vc_tab_bg_active' => 'wpb_js_vc_color_tab_bg_active',
		// Elements bottom margin
		'vc_element_margin_bottom' => array(
			'key' => 'wpb_js_margin',
			'default' => vc_settings()->getDefault( 'margin' ),
		),
		// Grid gutter width
		'grid-gutter-width' => array(
			'key' => 'wpb_js_gutter',
			'default' => vc_settings()->getDefault( 'gutter' ),
			'modify_output' => array(
				array(
					'plain' => array(
						'{{ value }}px',
					),
				),
			),
		),
		'screen-sm-min' => array(
			'key' => 'wpb_js_responsive_max',
			'default' => vc_settings()->getDefault( 'responsive_max' ),
			'modify_output' => array(
				array(
					'plain' => array(
						'{{ value }}px',
					),
				),
			),
		),
	) ) );

	return $submitButtonAttributes;
}

function vc_page_settings_desing_options_load() {
	add_filter( 'vc_settings-tab-submit-button-attributes-color', 'vc_page_settings_tab_color_submit_attributes' );
	wp_enqueue_script( 'vc_less_js', vc_asset_url( 'lib/bower/lessjs/dist/less.min.js' ), array(), WPB_VC_VERSION );
}

add_action( 'vc-settings-render-tab-vc-color', 'vc_page_settings_desing_options_load' );
