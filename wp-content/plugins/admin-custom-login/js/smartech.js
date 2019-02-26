jQuery(function() {

    jQuery('#side-menu').metisMenu();

    jQuery('.sidebar-toggle').on('click', function() {
        jQuery('body').toggleClass('sidebar-collapse');
        setTimeout(function() {
            jQuery(window).resize()
        }, 850)
    });

   /*  jQuery('.sidebar-search-collapse').on('click', function() {
        jQuery('body').toggleClass('sidebar-collapse');
        jQuery('.sidebar-search input').focus()
        setTimeout(function() {
            jQuery(window).resize()
        }, 500)
    }); */

    jQuery('.panel').on('click', '.panel-collapse', function() {
        var jQuerypanel = jQuery(this).closest('.panel')
        jQuery('.panel-heading .panel-collapse i', jQuerypanel).toggleClass('fa-caret-down').toggleClass('fa-caret-up')
        jQuery('.panel-body', jQuerypanel).toggleClass('hidden')
    })


    if (jQuery().sparkline)
        jQuery("#page-title-statistics").sparkline([10, 3, 4, -3, -2, 5, 8, 11, 6, 7, -7, -5, 8, 9, 5, 6, 7, 2, 0, -4, -2, 4], {
            type: 'bar',
            barColor: '#00a652',
            negBarColor: '#00a652'
        });

    jQuery('#toggle-right-sidebar').on('click', function() {
        jQuery('.sidebar-right').toggleClass('open')

        var width = jQuery(window).width();
        if (width < 768) {
            jQuery('.sidebar-right').attr('style', '')
        } else {
            jQuery('.sidebar-right').height(jQuery('body').height() - 50)
        }

        jQuery('.sidebar-right').css('display', 'block');

        setTimeout(function() {
            jQuery(window).resize()
            if (!jQuery('.sidebar-right').hasClass('open'))
                jQuery('.sidebar-right').hide(0);
        }, 500)
    })

});

jQuery(window).resize(function() {
    var width = jQuery(window).width();
    if (width < 768) {
        jQuery('.sidebar-right.open').attr('style', '')
    } else {
        jQuery('.sidebar-right.open').height(jQuery('body').height() - 50)
    }
})


//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
jQuery(function() {
    jQuery(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            jQuery('div.navbar-collapse').addClass('collapse')
            topOffset = 100; // 2-row-menu
        } else {
            jQuery('div.navbar-collapse').removeClass('collapse')
        }

        height = (this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height;
        height = height - topOffset;
        if (height < 1)
            height = 1;
        if (height > topOffset) {
            jQuery(".page-wrapper").css("min-height", (height) + "px");
        }
    })
})
