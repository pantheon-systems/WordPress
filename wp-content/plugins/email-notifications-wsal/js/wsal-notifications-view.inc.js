//@ requires jQuery
//@ requires jQuery.WSAL_EDIT_VIEW
//@ requires Wsal_FormValidator

var formValidator = new Wsal_FormValidator($, 'wsal_trigger',  'wsal-error-container', 'wsal-notif-title', 'wsal-notif-email', 'wsal-trigger-input', 'invalid');

// TITLE
var tplTitle = $('#scriptTitle').text().trim();
$('#wsal-section-title').append( Mark.up(tplTitle, wsalModel) );

// EMAIL
var tplEmail = $('#scriptEmail').text().trim();
$('#wsal-section-email').append( Mark.up(tplEmail, wsalModel) );

// TRIGGERS
var jsContentWrapper = $('#wsal_content_js')
    ,tplTrigger       = $('#scriptTrigger').text().trim();

// GLOBALS
Mark.globals.numTriggers = 0;      // holds the number of triggers added to the view
Mark.globals.lastId = 0;           // counter for each trigger added
Mark.globals.maxTriggers = 30;      // max number of triggers allowed to the view

// Removes the first dropdown from the first trigger from the view
// it also takes care that the correct css class is applied to this trigger
var updateView = function(){
    if(Mark.globals.numTriggers){
        // Find the first trigger and remove the first dropdown
        jsContentWrapper.find('.wsal_trigger')
            .first()
            .removeClass('wsal-section-light-full')
            .addClass('wsal-section-light-first')
            .find('.js_s1').remove();
    }
};

var handleS3Options = function(selectedValue, s3Control){
    if(selectedValue == 'ALERT CODE'){
        s3Control.empty();
        s3Control.append($("<option></option>").attr("value", 'IS EQUAL').text('IS EQUAL'));
    }
    else if(selectedValue == 'DATE'){
        s3Control.empty();
        s3Control.append($("<option></option>").attr("value", 'IS EQUAL').text('IS EQUAL'));
        s3Control.append($("<option></option>").attr("value", 'IS AFTER').text('IS AFTER'));
    }
    else if(selectedValue == 'TIME'){
        s3Control.empty();
        s3Control.append($("<option></option>").attr("value", 'IS EQUAL').text('IS EQUAL'));
        s3Control.append($("<option></option>").attr("value", 'IS BEFORE').text('IS BEFORE'));
        s3Control.append($("<option></option>").attr("value", 'IS AFTER').text('IS AFTER'));
    }
    else if(selectedValue == 'USERNAME' || selectedValue == 'USER ROLE'){
        s3Control.empty();
        s3Control.append($("<option></option>").attr("value", 'IS EQUAL').text('IS EQUAL'));
        s3Control.append($("<option></option>").attr("value", 'IS NOT').text('IS NOT'));
    }
    else if(selectedValue == 'SOURCE IP'){
        s3Control.empty();
        s3Control.append($("<option></option>").attr("value", 'IS EQUAL').text('IS EQUAL'));
        s3Control.append($("<option></option>").attr("value", 'CONTAINS').text('CONTAINS'));
        s3Control.append($("<option></option>").attr("value", 'IS NOT').text('IS NOT'));
    }
    else if(selectedValue == 'POST ID' || selectedValue == 'PAGE ID' || selectedValue == 'CUSTOM POST ID' || selectedValue == 'SITE DOMAIN'){
        s3Control.empty();
        s3Control.append($("<option></option>").attr("value", 'IS EQUAL').text('IS EQUAL'));
    }
    else if(selectedValue == 'POST TYPE'){
        s3Control.empty();
        s3Control.append($("<option></option>").attr("value", 'IS EQUAL').text('IS EQUAL'));
    }
    // invalid option
    else { s3Control.empty(); }
};

