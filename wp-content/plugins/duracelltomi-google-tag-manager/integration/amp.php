<?php
/**
* AMP (Accelerated Mobile Pages Support) for gtm4wp
*
* This intergration added AMP support when using amp-wp, the existing dataLayer used
* for Google Tag Manager on HTML is loaded and built into AMP compatible code.
*
* @author 	Vincent Koc <https://github.com/koconder/>
* @package	gtm4wp
*/

/**
*
* Todo's
*
* - Better handling of gtm4wp_amp_gtmampcode_check() to allow for other plugins
* - Develop array's into strings as AMP GTM dose not allow custom js variables
* - Better Client ID support (https://github.com/Automattic/amp-wp/issues/775)
* - Update AMP cache on GTM changes (https://github.com/Automattic/amp-wp/pull/605)
* - Supporting PWA with SuperPWA intergration with AMP and PWA for offline
*
*/


/**
 * Check if we are running AMP
 *
 * @author Vincent Koc <https://github.com/koconder/>
 * @return bool Returns true if we are running on an AMP page
 */
function gtm4wp_amp_running(){
	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		return true;
	}

	return false;
}

/**
 * Pre-injection Check AMP Project's AMP Analytics tag, using amp-wp hook
 *
 * @link https://github.com/Automattic/amp-wp/blob/develop/includes/amp-post-template-actions.php
 * @author Vincent Koc <https://github.com/koconder/>
 * @return array Returns AMP Analytics array used by amp-wp
 */
function gtm4wp_amp_gtmampcode_check( $data ) {
	global $gtm4wp_amp_headerinjected;

	// AMP-WP Plugin
	if ( ! empty( $data['amp_analytics'] ) ) {
		// Inject into AMP Plugin to load
		$data[ 'amp_component_scripts' ][ 'amp-analytics' ] = 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js';
		$gtm4wp_amp_headerinjected = true;

	// Manually load into AMP-WP Plugin
	} else {
		// Inject manually based on AMP <head> hook
		add_action( 'amp_post_template_head', 'gtm4wp_amp_gtmampcode_injecthead' );
	}

	// Return the $data back to amp-wp hook
	return $data;
}

/**
 * Post-check AMP Project's AMP Analytics tag, using amp-wp hook
 *
 * @link https://github.com/Automattic/amp-wp/blob/develop/includes/amp-post-template-actions.php
 * @author Vincent Koc <https://github.com/koconder/>
 */
function gtm4wp_amp_gtmampcode_injecthead() {
	global $gtm4wp_amp_headerinjected;

	$gtm4wp_amp_headerinjected = true;
	echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
}

/**
 * Generate AMP ready Google Tag Manager code
 *
 * @author Vincent Koc <https://github.com/koconder/>
 * @return int Returns number of injected snippets, false if no injection
 */
function gtm4wp_amp_gtmcode() {
	global $gtm4wp_datalayer_json, $gtm4wp_options;

	// Check dataLayer is loaded from the plugin
	if( !empty( $gtm4wp_datalayer_json ) ) {

		// Builds a list of GTM id's
		$gtm4wp_ampids = explode( ",", $gtm4wp_options[ GTM4WP_OPTION_INTEGRATE_AMPID ] );
		$gtm4wp_ampid_list = array();

		// Counter used for status return
		$x = 0;

		// Check we have more than one valid Google Tag Manager ID
		if ( count( $gtm4wp_ampids ) > 0 ) {

			// Loop through each GTM idea and build the AMP GTM code
			foreach( $gtm4wp_ampids as $gtm4wp_oneampid ) {

				// Docs: https://developers.google.com/analytics/devguides/collection/amp-analytics/
				// Examples: from https://www.simoahava.com/analytics/accelerated-mobile-pages-via-google-tag-manager/

				// Inject the AMP GTM code
				// TODO: Use AMP classes to enable cross-compatibility with other future plugins
				echo '<!-- Google Tag Manager --><amp-analytics config="https://www.googletagmanager.com/amp.json?id='.$gtm4wp_oneampid.'&gtm.url=SOURCE_URL" data-credentials="include">'.gtm4wp_amp_gtmvariables().'</amp-analytics>';

				// Add to counter
				$x++;
			}

			// Check how many injections for return
			if( $x > 0 ) {
				return $x;
			}
		}
	}

	// No injection has occured
	return false;
}

/**
 * Generate the AMP "vars" from the GTM dataLayer
 *
 * @author Vincent Koc <https://github.com/koconder/>
 * @return string Returns json dataLayer for AMP code
 */
function gtm4wp_amp_gtmvariables() {
	global $gtm4wp_datalayer_json;
	return '{"vars":{'.$gtm4wp_datalayer_json."} }";
}


// Set Status at start
$gtm4wp_amp_headerinjected = false;

// Load AMP-Analytics tag into <head>
add_action( 'amp_post_template_data', 'gtm4wp_amp_gtmampcode_check' );

// Load the GTM code processing to gain the GTM DataLayer
add_action( 'amp_post_template_head', 'gtm4wp_wp_header_begin');
add_action( 'amp_post_template_head', 'gtm4wp_wp_header_top', 1 );

//Try amp_post_template_body
//(https://github.com/Automattic/amp-wp/pull/1143)
//add_action( 'amp_post_template_body', 'gtm4wp_amp_gtmcode');

// Publish the GTM code and dataLayer to the footer
add_action( 'amp_post_template_footer', 'gtm4wp_amp_gtmcode');