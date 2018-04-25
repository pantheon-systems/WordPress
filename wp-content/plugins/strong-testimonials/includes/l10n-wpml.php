<?php
/**
 * ----------------------------------------
 * WPML
 * ----------------------------------------
 */

/**
 * Add translation actions & filters.
 */
function wpmtst_l10n_filters_wpml() {

	// Admin style
	add_action( 'admin_head-wpml-string-translation/menu/string-translation.php', 'wpmtst_admin_scripts_wpml' );
	add_action( 'admin_head-edit-tags.php', 'wpmtst_admin_scripts_wpml' );

	// Translate
	remove_filter( 'wpmtst_l10n', 'wpmtst_l10n_default' );
	add_filter( 'wpmtst_l10n', 'wpmtst_l10n_wpml', 10, 3 );
	add_filter( 'wpmtst_l10n_cats', 'wpmtst_wpml_translate_object_ids', 10, 2 );
	add_filter( 'get_term', 'wpmtst_wpml_get_term', 10, 2 );

	// Update strings
	add_action( 'update_option_wpmtst_custom_forms', 'wpmtst_form_fields_wpml', 10, 2 );
	add_action( 'update_option_wpmtst_form_options', 'wpmtst_form_options_wpml', 10, 2 );
	add_action( 'wpmtst_view_saved', 'wpmtst_update_view_wpml' );

	// Help
	add_action( 'wpmtst_before_form_settings', 'wpmtst_help_link_wpml' );
	add_action( 'wpmtst_before_fields_settings', 'wpmtst_help_link_wpml' );
	add_action( 'wpmtst_after_notification_fields', 'wpmtst_help_link_wpml' );

}
add_action( 'init', 'wpmtst_l10n_filters_wpml', 20 );

/**
 * @param $string
 * @param $context
 * @param $name
 *
 * @return mixed
 */
function wpmtst_l10n_wpml( $string, $context, $name ) {
	return apply_filters( 'wpml_translate_single_string', $string, $context, $name );
}

/**
 * Find the equivalent term ID in the current language.
 *
 * @since 2.2.3
 *
 * @param $term
 * @param $tax
 * @return mixed
 */
function wpmtst_wpml_get_term( $term, $tax ) {
	if ( 'wpm-testimonial-category' == $tax ) {
		$term->term_id = apply_filters( 'wpmtst_wpml_translate_object_ids', $term->term_id );
	}

	return $term;
}

/**
 * Returns the translated object ID (post_type or term) or original if missing
 *
 * @param $object_id integer|string|array The ID/s of the objects to check and return
 * @param object|string $type object type: post, page, {custom post type name}, nav_menu, nav_menu_item, category, tag etc.
 * @return string|array of object ids
 */
function wpmtst_wpml_translate_object_ids( $object_id, $type = 'wpm-testimonial-category' ) {

	// if array
	if ( is_array( $object_id ) ) {
		$translated_object_ids = array();
		foreach ( $object_id as $id ) {
			$translated_object_ids[] = apply_filters( 'wpml_object_id', $id, $type, true );
		}
		return $translated_object_ids;
	}
	// if string
	elseif ( is_string( $object_id ) ) {
		// check if we have a comma separated ID string
		$is_comma_separated = strpos( $object_id,"," );

		if ( $is_comma_separated !== FALSE ) {
			// explode the comma to create an array of IDs
			$object_id     = explode( ',', $object_id );

			$translated_object_ids = array();
			foreach ( $object_id as $id ) {
				$translated_object_ids[] = apply_filters ( 'wpml_object_id', $id, $type, true );
			}

			// make sure the output is a comma separated string (the same way it came in!)
			return implode ( ',', $translated_object_ids );
		}
		// if we don't find a comma in the string then this is a single ID
		else {
			return apply_filters( 'wpml_object_id', intval( $object_id ), $type, true );
		}
	}
	// if int
	else {
		return apply_filters( 'wpml_object_id', $object_id, $type, true );
	}
}

/**
 * Load custom style for WPML.
 *
 * @since 1.21.0
 */
function wpmtst_admin_scripts_wpml() {
	$plugin_version = get_option( 'wpmtst_plugin_version' );
	wp_enqueue_style( 'wpmtst-admin-style-wpml', WPMTST_ADMIN_URL . 'css/wpml.css', array(), $plugin_version );
}

