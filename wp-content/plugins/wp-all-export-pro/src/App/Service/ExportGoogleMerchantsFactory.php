<?php

namespace Wpae\App\Service;


use Wpae\App\Feed\Feed;
use Wpae\App\Field\FieldFactory;
use Wpae\WordPress\Filters;
use XmlExportEngine;

class ExportGoogleMerchantsFactory
{
    /**
     * @return ExportGoogleMerchants
     */
    public function createService()
    {
        $wordPressFilters = new Filters();
    
        $feed = new Feed(\XmlExportEngine::$exportOptions['google_merchants_post_data']);
        $fieldFactory = new FieldFactory($wordPressFilters, $feed);
        $googleMerchantsDataProcessor = new GoogleMerchantsDataProcessor($wordPressFilters, $fieldFactory, $feed);
        $exportGoogleMerchants = new ExportGoogleMerchants($googleMerchantsDataProcessor, $wordPressFilters);

        return $exportGoogleMerchants;
    }
}