var GoogleMerchants = angular.module('GoogleMerchants', ['templates-dist','dotjem.angular.tree', 'ngSanitize', 'ngAnimate', 'ng-slide-down', 'angular-click-outside']);

GoogleMerchants.constant('BACKEND', ajaxurl+'?action=wpae_api&q=');

GoogleMerchants.filter('safe', ['$sce', function($sce) {
    return $sce.trustAsHtml;
}]);