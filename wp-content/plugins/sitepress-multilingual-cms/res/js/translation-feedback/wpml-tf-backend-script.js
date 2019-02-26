/*jshint browser:true, devel:true */
/*global jQuery, wp */
(function($){
	"use strict";

	$(document).ready(function(){
		var table = $('.js-wpml-tf-feedback-list-table'),
			ajax_action = $('.wpml-tf-feedback-list-page input[name="ajax_action"]').val(),
			ajax_nonce = $('.wpml-tf-feedback-list-page input[name="ajax_nonce"]').val();

		var openFeedbackDetails = function(feedbackId) {
			table.find('#wpml-tf-feedback-' + feedbackId).hide();
			table.find('#wpml-tf-feedback-details-' + feedbackId).show();
		};

		var closeAllFeedbackDetails = function() {
			table.find('.js-wpml-tf-feedback-details').hide();
			table.find('.js-wpml-tf-feedback').show();
		};

		var getFeedbackNode = function(el) {
			return $(el).closest('.js-wpml-tf-feedback-details, .js-wpml-tf-feedback');
		};

		var getFeedbackId = function(el) {
			return getFeedbackNode(el).data('feedback-id');
		};

		var getFeedbackContentUpdate = function(node) {
			var newContentNode = node.find('.js-wpml-tf-edit-feedback'),
				originalContent = node.find('.js-wpml-tf-readonly-feedback').text();

			if ( newContentNode.length && originalContent !== newContentNode.val() ) {
				return newContentNode.val();
			}

			return false;
		};

		var getFeedbackReviewerId = function(node) {
			var reviewerNode = node.find('.js-wpml-tf-reviewer');

			if ( reviewerNode.length ) {
				return parseInt(reviewerNode.val());
			}

			return false;
		};

		var getFeedbackMessage = function(node) {
			var newMessage = node.find('.js-wpml-tf-new-message').val();

			if ( newMessage ) {
				return newMessage;
			}

			return false;
		};

		var submitActionOnFeedback = function(id, action) {
			var currentURL = window.location.href,
				params = {
					nonce: table.closest('form').find('input[name="nonce"]').val(),
					bulk_action: action,
					bulk_action2: action,
					feedback_ids: [ id ],
				};

			window.location = currentURL + '&' + $.param(params);
		};

		var sendRequest = function(node, data, reload) {
			var spinner  = node.find('.spinner'),
				error_notification = node.find('.js-wpml-tf-feedback-details-error').empty();

			spinner.addClass('is-active');

			data.action      = ajax_action;
			data.nonce       = ajax_nonce;
			data.feedback_id = getFeedbackId(node);

			wp.ajax.send({
				data: data,
				success:  function (response) {
					if (reload) {
						window.location.href = window.location.pathname + window.location.search + window.location.hash;
					} else {
						spinner.removeClass('is-active');
						refreshFeedbackRows(data.feedback_id, response);
					}
				},
				error: function (error_message) {
					spinner.removeClass('is-active');
					error_notification.html(error_message).fadeIn();
				}
			});
		};

		var refreshFeedbackRows = function(feedbackId, data) {
			var summary = $.parseHTML(data.summary_row),
				details = $.parseHTML(data.details_row);

			table.find('#wpml-tf-feedback-' + feedbackId).first().replaceWith(summary);
			table.find('#wpml-tf-feedback-details-' + feedbackId).first().replaceWith(details);
		};

		var attachEvents = function() {
			table.on( 'click', '.js-wpml-tf-open-details', function(e) {
				e.preventDefault();
				closeAllFeedbackDetails();
				openFeedbackDetails(getFeedbackId(this));
			}).on( 'click', '.js-wpml-tf-cancel', function(e) {
				e.preventDefault();
				closeAllFeedbackDetails();
			}).on( 'click', '.js-wpml-tf-enable-translator-note', function(e) {
				e.preventDefault();
				var trigger = $(this);
				trigger.siblings('.js-wpml-tf-translator-note').show();
				trigger.remove();
			}).on( 'click', '.js-wpml-tf-enable-edit-feedback', function(e) {
				e.preventDefault();
				var trigger = $(this);
				trigger.siblings('.js-wpml-tf-readonly-feedback').hide();
				trigger.siblings('.js-wpml-tf-edit-feedback').show();
				trigger.remove();
			}).on( 'click', '.js-wpml-tf-send', function(e) {
				e.preventDefault();

				var node = getFeedbackNode(this),
					data = {
						edit_action: 'update_feedback'
					};

				if (getFeedbackContentUpdate(node)) {
					data.feedback_content = getFeedbackContentUpdate(node);
				}

				if (getFeedbackReviewerId(node)) {
					data.feedback_reviewer_id = getFeedbackReviewerId(node);
				}

				if (getFeedbackMessage(node)) {
					data.message_content = getFeedbackMessage(node);
				}

				data.feedback_status = $(this).val();

				if (node.find('.js-wpml-tf-translation-fixed-checkbox:checked').length > 0) {
					data.feedback_status = 'fixed';
				}

				sendRequest(node, data);
			}).on('click', '.js-wpml-tf-refresh-status', function(e) {
				e.preventDefault();
				var node = getFeedbackNode(this),
					data = {
						edit_action: 'update_feedback',
						feedback_status: 'sent_to_ts_api'
					};

				sendRequest(node, data);
			}).on('click', '.js-wpml-tf-translation-fixed', function(e) {
				e.preventDefault();
				var node = getFeedbackNode(this),
					data = {
						edit_action: 'update_feedback',
						feedback_status: 'fixed'
					};

				sendRequest(node, data);
			}).on('click', '.js-wpml-tf-error-details a', function(e) {
				e.preventDefault();
				$(this).siblings('pre').show();
				$(this).remove();
			}).on('click', '.js-wpml-tf-trash', function(e) {
				e.preventDefault();
				submitActionOnFeedback(getFeedbackId(this), 'trash');
			}).on('click', '.js-wpml-tf-untrash', function(e) {
				e.preventDefault();
				submitActionOnFeedback(getFeedbackId(this), 'untrash');
			}).on('click', '.js-wpml-tf-delete', function(e) {
				e.preventDefault();
				submitActionOnFeedback(getFeedbackId(this), 'delete');
			});
		};

		attachEvents();
	});

})(jQuery);