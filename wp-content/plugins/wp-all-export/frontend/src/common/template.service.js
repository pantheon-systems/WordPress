GoogleMerchants.factory('templateService', ['$q', '$log', 'wpHttp', function($q, $log, wpHttp){

    var getTemplate = function(templateId) {
        var deferred = $q.defer();

        wpHttp.get('templates/get&templateId='+ templateId).then(function(data){
            deferred.resolve(data);
        }, function(msg, code){
            deferred.reject(msg,code);
            $log.error('There was a problem getting the export');
        });

        return deferred.promise;
    };

    return {
        getTemplate: getTemplate
    }
}]);