GoogleMerchants.directive('categoryMapper', function() {
    return {
        restrict: 'E',
        scope: {
            'cats': '=',
            'mapping': '=',
            'grey' : '=',
            'context' : '@?'
        },
        templateUrl: 'productCategories/categoryMapper/categoryMapper.tpl.html',
        controller: 'categoryMapperController'
    };
});