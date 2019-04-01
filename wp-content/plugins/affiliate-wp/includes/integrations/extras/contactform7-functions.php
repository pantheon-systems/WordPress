<?php
/**
 * Procedural functions for the contactform7 integration.
 * @since 2.0
 * @see   Affiliate_WP_Contact_Form_7
 */

add_action('wp_ajax_nopriv_affwp_cf7_ajax', 'affwp_cf7_ajax', 9999 );
add_action('wp_ajax_affwp_cf7_ajax', 'affwp_cf7_ajax', 9999 );

/**
 * Returns form-specific PayPal meta, via ajax.
 *
 * @since  2.0
 *
 * @return object Ajax response object.
 */
function affwp_cf7_ajax() {
	$affwp_cf7 = new Affiliate_WP_Contact_Form_7;

	return $affwp_cf7->ajax_get_paypal_meta();
}

/**
 * Wrapper for Affiliate_WP_Contact_Form_7::get_url_args.
 * Returns primary query arguments for use in PayPal return or cancel urls.
 *
 * @param  $cf7    CF7 form submission object.
 * @return string  The url query arguments.
 * @see    Affiliate_WP_Contact_Form_7::get_url_args
 * @since  2.0
 */
function affwp_cf7_get_url_args( $cf7 ) {
	$affwp_cf7 = new Affiliate_WP_Contact_Form_7;

	return $affwp_cf7->get_url_args( $cf7 );
}


/**
 * Overrides the default CF7 add-on function.
 *
 * @since  2.0
 *
 * @param  object  $cf7          CF7 form submission object.
 * @param  object  $result       Modified CF7 form object.
 * @param  object  $referral_id  Referral ID.
 *
 * @return void
 */
function affwp_cf7_paypal_redirect( $cf7, $result, $referral_id ) {

	$affwp_cf7 = new Affiliate_WP_Contact_Form_7;

	if ( ! $affwp_cf7->was_referred() ) {
		// No referral var was recorded. Let the PayPal plugin take over.
		if( function_exists( 'cf7pp_after_send_mail' ) ) {
			cf7pp_after_send_mail( $contactform );
		}
	}

	global $postid;

	$postid = $cf7->id();

	$enable = get_post_meta( $postid, "_cf7pp_enable", true );

	$options = get_option('cf7pp_options');

	if ( $options['mode'] == "1" ) {
		$email = $options['sandboxaccount'];
	} elseif ( $options['mode'] == "2" ) {
		$email = $options['liveaccount'];
	}

	if ( $enable == "1" && is_email( $email ) ) {
		affwp_cf7_paypal_redirect_output( $cf7, $result, $referral_id );
	}
}
add_action( 'affwp_cf7_submit', 'affwp_cf7_paypal_redirect', 10, 3 );

/**
 * Overrides the static PHP redirect defined in the CF7 add-on `contact-form-7-paypal-add-on`.
 *
 * @since  2.0
 *
 * @param  object  $cf7          CF7 form object.
 * @param  object  $result       Modified CF7 form object.
 * @param  object  $referral_id  Referral ID.
 *
 * @return void
 */
