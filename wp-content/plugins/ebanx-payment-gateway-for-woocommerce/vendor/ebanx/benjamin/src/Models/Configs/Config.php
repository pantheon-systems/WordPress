<?php
namespace Ebanx\Benjamin\Models\Configs;

use Ebanx\Benjamin\Models\BaseModel;
use Ebanx\Benjamin\Models\Currency;

class Config extends BaseModel implements AddableConfig
{
    const IOF = 0.0038;

    /**
     * Live integration key.
     *
     * @var string
     */
    public $integrationKey;

    /**
     * Sandbox integration key.
     *
     * @var string
     */
    public $sandboxIntegrationKey;

    /**
     * Determines if connection should be made using the sandbox environment settings.
     *
     * @var bool
     */
    public $isSandbox = true;

    /**
     * Sets the site default currency ISO code.
     * @see Ebanx\Benjamin\Models\Currency
     *
     * @var string
     */
    public $baseCurrency = Currency::USD;

    /**
     * The URL to send notifications to.
     *
     * @var string
     */
    public $notificationUrl = null;

    /**
     * The URL the customer should be redirected to.
     *
     * @var string
     */
    public $redirectUrl = null;

    /**
     * Defines if taxes are on merchant (true) or customer (false)
     * respectively and adds it to price for latter case.
     *
     * DO NOT SET IT TO TRUE BEFORE TALKING TO YOUR EBANX REPRESENTATIVE
     * OTHERWISE YOU WILL LOSE MONEY
     *
     * @var bool
     */
    public $taxesOnMerchant = false;

    /**
     * Extra information for reports
     *
     * @var array
     */
    public $userValues = [];
}
