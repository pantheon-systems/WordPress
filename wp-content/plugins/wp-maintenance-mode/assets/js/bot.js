var DEBUG = false;

// User info retrieved from chats is store in this object
var context = {};

// Cache conversation position
var conversationPos;

/*
---------------
Utility Functions
---------------
*/

function getCurrentTime() {
  var date = new Date();
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12;
  hours = hours ? hours : 12;
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ' ' + ampm;
  return strTime;
}

function renderStatement(statement) {
    jQuery('.chat-container').append('<div class="chat-message-wrapper"><div class="absolute-wrapper"><div class="message-details"><div class="bot-avatar"></div><span class="message-date">' + getCurrentTime() + '</span></div></div><p class="chat-message">' + statement + '</p></div>');
}

function showTyping() {
    jQuery('.chat-container').append('<div class="typing-wrapper"><div class="bot-avatar"></div><span class="bot-name">' + botName +'</span><p class="chat-message typing"><span class="dot"></span><span class="dot"></span><span class="dot"></span></p></div>');
}

function hideTyping() {
    jQuery('.typing-wrapper').remove();
}

var chatWrapper = jQuery('.bot-chat-wrapper');

function scrollToBottom() {
    chatWrapper.animate({
      scrollTop: 600
    }, "slow");
}

/**
 * Input checking
 */
function inputError(msg) {
    jQuery('.bot-error p').text(msg);
    jQuery('.bot-error')
    .animate({bottom: 0}, 500)
    .delay(3000)
    .animate({bottom: "-70px"}, 500);
}

function checkInput(option) {

    let input = jQuery('.bot-container input[type=text]');
    if (input.val().length > 2) {
        showResponse(option);
    } else {
        inputError(botVars.validationName);
    }
    return false;

}


function checkEmail(option) {

    let input = jQuery('.bot-container input[type=email]').val();
    let regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    let result = regex.test(String(input.toLowerCase()) );

    if (input.length > 7 && result === true) {

        // Add new entry to our db
        var bot_user_email = jQuery('.bot-container input[type=email]').serialize();
        var subscribe_bot_data = 'action=wpmm_add_subscriber&' + bot_user_email;

        jQuery.post(wpmm_vars.ajax_url, subscribe_bot_data, function(response) {
            if (!response.success) {
                // console.log(subscribe_bot_data);
                alert(response.data);
                return false;
            }
        }, 'json');

        showResponse(option);

    } else {
        inputError(botVars.validationEmail);
    }
    return false;
}

function clearChat() {
    jQuery('.chat-container').empty();
}

function clearFooter() {
    jQuery('.choices').empty();
    jQuery('.input').empty();
}

/*
------------------------
Setup Conversation Data
------------------------
*/

function startConversation(conv, pos) {

    clearFooter();
    clearChat();

    // Set conversation position
    // 'conversation' is in the global scope
    conversationPos = conv;

    // Load conversation data
    jQuery.getScript( botVars.uploadsBaseUrl + "data.js", function( data ) {
    // Show first bot statement
    showStatement(pos);

    });

}

