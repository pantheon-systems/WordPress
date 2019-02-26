<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\Adapters\CardPaymentAdapter;

class DebitCard extends DirectGateway
{
    const API_TYPE = 'debitcard';

    protected static function getEnabledCountries()
    {
        return [
            Country::MEXICO,
        ];
    }
    protected static function getEnabledCurrencies()
    {
        return [
            Currency::MXN,
            Currency::USD,
            Currency::EUR,
        ];
    }

    protected function getPaymentData(Payment $payment)
    {
        $payment->card->type = self::API_TYPE;

        $adapter = new CardPaymentAdapter($payment, $this->config);
        return $adapter->transform();
    }
}
