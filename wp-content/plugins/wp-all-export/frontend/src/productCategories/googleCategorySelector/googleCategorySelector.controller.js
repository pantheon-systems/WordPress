GoogleMerchants.controller('googleCategorySelectorController', ['$scope', '$log', '$window', 'googleCategoriesService', function($scope, $log, $window, googleCategoriesService){

    var categoriesBackup = [];

    $scope.categories = [];

    $scope.level = 1;

    $scope.search = '';

    $scope.loading = false;

    $scope.hasResults = true;

    $scope.byUser = false;

    var selectCategory = function(category, byUser) {
        $scope.selectedCategory = category.name
            .replace('<strong>','')
            .replace('</strong>','')
            .replace('<b>','')
            .replace('</b>','');
        $scope.selectedCategoryId = category.id;
        $scope.byUser = byUser;
        $scope.visible = false;
    };

    $scope.loadCategories = function(search) {

        $scope.loading = true;
        var searchQuery = '';

        if(search) {
            searchQuery = '&search=' + search;
        }

        googleCategoriesService.searchCategories(searchQuery).then(function(data) {
            $scope.categories = data;
        }).finally(function() {
            $scope.loading = false;
        });
    };

    $scope.expand = function(category) {

        if(category.opened) {
            category.opened = false;
            return;
        }

        $scope.loading = true;

        googleCategoriesService.getChildCategories(category.id)
            .then(function(data) {
                if(data != 'null') {
                    category.children = data;
                    category.opened = true;
                }
            }, function() {
                $log.error('There was a problem loading the categories');
            }).finally(function() {
                $scope.loading = false;
            });
    };

    $scope.select = function(category) {
        category.scopeId = $scope.$id;
        $scope.$parent.$parent.$broadcast('wpae.parentCategorySelected', category);
        selectCategory(category, true);
    };

    $scope.matchSearch = function(criteria) {
        return function(item) {
            return item.name === criteria.name;
        };
    };

    $scope.$on('wpae.parentCategorySelected', function(event, category) {
        // Only select category if we are in a child scope, because we will get the event in the
        // triggering scope also
        if(!$scope.byUser && $scope.$id != category.scopeId) {
            selectCategory(category, false);
        }
    });

    $scope.$watch('selectedCategory', function(newValue, oldValue){
        //TODO: Remove this watcher if not neeeded
        // We should do the search here and remove from below
    });

    $scope.$watch('search', function(newValue, oldValue) {
        // Keep the old state
        if(oldValue == '') {
            categoriesBackup = $scope.categories;
        }
        // Reload the old state
        if(newValue == '') {
            $scope.categories = categoriesBackup;
            return;
        }
        $scope.loadCategories(newValue);
    });

    $scope.categoryChanged = function() {
        // We are in search mode
        $scope.loadCategories($scope.selectedCategory);
    };

    $scope.categoryClicked = function() {

        var selectedCategoryBack = $scope.selectedCategory;
        if(!$scope.visible)  {
            $scope.visible = true;
        }

        if(!$scope.byUser) {
            $scope.selectedCategory = '';
        }
    };

    $scope.closeMe = function() {
        if($scope.visible) {
            $scope.visible = false;
        }
    };

}]);