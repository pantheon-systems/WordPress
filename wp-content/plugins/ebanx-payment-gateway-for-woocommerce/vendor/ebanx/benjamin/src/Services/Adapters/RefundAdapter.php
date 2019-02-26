<?php
namespace Ebanx\Benjamin\Services\Adapters;

use Ebanx\Benjamin\Models\Configs\Config;

class RefundAdapter extends BaseAdapter
{
    /**
     * @var array
     */
    private $data;

    /**
     * RefundAdapter constructor.
     *
     * @param array $hash
     * @param Config $config
     */
    public function __construct($hash, Config $config)
    {
        $this->data = $hash;
        parent::__construct($config);
    }

    public function transform()
    {
        $transformed = [
            'integration_key' => $this->getIntegrationKey(),
            'operation' => 'request',
            'amount' => $this->data['amount'],
            'description' => $this->data['description'],
        ];
        if (isset($this->data['hash'])) {
            $transformed['hash'] = $this->data['hash'];
        }

        if (isset($this->data['merchantPaymentCode'])) {
            $transformed['merchant_payment_code'] = $this->data['merchantPaymentCode'];
        }

        return $transformed;
    }

    public function transformCancel()
    {
        return [
            'integration_key' => $this->getIntegrationKey(),
            'operation' => 'cancel',
            'refund_id' => $this->data['refundId'],
        ];
    }
}
