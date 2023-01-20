/**
 * WordPress dependencies
 */
import { __ } from 'ct-i18n'

export const textFormattingShortcuts = [
	{
		keyCombination: { modifier: 'primary', character: 'b' },
		description: __('Make the selected text bold.', 'blocksy'),
	},
	{
		keyCombination: { modifier: 'primary', character: 'i' },
		description: __('Make the selected text italic.', 'blocksy'),
	},
	{
		keyCombination: { modifier: 'primary', character: 'k' },
		description: __('Convert the selected text into a link.', 'blocksy'),
	},
	{
		keyCombination: { modifier: 'primaryShift', character: 'k' },
		description: __('Remove a link.', 'blocksy'),
	},
	{
		keyCombination: { modifier: 'primary', character: 'u' },
		description: __('Underline the selected text.', 'blocksy'),
	},
]
