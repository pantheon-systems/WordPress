jQuery(document).ready(function ($) {
	var anchor = $( 'input[name="anchor"]' );
	var tab_wrapper_a = $( '.nav-tab-wrapper>a' );
	var actual_anchor = window.location.hash;
	if ( actual_anchor !== '' ) {
		actual_anchor = '#' + actual_anchor.replace( '#', '' );
	}

	if ( actual_anchor !== '' ) {
		anchor.val( actual_anchor );
	}

    $( '.table' ).addClass( 'ui-tabs-hide' );
    $( anchor.val() ).removeClass( 'ui-tabs-hide' );

	if ( anchor.val() == '#backwpup-tab-information' ) {
		$('#submit').hide();
		$('#default_settings').hide();
	}

	tab_wrapper_a.removeClass( 'nav-tab-active' );
	tab_wrapper_a.each( function () {
        if ( $(this).attr( 'href' ) == anchor.val() ) {
            $(this).addClass( 'nav-tab-active' );
        }
    });

	tab_wrapper_a.on( 'click', function () {
        var clickedid = $(this).attr('href');
		tab_wrapper_a.removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.table').addClass('ui-tabs-hide');
        $(clickedid).removeClass('ui-tabs-hide');
        $('#message').hide();
	    anchor.val(clickedid);
		if ( clickedid == '#backwpup-tab-information' ) {
			$('#submit').hide();
			$('#default_settings').hide();
		} else {
			$('#submit').show();
			$('#default_settings').show();
		}
	    window.location.hash = clickedid;
	    window.scrollTo(0, 0);
		return false;
    });

    $('#authentication_method').change( function () {
        var auth_method = $( '#authentication_method' ).val();
        if ( '' === auth_method ) {
            $('.authentication_basic').hide();
            $('.authentication_query_arg').hide();
            $('.authentication_user').hide();
        } else if ( 'basic' == auth_method ) {
            $('.authentication_basic').show();
            $('.authentication_query_arg').hide();
            $('.authentication_user').hide();
        } else if ( 'query_arg' == auth_method ) {
            $('.authentication_basic').hide();
            $('.authentication_query_arg').show();
            $('.authentication_user').hide();
        } else if ( 'user' == auth_method ) {
            $('.authentication_basic').hide();
            $('.authentication_query_arg').hide();
            $('.authentication_user').show();
        }
    });

	setTimeout(function() {
		window.scrollTo(0, 0);
	}, 1);
});
