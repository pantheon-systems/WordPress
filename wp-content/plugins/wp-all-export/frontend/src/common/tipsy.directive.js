GoogleMerchants.directive('tipsy', ['$document', function($document) {
    return {
        restrict: 'A',
        link: function (scope, element, attributes) {
            element.attr('original-title', attributes.tipsy);
            element.tipsy({
                gravity: function() {
                    var ver = 'n';
                    if ($document.scrollTop() < element.offset().top - angular.element('.tipsy').height() - 2) {
                        ver = 's';
                    }
                    var hor = '';
                    if (element.offset().left + angular.element('.tipsy').width() < $document.width() + $document.scrollLeft()) {
                        hor = 'w';
                    } else if (element.offset().left - angular.element('.tipsy').width() > $document.scrollLeft()) {
                        hor = 'e';
                    }
                    return ver + hor;
                },
                live: true,
                html: true,
                opacity: 1
            });
        }
    };
}]);