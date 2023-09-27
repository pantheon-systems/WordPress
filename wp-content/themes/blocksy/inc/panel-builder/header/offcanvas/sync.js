import { handleBackgroundOptionFor } from '../../../../static/js/customizer/sync/variables/background'
import ctEvents from 'ct-events'
import { updateAndSaveEl } from '../../../../static/js/customizer/sync'
import {
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
} from '../../../../static/js/customizer/sync/helpers'

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		const handleSectionBackground = ({ itemId }) =>
			handleBackgroundOptionFor({
				id: 'section',
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({ itemId }),
						operation: 'suffix',
						to_add: '.ct-panel-inner',
					})
				),

				responsive: true,
				addToDescriptors: {
					fullValue: true,
				},

				valueExtractor: ({ offcanvasBackground }) =>
					offcanvasBackground,
			}).section

		const handleRootBackground = ({ itemId }) =>
			handleBackgroundOptionFor({
				id: 'section',
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				responsive: true,
				addToDescriptors: {
					fullValue: true,
				},

				valueExtractor: ({
					offcanvas_behavior,
					offcanvasBackdrop,
					offcanvasBackground,
				}) =>
					offcanvas_behavior === 'modal'
						? offcanvasBackground
						: offcanvasBackdrop,
			}).section

		variableDescriptors['offcanvas'] = ({ itemId }) => ({
			offcanvas_behavior: [
				...handleSectionBackground({ itemId }),
				...handleRootBackground({ itemId }),
			],
			offcanvasBackground: [
				...handleSectionBackground({ itemId }),
				...handleRootBackground({ itemId }),
			],
			offcanvasBackdrop: [...handleRootBackground({ itemId })],

			headerPanelShadow: {
				selector: assembleSelector(
					`${
						getRootSelectorFor({ itemId })[0]
					} [data-behaviour*="side"]`
				),
				type: 'box-shadow',
				variable: 'box-shadow',
				responsive: true,
			},

			side_panel_width: {
				selector: assembleSelector(getRootSelectorFor({ itemId })),
				variable: 'side-panel-width',
				responsive: true,
				unit: '',
			},

			offcanvas_content_vertical_alignment: [
				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'vertical-alignment',
					responsive: true,
					unit: '',
				},
			],

			offcanvasContentAlignment: [
				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'horizontal-alignment',
					responsive: true,
					unit: '',
				},

				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'text-horizontal-alignment',
					responsive: true,
					unit: '',
					extractValue: (value) => {
						if (!value.desktop) {
							return value
						}

						if (value.desktop === 'initial') {
							value.desktop = 'left'
						}

						if (value.desktop === 'flex-end') {
							value.desktop = 'right'
						}

						if (value.tablet === 'initial') {
							value.tablet = 'left'
						}

						if (value.tablet === 'flex-end') {
							value.tablet = 'right'
						}

						if (value.mobile === 'initial') {
							value.mobile = 'left'
						}

						if (value.mobile === 'flex-end') {
							value.mobile = 'right'
						}

						return value
					},
				},

				{
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'has-indentation',
					unit: '',
					responsive: true,

					extractValue: (value) => {
						if (value.desktop) {
							if (
								value.desktop === 'center' ||
								value.tablet === 'center' ||
								value.mobile === 'center'
							) {
								return {
									desktop:
										value.desktop === 'center' ? '0' : '1',
									tablet:
										value.tablet === 'center' ? '0' : '1',
									mobile:
										value.mobile === 'center' ? '0' : '1',
								}
							}
						}

						return 'CT_CSS_SKIP_RULE'
					},
				},
			],

			menu_close_button_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '.ct-toggle-close',
						})
					),
					variable: 'icon-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '.ct-toggle-close:hover',
						})
					),
					variable: 'icon-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			menu_close_button_border_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '.ct-toggle-close[data-type="type-2"]',
						})
					),
					variable: 'toggle-button-border-color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add:
								'.ct-toggle-close[data-type="type-2"]:hover',
						})
					),
					variable: 'toggle-button-border-color',
					type: 'color:hover',
					responsive: true,
				},
			],

			menu_close_button_shape_color: [
				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '.ct-toggle-close[data-type="type-3"]',
						})
					),
					variable: 'toggle-button-background',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add:
								'.ct-toggle-close[data-type="type-3"]:hover',
						})
					),
					variable: 'toggle-button-background',
					type: 'color:hover',
					responsive: true,
				},
			],

			menu_close_button_icon_size: {
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({ itemId }),
						operation: 'suffix',
						to_add: '.ct-toggle-close',
					})
				),
				variable: 'icon-size',
				unit: 'px',
			},

			menu_close_button_border_radius: {
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({ itemId }),
						operation: 'suffix',
						to_add: '.ct-toggle-close',
					})
				),
				variable: 'toggle-button-radius',
				unit: 'px',
			},
		})
	}
)

ctEvents.on(
	'ct:header:sync:item:offcanvas',
	({ optionId, optionValue, values }) => {
		const selector = '#offcanvas'

		if (
			optionId === 'offcanvas_behavior' ||
			optionId === 'side_panel_position'
		) {
			const el = document.querySelector('#offcanvas')

			setTimeout(() => {
				el.removeAttribute('data-behaviour')
				el.classList.add('ct-disable-transitions')

				requestAnimationFrame(() => {
					el.dataset.behaviour =
						values.offcanvas_behavior === 'modal'
							? 'modal'
							: `${values.side_panel_position}-side`

					setTimeout(() => {
						el.classList.remove('ct-disable-transitions')
					})
				})
			}, 300)
		}

		if (optionId === 'menu_close_button_type') {
			let offcanvasModalClose = document.querySelector(
				'#offcanvas .ct-toggle-close'
			)

			setTimeout(() => {
				offcanvasModalClose.classList.add('ct-disable-transitions')

				requestAnimationFrame(() => {
					if (offcanvasModalClose) {
						offcanvasModalClose.dataset.type = optionValue
					}

					setTimeout(() => {
						offcanvasModalClose.classList.remove(
							'ct-disable-transitions'
						)
					})
				})
			}, 300)
		}
	}
)
