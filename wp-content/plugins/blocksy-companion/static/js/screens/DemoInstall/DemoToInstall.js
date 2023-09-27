import {
	createElement,
	Component,
	useEffect,
	useState,
	useContext,
	createContext,
	Fragment,
} from '@wordpress/element'
import {
	Dialog,
	DialogOverlay,
	DialogContent,
} from '../../helpers/reach/dialog'

import Overlay from '../../helpers/Overlay'

import { Transition } from 'blocksy-options'
import { __ } from 'ct-i18n'
import cn from 'classnames'
import DashboardContext from '../../DashboardContext'
import { DemosContext } from '../DemoInstall'

import ModifyDemo from './Wizzard/ModifyDemo'
import ChildTheme from './Wizzard/ChildTheme'
import PickBuilder from './Wizzard/PickBuilder'
import Content from './Wizzard/Content'
import Plugins from './Wizzard/Plugins'
import DemoInstaller from './DemoInstaller'
import { getStepsForDemoConfiguration } from './Installer/useInstaller'

const DemoToInstall = ({ location, navigate }) => {
	const [showDemoToInstall, setShowDemoToInstall] = useState(true)

	const {
		installerBlockingReleased,
		demos_list,
		currentDemo,
		pluginsStatus,
		currentlyInstalledDemo: globalCurrentlyInstalledDemo,
		setCurrentDemo,
	} = useContext(DemosContext)

	const { is_child_theme } = useContext(DashboardContext)

	const [currentlyInstalledDemo, setCurrentlyInstalledDemo] = useState(
		globalCurrentlyInstalledDemo
	)

	const [demoConfiguration, setDemoConfiguration] = useState({
		builder: '',
		child_theme: false,
		plugins: [],
		content: {
			options: true,
			widgets: true,
			content: true,
			erase_content: true,
		},
	})

	const [currentConfigurationStep, setCurrentConfigurationStep] = useState(0)

	const [properDemoName, _] = (currentDemo || '').split(':')

	const configurationSteps = [
		'modify_demo',
		'child_theme',
		'builder',
		'plugins',
		'content',
		'installer',
	].filter((step) => {
		if (!currentDemo) {
			return false
		}

		if (step === 'modify_demo') {
			if (!currentlyInstalledDemo) {
				return false
			}

			if (currentlyInstalledDemo.demo.indexOf(properDemoName) === -1) {
				return false
			}
		}

		if (step === 'child_theme') {
			if (is_child_theme) {
				return false
			}
		}

		const demoVariations = demos_list
			.filter(({ name }) => name === properDemoName)
			.sort((a, b) => {
				if (a.builder < b.builder) {
					return -1
				}

				if (a.builder > b.builder) {
					return 1
				}

				return 0
			})

		if (step === 'plugins') {
			if (
				demoVariations
					.reduce(
						(currentPlugins, currentVariation) => [
							...currentPlugins,
							...(currentVariation.plugins || []),
						],
						[]
					)
					.filter((plugin) => !pluginsStatus[plugin]).length === 0
			) {
				return false
			}
		}

		if (step !== 'builder') {
			return true
		}

		return demoVariations.length > 1
	})

	const stepName = configurationSteps[currentConfigurationStep]

	useEffect(() => {
		if (!properDemoName) return
		if (currentDemo.indexOf(':hide') > -1) return

		const demoVariations = demos_list
			.filter(({ name }) => name === properDemoName)
			.sort((a, b) => {
				if (a.builder < b.builder) {
					return -1
				}

				if (a.builder > b.builder) {
					return 1
				}

				return 0
			})

		setCurrentConfigurationStep(0)
		setCurrentlyInstalledDemo(globalCurrentlyInstalledDemo)

		setDemoConfiguration({
			builder:
				demoVariations.length === 1 ? demoVariations[0].builder : null,

			child_theme: false,

			plugins: demoVariations[0].plugins.map((plugin) => ({
				plugin,
				enabled: true,
			})),

			content: {
				options: true,
				widgets: true,
				content: true,
				erase_content: true,
			},
		})
	}, [currentDemo])

	return (
		<Overlay
			items={currentDemo}
			isVisible={(currentDemo) =>
				currentDemo && currentDemo.indexOf(':hide') === -1
			}
			className={cn('ct-demo-modal', {
				'ct-demo-installer':
					stepName === 'installer' || stepName === 'modify_demo',
			})}
			onDismiss={() => {
				if (stepName === 'installer' && !installerBlockingReleased) {
					return
				}

				setCurrentDemo(`${properDemoName}:hide`)
			}}
			render={() => (
				<div className="ct-modal-content ct-demo-step-container">
					<div className="ct-current-step">
						<Transition
							items={stepName}
							from={{ opacity: 0 }}
							enter={{ opacity: 1 }}
							leave={{ opacity: 0 }}
							initial={false}
							config={(key, phase) => {
								return phase === 'leave'
									? {
											duration: 150,
									  }
									: {
											delay: 150,
											duration: 150,
									  }
							}}>
							{(stepName) => (props) => (
								<Fragment>
									{stepName === 'modify_demo' && (
										<ModifyDemo
											demoConfiguration={
												demoConfiguration
											}
											nextStep={() => {
												setCurrentConfigurationStep(
													Math.min(
														currentConfigurationStep +
															1,
														configurationSteps.length -
															1
													)
												)
											}}
											style={props}
										/>
									)}

									{stepName === 'child_theme' && (
										<ChildTheme
											style={props}
											demoConfiguration={
												demoConfiguration
											}
											setDemoConfiguration={
												setDemoConfiguration
											}
										/>
									)}

									{stepName === 'plugins' && (
										<Plugins
											demoConfiguration={
												demoConfiguration
											}
											style={props}
											setDemoConfiguration={
												setDemoConfiguration
											}
										/>
									)}

									{stepName === 'builder' && (
										<PickBuilder
											style={props}
											demoConfiguration={
												demoConfiguration
											}
											setDemoConfiguration={
												setDemoConfiguration
											}
										/>
									)}

									{stepName === 'content' && (
										<Content
											style={props}
											demoConfiguration={
												demoConfiguration
											}
											setDemoConfiguration={
												setDemoConfiguration
											}
										/>
									)}

									{stepName === 'installer' && (
										<DemoInstaller
											style={props}
											demoConfiguration={
												demoConfiguration
											}
										/>
									)}
								</Fragment>
							)}
						</Transition>
					</div>

					{stepName !== 'installer' && stepName !== 'modify_demo' && (
						<div className="ct-demo-step-controls">
							{currentConfigurationStep > 0 && (
								<button
									className="ct-demo-btn demo-back-btn"
									onClick={() => {
										setCurrentConfigurationStep(
											Math.max(
												currentConfigurationStep - 1,
												0
											)
										)
									}}>
									{__('Back', 'blocksy-companion')}
								</button>
							)}

							{configurationSteps.length > 2 && (
								<ul className="ct-steps-pills">
									{configurationSteps.map((step, index) =>
										index ===
										configurationSteps.length - 1 ? null : (
											<li
												className={cn({
													active: step === stepName,
												})}
												key={step}>
												{index + 1}
											</li>
										)
									)}
								</ul>
							)}

							<button
								className="ct-demo-btn demo-main-btn"
								disabled={
									stepName === 'content' &&
									getStepsForDemoConfiguration({
										demoConfiguration,
										pluginsStatus,
										is_child_theme,
									}).length === 0
								}
								onClick={() => {
									setCurrentConfigurationStep(
										Math.min(
											currentConfigurationStep + 1,
											configurationSteps.length - 1
										)
									)
								}}>
								{stepName === 'content'
									? __('Install', 'blocksy-companion')
									: __('Next', 'blocksy-companion')}
							</button>
						</div>
					)}
				</div>
			)}
		/>
	)
}

export default DemoToInstall
