import { createElement, Component, Fragment } from '@wordpress/element'

import { sprintf, __ } from 'ct-i18n'
import classnames from 'classnames'

import { getMessageForAction } from '../Installer/messages'

const Progress = ({ stepName, stepsDescriptors, lastMessage, progress }) => {
	return (
		<Fragment>
			<i className="ct-demo-icon">
				<svg width="40" height="40" viewBox="0 0 50 50">
					<path
						class="g1"
						d="M47,38.8c0.3-1,0.5-2,0.5-3.1c0-1.1-0.2-2.1-0.5-3.1l0.2-0.1l1.8-1.7l-1.8-3.1l-2.3,0.7l-0.2,0.1c-1.4-1.5-3.3-2.7-5.4-3.1V25l-0.6-2.4h-3.5L34.5,25v0.3c-2.1,0.5-4,1.6-5.4,3.1l-0.2-0.1l-2.3-0.7l-1.8,3.1l1.7,1.7l0.2,0.1c-0.3,1-0.5,2-0.5,3.1c0,1.1,0.2,2.1,0.5,3.1l-0.2,0.1l-1.8,1.7l1.8,3.1l2.3-0.7l0.2-0.1c1.4,1.5,3.3,2.7,5.4,3.1v0.3l0.6,2.4h3.5l0.6-2.4V46c2.1-0.5,4-1.6,5.4-3.1l0.2,0.1l2.3,0.7l1.8-3.1l-1.7-1.7L47,38.8z M36.9,41.5c-3.3,0-5.9-2.6-5.9-5.9s2.6-5.9,5.9-5.9s5.9,2.6,5.9,5.9S40.1,41.5,36.9,41.5z"
					/>
					<path
						class="g2"
						d="M21.2,32.2c0.2-0.8,0.4-1.7,0.4-2.5c0-0.9-0.1-1.7-0.4-2.5l0.3-0.2l1.7-1.7l-1.8-3.1L19.1,23l-0.3,0.2c-1.2-1.2-2.7-2.1-4.4-2.5v-0.3l-0.6-2.4h-3.5l-0.6,2.4v0.3c-1.7,0.4-3.2,1.3-4.4,2.5L5.1,23l-2.3-0.7L1,25.4L2.7,27L3,27.2c-0.2,0.8-0.4,1.7-0.4,2.5c0,0.9,0.1,1.7,0.4,2.5l-0.3,0.1L1,34.1l1.8,3.1l2.3-0.7l0.3-0.1c1.2,1.2,2.7,2.1,4.4,2.5v0.3l0.6,2.4h3.5l0.6-2.4v-0.3c1.7-0.4,3.2-1.3,4.4-2.5l0.3,0.1l2.3,0.7l1.8-3.1l-1.7-1.7L21.2,32.2z M12.1,34.4c-2.6,0-4.7-2.1-4.7-4.7S9.5,25,12.1,25s4.7,2.1,4.7,4.7S14.7,34.4,12.1,34.4z"
					/>
					<path
						class="g3"
						d="M37.7,15.7c0.2-0.8,0.4-1.7,0.4-2.5c0-0.9-0.1-1.7-0.4-2.5l0.3-0.2l1.7-1.7l-1.8-3.1l-2.3,0.7l-0.3,0.2c-1.2-1.2-2.7-2.1-4.4-2.5V3.8l-0.6-2.4h-3.5l-0.6,2.4v0.3c-1.7,0.4-3.2,1.3-4.4,2.5l-0.3-0.2l-2.3-0.7l-1.8,3.1l1.7,1.7l0.3,0.2c-0.2,0.8-0.4,1.7-0.4,2.5c0,0.9,0.1,1.7,0.4,2.5l-0.3,0.1l-1.7,1.7l1.8,3.1l2.3-0.7l0.3-0.1c1.2,1.2,2.7,2.1,4.4,2.5v0.3l0.6,2.4h3.5l0.6-2.4v-0.3c1.7-0.4,3.2-1.3,4.4-2.5l0.3,0.1l2.3,0.7l1.8-3.1L38,15.9L37.7,15.7z M28.6,17.9c-2.6,0-4.7-2.1-4.7-4.7s2.1-4.7,4.7-4.7s4.7,2.1,4.7,4.7S31.2,17.9,28.6,17.9z"
					/>
				</svg>
			</i>

			<h2>
				{__('Installing', 'blocksy-companion')}
				...
			</h2>

			<p>
				{__(
					"Please be patient and don't refresh this page, the import process may take a while, this also depends on your server.",
					'blocksy-companion'
				)}
			</p>

			<div className="ct-progress-info">
				{stepsDescriptors[stepName].title}
				{lastMessage &&
				getMessageForAction(lastMessage, stepsDescriptors)
					? `: ${getMessageForAction(lastMessage, stepsDescriptors)}`
					: ''}
				<span>{Math.round(progress)}%</span>
			</div>

			<div
				style={{
					'--progress': `${progress}%`,
				}}
				className="ct-installer-progress">
				<div />
			</div>
		</Fragment>
	)
}

export default Progress
