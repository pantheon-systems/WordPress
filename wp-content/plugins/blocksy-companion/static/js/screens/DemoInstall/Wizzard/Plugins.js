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
import Checkbox from '../../../helpers/Checkbox'

export const getPluginsMap = (plugins) => ({
	'stackable-ultimate-gutenberg-blocks': 'Stackable - Gutenberg Blocks',
	'wpforms-lite': 'WPForms - Contact Form',
	woocommerce: 'WooCommerce',
	elementor: 'Elementor',
	brizy: 'Brizy',
	getwid: 'Getwid',
	'simply-gallery-block': 'SimpLy Gallery Block & Lightbox',
	'recipe-card-blocks-by-wpzoom': 'Recipe Card Blocks by WPZOOM',
	'map-block-gutenberg': 'Map Block for Google Maps',
	'mb-custom-post-type': 'MB Custom Post Types & Custom Taxonomies',
	leadin: 'HubSpot',
	'block-slider': 'Block Slider',
	'ht-slider-for-elementor': 'HT Slider For Elementor',
	'modula-best-grid-gallery': 'Modula - Image Gallery',
})

export const getNameForPlugin = (plugin) => {
	return (getPluginsMap()[plugin] || plugin).replace(/\b\w/, (v) =>
		v.toUpperCase()
	)
}

const Plugins = ({ demoConfiguration, setDemoConfiguration, style }) => {
	const { currentDemo, demos_list, pluginsStatus, setCurrentDemo } =
		useContext(DemosContext)

	const [properDemoName, _] = (currentDemo || '').split(':')

	const demosCollection = demos_list.filter(
		({ name }) => name === properDemoName || ''
	)

	return (
		<div style={style}>
			<div className="ct-demo-plugins">
				<i className="ct-demo-icon">
					<svg width="40" height="40" viewBox="0 0 40 40">
						<path
							fill="#0C7AB3"
							d="M20,0v7.6c0,0.3-0.2,0.5-0.5,0.5h-1.5c0,0,0,0,0,0c0,0,0,0-0.1,0c0,0,0,0-0.1,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c-0.5-0.7-1.3-1.1-2.1-1.1c-1.5,0-2.6,1.2-2.6,2.6c0,1.5,1.2,2.6,2.6,2.6c0.8,0,1.6-0.4,2.1-1.1c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0.1,0c0,0,0,0,0.1,0c0,0,0,0,0,0h1.5c0,0,0,0,0,0c0.3,0,0.5,0.2,0.5,0.5V20h8.1v-0.8c-0.8-0.7-1.3-1.7-1.3-2.8c0-2,1.7-3.7,3.7-3.7c2,0,3.7,1.7,3.7,3.7c0,1.1-0.5,2.1-1.3,2.8V20H40C40,9,31,0,20,0z"
						/>
						<path
							fill="#3497D3"
							d="M20,40v-7.6c0-0.3,0.2-0.5,0.5-0.5h1.5c0,0,0,0,0,0c0,0,0,0,0.1,0c0,0,0,0,0.1,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0.5,0.7,1.3,1.1,2.1,1.1c1.5,0,2.6-1.2,2.6-2.6c0-1.5-1.2-2.6-2.6-2.6c-0.8,0-1.6,0.4-2.1,1.1c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0,0,0c0,0,0,0-0.1,0c0,0,0,0-0.1,0c0,0,0,0,0,0h-1.5c0,0,0,0,0,0c-0.3,0-0.5-0.2-0.5-0.5V20h-8.1v0.8c0.8,0.7,1.3,1.7,1.3,2.8c0,2-1.7,3.7-3.7,3.7c-2,0-3.7-1.7-3.7-3.7c0-1.1,0.5-2.1,1.3-2.8V20H0C0,31,9,40,20,40z"
						/>
					</svg>
				</i>

				<h2>{__('Install & Activate Plugins', 'blocksy-companion')}</h2>
				<p>
					{__(
						'The following plugins are required for this starter site in order to work properly.',
						'blocksy-companion'
					)}
				</p>

				{demoConfiguration.plugins.map(({ plugin, enabled }) => (
					<Fragment key={plugin}>
						{!pluginsStatus[plugin] && (
							<Checkbox
								key={plugin}
								checked={enabled}
								onChange={() =>
									setDemoConfiguration({
										...demoConfiguration,
										plugins: demoConfiguration.plugins.map(
											(demo) =>
												demo.plugin === plugin
													? {
															...demo,
															enabled: !enabled,
													  }
													: demo
										),
									})
								}>
								{getNameForPlugin(plugin)}
							</Checkbox>
						)}

						{pluginsStatus[plugin] && (
							<Checkbox activated checked onChange={() => {}}>
								{getNameForPlugin(plugin)}
							</Checkbox>
						)}
					</Fragment>
				))}
			</div>
		</div>
	)
}

export default Plugins
