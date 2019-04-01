jQuery( document ).ready(function() {

	// disable collapse
	jQuery('.postbox h3, .postbox .handlediv').unbind('click.postboxes');

	// show logs
	jQuery('#fastvelocity_min_processed').on('click','.log',function(e){
		e.preventDefault();
		jQuery(this).parent().nextAll('pre').slideToggle();
	});

	function getFiles() {
		stamp = new Date().getTime();
		var data = { 'action': 'fastvelocity_min_files' };
		
		jQuery.post(ajaxurl, data, function(response) {
		
			if(response.cachesize.length > 0) { 
			jQuery("#fvm_cache_size").html(response.cachesize);
			}
			
			// reset
			var fvmarr = [];
			
			// js
			if(response.js.length > 0) { 
			jQuery(response.js).each(function(){
				fvmarr.push(this.uid);
				if(jQuery('#'+this.uid).length == 0) {
					jQuery('#fastvelocity_min_jsprocessed ul').append('<li id="'+this.uid+'"><span class="filename">'+this.filename+' ('+this.fsize+')</span> <span class="actions"><a href="#" class="log button button-primary">View Log</a></span><pre>'+this.log+'</pre></li><div class="clear"></div>');
				}
			});
			}
			
			// css
			if(response.css.length > 0) {
			jQuery(response.css).each(function(){
				fvmarr.push(this.uid);
				if(jQuery('#'+this.uid).length == 0) {
					jQuery('#fastvelocity_min_cssprocessed ul').append('<li id="'+this.uid+'"><span class="filename">'+this.filename+' ('+this.fsize+')</span> <span class="actions"><a href="#" class="log button button-primary">View Log</a></span><pre>'+this.log+'</pre></li><div class="clear"></div>');
				}
			});
			}
			
			// remove li, if not set (JS)
			jQuery('#fastvelocity_min_jsprocessed ul li, #fastvelocity_min_cssprocessed ul li').each(function(){
				if(jQuery.inArray(jQuery(this).attr('id'), fvmarr) == -1) {
					jQuery('#' + jQuery(this).attr('id')).remove();
				} 
			});
			
			// check for new files
			timeout = setTimeout(getFiles, 4000);
		});
	}

	getFiles();

});