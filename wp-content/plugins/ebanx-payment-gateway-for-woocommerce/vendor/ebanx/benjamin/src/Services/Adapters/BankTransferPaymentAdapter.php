<?php
namespace Ebanx\Benjamin\Services\Adapters;

class BankTransferPaymentAdapter extends BrazilPaymentAdapter
{
    public function transformPayment()
    {
        $transformed = parent::transformPayment();

        if (isset($this->payment->dueDate)) {
            $transformed->due_date = $this->payment->dueDate->format('d/m/Y');
        }

        return $transformed;
    }
}
