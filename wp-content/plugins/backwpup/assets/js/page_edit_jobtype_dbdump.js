jQuery(document).ready(function ($) {

    $('#dball').click(function () {
        $('input[name="tabledb[]"]').prop("checked", true).change();
    });

    $('#dbnone').click(function () {
        $('input[name="tabledb[]"]').prop("checked", false).change();
    });

    $('#dbwp').click(function () {
        $('input[name="tabledb[]"]').prop("checked", false).change();
        $('input[name="tabledb[]"][value^="' + $('#dbwp').val() + '"]').prop("checked", true).change();
    });

	var dbdumpwpdbsettings = $('input[name="dbdumpwpdbsettings"]');
	if ( dbdumpwpdbsettings.length > 0 ) {
		dbdumpwpdbsettings.change(function () {
			if ( dbdumpwpdbsettings.prop("checked") ) {
				$('#dbconnection').hide();
			} else {
				$('#dbconnection').show();
			}
		});
	}

	var trdbdumpmysqlfolder = $('#trdbdumpmysqlfolder');
	if ( trdbdumpmysqlfolder.length > 0 ) {
		$('input[name="dbdumptype"]').change(function () {
			if ( $('#iddbdumptype-syssql').prop("checked") ) {
				trdbdumpmysqlfolder.show();
			} else {
				trdbdumpmysqlfolder.hide();
			}
		});

		if ( $('#iddbdumptype-syssql').prop("checked") ) {
			trdbdumpmysqlfolder.show();
		} else {
			trdbdumpmysqlfolder.hide();
		}
	}

    function db_tables() {
        var data = {
            action:'backwpup_jobtype_dbdump',
            action2:'tables',
            dbname:$('#dbdumpdbname').val(),
            dbhost:$('#dbdumpdbhost').val(),
            dbuser:$('#dbdumpdbuser').val(),
            dbpassword:$('#dbdumpdbpassword').val(),
            wpdbsettings:$('#dbdumpwpdbsettings:checked').val(),
            jobid:$('#jobid').val(),
            _ajax_nonce:$('#backwpupajaxnonce').val()
        };
        $.post(ajaxurl, data, function (response) {
            $('#dbtables').replaceWith(response);
        });
    }
    $('#dbdumpdbname').change(function () {
        db_tables();
    });
    $('#dbdumpwpdbsettings').change(function () {
		db_tables();
		db_databases();
    });

    function db_databases() {
        var data = {
            action:'backwpup_jobtype_dbdump',
            action2:'databases',
            dbhost:$('#dbdumpdbhost').val(),
            dbuser:$('#dbdumpdbuser').val(),
            dbpassword:$('#dbdumpdbpassword').val(),
            dbname:$('input[name="dbselected"]').val(),
			wpdbsettings:$('#dbdumpwpdbsettings:checked').val(),
            _ajax_nonce:$('#backwpupajaxnonce').val()

        };
        $.post(ajaxurl, data, function (response) {
            $('#dbdumpdbname').replaceWith(response);
            db_tables();
            $('#dbdumpdbname').change(function () {
                db_tables();
            });
        });
    }
    $('#dbdumpdbhost').change(function () {
        db_databases();
    });
    $('#dbdumpdbuser').change(function () {
        db_databases();
    });
    $('#dbdumpdbpassword').change(function () {
        db_databases();
    });
});
