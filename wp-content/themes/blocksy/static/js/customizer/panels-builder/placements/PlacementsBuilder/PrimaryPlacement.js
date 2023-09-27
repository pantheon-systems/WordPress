import { createElement } from '@wordpress/element'
import DraggableItems from '../DraggableItems'

const PrimaryPlacement = ({ placementName, bar, direction }) => {
	const placement = bar.placements.find(({ id }) => id === placementName)

	let placementsToRender = [placement]

	if (placementName !== 'middle') {
		const middle = bar.placements.find(({ id }) => id === 'middle')

		if (middle && middle.items.length > 0) {
			if (placementName === 'start') {
				const startMiddle = bar.placements.find(
					({ id }) => id === 'start-middle'
				)

				placementsToRender = [placement, startMiddle]
			}

			if (placementName === 'end') {
				const endMiddle = bar.placements.find(
					({ id }) => id === 'end-middle'
				)

				placementsToRender = [endMiddle, placement]
			}
		}
	}

	return (
		<li
			className={[`ct-builder-column-${placement.id}`]}
			{...(placement.id === 'middle'
				? { 'data-count': placement.items.length }
				: {})}>
			{placementsToRender.map((placement) => (
				<DraggableItems
					key={placement.id}
					direction={direction}
					className={
						placement.id === 'middle'
							? ''
							: `ct-${
									placement.id.indexOf('-') > -1
										? 'secondary'
										: 'primary'
							  }-column`
					}
					draggableId={`${bar.id}:${placement.id}`}
					items={placement.items}
				/>
			))}
		</li>
	)
}

export default PrimaryPlacement
