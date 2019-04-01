<?php
namespace Ebanx\Benjamin\Services\Adapters;

class SafetyPayPaymentAdapter extends PaymentAdapter
{
    protected function transformPayment()
    {
        $transformed = parent::transformPayment();
        $transformed->payment_type_code = substr_replace($this->payment->type, '-', 9, 0);

        return $transformed;
    }
}
