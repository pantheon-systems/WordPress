import {
	createElement,
	Fragment,
	Component,
	useCallback,
	useRef,
	useEffect,
	useState,
} from '@wordpress/element'
import { registerPlugin, withPluginContext } from '@wordpress/plugins'
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/edit-post'
import { withSelect, withDispatch } from '@wordpress/data'
import { compose } from '@wordpress/compose'
import { IconButton, Button } from '@wordpress/components'
import { handleMetaboxValueChange, mountSync } from './editor/sync'

import ctEvents from 'ct-events'

import { __, sprintf } from 'ct-i18n'

import {
	OptionsPanel,
	getValueFromInput,
	PanelLevel,
	DeviceManagerProvider,
} from 'blocksy-options'

import { SVG, Path } from '@wordpress/primitives'

const closeSmall = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
		<Path d="M13 11.9l3.3-3.4-1.1-1-3.2 3.3-3.2-3.3-1.1 1 3.3 3.4-3.5 3.6 1 1L12 13l3.5 3.5 1-1z" />
	</SVG>
)

const starEmpty = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
		<Path
			fillRule="evenodd"
			d="M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z"
			clipRule="evenodd"
		/>
	</SVG>
)

const starFilled = (
	<SVG xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
		<Path d="M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z" />
	</SVG>
)

const BlocksyOptions = ({
	name,
	value,
	options,
	onChange,
	isActive,
	isPinnable = true,
	isPinned,
	togglePin,
	toggleSidebar,
	closeGeneralSidebar,
}) => {
	const containerRef = useRef()
	const parentContainerRef = useRef()
	const [values, setValues] = useState(null)

	useEffect(() => {
		document.body.classList[isActive ? 'add' : 'remove'](
			'blocksy-sidebar-active'
		)
	}, [isActive])

	const handleChange = ({ id: key, value: v }) => {
		const futureValue = {
			...(values || getValueFromInput(options, value || {})),
			[key]: v,
		}

		handleMetaboxValueChange(key, v)

		onChange(futureValue)
		setValues(futureValue)
	}

	useEffect(() => {
		ctEvents.on('ct:metabox:options:trigger-change', handleChange)

		return () => {
			ctEvents.off('ct:metabox:options:trigger-change', handleChange)
		}
	}, [])

	return (
		<Fragment>
			<PluginSidebarMoreMenuItem target="blocksy" icon="admin-customizer">
				{sprintf(
					__('%s Page Settings', 'blocksy'),
					ct_localizations.product_name
				)}
			</PluginSidebarMoreMenuItem>

			<PluginSidebar
				name={name}
				icon={
					<span
						style={{display: 'flex', width: '20px', height: '20px'}}
						dangerouslySetInnerHTML={{
							__html: ct_editor_localizations.options_panel_svg,
						}}
					/>
				}
				className="ct-components-panel"
				title={sprintf(
					__('%s Page Settings', 'blocksy'),
					ct_localizations.product_name
				)}>
				<div id="ct-page-options" ref={parentContainerRef}>
					<div className="ct-options-container" ref={containerRef}>
						<DeviceManagerProvider>
							<PanelLevel
								containerRef={containerRef}
								parentContainerRef={parentContainerRef}
								useRefsAsWrappers>
								<div className="ct-panel-options-header components-panel__header edit-post-sidebar-header">
									<strong>
										{sprintf(
											__('%s Page Settings', 'blocksy'),
											ct_localizations.product_name
										)}
									</strong>

									{isPinnable && (
										<Button
											icon={
												isPinned
													? starFilled
													: starEmpty
											}
											label={
												isPinned
													? __(
															'Unpin from toolbar',
															'blocksy'
													  )
													: __(
															'Pin to toolbar',
															'blocksy'
													  )
											}
											onClick={togglePin}
											isPressed={isPinned}
											aria-expanded={isPinned}
										/>
									)}

									<IconButton
										onClick={closeGeneralSidebar}
										icon={closeSmall}
										label={__('Close plugin', 'blocksy')}
									/>
								</div>
								<OptionsPanel
									onChange={(key, v) => {
										const futureValue = {
											...(values ||
												getValueFromInput(
													options,
													value || {}
												)),
											[key]: v,
										}

										handleMetaboxValueChange(key, v)

										onChange(futureValue)
										setValues(futureValue)
									}}
									value={
										values ||
										getValueFromInput(options, value || {})
									}
									options={options}
								/>
							</PanelLevel>
						</DeviceManagerProvider>
					</div>
				</div>
			</PluginSidebar>
		</Fragment>
	)
}

const BlocksyOptionsComposed = compose(
	withPluginContext((context, { name }) => ({
		sidebarName: `${context.name}/${name}`,
	})),

	withSelect((select, { sidebarName }) => {
		const value = select('core/editor').getEditedPostAttribute(
			'blocksy_meta'
		)

		const { getActiveGeneralSidebarName, isPluginItemPinned } = select(
			'core/edit-post'
		)

		return {
			isActive: getActiveGeneralSidebarName() === sidebarName,
			isPinned: isPluginItemPinned(sidebarName),
			value: Array.isArray(value) ? {} : value || {},
			options: ct_editor_localizations.post_options,
		}
	}),
	withDispatch((dispatch, { sidebarName }) => {
		const {
			closeGeneralSidebar,
			openGeneralSidebar,
			togglePinnedPluginItem,
		} = dispatch('core/edit-post')

		return {
			closeGeneralSidebar,
			togglePin: () => {
				togglePinnedPluginItem(sidebarName)
			},

			onChange: (blocksy_meta) => {
				dispatch('core/editor').editPost({
					blocksy_meta,
				})
			},
		}
	})
)(BlocksyOptions)

if (ct_editor_localizations.post_options) {
	registerPlugin('blocksy', {
		render: () => <BlocksyOptionsComposed name="blocksy" />,
	})
}
