import {
	createElement,
	Component,
	useEffect,
	useState,
	useRef,
	createContext,
	useContext,
	Fragment
} from '@wordpress/element'

import { __ } from 'ct-i18n'
import cn from 'classnames'

import DashboardContext from '../../../DashboardContext'

const InstallCompleted = () => {
	const { home_url, customizer_url } = useContext(DashboardContext)

	return (
		<div className="ct-install-success">
			<h2>{__('Starter Site Imported Successfully', 'blocksy-companion')}</h2>

			<p>
				{__(
					'Now you can view your website or start customizing it',
					'blocksy-companion'
				)}
			</p>

			<div>
				<a href={customizer_url} className="ct-button">
					{__('Customize', 'blocksy-companion')}
				</a>

				<a
					href={home_url}
					target="_blank"
					className="ct-button-primary">
					{__('View site', 'blocksy-companion')}
				</a>
			</div>
		</div>
	)
}

export default InstallCompleted
