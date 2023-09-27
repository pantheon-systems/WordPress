import {
	createElement,
	useRef,
	useState,
	useEffect,
	useCallback,
} from '@wordpress/element'
import md5 from 'md5'

const TextArea = ({ id, value, option, onChange }) => {
	const el = useRef()
	const editor = useRef(null)
	const [editorId, _] = useState(
		`${id}-${md5(
			Math.random() + '-' + Math.random() + '-' + Math.random()
		)}`
	)

	const correctEditor = () => wp.oldEditor || wp.editor

	const listener = useCallback(
		() => onChange(correctEditor().getContent(editorId)),
		[editorId]
	)

	useEffect(() => {
		correctEditor().initialize(editorId, {
			quicktags: true,
			mediaButtons: true,
			...option,

			tinymce: {
				toolbar1:
					'formatselect,styleselect,bold,italic,bullist,numlist,link,alignleft,aligncenter,alignright,wp_adv',
				toolbar2:
					'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',

				...(typeof option.tinymce === 'object' ? option.tinymce : {}),
				style_formats_merge: true,
				style_formats: [],
			},
		})

		setTimeout(
			() =>
				window.tinymce.editors[editorId] &&
				window.tinymce.editors[editorId].on('change', listener)
		)

		if (wp.oldEditor) {
			setTimeout(
				() => {
					setTimeout(() => {
						window.tinymce.editors[editorId].off('change', listener)
						correctEditor().remove(editorId)

						correctEditor().initialize(editorId, {
							quicktags: true,
							mediaButtons: true,
							...option,

							tinymce: {
								toolbar1:
									'formatselect,styleselect,bold,italic,bullist,numlist,link,alignleft,aligncenter,alignright,wp_adv',
								toolbar2:
									'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',

								...(typeof option.tinymce === 'object'
									? option.tinymce
									: {}),
								style_formats_merge: true,
								style_formats: [],
							},
						})

						setTimeout(
							() =>
								window.tinymce.editors[editorId] &&
								window.tinymce.editors[editorId].on(
									'change',
									listener
								)
						)
					}, 300)
				},

				1000
			)
		}

		return () => {
			if (!window.tinymce.editors[editorId]) return

			setTimeout(() => {
				window.tinymce.editors[editorId].off('change', listener)
				correctEditor().remove(editorId)
			}, 300)
		}
	}, [])

	return (
		<div className="ct-option-editor" {...(option.attr || {})}>
			<textarea
				style={{ opacity: 0 }}
				id={editorId}
				ref={el}
				value={value}
				className="wp-editor-area"
				{...{
					...(option.field_attr ? option.field_attr : {}),
					...(option.attr && option.attr.placeholder
						? {
								placeholder: option.attr.placeholder,
						  }
						: {}),
				}}
				onChange={({ target: { value } }) => onChange(value)}
			/>
		</div>
	)
}

export default TextArea
