import {
	createElement,
	Component,
	useEffect,
	useState,
	useContext,
	createContext,
	Fragment
} from '@wordpress/element'
import { __, sprintf } from 'ct-i18n'
import classnames from 'classnames'
import Checkbox from '../../../helpers/Checkbox'

const Content = ({
	demoConfiguration,
	setDemoConfiguration,
	currentDemo,
	style
}) => {
	return (
		<div style={style}>
			<i className="ct-demo-icon">
				<svg width="40" height="40" viewBox="0 0 40 40">
					<path d="M25,22.67a5,5,0,0,1-10,0H0V36a3.33,3.33,0,0,0,3.33,3.33H36.67A3.33,3.33,0,0,0,40,36V22.67Z" transform="translate(0 -0.67)" fill="#bdc8d7"/><rect x="2.5" y="14" width="35" height="3" rx="1.5" fill="#0c7ab3"/><rect x="5" y="7" width="30" height="3" rx="1.5" fill="#3497d3"/><rect x="7.5" width="25" height="3" rx="1.5" fill="#44acdf"/>
				</svg>
			</i>

			<h2>{__('Import Content', 'blocksy-companion')}</h2>

			<p>
				{__(
					'This will import posts, pages, comments, navigation menus, custom fields, terms and custom posts',
					'blocksy-companion'
				)}
			</p>

			{['options', 'widgets', 'content'].map(option => (
				<Checkbox
					checked={demoConfiguration.content[option]}
					onChange={() =>
						setDemoConfiguration({
							...demoConfiguration,
							content: {
								...demoConfiguration.content,
								[option]: !demoConfiguration.content[option]
							}
						})
					}
					key={option}>
					{option
						.split('_')
						.map(w => w.replace(/^\w/, c => c.toUpperCase()))
						.join(' ')}
				</Checkbox>
			))}

			<div className="ct-demo-erase">
				<Checkbox
					checked={demoConfiguration.content.erase_content}
					onChange={() =>
						setDemoConfiguration({
							...demoConfiguration,
							content: {
								...demoConfiguration.content,
								erase_content: !demoConfiguration.content
									.erase_content
							}
						})
					}>
					<div>
						{__('Clean Install', 'blocksy-companion')}
						<i>
							{__(
								'This option will remove the previous imported content and will perform a fresh and clean install.',
								'blocksy-companion'
							)}
						</i>
					</div>
				</Checkbox>
			</div>
		</div>
	)
}

export default Content
