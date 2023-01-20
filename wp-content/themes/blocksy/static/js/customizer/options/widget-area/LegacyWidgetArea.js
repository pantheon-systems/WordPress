import { createElement, useEffect, useRef } from '@wordpress/element'

const LegacyWidgetArea = ({
	value,
	option,
	option: { sidebarId = 'ct-footer-sidebar-1' },
	onChange,
}) => {
	const parentEl = useRef()

	useEffect(() => {
		const sectionId = `widgetAreaSection-${sidebarId}`

		const widgetsToMove = Object.keys(wp.customize.control._value).filter(
			(id) => {
				if (id.indexOf('widget_') !== 0) {
					return false
				}

				return (
					wp.customize.control(id).section() ===
					`sidebar-widgets-${sidebarId}`
				)
			}
		)

		const Section = wp.customize.Section.extend({
			containerParent: jQuery(parentEl.current),
			// containerPaneParent: parentEl.current

			collapse: function () {},

			embed: function () {
				var section = this

				section.containerParent = wp.customize.ensure(
					section.containerParent
				)

				var parentContainer = wp.customize.ensure(
					section.containerPaneParent
				)

				if (
					!section.contentContainer.parent().is(section.headContainer)
				) {
					section.containerParent.append(section.contentContainer)
					section.contentContainer[0].classList.add('open')
					section.contentContainer[0]
						.querySelector(
							'.customize-section-description-container'
						)
						.remove()
				}

				section.deferred.embedded.resolve()

				setTimeout(() => {
					widgetsToMove.map((control) => {
						wp.customize.control(control).embedWidgetControl()

						return
						console.log('here', control)

						setTimeout(() => {
							wp.customize
								.control(control)
								.container.one(
									'click.toggle-widget-expanded',
									function toggleWidgetExpanded() {
										const widgetControl =
											wp.customHtmlWidgets.widgetControls[
												control
													.replace('widget_', '')
													.replace(']', '')
													.replace('[', '-')
											]

										if (widgetControl) {
											widgetControl.updateFields()

											wp.customize
												.control(control)
												.container.find('textarea')
												.change()
										}
									}
								)

							const widgetControl =
								wp.customHtmlWidgets.widgetControls[
									control
										.replace('widget_', '')
										.replace(']', '')
										.replace('[', '-')
								]

							if (widgetControl) {
								widgetControl.updateFields()

								wp.customize
									.control(control)
									.container.find('textarea')
									.change()
							}

							jQuery(document).trigger('widget-added', [
								wp.customize
									.control(control)
									.container.find('.widget:first'),
							])

							if (wp.mediaWidgets.widgetControls[control]) {
								jQuery(
									wp.mediaWidgets.widgetControls[
										control
									].el.closest('.widget')
								).one(
									'click.toggle-widget-expanded',
									function toggleWidgetExpanded() {
										var widgetContainer = $(this)

										wp.mediaWidgets.handleWidgetAdded(
											new jQuery.Event('widget-added'),
											widgetContainer
										)
									}
								)
							}
						}, 50)
					})
				})
			},
		})

		const section = new Section(sectionId, {})

		wp.customize.section.add(section)

		const control = new wp.customize.controlConstructor.sidebar_widgets(
			'checkcheck',
			{
				params: {
					sidebar_id: sidebarId,
					priority: 999,
					section: section.id,
					setting: `sidebars_widgets[${sidebarId}]`,
					content: `<li id="customize-control-sidebars_widgets-ct-footer-sidebar-2" class="customize-control customize-control-sidebar_widgets">		<button type="button" class="button add-new-widget" aria-expanded="false" aria-controls="available-widgets">
			Add a Widget		</button>
		<button type="button" class="button-link reorder-toggle" aria-label="Reorder widgets" aria-describedby="reorder-widgets-desc-sidebars_widgets-ct-footer-sidebar-2">
			<span class="reorder">Reorder</span>
			<span class="reorder-done">Done</span>
		</button>
		<p class="screen-reader-text" id="reorder-widgets-desc-sidebars_widgets-ct-footer-sidebar-2">When in reorder mode, additional controls to reorder widgets will be available in the widgets list above.</p>
		</li>`,
				},
			}
		)

		widgetsToMove.map((control) => {
			wp.customize.control(
				control
			).prevSection = `sidebar-widgets-${sidebarId}`

			wp.customize.control(control).section(sectionId)
		})

		setTimeout(() => {
			if (!parentEl.currentEl) {
				return
			}

			jQuery(parentEl.current.firstElementChild).sortable(
				'option',
				'containment',
				'parent'
			)
		}, 1000)

		return () => {
			const widgetsToMove = Object.keys(
				wp.customize.control._value
			).filter((id) => {
				if (id.indexOf('widget_') !== 0) {
					return false
				}

				return (
					wp.customize.control(id).section() ===
						`sidebar-widgets-${sidebarId}` ||
					wp.customize.control(id).section() === sectionId
				)
			})

			widgetsToMove.map((control) => {
				if (
					wp.customize.control(control) &&
					wp.customize
						.control(control)
						.container[0].matches('[id*="widget_text"]')
				) {
					let container = wp.customize.control(control).container[0]
					let textarea = container.querySelector('textarea')

					let widgetId = container.querySelector('.widget-id').value

					if (wp.textWidgets.widgetControls[widgetId]) {
						wp.textWidgets.widgetControls[widgetId].remove()
					}
					wp.textWidgets.widgetControls[widgetId] = null

					wp.customize.control(control).collapse()
				}

				if (wp.customize.control(control)) {
					wp.customize
						.control(control)
						.section(
							wp.customize.control(control).prevSection ||
								`sidebar-widgets-${sidebarId}`
						)
				}
			})
			;[
				...document.querySelectorAll(
					`.customize-pane-parent [id="accordion-section-${sectionId}"]`
				),
			].map((container) => container.remove())

			wp.customize.section.remove(section.id)
		}
	}, [])

	return <div className="ct-option-widget-area" ref={parentEl}></div>
}

export default LegacyWidgetArea
