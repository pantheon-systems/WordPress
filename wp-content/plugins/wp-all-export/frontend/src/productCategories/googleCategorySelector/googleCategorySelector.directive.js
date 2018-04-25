GoogleMerchants.directive('googleCategorySelector', ['$rootScope', function($rootScope) {
    return {
        restrict: 'E',
        templateUrl: 'productCategories/googleCategorySelector/googleCategorySelector.tpl.html',
        controller: 'googleCategorySelectorController',
        link: function(scope, element) {
        }
    };
}]);