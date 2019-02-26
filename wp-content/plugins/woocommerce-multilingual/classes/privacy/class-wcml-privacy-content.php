<?php

/**
 * @author OnTheGo Systems
 */
class WCML_Privacy_Content extends WPML_Privacy_Content {

	/**
	 * @return string
	 */
	protected function get_plugin_name() {
		return 'WooCommerce Multilingual';
	}

	/**
	 * @return string|array
	 */
	protected function get_privacy_policy() {
		return array(
			__( 'WooCommerce Multilingual will use cookies to understand the basket info when using languages in domains and to transfer data between the domains.', 'woocommerce-multilingual' ),
			__( 'WooCommerce Multilingual will also use cookies to identify the language and currency of each customer’s order as well as the currency of the reports created by WooCommerce. WooCommerce Multilingual extends these reports by adding the currency’s information.', 'woocommerce-multilingual' ),
		);
	}

}