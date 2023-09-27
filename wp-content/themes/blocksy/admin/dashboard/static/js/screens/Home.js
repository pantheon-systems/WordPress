import {
	useState,
	createElement,
	useContext,
	Fragment,
} from '@wordpress/element'
import { __, sprintf } from 'ct-i18n'
import SubmitSupport from '../components/SubmitSupport'
import DashboardContext from '../context'
import ctEvents from 'ct-events'

import $ from 'jquery'

const Home = () => {
	const {
		is_companion_active,
		companion_download_link,
		child_download_link,
	} = useContext(DashboardContext)

	let beforeContent = { content: null }
	let afterContent = { content: null }

	const [isLoading, setIsLoading] = useState(false)
	const [customStatus, setCustomStatus] = useState(false)

	ctEvents.trigger('ct:dashboard:home:before', beforeContent)
	ctEvents.trigger('ct:dashboard:home:after', afterContent)

	const finalStatus = customStatus || is_companion_active

	return (
		<section>
			{beforeContent.content}

			<div className="ct-first-steps-container">
				<h2>{__('Customizer Shortcuts', 'blocksy')}</h2>

				<ul className="ct-first-steps">
					<li>
						<svg width="20" height="20" viewBox="0 0 20 20">
							<path d="M15.8,5.9L10,0L4.2,5.9C1,9.1,1,14.3,4.2,17.6C5.8,19.2,7.9,20,10,20s4.2-0.8,5.8-2.4C19,14.3,19,9.1,15.8,5.9z M10,17.9c-1.6,0-3.2-0.6-4.4-1.8c-1.2-1.2-1.8-2.7-1.8-4.4s0.6-3.2,1.8-4.4L10,2.9V17.9z" />
						</svg>

						<h4>{__('Color Options', 'blocksy')}</h4>
						<p>
							{__(
								'Set the theme global colors, select the font, button and background colors.',
								'blocksy'
							)}
						</p>

						<a
							href={`${
								ctDashboardLocalizations.customizer_url
							}${encodeURI(`[section]=color`)}`}
							className="ct-button">
							{__('Go to option', 'blocksy')}
						</a>
					</li>

					<li>
						<svg width="20" height="20" viewBox="0 0 20 20">
							<path d="M20,17.5v-15C20,1.1,18.9,0,17.5,0h-15C1.1,0,0,1.1,0,2.5v15C0,18.9,1.1,20,2.5,20h15C18.9,20,20,18.9,20,17.5z M18.8,17.6c0,0.6-0.6,1.2-1.2,1.2h-15c-0.7,0-1.2-0.5-1.2-1.2V7h17.5V17.6z" />
						</svg>

						<h4>{__('Header Options', 'blocksy')}</h4>

						<p>
							{__(
								'Set the theme header type, set the colors, spacing, alignment and more.',
								'blocksy'
							)}
						</p>

						<a
							href={`${
								ctDashboardLocalizations.customizer_url
							}${encodeURI(`[section]=header`)}`}
							className="ct-button"
							data-hover="white">
							{__('Go to option', 'blocksy')}
						</a>
					</li>

					<li>
						<svg width="20" height="20" viewBox="0 0 20 20">
							<path d="M18,0H5.3c-1.1,0-2,1.1-2,2.4V4H2C0.9,4,0,5.1,0,6.4v0.8v9.6v0.8C0,18.9,0.9,20,2,20h3.3h1.3H18c1.1,0,2-1.1,2-2.4V2.4C20,1.1,19.1,0,18,0zM3.3,16.8c0,0.4-0.3,0.8-0.7,0.8c-0.4,0-0.7-0.4-0.7-0.8V6.6c0-0.1,0.1-0.2,0.2-0.2h0.9c0.1,0,0.2,0.1,0.2,0.2V16.8z M18,17.4c0,0.1-0.1,0.2-0.2,0.2H5.2c0.1-0.3,0.1-0.5,0.1-0.8V7.2c0,0,0,0,0,0V2.6c0-0.1,0.1-0.2,0.2-0.2h12.3c0.1,0,0.2,0.1,0.2,0.2V17.4z M6.9,5.3c-0.1,0-0.2-0.1-0.2-0.2V3.5c0-0.1,0.1-0.2,0.2-0.2h9.6c0.1,0,0.2,0.1,0.2,0.2v1.6c0,0.1-0.1,0.2-0.2,0.2H6.9z M16.5,13.3c0.1,0,0.2-0.1,0.2-0.2V9.5c0-0.1-0.1-0.2-0.2-0.2h-4.3c-0.1,0-0.2,0.1-0.2,0.2v3.6c0,0.1,0.1,0.2,0.2,0.2H16.5zM10.7,7.3c0-0.4-0.3-0.7-0.7-0.7H7.3C7,6.7,6.7,7,6.7,7.3l0,0C6.7,7.7,7,8,7.3,8H10C10.4,8,10.7,7.7,10.7,7.3L10.7,7.3z M10.7,10c0-0.4-0.3-0.7-0.7-0.7H7.3C7,9.3,6.7,9.6,6.7,10l0,0c0,0.4,0.3,0.7,0.7,0.7H10C10.4,10.7,10.7,10.4,10.7,10L10.7,10z M16.7,7.3c0-0.4-0.3-0.7-0.7-0.7h-3.3C12.3,6.7,12,7,12,7.3l0,0C12,7.7,12.3,8,12.7,8H16C16.4,8,16.7,7.7,16.7,7.3L16.7,7.3z M10.7,15.3c0,0.4-0.3,0.7-0.7,0.7H7.3c-0.4,0-0.7-0.3-0.7-0.7l0,0c0-0.4,0.3-0.7,0.7-0.7H10C10.4,14.7,10.7,15,10.7,15.3L10.7,15.3zM16.7,15.3c0,0.4-0.3,0.7-0.7,0.7h-3.3c-0.4,0-0.7-0.3-0.7-0.7l0,0c0-0.4,0.3-0.7,0.7-0.7H16C16.4,14.7,16.7,15,16.7,15.3L16.7,15.3z M10.7,12.7c0-0.4-0.3-0.7-0.7-0.7H7.3c-0.4,0-0.7,0.3-0.7,0.7l0,0c0,0.4,0.3,0.7,0.7,0.7H10C10.4,13.3,10.7,13,10.7,12.7L10.7,12.7z" />
						</svg>

						<h4>{__('Blog Options', 'blocksy')}</h4>

						<p>
							{__(
								'Set the blog entries type, number of cards, spacing, colors and more.',
								'blocksy'
							)}
						</p>

						<a
							href={`${
								ctDashboardLocalizations.customizer_url
							}${encodeURI(`[section]=blog_posts`)}`}
							className="ct-button"
							data-hover="white">
							{__('Go to option', 'blocksy')}
						</a>
					</li>

					<li>
						<svg width="20" height="20" viewBox="0 0 20 20">
							<path d="M18,2h-2v16h2c1.1,0,2-0.9,2-2V4C20,2.9,19.1,2,18,2z" />
							<path d="M13.1,0H1.9C0.8,0,0,0.9,0,2v16c0,1.1,0.8,2,1.9,2h11.2c1,0,1.9-0.9,1.9-2V2C15,0.9,14.2,0,13.1,0zM13,16c0,0.5-0.5,1-1,1H3c-0.5,0-1-0.5-1-1v-2c0-0.5,0.5-1,1-1h9c0.5,0,1,0.5,1,1V16zM12.5,11h-10C2.2,11,2,10.8,2,10.5C2,10.2,2.2,10,2.5,10h10c0.3,0,0.5,0.2,0.5,0.5C13,10.8,12.8,11,12.5,11z M12.5,8h-10C2.2,8,2,7.8,2,7.5C2,7.2,2.2,7,2.5,7h10C12.8,7,13,7.2,13,7.5C13,7.8,12.8,8,12.5,8zM12.5,5h-10C2.2,5,2,4.8,2,4.5C2,4.2,2.2,4,2.5,4h10C12.8,4,13,4.2,13,4.5C13,4.8,12.8,5,12.5,5z" />
						</svg>

						<h4>{__('Page Options', 'blocksy')}</h4>

						<p>
							{__(
								'Set the page container width, spacing, sidebar and more.',
								'blocksy'
							)}
						</p>

						<a
							href={`${
								ctDashboardLocalizations.customizer_url
							}${encodeURI('[section]=single_pages')}`}
							className="ct-button"
							data-hover="white">
							{__('Go to option', 'blocksy')}
						</a>
					</li>

					<li>
						<svg width="20" height="20" viewBox="0 0 20 20">
							<path d="M0,2.5l0,15C0,18.9,1.1,20,2.5,20h15c1.4,0,2.5-1.1,2.5-2.5v-15C20,1.1,18.9,0,17.5,0l-15,0C1.1,0,0,1.1,0,2.5z M2.4,1.1H13v17.5H2.4c-0.7,0-1.2-0.5-1.2-1.2v-15C1.2,1.8,1.8,1.2,2.4,1.1L2.4,1.1z" />
						</svg>

						<h4>{__('Sidebar Options', 'blocksy')}</h4>

						<p>
							{__(
								'Set the sidebar width, design type, spacing, colors.',
								'blocksy'
							)}
						</p>

						<a
							href={`${
								ctDashboardLocalizations.customizer_url
							}${encodeURI('[section]=sidebar')}`}
							className="ct-button"
							data-hover="white">
							{__('Go to option', 'blocksy')}
						</a>
					</li>

					<li>
						<svg width="20" height="20" viewBox="0 0 20 20">
							<path d="M17.5,0h-15C1.1,0,0,1.1,0,2.5v15C0,18.9,1.1,20,2.5,20h15c1.4,0,2.5-1.1,2.5-2.5v-15C20,1.1,18.9,0,17.5,0z M18.8,13H1.2V2.4c0-0.6,0.6-1.2,1.2-1.2h15c0.7,0,1.2,0.5,1.2,1.2V13z" />
						</svg>

						<h4>{__('Footer Options', 'blocksy')}</h4>

						<p>
							{__(
								'Set the footer type, number of columns, spacing and colors.',
								'blocksy'
							)}
						</p>

						<a
							href={`${
								ctDashboardLocalizations.customizer_url
							}${encodeURI('[section]=footer')}`}
							className="ct-button"
							data-hover="white">
							{__('Go to option', 'blocksy')}
						</a>
					</li>
				</ul>

				{is_companion_active !== 'active' && (
					<Fragment>
						<h2 className="ct-downloads-heading">
							{__('Downloads', 'blocksy')}
						</h2>

						<ul className="ct-downloads">
							<li>
								<svg
									width="20"
									height="20"
									viewBox="0 0 20 20">
									<path d="M3.1,0c-0.4,0-0.8,0.2-1,0.6L0.2,3.9C0.1,4.1,0,4.2,0,4.4v13.3C0,19,1,20,2.2,20h15.6c1.2,0,2.2-1,2.2-2.2V4.4c0-0.2-0.1-0.4-0.2-0.6l-1.9-3.3c-0.2-0.3-0.6-0.6-1-0.6H3.1z M3.7,2.2h12.6l1.3,2.2H2.4L3.7,2.2z M2.2,6.7h15.6v11.1H2.2V6.7zM8.9,8.3v3.3H5.6l4.4,4.4l4.4-4.4h-3.3V8.3H8.9z"></path>
								</svg>

								<h3>
									{__('Blocksy Companion', 'blocksy')}
								</h3>

								<p>
									{__(
										'By downloading and installing this plugin you will have access to demo templates, extensions and a lot more stunning features.',
										'blocksy'
									)}
								</p>

								<a
									className="ct-button"
									onClick={(e) => {
										e.stopPropagation()

										setIsLoading(true)

										$.ajax(ajaxurl, {
											type: 'POST',
											data: {
												action:
													'blocksy_notice_button_click',
											},
										}).then(({ success, data }) => {
											if (success) {
												setCustomStatus(data.status)
												if (
													data.status === 'active'
												) {
													location.assign(
														data.pluginUrl
													)
												}
											}

											setIsLoading(false)
										})
									}}>
									{isLoading
										? __(
												'Installing & activating...',
												'blocksy'
										  )
										: finalStatus === 'uninstalled'
										? __(
												'Install Blocksy Companion',
												'blocksy'
										  )
										: finalStatus === 'installed'
										? __(
												'Activate Blocksy Companion',
												'blocksy'
										  )
										: __(
												'Blocksy Companion active!',
												'blocksy'
										  )}
								</a>
							</li>

							<li>								
								<svg
									width="20"
									height="20"
									viewBox="0 0 20 20">
									<path d="M3.1,0c-0.4,0-0.8,0.2-1,0.6L0.2,3.9C0.1,4.1,0,4.2,0,4.4v13.3C0,19,1,20,2.2,20h15.6c1.2,0,2.2-1,2.2-2.2V4.4c0-0.2-0.1-0.4-0.2-0.6l-1.9-3.3c-0.2-0.3-0.6-0.6-1-0.6H3.1z M3.7,2.2h12.6l1.3,2.2H2.4L3.7,2.2z M2.2,6.7h15.6v11.1H2.2V6.7zM8.9,8.3v3.3H5.6l4.4,4.4l4.4-4.4h-3.3V8.3H8.9z"></path>
								</svg>

								<h3>
									{__('Blocksy Child Theme', 'blocksy')}
								</h3>

								<p>
									{__(
										'By using a child theme you can modify any file without the fear of breaking something in the parent theme.',
										'blocksy'
									)}
								</p>


								<a
									className="ct-button"
									href={child_download_link}
									target="_blank">
									{__('Download now', 'blocksy')}
								</a>
							</li>
						</ul>
					</Fragment>
				)}
			</div>

			{afterContent.content}

			<SubmitSupport />
		</section>
	)
}

export default Home
