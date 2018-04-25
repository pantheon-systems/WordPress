GoogleMerchants.directive('mapping', function() {
    return {
        restrict: 'E',
        scope: {
            'mappings' : '=',
            'show' : '=',
            'context' : '='
        },
        templateUrl: 'common/mapping/mapping.tpl.html',
        controller: 'mappingController'
    };
});