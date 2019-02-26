<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\Adapters\SafetyPayPaymentAdapter;

abstract class SafetyPay extends DirectGateway
{
    protected static function getEnabledCountries()
    {
        return [
            Country::ECUADOR,
            Country::PERU,
        ];
    }

    protected static function getEnabledCurrencies()
    {
        return [
            Currency::PEN,
            Currency::USD,
            Currency::EUR,
        ];
    }

    protected function getPaymentData(Payment $payment)
    {
        $adapter = new SafetyPayPaymentAdapter($payment, $this->config);
        return $adapter->transform();
    }
}
