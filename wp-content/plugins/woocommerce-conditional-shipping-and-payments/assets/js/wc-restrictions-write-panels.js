/* global wc_restrictions_admin_params */

jQuery( function($) {

	$.fn.csp_select2 = function() {
		$( document.body ).trigger( 'wc-enhanced-select-init' );
	};

	$.fn.csp_scripts = function() {

		if ( wc_restrictions_admin_params.is_wc_version_gte_2_3 === 'yes' ) {
			$( this ).csp_select2();
		} else {
			$( this ).find( '.chosen_select' ).chosen();
		}

		$( this ).find( '.woocommerce-help-tip, .help_tip, .tips' ).tipTip( {
			'attribute' : 'data-tip',
			'fadeIn' : 50,
			'fadeOut' : 50,
			'delay' : 200
		} );
	};

	var $restrictions_data           = $( '#restrictions_data' ),
		$restrictions_toggle_wrapper = $restrictions_data.find( '.bulk_toggle_wrapper' ),
		$restrictions_wrapper        = $restrictions_data.find( '.wc-metaboxes' );

	/*---------------------*/
	/*  Restrictions       */
	/*---------------------*/

	if ( wc_restrictions_admin_params.post_id === '' ) {

		$restrictions_data.closest( 'table.form-table' ).removeClass( 'form-table' ).addClass( 'restrictions-form-table' );

		// Meta-Boxes - Open/close.
		$restrictions_data.on( 'click', '.wc-metabox > h3', function() {
			$( this ).next( '.wc-metabox-content' ).stop().slideToggle( 300 );
		} );

		$restrictions_data.on( 'click', '.wc-metabox > h3', function() {
			$( this ).parent( '.wc-metabox' ).toggleClass( 'closed' ).toggleClass( 'open' );
		} );

		if ( wc_restrictions_admin_params.is_wc_version_gte_2_3 === 'yes' ) {
			$restrictions_data.csp_select2();
		} else {
			$( '.chosen_select', $restrictions_data ).chosen();
		}

		$( '.wc-metabox', $restrictions_data ).each( function() {

			var p = $( this );
			var c = p.find( '.wc-metabox-content' );

			if ( p.hasClass( 'closed' ) ) {
				c.hide();
			}

		} );
	}

	// Restriction Remove.
	$restrictions_data.on( 'click', '.remove_row', function( e ) {

		var $parent = $( this ).parent().parent();

		$parent.find('*').off();
		$parent.remove();
		csp_row_indexes();

		e.preventDefault();

	} );

	// Restriction Keyup.
	$restrictions_data.on( 'keyup', 'textarea.short_description', function() {
		$( this ).closest( '.woocommerce_restriction' ).find( 'h3 .restriction_title_inner' ).text( $( this ).val() );
	} );

	// Restriction Expand.
	$restrictions_data.on( 'click', '.expand_all', function() {
		$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).show();
		return false;
	} );

	// Restriction Close.
	$restrictions_data.on( 'click', '.close_all', function() {
		$( this ).closest( '.wc-metaboxes-wrapper' ).find( '.wc-metabox > .wc-metabox-content' ).hide();
		return false;
	} );

	// Country Restriction Show/Hide States.
	$restrictions_data.on( 'change', '.exclude_states select', function() {
		if ( $( this ).val() === 'specific' ) {
			$( this ).closest( '.exclude_states' ).parent().children( '.excluded_states' ).show();
		} else {
			$( this ).closest( '.exclude_states' ).parent().children( '.excluded_states' ).hide();
		}
		return false;
	} );

	// Select all/none.
	$restrictions_data.on( 'click', '.wccsp_select_all', function() {
		$( this ).closest( '.select-field' ).find( 'select option' ).attr( 'selected', 'selected' );
		$( this ).closest( '.select-field' ).find( 'select' ).trigger( 'change' );
		return false;
	} );

	$restrictions_data.on( 'click', '.wccsp_select_none', function() {
		$( this ).closest( '.select-field' ).find( 'select option' ).removeAttr( 'selected' );
		$( this ).closest( '.select-field' ).find( 'select' ).trigger( 'change' );
		return false;
	} );

	// Restriction Add.
	var checkout_restrictions_metabox_count = $restrictions_wrapper.find( '.woocommerce_restriction' ).length;

	$restrictions_data.on( 'click', 'button.add_restriction', function () {

		// Check if restriction already exists and don't allow creating multiple rules if the restriction does not permit so.

		var restriction_id        = $( 'select.restriction_type', $restrictions_data ).val(),
			$applied_restrictions = $restrictions_wrapper.find( '.woocommerce_restriction_' + restriction_id ),
			$restrictions         = $restrictions_wrapper.find( '.woocommerce_restriction' );

		// If no option is selected, do nothing.
		if ( restriction_id === '' ) {
			return false;
		}

		var block_params = {};

		if ( wc_restrictions_admin_params.is_wc_version_gte_2_3 === 'yes' ) {
			block_params = {
				message: 	null,
				overlayCSS: {
					background: '#fff',
					opacity: 	0.6
				}
			};
		} else {
			block_params = {
				message: 	null,
				overlayCSS: {
					background: '#fff url(' + wc_restrictions_admin_params.wc_plugin_url + '/assets/images/ajax-loader.gif) no-repeat center',
					opacity: 	0.6
				}
			};
		}

		$restrictions_data.block( block_params );

		checkout_restrictions_metabox_count++;

		var data = {
			action: 		'woocommerce_add_checkout_restriction',
			post_id: 		wc_restrictions_admin_params.post_id,
			index: 			checkout_restrictions_metabox_count,
			restriction_id: restriction_id,
			applied_count: 	$applied_restrictions.length,
			count: 			$restrictions.length,
			security: 		wc_restrictions_admin_params.add_restriction_nonce
		};

		setTimeout( function() {

			$.post( wc_restrictions_admin_params.wc_ajax_url, data, function ( response ) {

				if ( response.errors.length > 0 ) {

					window.alert( response.errors.join( '\n\n' ) );

				} else {

					$restrictions_wrapper.append( response.markup );

					var $added = $restrictions_wrapper.find( '.woocommerce_restriction' ).last();

					$added.csp_scripts();

					$added.data( 'conditions_count', 0 );

					$restrictions_toggle_wrapper.removeClass( 'disabled' );
				}

				$restrictions_data.unblock();
				$restrictions_data.trigger( 'woocommerce_restriction_added', response );

			}, 'json' );

		}, 250 );

		return false;
	} );

	/*---------------------*/
	/*  Conditions         */
	/*---------------------*/

	var condition_row_templates         = {},
		condition_row_content_templates = {};

	/**
	 * Runtime cache for 'wp.template' calls: Condition row content templates.
	 */
	function get_csp_condition_row_content_template( restriction_id, condition_id ) {

		var template = false;

		if ( typeof( condition_row_content_templates[ restriction_id ] ) === 'object' && typeof( condition_row_content_templates[ restriction_id ][ condition_id ] ) === 'function' ) {
			template = condition_row_content_templates[ restriction_id ][ condition_id ];
		} else {
			template = wp.template( 'wc_csp_restriction_' + restriction_id + '_condition_' + condition_id + '_content' );
			if ( typeof( condition_row_content_templates[ restriction_id ] ) === 'undefined' ) {
				condition_row_content_templates[ restriction_id ] = {};
			}
			condition_row_content_templates[ restriction_id ][ condition_id ] = template;
		}

		return template;
	}

	/**
	 * Runtime cache for 'wp.template' calls: Condition row templates.
	 */
	function get_csp_condition_row_template( restriction_id ) {

		var template = false;

		if ( typeof( condition_row_templates[ restriction_id ] ) === 'function' ) {
			template = condition_row_templates[ restriction_id ];
		} else {
			template = wp.template( 'wc_csp_restriction_' + restriction_id + '_condition_row' );
			condition_row_templates[ restriction_id ] = template;
		}

		return template;
	}

	// Condition Add.
	$restrictions_wrapper.find( '.woocommerce_restriction' ).each( function() {
		var conditions_count = $( this ).find( '.restriction_conditions .condition_row' ).length;
		$( this ).data( 'conditions_count', conditions_count );
	} );

	$restrictions_data.on( 'click', 'button.add_condition', function () {

		var $restriction                           = $( this ).closest( '.woocommerce_restriction' ),
			restriction_id                         = $restriction.data( 'restriction_id' ),
			restriction_index                      = parseInt( $restriction.data( 'index' ) ),
			condition_index                        = parseInt( $restriction.data( 'conditions_count' ) ),
			condition_row_template                 = get_csp_condition_row_template( restriction_id ),
			condition_row_default_content_template = get_csp_condition_row_content_template( restriction_id, 'default' );

		if ( false === condition_row_template || false === condition_row_default_content_template ) {
			return false;
		}

		var $new_condition_row_content = condition_row_default_content_template( {
			restriction_index: restriction_index,
			condition_index:   condition_index
		} );

		var $new_condition_row = condition_row_template( {
			condition_index:   condition_index,
			condition_content: $new_condition_row_content
		} );

		$restriction.data( 'conditions_count', condition_index + 1 );

		$restriction.find( '.restriction_conditions tbody' ).append( $new_condition_row );

		var $added = $restriction.find( '.restriction_conditions' ).last();

		$added.csp_scripts();

		return false;
	} );

	// Condition Remove.
	$restrictions_data.on( 'click', 'button.remove_conditions', function () {

		var $remove = $( this ).closest( '.restriction_conditions' ).find( '.condition_row input.remove_condition:checked' );

		$remove.closest( '.condition_row' ).remove();

		return false;
	} );

	// Condition Change.
	$restrictions_data.on( 'change', 'select.condition_type', function () {

		var $restriction                   = $( this ).closest( '.woocommerce_restriction' ),
			restriction_id                 = $restriction.data( 'restriction_id' ),
			restriction_index              = parseInt( $restriction.data( 'index' ) ),
			$condition                     = $( this ).closest( '.condition_row' ),
			condition_id                   = $( this ).val(),
			condition_index                = parseInt( $condition.data( 'condition_index' ) ),
			condition_row_content_template = get_csp_condition_row_content_template( restriction_id, condition_id );

		if ( false === condition_row_content_template ) {
			return false;
		}

		var $new_condition_row_content = condition_row_content_template( {
			restriction_index: restriction_index,
			condition_index:   condition_index
		} );

		$condition.find( '.condition_content' ).html( $new_condition_row_content ).addClass( 'added' );

		var $added = $condition.find( '.added' );

		$added.csp_scripts();

		$added.removeClass( 'added' );

		return false;
	} );

	function csp_row_indexes() {
		var has_restrictions = false;
		$restrictions_wrapper.find( '.woocommerce_restriction' ).each( function( index, el ) {
			$( '.position', el ).val( index );
			$( '.restriction_title_index', el ).html( index + 1 );
			has_restrictions = true;
		} );
		if ( ! has_restrictions ) {
			$restrictions_toggle_wrapper.addClass( 'disabled' );
		} else {
			$restrictions_toggle_wrapper.removeClass( 'disabled' );
		}
	}

	function csp_metaboxes_init() {

		// Initial order.
		var woocommerce_checkout_restrictions = $restrictions_wrapper.find( '.woocommerce_restriction' ).get();

		woocommerce_checkout_restrictions.sort( function( a, b ) {
		   var compA = parseInt( $(a).attr( 'data-index' ) );
		   var compB = parseInt( $(b).attr( 'data-index' ) );
		   return ( compA < compB ) ? -1 : ( compA > compB ) ? 1 : 0;
		} );

		$( woocommerce_checkout_restrictions ).each( function( idx, itm ) {
			$restrictions_wrapper.append(itm);
		} );

		csp_row_indexes();

		// Component ordering.
		$restrictions_wrapper.sortable( {
			items:'.woocommerce_restriction',
			cursor:'move',
			axis:'y',
			handle: 'h3',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start:function(event,ui){
				ui.item.css( 'background-color','#f6f6f6' );
			},
			stop:function(event,ui){
				ui.item.removeAttr( 'style' );
				csp_row_indexes();
			}
		} );
	}

	// Init metaboxes.
	csp_metaboxes_init();
} );