/*
-------------------
Show Bot Statement
-------------------
*/
function showStatement(pos) {

    // Where are we in conversationData?
    var node = conversationData[conversationPos][pos];

    // If there is a side effect execute that within the context
    if ('sideeffect' in node && jQuery.type(node['sideeffect'] === "function")) {
        node['sideeffect'](context);
    }

    // Wrap the statements in an array (if they're not already)
    var statements;
    if (jQuery.type(node['statement']) === "array") {
        statements = node['statement'];
    } else if (jQuery.type(node['statement']) === "string") {
        statements = [node['statement']];
    } else if (jQuery.type(node['statement']) === "function") {
        statements = node['statement'](context);
    }

    /*
    ------------------------
    Render Bot Statement(s)
    ------------------------
    Run this function over each statement
    */
    async.eachSeries(statements, function(item, callback) {

        // Emulate typing then scroll to bottom
        showTyping();
        scrollToBottom();

        // Create random delay
        // If statement is short, delay 1.8 seconds
        // Else, random delay based on statement length
        if (item.length <= 50) {
            var delay = 1800;
        } else {
            var delay = (item.length / 3) * 30 * (Math.floor(Math.random() * 5) + 1.2);
        }

        if (DEBUG) { delay = 0; }


        setTimeout(function() {
            hideTyping();
            renderStatement(item);
            scrollToBottom();

            callback();
        }, delay);
    },

    /*
    ----------------------
    Render User Option(s)
    ----------------------
    This is the final callback of the series
    */
    function (err) {

        /*
        ----------------------------
        If User Option is Button(s)
        ----------------------------
        */
        if ('options' in node) {
            jQuery('.input').hide();
            jQuery('.choices').show();

            // Get the options' data
            var options = node["options"];

            // If there are options render them
            // Otherwise this is the end
            if (options.length > 0) {

                // Pause 750ms, then render options
                setTimeout(function() {

                    for (var i = 0; i < options.length; i++) {
                        var option = options[i];
                        var extraClass;
                        var clickFunction;

                        // Check option for a consequence
                        if (option['consequence'] === null) {

                            // The consequence is null meaning this is a branch we won't be exploring
                            // The button is given class 'disabled' and does nothing on click
                            clickFunction = null;
                            extraClass = "disabled";

                        } else {

                            // Else, click function (showResponse) is binded to it
                            clickFunction = function(option) {
                                showResponse(option);
                            }.bind(null, option);

                            extraClass = "";

                        }

                        // Render button
                        var button = jQuery('<p/>', {
                            text: option['choice'],
                            "class": "chat-message user",
                            click: clickFunction
                        }).appendTo('.choices');
                    }

                }, 750);

            }

        /*
        ------------------------
        If User Option is Input
        ------------------------
        */
        } else if ('input' in node) {
            jQuery('.input').show();
            jQuery('.choices').hide();

            var option = node['input'];


            /*
            Render Input
            ---------------
            */

            // Create a form to hold our input and submit button
            var form = jQuery('<form/>', {
                submit: checkInput.bind(null, option)
            });

            // Create a user bubble, append to form
            var inputBubble = jQuery('<p/>', {
                "class": "chat-message user"
            }).appendTo(form);

            // Create an input, append to user bubble
            var input = jQuery('<input/>', {
                type: 'text',
                placeholder: botVars.typeName,
                name: option['name'],
                autocomplete: 'off',
                required: true
            }).appendTo(inputBubble);

            // Create an input button, append to user bubble
            var button = jQuery('<a/>', {
                text: 'Send',
                click: checkInput.bind(null, option)
            }).appendTo(inputBubble);

            // Append form to div.input
            form.appendTo('.input');

            // Focus on the input we just put into the DOM
            async.nextTick(function() {
                input.focus();
            });

        /*
        ------------------------
        If User Option is Email
        ------------------------
        */
        } else if ('email' in node) {
            jQuery('.input').show();
            jQuery('.choices').hide();

            var option = node['email'];

            /*
            Render Input
            ---------------
            */

            // Create a form to hold our input and submit button
            var form = jQuery('<form/>', {
                "class": "bot_subscribe_form",
                submit: checkEmail.bind(null, option)
            });

            // Create a user bubble, append to form
            var inputBubble = jQuery('<p/>', {
                "class": "chat-message user"
            }).appendTo(form);

            // Create email input, append to user bubble
            var input = jQuery('<input/>', {
                type: 'email',
                placeholder: botVars.typeEmail,
                name: option['email'],
                autocomplete: 'off'
            }).appendTo(inputBubble);

            // Create an input button, append to user bubble
            var button = jQuery('<a/>', {
                text: botVars.send,
                // "class": "user-email-trigger",
                click: checkEmail.bind(null, option)
            }).appendTo(inputBubble);

            // Append form to div.input
            form.appendTo('.input');

            // Focus on the input we just put into the DOM
            async.nextTick(function() {
                input.focus();
            });

        }

        scrollToBottom();
    });
}

/*
---------------------
Render User Response
---------------------
*/
function showResponse(option) {

    // If there was an input element, put that into the global context
    var feedback = "";

    if ('name' in option) {
        context[option['name']] = jQuery('.bot-container input[type=text]').val();
        feedback = context[option['name']];
    }
    else if('email' in option) {
        context[option['email']] = jQuery('.bot-container input[type=email]').val();
        feedback = context[option['email']];
    }
    else {
        feedback = option['choice'];
    }

    clearFooter();

    // Show what the user chose
    jQuery(".chat-container").append('<p class="chat-message user">' + feedback + '</p>');

    if ('consequence' in option) {
        showStatement(option['consequence']);
    } else {
      // xxx
    }
}