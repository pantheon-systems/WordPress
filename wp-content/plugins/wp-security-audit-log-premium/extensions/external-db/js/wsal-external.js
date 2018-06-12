jQuery(document).ready(function(){
	var meta_index = 0;
    var occurrence_index = 0;
    var time = '01:00';

    if (jQuery('#archiving-time').val() != "") {
        time = jQuery('#archiving-time').val()
    }

    jQuery('#wsal-time').timeEntry({
        spinnerImage: '',
        show24Hours: is_24_hours
    }).timeEntry('setTime', time);

    // tab handling code
    jQuery('#wsal-tabs>a').click(function(){
        jQuery('#wsal-tabs>a').removeClass('nav-tab-active');
        jQuery('table.wsal-tab').hide();
        jQuery(jQuery(this).addClass('nav-tab-active').attr('href')).show();
    });
    // show relevant tab
    var hashlink = jQuery('#wsal-tabs>a[href="' + location.hash + '"]');
    if (hashlink.length) {
        hashlink.click();
    } else {
        jQuery('#wsal-tabs>a:first').click();
    }

	jQuery('#wsal-migrate').click(function() {
        var button = this;
		jQuery(button).addClass('disabled');
		jQuery(button).val('Migrating, Please Wait..');
		jQuery('#ajax-response').removeClass("hidden");

		MigrateMeta();
	});

	jQuery('#wsal-migrate-back').click(function() {
        var button = this;
		jQuery(button).addClass('disabled');
		jQuery(button).val('Migrating, Please Wait..');
		jQuery('#ajax-response').removeClass("hidden");

		MigrateBackMeta();
	});

	function MigrateMeta() {
		jQuery.ajax({
			type: 'POST',
            url: ajaxurl,
            async: true,
            dataType: 'json',
            data: {
                action: 'MigrateMeta',
                index: meta_index
            },
            success: function(response) {
            	if (typeof response['empty'] != "undefined") {
                    jQuery('#ajax-response').addClass("hidden");
                    msg = "No alerts to import";
                    alert(msg);
                    return;
                }
                meta_index = response['index'];
                if (!response['complete']) {
                    jQuery("#ajax-response-counter").html(' So far '+(query_limit * meta_index)+' alerts have been migrated.');
                    MigrateMeta();
                } else {
                    MigrateOccurrence();
                }
            }
		});
	}

	function MigrateOccurrence() {
		jQuery.ajax({
			type: 'POST',
            url: ajaxurl,
            async: true,
            dataType: 'json',
            data: {
                action: 'MigrateOccurrence',
                index: occurrence_index
            },
            success: function(response) {
            	if (typeof response['empty'] != "undefined") {
                    jQuery('#ajax-response').addClass("hidden");
            		msg = "No alerts to import";
                    alert(msg);
                    return;
            	}
                occurrence_index = response['index'];
                if (!response['complete']) {
                    jQuery("#ajax-response-counter").html(' So far '+(query_limit * occurrence_index)+' alerts have been migrated.');
                    MigrateOccurrence();
                } else {
                    msg = "WordPress security alerts successfully migrated to new database.";
                    afterCompleted('#wsal-migrate', msg);
                    return;
                }
            }
		});
	}

	function MigrateBackMeta() {
		jQuery.ajax({
			type: 'POST',
            url: ajaxurl,
            async: true,
            dataType: 'json',
            data: {
                action: 'MigrateBackMeta',
                index: meta_index
            },
            success: function(response) {
            	if (typeof response['empty'] != "undefined") {
            		jQuery('#ajax-response').addClass("hidden");
                    msg = "No alerts to import";
                    alert(msg);
                    return;
            	}
                meta_index = response['index'];
                if (!response['complete']) {
                    jQuery("#ajax-response-counter").html(' So far '+(query_limit * meta_index)+' alerts have been migrated.');
                    MigrateBackMeta();
                } else {
                   	MigrateBackOccurrence();
                }
            }
		});
	}

	function MigrateBackOccurrence() {
		jQuery.ajax({
			type: 'POST',
            url: ajaxurl,
            async: true,
            dataType: 'json',
            data: {
                action: 'MigrateBackOccurrence',
                index: occurrence_index
            },
            success: function(response) {
            	if (typeof response['empty'] != "undefined") {
            		jQuery('#ajax-response').addClass("hidden");
                    msg = "No alerts to import";
                    alert(msg);
                    return;
            	}
                occurrence_index = response['index'];
                if (!response['complete']) {
                    jQuery("#ajax-response-counter").html(' So far '+(query_limit * occurrence_index)+' alerts have been migrated.');
                    MigrateBackOccurrence();
                } else {
                    msg = "WordPress security alerts successfully migrated to Wordpress database.";
                    afterCompleted('#wsal-migrate-back', msg);
                    return;
                }
            }
		});
	}

	function afterCompleted(button, msg) {
		jQuery(button).val('Migration complete');
		jQuery('#ajax-response').addClass("hidden");
		alert(msg);
	}

    jQuery('#wsal-mirroring').click(function() {
        var button = this;
        jQuery( button ).val( 'Mirroring...' );
        jQuery( button ).attr( 'disabled', 'disabled' );
        MirroringNow( button );
    });

    function MirroringNow( button ) {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            async: true,
            data: {
                action: 'MirroringNow'
            },
            success: function() {
                setTimeout( function() {
                    jQuery( button ).val( 'Mirroring Complete!' );
                }, 1000 );
            }
        });
    }

    jQuery('#wsal-archiving').click(function() {
        var button = this;
        jQuery( button ).val( 'Archiving...' );
        jQuery( button ).attr( 'disabled', 'disabled' );
        ArchivingNow( button );
    });

    function ArchivingNow( button ) {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            async: true,
            data: {
                action: 'ArchivingNow'
            },
            success: function() {
                setTimeout( function() {
                    jQuery( button ).val( 'Archiving Complete!' );
                }, 1000 );
            }
        });
    }
});
