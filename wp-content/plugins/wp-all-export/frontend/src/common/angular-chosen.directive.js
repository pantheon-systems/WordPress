function chosen($timeout) {
    var EVENTS, scope, linker, watchCollection;

    /*
     * List of events and the alias used for binding with angularJS
     */
    EVENTS = [{
        onChange: 'change'
    }, {
        onReady: 'chosen:ready'
    }, {
        onMaxSelected: 'chosen:maxselected'
    }, {
        onShowDropdown: 'chosen:showing_dropdown'
    }, {
        onHideDropdown: 'chosen:hiding_dropdown'
    }, {
        onNoResult: 'chosen:no_results'
    }];

    /*
     * Items to be added in the scope of the directive
     */
    scope = {
        options: '=', // the options array
        ngModel: '=', // the model to bind to,,
        ngDisabled: '='
    };

    /*
     * initialize the list of items
     * to watch to trigger the chosen:updated event
     */
    watchCollection = [];
    Object.keys(scope).forEach(function (scopeName) {
        watchCollection.push(scopeName);
    });

    /*
     * Add the list of event handler of the chosen
     * in the scope.
     */
    EVENTS.forEach(function (event) {
        var eventNameAlias = Object.keys(event)[0];
        scope[eventNameAlias] = '=';
    });

    /* Linker for the directive */
    linker = function ($scope, iElm, iAttr) {
        var maxSelection = parseInt(iAttr.maxSelection, 10),
            searchThreshold = parseInt(iAttr.searchThreshold, 10);

        if (isNaN(maxSelection) || maxSelection === Infinity) {
            maxSelection = undefined;
        }

        if (isNaN(searchThreshold) || searchThreshold === Infinity) {
            searchThreshold = undefined;
        }

        var allowSingleDeselect = iElm.attr('allow-single-deselect') !== undefined ? true : false;
        var noResultsText = iElm.attr('no-results-text') !== undefined ? iAttr.noResultsText : "No results found.";

        iElm.chosen({
            width: '100%',
            max_selected_options: maxSelection,
            disable_search_threshold: searchThreshold,
            search_contains: true,
            allow_single_deselect: allowSingleDeselect,
            no_results_text: noResultsText
        });

        iElm.on('change', function () {
            iElm.trigger('chosen:updated');
        });

        $scope.$watchGroup(watchCollection, function () {
            $timeout(function () {
                iElm.trigger('chosen:updated');
            }, 100);
        });

        $scope.$on('chosen:updated', function() {
           iElm.trigger('chosen:updated');
        });

        // assign event handlers
        EVENTS.forEach(function (event) {
            var eventNameAlias = Object.keys(event)[0];

            if (typeof $scope[eventNameAlias] === 'function') { // check if the handler is a function
                iElm.on(event[eventNameAlias], function (event) {
                    $scope.$apply(function () {
                        $scope[eventNameAlias](event);
                    });
                }); // listen to the event triggered by chosen
            }
        });
    };

    // return the directive
    return {
        name: 'chosen',
        scope: scope,
        restrict: 'A',
        link: linker
    };
}

GoogleMerchants.directive('chosen', ['$timeout', chosen]);