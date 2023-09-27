import {
	createElement,
	useContext,
	useEffect,
	createPortal,
} from '@wordpress/element'
import classnames from 'classnames'
import bezierEasing from 'bezier-easing'

import OptionsPanel from '../../options/OptionsPanel'
import Switch from './ct-switch'
import { PanelContext } from '../components/PanelLevel'

import { Transition, animated } from '@react-spring/web'

export const PanelMetaWrapper = ({ id, option, getActualOption, value }) => {
	const {
		panelsState,
		panelsHelpers,
		panelsDispatch,
		containerRef,
	} = useContext(PanelContext)

	const selfPanelId = id

	useEffect(() => {
		if (panelsState.previousPanel) {
			return
		}

		if (!panelsHelpers.isTransitioningFor(id)) {
			return
		}

		if (panelsHelpers.isOpenFor(id)) {
			if (
				!panelsHelpers
					.getWrapperParent()
					.querySelector('.ct-tmp-panel-wrapper')
			) {
				const wrapper = document.createElement('div')
				wrapper.classList.add('ct-tmp-panel-wrapper')
				panelsHelpers.getWrapperParent().appendChild(wrapper)
			}

			if (panelsHelpers.getParentOptionsWrapper()) {
				panelsHelpers
					.getParentOptionsWrapper()
					.classList.add('ct-panel-open')
			}

			const h3 =
				containerRef.current.closest('ul') &&
				containerRef.current
					.closest('ul')
					.querySelector(
						'.customize-section-description-container h3'
					)

			panelsDispatch({
				type: 'PANEL_RECEIVE_TITLE',
				payload: {
					titlePrefix: h3
						? `${h3.querySelector('span').innerText} ▸ ${
								h3.innerText.split('\n')[
									h3.innerText.split('\n').length - 1
								]
						  }`
						: '',
				},
			})
		} else {
			if (
				!containerRef.current.closest('.accordion-section-content') ||
				!containerRef.current
					.closest('.accordion-section-content')
					.classList.contains('ct-panel-open')
			) {
				// return
			}

			if (panelsHelpers.getParentOptionsWrapper()) {
				panelsHelpers
					.getParentOptionsWrapper()
					.classList.remove('ct-panel-open')
			}

			/*
			setTimeout(() =>
				(containerRef.current.querySelector('button')
					? containerRef.current.querySelector('button')
					: containerRef.current
				).focus()
			)
            */
		}
	}, [panelsState.previousPanel, id, panelsHelpers.isOpenFor(id)])

	useEffect(() => {
		return () => {
			;[
				...document.querySelectorAll('.ct-panel-open:not(.open)'),
			].map((el) => el.classList.remove('ct-panel-open'))
		}
	}, [])

	const isEnabled = value === 'yes' || value === true

	return getActualOption({
		open: () => panelsHelpers.open(id),

		wrapperAttr: {
			className: `${
				option.switch
					? isEnabled
						? 'ct-click-allowed'
						: ''
					: 'ct-click-allowed'
			} ct-panel`,
			onClick: ({ target }) => {
				if (option.switch && !isEnabled) {
					return
				}

				if (target.closest('.ct-tmp-panel-wrapper')) {
					return
				}

				panelsHelpers.open(selfPanelId)
			},
		},
	})
}

