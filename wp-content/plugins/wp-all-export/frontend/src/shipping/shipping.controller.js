GoogleMerchants.controller('shippingController', ['$scope', 'currencyService', function($scope, currencyService){

    $scope.currency = currencyService.getCurrency();

}]);