// Hook up listeners to the controls inside a trigger
var bindListeners = function(trigger, lastId)
{
    var selectedValue = ''
        ,s1 = $('#select_1_'+lastId)
        ,s2 = $('#select_2_'+lastId)
        ,s3 = $('#select_3_'+lastId)
        ,i1 = $('#input_1_'+lastId);

    if(s1.length>0){
        s1.on('change', function(){
            var selected = trigger.select1.data.indexOf($(this).val());
            trigger.select1.selected = selected;
            $('#select_1_'+lastId+'_hidden').val(selected);
        });
        // Set selected item
        selectedValue = trigger.select1.data[trigger.select1.selected];
        s1.val(selectedValue);
        $('#select_1_'+lastId+'_hidden').val(trigger.select1.selected);
    }
    s2.on('change', function(){
        var selectedValue = $(this).val();
        var selected = trigger.select2.data.indexOf(selectedValue);

        trigger.select2.selected = selected;
        $('#select_2_'+lastId+'_hidden').val(selected);

        // Update the input1 as needed based on user selection
        if (selectedValue == 'DATE') {
            Wsal_CreateDatePicker($, i1, null);
        } else if (selectedValue == 'TIME') {
            Wsal_CreateTimePicker(i1, null);
        }
        // remove both
        else { 
            Wsal_RemovePickers(i1); 
        }

        // Disable invalid options from select3
        handleS3Options(selectedValue, s3);
    });
    // Set selected item
    selectedValue = trigger.select2.data[trigger.select2.selected];
    s2.val(selectedValue);
    $('#select_2_'+lastId+'_hidden').val(trigger.select2.selected);

    // Create plugins if needed
    if(jQuery.WSAL_EDIT_VIEW){
        var what = trigger.select2.data[trigger.select2.selected];
        if(what == 'DATE'){
            Wsal_CreateDatePicker($, i1, trigger.input1);
        }
        else if(what == 'TIME'){
            Wsal_CreateTimePicker(i1, trigger.input1);
        }
        handleS3Options(what, s3);
    }

    s3.on('change', function(){
        var selected = trigger.select3.data.indexOf($(this).val());
        trigger.select3.selected = selected;
        $('#select_3_'+lastId+'_hidden').val(selected);
    });
    // Set selected item
    selectedValue = trigger.select3.data[trigger.select3.selected];
    s3.val(selectedValue);
    $('#select_3_'+lastId+'_hidden').val(trigger.select3.selected);

    i1.val(trigger.input1);

    $('#deleteButton_'+lastId).on('click', function()
    {
        if(Mark.globals.numTriggers < 1){
            return false;
        }

        // remove trigger from DOM
        var eId = $(this).data('removeid');
        $('#'+eId).remove();

        Mark.globals.numTriggers --;

        // Update view
        updateView();
        return true;
    });

    // Attach event listeners for grouping
    $('#buttonAddToGroup_'+lastId).on('click', function(){
        var parentID = $(this).data('parentid');
        var parent = $('#'+parentID);
        GroupManager.AddToGroup(parent, $);
    });

    var dd = $('#wsal_options_'+lastId), $trigger = dd.parents('#trigger_id_'+lastId);
    dd.on('change', function(){
        var option = $(this).val();
        switch(option) {
            case "groupAbove": { GroupManager.GroupAbove($trigger); break; }
            case "groupBelow": { GroupManager.GroupBelow($trigger); break; }
            case "ungroup": { GroupManager.Ungroup($trigger); break; }
            case "moveUp": { GroupManager.MoveUp($trigger); break; }
            case "moveDown": { GroupManager.MoveDown($trigger); break; }
        }
        handleOptionsDropDown(jQuery, jQuery(this));
    });
};

