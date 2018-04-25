jQuery(document).ready(function ($) {
	var StringLocator;
	if ( false !== string_locator.CodeMirror && '' !== string_locator.CodeMirror ) {
		StringLocator = wp.codeEditor.initialize('code-editor', string_locator.CodeMirror);

		function resizeEditor(editor) {
			console.dir(editor);
			var setEditorSize = ( Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - 177 );
			editor.setSize(null, parseInt(setEditorSize));
		}

		$(".string-locator-edit-goto").click(function (e) {
			e.preventDefault();
			StringLocator.codemirror.scrollIntoView(parseInt($(this).data('goto-line')));
			StringLocator.codemirror.setCursor(parseInt($(this).data('goto-line') - 1), 0);
		});

		resizeEditor(StringLocator.codemirror);
		StringLocator.codemirror.scrollIntoView(parseInt(string_locator.goto_line));
		StringLocator.codemirror.setCursor(parseInt(string_locator.goto_line - 1), 0);
	} else {
		StringLocator = $("#code-editor");

		StringLocator.css( 'width', $(".string-locator-edit-wrap").width() );
		StringLocator.css( 'height', parseInt( ( Math.max(document.documentElement.clientHeight, window.innerHeight || 0) - 177 ) ) );
	}
});
