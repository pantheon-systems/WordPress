import {
	Fragment,
	createElement,
	createPortal,
	Component,
	useRef,
	useReducer,
	useEffect,
	useMemo,
	useCallback,
	useState,
} from '@wordpress/element'
import classnames from 'classnames'
import TypographyModal from './typography/TypographyModal'
import OutsideClickHandler from './react-outside-click-handler'
import { humanizeVariations, familyForDisplay } from './typography/helpers'
import { maybePromoteScalarValueIntoResponsive } from '../../customizer/components/responsive-controls'

import usePopoverMaker from '../helpers/usePopoverMaker'

import { Transition } from '@react-spring/web'
import bezierEasing from 'bezier-easing'

import { __ } from 'ct-i18n'

const getLeftForEl = (modal, el) => {
	if (!modal) return
	if (!el) return

	let style = getComputedStyle(modal)

	let wrapperLeft = parseFloat(style.left)

	el = el.getBoundingClientRect()

	return {
		'--option-modal-arrow-position': `${
			el.left + el.width / 2 - wrapperLeft - 6
		}px`,
	}
}

const Typography = ({
	option: { label = '', desc = '', attr = {} },
	option,
	value,
	device,
	onChange,
}) => {
	// const [isOpen, setIsOpen] = useState(false)

	// options | fonts | variations | search
	const [currentViewCache, setCurrentViewCache] = useState('_:_')

	const [isConfirmingGdpr, setIsConfirmingGdpr] = useState(false)

	const typographyWrapper = useRef()

	let [currentView, previousView] = useMemo(
		() => currentViewCache.split(':'),
		[currentViewCache]
	)

	const setCurrentView = useCallback(
		(newView) => setCurrentViewCache(`${newView}:${currentView}`),
		[currentView]
	)

	const [{ isOpen, isTransitioning }, setModalState] = useState({
		isOpen: false,
		isTransitioning: false,
	})

	const { styles, popoverProps } = usePopoverMaker({
		ref: typographyWrapper,
		defaultHeight: 430,
		shouldCalculate: isTransitioning || isOpen,
	})

	const setIsOpen = (isOpen) => {
		setModalState((state) => ({
			...state,
			isOpen,
			isTransitioning: true,
		}))
	}

	const stopTransitioning = () =>
		setModalState((state) => ({
			...state,
			isTransitioning: false,
		}))

	const fontFamilyRef = useRef()
	const fontSizeRef = useRef()
	const fontWeightRef = useRef()
	const dotsRef = useRef()

	const confirmationRef = useRef()

	const arrowLeft = useMemo(() => {
		const view = currentView

		const futureRef =
			view === 'options'
				? fontSizeRef.current
				: view === 'fonts'
				? fontFamilyRef.current
				: view === 'variations'
				? fontWeightRef.current
				: fontSizeRef.current

		return (
			popoverProps.ref &&
			popoverProps.ref.current &&
			getLeftForEl(popoverProps.ref.current, futureRef)
		)
	}, [
		isOpen,
		currentView,
		popoverProps.ref,
		popoverProps.ref && popoverProps.ref.current,
		fontFamilyRef && fontFamilyRef.current,
		fontWeightRef && fontWeightRef.current,
		fontSizeRef && fontSizeRef.current,
		dotsRef && dotsRef.current,
	])

	let visualFontSize =
		maybePromoteScalarValueIntoResponsive(value['size'])[device] ===
		'CT_CSS_SKIP_RULE'
			? __('Default Size', 'blocksy')
			: maybePromoteScalarValueIntoResponsive(value['size'])[device]

	let currentFontSizeUnit = maybePromoteScalarValueIntoResponsive(
		value['size']
	)
		[device].toString()
		.replace(/[0-9]/g, '')
		.replace(/\-/g, '')
		.replace(/\./g, '')
		.replace('CT_CSS_SKIP_RULE', '')

	let unitsList = ['px', 'em', 'rem', 'pt', 'vw']

	if (
		maybePromoteScalarValueIntoResponsive(value['size'])[device] !==
			'CT_CSS_SKIP_RULE' &&
		unitsList.indexOf(currentFontSizeUnit) === -1
	) {
		visualFontSize = __('Custom', 'blocksy')
	}

	return (
		<div className={classnames('ct-typography', {})}>
			<OutsideClickHandler
				disabled={!isOpen}
				useCapture={false}
				className="ct-typohraphy-value"
				additionalRefs={[popoverProps.ref, confirmationRef]}
				onOutsideClick={() => {
					if (isConfirmingGdpr) {
						return
					}
					setIsOpen(false)
				}}
				wrapperProps={{
					ref: typographyWrapper,
					onClick: (e) => {
						e.preventDefault()

						if (isOpen) {
							setCurrentView('options')
							return
						}

						setCurrentViewCache('options:_')
						setIsOpen('options')
					},
				}}>
				<div>
					<span
						onClick={(e) => {
							e.stopPropagation()

							if (isOpen) {
								setCurrentView('fonts')
								return
							}

							setCurrentViewCache('fonts:_')
							setIsOpen('fonts')
						}}
						className="ct-font"
						ref={fontFamilyRef}>
						<span>
							{value.family === 'Default'
								? __('Default Family', 'blocksy')
								: familyForDisplay(value.family)}
						</span>
					</span>
					<i>/</i>
					<span
						onClick={(e) => {
							e.stopPropagation()

							if (isOpen) {
								setCurrentView('options')
								return
							}

							setCurrentViewCache('options:_')
							setIsOpen('font_size')
						}}
						ref={fontSizeRef}
						className="ct-size">
						<span>{visualFontSize}</span>
					</span>
					<i>/</i>
					<span
						ref={fontWeightRef}
						onClick={(e) => {
							e.stopPropagation()

							if (isOpen) {
								setCurrentView('variations')
								return
							}

							setCurrentViewCache('variations:_')
							setIsOpen('variations')
						}}
						className="ct-weight">
						<span>{humanizeVariations(value.variation)}</span>
					</span>
				</div>

				<a ref={dotsRef} />
			</OutsideClickHandler>

			{(isTransitioning || isOpen) &&
				createPortal(
					<Transition
						items={isOpen}
						onRest={(isOpen) => {
							stopTransitioning()
						}}
						config={{
							duration: 100,
							easing: bezierEasing(0.25, 0.1, 0.25, 1.0),
						}}
						from={
							isOpen
								? {
										transform: 'scale3d(0.95, 0.95, 1)',
										opacity: 0,
								  }
								: { opacity: 1 }
						}
						enter={
							isOpen
								? {
										transform: 'scale3d(1, 1, 1)',
										opacity: 1,
								  }
								: {
										opacity: 1,
								  }
						}
						leave={
							!isOpen
								? {
										transform: 'scale3d(0.95, 0.95, 1)',
										opacity: 0,
								  }
								: {
										opacity: 1,
								  }
						}>
						{(style, item) => {
							if (!item) {
								return null
							}

							return (
								<TypographyModal
									isConfirmingGdpr={isConfirmingGdpr}
									setIsConfirmingGdpr={setIsConfirmingGdpr}
									confirmationRef={confirmationRef}
									wrapperProps={{
										style: {
											...style,
											...styles,
											...arrowLeft,
										},
										...popoverProps,
									}}
									onChange={onChange}
									value={value}
									option={option}
									initialView={item}
									setInititialView={(initialView) =>
										setIsOpen(initialView)
									}
									currentView={currentView}
									previousView={previousView}
									setCurrentView={setCurrentView}
								/>
							)

							/*
							isOpen &&
							((props) => (
								<TypographyModal
									wrapperProps={{
										style: {
											...props,
											...styles,
											...arrowLeft,
										},
										...popoverProps,
									}}
									onChange={onChange}
									value={value}
									option={option}
									initialView={isOpen}
									setInititialView={(initialView) =>
										setIsOpen(initialView)
									}
									currentView={currentView}
									previousView={previousView}
									setCurrentView={setCurrentView}
								/>
							))
                            */
						}}
					</Transition>,
					document.body
				)}
		</div>
	)
}

export default Typography
