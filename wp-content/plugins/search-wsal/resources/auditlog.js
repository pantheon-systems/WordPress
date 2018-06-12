window.WsalAs = (function(){
	var o = this;
	var attachEvents = [];

	o.AjaxUrl = window['ajaxurl'];
	o.AjaxAction = 'WsalAsWidgetAjax';

	// listen to auditlog refresh events
	o._WsalAuditLogRefreshed = window['WsalAuditLogRefreshed'];
	window['WsalAuditLogRefreshed'] = function(){
		o.Attach();
		o._WsalAuditLogRefreshed();
	};

	// add callbacks to attach event
	o.Attach = function(cb){
		if(typeof cb === 'undefined'){
			// call callbacks
			for(i = 0; i < attachEvents.length; i++)attachEvents[i]();
		}else{
			// add callbacks
			attachEvents.push(cb);
		}
	};
	
	// extend default search box
	o.Attach(function(){
		if (jQuery('#wsal-as-fake-search').length) return; // already attached
		
		// select some important elements
		o.real = jQuery('#wsal-as-search-search-input');
		o.flds = jQuery('#wsal-as-filter-fields');
		
		// nice search box effects
		o.real.css('width', '160px')
			.focus(function(){
				o.real.animate({ width: '360px' }, 'fast');
			})
			.blur(function(){
				o.real.animate({ width: '160px' }, 'fast');
			});
		
		// attach filter counter and dropdown
		o.fake = jQuery('<span id="wsal-as-fake-search" class="wsal-as-fake-search"/>');
		o.cntr = jQuery('<a href="javascript:;">0 filters</a>');
		o.ppup = jQuery('<div class="wsal-as-filter-popup" style="display: none;"/>');
		o.list = jQuery('<div class="wsal-as-filter-list no-filters"/>');
		o.real.before(o.ppup.append(o.list, o.flds.show()), o.fake).appendTo(o.fake).after(o.cntr);
		o.cntr.click(function(){
			o.ppup.toggle();
		});
		
		// attach suggestion dropdown
		// TODO suggestions should cause user query to be removed and the selected filter to appear in filters box
	});

	// add new filter
	o.AddFilter = function(text){
		var filter = text.split(':'); 
		if (filter[0] == 'from' || filter[0] == 'to') {
			// Validation date format
			if (!checkDate(filter[1])) {
	            return;
	        }
		}
		if(!jQuery('input[name="Filters[]"][value="' + text + '"]').length){
			o.list.append(
				jQuery('<span/>').append(
					jQuery('<input type="text" name="Filters[]"/>').val(text),
					jQuery('<a href="javascript:;" title="Remove">&times;</a></span>')
						.click(function(){
							jQuery(this).parents('span:first').fadeOut('fast', function(){
								jQuery(this).remove();
								o.CountFilters();
							});
						})
				)
			);
		}
		o.CountFilters();
	};
	
	// remove existing filters
	o.ClearFilters = function(){
		o.list.html('');
		o.CountFilters();
	};

	// update filter count
	o.CountFilters = function(){
		var count = o.list.find('>span').length;
		o.cntr.text(count + ' filters');
		o.list[count === 0 ? 'addClass' : 'removeClass']('no-filters');
	};

	return o;
})();

jQuery(document).ready(function($){
	window.WsalAs.Attach();
    wsal_CreateDatePicker($, $('#wsal_as_widget_7'), null);
    wsal_CreateDatePicker($, $('#wsal_as_widget_8'), null);
});
