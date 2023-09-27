import { typographyOption } from '../../../../static/js/customizer/sync/variables/typography'
import ctEvents from 'ct-events'
import {
	responsiveClassesFor,
	getRootSelectorFor,
	assembleSelector,
	mutateSelector,
} from '../../../../static/js/customizer/sync/helpers'

ctEvents.on(
	'ct:footer:sync:collect-variable-descriptors',
	(variableDescriptors) => {
		variableDescriptors['copyright'] = ({ itemId }) => ({
			...typographyOption({
				id: 'copyrightFont',
				selector: assembleSelector(
					getRootSelectorFor({ itemId, panelType: 'footer' })
				),
			}),

			copyrightColor: [
				{
					selector: assembleSelector(
						getRootSelectorFor({ itemId, panelType: 'footer' })
					),
					variable: 'color',
					type: 'color:default',
					responsive: true,
				},

				{
					selector: assembleSelector(
						getRootSelectorFor({ itemId, panelType: 'footer' })
					),
					variable: 'linkInitialColor',
					type: 'color:link_initial',
					responsive: true,
				},

				{
					selector: assembleSelector(
						getRootSelectorFor({ itemId, panelType: 'footer' })
					),
					variable: 'linkHoverColor',
					type: 'color:link_hover',
					responsive: true,
				},
			],

			footerCopyrightAlignment: {
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'replace-last',
						to_add: '[data-column="copyright"]',
					})
				),
				variable: 'horizontal-alignment',
				responsive: true,
				unit: '',
			},

			footerCopyrightVerticalAlignment: {
				selector: assembleSelector(
					mutateSelector({
						selector: getRootSelectorFor({
							itemId,
							panelType: 'footer',
						}),
						operation: 'replace-last',
						to_add: '[data-column="copyright"]',
					})
				),
				variable: 'vertical-alignment',
				responsive: true,
				unit: '',
			},

			copyrightMargin: {
				selector: assembleSelector(
					getRootSelectorFor({ itemId, panelType: 'footer' })
				),
				type: 'spacing',
				variable: 'margin',
				responsive: true,
				important: true,
			},
		})
	}
)

ctEvents.on(
	'ct:footer:sync:item:copyright',
	({ itemId, optionId, optionValue }) => {
		const selector = `[data-id="${itemId}"]`
		let el = document.querySelector(selector)

		if (optionId === 'copyright_text') {
			el.innerHTML = optionValue
				.replace('{current_year}', new Date().getFullYear())
				.replace(
					'{theme_author}',
					ct_localizations.customizer_sync.theme_author
				)
				.replace(
					'{site_title}',
					ct_localizations.customizer_sync.site_title
				)
		}

		if (optionId === 'footer_copyright_visibility') {
			responsiveClassesFor(optionValue, el)
		}
	}
)
