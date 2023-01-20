import { useMemo, useReducer } from '@wordpress/element'

const reducer = (state, _) => !state

const useForceUpdate = () => {
	const [, dispatch] = useReducer(reducer, true)

	const memoizedDispatch = useMemo(
		() => () => {
			dispatch(null)
		},
		[dispatch]
	)

	return memoizedDispatch
}

export default useForceUpdate
