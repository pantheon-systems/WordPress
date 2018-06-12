GoogleMerchants.directive('googleCategorySelectorAdder', [function() {
    return {
        restrict: 'E',
        scope: {
            'selectedCategory': '=',
            'selectedCategoryId' : '='
        },
        controller: ['$scope', function($scope){

            $scope.getPlaceholder = function() {

                if($scope.visible) {
                    return ''; }
                else {
                    return 'Select Google Product Category';
                }
            };

        }],
        templateUrl: 'productCategories/googleCategorySelector/googleCategorySelectorAdder.tpl.html'
    };
}]);