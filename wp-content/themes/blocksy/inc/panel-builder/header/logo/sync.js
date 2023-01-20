import { typographyOption } from '../../../../static/js/customizer/sync/variables/typography'
import { updateAndSaveEl } from '../../../../static/js/customizer/sync'
import ctEvents from 'ct-events'
import {
	responsiveClassesFor,
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
} from '../../../../static/js/customizer/sync/helpers'

import { getCurrentScreen } from '../../../../static/js/frontend/helpers/current-screen'

const getVariables = ({ itemId, panelType }) => ({
	logoMaxHeight: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({ itemId, panelType }),
				operation: 'suffix',
				to_add: '.site-logo-container',
			})
		),
		variable: 'logo-max-height',
		responsive: true,
		unit: 'px',
	},

	...typographyOption({
		id: 'siteTitle',
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({ itemId, panelType }),
				operation: 'suffix',
				to_add: '.site-title',
			})
		),
	}),

	...typographyOption({
		id: 'siteTagline',
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({ itemId, panelType }),
				operation: 'suffix',
				to_add: '.site-description',
			})
		),
	}),

	headerLogoMargin: {
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
		type: 'spacing',
		variable: 'margin',
		responsive: true,
		important: true,
	},

	// default state
	siteTitleColor: [
		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'suffix',
					to_add: '.site-title',
				})
			),
			variable: 'linkInitialColor',
			type: 'color:default',
			responsive: true,
		},

		{
			selector: assembleSelector(
				mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'suffix',
					to_add: '.site-title',
				})
			),
			variable: 'linkHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	siteTaglineColor: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({ itemId, panelType }),
				operation: 'suffix',
				to_add: '.site-description',
			})
		),
		variable: 'color',
		type: 'color:default',
		responsive: true,
	},

	// transparent state
	transparentSiteTitleColor: [
		{
			selector: assembleSelector(
				mutateSelector({
					selector: mutateSelector({
						selector: getRootSelectorFor({ itemId, panelType }),
						operation: 'suffix',
						to_add: '.site-title',
					}),
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
					selector: mutateSelector({
						selector: getRootSelectorFor({ itemId, panelType }),
						operation: 'suffix',
						to_add: '.site-title',
					}),
					to_add: '[data-transparent-row="yes"]',
				})
			),

			variable: 'linkHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	transparentSiteTaglineColor: {
		selector: assembleSelector(
			mutateSelector({
				selector: mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'suffix',
					to_add: '.site-description',
				}),
				to_add: '[data-transparent-row="yes"]',
			})
		),

		variable: 'color',
		type: 'color:default',
		responsive: true,
	},

	// sticky state
	stickySiteTitleColor: [
		{
			selector: assembleSelector(
				mutateSelector({
					selector: mutateSelector({
						selector: getRootSelectorFor({ itemId, panelType }),
						operation: 'suffix',
						to_add: '.site-title',
					}),
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
					selector: mutateSelector({
						selector: getRootSelectorFor({ itemId, panelType }),
						operation: 'suffix',
						to_add: '.site-title',
					}),
					operation: 'between',
					to_add: '[data-sticky*="yes"]',
				})
			),
			variable: 'linkHoverColor',
			type: 'color:hover',
			responsive: true,
		},
	],

	stickySiteTaglineColor: {
		selector: assembleSelector(
			mutateSelector({
				selector: mutateSelector({
					selector: getRootSelectorFor({ itemId, panelType }),
					operation: 'suffix',
					to_add: '.site-description',
				}),
				operation: 'between',
				to_add: '[data-sticky*="yes"]',
			})
		),
		variable: 'color',
		type: 'color:default',
		responsive: true,
	},

	header_logo_horizontal_alignment: {
		selector: assembleSelector(getRootSelectorFor({ itemId, panelType })),
		variable: 'horizontal-alignment',
		responsive: true,
		unit: '',
	},

	// footer logo
	footer_logo_horizontal_alignment: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({
					itemId,
					panelType: 'footer',
				}),
				operation: 'replace-last',
				to_add: '[data-column="logo"]',
			})
		),
		variable: 'horizontal-alignment',
		responsive: true,
		unit: '',
	},

	footer_logo_vertical_alignment: {
		selector: assembleSelector(
			mutateSelector({
				selector: getRootSelectorFor({
					itemId,
					panelType: 'footer',
				}),
				operation: 'replace-last',
				to_add: '[data-column="logo"]',
			})
		),
		variable: 'vertical-alignment',
		responsive: true,
		unit: '',
	},
})

ctEvents.on(
	'ct:header:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['logo'] = ({ itemId }) =>
			getVariables({ itemId, panelType: 'header' })
	}
)

ctEvents.on(
	'ct:footer:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['logo'] = ({ itemId }) =>
			getVariables({ itemId, panelType: 'footer' })
	}
)

ctEvents.on('ct:header:sync:item:logo', ({ itemId, optionId, optionValue }) => {
	const selector = `[data-id="${itemId}"]`

	if (optionId === 'blogdescription') {
		updateAndSaveEl(selector, (el) => {
			el.querySelector('.site-description') &&
				(el.querySelector('.site-description').innerHTML = optionValue)
		})
	}

	if (optionId === 'blogname_visibility') {
		updateAndSaveEl(selector, (el) => {
			responsiveClassesFor(
				{ ...optionValue },
				el.querySelector('.site-title')
			)
		})
	}

	if (optionId === 'blogdescription_visibility') {
		updateAndSaveEl(selector, (el) => {
			responsiveClassesFor(
				{ ...optionValue },
				el.querySelector('.site-description')
			)
		})
	}

	if (optionId === 'logo_position') {
		updateAndSaveEl(
			selector,
			(el) => {
				if (!optionValue.desktop) {
					optionValue = {
						desktop: optionValue,
						mobile: optionValue,
					}
				}

				el.dataset.logo = optionValue.desktop
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

				el.dataset.logo = optionValue.mobile
			},
			{ onlyView: 'mobile' }
		)
	}
})

ctEvents.on('ct:footer:sync:item:logo', ({ itemId, optionId, optionValue }) => {
	const selector = `.ct-footer [data-id="${itemId}"]`
	const el = document.querySelector(selector)

	if (optionId === 'blogdescription') {
		el.querySelector('.site-description') &&
			(el.querySelector('.site-description').innerHTML = optionValue)
	}

	if (optionId === 'blogname_visibility') {
		responsiveClassesFor(
			{ ...optionValue },
			el.querySelector('.site-title')
		)
	}

	if (optionId === 'visibility') {
		responsiveClassesFor(optionValue, el)
	}

	if (optionId === 'blogdescription_visibility') {
		responsiveClassesFor(
			{ ...optionValue },
			el.querySelector('.site-description')
		)
	}

	if (optionId === 'logo_position') {
		el.dataset.logo = optionValue
	}
})
