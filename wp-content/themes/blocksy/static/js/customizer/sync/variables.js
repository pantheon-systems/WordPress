import { getHeroVariables } from './hero-section'
import { getPostListingVariables } from './template-parts/content-loop'
import { getTypographyVariablesFor } from './variables/typography'
import { getBackgroundVariablesFor } from './variables/background'
import { getWooVariablesFor } from './variables/woocommerce'
import { getFormsVariablesFor } from './variables/forms'
import { getPaginationVariables } from './pagination'
import { getCommentsVariables } from './comments'

import { getSingleContentVariablesFor } from './single/structure'

import { getSingleElementsVariables } from './variables/single/related-posts'

import { updateVariableInStyleTags } from 'customizer-sync-helpers'
import { makeVariablesWithCondition } from './helpers/variables-with-conditions'

import { isFunction } from './builder'

import ctEvents from 'ct-events'

let variablesCache = null

const getAllVariables = () => {
	if (variablesCache) {
		return variablesCache
	}

	let allVariables = {
		result: {
			colorPalette: [
				{
					variable: 'paletteColor1',
					type: 'color:color1',
				},

				{
					variable: 'paletteColor2',
					type: 'color:color2',
				},

				{
					variable: 'paletteColor3',
					type: 'color:color3',
				},

				{
					variable: 'paletteColor4',
					type: 'color:color4',
				},

				{
					variable: 'paletteColor5',
					type: 'color:color5',
				},

				{
					variable: 'paletteColor6',
					type: 'color:color6',
				},

				{
					variable: 'paletteColor7',
					type: 'color:color7',
				},

				{
					variable: 'paletteColor8',
					type: 'color:color8',
				},
			],

			// darkColorPalette: [
			// 	{
			// 		variable: 'darkPaletteColor1',
			// 		type: 'color:color1',
			// 	},

			// 	{
			// 		variable: 'darkPaletteColor2',
			// 		type: 'color:color2',
			// 	},

			// 	{
			// 		variable: 'darkPaletteColor3',
			// 		type: 'color:color3',
			// 	},

			// 	{
			// 		variable: 'darkPaletteColor4',
			// 		type: 'color:color4',
			// 	},

			// 	{
			// 		variable: 'darkPaletteColor5',
			// 		type: 'color:color5',
			// 	},

			// 	{
			// 		variable: 'darkPaletteColor6',
			// 		type: 'color:color6',
			// 	},

			// 	{
			// 		variable: 'darkPaletteColor7',
			// 		type: 'color:color7',
			// 	},
			// ],

			background_pattern: [
				{
					variable: 'backgroundPattern',
				},
			],

			...getSingleContentVariablesFor(),

			// Page Hero
			...getHeroVariables(),

			...getPostListingVariables(),
			...getPaginationVariables(),

			...getTypographyVariablesFor(),
			...getBackgroundVariablesFor(),
			...getFormsVariablesFor(),
			...getCommentsVariables(),
			...getWooVariablesFor(),

			// Single
			...getSingleElementsVariables(),

			// Colors
			fontColor: {
				selector: ':root',
				variable: 'color',
				type: 'color',
			},

			linkColor: [
				{
					selector: ':root',
					variable: 'linkInitialColor',
					type: 'color:default',
				},

				{
					selector: ':root',
					variable: 'linkHoverColor',
					type: 'color:hover',
				},
			],

			selectionColor: [
				{
					selector: ':root',
					variable: 'selectionTextColor',
					type: 'color:default',
				},

				{
					selector: ':root',
					variable: 'selectionBackgroundColor',
					type: 'color:hover',
				},
			],

			border_color: {
				variable: 'border-color',
				type: 'color',
				selector: ':root',
			},

			// Headings
			headingColor: {
				variable: 'headings-color',
				type: 'color',
				selector: ':root',
			},

			heading_1_color: {
				variable: 'heading-1-color',
				type: 'color',
				selector: ':root',
			},

			heading_2_color: {
				variable: 'heading-2-color',
				type: 'color',
				selector: ':root',
			},

			heading_3_color: {
				variable: 'heading-3-color',
				type: 'color',
				selector: ':root',
			},

			heading_4_color: {
				variable: 'heading-4-color',
				type: 'color',
				selector: ':root',
			},

			heading_5_color: {
				variable: 'heading-5-color',
				type: 'color',
				selector: ':root',
			},

			heading_6_color: {
				variable: 'heading-6-color',
				type: 'color',
				selector: ':root',
			},

			// Content spacing
			contentSpacing: [
				{
					selector: ':root',
					variable: 'content-spacing',
					extractValue: (value) =>
						({
							none: '0',
							compact: '0.8em',
							comfortable: '1.5em',
							spacious: '2em',
						}[value]),
				},

				{
					selector: ':root',
					variable: 'has-content-spacing',
					extractValue: (value) => {
						return value === 'none' ? '0' : '1'
					},
				},
			],

			// Buttons
			buttonMinHeight: {
				selector: ':root',
				variable: 'buttonMinHeight',
				responsive: true,
				unit: 'px',
			},

			buttonHoverEffect: [
				{
					selector: ':root',
					variable: 'buttonShadow',
					extractValue: (value) =>
						value === 'yes' ? 'CT_CSS_SKIP_RULE' : 'none',
				},

				{
					selector: ':root',
					variable: 'buttonTransform',
					extractValue: (value) =>
						value === 'yes' ? 'CT_CSS_SKIP_RULE' : 'none',
				},
			],

			buttonTextColor: [
				{
					selector: ':root',
					variable: 'buttonTextInitialColor',
					type: 'color:default',
				},

				{
					selector: ':root',
					variable: 'buttonTextHoverColor',
					type: 'color:hover',
				},
			],

			buttonColor: [
				{
					selector: ':root',
					variable: 'buttonInitialColor',
					type: 'color:default',
				},

				{
					selector: ':root',
					variable: 'buttonHoverColor',
					type: 'color:hover',
				},
			],

			buttonBorder: [
				{
					selector: ':root',
					variable: 'button-border',
					type: 'border',
				},

				{
					selector: ':root',
					variable: 'button-border-hover-color',
					type: 'color:default',
					extractValue: ({ style, secondColor }) => ({
						default: {
							...secondColor,
							...(style === 'none'
								? {
										color: 'CT_CSS_SKIP_RULE',
								  }
								: {}),
						},
					}),
				},
			],

			buttonRadius: {
				selector: ':root',
				type: 'spacing',
				variable: 'buttonBorderRadius',
				responsive: true,
			},

			buttonPadding: {
				selector: ':root',
				type: 'spacing',
				variable: 'button-padding',
				responsive: true,
			},

			siteBackground: {
				variable: 'siteBackground',
				type: 'color',
			},

			// Layout
			maxSiteWidth: {
				selector: ':root',
				variable: 'normal-container-max-width',
				unit: 'px',
			},

			contentAreaSpacing: {
				selector: ':root',
				variable: 'content-vertical-spacing',
				responsive: true,
				unit: '',
			},

			narrowContainerWidth: {
				selector: ':root',
				variable: 'narrow-container-max-width',
				unit: 'px',
			},

			wideOffset: {
				selector: ':root',
				variable: 'wide-offset',
				unit: 'px',
			},

			// Sidebar
			sidebarWidth: [
				{
					selector: '[data-sidebar]',
					variable: 'sidebar-width',
					unit: '%',
				},
				{
					selector: '[data-sidebar]',
					variable: 'sidebar-width-no-unit',
					unit: '',
				},
			],

			sidebarGap: {
				selector: '[data-sidebar]',
				variable: 'sidebar-gap',
				unit: '',
			},

			sidebarOffset: {
				selector: '[data-sidebar]',
				variable: 'sidebar-offset',
				unit: 'px',
			},

			sidebarWidgetsTitleColor: {
				selector: '.ct-sidebar .widget-title',
				variable: 'heading-color',
				type: 'color',
				responsive: true,
			},

			mobile_sidebar_position: [
				{
					selector: ':root',
					variable: 'sidebar-order',
					responsive: true,
					extractValue: (value) => ({
						desktop: 'CT_CSS_SKIP_RULE',
						tablet: value === 'top' ? '-1' : 'CT_CSS_SKIP_RULE',
						mobile: value === 'top' ? '-1' : 'CT_CSS_SKIP_RULE',
					}),
				},
			],

			sidebarWidgetsFontColor: [
				{
					selector: '.ct-sidebar > *',
					variable: 'color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: '.ct-sidebar',
					variable: 'linkInitialColor',
					type: 'color:link_initial',
					responsive: true,
				},

				{
					selector: '.ct-sidebar',
					variable: 'linkHoverColor',
					type: 'color:link_hover',
					responsive: true,
				},
			],

			sidebarBackgroundColor: {
				selector: '[data-sidebar] > aside',
				variable: 'sidebar-background-color',
				type: 'color',
				responsive: true,
			},

			sidebarBorder: {
				selector: 'aside[data-type="type-2"]',
				variable: 'border',
				type: 'border',
				responsive: true,
			},

			sidebarDivider: {
				selector: 'aside[data-type="type-3"]',
				variable: 'border',
				type: 'border',
				responsive: true,
			},

			sidebarWidgetsSpacing: {
				selector: '.ct-sidebar',
				variable: 'sidebar-widgets-spacing',
				responsive: true,
				unit: 'px',
			},

			sidebarInnerSpacing: {
				selector: '[data-sidebar] > aside',
				variable: 'sidebar-inner-spacing',
				responsive: true,
				unit: 'px',
			},

			sidebarRadius: {
				selector: 'aside[data-type="type-2"]',
				type: 'spacing',
				variable: 'borderRadius',
				responsive: true,
			},

			sidebarShadow: {
				selector: 'aside[data-type="type-2"]',
				type: 'box-shadow',
				variable: 'box-shadow',
				responsive: true,
			},

			// To top button
			topButtonSize: {
				selector: '.ct-back-to-top .ct-icon',
				variable: 'icon-size',
				responsive: true,
				unit: 'px',
			},

			topButtonOffset: {
				selector: '.ct-back-to-top',
				variable: 'back-top-bottom-offset',
				responsive: true,
				unit: 'px',
			},

			sideButtonOffset: {
				selector: '.ct-back-to-top',
				variable: 'back-top-side-offset',
				responsive: true,
				unit: 'px',
			},

			topButtonIconColor: [
				{
					selector: '.ct-back-to-top',
					variable: 'icon-color',
					type: 'color:default',
				},

				{
					selector: '.ct-back-to-top',
					variable: 'icon-hover-color',
					type: 'color:hover',
				},
			],

			topButtonShapeBackground: [
				{
					selector: '.ct-back-to-top',
					variable: 'top-button-background-color',
					type: 'color:default',
				},

				{
					selector: '.ct-back-to-top',
					variable: 'top-button-background-hover-color',
					type: 'color:hover',
				},
			],

			topButtonRadius: {
				selector: '.ct-back-to-top',
				type: 'spacing',
				variable: 'border-radius',
				// responsive: true,
			},

			topButtonShadow: {
				selector: '.ct-back-to-top',
				type: 'box-shadow',
				variable: 'box-shadow',
				responsive: true,
			},

			// Passepartout
			...makeVariablesWithCondition('has_passepartout', {
				passepartoutSize: {
					selector: ':root',
					variable: 'frame-size',
					responsive: true,
					unit: 'px',
				},

				passepartoutColor: {
					selector: ':root',
					variable: 'frame-color',
					type: 'color',
				},
			}),

			// Breadcrumbs
			breadcrumbsFontColor: [
				{
					selector: '.ct-breadcrumbs',
					variable: 'color',
					type: 'color:default',
				},

				{
					selector: '.ct-breadcrumbs',
					variable: 'linkInitialColor',
					type: 'color:initial',
				},

				{
					selector: '.ct-breadcrumbs',
					variable: 'linkHoverColor',
					type: 'color:hover',
				},
			],
		},
	}

	ctEvents.trigger(
		'ct:customizer:sync:collect-variable-descriptors',
		allVariables
	)

	variablesCache = allVariables.result

	return variablesCache
}

wp.customize.bind('change', (e) => {
	let allVariables = getAllVariables()

	if (!allVariables[e.id]) {
		return
	}

	updateVariableInStyleTags({
		variableDescriptor: allVariables[e.id],
		value: e(),
	})
})
