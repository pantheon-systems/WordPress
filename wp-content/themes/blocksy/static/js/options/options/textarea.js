import { createElement, Component } from '@wordpress/element'

const TextArea = ({ value, option, onChange }) => {
	let { placeholder, ...attr } = {
		...(option.attr || {}),
	}

	return (
		<div className="ct-option-textarea" {...attr}>
			<textarea
				value={value}
				{...{
					...(option.field_attr ? option.field_attr : {}),
					...(placeholder
						? {
								placeholder,
						  }
						: {}),
				}}
				onChange={({ target: { value } }) => onChange(value)}
			/>
		</div>
	)
}

export default TextArea
