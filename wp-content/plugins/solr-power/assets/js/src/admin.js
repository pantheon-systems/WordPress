jQuery(document).ready(function () {
	var refURI = jQuery('input[name="_wp_http_referer"]').val();
	refURI = refURI.replace(/#.*$/, '');
	jQuery('#solr-tabs').find('a').click(function () {
			jQuery('#solr-tabs').find('a').removeClass('nav-tab-active');
			jQuery('.solrtab').removeClass('active');

			var id = jQuery(this).attr('id').replace('-tab', '');
			jQuery('#' + id).addClass('active');
			jQuery(this).addClass('nav-tab-active');
			jQuery('input[name="_wp_http_referer"]').val(refURI + '#top#' + id);
		}
	);

	// init
	var solrActiveTab = window.location.hash.replace('#top#', '');

	// default to first tab
	if (solrActiveTab === '' || solrActiveTab === '#_=_') {
		solrActiveTab = jQuery('.solrtab').first().attr('id');
	}

	jQuery('#' + solrActiveTab).addClass('active');
	jQuery('#' + solrActiveTab + '-tab').addClass('nav-tab-active').click();

});

var $j = jQuery.noConflict();

function switch1() {
	if ($j('#solrconnect_single').is(':checked')) {
		$j('#solr_admin_tab2').css('display', 'block');
		$j('#solr_admin_tab2_btn').addClass('solr_admin_on');
		$j('#solr_admin_tab3').css('display', 'none');
		$j('#solr_admin_tab3_btn').removeClass('solr_admin_on');
	}
	if ($j('#solrconnect_separated').is(':checked')) {
		$j('#solr_admin_tab2').css('display', 'none');
		$j('#solr_admin_tab2_btn').removeClass('solr_admin_on');
		$j('#solr_admin_tab3').css('display', 'block');
		$j('#solr_admin_tab3_btn').addClass('solr_admin_on');
	}
}

$j(document).ready(function () {
	switch1();
});

(function($){

	var solrActions = {

		batchIndexTemplate: false,
		currentBatch: 0,
		totalBatches: 0,
		successPosts: 0,
		failedPosts: 0,
		remainingPosts: 0,
		startTime: false,

		init: function() {
			if ( ! $('#solr-batch-index').length ) {
				return;
			}
			this.bindEvents();
			this.setupInitialState();
			this.renderIndexUI();
		},

		bindEvents: function() {
			$('#solr-batch-index').on('click', 'input',$.proxy(this.handleClickIndexPosts,this));
		},

		setupInitialState: function() {
			this.batchIndexTemplate = wp.template('solr-batch-index');
			var tmpl = $('#tmpl-solr-batch-index');
			this.currentBatch = tmpl.data('current-batch');
			this.totalBatches = tmpl.data('total-batches');
			this.remainingPosts = tmpl.data('remaining-posts');
			this.totalPosts = tmpl.data('total-posts');
		},

		disableAll: function() {
			$('.solr-admin-action').attr('disabled','disabled');
		},

		enableAll: function() {
			$('.solr-admin-action').removeAttr('disabled');
		},

		handleClickIndexPosts: function(e) {
			this.disableAll();
			e.preventDefault();
			var el = $(e.currentTarget);
			var action = 's4wp_start_index' === el.attr('name') ? 'start' : 'resume';
			if ( 'start' === action ) {
				this.currentBatch = 1;
				this.remainingPosts = this.totalPosts;
			}
			this.startTime = new Date();
			this.elapsedTime = '00:00:00';
			this.renderIndexUI();
			this.indexPosts( action );
		},

		indexPosts: function( action ) {
			if ( typeof action === 'undefined' ) {
				action = 'resume';
			}
			$.post( solr.ajax_url, {
				action  : 'solr_options',
				security: solr.security,
				method  : action + '-index',
			}, $.proxy(function( response ){
				this.currentBatch = response.currentBatch;
				this.successPosts += response.successPosts;
				this.failedPosts += response.failedPosts;
				this.remainingPosts = response.remainingPosts;
				this.renderIndexUI();
				if ( this.remainingPosts > 0 ) {
					this.indexPosts();
				} else {
					this.startTime = false;
				}
			}, this ) );
		},

		renderIndexUI: function() {
			var elapsedTime = false;
			if ( this.startTime ) {
				paddingLeft = function(originalValue,paddingValue) {
					return String(paddingValue + originalValue).slice(-paddingValue.length);
				};
				var endTime = new Date();
				var timeDiff = endTime - this.startTime;
				timeDiff /= 1000;
				var seconds = paddingLeft( Math.round(timeDiff % 60), '00' );
				timeDiff = Math.floor(timeDiff / 60);
				var minutes = paddingLeft( Math.round(timeDiff % 60), '00' );
				timeDiff = Math.floor(timeDiff / 60);
				var hours = paddingLeft( Math.round(timeDiff % 24), '00' );
				elapsedTime = hours + ':' + minutes + ':' + seconds;
			}
			$('#solr-batch-index').html( this.batchIndexTemplate({
				currentBatch: this.currentBatch,
				totalBatches: this.totalBatches,
				elapsedTime: elapsedTime,
				successPosts: this.successPosts,
				failedPosts: this.failedPosts,
				remainingPosts: this.remainingPosts,
			} ) );
		}
	};

	$(document).ready($.proxy(solrActions.init,solrActions));

}(jQuery));
