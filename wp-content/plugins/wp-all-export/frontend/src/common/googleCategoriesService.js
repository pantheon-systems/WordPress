GoogleMerchants.factory('googleCategoriesService', ['$rootScope', '$q', '$log', 'wpHttp', function($rootScope, $q, $log, wpHttp){

    var searchCategories = function(searchQuery) {
        return wpHttp.get('googleCategories/get&parent=0' + searchQuery);
    };

    var getChildCategories = function(parentId) {
        return wpHttp.get('googleCategories/get&parent=' + parentId);
    };

    var categorySelected = function(category) {
        $rootScope.$broadcast('wpae.category.selected', category);
    };

    return {
        searchCategories: searchCategories,
        getChildCategories: getChildCategories,
        categorySelected: categorySelected
    }
}]);