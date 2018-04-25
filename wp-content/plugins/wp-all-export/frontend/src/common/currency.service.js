GoogleMerchants.factory('currencyService', [function(){

    var internalCurrency = null;
    var internalCurrencyCode = null;

    return {
        setCurrency: function(currencySymbol, currencyCode) {
            internalCurrency = currencySymbol;
            internalCurrencyCode = currencyCode;
        },
        getCurrency: function() {
            return internalCurrency;
        },
        getCurrencyCode: function() {
            return internalCurrencyCode;
        }
    }
}]);