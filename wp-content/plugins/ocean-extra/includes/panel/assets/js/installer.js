var oe_installer = oe_installer || {};

jQuery( document ).ready( function( $ ) {

	"use strict";

	var is_loading = false;

   /**
	* Install the plugin
	*/
	oe_installer.install_plugin = function( el, plugin ) {

	   	// Confirm activation
	   	var r = confirm( oe_installer_localize.install_now );

	      if ( r ) {

	      	is_loading = true;
	      	el.addClass( 'installing' );

	      	$.ajax( {
		   		type 	: 'POST',
		   		url 	: oe_installer_localize.ajax_url,
		   		data 	: {
		   			action 		: 'oe_plugin_installer',
		   			plugin 		: plugin,
		   			nonce 		: oe_installer_localize.admin_nonce,
		   			dataType 	: 'json'
		   		},

		   		success: function( data ) {
			   		if ( data  ){
				   		if ( data.status === 'success' ) {
				   			el.attr( 'class', 'activate button button-primary' );
					   		el.html( oe_installer_localize.activate_btn );
				   		} else {
				   			el.removeClass( 'installing' );
			   			}
			   		} else {
			   			el.removeClass( 'installing' );
			   		}
			   		is_loading = false;
		   		},

		   		error: function( xhr, status, error ) {
		      		console.log( status );
		      		el.removeClass( 'installing' );
		      		is_loading = false;
		   		}
		   	} );

	   	}
	}

   /**
	* Activate the plugin
	*/
	oe_installer.activate_plugin = function( el, plugin ) {

		$.ajax( {
	   		type 	: 'POST',
	   		url 	: oe_installer_localize.ajax_url,
	   		data 	: {
	   			action 		: 'oe_plugin_activation',
	   			plugin 		: plugin,
	   			nonce 		: oe_installer_localize.admin_nonce,
	   			dataType 	: 'json'
	   		},

	   		success: function( data ) {
		   		if ( data ) {
			   		if ( data.status === 'success' ) {
				   		el.attr( 'class', 'installed button disabled' );
				   		el.html( oe_installer_localize.installed_btn );
			   		}
		   		}
		   		is_loading = false;
	   		},

	   		error: function( xhr, status, error ) {
	      		console.log( status );
	      		is_loading = false;
	   		}
	   	} );

	};

   /**
	* Activate the premium lugin
	*/
	oe_installer.activate_premium_plugin = function( el, plugin ) {

		$.ajax( {
	   		type 	: 'POST',
	   		url 	: oe_installer_localize.ajax_url,
	   		data 	: {
	   			action 		: 'oe_premium_plugin_activation',
	   			plugin 		: plugin,
	   			nonce 		: oe_installer_localize.admin_nonce,
	   			dataType 	: 'json'
	   		},

	   		success: function( data ) {
		   		if ( data ) {
			   		if ( data.status === 'success' ) {
				   		el.attr( 'class', 'installed button disabled' );
				   		el.html( oe_installer_localize.installed_btn );
			   		}
		   		}
		   		is_loading = false;
	   		},

	   		error: function( xhr, status, error ) {
	      		console.log( status );
	      		is_loading = false;
	   		}
	   	} );

	};

	/**
	* Install/Activate Button Click
	*/
	$( document ).on( 'click', '.oe-plugin-installer a.button:not(.premium-link)', function( e ) {
	   	var el 		= $( this ),
	   		plugin 	= el.data( 'slug' );

	   	e.preventDefault();

	   	if ( ! el.hasClass( 'disabled' ) ) {

	      	if ( is_loading ) return false;

		   	// Installation
	      	if ( el.hasClass( 'install' ) ) {
		      	oe_installer.install_plugin( el, plugin );
		   	}

		   	// Activation
		   	if ( el.hasClass( 'activate' ) ) {
			   	oe_installer.activate_plugin( el, plugin );
			}
	   	}
	} );

	/**
	* Activate Premium Extension
	*/
	$( document ).on( 'click', '.oe-plugin-installer a.button.premium-activation', function( e ) {
	   	var el 		= $( this ),
	   		plugin 	= el.data( 'slug' );

	   	e.preventDefault();

	   	if ( ! el.hasClass( 'disabled' ) ) {

	      	if ( is_loading ) return false;

		   	// Activation
		   	if ( el.hasClass( 'activate' ) ) {
			   	oe_installer.activate_premium_plugin( el, plugin );
			}
	   	}
	} );

});
