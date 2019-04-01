(function( $ ) {

	/**
	 * The list table used on the current page.
	 *   plugins.php     : .wp-list-table.plugins
	 *   update-core.php : #update-plugins-table
	 * @type {Object}
	 */
	var $listTable = $( '#update-plugins-table,.wp-list-table.plugins' );

	/**
	 * Class representing a single plugin in the context of a list table of plugin updates.
	 *
	 * @param {object} data
	 * @param {Plugin|undefined} parent
	 * @constructor
	 */
	var Plugin = function( data, parent ) {

		/**
		 * Data provided by the server.
		 * @type {Object}
		 */
		this.data = data;

		/**
		 * Parent Plugin instance, if representing an add-on.
		 * @type {Plugin|undefined}
		 */
		this.parent = parent;

		/**
		 * Object for the checkbox allowing the plugin to be submitted for update.
		 * @type {Object} jQuery
		 */
		this.$check = $listTable.find( '.check-column [value="' + data.basename + '"]' );

		/**
		 * Object for the primary list table row for this plugin.
		 * @type {Object} jQuery
		 */
		this.$row = this.$check.closest( 'tr' );

		/**
		 * Object for the container which holds update notices for this plugin.
		 * This container represents the main difference between the two list tables.
		 *
		 * This property is only initialized for plugins with license errors.
		 *
		 * @type {Object} jQuery
		 */
		this.$noticeContainer = null;
	};

	/**
	 * Get the html for this plugin's check license again link.
	 *
	 * @returns {string}
	 */
	Plugin.prototype.checkAgainLink = function() {
		return ' <a class="dbrains-check-my-licence-again" href="#">' + dbrains.strings.check_license_again + '</a>';
	};

	/**
	 * Does this plugin have any errors for its license?
	 *
	 * @returns {boolean}
	 */
	Plugin.prototype.hasLicenseErrors = function() {
		var license = this.isAddon() ? this.parent.data.license : this.data.license;

		return ! _.isEmpty( license.errors );
	};

	/**
	 * Disable the plugin's list table checkbox.
	 */
	Plugin.prototype.disableUpdateCheckbox = function() {
		if ( $listTable.is( '#update-plugins-table' ) ) {
			this.$check.prop( {
				disabled: true,
				checked: false
			} );
		}
	};

	/**
	 * Enable the plugin's list table checkbox.
	 */
	Plugin.prototype.enableUpdateCheckbox = function() {
		if ( $listTable.is( '#update-plugins-table' ) ) {
			this.$check.prop( {
				disabled: false
			} );
		}
	};

	/**
	 * Set the update notice with the given html.
	 *
	 * @param {string} html
	 */
	Plugin.prototype.setNotice = function( html ) {
		this.$noticeContainer.find( '.as3cf-license-notice' ).remove();
		this.$noticeContainer.append(
			'<div class="as3cf-license-notice update-message notice inline notice-warning notice-alt">' +
				'<p class="as3cf-before">' + html + this.checkAgainLink() + '</p>' +
			'</div>'
		);
	};

	/**
	 * Fade out our license notice and remove it, then restore WP update notices, if any.
	 */
	Plugin.prototype.clearNotice = function() {
		var plugin  = this;
		var $notice = this.$noticeContainer.find( '.as3cf-license-notice' );

		$.when( $notice.fadeOut() ).done( function() {
			$notice.remove();
			plugin.$row.children().css( 'box-shadow', '' );
			plugin.$noticeContainer.find( '.update-message' ).show();
		} );
	};

	/**
	 * Render the plugin's state to the DOM.
	 */
	Plugin.prototype.render = function() {
		if ( this.hasLicenseErrors() ) {
			this.initNoticeContainer();
			this.disableUpdateCheckbox();
			this.$noticeContainer.find( '.update-message:not(".as3cf-license-notice")' ).hide();
			this.setNotice( this.getLicenseNotice() );
		} else if ( this.$noticeContainer ) {
			this.enableUpdateCheckbox();
			this.clearNotice();
		}

		if ( ! this.isAddon() ) {
			_.invoke( this.addons, 'render' );
		}
	};

	/**
	 * Initialize the notice container.
	 */
	Plugin.prototype.initNoticeContainer = function() {
		if ( this.$noticeContainer ) {
			return;
		}

		// Check if it is the Plugins update table
		if ( ! $listTable.is( '#update-plugins-table' ) ) {

			// Check for an existing plugin-update notice row to use.
			var $noticeRow = $listTable.find( '.plugin-update-tr[data-plugin="' + this.data.basename + '"]' );

			if ( $noticeRow.length ) {
				this.$noticeContainer = $noticeRow.find( '.plugin-update' );
			} else {

				// If there is no plugin-update row, the plugin did not have an update, so inject a row to use.
				this.$row.after(
					'<tr class="plugin-update-tr ' + this.$row.attr( 'class' ) + '" ' +
						'id="' + this.data.slug + '-update" ' +
						'data-slug="' + this.data.slug + '" ' +
						'data-plugin="' + this.data.basename + '" ' +
					'>' +
						'<td colspan="3" class="plugin-update colspanchange"></td>' +
					'</tr>'
				);

				// The border between rows comes from a box-shadow on the children of the row above
				this.$row.children().css( 'box-shadow', 'none' );

				this.$noticeContainer = $listTable.find( '.plugin-update-tr[data-plugin="' + this.data.basename + '"] .plugin-update' );
			}
		} else {
			// Updates
			this.$noticeContainer = this.$row.find( '.plugin-title' );
		}

		// Finally, bind the click handler for ajax license checking
		this.$noticeContainer.on( 'click', '.dbrains-check-my-licence-again', _.bind( this.checkLicenseAgain, this ) );
	};

	/**
	 * Get the text for the license notice.
	 *
	 * @returns {string}
	 */
	Plugin.prototype.getLicenseNotice = function() {
		if ( this.isAddon() ) {
			return dbrains.strings.requires_parent_license.replace( '%s', this.parent.data.name );
		}

		return _.values( this.data.license.errors ).join( '' );
	};

	/**
	 * Event listener callback to handle the check the license again for this plugin.
	 *
	 * @param {Event} event
	 */
	Plugin.prototype.checkLicenseAgain = function( event ) {
		var plugin   = this;
		var $link    = $( event.target );
		var $spinner = $( '<span class="spinner is-active dbrains-licence-check-spinner" style="float:none; margin-top: -3px;"></span>' );

		event.preventDefault();

		$link.hide().after( $spinner );

		this.ajaxCheckLicense()
			.done( function( data ) {
				if ( plugin.isAddon() ) {
					plugin.parent.data.license.errors = data.errors;
					plugin.parent.render();
				} else {
					plugin.data.license.errors = data.errors;
					plugin.render();
				}
			} )
			.fail( function() {
				plugin.setNotice( dbrains.strings.license_check_problem );
			} )
			.always( function() {
				$spinner.remove();
			} )
		;
	};

	/**
	 * Send an ajax request to the server to check the plugin's license.
	 *
	 * @return {jqXHR} jQuery XHR Promise object
	 */
	Plugin.prototype.ajaxCheckLicense = function() {
		var prefix = this.isAddon() ? this.parent.data.prefix : this.data.prefix;

		return $.ajax( {
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			cache: false,
			data: {
				action: prefix + '_check_licence',
				nonce: dbrains.nonces.check_licence
			}
		} );
	};

	/**
	 * Initialize the plugin instance.
	 */
	Plugin.prototype.init = function() {
		this.render();

		if ( ! this.isAddon() ) {
			this.initAddons();
		}
	};

	/**
	 * Does this instance represent an add-on?
	 *
	 * @returns {boolean}
	 */
	Plugin.prototype.isAddon = function() {
		return !! this.parent;
	};

	/**
	 * Initialize Plugin add-ons.
	 */
	Plugin.prototype.initAddons = function() {
		var parent = this;

		this.addons = _.map( this.data.addons, function( addonData ) {
			return new Plugin( addonData, parent );
		} );

		_.invoke( this.addons, 'init' );
	};

	/**
	 * Instantiate and initialize plugin instances.
	 */
	function initialize() {
		_.chain( dbrains.plugins )
			.map( function( pluginData ) {
				return new Plugin( pluginData );
			} )
			.invoke( 'init' );
	}

	if ( $listTable.length ) {
		$( initialize );
	}
})( jQuery );

