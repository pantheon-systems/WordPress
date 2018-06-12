/*
 * Trigger grouping
 */
var GroupManager = {
    groupDefaultCssClass: 'wsal-condition-box',
    groupUIClass: 'wsal-group',
    triggerDefaultCssClass: 'wsal_trigger',
    triggerGroupCssClass: 'wsal_trigger wsal-group-darken',
    triggerDarkenCssClass: 'wsal-group-darken',

    GroupAbove: function(trigger){
        var parent = trigger.parent()
            ,prev = null;
        if(GroupManager.IsGroup(parent)){
            prev = parent.prev();
            if(GroupManager.IsTrigger(prev)){
                GroupManager.MakeGroup(prev, [trigger]);
                GroupManager.__updateOptions();
            }
            else if(GroupManager.IsGroup(prev)){
                GroupManager.AddToGroup(prev, trigger);
                GroupManager.__updateOptions();
            }
        }
        else {
            prev = trigger.prev();
            if(GroupManager.IsTrigger(prev)){
                GroupManager.MakeGroup(prev, [trigger]);
                GroupManager.__updateOptions();
            }
            else {
                GroupManager.AddToGroup(prev, trigger);
                GroupManager.__updateOptions();
            }
        }
    },

    GroupBelow: function(trigger){
        var parent = trigger.parent()
            ,next = null;
        if(GroupManager.IsGroup(parent)){
            next = parent.next();
            if(GroupManager.IsTrigger(next)){
                GroupManager.MakeGroup(next, [trigger]);
                GroupManager.__updateOptions();
            }
            else if(GroupManager.IsGroup(next)){
                GroupManager.AddToGroup(next, trigger);
                GroupManager.__updateOptions();
            }
        }
        else {
            next = trigger.next();
            if(GroupManager.IsTrigger(next)){
                GroupManager.MakeGroup(next, [trigger]);
                GroupManager.__updateOptions(trigger);
            }
            else {
                GroupManager.AddToGroup(next, trigger);
                GroupManager.__updateOptions();
            }
        }
    },

    AddToGroup: function($target, $element){
        $target.append($element);
        $element.removeClass().addClass(GroupManager.triggerGroupCssClass);
    },

    Ungroup: function(trigger){
        var parent = trigger.parent();
        if(GroupManager.IsGroup(parent))
        {
            // get children
            var ch = parent.children();
            if(ch.length == 2){

                var parentPrev = parent.prev();

                if(GroupManager.IsTrigger(parentPrev) || GroupManager.IsGroup(parentPrev)){
                    ch.each(function(){
                        jQuery(this).removeClass(GroupManager.triggerDarkenCssClass);
                    }).insertAfter(parentPrev);
                    parent.remove();
                }
                else {
                    ch.each(function(){
                        jQuery(this).removeClass(GroupManager.triggerDarkenCssClass);
                    }).insertAfter(parent);
                    parent.remove();
                }
            }
            else {
                trigger.insertAfter(parent).removeClass(GroupManager.triggerDarkenCssClass);;
            }
            GroupManager.__updateOptions();
        }
    },

    MoveUp: function(trigger){
        var prev = trigger.prev();
        if(GroupManager.IsTrigger(prev) || GroupManager.IsGroup(prev)){
            trigger.insertBefore(prev);
        }
        GroupManager.__updateOptions();
    },

    MoveDown: function(trigger){
        var next = trigger.next();
        if(GroupManager.IsTrigger(next) || GroupManager.IsGroup(next)){
            trigger.insertAfter(next);
        }
        GroupManager.__updateOptions();
    },

    IsTrigger: function($element){
        return $element.hasClass(GroupManager.triggerDefaultCssClass);
    },
    IsGroup: function($element){
        return $element.hasClass(GroupManager.groupDefaultCssClass);
    },

    // $elements = array()
    MakeGroup : function($target, $elements){
        if($elements.length){
            $target
                .removeClass()
                .addClass(GroupManager.triggerGroupCssClass)
                .wrap('<div class="'+GroupManager.groupDefaultCssClass+' '+GroupManager.groupUIClass+'"></div>');
            jQuery.each($elements, function(i, $element){
                GroupManager.AddToGroup($target.parent(), $element);
            });
            GroupManager.__updateOptions();
        }
    },

    __updateOptions: function(){
        jQuery('.wsal_dd_options').each(function(){
            handleOptionsDropDown(jQuery, jQuery(this));
        });
    }
};
