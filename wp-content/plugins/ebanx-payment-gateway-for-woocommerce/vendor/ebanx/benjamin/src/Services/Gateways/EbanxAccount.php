<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Bank;
use Ebanx\Benjamin\Models\Payment;
use Ebanx\Benjamin\Models\Currency;

class EbanxAccount extends Tef
{
    const API_TYPE = 'ebanxaccount';

    protected function getPaymentData(Payment $payment)
    {
        $payment->type = parent::API_TYPE;
        $payment->bankCode = Bank::EBANX_ACCOUNT;

        return parent::getPaymentData($payment);
    }

    protected static function getEnabledCurrencies()
    {
        return [
            Currency::USD,
        ];
    }
}
