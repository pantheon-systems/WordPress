<?php

use TrueBV\Punycode;

/**
 * Helper function to trigger displaying a form.
 *
 * @since 1.0.2
 *
 * @param mixed $form_id Form ID.
 * @param bool  $title   Form title.
 * @param bool  $desc    Form description.
 */
function wpforms_display( $form_id = false, $title = false, $desc = false ) {
	wpforms()->frontend->output( $form_id, $title, $desc );
}

/**
 * Perform json_decode and unslash.
 *
 * IMPORTANT: This function decodes the result of wpforms_encode() properly only if
 * wp_insert_post() or wp_update_post() were used after the data is encoded.
 * Both wp_insert_post() and wp_update_post() remove excessive slashes added by wpforms_encode().
 *
 * Using wpforms_decode() on wpforms_encode() result directly
 * (without using wp_insert_post() or wp_update_post() first) always returns null or false.
 *
 * @since 1.0.0
 *
 * @param string $data Data to decode.
 *
 * @return array|false|null
 */
function wpforms_decode( $data ) {

	if ( ! $data || empty( $data ) ) {
		return false;
	}

	return wp_unslash( json_decode( $data, true ) );
}

/**
 * Perform json_encode and wp_slash.
 *
 * IMPORTANT: This function adds excessive slashes to prevent data damage
 * by wp_insert_post() or wp_update_post() that use wp_unslash() on all the incoming data.
 *
 * Decoding the result of this function by wpforms_decode() directly
 * (without using wp_insert_post() or wp_update_post() first) always returns null or false.
 *
 * @since 1.3.1.3
 *
 * @param mixed $data Data to encode.
 *
 * @return string|false
 */
function wpforms_encode( $data = false ) {

	if ( empty( $data ) ) {
		return false;
	}

	return wp_slash( wp_json_encode( $data ) );
}

/**
 * Check if a string is a valid URL.
 *
 * @since 1.0.0
 * @since 1.5.8 Changed the pattern used to validate the URL.
 *
 * @param string $url Input URL.
 *
 * @return bool
 */
function wpforms_is_url( $url ) {

	// The pattern taken from https://gist.github.com/dperini/729294.
	// It is the best choice according to the https://mathiasbynens.be/demo/url-regex.
	$pattern = '%^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[/?#]\S*)?$%iu';

	if ( preg_match( $pattern, trim( $url ) ) ) {
		return true;
	}

	return false;
}

/**
 * Verify that an email is valid.
 * See the linked RFC.
 *
 * @see https://www.rfc-editor.org/rfc/inline-errata/rfc3696.html
 *
 * @since 1.7.3
 *
 * @param string $email Email address to verify.
 *
 * @return string|false Returns a valid email address on success, false on failure.
 */
function wpforms_is_email( $email ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

	static $punycode;

	// Do not allow callables, arrays and objects.
	if ( ! is_scalar( $email ) ) {
		return false;
	}

	// Allow smart tags in the email address.
	if ( preg_match( '/{.+?}/', $email ) ) {
		return $email;
	}

	// Email can't be longer than 254 octets,
	// otherwise it can't be used to send an email address (limitation in the MAIL and RCPT commands).
	// 1 octet = 8 bits = 1 byte.
	if ( strlen( $email ) > 254 ) {
		return false;
	}

	$email_arr = explode( '@', $email );

	if ( count( $email_arr ) !== 2 ) {
		return false;
	}

	list( $local, $domain ) = $email_arr;

	/**
	 * RFC requires local part to be no longer than 64 octets.
	 * Punycode library checks for 63 octets.
	 *
	 * @link https://github.com/true/php-punycode/blob/master/src/Punycode.php#L182.
	 */
	if ( strlen( $local ) > 63 ) {
		return false;
	}

	$domain_arr = explode( '.', $domain );

	foreach ( $domain_arr as $domain_label ) {
		$domain_label = trim( $domain_label );

		if ( ! $domain_label ) {
			return false;
		}

		// The RFC says: 'A DNS label may be no more than 63 octets long'.
		if ( strlen( $domain_label ) > 63 ) {
			return false;
		}
	}

	if ( ! $punycode ) {
		$punycode = new Punycode();
	}

	/**
	 * The wp_mail() uses phpMailer, which uses is_email() as verification callback.
	 * For verification, phpMailer sends the email address where the domain part is punycode encoded only.
	 * We follow here the same principle.
	 */
	$email_check = $local . '@' . $punycode->encode( $domain );

	// Other limitations are checked by the native WordPress function is_email().
	return is_email( $email_check ) ? $local . '@' . $domain : false;
}

/**
 * Check whether the string is json-encoded.
 *
 * @since 1.7.5
 *
 * @param string $string A string.
 *
 * @return bool
 */
function wpforms_is_json( $string ) {

	return (
		is_string( $string ) &&
		is_array( json_decode( $string, true ) ) &&
		json_last_error() === JSON_ERROR_NONE
	);
}

/**
 * Decode json-encoded string if it is in json format.
 *
 * @since 1.7.5
 *
 * @param string $string      A string.
 * @param bool   $associative Decode to the associative array if true. Decode to object if false.
 *
 * @return array|string
 */
function wpforms_json_decode( $string, $associative = false ) {

	$string = html_entity_decode( $string );

	if ( ! wpforms_is_json( $string ) ) {
		return $string;
	}

	return json_decode( $string, $associative );
}

/**
 * Get the current URL.
 *
 * @since 1.0.0
 * @since 1.7.2 Refactored based on the `home_url` function.
 *
 * @return string
 */
