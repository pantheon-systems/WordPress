<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Models\Configs\CreditCardConfig;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Responses\PaymentTerm;
use Ebanx\Benjamin\Services\Adapters\CaptureAdapter;
use Ebanx\Benjamin\Services\Adapters\CardPaymentAdapter;
use Ebanx\Benjamin\Services\Exchange;
use Ebanx\Benjamin\Services\Http\Client;

class CreditCard extends DirectGateway
{
    const API_TYPE = 'creditcard';

    protected static function getEnabledCountries()
    {
        return [
            Country::BRAZIL,
            Country::MEXICO,
            Country::COLOMBIA,
            Country::ARGENTINA,
        ];
    }

    protected static function getEnabledCurrencies()
    {
        return [
            Currency::BRL,
            Currency::MXN,
            Currency::COP,
            Currency::ARS,
            Currency::USD,
            Currency::EUR,
        ];
    }

    public static $instalmentIncrementCountry = [
        Country::BRAZIL => 1,
        Country::COLOMBIA => 1,
        Country::MEXICO => 3,
        Country::ARGENTINA => 3,
    ];

    public static $maxInstalmentCountry = [
        Country::BRAZIL   => 12,
        Country::COLOMBIA => 36,
        Country::MEXICO   => 12,
        Country::ARGENTINA => 12,
    ];

    private $creditCardConfig;

    private $interestRates;

    public function __construct(Config $config, CreditCardConfig $creditCardConfig, Client $client = null)
    {
        $this->creditCardConfig = $creditCardConfig;
        parent::__construct($config, $client);
    }

    protected function getPaymentData(Payment $payment)
    {
        $this->availableForCountryOrThrow($payment->address->country);

        $payment->type = $payment->card->type;

        $adapter = new CardPaymentAdapter($payment, $this->config);
        return $adapter->transform();
    }

    public function getMinInstalmentValueForCountry($country)
    {
        $this->availableForCountryOrThrow($country);

        $localCurrency = Currency::localForCountry($country);
        $acquirerMinimum = CreditCardConfig::acquirerMinInstalmentValueForCurrency($localCurrency);
        $configMinimum = $this->creditCardConfig->minInstalmentAmount;

        return max($acquirerMinimum, $configMinimum);
    }

    public function getPaymentTermsForCountryAndValue($country, $value)
    {
        $this->availableForCountryOrThrow($country);

        $paymentTerms = [];

        $localCurrency = Currency::localForCountry($country);
        $localValueWithTax = $this->exchange->siteToLocalWithTax($localCurrency, $value);
        $minInstalment = $this->getMinInstalmentValueForCountry($country);

        // HARD LIMIT
        $instalmentsByCountry = $this->getInstalmentsByCountry($country);

        foreach ($instalmentsByCountry as $instalment) {
            $paymentTerms[] = $this->calculatePaymentTerm($instalment, $value, $localValueWithTax, $minInstalment);
        }

        return array_filter($paymentTerms);
    }

    /**
     * @param string $hash
     * @param float  $amount
     * @param string $merchantCaptureCode
     * @return array
     */
    public function captureByHash($hash, $amount = null, $merchantCaptureCode = null)
    {
        $data = [
            'hash' => $hash,
            'amount' => $amount,
            'merchantCaptureCode' => $merchantCaptureCode,
        ];

        $adapter = new CaptureAdapter($data, $this->config);
        $response = $this->client->capture($adapter->transform());

        return $response;
    }

    /**
     * @param string $merchantPaymentCode
     * @param float  $amount
     * @param string $merchantCaptureCode
     * @return array
     */
    public function captureByMerchantPaymentCode($merchantPaymentCode, $amount = null, $merchantCaptureCode = null)
    {
        $data = [
            'merchantPaymentCode' => $merchantPaymentCode,
            'amount' => $amount,
            'merchantCaptureCode' => $merchantCaptureCode,
        ];

        $adapter = new CaptureAdapter($data, $this->config);
        $response = $this->client->capture($adapter->transform());

        return $response;
    }

    private function calculatePaymentTerm($instalment, $siteValue, $localValueWithTax, $minimum)
    {
        $interestRates = $this->getInterestRates();

        $interestRatio = 1 + (isset($interestRates[$instalment]) ? $interestRates[$instalment] / 100 : 0);

        if ($instalment !== 1
            && $localValueWithTax / $instalment * $interestRatio < $minimum) {
            return null;
        }

        return new PaymentTerm([
            'instalmentNumber' => $instalment,
            'baseAmount' => ($siteValue / $instalment) * $interestRatio,
            'localAmountWithTax' => ($localValueWithTax / $instalment) * $interestRatio,
            'hasInterests' => $interestRatio > 1
        ]);
    }

    private function getInterestRates()
    {
        if ($this->interestRates) {
            return $this->interestRates;
        }

        $this->interestRates = [];
        foreach ($this->creditCardConfig->interestRates as $item) {
            $this->interestRates[$item->instalmentNumber] = $item->interestRate;
        }

        return $this->interestRates;
    }

    /**
     * @param $country
     * @return array
     */
    public function getInstalmentsByCountry($country)
    {
        $instalments = [1 => 1];
        $maxInstalments = min(self::$maxInstalmentCountry[$country], $this->creditCardConfig->maxInstalments);

        if ($maxInstalments <= 1) {
            return $instalments;
        }

        if ($country !== Country::MEXICO) {
            $instalments[2] = 2;
        }

        for ($i = 3; $i <= $maxInstalments; $i += self::$instalmentIncrementCountry[$country]) {
            $instalments[$i] = $i;
        }

        return $instalments;
    }
}
