GoogleMerchants.controller('mainController', ['$scope', '$timeout', '$window', '$document', '$location', 'templateService', 'exportService', 'currencyService', 'attributesService', 'wpHttp', function ($scope, $timeout, $window, $document, $location, templateService, exportService, currencyService, attributesService, wpHttp) {

    var defaultMappings = [{mapFrom : '', mapTo: ''}];

    $scope.merchantsFeedData = {

        basicInformation: {
            open: true,
            itemTitle: "productTitle",
            hasVariations: true,
            useParentTitleForVariableProducts: true,
            additionalImageLink: "productImages",
            itemDescription: "productDescription",
            itemImageLink: "useProductFeaturedImage",
            itemLink: "productLink",
            condition: 'new',
            conditionMappings: defaultMappings,
            userVariationDescriptionForVariableProducts: true,
            addVariationAttributesToProductUrl: true,
            useVariationImage: true,
            useFeaturedImageIfThereIsNoVariationImage: true,
            useParentDescirptionIfThereIsNoVariationDescirption: true
        },
        detailedInformation: {
            open: false,
            color: 'selectFromWooCommerceProductAttributes',
            size: 'selectFromWooCommerceProductAttributes',
            gender: 'selectFromWooCommerceProductAttributes',
            setTheGroupId: 'automatically',
            mappings: defaultMappings,
            ageGroup: 'selectFromWooCommerceProductAttributes',
            material: 'selectFromWooCommerceProductAttributes',
            pattern: 'selectFromWooCommerceProductAttributes',
            genderAutodetect: 'keepBlank',
            sizeSystem: '',
            adjustPrice: false,
            adjustSalePrice: false

        },
        availabilityPrice: {
            open: false,
            price: 'useProductPrice',
            salePrice: 'useProductSalePrice',
            availability: 'useWooCommerceStockValues',
            adjustPriceValue: '',
            adjustPriceType: '%',
            adjustSalePriceType: '%',
            adjustSalePriceValue: '',
            currency: null
        },
        productCategories: {
            open: false,
            productType: 'useWooCommerceProductCategories',
            productCategories: 'mapProductCategories'
        },
        uniqueIdentifiers: {
            open: false,
            identifierExists: 1
        },
        shipping: {
            dimensions: 'useWooCommerceProductValues',
            convertTo: 'cm',
            adjustPriceType: '%'
        },
        template: {
            save: false,
            name: ''
        },
        advancedAttributes: {
            adult: 'no',
            unitPricingBaseMeasureUnit: 'kg',
            excludedDestination: 'no',
            customLabel0Mappings: defaultMappings,
            customLabel1Mappings: defaultMappings,
            customLabel2Mappings: defaultMappings,
            customLabel3Mappings: defaultMappings,
            customLabel4Mappings: defaultMappings,
            energyEfficiencyClassMappings: defaultMappings,
            promotionIdMappings: defaultMappings
        }
    };

    function getParameterByName(name, url) {
        if (!url) {
            url = window.location.href;
        }
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    function setCategoriesToCustomDataIfProductsNotCategorized() {

        if (!$scope.merchantsFeedData.productCategories.cats.children.length) {
            $scope.merchantsFeedData.productCategories.productCategories = 'customValue';
        }
    }

    $scope.init = function (currencySymbol, currencyCode) {

        attributesService.setAttributes(wpae_product_attributes);

        $scope.isGoogleMerchantExport = false;

        currencyService.setCurrency(currencySymbol, currencyCode);
    };

    $scope.selectGoogleMerchantsInitially = function() {
        $scope.selectGoogleMerchants();
    };

    $scope.selectGoogleMerchants = function () {

        jQuery('.wpallexport-element-label').parent().parent().slideUp();
        $scope.isGoogleMerchantExport = true;

        var id = getParameterByName('id');

        exportService.getExport(id).then(function (exportData) {
            if (angular.isObject(exportData)) {

                exportData.template = {
                    save: false,
                    name: ''
                };


                $scope.merchantsFeedData = exportData;

                setCategoriesToCustomDataIfProductsNotCategorized();

            } else {
                wpHttp.get('categories/index').then(function (data) {

                    $scope.merchantsFeedData.productCategories.cats = data;
                    $scope.merchantsFeedData.detailedInformation.genderCats = data;
                    $scope.merchantsFeedData.detailedInformation.ageGroupCats = data;

                    setCategoriesToCustomDataIfProductsNotCategorized();

                }, function () {
                    $log.error('There was a problem loading the WordPress categories');
                });
            }
        });

        if($scope.merchantsFeedData.availabilityPrice.currency == null) {
            $scope.merchantsFeedData.availabilityPrice.currency = currencyService.getCurrencyCode();
        }
    };

    $scope.$on('googleMerchantsSelected', function (event, hasVariations) {

        $scope.selectGoogleMerchants();
        $scope.merchantsFeedData.basicInformation.hasVariations = hasVariations;
        
        // Hide "All $element" lis
        jQuery('.wpallexport-element-label').parent().parent().slideUp();

        $timeout(function () {
            $scope.isGoogleMerchantExport = true;
        });
    });

    $scope.$on('googleMerchantsDeselected', function () {

        jQuery('.wpallexport-element-label').parent().parent().slideDown();

        $timeout(function () {
            $scope.isGoogleMerchantExport = false;
        });
    });

    $scope.$on('googleMerchantsSubmitted', function (event, data) {
        $scope.merchantsFeedData.template.name = data.templateName;
        $scope.process();
    });

    $scope.$on('templateShouldBeSaved', function (event, name) {
        $scope.merchantsFeedData.template.save = true;
        $scope.merchantsFeedData.template.name = name;
    });

    $scope.$on('templateShouldNotBeSaved', function () {
        $scope.merchantsFeedData.template.save = false;
    });

    $scope.$on('selectedTemplate', function (event, templateId) {
        templateService.getTemplate(templateId).then(function (template) {
            $scope.merchantsFeedData = template.google_merchants_post_data;
        });
    });


    $scope.process = function () {

        $scope.merchantsFeedData.extraData = jQuery('#templateForm').serialize();

        var id = getParameterByName('id');

        if(id) {
            $scope.merchantsFeedData.exportId = id;
            $scope.merchantsFeedData.update = true;
        }

        exportService.saveExport($scope.merchantsFeedData).then(function (response) {

            if(response.redirect) {
                $window.location.href = response.redirect;
            } else {
                $window.location.href = 'admin.php?page=pmxe-admin-export&action=options';
            }

        });
    };
}]);