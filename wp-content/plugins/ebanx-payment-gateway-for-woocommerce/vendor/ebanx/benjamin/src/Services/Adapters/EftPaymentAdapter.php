<?php
namespace Ebanx\Benjamin\Services\Adapters;

class EftPaymentAdapter extends PaymentAdapter
{
    protected function transformPayment()
    {
        $transformed = parent::transformPayment();
        $transformed->eft_code = $this->payment->bankCode;

        return $transformed;
    }
}
