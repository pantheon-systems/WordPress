import {
	createElement,
	Component,
	Fragment,
	memo,
	useMemo,
} from '@wordpress/element'
import GenericOptionType from './GenericOptionType'
import GenericContainerType from './GenericContainerType'
import { flattenOptions } from './helpers/get-value-from-input'

const OptionsPanel = (props) => {
	let {
		options,
		value,
		onChange, // default | customizer
		purpose = 'default',
		hasRevertButton = true,
		renderOptions = null,
		parentValue,
	} = props

	if (renderOptions) {
		return renderOptions({
			value,
			onChange,
		})
	}

	let SlotFillProvider = null

	if (window.wp.components) {
		SlotFillProvider = window.wp.components.SlotFillProvider
	}

	const renderingChunks = useMemo(() => {
		const localOptions = flattenOptions(options)

		return [
			...(localOptions.__CT_KEYS_ORDER__
				? Object.keys(localOptions.__CT_KEYS_ORDER__)
						.map((orderKey) => parseInt(orderKey, 10))
						.sort((a, b) => a - b)
						.map(
							(orderKey) =>
								localOptions.__CT_KEYS_ORDER__[orderKey]
						)
				: Object.keys(localOptions)),
		]
			.filter((id) => id !== '__CT_KEYS_ORDER__')
			.map((id) => ({
				...localOptions[id],
				id,
			}))
			.reduce((chunksHolder, currentOptionDescriptor, index) => {
				if (chunksHolder.length === 0) {
					return [[currentOptionDescriptor]]
				}

				let lastChunk = chunksHolder[chunksHolder.length - 1]

				if (
					((lastChunk[0].options &&
						lastChunk[0].type === currentOptionDescriptor.type) ||
						currentOptionDescriptor.type === 'ct-tab-group' ||
						currentOptionDescriptor.type === 'ct-tab-group-sync') &&
					/**
					 * Do not group rendering chunks for boxes
					 */
					currentOptionDescriptor.type !== 'box' &&
					/**
					 * Do not group rendering chunks for ct-popup's
					 */
					currentOptionDescriptor.type !== 'ct-popup'
				) {
					return [
						...chunksHolder.slice(0, -1),
						[...lastChunk, currentOptionDescriptor],
					]
				}

				return [...chunksHolder, [currentOptionDescriptor]]
			}, [])
	}, [options])

	let finalResult = renderingChunks.map((renderingChunk) => {
		/**
		 * We are dealing with a container
		 */
		if (
			renderingChunk[0].options ||
			renderingChunk[0].type === 'ct-tab-group-sync'
		) {
			return (
				<GenericContainerType
					key={renderingChunk[0].id}
					value={value}
					parentValue={parentValue}
					renderingChunk={renderingChunk}
					onChange={onChange}
					purpose={purpose}
					hasRevertButton={hasRevertButton}
				/>
			)
		}

		/**
		 * We have a regular option type here
		 */
		return (
			<GenericOptionType
				hasRevertButton={hasRevertButton}
				purpose={purpose}
				key={renderingChunk[0].id}
				id={renderingChunk[0].id}
				value={value[renderingChunk[0].id]}
				values={value}
				option={renderingChunk[0]}
				onChangeFor={(id, newValue) => onChange(id, newValue)}
				onChange={(newValue) =>
					onChange(renderingChunk[0].id, newValue)
				}
			/>
		)
	})

	return window.wp.components ? (
		<SlotFillProvider>{finalResult}</SlotFillProvider>
	) : (
		finalResult
	)
}

export default OptionsPanel
