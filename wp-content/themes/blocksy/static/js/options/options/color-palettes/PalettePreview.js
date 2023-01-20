import { createElement, useRef, Fragment } from '@wordpress/element'
import OptionsPanel from '../../OptionsPanel'
import { __, sprintf } from 'ct-i18n'
import classnames from 'classnames'

const PalettePreview = ({
	renderBefore = () => null,
	value,
	onChange,
	onClick,
	currentPalette = null,
	className,
}) => {
	if (!currentPalette) {
		currentPalette = value

		if (value.palettes) {
			currentPalette = value.palettes.find(
				({ id }) => id === value.current_palette
			)
		}
	}

	return (
		<div
			className={classnames('ct-single-palette', className)}
			onClick={(e) => {
				if (
					e.target.closest('.ct-color-picker-modal') ||
					e.target.classList.contains('ct-color-picker-modal')
				) {
					return
				}

				onClick()
			}}>
			{renderBefore()}
			<OptionsPanel
				hasRevertButton={false}
				onChange={(optionId, optionValue) => {
					if (optionId !== 'color') {
						return
					}

					onChange(
						optionId,
						Object.keys(optionValue).reduce(
							(finalValue, currentId) => ({
								...finalValue,
								...(currentId.indexOf('color') === 0
									? { [currentId]: optionValue[currentId] }
									: {}),
							}),

							{}
						)
					)
				}}
				value={{ color: currentPalette }}
				options={{
					color: {
						type: 'ct-color-picker',
						predefined: true,
						design: 'none',
						label: false,
						modalClassName: 'ct-color-palette-modal',
						value: currentPalette,

						...(onChange ? {} : { skipModal: true }),

						pickers: Object.keys(currentPalette)
							.filter((k) => k.indexOf('color') === 0)
							.map((key, index) => ({
								title: sprintf(
									__('Color %s', 'blocksy'),
									index + 1
								),
								id: key,
							})),
					},
				}}
			/>
		</div>
	)
}

export default PalettePreview
