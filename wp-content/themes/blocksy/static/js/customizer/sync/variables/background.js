import { maybePromoteScalarValueIntoResponsive } from 'customizer-sync-helpers/dist/promote-into-responsive'

const componentToHex = (c) => {
	var hex = c.toString(16)
	return hex.length == 1 ? '0' + hex : hex
}

const withResponsive = ({ responsive, value, cb }) => {
	value = maybePromoteScalarValueIntoResponsive(value, responsive)

	if (responsive) {
		return {
			desktop: cb(value.desktop),
			tablet: cb(value.tablet),
			mobile: cb(value.mobile),
		}
	}

	return cb(value)
}

export const handleBackgroundOptionFor = ({
	id,

	selector,

	responsive = false,
	valueExtractor = (value) => value,
	addToDescriptors = {},

	conditional_var = false,
	forced_background_image = false,
}) => ({
	[id]: [
		{
			variable: 'background-color',
			variableType: 'property',

			selector,

			responsive,
			extractValue: (value) =>
				withResponsive({
					value: valueExtractor(value),
					responsive,
					cb: (value) => {
						if (conditional_var) {
							return `var(${conditional_var}, ${value.backgroundColor.default.color})`
						}

						if (!value) {
							return 'CT_CSS_SKIP_RULE'
						}

						return value.backgroundColor.default.color
					},
				}),

			...addToDescriptors,
		},

		{
			variable: 'background-image',
			variableType: 'property',

			selector,

			responsive,
			extractValue: (value) =>
				withResponsive({
					value: valueExtractor(value),
					responsive,
					cb: ({
						background_type,
						gradient,
						background_image,
						background_pattern,
						patternColor,
						backgroundColor,
						overlayColor,
					} = {}) => {
						if (background_type === 'color') {
							if (forced_background_image) {
								return 'none'
							}

							return 'CT_CSS_SKIP_RULE'
						}

						const str_replace = ($old, $new, $text) =>
							($text + '').split($old).join($new)

						if (background_type === 'image') {
							if (!background_image.url) {
								return 'CT_CSS_SKIP_RULE'
							}

							return `${
								overlayColor.default.color !==
								'CT_CSS_SKIP_RULE'
									? `linear-gradient(${overlayColor.default.color}, ${overlayColor.default.color}), `
									: ''
							}url(${background_image.url})`
						}

						if (background_type === 'gradient') {
							return gradient
						}

						let opacity = 1
						let color = patternColor
							? patternColor.default.color
							: ''

						if (color.indexOf('paletteColor1') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor1')
						}

						if (color.indexOf('paletteColor2') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor2')
						}

						if (color.indexOf('paletteColor3') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor3')
						}

						if (color.indexOf('paletteColor4') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor4')
						}

						if (color.indexOf('paletteColor5') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor5')
						}

						if (color.indexOf('paletteColor6') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor6')
						}

						if (color.indexOf('paletteColor7') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor7')
						}

						if (color.indexOf('paletteColor8') > -1) {
							color = getComputedStyle(
								document.body
							).getPropertyValue('--paletteColor8')
						}

						if (color.indexOf('rgb') > -1) {
							const rgb_array = str_replace(
								'rgb(',
								'',
								str_replace(
									')',
									'',
									str_replace(
										'rgba(',
										'',
										str_replace(' ', '', color)
									)
								)
							).split(',')

							color = `#${componentToHex(
								parseInt(rgb_array[0], 10)
							)}${componentToHex(
								parseInt(rgb_array[1], 10)
							)}${componentToHex(parseInt(rgb_array[2], 10))}`

							if (rgb_array.length > 3) {
								opacity = rgb_array[3]
							}
						}

						color = str_replace('#', '', color)

						return `url("${str_replace(
							'OPACITY',
							opacity,
							str_replace(
								'COLOR',
								color,
								ct_localizations.customizer_sync.svg_patterns[
									background_pattern
								] ||
									ct_localizations.customizer_sync
										.svg_patterns['type-1']
							)
						)}")`
					},
				}),

			...addToDescriptors,
		},

		{
			variable: 'background-position',
			variableType: 'property',

			selector,
			responsive,
			...addToDescriptors,

			extractValue: (value) =>
				withResponsive({
					value: valueExtractor(value),
					responsive,
					cb: ({ background_type, background_image } = {}) => {
						if (background_type !== 'image') {
							return 'CT_CSS_SKIP_RULE'
						}

						return `${Math.round(
							parseFloat(background_image.x || 0) * 100
						)}% ${Math.round(
							parseFloat(background_image.y || 0) * 100
						)}%`
					},
				}),
		},

		{
			variable: 'background-size',
			variableType: 'property',

			selector,

			responsive,
			...addToDescriptors,

			extractValue: (value) =>
				withResponsive({
					value: valueExtractor(value),
					responsive,
					cb: ({ background_type, background_size } = {}) => {
						if (background_type !== 'image') {
							return 'CT_CSS_SKIP_RULE'
						}

						return background_size
					},
				}),
		},

		{
			variable: 'background-attachment',
			variableType: 'property',

			selector,

			responsive,
			...addToDescriptors,

			extractValue: (value) =>
				withResponsive({
					value: valueExtractor(value),
					responsive,
					cb: ({ background_type, background_attachment } = {}) => {
						if (background_type !== 'image') {
							return 'CT_CSS_SKIP_RULE'
						}

						return background_attachment
					},
				}),
		},

		{
			selector,
			variable: 'background-repeat',
			variableType: 'property',

			responsive,
			...addToDescriptors,
			extractValue: (value) =>
				withResponsive({
					value: valueExtractor(value),
					responsive,
					cb: ({ background_type, background_repeat } = {}) => {
						if (background_type !== 'image') {
							return 'CT_CSS_SKIP_RULE'
						}

						if (background_repeat === 'repeat') {
							return 'CT_CSS_SKIP_RULE'
						}

						return background_repeat
					},
				}),
		},
	],
})

export const getBackgroundVariablesFor = () => ({
	// Site background
	...handleBackgroundOptionFor({
		id: 'site_background',
		selector: 'body',
		responsive: true,
		forced_background_image: true,
	}),
})