// prepares the model object to be sent server-side for processing
var preparePostData = function()
{
    wsalModel.info.title = $('#wsal-notif-title').val().trim();
    wsalModel.errors.titleMissing = '';
    wsalModel.errors.titleInvalid = '';
    wsalModel.info.email = $('#wsal-notif-email').val().trim();
    wsalModel.errors.emailMissing = '';
    wsalModel.errors.emailInvalid = '';

    var _triggers = $('.wsal_trigger');
    wsalModel.triggers = []; // reset first

    // set the view state
    wsalModel.viewState = getViewState();

    // update triggers
    $.each(wsalModel.viewState, function(i, entry){
        if($.isArray(entry)){
            $.each(entry, function(k, id){
                var trigger = $('#'+id),
                    s1Selected = ~~$('.wsal-s1 input[type="hidden"]',trigger).val(),
                    s2Selected = ~~$('.wsal-s2 input[type="hidden"]',trigger).val(),
                    s3Selected = ~~$('.wsal-s3 input[type="hidden"]',trigger).val(),
                    i1 = $('.wsal-fly .wsal-trigger-input',trigger).val().trim();
                var obj = {
                    "select1": {
                        "data": wsalModel.default.select1.data,
                        "selected": s1Selected
                    },
                    "select2": {
                        "data": wsalModel.default.select2.data,
                        "selected": s2Selected
                    },
                    "select3": {
                        "data": wsalModel.default.select3.data,
                        "selected": s3Selected
                    },
                    "input1": i1,
                    "deleteButton": WsalTranslator.deleteButtonText
                };
                wsalModel.triggers.push(obj);
            });
        }
        else {
            var trigger = $('#'+entry),
                s1Selected = ~~$('.wsal-s1 input[type="hidden"]',trigger).val(),
                s2Selected = ~~$('.wsal-s2 input[type="hidden"]',trigger).val(),
                s3Selected = ~~$('.wsal-s3 input[type="hidden"]',trigger).val(),
                i1 = $('.wsal-fly .wsal-trigger-input',trigger).val().trim();
            var obj = {
                "select1": {
                    "data": wsalModel.default.select1.data,
                    "selected": s1Selected
                },
                "select2": {
                    "data": wsalModel.default.select2.data,
                    "selected": s2Selected
                },
                "select3": {
                    "data": wsalModel.default.select3.data,
                    "selected": s3Selected
                },
                "input1": i1,
                "deleteButton": WsalTranslator.deleteButtonText
            };
            console.log(s2Selected);
            wsalModel.triggers.push(obj);
        }
    });
    // Set input data
    $('#wsal-form-data').val(JSON.stringify(wsalModel));
};

/**
 * Restore the groups in the notification view
 */
var restoreViewState = function(){
    var data = wsalModel.viewState;
    if(data.length){
        $.each(data, function(i, entry){
            // restore groups
            if($.isArray(entry)){
                var target = $('#'+entry[0]);
                var elements = [];
                $.each(entry, function(j, id){ if(j>0){ elements.push($('#'+id)); } });
                GroupManager.MakeGroup(target, elements);
            }
        });
    }
};

// Add a trigger to the view
var addTrigger = function(tplTrigger, model){
    Mark.globals.lastId ++;
    Mark.globals.numTriggers ++;
    // Clear first
    if(! jQuery.WSAL_EDIT_VIEW){
        model.select1.selected = 0;
        model.select2.selected = 0;
        model.select3.selected = 0;
        model.input1 = '';
    }
    jsContentWrapper.append( Mark.up(tplTrigger, model) );
    handleS3Options(model.select2.data[model.select2.selected], $('#select_3_'+Mark.globals.lastId));
};

//region >>> ON_LOAD

// if there are triggers to display
var _triggers = wsalModel.triggers;
var _tl = _triggers.length;
if(_tl){
    // Remove extra triggers if any
    if(_tl >= Mark.globals.maxTriggers){
        var j = Mark.globals.maxTriggers;
        for (j; j < _tl; j++){
            _triggers.pop();
        }
        _tl = Mark.globals.maxTriggers;
    }
    // Append triggers
    $.each(_triggers, function(i, triggerData){
        addTrigger(tplTrigger, triggerData);
        bindListeners(triggerData, Mark.globals.lastId);
    });
    // Restore groups
    restoreViewState();
    GroupManager.__updateOptions();
}
else { wsalModel.viewState = []; }


// Display errors if any
if(! jQuery.isEmptyObject(wsalModel.errors.triggers))
{
    formValidator.clearErrors();
    $.each(wsalModel.errors.triggers, function(k, error){
        formValidator.addError(error);
        $('#input_1_'+k).addClass('invalid');
    });
    formValidator.addTitleForErrors('<span style="margin-bottom: 5px; display: block;"><strong style="font-size: 13px; padding-bottom:5px;">'+WsalTranslator.errorsTitle+'</strong></span>');
    formValidator.showErrors();
}
//endregion >>> ON_LOAD

$('#wsal-button-add-trigger').on('click', function(){
    if(Mark.globals.numTriggers < Mark.globals.maxTriggers){
        if(jQuery.WSAL_EDIT_VIEW){
            // Clear first
            wsalModel.default.select1.selected = 0;
            wsalModel.default.select2.selected = 0;
            wsalModel.default.select3.selected = 0;
            wsalModel.default.input1 = '';
        }
        addTrigger(tplTrigger, wsalModel.default);
        bindListeners(wsalModel.default, Mark.globals.lastId);
        GroupManager.__updateOptions();
    }
});

$('#wsal-submit').click(function(){
    if(formValidator.validate()){
        preparePostData();
        return true;
    }
    return false;
});