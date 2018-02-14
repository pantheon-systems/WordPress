/**
 * Not really a tour, but uses the same library to introduce the plugin.
 * This should always show when the user has no slideshows
 */

var no_slideshows = new Shepherd.Tour({
    defaults: {
        classes: 'shepherd-theme-arrows shepherd-no-slideshows'
    }
});
no_slideshows.addStep('welcome', {
    title: metaslider_tour.no_slideshows.title,
    text: metaslider_tour.no_slideshows.message,
    attachTo: '#create_new_tab bottom',
    tetherOptions: {
        offset: '-5px 0'
    },
    buttons: []
});
      
metaslider_tour.no_slideshows.show && no_slideshows.start();

// Main tour to introduce the plugin
var main_tour = new Shepherd.Tour();

// Set up the defaults for each step
main_tour.options.defaults = {
    classes: 'shepherd-theme-arrows shepherd-main-tour',
    showCancelLink: true
};

// If we have passed step two, don't let the media
// uploader events below effect the UX
main_tour.can_still_go_back = true;

// If they have slides already, allow them to skip this step
var skip_button = jQuery('.metaslider-ui .slide').length ? [{
    text: metaslider_tour.main_tour.skip_language,
    action: function() {
        main_tour.show('step_preview_slideshow');
    }
}] : [];
main_tour.addStep('step_add_slide', {
    title: metaslider_tour.main_tour.step1.title,
    text: metaslider_tour.main_tour.step1.message,
    attachTo: 'button.add-slide bottom',
    tetherOptions: {
        offset: '-5px 0'
    },
    buttons: skip_button
});
main_tour.addStep('step_show_slide_types', {
    title: metaslider_tour.main_tour.step2a.title,
    text: metaslider_tour.main_tour.step2a.message,
    attachTo: '.media-menu right',
    classes: 'shepherd-theme-arrows shepherd-main-tour super-index',
    tetherOptions: {
        constraints: null,
        attachment: 'top left',
        targetAttachment: 'top right',
        targetOffset: '10% -35px'
    },
    buttons: [
        {
            text: metaslider_tour.main_tour.next_language,
            action: function() {
                main_tour.next();
            }          
        }
    ]
});
main_tour.addStep('step_add_media', {
    title: metaslider_tour.main_tour.step2b.title,
    text: metaslider_tour.main_tour.step2b.message,
    attachTo: '.media-modal.wp-core-ui top',
    classes: 'shepherd-theme-arrows shepherd-main-tour super-index',
    tetherOptions: {
        targetAttachment: 'bottom right',
        offset: '50px 140px'
    },
    buttons: []
});
main_tour.addStep('step_preview_slideshow', {
    title: metaslider_tour.main_tour.step3.title,
    text: metaslider_tour.main_tour.step3.message,
    attachTo: '.metaslider-actions left',
    classes: 'shepherd-theme-arrows shepherd-main-tour',
    tetherOptions: {
        offset: '0 5px'
    },
    buttons: [
        {
            text: metaslider_tour.main_tour.next_language,
            action: function() {
                main_tour.next();
            }          
        }
    ]
});
main_tour.addStep('step_edit_settings', {
    title: metaslider_tour.main_tour.step4.title,
    text: metaslider_tour.main_tour.step4.message,
    attachTo: '#metaslider_configuration left',
    classes: 'shepherd-theme-arrows shepherd-main-tour',
    scrollTo: true,
    tetherOptions: {
        offset: '0 5px'
    },
    buttons: [
        {
            text: metaslider_tour.main_tour.next_language,
            action: function() {
                main_tour.next();
            }          
        }
    ]
});
main_tour.addStep('step_view_shortcode', {
    title: metaslider_tour.main_tour.step5.title,
    text: metaslider_tour.main_tour.step5.message,
    attachTo: '.metaslider-shortcode bottom',
    classes: 'shepherd-theme-arrows shepherd-main-tour',
    when: {
        show: function() {
            window.scrollTo(0, 0);
        }
    },
    tetherOptions: {
        offset: '-5px 0'
    },
    buttons: [
        {
            text : metaslider_tour.main_tour.step5.button,
            action : main_tour.cancel
        }
    ]
});
main_tour.addStep('step_show_ad', {
    title: metaslider_tour.main_tour.final_ad.title,
    text: metaslider_tour.main_tour.final_ad.message,
    classes: 'shepherd-theme-arrows shepherd-main-tour super-index',
    showCancelLink: false,
    buttons: [
        {
            text: metaslider_tour.main_tour.learn_more_language,
            action: function() {
                window.open(metaslider_tour.main_tour.upgrade_link, '_blank').focus();
            },
            classes: 'btn-cta-gradient'
        },
        {
            text : metaslider_tour.main_tour.final_ad.button,
            action : function() {
                jQuery('#wpwrap, .media-modal').removeClass('blurred-out');
                main_tour.hide();
            }
        }
    ]
});
main_tour.on('cancel', function() {
    
    // The tour is either finished or they hit the x
    main_tour.can_still_go_back = false; 
    var data = {
        action: 'set_tour_status',
        _wpnonce: metaslider_tour.main_tour.nonce,
        current_step: this.getCurrentStep().id
    };
    jQuery.ajax({
        url: metaslider.ajaxurl, 
        data: data,
        type: 'POST',
        error: function(response) {

            // Error to the console (useful if we get support feedback
            // that the tour always shows)
            console.log("Tour error: " + response.responseJSON.data.message);
        },
        success: function(response) {

            // We will show a final ad to lite users only when they cancel
            // and there are no errors (don't want to annoy them if there's a bug)
            if (!metaslider_tour.main_tour.is_pro) {
                jQuery('#wpwrap, .media-modal').addClass('blurred-out');
                main_tour.show('step_show_ad');
            }

        }
    });
});

