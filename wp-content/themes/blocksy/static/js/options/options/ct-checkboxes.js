import {
	createElement,
	Component,
	createContext,
	Fragment
} from '@wordpress/element'
import { maybeTransformUnorderedChoices } from '../helpers/parse-choices.js'
import classnames from 'classnames'

import _ from 'underscore'

const Checkboxes = ({
	option,
	value,
	onChange,
	option: { view = 'checkboxes' }
}) => {
	const orderedChoices = maybeTransformUnorderedChoices(option.choices)

	const { inline = false } = option

	if (view === 'checkboxes') {
		return (
			<div
				className="ct-option-checkbox"
				{...(inline ? { ['data-inline']: '' } : {})}
				{...option.attr || {}}>
				{orderedChoices.map(({ key, value: choiceValue }) => (
					<label key={key}>
						<input
							type="checkbox"
							checked={
								typeof value[key] === 'boolean'
									? value[key]
									: value[key] === 'true'
							}
							data-id={key}
							onChange={({ target: { checked } }) =>
								onChange({
									...value,
									[key]: value[key]
										? Object.values(value).filter(v => v)
												.length === 1 &&
											!option.allow_empty
											? true
											: false
										: true
								})
							}
						/>

						{choiceValue}
					</label>
				))}
			</div>
		)
	}

	return (
		<ul
			className="ct-option-checkbox ct-buttons-group"
			{...(inline ? { ['data-inline']: '' } : {})}
			{...option.attr || {}}>
			{orderedChoices.map(({ key, value: choiceValue }) => (
				<li
					className={classnames({
						active:
							typeof value[key] === 'boolean'
								? value[key]
								: value[key] === 'true'
					})}
					data-id={key}
					key={key}
					onClick={({ target: { checked } }) =>
						onChange({
							...value,
							[key]: value[key]
								? Object.values(value).filter(v => v).length ===
										1 && !option.allow_empty
									? true
									: false
								: true
						})
					}>
					{choiceValue}
				</li>
			))}
		</ul>
	)
}

export default Checkboxes
