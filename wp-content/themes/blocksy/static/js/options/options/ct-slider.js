import {
	createElement,
	Component,
	createRef,
	Fragment,
} from '@wordpress/element'
import classnames from 'classnames'
import linearScale from 'simple-linear-scale'

import OutsideClickHandler from './react-outside-click-handler'

import { __ } from 'ct-i18n'

export const clamp = (min, max, value) => Math.max(min, Math.min(max, value))
const clampMax = (max, value) => Math.min(max, value)

export const round = (value, decimalPlaces = 1) => {
	const multiplier = Math.pow(10, decimalPlaces)

	const rounded = Math.round(value * multiplier + Number.EPSILON) / multiplier

	return rounded
}

var roundWholeNumbers = function (num, precision) {
	num = parseFloat(num)
	if (!precision) return num
	return Math.round(num / precision) * precision
}

const UnitsList = ({
	option,
	value,
	onChange,
	is_open,
	toggleOpen,
	currentUnit,
	getNumericValue,
	getAllowedDecimalPlaces,

	forced_current_unit,
	setForcedCurrentUnit,
}) => {
	const pickUnit = (unit) => {
		const numericValue = getNumericValue()

		let futureUnitDescriptor = option.units.find(
			({ unit: u }) => u === unit
		)

		if (Object.keys(futureUnitDescriptor).includes('min')) {
			onChange(
				`${round(
					clamp(
						option.units.find(({ unit: u }) => u === unit).min,
						option.units.find(({ unit: u }) => u === unit).max,
						numericValue === '' ? -Infinity : numericValue
					),
					getAllowedDecimalPlaces(unit)
				)}${unit}`
			)
		} else {
			onChange(value)
		}

		if (
			futureUnitDescriptor.unit === '' &&
			futureUnitDescriptor.type === 'custom'
		) {
			setForcedCurrentUnit('')
		} else {
			setForcedCurrentUnit('__DEFAULT__')
		}
	}

	let futureUnitDescriptor = option.units.find(
		({ unit: u }) => u === currentUnit
	)

	return (
		<Fragment>
			<span
				onClick={() => toggleOpen()}
				className="ct-current-value"
				data-unit={
					currentUnit ||
					(futureUnitDescriptor &&
					futureUnitDescriptor.type === 'custom'
						? __('custom', 'blocksy')
						: '')
				}>
				{currentUnit ||
					(futureUnitDescriptor &&
					futureUnitDescriptor.type === 'custom'
						? __('Custom', 'blocksy')
						: '―')}
			</span>

			<OutsideClickHandler
				className="ct-units-list"
				onOutsideClick={() => {
					if (!is_open) {
						return
					}

					toggleOpen()
				}}>
				{option.units
					.filter(({ unit }) => unit !== currentUnit)
					.map(({ unit, type }) => (
						<span
							key={unit}
							data-unit={type === 'custom' ? 'custom' : unit}
							onClick={() => {
								pickUnit(unit)
								toggleOpen()
							}}>
							{unit ||
								(type === 'custom'
									? __('Custom', 'blocksy')
									: '―')}
						</span>
					))}
			</OutsideClickHandler>
		</Fragment>
	)
}

export default class Slider extends Component {
	state = {
		is_dragging: false,
		is_open: false,
		is_empty_input: false,
		forced_current_unit: '__DEFAULT__',
	}

	el = createRef()

	hasUnitsList = () =>
		this.props.option.units && this.props.option.units.length > 1

	getAllowedDecimalPlaces = (properUnit = null) => {
		const decimals = this.props.option.units
			? this.props.option.units.find(
					({ unit }) => unit === (properUnit || this.getCurrentUnit())
			  )?.decimals || 0
			: this.props.option.decimals

		return decimals !== 0 && !decimals ? 0 : decimals
	}

	withDefault = (currentUnit, defaultUnit) =>
		this.props.option.units
			? this.props.option.units.find(({ unit }) => unit === currentUnit)
				? currentUnit
				: currentUnit || defaultUnit
			: currentUnit || defaultUnit

	getCurrentUnit = () => {
		if (this.state.forced_current_unit !== '__DEFAULT__') {
			return this.state.forced_current_unit
		}

		if (!this.props.option.units) {
			return ''
		}

		let defaultUnit = this.props.option.units
			? this.props.option.units[0].unit
			: ''

		if (
			this.props.value === 'NaN' ||
			this.props.value === '' ||
			this.props.value === 'CT_CSS_SKIP_RULE'
		) {
			return defaultUnit
		}

		let computedUnit = this.props.value
			.toString()
			.replace(/[0-9]/g, '')
			.replace(/\-/g, '')
			.replace(/\./g, '')
			.replace('CT_CSS_SKIP_RULE', '')

		let maybeActualUnit = this.props.option.units.find(
			({ unit }) => unit === computedUnit
		)

		if (maybeActualUnit) {
			return computedUnit
		}

		return ''
	}

