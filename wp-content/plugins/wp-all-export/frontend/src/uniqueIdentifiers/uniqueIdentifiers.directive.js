GoogleMerchants.directive('uniqueIdentifiers', function() {
    return {
        restrict: 'E',
        scope: {
            'uniqueIdentifiers' : '=information'
        },
        templateUrl: 'uniqueIdentifiers/uniqueIdentifiers.tpl.html',
        controller:  'uniqueIdentifiersController'
    };
});