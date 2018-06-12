GoogleMerchants.directive('basicInformation', function() {
    return {
        restrict: 'E',
        scope: {
            'basicInformation' : '=information'
        },
        templateUrl: 'basicInformation/basicInformation.tpl.html',
        controller: 'basicInformationController',
    };
});