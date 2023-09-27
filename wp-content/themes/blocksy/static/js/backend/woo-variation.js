import { createElement, render } from '@wordpress/element'
import OptionsRoot from '../options/OptionsRoot'
import { getValueFromInput } from '../options/helpers/get-value-from-input'
import $ from 'jquery'
import { __ } from 'ct-i18n'

export const initWooVariation = (variationWrapper) => {
	const uploadImage = variationWrapper.querySelector('.upload_image')

	if (!uploadImage) {
		return
	}

	const div = document.createElement('p')

	div.classList.add('form-row')
	div.classList.add('form-row-full')
	div.classList.add('ct-variation-image-gallery')

	uploadImage.nextElementSibling.insertAdjacentElement('afterend', div)

	const input = variationWrapper.querySelector(
		'[name*="blocksy_post_meta_options"]'
	)

	if (!input) {
		return
	}

	const options = {
		gallery_source: {
			label: __('Variation Gallery Source', 'blocksy'),
			type: 'ct-radio',
			value: 'default',
			design: 'inline',
			divider: 'bottom',
			choices: {
				default: __('Default', 'blocksy'),
				custom: __('Custom', 'blocksy'),
			},
		},

		condition: {
			type: 'ct-condition',
			condition: {
				gallery_source: 'custom',
			},
			options: {
				images: {
					label: __('Variation Image Gallery', 'blocksy'),
					type: 'ct-multi-image-uploader',
					design: ({ value }) =>
						value.length === 0 ? 'inline' : 'block',
					value: [],
				},
			},
		},
	}

	render(
		<OptionsRoot
			options={options}
			value={getValueFromInput(
				options,
				JSON.parse(input.value),
				null,
				false
			)}
			input_id={input.id}
			input_name={input.name}
			hasRevertButton={false}
		/>,
		div
	)
}

export const initAllWooVariations = () => {
	;[
		...document.querySelectorAll(
			'.woocommerce_variations .woocommerce_variation'
		),
	].map((variationWrapper) => {
		if (variationWrapper.hasBlocksyOptions) {
			return
		}

		variationWrapper.hasBlocksyOptions = true

		initWooVariation(variationWrapper)
	})
}
