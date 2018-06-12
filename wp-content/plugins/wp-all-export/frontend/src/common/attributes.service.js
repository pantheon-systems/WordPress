GoogleMerchants.factory('attributesService', ['$rootScope', '$q', '$log', 'wpHttp', function($rootScope, $q, $log, wpHttp){

    var attributes = false;

    var setAttributes = function(productAttributes) {
        attributes = productAttributes;
    };

    var getAttributes = function() {
       return attributes;
    };

    return {
        setAttributes: setAttributes,
        getAttributes: getAttributes
    }
}]);