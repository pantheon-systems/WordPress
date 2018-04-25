GoogleMerchants.directive('autodetect', ['attributesService', function(attributesService) {
    return {
        restrict: 'A',
        require: '^ngModel',
        link: {
            post: function (scope, element, attributes, ngModelCtrl) {

                var autodetectValue = attributes.autodetect;

                attributes = attributesService.getAttributes();

                angular.forEach(attributes, function (attribute) {
                    if (attribute.label.toLowerCase() == autodetectValue.toLowerCase() || attribute.name.toLowerCase() == autodetectValue.toLowerCase()) {
                        ngModelCtrl.$setViewValue('{' + attribute.name + '}');
                        ngModelCtrl.$render();
                    }
                })
            }
        }
    };
}]);