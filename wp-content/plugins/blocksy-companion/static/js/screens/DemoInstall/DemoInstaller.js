import { createElement, Component, Fragment } from '@wordpress/element'
import { __ } from 'ct-i18n'
import { Transition } from 'blocksy-options'

import InstallCompleted from './Installer/InstallCompleted'

import { useInstaller } from './Installer/useInstaller'
import Progress from './components/Progress'
import Error from './components/Error'

const DemoInstaller = ({ demoConfiguration, style }) => {
	const {
		isCompleted,
		isError,
		stepName,
		stepsDescriptors,
		lastMessage,
		progress,
	} = useInstaller(demoConfiguration)

	const screenName = isCompleted ? 'complete' : isError ? 'error' : 'progress'

	return (
		<div className="ct-demo-install" style={style}>
			<Transition
				initial
				items={screenName}
				from={{ opacity: 0 }}
				enter={[{ opacity: 1 }]}
				leave={[{ opacity: 0 }]}
				config={(key, phase) => {
					return phase === 'leave'
						? {
								duration: 300,
						  }
						: {
								delay: 300,
								duration: 300,
						  }
				}}>
				{(screenName) => (props) =>
					(
						<div style={props}>
							{screenName === 'complete' && <InstallCompleted />}
							{screenName === 'error' && <Error />}
							{screenName === 'progress' && (
								<Progress
									{...{
										stepName,
										stepsDescriptors,
										lastMessage,
										progress,
									}}
								/>
							)}
						</div>
					)}
			</Transition>
		</div>
	)
}

export default DemoInstaller
