import {
	createElement,
	useContext,
	useState,
	Fragment,
} from '@wordpress/element'
import { SortableHandle } from 'react-sortable-hoc'
import { LayersContext } from '../ct-layers'
import { __ } from 'ct-i18n'
import { normalizeCondition, matchValuesWithCondition } from 'match-conditions'

const LayerControls = ({ itemIndex, items, onChange, value }) => {
	const { removeForId, addForId, option, toggleOptionsPanel } = useContext(
		LayersContext
	)

	const hasOptions =
		option.settings[value.id] &&
		option.settings[value.id].options &&
		(!option.settings[value.id].options_condition ||
			(option.settings[value.id].options_condition &&
				matchValuesWithCondition(
					normalizeCondition(
						option.settings[value.id].options_condition
					),
					{
						...value,
						itemIndex,
					}
				)))

	let itemsOfType = items.filter(({ id }) => id === value.id)
	let relativeIndex = itemsOfType.map(({ __id }) => __id).indexOf(value.__id)

	return (
		<div className="ct-layer-controls">
			{!option.disableHiding && (
				<button
					type="button"
					className="ct-visibility"
					onClick={(e) => {
						e.stopPropagation()
						onChange(
							items.map((l) =>
								l.__id === value.__id
									? {
											...l,
											enabled: !{
												enabled: true,
												...l,
											}.enabled,
									  }
									: l
							)
						)
					}}>
					<svg width="13px" height="13px" viewBox="0 0 24 24">
						<path d="M12,4C4.1,4,0,12,0,12s3.1,8,12,8c8.1,0,12-8,12-8S20.1,4,12,4z M12,17c-2.9,0-5-2.2-5-5c0-2.8,2.1-5,5-5s5,2.2,5,5C17,14.8,14.9,17,12,17z M12,9c-1.7,0-3,1.4-3,3c0,1.6,1.3,3,3,3s3-1.4,3-3C15,10.4,13.7,9,12,9z" />
					</svg>
				</button>
			)}

			<div className="ct-layer-label">
				<span>
					{window._.template(
						(
							option.settings[value.id] || {
								label: value.id,
							}
						).label
					)(value).replace(
						' INDEX',
						itemsOfType.length === 1 ? '' : ` ${relativeIndex + 1}`
					)}
				</span>
			</div>

			{option.settings[value.id] &&
				option.settings[value.id].clone &&
				items.filter(({ id }) => id === value.id).length <
					(parseInt(option.settings[value.id].clone) || 1) + 1 && (
					<button
						type="button"
						className="ct-clone"
						onClick={() => addForId(value.id, value)}>
						<svg width="11px" height="11px" viewBox="0 0 24 24">
							<path d="M23,24H7.7c-0.6,0-1-0.4-1-1V7.7c0-0.6,0.4-1,1-1H23c0.6,0,1,0.4,1,1V23C24,23.6,23.6,24,23,24z M8.7,22H22V8.7 H8.7V22z" />
							<path d="M17.3,16.3c0,0.6-0.4,1-1,1H1c-0.6,0-1-0.4-1-1V1c0-0.6,0.4-1,1-1h15.3c0.6,0,1,0.4,1,1V16.3z" />
						</svg>

						<i className="ct-tooltip-top">
							{__('Clone Item', 'blocksy')}
						</i>
					</button>
				)}

			{(option.manageable ||
				(option.settings[value.id] &&
					option.settings[value.id].clone &&
					items.filter(({ id }) => id === value.id).length > 1) ||
				!option.settings[value.id]) && (
				<button
					type="button"
					className="ct-remove"
					onClick={() => removeForId(value.__id)}
				/>
			)}

			{hasOptions && (
				<button
					type="button"
					className="ct-toggle"
					onMouseDown={(e) => {
						e.stopPropagation()
					}}
					onClick={(e) => {
						e.stopPropagation()
						toggleOptionsPanel(value.__id)
					}}
				/>
			)}
		</div>
	)
}

export default SortableHandle(LayerControls)