function wpforms_current_url() {

	$parsed_home_url = wp_parse_url( home_url() );

	$url = $parsed_home_url['scheme'] . '://' . $parsed_home_url['host'];

	if ( ! empty( $parsed_home_url['port'] ) ) {
		$url .= ':' . $parsed_home_url['port'];
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$url .= wp_unslash( $_SERVER['REQUEST_URI'] );

	return esc_url_raw( $url );
}

/**
 * Convert object to an array.
 *
 * @since 1.1.7
 *
 * @param object $object Object to convert.
 *
 * @return mixed
 */
function wpforms_object_to_array( $object ) {

	if ( ! is_object( $object ) && ! is_array( $object ) ) {
		return $object;
	}

	if ( is_object( $object ) ) {
		$object = get_object_vars( $object );
	}

	return array_map( 'wpforms_object_to_array', $object );
}

/**
 * Get the value of a specific WPForms setting.
 *
 * @since 1.0.0
 *
 * @param string $key
 * @param mixed  $default
 * @param string $option
 *
 * @return mixed
 */
function wpforms_setting( $key, $default = false, $option = 'wpforms_settings' ) {

	$key     = wpforms_sanitize_key( $key );
	$options = get_option( $option, false );
	$value   = is_array( $options ) && ! empty( $options[ $key ] ) ? wp_unslash( $options[ $key ] ) : $default;

	/**
	 * Allows plugin setting to be modified.
	 *
	 * @since 1.7.8
	 *
	 * @param mixed  $value   Setting value.
	 * @param string $key     Setting key.
	 * @param mixed  $default Setting default value.
	 * @param string $option  Settings option name.
	 */
	$value = apply_filters( 'wpforms_setting', $value, $key, $default, $option );

	return $value;
}

/**
 * Update plugin settings option and allow it to be filterable.
 *
 * @since 1.6.6
 *
 * @param array $settings A plugin settings array that is saved into options table.
 *
 * @return bool
 */
function wpforms_update_settings( $settings ) {

	/**
	 * Allows plugin settings to be modified before persisting in the database.
	 *
	 * @since 1.6.6
	 *
	 * @param array $settings An array of plugin settings to modify.
	 */
	$settings = (array) apply_filters( 'wpforms_update_settings', $settings );

	$updated = update_option( 'wpforms_settings', $settings );

	/**
	 * Fires after the plugin settings were persisted in the database.
	 *
	 * The `$updated` parameter allows to check whether the update was actually successful.
	 *
	 * @since 1.6.1
	 *
	 * @param array  $settings An array of plugin settings.
	 * @param bool   $updated  Whether an option was updated or not.
	 */
	do_action( 'wpforms_settings_updated', $settings, $updated );

	return $updated;
}

/**
 * Sanitize key, primarily used for looking up options.
 *
 * @since 1.3.9
 *
 * @param string $key
 *
 * @return string
 */
function wpforms_sanitize_key( $key = '' ) {
	return preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
}

/**
 * Check if form provided contains the specified field type.
 *
 * @since 1.0.5
 *
 * @param array|string $type
 * @param array|object $form
 * @param bool         $multiple
 *
 * @return bool
 */
function wpforms_has_field_type( $type, $form, $multiple = false ) {

	$form_data = '';
	$field     = false;
	$type      = (array) $type;

	if ( $multiple ) {
		foreach ( $form as $single_form ) {
			$field = wpforms_has_field_type( $type, $single_form );
			if ( $field ) {
				break;
			}
		}

		return $field;
	}

	if ( is_object( $form ) && ! empty( $form->post_content ) ) {
		$form_data = wpforms_decode( $form->post_content );
	} elseif ( is_array( $form ) ) {
		$form_data = $form;
	}

	if ( empty( $form_data['fields'] ) ) {
		return false;
	}

	foreach ( $form_data['fields'] as $single_field ) {
		if ( in_array( $single_field['type'], $type, true ) ) {
			$field = true;
			break;
		}
	}

	return $field;
}

/**
 * Check if form provided contains a field which a specific setting.
 *
 * @since 1.4.5
 *
 * @param string $setting
 * @param object|array  $form
 * @param bool   $multiple
 *
 * @return bool
 */
function wpforms_has_field_setting( $setting, $form, $multiple = false ) {

	$form_data = '';
	$field     = false;

	if ( $multiple ) {
		foreach ( $form as $single_form ) {
			$field = wpforms_has_field_setting( $setting, $single_form );
			if ( $field ) {
				break;
			}
		}

		return $field;
	}

	if ( is_object( $form ) && ! empty( $form->post_content ) ) {
		$form_data = wpforms_decode( $form->post_content );
	} elseif ( is_array( $form ) ) {
		$form_data = $form;
	}

	if ( empty( $form_data['fields'] ) ) {
		return false;
	}

	foreach ( $form_data['fields'] as $single_field ) {

		if ( ! empty( $single_field[ $setting ] ) ) {
			$field = true;
			break;
		}
	}

	return $field;
}

/**
 * Check if form provided contains Page Break, if so give details.
 *
 * @since 1.0.0
 *
 * @todo It is not used since 1.4.0. Probably, it should be deprecated and suggest using the wpforms_get_pagebreak_details() function.
 *
 * @param WP_Post|array $form Form data.
 *
 * @return int|bool Pages count or false.
 */
function wpforms_has_pagebreak( $form = false ) {

	if ( ! wpforms()->is_pro() ) {
		return false;
	}

	$form_data = '';
	$pagebreak = false;
	$pages     = 1;

	if ( is_object( $form ) && ! empty( $form->post_content ) ) {
		$form_data = wpforms_decode( $form->post_content );
	} elseif ( is_array( $form ) ) {
		$form_data = $form;
	}

	if ( empty( $form_data['fields'] ) ) {
		return false;
	}

	$fields = $form_data['fields'];

	foreach ( $fields as $field ) {

		if ( $field['type'] === 'pagebreak' && empty( $field['position'] ) ) {
			$pagebreak = true;

			$pages ++;
		}
	}

	if ( $pagebreak ) {
		return $pages;
	}

	return false;
}

/**
 * Try to find and return a top or bottom Page Break.
 *
 * @since 1.2.1
 *
 * @todo It is not used since 1.4.0. Probably, it should be deprecated and suggest using the wpforms_get_pagebreak_details() function.
 *
 * @param WP_Post|array $form Form data.
 * @param string|bool   $type Type of Page Break fields (top, bottom, pages or false).
 *
 * @return array|bool
 */
function wpforms_get_pagebreak( $form = false, $type = false ) {

	if ( ! wpforms()->is_pro() ) {
		return false;
	}

	$form_data = '';

	if ( is_object( $form ) && ! empty( $form->post_content ) ) {
		$form_data = wpforms_decode( $form->post_content );
	} elseif ( is_array( $form ) ) {
		$form_data = $form;
	}

	if ( empty( $form_data['fields'] ) ) {
		return false;
	}

	$fields = $form_data['fields'];
	$pages  = [];

	foreach ( $fields as $field ) {

		if ( $field['type'] !== 'pagebreak' ) {
			continue;
		}

		$position = ! empty( $field['position'] ) ? $field['position'] : false;

		if ( $type === 'pages' && $position !== 'bottom' ) {
			$pages[] = $field;
		} elseif ( $position === $type ) {
			return $field;
		}
	}

	if ( ! empty( $pages ) ) {
		return $pages;
	}

	return false;
}

/**
 * Return information about pages if the form has multiple pages.
 *
 * @since 1.3.7
 *
 * @param WP_Post|array $form Form data.
 *
 * @return false|array Page Break details or false.
 */
function wpforms_get_pagebreak_details( $form = false ) {

	if ( ! wpforms()->is_pro() ) {
		return false;
	}

	$details = [];
	$pages   = 1;

	if ( is_object( $form ) && ! empty( $form->post_content ) ) {
		$form_data = wpforms_decode( $form->post_content );
	} elseif ( is_array( $form ) ) {
		$form_data = $form;
	}

	if ( empty( $form_data['fields'] ) ) {
		return false;
	}

	foreach ( $form_data['fields'] as $field ) {

		if ( $field['type'] !== 'pagebreak' ) {
			continue;
		}

		if ( empty( $field['position'] ) ) {
			$pages ++;
			$details['total']   = $pages;
			$details['pages'][] = $field;
		} elseif ( $field['position'] === 'top' ) {
			$details['top'] = $field;
		} elseif ( $field['position'] === 'bottom' ) {
			$details['bottom'] = $field;
		}
	}

	if ( ! empty( $details ) ) {
		$details['top']     = empty( $details['top'] ) ? [] : $details['top'];
		$details['bottom']  = empty( $details['bottom'] ) ? [] : $details['bottom'];
		$details['current'] = 1;

		return $details;
	}

	return false;
}

/**
 * Format, sanitize, and return/echo HTML element ID, classes, attributes,
 * and data attributes.
 *
 * @since 1.3.7
 *
 * @param string $id
 * @param array  $class
 * @param array  $datas
 * @param array  $atts
 * @param bool   $echo
 *
 * @return string
 */
function wpforms_html_attributes( $id = '', $class = array(), $datas = array(), $atts = array(), $echo = false ) {

	$id    = trim( $id );
	$parts = array();

	if ( ! empty( $id ) ) {
		$id = sanitize_html_class( $id );
		if ( ! empty( $id ) ) {
			$parts[] = 'id="' . $id . '"';
		}
	}

	if ( ! empty( $class ) ) {
		$class = wpforms_sanitize_classes( $class, true );
		if ( ! empty( $class ) ) {
			$parts[] = 'class="' . $class . '"';
		}
	}

	if ( ! empty( $datas ) ) {
		foreach ( $datas as $data => $val ) {
			$parts[] = 'data-' . sanitize_html_class( $data ) . '="' . esc_attr( $val ) . '"';
		}
	}

	if ( ! empty( $atts ) ) {
		foreach ( $atts as $att => $val ) {
			if ( '0' === (string) $val || ! empty( $val ) ) {
				if ( $att[0] === '[' ) {
					// Handle special case for bound attributes in AMP.
					$escaped_att = '[' . sanitize_html_class( trim( $att, '[]' ) ) . ']';
				} else {
					$escaped_att = sanitize_html_class( $att );
				}
				$parts[] = $escaped_att . '="' . esc_attr( $val ) . '"';
			}
		}
	}

	$output = implode( ' ', $parts );

	if ( $echo ) {
		echo trim( $output ); // phpcs:ignore
	} else {
		return trim( $output );
	}
}

/**
 * Sanitize string of CSS classes.
 *
 * @since 1.2.1
 *
 * @param array|string $classes CSS classes.
 * @param bool         $convert True will convert strings to array and vice versa.
 *
 * @return string|array
 */
function wpforms_sanitize_classes( $classes, $convert = false ) {

	$array = is_array( $classes );
	$css   = [];

	if ( ! empty( $classes ) ) {
		if ( ! $array ) {
			$classes = explode( ' ', trim( $classes ) );
		}
		foreach ( array_unique( $classes ) as $class ) {
			if ( ! empty( $class ) ) {
				$css[] = sanitize_html_class( $class );
			}
		}
	}

	if ( $array ) {
		return $convert ? implode( ' ', $css ) : $css;
	}

	return $convert ? $css : implode( ' ', $css );
}

/**
 * Convert a file size provided, such as "2M", to bytes.
 *
 * @link http://stackoverflow.com/a/22500394
 *
 * @since 1.0.0
 *
 * @param string $size
 *
 * @return int
 */
function wpforms_size_to_bytes( $size ) {

	if ( is_numeric( $size ) ) {
		return $size;
	}

	$suffix = substr( $size, - 1 );
	$value  = substr( $size, 0, - 1 );

	switch ( strtoupper( $suffix ) ) {
		case 'P':
			$value *= 1024;

		case 'T':
			$value *= 1024;

		case 'G':
			$value *= 1024;

		case 'M':
			$value *= 1024;

		case 'K':
			$value *= 1024;
			break;
	}

	return $value;
}

/**
 * Convert a file size provided, such as "2M", to bytes.
 *
 * @link http://stackoverflow.com/a/22500394
 *
 * @since 1.0.0
 *
 * @param bool $bytes
 *
 * @return mixed
 */
function wpforms_max_upload( $bytes = false ) {

	$max = wp_max_upload_size();
	if ( $bytes ) {
		return $max;
	}

	return size_format( $max );
}

/**
 * Retrieve actual fields from a form.
 *
 * Non-posting elements such as section divider, page break, and HTML are
 * automatically excluded. Optionally a white list can be provided.
 *
 * @since 1.0.0
 *
 * @param mixed $form
 * @param array $whitelist
 *
 * @return mixed boolean or array
 */
function wpforms_get_form_fields( $form = false, $whitelist = array() ) {

	// Accept form (post) object or form ID.
	if ( is_object( $form ) ) {
		$form = wpforms_decode( $form->post_content );
	} elseif ( is_numeric( $form ) ) {
		$form = wpforms()->form->get(
			$form,
			array(
				'content_only' => true,
			)
		);
	}

	// White list of field types to allow.
	$allowed_form_fields = [
		'address',
		'checkbox',
		'date-time',
		'email',
		'file-upload',
		'gdpr-checkbox',
		'hidden',
		'likert_scale',
		'name',
		'net_promoter_score',
		'number',
		'number-slider',
		'payment-checkbox',
		'payment-multiple',
		'payment-select',
		'payment-single',
		'payment-total',
		'phone',
		'radio',
		'rating',
		'richtext',
		'select',
		'signature',
		'text',
		'textarea',
		'url',
	];
	$allowed_form_fields = apply_filters( 'wpforms_get_form_fields_allowed', $allowed_form_fields );

	if ( ! is_array( $form ) || empty( $form['fields'] ) ) {
		return false;
	}

	$whitelist = ! empty( $whitelist ) ? $whitelist : $allowed_form_fields;

	$form_fields = $form['fields'];

	foreach ( $form_fields as $id => $form_field ) {
		if ( ! in_array( $form_field['type'], $whitelist, true ) ) {
			unset( $form_fields[ $id ] );
		}
	}

	return $form_fields;
}

/**
 * Conditional logic form fields supported.
 *
 * @since 1.5.2
 *
 * @return array
 */
function wpforms_get_conditional_logic_form_fields_supported() {

	$fields_supported = [
		'checkbox',
		'email',
		'hidden',
		'net_promoter_score',
		'number',
		'number-slider',
		'payment-checkbox',
		'payment-multiple',
		'payment-select',
		'radio',
		'rating',
		'richtext',
		'select',
		'text',
		'textarea',
		'url',
	];

	return apply_filters( 'wpforms_get_conditional_logic_form_fields_supported', $fields_supported );
}

/**
 * Get meta key value for a form field.
 *
 * @since 1.1.9
 *
 * @param int|string $id        Field ID.
 * @param string     $key       Meta key.
 * @param mixed      $form_data Form data array.
 *
 * @return string
 */
function wpforms_get_form_field_meta( $id = '', $key = '', $form_data = '' ) {

	if ( empty( $id ) || empty( $key ) || empty( $form_data ) ) {
		return '';
	}

	if ( ! empty( $form_data['fields'][ $id ]['meta'][ $key ] ) ) {
		return $form_data['fields'][ $id ]['meta'][ $key ];
	}

	return '';
}

/**
 * Get meta key value for a form field.
 *
 * @since 1.3.1
 * @since 1.5.0 More strict parameters. Always return an array.
 *
 * @param string $key       Meta key.
 * @param string $value     Meta value to check against.
 * @param array  $form_data Form data array.
 *
 * @return array|bool Empty array, when no data is found.
 */
function wpforms_get_form_fields_by_meta( $key, $value, $form_data ) {

	$found = array();

	if ( empty( $key ) || empty( $value ) || empty( $form_data['fields'] ) ) {
		return $found;
	}

	foreach ( $form_data['fields'] as $id => $field ) {

		if ( ! empty( $field['meta'][ $key ] ) && $value === $field['meta'][ $key ] ) {
			$found[ $id ] = $field;
		}
	}

	return $found;
}

/**
 * US States.
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpforms_us_states() {

	$states = array(
		'AL' => esc_html__( 'Alabama', 'wpforms-lite' ),
		'AK' => esc_html__( 'Alaska', 'wpforms-lite' ),
		'AZ' => esc_html__( 'Arizona', 'wpforms-lite' ),
		'AR' => esc_html__( 'Arkansas', 'wpforms-lite' ),
		'CA' => esc_html__( 'California', 'wpforms-lite' ),
		'CO' => esc_html__( 'Colorado', 'wpforms-lite' ),
		'CT' => esc_html__( 'Connecticut', 'wpforms-lite' ),
		'DE' => esc_html__( 'Delaware', 'wpforms-lite' ),
		'DC' => esc_html__( 'District of Columbia', 'wpforms-lite' ),
		'FL' => esc_html__( 'Florida', 'wpforms-lite' ),
		'GA' => esc_html_x( 'Georgia', 'US State', 'wpforms-lite' ),
		'HI' => esc_html__( 'Hawaii', 'wpforms-lite' ),
		'ID' => esc_html__( 'Idaho', 'wpforms-lite' ),
		'IL' => esc_html__( 'Illinois', 'wpforms-lite' ),
		'IN' => esc_html__( 'Indiana', 'wpforms-lite' ),
		'IA' => esc_html__( 'Iowa', 'wpforms-lite' ),
		'KS' => esc_html__( 'Kansas', 'wpforms-lite' ),
		'KY' => esc_html__( 'Kentucky', 'wpforms-lite' ),
		'LA' => esc_html__( 'Louisiana', 'wpforms-lite' ),
		'ME' => esc_html__( 'Maine', 'wpforms-lite' ),
		'MD' => esc_html__( 'Maryland', 'wpforms-lite' ),
		'MA' => esc_html__( 'Massachusetts', 'wpforms-lite' ),
		'MI' => esc_html__( 'Michigan', 'wpforms-lite' ),
		'MN' => esc_html__( 'Minnesota', 'wpforms-lite' ),
		'MS' => esc_html__( 'Mississippi', 'wpforms-lite' ),
		'MO' => esc_html__( 'Missouri', 'wpforms-lite' ),
		'MT' => esc_html__( 'Montana', 'wpforms-lite' ),
		'NE' => esc_html__( 'Nebraska', 'wpforms-lite' ),
		'NV' => esc_html__( 'Nevada', 'wpforms-lite' ),
		'NH' => esc_html__( 'New Hampshire', 'wpforms-lite' ),
		'NJ' => esc_html__( 'New Jersey', 'wpforms-lite' ),
		'NM' => esc_html__( 'New Mexico', 'wpforms-lite' ),
		'NY' => esc_html__( 'New York', 'wpforms-lite' ),
		'NC' => esc_html__( 'North Carolina', 'wpforms-lite' ),
		'ND' => esc_html__( 'North Dakota', 'wpforms-lite' ),
		'OH' => esc_html__( 'Ohio', 'wpforms-lite' ),
		'OK' => esc_html__( 'Oklahoma', 'wpforms-lite' ),
		'OR' => esc_html__( 'Oregon', 'wpforms-lite' ),
		'PA' => esc_html__( 'Pennsylvania', 'wpforms-lite' ),
		'RI' => esc_html__( 'Rhode Island', 'wpforms-lite' ),
		'SC' => esc_html__( 'South Carolina', 'wpforms-lite' ),
		'SD' => esc_html__( 'South Dakota', 'wpforms-lite' ),
		'TN' => esc_html__( 'Tennessee', 'wpforms-lite' ),
		'TX' => esc_html__( 'Texas', 'wpforms-lite' ),
		'UT' => esc_html__( 'Utah', 'wpforms-lite' ),
		'VT' => esc_html__( 'Vermont', 'wpforms-lite' ),
		'VA' => esc_html__( 'Virginia', 'wpforms-lite' ),
		'WA' => esc_html__( 'Washington', 'wpforms-lite' ),
		'WV' => esc_html__( 'West Virginia', 'wpforms-lite' ),
		'WI' => esc_html__( 'Wisconsin', 'wpforms-lite' ),
		'WY' => esc_html__( 'Wyoming', 'wpforms-lite' ),
	);

	return apply_filters( 'wpforms_us_states', $states );
}

/**
 * Countries.
 *
 * @since 1.0.0
 *
 * @return array
 */
function wpforms_countries() {

	$countries = array(
		'AF' => esc_html__( 'Afghanistan', 'wpforms-lite' ),
		'AX' => esc_html__( 'Åland Islands', 'wpforms-lite' ),
		'AL' => esc_html__( 'Albania', 'wpforms-lite' ),
		'DZ' => esc_html__( 'Algeria', 'wpforms-lite' ),
		'AS' => esc_html__( 'American Samoa', 'wpforms-lite' ),
		'AD' => esc_html__( 'Andorra', 'wpforms-lite' ),
		'AO' => esc_html__( 'Angola', 'wpforms-lite' ),
		'AI' => esc_html__( 'Anguilla', 'wpforms-lite' ),
		'AQ' => esc_html__( 'Antarctica', 'wpforms-lite' ),
		'AG' => esc_html__( 'Antigua and Barbuda', 'wpforms-lite' ),
		'AR' => esc_html__( 'Argentina', 'wpforms-lite' ),
		'AM' => esc_html__( 'Armenia', 'wpforms-lite' ),
		'AW' => esc_html__( 'Aruba', 'wpforms-lite' ),
		'AU' => esc_html__( 'Australia', 'wpforms-lite' ),
		'AT' => esc_html__( 'Austria', 'wpforms-lite' ),
		'AZ' => esc_html__( 'Azerbaijan', 'wpforms-lite' ),
		'BS' => esc_html__( 'Bahamas', 'wpforms-lite' ),
		'BH' => esc_html__( 'Bahrain', 'wpforms-lite' ),
		'BD' => esc_html__( 'Bangladesh', 'wpforms-lite' ),
		'BB' => esc_html__( 'Barbados', 'wpforms-lite' ),
		'BY' => esc_html__( 'Belarus', 'wpforms-lite' ),
		'BE' => esc_html__( 'Belgium', 'wpforms-lite' ),
		'BZ' => esc_html__( 'Belize', 'wpforms-lite' ),
		'BJ' => esc_html__( 'Benin', 'wpforms-lite' ),
		'BM' => esc_html__( 'Bermuda', 'wpforms-lite' ),
		'BT' => esc_html__( 'Bhutan', 'wpforms-lite' ),
		'BO' => esc_html__( 'Bolivia (Plurinational State of)', 'wpforms-lite' ),
		'BQ' => esc_html__( 'Bonaire, Saint Eustatius and Saba', 'wpforms-lite' ),
		'BA' => esc_html__( 'Bosnia and Herzegovina', 'wpforms-lite' ),
		'BW' => esc_html__( 'Botswana', 'wpforms-lite' ),
		'BV' => esc_html__( 'Bouvet Island', 'wpforms-lite' ),
		'BR' => esc_html__( 'Brazil', 'wpforms-lite' ),
		'IO' => esc_html__( 'British Indian Ocean Territory', 'wpforms-lite' ),
		'BN' => esc_html__( 'Brunei Darussalam', 'wpforms-lite' ),
		'BG' => esc_html__( 'Bulgaria', 'wpforms-lite' ),
		'BF' => esc_html__( 'Burkina Faso', 'wpforms-lite' ),
		'BI' => esc_html__( 'Burundi', 'wpforms-lite' ),
		'CV' => esc_html__( 'Cabo Verde', 'wpforms-lite' ),
		'KH' => esc_html__( 'Cambodia', 'wpforms-lite' ),
		'CM' => esc_html__( 'Cameroon', 'wpforms-lite' ),
		'CA' => esc_html__( 'Canada', 'wpforms-lite' ),
		'KY' => esc_html__( 'Cayman Islands', 'wpforms-lite' ),
		'CF' => esc_html__( 'Central African Republic', 'wpforms-lite' ),
		'TD' => esc_html__( 'Chad', 'wpforms-lite' ),
		'CL' => esc_html__( 'Chile', 'wpforms-lite' ),
		'CN' => esc_html__( 'China', 'wpforms-lite' ),
		'CX' => esc_html__( 'Christmas Island', 'wpforms-lite' ),
		'CC' => esc_html__( 'Cocos (Keeling) Islands', 'wpforms-lite' ),
		'CO' => esc_html__( 'Colombia', 'wpforms-lite' ),
		'KM' => esc_html__( 'Comoros', 'wpforms-lite' ),
		'CG' => esc_html__( 'Congo', 'wpforms-lite' ),
		'CD' => esc_html__( 'Congo (Democratic Republic of the)', 'wpforms-lite' ),
		'CK' => esc_html__( 'Cook Islands', 'wpforms-lite' ),
		'CR' => esc_html__( 'Costa Rica', 'wpforms-lite' ),
		'CI' => esc_html__( 'Côte d\'Ivoire', 'wpforms-lite' ),
		'HR' => esc_html__( 'Croatia', 'wpforms-lite' ),
		'CU' => esc_html__( 'Cuba', 'wpforms-lite' ),
		'CW' => esc_html__( 'Curaçao', 'wpforms-lite' ),
		'CY' => esc_html__( 'Cyprus', 'wpforms-lite' ),
		'CZ' => esc_html__( 'Czech Republic', 'wpforms-lite' ),
		'DK' => esc_html__( 'Denmark', 'wpforms-lite' ),
		'DJ' => esc_html__( 'Djibouti', 'wpforms-lite' ),
		'DM' => esc_html__( 'Dominica', 'wpforms-lite' ),
		'DO' => esc_html__( 'Dominican Republic', 'wpforms-lite' ),
		'EC' => esc_html__( 'Ecuador', 'wpforms-lite' ),
		'EG' => esc_html__( 'Egypt', 'wpforms-lite' ),
		'SV' => esc_html__( 'El Salvador', 'wpforms-lite' ),
		'GQ' => esc_html__( 'Equatorial Guinea', 'wpforms-lite' ),
		'ER' => esc_html__( 'Eritrea', 'wpforms-lite' ),
		'EE' => esc_html__( 'Estonia', 'wpforms-lite' ),
		'ET' => esc_html__( 'Ethiopia', 'wpforms-lite' ),
		'FK' => esc_html__( 'Falkland Islands (Malvinas)', 'wpforms-lite' ),
		'FO' => esc_html__( 'Faroe Islands', 'wpforms-lite' ),
		'FJ' => esc_html__( 'Fiji', 'wpforms-lite' ),
		'FI' => esc_html__( 'Finland', 'wpforms-lite' ),
		'FR' => esc_html__( 'France', 'wpforms-lite' ),
		'GF' => esc_html__( 'French Guiana', 'wpforms-lite' ),
		'PF' => esc_html__( 'French Polynesia', 'wpforms-lite' ),
		'TF' => esc_html__( 'French Southern Territories', 'wpforms-lite' ),
		'GA' => esc_html__( 'Gabon', 'wpforms-lite' ),
		'GM' => esc_html__( 'Gambia', 'wpforms-lite' ),
		'GE' => esc_html_x( 'Georgia', 'Country', 'wpforms-lite' ),
		'DE' => esc_html__( 'Germany', 'wpforms-lite' ),
		'GH' => esc_html__( 'Ghana', 'wpforms-lite' ),
		'GI' => esc_html__( 'Gibraltar', 'wpforms-lite' ),
		'GR' => esc_html__( 'Greece', 'wpforms-lite' ),
		'GL' => esc_html__( 'Greenland', 'wpforms-lite' ),
		'GD' => esc_html__( 'Grenada', 'wpforms-lite' ),
		'GP' => esc_html__( 'Guadeloupe', 'wpforms-lite' ),
		'GU' => esc_html__( 'Guam', 'wpforms-lite' ),
		'GT' => esc_html__( 'Guatemala', 'wpforms-lite' ),
		'GG' => esc_html__( 'Guernsey', 'wpforms-lite' ),
		'GN' => esc_html__( 'Guinea', 'wpforms-lite' ),
		'GW' => esc_html__( 'Guinea-Bissau', 'wpforms-lite' ),
		'GY' => esc_html__( 'Guyana', 'wpforms-lite' ),
		'HT' => esc_html__( 'Haiti', 'wpforms-lite' ),
		'HM' => esc_html__( 'Heard Island and McDonald Islands', 'wpforms-lite' ),
		'HN' => esc_html__( 'Honduras', 'wpforms-lite' ),
		'HK' => esc_html__( 'Hong Kong', 'wpforms-lite' ),
		'HU' => esc_html__( 'Hungary', 'wpforms-lite' ),
		'IS' => esc_html__( 'Iceland', 'wpforms-lite' ),
		'IN' => esc_html__( 'India', 'wpforms-lite' ),
		'ID' => esc_html__( 'Indonesia', 'wpforms-lite' ),
		'IR' => esc_html__( 'Iran (Islamic Republic of)', 'wpforms-lite' ),
		'IQ' => esc_html__( 'Iraq', 'wpforms-lite' ),
		'IE' => esc_html__( 'Ireland (Republic of)', 'wpforms-lite' ),
		'IM' => esc_html__( 'Isle of Man', 'wpforms-lite' ),
		'IL' => esc_html__( 'Israel', 'wpforms-lite' ),
		'IT' => esc_html__( 'Italy', 'wpforms-lite' ),
		'JM' => esc_html__( 'Jamaica', 'wpforms-lite' ),
		'JP' => esc_html__( 'Japan', 'wpforms-lite' ),
		'JE' => esc_html__( 'Jersey', 'wpforms-lite' ),
		'JO' => esc_html__( 'Jordan', 'wpforms-lite' ),
		'KZ' => esc_html__( 'Kazakhstan', 'wpforms-lite' ),
		'KE' => esc_html__( 'Kenya', 'wpforms-lite' ),
		'KI' => esc_html__( 'Kiribati', 'wpforms-lite' ),
		'KP' => esc_html__( 'Korea (Democratic People\'s Republic of)', 'wpforms-lite' ),
		'KR' => esc_html__( 'Korea (Republic of)', 'wpforms-lite' ),
		'XK' => esc_html__( 'Kosovo', 'wpforms-lite' ),
		'KW' => esc_html__( 'Kuwait', 'wpforms-lite' ),
		'KG' => esc_html__( 'Kyrgyzstan', 'wpforms-lite' ),
		'LA' => esc_html__( 'Lao People\'s Democratic Republic', 'wpforms-lite' ),
		'LV' => esc_html__( 'Latvia', 'wpforms-lite' ),
		'LB' => esc_html__( 'Lebanon', 'wpforms-lite' ),
		'LS' => esc_html__( 'Lesotho', 'wpforms-lite' ),
		'LR' => esc_html__( 'Liberia', 'wpforms-lite' ),
		'LY' => esc_html__( 'Libya', 'wpforms-lite' ),
		'LI' => esc_html__( 'Liechtenstein', 'wpforms-lite' ),
		'LT' => esc_html__( 'Lithuania', 'wpforms-lite' ),
		'LU' => esc_html__( 'Luxembourg', 'wpforms-lite' ),
		'MO' => esc_html__( 'Macao', 'wpforms-lite' ),
		'MK' => esc_html__( 'North Macedonia (Republic of)', 'wpforms-lite' ),
		'MG' => esc_html__( 'Madagascar', 'wpforms-lite' ),
		'MW' => esc_html__( 'Malawi', 'wpforms-lite' ),
		'MY' => esc_html__( 'Malaysia', 'wpforms-lite' ),
		'MV' => esc_html__( 'Maldives', 'wpforms-lite' ),
		'ML' => esc_html__( 'Mali', 'wpforms-lite' ),
		'MT' => esc_html__( 'Malta', 'wpforms-lite' ),
		'MH' => esc_html__( 'Marshall Islands', 'wpforms-lite' ),
		'MQ' => esc_html__( 'Martinique', 'wpforms-lite' ),
		'MR' => esc_html__( 'Mauritania', 'wpforms-lite' ),
		'MU' => esc_html__( 'Mauritius', 'wpforms-lite' ),
		'YT' => esc_html__( 'Mayotte', 'wpforms-lite' ),
		'MX' => esc_html__( 'Mexico', 'wpforms-lite' ),
		'FM' => esc_html__( 'Micronesia (Federated States of)', 'wpforms-lite' ),
		'MD' => esc_html__( 'Moldova (Republic of)', 'wpforms-lite' ),
		'MC' => esc_html__( 'Monaco', 'wpforms-lite' ),
		'MN' => esc_html__( 'Mongolia', 'wpforms-lite' ),
		'ME' => esc_html__( 'Montenegro', 'wpforms-lite' ),
		'MS' => esc_html__( 'Montserrat', 'wpforms-lite' ),
		'MA' => esc_html__( 'Morocco', 'wpforms-lite' ),
		'MZ' => esc_html__( 'Mozambique', 'wpforms-lite' ),
		'MM' => esc_html__( 'Myanmar', 'wpforms-lite' ),
		'NA' => esc_html__( 'Namibia', 'wpforms-lite' ),
		'NR' => esc_html__( 'Nauru', 'wpforms-lite' ),
		'NP' => esc_html__( 'Nepal', 'wpforms-lite' ),
		'NL' => esc_html__( 'Netherlands', 'wpforms-lite' ),
		'NC' => esc_html__( 'New Caledonia', 'wpforms-lite' ),
		'NZ' => esc_html__( 'New Zealand', 'wpforms-lite' ),
		'NI' => esc_html__( 'Nicaragua', 'wpforms-lite' ),
		'NE' => esc_html__( 'Niger', 'wpforms-lite' ),
		'NG' => esc_html__( 'Nigeria', 'wpforms-lite' ),
		'NU' => esc_html__( 'Niue', 'wpforms-lite' ),
		'NF' => esc_html__( 'Norfolk Island', 'wpforms-lite' ),
		'MP' => esc_html__( 'Northern Mariana Islands', 'wpforms-lite' ),
		'NO' => esc_html__( 'Norway', 'wpforms-lite' ),
		'OM' => esc_html__( 'Oman', 'wpforms-lite' ),
		'PK' => esc_html__( 'Pakistan', 'wpforms-lite' ),
		'PW' => esc_html__( 'Palau', 'wpforms-lite' ),
		'PS' => esc_html__( 'Palestine (State of)', 'wpforms-lite' ),
		'PA' => esc_html__( 'Panama', 'wpforms-lite' ),
		'PG' => esc_html__( 'Papua New Guinea', 'wpforms-lite' ),
		'PY' => esc_html__( 'Paraguay', 'wpforms-lite' ),
		'PE' => esc_html__( 'Peru', 'wpforms-lite' ),
		'PH' => esc_html__( 'Philippines', 'wpforms-lite' ),
		'PN' => esc_html__( 'Pitcairn', 'wpforms-lite' ),
		'PL' => esc_html__( 'Poland', 'wpforms-lite' ),
		'PT' => esc_html__( 'Portugal', 'wpforms-lite' ),
		'PR' => esc_html__( 'Puerto Rico', 'wpforms-lite' ),
		'QA' => esc_html__( 'Qatar', 'wpforms-lite' ),
		'RE' => esc_html__( 'Réunion', 'wpforms-lite' ),
		'RO' => esc_html__( 'Romania', 'wpforms-lite' ),
		'RU' => esc_html__( 'Russian Federation', 'wpforms-lite' ),
		'RW' => esc_html__( 'Rwanda', 'wpforms-lite' ),
		'BL' => esc_html__( 'Saint Barthélemy', 'wpforms-lite' ),
		'SH' => esc_html__( 'Saint Helena, Ascension and Tristan da Cunha', 'wpforms-lite' ),
		'KN' => esc_html__( 'Saint Kitts and Nevis', 'wpforms-lite' ),
		'LC' => esc_html__( 'Saint Lucia', 'wpforms-lite' ),
		'MF' => esc_html__( 'Saint Martin (French part)', 'wpforms-lite' ),
		'PM' => esc_html__( 'Saint Pierre and Miquelon', 'wpforms-lite' ),
		'VC' => esc_html__( 'Saint Vincent and the Grenadines', 'wpforms-lite' ),
		'WS' => esc_html__( 'Samoa', 'wpforms-lite' ),
		'SM' => esc_html__( 'San Marino', 'wpforms-lite' ),
		'ST' => esc_html__( 'Sao Tome and Principe', 'wpforms-lite' ),
		'SA' => esc_html__( 'Saudi Arabia', 'wpforms-lite' ),
		'SN' => esc_html__( 'Senegal', 'wpforms-lite' ),
		'RS' => esc_html__( 'Serbia', 'wpforms-lite' ),
		'SC' => esc_html__( 'Seychelles', 'wpforms-lite' ),
		'SL' => esc_html__( 'Sierra Leone', 'wpforms-lite' ),
		'SG' => esc_html__( 'Singapore', 'wpforms-lite' ),
		'SX' => esc_html__( 'Sint Maarten (Dutch part)', 'wpforms-lite' ),
		'SK' => esc_html__( 'Slovakia', 'wpforms-lite' ),
		'SI' => esc_html__( 'Slovenia', 'wpforms-lite' ),
		'SB' => esc_html__( 'Solomon Islands', 'wpforms-lite' ),
		'SO' => esc_html__( 'Somalia', 'wpforms-lite' ),
		'ZA' => esc_html__( 'South Africa', 'wpforms-lite' ),
		'GS' => esc_html__( 'South Georgia and the South Sandwich Islands', 'wpforms-lite' ),
		'SS' => esc_html__( 'South Sudan', 'wpforms-lite' ),
		'ES' => esc_html__( 'Spain', 'wpforms-lite' ),
		'LK' => esc_html__( 'Sri Lanka', 'wpforms-lite' ),
		'SD' => esc_html__( 'Sudan', 'wpforms-lite' ),
		'SR' => esc_html__( 'Suriname', 'wpforms-lite' ),
		'SJ' => esc_html__( 'Svalbard and Jan Mayen', 'wpforms-lite' ),
		'SZ' => esc_html__( 'Eswatini (Kingdom of)', 'wpforms-lite' ),
		'SE' => esc_html__( 'Sweden', 'wpforms-lite' ),
		'CH' => esc_html__( 'Switzerland', 'wpforms-lite' ),
		'SY' => esc_html__( 'Syrian Arab Republic', 'wpforms-lite' ),
		'TW' => esc_html__( 'Taiwan, Republic of China', 'wpforms-lite' ),
		'TJ' => esc_html__( 'Tajikistan', 'wpforms-lite' ),
		'TZ' => esc_html__( 'Tanzania (United Republic of)', 'wpforms-lite' ),
		'TH' => esc_html__( 'Thailand', 'wpforms-lite' ),
		'TL' => esc_html__( 'Timor-Leste', 'wpforms-lite' ),
		'TG' => esc_html__( 'Togo', 'wpforms-lite' ),
		'TK' => esc_html__( 'Tokelau', 'wpforms-lite' ),
		'TO' => esc_html__( 'Tonga', 'wpforms-lite' ),
		'TT' => esc_html__( 'Trinidad and Tobago', 'wpforms-lite' ),
		'TN' => esc_html__( 'Tunisia', 'wpforms-lite' ),
		'TR' => esc_html__( 'Türkiye', 'wpforms-lite' ),
		'TM' => esc_html__( 'Turkmenistan', 'wpforms-lite' ),
		'TC' => esc_html__( 'Turks and Caicos Islands', 'wpforms-lite' ),
		'TV' => esc_html__( 'Tuvalu', 'wpforms-lite' ),
		'UG' => esc_html__( 'Uganda', 'wpforms-lite' ),
		'UA' => esc_html__( 'Ukraine', 'wpforms-lite' ),
		'AE' => esc_html__( 'United Arab Emirates', 'wpforms-lite' ),
		'GB' => esc_html__( 'United Kingdom of Great Britain and Northern Ireland', 'wpforms-lite' ),
		'US' => esc_html__( 'United States of America', 'wpforms-lite' ),
		'UM' => esc_html__( 'United States Minor Outlying Islands', 'wpforms-lite' ),
		'UY' => esc_html__( 'Uruguay', 'wpforms-lite' ),
		'UZ' => esc_html__( 'Uzbekistan', 'wpforms-lite' ),
		'VU' => esc_html__( 'Vanuatu', 'wpforms-lite' ),
		'VA' => esc_html__( 'Vatican City State', 'wpforms-lite' ),
		'VE' => esc_html__( 'Venezuela (Bolivarian Republic of)', 'wpforms-lite' ),
		'VN' => esc_html__( 'Vietnam', 'wpforms-lite' ),
		'VG' => esc_html__( 'Virgin Islands (British)', 'wpforms-lite' ),
		'VI' => esc_html__( 'Virgin Islands (U.S.)', 'wpforms-lite' ),
		'WF' => esc_html__( 'Wallis and Futuna', 'wpforms-lite' ),
		'EH' => esc_html__( 'Western Sahara', 'wpforms-lite' ),
		'YE' => esc_html__( 'Yemen', 'wpforms-lite' ),
		'ZM' => esc_html__( 'Zambia', 'wpforms-lite' ),
		'ZW' => esc_html__( 'Zimbabwe', 'wpforms-lite' ),
	);

	return apply_filters( 'wpforms_countries', $countries );
}

/**
 * Calendar Months.
 *
 * @since 1.3.7
 *
 * @return array
 */
function wpforms_months() {

	$months = array(
		'Jan' => esc_html__( 'January', 'wpforms-lite' ),
		'Feb' => esc_html__( 'February', 'wpforms-lite' ),
		'Mar' => esc_html__( 'March', 'wpforms-lite' ),
		'Apr' => esc_html__( 'April', 'wpforms-lite' ),
		'May' => esc_html__( 'May', 'wpforms-lite' ),
		'Jun' => esc_html__( 'June', 'wpforms-lite' ),
		'Jul' => esc_html__( 'July', 'wpforms-lite' ),
		'Aug' => esc_html__( 'August', 'wpforms-lite' ),
		'Sep' => esc_html__( 'September', 'wpforms-lite' ),
		'Oct' => esc_html__( 'October', 'wpforms-lite' ),
		'Nov' => esc_html__( 'November', 'wpforms-lite' ),
		'Dec' => esc_html__( 'December', 'wpforms-lite' ),
	);

	return apply_filters( 'wpforms_months', $months );
}

/**
 * Calendar Days.
 *
 * @since 1.3.7
 *
 * @return array
 */
function wpforms_days() {

	$days = array(
		'Sun' => esc_html__( 'Sunday', 'wpforms-lite' ),
		'Mon' => esc_html__( 'Monday', 'wpforms-lite' ),
		'Tue' => esc_html__( 'Tuesday', 'wpforms-lite' ),
		'Wed' => esc_html__( 'Wednesday', 'wpforms-lite' ),
		'Thu' => esc_html__( 'Thursday', 'wpforms-lite' ),
		'Fri' => esc_html__( 'Friday', 'wpforms-lite' ),
		'Sat' => esc_html__( 'Saturday', 'wpforms-lite' ),
	);

	return apply_filters( 'wpforms_days', $days );
}

/**
 * Get the user IP address.
 *
 * @since 1.2.5
 * @since 1.7.3 Improve the IP detection quality by taking care of proxies (e.g. when the site is behind Cloudflare).
 *
 * Code based on the:
 *   - WordPress method \WP_Community_Events::get_unsafe_client_ip
 *   - Cloudflare documentation https://support.cloudflare.com/hc/en-us/articles/206776727
 *
 * @return string
 */
function wpforms_get_ip() {

	$ip = '127.0.0.1';

	$address_headers = [
		'HTTP_TRUE_CLIENT_IP',
		'HTTP_CF_CONNECTING_IP',
		'HTTP_X_REAL_IP',
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'REMOTE_ADDR',
	];

	foreach ( $address_headers as $header ) {
		if ( empty( $_SERVER[ $header ] ) ) {
			continue;
		}

		/*
		 * HTTP_X_FORWARDED_FOR can contain a chain of comma-separated addresses, with or without spaces.
		 * The first address is the original client. It can't be trusted for authenticity,
		 * but we don't need to for this purpose.
		 */

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$address_chain = explode( ',', wp_unslash( $_SERVER[ $header ] ) );
		$ip            = filter_var( trim( $address_chain[0] ), FILTER_VALIDATE_IP );

		break;
	}

	/**
	 * Filter detected IP address.
	 *
	 * @since 1.2.5
	 *
	 * @param string $ip IP address.
	 */
	return filter_var( apply_filters( 'wpforms_get_ip', $ip ), FILTER_VALIDATE_IP );
}

/**
 * Sanitize hex color.
 *
 * @since 1.2.1
 *
 * @param string $color
 *
 * @return string
 */
function wpforms_sanitize_hex_color( $color ) {

	if ( empty( $color ) ) {
		return '';
	}

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}

	return '';
}

