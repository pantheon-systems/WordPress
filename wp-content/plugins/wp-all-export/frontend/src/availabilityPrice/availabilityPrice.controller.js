GoogleMerchants.controller('availabilityPriceController', ['$scope', 'currencyService', function($scope, currencyService){
    
    $scope.currency = currencyService.getCurrency();

}]);