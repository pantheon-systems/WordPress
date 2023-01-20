import { createElement, useContext } from '@wordpress/element'
import { __ } from 'ct-i18n'
import { PanelContext } from '../../../../options/components/PanelLevel'

import PrimaryPlacement from './PrimaryPlacement'

const Row = ({ bar, direction = 'horizontal' }) => {
	const { panelsHelpers } = useContext(PanelContext)

	return (
		<li className="builder-row">
			<div
				className="ct-row-actions"
				onClick={() => panelsHelpers.open(`builder_panel_${bar.id}`)}>
				{
					{
						'top-row': __('Top Row', 'blocksy'),
						'middle-row': __('Main Row', 'blocksy'),
						'bottom-row': __('Bottom Row', 'blocksy'),
						offcanvas: __('Off Canvas Area', 'blocksy')
					}[bar.id]
				}
			</div>

			<ul className="row-inner">
				{['start', 'middle', 'end']
					.filter(
						placementName =>
							!!bar.placements.find(
								({ id }) => id === placementName
							)
					)
					.map(placementName => (
						<PrimaryPlacement
							key={placementName}
							bar={bar}
							placementName={placementName}
							direction={direction}
						/>
					))}
			</ul>
		</li>
	)
}

export default Row
