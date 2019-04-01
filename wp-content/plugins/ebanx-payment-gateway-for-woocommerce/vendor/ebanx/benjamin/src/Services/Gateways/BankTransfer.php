<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Country;
use Ebanx\Benjamin\Models\Currency;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Services\Adapters\BankTransferPaymentAdapter;
use Ebanx\Benjamin\Services\Traits\Printable;

class BankTransfer extends DirectGateway
{
    use Printable;

    const API_TYPE = 'banktransfer';

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
        return 'https://%s.ebanxpay.com/print/voucher/execute?hash=%s';
    }

    /**
     * @param Payment $payment
     * @return object
     */
    protected function getPaymentData(Payment $payment)
    {
        $adapter = new BankTransferPaymentAdapter($payment, $this->config);
        return $adapter->transform();
    }
}
