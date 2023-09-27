import {
	createElement,
	Component,
	createPortal,
	useRef,
	createRef,
} from '@wordpress/element'
import PickerModal, { getNoColorPropFor } from './picker-modal'
import { Transition } from 'react-spring/renderprops'
import bezierEasing from 'bezier-easing'
import classnames from 'classnames'
import { __ } from 'ct-i18n'
import { normalizeCondition, matchValuesWithCondition } from 'match-conditions'

import usePopoverMaker from '../../helpers/usePopoverMaker'

const resolveInherit = (picker, option, values) => {
	if (typeof picker.inherit === 'string') {
		if (picker.inherit.indexOf('self') > -1) {
			const currentValue = values[option.id] || option.value
			const pickerToInheritFrom = picker.inherit.split(':')[1]

			let maybeNextValue = currentValue[pickerToInheritFrom].color

			if (maybeNextValue.indexOf('CT_CSS_SKIP_RULE') > -1) {
				maybeNextValue = option.pickers.find(
					({ id }) => id === pickerToInheritFrom
				).inherit
			}

			return {
				background: maybeNextValue || '',
			}
		}

		return { background: picker.inherit }
	}

	let background = Object.keys(picker.inherit).reduce(
		(maybeResult, currentVar) => {
			if (maybeResult) {
				return maybeResult
			}

			if (
				matchValuesWithCondition(
					normalizeCondition(picker.inherit[currentVar]),
					picker.inherit_source === 'global'
						? Object.keys(picker.inherit[currentVar]).reduce(
								(current, key) => ({
									...current,
									[key]: wp.customize(key)(),
								}),
								{}
						  )
						: values
				)
			) {
				return currentVar
			}

			return maybeResult
		},
		null
	)

	if (!background) {
		return {}
	}

	return {
		background,
	}
}

const SinglePicker = ({
	option,
	value,
	onChange,
	picker,

	onPickingChange,
	stopTransitioning,

	innerRef,

	containerRef,
	modalRef,

	isTransitioning,
	isPicking,
	values,
}) => {
	const el = useRef()

	const { appendToBody = true } = option

	const { refreshPopover, styles, popoverProps } = usePopoverMaker({
		contentRef: modalRef,
		ref: containerRef || {},
		defaultHeight: 379,
		shouldCalculate: !option.inline_modal || appendToBody,
	})

	if (option.inline_modal) {
		return (
			<PickerModal
				containerRef={containerRef}
				option={option}
				onChange={onChange}
				picker={picker}
				value={value}
				inline_modal={!!option.inline_modal}
			/>
		)
	}

	let modal = null

	if (
		isTransitioning === picker.id ||
		(isPicking || '').split(':')[0] === picker.id
	) {
		modal = createPortal(
			<Transition
				items={isPicking}
				onRest={() => stopTransitioning()}
				config={{
					duration: 100,
					easing: bezierEasing(0.25, 0.1, 0.25, 1.0),
				}}
				from={
					(isPicking || '').indexOf(':') === -1
						? {
								transform: 'scale3d(0.95, 0.95, 1)',
								opacity: 0,
						  }
						: { opacity: 1 }
				}
				enter={
					(isPicking || '').indexOf(':') === -1
						? {
								transform: 'scale3d(1, 1, 1)',
								opacity: 1,
						  }
						: {
								opacity: 1,
						  }
				}
				leave={
					(isPicking || '').indexOf(':') === -1
						? {
								transform: 'scale3d(0.95, 0.95, 1)',
								opacity: 0,
						  }
						: {
								opacity: 1,
						  }
				}>
				{(isPicking) =>
					(isPicking || '').split(':')[0] === picker.id &&
					((props) => (
						<PickerModal
							style={{
								...props,
								...(appendToBody ? styles : {}),
							}}
							option={option}
							onChange={onChange}
							picker={picker}
							value={value}
							el={el}
							inheritValue={
								picker.inherit
									? resolveInherit(picker, option, values)
											.background
									: ''
							}
							wrapperProps={
								appendToBody
									? popoverProps
									: {
											ref: modalRef,
									  }
							}
							appendToBody={appendToBody}
						/>
					))
				}
			</Transition>,
			appendToBody
				? document.body
				: el.current.closest('section').parentNode
		)
	}

	return (
		<div
			ref={(instance) => {
				el.current = instance

				if (innerRef) {
					innerRef.current = instance
				}
			}}
			className={classnames('ct-color-picker-single', {})}>
			<span tabIndex="0">
				<span
					tabIndex="0"
					className={classnames({
						[`ct-no-color`]:
							(value || {}).color === getNoColorPropFor(option),

						[`ct-color-inherit`]:
							(value || { color: '' }).color.indexOf('INHERIT') >
							-1,
					})}
					onClick={(e) => {
						if (option.skipModal) {
							return
						}
						e.stopPropagation()

						refreshPopover()

						let futureIsPicking = isPicking
							? isPicking.split(':')[0] === picker.id
								? null
								: `${picker.id}:${isPicking.split(':')[0]}`
							: picker.id

						onPickingChange(futureIsPicking)
					}}
					style={
						((value || {}).color || '').indexOf(
							getNoColorPropFor(option)
						) === -1
							? {
									background: (value || {}).color,
							  }
							: {
									...(picker.inherit &&
									(value || {}).color !==
										getNoColorPropFor(option)
										? resolveInherit(picker, option, values)
										: {}),
							  }
					}>
					<i className="ct-tooltip-top">
						{(value || { color: '' }).color.indexOf('INHERIT') > -1
							? __('Inherited', 'blocksy')
							: picker.title}
					</i>

					{(value || { color: '' }).color.indexOf('INHERIT') > -1 && (
						<svg width="25" height="25" viewBox="0 0 30 30">
							<path d="M15 3c-3 0-5.7 1.1-7.8 2.9-.4.3-.5.9-.2 1.4.3.4 1 .5 1.4.2h.1C10.3 5.9 12.5 5 15 5c5.2 0 9.5 3.9 10 9h-3l4 6 4-6h-3.1C26.4 7.9 21.3 3 15 3zM4 10l-4 6h3.1c.5 6.1 5.6 11 11.9 11 3 0 5.7-1.1 7.8-2.9.4-.3.5-1 .2-1.4-.3-.4-1-.5-1.4-.2h-.1c-1.7 1.5-4 2.4-6.5 2.4-5.2 0-9.5-3.9-10-9h3L4 10z" />
						</svg>
					)}
				</span>
			</span>

			{modal}
		</div>
	)
}

export default SinglePicker
