import {
	createElement,
	Component,
	useState,
	useRef,
	Fragment,
} from '@wordpress/element'
import cls from 'classnames'
import { __, sprintf } from 'ct-i18n'
import OutsideClickHandler from './react-outside-click-handler'
import RatioModal from './ratio/RatioModal'
import OptionsPanel from '../OptionsPanel'
import { getValueFromInput } from '../helpers/get-value-from-input'

const Ratio = ({ option, value, onChange, onChangeFor, values }) => {
	const [isForcedReversed, setIsReversed] = useState(false)
	let {
		hasOriginalRatio = true,
		// popup | inline
		view = 'popup',
		preview_width_key = null,
	} = option || {}

	const [currentModalTab, setCurrentTab] = useState('ratio')

	let normal_ratios = ['4/3', '16/9', '2/1']
	let reversed_ratios = ['3/4', '9/16', '1/2']

	const el = useRef()

	const [{ isPicking, isTransitioning }, setAnimationState] = useState({
		isPicking: false,
		isTransitioning: false,
	})

	const isReversed =
		normal_ratios.indexOf(value) > -1
			? false
			: reversed_ratios.indexOf(value) > -1
			? true
			: isForcedReversed

	let currentTab =
		value === 'original'
			? 'original'
			: value.indexOf('/') === -1
			? 'custom'
			: 'predefined'

	let isCustom = value.indexOf('/') === -1

	const inlineRatioView = (
		<Fragment>
			{option && option['inner-options'] && (
				<ul className="ct-modal-tabs">
					<li
						onClick={() => setCurrentTab('ratio')}
						className={cls({
							active: currentModalTab === 'ratio',
						})}>
						{__('Image Ratio', 'blocksy')}
					</li>
					<li
						onClick={() => setCurrentTab('size')}
						className={cls({
							active: currentModalTab === 'size',
						})}>
						{__('Image Size', 'blocksy')}
					</li>
				</ul>
			)}

			<div className="ct-ratio-content">
				{currentModalTab === 'ratio' && (
					<div
						className={cls('ct-ratio-picker', {
							reversed: isReversed,
						})}>
						<ul className="ct-radio-option ct-buttons-group">
							{hasOriginalRatio && (
								<li
									className={cls({
										active: currentTab === 'original',
									})}
									onClick={() => {
										if (value !== 'original') {
											onChange('original')
										}
									}}>
									{__('Original', 'blocksy')}
								</li>
							)}
							<li
								className={cls({
									active: currentTab === 'predefined',
								})}
								onClick={() => {
									if (
										value.indexOf('/') === -1 ||
										value === 'original'
									) {
										onChange(
											option.value === 'original'
												? '1/1'
												: option.value
										)
									}
								}}>
								{__('Predefined', 'blocksy')}
							</li>
							<li
								className={cls({
									active: currentTab === 'custom',
								})}
								onClick={() => {
									if (
										value.indexOf('/') !== -1 ||
										value === 'original'
									) {
										let [first, second] = (value ===
										'original'
											? option.value === 'original'
												? '1/1'
												: option.value
											: value
										).split('/')
										onChange(`${first}:${second}`)
									}
								}}>
								{__('Custom', 'blocksy')}
							</li>
						</ul>

						{currentTab === 'predefined' && (
							<div className="ct-ratio-predefined">
								<ul className="ct-buttons-group">
									{[
										'1/1',
										...(isReversed
											? reversed_ratios
											: normal_ratios),
									].map((ratio) => (
										<li
											key={ratio}
											className={cls({
												active: ratio === value,
											})}
											onClick={() => onChange(ratio)}>
											{ratio}
										</li>
									))}
								</ul>

								<button
									onClick={(e) => {
										e.preventDefault()

										if (value === '1/1') {
											setIsReversed(!isReversed)
											return
										}

										let [
											first_component,
											second_component,
										] = value.split('/')

										setIsReversed(
											+first_component < +second_component
										)

										onChange(
											value.split('/').reverse().join('/')
										)
									}}>
									<span />
									<i className="ct-tooltip-top">Reverse</i>
								</button>
							</div>
						)}

						{currentTab === 'custom' && (
							<div className="ct-ratio-custom">
								<div className="ct-option-input">
									<input
										type="text"
										value={value.split(':')[0]}
										onChange={({
											target: { value: val },
										}) => {
											onChange(
												`${val}:${value.split(':')[1]}`
											)
										}}
									/>
								</div>
								<span>:</span>
								<div className="ct-option-input">
									<input
										type="text"
										value={value.split(':')[1]}
										onChange={({
											target: { value: val },
										}) => {
											onChange(
												`${value.split(':')[0]}:${val}`
											)
										}}
									/>
								</div>

								<div
									className="ct-notification"
									dangerouslySetInnerHTML={{
										__html: sprintf(
											__(
												'Use this online %stool%s for calculating a custom image ratio based on your image size.',
												'blocksy'
											),
											'<a href="https://www.digitalrebellion.com/webapps/aspectcalc" target="_blank">',
											'</a>'
										),
									}}
								/>
							</div>
						)}

						{currentTab === 'original' && (
							<div className="ct-ratio-original">
								<div className="ct-notification">
									{__(
										'Images will be displayed using the aspect ratio in which they were uploaded.',
										'blocksy'
									)}
								</div>
							</div>
						)}
					</div>
				)}
				{currentModalTab === 'size' && option['inner-options'] && (
					<OptionsPanel
						onChange={(key, val) => onChangeFor(key, val)}
						options={option['inner-options']}
						value={values}
					/>
				)}
			</div>
		</Fragment>
	)

	if (view === 'inline') {
		return inlineRatioView
	}

	return (
		<div ref={el} className={cls('ct-ratio-picker-container', {})}>
			<OutsideClickHandler
				useCapture={false}
				disabled={!isPicking}
				className="ct-ratio-preview"
				onOutsideClick={() => {
					if (!isPicking) {
						return
					}

					setAnimationState({
						isTransitioning: false,
						isPicking: false,
					})
				}}
				wrapperProps={{
					onClick: (e) => {
						e.preventDefault()

						setAnimationState({
							isTransitioning: true,
							isPicking: !isPicking,
						})
					},
				}}>
				{value.indexOf(':') > -1 && (
					<span className="ct-ratio-key">
						{__('Custom', 'blocksy')}
					</span>
				)}

				{value.indexOf('/') > -1 && (
					<span className="ct-ratio-key">
						{__('Predefined', 'blocksy')}
					</span>
				)}

				{value === 'original'
					? __('Original Ratio', 'blocksy')
					: value.replace('/', ':')}

				{preview_width_key && (
					<span className="ct-width-key">
						{values[preview_width_key]}
					</span>
				)}
			</OutsideClickHandler>

			<RatioModal
				el={el}
				value={value}
				onChange={onChange}
				option={option}
				isPicking={isPicking}
				isTransitioning={isTransitioning}
				onPickingChange={(isPicking) => {
					setAnimationState({
						isTransitioning: true,
						isPicking,
					})
				}}
				stopTransitioning={() =>
					setAnimationState({
						isPicking,
						isTransitioning: false,
					})
				}
				renderContent={() => inlineRatioView}
			/>
		</div>
	)
}

Ratio.ControlEnd = () => (
	<div
		className="ct-color-modal-wrapper"
		onMouseDown={(e) => e.stopPropagation()}
		onMouseUp={(e) => e.stopPropagation()}
	/>
)

export default Ratio
