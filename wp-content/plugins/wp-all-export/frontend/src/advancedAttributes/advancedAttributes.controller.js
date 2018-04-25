GoogleMerchants.controller('advancedAttributesController', ['$scope', '$log', 'attributesService', function($scope, $log, attributesService){

    $scope.attributes = [];

    $scope.cats = [];

    $scope.attributes = attributesService.getAttributes();

}]);