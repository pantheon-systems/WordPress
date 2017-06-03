(function($){

	//Define global functions to use them in other plugins

	//Sets the current tab in navigation menu
	window.et_dashboard_set_current_tab = function set_current_tab( $tab_id, $section ) {
		var tab = $( 'div.' + $tab_id );
		var current = $( 'a.current' );

		$( current ).removeClass( 'current' );
		$( 'a#' + $tab_id ).addClass( 'current' );

		$( 'div.et_dashboard_tab_content' ).removeClass( 'et_tab_selected' );
		$( tab ).addClass( 'et_tab_selected' );

		//If the tab is in opened section, then we don't need to toggle current_section class
		if ( '' != $section ) {
			var current_section = $( 'ul.current_section' );

			current_section.removeClass( 'current_section' );
		}

		//Hide save button from the header section since there is nothing to save
		if ( 'header' == $section ) {
			$( '.et_dashboard_save_changes' ).css( { 'display' : 'none' } );
		}

		if ( 'side' == $section ) {
			var $parent_menu = $( 'a#' + $tab_id ).parent().parent(),
				$this_main_menu = $parent_menu.closest( 'li' );

			$parent_menu.toggleClass( 'current_section' );
			$( '.et_dashboard_save_changes' ).css( { 'display' : 'block' } );
			$( '#et_dashboard_navigation > ul > li' ).removeClass( 'et_active_menu' );
			$this_main_menu.addClass( 'et_active_menu' );
		}

		$( '#et_dashboard_content' ).removeAttr( 'class' );
		$( '#et_dashboard_content' ).addClass( 'current_tab_' + $tab_id );
	}

	//Generates image upload window
	window.et_dashboard_image_upload = function image_upload( $upload_button ) {
		$upload_button.click( function( event ) {
			var $this_el = $(this);

			event.preventDefault();

			et_file_frame = wp.media.frames.et_file_frame = wp.media({
				title: $this_el.data( 'choose' ),
				library: {
					type: $this_el.data( 'type' )
				},
				button: {
					text: $this_el.data( 'update' ),
				},
				multiple: false
			});

			et_file_frame.on( 'select', function() {
				var attachment = et_file_frame.state().get( 'selection' ).first().toJSON();

				$this_el.siblings( '.et-dashboard-upload-field' ).val( attachment.url );
				$this_el.siblings( '.et-dashboard-upload-id' ).val( attachment.id );

				et_dashboard_generate_preview_image( $this_el );
			});

			et_file_frame.open();
		} );
	}

	//Generates preview for image upload option
	window.et_dashboard_generate_preview_image = function generate_preview_image( $upload_button ) {
		var $upload_field = $upload_button.siblings( '.et-dashboard-upload-field' ),
			$preview = $( '.et-dashboard-upload-preview' ),
			image_url = '';

			//check wheter we have valid image URL in the input field
			if ( /^([a-z]([a-z]|\d|\+|-|\.)*):(\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?((\[(|(v[\da-f]{1,}\.(([a-z]|\d|-|\.|_|~)|[!\$&'\(\)\*\+,;=]|:)+))\])|((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)(:\d*)?)(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*|(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)|((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)){0})(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test( $upload_field.val().trim() ) ) {
					image_url = $upload_field.val().trim();
			}

		if ( $upload_button.data( 'type' ) !== 'image' ) return;

		if ( image_url === '' ) {
			if ( $preview.length ) $preview.remove();

			return;
		}

		if ( ! $preview.length ) {
			$upload_button.after( '<div class="et-dashboard-upload-preview"><img src="" width="300" height="300" /></div>' );
			$preview = $upload_field.parent().find( '.et-dashboard-upload-preview' );
		}

		$preview.find( 'img' ).attr( 'src', image_url );
	}

	//Displays warning message. HTML of warning message is input parameter.
	window.et_dashboard_display_warning = function display_warning( $warn_window ) {
		if ( '' == $warn_window ){
			return;
		}

		$( '#wpwrap' ).append( $warn_window );
	}

	//Generates warning pop up
	window.et_dashboard_generate_warning = function generate_warning( $message, $link, ok_text, custom_btn_text, custom_btn_link, custom_btn_class ){
		var link = '' == $link ? '#' : $link;
		$.ajax({
			type: 'POST',
			url: dashboardSettings.ajaxurl,
			data: {
				action : 'et_dashboard_generate_warning',
				message : $message,
				ok_link : link,
				ok_text : ok_text,
				custom_button_text : custom_btn_text,
				custom_button_link : custom_btn_link,
				custom_button_class : custom_btn_class,
				generate_warning_nonce : dashboardSettings.generate_warning
			},
			success: function( data ){
				window.et_dashboard_display_warning( data );
			}
		});
	}

	//Checks conditional options and toggles them
	window.et_dashboard_check_conditional_options = function check_conditional_options( $current_trigger, $is_load ){
		var all_triggers = $current_trigger.data( "enables" ).split( '#' ),
			option_value = '';

		if ( 0 < $current_trigger.find( 'select' ).length )  {
			option_value = $current_trigger.find( 'select' ).val();
		} else {
			option_value = true == $current_trigger.children( 'input' ).prop( 'checked' ) ? 'true' : 'false';
		}

		$.each( all_triggers, function( index, option_name ){
			$option_enabled = false;
			var current_option = $( '[name="et_dashboard[' + option_name + ']"]' );
				if ( current_option.hasClass( 'wp-color-picker' ) || 'radio' == current_option.attr( 'type' ) ) {
					current_option = current_option.hasClass( 'wp-color-picker' ) ? current_option.parent().parent().parent() : current_option.parent().parent();
				} else {
					current_option = current_option.parent().length ? current_option.parent() : $( '[data-name="et_dashboard[' + option_name + ']"]' );
				}

			var	values_array = String( current_option.data( 'condition' ) ).split( '#' );

			$.each( values_array, function( key, value ){
				if ( value == option_value ) {
					current_option.removeClass( 'et_dashboard_hidden_option' ).addClass( 'et_dashboard_visible_option' );

					increment_triggers = undefined == current_option.data( 'triggers_count' ) ? 0 : parseInt( current_option.data( 'triggers_count' ) );
					increment_triggers++;
					current_option.data( 'triggers_count', increment_triggers );
					current_option.data( 'just_enabled', true );

					$option_enabled = true;
				} else {
					if ( false == $option_enabled && ( ( false == $is_load ) || ( true == $is_load && true != current_option.data( 'just_enabled' ) ) ) ) {
						var triggers_count = undefined == current_option.data( 'triggers_count' ) ? 0 : parseInt( current_option.data( 'triggers_count' ) );
							triggers_count = 0 == triggers_count ? 0 : triggers_count - 1;
							current_option.data( 'triggers_count', triggers_count );

						if ( 0 == triggers_count ) {
							current_option.addClass( 'et_dashboard_hidden_option' ).removeClass( 'et_dashboard_visible_option' );
						}
					}
				}
			});
		});
	}

	window.et_dashboard_save = function et_dashboard_save( $button ) {
		tinyMCE.triggerSave();
		var options_fromform = $( '.' + dashboardSettings.plugin_class + ' #et_dashboard_options' ).serialize();
		$spinner = $button.parent().find( '.spinner' );
		$options_subtitle = $button.data( 'subtitle' );
		$.ajax({
			type: 'POST',
			url: dashboardSettings.ajaxurl,
			data: {
				action : dashboardSettings.plugin_class + '_save_settings',
				options : options_fromform,
				options_sub_title : $options_subtitle,
				save_settings_nonce : dashboardSettings.save_settings
			},
			beforeSend: function ( xhr ){
				$spinner.addClass( 'et_dashboard_spinner_visible' );
			},
			success: function( data ){
				$spinner.removeClass( 'et_dashboard_spinner_visible' );
				window.et_dashboard_display_warning( data );
			}
		});
	}

	$( document ).ready( function() {
		var url = window.location.href,
			tab_link = url.split( '#tab_' )[1],
			$et_modal_window;

		//Check whether tab_id specified in the URL, if not - set the first tab from the navigation as a current tab.
		if ( undefined != tab_link ) {
			var section = ( -1 != tab_link.indexOf( 'header' ) ) ? 'header' : 'side';

			window.et_dashboard_set_current_tab( tab_link, section );
		} else {
			window.et_dashboard_set_current_tab ( $( 'div#et_dashboard_navigation > ul > li > ul > li > a' ).first().attr( 'id' ), 'side' );
		}

		/* Create checkbox/toggle UI based off form data */

		$( 'body' ).on( 'click', 'div.et_dashboard_multi_selectable', function() {
			var checkbox = $( this ).children( 'input' );

			checkbox.prop( 'checked' ) == false ? checkbox.prop( 'checked', true ) : checkbox.prop( 'checked', false );
			$( this ).toggleClass( 'et_dashboard_selected et_dashboard_just_selected' );
			$( this ).mouseleave( function() {
			 	$( this ).removeClass( 'et_dashboard_just_selected' );
			});
		});

		$( 'body' ).on( 'click', 'div.et_dashboard_single_selectable', function() {
			var tabs = $( this ).parents( '.et_dashboard_row' ).find( 'div.et_dashboard_single_selectable' ),
				inputs = $( this ).parents( '.et_dashboard_row' ).find( 'input' );

			tabs.removeClass( 'et_dashboard_selected' );
			inputs.prop( 'checked', false );
			$( this ).toggleClass( 'et_dashboard_selected' );
			$( this ).children( 'input' ).prop( 'checked', true );
		});

		/* Tabs System */

		// Adding href to tabs of each parent element to store the link of current tab in URL properly
		$( 'div#et_dashboard_navigation > ul > li > a' ).each( function() {
			var $this_el = $( this );
			$this_el.attr( 'href', '#tab_' + $this_el.parent().find( 'ul > li > a' ).first().attr( 'id' ) );
		});

		$( 'body' ).on( 'click', 'div#et_dashboard_navigation > ul > li > a', function() {
			window.et_dashboard_set_current_tab ( $( this ).parent().find( 'ul > li > a' ).first().attr( 'id' ), 'side');
		});

		$( 'body' ).on( 'click', '#et_dashboard_navigation ul li ul li > a', function() {
			window.et_dashboard_set_current_tab ( $( this ).attr( 'id' ), '' );
		});

		$( 'body' ).on( 'click', 'div#et_dashboard_header > ul > li > a', function() {
			window.et_dashboard_set_current_tab ( $( this ).attr( 'id' ), 'header' );
		});


		$( 'body' ).on( 'click', '.et_dashboard_close', function(){
			var modal_container = $( this ).parent().parent().parent();

			//Remove the modal container of warning or hide the modal of networks picker
			if ( modal_container.hasClass( 'et_dashboard_warning' ) ) {
				modal_container.remove();
			} else {
				modal_container.css( { 'z-index' : '-1' , 'display' : 'none' } );
			}
		});

		//Handle click on the OK button in warning window
		$( 'body' ).on( 'click', '.et_dashboard_ok', function(){
			var this_el = $( this ),
				link = this_el.attr( 'href' ),
				main_container = this_el.parent().parent().parent();

			main_container.remove();

			//If OK button is a tab link, then open the tab
			if ( -1 != link.indexOf( '#tab' ) ) {
				var tab_link = link.split( '#tab_' )[1],
					section = ( -1 != tab_link.indexOf( 'header' ) ) ? 'header' : 'side';

				window.et_dashboard_set_current_tab( tab_link, section );

				return false;
			}

			//Do nothing if there is no link in the OK button
			if ( '#' == link ) {
				return false;
			}

		});

		$( 'body' ).on( 'click', '.et_dashboard_save_changes:not(.et_dashboard_custom_save) button', function() {
			window.et_dashboard_save( $( this ) );
			return false;
		});

		$( '.et-dashboard-color-picker' ).wpColorPicker();

		$( 'body' ).on( 'click', '.et_dashboard_conditional input[type="checkbox"]', function() {
			window.et_dashboard_check_conditional_options( $( this ).parent(), false );
		});

		$( 'body' ).on( 'change', '.et_dashboard_conditional select', function() {
			window.et_dashboard_check_conditional_options( $( this ).parent(), false );
		});

		if ( $( '.et_dashboard_conditional' ).length ) {
			$( '.et_dashboard_conditional' ).each( function() {
				window.et_dashboard_check_conditional_options( $( this ), true );
			});
		}

		$( 'body' ).on( 'click', '.et_dashboard_form span.et_dashboard_more_info', function() {
			$( this ).find( '.et_dashboard_more_text' ).fadeToggle( 400 );
		});

		if ( $('.et-dashboard-upload-button').length ) {
			var upload_button = $('.et-dashboard-upload-button');

			et_dashboard_image_upload( upload_button );

			upload_button.siblings( '.et-dashboard-upload-field' ).on( 'input', function() {
				et_dashboard_generate_preview_image( $(this).siblings( '.et-dashboard-upload-button' ) );
				$(this).siblings( '.et-dashboard-upload-id' ).val('');
			} );

			upload_button.siblings( '.et-dashboard-upload-field' ).each( function() {
				et_dashboard_generate_preview_image( $(this).siblings( '.et-dashboard-upload-button' ) );
			} );
		}

		$( 'body' ).on( 'focusin', '.et_dashboard_search_posts', function() {
			var $this_input = $( this );
			$this_input.closest('.et_dashboard_form').find( '.et_dashboard_live_search_res' ).addClass( 'visible_search_res' );
			if ( ! $this_input.hasClass( 'already_triggered' ) ) {
				$this_input.addClass( 'already_triggered' );

				handle_live_search( $this_input, 0, 1, true );
			}
		});

		$( document ).click( function() {
			$( '.et_dashboard_live_search_res' ).removeClass( 'visible_search_res' );
		});

		$( 'body' ).on( 'click', '.et_dashboard_search_posts', function() {
			return false;
		});

		$( 'body' ).on( 'input', '.et_dashboard_search_posts', function() {
			handle_live_search( $( this ), 500, 1, true );
		});

		$( 'body' ).on( 'click', '.et_dashboard_search_results li', function() {
			var $this_item = $( this );

			if ( ! $this_item.hasClass( 'et_dashboard_no_res' ) ) {
				var $text = $this_item.text(),
					$id = $this_item.data( 'post_id' ),
					$main_container = $this_item.closest('.et_dashboard_form'),
					$display_box = $main_container.find( '.et_dashboard_selected' ),
					$value_field = $main_container.find( 'input[type="hidden"]' ),
					$new_item = '<span data-post_id="' + $id + '">' + $text + '<span class="et_dashboard_menu_remove"></span></span>';

				if ( -1 === $display_box.html().indexOf( 'data-post_id="' + $id + '"' ) ) {
					$display_box.append( $new_item );

					if ( '' === $value_field.val() ) {
						$value_field.val( $id );
					} else {
						$value_field.val( function( index, value ) {
							return value + "," + $id;
						});
					}
				}
			}

			return false;
		});

		$( 'body' ).on( 'mousewheel DOMMouseScroll', '.et_dashboard_search_results', function() {
			var $this_el = $( this ),
				$page = typeof $this_el.data( 'page' ) === 'undefined' ? 1 : $this_el.data( 'page' ),
				$max_scroll = typeof $this_el.data( 'scroll' ) === 'undefined' ? 0 : $this_el.data( 'scroll' );

			if ( ( 0 === $this_el.scrollTop() % 200 ) && $this_el.scrollTop() > $max_scroll ) {
				$this_el.data( 'page', $page + 1 );
				$this_el.data( 'scroll', $this_el.scrollTop() );
				handle_live_search( $this_el.closest('.et_dashboard_form').find( '.et_dashboard_search_posts' ), 0, $page + 1, false );
			}
		});

		$( 'body' ).on( 'click', '.et_dashboard_selected span.et_dashboard_menu_remove', function() {
			var $this_item = $( this ).parent(),
				$value_field = $this_item.closest('.et_dashboard_form').find( 'input[type="hidden"]' ),
				$value_string = $value_field.val(),
				$id = $this_item.data( 'post_id' );
			if ( -1 !== $value_string.indexOf( $id ) ) {
				var $string_to_replace = -1 !== $value_string.indexOf( ',' + $id ) ? ',' + $id : $id + ',',
					$string_to_replace = -1 !== $value_string.indexOf( ',' ) ? $string_to_replace : $id,
					$new_value = $value_string;

				$new_value = $value_string.replace( $string_to_replace, '' );
				$value_field.val( $new_value );
			}

			$this_item.remove();

			return false;
		});

		function handle_live_search( $input, $delay, $page, $full_content ) {
			var $this_input = $input,
				$search_value = $this_input.val(),
				$post_type = $this_input.data( 'post_type' );

			setTimeout( function(){
				if ( $search_value === $this_input.val() ) {
					$.ajax({
						type: 'POST',
						url: dashboardSettings.ajaxurl,
						data: {
							action : 'et_dashboard_execute_live_search',
							dashboard_search : dashboardSettings.search_nonce,
							dashboard_live_search : $search_value,
							dashboard_post_type : $post_type,
							dashboard_page : $page,
							dashboard_full_content : $full_content
						},
						beforeSend: function( data ) {
							$this_input.parent().find( '.spinner' ).addClass( 'et_dashboard_spinner_visible' );
						},
						success: function( data ) {
							$this_input.parent().find( '.spinner' ).removeClass( 'et_dashboard_spinner_visible' );
							if ( true == $full_content ) {
								$this_input.closest('.et_dashboard_form').find( '.et_dashboard_search_results' ).replaceWith( data );
							} else {
								$this_input.closest('.et_dashboard_form').find( '.et_dashboard_search_results' ).append( data );
							}
						}
					});
				}
			}, $delay );
		}

    });

})(jQuery)