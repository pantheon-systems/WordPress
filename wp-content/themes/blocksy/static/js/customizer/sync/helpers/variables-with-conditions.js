import { withKeys } from '../helpers'

export const makeVariablesWithCondition = (
	condition,
	variables,
	predicate = null
) => {
	if (!predicate) {
		predicate = (values) => Object.values(values)[0] === 'yes'
	}

	const allConditions = Array.isArray(condition) ? condition : [condition]

	return withKeys(
		[...allConditions, ...Object.keys(variables)],
		Object.keys(variables).reduce(
			(all, currentKey) => [
				...all,

				...(Array.isArray(variables[currentKey])
					? variables[currentKey]
					: [variables[currentKey]]
				).map((variableDescriptor) => ({
					...variableDescriptor,

					extractValue: (value) => {
						let shouldOutput = true

						if (!variableDescriptor.skipOutputCheck) {
							shouldOutput = predicate(
								allConditions.reduce(
									(values, id) => ({
										...values,
										[id]: wp.customize(id)(),
									}),
									{}
								)
							)
						}

						if (!wp.customize(currentKey)) {
							return 'CT_CSS_SKIP_RULE'
						}

						let val = wp.customize(currentKey)()

						if (!shouldOutput) {
							if (variables[currentKey].type === 'box-shadow') {
								return 'CT_CSS_SKIP_RULE'
							}

							if (variables[currentKey].type === 'spacing') {
								return {
									...val,
									top: '',
									bottom: '',
									left: '',
									right: '',
								}
							}

							if (variables[currentKey].type === 'border') {
								return null
							}

							if (
								variables[currentKey].type &&
								variables[currentKey].type.indexOf('color') > -1
							) {
								let toReturn = Object.keys(val).reduce(
									(all, colorKey) => ({
										...all,
										[colorKey]: {
											...val[colorKey],
											color: 'CT_CSS_SKIP_RULE',
										},
									}),
									{}
								)

								return toReturn
							}

							return 'CT_CSS_SKIP_RULE'
						}

						if (variableDescriptor.extractValue) {
							return variableDescriptor.extractValue(val)
						}

						return val
					},
				})),
			],
			[]
		)
	)
}