const PanelContainer = ({ option, id, onChange, getValues, onChangeFor }) => {
	let maybeLabel =
		Object.keys(option).indexOf('label') === -1
			? (id || '')
					.replace(/./, (s) => s.toUpperCase())
					.replace(/\_|\-/g, ' ')
			: option.label

	const {
		panelsState: { titlePrefix, previousPanel },
		panelsState,
		panelsHelpers,
		containerRef,
	} = useContext(PanelContext)

	return containerRef.current &&
		panelsHelpers.getWrapperParent().querySelector('.ct-tmp-panel-wrapper')
		? createPortal(
				<Transition
					items={panelsHelpers.isOpenFor(id)}
					from={{ transform: 'translateX(100%)' }}
					enter={{
						transform: 'translateX(0%)',
					}}
					leave={
						previousPanel === id
							? {
									transform: 'translateX(-100%)',
							  }
							: {
									transform: 'translateX(100%)',
							  }
					}
					config={(item, type) => ({
						// delay: type === 'enter' ? 180 * 10 : 0,
						// duration: 2000,
						duration: 180,
						easing: bezierEasing(0.645, 0.045, 0.355, 1),
					})}
					onRest={(isOpen) => {
						panelsHelpers.stopTransitioning()

						if (isOpen) {
							;[
								...panelsHelpers
									.getWrapperParent()
									.querySelectorAll('.ct-tmp-panel-wrapper'),
							].map((el) => {
								setTimeout(() => {
									if (!el.firstElementChild) {
										return
									}

									el.firstElementChild.removeAttribute(
										'style'
									)
								})
							})
							return
						}
						if (!previousPanel) {
							;[
								...panelsHelpers
									.getWrapperParent()
									.querySelectorAll('.ct-tmp-panel-wrapper'),
							].map((el) => el.parentNode.removeChild(el))
						}
					}}>
					{(props, isOpen) =>
						isOpen && (
							<animated.div
								style={props}
								className={classnames(
									'ct-customizer-panel ct-options-container',
									{
										'ct-panel-second-level':
											panelsState.currentLevel === 2,
									}
								)}>
								<div>
									<div className="customize-panel-actions">
										<button
											onClick={(e) => {
												e.stopPropagation()
												panelsHelpers.close()
											}}
											type="button"
											className="customize-section-back"
										/>

										<h3>
											<span>{titlePrefix}</span>
											{maybeLabel}
										</h3>
									</div>

									<div className="customizer-panel-content">
										<OptionsPanel
											purpose="customizer"
											onChange={(key, val) =>
												onChangeFor(key, val)
											}
											options={option['inner-options']}
											value={getValues()}
										/>
									</div>
								</div>

								{(panelsState.currentLevel === 2 ||
									panelsState.secondLevelTitleLabel) && (
									<div>
										<div className="customize-panel-actions">
											<button
												onClick={(e) => {
													e.stopPropagation()
													panelsHelpers.close()
												}}
												type="button"
												className="customize-section-back"
											/>

											<h3>
												<span>
													{panelsState.titlePrefix +
														' ▸ ' +
														maybeLabel}
												</span>
												{
													panelsState.secondLevelTitleLabel
												}
											</h3>
										</div>

										<div className="customizer-panel-content"></div>
									</div>
								)}
							</animated.div>
						)
					}
				</Transition>,
				panelsHelpers
					.getWrapperParent()
					.querySelector('.ct-tmp-panel-wrapper')
		  )
		: null
}

const Panel = ({
	id,
	getValues,
	values,
	onChangeFor,
	option,
	value,
	view = 'normal',
	onChange,
}) => {
	const {
		panelsState: { isOpen, isTransitioning },
		panelsHelpers,
	} = useContext(PanelContext)

	if (view === 'simple') {
		return panelsHelpers.isTransitioningFor(id) ||
			panelsHelpers.isOpenFor(id) ? (
			<PanelContainer
				id={id}
				getValues={() => (getValues ? getValues() : values)}
				onChangeFor={onChangeFor}
				option={option}
			/>
		) : null
	}

	return (
		<div className="ct-customizer-panel-container">
			<div className={classnames('ct-customizer-panel-option')}>
				{option.switch && (
					<Switch
						value={value}
						onChange={onChange}
						onClick={(e) => e.stopPropagation()}
						option={{
							behavior: option.switchBehavior || 'words',
						}}
					/>
				)}

				<button type="button" />
			</div>

			{(panelsHelpers.isTransitioningFor(id) ||
				panelsHelpers.isOpenFor(id)) && (
				<PanelContainer
					id={id}
					getValues={() => (getValues ? getValues() : values)}
					onChangeFor={onChangeFor}
					option={option}
				/>
			)}
		</div>
	)
}

Panel.renderingConfig = {
	design: 'inline',
}

Panel.MetaWrapper = PanelMetaWrapper

export default Panel
