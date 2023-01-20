import { render, createElement, Component, Fragment } from '@wordpress/element'
import { onDocumentLoaded } from '../helpers'
import { __ } from 'ct-i18n'

onDocumentLoaded(() => {
	const darkModeSwitch = document.createElement('a')

	darkModeSwitch.classList.add('ct-dark-mode-switch')

	darkModeSwitch.innerHTML =
		'<span class="ct-night"><svg width="14" height="14" viewBox="0 0 30 30"><path d="M29.6,18.6C27.9,25.2,22,30,15,30C6.7,30,0,23.3,0,15C0,8,4.8,2.1,11.4,0.4c1-0.2,2-0.4,3-0.4c-0.4,1.3-0.7,2.6-0.7,4.1c0,6.8,5.5,12.3,12.3,12.3c1.4,0,2.8-0.2,4.1-0.7C30,16.7,29.8,17.7,29.6,18.6z"/></svg></span><span class="ct-day"><svg width="20" height="20" viewBox="0 0 30 30"><path d="M15,6.9c-4.5,0-8.1,3.6-8.1,8.1c0,4.5,3.6,8.1,8.1,8.1s8.1-3.6,8.1-8.1C23.1,10.5,19.5,6.9,15,6.9z M15,4.6c0.6,0,1.2-0.5,1.2-1.2V1.2C16.2,0.5,15.6,0,15,0c-0.6,0-1.2,0.5-1.2,1.2v2.3C13.8,4.1,14.4,4.6,15,4.6z M15,25.4c-0.6,0-1.2,0.5-1.2,1.2v2.3c0,0.6,0.5,1.2,1.2,1.2c0.6,0,1.2-0.5,1.2-1.2v-2.3C16.2,25.9,15.6,25.4,15,25.4z M24,7.7L25.6,6c0.5-0.5,0.5-1.2,0-1.6c-0.5-0.5-1.2-0.5-1.6,0L22.3,6c-0.5,0.5-0.5,1.2,0,1.6C22.8,8.1,23.5,8.1,24,7.7z M6,22.3L4.4,24c-0.5,0.5-0.5,1.2,0,1.6c0.5,0.5,1.2,0.5,1.6,0L7.7,24c0.5-0.5,0.5-1.2,0-1.6C7.2,21.9,6.5,21.9,6,22.3z M4.6,15c0-0.6-0.5-1.2-1.2-1.2H1.2C0.5,13.8,0,14.4,0,15c0,0.6,0.5,1.2,1.2,1.2h2.3C4.1,16.2,4.6,15.6,4.6,15z M28.8,13.8h-2.3c-0.6,0-1.2,0.5-1.2,1.2c0,0.6,0.5,1.2,1.2,1.2h2.3c0.6,0,1.2-0.5,1.2-1.2C30,14.4,29.5,13.8,28.8,13.8z M6,7.7c0.5,0.5,1.2,0.5,1.6,0c0.5-0.5,0.5-1.2,0-1.6L6,4.4c-0.5-0.5-1.2-0.5-1.6,0C3.9,4.8,3.9,5.6,4.4,6L6,7.7z M24,22.3c-0.5-0.5-1.2-0.5-1.6,0c-0.5,0.5-0.5,1.2,0,1.6l1.6,1.6c0.5,0.5,1.2,0.5,1.6,0c0.5-0.5,0.5-1.2,0-1.6L24,22.3z"/></svg></span>'

	darkModeSwitch.addEventListener('click', (e) => {
		e.preventDefault()

		wp.customize('customizer_color_scheme').set(
			wp.customize('customizer_color_scheme')() === 'yes' ? 'no' : 'yes'
		)
	})

	document
		.querySelector('#customize-footer-actions')
		.appendChild(darkModeSwitch)
})
