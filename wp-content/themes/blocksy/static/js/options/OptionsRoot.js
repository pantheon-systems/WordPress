import {
	createElement,
	Component,
	createRef,
	useRef,
	useCallback,
	useEffect,
	useState
} from '@wordpress/element'
import OptionsPanel from './OptionsPanel.js'
import $ from 'jquery'

import ctEvents from 'ct-events'

const INITIAL_VALUE = '__INITIAL__'

const OptionsRoot = ({
	value,
	options,
	input_name,
	input_id,
	hasRevertButton
}) => {
	const [internalValue, setInternalValue] = useState(value)

	const input = useRef()

	const handleChange = useCallback(({ id, value, input: inputRef }) => {
		if (inputRef === input.current) {
			setInternalValue(internalValue => ({
				...internalValue,
				[id]: value
			}))
		}
	}, [])

	useEffect(() => {
		ctEvents.on('ct:options:trigger-change', handleChange)

		return () => {
			ctEvents.off('ct:options:trigger-change', handleChange)
		}
	}, [])

	return (
		<div className="ct-options-root">
			<input
				value={JSON.stringify(
					Array.isArray(internalValue) ? {} : internalValue
				)}
				onChange={() => {}}
				name={input_name}
				id={input_id}
				type="hidden"
				ref={input}
			/>

			<OptionsPanel
				hasRevertButton={hasRevertButton}
				onChange={(key, newValue) => {
					setInternalValue(internalValue => ({
						...internalValue,
						[key]: newValue
					}))
					$(input.current).change()
				}}
				value={internalValue}
				options={options}
			/>
		</div>
	)
}

export default OptionsRoot
