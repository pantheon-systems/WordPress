import { createElement, Component, useContext } from '@wordpress/element'
import DashboardContext from './context'
import { sprintf, __ } from 'ct-i18n'
import ctEvents from 'ct-events'

const Heading = () => {
	const {
		theme_name,
		theme_custom_description,
		dashboard_has_heading,
	} = useContext(DashboardContext)
	let afterContent = { content: null }
	ctEvents.trigger('ct:dashboard:heading:after', afterContent)

	return (
		<div>
			<h2
				onClick={(e) =>
					e.shiftKey &&
					ctEvents.trigger('ct:dashboard:heading:advanced-click')
				}>
				{dashboard_has_heading === 'yes' && (
					<svg
						width="35"
						height="35"
						viewBox="0 0 50 50"
						xmlns="http://www.w3.org/2000/svg">
						<path
							d="M25 0c13.807 0 25 11.193 25 25S38.807 50 25 50 0 38.807 0 25 11.193 0 25 0zm5.469 25.701a.246.246 0 00-.332 0L19.36 35.812c-.073.07-.021.188.083.188h10.085a.486.486 0 00.331-.129l4.73-4.438c.548-.515.548-1.351 0-1.867zm0-11a.246.246 0 00-.332 0l-12 11.259a.427.427 0 00-.137.311v8.374c0 .098.126.147.2.078l15.551-14.666c.55-.516.55-1.748 0-2.264zM28.279 14H18.233c-.129 0-.234.099-.234.22v9.425c0 .098.126.148.2.078l10.161-9.535c.074-.07.022-.188-.083-.188z"
							fill="#23282D"
							fill-rule="evenodd"
						/>
					</svg>
				)}

				{theme_name}
				{dashboard_has_heading === 'yes' && afterContent.content}
			</h2>
			<p>
				{theme_custom_description ||
					__(
						'The most innovative, intuitive and lightning fast WordPress theme. Build your next web project visually, in no time.',
						'blocksy'
					)}
			</p>
		</div>
	)
}

export default Heading
