<?php
namespace Ebanx\Benjamin\Services;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Services\Http\Client;
use Ebanx\Benjamin\Services\Http\HttpService;
use Ebanx\Benjamin\Services\Adapters\ExchangeAdapter;

class Exchange extends HttpService
{
    /**
     * @param  string $localCurrency Customer's currency code
     * @param  double $siteValue     Value in shop currency
     * @return double Converted local value
     */
    public function siteToLocal($localCurrency, $siteValue = 1)
    {
        return $this->fetchRate($this->config->baseCurrency, $localCurrency) * $siteValue;
    }

    /**
     * @param  string $localCurrency Customer's currency code
     * @param  double $siteValue     Value in shop currency
     * @return double Converted local value with tax when it applies
     */
    public function siteToLocalWithTax($localCurrency, $siteValue = 1)
    {
        $tax = 0.0;

        if ($localCurrency === Currency::BRL
            && !$this->config->taxesOnMerchant) {
            $tax = Config::IOF;
        }

        return $this->siteToLocal($localCurrency, $siteValue) * (1 + $tax);
    }

    /**
     * @param  string $localCurrency Customer's currency code
     * @param  double $localValue    Value in customer's currency
     * @return double Value in shop's currency
     */
    public function localToSite($localCurrency, $localValue = 1)
    {
        return $this->fetchRate($localCurrency, $this->config->baseCurrency) * $localValue;
    }

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     *
     * @return float
     */
    public function fetchRate($fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return 1;
        }

        $fromIsGlobal = Currency::isGlobal($fromCurrency);
        $toIsGlobal = Currency::isGlobal($toCurrency);

        if (!($fromIsGlobal xor $toIsGlobal)) {
            return 0;
        }

        $adapter = new ExchangeAdapter(
            $fromCurrency,
            $toCurrency,
            $this->config
        );

        $response = $this->client->exchange($adapter->transform());

        if ($response['status'] === Client::ERROR) {
            return 0;
        }

        return $response['currency_rate']['rate'];
    }
}
