import { createElement } from '@wordpress/element'
/**
 * External dependencies
 */
import { defaultTo } from 'lodash'

/**
 * WordPress dependencies
 */
import { store as coreStore } from '@wordpress/core-data'
import { useSelect } from '@wordpress/data'
import { useMemo, createPortal } from '@wordpress/element'
import {
	BlockList,
	BlockTools,
	BlockSelectionClearer,
	BlockInspector,
	CopyHandler,
	ObserveTyping,
	WritingFlow,
	BlockEditorKeyboardShortcuts,
	__unstableBlockSettingsMenuFirstItem,
	__unstableEditorStyles as EditorStyles,
} from '@wordpress/block-editor'
import { uploadMedia } from '@wordpress/media-utils'

import { store as interfaceStore } from '../more-menu/interface/store'

/**
 * Internal dependencies
 */
import BlockInspectorButton from '../block-inspector-button'
import Header from '../header'
import useInserter from '../inserter/use-inserter'
import SidebarEditorProvider from './sidebar-editor-provider'
import WelcomeGuide from '../welcome-guide'
import KeyboardShortcuts from '../keyboard-shortcuts'

import { ButtonBlockAppender } from '@wordpress/block-editor'

export default function SidebarBlockEditor({
	blockEditorSettings,
	sidebar,
	inserter,
	inspector,
}) {
	const [isInserterOpened, setIsInserterOpened] = useInserter(inserter)

	const {
		hasUploadPermissions,
		isFixedToolbarActive,
		keepCaretInsideBlock,
		isWelcomeGuideActive,
	} = useSelect((select) => {
		const { isFeatureActive } = select(interfaceStore)
		return {
			hasUploadPermissions: defaultTo(
				select(coreStore).canUser('create', 'media'),
				true
			),
			isFixedToolbarActive: isFeatureActive(
				'core/customize-widgets',
				'fixedToolbar'
			),
			keepCaretInsideBlock: isFeatureActive(
				'core/customize-widgets',
				'keepCaretInsideBlock'
			),
			isWelcomeGuideActive: isFeatureActive(
				'core/customize-widgets',
				'welcomeGuide'
			),
		}
	}, [])

	const settings = useMemo(() => {
		let mediaUploadBlockEditor

		if (hasUploadPermissions) {
			mediaUploadBlockEditor = ({ onError, ...argumentsObject }) => {
				uploadMedia({
					wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
					onError: ({ message }) => onError(message),
					...argumentsObject,
				})
			}
		}

		return {
			...blockEditorSettings,
			__experimentalSetIsInserterOpened: (a) => {
				return setIsInserterOpened(a)
			},
			mediaUpload: mediaUploadBlockEditor,
			hasFixedToolbar: isFixedToolbarActive,
			keepCaretInsideBlock,
			__unstableHasCustomAppender: true,
		}
	}, [
		hasUploadPermissions,
		blockEditorSettings,
		isFixedToolbarActive,
		keepCaretInsideBlock,
		setIsInserterOpened,
	])

	if (isWelcomeGuideActive) {
		return <WelcomeGuide sidebar={sidebar} />
	}

	return (
		<>
			<BlockEditorKeyboardShortcuts.Register />
			<KeyboardShortcuts.Register />

			<SidebarEditorProvider sidebar={sidebar} settings={settings}>
				<KeyboardShortcuts
					undo={sidebar.undo}
					redo={sidebar.redo}
					save={sidebar.save}
				/>

				<Header
					sidebar={sidebar}
					inserter={inserter}
					isInserterOpened={isInserterOpened}
					setIsInserterOpened={setIsInserterOpened}
					isFixedToolbarActive={isFixedToolbarActive}
				/>

				<CopyHandler>
					<BlockTools>
						<EditorStyles styles={settings.defaultEditorStyles} />
						<BlockSelectionClearer>
							<WritingFlow className="editor-styles-wrapper">
								<ObserveTyping>
									<BlockList
										renderAppender={ButtonBlockAppender}
									/>
								</ObserveTyping>
							</WritingFlow>
						</BlockSelectionClearer>
					</BlockTools>
				</CopyHandler>

				{createPortal(
					// This is a temporary hack to prevent button component inside <BlockInspector>
					// from submitting form when type="button" is not specified.
					<form onSubmit={(event) => event.preventDefault()}>
						<BlockInspector />
					</form>,
					document
						.querySelector(
							'.ct-tmp-panel-wrapper .ct-customizer-panel'
						)
						.lastElementChild.querySelector(
							'.customizer-panel-content'
						)
				)}
			</SidebarEditorProvider>

			<__unstableBlockSettingsMenuFirstItem>
				{({ onClose }) => (
					<BlockInspectorButton
						inspector={inspector}
						closeMenu={onClose}
					/>
				)}
			</__unstableBlockSettingsMenuFirstItem>
		</>
	)
}