/**
 * Sanitize error message, primarily used during form frontend output.
 *
 * @since 1.3.7
 * @since 1.7.6 Expand list of allowed HTML tags and attributes.
 *
 * @param string $error Error message.
 *
 * @return string
 */
function wpforms_sanitize_error( $error = '' ) {

	$allow = [
		'a'          => [
			'href'   => [],
			'title'  => [],
			'target' => [],
			'rel'    => [],
		],
		'br'         => [],
		'em'         => [],
		'strong'     => [],
		'del'        => [],
		'p'          => [
			'style' => [],
		],
		'blockquote' => [],
		'ul'         => [],
		'ol'         => [],
		'li'         => [],
		'span'       => [
			'style' => [],
		],
	];

	return wp_kses( $error, $allow );
}

/**
 * Sanitize a string, that can be a multiline.
 *
 * @uses wpforms_sanitize_text_deeply()
 *
 * @since 1.4.1
 *
 * @param string $string String to deeply sanitize.
 *
 * @return string Sanitized string, or empty string if not a string provided.
 */
function wpforms_sanitize_textarea_field( $string ) {

	return wpforms_sanitize_text_deeply( $string, true );
}

/**
 * Deeply sanitize the string, preserve newlines if needed.
 * Prevent maliciously prepared strings from containing HTML tags.
 *
 * @since 1.6.0
 *
 * @param string $string        String to deeply sanitize.
 * @param bool   $keep_newlines Whether to keep newlines. Default: false.
 *
 * @return string Sanitized string, or empty string if not a string provided.
 */
