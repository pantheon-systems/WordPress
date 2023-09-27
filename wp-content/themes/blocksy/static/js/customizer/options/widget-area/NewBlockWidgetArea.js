import {
	createPortal,
	useContext,
	useState,
	useRef,
	useEffect,
	createElement,
} from '@wordpress/element'
import { SlotFillProvider, Popover } from '@wordpress/components'
import { removeFilter } from '@wordpress/hooks'

import {
	registerCoreBlocks,
	__experimentalGetCoreBlocks,
} from '@wordpress/block-library'

import { __ } from 'ct-i18n'

import {
	registerLegacyWidgetBlock,
	registerLegacyWidgetVariations,
	registerWidgetGroupBlock,
} from '@wordpress/widgets'

import {
	setFreeformContentHandlerName,
	store as blocksStore,
} from '@wordpress/blocks'

import { dispatch } from '@wordpress/data'

import CustomizeWidgets from './customize-widgets/components/customize-widgets'
import './customize-widgets/filters'

import useClearSelectedBlock from './customize-widgets/components/customize-widgets/use-clear-selected-block'

import { PanelContext } from '../../../options/components/PanelLevel'

const NewBlockWidgetArea = ({
	option: { sidebarId = 'ct-footer-sidebar-1' },
}) => {
	const blockEditorSettings = window.blocksyWidgetsBlockEditorSettings

	const { panelsState, panelsDispatch } = useContext(PanelContext)

	const [isMounted, setIsMounted] = useState(null)

	const popoverRef = useRef()
	const containerRef = useRef()

	useEffect(() => {
		removeFilter('editor.BlockEdit', 'core/customize-widgets/block-edit')
	}, [])

	const activeSidebarControl = wp.customize.control(
		`sidebars_widgets[${sidebarId}]`
	)

	useClearSelectedBlock(activeSidebarControl, popoverRef, containerRef)

	useEffect(() => {
		panelsDispatch({
			type: 'PANEL_RECEIVE_META',
			payload: {
				secondLevelTitleLabel: __('Block Settings', 'blocksy'),
			},
		})

		setTimeout(() => {
			setIsMounted(true)
		}, 1000)
	}, [])

	if (!document.querySelector('.ct-tmp-panel-actions')) {
		document.body.insertAdjacentHTML(
			'beforeend',
			'<div class="ct-tmp-panel-actions"></div>'
		)
	}

	const popover = createPortal(
		<div className="customize-widgets-popover" ref={popoverRef}>
			<Popover.Slot />
		</div>,
		document.querySelector('.ct-tmp-panel-actions')
	)

	if (!isMounted) {
		return (
			<div className="ct-option-widget-area ct-loading">
				<svg
					width="15"
					height="15"
					viewBox="0 0 100 100"
					className="ct-loader">
					<g transform="translate(50,50)">
						<g transform="scale(1)">
							<circle cx="0" cy="0" r="50" fill="#687c93" />
							<circle
								cx="0"
								cy="-26"
								r="12"
								fill="#ffffff"
								transform="rotate(161.634)">
								<animateTransform
									attributeName="transform"
									type="rotate"
									calcMode="linear"
									values="0 0 0;360 0 0"
									keyTimes="0;1"
									dur="1s"
									begin="0s"
									repeatCount="indefinite"
								/>
							</circle>
						</g>
					</g>
				</svg>
			</div>
		)
	}

	return (
		<SlotFillProvider>
			<div className="customize-control-sidebar_block_editor ct-option-widget-area">
				<CustomizeWidgets sidebarId={sidebarId} key={sidebarId} />
				{popover}
			</div>
		</SlotFillProvider>
	)
}

export default NewBlockWidgetArea
