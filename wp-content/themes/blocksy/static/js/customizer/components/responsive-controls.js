import { createElement, Component, Fragment } from '@wordpress/element'
import classnames from 'classnames'
export { maybePromoteScalarValueIntoResponsive } from 'customizer-sync-helpers/dist/promote-into-responsive'
import { getOptionFor } from '../../options/GenericOptionType'

/**
 * true
 * false
 * skip
 */
export const isOptionEnabledFor = (currentDevice, responsiveDescriptor) =>
	({
		desktop: true,
		tablet: true,
		mobile: true,

		...(typeof responsiveDescriptor === 'boolean'
			? {}
			: responsiveDescriptor || {}),
	}[currentDevice])

export const isOptionResponsiveFor = (option, args = {}) => {
	let { ignoreHidden = false } = args

	let OptionComponent = getOptionFor(option)

	if (OptionComponent.hiddenResponsive) {
		if (!ignoreHidden) {
			return true
		}
	}

	return !!option.responsive
}

export const getValueForDevice = ({ value, option, device }) => {
	const devices = ['desktop', 'tablet', 'mobile']

	if (
		device === 'tablet' &&
		isOptionEnabledFor(device, option.responsive) === 'skip'
	) {
		return value.mobile
	}

	return value[device]
}

const ResponsiveControls = ({ device, setDevice, responsiveDescriptor }) => (
	<div className="ct-control-options">
		<ul className="ct-responsive-controls ct-devices">
			{['desktop', 'tablet', 'mobile']
				.filter(
					(d) =>
						isOptionEnabledFor(d, responsiveDescriptor) !== 'skip'
				)
				.map((d) => (
					<li
						onClick={() => setDevice(d)}
						className={classnames(
							{
								active: d === device,
								'ct-disabled': !isOptionEnabledFor(
									d,
									responsiveDescriptor
								),
							},
							`ct-${d}`
						)}
						key={d}
					/>
				))}
		</ul>
	</div>
)

export default ResponsiveControls
