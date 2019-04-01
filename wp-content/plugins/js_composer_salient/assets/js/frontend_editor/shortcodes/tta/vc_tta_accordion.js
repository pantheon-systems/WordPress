(function ( $ ) {
	window.InlineShortcodeView_vc_tta_accordion = window.InlineShortcodeViewContainer.extend( {
		events: {},
		childTag: 'vc_tta_section',
		activeClass: 'vc_active',
		// controls_selector: '#vc_controls-template-vc_tta_accordion',
		defaultSectionTitle: window.i18nLocale.section,
		initialize: function () {
			_.bindAll( this, 'buildSortable', 'updateSorting' );
			window.InlineShortcodeView_vc_tta_accordion.__super__.initialize.call( this );
		},
		render: function () {
			window.InlineShortcodeViewContainer.__super__.render.call( this );
			this.content(); // just to remove span inline-container anchor..
			this.buildPagination();
			return this;
		},
		addControls: function () {
			this.$controls = $( '<div class="no-controls"></div>' );
			this.$controls.appendTo( this.$el );
			return this;
		},
		/**
		 * Add new element to Accordion.
		 * @param e
		 */
		addElement: function ( e ) {
			e && e.preventDefault();
			this.addSection( 'parent.prepend' === $( e.currentTarget ).data( 'vcControl' ) );
		},
		appendElement: function ( e ) {
			return this.addElement( e );
		},
		prependElement: function ( e ) {
			return this.addElement( e );
		},
		addSection: function ( prepend ) {
			var shortcode, params, i;

			shortcode = this.childTag;

			params = {
				shortcode: shortcode,
				parent_id: this.model.get( 'id' ),
				isActiveSection: true,
				params: {
					title: this.defaultSectionTitle
				}
			};

			if ( prepend ) {
				vc.activity = 'prepend';
				params.order = this.getSiblingsFirstPositionIndex();
			}

			vc.builder.create( params );

			// extend default params with settings presets if there are any
			for ( i = vc.builder.models.length - 1;
				  i >= 0;
				  i -- ) {
				shortcode = vc.builder.models[ i ].get( 'shortcode' );
			}

			vc.builder.render();
		},
		getSiblingsFirstPositionIndex: function () {
			var order,
				shortcodeFirst;
			order = 0;
			shortcodeFirst = vc.shortcodes.sort().findWhere( { parent_id: this.model.get( 'id' ) } );
			if ( shortcodeFirst ) {
				order = shortcodeFirst.get( 'order' ) - 1;
			}
			return order;
		},
		changed: function () {
			vc.frame_window.vc_iframe.buildTTA();
			window.InlineShortcodeView_vc_tta_accordion.__super__.changed.call( this );
			_.defer( this.buildSortable );
			this.buildPagination();
		},
		updated: function () {
			window.InlineShortcodeView_vc_tta_accordion.__super__.updated.call( this );
			_.defer( this.buildSortable );
			this.buildPagination();
		},
		buildSortable: function () {
			if ( ! vc_user_access().shortcodeEdit( this.model.get( 'shortcode' ) ) ) {
				return
			}
			if ( this.$el ) {
				this.$el.find( '.vc_tta-panels' ).sortable( {
					forcePlaceholderSize: true,
					placeholder: 'vc_placeholder-row', // TODO: fix placeholder
					start: this.startSorting,
					over: function ( event, ui ) {
						ui.placeholder.css( { maxWidth: ui.placeholder.parent().width() } );
						ui.placeholder.removeClass( 'vc_hidden-placeholder' );
					},
					items: '> .vc_element',
					handle: '.vc_tta-panel-heading, .vc_child-element-move',// TODO: change vc_column to vc_tta_section
					update: this.updateSorting
				} );
			}
		},
		startSorting: function ( event, ui ) {
			ui.placeholder.width( ui.item.width() );
		},
		updateSorting: function ( event, ui ) {
			var self = this;
			this.getPanelsList().find( '> .vc_element' ).each( function () {
				var shortcode, modelId, $this;

				$this = $( this );
				modelId = $this.data( 'modelId' );
				shortcode = vc.shortcodes.get( modelId );
				shortcode.save( { 'order': self.getIndex( $this ) }, { silent: true } );
			} );
			// re-render pagination
			this.buildPagination();
		},
		getIndex: function ( $element ) {
			return $element.index();
		},
		getPanelsList: function () {
			return this.$el.find( '.vc_tta-panels' );
		},
		parentChanged: function () {
			window.InlineShortcodeView_vc_tta_accordion.__super__.parentChanged.call( this );

			if ( 'undefined' !== typeof(vc.frame_window.vc_round_charts) ) {
				vc.frame_window.vc_round_charts( this.model.get( 'id' ) );
			}

			if ( 'undefined' !== typeof(vc.frame_window.vc_line_charts) ) {
				vc.frame_window.vc_line_charts( this.model.get( 'id' ) );
			}
		},
		buildPagination: function () {
		},
		removePagination: function () {
			this.$el.find( '.vc_tta-panels-container' ).find( ' > .vc_pagination' ).remove(); // TODO: check this
		},
		getPaginationList: function () {
			var $accordions,
				classes,
				styleChunks,
				that,
				html,
				params;

			params = this.model.get( 'params' );
			if ( ! _.isUndefined( params.pagination_style ) && params.pagination_style.length ) {
				$accordions = this.$el.find( '[data-vc-accordion]' );
				classes = [];
				classes.push( 'vc_general' );
				classes.push( 'vc_pagination' );
				styleChunks = params.pagination_style.split( '-' );
				classes.push( 'vc_pagination-style-' + styleChunks[ 0 ] );
				classes.push( 'vc_pagination-shape-' + styleChunks[ 1 ] );

				if ( ! _.isUndefined( params.pagination_color ) && params.pagination_color.length ) {
					classes.push( 'vc_pagination-color-' + params.pagination_color );
				}
				html = [];
				html.push( '<ul class="' + classes.join( ' ' ) + '">' );

				that = this;
				$accordions.each( function () {
					var sectionClasses,
						activeSection,
						$this,
						$closestPanel,
						selector,
						aHtml;

					$this = $( this );
					$closestPanel = $this.closest( '.vc_tta-panel' );
					activeSection = $closestPanel.hasClass( that.activeClass );
					sectionClasses = [ 'vc_pagination-item' ];
					if ( activeSection ) {
						sectionClasses.push( that.activeClass );
					}

					selector = $this.attr( 'href' );
					if ( 0 !== selector.indexOf( '#' ) ) {
						selector = '';
					}
					if ( $this.attr( 'data-vc-target' ) ) {
						selector = $this.attr( 'data-vc-target' );
					}
					aHtml = '<a href="javascript:;" data-vc-target="' + selector + '" class="vc_pagination-trigger" data-vc-tabs data-vc-container=".vc_tta"></a>';
					html.push( '<li class="' + sectionClasses.join( ' ' ) + '" data-vc-tab>' + aHtml + '</li>' );
				} );

				html.push( '</ul>' );
				return $( html.join( '' ) );
			}
			return null;
		}
	} );
})( window.jQuery );