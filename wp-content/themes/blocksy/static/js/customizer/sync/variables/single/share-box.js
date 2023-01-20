import {
	applyPrefixFor,
	handleResponsiveSwitch,
	getPrefixFor,
} from '../../helpers'
import { makeVariablesWithCondition } from '../../helpers/variables-with-conditions'

let prefix = getPrefixFor()

export const getSingleShareBoxVariables = () =>
	prefix === 'single_page'
		? {}
		: {
				...makeVariablesWithCondition(
					`${prefix}_has_share_box`,
					{
						[`${prefix}_share_box_icon_size`]: {
							selector: applyPrefixFor('.ct-share-box', prefix),
							variable: 'icon-size',
							responsive: true,
							unit: 'px',
						},

						[`${prefix}_share_box_icons_spacing`]: {
							selector: applyPrefixFor('.ct-share-box', prefix),
							variable: 'spacing',
							responsive: true,
							unit: 'px',
						},

						[`${prefix}_top_share_box_spacing`]: {
							selector: applyPrefixFor(
								'.ct-share-box[data-location="top"]',
								prefix
							),
							variable: 'margin',
							responsive: true,
							unit: '',
						},

						[`${prefix}_bottom_share_box_spacing`]: {
							selector: applyPrefixFor(
								'.ct-share-box[data-location="bottom"]',
								prefix
							),
							variable: 'margin',
							responsive: true,
							unit: '',
						},
					},

					() => true
				),

				...makeVariablesWithCondition(
					[`${prefix}_has_share_box`, `${prefix}_share_box_type`],
					{
						[`${prefix}_share_items_icon_color`]: [
							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-1"]',
									prefix
								),
								variable: 'icon-color',
								type: 'color:default',
							},

							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-1"]',
									prefix
								),
								variable: 'icon-hover-color',
								type: 'color:hover',
							},
						],

						[`${prefix}_share_items_border`]: {
							selector: applyPrefixFor(
								'.ct-share-box[data-type="type-1"]',
								prefix
							),
							variable: 'border',
							type: 'border',
						},

						[`${prefix}_share_items_icon`]: [
							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-2"]',
									prefix
								),
								variable: 'icon-color',
								type: 'color:default',
							},

							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-2"]',
									prefix
								),
								variable: 'icon-hover-color',
								type: 'color:hover',
							},
						],

						[`${prefix}_share_box_alignment`]: [
							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-2"]',
									prefix
								),
								variable: 'text-horizontal-alignment',
								responsive: true,
								unit: '',
							},

							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-2"]',
									prefix
								),
								variable: 'horizontal-alignment',
								responsive: true,
								unit: '',
								extractValue: (value) => {
									if (!value.desktop) {
										return value
									}

									if (value.desktop === 'left') {
										value.desktop = 'flex-start'
									}

									if (value.desktop === 'right') {
										value.desktop = 'flex-end'
									}

									if (value.tablet === 'left') {
										value.tablet = 'flex-start'
									}

									if (value.tablet === 'right') {
										value.tablet = 'flex-end'
									}

									if (value.mobile === 'left') {
										value.mobile = 'flex-start'
									}

									if (value.mobile === 'right') {
										value.mobile = 'flex-end'
									}

									return value
								},
							},
						],


						[`${prefix}_share_items_background`]: [
							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-2"]',
									prefix
								),
								variable: 'background-color',
								type: 'color:default',
							},

							{
								selector: applyPrefixFor(
									'.ct-share-box[data-type="type-2"]',
									prefix
								),
								variable: 'background-hover-color',
								type: 'color:hover',
							},
						],
					},

					(values) => {
						let share_box_type = values[`${prefix}_share_box_type`]
						let has_share_box = values[`${prefix}_has_share_box`]

						if (has_share_box !== 'yes') {
							return false
						}

						if (share_box_type !== 'type-1') {
							// return false
						}

						return true
					}
				),
		  }
