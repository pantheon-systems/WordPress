GoogleMerchants.directive('detailedInformation', function() {
    return {
        restrict: 'E',
        scope: {
            'detailedInformation' : '=information'
        },
        templateUrl: 'detailedInformation/detailedInformation.tpl.html',
        controller: 'detailedInformationController'
    };
});