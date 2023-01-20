import { handleResponsiveSwitch, withKeys } from '../helpers'
import { typographyOption } from './typography'
import { handleBackgroundOptionFor } from './background'

export const getWooVariablesFor = () => ({
	...handleBackgroundOptionFor({
		id: 'shop_archive_background',
		selector: '[data-prefix="woo_categories"]',
		responsive: true,
	}),

	// Woocommerce archive
	shop_cards_alignment_1: [
		{
			selector: '[data-products="type-1"] .product',
			variable: 'horizontal-alignment',
			responsive: true,
			unit: '',
		},

		{
			selector: '[data-products="type-1"] .product',
			variable: 'text-horizontal-alignment',
			responsive: true,
			unit: '',
			extractValue: (value) => {
				if (!value.desktop) {
					return value
				}

				if (value.desktop === 'flex-start') {
					value.desktop = 'left'
				}

				if (value.desktop === 'flex-end') {
					value.desktop = 'right'
				}

				if (value.tablet === 'flex-start') {
					value.tablet = 'left'
				}

				if (value.tablet === 'flex-end') {
					value.tablet = 'right'
				}

				if (value.mobile === 'flex-start') {
					value.mobile = 'left'
				}

				if (value.mobile === 'flex-end') {
					value.mobile = 'right'
				}

				return value
			},
		},
	],

	shopCardsGap: {
		selector: '[data-products]',
		variable: 'grid-columns-gap',
		responsive: true,
		unit: 'px',
	},

	...withKeys(['woocommerce_catalog_columns', 'blocksy_woo_columns'], {
		selector: '[data-products]',
		variable: 'shop-columns',
		responsive: true,
		unit: '',
		extractValue: () => {
			const value = wp.customize('blocksy_woo_columns')()

			return {
				desktop: `CT_CSS_SKIP_RULE`,
				tablet: `repeat(${value.tablet}, minmax(0, 1fr))`,
				mobile: `repeat(${value.mobile}, minmax(0, 1fr))`,
			}
		},
	}),

	cardProductTitleColor: [
		{
			selector:
				'[data-products] .woocommerce-loop-product__title, [data-products] .woocommerce-loop-category__title',
			variable: 'heading-color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector:
				'[data-products] .woocommerce-loop-product__title, [data-products] .woocommerce-loop-category__title',
			variable: 'linkHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	cardProductExcerptColor: {
		selector: '[data-products] .entry-excerpt',
		variable: 'color',
		type: 'color',
		responsive: true,
	},

	cardProductPriceColor: {
		selector: '[data-products] .price',
		variable: 'color',
		type: 'color',
		responsive: true,
	},

	starRatingColor: [
		{
			selector: ':root',
			variable: 'star-rating-initial-color',
			type: 'color:default',
		},

		{
			selector: ':root',
			variable: 'star-rating-inactive-color',
			type: 'color:inactive',
		},
	],

	global_quantity_color: [
		{
			selector: '.quantity',
			variable: 'quantity-initial-color',
			type: 'color:default',
		},

		{
			selector: '.quantity',
			variable: 'quantity-hover-color',
			type: 'color:hover',
		},
	],

	global_quantity_arrows: [
		{
			selector: '.quantity[data-type="type-1"]',
			variable: 'quantity-arrows-initial-color',
			type: 'color:default',
		},

		{
			selector: '.quantity[data-type="type-2"]',
			variable: 'quantity-arrows-initial-color',
			type: 'color:default_type_2',
		},

		{
			selector: '.quantity',
			variable: 'quantity-arrows-hover-color',
			type: 'color:hover',
		},
	],

	saleBadgeColor: [
		{
			selector: ':root',
			variable: 'badge-text-color',
			type: 'color:text',
		},

		{
			selector: ':root',
			variable: 'badge-background-color',
			type: 'color:background',
		},
	],

	outOfStockBadgeColor: [
		{
			selector: '.out-of-stock-badge',
			variable: 'badge-text-color',
			type: 'color:text',
		},

		{
			selector: '.out-of-stock-badge',
			variable: 'badge-background-color',
			type: 'color:background',
		},
	],

	cardProductCategoriesColor: [
		{
			selector: '[data-products] .entry-meta a',
			variable: 'linkInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '[data-products] .entry-meta a',
			variable: 'linkHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	// quick view button
	quick_view_button_icon_color: [
		{
			selector: '.ct-woo-card-extra .ct-open-quick-view',
			variable: 'icon-color',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '.ct-woo-card-extra .ct-open-quick-view',
			variable: 'icon-hover-color',
			type: 'color:hover',
			responsive: true,
		},
	],

	quick_view_button_background_color: [
		{
			selector: '.ct-woo-card-extra .ct-open-quick-view',
			variable: 'trigger-background',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '.ct-woo-card-extra .ct-open-quick-view',
			variable: 'trigger-hover-background',
			type: 'color:hover',
			responsive: true,
		},
	],

	quick_view_title_color: {
		selector: '.ct-quick-view-card .entry-summary .product_title',
		variable: 'heading-color',
		type: 'color',
	},

	quick_view_price_color: {
		selector: '.ct-quick-view-card .entry-summary .price',
		variable: 'color',
		type: 'color',
	},

	quick_view_description_color: {
		selector:
			'.ct-quick-view-card .woocommerce-product-details__short-description',
		variable: 'color',
		type: 'color',
	},

	quick_view_shadow: {
		selector: '.ct-quick-view-card',
		type: 'box-shadow',
		variable: 'box-shadow',
		responsive: true,
	},

	...handleBackgroundOptionFor({
		id: 'quick_view_background',
		selector: '.ct-quick-view-card > section',
		responsive: true,
	}),

	...handleBackgroundOptionFor({
		id: 'quick_view_backdrop',
		selector: '.quick-view-modal',
		responsive: true,
	}),

	cardProductButton1Text: [
		{
			selector: '[data-products="type-1"]',
			variable: 'buttonTextInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '[data-products="type-1"]',
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	cardProductButton2Text: [
		{
			selector: '[data-products="type-2"]',
			variable: 'buttonTextInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '[data-products="type-2"]',
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	cardProductButtonBackground: [
		{
			selector: '[data-products]',
			variable: 'buttonInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '[data-products]',
			variable: 'buttonHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	cardProductBackground: {
		selector: '[data-products="type-2"]',
		variable: 'backgroundColor',
		type: 'color',
		responsive: true,
	},

	cardProductRadius: {
		selector: '[data-products] .product',
		type: 'spacing',
		variable: 'borderRadius',
		responsive: true,
	},

	cardProductShadow: {
		selector: '[data-products="type-2"]',
		type: 'box-shadow',
		variable: 'box-shadow',
		responsive: true,
	},

	// Woocommerce single
	product_thumbs_spacing: {
		selector: '.product-entry-wrapper',
		variable: 'thumbs-spacing',
		responsive: true,
		unit: '',
	},

	productGalleryWidth: {
		selector: '.product-entry-wrapper',
		variable: 'product-gallery-width',
		unit: '%',
	},

	slider_nav_arrow_color: [
		{
			selector: '.woocommerce-product-gallery',
			variable: 'flexy-nav-arrow-color',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-product-gallery',
			variable: 'flexy-nav-arrow-hover-color',
			type: 'color:hover',
		},
	],

	slider_nav_background_color: [
		{
			selector: '.woocommerce-product-gallery',
			variable: 'flexy-nav-background-color',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-product-gallery',
			variable: 'flexy-nav-background-hover-color',
			type: 'color:hover',
		},
	],

	lightbox_button_icon_color: [
		{
			selector: '.woocommerce-product-gallery__trigger',
			variable: 'lightbox-button-icon-color',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-product-gallery__trigger',
			variable: 'lightbox-button-icon-hover-color',
			type: 'color:hover',
		},
	],

	lightbox_button_background_color: [
		{
			selector: '.woocommerce-product-gallery__trigger',
			variable: 'lightbox-button-background-color',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-product-gallery__trigger',
			variable: 'lightbox-button-hover-background-color',
			type: 'color:hover',
		},
	],

	singleProductTitleColor: {
		selector: '.entry-summary .entry-title',
		variable: 'heading-color',
		type: 'color',
	},

	singleProductPriceColor: {
		selector: '.entry-summary .price',
		variable: 'color',
		type: 'color',
	},

	// Store notice
	wooNoticeContent: {
		selector: '.demo_store',
		variable: 'color',
		type: 'color',
	},

	wooNoticeBackground: {
		selector: '.demo_store',
		variable: 'backgroundColor',
		type: 'color',
	},

	// success message
	success_message_text_color: [
		{
			selector: '.woocommerce-message',
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-message',
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	success_message_background_color: {
		selector: '.woocommerce-message',
		variable: 'background-color',
		type: 'color',
	},

	success_message_button_text_color: [
		{
			selector: '.woocommerce-message',
			variable: 'buttonTextInitialColor',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-message',
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
		},
	],

	success_message_button_background: [
		{
			selector: '.woocommerce-message',
			variable: 'buttonInitialColor',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-message',
			variable: 'buttonHoverColor',
			type: 'color:hover',
		},
	],

	// info message
	info_message_text_color: [
		{
			selector: '.woocommerce-info, .woocommerce-thankyou-order-received',
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-info, .woocommerce-thankyou-order-received',
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	info_message_background_color: {
		selector: '.woocommerce-info, .woocommerce-thankyou-order-received',
		variable: 'background-color',
		type: 'color',
	},

	info_message_button_text_color: [
		{
			selector: '.woocommerce-info',
			variable: 'buttonTextInitialColor',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-info',
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
		},
	],

	info_message_button_background: [
		{
			selector: '.woocommerce-info',
			variable: 'buttonInitialColor',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-info',
			variable: 'buttonHoverColor',
			type: 'color:hover',
		},
	],

	// error message
	error_message_text_color: [
		{
			selector: '.woocommerce-error',
			variable: 'color',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-error',
			variable: 'linkHoverColor',
			type: 'color:hover',
		},
	],

	error_message_background_color: {
		selector: '.woocommerce-error',
		variable: 'background-color',
		type: 'color',
	},

	error_message_button_text_color: [
		{
			selector: '.woocommerce-error',
			variable: 'buttonTextInitialColor',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-error',
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
		},
	],

	error_message_button_background: [
		{
			selector: '.woocommerce-error',
			variable: 'buttonInitialColor',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-error',
			variable: 'buttonHoverColor',
			type: 'color:hover',
		},
	],

	// add to cart actions
	add_to_cart_button_width: {
		selector: '.entry-summary form.cart',
		variable: 'button-width',
		responsive: true,
		unit: '',
	},

	quantity_color: [
		{
			selector: '.entry-summary .quantity',
			variable: 'quantity-initial-color',
			type: 'color:default',
		},

		{
			selector: '.entry-summary .quantity',
			variable: 'quantity-hover-color',
			type: 'color:hover',
		},
	],

	quantity_arrows: [
		{
			selector: '.entry-summary .quantity[data-type="type-1"]',
			variable: 'quantity-arrows-initial-color',
			type: 'color:default',
		},

		{
			selector: '.entry-summary .quantity[data-type="type-2"]',
			variable: 'quantity-arrows-initial-color',
			type: 'color:default_type_2',
		},

		{
			selector: '.entry-summary .quantity',
			variable: 'quantity-arrows-hover-color',
			type: 'color:hover',
		},
	],

	add_to_cart_text: [
		{
			selector: '.entry-summary .single_add_to_cart_button',
			variable: 'buttonTextInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '.entry-summary .single_add_to_cart_button',
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	add_to_cart_background: [
		{
			selector: '.entry-summary .single_add_to_cart_button',
			variable: 'buttonInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '.entry-summary .single_add_to_cart_button',
			variable: 'buttonHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	view_cart_button_text: [
		{
			selector: '.entry-summary .ct-cart-actions .added_to_cart',
			variable: 'buttonTextInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '.entry-summary .ct-cart-actions .added_to_cart',
			variable: 'buttonTextHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	view_cart_button_background: [
		{
			selector: '.entry-summary .ct-cart-actions .added_to_cart',
			variable: 'buttonInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: '.entry-summary .ct-cart-actions .added_to_cart',
			variable: 'buttonHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	// product tabs
	...typographyOption({
		id: 'woo_tabs_font',
		selector: '.woocommerce-tabs .tabs',
	}),

	woo_tabs_font_color: [
		{
			selector: '.woocommerce-tabs .tabs',
			variable: 'linkInitialColor',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-tabs .tabs',
			variable: 'linkHoverColor',
			type: 'color:hover',
		},

		{
			selector: '.woocommerce-tabs .tabs',
			variable: 'linkActiveColor',
			type: 'color:active',
		},
	],

	woo_tabs_border_color: {
		selector: '.woocommerce-tabs[data-type] .tabs',
		variable: 'tab-border-color',
		type: 'color',
	},

	woo_actibe_tab_border: {
		selector: '.woocommerce-tabs[data-type] .tabs',
		variable: 'tab-background',
		type: 'color',
	},

	woo_actibe_tab_background: [
		{
			selector: '.woocommerce-tabs[data-type*="type-2"] .tabs',
			variable: 'tab-background',
			type: 'color:default',
		},

		{
			selector: '.woocommerce-tabs[data-type*="type-2"] .tabs li.active',
			variable: 'tab-border-color',
			type: 'color:border',
		},
	],

	// account page
	account_nav_text_color: [
		{
			selector: '.ct-acount-nav',
			variable: 'account-nav-text-initial-color',
			type: 'color:default',
		},

		{
			selector: '.ct-acount-nav',
			variable: 'account-nav-text-active-color',
			type: 'color:active',
		},
	],

	account_nav_background_color: [
		{
			selector: '.ct-acount-nav',
			variable: 'account-nav-background-initial-color',
			type: 'color:default',
		},

		{
			selector: '.ct-acount-nav',
			variable: 'account-nav-background-active-color',
			type: 'color:active',
		},
	],

	account_nav_divider_color: [
		{
			selector: '.ct-acount-nav',
			variable: 'account-nav-divider-color',
			type: 'color:default',
		},
	],

	account_nav_shadow: {
		selector: '.ct-acount-nav',
		type: 'box-shadow',
		variable: 'box-shadow',
		responsive: true,
	},
})
