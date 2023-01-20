import {
	createElement,
	Component,
	useEffect,
	useState,
	Fragment,
} from '@wordpress/element'

import EditCredentials from '../EditCredentials'
import { __, sprintf } from 'ct-i18n'

const useActivationWithRequirements = (extension, cb = () => {}) => {
	const [isLoading, setIsLoading] = useState(false)
	const [isEditingCredentials, setIsEditingCredentials] = useState(false)

	const toggleActivationState = async () => {
		const body = new FormData()

		body.append('ext', extension.name)
		body.append(
			'action',
			extension.__object
				? 'blocksy_extension_deactivate'
				: 'blocksy_extension_activate'
		)

		setIsLoading(true)

		try {
			await fetch(ctDashboardLocalizations.ajax_url, {
				method: 'POST',
				body,
			})

			cb()
		} catch (e) {}

		setIsLoading(false)
	}

	const handleActionWithRequirements = () => {
		if (extension.__object || extension.data.api_key) {
			toggleActivationState()
			return
		}

		setIsEditingCredentials(true)
	}

	return [
		isLoading,
		handleActionWithRequirements,
		<Fragment>
			{extension.__object && extension.data.api_key && (
				<button
					className="ct-button ct-config-btn"
					data-button="white"
					title="Edit Credentials"
					onClick={() => setIsEditingCredentials(true)}>
					{__('Configure', 'blocksy-companion')}
				</button>
			)}

			<EditCredentials
				isEditingCredentials={isEditingCredentials}
				setIsEditingCredentials={setIsEditingCredentials}
				extension={extension}
				onCredentialsValidated={() => {
					if (!extension.__object) {
						toggleActivationState()
					}

					setIsEditingCredentials(false)
				}}
			/>
		</Fragment>,
	]
}

export default useActivationWithRequirements