function affwp_cf7_paypal_redirect_output( $cf7, $result, $referral_id ) {

	// Bail if no CF7 form submission object or referral ID is available.
	if ( ! $cf7 || ! $referral_id ) {
		return;
	}

	global $postid;

	$post_id = $postid;
	$enable  = get_post_meta( $post_id, "_cf7pp_enable", true );
	$name    = get_post_meta( $post_id, "_cf7pp_name",   true );
	$price   = get_post_meta( $post_id, "_cf7pp_price",  true );
	$id      = get_post_meta( $post_id, "_cf7pp_id",     true );
	$email   = get_post_meta( $post_id, "_cf7pp_email",  true );
	$options = get_option('cf7pp_options');

	$referral_id_string = '&referral_id=' . $referral_id;

	foreach ( $options as $k => $v ) {
		$value[$k] = $v;
	}

	// live or test mode
	if ( $value['mode'] == "1" ) {
		$account = $value['sandboxaccount'];
		$path    = "sandbox.paypal";
	} elseif ( $value['mode'] == "2" )  {
		$account = $value['liveaccount'];
		$path    = "paypal";
	}

	// currency
	if ( $value['currency'] == "1" ) { $currency = "AUD"; }
	if ( $value['currency'] == "2" ) { $currency = "BRL"; }
	if ( $value['currency'] == "3" ) { $currency = "CAD"; }
	if ( $value['currency'] == "4" ) { $currency = "CZK"; }
	if ( $value['currency'] == "5" ) { $currency = "DKK"; }
	if ( $value['currency'] == "6" ) { $currency = "EUR"; }
	if ( $value['currency'] == "7" ) { $currency = "HKD"; }
	if ( $value['currency'] == "8" ) { $currency = "HUF"; }
	if ( $value['currency'] == "9" ) { $currency = "ILS"; }
	if ( $value['currency'] == "10" ) { $currency = "JPY"; }
	if ( $value['currency'] == "11" ) { $currency = "MYR"; }
	if ( $value['currency'] == "12" ) { $currency = "MXN"; }
	if ( $value['currency'] == "13" ) { $currency = "NOK"; }
	if ( $value['currency'] == "14" ) { $currency = "NZD"; }
	if ( $value['currency'] == "15" ) { $currency = "PHP"; }
	if ( $value['currency'] == "16" ) { $currency = "PLN"; }
	if ( $value['currency'] == "17" ) { $currency = "GBP"; }
	if ( $value['currency'] == "18" ) { $currency = "RUB"; }
	if ( $value['currency'] == "19" ) { $currency = "SGD"; }
	if ( $value['currency'] == "20" ) { $currency = "SEK"; }
	if ( $value['currency'] == "21" ) { $currency = "CHF"; }
	if ( $value['currency'] == "22" ) { $currency = "TWD"; }
	if ( $value['currency'] == "23" ) { $currency = "THB"; }
	if ( $value['currency'] == "24" ) { $currency = "TRY"; }
	if ( $value['currency'] == "25" ) { $currency = "USD"; }

	// language
	if ( $value['language'] == "1" ) {
		$language = "da_DK";
	} //Danish

	if ( $value['language'] == "2" ) {
		$language = "nl_BE";
	} //Dutch

	if ( $value['language'] == "3" ) {
		$language = "EN_US";
	} //English

	if ( $value['language'] == "20" ) {
		$language = "en_GB";
	} //English - UK

	if ( $value['language'] == "4" ) {
		$language = "fr_CA";
	} //French

	if ( $value['language'] == "5" ) {
		$language = "de_DE";
	} //German

	if ( $value['language'] == "6" ) {
		$language = "he_IL";
	} //Hebrew

	if ( $value['language'] == "7" ) {
		$language = "it_IT";
	} //Italian

	if ( $value['language'] == "8" ) {
		$language = "ja_JP";
	} //Japanese

	if ( $value['language'] == "9" ) {
		$language = "no_NO";
	} //Norwgian

	if ( $value['language'] == "10" ) {
		$language = "pl_PL";
	} //Polish

	if ( $value['language'] == "11" ) {
		$language = "pt_BR";
	} //Portuguese

	if ( $value['language'] == "12" ) {
		$language = "ru_RU";
	} //Russian

	if ( $value['language'] == "13" ) {
		$language = "es_ES";
	} //Spanish

	if ( $value['language'] == "14" ) {
		$language = "sv_SE";
	} //Swedish

	if ( $value['language'] == "15" ) {
		$language = "zh_CN";
	} //Simplified Chinese - China

	if ( $value['language'] == "16" ) {
		$language = "zh_HK";
	} //Traditional Chinese - Hong Kong

	if ( $value['language'] == "17" ) {
		$language = "zh_TW";
	} //Traditional Chinese - Taiwan

	if ( $value['language'] == "18" ) {
		$language = "tr_TR";
	} //Turkish

	if ( $value['language'] == "19" ) {
		$language = "th_TH";
	} //Thai

	$affwp_cf7_return = $options['return'] . affwp_cf7_get_url_args( $cf7 ) . $referral_id_string;
	$affwp_cf7_cancel = $options['cancel'] . affwp_cf7_get_url_args( $cf7 ) . $referral_id_string;

	$form_url = 'https://www.' . $path . '.com/cgi-bin/webscr';

	?>
	<html>
		<head>
			<title><?php _e('Redirecting to Paypal...', 'affiliate-wp' ); ?></title>
		</head>
		<body>
			<form action='<?php echo esc_url( $form_url ); ?>' method='post' name="cf7pp">
			<input type='hidden' name='cmd' value='_xclick' />
			<input type='hidden' name='business' value='<?php echo $account; ?>' />
			<input type='hidden' name='item_name' value='<?php echo $name; ?>' />
			<input type='hidden' name='currency_code' value='<?php echo $currency; ?>' />
			<input type='hidden' name='amount' value='<?php echo $price; ?>' />
			<input type='hidden' name='lc' value='<?php echo $language; ?>'>
			<input type='hidden' name='item_number' value='<?php echo $id; ?>' />
			<input type='hidden' name='return' value='<?php echo esc_url( $affwp_cf7_return ); ?>' />
			<input type='hidden' name='bn' value='WPPlugin_SP'>
			<input type='hidden' name='cancel_return' value='<?php echo esc_url( $affwp_cf7_cancel ); ?>' />
			<input type='hidden' name='custom' value='<?php echo $referral_id; ?>'>
			<img alt='' border='0' style='border:none;display:none;' src='https://www.paypal.com/<?php echo $language; ?>/i/scr/pixel.gif' width='1' height='1'>
			</form>
			<script type="text/javascript">
			document.cf7pp.submit();
			</script>
		</body>
	</html>
	<?php
}
