GoogleMerchants.directive('availabilityPrice', function() {
    return {
        restrict: 'E',
        scope: {
            'availabilityPrice' : '=information'
        },
        templateUrl: 'availabilityPrice/availabilityPrice.tpl.html',
        controller:  'availabilityPriceController'
    };
});