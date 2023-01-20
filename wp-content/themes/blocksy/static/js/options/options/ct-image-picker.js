import { createElement, Component } from '@wordpress/element'
import classnames from 'classnames'
import { normalizeCondition, matchValuesWithCondition } from 'match-conditions'

const ImagePicker = ({
	option: { choices, tabletChoices, mobileChoices },
	option,
	device,
	value,
	values,
	onChange,
}) => {
	const { className, ...attr } = { ...(option.attr || {}) }

	let deviceChoices = option.choices

	if (device === 'tablet' && tabletChoices) {
		deviceChoices = tabletChoices
	}

	if (device === 'mobile' && mobileChoices) {
		deviceChoices = mobileChoices
	}

	let matchingChoices = (Array.isArray(deviceChoices)
		? deviceChoices
		: Object.keys(deviceChoices).map((choice) => ({
				key: choice,
				...deviceChoices[choice],
		  }))
	).filter(({ key }) => {
		if (!option.conditions) {
			return true
		}

		if (!option.conditions[key]) {
			return true
		}

		return matchValuesWithCondition(
			normalizeCondition(option.conditions[key]),
			values
		)
	})

	let normalizedValue = matchingChoices.map(({ key }) => key).includes(value)
		? value
		: option.value

	return (
		<ul
			{...attr}
			className={classnames('ct-image-picker', className)}
			{...(option.title && null ? { 'data-title': '' } : {})}>
			{matchingChoices.map((choice) => (
				<li
					className={classnames({
						active: choice.key === normalizedValue,
					})}
					onClick={() => onChange(choice.key)}
					key={choice.key}>
					{choice.src.indexOf('<svg') === -1 ? (
						<img src={choice.src} />
					) : (
						<span
							dangerouslySetInnerHTML={{
								__html: choice.src,
							}}
						/>
					)}

					{option.title && null && <span>{choice.title}</span>}

					{choice.title && (
						<span className="ct-tooltip-top">{choice.title}</span>
					)}
				</li>
			))}
		</ul>
	)
}

export default ImagePicker
