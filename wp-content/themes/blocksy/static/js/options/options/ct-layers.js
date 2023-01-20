import {
	createElement,
	Component,
	createContext,
	useState,
	Fragment,
} from '@wordpress/element'
import classnames from 'classnames'
import {
	SortableContainer,
	SortableElement,
	SortableHandle,
} from 'react-sortable-hoc'

import arrayMove from 'array-move'

import OptionsPanel from '../OptionsPanel'
import { getValueFromInput } from '../helpers/get-value-from-input'
import Select from './ct-select'
import nanoid from 'nanoid'

import SelectThatAddsItems from './ct-layers/SelectThatAddsItems'
import LayerControls from './ct-layers/LayerControls'

const valueWithUniqueIds = (value) =>
	value.map((singleItem) => ({
		...singleItem,

		...(singleItem.__id
			? {}
			: {
					__id: nanoid(),
			  }),
	}))

export const itemsThatAreNotAdded = (value, option) =>
	Object.keys(option.settings).filter(
		(optionId) => !value.find(({ id }) => id === optionId)
	)

const getDefaultState = () => ({
	currentlyPickedItem: null,
	isDragging: false,
	isOpen: false,
})

export const LayersContext = createContext(getDefaultState())

const { Provider, Consumer } = LayersContext

class SingleItem extends Component {
	state = {
		isOpen: false,
	}

	render() {
		const { value, items, onChange, index } = this.props
		const itemIndex = items
			.map(({ __id }) => __id)
			.indexOf(value.__id)
			.toString()

		return (
			<Consumer>
				{({ option, isDragging, isOpen, parentValue }) => (
					<li
						className={classnames('ct-layer', option.itemClass, {
							[`ct-disabled`]: !{ enabled: true, ...value }
								.enabled,
						})}>
						<LayerControls
							items={items}
							onChange={onChange}
							value={value}
							itemIndex={itemIndex}
						/>

						{option.settings[value.id] &&
							option.settings[value.id].options &&
							isOpen === value.__id &&
							(!isDragging ||
								(isDragging && isDragging !== isOpen)) && (
								<div className="ct-layer-content">
									<OptionsPanel
										hasRevertButton={false}
										parentValue={parentValue}
										onChange={(key, newValue) => {
											if (
												option.settings[value.id]
													.sync &&
												option.settings[value.id].clone
											) {
												let totalItems = items.filter(
													({ id }) => id === value.id
												).length

												let idForSync = `${
													option.settings[value.id]
														.sync.id
												}_first`

												if (
													totalItems > 1 &&
													items
														.filter(
															({ id }) =>
																id === value.id
														)
														.map(({ __id }) => __id)
														.indexOf(value.__id) > 0
												) {
													idForSync = `${
														option.settings[
															value.id
														].sync.id
													}_second`
												}

												wp.customize &&
													wp.customize.previewer &&
													wp.customize.previewer.send(
														'ct:sync:refresh_partial',
														{
															id: idForSync,
														}
													)
											}

											onChange(
												items.map((l) =>
													l.__id === value.__id
														? {
																...l,
																[key]: newValue,
														  }
														: l
												)
											)
										}}
										value={getValueFromInput(
											option.settings[value.id].options,
											{
												...(option.value.filter(
													({ id }) => id === value.id
												).length > 1
													? option.value.filter(
															({ id }) =>
																value.id === id
													  )[
															items
																.filter(
																	({ id }) =>
																		id ===
																		value.id
																)
																.map(
																	({
																		__id,
																	}) => __id
																)
																.indexOf(
																	value.__id
																)
													  ]
													: {}),
												...value,
												itemIndex,
											}
										)}
										options={
											option.settings[value.id].options
										}
									/>
								</div>
							)}
					</li>
				)}
			</Consumer>
		)
	}
}

const SortableItem = SortableElement(SingleItem)

const SortableList = SortableContainer(({ items, onChange }) => (
	<Consumer>
		{({ option }) => (
			<ul className="ct-layers">
				{items.map((value, index) => (
					<SortableItem
						key={value.__id}
						index={index}
						onChange={onChange}
						value={value}
						items={items}
						disabled={!!option.disableDrag}
					/>
				))}
			</ul>
		)}
	</Consumer>
))

const Layers = ({ value, option, onChange, values }) => {
	const [state, setState] = useState(getDefaultState())

	const addForId = (idToAdd, val = {}) => {
		onChange([
			...(value || []),
			{
				id: idToAdd,
				enabled: true,
				...getValueFromInput(
					option.settings[idToAdd].options || {},
					{}
				),
				...val,
				__id: nanoid(),
			},
		])
	}

	const computedValue = (option.manageable
		? valueWithUniqueIds(value)
		: [
				...valueWithUniqueIds(value),
				...option.value
					.filter(
						({ id }) => value.map(({ id }) => id).indexOf(id) === -1
					)
					.map((item) => ({
						...item,
						__id: nanoid(),
						enabled: false,
					})),
		  ]
	).filter((item) => !!option.settings[item.id])

	return (
		<Provider
			value={{
				...state,
				parentValue: values,
				addCurrentlySelectedItem: () => {
					const idToAdd =
						state.currentlyPickedItem ||
						itemsThatAreNotAdded(
							valueWithUniqueIds(value),
							option
						)[0]

					setState((state) => ({
						...state,
						currentlyPickedItem: null,
					}))
					addForId(idToAdd)
				},
				addForId: (id, value) => addForId(id, value),
				option: option,
				setCurrentItem: (currentlyPickedItem) =>
					setState((state) => ({ ...state, currentlyPickedItem })),
				removeForId: (idToRemove) =>
					onChange(
						valueWithUniqueIds(value).filter(
							({ __id: id }) => id !== idToRemove
						)
					),

				toggleOptionsPanel: (idToAdd) => {
					if (value.length > 0 && !value[0].__id) {
						wp.customize &&
							wp.customize.previewer &&
							wp.customize.previewer.send(
								'ct:sync:refresh_partial',
								{
									shouldSkip: true,
								}
							)

						onChange(computedValue)
					}

					setState((state) => ({
						...state,
						isOpen: state.isOpen === idToAdd ? false : idToAdd,
					}))
				},
			}}>
			{option.manageable && (
				<SelectThatAddsItems
					{...{
						value: computedValue,
						option,
					}}
				/>
			)}

			<SortableList
				useDragHandle
				distance={3}
				lockAxis="y"
				items={computedValue}
				onChange={(v) => onChange(v)}
				helperContainer={() =>
					document.querySelector('#customize-theme-controls') ||
					document.body
				}
				onSortEnd={({ oldIndex, newIndex }) => {
					onChange(arrayMove(computedValue, oldIndex, newIndex))

					setState((state) => ({
						...state,
						isDragging: false,
					}))
				}}
				updateBeforeSortStart={({ index }) => {
					new Promise((resolve) => {
						if (value.length > 0 && !value[0].__id) {
							wp.customize &&
								wp.customize.previewer &&
								wp.customize.previewer.send(
									'ct:sync:refresh_partial',
									{
										shouldSkip: true,
									}
								)
							onChange(computedValue)
						}

						setState((state) => ({
							...state,
							isDragging: computedValue[index].__id,
						}))
						resolve()
					})
				}}
			/>
		</Provider>
	)
}

export default Layers
