GoogleMerchants.directive('focusMeWhenEnabled', function($timeout) {
    return {
        priority: -1,
        link: function(scope, element) {
            scope.$watch(function() {
                return scope.$eval(element.attr('ng-disabled')); //this will evaluate attribute value `{{}}``
            }, function(newValue){
                if(newValue == false) {
                    $timeout(function(){
                        element[0].focus();
                    });
                }
            });
        }
    };
});