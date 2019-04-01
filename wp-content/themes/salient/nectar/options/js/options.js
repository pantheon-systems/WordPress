/*global $, jQuery, document, tabid:true, redux_opts, confirm, relid:true*/

jQuery(document).ready(function () {

    if (jQuery('#last_tab').val() === '') {
        jQuery('.redux-opts-group-tab:first').slideDown('fast');
        jQuery('#redux-opts-group-menu li:first').addClass('active');
    } else {
        tabid = jQuery('#last_tab').val();
        jQuery('#' + tabid + '_section_group').slideDown('fast');
        jQuery('#' + tabid + '_section_group_li').addClass('active');
    }

    jQuery('input[name="' + redux_opts.opt_name + '[defaults]"]').click(function () {
        if (!confirm(redux_opts.reset_confirm)) {
            return false;
        }
    });

    jQuery('.redux-opts-group-tab-link-a').click(function () {
        relid = jQuery(this).attr('data-rel');

        jQuery('#last_tab').val(relid);

        jQuery('.redux-opts-group-tab').each(function () {
            if (jQuery(this).attr('id') === relid + '_section_group') {
                jQuery(this).delay(400).fadeIn(1200);
            } else {
                jQuery(this).fadeOut('fast');
            }
        });

        jQuery('.redux-opts-group-tab-link-li').each(function () {
            if (jQuery(this).attr('id') !== relid + '_section_group_li' && jQuery(this).hasClass('active')) {
                jQuery(this).removeClass('active');
            }
            if (jQuery(this).attr('id') === relid + '_section_group_li') {
                jQuery(this).addClass('active');
            }
        });
    });

    if (jQuery('#redux-opts-save').is(':visible')) {
        jQuery('#redux-opts-save').delay(4000).slideUp('slow');
    }

    if (jQuery('#redux-opts-imported').is(':visible')) {
        jQuery('#redux-opts-imported').delay(4000).slideUp('slow');
    }

    jQuery('#redux-opts-form-wrapper').on('change', 'input, textarea, select', function () {
        if(this.id === 'google_webfonts' && this.value === '') return;
        jQuery('#redux-opts-save-warn').slideDown('slow');
    });

    jQuery('#redux-opts-import-code-button').click(function () {
        if (jQuery('#redux-opts-import-link-wrapper').is(':visible')) {
            jQuery('#redux-opts-import-link-wrapper').fadeOut('fast');
            jQuery('#import-link-value').val('');
        }
        jQuery('#redux-opts-import-code-wrapper').fadeIn('slow');
    });

    jQuery('#redux-opts-import-link-button').click(function () {
        if (jQuery('#redux-opts-import-code-wrapper').is(':visible')) {
            jQuery('#redux-opts-import-code-wrapper').fadeOut('fast');
            jQuery('#import-code-value').val('');
        }
        jQuery('#redux-opts-import-link-wrapper').fadeIn('slow');
    });

    jQuery('#redux-opts-export-code-copy').click(function () {
        if (jQuery('#redux-opts-export-link-value').is(':visible')) {jQuery('#redux-opts-export-link-value').fadeOut('slow'); }
        jQuery('#redux-opts-export-code').toggle('fade');
    });

    jQuery('#redux-opts-export-link').click(function () {
        if (jQuery('#redux-opts-export-code').is(':visible')) {jQuery('#redux-opts-export-code').fadeOut('slow'); }
        jQuery('#redux-opts-export-link-value').toggle('fade');
    });
    
    
    //5 column fields
    jQuery('.redux-opts-group-tab').each(function(){

		//add class speicifcally for header options tab to beind hide/show events	
		var $specificClass = (jQuery(this).attr('id') == '4_section_group') ? ' header-colors' : '' ;
		
	    jQuery(this).find('input.five-columns').parents('tr').wrapAll('<tr class="five-columns'+$specificClass+'">');
	    jQuery(this).find('input.five-columns').parents('td').unwrap();
	    jQuery(this).find('input.five-columns').parents('.wp-picker-container').unwrap();
	  
	    ////update desc
	    jQuery(this).find('input.five-columns').parents('.wp-picker-container').each(function(i){
	    	var $desc = jQuery(this).prev('th').find('span').text();
	   		jQuery(this).prev('th').remove();
	   		jQuery(this).prepend('<p>'+$desc+'</p>');
	   		jQuery(this).css('z-index',i + 1);
	    });
	  	
	  	////wrap the new markup in a full table col
	  	jQuery(this).find('input.five-columns').parents('.wp-picker-container').wrapAll('<td colspan="2">'); 	
	  	
	  	
	  	////add classes to clear columns
	  	jQuery(this).find('tr.five-columns td > div:nth-child(5n+6)').addClass('clear');
	  	
  	});
  		
    //only show header color options when custom is selected
    jQuery('#redux-opts-main select#header-color').change(function(){
    	headerColorsDisplay(jQuery(this).val());
    });
    
    ////on load
    headerColorsDisplay(jQuery('#redux-opts-main select#header-color').val());
    
    function headerColorsDisplay(selectedVal){
    	if(selectedVal == 'custom') { jQuery('#redux-opts-main #4_section_group tr.five-columns.header-colors').show(); } else {
    		jQuery('#redux-opts-main #4_section_group tr.five-columns.header-colors').hide();
    	}
    }

    //only show header color options when custom is selected
    jQuery('#redux-opts-main select#transition-method').change(function(){
        disableFadeOutOnClick(jQuery(this).val());
    });

    ////on load
    disableFadeOutOnClick(jQuery('#redux-opts-main select#transition-method').val());

    function disableFadeOutOnClick(selectedVal) {
        if(selectedVal == 'standard') { 
            jQuery('#disable-transition-fade-on-click').parents('tr').fadeIn(); 
        } else {
            jQuery('#disable-transition-fade-on-click').parents('tr').fadeOut(); 
        }
    }


    //hide social sharing for blog when using fullscreen header
    function blogSocialToggle(){
        if(jQuery('#blog_header_type').val() == 'fullscreen') {
            jQuery('input#blog_social').parents('tr').hide();
            jQuery('input#blog_social').parents('tr').next('tr').hide();
            jQuery('input#blog_social').parents('tr').next('tr').next('tr').hide();
            jQuery('input#blog_social').parents('tr').next('tr').next('tr').next('tr').hide();
            jQuery('input#blog_social').parents('tr').next('tr').next('tr').next('tr').next('tr').hide();
            jQuery('input#blog_social').parents('tr').next('tr').next('tr').next('tr').next('tr').next('tr').hide();
        } else {
            jQuery('input#blog_social').parents('tr').show();
            if(jQuery('input#blog_social:checked').length > 0){
                jQuery('input#blog_social').parents('tr').next('tr').show();
                jQuery('input#blog_social').parents('tr').next('tr').next('tr').show();
                jQuery('input#blog_social').parents('tr').next('tr').next('tr').next('tr').show();
                jQuery('input#blog_social').parents('tr').next('tr').next('tr').next('tr').next('tr').show();
                jQuery('input#blog_social').parents('tr').next('tr').next('tr').next('tr').next('tr').next('tr').show();
            }
        }
    }

    blogSocialToggle();
    jQuery('#blog_header_type').change(blogSocialToggle);
    
});
