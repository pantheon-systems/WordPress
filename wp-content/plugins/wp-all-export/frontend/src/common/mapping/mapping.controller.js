GoogleMerchants.controller('mappingController', ['$scope', function($scope){

    $scope.show = false;

    $scope.mappingsBackup = null;

    $scope.removeMapping = function(mapping) {

        if($scope.mappings.length > 1) {
            $scope.mappings.splice($scope.mappings.indexOf(mapping), 1);
        }
    };

    $scope.$watch('show', function(newValue){
        // If we show it, backup the current mappings in case cancel is pressed
        if(newValue) {
            $scope.mappingsBackup = $scope.mappings;
        }
    });
    $scope.addMapping = function() {
        $scope.mappings.push({});
    };

    $scope.close = function() {
        // Restore mappings
        $scope.mappings = $scope.mappingsBackup;
        $scope.show = false;
    };

    $scope.saveMappings = function() {
        $scope.show = false;
    };

}]);