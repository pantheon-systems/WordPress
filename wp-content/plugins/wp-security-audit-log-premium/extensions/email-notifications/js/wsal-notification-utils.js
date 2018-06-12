/**
 * Format a string
 * Example: 'Hello {0}!'.wsalFormat('Your Name');
 * @see: http://stackoverflow.com/a/2648463
 * @returns {string}
 */
String.wsalFormat = function() {
    var s = arguments[0];
    for (var i=0; i<arguments.length-1; i++) {
        var reg = new RegExp("\\{"+i+"\\}", "gm");
        s = s.replace(reg, arguments[i+1]);
    }
    return s;
};
/**
 * Cleanup html entities from the given string
 * @param string The string to cleanup
 * @returns {string}
 */
var wsalRemoveHtml = function(string) {
    var entityMap = { "&": "","<": "",">": "",'"': '',"'": '',"/": '',"?" : '',"!" : '',"#" : '' };
    return String(string).replace(/[&<>"'\/]/g, function (s) { return entityMap[s]; });
};
/**
 * Sanitize the provided input
 * @param input string. The string to sanitize
 * @param forSearch boolean Whether or not this function should be used in the search form
 * @returns {string}
 */
var wsalSanitize = function(input, forSearch) {
    var output = input.replace(/<script[^>]*?>.*?<\/script>/gi, '').
        replace(/<[\/\!]*?[^<>]*?>/gi, '').
        replace(/<style[^>]*?>.*?<\/style>/gi, '').
        replace(/<![\s\S]*?--[ \t\n\r]*>/gi, '');
    if(forSearch){
        output = wsalRemoveHtml(output);
    }
    return output.replace(/[^A-Z0-9_-]/gi, '');
};
/**
 * Sanitize the input from triggers
 * @param input string The text to sanitize
 * @returns {string}
 */
var wsalSanitizeCondition = function(input){
    var output = input.replace(/<script[^>]*?>.*?<\/script>/gi, '').
        replace(/<[\/\!]*?[^<>]*?>/gi, '').
        replace(/<style[^>]*?>.*?<\/style>/gi, '').
        replace(/<![\s\S]*?--[ \t\n\r]*>/gi, '');
    return output.replace(/[^a-z0-9.':\-]/gi, '');
};

// date should be set only when available
function Wsal_CreateDatePicker($, $input, date){
    $input.timeEntry('destroy');
    $input.val(''); // clear
    var WsalDatePick_onSelect = function(date){
        date = date || new Date();
        var v = $.datepick.formatDate(dateFormat, date[0]);
        $input.val(v);
    };
    $input.datepick({
        dateFormat: dateFormat,
        selectDefaultDate: true,
        rangeSelect: false,
        multiSelect: 0,
        onSelect: WsalDatePick_onSelect
    }).datepick('setDate', date);
    $input.attr("placeholder", dateFormat);
}
// time should be set only when available
function Wsal_CreateTimePicker($input, time){
    time = time || '12:00';
    $input.datepick('destroy');
    $input.val(''); // clear
    $input.timeEntry({
        spinnerImage: '',
        show24Hours: show24Hours
    }).timeEntry('setTime', time);
}

function Wsal_RemovePickers($input){
    $input.datepick('destroy');
    $input.timeEntry('destroy');
    $input.val(''); // clear
}
//
var handleOptionsDropDown = function($, $dd){
    $dd.empty()
        .append($("<option></option>").attr("value", '-1').text(WsalTranslator.groupOptions))
        .append($("<option></option>").attr("value", 'groupAbove').text(WsalTranslator.groupAbove))
        .append($("<option></option>").attr("value", 'groupBelow').text(WsalTranslator.groupBelow))
        .append($("<option></option>").attr("value", 'ungroup').text(WsalTranslator.ungroup))
        .append($("<option></option>").attr("value", 'moveUp').text(WsalTranslator.moveUp))
        .append($("<option></option>").attr("value", 'moveDown').text(WsalTranslator.moveDown))

    var $parent = $dd.parents('.wsal_trigger')
        ,$group = $dd.parents('.'+GroupManager.groupDefaultCssClass)
        ,inGroup = ($group.length > 0);

    var prevElement = $parent.prev()
        ,prevIsFirstChild = true
        ,prevIsTrigger = prevElement.length>0 ? GroupManager.IsTrigger(prevElement): false
        ,prevIsGroup = prevElement.length>0 ? GroupManager.IsGroup(prevElement): false
        ,nextElement = $parent.next()
        ,nextIsTrigger = nextElement.length>0 ? GroupManager.IsTrigger(nextElement) : false
        ,nextIsGroup = nextElement.length>0 ? GroupManager.IsGroup(nextElement) : false
        ,s1 = prevElement.find('.wsal-s1 select');

    if(prevElement.prev().length > 0){
        prevIsFirstChild = false;
    }

    if(inGroup) {
        $dd.find("option[value='ungroup']").prop("disabled", false);
        $dd.find("option[value='groupAbove']").prop("disabled", true);
        $dd.find("option[value='groupBelow']").prop("disabled", true);
        if(s1.length<1){
            $dd.find("option[value='moveUp']").prop("disabled", true);
        }
        if(nextIsTrigger){
            $dd.find("option[value='moveDown']").prop("disabled", false);
        }
        else { $dd.find("option[value='moveDown']").prop("disabled", true); }
    }
    else {
        $dd.find("option[value='ungroup']").prop("disabled", true);

        if(s1.length>0){
            $dd.find("option[value='moveUp']").prop("disabled", false);
        }
        if(nextIsTrigger || nextIsGroup){
            $dd.find("option[value='moveDown']").prop("disabled", false);
        }
        else {
            $dd.find("option[value='groupBelow']").prop("disabled", true);
            $dd.find("option[value='moveDown']").prop("disabled", true);
        }
        if(prevIsFirstChild){
            $dd.find("option[value='moveUp']").prop("disabled", true);
        }
        else { $dd.find("option[value='moveUp']").prop("disabled", false); }
    }
};

/**
 * Retrieve the view state of the notification
 * @returns {Array}
 */
var getViewState = function(){
    $ = jQuery;
    var result = [], children = $('#wsal_content_js').children();
    if(children.length){
        $.each(children, function(i,v){
            var group = [];
            var element = $(this);
            //id = element.attr('id')
            if(GroupManager.IsTrigger(element)){
                result.push(element.attr('id'));
            }
            else if(GroupManager.IsGroup(element)){
                var elements = element.children();
                $.each(elements, function(k,j){
                    group.push($(this).attr('id'));
                });
                result.push(group);
            }
        });
    }
    return result;
};
