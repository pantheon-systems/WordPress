import {
	createElement,
	Component,
	useEffect,
	useState,
	useContext,
	createContext,
	Fragment,
} from '@wordpress/element'
import { __ } from 'ct-i18n'
import classnames from 'classnames'
import { DemosContext } from '../../DemoInstall'
import DashboardContext from '../../../DashboardContext'

const ModifyDemo = ({ style, nextStep }) => {
	const { is_child_theme } = useContext(DashboardContext)
	const {
		setCurrentlyInstalledDemo,
		setCurrentDemo,
		currentDemo,
		demos_list,
	} = useContext(DemosContext)

	const [currentStep, setCurrentStep] = useState(0)

	// idle | loading | done
	const [runningState, setRunningState] = useState('idle')

	const [properDemoName, _] = (currentDemo || '').split(':')

	const demoVariations = demos_list.filter(
		({ name }) => name === properDemoName
	)

	const stepsDescriptors = {
		erase_content: {
			title: __('Erase content', 'blocksy-companion'),
			query_string: `action=blocksy_demo_erase_content&wp_customize=on`,
		},

		deactivate_demo_plugins: {
			title: __('Deactivate demo plugins', 'blocksy-companion'),
			query_string: `action=blocksy_demo_deactivate_plugins&plugins=${demoVariations[0].plugins.join(
				':'
			)}`,
		},

		deregister_current_demo: {
			title: __('Erase content', 'blocksy-companion'),
			query_string: `action=blocksy_demo_deregister_current_demo`,
		},
	}

	const stepsForConfiguration = [
		'erase_content',
		'deactivate_demo_plugins',
		'deregister_current_demo',
	]

	const stepName = stepsForConfiguration[currentStep]

	const fireOffNextStep = () => {
		const stepDescriptor = stepsDescriptors[stepName]

		var evtSource = new EventSource(
			`${ctDashboardLocalizations.ajax_url}?${stepDescriptor.query_string}`
		)

		evtSource.onmessage = (e) => {
			var data = JSON.parse(e.data)

			if (data.action === 'complete') {
				evtSource && evtSource.close && evtSource.close()

				if (currentStep === stepsForConfiguration.length - 1) {
					setRunningState('done')
					return
				}

				setCurrentStep(
					Math.min(stepsForConfiguration.length - 1, currentStep + 1)
				)
			}
		}
	}

	useEffect(() => {
		if (currentStep === 0) return

		if (runningState === 'done') {
			return
		}

		fireOffNextStep()
	}, [stepName])

	return (
		<div className="ct-modify-demo" style={style}>
			<i className="ct-demo-icon">
				<svg width="36" height="36" viewBox="0 0 40 40">
					<path
						d="M5.71,40a1,1,0,0,1-1-1V21.59a1,1,0,0,1,1.91,0V39.05A1,1,0,0,1,5.71,40Zm1-31.83V1.07A1,1,0,0,0,5.71,0a1,1,0,0,0-1,1.07v7.1a1,1,0,0,0,1,1.07A1,1,0,0,0,6.67,8.17ZM21,39.05V34.29a1,1,0,1,0-1.9,0v4.76a1,1,0,1,0,1.9,0Zm0-18.14V1a1,1,0,1,0-1.9,0V20.91a1,1,0,1,0,1.9,0ZM35.24,39.05V26.35a1,1,0,0,0-1.91,0v12.7a1,1,0,0,0,1.91,0Zm0-26.25V1a1,1,0,1,0-1.91,0V12.8a1,1,0,1,0,1.91,0Z"
						transform="translate(-0.71)"
						fill="#dae3e8"
					/>
					<path
						d="M5.71,18.06a5,5,0,1,1,5-5A5,5,0,0,1,5.71,18.06ZM20,30.76a5,5,0,1,1,5-5A5,5,0,0,1,20,30.76Zm14.29-7.93a5,5,0,1,1,5-5A5,5,0,0,1,34.29,22.83Z"
						transform="translate(-0.71)"
						fill="#0c7ab3"
					/>
				</svg>
			</i>

			{runningState === 'idle' && (
				<h2>{__('This starter site is already installed', 'blocksy-companion')}</h2>
			)}
			{runningState === 'loading' && <h2>Removing starter site...</h2>}
			{runningState === 'done' && (
				<Fragment>
					<h2>{__('Starter Site Removed', 'blocksy-companion')}</h2>

					<div className="ct-modify-actions">
						<button
							className="ct-demo-btn ct-dismiss"
							onClick={(e) => {
								e.preventDefault()
								setCurrentDemo(`${properDemoName}:hide`)
							}}>
							{__('Dismiss', 'blocksy-companion')}
						</button>
					</div>
				</Fragment>
			)}

			{runningState === 'idle' && (
				<Fragment>
					<p>
						{__('What steps do you want to perform next?', 'blocksy-companion')}
					</p>

					<div className="ct-modify-actions">
						<button
							className="ct-demo-btn demo-remove"
							onClick={(e) => {
								setRunningState('loading')
								e.preventDefault()
								fireOffNextStep()
								setCurrentlyInstalledDemo()
							}}>
							{__('Remove', 'blocksy-companion')}
						</button>

						<button
							className="ct-demo-btn"
							onClick={(e) => {
								e.preventDefault()
								nextStep()
							}}>
							{__('Reinstall', 'blocksy-companion')}
						</button>
					</div>
				</Fragment>
			)}
		</div>
	)
}

export default ModifyDemo
