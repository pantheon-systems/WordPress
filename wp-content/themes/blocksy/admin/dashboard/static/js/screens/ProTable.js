import { createElement, useState, useEffect } from '@wordpress/element'
import { dateI18n } from '@wordpress/date'
import { __, sprintf } from 'ct-i18n'
import classnames from 'classnames'
import { Transition, animated } from 'react-spring/renderprops'

export default () => {
	if (
		window.ctDashboardLocalizations &&
		window.ctDashboardLocalizations.plugin_data &&
		window.ctDashboardLocalizations.plugin_data.is_pro
	) {
		return null
	}

	return (
		<section className="ct-comparison-table">
			<ul>
				<li className="ct-table-heading">
					<span>{__('General Features', 'blocksy')}</span>
					<span>{__('Free', 'blocksy')}</span>
					<span>{__('Pro', 'blocksy')}</span>
				</li>

				<li>
					<span>{__('Global color palette', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Related posts module', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Trending posts module', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Newsletter subscribe module', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Cookies notice module', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Custom widgets', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Google fonts', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Custom post types support', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('WooCommerce support', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>
						{__('Multiple conditional sidebars', 'blocksy')}
					</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Content blocks (hooks)', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Custom code snippets', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>
						{__(
							'Custom fonts (+ support for variable fonts)',
							'blocksy'
						)}
					</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Adobe Typekit fonts', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Local Google fonts (GDPR)', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Advanced menu (mega menu)', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Shortcuts bar', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>
						{__('White label (only in Agency package)', 'blocksy')}
					</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>
			</ul>

			<ul>
				<li className="ct-table-heading">
					<span>{__('Header Features', 'blocksy')}</span>
					<span>{__('Free', 'blocksy')}</span>

					<span>{__('Pro', 'blocksy')}</span>
				</li>

				<li>
					<span>{__('Header builder', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Transparent header', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Sticky header', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Multiple conditional headers', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Duplicate headers items', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Desktop off-canvas drawer', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('More headers items', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>
			</ul>

			<ul>
				<li className="ct-table-heading">
					<span>{__('Footer Features', 'blocksy')}</span>
					<span>{__('Free', 'blocksy')}</span>
					<span>{__('Pro', 'blocksy')}</span>
				</li>

				<li>
					<span>{__('Footer builder', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Footer reveal effect', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Multiple conditional footers', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Duplicate footer items', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('More footer items', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>
			</ul>

			<ul>
				<li className="ct-table-heading">
					<span>{__('WooCommerce Features', 'blocksy')}</span>
					<span>{__('Free', 'blocksy')}</span>
					<span>{__('Pro', 'blocksy')}</span>
				</li>

				<li>
					<span>{__('Advanced archive layouts', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>
						{__('Advanced single product layouts', 'blocksy')}
					</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('AJAX add to cart', 'blocksy')}</span>
					<span className="dashicons dashicons-yes"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Quick view module', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Floating cart', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Off-canvas filters', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Off-canvas cart', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('More single product layouts', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Product share icons', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Product gallery slider', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>

				<li>
					<span>{__('Wishlist', 'blocksy')}</span>
					<span className="dashicons dashicons-no-alt"></span>
					<span className="dashicons dashicons-yes"></span>
				</li>
			</ul>

			<div className="ct-cta-container">

				<svg width="55" height="55" viewBox="0 0 40.5 48.3">
					<path fill="#2d82c8" d="M33.4 29.4l7.1 12.3-7.4.6-4 6-7.3-12.9"/>
					<path d="M33.5 29.6L26 42.7l-4.2-7.3 11.6-6 .1.2zM0 41.7l7.5.6 3.9 6 7.2-12.4-11-7.3L0 41.7z" fill="#2271b1"/>
					<path d="M39.5 18.7c0 1.6-2.4 2.8-2.7 4.3-.4 1.5 1 3.8.2 5.1-.8 1.3-3.4 1.2-4.5 2.3-1.1 1.1-1 3.7-2.3 4.5-1.3.8-3.6-.6-5.1-.2-1.5.4-2.7 2.7-4.3 2.7S18 35 16.5 34.7c-1.5-.4-3.8 1-5.1.2s-1.2-3.4-2.3-4.5-3.7-1-4.5-2.3.6-3.6.2-5.1-2.7-2.7-2.7-4.3 2.4-2.8 2.7-4.3c.4-1.5-1-3.8-.2-5.1C5.4 8 8.1 8.1 9.1 7c1.1-1.1 1-3.7 2.3-4.5s3.6.6 5.1.2C18 2.4 19.2 0 20.8 0c1.6 0 2.8 2.4 4.3 2.7 1.5.4 3.8-1 5.1-.2 1.3.8 1.2 3.4 2.3 4.5 1.1 1.1 3.7 1 4.5 2.3s-.6 3.6-.2 5.1c.3 1.5 2.7 2.7 2.7 4.3z" fill="#599fd9"/>
					<path d="M23.6 7c-6.4-1.5-12.9 2.5-14.4 8.9-.7 3.1-.2 6.3 1.5 9.1 1.7 2.7 4.3 4.6 7.4 5.4.9.2 1.9.3 2.8.3 2.2 0 4.4-.6 6.3-1.8 2.7-1.7 4.6-4.3 5.4-7.5C34 15 30 8.5 23.6 7zm7 14c-.6 2.6-2.2 4.8-4.5 6.2-2.3 1.4-5 1.8-7.6 1.2-2.6-.6-4.8-2.2-6.2-4.5-1.4-2.3-1.8-5-1.2-7.6.6-2.6 2.2-4.8 4.5-6.2 1.6-1 3.4-1.5 5.2-1.5.8 0 1.5.1 2.3.3 5.4 1.3 8.7 6.7 7.5 12.1zm-8.2-4.5l3.7.5-2.7 2.7.7 3.7-3.4-1.8-3.3 1.8.6-3.7-2.7-2.7 3.8-.5 1.6-3.4 1.7 3.4z" fill="#fff"/>
				</svg>

				<h2>{__('Upgrade Now', 'blocksy')}</h2>

				<p>
					{__(
						'Upgrade to the Pro version and get instant access to all premium extensions, features and future updates.',
						'blocksy'
					)}
				</p>
				<a
					href="https://creativethemes.com/blocksy/pricing/"
					className="ct-button-primary"
					target="_blank">
					{__('Get Blocksy Pro Now', 'blocksy')}
				</a>
			</div>
		</section>
	)
}