function wpforms_sanitize_text_deeply( $string, $keep_newlines = false ) {

	if ( is_object( $string ) || is_array( $string ) ) {
		return '';
	}

	$string        = (string) $string;
	$keep_newlines = (bool) $keep_newlines;

	$new_value = _sanitize_text_fields( $string, $keep_newlines );

	if ( strlen( $new_value ) !== strlen( $string ) ) {
		$new_value = wpforms_sanitize_text_deeply( $new_value, $keep_newlines );
	}

	return $new_value;
}

/**
 * Sanitize an HTML string with a set of allowed HTML tags.
 *
 * @since 1.7.0
 *
 * @param string $value String to sanitize.
 *
 * @return string Sanitized string.
 */
function wpforms_sanitize_richtext_field( $value ) {

	$count = 1;
	$value = convert_invalid_entities( $value );

	// Remove 'script' and 'style' tags recursively.
	while ( $count ) {
		$value = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $value, - 1, $count );
	}

	// Make sure we have allowed tags only.
	$value = wp_kses( $value, wpforms_get_allowed_html_tags_for_richtext_field() );

	// Make sure that all tags are balanced.
	return force_balance_tags( $value );
}

/**
 * Escaping for Rich Text field values.
 *
 * @since 1.7.0
 *
 * @param string $value Text to escape.
 *
 * @return string Escaped text.
 */
function wpforms_esc_richtext_field( $value ) {

	return wpautop( wpforms_sanitize_richtext_field( $value ) );
}

/**
 * Retrieve allowed HTML tags for Rich Text field.
 *
 * @since 1.7.0
 *
 * @return array Array of allowed tags.
 */
function wpforms_get_allowed_html_tags_for_richtext_field() {

	$allowed_tags = array_fill_keys(
		[
			'img',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'a',
			'ul',
			'ol',
			'li',
			'dl',
			'dt',
			'dd',
			'hr',
			'br',
			'code',
			'pre',
			'strong',
			'b',
			'em',
			'i',
			'blockquote',
			'cite',
			'q',
			'del',
			'span',
			'small',
			'table',
			'thead',
			'tbody',
			'th',
			'tr',
			'td',
			'abbr',
			'address',
			'sub',
			'sup',
			'ins',
			'figure',
			'figcaption',
			'div',
		],
		array_fill_keys(
			[ 'align', 'class', 'id', 'style', 'src', 'rel', 'alt', 'href', 'target', 'width', 'height', 'title', 'cite', 'start', 'reversed', 'datetime' ],
			[]
		)
	);

	/**
	 * Allowed HTML tags for Rich Text field.
	 *
	 * @since 1.7.0
	 *
	 * @param array $allowed_tags Allowed HTML tags.
	 */
	$tags = (array) apply_filters( 'wpforms_get_allowed_html_tags_for_richtext_field', $allowed_tags );

	// Force unset iframes, script and style no matter when we get back
	// from apply_filters, as they are a huge security risk.
	unset( $tags['iframe'], $tags['script'], $tags['style'] );

	return $tags;
}

/**
 * Sanitize an array, that consists of values as strings.
 * After that - merge all array values into multiline string.
 *
 * @since 1.4.1
 *
 * @param array $array
 *
 * @return mixed If not an array is passed (or empty var) - return unmodified var. Otherwise - a merged array into multiline string.
 */
function wpforms_sanitize_array_combine( $array ) {

	if ( empty( $array ) || ! is_array( $array ) ) {
		return $array;
	}

	return implode( "\n", array_map( 'sanitize_text_field', $array ) );
}

/**
 * Detect if we should use a light or dark color based on the color given.
 *
 * @since 1.2.5
 * @link https://docs.woocommerce.com/wc-apidocs/source-function-wc_light_or_dark.html#608-627
 *
 * @param mixed $color
 * @param string $dark (default: '#000000').
 * @param string $light (default: '#FFFFFF').
 *
 * @return string
 */
function wpforms_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {

	$hex = str_replace( '#', '', $color );

	$c_r = hexdec( substr( $hex, 0, 2 ) );
	$c_g = hexdec( substr( $hex, 2, 2 ) );
	$c_b = hexdec( substr( $hex, 4, 2 ) );

	$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

	return $brightness > 155 ? $dark : $light;
}

/**
 * Build and return either a taxonomy or post type object that is
 * nested to accommodate any hierarchy.
 *
 * @since 1.3.9
 * @since 1.5.0 Return array only. Empty array of no data.
 *
 * @param array $args Object arguments to pass to data retrieval function.
 * @param bool  $flat Preserve hierarchy or not. False by default - preserve it.
 *
 * @return array
 */
