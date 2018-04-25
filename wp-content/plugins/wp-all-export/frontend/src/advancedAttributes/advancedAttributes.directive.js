GoogleMerchants.directive('advancedAttributes', function() {
    return {
        restrict: 'E',
        scope: {
            'advancedAttributes' : '=information'
        },
        templateUrl: 'advancedAttributes/advancedAttributes.tpl.html',
        controller: 'advancedAttributesController'
    };
});