/**
 * Register form field strings.
 *
 * @param $oldvalue
 * @param $newvalue
 * @param string $option
 */
function wpmtst_form_fields_wpml( $oldvalue, $newvalue, $option = 'wpmtst_custom_forms' ) {
	// Reverse field order to match the form.
	$wpml = $newvalue[1]['fields'];
	krsort( $wpml );
	foreach ( $wpml as $field ) {
		$name    = $field['name'] . ' : ';
		$context = 'strong-testimonials-form-fields';

		/* Translators: A form field on the String Translation screen. */
		if ( isset( $field['after'] ) ) {
			do_action( 'wpml_register_single_string', $context, $name . __( 'after', 'strong-testimonials' ), $field['after'] );
		}

		if ( isset( $field['before'] ) ) {
			do_action( 'wpml_register_single_string', $context, $name . __( 'before', 'strong-testimonials' ), $field['before'] );
		}

		if ( isset( $field['placeholder'] ) ) {
			do_action( 'wpml_register_single_string', $context, $name . __( 'placeholder', 'strong-testimonials' ), $field['placeholder'] );
		}

		if ( isset( $field['label'] ) ) {
			do_action( 'wpml_register_single_string', $context, $name . __( 'label', 'strong-testimonials' ), $field['label'] );
		}

		if ( isset( $field['text'] ) ) {
			do_action( 'wpml_register_single_string', $context, $name . __( 'text', 'strong-testimonials' ), $field['text'] );
		}

		if ( isset( $field['default_form_value'] ) ) {
			do_action( 'wpml_register_single_string', $context, $name . __( 'default form value', 'strong-testimonials' ), $field['default_form_value'] );
		}

		if ( isset( $field['default_display_value'] ) ) {
			do_action( 'wpml_register_single_string', $context, $name . __( 'default display value', 'strong-testimonials' ), $field['default_display_value'] );
		}
	}
}

/**
 * Register form option strings.
 *
 * @param $oldvalue
 * @param $newvalue
 * @param string $option
 */
function wpmtst_form_options_wpml( $oldvalue, $newvalue, $option = 'wpmtst_form_options' ) {
	// Form messages. Reverse field order to match the form.
	$context = 'strong-testimonials-form-messages';
	$wpml    = $newvalue['messages'];
	krsort( $wpml );
	foreach ( $wpml as $key => $field ) {
		// We can translate here because the description was localized when added.
		do_action( 'wpml_register_single_string', $context, __( $field['description'], 'strong-testimonials' ), $field['text'] );
	}

	// Form notification
	$context = 'strong-testimonials-notification';
	do_action( 'wpml_register_single_string', $context, __( 'Email message', 'strong-testimonials' ), $newvalue['email_message'] );
	do_action( 'wpml_register_single_string', $context, __( 'Email subject', 'strong-testimonials' ), $newvalue['email_subject'] );
}

/**
 * Register "Read more" link text.
 *
 * @since 2.11.17
 *
 * @param $options
 */
function wpmtst_readmore_wpml( $options ) {
	$context = 'strong-testimonials-read-more';

	/* Translators: %s is the View ID. */
	$string = sprintf( __( 'View %s : Read more (testimonial)', 'strong-testimonials' ), $options['id'] );
	do_action( 'wpml_register_single_string', $context, $string, $options['more_post_text'] );

	$string = sprintf( __( 'View %s : Read more (page or post)', 'strong-testimonials' ), $options['id'] );
	do_action( 'wpml_register_single_string', $context, $string, $options['more_page_text'] );
}

/**
 * Update strings after updating a view.
 *
 * @since 2.11.17
 *
 * @param $view
 */
function wpmtst_update_view_wpml( $view ) {
	wpmtst_readmore_wpml(
		array(
			'id'             => $view['id'],
			'more_post_text' => $view['data']['more_post_text'],
			'more_page_text' => $view['data']['more_page_text'],
		)
	);
}

/**
 * Help link on various settings screens.
 *
 * @param $context
 */
function wpmtst_help_link_wpml( $context ) {
	echo '<p>';
	echo '<span class="dashicons dashicons-info icon-blue"></span>&nbsp;';
	printf( __( 'Translate these fields in <a href="%s">WPML String Translations</a>', 'strong-testimonials' ),
		admin_url( 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=strong-testimonials-' . $context ) );
	echo '</p>';
}
