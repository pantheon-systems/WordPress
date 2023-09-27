import ctEvents from 'ct-events'

ctEvents.on(
	'ct:customizer:sync:collect-variable-descriptors',
	(allVariables) => {
		allVariables.result = {
			...allVariables.result,
			cookieContentColor: [
				{
					selector: '.cookie-notification',
					variable: 'color',
					type: 'color:default',
				},

				{
					selector: '.cookie-notification',
					variable: 'colorHover',
					type: 'color:hover',
				},
			],

			cookieBackground: {
				selector: '.cookie-notification',
				variable: 'backgroundColor',
				type: 'color',
			},

			cookieButtonText: [
				{
					selector: '.cookie-notification .ct-cookies-accept-button',
					variable: 'buttonTextInitialColor',
					type: 'color:default',
				},

				{
					selector: '.cookie-notification .ct-cookies-accept-button',
					variable: 'buttonTextHoverColor',
					type: 'color:hover',
				},
			],

			cookieButtonBackground: [
				{
					selector: '.cookie-notification .ct-cookies-accept-button',
					variable: 'buttonInitialColor',
					type: 'color:default',
				},

				{
					selector: '.cookie-notification .ct-cookies-accept-button',
					variable: 'buttonHoverColor',
					type: 'color:hover',
				},
			],

			cookieDeclineButtonText: [
				{
					selector: '.cookie-notification .ct-cookies-decline-button',
					variable: 'buttonTextInitialColor',
					type: 'color:default',
				},

				{
					selector: '.cookie-notification .ct-cookies-decline-button',
					variable: 'buttonTextHoverColor',
					type: 'color:hover',
				},
			],

			cookieDeclineButtonBackground: [
				{
					selector: '.cookie-notification .ct-cookies-decline-button',
					variable: 'buttonInitialColor',
					type: 'color:default',
				},

				{
					selector: '.cookie-notification .ct-cookies-decline-button',
					variable: 'buttonHoverColor',
					type: 'color:hover',
				},
			],

			cookieMaxWidth: {
				selector: '.cookie-notification',
				variable: 'maxWidth',
				unit: 'px',
			},
		}
	}
)
