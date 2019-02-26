/* globals ajaxurl, wpml_groups_to_scan, wpml_active_plugins_themes */

var WPML_ST = WPML_ST || {};

jQuery(function($) {

	'use strict';

	WPML_ST.ThemePluginFilter = function(scanningSection){
		this.scanningSection = scanningSection;
	};

	WPML_ST.ThemePluginFilter.prototype = {

		attachEvents: function() {
			this.scanningSection.section.on( 'click', '.state-selector li', {instance: this}, function (event){
				event.preventDefault();
				event.data.instance.toggleItems( event.data.instance.scanningSection, $(this) );
			});

			this.scanningSection.section.on( 'click', 'thead input:checkbox, tfoot input:checkbox', {instance: this}, function (event){
				event.data.instance.toggleCheckboxes( event.data.instance.scanningSection.section, $(this) );
			});

			this.scanningSection.section.on( 'change', 'input:checkbox', {instance: this}, function(event) {
				var checked = event.data.instance.scanningSection.section.find( '.item input:checkbox:checked' );
				var disableScanButton = ! Boolean( checked.length );
				$(event.data.instance.scanningSection.scanButton).prop('disabled', disableScanButton );
			});

			this.scanningSection.section.on( 'click', '.item input:checkbox', {instance: this}, function(event) {
				var check_all_items = event.data.instance.scanningSection.section.find( 'thead input:checkbox, tfoot input:checkbox' );
				var all_items = event.data.instance.scanningSection.section.find( '.item input:checkbox' );
				var all_items_checked = event.data.instance.scanningSection.section.find( '.item input:checkbox:checked' );

				check_all_items.prop( 'checked', all_items.length === all_items_checked.length );
			});
		},

		toggleItems: function (data, triggerElement) {
			var status = triggerElement.data('status') ? triggerElement.data('status') : '';

			$(data.section).find('ul li').removeClass('active');
			triggerElement.addClass('active');
			data.section.data('active-selector', status);

			if ('active' === status) {
				$(data.section).find('table tr.item').hide();
				$(data.section).find('table .active').show();
			} else if ('inactive' === status) {
				$(data.section).find('table tr.item').show();
				$(data.section).find('table .active').hide();
			} else if ('all' === status) {
				$(data.section).find('table tr.item').show();
			}
		},

		toggleCheckboxes: function (e, trigger) {
			$(this.scanningSection.section).find('input:checkbox').prop('checked', trigger.prop('checked') );
		}
	};

	WPML_ST.ScanningSections = function() {
		this.theme = {
			type: 'theme',
			section: $('.wpml_theme_localization'),
			scanButton: '#wpml_theme_localization_scan'
		};

		this.plugin = {
			type: 'plugin',
			section: $('.wpml_plugin_localization'),
			scanButton: '#wpml_plugin_localization_scan'
		};
	};

	WPML_ST.ScanningCounter = function() {
		this.filesChunkCount = 0;
		this.filesChunkCounter = 0;
		this.totalStrings = 0;
		this.scannedFiles = [];
	};

	WPML_ST.ScanningCounter.prototype = {
		reset: function() {
			this.filesChunkCount = 0;
			this.filesChunkCounter = 0;
			this.totalStrings = 0;
			this.scannedFiles = [];
		}
	};

	WPML_ST.StringsScanning = function(isFirstSection, counter) {
		this.numberOfFilesPerChunk = 50;
		this.scannedFiles = [];
		this.totalStrings = 0;
		this.filesChunkCount = 0;
		this.filesChunkCounter = 0;
		this.triggerElement = {};
		this.ajaxScanDirFiles = {};
		this.elements = {};
		this.scanSuccessfulMessage = '';
		this.filesProcessedMessage = '';
		this.spinner = '.wpml-scanning-progress .spinner';
		this.progressMsg = '.wpml-scanning-progress-msg';
		this.statsSection = '.wpml-scanning-results';
		this.scanningProgressDialog = '.wpml-scanning-progress';
		this.dialogCloseSelector = '.ui-dialog-titlebar-close';
		this.isFirstSection = isFirstSection;
		this.counter = counter;
	};

	WPML_ST.StringsScanning.prototype = {
		init: function() {
			$( this.progressMsg ).hide();
		},

		attachEvents: function( sectionData ) {
			sectionData.section.on( 'click', sectionData.scanButton, {instance: this}, function(event){
				var instance = event.data.instance;
				event.preventDefault();
				instance.triggerElement = $( this );
				instance.elements = sectionData;

				instance.scan(sectionData);
			});
		},

		scan: function( sectionData ) {
			var selectedItems = sectionData.section.find('table').find('input:checkbox:checked[data-component-name]');
			var itemsCount = 0;
			var that = this;
			var type = sectionData.type;

			this.elements = sectionData;
			this.triggerElement.prop('disabled', true);

			$( this.spinner ).addClass('is-active');
			$( this.progressMsg ).show();
			$( this.statsSection ).empty();
			$( this.scanningProgressDialog ).dialog({
				width: 350,
				maxHeight: 300,
				modal: true,
				open: function() {
					$(that.dialogCloseSelector).hide();
				},
				closeOnEscape: false
			});

			if (selectedItems.length) {
				selectedItems.toArray().forEach(function(element) {
					this.ajaxScanDirFiles = {};

					if ($('input[name="use_theme_plugin_domain"]').prop('checked')) {
						this.ajaxScanDirFiles.auto_text_domain = 1;
					}

					type = sectionData.type;
					if ( -1 !== $( element ).data( 'component-name' ).search( 'mu-::-' ) ) {
						type = 'mu-plugin';
					}

					this.ajaxScanDirFiles[ type ] = $(element).val();
					this.ajaxScanDirFiles.action = $( sectionData.section ).attr('data-scan_folder-action');
					this.ajaxScanDirFiles.nonce = $( sectionData.section ).attr('data-scan_folder-nonce');

					this.scanDirAjax( sectionData.type, $(element).val() );
					itemsCount++;
				}, this);
			}
		},

		scanDirAjax: function( section_type, section_value ) {
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				section_type: section_type,
				section_value: section_value,
				instance: this,
				data: this.ajaxScanDirFiles,
				success: this.scanDirSuccess
			});
		},

		updateHashAjax: function() {
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					'action': $( this.elements.section ).attr('data-update_hash-action'),
					'nonce': $( this.elements.section ).attr('data-update_hash-nonce'),
					'files': this.counter.scannedFiles
				},
				success: $.proxy(this.refreshScreen, this)
			});
		},

		scanFilesAjax: function(ajax_files_chunk, files_chunks, index) {

			if ( index === files_chunks.length - 1 ) {
				ajax_files_chunk.scan_mo_files = 1;
			}

			ajax_files_chunk.files = files_chunks[index];

			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: ajax_files_chunk,
				context: this,
				success: $.proxy(this.scanFilesSuccess, this)
			}).done(function() {
				this.scanFilesAjaxDone(files_chunks, ajax_files_chunk, index);
			});
		},

		scanFilesAjaxDone: function(files_chunks, ajax_files_chunk, index) {
			if (index < files_chunks.length - 1) {
				this.scanFilesAjax(ajax_files_chunk, files_chunks, index + 1);
			}
		},

		scanFilesSuccess: function( result ) {

			var scanned_files_obj = result.data;

			this.counter.scannedFiles = this.counter.scannedFiles.concat(scanned_files_obj.files_processed);
			this.counter.totalStrings = this.counter.totalStrings + scanned_files_obj.strings_found;
			this.scanSuccessfulMessage = scanned_files_obj.scan_successful_message;
			this.filesProcessedMessage = scanned_files_obj.files_processed_message;
			this.counter.filesChunkCounter++;

			$(this.statsSection).html(this.renderStringsCounter());
			$(this.statsSection).show();

			if ( this.counter.filesChunkCounter === this.counter.filesChunkCount ) {
				this.updateHashAjax();
			}
		},

		scanDirSuccess: function( dirFilesResult ) {

			var ajaxFilesChunk = {},
				dirFiles = dirFilesResult.data.files,
				filesChunks = [],
				index = 0;

			ajaxFilesChunk.action = $( this.instance.elements.section ).attr('data-scan_files-action');
			ajaxFilesChunk.nonce = $( this.instance.elements.section ).attr('data-scan_files-nonce');
			ajaxFilesChunk[this.section_type] = this.section_value;

			while ( 0 < dirFiles.length ) {
				filesChunks.push( dirFiles.splice(0, this.instance.numberOfFilesPerChunk) );
			}

			this.instance.counter.filesChunkCount = this.instance.counter.filesChunkCount + filesChunks.length;

			if ( filesChunks.length > 0 ) {
				this.instance.scanFilesAjax(ajaxFilesChunk, filesChunks, index);
			} else if ( this.instance.counter.filesChunkCounter === this.instance.counter.filesChunkCount ) {
				this.instance.restoreUI();
				$(this.instance.statsSection).html( dirFilesResult.data.no_files_message );
				$(this.instance.statsSection).show();
			}
		},

		refreshScreen: function() {
			this.elements.section.load(location + ' #wpml-' + this.elements.type + '-content', $.proxy( this.afterScreenReload, this ) );
		},

		afterScreenReload: function() {
			$(this.statsSection).html(this.renderScanningResults());
			$(this.statsSection).show();

			var currentFilterSelector = this.elements.section.data( 'active-selector' );
			this.elements.section.find( 'ul [data-status="' + currentFilterSelector + '"]' ).trigger( 'click' );
			this.restoreUI();
		},

		renderScanningResults: function() {
			var text = '';

			text = text + this.scanSuccessfulMessage.replace('%s', this.counter.totalStrings) + '<br />';


			if ( this.filesProcessedMessage ) {
				text = text + this.filesProcessedMessage + '<br /><br />';

				if ( this.counter.scannedFiles ) {
					$.each(this.counter.scannedFiles, function (index, element) {
						text = text + element + '<br />';
					});
				}
			}

			return text;
		},

		renderStringsCounter: function() {
			var text = '';

			text = text + this.scanSuccessfulMessage.replace('%s', this.counter.totalStrings) + '<br />';
			text = text.split('!')[1];
			text = text.split('.')[0];

			return text;
		},

		restoreUI: function() {
			$(this.spinner).removeClass('is-active');
			this.triggerElement.prop('disabled', false);
			$( this.progressMsg ).hide();
			this.allowClosingDialog();

			this.counter.reset();
		},

		allowClosingDialog: function() {
			$(this.dialogCloseSelector).show();
			$(this.scanningProgressDialog).dialog('option', 'closeOnEscape', true);
			$(this.scanningProgressDialog).closest('.ui-dialog').focus();
		}
	};

	WPML_ST.AutoScan = function( groups, scanningSections ) {
		this.groups = groups;
		this.scanningSections = scanningSections;
	};

	WPML_ST.AutoScan.prototype = {
		init: function() {
			if ( this.shouldRunAutoScan() ) {
				for (var group in this.groups) {
					if (this.groups.hasOwnProperty(group)) {
						this.selectItems( this.scanningSections[group].scanButton, this.groups[group] );
						this.scan( this.scanningSections[group].scanButton );
					}
				}
			}
		},

		selectItems: function( scanButton, group ) {
			group.forEach(function(item){
				$( 'input[value="' + item + '"]' ).attr( 'checked', 'checked' );
			});
		},

		scan: function( scanButton ) {
			$( scanButton ).click();
		},

		shouldRunAutoScan: function() {
			return '' !== this.groups[0] && ( -1 !== location.href.search('action=scan_from_notice') || -1 !== location.href.search('action=scan_active_items'))
		}
	};

	$(document).ready(function () {
		var scanningSections = new WPML_ST.ScanningSections();
		var	auto_scan_type = wpml_active_plugins_themes;
		var sections_count = 0;
		var counter = new WPML_ST.ScanningCounter();

		if ( -1 !== location.href.search('action=scan_from_notice') ) {
			auto_scan_type = wpml_groups_to_scan;
		}

		for (var section in scanningSections) {
			if (scanningSections.hasOwnProperty(section)) {
				var	isFirstSection = 0 === sections_count;
				var scanSection = new WPML_ST.StringsScanning(isFirstSection, counter);
				var	themePluginFilter = new WPML_ST.ThemePluginFilter(scanningSections[section]);

				scanSection.init();
				scanSection.attachEvents(scanningSections[section]);
				themePluginFilter.attachEvents();
			}
			sections_count++;
		}

		var autoScan = new WPML_ST.AutoScan( auto_scan_type, scanningSections );
		autoScan.init();
	});
});
