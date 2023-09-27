import { createElement, Fragment } from '@wordpress/element'
import OptionsPanel from '../OptionsPanel'

const Group = ({ renderingChunk, value, onChange, purpose, hasRevertButton }) =>
	renderingChunk.map((conditionOption) => {
		const {
			label,
			options,
			id,
			attr = {},
			responsive = false,
			optionId,
		} = conditionOption

		if (
			value[optionId].find(
				({ id, enabled, meta_elements }) =>
					enabled &&
					(id === 'post_meta' || id === 'custom_meta') &&
					(meta_elements || []).find(
						({ id, style }) =>
							(id === 'categories' || id === 'tags') &&
							style === 'pill'
					)
			)
		) {
			return (
				<OptionsPanel
					purpose={purpose}
					onChange={onChange}
					options={options}
					value={value}
					hasRevertButton={hasRevertButton}
				/>
			)
		}

		return null
	})

export default Group
