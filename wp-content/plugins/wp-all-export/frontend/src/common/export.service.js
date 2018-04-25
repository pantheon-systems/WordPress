GoogleMerchants.factory('exportService', ['$q', '$log', 'wpHttp', function($q, $log, wpHttp){

    var getExport = function(id) {

        var deferred = $q.defer();

        var query = 'export/get';

        if(id !== null) {
            query = query + '&id='+id;
        }
        wpHttp.get(query).then(function(data){
            deferred.resolve(data);
        }, function(msg, code){
            deferred.reject(msg,code);
            $log.error('There was a problem getting the export');
        });

        return deferred.promise;
    };

    var saveExport = function(exportData) {
        var deferred = $q.defer();

        var url = 'export/save';

        wpHttp.post(url , exportData).then(function(data){
            deferred.resolve(data);
        },function(msg,code){
            deferred.reject(msg);
            $log.error(msg,code);
        });

        return deferred.promise;
    };

    return {
        getExport: getExport,
        saveExport: saveExport
    }
}]);