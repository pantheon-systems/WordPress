export default () => {
	return {
		get location() {
			return {
				...window.location,
				pathname: (window.location.hash || '#/').replace(/#/g, '')
			}
		},

		addEventListener(name, fn) {
			window.addEventListener(name, fn)
		},

		removeEventListener(name, fn) {
			window.removeEventListener(name, fn)
		},

		history: {
			get state() {
				return window.history.state
			},
			replaceState(state, maybeRef, to) {
				window.history.replaceState(state, maybeRef, `#${to}`)
			},

			pushState(state, maybeRef, to) {
				window.history.pushState(state, maybeRef, `#${to}`)
			}
		}
	}
}
