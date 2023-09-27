import { Fragment, createElement } from '@wordpress/element'
import OptionsPanel from '../OptionsPanel'

const LayersMirror = ({ values, onChangeFor, value, option, onChange }) => {
	const wholeLayers = values[option.layers] || []

	const ourLayers = wholeLayers.filter(
		({ id, enabled }) => enabled && id === option.field
	)

	if (ourLayers.length === 0) {
		return null
	}

	return ourLayers.map((layer, index) => {
		let computedOptions = Object.keys(option['inner-options']).reduce(
			(all, optionId) => ({
				...all,
				[optionId]: {
					...option['inner-options'][optionId],
					label: option['inner-options'][optionId].label.replace(
						'INDEX ',
						ourLayers.length === 1 ? '' : `${index + 1} `
					),
				},
			}),
			{}
		)

		return (
			<OptionsPanel
				key={layer.__id || layer.id}
				onChange={(id, value) => {
					onChangeFor(
						option.layers,
						wholeLayers.map((l) =>
							l.__id !== layer.__id
								? l
								: {
										...l,
										[id]: value,
								  }
						)
					)
				}}
				options={computedOptions}
				value={layer}
			/>
		)
	})
}

LayersMirror.renderingConfig = { design: 'none' }

export default LayersMirror
