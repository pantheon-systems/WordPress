import { handleBackgroundOptionFor } from 'blocksy-customizer-sync'
import ctEvents from 'ct-events'

ctEvents.on(
	'ct:customizer:sync:collect-variable-descriptors',
	(allVariables) => {
		allVariables.result = {
			...allVariables.result,
			newsletter_subscribe_title_color: {
				selector: '.ct-newsletter-subscribe-block',
				variable: 'heading-color',
				type: 'color:default',
				responsive: true,
			},

			newsletter_subscribe_content: [
				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'color',
					type: 'color:default',
				},

				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'linkHoverColor',
					type: 'color:hover',
				},
			],

			newsletter_subscribe_button: [
				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'buttonInitialColor',
					type: 'color:default',
				},

				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'buttonHoverColor',
					type: 'color:hover',
				},
			],

			newsletter_subscribe_input_font_color: [
				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'form-text-initial-color',
					type: 'color:default',
				},

				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'form-text-focus-color',
					type: 'color:focus',
				},
			],

			newsletter_subscribe_border_color: [
				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'form-field-border-initial-color',
					type: 'color:default',
				},

				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'form-field-border-focus-color',
					type: 'color:focus',
				},
			],

			newsletter_subscribe_input_background: [
				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'form-field-initial-background',
					type: 'color:default',
				},

				{
					selector: '.ct-newsletter-subscribe-block',
					variable: 'form-field-focus-background',
					type: 'color:focus',
				},
			],

			...handleBackgroundOptionFor({
				id: 'newsletter_subscribe_container_background',
				selector: '.ct-newsletter-subscribe-block',
				responsive: true,
			}),

			newsletter_subscribe_container_border: {
				selector: '.ct-newsletter-subscribe-block',
				variable: 'newsletter-container-border',
				type: 'border',
				responsive: true,
				skip_none: true,
			},

			newsletter_subscribe_shadow: {
				selector: '.ct-newsletter-subscribe-block',
				type: 'box-shadow',
				variable: 'box-shadow',
				responsive: true,
			},

			newsletter_subscribe_container_spacing: {
				selector: '.ct-newsletter-subscribe-block',
				type: 'spacing',
				variable: 'padding',
				responsive: true,
			},

			newsletter_subscribe_container_border_radius: {
				selector: '.ct-newsletter-subscribe-block',
				type: 'spacing',
				variable: 'border-radius',
				responsive: true,
			},
		}
	}
)
