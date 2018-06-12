GoogleMerchants.directive('cascade', [function() {
    return {
        restrict: 'A',
        controller: ['$scope', function($scope) {

            var uniqueId = function() {
                return 'id-' + Math.random().toString(36).substr(2, 16);
            };

            $scope.$on('wpae.gender.changed', function(event, scopeId, selectedGender){
                if($scope.$id != scopeId) {
                    $scope.node.selectedGender = selectedGender;
                }
            });

            $scope.$on('wpae.ageGroup.changed', function(event, scopeId, selectedAgeGroup){
                if($scope.$id != scopeId) {
                    $scope.node.selectedAgeGroup = selectedAgeGroup;
                }
            });

            $scope.selectGender = function() {
                $scope.$parent.$broadcast('wpae.gender.changed', $scope.$id, $scope.node.selectedGender);
            };

            $scope.selectAgeGroup = function() {
                $scope.$parent.$broadcast('wpae.ageGroup.changed', $scope.$id, $scope.node.selectedAgeGroup);
            };

        }],
        link: function (scope, element, attributes) {
            scope.cascadeName = attributes.$attr.cascade;
        }
    };
}]);