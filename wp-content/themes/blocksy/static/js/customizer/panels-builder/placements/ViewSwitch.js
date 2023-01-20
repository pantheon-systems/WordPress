import { createElement, useState, useEffect } from '@wordpress/element'
import cls from 'classnames'
import { __ } from 'ct-i18n'

const ViewSwitch = ({ currentView, setCurrentView }) => {
	const [builderCollapsed, setBuilderCollapsed] = useState(false)

	useEffect(() => {
		return () => {
			document
				.querySelector('.wp-full-overlay')
				.classList.remove('ct-builder-collapsed')
		}
	}, [])

	return (
		<ul className="ct-view-switch">
			{['desktop', 'mobile'].map((view) => (
				<li
					key={view}
					onClick={() =>
						setCurrentView(view === 'mobile' ? 'tablet' : view)
					}
					className={cls({
						active: currentView === view,
					})}>
					{
						{
							desktop: __('Desktop Header', 'blocksy'),
							mobile: __('Tablet / Mobile Header', 'blocksy'),
						}[view]
					}
				</li>
			))}

			<li
				className="ct-builder-toggle"
				onClick={() => {
					setBuilderCollapsed(!builderCollapsed)

					if (builderCollapsed) {
						document
							.querySelector('.wp-full-overlay')
							.classList.remove('ct-builder-collapsed')
					} else {
						document
							.querySelector('.wp-full-overlay')
							.classList.add('ct-builder-collapsed')
					}
				}}>
				{builderCollapsed
					? __('Show Builder', 'blocksy')
					: __('Hide Builder', 'blocksy')}
			</li>
		</ul>
	)
}

export default ViewSwitch
