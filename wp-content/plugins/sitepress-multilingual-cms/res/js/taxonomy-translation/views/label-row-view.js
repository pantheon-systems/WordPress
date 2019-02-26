(function () {

	TaxonomyTranslation.views.LabelRowView = Backbone.View.extend({

		tagName: 'tbody',
		model: TaxonomyTranslation.models.Taxonomy,
		events: {
			'click .icl_tt_label': 'openPopUPLabel',
			'click .js-show-lang-selector': 'showLanguageSelector',
			'change .js-tax-lang-selector': 'changeTaxStringsLanguage'
		},
		initialize: function () {
			var self = this;
			self.listenTo(self.model, 'labelTranslationSaved', self.render);
		},

		showLanguageSelector: function(e) {
			e.preventDefault();
			this.render( true );

			if ( WPML_Core.SimpleLanguageSelector ) {
				new WPML_Core.SimpleLanguageSelector();
			}
		},

		render: function ( withLangSelector ) {
			var self = this,
				taxLabels = TaxonomyTranslation.data.translatedTaxonomyLabels,
				langs = TaxonomyTranslation.util.langCodes,
				taxonomy = self.model.get( 'taxonomy' ),
				labelLang = TaxonomyTranslation.classes.taxonomy.get( 'stDefaultLang' ),
				langSelector = withLangSelector ? TaxonomyTranslation.data.langSelector : '',
				html = '<tr>';

			
			html += WPML_core[ 'templates/taxonomy-translation/original-label.html' ](
				{
					taxLabel    : taxLabels[labelLang],
					flag        : TaxonomyTranslation.data.allLanguages[labelLang].flag,
					langSelector: langSelector
				}
			);

			html += '<td class="wpml-col-languages">';
			
			_.each(langs, function(lang, code) {
				if( ! taxLabels[lang] ) {
					html += WPML_core[ 'templates/taxonomy-translation/not-translated-label.html' ](
						{
							taxonomy: taxonomy,
							lang    : lang,
							langs   : TaxonomyTranslation.data.activeLanguages
						}
					);
				} else {
					if( taxLabels[lang].original ) {
						html += WPML_core[ 'templates/taxonomy-translation/original-label-disabled.html' ](
							{
								lang : lang,
								langs: TaxonomyTranslation.data.activeLanguages
							}
						);
					} else {
						html += WPML_core[ 'templates/taxonomy-translation/individual-label.html' ](
							{
								taxonomy: taxonomy,
								lang    : lang,
								langs   : TaxonomyTranslation.data.activeLanguages
							}
						);
					}
				}
			});

			html += '</td>';
			html += '</tr>';
			
			self.$el.html( html );

			self.delegateEvents();
			return self;
		},
		openPopUPLabel: function (e) {

			e.preventDefault();

			var link = e.target.closest( '.icl_tt_label' ),
				id = jQuery( link ).attr( 'id' ),
				lang = id.split( '_' ).pop();

			if (TaxonomyTranslation.classes.labelPopUpView && typeof TaxonomyTranslation.classes.labelPopUpView !== 'undefined') {
				TaxonomyTranslation.classes.labelPopUpView.close();
			}

			TaxonomyTranslation.classes.labelPopUpView = new TaxonomyTranslation.views.LabelPopUpView({model: TaxonomyTranslation.classes.taxonomy}, {
				lang: lang,
				defLang: TaxonomyTranslation.classes.taxonomy.get( 'defaultLang' )
			});
			TaxonomyTranslation.classes.labelPopUpView.open( lang );
		},

		changeTaxStringsLanguage: function(e) {
			var sourceLang = e.target.value;
			this.$el.find('.js-tax-lang-selector').prepend( '<span class="spinner is-active">' );
			this.model.changeTaxStringsLanguage(sourceLang);
		}
	});
}(TaxonomyTranslation));