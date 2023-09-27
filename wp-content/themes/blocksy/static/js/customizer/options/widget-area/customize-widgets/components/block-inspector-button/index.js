/**
 * WordPress dependencies
 */
import { useContext, createElement } from '@wordpress/element'
import { __ } from 'ct-i18n'
import { MenuItem } from '@wordpress/components'

import { PanelContext } from '../../../../../../options/components/PanelLevel'

function BlockInspectorButton({ closeMenu, ...props }) {
	const { panelsHelpers } = useContext(PanelContext)

	return (
		<MenuItem
			onClick={() => {
				panelsHelpers.openSecondLevel()
				// Then close the dropdown menu.
				closeMenu()
			}}
			{...props}>
			{__('Show more settings', 'blocksy')}
		</MenuItem>
	)
}

export default BlockInspectorButton
