import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'
import ctEvents from 'ct-events'

import NewsletterSubscribe from './NewsletterSubscribe'

ctEvents.on('ct:extensions:card', ({ CustomComponent, extension }) => {
	if (extension.name !== 'newsletter-subscribe') return
	CustomComponent.extension = NewsletterSubscribe
})
