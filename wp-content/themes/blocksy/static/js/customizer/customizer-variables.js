import { handleVariablesFor } from 'customizer-sync-helpers/dist/simplified'

export const listenToVariables = () => {
	handleVariablesFor({
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

		darkColorPalette: [
			{
				variable: 'darkPaletteColor1',
				type: 'color:color1',
			},

			{
				variable: 'darkPaletteColor2',
				type: 'color:color2',
			},

			{
				variable: 'darkPaletteColor3',
				type: 'color:color3',
			},

			{
				variable: 'darkPaletteColor4',
				type: 'color:color4',
			},

			{
				variable: 'darkPaletteColor5',
				type: 'color:color5',
			},

			{
				variable: 'darkPaletteColor6',
				type: 'color:color6',
			},

			{
				variable: 'darkPaletteColor7',
				type: 'color:color7',
			},
		],

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

		formTextColor: [
			{
				selector: ':root',
				variable: 'form-text-initial-color',
				type: 'color:default',
			},

			{
				selector: ':root',
				variable: 'form-text-focus-color',
				type: 'color:focus',
			},
		],

		formBorderColor: [
			{
				selector: ':root',
				variable: 'form-field-border-initial-color',
				type: 'color:default',
			},

			{
				selector: ':root',
				variable: 'form-field-border-focus-color',
				type: 'color:focus',
			},
		],

		formBackgroundColor: [
			{
				selector: ':root',
				variable: 'form-field-initial-background',
				type: 'color:default',
			},

			{
				selector: ':root',
				variable: 'form-field-focus-background',
				type: 'color:focus',
			},
		],

		border_color: {
			selector: ':root',
			variable: 'border-color',
			type: 'color',
		},

		headingColor: {
			selector: ':root',
			variable: 'headings-color',
			type: 'color',
		},

		heading_1_color: {
			selector: ':root',
			variable: 'heading-1-color',
			type: 'color',
		},

		heading_2_color: {
			selector: ':root',
			variable: 'heading-2-color',
			type: 'color',
		},

		heading_3_color: {
			selector: ':root',
			variable: 'heading-3-color',
			type: 'color',
		},

		heading_4_color: {
			selector: ':root',
			variable: 'heading-4-color',
			type: 'color',
		},

		heading_5_color: {
			selector: ':root',
			variable: 'heading-5-color',
			type: 'color',
		},

		heading_6_color: {
			selector: ':root',
			variable: 'heading-6-color',
			type: 'color',
		},

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

		global_quantity_color: [
			{
				selector: ':root',
				variable: 'quantity-initial-color',
				type: 'color:default',
			},

			{
				selector: ':root',
				variable: 'quantity-hover-color',
				type: 'color:hover',
			},
		],

		global_quantity_arrows: [
			{
				selector: ':root',
				variable: 'quantity-arrows-initial-color',
				type: 'color:default',
			},

			{
				selector: ':root',
				variable: 'quantity-arrows-hover-color',
				type: 'color:hover',
			},
		],
	})
}
