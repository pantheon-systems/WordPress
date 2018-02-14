<?php

namespace Wpae\App\Tests\Service;


use Wpae\App\Service\ExportGoogleMerchants;
use Wpae\App\Service\ExportGoogleMerchantsFactory;

class ExportGoogleMerchantsFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanCreateExportGoogleMerchants()
    {
        $xmlExportEngine = $this->getMock('XmlExportEngine');
        $xmlExportEngine::$exportOptions = [];
        $exportGoogleMerchantsFactory = new ExportGoogleMerchantsFactory();

        $exportGoogleMerchants = $exportGoogleMerchantsFactory->createService();

        $this->assertInstanceOf(ExportGoogleMerchants::class, $exportGoogleMerchants);
    }
}