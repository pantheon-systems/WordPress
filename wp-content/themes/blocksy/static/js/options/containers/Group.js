import { createElement, Fragment } from '@wordpress/element'
import OptionsPanel from '../OptionsPanel'
import { capitalizeFirstLetter } from '../GenericOptionType'

import {
	useDeviceManagerState,
	useDeviceManagerActions,
} from '../../customizer/components/useDeviceManager'
import ResponsiveControls from '../../customizer/components/responsive-controls'

const Group = ({ renderingChunk, value, onChange, purpose, hasRevertButton }) =>
	renderingChunk.map((groupOption) => {
		const {
			label,
			options,
			id,
			attr = {},
			wrapperAttr = {},
			responsive = false,
		} = groupOption
		const { currentView } = useDeviceManagerState()
		const { setDevice } = useDeviceManagerActions()

		const groupContents = (
			<OptionsPanel
				purpose={purpose}
				onChange={onChange}
				options={options}
				value={value}
				hasRevertButton={hasRevertButton}
			/>
		)

		return (
			<div key={id} className="ct-controls-group" {...wrapperAttr}>
				{label && (
					<header>
						<label>{label}</label>

						{responsive && (
							<ResponsiveControls
								device={currentView}
								responsiveDescriptor={responsive}
								setDevice={setDevice}
							/>
						)}
					</header>
				)}
				<section
					{...attr}
					{...(currentView !== 'desktop'
						? { 'data-disabled-last': '' }
						: {})}>
					{groupContents}
				</section>
			</div>
		)
	})

export default Group
