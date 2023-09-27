export const maybeTransformUnorderedChoices = choices =>
	Array.isArray(choices)
		? choices
		: Object.keys(choices).reduce(
				(current, choice) => [
					...current,
					{
						key: choice,
						value: choices[choice]
					}
				],
				[]
			)
