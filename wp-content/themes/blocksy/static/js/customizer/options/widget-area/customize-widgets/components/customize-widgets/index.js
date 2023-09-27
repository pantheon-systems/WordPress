import { createElement } from '@wordpress/element'

/**
 * WordPress dependencies
 */
import { useState, useEffect, useRef, createPortal } from '@wordpress/element'
import { ShortcutProvider } from '@wordpress/keyboard-shortcuts'

/**
 * Internal dependencies
 */
import ErrorBoundary from '../error-boundary'
import SidebarBlockEditor from '../sidebar-block-editor'
import FocusControl from '../focus-control'
import SidebarControls from '../sidebar-controls'

import SidebarAdapter from '../sidebar-block-editor/sidebar-adapter'

let cacheForAdapters = {}

const CustomizeWidgets = ({ sidebarId }) => {
	const blockEditorSettings = window.blocksyWidgetsBlockEditorSettings
	const popoverRef = useRef()

	const sidebarControls = Object.values(wp.customize.control._value).filter(
		(v) => v.constructor.name === 'SidebarControl'
	)

	const activeSidebarControl = wp.customize.control(
		`sidebars_widgets[${sidebarId}]`
	)

	let sidebarAdapter = null

	if (!cacheForAdapters[sidebarId]) {
		cacheForAdapters[sidebarId] = new SidebarAdapter(
			activeSidebarControl.setting
		)
	}

	const activeSidebar = (
		<ErrorBoundary>
			<SidebarBlockEditor
				key={sidebarId}
				sidebarId={sidebarId}
				blockEditorSettings={blockEditorSettings}
				sidebar={cacheForAdapters[sidebarId]}
				inserter={activeSidebarControl.inserter}
				inspector={activeSidebarControl.inspector}
			/>
		</ErrorBoundary>
	)

	return (
		<ShortcutProvider>
			<SidebarControls
				sidebarControls={[]}
				activeSidebarControl={activeSidebarControl}>
				{activeSidebar}
			</SidebarControls>
		</ShortcutProvider>
	)
}

export default CustomizeWidgets