	getMax = () =>
		this.props.option.units
			? this.props.option.units.find(
					({ unit }) => unit === this.getCurrentUnit()
			  )?.max || 0
			: this.props.option.max

	getMin = () => {
		return this.props.option.units
			? this.props.option.units.find(
					({ unit }) => unit === this.getCurrentUnit()
			  )?.min || 0
			: this.props.option.min
	}

	getNumericValue = ({ forPosition = false } = {}) => {
		const maybeValue = parseFloat(this.props.value, 10)

		if (maybeValue === 0) {
			return maybeValue
		}

		if (!maybeValue) {
			if (
				this.props.option.defaultPosition &&
				this.props.option.defaultPosition === 'center' &&
				forPosition
			) {
				let min = parseFloat(this.getMin(), 10)
				let max = parseFloat(this.getMax(), 10)

				return (max - min) / 2 + min
			}

			return ''
		}

		return maybeValue
	}

	computeAndSendNewValue({ pageX, shiftKey }) {
		let { top, left, right, width } =
			this.el.current.getBoundingClientRect()

		let elLeftOffset = pageX - left - pageXOffset

		this.props.onChange(
			`${roundWholeNumbers(
				round(
					linearScale(
						[0, width],
						[
							parseFloat(this.getMin(), 10),
							parseFloat(this.getMax(), 10),
						],
						true
					)(
						document.body.classList.contains('rtl')
							? width - elLeftOffset
							: elLeftOffset
					),
					this.getAllowedDecimalPlaces()
				),

				shiftKey ? 10 : 1
			)}${this.getCurrentUnit()}`
		)
	}

	handleMove = (event) => {
		if (!this.state.is_dragging) return
		this.computeAndSendNewValue(event)
	}

	handleUp = () => {
		this.setState({
			is_dragging: false,
		})

		this.detachEvents()
	}

	handleFocus = () => {
		if (this.isCustomValueInput()) {
			this.setState({
				forced_current_unit: this.getCurrentUnit(),
			})
		}
	}

	handleOptionRevert = () => {
		this.setState({
			forced_current_unit: '__DEFAULT__',
		})
	}

	handleBlur = () => {
		this.setState({ is_empty_input: false })

		if (this.props.option.value === 'CT_CSS_SKIP_RULE') {
			if (this.props.value === 'CT_CSS_SKIP_RULE') {
				return
			}

			if (this.getNumericValue() === '') {
				this.props.onChange('CT_CSS_SKIP_RULE')
				return
			}
		}

		if (this.props.value.toString().trim() === '') {
			this.props.onChange(this.props.option.value)
			return
		}

		this.props.onChange(
			`${clamp(
				parseFloat(this.getMin(), 10),
				parseFloat(this.getMax(), 10),
				parseFloat(this.getNumericValue(), 10)
			)}${this.getCurrentUnit()}`
		)
	}

	handleChange = (value) => {
		if (this.props.option.value === 'CT_CSS_SKIP_RULE') {
			if (value.toString().trim() === '') {
				this.props.onChange('CT_CSS_SKIP_RULE')
				return
			}
		}

		if (this.isCustomValueInput()) {
			this.props.onChange(value)
			return
		}

		if (value.toString().trim() === '') {
			this.setState({ is_empty_input: true })
			return
		}

		this.setState({ is_empty_input: false })

		this.props.onChange(
			`${clampMax(
				parseFloat(this.getMax(), 10),
				parseFloat(value || this.getMin())
			)}${this.getCurrentUnit()}`
		)
	}

	attachEvents() {
		document.documentElement.addEventListener(
			'mousemove',
			this.handleMove,
			true
		)

		document.documentElement.addEventListener(
			'mouseup',
			this.handleUp,
			true
		)
	}

	detachEvents() {
		document.documentElement.removeEventListener(
			'mousemove',
			this.handleMove,
			true
		)

		document.documentElement.removeEventListener(
			'mouseup',
			this.handleUp,
			true
		)
	}

	getLeftValue() {
		return `${linearScale(
			[parseFloat(this.getMin(), 10), parseFloat(this.getMax(), 10)],
			[0, 100]
		)(
			clamp(
				parseFloat(this.getMin(), 10),
				parseFloat(this.getMax(), 10),
				parseFloat(this.getNumericValue({ forPosition: true }), 10) ===
					0
					? 0
					: parseFloat(
							this.getNumericValue({ forPosition: true }),
							10
					  )
					? parseFloat(
							this.getNumericValue({ forPosition: true }),
							10
					  )
					: parseFloat(this.getMin(), 10)
			)
		)}`
	}

