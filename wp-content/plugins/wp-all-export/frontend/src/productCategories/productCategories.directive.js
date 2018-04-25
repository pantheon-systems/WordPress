GoogleMerchants.directive('productCategories', function() {
    return {
        restrict: 'E',
        scope: {
            'productCategories' : '=information'
        },
        templateUrl: 'productCategories/productCategories.tpl.html',
        controller: 'productCategoriesController'
    };
});