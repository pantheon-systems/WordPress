import { createElement, useContext } from '@wordpress/element'
import { __ } from 'ct-i18n'
import cls from 'classnames'
import DraggableItems from './DraggableItems'
import Row from './PlacementsBuilder/Row'

const PlacementsBuilder = ({
	inlinedItemsFromBuilder,
	view,
	builderValueWithView,
}) => {
	let hasOffcanvas =
		view === 'mobile' ||
		(inlinedItemsFromBuilder.indexOf('trigger') > -1 &&
			builderValueWithView.find(({ id }) => id === 'offcanvas'))

	return (
		<div
			className={cls('placements-builder', {
				'ct-mobile': hasOffcanvas,
			})}>
			{hasOffcanvas && (
				<ul className="offcanvas-container">
					<Row
						direction="vertical"
						bar={builderValueWithView.find(
							({ id }) => id === 'offcanvas'
						)}
					/>
				</ul>
			)}

			<ul className="horizontal-rows">
				{['top-row', 'middle-row', 'bottom-row'].map((bar) => {
					const maybeBar = builderValueWithView.find(
						({ id }) => id === bar
					)

					if (!maybeBar) {
						return null
					}

					return <Row bar={maybeBar} key={bar} />
				})}
			</ul>
		</div>
	)
}

export default PlacementsBuilder
