<?php

class WCML_Compatibility {

	/**
	 * @var SitePress
	 */
	private $sitepress;
	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;
	/**
	 * @var wpdb
	 */
	private $wpdb;
	/** @var WPML_Element_Translation_Package */
	private $tp;

	/**
	 * WCML_Compatibility constructor.
	 *
	 * @param SitePress $sitepress
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param wpdb $wpdb
	 * @param WPML_Element_Translation_Package $tp
	 */
	function __construct( SitePress $sitepress, woocommerce_wpml $woocommerce_wpml, wpdb $wpdb, WPML_Element_Translation_Package $tp ) {
		$this->sitepress        = $sitepress;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wpdb             = $wpdb;
		$this->tp               = $tp;
		$this->init();

	}

	function init() {
		//hardcoded list of extensions and check which ones the user has and then include the corresponding file from the ‘compatibility’ folder
		global $woocommerce;

		//WooCommerce Tab Manager plugin
		if ( class_exists( 'WC_Tab_Manager' ) ) {
			$this->tab_manager = new WCML_Tab_Manager( $this->sitepress, $woocommerce, $this->woocommerce_wpml, $this->wpdb, $this->tp );
			$this->tab_manager->add_hooks();
		}

		//WooCommerce Table Rate Shipping plugin
		if ( defined( 'TABLE_RATE_SHIPPING_VERSION' ) ) {
			$this->table_rate_shipping = new WCML_Table_Rate_Shipping( $this->sitepress, $this->woocommerce_wpml );
			$this->table_rate_shipping->add_hooks();
		}

		//WooCommerce Subscriptions
		if ( class_exists( 'WC_Subscriptions' ) ) {
			$this->wp_subscriptions = new WCML_WC_Subscriptions( $this->woocommerce_wpml, $this->wpdb );
			$this->wp_subscriptions->add_hooks();
		}

		//WooCommerce Name Your Price
		if ( class_exists( 'WC_Name_Your_Price' ) ) {
			$this->name_your_price = new WCML_WC_Name_Your_Price();
		}

		//Product Bundle
		if ( class_exists( 'WC_Product_Bundle' ) && function_exists( 'WC_PB' ) ) {
			if ( version_compare( WC_PB()->version, '5.0.0', '<' ) ) {
				$this->product_bundles = new WCML_Product_Bundles_Legacy( $this->sitepress, $this->woocommerce_wpml, $this->tp );
				$this->product_bundles->add_hooks();
			} else {
				$product_bundle_items  = new WCML_WC_Product_Bundles_Items();
				$this->product_bundles = new WCML_Product_Bundles( $this->sitepress, $this->woocommerce_wpml, $product_bundle_items, $this->wpdb );
			}
		}

		// WooCommerce Variation Swatches and Photos
		if ( class_exists( 'WC_SwatchesPlugin' ) ) {
			$this->variation_sp = new WCML_Variation_Swatches_and_Photos();
		}

		// Product Add-ons
		if ( class_exists( 'Product_Addon_Display' ) ) {
			$this->product_addons = new WCML_Product_Addons( $this->sitepress, $this->woocommerce_wpml->settings['enable_multi_currency'] );
			$this->product_addons->add_hooks();
		}

		// Product Per Product Shipping
		if ( defined( 'PER_PRODUCT_SHIPPING_VERSION' ) ) {
			new WCML_Per_Product_Shipping();
		}
		//Store Exporter plugin
		if ( defined( 'WOO_CE_PATH' ) ) {
			$this->wc_exporter = new WCML_wcExporter( $this->sitepress, $this->woocommerce_wpml );
			$this->wc_exporter->add_hooks();
		}

		//Gravity Forms
		if ( class_exists( 'GFForms' ) ) {
			$this->gravityforms = new WCML_gravityforms();
		}

		//Sensei WooThemes
		if ( class_exists( 'WooThemes_Sensei' ) ) {
			$this->sensei = new WCML_Sensei(
				$this->sitepress,
				$this->wpdb,
				new WPML_Custom_Columns( $this->sitepress )
			);
			$this->sensei->add_hooks();
		}

		//Extra Product Options
		if ( class_exists( 'TM_Extra_Product_Options' ) ) {
			$this->extra_product_options = new WCML_Extra_Product_Options();
		}

		// Dynamic Pricing
		if ( class_exists( 'WC_Dynamic_Pricing' ) ) {
			$this->dynamic_pricing = new WCML_Dynamic_Pricing( $this->sitepress );
			$this->dynamic_pricing->add_hooks();
		}

		// WooCommerce Bookings
		if ( defined( 'WC_BOOKINGS_VERSION' ) && version_compare( WC_BOOKINGS_VERSION, '1.7.8', '>=' ) ) {
			$this->bookings = new WCML_Bookings( $this->sitepress, $this->woocommerce_wpml, $woocommerce, $this->wpdb, $this->tp );
			$this->bookings->add_hooks();

			// WooCommerce Accommodation Bookings
			if ( defined( 'WC_ACCOMMODATION_BOOKINGS_VERSION' ) ) {
				$this->accomodation_bookings = new WCML_Accommodation_Bookings( $this->woocommerce_wpml );
			}
		}

		// WooCommerce Checkout Field Editor
		if ( function_exists( 'woocommerce_init_checkout_field_editor' ) ) {
			$this->checkout_field_editor = new WCML_Checkout_Field_Editor();
			$this->checkout_field_editor->add_hooks();
		}

		if ( class_exists( 'WC_Bulk_Stock_Management' ) ) {
			$this->wc_bulk_stock_management = new WCML_Bulk_Stock_Management();
		}

		// WooCommerce Advanced Ajax Layered Navigation
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'woocommerce-ajax-layered-nav/ajax_layered_nav-widget.php' ) ) {
			$this->wc_ajax_layered_nav_widget = new WCML_Ajax_Layered_Nav_Widget();
		}

		if ( is_plugin_active( 'woocommerce-ajax-cart/wooajaxcart.php' ) ) {
			$this->wc_ajax_cart = new WCML_WC_Ajax_Cart();
		}

		// woocommerce composite products
		if ( isset( $GLOBALS['woocommerce_composite_products'] ) ) {
			$this->wc_composite_products = new WCML_Composite_Products( $this->sitepress, $this->woocommerce_wpml, $this->tp );
			$this->wc_composite_products->add_hooks();
		}

		// woocommerce checkout addons
		if ( function_exists( 'init_woocommerce_checkout_add_ons' ) ) {
			$this->wc_checkout_addons = new WCML_Checkout_Addons();
		}

		// woocommerce checkout addons
		if ( wp_get_theme() == 'Flatsome' ) {
			$this->flatsome = new WCML_Flatsome();
		}

		if ( class_exists( 'WC_Mix_and_Match' ) ) {
			$this->mix_and_match_products = new WCML_Mix_and_Match_Products();
		}

		if ( defined( 'WPSEO_VERSION' ) ) {
			$this->wpseo = new WCML_WPSEO();
		}

		//Adventure Tours theme
		if ( function_exists( 'adventure_tours_check' ) ) {
			$this->adventure_tours = new WCML_Adventure_tours( $this->woocommerce_wpml, $this->sitepress, $this->tp );
			$this->adventure_tours->add_hooks();
		}

		//Aurum Theme
		if ( wp_get_theme() == 'Aurum' ) {
			new WCML_Aurum();
		}

		// Visual Products Configurator
		if ( class_exists( 'Vpc' ) ) {
			$this->vpc = new WCML_Vpc();
		}

		// WooCommerce Show Single Variations
		if ( defined( 'JCK_WSSV_PATH' ) ) {
			new WCML_JCK_WSSV();
		}

		// WooCommerce Print Invoices
		if ( class_exists( 'WC_PIP' ) ) {
			$this->pip = new WCML_Pip();
		}

		// The Events Calendar
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			new WCML_The_Events_Calendar( $this->sitepress, $this->woocommerce_wpml );
		}

		// Klarna Gateway
		if ( class_exists( 'WC_Gateway_Klarna' ) ) {
			$this->klarna_gateway = new WCML_Klarna_Gateway();
			$this->klarna_gateway->add_hooks();
		}

		// YITH_WCQV
		if ( class_exists( 'YITH_WCQV' ) ) {
			$this->yith_wcqv = new WCML_YITH_WCQV();
			$this->yith_wcqv->add_hooks();
		}

		// Woocommerce Memberships
		if ( class_exists( 'WC_Memberships' ) ) {
			$this->wc_memberships = new WCML_WC_Memberships( $this->sitepress->get_wp_api() );
			$this->wc_memberships->add_hooks();
		}

		// MaxStore-Pro Theme
		if ( function_exists( 'maxstore_pro_setup' ) ) {
			$this->maxstore = new WCML_MaxStore();
			$this->maxstore->add_hooks();
		}

		// MaxStore-Pro Theme
		if ( defined( 'ETHEME_THEME_NAME') && 'Blanco' === ETHEME_THEME_NAME ) {
			$this->etheme_blanco = new WCML_Etheme_Blanco();
			$this->etheme_blanco->add_hooks();
		}

		// WPBakery Page Builder
		if ( defined( 'WPB_VC_VERSION') ) {
			$this->wpb_vc= new WCML_Wpb_Vc();
			$this->wpb_vc->add_hooks();
		}

		// Relevanssi plugin
		if ( function_exists( 'relevanssi_insert_edit' ) ) {
			$this->relevanssi = new WCML_Relevanssi();
			$this->relevanssi->add_hooks();
		}

		// Woo Variations Table
		if ( defined( 'WOO_VARIATIONS_TABLE_VERSION') ) {
			$this->wpb_woo_var_table = new WCML_Woo_Var_Table( $this->sitepress->get_current_language() );
			$this->wpb_woo_var_table->add_hooks();
		}

		// LiteSpeed Cache
		if ( class_exists( 'LiteSpeed_Cache' ) ) {
			$this->litespeed_cache = new WCML_LiteSpeed_Cache();
			$this->litespeed_cache->add_hooks();
		}

	}

}
