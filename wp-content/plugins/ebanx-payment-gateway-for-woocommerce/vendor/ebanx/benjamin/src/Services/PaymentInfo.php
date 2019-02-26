<?php
namespace Ebanx\Benjamin\Services;

use Ebanx\Benjamin\Models\Configs\Config;
use Ebanx\Benjamin\Services\Adapters\PaymentInfoAdapter;
use Ebanx\Benjamin\Services\Http\HttpService;

class PaymentInfo extends HttpService
{
    /**
     * @param string    $hash
     * @param bool|null $isSandbox
     * @return array
     */
    public function findByHash($hash, $isSandbox = null)
    {
        return $this->fetchInfoByType('hash', $hash, $isSandbox);
    }

    /**
     * @param string $merchantPaymentCode
     * @param bool|null $isSandbox
     * @return array
     */
    public function findByMerchantPaymentCode($merchantPaymentCode, $isSandbox = null)
    {
        return $this->fetchInfoByType('merchant_payment_code', $merchantPaymentCode, $isSandbox);
    }

    /**
     * @param string $type Search type
     * @param string $query Search key
     * @param bool|null $isSandbox
     * @return array
     */
    private function fetchInfoByType($type, $query, $isSandbox)
    {
        $adapter = new PaymentInfoAdapter($type, $query, $this->config);

        $this->switchMode($isSandbox);
        $response = $this->client->paymentInfo($adapter->transform());
        $this->switchMode(null);

        //TODO: decorate response
        return $response;
    }
}
