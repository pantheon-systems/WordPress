var wsalModel = wsalModelWp || {
    "info" : {
        "title" : '',
        "email" : '',
        "emailLabel" : WsalTranslator.emailText
    },
    "errors": {
        "titleMissing": '',
        "titleInvalid": '',
        "emailMissing": '',
        "emailInvalid": '',
        "triggersMissing": '',
        "triggers": {}
    },
    "buttons" : {
        "deleteButton" : WsalTranslator.deleteButtonText,
        "saveNotifButton" : WsalTranslator.saveNotifButtonText,
        "addNotifButton" : WsalTranslator.addNotifButtonText
    },
    "triggers" : [],
    "default" :
    {
        "select1" : {
            "data": ["AND","OR"],
            "selected": 0
        },
        "select2" : {
            "data": ["ALERT CODE", "DATE", "TIME", "USERNAME", "USER ROLE", "SOURCE IP", 'POST ID', 'PAGE ID', 'CUSTOM POST ID', 'SITE DOMAIN', "POST TYPE"],
            "selected": 0
        },
        "select3" : {
            "data": ["IS EQUAL", "CONTAINS", "IS AFTER", "IS BEFORE", "IS NOT"],
            "selected": 0
        },
        "input1" : "",
        "deleteButton" : WsalTranslator.deleteButtonText
    },
    "viewState": []
};
