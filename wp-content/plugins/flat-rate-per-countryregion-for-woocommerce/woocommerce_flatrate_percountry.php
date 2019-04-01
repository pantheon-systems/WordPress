<?php
/**
 * Plugin Name: Flat Rate per State/Country/Region for WooCommerce
 * Plugin URI: https://www.webdados.pt/produtos-e-servicos/internet/desenvolvimento-wordpress/flat-rate-per-countryregion-woocommerce-wordpress/
 * Description: This plugin allows you to set a flat delivery rate per States, Countries or World Regions (and a fallback "Rest of the World" rate) on WooCommerce.
 * Version: 2.5.3.1
 * Author: Webdados
 * Author URI: https://www.webdados.pt
 * Text Domain: flat-rate-per-countryregion-for-woocommerce
 * Domain Path: /lang
 * WC tested up to: 3.5.2
**/

/* WooCommerce CRUD not needed */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 **/
// Get active network plugins - "Stolen" from Novalnet Payment Gateway
function frpc_active_nw_plugins() {
	if (!is_multisite())
		return false;
	$frpc_activePlugins = (get_site_option('active_sitewide_plugins')) ? array_keys(get_site_option('active_sitewide_plugins')) : array();
	return $frpc_activePlugins;
}
if (in_array('woocommerce/woocommerce.php', (array) get_option('active_plugins')) || in_array('woocommerce/woocommerce.php', (array) frpc_active_nw_plugins())) {
	
	
	function woocommerce_flatrate_percountry_init() {
		
		if ( ! class_exists( 'WC_Flat_Rate_Per_Country_Region' ) ) {
		class WC_Flat_Rate_Per_Country_Region extends WC_Shipping_Method {
			/**
			 * Constructor for your shipping class
			 *
			 * @access public
			 * @return void
			 */
			public function __construct() {
				$this->version              = '2.5.3.1';
				$this->id					= 'woocommerce_flatrate_percountry';
				//load_plugin_textdomain( 'flat-rate-per-countryregion-for-woocommerce', false, dirname(plugin_basename(__FILE__)) . '/lang/' );
				load_plugin_textdomain( 'flat-rate-per-countryregion-for-woocommerce' );
				$this->method_title			= __('Flat Rate per State/Country/Region', 'flat-rate-per-countryregion-for-woocommerce');
				$this->method_description	= __('Allows you to set a flat delivery rate per country and/or world region.<br/><br/>If you set a rate for the client\'s country it will be used. Otherwise, if you set a rate for client\'s region it will be used.<br/>If none of the rates are set, the "Rest of the World" rate will be used.', 'flat-rate-per-countryregion-for-woocommerce').'<br/><br/>'.__('You can also choose either to apply the shipping fee for the whole order or multiply it per each item.', 'flat-rate-per-countryregion-for-woocommerce');
				$this->wpml = function_exists('icl_object_id') && function_exists('icl_register_string');
				$this->polylang = $this->wpml; //Not used yet
				if ($this->wpml) { //Really WPML?
					global $sitepress;
					$this->wpml=is_object($sitepress);
					if (!$this->wpml) {
						//Maybe Polylang?
						//...
					}
				}
				if ($this->wpml) {
					$this->shipping_classes=$this->get_all_shipping_classes_wpml();
				} else {
					$this->shipping_classes=$this->get_all_shipping_classes();
				}

				$this->init();
				$this->init_form_fields_per_region();
				$this->init_form_fields_per_country();
				$this->init_form_fields_per_state();
				$this->tax_status = $this->settings['tax_status']; //Important so that WooCommerce knows if it should or shouldn't add taxes to this method
			}

			/* Init the settings */
			function init() {
				//Let's sort arrays the right way
				setlocale(LC_COLLATE, get_locale());
				//Regions - Source: http://www.geohive.com/earth/gen_codes.aspx
				if ( WC()->countries ) {
					$this->regions = array(
						//Africa
						'AF_EA' => array(
							'name' => __('Africa - Eastern Africa', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BI', 'KM' ,'DJ', 'ER', 'ET', 'KE', 'MG', 'MW', 'MU', 'YT', 'MZ', 'RE', 'RW', 'SC', 'SO', 'TZ', 'UG', 'ZM', 'ZW'),
						),
						'AF_MA' => array(
							'name' => __('Africa - Middle Africa', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AO', 'CM', 'CF', 'TD', 'CG', 'CD', 'GQ', 'GA', 'ST'),
						),
						'AF_NA' => array(
							'name' => __('Africa - Northern Africa', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('DZ', 'EG', 'LY', 'MA', 'SS', 'SD', 'TN', 'EH'),
						),
						'AF_SA' => array(
							'name' => __('Africa - Southern Africa', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BW', 'LS', 'NA', 'ZA', 'SZ'),
						),
						'AF_WA' => array(
							'name' => __('Africa - Western Africa', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BJ', 'BF', 'CV', 'CI', 'GM', 'GH', 'GN', 'GW', 'LR', 'ML', 'MR', 'NE', 'NG', 'SH', 'SN', 'SL', 'TG'),
						),
						//Americas
						'AM_LAC' => array(
							'name' => __('Americas - Latin America and the Caribbean', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AI', 'AG', 'AW', 'BS', 'BB', 'BQ', 'VG', 'KY', 'CU', 'CW', 'DM', 'DO', 'GD', 'GP', 'HT', 'JM', 'MQ', 'MS', 'PR', 'BL', 'KN', 'LC', 'MF', 'VC', 'SX', 'TT', 'TC', 'VI'),
						),
						'AM_CA' => array(
							'name' => __('Americas - Central America', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BZ', 'CR', 'SV', 'GT', 'HN', 'MX', 'NI', 'PA'),
						),
						'AM_SA' => array(
							'name' => __('Americas - South America', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AR', 'BO', 'BR', 'CL', 'CO', 'EC', 'FK', 'GF', 'GY', 'PY', 'PE', 'SR', 'UY', 'VE'),
						),
						'AM_NA' => array(
							'name' => __('Americas - Northern America', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BM', 'CA', 'GL', 'PM', 'US'),
						),
						//Asia
						'AS_CA' => array(
							'name' => __('Asia - Central Asia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('KZ', 'KG', 'TJ', 'TM', 'UZ'),
						),
						'AS_EA' => array(
							'name' => __('Asia - Eastern Asia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('CN', 'HK', 'MO', 'JP', 'KP', 'KR', 'MN', 'TW'),
						),
						'AS_SA' => array(
							'name' => __('Asia - Southern Asia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AF', 'BD', 'BT', 'IN', 'IR', 'MV', 'NP', 'PK', 'LK'),
						),
						'AS_SEA' => array(
							'name' => __('Asia - South-Eastern Asia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BN', 'KH', 'ID', 'LA', 'MY', 'MM', 'PH', 'SG', 'TH', 'TL', 'VN'),
						),
						'AS_WA' => array(
							'name' => __('Asia - Western Asia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AM', 'AZ', 'BH', 'CY', 'GE', 'IQ', 'IL', 'JO', 'KW', 'LB', 'PS', 'OM', 'QA', 'SA', 'SY', 'TR', 'AE', 'YE'),
						),
						//Europe
						'EU_EE' => array(
							'name' => __('Europe - Eastern Europe', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BY', 'BG', 'CZ', 'HU', 'MD', 'PL', 'RO', 'RU', 'SK', 'UA'),
						),
						'EU_NE' => array(
							'name' => __('Europe - Northern Europe', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AX', 'DK', 'EE', 'FO', 'FI', 'GG', 'IS', 'IE', 'JE', 'LV', 'LT', 'IM', 'NO', 'SJ', 'SE', 'GB'),
						),
						'EU_SE' => array(
							'name' => __('Europe - Southern Europe', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AL', 'AD', 'BA', 'HR', 'GI', 'GR', 'VA', 'IT', 'MK', 'MT', 'ME', 'PT', 'SM', 'RS', 'SI', 'ES'),
						),
						'EU_WE' => array(
							'name' => __('Europe - Western Europe', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AT', 'BE', 'FR', 'DE', 'LI', 'LU', 'MC', 'NL', 'CH'),
						),
						//Special EU Group
						/*'EU_EU' => array(
							'name' => __('European Union', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('BE', 'BG', 'CZ', 'DK', 'DE', 'EE', 'IE', 'GR', 'ES', 'FR', 'HR', 'IT', 'CY', 'LV', 'LT', 'LU', 'HU', 'MT', 'NL', 'AT', 'PL', 'PT', 'RO', 'SI', 'SK', 'FI', 'SE', 'GB'),
						),*/
						//Special EU Group - From WooCommerce
						'EU_EU' => array(
							'name' => __('European Union', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => WC()->countries->get_european_union_countries()
						),
						//Special EU Group + Monaco and Isle of Man - From WooCommerce
						'EU_EUVAT' => array(
							'name' => __('European Union', 'flat-rate-per-countryregion-for-woocommerce').' + '.__('Monaco and Isle of Man', 'flat-rate-per-countryregion-for-woocommerce').' (EU VAT)',
							'countries' => WC()->countries->get_european_union_countries('eu_vat')
						),
						//Oceania
						'OC_ANZ' => array(
							'name' => __('Oceania - Australia and New Zealand', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AU', 'CX', 'CC', 'NZ', 'NF'),
						),
						'OC_ML' => array(
							'name' => __('Oceania - Melanesia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('FJ', 'NC', 'PG', 'SB', 'VU'),
						),
						'OC_MN' => array(
							'name' => __('Oceania - Micronesia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('GU', 'KI', 'MH', 'FM', 'NR', 'MP', 'PW'),
						),
						'OC_PL' => array(
							'name' => __('Oceania - Polynesia', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AS', 'CK', 'PF', 'NU', 'PN', 'WS', 'TK', 'TO', 'TV', 'WF'),
						),
						/*
						'UNCLASSIFIED' => array(
							'name' => __('Unclassified', 'flat-rate-per-countryregion-for-woocommerce'),
							'countries' => array('AQ', 'BV', 'IO', 'TF', 'HM', 'AN', 'GS', 'UM'),
						),
						*/
						/*
						AQ - Antarctica
						BV - Bouvet Island
						IO - British Indian Ocean Territory
						TF - French Southern Territories
						HM - Heard Island and McDonald Islands
						AN - Netherlands Antilles
						GS - South Georgia/Sandwich Islands
						UM - ?
						*/
					);
				} else {
					$this->regions = array();
				} 
				$this->regionslist=array();
				foreach($this->regions as $key => $temp) {
					$this->regionslist[$key]=$temp['name'];
				}
				asort($this->regionslist, SORT_LOCALE_STRING);

				// Load the settings API
				$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
				$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

				//Fix 2.5.1 - Reduce variable name length
				if ( isset( $this->version ) && version_compare( $this->version, '2.5.1', '>=' ) ) {
					if ( ! isset( $this->settings['fix_251'] ) ) {
						$fixed = 0;
						//World
						$this->settings['world_fr_class_type'] = $this->settings['world_free_class_type'];
						unset( $this->settings['world_free_class_type'] );
						$fixed++;
						//Regions
						foreach ( $this->settings as $key => $value ) {
							if ( substr( $key, 0, 10 ) == 'per_region' ) {
								$new_key = 'pr'.substr( $key, 10 );
								$this->settings[$new_key] = $value;
								unset( $this->settings[$key] );
								$fixed++;
							}
						}
						//Countries
						foreach ( $this->settings as $key => $value ) {
							if ( substr( $key, 0, 11 ) == 'per_country' ) {
								$new_key = 'pc'.substr( $key, 11 );
								$this->settings[$new_key] = $value;
								unset( $this->settings[$key] );
								$fixed++;
							}
						}
						//States
						foreach ( $this->settings as $key => $value ) {
							if ( substr( $key, 0, 9 ) == 'per_state' ) {
								$new_key = 'ps'.substr( $key, 9 );
								$this->settings[$new_key] = $value;
								unset( $this->settings[$key] );
								$fixed++;
							}
						}
						//Set fix
						$this->settings['fix_251'] = 1;
						update_option( 'woocommerce_'.$this->id.'_settings', $this->settings );
					}
				}

				$this->title				= $this->settings['title'];
				$this->enabled				= $this->settings['enabled'];

				//WPML label
				if ($this->wpml) add_filter('woocommerce_cart_shipping_method_full_label', array($this, 'wpml_shipping_method_label'), 9, 2);

				//Remove "free" from the label
				if (isset($this->settings['remove_free'])) {
					if ($this->settings['remove_free']=='yes') {
						add_filter('woocommerce_cart_shipping_method_full_label', array($this, 'remove_free_price_text'), 10, 2);
					}
				}

				// Save settings in admin if you have any defined
				add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				if ($this->wpml) add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'register_wpml_strings'));

			}

			function get_all_shipping_classes() {
				$shipping_classes=array();
				if ( $temp=WC()->shipping->get_shipping_classes() ) {
					if (!is_wp_error($temp)) { //On Multisite we aren't able to get the terms... - To be fixed.
						foreach ( $temp as $shipping_class ) {
							$shipping_classes[$shipping_class->slug]=$shipping_class->name;
						}
					}
				}
				return $shipping_classes;
			}

			/**
			 * WPML compatibility
			 * We need to register our own several possible Shipping Method titles because WooCommerce Multilingual assumes each Shipping Method will only have one static title
			 */
			function register_wpml_strings() {
				$to_register=array(
					'title',
					'world_rulename',
				);
				//Region
				$count=(isset($this->settings['pr_count']) ? intval($this->settings['pr_count']) : 0);
				for($counter = 1; $count >= $counter; $counter++) {
					$to_register[]='pr_'.$counter.'_txt';
				}
				//Country
				$count=(isset($this->settings['pc_count']) ? intval($this->settings['pc_count']) : 0);
				for($counter = 1; $count >= $counter; $counter++) {
					$to_register[]='pc_'.$counter.'_txt';
				}
				//State
				$count=(isset($this->settings['ps_count']) ? intval($this->settings['ps_count']) : 0);
				for($counter = 1; $count >= $counter; $counter++) {
					$to_register[]='ps_'.$counter.'_txt';
				}
				foreach($to_register as $string) {
					//Only if the state rule name exists already (we may still be choosing the country for this rule)
					if (isset($this->settings[$string])) icl_register_string($this->id, $this->id.'_'.$string, $this->settings[$string]);
				}
				add_action($this->id.'_notices', array($this, 'register_wpml_strings_notice'));
			}
			function register_wpml_strings_notice() {
				?>
				<div id="message" class="updated">
					<p>
						<strong>
							<?php printf(__( 'You should now check and, if necessary, translate the rule names and method title on <a href="%s">WPML String Translation</a>', 'flat-rate-per-countryregion-for-woocommerce'), 'admin.php?page=wpml-string-translation/menu/string-translation.php&amp;context='.$this->id); ?>
						</strong>
					</p>
				</div>
				<?php
			}

			function get_all_shipping_classes_wpml() {
				$shipping_classes=array();
				$terms=get_terms('product_shipping_class', array(
					'hide_empty'	=> false
				));
				if (!is_wp_error($terms)) { //On Multisite we aren't able to get the terms... - To be fixed.
					global $sitepress;
					$langs=$sitepress->get_active_languages();
					foreach($terms as $term) {
						$shipping_classes[$term->slug]=$term->name;
						foreach($langs as $lang => $language) {
							if ($term_tr=$this->get_translated_term($term->term_id, 'product_shipping_class', $lang)) {
								$shipping_classes[$term_tr->slug]=$term_tr->name;
							}
						}
					}
				}
				return $shipping_classes;
			}

			/* Get translated term */
			function get_translated_term($term_id, $taxonomy, $language) {
				global $sitepress;
				$translated_term_id = icl_object_id(intval($term_id), $taxonomy, true, $language);
				remove_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1 );
				$translated_term_object = get_term_by('id', intval($translated_term_id), $taxonomy);
				add_filter( 'get_term', array( $sitepress, 'get_term_adjust_id' ), 1, 1 );
				return $translated_term_object;
			}

			/* The form */
			function init_form_fields() {
				$fields = array(
					'global_def' => array(
						'title'		 => __('Global settings', 'flat-rate-per-countryregion-for-woocommerce'),
						'type'		  => 'title'
					),
					'enabled' => array(
						'title'		=> __('Enable/Disable', 'flat-rate-per-countryregion-for-woocommerce'),
						'type'			=> 'checkbox',
						'label'		=> __('Enable this shipping method', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'no',
						'desc_tip'		=> true
					),
					'title' => array(
						'title'		=> __('Method title', 'flat-rate-per-countryregion-for-woocommerce'),
						'type'			=> 'text',
						'description'	=> __('This controls the title which the user sees during checkout.', 'flat-rate-per-countryregion-for-woocommerce').' '.__('(If chosen below)', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> __('Flat Rate per State/Country/Region', 'flat-rate-per-countryregion-for-woocommerce'),
						'desc_tip'		=> true
					),
					'show_region_country' => array(
						'title'		=> __('Label to show to the user', 'flat-rate-per-countryregion-for-woocommerce'),
						'type'			=> 'select',
						'description'	=> __('Choose either to show the region name, the country name, the method title (or a combination of these) on the checkout screen.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'region',
						'options'		=> array(
								'country'		=> __('Country', 'flat-rate-per-countryregion-for-woocommerce'),
								'region'		=> __('State or Country or Region name or "Rest of the World"', 'flat-rate-per-countryregion-for-woocommerce'),
								'title'		=> __('Method title', 'flat-rate-per-countryregion-for-woocommerce').' '.__('(as defined above)', 'flat-rate-per-countryregion-for-woocommerce'),
								'title_country'	=> __('Method title', 'flat-rate-per-countryregion-for-woocommerce').' + '.__('Country', 'flat-rate-per-countryregion-for-woocommerce'),
								'title_region'	=> __('Method title', 'flat-rate-per-countryregion-for-woocommerce').' + '.__('State or Country or Region name or "Rest of the World"', 'flat-rate-per-countryregion-for-woocommerce'),
								'rule_name'	=> __('Rule name', 'flat-rate-per-countryregion-for-woocommerce'),
							),
						'desc_tip'		=> true
					),
					'tax_status' => array(
						'title'		=> __('Tax Status', 'flat-rate-per-countryregion-for-woocommerce'),
						'type'			=> 'select',
						'description'	=> '',
						'default'		=> 'taxable',
						'options'		=> array(
								'taxable'	=> __('Taxable', 'flat-rate-per-countryregion-for-woocommerce'),
								'none'		=> __('None', 'flat-rate-per-countryregion-for-woocommerce'),
							),
						'desc_tip'		=> true
					),
					'remove_free' => array(
						'title'		=> __('Remove "(Free)"', 'flat-rate-per-countryregion-for-woocommerce'),
						'type'			=> 'checkbox',
						'description'	=> __('If the final rate is zero, remove the "(Free)" text from the checkout screen. Useful if you need to get a quote for the shipping cost from the carrier.', 'flat-rate-per-countryregion-for-woocommerce'),
						'label'		=> __('Remove "(Free)" from checkout if delivery rate equals zero', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'no',
						'desc_tip'		=> true
					),
					'world_title' => array(
						'title'		 => __('"Rest of the World" Rates', 'flat-rate-per-countryregion-for-woocommerce'),
						'type'		 => 'title'
					),
					'world_disable' => array(
						'title'		=> '<span class="rules_items">'.__('Disable', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'checkbox',
						'description'	=> __('If this checkbox is enabled, this shipping method will only be available for the World Regions, Countries and States rules set below.', 'flat-rate-per-countryregion-for-woocommerce'),
						'label'		=> __('Disable "Rest of the World" fee', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'no',
						'desc_tip'		=> true
					),
					'world_rulename' => array(
						'title'		=> '<span class="rules_items">'.__( 'Rule name', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'text',
						'description'	=> __('The name for this rule, if you choose to show it to the client.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> __('Rest of the World', 'flat-rate-per-countryregion-for-woocommerce'),
						'placeholder'	=> '',
						'desc_tip'		=> true
					),
					'tax_type' => array(
						'title'		=> '<span class="rules_items">'.__('Apply rate', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'select',
						'description'	=> __('Choose either to apply the shipping fee for the whole order or multiply it per each item.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'per_order',
						'options'		=> array(
								'per_order'	=> __('Per order', 'flat-rate-per-countryregion-for-woocommerce'),
								'per_item'		=> __('Per item', 'flat-rate-per-countryregion-for-woocommerce'),
							),
						'desc_tip'		=> true
					),
					'fee_world' => array(
						'title'		=> '<span class="rules_items">'.__('Rate', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
						'type'			=> 'price',
						'description'	=> __('The shipping fee for all the Countries/Regions not specified below.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '0',
						'desc_tip'		=> true
					),
					'world_free_above' => array(
						'title'		=> '<span class="rules_items">'.__('Free for orders above', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
						'type'			=> 'price',
						'description'	=> __('The shipping fee will be free if the order total reaches this value. Empty or zero for no free shipping.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '',
						'desc_tip'		=> true
					),
					'world_free_class' => array(
						'title'		=> '<span class="rules_items">'.__('Free for shipping classes', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? '</span><br/><span class="rules_items">('.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce').')' : '').'</span>',
						'type'		=> 'multiselect',
						'description'	=> __('The shipping fee will be free if at least one item, or all items, depending on the setting below, belong to the selected shipping classes.', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? ' '.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce') : ''),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> $this->shipping_classes,
						'desc_tip'		=> true
					),
					'world_fr_class_type' => array(
						'title'		=> '<span class="rules_items">'.__('Free for shipping classes if', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'select',
						'description'	=> __('Choose either one item on Shipping Class is enough to set the rate as free or all items should belong to the Shipping Classes.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'one',
						'options'		=> array(
							'one'	=> __('At least one item belongs to the class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
							'all'	=> __('All items belong to class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
						),
						'desc_tip'		=> true
					),
				);
				$this->form_fields=$fields;
			}

			/* Per Region form fields */
			function init_form_fields_per_region() {
				$this->form_fields['pr']=array(
					'title'		 => __('Per Region Rates', 'flat-rate-per-countryregion-for-woocommerce'),
					'type'		 => 'title'
				);
				$this->form_fields['pr_count']=array(
					'title'		=> __('Number of Region rules', 'flat-rate-per-countryregion-for-woocommerce'),
					'type'			=> 'number',
					'description'	=> __('How many diferent "per region" rates do you want to set?', 'flat-rate-per-countryregion-for-woocommerce').' '.__('Please save the options after changing this value.', 'flat-rate-per-countryregion-for-woocommerce'),
					'default'		=> 0,
					'desc_tip'		=> true
				);
				$count=(isset($this->settings['pr_count']) ? intval($this->settings['pr_count']) : 0);
				for($counter = 1; $count >= $counter; $counter++) {
					$this->form_fields['pr_'.$counter.'_sep']=array(
						'title'		=> sprintf(__( 'Region rule #%s', 'flat-rate-per-countryregion-for-woocommerce'), $counter),
						'class'		=> 'rules_sep',
						'type'		 => 'rules_sep'
					);
					$this->form_fields['pr_'.$counter.'_region']=array(
						'title'		=> '<span class="rules_items">'.__( 'Region', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'		=> 'multiselect',
						'description'	=> __('Choose one or more regions for this rule.', 'flat-rate-per-countryregion-for-woocommerce'),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> $this->regionslist,
						'desc_tip'		=> true
					);
					$this->form_fields['pr_'.$counter.'_txt']=array(
						'title'		=> '<span class="rules_items">'.__( 'Rule name', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'text',
						'description'	=> __('The name for this rule, if you choose to show it to the client.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '',
						'desc_tip'		=> true
					);
					$this->form_fields['pr_'.$counter.'_t']= array(
						'title'		=> '<span class="rules_items">'.__('Apply rate', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'select',
						'description'	=> __('Choose either to apply the shipping fee for the whole order or multiply it per each item.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'per_order',
						'options'		=> array(
								'per_order'	=> __('Per order', 'flat-rate-per-countryregion-for-woocommerce'),
								'per_item'		=> __('Per item', 'flat-rate-per-countryregion-for-woocommerce'),
							),
						'desc_tip'		=> true
					);
					$this->form_fields['pr_'.$counter.'_fee']=array(
						'title'		=> '<span class="rules_items">'.__( 'Rate', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
						'type'			=> 'price',
						'description'	=> __('The shipping fee for the regions specified above.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '0',
						'desc_tip'		=> true
					);
					$this->form_fields['pr_'.$counter.'_fr']=array(
						'title'		=> '<span class="rules_items">'.__( 'Free for orders above', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
						'type'			=> 'price',
						'description'	=> __('The shipping fee will be free if the order total reaches this value. Empty or zero for no free shipping.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '',
						'desc_tip'		=> true
					);
					$this->form_fields['pr_'.$counter.'_fr_class']=array(
						'title'		=> '<span class="rules_items">'.__('Free for shipping classes', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? '</span><br/><span class="rules_items">('.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce').')' : '').'</span>',
						'type'		=> 'multiselect',
						'description'	=> __('The shipping fee will be free if at least one item belongs to the selected shipping classes.', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? ' '.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce') : ''),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> $this->shipping_classes,
						'desc_tip'		=> true
					);
					$this->form_fields['pr_'.$counter.'_fr_class_type']=array(
						'title'		=> '<span class="rules_items">'.__('Free for shipping classes if', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'select',
						'description'	=> __('Choose either one item on Shipping Class is enough to set the rate as free or all items should belong to the Shipping Classes.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'one',
						'options'		=> array(
							'one'	=> __('At least one item belongs to the class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
							'all'	=> __('All items belong to class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
						),
						'desc_tip'		=> true
					);
					$this->form_fields['pr_'.$counter.'_disable_class']=array(
						'title'		=> '<span class="rules_items">'.__('Disable for shipping classes', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? '</span><br/><span class="rules_items">('.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce').')' : '').'</span>',
						'type'		=> 'multiselect',
						'description'	=> __('The shipping fee will not be available if at least one item belongs to the selected shipping classes. This may be useful for disabling shipping of certain products to certain destinations, if this plugin is the only one being used for shipping cost calculations.', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? ' '.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce') : ''),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> $this->shipping_classes,
						'desc_tip'		=> true
					);
				}
			}

			/* Per Country form fields */
			function init_form_fields_per_country() {
				$this->form_fields['pc']=array(
					'title'		 => __('Per Country Rates', 'flat-rate-per-countryregion-for-woocommerce'),
					'type'		  => 'title'
				);
				$this->form_fields['pc_count']=array(
					'title'		=> __('Number of Country rules', 'flat-rate-per-countryregion-for-woocommerce'),
					'type'			=> 'number',
					'description'	=> __('How many diferent "per country" rates do you want to set?', 'flat-rate-per-countryregion-for-woocommerce').' '.__('Please save the options after changing this value.', 'flat-rate-per-countryregion-for-woocommerce'),
					'default'		=> 0,
					'desc_tip'		=> true
				);
				$count=(isset($this->settings['pc_count']) ? intval($this->settings['pc_count']) : 0);
				for($counter = 1; $count >= $counter; $counter++) {
					$this->form_fields['pc_'.$counter.'_sep']=array(
						'title'		=> sprintf(__( 'Country rule #%s', 'flat-rate-per-countryregion-for-woocommerce'), $counter),
						'class'		=> 'rules_sep',
						'type'		=> 'rules_sep'
					);
					$this->form_fields['pc_'.$counter.'_c']=array(
						'title'		=> '<span class="rules_items">'.__( 'Country', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'		=> 'multiselect',
						'description'	=> __('Choose one or more countries for this rule.', 'flat-rate-per-countryregion-for-woocommerce'),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> ( WC()->countries ? WC()->countries->countries : array() ),
						'desc_tip'		=> true
					);
					$this->form_fields['pc_'.$counter.'_txt']=array(
						'title'		=> '<span class="rules_items">'.__( 'Rule name', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'text',
						'description'	=> __('The name for this rule, if you choose to show it to the client.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '',
						'desc_tip'		=> true
					);
					$this->form_fields['pc_'.$counter.'_t']= array(
						'title'		=> '<span class="rules_items">'.__('Apply rate', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'select',
						'description'	=> __('Choose either to apply the shipping fee for the whole order or multiply it per each item.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'per_order',
						'options'		=> array(
								'per_order'	=> __('Per order', 'flat-rate-per-countryregion-for-woocommerce'),
								'per_item'		=> __('Per item', 'flat-rate-per-countryregion-for-woocommerce'),
							),
						'desc_tip'		=> true
					);
					$this->form_fields['pc_'.$counter.'_fee']=array(
						'title'		=> '<span class="rules_items">'.__( 'Rate', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
						'type'			=> 'price',
						'description'	=> __('The shipping fee for the countries specified above.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '0',
						'desc_tip'		=> true
					);
					$this->form_fields['pc_'.$counter.'_fr']=array(
						'title'		=> '<span class="rules_items">'.__( 'Free for orders above', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
						'type'			=> 'price',
						'description'	=> __('The shipping fee will be free if the order total reaches this value. Empty or zero for no free shipping.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> '',
						'placeholder'	=> '0',
						'desc_tip'		=> true
					);
					$this->form_fields['pc_'.$counter.'_fr_class']=array(
						'title'		=> '<span class="rules_items">'.__('Free for shipping classes', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? '</span><br/><span class="rules_items">('.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce').')' : '').'</span>',
						'type'		=> 'multiselect',
						'description'	=> __('The shipping fee will be free if at least one item belongs to the selected shipping classes.', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? ' '.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce') : ''),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> $this->shipping_classes,
						'desc_tip'		=> true
					);
					$this->form_fields['pc_'.$counter.'_fr_class_type']=array(
						'title'		=> '<span class="rules_items">'.__('Free for shipping classes if', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'			=> 'select',
						'description'	=> __('Choose either one item on Shipping Class is enough to set the rate as free or all items should belong to the Shipping Classes.', 'flat-rate-per-countryregion-for-woocommerce'),
						'default'		=> 'one',
						'options'		=> array(
							'one'	=> __('At least one item belongs to the class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
							'all'	=> __('All items belong to class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
						),
						'desc_tip'		=> true
					);
					$this->form_fields['pc_'.$counter.'_disable_class']=array(
						'title'		=> '<span class="rules_items">'.__('Disable for shipping classes', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? '</span><br/><span class="rules_items">('.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce').')' : '').'</span>',
						'type'		=> 'multiselect',
						'description'	=> __('The shipping fee will not be available if at least one item belongs to the selected shipping classes. This may be useful for disabling shipping of certain products to certain destinations, if this plugin is the only one being used for shipping cost calculations.', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? ' '.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce') : ''),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> $this->shipping_classes,
						'desc_tip'		=> true
					);
				}
			}

			/* Per State form fields */
			function init_form_fields_per_state() {
				$this->form_fields['ps']=array(
					'title'		 => __('Per State Rates', 'flat-rate-per-countryregion-for-woocommerce'),
					'type'		  => 'title'
				);
				$this->form_fields['ps_count']=array(
					'title'		=> __('Number of State rules', 'flat-rate-per-countryregion-for-woocommerce'),
					'type'			=> 'number',
					'description'	=> __('How many diferent "per state" rates do you want to set?', 'flat-rate-per-countryregion-for-woocommerce').' '.__('Please save the options after changing this value.', 'flat-rate-per-countryregion-for-woocommerce'),
					'default'		=> 0,
					'desc_tip'		=> true
				);
				$count=(isset($this->settings['ps_count']) ? intval($this->settings['ps_count']) : 0);
				for($counter = 1; $count >= $counter; $counter++) {
					$this->form_fields['ps_'.$counter.'_sep']=array(
						'title'		=> sprintf(__( 'State rule #%s', 'flat-rate-per-countryregion-for-woocommerce'), $counter),
						'class'		=> 'rules_sep',
						'type'		=> 'rules_sep'
					);
					$this->form_fields['ps_'.$counter.'_c']=array(
						'title'		=> '<span class="rules_items">'.__( 'Country', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
						'type'		=> 'select',
						'description'	=> __('Choose the country for this rule.', 'flat-rate-per-countryregion-for-woocommerce').' '.__('Please save the options after changing this value.', 'flat-rate-per-countryregion-for-woocommerce'),
						'class'		=> 'chosen_select',
						'css'		=> 'width: 450px;',
						'default'	=> '',
						'options'	=> ( WC()->countries ? WC()->countries->countries : array() ),
						'desc_tip'		=> true
					);
					if (isset($this->settings['ps_'.$counter.'_c']) && !empty($this->settings['ps_'.$counter.'_c'])) {
						$this->form_fields['ps_'.$counter.'_s']=array(
							'title'		=> '<span class="rules_items">'.__( 'State', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
							'type'		=> 'multiselect',
							'description'	=> __('Choose one or more states for this rule.', 'flat-rate-per-countryregion-for-woocommerce'),
							'class'		=> 'chosen_select',
							'css'		=> 'width: 450px;',
							'default'	=> '',
							'options'	=> ( WC()->countries ? WC()->countries->get_states($this->settings['ps_'.$counter.'_c']) : array() ),
							'desc_tip'		=> true
						);
						$this->form_fields['ps_'.$counter.'_txt']=array(
							'title'		=> '<span class="rules_items">'.__( 'Rule name', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
							'type'			=> 'text',
							'description'	=> __('The name for this rule, if you choose to show it to the client.', 'flat-rate-per-countryregion-for-woocommerce'),
							'default'		=> '',
							'placeholder'	=> '',
							'desc_tip'		=> true
						);
						$this->form_fields['ps_'.$counter.'_t']= array(
							'title'		=> '<span class="rules_items">'.__('Apply rate', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
							'type'			=> 'select',
							'description'	=> __('Choose either to apply the shipping fee for the whole order or multiply it per each item.', 'flat-rate-per-countryregion-for-woocommerce'),
							'default'		=> 'per_order',
							'options'		=> array(
									'per_order'	=> __('Per order', 'flat-rate-per-countryregion-for-woocommerce'),
									'per_item'		=> __('Per item', 'flat-rate-per-countryregion-for-woocommerce'),
								),
							'desc_tip'		=> true
						);
						$this->form_fields['ps_'.$counter.'_fee']=array(
							'title'		=> '<span class="rules_items">'.__( 'Rate', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
							'type'			=> 'price',
							'description'	=> __('The shipping fee for the states specified above.', 'flat-rate-per-countryregion-for-woocommerce'),
							'default'		=> '',
							'placeholder'	=> '0',
							'desc_tip'		=> true
						);
						$this->form_fields['ps_'.$counter.'_fr']=array(
							'title'		=> '<span class="rules_items">'.__( 'Free for orders above', 'flat-rate-per-countryregion-for-woocommerce').' ('.get_woocommerce_currency().')</span>',
							'type'			=> 'price',
							'description'	=> __('The shipping fee will be free if the order total reaches this value. Empty or zero for no free shipping.', 'flat-rate-per-countryregion-for-woocommerce'),
							'default'		=> '',
							'placeholder'	=> '0',
							'desc_tip'		=> true
						);
						$this->form_fields['ps_'.$counter.'_fr_class']=array(
							'title'		=> '<span class="rules_items">'.__('Free for shipping classes', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? '</span><br/><span class="rules_items">('.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce').')' : '').'</span>',
							'type'		=> 'multiselect',
							'description'	=> __('The shipping fee will be free if at least one item belongs to the selected shipping classes.', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? ' '.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce') : ''),
							'class'		=> 'chosen_select',
							'css'		=> 'width: 450px;',
							'default'	=> '',
							'options'	=> $this->shipping_classes,
							'desc_tip'		=> true
						);
						$this->form_fields['ps_'.$counter.'_fr_class_type']=array(
							'title'		=> '<span class="rules_items">'.__('Free for shipping classes if', 'flat-rate-per-countryregion-for-woocommerce').'</span>',
							'type'			=> 'select',
							'description'	=> __('Choose either one item on Shipping Class is enough to set the rate as free or all items should belong to the Shipping Classes.', 'flat-rate-per-countryregion-for-woocommerce'),
							'default'		=> 'one',
							'options'		=> array(
								'one'	=> __('At least one item belongs to the class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
								'all'	=> __('All items belong to class(es) set above', 'flat-rate-per-countryregion-for-woocommerce'),
							),
							'desc_tip'		=> true
						);
						$this->form_fields['ps_'.$counter.'_disable_class']=array(
							'title'		=> '<span class="rules_items">'.__('Disable for shipping classes', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? '</span><br/><span class="rules_items">('.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce').')' : '').'</span>',
							'type'		=> 'multiselect',
							'description'	=> __('The shipping fee will not be available if at least one item belongs to the selected shipping classes. This may be useful for disabling shipping of certain products to certain destinations, if this plugin is the only one being used for shipping cost calculations.', 'flat-rate-per-countryregion-for-woocommerce').($this->wpml ? ' '.__('Choose all languages variations', 'flat-rate-per-countryregion-for-woocommerce') : ''),
							'class'		=> 'chosen_select',
							'css'		=> 'width: 450px;',
							'default'	=> '',
							'options'	=> $this->shipping_classes,
							'desc_tip'		=> true
						);
					} else {
						//País ainda não escolhido.
					}
					
				}
			}

			function generate_rules_sep_html($key, $data) {
				$defaults = array(
					'title'	=> '',
					'class'	=> ''
				);
				$data = wp_parse_args($data, $defaults);
				ob_start();
				?>
				<tr valign="top">
					<th colspan="2" class="<?php echo esc_attr( $data['class'] ); ?>"><?php echo wp_kses_post($data['title']); ?></th>
				</tr>
				<?php
				return ob_get_clean();
			}

			function admin_options() {
				do_action($this->id.'_notices');
				?>
				<div id="wc_flatrate_wd">
					<div id="wc_flatrate_wd_rightbar">
						<h4><?php _e('Free technical support (limited)', 'flat-rate-per-countryregion-for-woocommerce'); ?>:</h4>
						<p><a href="https://wordpress.org/support/plugin/flat-rate-per-countryregion-for-woocommerce" target="_blank">WordPress.org</a></p>
						<h4><?php _e('Premium technical support or custom WordPress / WooCommerce development', 'flat-rate-per-countryregion-for-woocommerce'); ?>:</h4>
						<p><a href="https://www.webdados.pt/contactos/" title="<?php echo esc_attr(sprintf(__('Please contact %s', 'flat-rate-per-countryregion-for-woocommerce'), 'Webdados')); ?>" target="_blank"><img src="<?php echo plugins_url('images/webdados.svg', __FILE__); ?>" width="200"/></a></p>
						<h4><?php _e('Help us translate this plugin', 'flat-rate-per-countryregion-for-woocommerce'); ?>:</h4>
						<p><?php printf(__('Download the <a href="%s">.pot file</a> and send us the translation on your language', 'flat-rate-per-countryregion-for-woocommerce'), plugins_url('lang/flat-rate-per-countryregion-for-woocommerce.pot', __FILE__) ); ?></p>		
						<hr/>
						<h4><?php _e('Please rate our plugin at WordPress.org', 'flat-rate-per-countryregion-for-woocommerce'); ?>:</h4>
						<a href="https://wordpress.org/support/view/plugin-reviews/flat-rate-per-countryregion-for-woocommerce?filter=5#postform" target="_blank" style="text-align: center; display: block;">
							<div class="star-rating"><div class="star star-full"></div><div class="star star-full"></div><div class="star star-full"></div><div class="star star-full"></div><div class="star star-full"></div></div>
						</a>
						<div class="clear"></div>
					</div>
					<div id="wc_flatrate_wd_settings">
						<h3><?php echo $this->method_title; ?></h3>
						<p><?php echo $this->method_description; ?></p>
						<p><a href="#" onclick="jQuery('#WC_FRPC_Country_List').show(); return false;"><?php _e('Click here to see list of regions, and the countries included on each one.', 'flat-rate-per-countryregion-for-woocommerce'); ?></a></p>
						<div id="WC_FRPC_Country_List">
							<?php
							foreach($this->regionslist as $key => $region) {
								?>
								<p><b><?php echo $region; ?>:</b><br/>
								<?php
								$countries=array();
								foreach($this->regions[$key]['countries'] as $country) {
									if (isset(WC()->countries->countries[$country]) && trim(WC()->countries->countries[$country])!='') $countries[]=WC()->countries->countries[$country];
								}
								sort($countries, SORT_LOCALE_STRING);
								echo implode(', ', $countries);
								?>
								</p>
								<?php
							}
							?>
							<hr/><p><b><?php _e('NOT ASSIGNED', 'flat-rate-per-countryregion-for-woocommerce'); ?>:</b><br/>
							<?php
							$countries=array();
							foreach(WC()->countries->countries as $code => $country) {
								$done=false;
								foreach($this->regions as $region) {
									if (in_array($code, $region['countries'])) {
										$done=true;
									}
								}
								if (!$done) $countries[]=WC()->countries->countries[$code];
							}
							sort($countries, SORT_LOCALE_STRING);
							echo implode(', ', $countries);
							?>
							</p>
							<p style="text-align: center;">[<a href="#" onclick="jQuery('#WC_FRPC_Country_List').hide(); return false;"><?php _e('Close country list', 'flat-rate-per-countryregion-for-woocommerce'); ?></a>]</p>
						</div>
						<table class="form-table">
						<?php $this->generate_settings_html(); ?>
						</table>
					</div>
				</div>
				<div class="clear"></div>
				<style type="text/css">
					#WC_FRPC_Country_List {
						display: none;
						margin: 10px;
						padding: 20px;
						background-color: #fff;
					}
					#WC_FRPC_Country_List p:first-child {
						margin-top: 0px;
					}
					#WC_FRPC_Country_List p:last-child {
						margin-bottom: 0px;
					}
					#wc_flatrate_wd_rightbar {
						display: none;
					}
					@media (min-width: 961px) {
						#wc_flatrate_wd {
							height: auto;
							overflow: hidden;
						}
						#wc_flatrate_wd_settings {
							width: auto;
							overflow: hidden;
						}
						#wc_flatrate_wd_rightbar {
							display: block;
							float: right;
							width: 200px;
							max-width: 20%;
							margin-left: 20px;
							padding: 15px;
							background-color: #fff;
						}
						#wc_flatrate_wd_rightbar h4:first-child {
							margin-top: 0px;
						}
						#wc_flatrate_wd_rightbar p {
						}
						#wc_flatrate_wd_rightbar p img {
							max-width: 100%;
							height: auto;
						}
					}
					.form-table th {
						width: 250px;
					}
					.woocommerce_page_wc-settings h4.wc-settings-sub-title {
						font-size: 1.4em;
						padding-bottom: 0.5em;
						border-bottom: 1px solid #444;
					}
					.woocommerce_page_wc-settings .rules_sep {
						border-bottom: 1px solid #CCC;
					}
					.woocommerce_page_wc-settings .rules_items {
						padding-left: 2em;
						font-weight: normal;
					}
				</style>
				<?php
			}

			/* Removes the "(Free)" text from the shipping label if the rate is zero */
			public function remove_free_price_text($full_label, $method) {
				return str_replace(' ('.__('Free', 'flat-rate-per-countryregion-for-woocommerce').')', '', $full_label);
			}

			/* Find shipping classes on the ordered items - Stolen from flat-rate shipping */
			public function find_shipping_classes( $package ) {
				$found_shipping_classes = array();
				// Find shipping classes for products in the cart
				if ( sizeof( $package['contents'] ) > 0 ) {
					foreach ( $package['contents'] as $item_id => $values ) {
						if ( $values['data']->needs_shipping() ) {
							$found_class = $values['data']->get_shipping_class();
							if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
								$found_shipping_classes[ $found_class ] = array();
							}
							$found_shipping_classes[ $found_class ][ $item_id ] = $values;
						}
					}
				}
				return $found_shipping_classes;
			}

			/* Force correct WPML label translation */
			function wpml_shipping_method_label($label, $method) {
				if ($method->id==$this->id) {
					$pos = strpos($label, ':');
					$label = isset($GLOBALS['woocommerce_flatrate_percountry_label'])
								?
							$GLOBALS['woocommerce_flatrate_percountry_label']
								:
							(
								$pos > 0
								?
								substr($label, 0, $pos)
								:
								$label
							);
					if ( $method->cost > 0 ) {
						if ( WC()->cart->tax_display_cart == 'excl' ) {
							$label .= ': ' . wc_price( $method->cost );
							if ( $method->get_shipping_tax() > 0 && WC()->cart->prices_include_tax ) {
								$label .= ' <small>' . WC()->countries->ex_tax_or_vat() . '</small>';
							}
						} else {
							$label .= ': ' . wc_price( $method->cost + $method->get_shipping_tax() );
							if ( $method->get_shipping_tax() > 0 && ! WC()->cart->prices_include_tax ) {
								$label .= ' <small>' . WC()->countries->inc_tax_or_vat() . '</small>';
							}
						}
					} elseif ( $method->id !== 'free_shipping' ) {
						$label .= ' (' . __( 'Free', 'flat-rate-per-countryregion-for-woocommerce' ) . ')';
					}
				}
				return $label;
			}

			/* Calculate the rate */
			public function calculate_shipping($package = array()) {
				//Per order by default
				$tax_type='per_order';
				//Order total
				if (WC()->cart->prices_include_tax) {
					$order_total = WC()->cart->cart_contents_total + array_sum( version_compare( WC_VERSION, '3.0', '>=' ) ? WC()->cart->get_cart_contents_taxes() : WC()->cart->taxes );
				} else {
					$order_total = WC()->cart->cart_contents_total;
				}
				//Label
				$label='';
				if(trim($package['destination']['country'])!='') {
					$final_rate=-1;
					//State
					if ($final_rate==-1) {
						$count=intval($this->settings['ps_count']);
						for($i=1; $i<=$count; $i++){
							if (isset($this->settings['ps_'.$i.'_s']) && is_array($this->settings['ps_'.$i.'_s'])) {
								if (trim($package['destination']['country'])==$this->settings['ps_'.$i.'_c']) { //País correcto
									$states=WC()->countries->get_states($this->settings['ps_'.$i.'_c']);
									if (in_array(trim($package['destination']['state']), $this->settings['ps_'.$i.'_s'])) { //State found in this state rule
										if (isset($this->settings['ps_'.$i.'_fee']) && is_numeric($this->settings['ps_'.$i.'_fee'])) { //Rate is set for this rule
											//The rate
											$final_rate=$this->settings['ps_'.$i.'_fee'];
											//Free based on price?
											if (isset($this->settings['ps_'.$i.'_fr']) && ! empty($this->settings['ps_'.$i.'_fr'])) {
												if (intval($this->settings['ps_'.$i.'_fr'])>0) {
													if ($order_total>=intval($this->settings['ps_'.$i.'_fr'])) $final_rate=0; //Free
												}
											}
											//Free based on shipping class?
											if (isset($this->settings['ps_'.$i.'_fr_class']) && is_array($this->settings['ps_'.$i.'_fr_class'])) {
												if (count($this->settings['ps_'.$i.'_fr_class'])>0) {
													switch($this->settings['ps_'.$i.'_fr_class_type']) {
														case 'all':
															$final_rate_free=true;
															foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
																if (trim($shipping_class)!='') {
																	if (!in_array($shipping_class, $this->settings['ps_'.$i.'_fr_class'])) {
																		$final_rate_free=false; //Not free
																		break;
																	}
																} else {
																	$final_rate_free=false; //Not free
																}
															}
															if ($final_rate_free) $final_rate=0; //Free
															break;
														//case 'one':
														default:
															foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
																if (trim($shipping_class)!='') {
																	if (in_array($shipping_class, $this->settings['pc_'.$i.'_fr_class'])) {
																		$final_rate=0; //Free
																		break;
																	}
																}
															}
															break;
													}
												}
											}
											//Per order or per item?
											if (isset($this->settings['ps_'.$i.'_t']) && ! empty($this->settings['ps_'.$i.'_t'])) $tax_type=$this->settings['ps_'.$i.'_t'];
											//The label
											if ($this->settings['show_region_country']=='rule_name') {
												//$label=$this->settings['ps_'.$i.'_txt'];
												$label=(
													$this->wpml
													?
													icl_translate($this->id, $this->id.'_ps_'.$i.'_txt', $this->settings['ps_'.$i.'_txt'])
													:
													$this->settings['ps_'.$i.'_txt']
												);
											} else {
												$label=$states[trim($package['destination']['state'])];
											}
											//Disable based on shipping class?
											if ( isset( $this->settings['ps_'.$i.'_disable_class'] ) && is_array( $this->settings['ps_'.$i.'_disable_class'] ) ) {
												foreach ( $this->find_shipping_classes( $package ) as $shipping_class => $items ) {
													if ( trim( $shipping_class ) != '' ) {
														if ( in_array( $shipping_class, $this->settings['ps_'.$i.'_disable_class'] ) ) {
															$final_rate = -2; //Disable
															break;
														}
													}
												}
											}
											break;
										}
									}
								}
							}
						}
					}
					//Country
					if ($final_rate==-1) {
						$count=intval($this->settings['pc_count']);
						for($i=1; $i<=$count; $i++){
							if (isset($this->settings['pc_'.$i.'_c']) && is_array($this->settings['pc_'.$i.'_c'])) {
								if (in_array(trim($package['destination']['country']), $this->settings['pc_'.$i.'_c'])) { //Country found in this country rule
									if (isset($this->settings['pc_'.$i.'_fee']) && is_numeric($this->settings['pc_'.$i.'_fee'])) { //Rate is set for this rule
										//The rate
										$final_rate=$this->settings['pc_'.$i.'_fee'];
										//Free based on price?
										if (isset($this->settings['pc_'.$i.'_fr']) && ! empty($this->settings['pc_'.$i.'_fr'])) {
											if (intval($this->settings['pc_'.$i.'_fr'])>0) {
												if ($order_total>=intval($this->settings['pc_'.$i.'_fr'])) $final_rate=0; //Free
											}
										}
										//Free based on shipping class?
										if ( isset($this->settings['pc_'.$i.'_fr_class']) && is_array($this->settings['pc_'.$i.'_fr_class']) ) {
											if (count($this->settings['pc_'.$i.'_fr_class'])>0) {
												switch($this->settings['pc_'.$i.'_fr_class_type']) {
													case 'all':
														$final_rate_free=true;
														foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
															if (trim($shipping_class)!='') {
																if (!in_array($shipping_class, $this->settings['pc_'.$i.'_fr_class'])) {
																	$final_rate_free=false; //Not free
																	break;
																}
															} else {
																$final_rate_free=false; //Not free
															}
														}
														if ($final_rate_free) $final_rate=0; //Free
														break;
													//case 'one':
													default:
														foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
															if (trim($shipping_class)!='') {
																if (in_array($shipping_class, $this->settings['pc_'.$i.'_fr_class'])) {
																	$final_rate=0; //Free
																	break;
																}
															}
														}
														break;
												}
											}
										}
										//Per order or per item?
										if (isset($this->settings['pc_'.$i.'_t']) && ! empty($this->settings['pc_'.$i.'_t'])) $tax_type=$this->settings['pc_'.$i.'_t'];
										//The label
										if ($this->settings['show_region_country']=='rule_name') {
											//$label=$this->settings['pc_'.$i.'_txt'];
											$label=(
												$this->wpml
												?
												icl_translate($this->id, $this->id.'_pc_'.$i.'_txt', $this->settings['pc_'.$i.'_txt'])
												:
												$this->settings['pc_'.$i.'_txt']
											);
										} else {
											$label=WC()->countries->countries[trim($package['destination']['country'])];
										}
										//Disable based on shipping class?
										if ( isset( $this->settings['pc_'.$i.'_disable_class'] ) && is_array( $this->settings['pc_'.$i.'_disable_class'] ) ) {
											foreach ( $this->find_shipping_classes( $package ) as $shipping_class => $items ) {
												if ( trim( $shipping_class ) != '' ) {
													if ( in_array( $shipping_class, $this->settings['pc_'.$i.'_disable_class'] ) ) {
														$final_rate = -2; //Disable
														break;
													}
												}
											}
										}
										break;
									}
								}
							}
						}
					}
					//Region
					if ( $final_rate==-1 ) {
						$count=intval($this->settings['pr_count']);
						for($i=1; $i<=$count; $i++){
							if (isset($this->settings['pr_'.$i.'_region']) && is_array($this->settings['pr_'.$i.'_region'])) {
								foreach($this->settings['pr_'.$i.'_region'] as $region) {
									if (in_array(trim($package['destination']['country']), $this->regions[trim($region)]['countries'])) { //Country found in this region rule
										if (isset($this->settings['pr_'.$i.'_fee']) && is_numeric($this->settings['pr_'.$i.'_fee'])) { //Rate is set for this rule
											//The rate
											$final_rate=$this->settings['pr_'.$i.'_fee'];
											//Free based on price?
											if (isset($this->settings['pr_'.$i.'_fr']) && ! empty($this->settings['pr_'.$i.'_fr'])) {
												if (intval($this->settings['pr_'.$i.'_fr'])>0) {
													if ($order_total>=intval($this->settings['pr_'.$i.'_fr'])) $final_rate=0; //Free
												}
											}
											//Free based on shipping class?
											if (isset($this->settings['pr_'.$i.'_fr_class']) && is_array($this->settings['pr_'.$i.'_fr_class'])) {
												if (count($this->settings['pr_'.$i.'_fr_class'])>0) {
													switch($this->settings['pr_'.$i.'_fr_class_type']) {
														case 'all':
															$final_rate_free=true;
															foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
																if (trim($shipping_class)!='') {
																	if (!in_array($shipping_class, $this->settings['pr_'.$i.'_fr_class'])) {
																		$final_rate_free=false; //Not free
																		break;
																	}
																} else {
																	$final_rate_free=false; //Not free
																}
															}
															if ($final_rate_free) $final_rate=0; //Free
															break;
														//case 'one':
														default:
															foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
																if (trim($shipping_class)!='') {
																	if (in_array($shipping_class, $this->settings['pr_'.$i.'_fr_class'])) {
																		$final_rate=0; //Free
																		break;
																	}
																}
															}
															break;
													}
												}
											}
											//Per order or per item?
											if (isset($this->settings['pr_'.$i.'_t']) && ! empty($this->settings['pr_'.$i.'_t'])) $tax_type=$this->settings['pr_'.$i.'_t'];
											//The label
											if ($this->settings['show_region_country']=='rule_name') {
												//$label=$this->settings['pr_'.$i.'_txt'];
												$label=(
													$this->wpml
													?
													icl_translate($this->id, $this->id.'_pr_'.$i.'_txt', $this->settings['pr_'.$i.'_txt'])
													:
													$this->settings['pr_'.$i.'_txt']
												);
											} else {
												$label=$this->regions[trim($region)]['name'];
											}
											//Disable based on shipping class?
											if ( isset( $this->settings['pr_'.$i.'_disable_class'] ) && is_array( $this->settings['pr_'.$i.'_disable_class'] ) ) {
												foreach ( $this->find_shipping_classes( $package ) as $shipping_class => $items ) {
													if ( trim( $shipping_class ) != '' ) {
														if ( in_array( $shipping_class, $this->settings['pr_'.$i.'_disable_class'] ) ) {
															$final_rate = -2; //Disable
															break;
														}
													}
												}
											}
											break;
										}
									}
								}
								if ($final_rate!=-1) break; //Region rate found, break for
							}
						}
					}
					//Rest of the World
					if ( $final_rate==-1 ) {
						if (isset($this->settings['world_disable']) && $this->settings['world_disable']=='yes') return; //Exit with no fee set
						if (isset($this->settings['fee_world']) && is_numeric($this->settings['fee_world'])) {
							//The rate
							$final_rate=$this->settings['fee_world'];
							//Free based on price?
							if (isset($this->settings['world_free_above']) && ! empty($this->settings['world_free_above'])) {
								if (intval($this->settings['world_free_above'])>0) {
									if ($order_total>=intval($this->settings['world_free_above'])) $final_rate=0; //Free
								}
							}
							//Free based on shipping class?
							if (isset($this->settings['world_free_class']) && is_array($this->settings['world_free_class'])) {
								if (count($this->settings['world_free_class'])>0) {
									switch($this->settings['world_fr_class_type']) {
										case 'all':
											$final_rate_free=true;
											foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
												if (trim($shipping_class)!='') {
													if (!in_array($shipping_class, $this->settings['world_free_class'])) {
														$final_rate_free=false; //Not free
														break;
													}
												} else {
													$final_rate_free=false; //Not free
												}
											}
											if ($final_rate_free) $final_rate=0; //Free
											break;
										//case 'one':
										default:
											foreach ($this->find_shipping_classes($package) as $shipping_class => $items) {
												if (trim($shipping_class)!='') {
													if (in_array($shipping_class, $this->settings['world_free_class'])) {
														$final_rate=0; //Free
														break;
													}
												}
											}
											break;
									}
								}
							}
							//Per order or per item?
							if (isset($this->settings['tax_type']) && ! empty($this->settings['tax_type'])) $tax_type=$this->settings['tax_type'];
							//The label
							if ($this->settings['show_region_country']=='rule_name') {
								//$label=$this->settings['world_rulename'];
								$label=(
									$this->wpml
									?
									icl_translate($this->id, $this->id.'_world_rulename', $this->settings['world_rulename'])
									:
									$this->settings['world_rulename']
								);
							} else {
								$label=__('Rest of the World', 'flat-rate-per-countryregion-for-woocommerce');
							}
						}
					}
					//Let's customize the label
					if (isset($this->settings['show_region_country']) && ! empty($this->settings['show_region_country'])) {
						switch($this->settings['show_region_country']) {
							case 'region':
							case 'rule_name':
								//The default or already set
								break;
							case 'country':
								$label=WC()->countries->countries[trim($package['destination']['country'])];
								break;
							case 'title':
								//$label=$this->title;
								$label=(
									$this->wpml
									?
									icl_translate($this->id, $this->id.'_title', $this->title)
									:
									$this->title
								);
								break;
							case 'title_region':
								//$label=$this->title.' - '.$label;
								$label=(
									$this->wpml
									?
									icl_translate($this->id, $this->id.'_title', $this->title)
									:
									$this->title
								).' - '.$label;
								break;
							case 'title_country':
								//$label=$this->title.' - '.WC()->countries->countries[trim($package['destination']['country'])];
								$label=(
									$this->wpml
									?
									icl_translate($this->id, $this->id.'_title', $this->title)
									:
									$this->title
								).' - '.WC()->countries->countries[trim($package['destination']['country'])];
								break;
							default:
								//The default - already set
								break;
						}
					}
					//Still no rate found. Well... That means it's free right?
					if ( $final_rate==-1 ) {
						$final_rate=0;
						$label=__('Flat rate not set', 'flat-rate-per-countryregion-for-woocommerce');
					}
				} else {
					$final_rate = 0; //No country? Is the client from outer world?
				}
				if ( $final_rate!=-2 ) {
					$label=(trim($label)!='' ? $label : $this->title);
					if ($this->wpml) $GLOBALS['woocommerce_flatrate_percountry_label'] = $label; //This is so dirty...
					$rate = array(
						'id'	   => $this->id,
						'label'	=> $label,
						'cost'	 => floatval($final_rate),
						'calc_tax' => 'per_order'
					);
					switch($tax_type) {
						case 'per_order':
							//The default - already set
							break;
						case 'per_item':
							$final_rate_items=0;
							foreach ($package['contents'] as $item_id => $values) {
								$_product=$values['data'];
								if ($values['quantity']>0 && $_product->needs_shipping()) {
									$temp_qty=floatval($values['quantity']);
									//WooCommerce Product Bundles integration (https://wordpress.org/support/topic/for-use-with-woocommerce-product-bundles)
									if (get_class($_product) == 'WC_Product_Bundle') {
										if ($_product->per_product_shipping_active) { //Shipping per product?
											$temp_qty_bundle=0;
											$temp_bundles = $_product->get_bundled_items();
											if (is_array($temp_bundles) && count($temp_bundles) > 0) {
												foreach ($temp_bundles as $temp_bundle_product) {
													$temp_qty_bundle += $temp_qty * (float)$temp_bundle_product->get_quantity();
												}
											}
											$temp_qty=$temp_qty_bundle;
										}
									}
									$final_rate_items+=floatval($final_rate)*$temp_qty;
								}
							}
							$rate['cost']=$final_rate_items;
							//$rate['calc_tax']='per_item'; //Not really needed, is it?
							break;
						default:
							//The default - already set
							break;
					}
					// Register the rate
					$this->add_rate($rate);
				} else {
					//Removed by shipping class restriction
				}
			}

		}
		}

	}
	add_action( 'woocommerce_shipping_init', 'woocommerce_flatrate_percountry_init' );

	/* Add to WooCommerce */
	function woocommerce_flatrate_percountry_add( $methods ) {
		$methods[] = 'WC_Flat_Rate_Per_Country_Region'; 
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'woocommerce_flatrate_percountry_add' );

	if ( ( ! defined( 'WEBDADOS_INVOICEXPRESS_NAG' ) ) && empty( get_transient( 'webdados_invoicexpress_nag' ) ) ) {
		define( 'WEBDADOS_INVOICEXPRESS_NAG', true );
		require_once( 'webdados_invoicexpress_nag.php' );
	}

	/* If you're reading this you must know what you're doing ;-) Greetings from sunny Portugal! */
	
}