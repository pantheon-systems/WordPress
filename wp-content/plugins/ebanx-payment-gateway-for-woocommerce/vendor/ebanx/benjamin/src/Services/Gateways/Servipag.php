<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\Adapters\EftPaymentAdapter;

class Servipag extends DirectGateway
{
    const API_TYPE = 'servipag';

    protected static function getEnabledCountries()
    {
        return [Country::CHILE];
    }

    protected static function getEnabledCurrencies()
    {
        return [
            Currency::CLP,
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
