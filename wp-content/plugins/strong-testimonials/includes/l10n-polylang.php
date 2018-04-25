<?php
/**
 * ----------------------------------------
 * POLYLANG
 * ----------------------------------------
 */

/**
 * Add translation actions & filters.
 */
function wpmtst_l10n_filters_polylang() {

	// Translate
	remove_filter( 'wpmtst_l10n', 'wpmtst_l10n_default' );
	add_filter( 'wpmtst_l10n', 'wpmtst_l10n_polylang', 20, 3 );
	// TODO handle cat IDs like WPML

	// Help
	add_action( 'wpmtst_before_form_settings', 'wpmtst_help_link_polylang' );
	add_action( 'wpmtst_before_fields_settings', 'wpmtst_help_link_polylang' );
	add_action( 'wpmtst_after_notification_fields', 'wpmtst_help_link_polylang' );

}
add_action( 'init', 'wpmtst_l10n_filters_polylang', 20 );

/**
 * @param $string
 * @param $context
 * @param $name
 *
 * @return bool|string
 */
function wpmtst_l10n_polylang( $string, $context, $name ) {
	if ( function_exists( 'pll__' ) ) {
		return pll__( $string );
	}
	return $string;
}

/**
 * pll_register_string($name, $string, $group, $multiline);
 *   $name      (required) name provided for sorting convenience (ex: ‘myplugin’)
 *   $string    (required) the string to translate
 *   $group     (optional) the group in which the string is registered, defaults to ‘polylang’
 *   $multiline (optional) if set to true, the translation text field will be multiline, defaults to false
 */

/**
 * Register form field strings.
 *
 * @param $fields
 */
function wpmtst_form_fields_polylang( $fields ) {
	if ( function_exists( 'pll_register_string' ) ) {
		$context = 'strong-testimonials-form-fields';
		foreach ( $fields as $field ) {
			$name = $field['name'] . ' : ';
			if ( isset( $field['after'] ) && $field['after'] ) {
				pll_register_string( $name . __( 'after', 'strong-testimonials' ), $field['after'], $context );
			}
			if ( isset( $field['before'] ) && $field['before'] ) {
				pll_register_string( $name . __( 'before', 'strong-testimonials' ), $field['before'], $context );
			}
			if ( isset( $field['placeholder'] ) && $field['placeholder'] ) {
				pll_register_string( $name . __( 'placeholder', 'strong-testimonials' ), $field['placeholder'], $context );
			}
			if ( isset( $field['label'] ) && $field['label'] ) {
				pll_register_string( $name . __( 'label', 'strong-testimonials' ), $field['label'], $context );
			}
			if ( isset( $field['default_form_value'] ) && $field['default_form_value'] ) {
				pll_register_string( $name . __( 'default form value', 'strong-testimonials' ), $field['default_form_value'], $context );
			}
			if ( isset( $field['default_display_value'] ) && $field['default_display_value'] ) {
				pll_register_string( $name . __( 'default display value', 'strong-testimonials' ), $field['default_display_value'], $context );
			}
		}
	}
}

/**
 * Register form strings.
 *
 * @param $options
 */
function wpmtst_form_options_polylang( $options ) {
	if ( function_exists( 'pll_register_string' ) ) {
		// Form messages
		$context = 'strong-testimonials-form-messages';
		foreach ( $options['messages'] as $key => $field ) {
			pll_register_string( __( $field['description'], 'strong-testimonials' ), $field['text'], $context );
		}

		// Form notification
		$context = 'strong-testimonials-notification';
		pll_register_string( __( 'Email subject', 'strong-testimonials' ), $options['email_subject'], $context );
		pll_register_string( __( 'Email message', 'strong-testimonials' ), $options['email_message'], $context, true );
	}
}

/**
 * Register "Read more" link text.
 *
 * @since 2.11.17
 */
function wpmtst_readmore_polylang() {
	if ( function_exists( 'pll_register_string' ) ) {
		$context = 'strong-testimonials-views';

		$views = wpmtst_get_views();
		if ( ! $views ) {
			return;
		}

		foreach ( $views as $key => $view ) {
			$view_data = unserialize( $view['value'] );
			if ( ! is_array( $view_data ) ) {
				continue;
			}

			pll_register_string( sprintf( __( 'View %s : Read more (testimonial)', 'strong-testimonials', false ),
				$view['id'] ), $view_data['more_post_text'], $context );

			pll_register_string( sprintf( __( 'View %s : Read more (page or post)', 'strong-testimonials', false ),
				$view['id'] ), $view_data['more_page_text'], $context );
		}
	}
}

/**
 * Polylang string translations
 *
 * @since 1.21.0
 * `add_action( 'load-languages_page_mlang_strings', 'wpmtst_admin_polylang' );`
 *
 * @since 2.26.10
 * We can no longer use the page-specific hook because it's constructed using the user's admin language.
 * Polylang does not provide a hook either.
 * English: load-languages_page_mlang_strings
 * French:  load-langues_page_mlang_strings
 */
function wpmtst_admin_polylang() {
	global $plugin_page;

	if ( isset( $plugin_page ) && 'mlang_strings' == $plugin_page ) {
		// Minor improvements to list table style
		$plugin_version = get_option( 'wpmtst_plugin_version' );
		wp_enqueue_style( 'wpmtst-admin-style-polylang', WPMTST_ADMIN_URL . 'css/polylang.css', array(), $plugin_version );

		// Register strings for translation
		wpmtst_form_fields_polylang( wpmtst_get_all_fields() );
		wpmtst_form_options_polylang( get_option( 'wpmtst_form_options' ) );
		wpmtst_readmore_polylang();
	}
}
add_action( 'admin_init', 'wpmtst_admin_polylang' );

/**
 * Help link on various settings screens.
 *
 * @param $context
 */
function wpmtst_help_link_polylang( $context ) {
	echo '<p>';
	echo '<span class="dashicons dashicons-info icon-blue"></span>&nbsp;';
	printf( __( 'Translate these fields in <a href="%s">Polylang String Translations</a>', 'strong-testimonials' ),
		admin_url( 'admin.php?page=mlang_strings&group=strong-testimonials-' . $context . '&paged=1' ) );
	echo '</p>';
}
