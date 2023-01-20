import { __ } from 'ct-i18n'

export const fontFamilyToCSSFamily = (family) => {
	if (family === 'System Default') {
		return "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'"
	}

	return family.replace('ct_typekit_', '')
}

const findSourceTypeSettingsFor = (font_family, fonts_list) =>
	Object.values(fonts_list).find(
		(single_font_source) =>
			single_font_source.families
				.map(({ family }) => family)
				.indexOf(font_family) > -1
	)

export const findSourceTypeFor = (font_family, fonts_list) => {
	let source = findSourceTypeSettingsFor(font_family, fonts_list)
	if (!source) return false
	return source.type
}

export const findSelectedFontFamily = (font_family, fonts_list) => {
	let source = findSourceTypeSettingsFor(font_family, fonts_list)

	if (!source) {
		return null
	}

	return source.families.find(({ family }) => family === font_family)
}

export const decideVariationToSelect = (newValue, oldValue) => {
	if (newValue.all_variations.indexOf(oldValue.variation) > -1) {
		return oldValue.variation
	}

	if (newValue.all_variations.indexOf('n4') > -1) {
		return 'n4'
	}

	return newValue.all_variations[0]
}

export const humanizeVariationsShort = (variation) => {
	var all = {
		n1: '100',
		i1: '100',
		n2: '200',
		i2: '200',
		n3: '300',
		i3: '300',
		n4: '400',
		i4: '400',
		n5: '500',
		i5: '500',
		n6: '600',
		i6: '600',
		n7: '700',
		i7: '700',
		n8: '800',
		i8: '800',
		n9: '900',
		i9: '900',
		Default: __('Default', 'blocksy'),
	}

	return all[variation]
}

export const humanizeVariations = (variation) => {
	var all = {
		n1: __('Thin 100', 'blocksy'),
		i1: __('Thin 100 Italic', 'blocksy'),
		n2: __('Extra-Light 200', 'blocksy'),
		i2: __('Extra-Light 200 Italic', 'blocksy'),
		n3: __('Light 300', 'blocksy'),
		i3: __('Light 300 Italic', 'blocksy'),
		n4: __('Regular', 'blocksy'),
		i4: __('Regular 400 Italic', 'blocksy'),
		n5: __('Medium 500', 'blocksy'),
		i5: __('Medium 500 Italic', 'blocksy'),
		n6: __('Semi-Bold 600', 'blocksy'),
		i6: __('Semi-Bold 600 Italic', 'blocksy'),
		n7: __('Bold 700', 'blocksy'),
		i7: __('Bold 700 Italic', 'blocksy'),
		n8: __('Extra-Bold 800', 'blocksy'),
		i8: __('Extra-Bold 800 Italic', 'blocksy'),
		n9: __('Ultra-Bold 900', 'blocksy'),
		i9: __('Ultra-Bold 900 Italic', 'blocksy'),
		Default: __('Default Weight', 'blocksy'),
	}

	return all[variation]
}

export const familyForDisplay = (family) => {
	if (family.indexOf('ct_font') === 0) {
		return family
			.replace('ct_font_', '')
			.replace(/([-_][a-z])/gi, ($1) =>
				$1.toUpperCase().replace('-', '').replace('_', '')
			)
	}

	if (family.indexOf('ct_typekit') === 0) {
		return family
			.replace('ct_typekit_', '')
			.replace(/([-_][a-z])/gi, ($1) =>
				$1.toUpperCase().replace('-', ' ').replace('_', ' ')
			)
	}

	if (family === 'System Default') {
		return __('System Default', 'blocksy')
	}

	if (family === 'Default') {
		return __('Default', 'blocksy')
	}

	return family
}

export const humanizeFontSource = (source) => {
	let titles = {
		system: __('System Font', 'blocksy'),
		'local-google-fonts': __('Local Google Font', 'blocksy'),
		typekit: __('Adobe Font', 'blocksy'),
		file: __('Custom Font', 'blocksy'),
		google: __('Google Font', 'blocksy'),
	}

	return titles[source] || source
}
