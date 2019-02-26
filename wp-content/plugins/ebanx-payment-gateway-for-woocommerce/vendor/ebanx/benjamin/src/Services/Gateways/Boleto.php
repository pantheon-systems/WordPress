<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\Adapters\BoletoPaymentAdapter;
use Ebanx\Benjamin\Services\Traits\Printable;

class Boleto extends DirectGateway
{
    use Printable;

    const API_TYPE = 'boleto';

    protected static function getEnabledCountries()
    {
        return [Country::BRAZIL];
    }

    protected static function getEnabledCurrencies()
    {
        return [
            Currency::BRL,
            Currency::USD,
            Currency::EUR,
        ];
    }

    /**
     * @return string
     */
    protected function getUrlFormat()
    {
        return 'https://%s.ebanxpay.com/print/?hash=%s';
    }

    /**
     * @param Payment $payment
     * @return object
     */
    protected function getPaymentData(Payment $payment)
    {
        $adapter = new BoletoPaymentAdapter($payment, $this->config);
        return $adapter->transform();
    }
}
