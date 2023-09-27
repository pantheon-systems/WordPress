import {
	createElement,
	render,
	unmountComponentAtNode,
} from '@wordpress/element'
import classnames from 'classnames'
import ResponsiveControls from '../components/responsive-controls.js'

const OptionWrapper = ({
	option,
	children,
	value,
	id,
	design,
	labelToolbar,
	controlEnd,
	wrapperAttr: { className, ...wrapperAttr },
}) => {
	if (design === 'none') {
		return children
	}

	let maybeLabel =
		Object.keys(option).indexOf('label') === -1
			? (id || '')
					.replace(/./, (s) => s.toUpperCase())
					.replace(/\_|\-/g, ' ')
			: option.label

	if (maybeLabel === '') {
		maybeLabel = true
	}

	let maybeDesc =
		Object.keys(option).indexOf('desc') === -1 ? false : option.desc

	return (
		<div
			className={classnames('ct-control', className)}
			data-design={design}
			{...wrapperAttr}>
			<header>
				{maybeLabel && <label>{maybeLabel}</label>}
				{labelToolbar()}
			</header>
			<section>{children}</section>
			{controlEnd()}
		</div>
	)
}

export const defineCustomizerControl = (type, Component) =>
	(wp.customize.controlConstructor[type] = wp.customize.Control.extend({
		initialize(id, params) {
			const control = this

			wp.customize.Control.prototype.initialize.call(control, id, params)

			control.container[0].classList.remove('customize-control')

			// The following should be eliminated with <https://core.trac.wordpress.org/ticket/31334>.
			function onRemoved(removedControl) {
				if (control === removedControl) {
					control.destroy()
					control.container.remove()
					wp.customize.control.unbind('removed', onRemoved)
				}
			}

			wp.customize.control.bind('removed', onRemoved)
		},

		renderContent() {
			return

			const ChildComponent = Component

			let MyChildComponent = Component

			// block | inline
			let design = 'block'

			let LabelToolbar = () => null
			let OptionMetaWrapper = null
			let ControlEnd = () => null

			/*
			OptionMetaWrapper = ({
				getActualOption,
				option,
				value,
				...props
			}) => getActualOption(props)
            */

			let wrapperAttr = {}

			if (Component.wrapperAttr) {
				wrapperAttr = Component.wrapperAttr
			}

			if (Component.renderingConfig) {
				design = Component.renderingConfig.design || design
			}

			if (Component.LabelToolbar) {
				LabelToolbar = Component.LabelToolbar
			}

			if (Component.ControlEnd) {
				LabelToolbar = Component.ControlEnd
			}

			if (Component.MetaWrapper) {
				OptionMetaWrapper = Component.MetaWrapper
			}

			/*
			if (this.params.option.responsive) {
				MyChildComponent = ResponsiveControls
			}
            */

			const getActualOption = ({
				wrapperAttr: additionalWrapperAttr = {},
				...props
			} = {}) => {
				return (
					<OptionWrapper
						design={design}
						id={this.id}
						wrapperAttr={{
							...wrapperAttr,
							...this.params.option.wrapperAttr,
							...additionalWrapperAttr,
						}}
						option={this.params.option}
						labelToolbar={() => (
							<LabelToolbar
								onChange={(v) => this.setting.set(v)}
								value={this.setting.get()}
								option={this.params.option}
							/>
						)}
						controlEnd={() => (
							<ControlEnd
								onChange={(v) => this.setting.set(v)}
								value={this.setting.get()}
								option={this.params.option}
							/>
						)}
						value={this.setting.get()}>
						<MyChildComponent
							id={this.id}
							onChange={(v) => this.setting.set(v)}
							value={this.setting.get()}
							option={this.params.option}>
							{(props) => <ChildComponent {...props} />}
						</MyChildComponent>
					</OptionWrapper>
				)
			}

			if (this.params.option.customizer_section !== 'layout') {
				// return
			}

			render(
				OptionMetaWrapper ? (
					<OptionMetaWrapper
						option={this.params.option}
						value={this.setting.get()}
						getActualOption={(props) => getActualOption(props)}
					/>
				) : (
					getActualOption()
				),
				this.container[0]
			)
		},

		ready() {
			// this.setting.bind(() => this.renderContent())
		},

		destroy() {
			unmountComponentAtNode(this.container[0])

			if (wp.customize.Control.prototype.destroy) {
				wp.customize.Control.prototype.destroy.call(this)
			}
		},
	}))
