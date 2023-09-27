import { withKeys, mapValue } from '../helpers'

export const getFormsVariablesFor = () => ({
	forms_type: [
		{
			selector: ':root',
			variable: 'has-classic-forms',
			unit: '',
			extractValue: (value) =>
				mapValue({
					value,
					map: {
						'classic-forms': 'var(--true)',
						'modern-forms': 'var(--false)',
					},
				}),
		},

		{
			selector: ':root',
			variable: 'has-modern-forms',
			unit: '',

			extractValue: (value) =>
				mapValue({
					value,
					map: {
						'classic-forms': 'var(--false)',
						'modern-forms': 'var(--true)',
					},
				}),
		},

		{
			selector: ':root',
			variable: 'form-field-border-width',
			unit: '',
			extractValue: (value) => {
				if (value === 'modern-forms') {
					return `0 0 ${wp.customize('formBorderSize')()}px 0`
				}

				return `${wp.customize('formBorderSize')()}px`
			},
		},
	],

	formBorderSize: [
		{
			selector: ':root',
			variable: 'form-field-border-width',
			unit: '',
			extractValue: (value) => {
				if (wp.customize('forms_type')() === 'modern-forms') {
					return `0 0 ${value}px 0`
				}

				return `${value}px`
			},
		},

		{
			selector: ':root',
			variable: 'form-selection-control-border-width',
			unit: 'px',
		},
	],

	// general
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

	formFontSize: {
		selector: ':root',
		variable: 'form-font-size',
		unit: 'px',
	},

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

	formInputHeight: {
		selector: ':root',
		variable: 'form-field-height',
		unit: 'px',
	},

	formTextAreaHeight: {
		selector: 'form textarea',
		variable: 'form-field-height',
		unit: 'px',
	},

	formFieldBorderRadius: {
		selector: ':root',
		variable: 'form-field-border-radius',
		unit: 'px',
	},

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

	// select dropdown
	formSelectFontColor: [
		{
			selector: ':root',
			variable: 'form-field-select-initial-color',
			type: 'color:default',
		},

		{
			selector: ':root',
			variable: 'form-field-select-active-color',
			type: 'color:active',
		},
	],

	formSelectBackgroundColor: [
		{
			selector: ':root',
			variable: 'form-field-select-initial-background',
			type: 'color:default',
		},

		{
			selector: ':root',
			variable: 'form-field-select-active-background',
			type: 'color:active',
		},
	],

	// radio & checkbox
	radioCheckboxColor: [
		{
			selector: ':root',
			variable: 'form-selection-control-initial-color',
			type: 'color:default',
		},

		{
			selector: ':root',
			variable: 'form-selection-control-accent-color',
			type: 'color:accent',
		},
	],

	checkboxBorderRadius: {
		selector: ':root',
		variable: 'form-checkbox-border-radius',
		unit: 'px',
	},
})