function wpforms_get_hierarchical_object( $args = [], $flat = false ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

	if ( empty( $args['taxonomy'] ) && empty( $args['post_type'] ) ) {
		return [];
	}

	$children   = [];
	$parents    = [];
	$ref_parent = '';
	$ref_name   = '';
	$number     = 0;

	if ( ! empty( $args['post_type'] ) ) {

		$defaults   = [
			'posts_per_page' => - 1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		];
		$args       = wp_parse_args( $args, $defaults );
		$items      = get_posts( $args );
		$ref_parent = 'post_parent';
		$ref_id     = 'ID';
		$ref_name   = 'post_title';
		$number     = ! empty( $args['posts_per_page'] ) ? $args['posts_per_page'] : 0;

	} elseif ( ! empty( $args['taxonomy'] ) ) {

		$defaults   = [
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		];
		$args       = wp_parse_args( $args, $defaults );
		$items      = get_terms( $args );
		$ref_parent = 'parent';
		$ref_id     = 'term_id';
		$ref_name   = 'name';
		$number     = ! empty( $args['number'] ) ? $args['number'] : 0;
	}

	if ( empty( $items ) || is_wp_error( $items ) ) {
		return [];
	}

	foreach ( $items as $item ) {
		if ( $item->{$ref_parent} ) {
			$children[ $item->{$ref_id} ]     = $item;
			$children[ $item->{$ref_id} ]->ID = (int) $item->{$ref_id};
		} else {
			$parents[ $item->{$ref_id} ]     = $item;
			$parents[ $item->{$ref_id} ]->ID = (int) $item->{$ref_id};
		}
	}

	$children_count = count( $children );
	$is_limited     = $number > 1;

	// We can't guarantee that all children have a parent if there is a limit in the request.
	// Hence, we have to make sure that there is a parent for every child.
	if ( $is_limited && $children_count ) {
		foreach ( $children as $child ) {
			if ( ! empty( $parents[ $child->{$ref_parent} ] ) || ! empty( $children[ $child->{$ref_parent} ] ) ) {
				continue;
			}

			do {
				$current_parent = ! empty( $args['post_type'] ) ? get_post( $child->{$ref_parent} ) : get_term( $child->{$ref_parent} );

				if ( $current_parent->{$ref_parent} === 0 ) {
					$parents[ $current_parent->{$ref_id} ]     = $current_parent;
					$parents[ $current_parent->{$ref_id} ]->ID = (int) $current_parent->{$ref_id};
				} else {
					$children[ $current_parent->{$ref_id} ]     = $current_parent;
					$children[ $current_parent->{$ref_id} ]->ID = (int) $current_parent->{$ref_id};
				}
			} while ( $current_parent->{$ref_parent} > 0 );
		}
	}

	while ( $children_count >= 1 ) {
		foreach ( $children as $child ) {
			_wpforms_get_hierarchical_object_search( $child, $parents, $children, $ref_parent );

			// $children is modified by reference, so we need to recount to make sure we met the limits.
			$children_count = count( $children );
		}
	}

	// Sort nested child objects alphabetically using natural order, applies only
	// to ordering by entry title or term name.
	if ( in_array( $args['orderby'], [ 'title', 'name' ], true ) ) {
		_wpforms_sort_hierarchical_object( $parents, $args['orderby'], $args['order'] );
	}

	if ( $flat ) {
		$parents_flat = [];

		_wpforms_get_hierarchical_object_flatten( $parents, $parents_flat, $ref_name );

		$parents = $parents_flat;
	}

	return $is_limited ? array_slice( $parents, 0, $number ) : $parents;
}

/**
 * Sort a nested array of objects.
 *
 * @since 1.6.5
 *
 * @param array  $objects An array of objects to sort.
 * @param string $orderby The object field to order by.
 * @param string $order   Order direction.
 */
function _wpforms_sort_hierarchical_object( &$objects, $orderby, $order ) {

	// Map WP_Query/WP_Term_Query orderby to WP_Post/WP_Term property.
	$map = [
		'title' => 'post_title',
		'name'  => 'name',
	];

	foreach ( $objects as $object ) {
		if ( ! isset( $object->children ) ) {
			continue;
		}

		uasort(
			$object->children,
			static function ( $a, $b ) use ( $map, $orderby, $order ) {

				/**
				 * This covers most cases and works for most languages. For some – e.g. European languages
				 * that use extended latin charset (Polish, German etc) it will sort the objects into 2
				 * groups – base and extended, properly sorted within each group. Making it even more
				 * robust requires either additional PHP extensions to be installed on the server
				 * or using heavy (and slow) conversions and computations.
				 */
				return $order === 'ASC' ?
					strnatcasecmp( $a->{$map[ $orderby ]}, $b->{$map[ $orderby ]} ) :
					strnatcasecmp( $b->{$map[ $orderby ]}, $a->{$map[ $orderby ]} );
			}
		);

		_wpforms_sort_hierarchical_object( $object->children, $orderby, $order );
	}
}

/**
 * Search a given array and find the parent of the provided object.
 *
 * @since 1.3.9
 *
 * @param object $child      Current child.
 * @param array  $parents    Parents list.
 * @param array  $children   Children list.
 * @param string $ref_parent Parent reference.
 */
function _wpforms_get_hierarchical_object_search( $child, &$parents, &$children, $ref_parent ) {

	foreach ( $parents as $id => $parent ) {

		if ( $parent->ID === $child->{$ref_parent} ) {

			if ( empty( $parent->children ) ) {
				$parents[ $id ]->children = array(
					$child->ID => $child,
				);
			} else {
				$parents[ $id ]->children[ $child->ID ] = $child;
			}

			unset( $children[ $child->ID ] );

		} elseif ( ! empty( $parent->children ) && is_array( $parent->children ) ) {

			_wpforms_get_hierarchical_object_search( $child, $parent->children, $children, $ref_parent );
		}
	}
}

/**
 * Flatten a hierarchical object.
 *
 * @since 1.3.9
 *
 * @param array  $array    Array to process.
 * @param array  $output   Processed output.
 * @param string $ref_name Name reference.
 * @param int    $level    Nesting level.
 */
function _wpforms_get_hierarchical_object_flatten( $array, &$output, $ref_name = 'name', $level = 0 ) {

	foreach ( $array as $key => $item ) {

		$indicator           = apply_filters( 'wpforms_hierarchical_object_indicator', '&mdash;' );
		$item->{$ref_name}   = str_repeat( $indicator, $level ) . ' ' . $item->{$ref_name};
		$item->depth         = $level + 1;
		$output[ $item->ID ] = $item;

		if ( ! empty( $item->children ) ) {

			_wpforms_get_hierarchical_object_flatten( $item->children, $output, $ref_name, $level + 1 );
			unset( $output[ $item->ID ]->children );
		}
	}
}

/**
 * Return field choice properties for field configured with dynamic choices.
 *
 * @since 1.4.5
 *
 * @param array $field     Field settings.
 * @param int   $form_id   Form ID.
 * @param array $form_data Form data and settings.
 *
 * @return false|array
 */
function wpforms_get_field_dynamic_choices( $field, $form_id, $form_data = array() ) {

	if ( empty( $field['dynamic_choices'] ) ) {
		return false;
	}

	$choices = array();

	if ( 'post_type' === $field['dynamic_choices'] ) {

		if ( empty( $field['dynamic_post_type'] ) ) {
			return false;
		}

		$posts = wpforms_get_hierarchical_object(
			apply_filters(
				'wpforms_dynamic_choice_post_type_args',
				array(
					'post_type'      => $field['dynamic_post_type'],
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
				),
				$field,
				$form_id
			),
			true
		);

		foreach ( $posts as $post ) {
			$choices[] = array(
				'value' => $post->ID,
				'label' => wpforms_get_post_title( $post ),
				'depth' => isset( $post->depth ) ? absint( $post->depth ) : 1,
			);
		}
	} elseif ( 'taxonomy' === $field['dynamic_choices'] ) {

		if ( empty( $field['dynamic_taxonomy'] ) ) {
			return false;
		}

		$terms = wpforms_get_hierarchical_object(
			apply_filters(
				'wpforms_dynamic_choice_taxonomy_args',
				array(
					'taxonomy'   => $field['dynamic_taxonomy'],
					'hide_empty' => false,
				),
				$field,
				$form_data
			),
			true
		);

		foreach ( $terms as $term ) {
			$choices[] = array(
				'value' => $term->term_id,
				'label' => wpforms_get_term_name( $term ),
				'depth' => isset( $term->depth ) ? absint( $term->depth ) : 1,
			);
		}
	}

	return $choices;
}

/**
 * Insert an array into another array before/after a certain key.
 *
 * @link  https://gist.github.com/scribu/588429
 *
 * @since 1.3.9
 *
 * @param array  $array    The initial array.
 * @param array  $pairs    The array to insert.
 * @param string $key      The certain key.
 * @param string $position Where to insert the array - before or after the key.
 *
 * @return array
 */
function wpforms_array_insert( $array, $pairs, $key, $position = 'after' ) {

	$key_pos = array_search( $key, array_keys( $array ), true );
	if ( 'after' === $position ) {
		$key_pos ++;
	}

	if ( false !== $key_pos ) {
		$result = array_slice( $array, 0, $key_pos );
		$result = array_merge( $result, $pairs );
		$result = array_merge( $result, array_slice( $array, $key_pos ) );
	} else {
		$result = array_merge( $array, $pairs );
	}

	return $result;
}

/**
 * Recursively remove empty strings from an array.
 *
 * @since 1.3.9.1
 *
 * @param array $data
 *
 * @return array
 */
function wpforms_array_remove_empty_strings( $data ) {

	foreach ( $data as $key => $value ) {
		if ( is_array( $value ) ) {
			$data[ $key ] = wpforms_array_remove_empty_strings( $data[ $key ] );
		}

		if ( '' === $data[ $key ] ) {
			unset( $data[ $key ] );
		}
	}

	return $data;
}

/**
 * Check whether plugin works in a debug mode.
 *
 * @since 1.2.3
 *
 * @return bool
 */
function wpforms_debug() {

	$debug = false;

	if ( ( defined( 'WPFORMS_DEBUG' ) && true === WPFORMS_DEBUG ) && is_super_admin() ) {
		$debug = true;
	}

	return apply_filters( 'wpforms_debug', $debug );
}

/**
 * Helper function to display debug data.
 *
 * @since 1.0.0
 *
 * @param mixed $data What to dump, can be any type.
 * @param bool  $echo Whether to print or return. Default is to print.
 *
 * @return string|void
 */
