<?php
namespace Ebanx\Benjamin\Services;

use Ebanx\Benjamin\Services\Adapters\CancelAdapter;
use Ebanx\Benjamin\Services\Http\HttpService;

class CancelPayment extends HttpService
{
    /**
     * @param $hash
     *
     * @return array
     */
    public function request($hash)
    {
        $adapter = new CancelAdapter($hash, $this->config);
        $response = $this->client->cancel($adapter->transform());

        return $response;
    }
}
