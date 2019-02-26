/*global wpml_tm_strings, jQuery, Backbone, icl_ajxloaderimg, ajaxurl, ProgressBar */
/*jslint laxbreak: true */

var WPML_TM = WPML_TM || {};

(function () {
	'use strict';

WPML_TM.Dashboard = Backbone.View.extend({
	events: {
		'click td[scope="row"] :checkbox':   'update_td',
		"click td.check-column :checkbox":   'icl_tm_select_all_documents',
		"change #icl_tm_languages :radio":   'change_radio',
		"change #parent-filter-control":     'populate_parent_list',
		"change #icl_language_selector":     'populate_parent_list',
		"click #duplicate-all":              'icl_tm_bulk_batch_selection',
		"click #translate-all":              'icl_tm_bulk_batch_selection',
		"click #update-none":                'icl_tm_bulk_batch_selection',
		"submit #icl_tm_dashboard_form":     'submit',
		"change #filter_type":               'show_hide_parent_controls'
	},
    counts: {
        all: 0,
        duplicate: 0,
        translate: 0
    },
	init: function ( $ ) {
		var self = this;
		self.$ = $;
		self.counts.all = self.setElement( '.icl_tm_wrap' );
		self.change_radio();
		self.show_hide_parent_controls();
	},
    submit: function (e) {
			var self = this;
			self.recount();
			if (self.counts.duplicate > 0) {
				e.preventDefault();
				var post_ids = [];
				var langs = [];
				var radios = jQuery('#icl_tm_languages').find('tbody').find(':radio:checked').filter('[value=2]');
				radios.each(function () {
					langs.push(jQuery(this).attr('name').replace('tr_action[', '').replace(']', ''));
				});

				var languagesCount = langs.length;
				if (0 < languagesCount) {
					var post_id_boxes = self.$el.find('#icl-tm-translation-dashboard tbody td :checkbox:checked');
					var post_ids_count = post_id_boxes.length;

					for (var p = 0; p < post_ids_count; p++) {
						for (var l = 0; l < languagesCount; l++) {
							post_ids.push({
															postId:       jQuery(post_id_boxes[p]).val(),
															languageCode: langs[l]
														});
						}
					}
					var duplication_ui = new PostDuplication(post_ids, jQuery('#icl_dup_ovr_warn'));
					duplication_ui.sendBatch();
				}
			}
    },
    iclTmUpdateDashboardSelection: function () {
        var self = this;
        if (self.$el.find(':checkbox:checked').length > 0) {
            var checked_items = self.$el.find('td.check-column :checkbox');
            if (self.$el.find('td[scope="row"] :checkbox:checked').length === self.$el.find('td[scope="row"] :checkbox').length) {
                checked_items.attr('checked', 'checked');
            } else {
                checked_items.removeAttr('checked');
            }
        }
    },
    recount: function(){
        var self = this;
        var radios = jQuery('#icl_tm_languages').find('tbody').find(':radio:checked');
        self.counts.duplicate = radios.filter('[value=2]').length;
        self.counts.translate = radios.filter('[value=1]').length;
        self.counts.all = radios.length;

        return self;
    },
    change_radio: function () {
        var bulk_select_radio, bulk_select_val, self;
        self = this;
        self.recount();
        self.icl_tm_enable_submit();
        self.icl_tm_dup_warn();
        bulk_select_val = self.counts.duplicate === self.counts.all ? "2" : false;
        bulk_select_val = self.counts.translate === self.counts.all ? "1" : bulk_select_val;
        bulk_select_val = self.counts.translate === 0 && self.counts.duplicate === 0 ? "0" : bulk_select_val;
        bulk_select_radio = bulk_select_val !== false
            ? self.$el.find('[name="radio-action-all"]').filter('[value=' + bulk_select_val + ']')
            : self.$el.find('[name="radio-action-all"]');
        bulk_select_radio.attr('checked', !!bulk_select_val);
    },
    update_td: function () {
        var self = this;
        self.icl_tm_update_word_count_estimate();
        self.iclTmUpdateDashboardSelection();
    },
    icl_tm_select_all_documents: function (e) {
        var self = this;
        self.$el.find('#icl-tm-translation-dashboard').find(':checkbox').attr('checked', !!jQuery(e.target).attr('checked'));
        self.icl_tm_update_word_count_estimate();
        self.icl_tm_update_doc_count();
        self.icl_tm_enable_submit();
    },
    icl_tm_update_word_count_estimate: function () {
        var self = this;
        self.icl_tm_enable_submit();
        var element_rows = self.$el.find('tbody').find('tr');
        var current_overall_word_count = 0;
        var icl_tm_estimated_words_count = jQuery('#icl-tm-estimated-words-count');
        jQuery.each(element_rows, function () {
            var row = jQuery(this);
            if (row.find(':checkbox').attr('checked')) {
                var item_word_count = row.data('word_count');
                var val = parseInt(item_word_count);
                val = isNaN(val) ? 0 : val;
                current_overall_word_count += val;
            }
        });
        icl_tm_estimated_words_count.html(current_overall_word_count);
        self.icl_tm_update_doc_count();
    },

	populate_parent_list: function () {
		var self = this,
			parent_select = self.$( '#parent-filter-control' ),
			parent_taxonomy_item_container = self.$( '[name="parent-taxonomy-item-container"]' ),
			val = parent_select.val();

		if ( val ) {
			parent_taxonomy_item_container.hide();
			if ( val != 'any' ) {
				var ajax_loader = self.$( '<span class="spinner"></span>' );
				ajax_loader.insertBefore( parent_taxonomy_item_container ).css( {
					visibility: "visible",
					float: "none"
				} );
				self.$.ajax( {
					type: "POST",
					url: ajaxurl,
					dataType: 'json',
					data: {
						action: 'icl_tm_parent_filter',
						type: val,
						from_lang: self.$( 'select[name="filter[from_lang]"]' ).val(),
						parent_id: self.$( '[name="filter[parent_id]"]' ).val()
					},
					success: function ( response ) {
						parent_taxonomy_item_container.html( response.data.html );
						parent_taxonomy_item_container.show();
						ajax_loader.remove();
					}
				} );
			}
		}
	},

	show_hide_parent_controls: function (e) {
		var self = this,
			selected_option = self.$( '#filter_type option:selected' ),
			parent_data = selected_option.data( 'parent' ),
			taxonomy_data = selected_option.data( 'taxonomy' );

		if ( parent_data || taxonomy_data ) {
			self.$( '#parent-taxonomy-container' ).show();
			self.fill_parent_type_select( parent_data, taxonomy_data );
			self.populate_parent_list();
		} else {
			self.$( '#parent-taxonomy-container' ).hide();
		}
	},

	fill_parent_type_select: function ( parent_data, taxonomy_data ) {
		var self = this,
			parent_select = self.$( '#parent-filter-control' );

		parent_select.find( 'option' ).remove();

		parent_select.append( '<option value="any">' + wpml_tm_strings.any + '</option>' );

		if ( parent_data ) {
			parent_select.append( '<option value="page">' + wpml_tm_strings.post_parent + '</option>' );
		}
		if ( taxonomy_data ) {
			taxonomy_data = taxonomy_data.split( ',' );
			for ( var i = 0; i < taxonomy_data.length; i++ ) {
				var parts = taxonomy_data[i].split( '=' );
				parent_select.append( '<option value="' + parts[0] + '">' + parts[1] + '</option>' );
			}
		}
		parent_select.val( parent_select.data( 'original' ) );
		parent_select.data( 'original', '' );
		if ( ! parent_select.val() ) {
			parent_select.val( 'any' );
		}

	},

    icl_update_button_label: function (dupl_count, trans_count) {
        var button_label;
        if (dupl_count > 0 && trans_count === 0) {
            button_label = wpml_tm_strings.BB_duplicate_all;
        } else if (dupl_count > 0 && trans_count > 0) {
            button_label = wpml_tm_strings.BB_mixed_actions;
        } else if (dupl_count === 0 && trans_count > 0) {
            button_label = wpml_tm_strings.BB_default;
        } else {
            button_label = wpml_tm_strings.BB_no_actions;
        }

		jQuery('#icl_tm_jobs_submit').html(button_label);
    },
	icl_update_button_class: function (dupl_count, trans_count) {
		var button= jQuery('#icl_tm_jobs_submit');
		var button_class= 'wpml-tm-button-basket';
		if (dupl_count > 0 && trans_count === 0) {
			button.removeClass(button_class);
		} else {
			button.addClass(button_class);
		}
	},
	icl_tm_dup_warn: function () {
        var self = this;
        if (self.counts.duplicate > 0 !== self.$el.find('[id="icl_dup_ovr_warn"]:visible').length > 0) {
            self.$el.find('#icl_dup_ovr_warn').fadeToggle(400);
        }
        self.icl_update_button_label(self.counts.duplicate, self.counts.translate);
        self.icl_update_button_class(self.counts.duplicate, self.counts.translate);
    },
    icl_tm_bulk_batch_selection: function (e) {
        var self = this;
        var element = jQuery(e.target);
        var value = element.val();
        element.attr('checked', 'checked');
        self.$el.find('#icl_tm_languages').find('tbody input:radio[value=' + value + ']').attr('checked', 'checked');
        self.change_radio();
        return self;
    },
    icl_tm_enable_submit: function () {
        var self = this;
        if ((self.counts.duplicate > 0 || self.counts.translate > 0)
            && jQuery('#icl-tm-translation-dashboard').find('td :checkbox:checked').length > 0) {
            jQuery('#icl_tm_jobs_submit').removeAttr('disabled');
        } else {
            jQuery('#icl_tm_jobs_submit').attr('disabled', 'disabled');
        }
    },
    icl_tm_update_doc_count: function () {
        var self = this;
        var dox = self.$el.find('tbody td :checkbox:checked').length;
        jQuery('#icl-tm-sel-doc-count').html(dox);
        if (dox) {
            jQuery('#icl-tm-doc-wrap').fadeIn();
        } else {
            jQuery('#icl-tm-doc-wrap').fadeOut();
        }
    }
});

var PostDuplication = Backbone.View.extend({
    ui:                          {},
    posts:                       [],
    duplicatedIDs:               [],
    langs:                       '',
    initialize:                  function (posts, element) {
        var self = this;
        self.posts = posts;
        self.ui = new ProgressBar();
        self.ui.overall_count = posts.length;
        self.ui.actionText = wpml_tm_strings.duplicating;
        element.replaceWith(self.ui.getDomElement());
        self.ui.start();
    },
    sendBatch:                   function () {
			var nonce;
			var p;
			var postsToSend;
			var languages;
			var self = this;
			var postsDataToSend = self.posts.splice(0, 5);
			var postsDataToSendCount = postsDataToSend.length;

			if(0 < postsDataToSendCount) {
				postsToSend = [];
				languages = [];
				for (p = 0; p < postsDataToSendCount; p++) {
					if (-1 === jQuery.inArray(postsDataToSend[p].postId, postsToSend)) {
						postsToSend.push(postsDataToSend[p].postId);
					}
					if (-1 === jQuery.inArray(postsDataToSend[p].languageCode, languages)) {
						languages.push(postsDataToSend[p].languageCode);
					}
				}

				if(0 < postsToSend.length && 0 < languages.length) {
					nonce = wpml_tm_strings.wpml_duplicate_dashboard_nonce;
					jQuery.ajax({
												type:     "POST",
												url:      ajaxurl,
												dataType: 'json',
												data:     {
													action:                     'wpml_duplicate_dashboard',
													duplicate_post_ids:         postsToSend,
													duplicate_target_languages: languages,
													_icl_nonce:                 nonce
												},
												success:  function () {
													self.ui.change(postsToSend.length);
													self.duplicatedIDs = self.duplicatedIDs.concat(postsToSend);
													if (0 < self.posts.length) {
														self.sendBatch();
													} else {
														self.ui.complete(wpml_tm_strings.duplication_complete, false);
														jQuery('#icl_tm_languages').find('tbody').find(':radio:checked').filter('[value=2]').attr('checked', false);
														self.setHierarchyNoticeAndSubmit();
													}
												}
											});
				}
			}
    },
    setHierarchyNoticeAndSubmit: function () {
        var self = this;

        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: 'json',
            data: {
                action: 'wpml_need_sync_message',
                duplicated_post_ids: self.duplicatedIDs.join(','),
                _icl_nonce: wpml_tm_strings.wpml_need_sync_message_nonce

            },
            success: function () {
                jQuery('#icl_tm_dashboard_form').submit();
            }
        });
    }
});

jQuery( document ).ready( function () {
	var tmDashboard = new WPML_TM.Dashboard();
	tmDashboard.init( jQuery );
} );

}());