import {
	createElement,
	createPortal,
	useContext,
	Fragment,
	useState,
	Component,
	useRef,
} from '@wordpress/element'
import { maybeTransformUnorderedChoices } from '../helpers/parse-choices.js'
import classnames from 'classnames'
import { Transition } from 'react-spring/renderprops'
import bezierEasing from 'bezier-easing'
import OutsideClickHandler from './react-outside-click-handler'
import { __ } from 'ct-i18n'

const InlineVisibility = ({ option, value, onChange }) => {
	return (
		<ul
			className="ct-visibility-option ct-devices ct-buttons-group"
			{...(option.attr || {})}>
			{maybeTransformUnorderedChoices(option.choices).map(
				({ key, value: val }) => (
					<li
						className={classnames(
							{
								active: value[key],
							},
							`ct-${key}`
						)}
						onClick={() =>
							onChange({
								...value,
								[key]: value[key]
									? Object.values(value).filter((v) => v)
											.length === 1 && !option.allow_empty
										? true
										: false
									: true,
							})
						}
						key={key}
					/>
				)
			)}
		</ul>
	)
}

const VisibilityModal = ({ option, value, onChange }) => {
	const [{ isPicking, isTransitioning }, setAnimationState] = useState({
		isPicking: null,
		isTransitioning: null,
	})

	const stopTransitioning = () =>
		setAnimationState({
			isPicking,
			isTransitioning: false,
		})

	const el = useRef()

	return (
		<Fragment>
			<OutsideClickHandler
				useCapture={false}
				disabled={!isPicking}
				className="ct-visibility-trigger"
				additionalRefs={[]}
				onOutsideClick={() => {
					if (!isPicking) {
						return
					}

					setAnimationState({
						isTransitioning: true,
						isPicking: null,
					})
				}}
				wrapperProps={{
					ref: el,
					onClick: (e) => {
						e.preventDefault()

						setAnimationState({
							isTransitioning: true,
							isPicking: true,
						})
					},
				}}>
				<span>open visibility</span>
			</OutsideClickHandler>

			{(isTransitioning || isPicking) &&
				createPortal(
					<Transition
						items={isPicking}
						onRest={(isOpen) => {
							stopTransitioning()
						}}
						config={{
							duration: 100,
							easing: bezierEasing(0.25, 0.1, 0.25, 1.0),
						}}
						from={
							isPicking
								? {
										transform: 'scale3d(0.95, 0.95, 1)',
										opacity: 0,
								  }
								: { opacity: 1 }
						}
						enter={
							isPicking
								? {
										transform: 'scale3d(1, 1, 1)',
										opacity: 1,
								  }
								: {
										opacity: 1,
								  }
						}
						leave={
							isPicking
								? {
										transform: 'scale3d(0.95, 0.95, 1)',
										opacity: 0,
								  }
								: {
										opacity: 1,
								  }
						}>
						{(isPicking) =>
							isPicking &&
							((props) => (
								<div
									style={props}
									className="ct-box-shadow-modal"
									onClick={(e) => {
										e.preventDefault()
										e.stopPropagation()
									}}
									onMouseDownCapture={(e) => {
										e.nativeEvent.stopImmediatePropagation()
										e.nativeEvent.stopPropagation()
									}}
									onMouseUpCapture={(e) => {
										e.nativeEvent.stopImmediatePropagation()
										e.nativeEvent.stopPropagation()
									}}>
									<InlineVisibility
										option={option}
										value={value}
										onChange={onChange}
									/>
								</div>
							))
						}
					</Transition>,
					el.current.closest('.ct-labeled-group-item')
						? el.current
								.closest('.ct-labeled-group-item')
								.querySelector('.ct-visibility-modal-wrapper')
						: el.current.closest('.ct-single-palette')
						? el.current
								.closest('.ct-single-palette')
								.querySelector('.ct-visibility-modal-wrapper')
						: el.current.closest('.ct-visibility-modal-wrapper')
						? el.current.closest('.ct-visibility-modal-wrapper')
						: el.current
								.closest('.ct-control')
								.querySelector('.ct-visibility-modal-wrapper')
				)}
		</Fragment>
	)
}

const Visibility = ({
	option,
	option: {
		// inline | modal
		view = 'inline',
	},
	value,
	onChange,
}) => {
	if (view === 'inline') {
		return (
			<InlineVisibility
				option={option}
				value={value}
				onChange={onChange}
			/>
		)
	}

	return <VisibilityModal option={option} value={value} onChange={onChange} />
}

Visibility.hiddenResponsive = true

Visibility.ControlEnd = () => <div className="ct-visibility-modal-wrapper" />

export default Visibility
