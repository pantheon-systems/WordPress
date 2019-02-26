<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\Adapters\EftPaymentAdapter;

class Eft extends DirectGateway
{
    const API_TYPE = 'eft';

    protected static function getEnabledCountries()
    {
        return [Country::COLOMBIA];
    }

    protected static function getEnabledCurrencies()
    {
        return [
            Currency::COP,
            Currency::USD,
            Currency::EUR,
        ];
    }

    protected function getPaymentData(Payment $payment)
    {
        $adapter = new EftPaymentAdapter($payment, $this->config);
        return $adapter->transform();
    }
}
