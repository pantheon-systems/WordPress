import './public-path'
import { createElement, Fragment, Component } from '@wordpress/element'
import ListPicker from './ListPicker'
import ctEvents from 'ct-events'

document.addEventListener('DOMContentLoaded', () =>
	ctEvents.on('blocksy:options:register', (opts) => {
		opts['blocksy-newsletter-subscribe'] = ListPicker
	})
)
