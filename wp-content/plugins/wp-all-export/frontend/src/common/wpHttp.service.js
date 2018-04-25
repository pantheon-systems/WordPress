GoogleMerchants.factory('wpHttp', ['$http', '$q', '$log', 'BACKEND', 'NONCE', function($http, $q, $log, BACKEND, NONCE){

    var post = function(url, data) {

        var deferred = $q.defer();
        $http.post(BACKEND + url + '&security=' + NONCE, data).then(function(response){
            deferred.resolve(response.data);
        }, function(msg, code){
            deferred.reject(msg,code);
        });

        return deferred.promise;
    };

    var get = function(url) {

        var deferred = $q.defer();
        $http.get(BACKEND + url + '&security=' + NONCE).then(function(response){
            deferred.resolve(response.data);
        }, function(msg, code){
            deferred.reject(msg,code);
        });

        return deferred.promise;
    };

    return {
        post: post,
        get: get
    }
}]);