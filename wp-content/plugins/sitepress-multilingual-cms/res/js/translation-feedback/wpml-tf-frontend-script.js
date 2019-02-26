/*jshint browser:true, devel:true */
/*global jQuery, wp */
(function($){
	"use strict";

	$(document).ready(function(){
		var form         = $('.js-wpml-tf-feedback-form'),
			openIcon     = $('.js-wpml-tf-feedback-icon'),
			ratingInput  = form.find('input[name="wpml-tf-rating"]'),
			sendButton   = form.find('.js-wpml-tf-comment-button'),
			documentId   = form.find('input[name="document_id"]').val(),
			documentType = form.find('input[name="document_type"]').val(),
			action       = form.find('input[name="action"]').val(),
			nonce        = form.find('input[name="nonce"]').val(),
			noCommentThreshold = 4,
			dialogInitialized    = false,
			feedbackId;

		var disableRating = function() {
			ratingInput.prop('disabled', true);
		};

		var enableRating = function() {
			if(feedbackId && !form.hasClass('wpml-tf-closing-rating')) {
				ratingInput.prop('disabled', false);
			}
		};

		var enableComment = function() {
			sendButton.prop('disabled', false);
		};

		var displayClosingComment = function() {
			form.removeClass('wpml-tf-pending-comment').addClass('wpml-tf-closing-comment');
			window.setTimeout(destroyDialogAndButton, 3000);
		};

		var displayPendingComment = function() {
			form.addClass('wpml-tf-pending-comment').removeClass('wpml-tf-closing-rating');
			enableRating();
		};

		var displayClosingRating = function() {
			form.addClass('wpml-tf-closing-rating').removeClass('wpml-tf-pending-comment');
			disableRating();
		};

		var displayPendingRating = function() {
			form.removeClass('wpml-tf-closing-rating').removeClass('wpml-tf-pending-comment');
			enableRating();
		};

		var sendFeedback = function(data) {
			var options;

			if ( ! itLooksLikeSpam() ) {
				disableRating();
				form.addClass('wpml-tf-pending-request');

				data.nonce         = nonce;
				data.document_id   = documentId;
				data.document_type = documentType;

				options = {
					data: data,
					success: function(data) {
						feedbackId = data.feedback_id;
						form.addClass('wpml-tf-has-feedback-id').removeClass('wpml-tf-pending-request');
						enableRating();
						enableComment();
					}
				};

				wp.ajax.send(action, options);
			}
		};

		var itLooksLikeSpam = function() {
			var more_comment = form.find('textarea[name="more_comment"]');
			return ! dialogInitialized || more_comment.val();
		};

		var openForm = function() {
			form.dialog({
				dialogClass: 'wpml-tf-feedback-form-dialog otgs-ui-dialog',
				closeOnEscape: true,
				draggable: true,
				modal: false,
				title: form.data('dialog-title'),
				dragStop: function() {
					$(this).parent().height('auto');
				}
			});

			dialogInitialized = true;

			// Fix display glitch with bootstrap.js
			$('.wpml-tf-feedback-form-dialog').find('.ui-dialog-titlebar-close').addClass('ui-button');
		};

		var destroyDialogAndButton = function() {
			form.dialog( 'destroy' );
			openIcon.remove();
		};

		openIcon.on('click', function(e) {
			e.preventDefault();
			openForm();
		});

		ratingInput.on('click', function() {
			var data = {
				rating: $(this).val()
			};

			if ( feedbackId ) {
				data.feedback_id = feedbackId;
			}

			sendFeedback(data);

			if(data.rating < noCommentThreshold) {
				displayPendingComment();
			} else {
				displayClosingRating();
			}
		});

		form.on('click', '.js-wpml-tf-change-rating', function(e) {
			e.preventDefault();
			displayPendingRating();
		});

		sendButton.on('click', function(e) {
			e.preventDefault();
			var data = {
				content:     $('textarea[name="wpml-tf-comment"]').val(),
				feedback_id: feedbackId
			};

			sendFeedback(data);
			displayClosingComment();
		});

	});
})(jQuery);