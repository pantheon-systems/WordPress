GoogleMerchants.controller('categoryMapperController', ['$scope', '$log', 'wpHttp', function($scope, $log, wpHttp){

    $scope.dialogVisible = true;

    $scope.selectedCategory = '';

    $scope.selectedCategoryId = 0;

    $scope.parentWidth = false;

    $scope.siteCats = [];

    // Context can be: 'categories', 'gender', 'ageGroup'
    if (angular.isUndefined($scope.context)){
        $scope.context = 'categories';
    }
    
    $scope.expandNode = function(node) {
        if(node.children.length) {
            node.expanded = !node.expanded;
        }
    };

    $scope.getTimes= function(num) {
        return new Array(num);
    };

    $scope.toggleDialog = function() {
        $scope.dialogVisible = !$scope.dialogVisible;
    };
        
}]);