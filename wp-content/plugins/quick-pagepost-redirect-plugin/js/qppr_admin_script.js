;(function($,d,z){
	$(d).ready(function() {
		$('span.qppr_meta_help').css('display','none');
		$('.inside').delegate('span.qppr_meta_help_wrap', 'hover', function(e){
			var $curdisp = $(this).find('span.qppr_meta_help').css('display');
			if($curdisp == 'none'){
				$(this).find('span.qppr_meta_help').css('display','inline');
			}else{
				$(this).find('span.qppr_meta_help').css('display','none');
			}
			e.preventDefault();
		});
		var mainurl = z.ajaxurl; 
		$( '#pprredirect_type').on( 'change', function(e){
			e.preventDefault();
			$( '.qppr-meta-section-wrapper' ).removeClass( 'meta-selected meta-not-selected' );
			var selVal = $( this ).val();
			if( selVal == 'meta' ){
				$( '.qppr-meta-section-wrapper' ).slideDown( 'slow' );
			}else{
				$( '.qppr-meta-section-wrapper' ).slideUp( 'slow' );
			}
		});
		$( '.qppr-delete-everything' ).on( 'click', function(e){
			e.preventDefault();
			if( confirm( z.msgAllDeleteConfirm ) ){ 
				var remove_qppr_all_data = {'action' : 'qppr_delete_all_settings', 'security': z.securityDelete};
				$.post(z.ajaxurl, remove_qppr_all_data, function( response ) {
					if( response == 'success' ){
						d.location.href = z.adminURL+'?page=redirect-options&update=6';
					}else{
						d.location.href = z.adminURL+'?page=redirect-options&update=0';
					}
				});
			}
		});
		$( '.qppr-delete-regular' ).on( 'click', function(e){
			e.preventDefault();
			if( confirm( z.msgIndividualDeleteConfirm ) ){ 
				var remove_qppr_all_indiviual_data = {'action' : 'qppr_delete_all_iredirects', 'security': z.securityDelete};
				$.post(z.ajaxurl, remove_qppr_all_indiviual_data, function( response ) {
					if( response == 'success' ){
						d.location.href = z.adminURL+'?page=redirect-options&update=2';
					}else{
						d.location.href = z.adminURL+'?page=redirect-options&update=0';
					}
				});
			}
		});
		
		$( '.qppr-delete-quick' ).on( 'click', function(e){
			e.preventDefault();
			if( confirm( z.msgQuickDeleteConfirm ) ){ 
				var remove_qppr_all_quick_data = {'action' : 'qppr_delete_all_qredirects', 'security': z.securityDelete};
				$.post(z.ajaxurl, remove_qppr_all_quick_data, function(response) {
					if( response == 'success' ){
						d.location.href = z.adminURL+'?page=redirect-options&update=3';
					}else{
						d.location.href = z.adminURL+'?page=redirect-options&update=0';
					}
				});
			}
		});

		$( '#qppr_quick_save_form' ).on( 'submit', function(e){
			var obj = $( this ),
				reqs = $('input[name^="quickppr_redirects[request]"'),
				dest = $('input[name^="quickppr_redirects[destination]"'),
				err  = false;
			if( reqs[0].value == '' && dest[0].value == '' )
				err  = true;
			if( err ){
				e.preventDefault();
				alert( z.error );
				return false;
			}
			return true;
		});
		
		$('#qppr_quick_save_form').delegate('.delete-qppr','click', function(e){ 
			e.preventDefault(); 
			var rowID 	= $(this).data('rowid'),
				request = $('#'+rowID).children('.table-qppr-req').children('.qppr-request').text(),
				qrdata 	= {'action' : 'qppr_delete_quick_redirect','request': request,'security': z.security},
				qr 		= 1;
			if( confirm( z.msgDeleteConfirm ) ){ 
				$.post(z.ajaxurl, qrdata, function(r) {
					$('#'+rowID).remove(); 
				}).done(function() {
					$('.qppr-count-row').each(function(e){
						$( this ).text( qr + '.' );
						qr++;
					});
				});
			}
		}); 
		
		$(".edit-qppr").click(function(e){ 
			e.preventDefault(); 
			var r = $(this).data('rowid'),
				x = $('#'+r), 
				v = $('#qppr-edit-row-holder').children('td'); 
			$( '#' + r + ' td' ).addClass('editing');
			x.addClass('editing-redirect');
			v.clone().prependTo(x);
			var aa = x.children('.table-qppr-nwn.editing').children( '.qppr-newindow' ).text() == 'X' ? true : false , 
				bb = x.children('.table-qppr-nfl.editing').children( '.qppr-nofollow' ).text() == 'X' ? true : false ; 
			x.children('.table-qppr-req.cloned').children('.input-qppr-req').attr( 'value', x.children('.table-qppr-req.editing').children('.qppr-request').text());
			x.children('.table-qppr-des.cloned').children('.input-qppr-dest').attr( 'value', x.children('.table-qppr-des.editing').children('.qppr-destination').text());
			x.children('.table-qppr-nwn.cloned').children('.input-qppr-neww').prop( 'checked', aa );
			x.children('.table-qppr-nfl.cloned').children('.input-qppr-nofo').prop( 'checked', bb );
			x.children('.table-qppr-sav.cloned').children('.table-qppr-sav span').attr( 'data-rowid', r );
			x.children('.table-qppr-can.cloned').children('.table-qppr-can span').attr( 'data-rowid', r );
		}); 
		
		$(".qppr_quick_redirects_wrapper").delegate('.table-qppr-sav span.qpprfont-save', 'hover', function(e){
			if( $( '.active-saving' ).length != 0 && !$( this ).parent().parent().hasClass('active-saving'))
				$( this ).css( {'cursor':'no-drop','color':'#ff0000'} );
		});

		$(".qppr_quick_redirects_wrapper").delegate('.table-qppr-sav span.qpprfont-save', 'click', function(e){
			e.preventDefault();
			if( $( '.active-saving' ).length != 0 && !$( this ).parent().parent().hasClass('active-saving'))
				return false;
			var editRow 	= $('#'+$(this).data('rowid')),
				rowID 		= $(this).data('rowid'),
				requestOrig	= editRow.children('.table-qppr-req.editing').children('.qppr-request').data('qppr-orig-url'),
				request 	= editRow.children('.table-qppr-req.cloned').children('.input-qppr-req').val(),
				destination = editRow.children('.table-qppr-des.cloned').children('.input-qppr-dest').val(),
				newWin 		= editRow.children('.table-qppr-nwn.cloned').children('.input-qppr-neww:checked').val(),
				noFoll 		= editRow.children('.table-qppr-nfl.cloned').children('.input-qppr-nofo:checked').val();
			newWin = (typeof newWin == 'undefined' || newWin == 'undefined') ? 0 : newWin;
			noFoll = (typeof noFoll == 'undefined' || noFoll == 'undefined') ? 0 : noFoll;
			editRow.children('.cloned').remove();
			// do a little checking of the request to make sure is ok.
			var protocols	= z.protocols;
			var slash 		= request.substring(0, 1);
			var hasSlash 	= slash ==  '/' ? true : false;
			var protocol	= '';
			var protoLen	= -1;
			if( !hasSlash ){
				protoLen	= request.indexOf(":");
				protocol	= request.substring(0, protoLen); // first three of protocol
			}
			if( !hasSlash && $.inArray( protocol, protocols ) === -1 ){
				request = '/' + request;
			}

			$( '#qppr-edit-row-saving .qppr-saving-row' ).clone().prependTo( '#' + rowID );
			editRow.addClass( 'active-saving' );
			var save_data 	= {
				'action' 		: 'qppr_save_quick_redirect', 
				'row'			: rowID.replace('rowpprdel-',''), 
				'original'		: requestOrig,
				'request'		: request,
				'destination'	: destination,
				'newwin'		: newWin,
				'nofollow'		: noFoll,
				'security'		: z.security
			};
			
			$.post(z.ajaxurl, save_data, function(response) {
				var err = 0;
				if( response == 'error' ){
					alert(z.msgErrorSave);
					err = 1;
				}
				if(response == 'duplicate' ){
					alert(z.msgDuplicate);
					var dupRow = '#' + $( ".table-qppr-req:contains(" + request + ")" ).parent('tr').attr('id');
					$( dupRow ).addClass('qppr-duplicate');
					err = 1;
				}
				if(	err != 1 ){
					if(noFoll == 1){noFoll = 'X';}else{noFoll = '';}
					if(newWin == 1){newWin = 'X';}else{newWin = '';}
					editRow.children('.table-qppr-req.editing').children('.qppr-request').text(request);
					editRow.children('.table-qppr-des.editing').children('.qppr-destination').text(destination);
					editRow.children('.table-qppr-nfl.editing').children('.qppr-nofollow').text(noFoll);
					editRow.children('.table-qppr-nwn.editing').children('.qppr-newindow').text(newWin);
					editRow.children('.table-qppr-req.editing').children('.qppr-request').data('qppr-orig-url', requestOrig );
				}
				editRow.children('td').removeClass('editing');
				editRow.children('.qppr-saving-row').remove();
			}).done(function() {
				editRow.removeClass('editing-redirect active-saving');
				$( '.table-qppr-sav span.qpprfont-save' ).css( {'cursor':'','color':''} );
			});
		});
		$('tr[id^="rowpprdel"]').on('hover',function(){
			$(this).removeClass('qppr-duplicate');
		});
		$(".qppr_quick_redirects_wrapper").delegate('.table-qppr-can span.qpprfont-cancel','click', function(e){
			e.preventDefault();
			var rowID = $('#'+$(this).data('rowid'));
			rowID.children('.cloned').remove();
			rowID.children('td').removeClass('editing');
			rowID.removeClass('editing-redirect');
		});
		
		$("#hidepprjqmessage").click(function(e){
			e.preventDefault(); 
			var pprhidemessage_data = {'action' : 'qppr_pprhidemessage_ajax','pprhidemessage': 1,'scid': z.security};
			$.post(z.ajaxurl, pprhidemessage_data, function(response) {$('#usejqpprmessage').remove();}).done(function() {});
		});
		 
		$("#hidepprjqmessage2").click(function(e){ 
			e.preventDefault(); 
			var pprhidemessage_data = {'action' : 'qppr_pprhidemessage_ajax','pprhidemessage': 2,'scid': z.security};
			$.post(z.ajaxurl, pprhidemessage_data, function(response) {$('#usejqpprmessage2').remove();}).done(function() {});
		}); 
		
		$("#qppr-import-quick-redirects-button").click(function(e){
			e.preventDefault();
			$('#qppr_addto_form').css({'display':'none'});
			if($('#qppr_import_form').css('display')=='block'){
				$('#qppr_import_form').css({'display':'none'});
			}else{
				$('#qppr_import_form').css({'display':'block'});
			}
		});
		
		$("#qppr_addto_qr_button").click(function(e){
			$('#qppr_import_form').css({'display':'none'});
			if($('#qppr_addto_form').css('display')=='block'){
				$('#qppr_addto_form').css({'display':'none'});
			}else{
				$('#qppr_addto_form').css({'display':'block'});
			}
			e.preventDefault();
		});
		
		$("#import_redirects_add_qppr").click(function(e){
			if($("[name|=qppr_file_add]").attr('value')==''){
				e.preventDefault();
				alert(z.msgSelect);
				return false;
			}
		});
		
		$("#import-quick-redrects-file").click(function(e){
			if($("[name|=qppr_file]").attr('value')==''){
				e.preventDefault();
				alert(z.msgSelect);
				return false;
			}
		});
	});
})(jQuery, document, qpprData);

function qppr_check_file(fname){
	str		= fname.value.toUpperCase();
	suffix	= ".TXT";
	if(!(str.indexOf(suffix, str.length - suffix.length) !== -1)){
		alert( qpprData.msgFileType );
		fname.value	= '';
	}
}

function qppr_goOnConfirm(message, href) {
	if( confirm( message ) ){
		document.location.href = qpprData.adminURL+href;
	}
}