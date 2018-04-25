GoogleMerchants.directive('styledInput', function($timeout) {
    return {
        priority: -1,
        scope: {
            'placeholder' : '=',
            'ngModel' : '='
        },

        template: '<div class="editable" contenteditable="true" ng-model="ngModel" placeholder="{{placeholder}}"></div>',
        link: function(scope, element) {

            var KEY_A = 65;
            var KEY_X = 88;
            var KEY_C = 67;
            var KEY_V = 86;
            element.bind('keydown', function(event) {
                //Disable bold (Ctrl+B, Command+b), italic (Ctrl+I, Command+I) etc.,
                // but allow select all (Ctrl+A), copy, cut, past
                if((event.ctrlKey || event.metaKey)
                    && event.which != KEY_A
                    && event.which != KEY_X
                    && event.which != KEY_C
                    && event.which != KEY_V)
                {
                    return false;
                }

                // Disable new line
                if(event.which == 13) {
                    return false;
                }
            });
        }
    };
});