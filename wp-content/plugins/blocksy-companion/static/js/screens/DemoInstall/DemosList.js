import {
	createElement,
	Component,
	useEffect,
	useState,
	createContext,
	useContext,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import classnames from 'classnames'
import { DemosContext } from '../DemoInstall'
import DashboardContext from '../../DashboardContext'
import { getNameForPlugin } from './Wizzard/Plugins'

const DemosList = () => {
	const {
		currentlyInstalledDemo,
		demos_list,
		setCurrentDemo,
		demo_error,
		setInstallerBlockingReleased,
	} = useContext(DemosContext)
	const { Link } = useContext(DashboardContext)

	return (
		<ul>
			{demos_list
				.filter(
					(v, i) =>
						demos_list.map(({ name }) => name).indexOf(v.name) === i
				)
				.map((demo) => (
					<li
						key={demo.name}
						className={classnames('ct-single-demo', {
							'ct-is-pro': demo.is_pro,
						})}>
						<figure>
							<img src={demo.screenshot} />

							<section>
								<h3>{__('Available for', 'blocksy-companion')}</h3>
								<div>
									{demos_list
										.filter(
											({ name }) =>
												name === demo.name || ''
										)

										.sort((a, b) => {
											if (a.builder < b.builder) {
												return -1
											}

											if (a.builder > b.builder) {
												return 1
											}

											return 0
										})
										.map(({ builder }) => (
											<span key={builder}>
												{getNameForPlugin(builder) ||
													'Gutenberg'}
											</span>
										))}
								</div>
							</section>

							{demo.is_pro && (
								<a onClick={(e) => e.preventDefault()} href="#">
									PRO
								</a>
							)}
						</figure>

						<div className="ct-demo-actions">
							<h4>{demo.name}</h4>

							<div>
								<a
									className="ct-button"
									target="_blank"
									href={demo.url}>
									{__('Preview', 'blocksy-companion')}
								</a>
								<button
									className="ct-button-primary"
									onClick={() => {
										setInstallerBlockingReleased(false)
										setCurrentDemo(demo.name)
									}}
									disabled={!!demo_error}>
									{currentlyInstalledDemo &&
									currentlyInstalledDemo.demo.indexOf(
										demo.name
									) > -1
										? __('Modify', 'blocksy-companion')
										: __('Import', 'blocksy-companion')}
								</button>
							</div>
						</div>
					</li>
				))}
		</ul>
	)
}

export default DemosList