function wpforms_debug_data( $data, $echo = true ) {

	if ( ! wpforms_debug() ) {
		return;
	}

	if ( is_array( $data ) || is_object( $data ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
		$data = print_r( $data, true );
	}

	$output = sprintf(
		'<style>
			.wpforms-debug {
				line-height: 0;
			}
			.wpforms-debug textarea {
				background: #f6f7f7 !important;
				margin: 20px 0 0 0;
				width: 100%%;
				height: 500px;
				font-size: 12px;
				font-family: Consolas, Menlo, Monaco, monospace;
				direction: ltr;
				unicode-bidi: embed;
				line-height: 1.4;
				padding: 10px;
				border-radius: 0;
				border-color: #c3c4c7;
			}
			.postbox .wpforms-debug {
				padding-top: 12px;
			}
			.postbox .wpforms-debug:first-of-type {
				padding-top: 6px;
			}
			.postbox .wpforms-debug textarea {
				margin-top: 0 !important;
			}
		</style>
		<div class="wpforms-debug">
			<textarea readonly>=================== WPFORMS DEBUG ===================%s</textarea>
		</div>',
		"\n\n" . $data
	);

	/**
	 * Allow developers to determine whether the debug data should be displayed.
	 * Works only in debug mode (`WPFORMS_DEBUG` constant is `true`).
	 *
	 * @since 1.6.8
	 *
	 * @param bool $allow_display True by default.
	 */
	$allow_display = apply_filters( 'wpforms_debug_data_allow_display', true );

	if ( $echo && $allow_display ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $output;
	} else {
		return $output;
	}
}

/**
 * Log helper.
 *
 * @since 1.0.0
 *
 * @param string $title   Title of a log message.
 * @param mixed  $message Content of a log message.
 * @param array  $args    Expected keys: form_id, meta, parent.
 */
function wpforms_log( $title = '', $message = '', $args = array() ) {

	// Skip if logs disabled in Tools -> Logs.
	if ( ! wpforms_setting( 'logs-enable', false ) ) {
		return;
	}

	// Require log title.
	if ( empty( $title ) ) {
		return;
	}

	/**
	 * Compare error levels to determine if we should log.
	 * Current supported levels:
	 * - Conditional Logic (conditional_logic)
	 * - Entries (entry)
	 * - Errors (error)
	 * - Payments (payment)
	 * - Providers (provider)
	 * - Security (security)
	 * - Spam (spam)
	 * - Log (log)
	 */
	$types = ! empty( $args['type'] ) ? (array) $args['type'] : [ 'error' ];

	// Skip invalid logs types.
	$log_types = \WPForms\Logger\Log::get_log_types();
	foreach ( $types as $key => $type ) {
		if ( ! isset( $log_types[ $type ] ) ) {
			unset( $types[ $key ] );
		}
	}
	if ( empty( $types ) ) {
		return;
	}

	// Make arrays and objects look nice.
	if ( is_array( $message ) || is_object( $message ) ) {
		$message = '<pre>' . print_r( $message, true ) . '</pre>'; // phpcs:ignore
	}

	// Filter logs types from Tools -> Logs page.
	$logs_types = wpforms_setting( 'logs-types', false );

	if ( $logs_types && empty( array_intersect( $logs_types, $types ) ) ) {
		return;
	}

	// Filter user roles from Tools -> Logs page.
	$current_user       = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
	$current_user_id    = $current_user ? $current_user->ID : 0;
	$current_user_roles = $current_user ? $current_user->roles : [];
	$logs_user_roles    = wpforms_setting( 'logs-user-roles', false );

	if ( $logs_user_roles && empty( array_intersect( $logs_user_roles, $current_user_roles ) ) ) {
		return;
	}

	// Filter logs users from Tools -> Logs page.
	$logs_users = wpforms_setting( 'logs-users', false );

	if ( $logs_users && ! in_array( $current_user_id, $logs_users, true ) ) {
		return;
	}

	$log = wpforms()->get( 'log' );

	if ( ! method_exists( $log, 'add' ) ) {
		return;
	}
	// Create log entry.
	$log->add(
		$title,
		$message,
		$types,
		isset( $args['form_id'] ) ? absint( $args['form_id'] ) : 0,
		isset( $args['parent'] ) ? absint( $args['parent'] ) : 0,
		$current_user_id
	);
}

/**
 * Check whether the current page is in AMP mode or not.
 * We need to check for specific functions, as there is no special AMP header.
 *
 * @since 1.4.1
 *
 * @param bool $check_theme_support Whether theme support should be checked. Defaults to true.
 *
 * @return bool
 */
function wpforms_is_amp( $check_theme_support = true ) {

	$is_amp = false;

	if (
		// AMP by Automattic; ampforwp.
		( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ||
		// Better AMP.
		( function_exists( 'is_better_amp' ) && is_better_amp() )
	) {
		$is_amp = true;
	}

	if ( $is_amp && $check_theme_support ) {
		$is_amp = current_theme_supports( 'amp' );
	}

	return apply_filters( 'wpforms_is_amp', $is_amp );
}

/**
 * Decode special characters, both alpha- (<) and numeric-based (').
 * Sanitize recursively, preserve new lines.
 * Handle all the possible mixed variations of < and `&lt;` that can be processed into tags.
 *
 * @since 1.4.1
 * @since 1.6.0 Sanitize recursively, preserve new lines.
 *
 * @param string $string Raw string to decode.
 *
 * @return string
 */
function wpforms_decode_string( $string ) {

	if ( ! is_string( $string ) ) {
		return $string;
	}

	/*
	 * Sanitization should be done first, so tags are stripped and < is converted to &lt; etc.
	 * This iteration may do nothing when the string already comes with &lt; and &gt; only.
	 */
	$string = wpforms_sanitize_text_deeply( $string, true );

	// Now we need to convert the string without tags: &lt; back to < (same for quotes).
	$string = wp_kses_decode_entities( html_entity_decode( $string, ENT_QUOTES ) );

	// And now we need to sanitize AGAIN, to avoid unwanted tags that appeared after decoding.
	return wpforms_sanitize_text_deeply( $string, true );
}

/**
 * Get a suffix for assets, `.min` if debug is disabled.
 *
 * @since 1.4.1
 *
 * @return string
 */
function wpforms_get_min_suffix() {
	return wpforms_debug() ? '' : '.min';
}

/**
 * Get the required label text, with a filter.
 *
 * @since 1.4.4
 *
 * @return string
 */
function wpforms_get_required_label() {
	return apply_filters( 'wpforms_required_label', esc_html__( 'This field is required.', 'wpforms-lite' ) );
}

/**
 * Get the required field label HTML, with a filter.
 *
 * @since 1.4.8
 *
 * @return string
 */
function wpforms_get_field_required_label() {

	$label_html = apply_filters_deprecated(
		'wpforms_field_required_label',
		array( ' <span class="wpforms-required-label">*</span>' ),
		'1.4.8 of the WPForms plugin',
		'wpforms_get_field_required_label'
	);

	return apply_filters( 'wpforms_get_field_required_label', $label_html );
}

/**
 * Get the default capability to manage everything for WPForms.
 *
 * @since 1.4.4
 *
 * @return string
 */
function wpforms_get_capability_manage_options() {
	return apply_filters( 'wpforms_manage_cap', 'manage_options' );
}

/**
 * Check WPForms permissions for currently logged in user.
 * Both short (e.g. 'view_own_forms') or long (e.g. 'wpforms_view_own_forms') capability name can be used.
 * Only WPForms capabilities get processed.
 *
 * @since 1.4.4
 *
 * @param array|string $caps Capability name(s).
 * @param int          $id   ID of the specific object to check against if capability is a "meta" cap. "Meta"
 *                           capabilities, e.g. 'edit_post', 'edit_user', etc., are capabilities used by
 *                           map_meta_cap() to map to other "primitive" capabilities, e.g. 'edit_posts',
 *                           edit_others_posts', etc. Accessed via func_get_args() and passed to
 *                           WP_User::has_cap(), then map_meta_cap().
 *
 * @return bool
 */
function wpforms_current_user_can( $caps = [], $id = 0 ) {

	$access = wpforms()->get( 'access' );

	if ( ! method_exists( $access, 'current_user_can' ) ) {
		return false;
	}

	$user_can = $access->current_user_can( $caps , $id );

	return apply_filters( 'wpforms_current_user_can', $user_can, $caps, $id );
}

/**
 * Return date and time formatted as expected.
 *
 * @since 1.6.3
 *
 * @param string|int $date       Date to format.
 * @param string     $format     Optional. Format for the date and time.
 * @param bool       $gmt_offset Optional. GTM offset.
 *
 * @return string
 */
function wpforms_datetime_format( $date, $format = '', $gmt_offset = false ) {

	if ( '' === $format ) {
		$format = sprintf( '%s %s', get_option( 'date_format' ), get_option( 'time_format' ) );
	}

	if ( is_string( $date ) ) {
		$date = strtotime( $date );
	}

	if ( $gmt_offset ) {
		$date += (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
	}

	return date_i18n( $format, $date );
}

/**
 * Return date formatted as expected.
 *
 * @since 1.6.3
 *
 * @param string|int $date       Date to format.
 * @param string     $format     Optional. Format for the date.
 * @param bool       $gmt_offset Optional. GTM offset.
 *
 * @return string
 */
function wpforms_date_format( $date, $format = '', $gmt_offset = false ) {

	if ( '' === $format ) {
		$format = get_option( 'date_format' );
	}

	return wpforms_datetime_format( $date, $format, $gmt_offset );
}

/**
 * Get the certain date of a specified day in a specified format.
 *
 * @since 1.4.4
 * @since 1.6.3 Added $use_gmt_offset parameter.
 *
 * @param string $period         Supported values: start, end.
 * @param string $timestamp      Default is the current timestamp, if left empty.
 * @param string $format         Default is a MySQL format.
 * @param bool   $use_gmt_offset Use GTM offset.
 *
 * @return string
 */
function wpforms_get_day_period_date( $period, $timestamp = '', $format = 'Y-m-d H:i:s', $use_gmt_offset = false ) {

	$date = '';

	if ( empty( $timestamp ) ) {
		$timestamp = time();
	}

	$offset_sec = $use_gmt_offset ? get_option( 'gmt_offset' ) * 3600 : 0;

	switch ( $period ) {
		case 'start_of_day':
			$date = gmdate( $format, strtotime( 'today', $timestamp ) - $offset_sec );
			break;

		case 'end_of_day':
			$date = gmdate( $format, strtotime( 'tomorrow', $timestamp ) - 1 - $offset_sec );
			break;
	}

	return $date;
}

/**
 * Return available date formats.
 *
 * @since 1.7.5
 *
 * @return array
 */
function wpforms_date_formats() {

	/**
	 * Filters available date formats.
	 *
	 * @since 1.3.0
	 *
	 * @param array $date_formats Default date formats.
	 *                            Item key is JS date character - see https://flatpickr.js.org/formatting/
	 *                            Item value is in PHP format - see http://php.net/manual/en/function.date.php.
	 */
	return (array) apply_filters(
		'wpforms_datetime_date_formats',
		[
			'm/d/Y'  => 'm/d/Y',
			'd/m/Y'  => 'd/m/Y',
			'F j, Y' => 'F j, Y',
		]
	);
}

/**
 * Return available time formats.
 *
 * @since 1.7.7
 *
 * @return array
 */
function wpforms_time_formats() {

	/**
	 * Filters available time formats.
	 *
	 * @since 1.5.9
	 *
	 * @param array $time_formats Default time formats.
	 *                            Item key is in PHP format which it used in jquery.timepicker as well,
	 *                            see http://php.net/manual/en/function.date.php.
	 */
	return (array) apply_filters(
		'wpforms_datetime_time_formats',
		[
			'g:i A' => '12 H',
			'H:i'   => '24 H',
		]
	);
}

/**
 * Get an array of all possible provider addons.
 *
 * @since 1.5.5
 *
 * @return array
 */
function wpforms_get_providers_all() {

	return [
		[
			'name'        => 'ActiveCampaign',
			'slug'        => 'activecampaign',
			'img'         => 'addon-icon-activecampaign.png',
			'plugin'      => 'wpforms-activecampaign/wpforms-activecampaign.php',
			'plugin_slug' => 'wpforms-activecampaign',
			'license'     => 'elite',
		],
		[
			'name'        => 'AWeber',
			'slug'        => 'aweber',
			'img'         => 'addon-icon-aweber.png',
			'plugin'      => 'wpforms-aweber/wpforms-aweber.php',
			'plugin_slug' => 'wpforms-aweber',
			'license'     => 'pro',
		],
		[
			'name'        => 'Campaign Monitor',
			'slug'        => 'campaign-monitor',
			'img'         => 'addon-icon-campaign-monitor.png',
			'plugin'      => 'wpforms-campaign-monitor/wpforms-campaign-monitor.php',
			'plugin_slug' => 'wpforms-campaign-monitor',
			'license'     => 'pro',
		],
		[
			'name'        => 'Drip',
			'slug'        => 'drip',
			'img'         => 'addon-icon-drip.png',
			'plugin'      => 'wpforms-drip/wpforms-drip.php',
			'plugin_slug' => 'wpforms-drip',
			'license'     => 'pro',
		],
		[
			'name'        => 'GetResponse',
			'slug'        => 'getresponse',
			'img'         => 'addon-icon-getresponse.png',
			'plugin'      => 'wpforms-getresponse/wpforms-getresponse.php',
			'plugin_slug' => 'wpforms-getresponse',
			'license'     => 'pro',
		],
		[
			'name'        => 'Mailchimp',
			'slug'        => 'mailchimp',
			'img'         => 'addon-icon-mailchimp.png',
			'plugin'      => 'wpforms-mailchimp/wpforms-mailchimp.php',
			'plugin_slug' => 'wpforms-mailchimp',
			'license'     => 'pro',
		],
		[
			'name'        => 'Salesforce',
			'slug'        => 'salesforce',
			'img'         => 'addon-icon-salesforce.png',
			'plugin'      => 'wpforms-salesforce/wpforms-salesforce.php',
			'plugin_slug' => 'wpforms-salesforce',
			'license'     => 'elite',
		],
		[
			'name'        => 'Sendinblue',
			'slug'        => 'sendinblue',
			'img'         => 'addon-icon-sendinblue.png',
			'plugin'      => 'wpforms-sendinblue/wpforms-sendinblue.php',
			'plugin_slug' => 'wpforms-sendinblue',
			'license'     => 'pro',
		],
		[
			'name'        => 'Zapier',
			'slug'        => 'zapier',
			'img'         => 'addon-icon-zapier.png',
			'plugin'      => 'wpforms-zapier/wpforms-zapier.php',
			'plugin_slug' => 'wpforms-zapier',
			'license'     => 'pro',
		],
		[
			'name'        => 'HubSpot',
			'slug'        => 'hubspot',
			'img'         => 'addon-icon-hubspot.png',
			'plugin'      => 'wpforms-hubspot/wpforms-hubspot.php',
			'plugin_slug' => 'wpforms-hubspot',
			'license'     => 'pro',
		],
	];
}

/**
 * Get an array of all the active provider addons.
 *
 * @since 1.4.7
 *
 * @return array
 */
function wpforms_get_providers_available() {
	return (array) apply_filters( 'wpforms_providers_available', array() );
}

/**
 * Get options for all providers.
 *
 * @since 1.4.7
 *
 * @param string $provider Define a single provider to get options for this one only.
 *
 * @return array
 */
function wpforms_get_providers_options( $provider = '' ) {

	$options  = get_option( 'wpforms_providers', [] );
	$provider = sanitize_key( $provider );
	$data     = $options;

	if ( ! empty( $provider ) && isset( $options[ $provider ] ) ) {
		$data = $options[ $provider ];
	}

	return (array) apply_filters( 'wpforms_get_providers_options', $data, $provider );
}

/**
 * Update options for all providers.
 *
 * @since 1.4.7
 *
 * @param string      $provider Provider slug.
 * @param array|false $options  If false is passed - provider will be removed. Otherwise saved.
 * @param string      $key      Optional key to identify which connection to update. If empty - generate a new one.
 */
function wpforms_update_providers_options( $provider, $options, $key = '' ) {

	$providers = wpforms_get_providers_options();
	$id        = ! empty( $key ) ? $key : uniqid();
	$provider  = sanitize_key( $provider );

	if ( $options ) {
		$providers[ $provider ][ $id ] = (array) $options;
	} else {
		unset( $providers[ $provider ] );
	}

	update_option( 'wpforms_providers', $providers );
}

/**
 * Helper function to determine if loading on WPForms related admin page.
 *
 * Here we determine if the current administration page is owned/created by
 * WPForms. This is done in compliance with WordPress best practices for
 * development, so that we only load required WPForms CSS and JS files on pages
 * we create. As a result we do not load our assets admin wide, where they might
 * conflict with other plugins needlessly, also leading to a better, faster user
 * experience for our users.
 *
 * @since 1.3.9
 *
 * @param string $slug Slug identifier for a specific WPForms admin page.
 * @param string $view Slug identifier for a specific WPForms admin page view ("subpage").
 *
 * @return bool
 */
function wpforms_is_admin_page( $slug = '', $view = '' ) {

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	// Check against basic requirements.
	if (
		! is_admin() ||
		empty( $_REQUEST['page'] ) ||
		strpos( $_REQUEST['page'], 'wpforms' ) === false
	) {
		return false;
	}

	// Check against page slug identifier.
	if (
		( ! empty( $slug ) && 'wpforms-' . $slug !== $_REQUEST['page'] ) ||
		( empty( $slug ) && 'wpforms-builder' === $_REQUEST['page'] )
	) {
		return false;
	}

	// Check against sub-level page view.
	if (
		! empty( $view ) &&
		( empty( $_REQUEST['view'] ) || $view !== $_REQUEST['view'] )
	) {
		return false;
	}
	// phpcs:enable

	return true;
}

/**
 * Get the ISO 639-2 Language Code from user/site locale.
 *
 * @see http://www.loc.gov/standards/iso639-2/php/code_list.php
 *
 * @since 1.5.0
 *
 * @return string
 */
function wpforms_get_language_code() {

	$default_lang = 'en';
	$locale       = get_user_locale();

	if ( ! empty( $locale ) ) {
		$lang = explode( '_', $locale );
		if ( ! empty( $lang ) && is_array( $lang ) ) {
			$default_lang = strtolower( $lang[0] );
		}
	}

	return $default_lang;
}

/**
 * Determine if we should show the "Show Values" toggle for checkbox, radio, or
 * select fields in form builder. Legacy.
 *
 * @since 1.5.0
 *
 * @return bool
 */
function wpforms_show_fields_options_setting() {

	return apply_filters( 'wpforms_fields_show_options_setting', false );
}

/**
 * Check if a string is empty.
 *
 * @since 1.5.0
 *
 * @param string $string String to test.
 *
 * @return bool
 */
function wpforms_is_empty_string( $string ) {

	return is_string( $string ) && '' === $string;
}

/**
 * Return URL to form preview page.
 *
 * @since 1.5.1
 *
 * @param int  $form_id    Form ID.
 * @param bool $new_window New window flag.
 *
 * @return string
 */
function wpforms_get_form_preview_url( $form_id, $new_window = false ) {

	$url = add_query_arg(
		array(
			'wpforms_form_preview' => absint( $form_id ),
		),
		home_url()
	);

	if ( $new_window ) {
		$url = add_query_arg(
			array(
				'new_window' => 1,
			),
			$url
		);
	}

	return $url;
}

/**
 * Include a template - alias to \WPForms\Helpers\Template::get_html.
 * Use 'require' if $args are passed or 'load_template' if not.
 *
 * @since 1.5.6
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments.
 * @param bool   $extract       Extract arguments.
 *
 * @throws \RuntimeException If extract() tries to modify the scope.
 *
 * @return string Compiled HTML.
 */
function wpforms_render( $template_name, $args = array(), $extract = false ) {

	return \WPForms\Helpers\Templates::get_html( $template_name, $args, $extract );
}

/**
 * Chain monad, useful for chaining certain array or string related functions.
 *
 * @since 1.5.6
 *
 * @param mixed $value Any data.
 *
 * @return \WPForms\Helpers\Chain
 */
function wpforms_chain( $value ) {

	return \WPForms\Helpers\Chain::of( $value );
}

/**
 * Get the current installation license type (always lowercase).
 *
 * @since 1.5.6
 *
 * @return string|false
 */
function wpforms_get_license_type() {

	$type = wpforms_setting( 'type', '', 'wpforms_license' );

	if ( empty( $type ) || ! wpforms()->is_pro() ) {
		return false;
	}

	return strtolower( $type );
}

/**
 * Get the current installation license key.
 *
 * @since 1.6.2.3
 *
 * @return string
 */
function wpforms_get_license_key() {

	// Check for the license key.
	$key = wpforms_setting( 'key', '', 'wpforms_license' );

	// Allow wp-config constant to pass key.
	if ( empty( $key ) && defined( 'WPFORMS_LICENSE_KEY' ) && WPFORMS_LICENSE_KEY ) {
		$key = WPFORMS_LICENSE_KEY;
	}

	return $key;
}

/**
 * Get when WPForms was first installed.
 *
 * @since 1.6.0
 *
 * @param string $type Specific install type to check for.
 *
 * @return int|false Unix timestamp. False on failure.
 */
function wpforms_get_activated_timestamp( $type = '' ) {

	$activated = (array) get_option( 'wpforms_activated', [] );

	if ( empty( $activated ) ) {
		return false;
	}

	// When a passed install type is empty, then get it from a DB.
	// If it is installed/activated first, it is saved first.
	$type = empty( $type ) ? (string) array_keys( $activated )[0] : $type;

	if ( ! empty( $activated[ $type ] ) ) {
		return absint( $activated[ $type ] );
	}

	// Fallback.
	$types = array_diff( [ 'lite', 'pro' ], [ $type ] );

	foreach ( $types as $_type ) {
		if ( ! empty( $activated[ $_type ] ) ) {
			return absint( $activated[ $_type ] );
		}
	}

	return false;
}

/**
 * Retrieve a timestamp when WPForms was upgraded.
 *
 * @since 1.7.5
 *
 * @param string $version Specific plugin version to check for.
 *
 * @return int|false Unix timestamp or migration status. False on failure.
 *                   Available migration statuses:
 *                   -2 if migration is failed;
 *                   -1 if migration is started (in progress);
 *                    0 if migration is completed, but no luck to set a timestamp.
 */
function wpforms_get_upgraded_timestamp( $version ) {

	$option_name = wpforms()->is_pro() ? 'wpforms_versions' : 'wpforms_versions_lite';
	$upgrades    = (array) get_option( $option_name, [] );

	if ( ! isset( $upgrades[ $version ] ) ) {
		return false;
	}

	return (int) $upgrades[ $version ];
}

/**
 * Detect if AJAX frontend form submit is being processed.
 *
 * @since 1.5.8.2
 * @since 1.6.5 Added filterable frontend ajax actions list as a fallback to missing referer cases.
 * @since 1.6.7.1 Removed a requirement for an AJAX action to be a WPForms action if referer is not missing.
 *
 * @return bool
 */
function wpforms_is_frontend_ajax() {

	if ( ! wp_doing_ajax() ) {
		return false;
	}

	// Additional check to make sure the request targets admin-ajax.php.
	if ( isset( $_SERVER['SCRIPT_FILENAME'] ) && basename( sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_FILENAME'] ) ) ) !== 'admin-ajax.php' ) {
		return false;
	}

	$ref = wp_get_raw_referer();

	if ( ! $ref ) {

		// Try to detect a frontend AJAX call indirectly by comparing the current action
		// with a known frontend actions list in case there's no HTTP referer.
		$frontend_actions = [
			'wpforms_submit',
			'wpforms_file_upload_speed_test',
			'wpforms_upload_chunk_init',
			'wpforms_upload_chunk',
			'wpforms_file_chunks_uploaded',
			'wpforms_remove_file',
			'wpforms_restricted_email',
			'wpforms_form_locker_unique_answer',
			'wpforms_form_abandonment',
		];

		$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		// This filter may be running as early as "plugins_loaded" hook. Please mind the hooks order when using it.
		$frontend_actions = (array) apply_filters( 'wpforms_is_frontend_ajax_frontend_actions', $frontend_actions );

		return in_array( $action, $frontend_actions, true );
	}

	$path       = wp_parse_url( $ref, PHP_URL_PATH );
	$admin_path = wp_parse_url( admin_url(), PHP_URL_PATH );

	// It's a frontend AJAX call if HTTP referer doesn't contain an admin path.
	return strpos( $path, $admin_path ) === false;
}

/**
 * Dequeue enqueues by URI list.
 * Parts of URI (e.g. filename) is also supported.
 *
 * @since 1.6.1
 *
 * @param array|string           $uris     List of URIs or individual URI to dequeue.
 * @param \WP_Scripts|\WP_Styles $enqueues Enqueues list to dequeue from.
 */
function wpforms_dequeue_by_uri( $uris, $enqueues ) {

	if ( empty( $enqueues->queue ) ) {
		return;
	}

	foreach ( $enqueues->queue as $handle ) {

		if ( empty( $enqueues->registered[ $handle ]->src ) ) {
			continue;
		}

		$src = wp_make_link_relative( $enqueues->registered[ $handle ]->src );

		// Support full URLs.
		$src = site_url( $src );

		foreach ( (array) $uris as $uri ) {
			if ( strpos( $src, $uri ) !== false ) {
				wp_dequeue_script( $handle );
				break;
			}
		}
	}
}

/**
 * Dequeue scripts by URI list.
 * Parts of URI (e.g. filename) is also supported.
 *
 * @since 1.6.1
 *
 * @param array|string $uris List of URIs or individual URI to dequeue.
 */
function wpforms_dequeue_scripts_by_uri( $uris ) {

	wpforms_dequeue_by_uri( $uris, wp_scripts() );
}

/**
 * Dequeue styles by URI list.
 * Parts of URI (e.g. filename) is also supported.
 *
 * @since 1.6.1
 *
 * @param array|string $uris List of URIs or individual URI to dequeue.
 */
function wpforms_dequeue_styles_by_uri( $uris ) {

	wpforms_dequeue_by_uri( $uris, wp_styles() );
}

/**
 * Count words in the string.
 *
 * @since 1.6.2
 *
 * @param string $string String value.
 *
 * @return integer Words count.
 */
function wpforms_count_words( $string ) {

	if ( ! is_string( $string ) ) {
		return 0;
	}

	$patterns = [
		'/([A-Z]+),([A-Z]+)/i',
		'/([0-9]+),([A-Z]+)/i',
		'/([A-Z]+),([0-9]+)/i',
	];

	foreach ( $patterns as $pattern ) {
		$string = preg_replace_callback(
			$pattern,
			function( $matches ) {
				return $matches[1] . ', ' . $matches[2];
			},
			$string
		);
	}

	$words = preg_split( '/[\s]+/', $string );

	return is_array( $words ) ? count( $words ) : 0;
}

/**
 * Get WPForms upload root path (e.g. /wp-content/uploads/wpforms).
 *
 * As of 1.7.0, you can pass in your own value that matches the output of wp_upload_dir()
 * in order to use this function inside of a filter without infinite looping.
 *
 * @since 1.6.1
 *
 * @return array WPForms upload root path (no trailing slash).
 */
function wpforms_upload_dir() {

	$upload_dir = wp_upload_dir();

	if ( ! empty( $upload_dir['error'] ) ) {
		return [ 'error' => $upload_dir['error'] ];
	}

	$basedir             = wp_is_stream( $upload_dir['basedir'] ) ? $upload_dir['basedir'] : realpath( $upload_dir['basedir'] );
	$wpforms_upload_root = trailingslashit( $basedir ) . 'wpforms';

	/**
	 * Allow developers to change a directory where cache and uploaded files will be stored.
	 *
	 * @since 1.5.2
	 *
	 * @param string $wpforms_upload_root WPForms upload root directory.
	 */
	$custom_uploads_root = apply_filters( 'wpforms_upload_root', $wpforms_upload_root );

	if ( is_dir( $custom_uploads_root ) && wp_is_writable( $custom_uploads_root ) ) {
		$wpforms_upload_root = wp_is_stream( $custom_uploads_root )
			? $custom_uploads_root
			: realpath( $custom_uploads_root );
	}

	return [
		'path'  => $wpforms_upload_root,
		'url'   => trailingslashit( $upload_dir['baseurl'] ) . 'wpforms',
		'error' => false,
	];
}

/**
 * Create index.html file in the specified directory if it doesn't exist.
 *
 * @since 1.6.1
 *
 * @param string $path Path to the directory.
 *
 * @return int|false Number of bytes that were written to the file, or false on failure.
 */
function wpforms_create_index_html_file( $path ) {

	if ( ! is_dir( $path ) || is_link( $path ) ) {
		return false;
	}

	$index_file = wp_normalize_path( trailingslashit( $path ) . 'index.html' );

	// Do nothing if index.html exists in the directory.
	if ( file_exists( $index_file ) ) {
		return false;
	}

	// Create empty index.html.
	return file_put_contents( $index_file, '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
}

/**
 * Create .htaccess file in the WPForms upload directory.
 *
 * @since 1.6.1
 *
 * @return bool True when the .htaccess file exists, false on failure.
 */
function wpforms_create_upload_dir_htaccess_file() {

	if ( ! apply_filters( 'wpforms_create_upload_dir_htaccess_file', true ) ) {
		return false;
	}

	$upload_dir = wpforms_upload_dir();

	if ( ! empty( $upload_dir['error'] ) ) {
		return false;
	}

	$htaccess_file = wp_normalize_path( trailingslashit( $upload_dir['path'] ) . '.htaccess' );
	$cache_key     = 'wpforms_htaccess_file';

	if ( is_file( $htaccess_file ) ) {
		$cached_stat = get_transient( $cache_key );
		$stat        = array_intersect_key(
			stat( $htaccess_file ),
			[
				'size'  => 0,
				'mtime' => 0,
				'ctime' => 0,
			]
		);

		if ( $cached_stat === $stat ) {
			return true;
		}

		@unlink( $htaccess_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	if ( ! function_exists( 'insert_with_markers' ) ) {
		require_once ABSPATH . 'wp-admin/includes/misc.php';
	}

	$contents = apply_filters(
		'wpforms_create_upload_dir_htaccess_file_content',
		'# Disable PHP and Python scripts parsing.
<Files *>
  SetHandler none
  SetHandler default-handler
  RemoveHandler .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
  RemoveType .cgi .php .php3 .php4 .php5 .phtml .pl .py .pyc .pyo
</Files>
<IfModule mod_php5.c>
  php_flag engine off
</IfModule>
<IfModule mod_php7.c>
  php_flag engine off
</IfModule>
<IfModule mod_php8.c>
  php_flag engine off
</IfModule>
<IfModule headers_module>
  Header set X-Robots-Tag "noindex"
</IfModule>'
	);

	$created = insert_with_markers( $htaccess_file, 'WPForms', $contents );

	if ( $created ) {
		clearstatcache( true, $htaccess_file );
		$stat = array_intersect_key(
			stat( $htaccess_file ),
			[
				'size'  => 0,
				'mtime' => 0,
				'ctime' => 0,
			]
		);

		set_transient( $cache_key, $stat );
	}

	return $created;
}

/**
 * Check if Gutenberg is active.
 *
 * @since 1.6.2
 *
 * @return bool True if Gutenberg is active.
 */
function wpforms_is_gutenberg_active() {

	$gutenberg    = false;
	$block_editor = false;

	if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
		// Gutenberg is installed and activated.
		$gutenberg = true;
	}

	if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
		// Block editor.
		$block_editor = true;
	}

	if ( ! $gutenberg && ! $block_editor ) {
		return false;
	}

	include_once ABSPATH . 'wp-admin/includes/plugin.php';

	if ( is_plugin_active( 'disable-gutenberg/disable-gutenberg.php' ) ) {
		return ! disable_gutenberg();
	}

	if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
		return get_option( 'classic-editor-replace' ) === 'block';
	}

	return true;
}

/**
 * Determine if the plugin/addon installations are allowed.
 *
 * @since 1.6.2.3
 *
 * @param string $type Should be `plugin` or `addon`.
 *
 * @return bool
 */
function wpforms_can_install( $type ) {

	return wpforms_can_do( 'install', $type );
}

/**
 * Determine if the plugin/addon activations are allowed.
 *
 * @since 1.7.3
 *
 * @param string $type Should be `plugin` or `addon`.
 *
 * @return bool
 */
function wpforms_can_activate( $type ) {

	return wpforms_can_do( 'activate', $type );
}

/**
 * Determine if the plugin/addon installations/activations are allowed.
 *
 * @since 1.7.3
 *
 * @internal Use wpforms_can_activate() or wpforms_can_install() instead.
 *
 * @param string $what Should be 'activate' or 'install'.
 * @param string $type Should be `plugin` or `addon`.
 *
 * @return bool
 */
function wpforms_can_do( $what, $type ) {

	if ( ! in_array( $what, [ 'install', 'activate' ], true ) ) {
		return false;
	}

	if ( ! in_array( $type, [ 'plugin', 'addon' ], true ) ) {
		return false;
	}

	$capability = $what . '_plugins';

	if ( ! current_user_can( $capability ) ) {
		return false;
	}

	// Determine whether file modifications are allowed and it is activation permissions checking.
	if ( $what === 'install' && ! wp_is_file_mod_allowed( 'wpforms_can_install' ) ) {
		return false;
	}

	// All plugin checks are done.
	if ( $type === 'plugin' ) {
		return true;
	}

	// Addons require additional license checks.
	$license = get_option( 'wpforms_license', [] );

	// Allow addons installation if license is not expired, enabled and valid.
	return empty( $license['is_expired'] ) && empty( $license['is_disabled'] ) && empty( $license['is_invalid'] );
}

/**
 * Retrieve the full config for CAPTCHA.
 *
 * @since 1.6.4
 *
 * @return array
 */
function wpforms_get_captcha_settings() {

	$allowed_captcha_list = [ 'hcaptcha', 'recaptcha' ];
	$captcha_provider     = wpforms_setting( 'captcha-provider', 'recaptcha' );

	if ( ! in_array( $captcha_provider, $allowed_captcha_list, true ) ) {
		return [
			'provider' => 'none',
		];
	}

	return [
		'provider'       => $captcha_provider,
		'site_key'       => sanitize_text_field( wpforms_setting( "{$captcha_provider}-site-key", '' ) ),
		'secret_key'     => sanitize_text_field( wpforms_setting( "{$captcha_provider}-secret-key", '' ) ),
		'recaptcha_type' => wpforms_setting( 'recaptcha-type', 'v2' ),
	];
}

/**
 * Wrapper for set_time_limit to see if it is enabled.
 *
 * @since 1.6.4
 *
 * @param int $limit Time limit.
 */
function wpforms_set_time_limit( $limit = 0 ) {

	if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) { // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.safe_modeDeprecatedRemoved
		@set_time_limit( $limit ); // @codingStandardsIgnoreLine
	}
}

/**
 * Determine if collecting user's IP is allowed by GDPR setting (globally or per form).
 * Majority of our users have GDPR disabled.
 * So we remove this data from the request only when it's not needed:
 * 1) when GDPR is enabled AND globally disabled user details storage;
 * 2) when GDPR is enabled AND IP address processing is disabled on per form basis.
 *
 * @since 1.6.6
 *
 * @param array $form_data Form settings.
 *
 * @return bool
 */
function wpforms_is_collecting_ip_allowed( $form_data = [] ) {

	if (
		wpforms_setting( 'gdpr', false ) &&
		(
			wpforms_setting( 'gdpr-disable-details', false ) ||
			( ! empty( $form_data ) && ! empty( $form_data['settings']['disable_ip'] ) )
		)
	) {
		return false;
	}

	return true;
}

/**
 * Determine if collecting cookies is allowed by GDPR setting.
 *
 * @since 1.7.5
 *
 * @return bool
 */
function wpforms_is_collecting_cookies_allowed() {

	return ! ( wpforms_setting( 'gdpr', false ) && wpforms_setting( 'gdpr-disable-uuid', false ) );
}

/**
 * Retrieve a timezone from the site settings as a `DateTimeZone` object.
 *
 * Timezone can be based on a PHP timezone string or a ±HH:MM offset.
 *
 * @since 1.6.6
 *
 * @return DateTimeZone Timezone object.
 */
function wpforms_get_timezone() {

	if ( function_exists( 'wp_timezone' ) ) {
		return wp_timezone();
	}

	// Fallback for WordPress version < 5.3.
	$timezone_string = get_option( 'timezone_string' );

	if ( ! $timezone_string ) {
		$offset  = (float) get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = ( $offset - $hours );

		$sign     = ( $offset < 0 ) ? '-' : '+';
		$abs_hour = abs( $hours );
		$abs_mins = abs( $minutes * 60 );

		$timezone_string = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );
	}

	return timezone_open( $timezone_string );
}

/**
 * Alias for default readonly function.
 *
 * @since 1.6.9
 *
 * @param mixed $readonly One of the values to compare.
 * @param mixed $current  The other value to compare if not just true.
 * @param bool  $echo     Whether to echo or just return the string.
 *
 * @return string HTML attribute or empty string.
 */
function wpforms_readonly( $readonly, $current = true, $echo = true ) {

	if ( function_exists( 'wp_readonly' ) ) {
		return wp_readonly( $readonly, $current, $echo );
	}

	return __checked_selected_helper( $readonly, $current, $echo, 'readonly' );
}

/**
 * Process smart tags.
 *
 * @since 1.7.1
 *
 * @param string $content   Content.
 * @param array  $form_data Form data.
 * @param array  $fields    List of fields.
 * @param string $entry_id  Entry ID.
 *
 * @return string
 */
function wpforms_process_smart_tags( $content, $form_data, $fields = [], $entry_id = '' ) {

	// Skip it if variables have invalid format.
	if ( ! is_string( $content ) || ! is_array( $form_data ) || ! is_array( $fields ) ) {
		return $content;
	}

	/**
	 * Process smart tags.
	 *
	 * @since 1.4.0
	 *
	 * @param string $content   Content.
	 * @param array  $form_data Form data.
	 * @param array  $fields    List of fields.
	 * @param string $entry_id  Entry ID.
	 *
	 * @return string
	 */
	return apply_filters( 'wpforms_process_smart_tags',  $content, $form_data, $fields, $entry_id );
}

/**
 * Get formatted [ id => title ] pages list.
 *
 * @since 1.7.2
 * @deprecated 1.7.9
 *
 * @param array|string $args Array or string of arguments to retrieve pages.
 *
 * @return array
 */
function wpforms_get_pages_list( $args = [] ) {

	_deprecated_function( __FUNCTION__, '1.7.9 of the WPForms plugin' );

	$defaults = [
		'number' => 20,
	];
	$args     = wp_parse_args( $args, $defaults );
	$pages    = get_pages( $args );
	$list     = [];

	if ( empty( $pages ) ) {
		return $list;
	}

	foreach ( $pages as $page ) {
		$title             = wpforms_get_post_title( $page );
		$depth             = count( $page->ancestors );
		$list[ $page->ID ] = str_repeat( '&nbsp;', $depth * 3 ) . $title;
	}

	return $list;
}

/**
 * Changes array of items into string of items, separated by comma and sql-escaped.
 *
 * @see https://coderwall.com/p/zepnaw
 *
 * @since 1.7.4
 *
 * @param mixed|array $items  Item(s) to be joined into string.
 * @param string      $format Can be %s or %d.
 *
 * @return string Items separated by comma and sql-escaped.
 */
function wpforms_wpdb_prepare_in( $items, $format = '%s' ) {

	global $wpdb;

	$items    = (array) $items;
	$how_many = count( $items );

	if ( $how_many === 0 ) {
		return '';
	}

	$placeholders    = array_fill( 0, $how_many, $format );
	$prepared_format = implode( ',', $placeholders );

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	return $wpdb->prepare( $prepared_format, $items );
}

/**
 * Add UTM tags to a link that allows detecting traffic sources for our or partners' websites.
 *
 * @since 1.7.5
 *
 * @param string $link    Link to which you need to add UTM tags.
 * @param string $medium  The page or location description. Check your current page and try to find
 *                        and use an already existing medium for links otherwise, use a page name.
 * @param string $content The feature's name, the button's content, the link's text, or something
 *                        else that describes the element that contains the link.
 * @param string $term    Additional information for the content that makes the link more unique.
 *
 * @return string
 */
function wpforms_utm_link( $link, $medium, $content = '', $term = '' ) {

	return add_query_arg(
		array_filter(
			[
				'utm_campaign' => wpforms()->is_pro() ? 'plugin' : 'liteplugin',
				'utm_source'   => strpos( $link, 'https://wpforms.com' ) === 0 ? 'WordPress' : 'wpformsplugin',
				'utm_medium'   => rawurlencode( $medium ),
				'utm_content'  => rawurlencode( $content ),
				'utm_term'     => rawurlencode( $term ),
			]
		),
		$link
	);
}

/**
 * Determines whether the current request is a WP CLI request.
 *
 * @since 1.7.6
 *
 * @return bool
 */
function wpforms_doing_wp_cli() {

	return defined( 'WP_CLI' ) && WP_CLI;
}

/**
 * Modify the default USer-Agent generated by wp_remote_*() to include additional information.
 *
 * @since 1.7.5.2
 *
 * @return string
 */
function wpforms_get_default_user_agent() {

	$license_type = wpforms()->is_pro() ? ucwords( (string) wpforms_get_license_type() ) : 'Lite';

	return 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ) . '; WPForms/' . $license_type . '-' . WPFORMS_VERSION;
}

/**
 * Get sanitized post title or "no title" placeholder.
 *
 * The placeholder is prepended with post ID.
 *
 * @since 1.7.6
 *
 * @param WP_Post|object $post Post object.
 *
 * @return string Post title.
 */
function wpforms_get_post_title( $post ) {

	/* translators: %d - a post ID. */
	return wpforms_is_empty_string( trim( $post->post_title ) ) ? sprintf( __( '#%d (no title)', 'wpforms-lite' ), absint( $post->ID ) ) : $post->post_title;
}

/**
 * Get sanitized term name or "no name" placeholder.
 *
 * The placeholder is prepended with term ID.
 *
 * @since 1.7.6
 *
 * @param WP_Term $term Term object.
 *
 * @return string Term name.
 */
function wpforms_get_term_name( $term ) {

	/* translators: %d - a taxonomy term ID. */
	return wpforms_is_empty_string( trim( $term->name ) ) ? sprintf( __( '#%d (no name)', 'wpforms-lite' ), absint( $term->term_id ) ) : $term->name;
}

/**
 * Search for posts editable by user.
 *
 * @since 1.7.9
 *
 * @param string $search_term Optional search term. Default ''.
 * @param array  $args        Args {
 *                            Optional. An array of arguments.
 *
 * @type string   $post_type   Post type to search for.
 * @type string[] $post_status Post status to search for.
 * @type int      $count       Number of results to return. Default 20.
 * }
 *
 * @return array
 * @noinspection PhpTernaryExpressionCanBeReducedToShortVersionInspection
 * @noinspection ElvisOperatorCanBeUsedInspection
 */
function wpforms_search_posts( $search_term = '', $args = [] ) {

	global $wpdb;

	$default_args = [
		'post_type'   => 'page',
		'post_status' => [ 'publish' ],
		'count'       => 20,
	];
	$args         = wp_parse_args( $args, $default_args );

	// @todo: add trash access capabilities to MySQL.
	// See edit_post/edit_page case in map_meta_cap().
	$args['post_status'] = array_diff( $args['post_status'], [ 'trash' ] );

	$user      = wp_get_current_user();
	$user_id   = $user ? $user->ID : 0;
	$post_type = get_post_type_object( $args['post_type'] );

	if ( ! $user_id || ! $post_type || $args['count'] <= 0 ) {
		return [];
	}

	$last_changed = wp_cache_get_last_changed( 'posts' );
	$key          = __FUNCTION__ . ":$search_term:$last_changed";
	$cache_posts  = wp_cache_get( $key, '', false, $found );

	if ( $found ) {
		return $cache_posts;
	}

	$post_title_where = $search_term ? $wpdb->prepare(
		'post_title LIKE %s AND',
		'%' . $wpdb->esc_like( $search_term ) . '%'
	) :
	'';

	$post_statuses              = array_intersect( array_keys( get_post_statuses() ), $args['post_status'] );
	$post_statuses              = wpforms_wpdb_prepare_in( $post_statuses );
	$policy_id                  = (int) get_option( 'wp_page_for_privacy_policy' );
	$can_delete_published_posts = (int) $user->has_cap( $post_type->cap->delete_published_posts );
	$can_delete_posts           = (int) $user->has_cap( $post_type->cap->delete_posts );
	$can_delete_others_posts    = (int) $user->has_cap( $post_type->cap->delete_others_posts );
	$can_delete_private_posts   = (int) $user->has_cap( $post_type->cap->delete_private_posts );
	$can_edit_policy            = (int) $user->has_cap( map_meta_cap( 'manage_privacy_options', $user_id )[0] );

	// For the case when user is post author.
	$capability_author_where = "post_author = $user_id AND
		( ( post_status IN ( 'publish', 'future' ) AND $can_delete_published_posts ) OR
		( ( post_status NOT IN ( 'publish', 'future', 'trash' ) ) AND $can_delete_posts )
		)";

	// For the case when accessing someone other's post.
	$capability_other_where = "post_author != $user_id AND
		$can_delete_others_posts AND
		( ( post_status IN ( 'publish', 'future' ) AND $can_delete_published_posts ) OR
		( ( post_status IN ( 'private' ) ) AND $can_delete_private_posts )
		)";

	// For privacy policy page.
	$capability_policy_where = "ID = $policy_id AND $can_edit_policy";

	$capability_where = '( ' .
	                    '(' . $capability_author_where . ') OR ' .
	                    '(' . $capability_other_where . ') OR ' .
	                    '(' . $capability_policy_where . ')' .
	                    ' )';

	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$posts = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_author
					FROM $wpdb->posts
					WHERE $post_title_where
					post_type = '{$args['post_type']}' AND
					post_status IN ( $post_statuses ) AND
					$capability_where
					ORDER BY post_title LIMIT %d",
			absint( $args['count'] )
		)
	);
	// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	$posts = $posts ? $posts : [];
	$posts = array_map(
		static function ( $post ) {
			$post->post_title = wpforms_get_post_title( $post );

			unset( $post->post_author );

			return $post;
		},
		$posts
	);

	wp_cache_set( $key, $posts );

	return $posts;
}

