import {
	createElement,
	Component,
	useContext,
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

import { __ } from 'ct-i18n'

import OptionsPanel from '../OptionsPanel'
import { getValueFromInput } from '../helpers/get-value-from-input'
import nanoid from 'nanoid'

const LayerControls = SortableHandle(({ items, onChange, value }) => {
	const { removeForId, addForId, option, toggleOptionsPanel } = useContext(
		LayersContext
	)

	return (
		<div className="ct-layer-controls">
			<button
				type="button"
				className="ct-visibility"
				onClick={(e) => {
					e.stopPropagation()
					onChange(
						items.map((l) =>
							l.__id === value.__id
								? {
										...l,
										enabled: !{
											enabled: true,
											...l,
										}.enabled,
								  }
								: l
						)
					)
				}}>
				<svg width="13px" height="13px" viewBox="0 0 24 24">
					<path d="M12,4C4.1,4,0,12,0,12s3.1,8,12,8c8.1,0,12-8,12-8S20.1,4,12,4z M12,17c-2.9,0-5-2.2-5-5c0-2.8,2.1-5,5-5s5,2.2,5,5C17,14.8,14.9,17,12,17z M12,9c-1.7,0-3,1.4-3,3c0,1.6,1.3,3,3,3s3-1.4,3-3C15,10.4,13.7,9,12,9z" />
				</svg>
			</button>

			<div className="ct-layer-label">
				<span>{window._.template(option['preview-template'])(value)}</span>
			</div>

			<button
				type="button"
				className="ct-clone"
				onClick={() => addForId(value)}>
				<svg width="11px" height="11px" viewBox="0 0 24 24">
					<path d="M23,24H7.7c-0.6,0-1-0.4-1-1V7.7c0-0.6,0.4-1,1-1H23c0.6,0,1,0.4,1,1V23C24,23.6,23.6,24,23,24z M8.7,22H22V8.7 H8.7V22z" />
					<path d="M17.3,16.3c0,0.6-0.4,1-1,1H1c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h15.3c0.6,0,1,0.4,1,1V16.3z" />
				</svg>

				<i className="ct-tooltip-top">{__('Clone Item', 'blocksy')}</i>
			</button>

			<button
				type="button"
				className="ct-remove"
				onClick={() => removeForId(value.__id)}
			/>

			{option['inner-options'] && (
				<button
					type="button"
					className="ct-toggle"
					onMouseDown={(e) => {
						e.stopPropagation()
					}}
					onClick={(e) => {
						e.stopPropagation()
						toggleOptionsPanel(value.__id)
					}}
				/>
			)}
		</div>
	)
})

const valueWithUniqueIds = (value) =>
	value.map((singleItem) => ({
		...singleItem,

		...(singleItem.__id
			? {}
			: {
					__id: nanoid(),
			  }),
	}))

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
		const { value, items, onChange } = this.props

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
						/>

						{isOpen === value.__id &&
							(!isDragging ||
								(isDragging && isDragging !== isOpen)) && (
								<div className="ct-layer-content">
									<OptionsPanel
										hasRevertButton={false}
										parentValue={parentValue}
										onChange={(key, newValue) => {
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
											option['inner-options'],
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
											}
										)}
										options={option['inner-options']}
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

	const localOnChange = (v) => {
		onChange(v)
	}

	const addForId = (val = {}) => {
		localOnChange([
			...(value || []),
			{
				enabled: true,
				...getValueFromInput(option['inner-options'] || {}, {}),
				...val,
				__id: nanoid(),
			},
		])
	}

	const computedValue = valueWithUniqueIds(value)

	return (
		<Provider
			value={{
				...state,
				parentValue: values,
				addForId,
				option,

				removeForId: (idToRemove) =>
					localOnChange(
						valueWithUniqueIds(value).filter(
							({ __id: id }) => id !== idToRemove
						)
					),

				toggleOptionsPanel: (idToAdd) => {
					if (value.length > 0 && !value[0].__id) {
						localOnChange(computedValue)
					}

					setState((state) => ({
						...state,
						isOpen: state.isOpen === idToAdd ? false : idToAdd,
					}))
				},
			}}>
			<SortableList
				useDragHandle
				distance={3}
				lockAxis="y"
				items={computedValue}
				onChange={(v) => {
					localOnChange(v)
				}}
				helperContainer={() =>
					document.querySelector('#customize-theme-controls') ||
					document.body
				}
				onSortEnd={({ oldIndex, newIndex }) => {
					localOnChange(arrayMove(computedValue, oldIndex, newIndex))

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

							localOnChange(computedValue)
						}

						setState((state) => ({
							...state,
							isDragging: computedValue[index].__id,
						}))

						resolve()
					})
				}}
			/>

			<button
				className="button"
				onClick={(e) => {
					e.preventDefault()
					addForId()
				}}>
				{__('Add New Item', 'blocksy')}
			</button>
		</Provider>
	)
}

export default Layers
