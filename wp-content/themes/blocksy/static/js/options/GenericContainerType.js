import { createElement, Fragment } from '@wordpress/element'
import Condition from './containers/Condition'
import Tabs from './containers/Tabs'
import Group from './containers/Group'
import LabeledGroup from './containers/LabeledGroup'
import HasMetaCategoryButton from './containers/ct-has-meta-category-button'

const GenericContainerType = ({
	value,
	renderingChunk,
	onChange,
	parentValue,
	purpose,
	hasRevertButton,
}) => {
	let Container = null

	if (renderingChunk[0].type === 'ct-has-meta-category-button') {
		Container = HasMetaCategoryButton
	}

	if (renderingChunk[0].type === 'ct-condition') {
		Container = Condition
	}

	if (renderingChunk[0].type === 'tab') {
		Container = Tabs
	}

	if (renderingChunk[0].type === 'ct-group') {
		Container = Group
	}

	if (renderingChunk[0].type === 'ct-labeled-group') {
		Container = LabeledGroup
	}

	if (Container) {
		return (
			<Container
				purpose={purpose}
				onChange={onChange}
				value={value}
				renderingChunk={renderingChunk}
				hasRevertButton={hasRevertButton}
				parentValue={parentValue}
			/>
		)
	}

	return <div>Unknown container type.</div>
}

export default GenericContainerType