	isCustomValueInput() {
		if (!this.hasUnitsList()) return false

		let maybeUnit = this.props.option.units.find(({ unit: u }) => u === '')

		if (!maybeUnit) {
			return false
		}

		return (
			this.getCurrentUnit() === '' &&
			maybeUnit.unit === '' &&
			maybeUnit.type === 'custom'
		)
	}

	render() {
		return (
			<div className="ct-option-slider">
				{this.props.beforeOption && this.props.beforeOption()}

				{this.isCustomValueInput() ? (
					<>
						<input
							type="text"
							{...(this.props.option.ref
								? { ref: this.props.option.ref }
								: {})}
							value={
								this.state.is_empty_input ||
								this.props.value === 'NaN' ||
								(this.props.value || '')
									.toString()
									.indexOf('CT_CSS_SKIP_RULE') > -1
									? ''
									: this.props.value
							}
							onFocus={() => this.handleFocus()}
							onChange={({ target: { value } }) =>
								this.handleChange(value)
							}
						/>
					</>
				) : (
					<div
						onMouseDown={({ pageX, pageY }) => {
							this.attachEvents()
							this.setState({ is_dragging: true })
						}}
						onClick={(e) => this.computeAndSendNewValue(e)}
						ref={this.el}
						className="ct-slider"
						{...(this.props.option.steps
							? { ['data-steps']: '' }
							: {})}>
						<div style={{ width: `${this.getLeftValue()}%` }} />
						<span
							tabIndex="0"
							onKeyDown={(e) => {
								const valueForComputation =
									this.getNumericValue()

								let step =
									1 /
									Math.pow(10, this.getAllowedDecimalPlaces())

								let actualStep = e.shiftKey ? step * 10 : step

								/**
								 * Arrow up or left
								 */
								if (e.keyCode === 38 || e.keyCode === 39) {
									e.preventDefault()

									this.props.onChange(
										`${clamp(
											parseFloat(this.getMin(), 10),
											parseFloat(this.getMax(), 10),
											valueForComputation + actualStep
										)}${this.getCurrentUnit()}`
									)
								}

								/**
								 * Arrow down or right
								 */
								if (e.keyCode === 40 || e.keyCode === 37) {
									e.preventDefault()

									this.props.onChange(
										`${clamp(
											parseFloat(this.getMin(), 10),
											parseFloat(this.getMax(), 10),
											valueForComputation - actualStep
										)}${this.getCurrentUnit()}`
									)
								}
							}}
							style={{
								'--position': `${this.getLeftValue()}%`,
							}}
						/>

						{this.props.option.steps && (
							<section className={this.props.option.steps}>
								<i className="minus"></i>
								<i className="zero"></i>
								<i className="plus"></i>
							</section>
						)}
					</div>
				)}

				{!this.props.option.skipInput && (
					<div
						className={classnames('ct-slider-input', {
							// ['ct-unit-changer']: !!this.props.option.units,
							['ct-value-changer']: true,
							'no-unit-list': !this.hasUnitsList(),
							active: this.state.is_open,
						})}>
						{!this.isCustomValueInput() && (
							<>
								<input
									type="number"
									{...(this.props.option.ref
										? { ref: this.props.option.ref }
										: {})}
									step={
										1 /
										Math.pow(
											10,
											this.getAllowedDecimalPlaces()
										)
									}
									value={
										this.state.is_empty_input
											? ''
											: this.getNumericValue()
									}
									onFocus={() => this.handleFocus()}
									onBlur={() => this.handleBlur()}
									onChange={({ target: { value } }) => {
										this.handleChange(value)
									}}
								/>
							</>
						)}

						{!this.hasUnitsList() && (
							<span className="ct-current-value">
								{this.withDefault(
									this.getCurrentUnit(),
									this.props.option.defaultUnit || 'px'
								)}
							</span>
						)}

						{this.hasUnitsList() && (
							<UnitsList
								option={this.props.option}
								value={this.props.value}
								onChange={this.props.onChange}
								is_open={this.state.is_open}
								forced_current_unit={
									this.state.forced_current_unit
								}
								setForcedCurrentUnit={(unit) => {
									this.setState({ forced_current_unit: unit })
								}}
								toggleOpen={() =>
									this.setState({
										is_open: !this.state.is_open,
									})
								}
								currentUnit={this.getCurrentUnit()}
								getNumericValue={this.getNumericValue}
								getAllowedDecimalPlaces={
									this.getAllowedDecimalPlaces
								}
							/>
						)}
					</div>
				)}
			</div>
		)
	}
}
