import ctEvents from 'ct-events'
import {
	updateAndSaveEl,
	handleBackgroundOptionFor,
	assembleSelector,
	typographyOption,
	mutateSelector,
	getRootSelectorFor,
	responsiveClassesFor,
} from 'blocksy-customizer-sync'

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['account'] = ({ itemId }) => {
			return {
				accountHeaderIconSize: {
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'el-suffix',
							to_add: '[data-state="out"]',
						})
					),
					variable: 'icon-size',
					responsive: true,
					unit: 'px',
				},

				account_loggedin_icon_size: {
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'el-suffix',
							to_add: '[data-state="in"]',
						})
					),
					variable: 'icon-size',
					responsive: true,
					unit: 'px',
				},

				accountHeaderAvatarSize: {
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					variable: 'avatar-size',
					responsive: true,
					unit: 'px',
				},

				...handleBackgroundOptionFor({
					id: 'accountHeaderFormBackground',
					selector: assembleSelector(
						mutateSelector({
							selector: [getRootSelectorFor({ itemId })[0]],
							operation: 'suffix',
							to_add: '#account-modal .ct-account-form',
						})
					),
				}),

				...handleBackgroundOptionFor({
					id: 'accountHeaderBackground',
					selector: assembleSelector(
						mutateSelector({
							selector: [getRootSelectorFor({ itemId })[0]],
							operation: 'suffix',
							to_add: '#account-modal',
						})
					),
				}),

				account_form_shadow: {
					selector: assembleSelector(
						mutateSelector({
							selector: [getRootSelectorFor({ itemId })[0]],
							operation: 'suffix',
							to_add: '#account-modal .ct-account-form',
						})
					),
					type: 'box-shadow',
					variable: 'box-shadow',
					// responsive: true,
				},

				account_modal_font_color: [
					{
						selector: '#account-modal .ct-account-form',
						variable: 'color',
						type: 'color:default',
					},

					{
						selector: '#account-modal .ct-account-form',
						variable: 'linkHoverColor',
						type: 'color:hover',
					},
				],

				account_modal_form_text_color: [
					{
						selector: '#account-modal form',
						variable: 'form-text-initial-color',
						type: 'color:default',
					},

					{
						selector: '#account-modal form',
						variable: 'form-text-focus-color',
						type: 'color:focus',
					},
				],

				account_modal_form_border_color: [
					{
						selector: '#account-modal form',
						variable: 'form-field-border-initial-color',
						type: 'color:default',
					},

					{
						selector: '#account-modal form',
						variable: 'form-field-border-focus-color',
						type: 'color:focus',
					},
				],

				account_modal_form_background_color: [
					{
						selector: '#account-modal form',
						variable: 'form-field-initial-background',
						type: 'color:default',
					},

					{
						selector: '#account-modal form',
						variable: 'form-field-focus-background',
						type: 'color:focus',
					},
				],

				account_close_button_color: [
					{
						selector: '#account-modal .ct-toggle-close',
						variable: 'icon-color',
						type: 'color:default',
					},

					{
						selector: '#account-modal .ct-toggle-close:hover',
						variable: 'icon-color',
						type: 'color:hover',
					},
				],

				account_close_button_border_color: [
					{
						selector:
							'#account-modal .ct-toggle-close[data-type="type-2"]',
						variable: 'toggle-button-border-color',
						type: 'color:default',
					},

					{
						selector:
							'#account-modal .ct-toggle-close[data-type="type-2"]:hover',
						variable: 'toggle-button-border-color',
						type: 'color:hover',
					},
				],

				account_close_button_shape_color: [
					{
						selector:
							'#account-modal .ct-toggle-close[data-type="type-3"]',
						variable: 'toggle-button-background',
						type: 'color:default',
					},

					{
						selector:
							'#account-modal .ct-toggle-close[data-type="type-3"]:hover',
						variable: 'toggle-button-background',
						type: 'color:hover',
					},
				],

				accountHeaderMargin: {
					selector: assembleSelector(getRootSelectorFor({ itemId })),
					type: 'spacing',
					variable: 'margin',
					responsive: true,
					important: true,
				},

				...typographyOption({
					id: 'account_label_font',
					selector: assembleSelector(
						mutateSelector({
							selector: getRootSelectorFor({ itemId }),
							operation: 'suffix',
							to_add: '.ct-label',
						})
					),
				}),

				// default state
				accountHeaderColor: [
					{
						selector: assembleSelector(
							getRootSelectorFor({ itemId })
						),
						variable: 'linkInitialColor',
						type: 'color:default',
						responsive: true,
					},

					{
						selector: assembleSelector(
							getRootSelectorFor({ itemId })
						),
						variable: 'linkHoverColor',
						type: 'color:hover',
						responsive: true,
					},
				],

				header_account_icon_color: [
					{
						selector: assembleSelector(
							getRootSelectorFor({ itemId })
						),
						variable: 'icon-color',
						type: 'color:default',
						responsive: true,
					},

					{
						selector: assembleSelector(
							getRootSelectorFor({ itemId })
						),
						variable: 'icon-hover-color',
						type: 'color:hover',
						responsive: true,
					},
				],

				// transparent state
				transparentAccountHeaderColor: [
					{
						selector: assembleSelector(
							mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'between',
								to_add: '[data-transparent-row="yes"]',
							})
						),

						variable: 'linkInitialColor',
						type: 'color:default',
						responsive: true,
					},

					{
						selector: assembleSelector(
							mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'between',
								to_add: '[data-transparent-row="yes"]',
							})
						),

						variable: 'linkHoverColor',
						type: 'color:hover',
						responsive: true,
					},
				],

				transparent_header_account_icon_color: [
					{
						selector: assembleSelector(
							mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'between',
								to_add: '[data-transparent-row="yes"]',
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
								operation: 'between',
								to_add: '[data-transparent-row="yes"]',
							})
						),
						variable: 'icon-hover-color',
						type: 'color:hover',
						responsive: true,
					},
				],

				// sticky state
				stickyAccountHeaderColor: [
					{
						selector: assembleSelector(
							mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'between',
								to_add: '[data-sticky*="yes"]',
							})
						),
						variable: 'linkInitialColor',
						type: 'color:default',
						responsive: true,
					},

					{
						selector: assembleSelector(
							mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'between',
								to_add: '[data-sticky*="yes"]',
							})
						),
						variable: 'linkHoverColor',
						type: 'color:hover',
						responsive: true,
					},
				],

				sticky_header_account_icon_color: [
					{
						selector: assembleSelector(
							mutateSelector({
								selector: getRootSelectorFor({ itemId }),
								operation: 'between',
								to_add: '[data-sticky*="yes"]',
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
								operation: 'between',
								to_add: '[data-sticky*="yes"]',
							})
						),
						variable: 'icon-hover-color',
						type: 'color:hover',
						responsive: true,
					},
				],
			}
		}
	}
)

