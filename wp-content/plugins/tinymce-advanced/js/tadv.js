// TinyMCE Advanced
jQuery( document ).ready( function( $ ) {
	var $importElement = $('#tadv-import'),
		$importError = $('#tadv-import-error');

	$('.container').sortable({
		connectWith: '.container',
		items: '> li',
		cursor: 'move',
		stop: function( event, ui ) {
			var toolbar_id;

			if ( ui && ( toolbar_id = ui.item.parent().attr('id') ) ) {
				ui.item.find('input.tadv-button').attr('name', toolbar_id + '[]');
			}
		},
		/*
		activate: function( event, ui ) {
			if ( this.id !== ui.sender.attr('id') ) {
				$(this).parent().css({ 'border-color': '#888' }); // , 'background-color': '#fafff9'
			}
		},
		deactivate: function( event, ui ) {
			$(this).parent().css({ 'border-color': '' }); // , 'background-color': ''
		},
		*/
		revert: 300,
		opacity: 0.7,
		placeholder: 'tadv-placeholder',
		forcePlaceholderSize: true,
		containment: 'document'
	});

	$( '#menubar' ).on( 'change', function() {
		$( '#tadv-mce-menu' ).toggleClass( 'enabled', $(this).prop('checked') );
	});

	$('#tadv-export-select').click( function() {
		$('#tadv-export').focus().select();
	});

	$importElement.change( function() {
		$importError.empty();
	});

	$('#tadv-import-verify').click( function() {
		var string;

		string = ( $importElement.val() || '' ).replace( /^[^{]*/, '' ).replace( /[^}]*$/, '' );
		$importElement.val( string );

		try {
			JSON.parse( string );
			$importError.text( 'No errors.' );
		} catch( error ) {
			$importError.text( error );
		}
	});

	function translate( str ) {
		if ( window.tadvTranslation.hasOwnProperty( str ) ) {
			return window.tadvTranslation[str];
		}
		return str;
	}

	if ( typeof window.tadvTranslation === 'object' ) {
		$( '.tadvitem' ).each( function( i, element ) {
			var $element = $( element ),
				$descr = $element.find( '.descr' ),
				text = $descr.text();

			if ( text ) {
				text = translate( text );
				$descr.text( text );
				$element.find( '.mce-ico' ).attr( 'title', text );
			}
		});

		$( '#tadv-mce-menu .tadv-translate' ).each( function( i, element ) {
			var $element = $( element ),
				text = $element.text();

			if ( text ) {
				$element.text( translate( text ) );
			}
		});
	}
});