/**
 * Search pages by search term and return an array containing
 * `value` and `label` which is the post ID and post title respectively.
 *
 * @since 1.7.9
 *
 * @param string $search_term The search term.
 * @param array  $args        Optional. An array of arguments.
 *
 * @return array
 */
function wpforms_search_pages_for_dropdown( $search_term, $args = [] ) {

	$search_results = wpforms_search_posts( $search_term, $args );
	$result_pages   = [];

	// Prepare for ChoicesJS render.
	foreach ( $search_results as $search_result ) {
		$result_pages[] = [
			'value' => absint( $search_result->ID ),
			'label' => esc_html( $search_result->post_title ),
		];
	}

	return $result_pages;
}

/**
 * Convert hex color value to RGB.
 *
 * @since 1.7.9
 *
 * @param string $hex Color value in hex format.
 *
 * @return string Color value in RGB format.
 */
function wpforms_hex_to_rgb( $hex ) {

	$hex = ltrim( $hex, '#' );

	// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
	$rgb_parts = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $hex );

	return sprintf(
		'%1$d, %2$d, %3$d',
		hexdec( $rgb_parts[0] . $rgb_parts[1] ),
		hexdec( $rgb_parts[2] . $rgb_parts[3] ),
		hexdec( $rgb_parts[4] . $rgb_parts[5] )
	);
}
