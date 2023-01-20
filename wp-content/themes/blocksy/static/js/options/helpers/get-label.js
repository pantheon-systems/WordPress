import { normalizeCondition, matchValuesWithCondition } from 'match-conditions'

export const capitalizeFirstLetter = str => {
	str = str == null ? '' : String(str)
	return str.charAt(0).toUpperCase() + str.slice(1)
}

export const getOptionLabelFor = ({ id, option, values, renderingConfig }) => {
	let maybeLabel =
		Object.keys(option).indexOf('label') === -1
			? capitalizeFirstLetter(id).replace(/\_|\-/g, ' ')
			: option.label

	if (maybeLabel !== maybeLabel.toString()) {
		maybeLabel =
			Object.keys(maybeLabel).reduce((approvedLabel, currentLabel) => {
				if (approvedLabel) {
					return approvedLabel
				}

				if (
					matchValuesWithCondition(
						normalizeCondition(maybeLabel[currentLabel]),
						values
					)
				) {
					return currentLabel
				}

				return approvedLabel
			}, null) || Object.keys(maybeLabel)[0]
	}

	/**
	 * Fuck JS
	 */
	if (maybeLabel === '') {
		maybeLabel = true
	}

	if (renderingConfig && !renderingConfig.label) {
		maybeLabel = false
	}

	return maybeLabel
}
