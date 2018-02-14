var theEditor = document.getElementById( 'code-editor');

if ( theEditor != null ) {
	var editLine       = theEditor.getAttribute( 'data-editor-goto-line'),
		editorLanguage = theEditor.getAttribute( 'data-editor-language');

	function resizeEditor( editor ) {
		var setEditorSize  = ( Math.max( document.documentElement.clientHeight, window.innerHeight || 0 ) - 177 );
		editor.setSize( null, parseInt( setEditorSize ) );
	}

	var editor = CodeMirror.fromTextArea( document.getElementById( 'code-editor' ), {
		lineNumbers: true,
		mode: editorLanguage,
		styleActiveLine : true,
		matchBrackets   : true,
		indentWithTabs  : true,
		indentUnit      : 4,
		theme           : 'twilight'
	} );

	editor.scrollIntoView( parseInt( editLine ) );
	editor.setCursor( parseInt( editLine - 1 ), 0 );
	resizeEditor( editor );
}

var gotoClick = document.getElementsByClassName( 'string-locator-edit-goto' );
for( var i = 0; i < gotoClick.length; i++ ) {
	var click = gotoClick[i];
	click.onclick = function() {
		editor.scrollIntoView( parseInt( this.getAttribute( 'data-goto-line' ) ) );
		editor.setCursor( parseInt( this.getAttribute( 'data-goto-line' ) - 1 ), 0 );
	}
}