metaslider_tour.main_tour.show && main_tour.start();

// Specifics for lite users (i.e. ads)
if (!metaslider_tour.main_tour.is_pro) {
    
    // Add a CTA button to this step if add-on pack is disabled
    main_tour.getById('step_show_slide_types').options.buttons.unshift({
        text: metaslider_tour.main_tour.learn_more_language,
        action: function() {
            window.open(metaslider_tour.main_tour.upgrade_link, '_blank').focus();
        },
        classes: 'btn-cta-gradient'
    });
}

// Load jQuery events after DOM load
jQuery(function($) {
    if (metaslider_tour.main_tour.show) {

        // When the add slide UI is opened, progress the tour
        $('body').on('click', '.add-slide.shepherd-target', function(event) {
            window.setTimeout(function() {
                main_tour.can_still_go_back && main_tour.show('step_show_slide_types');

                // If it's an image, we can progress (see below for other slides)
                var add_to_slideshow_event = function() {
                    $('.media-button').on('click', function(event) {
                        main_tour.can_still_go_back && main_tour.show('step_preview_slideshow');
                        main_tour.can_still_go_back = false;
                    });
                };
                add_to_slideshow_event();

                // If we're on pro, call the above function. But if we're on lite, then
                // we have to rebind the events. I'm not really sure why, but it works,
                // and without it, it doesn't work. 
                metaslider_tour.main_tour.is_pro || $('.media-menu-item').on('click', function(event) {
                    add_to_slideshow_event();
                    if($('.media-menu-item').eq(0).is(this)) {
                        main_tour.show('step_add_media');
                    } else {
                        main_tour.hide();
                    }
                });
            }, 500);
        });
    
        // When the UI is closed and no slide is set, reverse the tour
        $('body').on('click', '.media-modal-close, .media-modal-backdrop', function(event) {

            // If the user is pro, and adds a non-image slide, the way it's handled
            // is the iFrame triggers a click on the main add media button. So this will also 
            // fire. Let's move forward if the count of slides is more than 1
            var slide_count = $('.metaslider-slides-container .slide').length;
            if (metaslider_tour.main_tour.is_pro && slide_count) {
                main_tour.can_still_go_back && main_tour.show('step_preview_slideshow');
                main_tour.can_still_go_back = false;
            }

            // Only go back if we still can (i.e. reopening the UI doesn't reset the tour)
            main_tour.can_still_go_back && main_tour.show('step_add_slide');
        });

        // This is to handle the Escape key.
        $(document).keyup(function(event) {
            
            // If they press escape during the add-media step, send them back one step.
			if (27 == event.keyCode && ('add-media' === main_tour.getCurrentStep().id)) {
                main_tour.can_still_go_back && main_tour.show('step_add_slide');
            }
        });
    }
});