ctEvents.on('ct:header:sync:item:account', ({ optionId, optionValue }) => {
	const selector = '[data-id="account"]'

	if (optionId === 'header_account_visibility') {
		updateAndSaveEl(selector, (el) =>
			responsiveClassesFor({ ...optionValue, desktop: true }, el)
		)
	}

	if (
		optionId === 'loggedin_account_label_visibility' ||
		optionId === 'loggedout_account_label_visibility'
	) {
		updateAndSaveEl(selector, (el) => {
			;[...el.querySelectorAll('.ct-label')].map((label) => {
				responsiveClassesFor(optionValue, label)
			})
		})
	}

	if (optionId === 'loggedin_label') {
		updateAndSaveEl(selector, (el) => {
			;[...el.querySelectorAll('.ct-label')].map((label) => {
				label.innerHTML = optionValue
			})
		})
	}

	if (optionId === 'login_label') {
		updateAndSaveEl(selector, (el) => {
			;[...el.querySelectorAll('.ct-label')].map((label) => {
				label.innerHTML = optionValue
			})
		})
	}

	if (
		optionId === 'loggedout_label_position' ||
		optionId === 'loggedin_label_position'
	) {
		updateAndSaveEl(
			selector,
			(el) => {
				if (!optionValue.desktop) {
					optionValue = {
						desktop: optionValue,
						mobile: optionValue,
					}
				}

				el.dataset.label = optionValue.desktop
			},
			{ onlyView: 'desktop' }
		)

		updateAndSaveEl(
			selector,
			(el) => {
				if (!optionValue.desktop) {
					optionValue = {
						desktop: optionValue,
						mobile: optionValue,
					}
				}

				el.dataset.label = optionValue.mobile
			},
			{ onlyView: 'mobile' }
		)
	}

	if (optionId === 'account_close_button_type') {
		let offcanvasModalClose = document.querySelector(
			'#account-modal .ct-toggle-close'
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
})
