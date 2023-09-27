import { createElement, Component, Fragment } from '@wordpress/element'

import { __ } from 'ct-i18n'

const Error = () => {
	return (
		<Fragment>
			<i className="ct-demo-icon">
				<svg width="37" height="37" viewBox="0 0 40 40">
					<path fill="#BDC8D7" d="M30.7,25.4L14.6,9.3c0.7-2.5,0-5.2-1.9-7.2c-2.4-2.3-6-2.7-8.8-1.3l4.5,4.5L7.9,7.9L5.3,8.4L0.8,3.9c-1.5,2.8-1,6.4,1.3,8.7c2,2,4.7,2.6,7.2,1.9l16.1,16.1c-0.7,2.5,0,5.2,1.9,7.2c2.3,2.3,5.9,2.8,8.7,1.3l-4.5-4.5L32,32l2.6-0.5l4.5,4.5c1.5-2.8,1-6.4-1.3-8.7C35.9,25.4,33.1,24.7,30.7,25.4z"/>
					<polygon fill="#44ACDF" points="34.6,11.3 39.8,3.7 36.3,0.2 28.7,5.4 28.7,7.8 11.8,24.7 15.3,28.2 32.2,11.3 "/>
					<path fill="#0C7AB3" d="M18.4,27.5l-5.9-5.9c-0.4-0.4-1-0.4-1.4,0s-0.4,1,0,1.4l0,0L0.7,33.5c-0.7,0.7-0.7,1.7,0,2.3l3.5,3.5c0.7,0.7,1.7,0.7,2.3,0L17,28.9l0,0c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3C18.8,28.5,18.8,27.9,18.4,27.5z"/>
				</svg>
			</i>

			<h2>{__('Can\'t Import Starter Site', 'blocksy-companion')}</h2>

			<p>{__('Unfortunately, your hosting configuration doesn\'t meet the minimum requirements for importing a starter site.', 'blocksy-companion')}</p>

			<a
				href="https://creativethemes.com/blocksy/docs/troubleshooting/starter-site-import-stuck-at-xx/"
				className="ct-demo-btn"
				target="_blank">
				More Information
			</a>
		</Fragment>
	)
}

export default Error