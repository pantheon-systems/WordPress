GoogleMerchants.directive('shipping', function() {
    return {
        restrict: 'E',
        scope: {
            'shipping' : '=information'
        },
        templateUrl: 'shipping/shipping.tpl.html',
        controller: 'shippingController'
    };
});