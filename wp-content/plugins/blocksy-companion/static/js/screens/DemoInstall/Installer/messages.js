import { sprintf, __ } from 'ct-i18n'
import { getNameForPlugin } from '../Wizzard/Plugins'

export const getMessageForAction = (message, stepsDescriptors) => {
	const { action } = message

	if (action === 'complete') {
		return ''
	}

	if (action === 'import_install_child') {
		return __('copying child theme sources', 'blocksy-companion')
	}

	if (action === 'import_activate_child') {
		return __('activating child theme', 'blocksy-companion')
	}

	if (action === 'install_plugin') {
		return sprintf(
			__('installing plugin %s', 'blocksy-companion'),
			getNameForPlugin(message.name) || message.name
		)
	}

	if (action === 'activate_plugin') {
		return sprintf(
			__('activating plugin %s', 'blocksy-companion'),
			getNameForPlugin(message.name) || message.name
		)
	}

	if (action === 'download_demo_widgets') {
		return __('downloading demo widgets', 'blocksy-companion')
	}

	if (action === 'apply_demo_widgets') {
		return __('installing demo widgets', 'blocksy-companion')
	}

	if (action === 'download_demo_options') {
		return __('downloading demo options', 'blocksy-companion')
	}

	if (action === 'import_mods_images') {
		return __('importing images from customizer', 'blocksy-companion')
	}

	if (action === 'import_customizer_options') {
		return __('import customizer options', 'blocksy-companion')
	}

	if (action === 'activate_required_extensions') {
		return __('activating required extensions', 'blocksy-companion')
	}

	if (action === 'erase_previous_posts') {
		return __('removing previously installed posts', 'blocksy-companion')
	}

	if (action === 'erase_previous_terms') {
		return __('removing previously installed taxonomies', 'blocksy-companion')
	}

	if (action === 'erase_default_pages') {
		return __('removing default WordPress pages', 'blocksy-companion')
	}

	if (action === 'erase_customizer_settings') {
		return __('resetting customizer options', 'blocksy-companion')
	}

	if (action === 'erase_widgets_data') {
		return __('resetting widgets', 'blocksy-companion')
	}

	if (action === 'content_installer_progress') {
		if (!message.kind) {
			return ''
		}

		const total =
			stepsDescriptors.content.preliminary_data[`${message.kind}_count`]

		const processed = stepsDescriptors.content[`${message.kind}_count`]

		return `${Math.min(processed, total)} of ${total} ${
			{
				users: __('users', 'blocksy-companion'),
				term: __('terms', 'blocksy-companion'),
				media: __('images', 'blocksy-companion'),
				post: __('posts', 'blocksy-companion'),
				comment: __('comments', 'blocksy-companion')
			}[message.kind]
		}`
	}

	return ''
}
