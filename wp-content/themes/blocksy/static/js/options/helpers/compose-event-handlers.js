export const composeEventHandlers = (...fns) => (event, ...args) =>
	fns.every(fn => fn && fn(event, ...args))
