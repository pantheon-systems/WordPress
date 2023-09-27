import {
	createPortal,
	useState,
	useEffect,
	useRef,
	createElement,
	Component,
	Fragment,
} from '@wordpress/element'
import { maybeTransformUnorderedChoices } from '../helpers/parse-choices.js'
import Downshift from 'downshift'
import classnames from 'classnames'
import { __ } from 'ct-i18n'

import usePopoverMaker from '../helpers/usePopoverMaker'

const Select = ({
	value,
	option,
	option: {
		choices,
		tabletChoices,
		mobileChoices,
		placeholder,
		searchPlaceholder,
		defaultToFirstItem = true,
		search = false,
		inputClassName = '',
		selectInputStart,
		appendToBody = false,
	},
	onInputValueChange = () => {},
	renderItemFor = (item) => item.value,
	onChange,
	device = 'desktop',
}) => {
	const inputRef = useRef(null)
	const [tempState, setTempState] = useState(null)

	let deviceChoices = choices

	if (device === 'tablet' && tabletChoices) {
		deviceChoices = tabletChoices
	}

	if (device === 'mobile' && mobileChoices) {
		deviceChoices = mobileChoices
	}

	const orderedChoices = maybeTransformUnorderedChoices(deviceChoices)

	let potentialValue =
		value || !defaultToFirstItem
			? value
			: parseInt(value, 10) === 0
			? value
			: (orderedChoices[0] || {}).key

	const { styles, popoverProps } = usePopoverMaker({
		ref: inputRef,
		defaultHeight: 228,
		shouldCalculate: appendToBody,
	})

	useEffect(() => {
		if (!appendToBody) {
			return
		}

		setTimeout(() => {
			setTempState(Math.round())
		}, 50)
	}, [])

	let maybeSelectedItem = orderedChoices.find(
		({ key }) => key === potentialValue
	)

	if (!maybeSelectedItem) {
		maybeSelectedItem = orderedChoices.find(
			({ key }) => parseInt(key) === parseInt(potentialValue)
		)
	}

	return (
		<Downshift
			key={maybeSelectedItem?.key || 'downshift'}
			onInputValueChange={(value) => {
				onInputValueChange(value)
			}}
			selectedItem={
				maybeSelectedItem || !defaultToFirstItem
					? potentialValue
					: (orderedChoices[0] || {}).key
			}
			onChange={(selection) => {
				onChange(selection)
			}}
			itemToString={(item) => {
				let maybeSelectedItem = orderedChoices.find(
					({ key }) => key === item
				)

				if (!maybeSelectedItem) {
					maybeSelectedItem = orderedChoices.find(
						({ key }) => parseInt(key) === parseInt(item)
					)
				}

				return item && maybeSelectedItem ? maybeSelectedItem.value : ''
			}}>
			{({
				getInputProps,
				getItemProps,
				getLabelProps,
				getMenuProps,
				isOpen,
				inputValue,
				highlightedIndex,
				selectedItem,
				openMenu,
				toggleMenu,
				setState,
			}) => {
				let dropdown = null

				if (isOpen) {
					dropdown = (
						<div
							{...getMenuProps({
								className: classnames('ct-select-dropdown', {
									'ct-fixed': appendToBody,
								}),

								...(appendToBody ? popoverProps : {}),
							})}
							style={appendToBody ? styles : {}}>
							{orderedChoices
								.filter(
									(item) =>
										!inputValue ||
										(orderedChoices.find(
											({ key }) =>
												key.toString() ===
												selectedItem.toString()
										) &&
											orderedChoices.find(
												({ key }) =>
													key.toString() ===
													selectedItem.toString()
											).value === inputValue) ||
										item.value
											.toLowerCase()
											.includes(
												inputValue.toLowerCase()
											) ||
										item.key
											.toString()
											.toLowerCase()
											.includes(
												inputValue
													.toString()
													.toLowerCase()
											)
								)
								.map((item, index) => (
									<Fragment key={index}>
										{item.group &&
											(index === 0 ||
												orderedChoices[index - 1]
													.group !==
													orderedChoices[index]
														.group) && (
												<div
													className="ct-select-dropdown-group"
													key={`${index}-group`}>
													{item.group}
												</div>
											)}
										<div
											{...getItemProps({
												key: item.key,
												index,
												item: item.key,
												className: classnames(
													'ct-select-dropdown-item',
													{
														active:
															highlightedIndex ===
															index,
														selected:
															selectedItem ===
															item.key,
													}
												),
											})}>
											{renderItemFor(item)}
										</div>
									</Fragment>
								))}
						</div>
					)

					if (appendToBody) {
						dropdown = createPortal(dropdown, document.body)
					}
				}

				return (
					<div
						className={classnames(
							'ct-select-input 1',
							inputClassName
						)}>
						{selectInputStart && selectInputStart()}
						<input
							{...getInputProps({
								onKeyDown: (e) => {
									if (
										e.key === 'ArrowDown' &&
										search &&
										!isOpen
									) {
										setState({
											inputValue: '',
										})
									}
								},
								onClick: () => {
									toggleMenu()

									setTimeout(() => {
										let popover

										if (appendToBody) {
											popover = document.querySelector(
												'body > .ct-select-dropdown.ct-fixed .ct-select-dropdown-item.selected'
											)
										} else {
											popover = inputRef.current
												.closest('.ct-select-input')
												.querySelector(
													'.ct-select-dropdown .ct-select-dropdown-item.selected'
												)
										}

										if (popover) {
											let popoverTop = popover.parentNode.getBoundingClientRect()
												.top

											let itemTop = popover.getBoundingClientRect()
												.top

											popover.parentNode.scrollTop =
												itemTop - popoverTop
										}
									})

									setTimeout(() => {
										setTempState(Math.round())
									}, 50)

									if (search) {
										setState({
											inputValue: '',
										})
									}
								},
								ref: inputRef,
							})}
							placeholder={
								search && isOpen
									? searchPlaceholder ||
									  __('Type to search...', 'blocksy')
									: placeholder ||
									  __('Select value...', 'blocksy')
							}
							readOnly={search ? !isOpen : true}
						/>

						{dropdown}
					</div>
				)
			}}
		</Downshift>
	)
}

export default Select
