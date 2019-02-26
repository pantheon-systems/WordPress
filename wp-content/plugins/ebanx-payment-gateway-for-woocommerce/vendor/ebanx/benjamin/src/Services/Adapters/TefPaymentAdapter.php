<?php
namespace Ebanx\Benjamin\Services\Adapters;

class TefPaymentAdapter extends BrazilPaymentAdapter
{
    protected function transformPayment()
    {
        $transformed = parent::transformPayment();
        $transformed->payment_type_code = $this->payment->bankCode;

        return $transformed;
    }
}
