<?php
namespace Ebanx\Benjamin\Services\Gateways;

use Ebanx\Benjamin\Models\Payment;

abstract class DirectGateway extends BaseGateway
{
    // Please override me
    const API_TYPE = 'Invalid';

    abstract protected function getPaymentData(Payment $payment);

    /**
     * @param  Payment $payment
     * @return array
     */
    public function create(Payment $payment)
    {
        $payment->type = static::API_TYPE;
        $body = $this->client->payment($this->getPaymentData($payment));

        return $body;
    }

    /**
     * @deprecated 1.3.0 Payment requests should be made using Hosted gateway's create method
     * @param  Payment $payment
     * @return array
     */
    public function request(Payment $payment)
    {
        $payment->type = static::API_TYPE;
        $body = $this->client->request($this->getPaymentData($payment));

        return $body;
    }
}
