import { __ } from 'ct-i18n'
import {
	useRef,
	useState,
	Fragment,
	createElement,
	createPortal,
	render,
} from '@wordpress/element'

import $ from 'jquery'

import OptionsPanel from '../options/OptionsPanel'

import { getValueFromInput } from '../options/helpers/get-value-from-input'

import deepEqual from 'deep-equal'

const TaxonomyRoot = ({ options, input_name, value }) => {
	const [internalValue, setInternalValue] = useState(value)
	const input = useRef()

	return (
		<Fragment>
			<input
				value={JSON.stringify(
					Array.isArray(internalValue) ? {} : internalValue
				)}
				onChange={() => {}}
				name={input_name}
				type="hidden"
				ref={input}
			/>

			{createPortal(
				<OptionsPanel
					value={internalValue}
					options={{
						accent_color: options.accent_color,
					}}
					onChange={(key, newValue) => {
						setInternalValue((internalValue) => ({
							...internalValue,
							[key]: newValue,
						}))
						$(input.current).change()
					}}
				/>,

				document.querySelector('.term-blocksy-accent-color-wrap td')
			)}

			{createPortal(
				<button
					type="button"
					disabled={deepEqual(
						options.accent_color.value,
						internalValue.accent_color
					)}
					className="ct-revert"
					onClick={() => {
						setInternalValue((internalValue) => ({
							...internalValue,
							accent_color: options.accent_color.value,
						}))
						$(input.current).change()
					}}
				/>,

				document.querySelector(
					'.term-blocksy-accent-color-wrap th label'
				)
			)}

			{createPortal(
				<OptionsPanel
					value={internalValue}
					options={{
						image: options.image,
					}}
					onChange={(key, newValue) => {
						setInternalValue((internalValue) => ({
							...internalValue,
							[key]: newValue,
						}))
						$(input.current).change()
					}}
				/>,

				document.querySelector('.term-blocksy-image-wrap td')
			)}
		</Fragment>
	)
}

export const initTaxonomies = () => {
	const maybeTaxonomyField = document.querySelector(
		'[name*="blocksy_taxonomy_meta_options"]'
	)

	if (!maybeTaxonomyField) {
		return
	}

	let options = {
		image: {
			label: __('Transparent State Logo', 'blocksy'),
			type: 'ct-image-uploader',
			value: '',
			attr: { 'data-type': 'large' },
			design: 'none',
			emptyLabel: __('Select Image', 'blocksy'),
		},

		accent_color: {
			label: __('Site Title Color', 'blocksy'),
			type: 'ct-color-picker',

			design: 'none',

			value: {
				default: {
					color: 'CT_CSS_SKIP_RULE',
				},

				hover: {
					color: 'CT_CSS_SKIP_RULE',
				},

				background_initial: {
					color: 'CT_CSS_SKIP_RULE',
				},

				background_hover: {
					color: 'CT_CSS_SKIP_RULE',
				},
			},

			pickers: [
				{
					title: __('Text Initial', 'blocksy'),
					id: 'default',
				},

				{
					title: __('Text Hover', 'blocksy'),
					id: 'hover',
				},

				{
					title: __('Background Initial', 'blocksy'),
					id: 'background_initial',
				},

				{
					title: __('Background Hover', 'blocksy'),
					id: 'background_hover',
				},
			],
		},
	}

	render(
		<TaxonomyRoot
			input_name={maybeTaxonomyField.name}
			options={options}
			value={getValueFromInput(
				options,
				JSON.parse(maybeTaxonomyField.value),
				null,
				false
			)}
		/>,
		maybeTaxonomyField.parentNode
	)
}
