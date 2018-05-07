/**
 * Convenient method for form validation.
 *
 * @param $                object - The reference to the instance of the jQuery object.
 * @param triggerClass     string - The class name of triggers.
 * @param errorContainerID string - The ID of the main container.
 * @param titleID          string - The ID of the title element.
 * @param emailID          string - The ID of the email element.
 * @param inputClass       string - The name of the class set for trigger inputs.
 * @param errorClass       string - The error class to set for invalid elements.
 */
var Wsal_FormValidator = function( $, triggerClass, errorContainerID, titleID, emailID, inputClass, errorClass ) {
    // region  PUBLIC
    this.titleRules = { maxLength: 125 };
    this.inputRules = { maxLength: 50 };
    // endregion #PUBLIC

    // region VALIDATION

    // http://jqueryvalidation.org/email-method/
    var validateEmail = function(value) {
        // From http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#e-mail-state-%28type=email%29
        // Retrieved 2014-01-14
        // If you have a problem with this implementation, report a bug against the above spec
        // Or use custom methods to implement your own email validation
        return /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/.test(value);
    };

    var validateTitle = function(value) {
        return /^[ A-Za-z0-9_@#%&*+-=,.!?\$\^]*$/.test(value);
    };

    var isValidIP = function(ip, fullIP) {
        if(!ip){
            return false;
        }

        var parts = ip.split('.')
            ,len = parts.length;

        if(len > 4){
            return false;
        }

        if(fullIP && len != 4){
            return false;
        }
        // partial or full IP check
        for(var i=0; i< len; i++){
            if(parts[i] < 0 || parts[i]>255){
                return false;
            }
        }
        return true;
    };

    var isValidDate = function(date) {
        if (dateFormat == 'mm-dd-yyyy' || dateFormat == 'dd-mm-yyyy') {
            // regular expression to match date format mm-dd-yyyy or dd-mm-yyyy
            regEx = /^(\d{1,2})-(\d{1,2})-(\d{4})$/;
        } else {
            // regular expression to match date format yyyy-mm-dd
            regEx = /^(\d{4})-(\d{1,2})-(\d{1,2})$/;
        }
        return date.match(regEx);
    };
    // endregion VALIDATION

    var self = this
        ,_errors = []
        ,errorContainer = $('#'+errorContainerID)
        ,_errorClass = 'error';
    if ( errorClass.length ) {
        _errorClass = errorClass;
    }

    // region ERROR MGMT
    var addError = function(error) {
        _errors.push('<span style="display: block;">'+error+'</span>');
    };

    var addTitleForErrors = function(title) {
        _errors.unshift(title);
    };

    var showErrors = function() {
        if (_errors.length) {
            var _p = errorContainer.find('p');
            $.each(_errors, function(i, error){
                _p.append(error);
            });
            errorContainer.show();
        }
    };

    var clearErrors = function() {
        _errors = [];
        errorContainer.html('<p></p>').hide();
        // Hide if there are any other messages
        $('.wrap *').removeClass(errorClass);
    };
    //endregion #ERROR MGMT

    //region VALIDATE TRIGGERS
    var validateTrigger = function( triggerContainer, select2Selected, select3Selected, select4Selected, input1 ) {
        var validValues = ["ALERT CODE", "DATE", "TIME", "USERNAME", "USER ROLE", "SOURCE IP", "POST ID", "PAGE ID", "CUSTOM POST ID", "SITE DOMAIN", "POST TYPE", "POST STATUS"]
            , select2Value = validValues[select2Selected];
        var error = '';
        if (!select2Selected || !select2Value) {
            error = WsalTranslator.triggerNotValid;
            triggerContainer.addClass(_errorClass).attr('title', error);
            return {'error':error};
        }

        // Make sure this is a valid input
        var i_val = wsalSanitizeCondition(input1.val().trim())
            ,i_len = i_val.length
            ,i_v = null
            ,ival = null;

        // General validation
        if ( i_len < 1 ) {
            error = select2Value+' '+WsalTranslator.isMissing;
            input1.addClass(_errorClass).attr('title', error);
            return {'error':error};
        } else if ( i_len > self.inputRules.maxLength ) {
            error = WsalTranslator.inputRequired;
            input1.addClass(_errorClass).attr('title', error);
            return {'error':error};
        }

        // ALERT CODE
        if ('ALERT CODE' == select2Value) {
            i_v = parseInt(i_val, 10);
            if (i_v < 1) {
                error = WsalTranslator.alertCodeNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            } else if (!/^\d+$/.test(i_val)) {
                error = WsalTranslator.alertCodeNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
        }

        // DATE
        else if ('DATE' == select2Value) {
            if (! isValidDate(i_val)) {
                error = WsalTranslator.dateNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
        }

        // TIME
        else if('TIME' == select2Value){
            var parts = i_val.split(':');
            if(parts.length != 2){
                error = WsalTranslator.timeNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }

            var p1 = parseInt(parts[0], 10);
            if(p1 < 0 || p1 > 23){
                error = WsalTranslator.timeNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
            var p2 = parseInt(parts[0], 10);
            if(p2 < 0 || p2 > 59){
                error = WsalTranslator.timeNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
        }

        // SOURCE IP
        else if('SOURCE IP' == select2Value){
            validValues = ["IS EQUAL", "CONTAINS", "IS AFTER", "IS BEFORE", "IS NOT"];
            var select3Value = validValues[select3Selected];
            if('IS EQUAL' == select3Value || 'IS NOT' == select3Value){
                if(! isValidIP(i_val, true)){
                    error = WsalTranslator.sourceIpNotValid;
                    input1.addClass(_errorClass).attr('title', error);
                    return {'error':error};
                }
            }
            // 'CONTAINS'
            else if('CONTAINS' == select3Value){
                if(! isValidIP(i_val, false)){
                    error = WsalTranslator.sourceIpNotValid;
                    input1.addClass(_errorClass).attr('title', error);
                    return {'error':error};
                }
            }
            else{
                error = WsalTranslator.sourceIpNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
        }

        // POST ID, PAGE ID, CUSTOM POST ID
        else if('POST ID' == select2Value){
            ival = input1.val().trim();
            if(parseInt(ival,10) < 1){
                error = WsalTranslator.postIdNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
            else if(!/^\d+$/.test(ival)){
                error = WsalTranslator.postIdNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
        }

        else if('PAGE ID' == select2Value){
            ival = input1.val().trim();
            if(parseInt(ival,10) < 1){
                error = WsalTranslator.pageIdNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
            else if(!/^\d+$/.test(ival)){
                error = WsalTranslator.pageIdNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
        }

        else if ('CUSTOM POST ID' == select2Value){
            ival = input1.val().trim();
            if(parseInt(ival,10) < 1){
                error = WsalTranslator.customPostIdNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
            else if(!/^\d+$/.test(ival)){
                error = WsalTranslator.customPostIdNotValid;
                input1.addClass(_errorClass).attr('title', error);
                return {'error':error};
            }
        }

        return true;
    };
    // endregion VALIDATE TRIGGERS

    // region  PUBLIC
    this.addError = addError;
    this.showErrors = showErrors;
    this.clearErrors = clearErrors;
    this.addTitleForErrors = addTitleForErrors;

    /**
     * Validate the form.
     *
     * @public
     * @returns {boolean}
     */
    this.validate = function() {
        clearErrors();

        var $title = $('#'+titleID)
            ,$email = $('#'+emailID);

        var title = $title.val()
            ,emailStr = $email.val()
            , hasErrors = false;
        var emails = emailStr.split(/[;,]+/);

        // Validate title.
        if ( title.length < 1 ) {
            addError(WsalTranslator.titleMissing);
            $title.addClass(_errorClass);
            hasErrors = true;
        } else if ( title.length > self.titleRules.maxLength ) {
            addError(WsalTranslator.titleLengthError.wsalFormat(self.titleRules.maxLength));
            $title.addClass(_errorClass);
            hasErrors = true;
        } else if ( ! validateTitle( title ) ) {
            addError(WsalTranslator.titleNotValid);
            $title.addClass(_errorClass);
            hasErrors = true;
        }

        // Validate triggers.
        var triggers = $('.'+triggerClass);
        if(triggers.length){
            $.each(triggers, function(){
                var $this = $(this)
                    ,select2 = $('.js_s2', $this).next('input').val()
                    ,select3 = $('.js_s3', $this).next('input').val()
                    ,select4 = $('.js_s4', $this).next('input').val()
                    ,select5 = $('.js_s5', $this).next('input').val()
                    ,select6 = $('.js_s6', $this).next('input').val()
                    ,$input1 = $('.'+inputClass, $this);

                var result = validateTrigger($this, select2, select3, select4, $input1);
                if(result !== true) {
                    addError(result.error);
                    hasErrors = true;
                }
            });
        }
        // No triggers...at least one is required.
        else {
            hasErrors = true;
            addError(WsalTranslator.inputAtLeastOne);
        }

        // Validate Email.
        if(jQuery.isEmptyObject(emails)){
            addError(WsalTranslator.emailMissing);
            $email.addClass(_errorClass);
            hasErrors = true;
        } else {
            /* old email validation
            for (var i in emails) {
                var email = $.trim(emails[i]);
                if(! validateEmail(email)){
                    addError(WsalTranslator.emailNotValid);
                    $email.addClass(_errorClass);
                    hasErrors = true;
                }
            }
            */
        }

        if ( hasErrors ) {
            addTitleForErrors('<span style="margin-bottom: 5px; display: block;"><strong style="font-size: 13px; padding-bottom:5px;">'+WsalTranslator.errorsTitle+'</strong></span>');
            showErrors();
            return false;
        }
        // All good.
        return true;
    };
    //endregion  PUBLIC
};
