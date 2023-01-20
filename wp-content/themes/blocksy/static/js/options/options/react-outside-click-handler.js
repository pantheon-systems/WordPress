import {
	createElement,
	Component,
	Fragment,
	createRef,
} from '@wordpress/element'
import cls from 'classnames'

const DISPLAY = {
	BLOCK: 'block',
	FLEX: 'flex',
	INLINE_BLOCK: 'inline-block',
}

const defaultProps = {
	disabled: false,

	// `useCapture` is set to true by default so that a `stopPropagation` in the
	// children will not prevent all outside click handlers from firing - maja
	useCapture: true,
	display: DISPLAY.BLOCK,
}

const updateRef = (ref, instance) => {
	if (typeof ref === 'function') {
		ref(instance)
	} else {
		ref.current = instance
	}
}

export default class OutsideClickHandler extends Component {
	componentDidMount() {
		const { disabled, useCapture } = this.props

		if (!disabled) this.addMouseDownEventListener(useCapture)
	}

	childNode = createRef()

	checkIsInside = (event) => {
		const result = [
			this.childNode,
			...(this.props.additionalRefs || []),
		].reduce((isInside, currentRef) => {
			if (isInside) {
				return isInside
			}

			if (!currentRef || !currentRef.current) {
				return isInside
			}

			return currentRef.current.contains(event.target)
		}, false)

		return result
	}

	UNSAFE_componentWillReceiveProps({ disabled, useCapture }) {
		const { disabled: prevDisabled } = this.props

		if (prevDisabled !== disabled) {
			if (disabled) {
				this.removeEventListeners()
			} else {
				this.addMouseDownEventListener(useCapture)
			}
		}
	}

	componentWillUnmount() {
		this.removeEventListeners()
	}

	// Use mousedown/mouseup to enforce that clicks remain outside the root's
	// descendant tree, even when dragged. This should also get triggered on
	// touch devices.
	onMouseDown = (e) => {
		const { useCapture } = this.props

		if (!this.checkIsInside(e)) {
			if (this.removeMouseUp) {
				this.removeMouseUp()
				this.removeMouseUp = null
			}

			document.addEventListener('mouseup', this.onMouseUp, useCapture)

			this.removeMouseUp = () => {
				document.removeEventListener(
					'mouseup',
					this.onMouseUp,
					useCapture
				)
			}
		}
	}

	// Use mousedown/mouseup to enforce that clicks remain outside the root's
	// descendant tree, even when dragged. This should also get triggered on
	// touch devices.
	onMouseUp = (e) => {
		const { onOutsideClick } = this.props

		if (this.removeMouseUp) {
			this.removeMouseUp()
			this.removeMouseUp = null
		}

		if (!this.checkIsInside(e)) {
			onOutsideClick(e)
		}
	}

	setChildNodeRef = (ref) => {
		if (this.props.wrapperProps && this.props.wrapperProps.ref) {
			updateRef(this.props.wrapperProps.ref, ref)
		}

		updateRef(this.childNode, ref)
	}

	addMouseDownEventListener(useCapture) {
		document.addEventListener('mousedown', this.onMouseDown, useCapture)

		this.removeMouseDown = () => {
			document.removeEventListener(
				'mousedown',
				this.onMouseDown,
				useCapture
			)
		}
	}

	removeEventListeners() {
		if (this.removeMouseDown) this.removeMouseDown()
		if (this.removeMouseUp) this.removeMouseUp()
	}

	render() {
		const { children, display, className, wrapperProps } = this.props

		return (
			<div
				className={cls(className)}
				{...(wrapperProps || {})}
				ref={this.setChildNodeRef}>
				{children}
			</div>
		)
	}
}

OutsideClickHandler.defaultProps = defaultProps
