<?php
namespace Ebanx\Benjamin\Services;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Services\Adapters\RefundAdapter;
use Ebanx\Benjamin\Services\Http\HttpService;

class Refund extends HttpService
{
    /**
     * @param string    $hash           The payment hash.
     * @param float     $amount         The amount to be refunded; expressed in the original payment currency.
     * @param string    $description    Description of the refund reason.
     * @return array
     */
    public function requestByHash($hash, $amount, $description)
    {
        $data = [
            'hash' => $hash,
            'amount' => $amount,
            'description' => $description,
        ];

        return $this->request($data);
    }

    /**
     * @param string    $merchantPaymentCode    The merchant payment code
     * @param float     $amount                 The amount to be refunded; expressed in the original payment currency.
     * @param string    $description            Description of the refund reason.
     * @return array
     */
    public function requestByMerchantPaymentCode($merchantPaymentCode, $amount, $description)
    {
        $data = [
            'merchantPaymentCode' => $merchantPaymentCode,
            'amount' => $amount,
            'description' => $description,
        ];

        return $this->request($data);
    }

    public function cancel($refundId)
    {
        $adapter = new RefundAdapter(['refundId' => $refundId], $this->config);
        $response = $this->client->refund($adapter->transformCancel());

        return $response;
    }

    /**
     * @param $data
     * @return array
     */
    private function request($data)
    {
        $adapter = new RefundAdapter($data, $this->config);
        $response = $this->client->refund($adapter->transform());

        return $response;
    }
}
